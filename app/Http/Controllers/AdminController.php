<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\RepairJob;
use App\Models\Service;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalUsers = User::count();
        $totalJobs = RepairJob::count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $pendingJobs = RepairJob::where('status', 'pending')->count();
        $completedJobs = RepairJob::where('status', 'completed')->count();

        // Weather integration (initial page render)
        // prefer per-user weather city when available, otherwise use configured default
        $city = null;
        if ($request->user() && !empty($request->user()->weather_city)) {
            $city = $request->user()->weather_city;
        }
        $city = $city ?? config('services.weather.default_city', env('WEATHER_DEFAULT_CITY', 'Manila'));
        [$weather, $weather_error] = $this->fetchWeatherData($city);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalJobs',
            'totalRevenue',
            'pendingJobs',
            'completedJobs',
            'weather',
            'weather_error'
        ));
    }

    /**
     * Sample raw weather structure used as a fallback when the external API is unavailable.
     */
    private function sampleWeatherRaw($city)
    {
        $now = Carbon::now();
        $sunrise = $now->copy()->setTime(6, 0, 0)->timestamp;
        $sunset = $now->copy()->setTime(18, 0, 0)->timestamp;

        return [
            'name' => $city,
            'timezone' => 0,
            'sys' => [
                'sunrise' => $sunrise,
                'sunset' => $sunset,
            ],
            'main' => [
                'temp' => 25.4,
                'feels_like' => 26.0,
                'temp_min' => 22.0,
                'temp_max' => 28.0,
                'humidity' => 60,
            ],
            'weather' => [
                [
                    'description' => 'clear sky',
                    'icon' => '01d',
                ]
            ],
            'wind' => [
                'speed' => 3.5,
                'deg' => 90,
            ],
            'mock' => true,
        ];
    }

    /**
     * JSON endpoint for live weather used by client-side polling.
     */
    public function weather(Request $request)
    {
        // priority: query param 'city' -> user's preference -> default
        $city = $request->query('city');
        if (! $city && $request->user() && !empty($request->user()->weather_city)) {
            $city = $request->user()->weather_city;
        }
        $city = $city ?? config('services.weather.default_city', env('WEATHER_DEFAULT_CITY', 'Manila'));
        [$weather, $weather_error] = $this->fetchWeatherData($city);

        if ($weather) {
            return response()->json(['success' => true, 'weather' => $weather]);
        }

        return response()->json(['success' => false, 'message' => $weather_error ?? 'Weather unavailable'], 503);
    }

    /**
     * Return current user's weather preferences (live updates and unit)
     */
    public function getWeatherPreferences(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        return response()->json([
            'success' => true,
            'weather_live_updates' => (bool) ($user->weather_live_updates ?? true),
            'weather_unit' => $user->weather_unit ?? 'C',
            'weather_city' => $user->weather_city ?? null,
        ]);
    }

    /**
     * Save current user's weather preferences
     */
    public function saveWeatherPreferences(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $validated = $request->validate([
            'weather_live_updates' => 'nullable|boolean',
            'weather_unit' => 'nullable|in:C,F',
            'weather_city' => 'nullable|string|max:100',
        ]);

        $attrs = [];
        if ($request->has('weather_live_updates')) $attrs['weather_live_updates'] = $request->boolean('weather_live_updates', true);
        if ($request->has('weather_unit')) $attrs['weather_unit'] = $request->input('weather_unit', 'C');
        if ($request->filled('weather_city')) $attrs['weather_city'] = $request->input('weather_city');

        if (! empty($attrs)) {
            $user->update($attrs);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Perform the external API call and map the response.
     * Returns array: [weather|null, errorMessage|null]
     */
    private function fetchWeatherData($city)
    {
        $weather = null;
        $weather_error = null;

        try {
            $apiKey = config('services.weather.key');
            $useFallback = false;
            if (! $apiKey) {
                $useFallback = true;
            }

            $cacheKey = 'weather_'.md5(strtolower($city));
            $weather = Cache::remember($cacheKey, 60, function() use ($city, $apiKey, $useFallback) {
                // If no API key or fallback requested, return mocked sample data
                if ($useFallback) {
                    $data = $this->sampleWeatherRaw($city);
                } else {
                    $response = Http::timeout(5)->get('https://api.openweathermap.org/data/2.5/weather', [
                        'q' => $city,
                        'appid' => $apiKey,
                        'units' => 'metric',
                    ]);

                    if (! $response->successful()) {
                        try {
                            Log::warning('Weather API returned non-success status', [
                                'status' => $response->status(),
                                'body' => $response->body(),
                            ]);
                        } catch (\Exception $_e) {}
                        // Fall back to sample data so UI remains usable
                        $data = $this->sampleWeatherRaw($city);
                    } else {
                        $data = $response->json();
                    }
                }
                $tz = $data['timezone'] ?? 0; // seconds offset from UTC
                $sunrise_ts = isset($data['sys']['sunrise']) ? ($data['sys']['sunrise'] + $tz) : null;
                $sunset_ts = isset($data['sys']['sunset']) ? ($data['sys']['sunset'] + $tz) : null;

                $wind_deg = $data['wind']['deg'] ?? null;
                $wind_label = null;
                if ($wind_deg !== null) {
                    $val = intval(floor(($wind_deg / 22.5) + 0.5)) % 16;
                    $dirs = ['N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSW','SW','WSW','W','WNW','NW','NNW'];
                    $wind_label = $dirs[$val] ?? null;
                }

                return [
                    'city' => $data['name'] ?? $city,
                    'temperature' => isset($data['main']['temp']) ? round($data['main']['temp'], 1) : null,
                    'feels_like' => isset($data['main']['feels_like']) ? round($data['main']['feels_like'], 1) : null,
                    'temp_min' => isset($data['main']['temp_min']) ? round($data['main']['temp_min'], 1) : null,
                    'temp_max' => isset($data['main']['temp_max']) ? round($data['main']['temp_max'], 1) : null,
                    'description' => $data['weather'][0]['description'] ?? null,
                    'icon' => $data['weather'][0]['icon'] ?? null,
                    'humidity' => $data['main']['humidity'] ?? null,
                    'wind_speed' => $data['wind']['speed'] ?? null,
                    'wind_deg' => $wind_deg,
                    'wind_label' => $wind_label,
                    'sunrise' => $sunrise_ts ? Carbon::createFromTimestamp($sunrise_ts)->toDateTimeString() : null,
                    'sunset' => $sunset_ts ? Carbon::createFromTimestamp($sunset_ts)->toDateTimeString() : null,
                    'fetched_at' => now()->toDateTimeString(),
                    'mock' => ($useFallback || ($data['mock'] ?? false)) ? true : false,
                ];
            });

            if (! $weather) {
                $weather_error = 'Weather data unavailable (API returned error)';
            }
        } catch (\Exception $e) {
            try { Log::error('Weather fetch exception: '.$e->getMessage()); } catch (\Exception $_) {}
            $weather_error = 'Unable to retrieve weather data';
        }

        return [$weather, $weather_error];
    }

    // User Management
    public function users()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    // Service Management
    public function services()
    {
        $services = Service::paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function createService()
    {
        return view('admin.services.create');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'estimated_hours' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Service::create($validated);
        return redirect()->route('admin.services.index')->with('success', 'Service created successfully');
    }

    public function editService($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    public function updateService(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'estimated_hours' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $service->update($validated);
        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully');
    }

    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully');
    }

    // Repair Jobs Management
    public function jobs()
    {
        $jobs = RepairJob::with('customer', 'mechanic')->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function editJob($id)
    {
        $job = RepairJob::with('services')->findOrFail($id);
        $mechanics = User::where('role_id', 3)->get(); // mechanic role
        $managers = User::where('role_id', 2)->get(); // manager role
        $services = Service::all();
        return view('admin.jobs.edit', compact('job', 'mechanics', 'managers', 'services'));
    }

    public function updateJob(Request $request, $id)
    {
        $job = RepairJob::findOrFail($id);
        $validated = $request->validate([
            'mechanic_id' => 'nullable|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,assigned,in-progress,completed,cancelled',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date_time',
            'completion_date' => 'nullable|date_time',
            'notes' => 'nullable|string',
        ]);

        $job->update($validated);
        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully');
    }

    // Reports
    public function reports()
    {
        return view('admin.reports.index');
    }

    public function revenueReport()
    {
        $invoices = Invoice::with('customer')->whereYear('created_at', date('Y'))->get();
        return view('admin.reports.revenue', compact('invoices'));
    }
}

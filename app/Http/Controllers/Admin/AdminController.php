<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\RepairJob;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalUsers = User::count();
        $totalJobs = RepairJob::count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $pendingJobs = RepairJob::where('status', 'pending')->count();
        $completedJobs = RepairJob::where('status', 'completed')->count();

        $revenueTrend = Invoice::selectRaw('SUM(total) as total, DATE_FORMAT(created_at, "%Y-%m") as month_key')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->where('status', 'paid')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->mapWithKeys(function ($row) {
                return [Carbon::parse($row->month_key.'-01')->format('M') => (float) $row->total];
            })
            ->all();

        $jobBreakdown = [
            'pending' => RepairJob::where('status', 'pending')->count(),
            'assigned' => RepairJob::where('status', 'assigned')->count(),
            'in-progress' => RepairJob::where('status', 'in-progress')->count(),
            'completed' => RepairJob::where('status', 'completed')->count(),
        ];

        $monthlyLabels = [];
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $key = $month->format('M');
            $monthlyLabels[] = $key;
            $monthlyRevenue[] = (float) ($revenueTrend[$key] ?? 0);
        }

        // Weather integration (initial page render)
        // prefer per-user weather city when available, otherwise use configured default
        $city = null;
        if ($request->user() && ! empty($request->user()->weather_city)) {
            $city = $request->user()->weather_city;
        }
        $city = $city ?? config('services.weather.default_city', env('WEATHER_DEFAULT_CITY', 'Manila'));
        [$weather, $weather_error] = $this->fetchWeatherData($city);

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalJobs',
            'totalRevenue',
            'pendingJobs',
            'completedJobs',
            'weather',
            'weather_error',
            'monthlyLabels',
            'monthlyRevenue',
            'jobBreakdown'
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

        $seed = hexdec(substr(md5((string) $city), 0, 8));
        $conditions = [
            'clear sky',
            'few clouds',
            'broken clouds',
            'light rain',
            'overcast clouds',
            'mist',
            'scattered clouds',
        ];
        $condition = $conditions[$seed % count($conditions)];
        $iconMap = [
            'clear sky' => '01d',
            'few clouds' => '02d',
            'broken clouds' => '04d',
            'light rain' => '10d',
            'overcast clouds' => '04d',
            'mist' => '50d',
            'scattered clouds' => '03d',
        ];

        $temperature = 18 + ($seed % 26);
        $feelsLike = $temperature + 1 + ($seed % 4);
        $minTemp = max(12, $temperature - 4 - ($seed % 3));
        $maxTemp = $temperature + 3 + ($seed % 4);
        $humidity = 40 + ($seed % 45);
        $windSpeed = 1 + ($seed % 12) + ($seed % 5) / 10;
        $windDeg = $seed % 360;

        return [
            'name' => $city,
            'timezone' => 0,
            'sys' => [
                'sunrise' => $sunrise,
                'sunset' => $sunset,
            ],
            'main' => [
                'temp' => $temperature,
                'feels_like' => $feelsLike,
                'temp_min' => $minTemp,
                'temp_max' => $maxTemp,
                'humidity' => $humidity,
            ],
            'weather' => [
                [
                    'description' => $condition,
                    'icon' => $iconMap[$condition] ?? '01d',
                ],
            ],
            'wind' => [
                'speed' => round($windSpeed, 1),
                'deg' => $windDeg,
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
        if (! $city && $request->user() && ! empty($request->user()->weather_city)) {
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
        if (! $user) {
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
        if (! $user) {
            return response()->json(['success' => false], 401);
        }

        $validated = $request->validate([
            'weather_live_updates' => 'nullable|boolean',
            'weather_unit' => 'nullable|in:C,F',
            'weather_city' => 'nullable|string|max:100',
        ]);

        $attrs = [];
        if ($request->has('weather_live_updates')) {
            $attrs['weather_live_updates'] = $request->boolean('weather_live_updates', true);
        }
        if ($request->has('weather_unit')) {
            $attrs['weather_unit'] = $request->input('weather_unit', 'C');
        }
        if ($request->has('weather_city')) {
            $city = trim((string) $request->input('weather_city', ''));
            $attrs['weather_city'] = $city !== '' ? $city : null;
        }

        if (! empty($attrs)) {
            $user->update($attrs);
        }

        return response()->json(['success' => true]);
    }

    private function mapWeatherResponse(array $data, string $city, bool $isFallback = false): array
    {
        $tz = $data['timezone'] ?? 0;
        $sunrise_ts = isset($data['sys']['sunrise']) ? ($data['sys']['sunrise'] + $tz) : null;
        $sunset_ts = isset($data['sys']['sunset']) ? ($data['sys']['sunset'] + $tz) : null;

        $wind_deg = $data['wind']['deg'] ?? null;
        $wind_label = null;
        if ($wind_deg !== null) {
            $val = intval(floor(($wind_deg / 22.5) + 0.5)) % 16;
            $dirs = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
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
            'mock' => $isFallback,
        ];
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
            $lastKnownKey = 'weather_last_known_'.md5(strtolower($city));

            $weather = Cache::remember($cacheKey, 60, function () use ($city, $apiKey, $useFallback, $lastKnownKey) {
                if ($useFallback) {
                    $data = $this->sampleWeatherRaw($city);
                } else {
                    try {
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
                            } catch (\Exception $_e) {
                            }
                            $data = $this->sampleWeatherRaw($city);
                        } else {
                            $data = $response->json();
                        }
                    } catch (\Exception $e) {
                        Log::warning('Weather API request failed, using offline fallback', [
                            'city' => $city,
                            'error' => $e->getMessage(),
                        ]);
                        $data = $this->sampleWeatherRaw($city);
                    }
                }

                $formatted = $this->mapWeatherResponse($data, $city, $useFallback || ($data['mock'] ?? false));
                Cache::put($lastKnownKey, $formatted, 60 * 24 * 7);

                return $formatted;
            });

            if (! $weather) {
                $weather = Cache::get($lastKnownKey) ?: $this->mapWeatherResponse($this->sampleWeatherRaw($city), $city, true);
                $weather_error = 'No internet connection. Showing the last available weather.';
            }
        } catch (\Exception $e) {
            try {
                Log::error('Weather fetch exception: '.$e->getMessage());
            } catch (\Exception $_) {
            }
            $weather = Cache::get('weather_last_known_'.md5(strtolower($city))) ?: $this->mapWeatherResponse($this->sampleWeatherRaw($city), $city, true);
            $weather_error = 'No internet connection. Showing the last available weather.';
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
            'email' => 'required|email|unique:users,email,'.$id,
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
        $mechanics = User::whereHas('role', fn ($query) => $query->where('name', 'mechanic'))->get();
        $managers = User::whereHas('role', fn ($query) => $query->where('name', 'manager'))->get();
        $services = Service::all();

        return view('admin.jobs.edit', compact('job', 'mechanics', 'managers', 'services'));
    }

    public function updateJob(Request $request, $id)
    {
        $job = RepairJob::findOrFail($id);
        $oldStatus = $job->status;

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

        // If a mechanic was selected, ensure that user has the 'mechanic' role
        if (! empty($validated['mechanic_id'])) {
            $mechanic = User::find($validated['mechanic_id']);
            if ($mechanic) {
                $mechanicRole = Role::firstOrCreate(
                    ['name' => 'mechanic'],
                    ['description' => 'Mechanic']
                );
                if ($mechanic->role_id !== $mechanicRole->id) {
                    $mechanic->role_id = $mechanicRole->id;
                    $mechanic->save();
                }
            }
        }

        // If a manager was selected, ensure that user has the 'manager' role
        if (! empty($validated['manager_id'])) {
            $manager = User::find($validated['manager_id']);
            if ($manager) {
                $managerRole = Role::firstOrCreate(
                    ['name' => 'manager'],
                    ['description' => 'Manager']
                );
                if ($manager->role_id !== $managerRole->id) {
                    $manager->role_id = $managerRole->id;
                    $manager->save();
                }
            }
        }

        // If admin marked job completed, set completion date and create invoice
        if (($validated['status'] ?? null) === 'completed' && $oldStatus !== 'completed') {
            $job->update(['completion_date' => now()]);

            $existing = Invoice::where('repair_job_id', $job->id)->first();
            if (! $existing) {
                $subtotal = $job->actual_cost ?? $job->estimated_cost ?? 0;
                $tax = 0;
                $invoice = new Invoice([
                    'repair_job_id' => $job->id,
                    'customer_id' => $job->customer_id,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $subtotal + $tax,
                    'status' => 'paid',
                    'paid_date' => now(),
                ]);
                $invoice->invoice_number = $invoice->generateInvoiceNumber();
                $invoice->save();
            }
        }

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully');
    }

    public function deleteJob($id)
    {
        $job = RepairJob::findOrFail($id);
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully');
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

    // Task Management
    public function taskDashboard(Request $request)
    {
        $statusFilter = $request->input('status');
        $typeFilter = $request->input('type');

        $query = Task::query();
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        $stats = [
            'total' => Task::count(),
            'queued' => Task::where('status', 'queued')->count(),
            'processing' => Task::where('status', 'processing')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'failed' => Task::where('status', 'failed')->count(),
        ];

        $recentTasks = $query->latest()->take(8)->get();
        $taskTypes = Task::select('type')->distinct()->orderBy('type')->pluck('type');
        $failureSummary = DB::table('failed_jobs')
            ->selectRaw('queue, COUNT(*) as failures')
            ->groupBy('queue')
            ->orderByDesc('failures')
            ->get();

        return view('admin.tasks.dashboard', compact('stats', 'recentTasks', 'failureSummary', 'taskTypes', 'statusFilter', 'typeFilter'));
    }

    public function taskHistory(Request $request)
    {
        $statusFilter = $request->input('status');
        $typeFilter = $request->input('type');

        $query = Task::query();
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        $tasks = $query->latest()->paginate(15)->appends($request->only(['status', 'type']));

        return view('admin.tasks.history', compact('tasks', 'statusFilter', 'typeFilter'));
    }

    public function taskFailures()
    {
        $failures = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->paginate(15);

        return view('admin.tasks.failures', compact('failures'));
    }

    public function retryFailedJob($id)
    {
        $failure = DB::table('failed_jobs')->where('id', $id)->first();

        if (! $failure) {
            return redirect()->route('admin.tasks.failures')->with('error', 'Failed job not found.');
        }

        Artisan::call('queue:retry', ['id' => [$failure->uuid]]);

        return redirect()->route('admin.tasks.failures')->with('success', 'Failed job has been queued for retry.');
    }
}

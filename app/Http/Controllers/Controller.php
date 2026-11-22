<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    use AuthorizesRequests;

    protected function weatherForUser(Request $request): array
    {
        $city = $request->query('city');
        if (! $city && $request->user() && ! empty($request->user()->weather_city)) {
            $city = $request->user()->weather_city;
        }

        $city = $city ?? config('services.weather.default_city', env('WEATHER_DEFAULT_CITY', 'Manila'));

        return $this->fetchWeatherData($city);
    }

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

    private function mapWeatherResponse(array $data, string $city, bool $isFallback = false): array
    {
        $tz = $data['timezone'] ?? 0;
        $sunriseTs = isset($data['sys']['sunrise']) ? ($data['sys']['sunrise'] + $tz) : null;
        $sunsetTs = isset($data['sys']['sunset']) ? ($data['sys']['sunset'] + $tz) : null;

        $windDeg = $data['wind']['deg'] ?? null;
        $windLabel = null;
        if ($windDeg !== null) {
            $val = intval(floor(($windDeg / 22.5) + 0.5)) % 16;
            $dirs = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
            $windLabel = $dirs[$val] ?? null;
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
            'wind_deg' => $windDeg,
            'wind_label' => $windLabel,
            'sunrise' => $sunriseTs ? Carbon::createFromTimestamp($sunriseTs)->toDateTimeString() : null,
            'sunset' => $sunsetTs ? Carbon::createFromTimestamp($sunsetTs)->toDateTimeString() : null,
            'fetched_at' => now()->toDateTimeString(),
            'mock' => $isFallback,
        ];
    }

    private function fetchWeatherData($city)
    {
        $weather = null;
        $weatherError = null;

        try {
            $apiKey = config('services.weather.key');
            $useFallback = ! $apiKey;
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
                $weatherError = 'No internet connection. Showing the last available weather.';
            }
        } catch (\Exception $e) {
            Log::error('Weather fetch exception: '.$e->getMessage());
            $weather = Cache::get('weather_last_known_'.md5(strtolower($city))) ?: $this->mapWeatherResponse($this->sampleWeatherRaw($city), $city, true);
            $weatherError = 'No internet connection. Showing the last available weather.';
        }

        return [$weather, $weatherError];
    }
}

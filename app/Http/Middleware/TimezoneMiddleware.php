<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class TimezoneMiddleware
{
    public function handle($request, Closure $next)
    {
        $setting = Setting::first();
        if ($setting && $setting->timezone) {
            config(['app.timezone' => $setting->timezone]);
            date_default_timezone_set($setting->timezone);
        }

        return $next($request);
    }
}

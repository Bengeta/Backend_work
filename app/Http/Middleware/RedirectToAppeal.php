<?php

namespace App\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;

class RedirectToAppeal
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('appeal') === true)
            return $next($request);
        if (!session()->exists('suggestion')) {
            session()->put('suggestion', 0);
            session()->put('transaction', 0);
        }
        error_log($request->session()->get('transaction'));
        $settings = app(Settings::class);
        error_log($request->session()->get('suggestion'));
        if ($request->session()->get('suggestion') < $settings->max) {
            if ($request->session()->get('transaction') < $settings->period) {
                $request->session()->increment('transaction');
            } else {
                $request->session()->now('suggest', true);
                $request->session()->put('message', true);
                $request->session()->increment('suggestion');
                $request->session()->put('transaction', 0);
            }
        }


        return $next($request);
    }
}

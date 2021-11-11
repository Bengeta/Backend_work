<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;

class RedirectFromOldSlug
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
        $url = parse_url($request->url())['path'];
        $redirect = Redirect::where('old_slug', $url)->orderByDesc('created_at')->orderByDesc('id')->first();
        if ($redirect !== null) {
            while ($redirect !== null) {
                $url = $redirect->new_slug;
                $redirect = Redirect::where('old_slug', $url)->where('created_at', '>', $redirect->created_at)->orderByDesc('created_at')->orderByDesc('id')->first();
            }
            return redirect($url);
        }

        return $next($request);
    }
}

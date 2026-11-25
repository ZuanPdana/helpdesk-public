<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var array<int, string> */
    protected array $supported = ['id', 'en'];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Authenticated user preference
        if ($request->user() && in_array($request->user()->locale, $this->supported, true)) {
            $locale = $request->user()->locale;
        }

        // 2. Session preference
        if ($locale === null) {
            $sessionLocale = $request->session()->get('locale');
            if (in_array($sessionLocale, $this->supported, true)) {
                $locale = $sessionLocale;
            }
        }

        // 3. Application default
        if ($locale === null) {
            $locale = config('app.locale', 'id');
            if (! in_array($locale, $this->supported, true)) {
                $locale = 'id';
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}

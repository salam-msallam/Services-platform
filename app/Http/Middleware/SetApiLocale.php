<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) $request->header('Accept-Language', '');
        $preferred = strtolower(substr($header, 0, 2));

        if (in_array($preferred, ['ar', 'en'], true)) {
            App::setLocale($preferred);
        } else {
            App::setLocale((string) config('app.locale', 'en'));
        }

        return $next($request);
    }
}

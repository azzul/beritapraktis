<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNonCanonicalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = ltrim($request->path(), '/');

        // Izinkan endpoint validasi SSL (AutoSSL/Let's Encrypt) dan healthcheck
        if (str_starts_with($path, '.well-known/') || $path === 'up') {
            return $next($request);
        }

        $host = strtolower($request->getHost());
        $appUrl = (string) config('app.url', '');
        $parsedHost = strtolower((string) parse_url($appUrl, PHP_URL_HOST));
        $parsedScheme = strtolower((string) parse_url($appUrl, PHP_URL_SCHEME)) ?: 'https';

        $isConfiguredDomain = $parsedHost !== ''
            && !in_array($parsedHost, ['localhost', '127.0.0.1', '::1'], true)
            && !str_starts_with($parsedHost, 'mail.');

        // 1. Selalu redirect subdomain mail.* (mis. mail.beritapraktis.biz.id) agar tidak terindeks sebagai duplikat
        if (str_starts_with($host, 'mail.')) {
            $targetHost = $isConfiguredDomain ? $parsedHost : substr($host, 5);
            $targetUrl = "https://{$targetHost}{$request->getRequestUri()}";

            return redirect()->away($targetUrl, 301, [
                'X-Robots-Tag' => 'noindex, nofollow',
            ]);
        }

        // 2. Di production, paksa host kanonikal (non-www) dan HTTPS
        if (app()->environment('production') && $isConfiguredDomain) {
            $needsHostRedirect = $host !== $parsedHost;
            $needsHttpsRedirect = $parsedScheme === 'https' && !$request->isSecure();

            if ($needsHostRedirect || $needsHttpsRedirect) {
                $targetUrl = "{$parsedScheme}://{$parsedHost}{$request->getRequestUri()}";

                return redirect()->away($targetUrl, 301);
            }
        }

        return $next($request);
    }
}

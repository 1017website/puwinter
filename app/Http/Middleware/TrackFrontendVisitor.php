<?php

namespace App\Http\Middleware;

use App\Models\FrontendVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackFrontendVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        try {
            $visitorId = (string) $request->cookie('puwinter_visitor_id');
            if (! Str::isUuid($visitorId)) {
                $visitorId = (string) Str::uuid();
                $response->headers->setCookie(cookie(
                    'puwinter_visitor_id',
                    $visitorId,
                    60 * 24 * 365 * 2,
                    '/',
                    null,
                    $request->isSecure(),
                    true,
                    false,
                    'Lax'
                ));
            }

            $agent = (string) $request->userAgent();
            $referrer = (string) $request->headers->get('referer', '');

            FrontendVisit::create([
                'visitor_id' => $visitorId,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'user_id' => $request->user()?->id,
                'ip_hash' => $request->ip() ? hash_hmac('sha256', $request->ip(), (string) config('app.key')) : null,
                'path' => '/'.ltrim($request->path(), '/'),
                'route_name' => $request->route()?->getName(),
                'referrer' => $referrer ?: null,
                'referrer_domain' => $referrer ? (parse_url($referrer, PHP_URL_HOST) ?: null) : null,
                'device' => $this->device($agent),
                'browser' => $this->browser($agent),
                'operating_system' => $this->operatingSystem($agent),
                'user_agent' => $agent ?: null,
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->user() || $response->getStatusCode() >= 400) {
            return false;
        }

        if (! Schema::hasTable('frontend_visits')) {
            return false;
        }

        $agent = strtolower((string) $request->userAgent());
        $bots = ['bot', 'crawler', 'spider', 'slurp', 'facebookexternalhit', 'whatsapp', 'preview'];

        return ! Str::contains($agent, $bots)
            && strtolower((string) $request->headers->get('sec-purpose')) !== 'prefetch';
    }

    private function device(string $agent): string
    {
        if (preg_match('/ipad|tablet|kindle|playbook/i', $agent)) {
            return 'Tablet';
        }

        if (preg_match('/mobile|iphone|ipod|android.*mobile|windows phone/i', $agent)) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private function browser(string $agent): string
    {
        return match (true) {
            preg_match('/Edg\//i', $agent) === 1 => 'Edge',
            preg_match('/OPR\//i', $agent) === 1 => 'Opera',
            preg_match('/Chrome\//i', $agent) === 1 => 'Chrome',
            preg_match('/Firefox\//i', $agent) === 1 => 'Firefox',
            preg_match('/Safari\//i', $agent) === 1 => 'Safari',
            default => 'Lainnya',
        };
    }

    private function operatingSystem(string $agent): string
    {
        return match (true) {
            preg_match('/Windows/i', $agent) === 1 => 'Windows',
            preg_match('/iPhone|iPad|iPod/i', $agent) === 1 => 'iOS',
            preg_match('/Android/i', $agent) === 1 => 'Android',
            preg_match('/Mac OS X|Macintosh/i', $agent) === 1 => 'macOS',
            preg_match('/Linux/i', $agent) === 1 => 'Linux',
            default => 'Lainnya',
        };
    }
}

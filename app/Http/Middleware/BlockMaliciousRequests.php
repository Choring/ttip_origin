<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BlockMaliciousRequests
{
    // 차단할 IP 목록
    protected array $blockedIps = [
        '91.208.197.53',
        '222.239.104.46',
    ];

    // User-Agent에 포함되면 차단할 패턴 (환경변수 탈취 시도 등)
    protected array $maliciousPatterns = [
        '${env:',
        '${',
        '#{',
        '{{',
        'jndi:',
        'ldap://',
        '../',
        '/etc/passwd',
        'cmd.exe',
        'powershell',
        '<script',
        'eval(',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';

        // IP 차단
        if (in_array($ip, $this->blockedIps)) {
            Log::warning('[보안] 차단된 IP 접근 시도', ['ip' => $ip, 'url' => $request->fullUrl()]);
            abort(403, 'Forbidden');
        }

        // 악성 User-Agent 차단
        foreach ($this->maliciousPatterns as $pattern) {
            if (str_contains(strtolower($userAgent), strtolower($pattern))) {
                Log::warning('[보안] 악성 User-Agent 차단', [
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'pattern' => $pattern,
                ]);
                abort(403, 'Forbidden');
            }
        }

        return $next($request);
    }
}

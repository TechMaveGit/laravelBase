<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set CSP headers
        // Content-Security-Policy: default-src 'self';
        // Content-Security-Policy: default-src 'self'; script-src 'self' https://apis.google.com;
        // Content-Security-Policy: script-src 'self' 'nonce-abc123';
        // Content-Security-Policy: default-src 'self'; img-src 'self' https://cdn.example.com;
        // Content-Security-Policy-Report-Only: default-src 'self'; report-uri /csp-violation-report-endpoint/;
        // Content-Security-Policy: default-src 'self'; style-src 'self' https://fonts.googleapis.com;
        // Content-Security-Policy: upgrade-insecure-requests;



        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; img-src 'self'; style-src 'self' 'unsafe-inline';");

        return $response;
    }
}

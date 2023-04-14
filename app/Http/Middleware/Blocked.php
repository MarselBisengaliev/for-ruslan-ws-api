<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Blocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->blocked) {
            $reason = $request->user()->blocked->reason;
            $parsedReason = '';

            if ($reason === 'admin') {
                $parsedReason = 'You have been blocked by an administrator';
            } else if ($reason === 'spam') {
                $parsedReason = 'You have been blocked for spamming';
            } else {
                $parsedReason = 'You have been blocked for cheating';
            }
            return response()->json([
                "status" => "blocked",
                "message" => "User blocked",
                "reason" => $parsedReason
            ], 403);
        }

        return $next($request);
    }
}

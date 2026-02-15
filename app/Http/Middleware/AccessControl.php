<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
 public function handle(Request $request, Closure $next, ...$except): Response
    {
        $user = $request->user();

        $route = $request->route();
        $routeName = $route?->getName();

        if (!$routeName) {
            abort(403);
        }

        if (in_array($routeName, $except, true)) {
            return $next($request);
        }

        abort_unless($user?->can($routeName), 403);

        return $next($request);
    }
}

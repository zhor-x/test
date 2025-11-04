<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->role || $user->role !== UserRole::ADMIN->value) {
            return response()->json(['message' => 'Forbidden: Admins only.'], 403);
        }

        return $next($request);
    }
}

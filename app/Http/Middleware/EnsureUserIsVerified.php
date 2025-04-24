<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        
        if ($user && method_exists($user, 'isVerified') && ! $user->isVerified()) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم التحقق من حسابك بعد. يرجى التحقق أولاً.',
            ], 403);
        }

        return $next($request);
    }

}

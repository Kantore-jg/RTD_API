<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganizationStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $org = $user->organization;

        if ($org && $org->status === 'suspended') {
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Votre organisation a été suspendue. Veuillez contacter l\'administration.',
                'suspended' => true,
            ], 403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    public function getOrgSettings(Request $request): JsonResponse
    {
        $org = $request->user()->organization;

        abort_if(! $org, 404, 'Aucune organisation associée.');

        return response()->json($org);
    }

    public function updateOrgSettings(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'nif' => ['nullable', 'string', 'max:100'],
            'accent_color' => ['nullable', 'string', 'in:blue,violet,emerald,rose,orange,slate'],
        ]);

        $org = $request->user()->organization;
        $org->update($request->only([
            'name', 'domain', 'address', 'phone', 'email', 'company_email', 'nif', 'accent_color',
        ]));

        return response()->json($org);
    }

    public function updateLogo(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'logo' => ['required', 'image', 'max:2048'],
        ]);

        $org = $request->user()->organization;

        if ($org->logo) {
            Storage::disk('public')->delete($org->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $org->update(['logo' => $path]);

        return response()->json([
            'data' => $org,
            'logo' => Storage::disk('public')->url($path),
        ]);
    }

    public function removeLogo(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin(), 403);

        $org = $request->user()->organization;

        if ($org->logo) {
            Storage::disk('public')->delete($org->logo);
            $org->update(['logo' => null]);
        }

        return response()->json($org);
    }

    public function updateCredentials(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'current_password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Le mot de passe actuel est incorrect.'],
            ]);
        }

        $user->update(['email' => $request->email]);

        return response()->json(['user' => $user]);
    }

    public function updateNotifications(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Préférences de notification mises à jour.']);
    }
}

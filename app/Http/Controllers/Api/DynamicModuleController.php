<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamicModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DynamicModuleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $modules = DynamicModule::where('organization_id', $orgId)
            ->withCount('entries')
            ->get();

        return response()->json($modules);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'show_in_sidebar' => ['nullable', 'boolean'],
            'fields' => ['nullable', 'array'],
        ]);

        $module = DynamicModule::create([
            'organization_id' => $request->user()->organization_id,
            ...$request->only(['name', 'description', 'icon', 'show_in_sidebar', 'fields']),
        ]);

        return response()->json($module, 201);
    }

    public function show(Request $request, DynamicModule $module): JsonResponse
    {
        abort_if($module->organization_id !== $request->user()->organization_id, 403);

        return response()->json($module->loadCount('entries'));
    }

    public function update(Request $request, DynamicModule $module): JsonResponse
    {
        abort_if($module->organization_id !== $request->user()->organization_id, 403);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'show_in_sidebar' => ['nullable', 'boolean'],
            'fields' => ['nullable', 'array'],
        ]);

        $module->update($request->only([
            'name', 'description', 'icon', 'show_in_sidebar', 'fields',
        ]));

        return response()->json($module);
    }

    public function destroy(Request $request, DynamicModule $module): JsonResponse
    {
        abort_if($module->organization_id !== $request->user()->organization_id, 403);

        $module->entries()->delete();
        $module->delete();

        return response()->json(['message' => 'Module supprimé.']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $folders = Folder::where('organization_id', $orgId)
            ->withCount('files')
            ->latest()
            ->get();

        return response()->json($folders);
    }

    public function store(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $folder = Folder::create([
            'organization_id' => $request->user()->organization_id,
            'name' => $request->name,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($folder, 201);
    }

    public function destroy(Request $request, Folder $folder): JsonResponse
    {
        abort_if($folder->organization_id !== $request->user()->organization_id, 403);

        $folder->files()->delete();
        $folder->delete();

        return response()->json(['message' => 'Dossier supprimé.']);
    }
}

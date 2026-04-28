<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArchivedFile;
use App\Models\FileAccessLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArchivedFileController extends Controller
{
    private const TYPE_MAP = [
        'pdf' => 'PDF',
        'doc' => 'Document', 'docx' => 'Document',
        'xls' => 'Tableur', 'xlsx' => 'Tableur', 'csv' => 'Tableur',
        'jpg' => 'Image', 'jpeg' => 'Image', 'png' => 'Image', 'gif' => 'Image', 'webp' => 'Image',
        'mp4' => 'Vidéo', 'avi' => 'Vidéo', 'mov' => 'Vidéo',
        'zip' => 'Archive', 'rar' => 'Archive',
    ];

    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $query = ArchivedFile::where('organization_id', $orgId)->with(['folder', 'uploader', 'accessLogs.user']);

        if ($folderId = $request->get('folder_id')) {
            $query->where('folder_id', $folderId);
        }

        $files = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($files);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'],
            'folder_id' => ['nullable', 'exists:folders,id'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $orgId = $request->user()->organization_id;
        $uploadedFile = $request->file('file');
        $extension = strtolower($uploadedFile->getClientOriginalExtension());

        $path = $uploadedFile->store("archives/{$orgId}", 'public');

        $file = ArchivedFile::create([
            'organization_id' => $orgId,
            'folder_id' => $request->folder_id,
            'name' => $uploadedFile->hashName(),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'type' => self::TYPE_MAP[$extension] ?? 'Autre',
            'category' => $request->category,
            'size' => $uploadedFile->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json($file, 201);
    }

    public function show(Request $request, ArchivedFile $file): JsonResponse
    {
        abort_if($file->organization_id !== $request->user()->organization_id, 403);

        return response()->json($file->load('accessLogs.user', 'folder'));
    }

    public function download(Request $request, ArchivedFile $file): StreamedResponse
    {
        abort_if($file->organization_id !== $request->user()->organization_id, 403);

        FileAccessLog::create([
            'archived_file_id' => $file->id,
            'user_id' => $request->user()->id,
            'action' => 'download',
        ]);

        return Storage::disk('public')->download($file->path, $file->original_name);
    }

    public function destroy(Request $request, ArchivedFile $file): JsonResponse
    {
        abort_if($file->organization_id !== $request->user()->organization_id, 403);

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return response()->json(['message' => 'Fichier supprimé.']);
    }
}

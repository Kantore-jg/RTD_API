<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamicEntry;
use App\Models\DynamicModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DynamicEntryController extends Controller
{
    public function index(Request $request, DynamicModule $dynamicModule): JsonResponse
    {
        abort_if($dynamicModule->organization_id !== $request->user()->organization_id, 403);

        $query = $dynamicModule->entries();

        if ($search = $request->get('search')) {
            $query->where('data', 'like', "%{$search}%");
        }

        $entries = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($entries);
    }

    public function store(Request $request, DynamicModule $dynamicModule): JsonResponse
    {
        abort_if($dynamicModule->organization_id !== $request->user()->organization_id, 403);

        $request->validate([
            'data' => ['required', 'array'],
        ]);

        $entry = DynamicEntry::create([
            'dynamic_module_id' => $dynamicModule->id,
            'data' => $request->data,
            'submitted_by' => $request->user()->id,
        ]);

        return response()->json($entry, 201);
    }

    public function destroy(Request $request, DynamicModule $dynamicModule, DynamicEntry $dynamicEntry): JsonResponse
    {
        abort_if($dynamicModule->organization_id !== $request->user()->organization_id, 403);
        abort_if($dynamicEntry->dynamic_module_id !== $dynamicModule->id, 404);

        $dynamicEntry->delete();

        return response()->json(['message' => 'Entrée supprimée.']);
    }

    public function exportCsv(Request $request, DynamicModule $dynamicModule): StreamedResponse
    {
        abort_if($dynamicModule->organization_id !== $request->user()->organization_id, 403);

        $entries = $dynamicModule->entries()->get();
        $fields = collect($dynamicModule->fields ?? []);
        $headers = $fields->pluck('label', 'name')->toArray();

        return response()->streamDownload(function () use ($entries, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_values($headers) ?: ['Données']);

            foreach ($entries as $entry) {
                $row = [];
                foreach (array_keys($headers) as $fieldName) {
                    $row[] = $entry->data[$fieldName] ?? '';
                }
                fputcsv($handle, $row ?: [json_encode($entry->data)]);
            }

            fclose($handle);
        }, 'export_' . str_replace(' ', '_', $dynamicModule->name) . '.csv', ['Content-Type' => 'text/csv']);
    }
}

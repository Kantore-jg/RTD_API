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
    public function index(Request $request, DynamicModule $module): JsonResponse
    {
        abort_if($module->organization_id !== $request->user()->organization_id, 403);

        $query = $module->entries()->with('submitter:id,name');

        if ($search = $request->get('search')) {
            $query->where('data', 'like', "%{$search}%");
        }

        $entries = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($entries);
    }

    public function store(Request $request, DynamicModule $module): JsonResponse
    {
        abort_if($module->organization_id !== $request->user()->organization_id, 403);

        $request->validate([
            'data' => ['required', 'array'],
        ]);

        $entry = DynamicEntry::create([
            'dynamic_module_id' => $module->id,
            'data' => $request->data,
            'submitted_by' => $request->user()->id,
        ]);

        return response()->json($entry, 201);
    }

    public function destroy(Request $request, DynamicEntry $entry): JsonResponse
    {
        $module = $entry->module;
        abort_if($module->organization_id !== $request->user()->organization_id, 403);

        $entry->delete();

        return response()->json(['message' => 'Entrée supprimée.']);
    }

    public function exportCsv(Request $request, DynamicModule $module): StreamedResponse
    {
        abort_if($module->organization_id !== $request->user()->organization_id, 403);

        $entries = $module->entries()->get();
        $fields = collect($module->fields ?? []);

        return response()->streamDownload(function () use ($entries, $fields) {
            $handle = fopen('php://output', 'w');
            $labels = $fields->pluck('label')->toArray();
            fputcsv($handle, $labels ?: ['Données']);

            foreach ($entries as $entry) {
                $row = [];
                foreach ($fields as $field) {
                    $row[] = $entry->data[$field['label']] ?? $entry->data[$field['name'] ?? ''] ?? '';
                }
                fputcsv($handle, $row ?: [json_encode($entry->data)]);
            }

            fclose($handle);
        }, 'export_' . str_replace(' ', '_', $module->name) . '.csv', ['Content-Type' => 'text/csv']);
    }
}

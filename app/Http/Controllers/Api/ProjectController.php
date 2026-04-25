<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Traits\CachesQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use CachesQueries;

    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $cacheKey = $this->orgCacheKey('projects', $orgId) . ':' . md5($request->getQueryString() ?? '');

        $data = $this->cached($cacheKey, 60, function () use ($request, $orgId) {
            $query = Project::where('organization_id', $orgId)->withCount('tasks');

            if ($search = $request->get('search')) {
                $query->where('name', 'like', "%{$search}%");
            }

            if ($status = $request->get('status')) {
                $query->where('status', $status);
            }

            if ($category = $request->get('category')) {
                $query->where('category', $category);
            }

            return $query->latest()->paginate($request->get('per_page', 15))->toArray();
        });

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string'],
            'budget' => ['nullable', 'numeric'],
            'deadline' => ['nullable', 'date'],
            'team' => ['nullable', 'array'],
        ]);

        $orgId = $request->user()->organization_id;

        $project = Project::create([
            'organization_id' => $orgId,
            ...$request->only(['name', 'description', 'category', 'status', 'budget', 'deadline', 'team']),
            'progress' => 0,
        ]);

        $this->clearOrgCache('projects', $orgId);

        return response()->json($project, 201);
    }

    public function show(Request $request, Project $project): JsonResponse
    {
        abort_if($project->organization_id !== $request->user()->organization_id, 403);

        return response()->json($project->loadCount('tasks'));
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        abort_if($project->organization_id !== $request->user()->organization_id, 403);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string'],
            'budget' => ['nullable', 'numeric'],
            'deadline' => ['nullable', 'date'],
            'team' => ['nullable', 'array'],
        ]);

        $project->update($request->only([
            'name', 'description', 'category', 'status', 'budget', 'deadline', 'team',
        ]));

        $this->clearOrgCache('projects', $request->user()->organization_id);

        return response()->json($project);
    }

    public function updateProgress(Request $request, Project $project): JsonResponse
    {
        abort_if($project->organization_id !== $request->user()->organization_id, 403);

        $request->validate([
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $data = ['progress' => $request->progress];

        if ((int) $request->progress === 100) {
            $data['status'] = 'Terminé';
        }

        $project->update($data);
        $this->clearOrgCache('projects', $request->user()->organization_id);

        return response()->json($project);
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        abort_if($project->organization_id !== $request->user()->organization_id, 403);

        $project->delete();
        $this->clearOrgCache('projects', $request->user()->organization_id);

        return response()->json(['message' => 'Projet supprimé.']);
    }
}

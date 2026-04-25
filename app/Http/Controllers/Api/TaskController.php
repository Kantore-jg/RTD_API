<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Traits\CachesQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use CachesQueries;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $orgId = $user->organization_id;
        $cacheKey = $this->orgCacheKey('tasks', $orgId) . ':' . $user->id . ':' . md5($request->getQueryString() ?? '');

        $data = $this->cached($cacheKey, 30, function () use ($request, $user, $orgId) {
            $query = Task::where('organization_id', $orgId)->with('assignees', 'project');

            if ($user->isEmployee()) {
                $employeeId = $user->employee?->id;
                $query->whereHas('assignees', fn ($q) => $q->where('employees.id', $employeeId));
            }

            if ($status = $request->get('status')) {
                $query->where('status', $status);
            }

            if ($priority = $request->get('priority')) {
                $query->where('priority', $priority);
            }

            if ($projectId = $request->get('project_id')) {
                $query->where('project_id', $projectId);
            }

            return $query->latest()->paginate($request->get('per_page', 15))->toArray();
        });

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'due_date' => ['nullable', 'date'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['exists:employees,id'],
        ]);

        $orgId = $request->user()->organization_id;

        $task = Task::create([
            'organization_id' => $orgId,
            ...$request->only(['project_id', 'title', 'description', 'status', 'priority', 'progress', 'due_date']),
        ]);

        if ($request->has('assignees')) {
            $task->assignees()->sync($request->assignees);
        }

        $this->clearOrgCache('tasks', $orgId);

        return response()->json($task->load('assignees'), 201);
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        abort_if($task->organization_id !== $request->user()->organization_id, 403);

        return response()->json($task->load('assignees', 'project'));
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        abort_if($task->organization_id !== $request->user()->organization_id, 403);

        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'due_date' => ['nullable', 'date'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['exists:employees,id'],
        ]);

        $task->update($request->only([
            'title', 'description', 'status', 'priority', 'progress', 'due_date',
        ]));

        if ($request->has('assignees')) {
            $task->assignees()->sync($request->assignees);
        }

        $this->clearOrgCache('tasks', $request->user()->organization_id);

        return response()->json($task->load('assignees'));
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        abort_if($task->organization_id !== $request->user()->organization_id, 403);

        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $task->update(['status' => $request->status]);
        $this->clearOrgCache('tasks', $request->user()->organization_id);

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        abort_if($task->organization_id !== $request->user()->organization_id, 403);

        $task->delete();
        $this->clearOrgCache('tasks', $request->user()->organization_id);

        return response()->json(['message' => 'Tâche supprimée.']);
    }
}

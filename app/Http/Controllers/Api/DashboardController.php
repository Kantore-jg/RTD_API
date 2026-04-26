<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\FinanceRecord;
use App\Models\Project;
use App\Models\Task;
use App\Traits\CachesQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    use CachesQueries;

    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $cacheKey = $this->versionedOrgCacheKey('dashboard', $orgId);

        $data = $this->cached($cacheKey, 30, function () use ($orgId) {
            return [
                'stats' => $this->buildStats($orgId),
                'projects' => $this->buildProjects($orgId),
                'activities' => $this->buildActivities($orgId),
            ];
        });

        return response()->json($data);
    }

    private function buildStats(int $orgId): array
    {
        $employeeCount = Employee::where('organization_id', $orgId)->count();
        $projectCount = Project::where('organization_id', $orgId)->count();
        $taskTotal = Task::where('organization_id', $orgId)->count();
        $taskCompleted = Task::where('organization_id', $orgId)->where('status', 'COMPLETED')->count();

        $revenues = (float) FinanceRecord::where('organization_id', $orgId)
            ->where('type', 'Revenu')->sum('montant');
        $expenses = (float) FinanceRecord::where('organization_id', $orgId)
            ->where('type', 'Dépense')->sum('montant');

        $thisMonth = Carbon::now()->startOfMonth();
        $newEmployeesThisMonth = Employee::where('organization_id', $orgId)
            ->where('created_at', '>=', $thisMonth)->count();
        $newProjectsThisMonth = Project::where('organization_id', $orgId)
            ->where('created_at', '>=', $thisMonth)->count();
        $newTasksThisMonth = Task::where('organization_id', $orgId)
            ->where('created_at', '>=', $thisMonth)->count();

        return [
            [
                'label' => 'Employés',
                'value' => (string) $employeeCount,
                'icon' => 'Users',
                'change' => $newEmployeesThisMonth > 0 ? "+{$newEmployeesThisMonth} ce mois" : 'Stable',
                'color' => 'text-blue-500',
                'bg' => 'bg-blue-500/10',
                'link' => '/employees',
            ],
            [
                'label' => 'Projets',
                'value' => (string) $projectCount,
                'icon' => 'Briefcase',
                'change' => $newProjectsThisMonth > 0 ? "+{$newProjectsThisMonth} ce mois" : 'Stable',
                'color' => 'text-emerald-500',
                'bg' => 'bg-emerald-500/10',
                'link' => '/projects',
            ],
            [
                'label' => 'Revenus',
                'value' => number_format($revenues, 0, ',', ' ') . ' BIF',
                'icon' => 'Wallet',
                'change' => number_format($revenues - $expenses, 0, ',', ' ') . ' net',
                'color' => 'text-violet-500',
                'bg' => 'bg-violet-500/10',
                'link' => '/finance',
            ],
            [
                'label' => 'Tâches',
                'value' => (string) $taskTotal,
                'icon' => 'TrendingUp',
                'change' => $taskTotal > 0
                    ? round(($taskCompleted / $taskTotal) * 100) . '% terminées'
                    : '0%',
                'color' => 'text-orange-500',
                'bg' => 'bg-orange-500/10',
                'link' => '/tasks',
            ],
        ];
    }

    private function buildProjects(int $orgId): array
    {
        return Project::where('organization_id', $orgId)
            ->where('status', '!=', 'Terminé')
            ->with('members:id,name')
            ->withCount('tasks')
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get(['id', 'name', 'status', 'progress', 'deadline'])
            ->map(function ($p) {
                $isOverdue = $p->deadline && Carbon::parse($p->deadline)->isPast() && $p->progress < 100;

                return [
                    'name' => $p->name,
                    'progress' => $p->progress,
                    'status' => $p->status,
                    'isOverdue' => $isOverdue,
                    'deadline' => $p->deadline?->format('d/m/Y'),
                    'tasks_count' => $p->tasks_count,
                    'members' => $p->members->map(fn ($m) => [
                        'id' => $m->id,
                        'name' => $m->name,
                        'initials' => collect(explode(' ', $m->name))->map(fn ($n) => mb_substr($n, 0, 1))->join(''),
                    ])->toArray(),
                    'border' => $isOverdue ? 'border-l-orange-500' : 'border-l-primary',
                ];
            })
            ->toArray();
    }

    private function buildActivities(int $orgId): array
    {
        $activities = collect();

        // Recent tasks (created or completed)
        Task::where('organization_id', $orgId)
            ->with('assignees:id,name')
            ->latest('updated_at')
            ->limit(5)
            ->get(['id', 'title', 'status', 'created_at', 'updated_at'])
            ->each(function ($t) use ($activities) {
                $assignee = $t->assignees->first();
                $userName = $assignee?->name ?? 'Système';
                $initials = $assignee
                    ? collect(explode(' ', $assignee->name))->map(fn ($n) => mb_substr($n, 0, 1))->join('')
                    : 'SY';

                $action = match ($t->status) {
                    'COMPLETED' => 'a terminé la tâche',
                    'IN_PROGRESS' => 'travaille sur',
                    'CANCELLED' => 'a annulé la tâche',
                    default => 'a créé la tâche',
                };

                $activities->push([
                    'user' => $userName,
                    'avatar' => mb_strtoupper($initials),
                    'action' => $action,
                    'target' => $t->title,
                    'time' => $t->updated_at->diffForHumans(short: true),
                    'timestamp' => $t->updated_at,
                ]);
            });

        // Recent employees
        Employee::where('organization_id', $orgId)
            ->latest('created_at')
            ->limit(3)
            ->get(['id', 'name', 'department', 'created_at'])
            ->each(function ($e) use ($activities) {
                $initials = collect(explode(' ', $e->name))->map(fn ($n) => mb_substr($n, 0, 1))->join('');
                $activities->push([
                    'user' => $e->name,
                    'avatar' => mb_strtoupper($initials),
                    'action' => 'a rejoint l\'équipe',
                    'target' => $e->department ?? '',
                    'time' => $e->created_at->diffForHumans(short: true),
                    'timestamp' => $e->created_at,
                ]);
            });

        // Recent attendance
        Attendance::where('organization_id', $orgId)
            ->with('employee:id,name')
            ->latest('created_at')
            ->limit(3)
            ->get(['id', 'employee_id', 'arrivee', 'depart', 'created_at'])
            ->each(function ($a) use ($activities) {
                $name = $a->employee?->name ?? 'Employé';
                $initials = collect(explode(' ', $name))->map(fn ($n) => mb_substr($n, 0, 1))->join('');
                $action = $a->depart ? 'a quitté le bureau' : 'est arrivé au bureau';
                $activities->push([
                    'user' => $name,
                    'avatar' => mb_strtoupper($initials),
                    'action' => $action,
                    'target' => $a->arrivee ? "à {$a->arrivee}" : '',
                    'time' => $a->created_at->diffForHumans(short: true),
                    'timestamp' => $a->created_at,
                ]);
            });

        return $activities
            ->sortByDesc('timestamp')
            ->take(8)
            ->map(fn ($a) => collect($a)->except('timestamp')->toArray())
            ->values()
            ->toArray();
    }
}

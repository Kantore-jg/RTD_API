<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\FinanceRecord;
use App\Models\Project;
use App\Models\Task;
use App\Traits\CachesQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use CachesQueries;

    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $cacheKey = $this->orgCacheKey('dashboard', $orgId);

        $data = $this->cached($cacheKey, 60, function () use ($orgId) {
            $employeeCount = Employee::where('organization_id', $orgId)->count();
            $projectCount = Project::where('organization_id', $orgId)->count();

            $taskStats = [
                'total' => Task::where('organization_id', $orgId)->count(),
                'completed' => Task::where('organization_id', $orgId)->where('status', 'Terminée')->count(),
                'in_progress' => Task::where('organization_id', $orgId)->where('status', 'En cours')->count(),
                'pending' => Task::where('organization_id', $orgId)->where('status', 'En attente')->count(),
            ];

            $financeSummary = [
                'revenues' => (float) FinanceRecord::where('organization_id', $orgId)
                    ->where('type', 'Revenu')->sum('montant'),
                'expenses' => (float) FinanceRecord::where('organization_id', $orgId)
                    ->where('type', 'Dépense')->sum('montant'),
            ];
            $financeSummary['net'] = $financeSummary['revenues'] - $financeSummary['expenses'];

            return [
                'employee_count' => $employeeCount,
                'project_count' => $projectCount,
                'task_stats' => $taskStats,
                'finance_summary' => $financeSummary,
            ];
        });

        return response()->json($data);
    }
}

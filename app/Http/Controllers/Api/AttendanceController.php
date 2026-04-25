<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Traits\CachesQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    use CachesQueries;

    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $cacheKey = $this->orgCacheKey('attendances', $orgId) . ':' . md5($request->getQueryString() ?? '');

        $data = $this->cached($cacheKey, 30, function () use ($request, $orgId) {
            $query = Attendance::where('organization_id', $orgId)->with('employee');

            if ($date = $request->get('date')) {
                $query->whereDate('date', $date);
            }

            if ($from = $request->get('from')) {
                $query->whereDate('date', '>=', $from);
            }

            if ($to = $request->get('to')) {
                $query->whereDate('date', '<=', $to);
            }

            if ($employeeId = $request->get('employee_id')) {
                $query->where('employee_id', $employeeId);
            }

            return $query->latest('date')->paginate($request->get('per_page', 15))->toArray();
        });

        return response()->json($data);
    }

    public function clockIn(Request $request): JsonResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        abort_if(! $employee, 403, 'Aucun profil employé associé.');

        $request->validate([
            'poste' => ['nullable', 'string', 'max:100'],
        ]);

        $today = now()->toDateString();

        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Pointage déjà effectué aujourd\'hui.'], 422);
        }

        $attendance = Attendance::create([
            'organization_id' => $user->organization_id,
            'employee_id' => $employee->id,
            'date' => $today,
            'arrivee' => now()->format('H:i'),
            'statut' => 'Présent',
            'poste' => $request->get('poste'),
        ]);

        $this->clearOrgCache('attendances', $user->organization_id);

        return response()->json($attendance, 201);
    }

    public function clockOut(Request $request): JsonResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        abort_if(! $employee, 403, 'Aucun profil employé associé.');

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (! $attendance) {
            return response()->json(['message' => 'Aucun pointage trouvé pour aujourd\'hui.'], 422);
        }

        $attendance->update(['depart' => now()->format('H:i')]);
        $this->clearOrgCache('attendances', $user->organization_id);

        return response()->json($attendance);
    }

    public function stats(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $cacheKey = $this->orgCacheKey('attendance_stats', $orgId);

        $data = $this->cached($cacheKey, 60, function () use ($orgId) {
            $today = now()->toDateString();

            return [
                'present' => Attendance::where('organization_id', $orgId)
                    ->whereDate('date', $today)->where('statut', 'Présent')->count(),
                'absent' => Attendance::where('organization_id', $orgId)
                    ->whereDate('date', $today)->where('statut', 'Absent')->count(),
                'late' => Attendance::where('organization_id', $orgId)
                    ->whereDate('date', $today)->where('statut', 'Retard')->count(),
            ];
        });

        return response()->json($data);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $orgId = $request->user()->organization_id;
        $attendances = Attendance::where('organization_id', $orgId)->with('employee')->get();

        return response()->streamDownload(function () use ($attendances) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employé', 'Date', 'Arrivée', 'Départ', 'Statut', 'Poste']);

            foreach ($attendances as $att) {
                fputcsv($handle, [
                    $att->employee?->name, $att->date?->format('Y-m-d'),
                    $att->arrivee, $att->depart, $att->statut, $att->poste,
                ]);
            }

            fclose($handle);
        }, 'presences.csv', ['Content-Type' => 'text/csv']);
    }
}

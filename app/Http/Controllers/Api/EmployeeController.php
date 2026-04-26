<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Traits\CachesQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    use CachesQueries;

    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $cacheKey = $this->versionedOrgCacheKey('employees', $orgId, md5($request->getQueryString() ?? ''));

        $data = $this->cached($cacheKey, 60, function () use ($request, $orgId) {
            $query = Employee::where('organization_id', $orgId);

            if ($search = $request->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%");
                });
            }

            if ($status = $request->get('status')) {
                $query->where('status', $status);
            }

            return $query->latest()->paginate($request->get('per_page', 15))->toArray();
        });

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:Active,Inactive,On Leave'],
            'joined_at' => ['nullable', 'date'],
            'create_account' => ['nullable', 'boolean'],
            'identifiant' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $orgId = $request->user()->organization_id;

        $employee = DB::transaction(function () use ($request, $orgId) {
            $userId = null;

            if ($request->boolean('create_account')) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->identifiant,
                    'password' => Hash::make($request->password),
                    'role' => 'EMPLOYEE',
                    'organization_id' => $orgId,
                ]);
                $userId = $user->id;
            }

            return Employee::create([
                'organization_id' => $orgId,
                'user_id' => $userId,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'department' => $request->department,
                'status' => $request->get('status', 'Active'),
                'joined_at' => $request->joined_at,
            ]);
        });

        $this->clearOrgCache('employees', $orgId);

        return response()->json($employee, 201);
    }

    public function show(Request $request, Employee $employee): JsonResponse
    {
        $this->authorizeOrg($request, $employee);

        return response()->json($employee->load('user'));
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $this->authorizeOrg($request, $employee);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:Active,Inactive,On Leave'],
            'joined_at' => ['nullable', 'date'],
        ]);

        $employee->update($request->only([
            'name', 'email', 'phone', 'role', 'department', 'status', 'joined_at',
        ]));

        $this->clearOrgCache('employees', $request->user()->organization_id);

        return response()->json($employee);
    }

    public function destroy(Request $request, Employee $employee): JsonResponse
    {
        $this->authorizeOrg($request, $employee);

        DB::transaction(function () use ($employee) {
            if ($employee->user_id) {
                User::destroy($employee->user_id);
            }
            $employee->delete();
        });

        $this->clearOrgCache('employees', $request->user()->organization_id);

        return response()->json(['message' => 'Employé supprimé.']);
    }

    public function toggleStatus(Request $request, Employee $employee): JsonResponse
    {
        $this->authorizeOrg($request, $employee);

        $employee->update([
            'status' => $employee->status === 'Active' ? 'Inactive' : 'Active',
        ]);

        $this->clearOrgCache('employees', $request->user()->organization_id);

        return response()->json($employee);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $orgId = $request->user()->organization_id;
        $employees = Employee::where('organization_id', $orgId)->get();

        return response()->streamDownload(function () use ($employees) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nom', 'Email', 'Téléphone', 'Poste', 'Département', 'Statut', 'Date embauche']);

            foreach ($employees as $emp) {
                fputcsv($handle, [
                    $emp->name, $emp->email, $emp->phone, $emp->role,
                    $emp->department, $emp->status, $emp->joined_at?->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, 'employes.csv', ['Content-Type' => 'text/csv']);
    }

    private function authorizeOrg(Request $request, Employee $employee): void
    {
        abort_if($employee->organization_id !== $request->user()->organization_id, 403);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyPayment;
use App\Models\Organization;
use App\Models\User;
use App\Traits\CachesQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    use CachesQueries;

    public function dashboard(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $data = $this->cached('super_admin:dashboard', 120, function () {
            return [
                'total_organizations' => Organization::count(),
                'total_users' => User::count(),
                'total_revenue' => (float) CompanyPayment::where('statut', 'Validé')->sum('montant'),
                'active_organizations' => Organization::where('status', 'active')->count(),
            ];
        });

        return response()->json($data);
    }

    public function organizations(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $query = Organization::withCount('users', 'employees');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $organizations = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($organizations);
    }

    public function storeOrganization(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'nif' => ['nullable', 'string', 'max:100'],
            'plan' => ['nullable', 'string', 'max:50'],
            'monthly_fee' => ['nullable', 'numeric'],
            'modules' => ['nullable', 'array'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['nullable', 'email', 'unique:users,email'],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ]);

        $org = Organization::create($request->only([
            'name', 'domain', 'address', 'phone', 'email', 'company_email',
            'nif', 'plan', 'monthly_fee', 'modules',
        ]));

        if ($request->filled('admin_email')) {
            User::create([
                'name' => $request->admin_name ?? $request->name . ' Admin',
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password ?? 'password'),
                'role' => 'ORG_ADMIN',
                'organization_id' => $org->id,
            ]);
        }

        return response()->json($org->loadCount('users'), 201);
    }

    public function updateOrganization(Request $request, Organization $organization): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'nif' => ['nullable', 'string', 'max:100'],
            'plan' => ['nullable', 'string', 'max:50'],
            'monthly_fee' => ['nullable', 'numeric'],
            'modules' => ['nullable', 'array'],
        ]);

        $organization->update($request->only([
            'name', 'domain', 'address', 'phone', 'email', 'company_email',
            'nif', 'plan', 'monthly_fee', 'modules',
        ]));

        return response()->json($organization);
    }

    public function toggleOrganizationStatus(Request $request, Organization $organization): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $organization->update([
            'status' => $organization->status === 'active' ? 'suspended' : 'active',
        ]);

        return response()->json($organization);
    }

    public function deleteOrganization(Request $request, Organization $organization): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $organization->delete();

        return response()->json(['message' => 'Organisation supprimée.']);
    }

    public function toggleModule(Request $request, Organization $organization): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'module' => ['required', 'string'],
        ]);

        $modules = $organization->modules ?? [];
        $module = $request->module;

        if (in_array($module, $modules)) {
            $modules = array_values(array_diff($modules, [$module]));
        } else {
            $modules[] = $module;
        }

        $organization->update(['modules' => $modules]);

        return response()->json($organization);
    }

    public function payments(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $payments = CompanyPayment::with(['organization:id,name', 'paymentMethod:id,bank_name,account_number,type'])
            ->latest('date')
            ->paginate($request->get('per_page', 15));

        return response()->json($payments);
    }

    public function validatePayment(Request $request, CompanyPayment $payment): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $payment->update(['statut' => 'Validé']);

        return response()->json($payment);
    }

    public function rejectPayment(Request $request, CompanyPayment $payment): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $payment->update(['statut' => 'Rejeté']);

        return response()->json($payment);
    }
}

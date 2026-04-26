<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinanceRecord;
use App\Traits\CachesQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    use CachesQueries;

    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $cacheKey = $this->versionedOrgCacheKey('finances', $orgId, md5($request->getQueryString() ?? ''));

        $data = $this->cached($cacheKey, 60, function () use ($request, $orgId) {
            $query = FinanceRecord::where('organization_id', $orgId);

            if ($type = $request->get('type')) {
                $query->where('type', $type);
            }

            return $query->latest('date')->paginate($request->get('per_page', 15))->toArray();
        });

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:Revenu,Dépense'],
            'montant' => ['required', 'numeric', 'min:0'],
            'statut' => ['nullable', 'in:Validé,Encaissé,En attente'],
        ]);

        $orgId = $request->user()->organization_id;

        $record = FinanceRecord::create([
            'organization_id' => $orgId,
            ...$request->only(['date', 'description', 'type', 'montant', 'statut']),
        ]);

        $this->clearOrgCache('finances', $orgId);
        $this->clearOrgCache('finance_summary', $orgId);

        return response()->json($record, 201);
    }

    public function destroy(Request $request, FinanceRecord $finance): JsonResponse
    {
        abort_if($finance->organization_id !== $request->user()->organization_id, 403);

        $finance->delete();
        $this->clearOrgCache('finances', $request->user()->organization_id);
        $this->clearOrgCache('finance_summary', $request->user()->organization_id);

        return response()->json(['message' => 'Enregistrement supprimé.']);
    }

    public function summary(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;
        $cacheKey = $this->orgCacheKey('finance_summary', $orgId);

        $data = $this->cached($cacheKey, 120, function () use ($orgId) {
            $revenues = (float) FinanceRecord::where('organization_id', $orgId)
                ->where('type', 'Revenu')->sum('montant');

            $expenses = (float) FinanceRecord::where('organization_id', $orgId)
                ->where('type', 'Dépense')->sum('montant');

            return [
                'revenues' => $revenues,
                'expenses' => $expenses,
                'net_balance' => $revenues - $expenses,
            ];
        });

        return response()->json($data);
    }
}

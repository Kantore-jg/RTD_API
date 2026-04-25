<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $payments = CompanyPayment::where('organization_id', $orgId)
            ->latest('date')
            ->paginate($request->get('per_page', 15));

        return response()->json($payments);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:0'],
            'receipt' => ['nullable', 'file', 'max:5120'],
            'account' => ['nullable', 'string', 'max:255'],
            'statut' => ['nullable', 'string'],
        ]);

        $orgId = $request->user()->organization_id;
        $data = [
            'organization_id' => $orgId,
            ...$request->only(['date', 'description', 'montant', 'account', 'statut']),
        ];

        if ($request->hasFile('receipt')) {
            $data['receipt'] = $request->file('receipt')->store("receipts/{$orgId}", 'public');
        }

        $payment = CompanyPayment::create($data);

        return response()->json($payment, 201);
    }

    public function destroy(Request $request, CompanyPayment $companyPayment): JsonResponse
    {
        abort_if($companyPayment->organization_id !== $request->user()->organization_id, 403);

        $companyPayment->delete();

        return response()->json(['message' => 'Paiement supprimé.']);
    }
}

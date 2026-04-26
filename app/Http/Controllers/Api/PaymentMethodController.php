<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        return response()->json(PaymentMethod::all());
    }

    public function listAll(): JsonResponse
    {
        return response()->json(PaymentMethod::all());
    }

    public function store(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:100'],
            'account_holder' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:50'],
        ]);

        $method = PaymentMethod::create($request->only([
            'bank_name', 'account_number', 'account_holder', 'type',
        ]));

        return response()->json($method, 201);
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $paymentMethod->delete();

        return response()->json(['message' => 'Méthode de paiement supprimée.']);
    }
}

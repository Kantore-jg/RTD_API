<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $contactMessage = ContactMessage::create($request->only([
            'first_name', 'last_name', 'email', 'phone', 'company', 'subject', 'message',
        ]));

        return response()->json([
            'message' => 'Votre message a été envoyé avec succès.',
            'data' => $contactMessage,
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $messages = ContactMessage::latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($messages);
    }

    public function markRead(Request $request, ContactMessage $message): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $message->update(['read' => true]);

        return response()->json($message);
    }
}

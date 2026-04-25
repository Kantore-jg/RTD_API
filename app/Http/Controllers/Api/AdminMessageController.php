<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $adminMessage = AdminMessage::create([
            'organization_id' => $request->user()->organization_id,
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'read' => false,
        ]);

        return response()->json($adminMessage, 201);
    }

    public function index(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $messages = AdminMessage::with('user:id,name,email', 'organization:id,name')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($messages);
    }

    public function markRead(Request $request, AdminMessage $adminMessage): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $adminMessage->update(['read' => true]);

        return response()->json($adminMessage);
    }
}

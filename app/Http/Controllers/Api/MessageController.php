<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, Channel $channel): JsonResponse
    {
        abort_if($channel->organization_id !== $request->user()->organization_id, 403);

        $messages = $channel->messages()
            ->with('user:id,name,avatar')
            ->oldest()
            ->paginate($request->get('per_page', 50));

        return response()->json($messages);
    }

    public function store(Request $request, Channel $channel): JsonResponse
    {
        abort_if($channel->organization_id !== $request->user()->organization_id, 403);

        $request->validate([
            'text' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'channel_id' => $channel->id,
            'user_id' => $request->user()->id,
            'text' => $request->text,
        ]);

        return response()->json($message->load('user:id,name,avatar'), 201);
    }
}

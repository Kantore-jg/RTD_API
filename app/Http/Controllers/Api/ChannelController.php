<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $channels = Channel::where('organization_id', $orgId)
            ->withCount('messages')
            ->get();

        return response()->json($channels);
    }

    public function store(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:50'],
        ]);

        $channel = Channel::create([
            'organization_id' => $request->user()->organization_id,
            'name' => $request->name,
            'type' => $request->get('type', 'general'),
        ]);

        return response()->json($channel, 201);
    }

    public function destroy(Request $request, Channel $channel): JsonResponse
    {
        abort_if($channel->organization_id !== $request->user()->organization_id, 403);
        abort_if(! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin(), 403);

        $channel->messages()->delete();
        $channel->delete();

        return response()->json(['message' => 'Canal supprimé.']);
    }
}

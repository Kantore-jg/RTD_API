<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $orgId = $user->organization_id;

        $query = Suggestion::where('organization_id', $orgId)->with('user:id,name');

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        }

        $suggestions = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($suggestions);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'text' => ['required', 'string', 'max:1000'],
        ]);

        $suggestion = Suggestion::create([
            'organization_id' => $request->user()->organization_id,
            'user_id' => $request->user()->id,
            'text' => $request->text,
            'votes' => 0,
            'status' => 'open',
        ]);

        return response()->json($suggestion, 201);
    }

    public function vote(Request $request, Suggestion $suggestion): JsonResponse
    {
        abort_if($suggestion->organization_id !== $request->user()->organization_id, 403);

        $suggestion->increment('votes');

        return response()->json($suggestion);
    }

    public function updateStatus(Request $request, Suggestion $suggestion): JsonResponse
    {
        abort_if($suggestion->organization_id !== $request->user()->organization_id, 403);
        abort_if(! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'status' => ['required', 'in:open,implemented,rejected'],
        ]);

        $suggestion->update(['status' => $request->status]);

        return response()->json($suggestion);
    }
}

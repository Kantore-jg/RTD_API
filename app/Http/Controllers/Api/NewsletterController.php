<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $newsletters = Newsletter::latest()->paginate($request->get('per_page', 15));

        return response()->json($newsletters);
    }

    public function store(Request $request): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $newsletter = Newsletter::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'status' => 'draft',
        ]);

        return response()->json($newsletter, 201);
    }

    public function send(Request $request, Newsletter $newsletter): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        if ($newsletter->status === 'sent') {
            return response()->json(['message' => 'Cette newsletter a déjà été envoyée.'], 422);
        }

        $organizations = Organization::where('status', 'active')
            ->where(function ($q) {
                $q->whereNotNull('company_email')->orWhereNotNull('email');
            })
            ->get();

        $sent = 0;
        foreach ($organizations as $org) {
            $recipientEmail = $org->company_email ?: $org->email;
            if (! $recipientEmail) {
                continue;
            }

            Mail::to($recipientEmail)->send(new NewsletterMail($newsletter, $org->name));
            $sent++;
        }

        $newsletter->update([
            'status' => 'sent',
            'recipients_count' => $sent,
            'sent_at' => now(),
        ]);

        return response()->json([
            'message' => "Newsletter envoyée à {$sent} organisation(s).",
            'data' => $newsletter->fresh(),
        ]);
    }

    public function destroy(Request $request, Newsletter $newsletter): JsonResponse
    {
        abort_if(! $request->user()->isSuperAdmin(), 403);

        if ($newsletter->status === 'sent') {
            return response()->json(['message' => 'Impossible de supprimer une newsletter déjà envoyée.'], 422);
        }

        $newsletter->delete();

        return response()->json(['message' => 'Brouillon supprimé.']);
    }

    public function received(Request $request): JsonResponse
    {
        $newsletters = Newsletter::where('status', 'sent')
            ->latest('sent_at')
            ->paginate($request->get('per_page', 10));

        return response()->json($newsletters);
    }
}

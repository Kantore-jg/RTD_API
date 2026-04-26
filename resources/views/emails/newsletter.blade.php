@extends('emails.layout')
@section('subtitle', 'Newsletter')
@section('content')
    <h2>{{ $newsletter->subject }}</h2>

    <div style="white-space: pre-line; line-height: 1.8;">
{!! nl2br(e($newsletter->content)) !!}
    </div>

    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
    <p style="font-size: 12px; color: #94a3b8;">Vous recevez cet email car votre organisation est enregistrée sur Registre Dynamique de Travail.</p>
@endsection

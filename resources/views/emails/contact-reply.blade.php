@extends('emails.layout')
@section('subtitle', 'Réponse à votre message')
@section('content')
    <h2>Bonjour, {{ $recipientName }}</h2>
    <p>Nous avons bien reçu votre message concernant « <strong>{{ $originalSubject }}</strong> » et voici notre réponse :</p>

    <div class="info-box">
        <div style="white-space: pre-line; font-size: 14px; color: #1e293b;">{{ $replyBody }}</div>
    </div>

    <p>Si vous avez d'autres questions, n'hésitez pas à nous recontacter.</p>
    <p>Cordialement,<br><strong>L'équipe RDT</strong></p>
@endsection

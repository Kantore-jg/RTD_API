@extends('emails.layout')
@section('subtitle', 'Bienvenue sur la plateforme')
@section('content')
    <h2>Bienvenue, {{ $organization->name }} !</h2>
    <p>Votre compte entreprise a été créé avec succès sur la plateforme <strong>Registre Dynamique de Travail</strong>.</p>
    <p>Vous pouvez dès maintenant vous connecter et commencer à gérer votre entreprise.</p>

    <div class="info-box">
        <div class="row"><span class="label">Entreprise</span><span class="value">{{ $organization->name }}</span></div>
        <div class="row"><span class="label">Email de connexion</span><span class="value">{{ $adminEmail }}</span></div>
        <div class="row"><span class="label">Mot de passe</span><span class="value">{{ $plainPassword }}</span></div>
        <div class="row"><span class="label">Plan</span><span class="value">{{ ucfirst($organization->plan ?? 'trial') }}</span></div>
    </div>

    <p style="color: #ef4444; font-size: 13px; font-weight: 600;">⚠ Veuillez changer votre mot de passe dès votre première connexion.</p>

    <a href="{{ config('app.frontend_url', 'http://34.59.117.62:84') }}/login" class="btn">Se connecter</a>

    <p>Si vous avez des questions, n'hésitez pas à nous contacter via la page de contact de la plateforme.</p>
@endsection

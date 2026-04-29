@extends('emails.layout')
@section('subtitle', 'Nouveau collaborateur')
@section('content')
    <h2>Bienvenue, {{ $employee->name }} !</h2>
    <p>Vous avez été ajouté(e) à l'équipe de <strong>{{ $orgName }}</strong> sur la plateforme Registre Dynamique de Travail.</p>

    <div class="info-box">
        <div class="row"><span class="label">Nom</span><span class="value">{{ $employee->name }}</span></div>
        @if($employee->department)
        <div class="row"><span class="label">Département</span><span class="value">{{ $employee->department }}</span></div>
        @endif
        @if($employee->role)
        <div class="row"><span class="label">Poste</span><span class="value">{{ $employee->role }}</span></div>
        @endif
        @if($loginEmail)
        <div class="row"><span class="label">Email de connexion</span><span class="value">{{ $loginEmail }}</span></div>
        <div class="row"><span class="label">Mot de passe</span><span class="value">{{ $plainPassword }}</span></div>
        @endif
    </div>

    @if($loginEmail)
    <p style="color: #ef4444; font-size: 13px; font-weight: 600;">⚠ Veuillez changer votre mot de passe dès votre première connexion.</p>
    <a href="{{ config('app.frontend_url', 'http://34.59.117.62:84') }}/login" class="btn">Se connecter</a>
    @else
    <p>Votre administrateur vous communiquera vos accès de connexion si nécessaire.</p>
    @endif
@endsection

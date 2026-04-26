@extends('emails.layout')
@section('subtitle', 'Statut de votre compte')
@section('content')
    <h2>Mise à jour du statut de votre compte</h2>
    <p>Le statut de votre organisation <strong>{{ $organization->name }}</strong> a été modifié.</p>

    <div class="info-box">
        <div class="row">
            <span class="label">Nouveau statut</span>
            <span>
                @if($newStatus === 'active')
                    <span class="badge-success">✓ Actif</span>
                @else
                    <span class="badge-danger">✗ Suspendu</span>
                @endif
            </span>
        </div>
    </div>

    @if($newStatus === 'active')
    <p>Votre compte a été réactivé. Vous pouvez à nouveau accéder à la plateforme avec tous vos modules.</p>
    <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/login" class="btn">Se connecter</a>
    @else
    <p>Votre compte a été suspendu. L'accès à la plateforme est temporairement désactivé pour votre organisation et tous vos employés.</p>
    <p>Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administration de la plateforme.</p>
    @endif
@endsection

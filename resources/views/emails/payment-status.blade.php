@extends('emails.layout')
@section('subtitle', 'Mise à jour de paiement')
@section('content')
    <h2>Mise à jour de votre paiement</h2>
    <p>Le paiement de <strong>{{ $orgName }}</strong> a été traité par l'administration.</p>

    <div class="info-box">
        <div class="row"><span class="label">Date</span><span class="value">{{ $payment->date }}</span></div>
        <div class="row"><span class="label">Montant</span><span class="value">{{ number_format($payment->montant, 0, ',', ' ') }} BIF</span></div>
        <div class="row">
            <span class="label">Statut</span>
            <span>
                @if($status === 'Validé')
                    <span class="badge-success">✓ Validé</span>
                @else
                    <span class="badge-danger">✗ Rejeté</span>
                @endif
            </span>
        </div>
    </div>

    @if($status === 'Validé')
    <p>Merci pour votre paiement. Votre abonnement est bien actif.</p>
    @else
    <p>Votre paiement a été rejeté. Veuillez vérifier les informations et soumettre un nouveau paiement, ou contacter l'administration pour plus de détails.</p>
    @endif
@endsection

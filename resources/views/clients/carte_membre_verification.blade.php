@extends('layouts.app')

@section('page_title', 'Vérification carte membre')
@section('breadcrumb_parent', 'Clients')
@section('breadcrumb', 'Vérification carte')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="row justify-content-center">
<div class="col-md-6">
    <div class="card card-outline {{ $carte->statut === 'REVOQUEE' ? 'card-danger' : 'card-success' }}">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-id-card mr-2"></i>Vérification de carte membre
            </h5>
        </div>
        <div class="card-body text-center">
            @if($client->photo)
                <img src="{{ route('clients.photo', basename($client->photo)) }}" alt="Photo"
                     style="width:110px;height:130px;object-fit:cover;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.2);">
            @endif
            <h4 class="mt-3 mb-0">{{ $client->full_name }}</h4>
            <p class="text-muted mb-2"><code>{{ $client->matricule }}</code></p>

            @if($carte->statut === 'REVOQUEE')
                <span class="badge badge-danger p-2"><i class="fas fa-ban mr-1"></i>Carte révoquée / invalide</span>
            @elseif($carte->statut === 'IMPRIMEE')
                <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i>Carte active</span>
            @else
                <span class="badge badge-warning p-2"><i class="fas fa-clock mr-1"></i>Payée, non encore imprimée</span>
            @endif

            <table class="table table-sm table-borderless small text-left mt-3 mb-0">
                <tr><th>Agence / Zone</th><td>{{ $client->zone->nom ?? '—' }}</td></tr>
                <tr><th>Carte N°</th><td>{{ str_pad((string) $carte->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
                <tr><th>Émise le</th><td>{{ $carte->created_at->format('d/m/Y') }}</td></tr>
            </table>
        </div>
    </div>
    <p class="text-muted small text-center">
        <i class="fas fa-shield-alt mr-1"></i>Cette page ne divulgue jamais le solde ni le numéro de compte du client.
    </p>
</div>
</div>

</div>
</section>
@endsection

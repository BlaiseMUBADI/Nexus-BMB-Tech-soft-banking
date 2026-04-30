@extends('layouts.app')

@section('page_title', 'Détail compte — ' . $compte->code_compte)
@section('breadcrumb_parent', 'Gestion des comptes')
@section('breadcrumb', 'Détail du compte')

@push('css')
<style>
    .compte-show-wrapper .profile-box { min-height: 100%; }

    .compte-show-wrapper .profile-photo {
        width: 132px;
        height: 132px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.06);
        display: block;
        margin: 0 auto;
    }

    .compte-show-wrapper .section-card .card-header {
        font-weight: 600;
    }

    .compte-show-wrapper .kv-table th {
        width: 34%;
        font-weight: 600;
    }

    .compte-show-wrapper .kv-table th,
    .compte-show-wrapper .kv-table td {
        border-top: 0;
        padding-top: 0.36rem;
        padding-bottom: 0.36rem;
    }

    .compte-show-wrapper .mini-kpi {
        border-radius: 4px;
        padding: 0.6rem 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
        margin-bottom: 0.5rem;
    }

    .compte-show-wrapper .mini-kpi .label {
        font-size: .78rem;
        color: #9fb0c0;
        margin-bottom: 0.1rem;
    }

    .compte-show-wrapper .mini-kpi .value {
        font-weight: 700;
        font-size: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid compte-show-wrapper">
    @php
        $client = $compte->client;
        $pfAgent = $compte->portefeuille?->affectationActive?->agent ?? $compte->portefeuille?->agent;
        $clientNom = $client
            ? ($client->full_name ?? trim(($client->nom ?? '') . ' ' . ($client->postnom ?? '') . ' ' . ($client->prenom ?? '')))
            : 'Client non renseigné';
        $clientPhotoUrl = !empty(optional($client)->photo)
            ? route('clients.photo', basename($client->photo))
            : null;
        $typeLabels = [
            'CC' => 'Compte courant',
            'RMB' => 'Remboursement',
            'GTC' => 'Caution',
            'DAT' => 'Dépôt à terme',
            'EAV' => 'Épargne & Vie',
        ];
        $typeBadge = [
            'CC' => 'badge-info',
            'RMB' => 'badge-secondary',
            'GTC' => 'badge-primary',
            'DAT' => 'badge-warning',
            'EAV' => 'badge-success',
        ][$compte->type] ?? 'badge-light';
    @endphp

    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title mb-0">
                <i class="fas fa-university mr-2"></i>{{ $clientNom }}
                <small class="text-muted ml-2">{{ $compte->code_compte }}</small>
            </h3>
            <div class="mt-2 mt-md-0">
                <a href="{{ route('comptes.edit', $compte->code_compte) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-edit mr-1"></i>Modifier
                </a>
                <a href="{{ route('comptes.historique', $compte->code_compte) }}" class="btn btn-sm btn-primary ml-1">
                    <i class="fas fa-history mr-1"></i>Historique
                </a>
                <a href="{{ route('comptes.index') }}" class="btn btn-sm btn-secondary ml-1">
                    <i class="fas fa-arrow-left mr-1"></i>Retour
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 mb-3">
                    <div class="card card-outline card-secondary profile-box mb-0">
                        <div class="card-body text-center">
                            @if($clientPhotoUrl)
                                <img src="{{ $clientPhotoUrl }}" alt="Photo client" class="profile-photo mb-2">
                            @else
                                <div class="profile-photo d-flex align-items-center justify-content-center mb-2">
                                    <i class="fas fa-user fa-3x text-muted"></i>
                                </div>
                                <small class="text-muted">Aucune photo</small>
                            @endif
                            <hr>
                            <div class="small text-muted">Matricule client</div>
                            <div class="font-weight-bold">{{ $client->matricule ?? '-' }}</div>
                            @if($compte->devise)
                                <div class="small text-muted mt-2">Devise de tenue</div>
                                <div class="font-weight-bold">{{ $compte->devise }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="card card-outline card-info section-card mb-3">
                        <div class="card-header py-2"><i class="fas fa-id-card mr-1"></i>Identité & rattachement</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless kv-table mb-0">
                                        <tr>
                                            <th>Client</th>
                                            <td>{{ $clientNom }}</td>
                                        </tr>
                                        <tr>
                                            <th>Code compte</th>
                                            <td><code>{{ $compte->code_compte }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Portefeuille</th>
                                            <td>
                                                @if($pfAgent)
                                                    {{ trim(($pfAgent->nom ?? '') . ' ' . ($pfAgent->prenom ?? '')) }}
                                                    <br><small class="text-muted">{{ $pfAgent->matricule }}</small>
                                                @else
                                                    <span class="text-muted">Non affecté</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless kv-table mb-0">
                                        <tr>
                                            <th>Date création</th>
                                            <td>{{ optional($compte->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dernière MAJ</th>
                                            <td>{{ optional($compte->updated_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Devise</th>
                                            <td><span class="badge badge-secondary">{{ $compte->devise }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-warning section-card mb-0">
                        <div class="card-header py-2"><i class="fas fa-wallet mr-1"></i>Synthèse financière du compte</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mini-kpi">
                                        <div class="label">Type de compte</div>
                                        <div class="value"><span class="badge {{ $typeBadge }}">{{ $compte->type }} - {{ $typeLabels[$compte->type] ?? $compte->type }}</span></div>
                                    </div>
                                    <div class="mini-kpi">
                                        <div class="label">Solde réel</div>
                                        <div class="value text-success">{{ number_format((float) $compte->solde_reel, 2, ',', ' ') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mini-kpi">
                                        <div class="label">Solde bloqué</div>
                                        <div class="value text-warning">{{ number_format((float) $compte->solde_bloque, 2, ',', ' ') }}</div>
                                    </div>
                                    <div class="mini-kpi mb-0">
                                        <div class="label">Total positions</div>
                                        <div class="value">{{ number_format((float) $compte->solde_reel + (float) $compte->solde_bloque, 2, ',', ' ') }} {{ $compte->devise }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

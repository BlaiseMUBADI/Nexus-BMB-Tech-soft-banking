@extends('layouts.app')

@section('page_title', 'Tableau de bord – Crédits')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Tableau de bord')

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- ── Titre ────────────────────────────────────────────── --}}
    <div class="row mb-3">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="mb-0"><i class="fas fa-hand-holding-usd mr-2 text-warning"></i>Tableau de bord – Module Crédit</h4>
            <a href="{{ route('credit.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle mr-1"></i> Nouvelle demande
            </a>
        </div>
    </div>

    {{-- ── Statistiques ─────────────────────────────────────── --}}
    <div class="row">
        {{-- Total dossiers --}}
        <div class="col-6 col-md-3">
            <div class="small-box bg-gradient-dark">
                <div class="inner"><h3>{{ $stats['total'] }}</h3><p>Total dossiers</p></div>
                <div class="icon"><i class="fas fa-folder-open"></i></div>
                <a href="{{ route('credit.index') }}" class="small-box-footer">Voir tout <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- Brouillons --}}
        <div class="col-6 col-md-3">
            <div class="small-box bg-gradient-secondary">
                <div class="inner"><h3>{{ $stats['brouillons'] }}</h3><p>Brouillons</p></div>
                <div class="icon"><i class="fas fa-pencil-alt"></i></div>
                <a href="{{ route('credit.index') }}?statut=BROUILLON" class="small-box-footer">Voir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- En analyse --}}
        <div class="col-6 col-md-3">
            <div class="small-box bg-gradient-primary">
                <div class="inner"><h3>{{ $stats['en_analyse'] }}</h3><p>En analyse</p></div>
                <div class="icon"><i class="fas fa-search-dollar"></i></div>
                <a href="{{ route('credit.index') }}?statut=EN_ANALYSE" class="small-box-footer">Voir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- En validation --}}
        <div class="col-6 col-md-3">
            <div class="small-box bg-gradient-warning">
                <div class="inner"><h3>{{ $stats['en_validation'] }}</h3><p>En validation</p></div>
                <div class="icon"><i class="fas fa-check-double"></i></div>
                <a href="{{ route('credit.index') }}?statut=EN_VALIDATION" class="small-box-footer">Voir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Prêts à débloquer --}}
        <div class="col-6 col-md-3">
            <div class="small-box bg-gradient-success">
                <div class="inner"><h3>{{ $stats['pret_a_debloquer'] }}</h3><p>Prêts à débloquer</p></div>
                <div class="icon"><i class="fas fa-unlock-alt"></i></div>
                <a href="{{ route('credit.index') }}?statut=PRET_A_DEBLOQUER" class="small-box-footer">Débloquer <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- En remboursement --}}
        <div class="col-6 col-md-3">
            <div class="small-box" style="background:#1a7a4a;color:#fff;">
                <div class="inner"><h3>{{ $stats['en_remboursement'] }}</h3><p>En remboursement</p></div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                <a href="{{ route('credit.index') }}?statut=EN_REMBOURSEMENT" class="small-box-footer" style="color:#fff;">Voir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- En retard --}}
        <div class="col-6 col-md-3">
            <div class="small-box bg-gradient-danger">
                <div class="inner"><h3>{{ $stats['en_retard'] }}</h3><p>En retard</p></div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="{{ route('credit.index') }}?statut=EN_RETARD" class="small-box-footer">Voir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- Soldés --}}
        <div class="col-6 col-md-3">
            <div class="small-box bg-gradient-dark">
                <div class="inner"><h3>{{ $stats['solde'] }}</h3><p>Soldés</p></div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <a href="{{ route('credit.index') }}?statut=SOLDE" class="small-box-footer">Voir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Annulés / Suspendus / Suspects --}}
        <div class="col-md-4">
            <div class="info-box {{ $stats['annule'] > 0 ? 'bg-danger' : '' }}">
                <span class="info-box-icon"><i class="fas fa-ban"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Annulés</span>
                    <span class="info-box-number">{{ $stats['annule'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box {{ $stats['suspendu'] > 0 ? 'bg-warning' : '' }}">
                <span class="info-box-icon"><i class="fas fa-pause-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Suspendus</span>
                    <span class="info-box-number">{{ $stats['suspendu'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box {{ $stats['suspect'] > 0 ? 'bg-danger' : '' }}">
                <span class="info-box-icon"><i class="fas fa-user-secret"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Suspects</span>
                    <span class="info-box-number">{{ $stats['suspect'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Montants ─────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-success">
                <div class="card-header"><h5 class="card-title"><i class="fas fa-coins mr-2"></i>Montant total débloqué</h5></div>
                <div class="card-body text-center">
                    <h2 class="text-success font-weight-bold">
                        {{ number_format($stats['montant_total_debloque'], 2, ',', ' ') }} CDF
                    </h2>
                    <p class="text-muted">Tous les crédits débloqués, en cours et soldés</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header"><h5 class="card-title"><i class="fas fa-hand-holding-usd mr-2"></i>Montant total recouvré</h5></div>
                <div class="card-body text-center">
                    <h2 class="text-primary font-weight-bold">
                        {{ number_format($stats['montant_total_a_recouvrer'], 2, ',', ' ') }} CDF
                    </h2>
                    <p class="text-muted">Somme de tous les remboursements enregistrés</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Derniers dossiers ────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-history mr-2"></i>Derniers dossiers créés</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>N° Dossier</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniersDossiers as $d)
                    <tr>
                        <td><code>{{ $d->numero_dossier }}</code></td>
                        <td>{{ $d->client?->nom }} {{ $d->client?->prenom }}</td>
                        <td>{{ number_format($d->montant_demande, 0, ',', ' ') }} {{ $d->devise }}</td>
                        <td>{{ $d->duree_mois }} mois</td>
                        <td>{!! $d->badgeStatut() !!}</td>
                        <td>{{ $d->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Aucun dossier crédit pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-right">
            <a href="{{ route('credit.index') }}" class="btn btn-sm btn-outline-primary">Voir tous les dossiers</a>
        </div>
    </div>

</div>
</section>
@endsection

{{-- ============================================================
     Rapport de Fin de Journée — Guichets FIXE (bureau)
     Récapitulatif des opérations + soldes actuels
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'Rapport de Fin de Journée')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Rapport Journalier')

@section('content')
<div class="container-fluid">

    @php
        $totauxParDevise = collect($stats['par_devise'] ?? []);
        $soldesActuels = collect($stats['soldes_actuels'] ?? []);
        $parType = collect($stats['par_type'] ?? []);
    @endphp

    @if(!$guichet)
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-warning card-outline shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                    <h4>Aucun guichet affecté</h4>
                </div>
            </div>
        </div>
    </div>
    @else

    {{-- ── Sélecteur de date ──────────────────────────────────── --}}
    <div class="card card-outline card-info shadow-sm mb-3 rapport-filter-shell">
        <div class="card-header py-2 d-flex align-items-center justify-content-between rapport-filter-shell-head">
            <h5 class="mb-0">
                <i class="fas fa-filter mr-2 text-info"></i>
                <strong>Filtres</strong>
            </h5>
            <div class="d-flex align-items-center rapport-filter-shell-tools">
                <a id="btnChargerRapport" href="#" class="btn btn-info btn-sm rapport-filter-btn">
                    <i class="fas fa-sync-alt mr-1"></i> Afficher
                </a>
            </div>
        </div>
        <div class="card-body py-3">
            <div class="row align-items-end">
                <div class="col-12 col-md-4 col-lg-3 mb-2 mb-md-0">
                    <label class="mb-1 small font-weight-bold">
                        <i class="fas fa-calendar-day mr-1 text-info"></i> Date
                    </label>
                    <input type="date" class="form-control form-control-sm rapport-filter-control" id="filtreDate"
                           value="{{ $date }}">
                </div>
                <div class="col-12 col-md-8 col-lg-9">
                    <div class="rapport-guichet-badge">
                        <i class="fas fa-desktop mr-1 text-secondary"></i>
                        {{ $guichet->code_guichet }} — {{ $guichet->intitule }}
                        <span class="badge badge-{{ $guichet->statut_operationnel === 'OUVERT' ? 'success' : 'secondary' }} px-2 ml-2">
                            {{ $guichet->statut_operationnel }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-3 col-sm-6 col-12 mb-2">
            <div class="small-box bg-info shadow elevation-2 mb-0">
                <div class="inner">
                    <h4>{{ $stats['total_operations'] }}</h4>
                    <p>Total opérations</p>
                </div>
                <div class="icon"><i class="fas fa-exchange-alt"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-2">
            <div class="small-box bg-primary shadow elevation-2 mb-0">
                <div class="inner">
                    <h4>{{ $parType->count() }}</h4>
                    <p>Types d'opérations</p>
                </div>
                <div class="icon"><i class="fas fa-layer-group"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-2">
            <div class="small-box bg-success shadow elevation-2 mb-0">
                <div class="inner">
                    <h4>{{ $totauxParDevise->count() }}</h4>
                    <p>Devises actives</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-2">
            <div class="small-box bg-warning shadow elevation-2 mb-0">
                <div class="inner">
                    <h4>{{ $soldesActuels->count() }}</h4>
                    <p>Soldes configurés</p>
                </div>
                <div class="icon"><i class="fas fa-wallet"></i></div>
            </div>
        </div>
    </div>

    {{-- ── Soldes actuels ─────────────────────────────────────── --}}
    @if($soldesActuels->isNotEmpty())
    <div class="row mb-3">
        @foreach($soldesActuels as $s)
        <div class="col-sm-4 col-md-3 col-lg-2 mb-2">
            <div class="info-box shadow-sm mb-0"
                 style="background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.10);">
                <span class="info-box-icon" style="background:rgba(40,167,69,.2);">
                    <span class="font-weight-bold text-success">{{ $s['symbole'] }}</span>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted" style="font-size:.78rem;">{{ $s['devise_code'] }}</span>
                    <span class="info-box-number" style="font-size:1.05rem;">{{ $s['solde_fmt'] }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($stats['total_operations'] === 0)
    {{-- ── Aucune opération ───────────────────────────────────── --}}
    <div class="card card-outline card-secondary shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucune opération confirmée pour cette journée.</h5>
            <p class="text-muted small">Sélectionnez une autre date ou effectuez des opérations.</p>
        </div>
    </div>
    @else

    @if($totauxParDevise->isNotEmpty())
    <div class="row mb-3">
        @foreach($totauxParDevise as $resume)
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <div class="card border-0 shadow-sm h-100 rapport-devise-card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge badge-light border px-2 py-1">{{ $resume['devise'] }}</span>
                        <small class="text-muted">{{ $resume['count'] }} op.</small>
                    </div>
                    <div class="small text-success font-weight-bold mb-1">Entrées : {{ number_format($resume['total_entrees'], 2, ',', ' ') }}</div>
                    <div class="small text-danger font-weight-bold mb-1">Sorties : {{ number_format($resume['total_sorties'], 2, ',', ' ') }}</div>
                    <div class="small text-info font-weight-bold mb-1">Volume : {{ number_format($resume['volume_total'], 2, ',', ' ') }}</div>
                    <div class="small font-weight-bold {{ $resume['net'] >= 0 ? 'text-success' : 'text-danger' }}">Net : {{ number_format($resume['net'], 2, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Résumé par type ────────────────────────────────────── --}}
    <div class="row mb-3">
        <div class="col-12">
            <h6 class="text-muted text-uppercase mb-2" style="font-size:.82rem; letter-spacing:.08em;">
                <i class="fas fa-chart-pie mr-1"></i>
                Résumé — {{ $stats['total_operations'] }} opération(s) confirmée(s) le {{ \Carbon\Carbon::parse($date)->isoFormat('dddd D MMMM YYYY') }}
            </h6>
        </div>
        @foreach($stats['par_type'] as $typeData)
        <div class="col-sm-6 col-md-4 col-lg-3 mb-2">
            <div class="card card-outline shadow-sm h-100"
                 style="border-top: 3px solid rgba(255,255,255,.15);">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge {{ $typeData['badge'] }} mr-2">{{ $typeData['label'] }}</span>
                        <strong>{{ $typeData['count'] }} opération(s)</strong>
                    </div>
                    @foreach($typeData['par_devise'] as $dev)
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">{{ $dev['devise'] }}</span>
                        <strong>{{ $dev['fmt'] }}</strong>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Tableau récapitulatif ──────────────────────────────── --}}
    @if(!empty($stats['ops_recentes']) && count($stats['ops_recentes']))
    <div class="card card-outline card-secondary shadow">
        <div class="card-header py-2">
            <h6 class="mb-0">
                <i class="fas fa-list mr-1 text-secondary"></i>
                Détail des {{ count($stats['ops_recentes']) }} dernières opérations
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['ops_recentes'] as $op)
                        <tr>
                            <td><small class="text-monospace">{{ $op['reference'] }}</small></td>
                            <td><span class="badge {{ $op['badge_class'] }} badge-sm">{{ $op['type_label'] }}</span></td>
                            <td class="font-weight-bold"><small>{{ $op['montant_fmt'] }}</small></td>
                            <td><small>{{ $op['heure'] }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @endif {{-- /stats non vides --}}
    @endif {{-- /guichet --}}

</div>
@endsection

@push('css')
<style>
    .table-sm td, .table-sm th { font-size:.88rem; vertical-align:middle; }
    .badge-sm { font-size:.72rem; padding:.15em .4em; }
    .gap-2 { gap:.5rem; }
    .info-box { min-height: 60px; }
    .info-box .info-box-icon { font-size: 1.2rem; width: 60px; }
    .rapport-filter-shell {
        border-top-width: 3px;
    }
    .rapport-guichet-badge {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        width: 100%;
        padding: .7rem .9rem;
        border-radius: .35rem;
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.15);
        font-size: .9rem;
        line-height: 1.45;
    }
    .rapport-devise-card {
        background: rgba(255,255,255,.04);
    }

    @media (max-width: 767.98px) {
        .rapport-filter-shell-head,
        .rapport-filter-shell-tools {
            flex-wrap: wrap;
            gap: .5rem;
            align-items: flex-start !important;
        }

        .rapport-filter-card {
            flex-wrap: wrap;
            align-items: stretch !important;
        }

        .rapport-filter-control,
        .rapport-filter-btn,
        .rapport-guichet-badge {
            width: 100%;
            margin-left: 0 !important;
        }

        .rapport-filter-shell-tools .btn {
            width: 100%;
        }

        .rapport-guichet-badge {
            white-space: normal;
            text-align: left;
            line-height: 1.45;
        }
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function () {
    // Navigation vers la même page avec nouvelle date
    $('#btnChargerRapport').on('click', function (e) {
        e.preventDefault();
        var date = $('#filtreDate').val();
        if (date) {
            window.location.href = '{{ route("caisses.rapport.fin.journee") }}?date=' + date;
        }
    });
});
</script>
@endpush

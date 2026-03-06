{{-- ============================================================
     Vue Caisse : Ouverture / Fermeture physique des Guichets
     Tables : tb_caisses_guichets + tb_caisses_guichets_soldes
              + tb_affectations (agent titulaire)
     Permissions : EBEN-PER10 (voir) | EBEN-PER11 (ouvrir) | EBEN-PER12 (fermer)
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'Ouverture / Fermeture Guichet')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Ouverture / Fermeture')

@section('content')
<div class="container-fluid">

    {{-- ═══════════════════════════════════════════════════════
         LIGNE 1 — EN-TÊTE : date/heure + résumé session
         ═══════════════════════════════════════════════════════ --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="callout callout-info mb-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-calendar-day mr-2 text-info"></i>
                            Session du {{ now()->isoFormat('dddd D MMMM YYYY') }}
                        </h5>
                        <p class="mb-0 text-muted small">
                            Gérez l'état opérationnel de chaque guichet avant de démarrer les opérations.
                        </p>
                    </div>
                    <div class="text-right mt-2 mt-md-0">
                        <span class="badge badge-info px-3 py-2" id="clock" style="font-size:1rem;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         LIGNE 2 — MINI-DASHBOARD
         ═══════════════════════════════════════════════════════ --}}
    @php
        $totalGuichets  = $guichets->count();
        $nbOuverts      = $guichets->where('statut_operationnel', 'OUVERT')->count();
        $nbFermes       = $guichets->where('statut_operationnel', 'FERME')->count();
        $nbSuspendus    = $guichets->where('statut_operationnel', 'SUSPENDU')->count();

        // Totaux soldes par devise (tous guichets confondus)
        $totalsCDF = $guichets->flatMap->soldes->where('devise_code', 'CDF')->sum('solde_en_caisse');
        $totalsUSD = $guichets->flatMap->soldes->where('devise_code', 'USD')->sum('solde_en_caisse');
    @endphp

    <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-secondary elevation-1">
                    <i class="fas fa-cash-register"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Guichets</span>
                    <span class="info-box-number">{{ $totalGuichets }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-door-open"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Ouverts</span>
                    <span class="info-box-number">{{ $nbOuverts }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-info elevation-1">
                    <i class="fas fa-coins"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total CDF (Fc)</span>
                    <span class="info-box-number" style="font-size:1rem;">
                        {{ number_format($totalsCDF, 2, ',', ' ') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-warning elevation-1">
                    <i class="fas fa-dollar-sign"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total USD ($)</span>
                    <span class="info-box-number" style="font-size:1rem;">
                        {{ number_format($totalsUSD, 2, ',', ' ') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         LIGNE 3 — CARTES GUICHETS
         ═══════════════════════════════════════════════════════ --}}
    <div class="row" id="guichetsContainer">
        @forelse($guichets as $g)

        @php
            $couleur = match($g->statut_operationnel) {
                'OUVERT'   => 'success',
                'SUSPENDU' => 'warning',
                default    => 'danger',
            };
            $iconStatut = match($g->statut_operationnel) {
                'OUVERT'   => 'fa-door-open',
                'SUSPENDU' => 'fa-pause-circle',
                default    => 'fa-door-closed',
            };
            $agent = $g->affectationActive?->agent;
        @endphp

        <div class="col-xl-4 col-md-6 mb-4 guichet-card" data-id="{{ $g->id }}">
            <div class="card shadow-sm border-top-{{ $couleur }} h-100">
                <div class="card-body pb-2">

                    {{-- ── En-tête carte ── --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="font-weight-bold mb-0">
                                <i class="fas fa-cash-register mr-1 text-secondary"></i>
                                {{ $g->code_guichet }}
                            </h5>
                            <small class="text-muted">{{ $g->intitule }}</small>
                        </div>
                        <span class="badge badge-{{ $couleur }} px-3 py-2 statut-badge">
                            <i class="fas {{ $iconStatut }} mr-1"></i>
                            {{ $g->statut_operationnel }}
                        </span>
                    </div>

                    <hr class="my-2">

                    {{-- ── Informations ── --}}
                    <table class="table table-sm table-borderless mb-2 small">
                        {{-- Agent titulaire --}}
                        <tr>
                            <td class="text-muted pl-0 align-middle" style="width:32%; white-space:nowrap;">
                                <i class="fas fa-user-tie mr-1"></i> Agent
                            </td>
                            <td class="font-weight-bold">
                                @if($agent)
                                    <span title="{{ $agent->matricule }}">
                                        {{ $agent->prenom }} {{ $agent->nom }}
                                    </span>
                                @else
                                    <span class="text-warning">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Non affecté
                                    </span>
                                @endif
                            </td>
                        </tr>

                        {{-- Soldes multi-devises --}}
                        <tr>
                            <td class="text-muted pl-0 align-top">
                                <i class="fas fa-coins mr-1"></i> Soldes
                            </td>
                            <td>
                                @forelse($g->soldes->sortBy('devise_code') as $s)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge badge-secondary mr-1" style="min-width:38px;">
                                        {{ $s->devise_code }}
                                    </span>
                                    <span class="font-weight-bold text-right flex-fill">
                                        {{ number_format($s->solde_en_caisse, 2, ',', ' ') }}
                                        <small class="text-muted">{{ $s->devise->symbole ?? '' }}</small>
                                    </span>
                                </div>
                                @empty
                                <span class="text-muted font-italic small">
                                    <i class="fas fa-info-circle mr-1"></i>Aucun solde initialisé
                                </span>
                                @endforelse
                            </td>
                        </tr>

                        {{-- Dernière modification --}}
                        <tr>
                            <td class="text-muted pl-0">
                                <i class="fas fa-clock mr-1"></i> Créé le
                            </td>
                            <td class="small text-muted">
                                {{ $g->created_at ? \Carbon\Carbon::parse($g->created_at)->format('d/m/Y') : '—' }}
                            </td>
                        </tr>
                    </table>

                    {{-- ── Alerte si aucun agent ── --}}
                    @if(!$agent)
                    <div class="alert alert-warning py-1 px-2 mb-2 small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Aucun agent actif.
                        <a href="{{ route('affectations.index') }}" class="alert-link">Affecter via RH</a>
                    </div>
                    @endif

                </div>{{-- /card-body --}}

                {{-- ── Pied de carte : boutons d'action ── --}}
                <div class="card-footer bg-transparent pt-2 pb-3">

                    {{-- Ligne 1 : Ouvrir / Fermer --}}
                    <div class="d-flex mb-1">
                        @if($g->statut_operationnel !== 'OUVERT')
                        <button class="btn btn-success btn-sm flex-fill mr-1 btn-action"
                                data-id="{{ $g->id }}"
                                data-statut="OUVERT"
                                data-code="{{ $g->code_guichet }}"
                                title="Démarrer la session caisse">
                            <i class="fas fa-door-open mr-1"></i> Ouvrir
                        </button>
                        @else
                        <button class="btn btn-success btn-sm flex-fill mr-1" disabled
                                title="Ce guichet est déjà ouvert">
                            <i class="fas fa-check-circle mr-1"></i> Ouvert
                        </button>
                        @endif

                        @if($g->statut_operationnel !== 'FERME')
                        <button class="btn btn-danger btn-sm flex-fill ml-1 btn-action"
                                data-id="{{ $g->id }}"
                                data-statut="FERME"
                                data-code="{{ $g->code_guichet }}"
                                title="Terminer la session caisse">
                            <i class="fas fa-door-closed mr-1"></i> Fermer
                        </button>
                        @else
                        <button class="btn btn-danger btn-sm flex-fill ml-1" disabled
                                title="Ce guichet est déjà fermé">
                            <i class="fas fa-lock mr-1"></i> Fermé
                        </button>
                        @endif
                    </div>

                    {{-- Ligne 2 : Suspendre (uniquement si ouvert) --}}
                    @if($g->statut_operationnel === 'OUVERT')
                    <button class="btn btn-warning btn-sm btn-block btn-action"
                            data-id="{{ $g->id }}"
                            data-statut="SUSPENDU"
                            data-code="{{ $g->code_guichet }}"
                            title="Pause temporaire du guichet">
                        <i class="fas fa-pause-circle mr-1"></i> Suspendre temporairement
                    </button>
                    @endif

                    {{-- Ligne 2 : Réactiver (si suspendu) --}}
                    @if($g->statut_operationnel === 'SUSPENDU')
                    <button class="btn btn-info btn-sm btn-block btn-action"
                            data-id="{{ $g->id }}"
                            data-statut="OUVERT"
                            data-code="{{ $g->code_guichet }}"
                            title="Réactiver le guichet suspendu">
                        <i class="fas fa-play-circle mr-1"></i> Réactiver
                    </button>
                    @endif

                </div>
            </div>
        </div>

        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x mb-3 d-block text-warning"></i>
                <h5>Aucun guichet configuré</h5>
                <p class="mb-2 text-muted">Veuillez d'abord créer des guichets dans la section Administration.</p>
                <a href="{{ route('administration.guichets.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-cog mr-1"></i> Administration → Guichets
                </a>
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection


@section('css')
<style>
    /* Bordure colorée en haut des cartes */
    .border-top-success { border-top: 4px solid #28a745 !important; }
    .border-top-warning { border-top: 4px solid #ffc107 !important; }
    .border-top-danger  { border-top: 4px solid #dc3545 !important; }

    /* Hover effect */
    .guichet-card .card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .guichet-card .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(0,0,0,.15) !important;
    }

    /* Bouton désactivé plus visible */
    .btn:disabled { opacity: 0.55; cursor: not-allowed; }

    .card-footer { border-top: 1px solid rgba(0,0,0,.06); }

    .info-box-number { font-size: 1.35rem; }
</style>
@endsection


@push('js')
<script>
$(document).ready(function () {

    // ── Horloge temps réel ─────────────────────────────────────────────────────
    function updateClock() {
        var now = new Date();
        var h   = String(now.getHours()).padStart(2, '0');
        var m   = String(now.getMinutes()).padStart(2, '0');
        var s   = String(now.getSeconds()).padStart(2, '0');
        $('#clock').text(h + ':' + m + ':' + s);
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── AJAX setup csrf ────────────────────────────────────────────────────────
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    // ── Labels et messages de confirmation par statut ─────────────────────────
    var labels = {
        'OUVERT'   : { verbe: 'ouvrir',    question: 'Démarrer la session sur le guichet',   classe: 'success' },
        'FERME'    : { verbe: 'fermer',     question: 'Fermer (terminer la session) du guichet', classe: 'danger' },
        'SUSPENDU' : { verbe: 'suspendre',  question: 'Suspendre temporairement le guichet', classe: 'warning' },
    };

    // ── Bouton changer statut ──────────────────────────────────────────────────
    $(document).on('click', '.btn-action', function () {
        var $btn   = $(this);
        var id     = $btn.data('id');
        var statut = $btn.data('statut');
        var code   = $btn.data('code');
        var info   = labels[statut] || { verbe: statut, question: 'Modifier le guichet', classe: 'primary' };

        showUniversalConfirm(
            info.question + ' <strong>' + code + '</strong> ?',
            function () {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> En cours…');

                $.ajax({
                    url    : '{{ route("caisses.changerStatut", ["id" => "__ID__"]) }}'.replace('__ID__', id),
                    method : 'POST',
                    data   : { statut: statut },
                    dataType: 'json'
                })
                .done(function (response) {
                    showSystemMessage('success', response.message || 'Statut mis à jour.');
                    setTimeout(function () { location.reload(); }, 800);
                })
                .fail(function (xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-exclamation-circle mr-1"></i> Erreur');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Erreur lors du changement de statut (' + xhr.status + ').';
                    showSystemMessage('error', msg);
                });
            },
            'Confirmer l\'action'
        );
    });

});
</script>
@endpush

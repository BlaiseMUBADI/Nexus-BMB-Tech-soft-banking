{{-- ============================================================
     Ouverture / Fermeture — Vue Guichetier
     Affiche UNIQUEMENT le guichet de l'agent connecté.
     Permissions : EBEN-PER10 (voir) | EBEN-PER11 (ouvrir/fermer)
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'Mon Guichet')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Ouverture / Fermeture')

@section('content')
<div class="container-fluid">

    {{-- ══════════════════════════════════════════════════════════
         CAS 1 — L'agent connecté n'a PAS de guichet affecté
         ══════════════════════════════════════════════════════════ --}}
    @if(!$guichet)
    <div class="row justify-content-center mt-4">
        <div class="col-md-7">
            <div class="card card-danger card-outline shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                    <h4 class="font-weight-bold">Aucun guichet assigné</h4>
                    <p class="text-muted mb-4">
                        Votre compte <strong>{{ $user->name }}</strong> n'est pas encore associé à un guichet de caisse.<br>
                        Veuillez contacter un administrateur pour être affecté.
                    </p>
                    <a href="{{ route('administration.guichets.index') }}"
                       class="btn btn-outline-primary">
                        <i class="fas fa-cog mr-1"></i> Administration → Guichets
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         CAS 2 — L'agent a un guichet → interface de gestion
         ══════════════════════════════════════════════════════════ --}}
    @else
    @php
        $statut   = $guichet->statut_operationnel;
        $couleur  = match($statut) {
            'OUVERT'          => 'success',
            'SUSPENDU'        => 'warning',
            'EN_VERIFICATION' => 'info',
            default           => 'danger',
        };
        $iconeStatut = match($statut) {
            'OUVERT'          => 'fa-door-open',
            'SUSPENDU'        => 'fa-pause-circle',
            'EN_VERIFICATION' => 'fa-hourglass-half',
            default           => 'fa-door-closed',
        };
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            {{-- ── En-tête session ───────────────────────────────────── --}}
            <div class="callout callout-{{ $couleur }} mb-4">
                <div class="d-flex justify-content-between align-items-center ouverture-session-head">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Session du {{ now()->isoFormat('dddd D MMMM YYYY') }}
                        </h5>
                        <p class="mb-0 small text-muted">
                            Bonjour <strong>{{ $user->name }}</strong> —
                            gérez l'état de votre guichet avant de démarrer les opérations.
                        </p>
                    </div>
                                                <span class="badge badge-{{ $couleur }} px-3 py-2 ouverture-clock" id="clock"
                          style="font-size:1.25rem; border-radius:8px;"></span>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         LIGNE 2 : Formulaire (gauche) + Carte guichet (droite)
         ══════════════════════════════════════════════════════════ --}}
    <div class="row">

        {{-- ── COL GAUCHE : Formulaire demande ───────────────── --}}
        <div class="col-lg-4 col-md-5 mb-3">
            <div class="card card-outline card-primary shadow h-100">
                <div class="card-header py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-paper-plane mr-1 text-primary"></i>
                        Demander un approvisionnement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold">Devise <span class="text-danger">*</span></label>
                        <select class="form-control" id="demandeDevise">
                            <option value="">— Sélectionnez —</option>
                            @foreach($guichet->soldes->sortBy('devise_code') as $s)
                            <option value="{{ $s->devise_code }}">{{ $s->devise_code }} — {{ $s->devise->nom ?? $s->devise_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold">Montant demandé <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="demandeMontant"
                               placeholder="Ex : 500000" min="1" step="any">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Motif</label>
                        <input type="text" class="form-control" id="demandeMotif"
                               placeholder="Raison de la demande…" maxlength="255">
                    </div>
                    <button class="btn btn-primary btn-block" id="btnEnvoyerDemande">
                        <i class="fas fa-paper-plane mr-1"></i> Envoyer la demande
                    </button>
                </div>
            </div>
        </div>

        {{-- ── COL DROITE : Carte guichet ─────────────────────── --}}
        <div class="col-lg-8 col-md-7 mb-3">
            <div class="card shadow border-top-{{ $couleur }} h-100" id="guichetCard">
                <div class="card-header bg-transparent pb-0">
                    <div class="d-flex justify-content-between align-items-center ouverture-card-head">
                        <h4 class="font-weight-bold mb-0">
                            <i class="fas fa-cash-register mr-2 text-secondary"></i>
                            {{ $guichet->code_guichet }}
                            <small class="text-muted font-weight-normal ml-1">
                                {{ $guichet->intitule }}
                            </small>
                        </h4>
                        <span class="badge badge-{{ $couleur }} px-3 py-2" id="statutBadge"
                              style="font-size:1.05rem;">
                            <i class="fas {{ $iconeStatut }} mr-1 statut-icon"></i>
                            <span class="statut-text">{{ $statut }}</span>
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    {{-- ── Soldes multi-devises ──────────────── --}}
                    <h6 class="text-muted text-uppercase mb-2" style="font-size:.85rem; letter-spacing:1px;">
                        <i class="fas fa-coins mr-1"></i> Soldes de caisse
                    </h6>

                    @if($guichet->soldes->isNotEmpty())
                    <div class="row mb-3">
                        @foreach($guichet->soldes->sortBy('devise_code') as $s)
                        <div class="col-12 col-sm-4 mb-2">
                            <div class="solde-card solde-card-{{ $couleur === 'danger' ? 'secondary' : $couleur }}">
                                <div class="solde-card-icon">
                                    <span class="solde-symbole">{{ $s->devise->symbole ?? $s->devise_code }}</span>
                                </div>
                                <div class="solde-card-body">
                                    <div class="solde-devise">{{ $s->devise_code }}</div>
                                    <div class="solde-montant">{{ number_format($s->solde_en_caisse, 2, ',', ' ') }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-light border mb-3 py-2">
                        <i class="fas fa-info-circle mr-1 text-info"></i>
                        Aucun solde initialisé sur ce guichet.
                        Contactez l'administration pour initialiser les devises.
                    </div>
                    @endif

                    <hr class="my-3">

                    {{-- ── Boutons d'action ─────────────────── --}}
                    <h6 class="text-muted text-uppercase mb-3" style="font-size:.85rem; letter-spacing:1px;">
                        <i class="fas fa-toggle-on mr-1"></i> Actions
                    </h6>

                    <div class="row">
                        {{-- Ouvrir --}}
                        <div class="col-12 col-sm-4 mb-2">
                            <button class="btn btn-success btn-block btn-action {{ in_array($statut, ['OUVERT','EN_VERIFICATION']) ? 'disabled' : '' }}"
                                    data-id="{{ $guichet->id }}"
                                    data-statut="OUVERT"
                                    data-code="{{ $guichet->code_guichet }}"
                                    {{ in_array($statut, ['OUVERT','EN_VERIFICATION']) ? 'disabled' : '' }}
                                    title="{{ $statut === 'OUVERT' ? 'Guichet déjà ouvert' : 'Démarrer la session caisse' }}">
                                <i class="fas fa-door-open d-block fa-2x mb-1"></i>
                                Ouvrir
                            </button>
                        </div>

                        {{-- Suspendre --}}
                        <div class="col-12 col-sm-4 mb-2">
                            <button class="btn btn-warning btn-block btn-action {{ $statut !== 'OUVERT' ? 'disabled' : '' }}"
                                    data-id="{{ $guichet->id }}"
                                    data-statut="SUSPENDU"
                                    data-code="{{ $guichet->code_guichet }}"
                                    {{ $statut !== 'OUVERT' ? 'disabled' : '' }}
                                    title="{{ $statut === 'OUVERT' ? 'Pause temporaire' : '' }}">
                                <i class="fas fa-pause-circle d-block fa-2x mb-1"></i>
                                Suspendre
                            </button>
                        </div>

                        {{-- Fermer --}}
                        <div class="col-12 col-sm-4 mb-2">
                            <button class="btn btn-danger btn-block btn-action {{ in_array($statut, ['FERME','EN_VERIFICATION']) ? 'disabled' : '' }}"
                                    data-id="{{ $guichet->id }}"
                                    data-statut="FERME"
                                    data-code="{{ $guichet->code_guichet }}"
                                    {{ in_array($statut, ['FERME','EN_VERIFICATION']) ? 'disabled' : '' }}
                                    title="{{ $statut === 'FERME' ? 'Guichet déjà fermé' : ($statut === 'EN_VERIFICATION' ? 'Clôture en cours de validation' : 'Terminer la session caisse') }}">
                                <i class="fas fa-door-closed d-block fa-2x mb-1"></i>
                                Clôturer
                            </button>
                        </div>
                    </div>

                    {{-- Message informatif selon statut --}}
                    <div class="mt-3">
                        @if($statut === 'OUVERT')
                        <div class="alert alert-success py-2 mb-0">
                            <i class="fas fa-check-circle mr-1"></i>
                            Votre guichet est <strong>ouvert</strong>. Vous pouvez démarrer les opérations.
                        </div>
                        @elseif($statut === 'SUSPENDU')
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="fas fa-pause-circle mr-1"></i>
                            Guichet <strong>suspendu</strong>. Cliquez sur <em>Ouvrir</em> pour reprendre.
                        </div>
                        @elseif($statut === 'EN_VERIFICATION')
                        <div class="alert alert-info py-2 mb-0">
                            <i class="fas fa-hourglass-half mr-1"></i>
                            Billetage soumis. <strong>En attente de validation du superviseur.</strong>
                            Votre guichet est bloqué jusqu'à la confirmation.
                        </div>
                        @else
                        <div class="alert alert-danger py-2 mb-0">
                            <i class="fas fa-lock mr-1"></i>
                            Guichet <strong>fermé</strong>. Ouvrez-le avant de démarrer les opérations.
                        </div>
                        @endif
                    </div>

                </div>{{-- /card-body --}}
            </div>{{-- /card guichet --}}
        </div>

    </div>{{-- /row formulaire + guichet --}}

    {{-- ══════════════════════════════════════════════════════════
         LIGNE 3 : Tableau historique pleine largeur
         ══════════════════════════════════════════════════════════ --}}
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card card-outline card-secondary shadow">
                <div class="card-header d-flex align-items-center justify-content-between py-2 ouverture-demandes-head">
                    <h6 class="mb-0">
                        <i class="fas fa-history mr-1 text-secondary"></i>
                        Mes demandes d'approvisionnement
                    </h6>
                    <button class="btn btn-xs btn-outline-secondary" id="btnRefreshDemandes" title="Actualiser">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Réf.</th>
                                    <th>Montant</th>
                                    <th>Motif</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyMesDemandes">
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Chargement…
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- /row tableau --}}

    {{-- ══════════════════════════════════════════════════════════
         MODAL : Arrêté de Caisse (Billetage + Clôture)
         ══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalArreteCaisse" tabindex="-1" role="dialog"
         aria-labelledby="arreteCaisseLabel" aria-hidden="true"
         data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="arreteCaisseLabel">
                        <i class="fas fa-calculator mr-2"></i>
                        Arrêté de Caisse &mdash; <span id="arreteCodeGuichet">…</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- Corps chargé dynamiquement par JS --}}
                <div class="modal-body p-0" id="arreteBody">
                    <div class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-3 text-muted">Chargement des soldes…</p>
                    </div>
                </div>

                <div class="modal-footer" style="background:rgba(0,0,0,.25);">
                    <small class="text-muted mr-auto">
                        <i class="fas fa-shield-alt mr-1 text-info"></i>
                        Votre billetage sera soumis au superviseur pour validation. Le guichet sera bloqué jusqu'à confirmation.
                    </small>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Annuler
                    </button>
                    <button type="button" class="btn btn-danger" id="btnConfirmerArrete" disabled>
                        <i class="fas fa-lock mr-1"></i> Confirmer la clôture
                    </button>
                </div>

            </div>
        </div>
    </div>{{-- /modal arrêté caisse --}}

    @endif

</div>
@endsection


@push('css')
<style>
    .border-top-success  { border-top: 4px solid #28a745 !important; }
    .border-top-warning  { border-top: 4px solid #ffc107 !important; }
    .border-top-danger   { border-top: 4px solid #dc3545 !important; }
    .border-top-info     { border-top: 4px solid #17a2b8 !important; }

    /* ── Boutons action ─────────────────────────────────────── */
    .btn-action { padding: 0.75rem 0.5rem; font-size: 1rem; font-weight: 600; border-radius: 8px; transition: transform .1s; }
    .btn-action:not(:disabled):active { transform: scale(.96); }
    .btn-action:not(:disabled) { cursor: pointer; }
    .btn:disabled { opacity: 0.38; cursor: not-allowed; }

    /* ── Cartes soldes (dark-safe) ──────────────────────────── */
    .solde-card {
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 8px;
        padding: 10px 12px;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.10);
    }
    .solde-card-success   { border-left: 3px solid #28a745; }
    .solde-card-warning   { border-left: 3px solid #ffc107; }
    .solde-card-secondary { border-left: 3px solid #6c757d; }

    .solde-card-icon {
        width: 44px; height: 44px;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1rem; flex-shrink: 0;
    }
    .solde-card-success   .solde-card-icon { background: rgba(40,167,69,.25);  color: #5dd879; }
    .solde-card-warning   .solde-card-icon { background: rgba(255,193,7,.20);  color: #ffd54f; }
    .solde-card-secondary .solde-card-icon { background: rgba(108,117,125,.25); color: #adb5bd; }

    .solde-card-body { min-width: 0; }
    .solde-devise  { font-size: .82rem; text-transform: uppercase; letter-spacing: .06em; color: #8a9bb0; margin-bottom: 2px; }
    .solde-montant { font-size: 1.08rem; font-weight: 700; color: #e9ecef; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* ── Tableau demandes ───────────────────────────────────── */
    #tbodyMesDemandes td { font-size: .92rem; vertical-align: middle; }
    #tbodyMesDemandes thead th { font-size: .88rem; }
    .table-sm th, .table-sm td { padding: .5rem .6rem; }
    /* ── Modal Arrêté de Caisse ─────────────────────────────────────────── */
    .arrete-devise-bloc {
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 8px;
        overflow: hidden;
    }
    .arrete-devise-header {
        background: rgba(220, 53, 69, .18);
        border-bottom: 1px solid rgba(220, 53, 69, .30);
        font-size: .95rem;
    }
    .table-arrete thead th { font-size: .88rem; }
    .table-arrete tfoot td { font-size: .92rem; }
    .table-arrete .denom-qty {
        font-size: 1rem;
        font-weight: 700;
        text-align: center;
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.20);
        color: #e9ecef;
        width: 90px;
        min-width: 70px;
    }
    .table-arrete .denom-qty:focus {
        background: rgba(255,193,7,.15);
        border-color: #ffc107;
        color: #fff;
        box-shadow: 0 0 0 2px rgba(255,193,7,.35);
    }
    .table-arrete .denom-subtotal { font-size: .95rem; min-width: 100px; }
    .table-arrete .denom-row.table-active td { background: rgba(255,193,7,.10) !important; }
    /* ── Saisie directe montant physique ──────────────────────────────── */
    .arrete-montant-direct-row {
        background: rgba(23, 162, 184, .08);
        border-bottom: 1px solid rgba(23, 162, 184, .25);
    }
    .arrete-montant-direct-row .montant-direct {
        background: rgba(255,255,255,.07);
        border-color: rgba(23, 162, 184, .40);
        color: #e9ecef;
        font-weight: 600;
    }
    .arrete-montant-direct-row .montant-direct:focus {
        background: rgba(23, 162, 184, .15);
        border-color: #17a2b8;
        color: #fff;
        box-shadow: 0 0 0 2px rgba(23, 162, 184, .35);
    }
    .arrete-montant-direct-row small {
        display: block;
        margin-top: .25rem;
        color: rgba(255,255,255,.5) !important;
        font-size: .80rem;
    }
    /* ── Ligne Total physique ──────────────────── */
    .arrete-total-row td {
        background: rgba(52, 152, 219, .25) !important;
        border-top: 2px solid rgba(52, 152, 219, .60) !important;
        color: #e9ecef !important;
        font-weight: 700;
        font-size: .97rem;
    }
    /* ── Ligne Écart — équilibrée ─────────────── */
    .arrete-ecart-row td {
        background: #1a3d27 !important;
        border-top: 2px solid #28a745 !important;
        color: #6fdf8a !important;
        font-weight: 700;
        font-size: .97rem;
    }
    .arrete-ecart-row td span { color: #6fdf8a !important; }
    /* ── Ligne Écart — déficit ───────────────── */
    .arrete-ecart-row.table-danger td {
        background: #3d0e14 !important;
        border-top: 2px solid #dc3545 !important;
        color: #ff8a96 !important;
    }
    .arrete-ecart-row.table-danger td span { color: #ff8a96 !important; }
    /* ── Ligne Écart — excédent ─────────────── */
    .arrete-ecart-row.table-warning td {
        background: #3d2e00 !important;
        border-top: 2px solid #ffc107 !important;
        color: #ffd54f !important;
    }
    .arrete-ecart-row.table-warning td span { color: #ffd54f !important; }
    #arreteBody { max-height: 72vh; overflow-y: auto; }

    @media (max-width: 767.98px) {
        .ouverture-session-head,
        .ouverture-card-head,
        .ouverture-demandes-head {
            flex-wrap: wrap;
            gap: .75rem;
            align-items: flex-start !important;
        }

        .ouverture-clock,
        #statutBadge {
            width: 100%;
            text-align: center;
        }

        .solde-card {
            padding: .85rem .95rem;
        }

        .solde-montant {
            white-space: normal;
            overflow: visible;
            text-overflow: initial;
            word-break: break-word;
        }

        .btn-action {
            min-height: 82px;
        }

        #btnRefreshDemandes {
            width: 100%;
        }

        #modalArreteCaisse .modal-footer {
            flex-direction: column;
            align-items: stretch;
        }

        #modalArreteCaisse .modal-footer small {
            margin-right: 0 !important;
            margin-bottom: .75rem;
        }

        #modalArreteCaisse .modal-footer .btn {
            width: 100%;
        }
    }
</style>
@endpush


@push('js')
<script>
$(document).ready(function () {

    // ── Horloge temps réel ─────────────────────────────────────────────────
    function updateClock() {
        var t = new Date();
        $('#clock').text(
            String(t.getHours()).padStart(2,'0') + ':' +
            String(t.getMinutes()).padStart(2,'0') + ':' +
            String(t.getSeconds()).padStart(2,'0')
        );
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── AJAX setup ────────────────────────────────────────────────────────
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    @if($guichet)
    // ── URLs arrêté de caisse ─────────────────────────────────────────────
    var urlInitierFermeture  = '{{ route("caisses.fermeture.initier", ["id" => "__ID__"]) }}';
    var urlConfirmerFermeture= '{{ route("caisses.fermeture.confirmer", ["id" => "__ID__"]) }}';
    var urlPendante          = '{{ route("caisses.fermeture.pendante") }}';
    @endif

    // ── Labels et options modal par statut ───────────────────────────────
    var cfg = {
        'OUVERT'   : {
            confirm : 'Démarrer la session caisse sur le guichet',
            opts    : {
                title       : 'Ouvrir le guichet',
                btnLabel    : 'Ouvrir',
                btnClass    : 'btn-success',
                icon        : 'fas fa-door-open',
                bodyIcon    : 'fas fa-door-open fa-3x text-success',
                headerClass : 'bg-success text-white',
                showWarning : false,
            }
        },
        'FERME'    : {
            confirm : 'Fermer le guichet et terminer la session sur',
            opts    : {
                title       : 'Fermer le guichet',
                btnLabel    : 'Fermer',
                btnClass    : 'btn-danger',
                icon        : 'fas fa-door-closed',
                bodyIcon    : 'fas fa-door-closed fa-3x text-danger',
                headerClass : 'bg-danger text-white',
                showWarning : false,
            }
        },
        'SUSPENDU' : {
            confirm : 'Suspendre temporairement le guichet',
            opts    : {
                title       : 'Suspendre le guichet',
                btnLabel    : 'Suspendre',
                btnClass    : 'btn-warning',
                icon        : 'fas fa-pause-circle',
                bodyIcon    : 'fas fa-pause-circle fa-3x text-warning',
                headerClass : 'bg-warning text-dark',
                showWarning : false,
            }
        },
    };

    // ── Arrêté de caisse — Dénominations par devise ──────────────────────
    var DENOMS = {
        'CDF': [50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100, 50],
        'USD': [100, 50, 20, 10, 5, 2, 1],
        'EUR': [500, 200, 100, 50, 20, 10, 5, 2, 1],
    };
    var FALLBACK_DENOMS = [1000, 500, 200, 100, 50, 10, 5, 1];
    var _arreteGuichetId = null;

    /** Formate un nombre en style français : 1 250 000,00 */
    function fmtArrete(val) {
        val = parseFloat(val) || 0;
        var parts = val.toFixed(2).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '\u202f'); /* espace fine insécable */
        return parts[0] + ',' + parts[1];
    }

    // ── Délégation unique pour les champs billetage ─────────────────────
    $(document).on('input change keyup', '#arreteBody .denom-qty', function () {
        recalculerArrete();
    });

    // ── Saisie directe du montant physique ───────────────────────────────
    $(document).on('input change', '#arreteBody .montant-direct', function () {
        /* Si le champ direct est renseigné, effacer les coupures du même bloc */
        var $bloc = $(this).closest('.arrete-devise-bloc');
        if ($.trim($(this).val()) !== '') {
            $bloc.find('.denom-qty').val(0);
        }
        recalculerArrete();
    });

    // ── Bouton Pré-remplir : copie le solde système → champ direct ───────
    $(document).on('click', '#arreteBody .btn-prefill-direct', function () {
        var $bloc  = $(this).closest('.arrete-devise-bloc');
        var solde  = parseFloat($(this).data('solde')) || 0;
        $bloc.find('.montant-direct').val(solde.toFixed(2));
        /* Vider les coupures puisqu'on utilise la saisie directe */
        $bloc.find('.denom-qty').val(0);
        recalculerArrete();
    });

    /** Construit le corps HTML du modal à partir des données JSON de /initier */
    function buildArreteBody(data) {
        var html = '<div class="p-3">';
        html += '<div class="alert alert-secondary py-2 mb-3 small">'
             + '<i class="fas fa-info-circle mr-1 text-info"></i>'
             + 'Comptez les billets dans votre tiroir et saisissez les quantités. '
             + 'Le système calcule l\'écart automatiquement.'
             + '</div>';

        $.each(data.soldes, function(i, s) {
            var denoms = DENOMS[s.devise_code] || FALLBACK_DENOMS;
            html += '<div class="arrete-devise-bloc mb-4"'
                 + ' data-devise="' + s.devise_code + '"'
                 + ' data-solde-comptable="' + s.solde_comptable + '">';
            html += '<div class="arrete-devise-header d-flex justify-content-between align-items-center px-3 py-2">'
                 + '<strong><i class="fas fa-money-bill-wave mr-1"></i> ' + s.devise_code + ' — ' + s.devise_nom + '</strong>'
                 + '<span class="text-muted">Solde système : <strong class="text-light">' + s.solde_fmt + '</strong></span>'
                 + '</div>';
            /* ── Saisie directe du montant physique (alternative au billetage) ── */
            html += '<div class="arrete-montant-direct-row px-3 pt-2 pb-1">'
                 + '<div class="input-group input-group-sm">'
                 + '<div class="input-group-prepend">'
                 + '<span class="input-group-text"><i class="fas fa-coins mr-1"></i> Montant physique</span>'
                 + '</div>'
                 + '<input type="number" class="form-control montant-direct" min="0" step="0.01"'
                 + ' placeholder="Saisir le total compte — si vous ne décomposez pas en coupures"'
                 + ' data-devise="' + s.devise_code + '">'
                 + '<div class="input-group-append">'
                 + '<span class="input-group-text">' + s.devise_code + '</span>'
                 + '<button type="button" class="btn btn-outline-info btn-sm btn-prefill-direct"'
                 + ' data-solde="' + s.solde_comptable + '"'
                 + ' title="Copier le solde système comme montant physique">'
                 + '<i class="fas fa-magic mr-1"></i>Pré-remplir</button>'
                 + '</div>'
                 + '</div>'
                 + '<small class="text-muted"><i class="fas fa-info-circle mr-1"></i>'
                 + 'Remplissez ce champ <strong>OU</strong> décomposez en coupures ci-dessous. '
                 + 'Cliquez <em>Pré-remplir</em> pour confirmer les montants système sans billetage.</small>'
                 + '</div>';
            html += '<table class="table table-sm table-bordered mb-0 table-arrete">'
                 + '<thead class="thead-dark"><tr>'
                 + '<th class="text-right" style="width:42%">Coupure</th>'
                 + '<th class="text-center" style="width:25%">Quantité</th>'
                 + '<th class="text-right">Sous-total</th>'
                 + '</tr></thead><tbody>';
            $.each(denoms, function(j, denom) {
                html += '<tr class="denom-row">'
                     + '<td class="text-right font-weight-bold py-1">' + fmtArrete(denom) + '&nbsp;' + s.devise_code + '</td>'
                     + '<td class="py-1 px-2"><input type="number" class="form-control form-control-sm denom-qty text-center" min="0" step="1" value="0" data-denom="' + denom + '"></td>'
                     + '<td class="text-right py-1 denom-subtotal text-muted">0,00</td>'
                     + '</tr>';
            });
            html += '</tbody><tfoot>'
                 + '<tr class="arrete-total-row">'
                 + '<td colspan="2" class="text-right"><i class="fas fa-calculator mr-1"></i>Total physique</td>'
                 + '<td class="text-right arrete-total-physique">0,00&nbsp;' + s.devise_code + '</td>'
                 + '</tr>'
                 + '<tr class="arrete-ecart-row">'
                 + '<td colspan="2" class="text-right"><i class="fas fa-balance-scale mr-1"></i>Écart (physique − système)</td>'
                 + '<td class="text-right arrete-ecart-val">—</td>'
                 + '</tr>'
                 + '</tfoot></table>';
            html += '</div>';
        });

        html += '<div id="motifEcartSection" class="mt-3 d-none">'
             + '<div class="alert alert-warning py-2 mb-2">'
             + '<i class="fas fa-exclamation-triangle mr-1"></i>'
             + '<strong>Écart détecté.</strong> Le motif est obligatoire pour valider la clôture.'
             + '</div>'
             + '<div class="form-group mb-0">'
             + '<label class="font-weight-bold">Motif de l\'écart <span class="text-danger">*</span></label>'
             + '<textarea class="form-control" id="champMotifEcart" rows="3"'
             + ' placeholder="Ex : Erreur de rendu de monnaie lors d\'une transaction client…"'
             + ' maxlength="500"></textarea>'
             + '</div>'
             + '</div>';

        html += '</div>';
        return html;
    }

    /** Recalcule totaux et écarts après chaque frappe dans un champ quantité */
    function recalculerArrete() {
        var aUnEcart = false;
        $('#arreteBody .arrete-devise-bloc').each(function() {
            var $bloc      = $(this);
            var comptable  = parseFloat($bloc.data('solde-comptable')) || 0;
            var totalPhy   = 0;
            var devise     = $bloc.data('devise');

            /* Saisie directe prioritaire sur billetage coupure par coupure */
            var directRaw  = $.trim($bloc.find('.montant-direct').val());
            var directVal  = parseFloat(directRaw);
            var usesDirect = (directRaw !== '' && !isNaN(directVal));

            if (usesDirect) {
                totalPhy = directVal;
                /* Effacer les sous-totaux des coupures (non utilisées) */
                $bloc.find('.denom-row').each(function() {
                    $(this).find('.denom-subtotal').text('0,00').addClass('text-muted');
                    $(this).removeClass('table-active');
                });
            } else {
                $bloc.find('.denom-row').each(function() {
                    var $row  = $(this);
                    var denom = parseFloat($row.find('.denom-qty').data('denom')) || 0;
                    var qty   = parseInt($row.find('.denom-qty').val()) || 0;
                    var sub   = denom * qty;
                    totalPhy += sub;
                    var $sub = $row.find('.denom-subtotal');
                    if (sub > 0) {
                        $sub.html('<strong>' + fmtArrete(sub) + '</strong>').removeClass('text-muted');
                        $row.addClass('table-active');
                    } else {
                        $sub.text('0,00').addClass('text-muted');
                        $row.removeClass('table-active');
                    }
                });
            }

            var ecart      = totalPhy - comptable;
            var $ecartCell = $bloc.find('.arrete-ecart-val');
            var $ecartRow  = $bloc.find('.arrete-ecart-row');

            $bloc.find('.arrete-total-physique').html(fmtArrete(totalPhy) + '&nbsp;' + devise);

            if (Math.abs(ecart) < 0.01) {
                $ecartCell.html('0,00 &mdash; <span class="text-success">Équilibrée ✓</span>');
                $ecartRow.removeClass('table-danger table-warning');
            } else if (ecart > 0) {
                $ecartCell.html('<span class="text-warning">+' + fmtArrete(ecart) + '&nbsp;' + devise + ' (Excédent)</span>');
                $ecartRow.addClass('table-warning').removeClass('table-danger');
                aUnEcart = true;
            } else {
                $ecartCell.html('<span class="text-danger">' + fmtArrete(ecart) + '&nbsp;' + devise + ' (Déficit)</span>');
                $ecartRow.addClass('table-danger').removeClass('table-warning');
                aUnEcart = true;
            }
        });

        if (aUnEcart) {
            $('#motifEcartSection').removeClass('d-none');
            $('#champMotifEcart').off('input.arreteCheck').on('input.arreteCheck', checkArreteValid);
        } else {
            $('#motifEcartSection').addClass('d-none');
        }
        checkArreteValid();
    }

    function checkArreteValid() {
        var aUnEcart = ($('.arrete-ecart-row.table-danger, .arrete-ecart-row.table-warning').length > 0);
        var motifOk  = !aUnEcart || ($.trim($('#champMotifEcart').val()).length >= 5);
        $('#btnConfirmerArrete').prop('disabled', !motifOk);
    }

    /** Ouvre le modal d'arrêté de caisse et charge les soldes */
    function ouvrirModalArrete(id, code) {
        _arreteGuichetId = id;
        $('#arreteCodeGuichet').text(code);
        $('#arreteBody').html(
            '<div class="text-center py-5">'
            + '<i class="fas fa-spinner fa-spin fa-2x text-muted"></i>'
            + '<p class="mt-3 text-muted">Chargement des soldes…</p>'
            + '</div>'
        );
        $('#btnConfirmerArrete').prop('disabled', true);
        $('#modalArreteCaisse').modal('show');

        $.ajax({
            url     : urlInitierFermeture.replace('__ID__', id),
            method  : 'GET',
            dataType: 'json',
            headers : { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function(data) {
            if (!data.success) {
                $('#modalArreteCaisse').modal('hide');
                showSystemMessage('error', data.message || 'Impossible d\'ouvrir l\'arrêté de caisse.');
                return;
            }
            $('#arreteBody').html(buildArreteBody(data));
            recalculerArrete();
        }).fail(function(xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Impossible de charger les soldes.';
            $('#modalArreteCaisse').modal('hide');
            showSystemMessage('error', msg);
            logFrontendError(msg, 'Chargement soldes arrêté caisse', xhr.status);
        });
    }

    // ── Soumettre l'arrêté de caisse ─────────────────────────────────────
    $('#btnConfirmerArrete').on('click', function () {
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        // Collecter le billetage
        var billetage  = [];
        var billetageOk = true;
        $('#arreteBody .arrete-devise-bloc').each(function () {
            var $bloc      = $(this);
            var devise     = $bloc.data('devise');
            var physique   = 0;
            var detail     = {};

            /* Utiliser la saisie directe si elle a été renseignée */
            var directRaw = $.trim($bloc.find('.montant-direct').val());
            var directVal = parseFloat(directRaw);
            if (directRaw !== '' && !isNaN(directVal)) {
                physique = directVal;
                /* Pas de détail de billetage en saisie directe */
            } else {
                $bloc.find('.denom-row').each(function () {
                    var $r  = $(this);
                    var den = parseFloat($r.find('.denom-qty').data('denom')) || 0;
                    var qty = parseInt($r.find('.denom-qty').val()) || 0;
                    physique += den * qty;
                    if (qty > 0) detail[den] = qty;
                });
            }

            billetage.push({ devise_code: devise, solde_physique: physique, detail: detail });
        });

        if (!billetage.length) { billetageOk = false; }

        var motif = $.trim($('#champMotifEcart').val() || '');

        $btn.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin mr-1"></i> Soumission en cours…');

        $.ajax({
            url     : urlConfirmerFermeture.replace('__ID__', _arreteGuichetId),
            method  : 'POST',
            contentType: 'application/json',
            data    : JSON.stringify({ billetage: billetage, motif_ecart: motif }),
            dataType: 'json',
            headers : { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .done(function (r) {
            if (r.success) {
                $('#modalArreteCaisse').modal('hide');
                showSystemMessage('success', r.message || 'Billetage soumis. En attente de validation.');
                // Recharger la page après 1.5s pour afficher EN_VERIFICATION
                setTimeout(function () { location.reload(); }, 1500);
            } else {
                showSystemMessage('error', r.message || 'Erreur inconnue.');
                $btn.prop('disabled', false)
                    .html('<i class="fas fa-lock mr-1"></i> Confirmer la clôture');
            }
        })
        .fail(function (xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Une erreur est survenue.';
            showSystemMessage('error', msg);
            $btn.prop('disabled', false).html('<i class="fas fa-lock mr-1"></i> Confirmer la clôture');
            logFrontendError(msg, 'Confirmation arrêté caisse', xhr.status);
        });
    });

    // ── Changer statut ────────────────────────────────────────────────────
    $(document).on('click', '.btn-action:not(:disabled)', function () {
        var $btn   = $(this);
        var id     = $btn.data('id');
        var statut = $btn.data('statut');
        var code   = $btn.data('code');

        // FERME → Arrêté de caisse complet (billetage + confrontation)
        if (statut === 'FERME') {
            ouvrirModalArrete(id, code);
            return;
        }

        var info   = cfg[statut];
        if (!info) return;

        showUniversalConfirm(
            info.confirm + ' <strong>' + code + '</strong> ?',
            function () {
                $btn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin d-block fa-2x mb-1"></i>En cours…');

                $.ajax({
                    url     : '{{ route("caisses.changerStatut", ["id" => "__ID__"]) }}'.replace('__ID__', id),
                    method  : 'POST',
                    data    : { statut: statut },
                    dataType: 'json'
                })
                .done(function (res) {
                    showSystemMessage('success', res.message || 'Statut mis à jour.');
                    setTimeout(function () { location.reload(); }, 1200);
                })
                .fail(function (xhr) {
                    $btn.prop('disabled', false);
                    handleAjaxFail(xhr, 'Changement statut guichet');
                });
            },
            info.opts
        );
    });

    // ── Demandes d'approvisionnement ──────────────────────────────────────
    @if($guichet)
    var urlDemandePost   = '{{ route("caisses.demande.appro") }}';
    var urlMesDemandes   = '{{ route("caisses.mes.demandes") }}';

    var statutLabels = { EN_ATTENTE: 'En attente', CONFIRME: 'Approuvée', ANNULE: 'Rejetée' };
    var statutClasses = { EN_ATTENTE: 'badge-warning text-dark', CONFIRME: 'badge-success', ANNULE: 'badge-danger' };

    function chargerMesDemandes() {
        $.ajax({
            url     : urlMesDemandes,
            method  : 'GET',
            dataType: 'json',
            headers : { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function(data) {
            var tbody = $('#tbodyMesDemandes');
            tbody.empty();
            if (!data.length) {
                tbody.html('<tr><td colspan="5" class="text-center py-3 text-muted"><i class="fas fa-inbox mr-1"></i> Aucune demande.</td></tr>');
                return;
            }
            $.each(data, function(i, d) {
                var motif = d.motif
                    ? (d.motif.length > 35 ? d.motif.substring(0,35) + '…' : d.motif)
                    : '<span class="text-muted">—</span>';
                tbody.append(
                    '<tr>'
                    + '<td><small class="text-monospace">' + (d.reference || '—') + '</small></td>'
                    + '<td><strong>' + d.montant_fmt + '</strong></td>'
                    + '<td><small>' + motif + '</small></td>'
                    + '<td><small>' + (d.date || '—') + '</small></td>'
                    + '<td><span class="badge ' + (statutClasses[d.statut] || 'badge-secondary') + '">'  
                    + (statutLabels[d.statut] || d.statut) + '</span></td>'
                    + '</tr>'
                );
            });
        }).fail(function(xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Erreur de chargement.';
            logFrontendError(msg, 'Chargement demandes appro', xhr.status);
            $('#tbodyMesDemandes').html('<tr><td colspan="5" class="text-center text-danger py-2"><i class="fas fa-exclamation-triangle mr-1"></i> ' + msg + '</td></tr>');
        });
    }

    $('#btnEnvoyerDemande').on('click', function() {
        var devise  = $('#demandeDevise').val();
        var montant = $('#demandeMontant').val();
        var motif   = $('#demandeMotif').val();

        if (!devise || !montant || parseFloat(montant) <= 0) {
            showSystemMessage('error', 'Veuillez sélectionner une devise et saisir un montant valide.');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');

        $.ajax({
            url     : urlDemandePost,
            method  : 'POST',
            data    : { devise_code: devise, montant: montant, motif: motif },
            dataType: 'json',
            headers : { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .done(function(r) {
                if (r.success) {
                    showSystemMessage('success', r.message || 'Demande envoyée.');
                    $('#demandeDevise').val('');
                    $('#demandeMontant').val('');
                    $('#demandeMotif').val('');
                    chargerMesDemandes();
                } else {
                    showSystemMessage('error', r.message || 'Erreur inconnue.');
                }
            })
            .fail(function(xhr) {
                handleAjaxFail(xhr, 'Envoi demande approvisionnement');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Envoyer la demande');
            });
    });

    $('#btnRefreshDemandes').on('click', chargerMesDemandes);

    chargerMesDemandes();
    setInterval(chargerMesDemandes, 30000);
    @endif

    // ══════════════════════════════════════════════════════════════════
    // POLLING STATUT GUICHET — mise à jour temps réel toutes les 20s
    // ══════════════════════════════════════════════════════════════════
    @if($guichet)
    var _statutCourant = '{{ $statut }}';

    var _statutMap = {
        'OUVERT':         { couleur: 'success', icon: 'fa-door-open',      alerte: 'success',
                            msg: 'Votre guichet est <strong>ouvert</strong>. Vous pouvez démarrer les opérations.' },
        'SUSPENDU':       { couleur: 'warning', icon: 'fa-pause-circle',   alerte: 'warning',
                            msg: 'Guichet <strong>suspendu</strong>. Cliquez sur <em>Ouvrir</em> pour reprendre.' },
        'EN_VERIFICATION':{ couleur: 'info',    icon: 'fa-hourglass-half', alerte: 'info',
                            msg: 'Billetage soumis. <strong>En attente de validation du superviseur.</strong> Guichet bloqué.' },
        'FERME':          { couleur: 'danger',  icon: 'fa-door-closed',    alerte: 'danger',
                            msg: 'Guichet <strong>fermé</strong>. Ouvrez-le avant de démarrer les opérations.' },
    };

    function appliquerStatutGuichet(data) {
        var statut = data.statut_guichet;
        if (!statut) return;

        // ── Transition EN_VERIFICATION → autre statut : recharger la page ──
        if (_statutCourant === 'EN_VERIFICATION' && statut !== 'EN_VERIFICATION') {
            // Toast rapide avant reload
            var msgToast = statut === 'FERME'
                ? '<i class="fas fa-check-circle mr-1"></i> Clôture <strong>approuvée</strong> par le superviseur. Rechargement…'
                : '<i class="fas fa-times-circle mr-1"></i> Clôture <strong>rejetée</strong>. Guichet réouvert. Rechargement…';
            var cls = statut === 'FERME' ? 'success' : 'warning';
            $('body').append('<div id="_toastStatut" class="alert alert-' + cls + ' shadow" '
                + 'style="position:fixed;bottom:20px;right:20px;z-index:9999;min-width:320px;font-size:.97rem;">' + msgToast + '</div>');
            setTimeout(function() { location.reload(); }, 2000);
            return;
        }

        var m = _statutMap[statut] || _statutMap['FERME'];
        _statutCourant = statut;

        // Badge statut
        $('#statutBadge')
            .removeClass('badge-success badge-warning badge-info badge-danger badge-secondary')
            .addClass('badge-' + m.couleur);
        $('#statutBadge .statut-icon').attr('class', 'fas ' + m.icon + ' mr-1 statut-icon');
        $('#statutBadge .statut-text').text(statut);

        // Callout en-tête
        $('.callout').removeClass('callout-success callout-warning callout-info callout-danger')
            .addClass('callout-' + m.couleur);

        // Bordure supérieure carte guichet
        $('#guichetCard').removeClass('border-top-success border-top-warning border-top-info border-top-danger')
            .addClass('border-top-' + m.couleur);

        // Alerte informative
        var alertHtml = '<div class="alert alert-' + m.alerte + ' py-2 mb-0">'
            + '<i class="fas ' + m.icon + ' mr-1"></i>' + m.msg + '</div>';
        $('#guichetCard .card-body .mt-3').html(alertHtml);

        // Boutons d'action
        var ouvrirOff  = (statut === 'OUVERT' || statut === 'EN_VERIFICATION');
        var suspendreOff = (statut !== 'OUVERT');
        var fermerOff  = (statut === 'FERME' || statut === 'EN_VERIFICATION');
        $('[data-statut="OUVERT"]').prop('disabled', ouvrirOff).toggleClass('disabled', ouvrirOff);
        $('[data-statut="SUSPENDU"]').prop('disabled', suspendreOff).toggleClass('disabled', suspendreOff);
        $('[data-statut="FERME"]').prop('disabled', fermerOff).toggleClass('disabled', fermerOff);

        // Soldes
        if (data.soldes && data.soldes.length) {
            data.soldes.forEach(function(s) {
                var fmt = parseFloat(s.solde_en_caisse).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $('.solde-card').filter(function() {
                    return $(this).find('.solde-devise').text().trim() === s.devise_code;
                }).find('.solde-montant').text(fmt);
            });
        }
    }

    function pollStatutGuichet() {
        $.getJSON(urlPendante)
            .done(function(data) { appliquerStatutGuichet(data); })
            .fail(function() { /* silencieux */ });
    }

    // Premier appel immédiat + intervalle 20s
    pollStatutGuichet();
    setInterval(pollStatutGuichet, 20000);
    @endif

});
</script>
@endpush

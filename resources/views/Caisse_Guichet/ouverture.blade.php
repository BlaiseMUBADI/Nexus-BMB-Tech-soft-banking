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
            'OUVERT'   => 'success',
            'SUSPENDU' => 'warning',
            default    => 'danger',
        };
        $iconeStatut = match($statut) {
            'OUVERT'   => 'fa-door-open',
            'SUSPENDU' => 'fa-pause-circle',
            default    => 'fa-door-closed',
        };
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            {{-- ── En-tête session ───────────────────────────────────── --}}
            <div class="callout callout-{{ $couleur }} mb-4">
                <div class="d-flex justify-content-between align-items-center">
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
                    <span class="badge badge-{{ $couleur }} px-3 py-2" id="clock"
                          style="font-size:1.1rem; border-radius:8px;"></span>
                </div>
            </div>

            {{-- ── Carte principale du guichet ────────────────────────── --}}
            <div class="card shadow border-top-{{ $couleur }}" id="guichetCard">
                <div class="card-header bg-transparent pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-weight-bold mb-0">
                            <i class="fas fa-cash-register mr-2 text-secondary"></i>
                            {{ $guichet->code_guichet }}
                            <small class="text-muted font-weight-normal ml-1">
                                {{ $guichet->intitule }}
                            </small>
                        </h4>
                        <span class="badge badge-{{ $couleur }} px-3 py-2" id="statutBadge"
                              style="font-size:0.95rem;">
                            <i class="fas {{ $iconeStatut }} mr-1 statut-icon"></i>
                            <span class="statut-text">{{ $statut }}</span>
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    {{-- ── Soldes multi-devises ────────────────────────── --}}
                    <h6 class="text-muted text-uppercase mb-2" style="font-size:.75rem; letter-spacing:1px;">
                        <i class="fas fa-coins mr-1"></i> Soldes de caisse
                    </h6>

                    @if($guichet->soldes->isNotEmpty())
                    <div class="row mb-3">
                        @foreach($guichet->soldes->sortBy('devise_code') as $s)
                        <div class="col-sm-4 mb-2">
                            <div class="info-box shadow-none mb-0"
                                 style="min-height:0; background: var(--card-bg, #fff);">
                                <span class="info-box-icon bg-{{ $couleur === 'danger' ? 'secondary' : $couleur }} elevation-0"
                                      style="height:52px; width:52px; font-size:1rem; line-height:52px;">
                                    {{ $s->devise->symbole ?? $s->devise_code }}
                                </span>
                                <div class="info-box-content" style="padding: 6px 10px;">
                                    <span class="info-box-text small text-muted">{{ $s->devise_code }}</span>
                                    <span class="info-box-number"
                                          style="font-size:1.05rem; font-weight:700;">
                                        {{ number_format($s->solde_en_caisse, 2, ',', ' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-light border mb-3 py-2 small">
                        <i class="fas fa-info-circle mr-1 text-info"></i>
                        Aucun solde initialisé sur ce guichet.
                        Contactez l'administration pour initialiser les devises.
                    </div>
                    @endif

                    <hr class="my-3">

                    {{-- ── Boutons d'action ───────────────────────────── --}}
                    <h6 class="text-muted text-uppercase mb-3" style="font-size:.75rem; letter-spacing:1px;">
                        <i class="fas fa-toggle-on mr-1"></i> Actions
                    </h6>

                    <div class="row">
                        {{-- Ouvrir --}}
                        <div class="col-sm-4 mb-2">
                            <button class="btn btn-success btn-block btn-action {{ $statut === 'OUVERT' ? 'disabled' : '' }}"
                                    data-id="{{ $guichet->id }}"
                                    data-statut="OUVERT"
                                    data-code="{{ $guichet->code_guichet }}"
                                    {{ $statut === 'OUVERT' ? 'disabled' : '' }}
                                    title="{{ $statut === 'OUVERT' ? 'Guichet déjà ouvert' : 'Démarrer la session caisse' }}">
                                <i class="fas fa-door-open d-block fa-2x mb-1"></i>
                                Ouvrir
                            </button>
                        </div>

                        {{-- Suspendre --}}
                        <div class="col-sm-4 mb-2">
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
                        <div class="col-sm-4 mb-2">
                            <button class="btn btn-danger btn-block btn-action {{ $statut === 'FERME' ? 'disabled' : '' }}"
                                    data-id="{{ $guichet->id }}"
                                    data-statut="FERME"
                                    data-code="{{ $guichet->code_guichet }}"
                                    {{ $statut === 'FERME' ? 'disabled' : '' }}
                                    title="{{ $statut === 'FERME' ? 'Guichet déjà fermé' : 'Terminer la session caisse' }}">
                                <i class="fas fa-door-closed d-block fa-2x mb-1"></i>
                                Fermer
                            </button>
                        </div>
                    </div>

                    {{-- Message informatif selon statut --}}
                    <div class="mt-3">
                        @if($statut === 'OUVERT')
                        <div class="alert alert-success py-2 mb-0 small">
                            <i class="fas fa-check-circle mr-1"></i>
                            Votre guichet est <strong>ouvert</strong>. Vous pouvez démarrer les opérations.
                        </div>
                        @elseif($statut === 'SUSPENDU')
                        <div class="alert alert-warning py-2 mb-0 small">
                            <i class="fas fa-pause-circle mr-1"></i>
                            Guichet <strong>suspendu</strong>. Cliquez sur <em>Ouvrir</em> pour reprendre.
                        </div>
                        @else
                        <div class="alert alert-danger py-2 mb-0 small">
                            <i class="fas fa-lock mr-1"></i>
                            Guichet <strong>fermé</strong>. Ouvrez-le avant de démarrer les opérations.
                        </div>
                        @endif
                    </div>

                </div>{{-- /card-body --}}
            </div>{{-- /card --}}

        </div>
    </div>
    @endif

</div>
@endsection


@section('css')
<style>
    .border-top-success  { border-top: 4px solid #28a745 !important; }
    .border-top-warning  { border-top: 4px solid #ffc107 !important; }
    .border-top-danger   { border-top: 4px solid #dc3545 !important; }

    .btn-action { padding: 0.6rem 0.5rem; font-weight: 600; }
    .btn-action:not(:disabled) { cursor: pointer; }
    .btn:disabled { opacity: 0.45; cursor: not-allowed; }
    .info-box { border-radius: 6px; border: 1px solid rgba(0,0,0,.07); }
</style>
@endsection


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

    // ── Labels par statut ─────────────────────────────────────────────────
    var cfg = {
        'OUVERT'   : { label: 'ouvrir',    confirm: 'Démarrer votre session caisse sur le guichet' },
        'FERME'    : { label: 'fermer',    confirm: 'Fermer votre guichet et terminer la session' },
        'SUSPENDU' : { label: 'suspendre', confirm: 'Suspendre temporairement le guichet' },
    };

    // ── Changer statut ────────────────────────────────────────────────────
    $(document).on('click', '.btn-action:not(:disabled)', function () {
        var $btn   = $(this);
        var id     = $btn.data('id');
        var statut = $btn.data('statut');
        var code   = $btn.data('code');
        var info   = cfg[statut] || { label: statut, confirm: 'Modifier le guichet' };

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
                    setTimeout(function () { location.reload(); }, 700);
                })
                .fail(function (xhr) {
                    $btn.prop('disabled', false);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Erreur lors du changement de statut (' + xhr.status + ').';
                    showSystemMessage('error', msg);
                    // Re-rendre le bouton
                    location.reload();
                });
            },
            'Confirmer l\'action'
        );
    });

});
</script>
@endpush

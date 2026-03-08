{{-- ============================================================
     Vue Administration : Gestion des Guichets (multi-devises)
     Architecture : tb_caisses_guichets + tb_caisses_guichets_soldes
     Agent titulaire géré via tb_affectations (module RH)
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'Gestion des Guichets')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Guichets')

@section('content')
<div class="container-fluid">

    {{-- ══════════════════════════════════════════════════════════
         LIGNE 1 : MINI-DASHBOARD STATISTIQUES
         ══════════════════════════════════════════════════════════ --}}
    <div class="row mb-3">

        {{-- Total guichets --}}
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1">
                    <i class="fas fa-store-alt"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Guichets</span>
                    <span class="info-box-number">{{ $stats['total'] }}</span>
                </div>
            </div>
        </div>

        {{-- Guichets ouverts --}}
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-door-open"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Ouverts</span>
                    <span class="info-box-number">{{ $stats['ouverts'] }}</span>
                </div>
            </div>
        </div>

        {{-- Guichets fermés --}}
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-danger elevation-1">
                    <i class="fas fa-door-closed"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Fermés</span>
                    <span class="info-box-number">{{ $stats['fermes'] }}</span>
                </div>
            </div>
        </div>

        {{-- Guichets suspendus --}}
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1">
                    <i class="fas fa-pause-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Suspendus</span>
                    <span class="info-box-number">{{ $stats['suspendus'] }}</span>
                </div>
            </div>
        </div>

    </div>{{-- /dashboard --}}

    {{-- Ligne 1b : Agents affectés --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-teal elevation-1">
                    <i class="fas fa-user-check"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Avec titulaire</span>
                    <span class="info-box-number">{{ $stats['avec_titulaire'] }}</span>
                    <span class="progress-description">agent actif affecté</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-secondary elevation-1">
                    <i class="fas fa-user-times"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Sans titulaire</span>
                    <span class="info-box-number">{{ $stats['sans_titulaire'] }}</span>
                    <span class="progress-description">non encore affecté</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         LIGNE 2 : SOLDES TOTAUX PAR DEVISE (tous guichets)
         ══════════════════════════════════════════════════════════ --}}
    @if($soldesParDevise->count())
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header pb-1 pt-2">
                    <h6 class="mb-0">
                        <i class="fas fa-coins mr-1 text-info"></i>
                        Soldes globaux en caisse (cumul de tous les guichets)
                    </h6>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($soldesParDevise as $s)
                        <div class="solde-devise-card px-3 py-2 mr-3 mb-1">
                            <span class="devise-label">
                                {{ $s->devise->symbole ?? $s->devise_code }}
                                <small class="text-muted">({{ $s->devise_code }})</small>
                            </span>
                            <span class="devise-montant ml-2">
                                {{ number_format($s->total, 2, ',', ' ') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         LIGNE 3 : ONGLETS — Gestion Guichets
         ══════════════════════════════════════════════════════════ --}}
    <div class="card card-primary card-outline">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs pt-2 px-2" id="guichetsTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="tab-gestion-link"
                       data-toggle="tab" href="#tab-gestion" role="tab">
                        <i class="fas fa-store-alt mr-1"></i> Gestion Guichets
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-2">
            <div class="tab-content" id="guichetsTabContent">

                {{-- ══════════════════════════════════════════════
                     ONGLET 1 — GESTION GUICHETS
                     ══════════════════════════════════════════════ --}}
                <div class="tab-pane fade show active" id="tab-gestion" role="tabpanel">
                    <div class="row">

                        {{-- ──────────────── FORMULAIRE CRÉATION ──────────────── --}}
                        <div class="col-md-4">
                            <div class="card card-primary card-outline">
                                <div class="card-header pb-0">
                                    <h5><i class="fas fa-plus-circle mr-1 text-primary"></i> Créer un Guichet</h5>
                                </div>
                                <div class="card-body">
                                    <form id="guichetForm">
                                        @csrf

                                        {{-- Code guichet --}}
                                        <div class="form-group">
                                            <label for="code_guichet">
                                                Code Guichet <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="code_guichet" id="code_guichet"
                                                class="form-control text-uppercase"
                                                placeholder="Ex : G01, G02 ..."
                                                maxlength="20" required autocomplete="off">
                                            <small class="text-muted">Identifiant unique (ex : G01, CAISSE-USD)</small>
                                        </div>

                                        {{-- Type de guichet --}}
                                        <div class="form-group">
                                            <label for="type_guichet">
                                                Type <span class="text-danger">*</span>
                                            </label>
                                            <select name="type_guichet" id="type_guichet" class="form-control" required>
                                                <option value="FIXE" selected>FIXE — Bureau (fonds de roulement matin)</option>
                                                <option value="MOBILE">MOBILE — Agent terrain (démarre à 0)</option>
                                            </select>
                                            <small id="type_guichet_help" class="text-muted">
                                                <span id="help_FIXE">Le guichet doit être alimenté chaque matin.</span>
                                                <span id="help_MOBILE" class="d-none">L'agent récupère des fonds sur le terrain. Pas d'alimentation matinale.</span>
                                            </small>
                                        </div>

                                        {{-- Intitulé --}}
                                        <div class="form-group">
                                            <label for="intitule">
                                                Intitulé <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="intitule" id="intitule"
                                                class="form-control"
                                                placeholder="Ex : Guichet Principal CDF"
                                                maxlength="100" required autocomplete="off">
                                        </div>

                                        {{-- Devises gérées (checkboxes multi-sélection) --}}
                                        <div class="form-group">
                                            <label>
                                                Devises gérées <span class="text-danger">*</span>
                                            </label>
                                            <small class="d-block text-muted mb-2">
                                                Sélectionnez au moins une devise. Un solde initial à 0 sera créé pour chacune.
                                            </small>
                                            <div class="devises-checkbox-grid border rounded p-2" style="max-height:180px; overflow-y:auto;">
                                                @forelse($devises as $d)
                                                <div class="custom-control custom-checkbox mb-1">
                                                    <input class="custom-control-input devise-cb"
                                                        type="checkbox"
                                                        name="devises[]"
                                                        value="{{ $d->code_iso }}"
                                                        id="devise_{{ $d->code_iso }}">
                                                    <label class="custom-control-label" for="devise_{{ $d->code_iso }}">
                                                        <span class="badge badge-secondary mr-1">{{ $d->code_iso }}</span>
                                                        {{ $d->nom }}
                                                        @if($d->symbole)
                                                            <small class="text-muted">({{ $d->symbole }})</small>
                                                        @endif
                                                    </label>
                                                </div>
                                                @empty
                                                <p class="text-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Aucune devise configurée.
                                                </p>
                                                @endforelse
                                            </div>
                                            <small id="devises-error" class="text-danger d-none">
                                                Veuillez sélectionner au moins une devise.
                                            </small>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-plus-circle mr-1"></i> Créer le guichet
                                        </button>

                                        <div class="alert alert-info mt-3 mb-0 py-2 px-3" style="font-size:.82rem;">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            L'agent titulaire est affecté via le module
                                            <strong>RH → Affectations</strong>.
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- ──────────────── TABLEAU LISTE ──────────────── --}}
                        <div class="col-md-8">
                            <div class="card card-info card-outline">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h3 class="card-title mb-0">
                                        <i class="fas fa-list mr-2"></i> Liste des Guichets
                                    </h3>
                                    <div class="card-tools">
                                        <span class="badge badge-info badge-pill">{{ $guichets->count() }}</span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="px-2 pt-2">
                                        <input type="text" id="searchGuichets" class="form-control form-control-sm"
                                               placeholder="🔍 Rechercher un guichet…">
                                    </div>
                                    <div class="table-responsive mt-1" style="max-height:640px; overflow-y:auto;">
                                        <table class="table table-bordered table-striped table-sm mb-0" id="guichetsTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="width:35px">#</th>
                                                    <th>Code</th>
                                                    <th>Intitulé</th>
                                                    <th>Devises &amp; Soldes</th>
                                                    <th>Titulaire actif</th>
                                                    <th class="text-center" style="width:100px">Statut</th>
                                                    <th class="text-center" style="width:70px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($guichets as $g)
                                                <tr id="row-guichet-{{ $g->id }}">
                                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                                    <td><strong>{{ $g->code_guichet }}</strong></td>
                                                    <td>{{ $g->intitule }}</td>

                                                    {{-- Soldes par devise --}}
                                                    <td>
                                                        @forelse($g->soldes as $s)
                                                        <span class="solde-badge mr-1 mb-1" title="{{ $s->devise->nom ?? $s->devise_code }}">
                                                            <span class="solde-devise-code">{{ $s->devise_code }}</span>
                                                            <span class="solde-montant ml-1">{{ number_format($s->solde_en_caisse, 2, ',', ' ') }}</span>
                                                        </span>
                                                        @empty
                                                        <span class="text-muted small">
                                                            <i class="fas fa-minus-circle mr-1"></i>Aucune devise
                                                        </span>
                                                        @endforelse
                                                    </td>

                                                    {{-- Titulaire actif --}}
                                                    <td>
                                                        @if($g->affectationActive && $g->affectationActive->agent)
                                                            @php $ag = $g->affectationActive->agent; @endphp
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge badge-success mr-1">
                                                                    <i class="fas fa-user-check mr-1"></i>
                                                                    <code style="color:inherit;font-size:.8em">{{ $ag->matricule }}</code>
                                                                </span>
                                                            </div>
                                                            <small class="text-muted d-block" style="font-size:.75rem">
                                                                {{ $ag->nom }} {{ $ag->postnom }}
                                                            </small>
                                                        @else
                                                            <span class="text-muted small">
                                                                <i class="fas fa-user-slash mr-1"></i>Non affecté
                                                            </span>
                                                        @endif
                                                    </td>

                                                    {{-- Statut --}}
                                                    <td class="text-center">
                                                        @if($g->statut_operationnel === 'OUVERT')
                                                            <span class="badge badge-success">OUVERT</span>
                                                        @elseif($g->statut_operationnel === 'FERME')
                                                            <span class="badge badge-danger">FERMÉ</span>
                                                        @else
                                                            <span class="badge badge-warning">SUSPENDU</span>
                                                        @endif
                                                    </td>

                                                    {{-- Actions --}}
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-danger btn-delete-guichet"
                                                            data-id="{{ $g->id }}"
                                                            data-code="{{ $g->code_guichet }}"
                                                            title="Supprimer (seulement si FERMÉ et soldes nuls)">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-5">
                                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                        Aucun guichet enregistré.
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /row tab-gestion --}}
                </div>{{-- /tab-pane tab-gestion --}}

            </div>{{-- /tab-content --}}
        </div>{{-- /card-body --}}
    </div>{{-- /card tabs --}}

</div>{{-- /container-fluid --}}
@endsection


@push('css')
<style>
    /* ══ DARK ADMINLTE — En-têtes tableau ═══════════════════════ */
    #guichetsTable thead th {
        background-color: #2c3136 !important;
        color: #c2c7d0 !important;
        border-color: #3d4349 !important;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    /* ── Survol lignes ──────────────────────────────────────── */
    #guichetsTable tbody tr:hover > td {
        background-color: rgba(0, 123, 255, 0.15) !important;
        color: #fff !important;
    }

    /* ── Badges soldes par devise (dark-friendly) ───────────── */
    .solde-badge {
        display: inline-flex;
        align-items: center;
        background-color: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 4px;
        padding: 2px 8px;
        font-size: .78rem;
    }
    .solde-badge .solde-devise-code {
        font-weight: 700;
        color: #74c0fc;
    }
    .solde-badge .solde-montant {
        color: #c2c7d0;
    }

    /* ── Grille devises checkbox ────────────────────────────── */
    .devises-checkbox-grid {
        background: transparent;
        border-color: rgba(255,255,255,.12) !important;
    }
    .devises-checkbox-grid .custom-control-label {
        cursor: pointer;
        font-size: .88rem;
        color: inherit;
    }
    .devises-checkbox-grid .custom-control-input ~ .custom-control-label::before {
        background-color: rgba(255,255,255,.08);
        border-color: rgba(255,255,255,.3);
    }

    /* ── Cartes soldes globaux par devise ───────────────────── */
    .solde-devise-card {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
    }
    .solde-devise-card .devise-label   { font-weight: 600; font-size: .88rem; }
    .solde-devise-card .devise-montant { font-size: 1rem; font-weight: 700; }

    /* ── Input recherche ────────────────────────────────────── */
    #searchGuichets { margin-bottom: 0; }

</style>
@endpush


@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept'      : 'application/json'
        }
    });

    // ── Recherche live : tableau guichets ─────────────────────
    $('#searchGuichets').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('#guichetsTable tbody tr').each(function () {
            $(this).toggle(q === '' || $(this).text().toLowerCase().indexOf(q) !== -1);
        });
    });

    // ══════════════════════════════════════════════════════
    // CRÉER GUICHET (multi-devises)
    // ══════════════════════════════════════════════════════

    // Aide contextuelle selon type_guichet
    $('#type_guichet').on('change', function () {
        var t = $(this).val();
        $('#help_FIXE').toggleClass('d-none', t !== 'FIXE');
        $('#help_MOBILE').toggleClass('d-none', t !== 'MOBILE');
    });

    $('#guichetForm').on('submit', function (e) {
        e.preventDefault();

        // Validation côté client : au moins une devise cochée
        var devisesCochees = $('input.devise-cb:checked');
        if (devisesCochees.length === 0) {
            $('#devises-error').removeClass('d-none');
            return;
        }
        $('#devises-error').addClass('d-none');

        showSystemMessage('info', 'Création en cours…');
        var $btn = $(this).find('[type=submit]');
        $btn.prop('disabled', true);

        $.ajax({
            type    : 'POST',
            url     : '{{ route("administration.guichets.store") }}',
            data    : $(this).serialize(),
            dataType: 'json'
        })
        .done(function (data) {
            if (data.success) {
                showSystemMessage('success', data.message);
                $('#guichetForm')[0].reset();
                setTimeout(function () { location.reload(); }, 1200);
            } else {
                showSystemMessage('error', data.message || 'Erreur.');
                $btn.prop('disabled', false);
            }
        })
        .fail(function (xhr) {
            handleAjaxFail(xhr, 'Ajout guichet');
            $btn.prop('disabled', false);
        });
    });

    // Masquer l'erreur dès qu'on coche une devise
    $(document).on('change', 'input.devise-cb', function () {
        if ($('input.devise-cb:checked').length > 0) {
            $('#devises-error').addClass('d-none');
        }
    });

    // ══════════════════════════════════════════════════════
    // SUPPRIMER GUICHET
    // ══════════════════════════════════════════════════════
    $(document).on('click', '.btn-delete-guichet', function () {
        var id   = $(this).data('id');
        var code = $(this).data('code');
        var $btn = $(this);

        showUniversalConfirm(
            'Supprimer le guichet <strong>' + code + '</strong> ?<br>' +
            '<small class="text-danger">Uniquement si <strong>FERMÉ</strong> et tous les soldes sont à <strong>0</strong>.</small>',
            function () {
                $btn.prop('disabled', true);
                var deleteUrl = '{{ route("administration.guichets.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id);

                $.ajax({
                    type    : 'POST',
                    url     : deleteUrl,
                    data    : { _method: 'DELETE' },
                    dataType: 'json'
                })
                .done(function (data) {
                    if (data.success) {
                        showSystemMessage('success', data.message);
                        $('#row-guichet-' + id).fadeOut(400, function () { $(this).remove(); });
                        var badge = $('.card-header .badge-info');
                        badge.text(parseInt(badge.text()) - 1);
                    } else {
                        showSystemMessage('error', data.message || 'Erreur.');
                        $btn.prop('disabled', false);
                    }
                })
                .fail(function (xhr) {
                    handleAjaxFail(xhr, 'Suppression guichet');
                    $btn.prop('disabled', false);
                });
            },
            { title: 'Confirmer la suppression' }
        );
    });

});
</script>
@endpush


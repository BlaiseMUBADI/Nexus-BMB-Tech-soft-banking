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
         LIGNE 3 : FORMULAIRE | TABLEAU
         ══════════════════════════════════════════════════════════ --}}
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
            <div class="card">
                <div class="card-header pb-0 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-list mr-1"></i> Liste des Guichets</h5>
                    <span class="badge badge-primary">{{ $guichets->count() }} guichet(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:680px; overflow-y:auto;">
                        <table class="table table-bordered table-striped table-hover mb-0" id="guichetsTable">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th style="width:35px">#</th>
                                    <th>Code</th>
                                    <th>Intitulé</th>
                                    <th>Devises &amp; Soldes</th>
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
                                        <span class="badge badge-light border mr-1 mb-1 px-2 py-1" title="{{ $s->devise->nom ?? $s->devise_code }}">
                                            <span class="text-secondary font-weight-bold">{{ $s->devise_code }}</span>
                                            <span class="ml-1">{{ number_format($s->solde_en_caisse, 2, ',', ' ') }}</span>
                                        </span>
                                        @empty
                                        <span class="text-muted small">
                                            <i class="fas fa-minus-circle mr-1"></i>Aucune devise
                                        </span>
                                        @endforelse
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
                                    <td colspan="6" class="text-center text-muted py-5">
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

    </div>{{-- /row --}}
</div>{{-- /container-fluid --}}
@endsection


@section('css')
<style>
    /* Card outline primary */
    .card-primary.card-outline { border-top: 3px solid #007bff; }

    /* ── Grille devises checkbox ──────────────────────────────── */
    .devises-checkbox-grid {
        background: transparent;
        border-color: rgba(255,255,255,.12) !important;
    }
    .devises-checkbox-grid .custom-control-label {
        cursor: pointer;
        font-size: .88rem;
        color: inherit;          /* hérite la couleur du thème (blanc en dark) */
    }
    /* Fix: le carré de la checkbox AdminLTE en thème sombre */
    .devises-checkbox-grid .custom-control-input ~ .custom-control-label::before {
        background-color: rgba(255,255,255,.08);
        border-color: rgba(255,255,255,.3);
    }

    /* ── Cartes soldes globaux par devise ─────────────────────── */
    .solde-devise-card {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
    }
    .solde-devise-card .devise-label  { font-weight: 600; font-size: .88rem; }
    .solde-devise-card .devise-montant { font-size: 1rem; font-weight: 700; }

    /* ── Tableau ──────────────────────────────────────────────── */
    #guichetsTable thead.sticky-top th {
        top: 0;
        z-index: 2;
    }
</style>
@endsection


@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ══════════════════════════════════════════════════════
    // CRÉER GUICHET (multi-devises)
    // ══════════════════════════════════════════════════════
    $('#guichetForm').on('submit', function (e) {
        e.preventDefault();

        // Validation côté client : au moins une devise cochée
        let devisesCochees = $('input.devise-cb:checked');
        if (devisesCochees.length === 0) {
            $('#devises-error').removeClass('d-none');
            return;
        }
        $('#devises-error').addClass('d-none');

        showSystemMessage('info', 'Création en cours...');

        $.post("{{ route('administration.guichets.store') }}", $(this).serialize())
            .done(function (response) {
                showSystemMessage('success', response.message);
                $('#guichetForm')[0].reset();
                setTimeout(() => location.reload(), 1200);
            })
            .fail(function (xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let msg = Object.values(errors).flat().join('<br>');
                    showSystemMessage('error', msg);
                } else {
                    showSystemMessage('error', xhr.responseJSON?.message || 'Erreur lors de la création.');
                }
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
        let id   = $(this).data('id');
        let code = $(this).data('code');

        showUniversalConfirm(
            `Supprimer le guichet <strong>${code}</strong> ?<br>
             <small class="text-danger">
                 Uniquement si <strong>FERMÉ</strong> et tous les soldes sont à <strong>0</strong>.
             </small>`,
            function () {
                $.ajax({
                    url  : `{{ url('administration/guichets') }}/${id}`,
                    type : 'DELETE',
                    success: function (response) {
                        showSystemMessage('success', response.message);
                        $(`#row-guichet-${id}`).fadeOut(400, function () { $(this).remove(); });
                        // Décrémenter le compteur dans le badge header
                        let badge = $('.card-header .badge-primary');
                        let n = parseInt(badge.text()) - 1;
                        badge.text(n + ' guichet(s)');
                    },
                    error: function (xhr) {
                        showSystemMessage('error', xhr.responseJSON?.message || 'Erreur lors de la suppression.');
                    }
                });
            },
            'Confirmer la suppression'
        );
    });

});
</script>
@endpush

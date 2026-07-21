@extends('layouts.app')

@section('title', 'Proposer un Virement Bancaire')
@section('page_title', 'Proposer un Virement Bancaire')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
<li class="breadcrumb-item"><a href="{{ route('comptabilite.dashboard') }}">Comptabilité</a></li>
<li class="breadcrumb-item"><a href="{{ route('comptabilite.virements.index') }}">Virements bancaires</a></li>
<li class="breadcrumb-item active">Proposer</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-exchange-alt mr-1"></i>Nouvelle demande de virement</h6>
                </div>
                <div class="card-body">

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Cette demande sera soumise à validation (Gérant/Directeur) avant toute exécution réelle. Les comptes de garantie (GTC) ne sont pas sélectionnables.
                    </div>

                    <form id="formVirement">
                        @csrf

                        {{-- ── Compte source ─────────────────────────────────────── --}}
                        <div class="card card-outline card-secondary mb-3">
                            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-arrow-up mr-1 text-danger"></i>Compte source (débité)</h6></div>
                            <div class="card-body py-2">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Rechercher client ou compte source</label>
                                    <input type="text" id="searchSource" class="form-control form-control-sm" placeholder="Nom du client ou numéro de compte…" autocomplete="off">
                                    <div id="resultsSource" class="list-group mt-1" style="position:relative; z-index:10; max-height:220px; overflow-y:auto;"></div>
                                </div>
                                <input type="hidden" id="compte_source_code" name="compte_source_code" required>
                                <div id="compteSourceInfo" class="small text-muted"></div>
                            </div>
                        </div>

                        {{-- ── Compte destination ────────────────────────────────── --}}
                        <div class="card card-outline card-secondary mb-3">
                            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-arrow-down mr-1 text-success"></i>Compte destination (crédité)</h6></div>
                            <div class="card-body py-2">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Rechercher client ou compte destination</label>
                                    <input type="text" id="searchDest" class="form-control form-control-sm" placeholder="Nom du client ou numéro de compte…" autocomplete="off">
                                    <div id="resultsDest" class="list-group mt-1" style="position:relative; z-index:10; max-height:220px; overflow-y:auto;"></div>
                                </div>
                                <input type="hidden" id="compte_dest_code" name="compte_dest_code" required>
                                <div id="compteDestInfo" class="small text-muted"></div>
                            </div>
                        </div>

                        {{-- ── Montant / devise / taux ───────────────────────────── --}}
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold">Montant (devise source) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" id="montant_source" name="montant_source" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-4 form-group" id="tauxChangeGroup" style="display:none;">
                                <label class="small font-weight-bold">Taux de change ACTIF</label>
                                <div class="alert alert-warning py-2 px-2 mb-1 text-center" style="font-size:1rem; font-weight:bold;" id="taux_change_affiche">—</div>
                                <small class="text-muted" id="tauxInfoLabel"></small>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold">Montant destination</label>
                                <div class="alert alert-success py-2 px-2 mb-1 text-center" style="font-size:1rem; font-weight:bold;" id="montantDestApercu">—</div>
                            </div>
                        </div>
                        <div class="alert alert-info small" id="tauxMemeDeviseInfo" style="display:none;">
                            <i class="fas fa-info-circle mr-1"></i>Mêmes devises source et destination — aucune conversion nécessaire.
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold">Motif du virement <span class="text-danger">*</span></label>
                            <textarea id="motif" name="motif" class="form-control form-control-sm" rows="2" maxlength="500" required></textarea>
                        </div>

                        <div id="alertZone"></div>

                        <button type="submit" class="btn btn-primary btn-block" id="btnSoumettre">
                            <i class="fas fa-paper-plane mr-1"></i>Soumettre la demande de virement
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    #resultsSource, #resultsDest {
        background: #23272b;
        border: 1px solid #495057;
        border-radius: 4px;
    }
    .list-group-item-action {
        cursor: pointer;
        padding: .5rem .7rem;
        font-size: .85rem;
        background: #2b3238;
        color: #f1f1f1 !important;
        border-color: #495057 !important;
    }
    .list-group-item-action strong {
        color: #ffffff;
    }
    .list-group-item-action small {
        color: #b7bec4 !important;
    }
    .list-group-item-action:hover,
    .list-group-item-action:focus {
        background: #1f5fa8 !important;
        color: #ffffff !important;
    }
    .list-group-item-action:hover small,
    .list-group-item-action:hover strong {
        color: #ffffff !important;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' } });

    var urlSearch    = '{{ route("comptabilite.virements.rechercher-compte") }}';
    var urlStore     = '{{ route("comptabilite.virements.store") }}';
    var urlTauxActif = '{{ route("administration.devises-taux.actif") }}';

    var compteSource = null;
    var compteDest = null;
    window._tauxActifCourant = null;

    function setupSearch(inputId, resultsId, onSelect) {
        var timer = null;
        $('#' + inputId).on('input', function () {
            var q = $(this).val();
            clearTimeout(timer);
            if (q.length < 2) { $('#' + resultsId).empty(); return; }
            timer = setTimeout(function () {
                $.getJSON(urlSearch, { q: q }).done(function (data) {
                    var html = '';
                    if (!data.length) {
                        html = '<div class="list-group-item text-muted small">Aucun résultat.</div>';
                    }
                    $.each(data, function (i, c) {
                        html += '<a href="#" class="list-group-item list-group-item-action" '
                            + 'data-compte=\'' + JSON.stringify(c).replace(/'/g, "&#39;") + '\'>'
                            + '<strong>' + c.code_compte + '</strong> (' + c.type + ' - ' + c.devise + ') — ' + c.client_nom
                            + '<br><small class="text-muted">Solde disponible : ' + c.solde_reel + ' ' + c.devise + '</small>'
                            + '</a>';
                    });
                    $('#' + resultsId).html(html);
                });
            }, 300);
        });

        $(document).on('click', '#' + resultsId + ' a', function (e) {
            e.preventDefault();
            var c = $(this).data('compte');
            onSelect(c);
            $('#' + resultsId).empty();
        });
    }

    setupSearch('searchSource', 'resultsSource', function (c) {
        compteSource = c;
        $('#compte_source_code').val(c.code_compte);
        $('#searchSource').val(c.client_nom + ' — ' + c.code_compte);
        $('#compteSourceInfo').html('Type : <strong>' + c.type + '</strong> — Devise : <strong>' + c.devise + '</strong> — Solde disponible : <strong>' + c.solde_reel + ' ' + c.devise + '</strong>');
        refreshDeviseUI();
    });

    setupSearch('searchDest', 'resultsDest', function (c) {
        compteDest = c;
        $('#compte_dest_code').val(c.code_compte);
        $('#searchDest').val(c.client_nom + ' — ' + c.code_compte);
        $('#compteDestInfo').html('Type : <strong>' + c.type + '</strong> — Devise : <strong>' + c.devise + '</strong> — Solde actuel : <strong>' + c.solde_reel + ' ' + c.devise + '</strong>');
        refreshDeviseUI();
    });

    function fmtNombre(n, decimales) {
        return parseFloat(n).toLocaleString('fr-FR', { minimumFractionDigits: decimales, maximumFractionDigits: decimales });
    }

    function refreshDeviseUI() {
        window._tauxActifCourant = null;
        $('#taux_change_affiche').text('—');
        $('#tauxInfoLabel').text('').removeClass('text-danger');
        $('#tauxMemeDeviseInfo').hide();
        $('#tauxChangeGroup').hide();

        if (!compteSource || !compteDest) { calculerApercu(); return; }

        if (compteSource.devise === compteDest.devise) {
            $('#tauxMemeDeviseInfo').show();
            calculerApercu();
            return;
        }

        $('#tauxChangeGroup').show();

        // Taux ACTIF consulté automatiquement — jamais saisi librement par le comptable.
        $.getJSON(urlTauxActif, { source: compteSource.devise, destination: compteDest.devise })
            .done(function (r) {
                if (r.success) {
                    window._tauxActifCourant = parseFloat(r.taux);
                    $('#taux_change_affiche').html('1 ' + compteSource.devise + ' = <strong>' + fmtNombre(r.taux, 4) + '</strong> ' + compteDest.devise);
                    $('#tauxInfoLabel').removeClass('text-danger')
                        .text(r.date_fin ? ('Actif du ' + r.date_debut + ' au ' + r.date_fin) : ('Actif depuis le ' + (r.date_debut || '—')));
                }
                calculerApercu();
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Aucun taux actif trouvé pour cette paire de devises.';
                $('#taux_change_affiche').text('—');
                $('#tauxInfoLabel').addClass('text-danger').text(msg);
                calculerApercu();
            });
    }

    function calculerApercu() {
        var montant = parseFloat($('#montant_source').val()) || 0;
        if (compteSource && compteDest && compteSource.devise !== compteDest.devise) {
            var taux = window._tauxActifCourant || 0;
            $('#montantDestApercu').text(taux > 0 && montant > 0 ? (fmtNombre(montant * taux, 2) + ' ' + compteDest.devise) : '—');
        } else {
            $('#montantDestApercu').text(montant > 0 ? (fmtNombre(montant, 2) + ' ' + (compteDest ? compteDest.devise : '')) : '—');
        }
    }

    $('#montant_source').on('input', calculerApercu);

    $('#formVirement').on('submit', function (e) {
        e.preventDefault();
        $('#alertZone').empty();

        if (!compteSource || !compteDest) {
            showSystemMessage('warning', 'Sélectionnez un compte source et un compte destination.');
            return;
        }
        if (compteSource.code_compte === compteDest.code_compte) {
            showSystemMessage('warning', 'Le compte source et le compte destination doivent être différents.');
            return;
        }
        if (compteSource.devise !== compteDest.devise && !window._tauxActifCourant) {
            showSystemMessage('error', 'Aucun taux de change actif n\'est défini pour cette paire de devises. Contactez la Trésorerie.');
            return;
        }

        var payload = {
            compte_source_code: compteSource.code_compte,
            compte_dest_code: compteDest.code_compte,
            montant_source: $('#montant_source').val(),
            motif: $('#motif').val(),
        };

        $('#btnSoumettre').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Envoi…');
        $.post(urlStore, payload)
            .done(function (r) {
                showSystemMessage('success', r.message || 'Demande créée.');
                setTimeout(function () {
                    window.location.href = '{{ route("comptabilite.virements.index") }}';
                }, 1200);
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Création demande de virement');
                $('#btnSoumettre').prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i>Soumettre la demande de virement');
            });
    });
});
</script>
@endpush

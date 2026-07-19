@extends('layouts.app')

@section('title', 'Demandes de Modification — Superviseur')
@section('page_title', 'Demandes de Modification / Suppression')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
<li class="breadcrumb-item active">Demandes de modification</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- ── Filtres ─────────────────────────────────────────────────── --}}
    <div class="card card-outline card-warning mb-3 shadow-sm">
        <div class="card-header py-2">
            <h6 class="card-title mb-0"><i class="fas fa-filter mr-1"></i>Filtres</h6>
        </div>
        <div class="card-body py-2">
            <div class="row g-2 align-items-end demande-filter-row">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="small font-weight-bold mb-1">Statut</label>
                    <select id="filtreStatut" class="form-control form-control-sm">
                        <option value="">— Tous —</option>
                        <option value="EN_ATTENTE" selected>En attente</option>
                        <option value="APPROUVEE">Approuvée</option>
                        <option value="REJETEE">Rejetée</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="small font-weight-bold mb-1">Type de demande</label>
                    <select id="filtreType" class="form-control form-control-sm">
                        <option value="">— Tous —</option>
                        <option value="MODIFICATION">Modification</option>
                        <option value="SUPPRESSION">Suppression</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="small font-weight-bold mb-1">Date début</label>
                    <input type="date" id="filtreDebut" class="form-control form-control-sm"
                           value="{{ today()->toDateString() }}">
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="small font-weight-bold mb-1">Date fin</label>
                    <input type="date" id="filtreFin" class="form-control form-control-sm"
                           value="{{ today()->toDateString() }}">
                </div>
                <div class="col-12 col-sm-6 col-md-2 demande-filter-action">
                    <button id="btnCharger" class="btn btn-sm btn-warning btn-block">
                        <i class="fas fa-search mr-1"></i>Rechercher
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tableau des demandes ─────────────────────────────────────── --}}
    <div class="card card-outline card-warning shadow-sm">
        <div class="card-header py-2 d-flex align-items-center justify-content-between demandes-head-row">
            <h6 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i>
                Demandes
                <span class="badge badge-warning ml-1" id="badgeCount">0</span>
            </h6>
            <button class="btn btn-xs btn-outline-secondary" id="btnRefresh" title="Actualiser">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="thead-dark" style="font-size:.80rem;">
                        <tr>
                            <th class="text-center">Type</th>
                            <th>Référence opération</th>
                            <th>Guichet / Agent</th>
                            <th>Compte / Client</th>
                            <th class="text-right">Ancien montant</th>
                            <th class="text-right">Nouv. montant</th>
                            <th>Motif</th>
                            <th>Date demande</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDemandes">
                        <tr><td colspan="10" class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Chargement…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — Approuver une demande
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalApprouver" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header py-2 bg-success text-white">
                <h6 class="modal-title mb-0"><i class="fas fa-check-circle mr-2"></i>Approuver la demande</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="small mb-2 text-muted" id="approuverResume">—</p>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Commentaire (optionnel)</label>
                    <textarea id="inpCommentaireApp" class="form-control form-control-sm" rows="3"
                              placeholder="Commentaire du superviseur…" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                <button class="btn btn-sm btn-success font-weight-bold" id="btnConfirmerApp">
                    <i class="fas fa-check mr-1"></i>Approuver
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — Rejeter une demande
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalRejeter" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header py-2 bg-danger text-white">
                <h6 class="modal-title mb-0"><i class="fas fa-times-circle mr-2"></i>Rejeter la demande</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="small mb-2 text-muted" id="rejeterResume">—</p>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Motif du rejet <span class="text-danger">*</span></label>
                    <textarea id="inpCommentaireRej" class="form-control form-control-sm" rows="3"
                              placeholder="Expliquez pourquoi vous rejetez cette demande…" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                <button class="btn btn-sm btn-danger font-weight-bold" id="btnConfirmerRej">
                    <i class="fas fa-times mr-1"></i>Rejeter
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
    .table-sm td { font-size: .85rem; vertical-align: middle; }
    .table-sm th { font-size: .80rem; }
    .badge-sm { font-size: .70rem; padding: .15em .45em; }
    .btn-xs { padding: .15rem .45rem; font-size: .78rem; }

    @media (max-width: 767.98px) {
        .demandes-head-row {
            flex-wrap: wrap;
            gap: .5rem;
            align-items: flex-start !important;
        }

        #btnRefresh {
            width: 100%;
        }

        .demande-filter-action .btn {
            width: 100%;
        }

        #tbodyDemandes td,
        #tbodyDemandes th {
            white-space: nowrap;
        }
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    var urlData      = '{{ route("caisses.demandes.modification.data") }}';
    var urlApprouver = '{{ route("caisses.demandes.modification.approuver", ["id" => "__ID__"]) }}';
    var urlRejeter   = '{{ route("caisses.demandes.modification.rejeter",   ["id" => "__ID__"]) }}';

    // ── Charger les demandes ─────────────────────────────────────
    function chargerDemandes() {
        var params = {
            statut     : $('#filtreStatut').val(),
            type_demande: $('#filtreType').val(),
            date_debut : $('#filtreDebut').val(),
            date_fin   : $('#filtreFin').val()
        };
        $('#tbodyDemandes').html(
            '<tr><td colspan="10" class="text-center py-4 text-muted">'
            + '<i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Chargement…</td></tr>'
        );
        $.getJSON(urlData, params)
        .done(function (data) {
            var rows = Array.isArray(data) ? data : [];
            $('#badgeCount').text(rows.length);
            if (!rows.length) {
                $('#tbodyDemandes').html(
                    '<tr><td colspan="10" class="text-center py-4 text-muted">'
                    + '<i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune demande trouvée.</td></tr>'
                );
                return;
            }
            var html = '';
            $.each(rows, function(i, d) {
                var typeBadge = d.type_demande === 'SUPPRESSION'
                    ? '<span class="badge badge-danger badge-sm">Suppression</span>'
                    : '<span class="badge badge-warning badge-sm text-dark">Modification</span>';
                var statutBadge;
                if (d.statut === 'EN_ATTENTE') {
                    statutBadge = '<span class="badge badge-info badge-sm">En attente</span>';
                } else if (d.statut === 'APPROUVEE') {
                    statutBadge = '<span class="badge badge-success badge-sm">Approuvée</span>';
                } else {
                    statutBadge = '<span class="badge badge-danger badge-sm">Rejetée</span>';
                }
                var actions = '';
                if (d.statut === 'EN_ATTENTE') {
                    actions = '<button class="btn btn-xs btn-success btn-approuver mr-1"'
                        + ' data-id="' + d.id + '"'
                        + ' data-resume="' + escHtml(d.reference_operation) + ' — ' + escHtml(d.client_nom) + '"'
                        + ' title="Approuver"><i class="fas fa-check"></i></button>'
                        + '<button class="btn btn-xs btn-danger btn-rejeter"'
                        + ' data-id="' + d.id + '"'
                        + ' data-resume="' + escHtml(d.reference_operation) + ' — ' + escHtml(d.client_nom) + '"'
                        + ' title="Rejeter"><i class="fas fa-times"></i></button>';
                } else {
                    actions = '<small class="text-muted">' + escHtml(d.traitee_le || '') + '</small>';
                    if (d.commentaire_superviseur) {
                        actions += '<br><em class="text-muted" style="font-size:.75rem;">' + escHtml(d.commentaire_superviseur) + '</em>';
                    }
                }
                var nouvMontant = d.type_demande === 'MODIFICATION' && d.nouveau_montant
                    ? '<span class="text-primary font-weight-bold">' + escHtml(d.nouveau_montant) + '</span>'
                    : '<span class="text-muted">—</span>';
                html +=
                    '<tr>'
                    + '<td class="text-center">' + typeBadge + '</td>'
                    + '<td><small class="text-monospace">' + escHtml(d.reference_operation) + '</small>'
                    + '<br><small class="text-muted">' + escHtml(d.type_operation || '') + '</small></td>'
                    + '<td><small>' + escHtml(d.guichet || '—') + '</small>'
                    + '<br><small class="text-muted">' + escHtml(d.agent_nom || d.agent_matricule) + '</small></td>'
                    + '<td><small class="text-monospace">' + escHtml(d.compte_code || '—') + '</small>'
                    + '<br><small>' + escHtml(d.client_nom || '—') + '</small></td>'
                    + '<td class="text-right font-weight-bold">' + escHtml(d.ancien_montant) + '</td>'
                    + '<td class="text-right">' + nouvMontant + '</td>'
                    + '<td><small style="max-width:160px; display:block; white-space:normal;">' + escHtml(d.motif) + '</small></td>'
                    + '<td><small>' + escHtml(d.demandee_le || '') + '</small></td>'
                    + '<td class="text-center">' + statutBadge + '</td>'
                    + '<td class="text-center" style="min-width:90px;">' + actions + '</td>'
                    + '</tr>';
            });
            $('#tbodyDemandes').html(html);
        })
        .fail(function (xhr) {
            $('#tbodyDemandes').html(
                '<tr><td colspan="10" class="text-center py-3 text-danger">'
                + '<i class="fas fa-exclamation-triangle mr-1"></i>Erreur lors du chargement des demandes.</td></tr>'
            );
        });
    }

    function escHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    $('#btnCharger, #btnRefresh').on('click', chargerDemandes);
    chargerDemandes();
    setInterval(chargerDemandes, 60000);

    // ── Approuver ────────────────────────────────────────────────
    var _appId = null;
    $(document).on('click', '.btn-approuver', function () {
        _appId = $(this).data('id');
        $('#approuverResume').text($(this).data('resume') || '—');
        $('#inpCommentaireApp').val('');
        $('#modalApprouver').modal('show');
    });
    $('#btnConfirmerApp').on('click', function () {
        if (!_appId) return;
        var url = urlApprouver.replace('__ID__', _appId);
        $('#btnConfirmerApp').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Traitement…');
        $.post(url, { commentaire: $('#inpCommentaireApp').val() })
        .done(function (r) {
            $('#modalApprouver').modal('hide');
            toastr ? toastr.success(r.message || 'Demande approuvée.') : alert(r.message || 'Demande approuvée.');
            chargerDemandes();
        })
        .fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erreur serveur.';
            alert(msg);
        })
        .always(function () {
            $('#btnConfirmerApp').prop('disabled', false).html('<i class="fas fa-check mr-1"></i>Approuver');
        });
    });

    // ── Rejeter ──────────────────────────────────────────────────
    var _rejId = null;
    $(document).on('click', '.btn-rejeter', function () {
        _rejId = $(this).data('id');
        $('#rejeterResume').text($(this).data('resume') || '—');
        $('#inpCommentaireRej').val('');
        $('#modalRejeter').modal('show');
    });
    $('#btnConfirmerRej').on('click', function () {
        if (!_rejId) return;
        var commentaire = $.trim($('#inpCommentaireRej').val());
        if (!commentaire) {
            alert('Le motif du rejet est obligatoire.');
            $('#inpCommentaireRej').focus();
            return;
        }
        var url = urlRejeter.replace('__ID__', _rejId);
        $('#btnConfirmerRej').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Traitement…');
        $.post(url, { commentaire: commentaire })
        .done(function (r) {
            $('#modalRejeter').modal('hide');
            toastr ? toastr.warning(r.message || 'Demande rejetée.') : alert(r.message || 'Demande rejetée.');
            chargerDemandes();
        })
        .fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erreur serveur.';
            alert(msg);
        })
        .always(function () {
            $('#btnConfirmerRej').prop('disabled', false).html('<i class="fas fa-times mr-1"></i>Rejeter');
        });
    });

});
</script>
@endpush

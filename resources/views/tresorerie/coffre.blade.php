{{-- ============================================================
     Vue Trésorerie : État du Coffre-Fort Central
     Permission : EBEN-PER44 (ROL1, ROL3, ROL5, ROL8)
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'État du Coffre')
@section('breadcrumb_parent', 'Trésorerie')
@section('breadcrumb', 'État du Coffre')

@section('content')
<div class="container-fluid">

    {{-- ══════════════════════════════════ SOLDES COFFRE ══════════════════════════════════ --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-warning card-outline shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-lock mr-2 text-warning"></i>
                        <strong>COFFRE-FORT CENTRAL</strong>
                        <span class="badge badge-primary ml-2">{{ $coffre->code_guichet }}</span>
                        <span class="badge badge-success ml-1">{{ $coffre->statut_operationnel }}</span>
                    </h5>
                    <div class="d-flex align-items-center">
                        <small class="text-muted mr-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            Source unique de fonds — toute alimentation guichet débite ce coffre
                        </small>
                        <button class="btn btn-xs btn-outline-warning" id="btnRefreshBalances">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="row" id="coffreBalancesRow">
                        @forelse($coffre->soldes as $sc)
                        <div class="col-6 col-md-3 col-lg-2 mb-3">
                            <div class="coffre-balance-card text-center p-3" data-devise="{{ $sc->devise_code }}">
                                <div class="coffre-devise-code">
                                    <i class="fas fa-coins mr-1 text-warning"></i>
                                    {{ $sc->devise->symbole ?? $sc->devise_code }}
                                </div>
                                <div class="coffre-montant" id="coffreBalCard_{{ $sc->devise_code }}">
                                    {{ number_format($sc->solde_en_caisse, 2, ',', ' ') }}
                                </div>
                                <small class="text-muted">{{ $sc->devise_code }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Aucun solde configuré.
                            </p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════ STATISTIQUES JOURNÉE ══════════════════════════ --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-secondary shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar mr-2 text-secondary"></i>
                        <strong>Activité du jour</strong>
                        <small class="text-muted font-weight-normal ml-2" style="font-size:.78rem">{{ now()->format('d/m/Y') }}</small>
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-success mr-1" id="statTotalEntrees">{{ $stats['total_entrees'] }} dégag.</span>
                        <span class="badge badge-danger mr-1" id="statTotalSorties">{{ $stats['total_sorties'] }} alim.</span>
                        <span class="badge badge-info mr-2" id="statTotalMvt">{{ $stats['total_mouvements'] }} mvt</span>
                        <button class="btn btn-xs btn-outline-secondary" id="btnRefreshStats" title="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" id="statsDeviseContainer">
                    @forelse($stats['par_devise'] as $d)
                    <div class="stat-devise-block border-bottom" data-devise="{{ $d['devise_code'] }}">
                        <div class="stat-devise-header d-flex align-items-center justify-content-between px-3 py-2"
                             data-toggle="collapse" data-target="#statDevise{{ $d['devise_code'] }}"
                             aria-expanded="false" aria-controls="statDevise{{ $d['devise_code'] }}">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-coins mr-2 text-warning"></i>
                                <strong class="mr-2">{{ $d['devise_code'] }}</strong>
                                <span class="badge badge-secondary">{{ $d['total'] }} mouvement(s)</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-success mr-1 d-none d-sm-inline">{{ $d['entrees_count'] }} in</span>
                                <span class="badge badge-danger mr-2 d-none d-sm-inline">{{ $d['sorties_count'] }} out</span>
                                <i class="fas fa-chevron-down stat-chevron text-muted"></i>
                            </div>
                        </div>
                        <div class="collapse" id="statDevise{{ $d['devise_code'] }}">
                            <div class="px-4 py-3">
                                <div class="row">
                                    <div class="col-12 col-md-4 mb-2 mb-md-0">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon-box bg-success mr-3"><i class="fas fa-arrow-down"></i></div>
                                            <div>
                                                <small class="text-muted d-block stat-label">Dégagements reçus</small>
                                                <strong class="d-block">{{ number_format($d['entrees_montant'], 2, ',', ' ') }} {{ $d['devise_code'] }}</strong>
                                                <small class="text-success">{{ $d['entrees_count'] }} transaction(s)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 mb-2 mb-md-0">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon-box bg-danger mr-3"><i class="fas fa-arrow-up"></i></div>
                                            <div>
                                                <small class="text-muted d-block stat-label">Alimentations envoyées</small>
                                                <strong class="d-block">{{ number_format($d['sorties_montant'], 2, ',', ' ') }} {{ $d['devise_code'] }}</strong>
                                                <small class="text-danger">{{ $d['sorties_count'] }} transaction(s)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon-box bg-info mr-3"><i class="fas fa-exchange-alt"></i></div>
                                            <div>
                                                <small class="text-muted d-block stat-label">Total flux</small>
                                                <strong class="d-block">{{ $d['total'] }} mouvement(s)</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-3 text-center text-muted small">
                        <i class="fas fa-moon mr-1"></i> Aucun mouvement confirmé aujourd'hui.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════ GUICHETS EN VÉRIFICATION ════════════════════════ --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-outline card-info shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-half mr-2 text-info"></i>
                        <strong>Clôtures en attente de validation</strong>
                        <span class="badge badge-info ml-2" id="badgeCloturesCount" style="display:none"></span>
                    </h5>
                    <button class="btn btn-xs btn-outline-info" id="btnRefreshClotures" title="Actualiser">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="cloturePanelBody">
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Chargement…
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════ DEMANDES D'APPROVISIONNEMENT ═══════════════════ --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-bell mr-2 text-primary"></i>
                        <strong>Demandes d'approvisionnement</strong>
                        <span class="badge badge-danger ml-2" id="badgeDemandesCount" style="display:none"></span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        {{-- Filtres statut --}}
                        <div class="btn-group btn-group-sm mr-2" role="group">
                            <button type="button" class="btn btn-outline-secondary filtre-statut active" data-statut="">Toutes</button>
                            <button type="button" class="btn btn-outline-warning filtre-statut" data-statut="EN_ATTENTE">
                                En attente <span class="badge badge-warning" id="badgeFiltreAttente">0</span>
                            </button>
                            <button type="button" class="btn btn-outline-success filtre-statut" data-statut="CONFIRME">Approuvées</button>
                            <button type="button" class="btn btn-outline-danger filtre-statut" data-statut="ANNULE">Rejetées</button>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" id="btnRefreshDemandes">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="tableDemandes">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Réf.</th>
                                    <th>Guichet</th>
                                    <th>Agent</th>
                                    <th>Montant</th>
                                    <th>Motif</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyDemandes">
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Chargement…
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /container-fluid --}}

{{-- Modal Rejet --}}
<div class="modal fade" id="modalRejet" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title mb-0"><i class="fas fa-times-circle mr-1"></i> Motif du rejet</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejetDemandeId">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold">Observations <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm" id="rejetObservations" rows="3" placeholder="Raison du rejet…"></textarea>
                    <small class="text-danger d-none" id="rejetErreur">Ce champ est obligatoire.</small>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmRejet">
                    <i class="fas fa-times mr-1"></i>Rejeter
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Modal Rejet Clôture (global guichet) --}}
<div class="modal fade" id="modalRejetCloture" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title mb-0"><i class="fas fa-times-circle mr-1"></i> Rejeter la clôture</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejetClotureGuichetId">
                <p class="small mb-2">Le guichet <strong id="rejetClotureCode"></strong> sera remis en <span class="badge badge-success">OUVERT</span> pour correction par l'agent.</p>
                <div class="form-group mb-2">
                    <label class="font-weight-bold">Motif du rejet <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejetClotureObs" rows="3"
                              placeholder="Ex : Écart non justifié, recomptez les billets…" maxlength="500"></textarea>
                    <small class="text-danger d-none" id="rejetClotureErreur">Ce champ est obligatoire.</small>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="btnConfirmRejetCloture">
                    <i class="fas fa-times mr-1"></i>Rejeter
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Rejet LIGNE (par devise) --}}
<div class="modal fade" id="modalRejetLigne" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title mb-0"><i class="fas fa-times-circle mr-1"></i> Rejeter la devise</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejetLigneClotureId">
                <p class="small mb-2">
                    Rejet de la devise <strong id="rejetLigneDevise"></strong> du guichet <strong id="rejetLigneGuichet"></strong>.<br>
                    <span class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Toutes les devises en attente seront rejetées et le guichet remis en <span class="badge badge-success">OUVERT</span>.</span>
                </p>
                <div class="form-group mb-2">
                    <label class="font-weight-bold">Motif du rejet <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejetLigneObs" rows="3"
                              placeholder="Ex : Écart non justifié, recomptez les billets…" maxlength="500"></textarea>
                    <small class="text-danger d-none" id="rejetLigneErreur">Ce champ est obligatoire.</small>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="btnConfirmRejetLigne">
                    <i class="fas fa-times mr-1"></i>Confirmer le rejet
                </button>
            </div>
        </div>
    </div>
</div>
@endsection


@push('css')
<style>
    .coffre-balance-card {
        background: rgba(255, 193, 7, 0.10);
        border: 2px solid rgba(255, 193, 7, 0.40);
        border-radius: 10px;
        transition: border-color .2s;
    }
    .coffre-balance-card:hover { border-color: #ffc107; }
    .coffre-devise-code  { font-weight: 700; font-size: 1rem; color: #ffc107; }
    .coffre-montant      { font-size: 1.4rem; font-weight: 800; color: #fff; word-break: break-all; }

    /* Badges statut demandes */
    .badge-statut-EN_ATTENTE { background-color: #ffc107; color:#000; }
    .badge-statut-CONFIRME   { background-color: #28a745; color:#fff; }
    .badge-statut-ANNULE     { background-color: #dc3545; color:#fff; }

    #tableDemandes th { font-size: .78rem; white-space: nowrap; }
    #tableDemandes td { font-size: .82rem; vertical-align: middle; }

    /* Ligne EN_ATTENTE légèrement surlignée */
    tr.row-en-attente { background-color: rgba(255,193,7,.06) !important; }

    /* ── Clôtures en vérification ─────────────────────────────── */
    .cloture-guichet-card {
        border-left: 4px solid #17a2b8;
        border-radius: 6px;
        background: rgba(23,162,184,.07);
        padding: 1rem;
    }
    .cloture-ecart-ok     { color: #5dd879; font-weight: 700; }
    .cloture-ecart-warn   { color: #ffd54f; font-weight: 700; }
    .cloture-ecart-danger { color: #f87272; font-weight: 700; }

    /* ── Statistiques par devise (accordéon) ─────────────────── */
    .stat-devise-header {
        cursor: pointer;
        user-select: none;
        transition: background .15s;
    }
    .stat-devise-header:hover { background: rgba(255,255,255,.05); }
    .stat-devise-header[aria-expanded="true"] .stat-chevron { transform: rotate(180deg); }
    .stat-chevron { transition: transform .25s ease; }
    .stat-icon-box {
        width: 38px; height: 38px; min-width: 38px;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
    }
    .stat-icon-box i { color: #fff; font-size: .85rem; }
    .stat-label { font-size: .75rem; }
</style>
@endpush


@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept'       : 'application/json'
        }
    });

    var urlBalances   = '{{ route("tresorerie.coffre.balances") }}';
    var urlDemandes   = '{{ route("tresorerie.coffre.demandes") }}';
    var urlCount      = '{{ route("tresorerie.coffre.demandes.count") }}';
    var urlApprouver  = '{{ url("tresorerie/coffre/demandes") }}';
    var urlClotures       = '{{ route("tresorerie.coffre.clotures") }}';
    var urlClotureAction  = '{{ url("tresorerie/coffre/clotures") }}';
    var urlLigneAction    = '{{ url("tresorerie/coffre/clotures/ligne") }}';
    var urlStats          = '{{ route("tresorerie.coffre.stats") }}';
    var filtreActif   = '';
    var toutesLesDemandes = [];

    // ══════════════════════════════════════
    // CLÔTURES EN VÉRIFICATION
    // ══════════════════════════════════════

    function renderClotures(data) {
        var $panel = $('#cloturePanelBody');
        if (!data.length) {
            $panel.html(
                '<div class="py-4 text-center text-muted">'
                + '<i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>'
                + 'Aucun guichet en attente de validation.'
                + '</div>'
            );
            $('#badgeCloturesCount').hide();
            return;
        }

        $('#badgeCloturesCount').text(data.length).show();

        var html = '<div class="p-3"><div class="row">';
        $.each(data, function (i, g) {
            html += '<div class="col-lg-6 mb-3">'
                  + '<div class="cloture-guichet-card">'
                  + '<div class="d-flex justify-content-between align-items-start mb-2">'
                  + '<div>'
                  + '<h6 class="mb-0 font-weight-bold"><i class="fas fa-cash-register mr-1 text-info"></i> ' + g.code_guichet + '</h6>'
                  + '<small class="text-muted">' + g.intitule + '</small><br>'
                  + '<small><i class="fas fa-user mr-1"></i>' + g.agent_nom + ' <span class="text-muted">(' + (g.agent_matric || '') + ')</span></small>'
                  + '</div>'
                  + '<span class="badge badge-warning badge-pill py-1 px-2"><i class="fas fa-clock mr-1"></i>En vérification</span>'
                  + '</div>';

            // Tableau des devises
            html += '<table class="table table-sm table-bordered mb-0" style="font-size:.85rem">'
                  + '<thead class="thead-dark"><tr>'
                  + '<th>Devise</th><th>Système</th><th>Physique (agent)</th><th>Écart</th><th>Actions</th>'
                  + '</tr></thead><tbody>';

            $.each(g.montants, function (j, m) {
                var ecartClass = m.statut_ecart === 'EQUILIBRE'
                    ? 'cloture-ecart-ok'
                    : (m.statut_ecart === 'EXCEDENT' ? 'cloture-ecart-warn' : 'cloture-ecart-danger');
                var ecartIcon = m.statut_ecart === 'EQUILIBRE'
                    ? '<i class="fas fa-check text-success"></i>'
                    : '<i class="fas fa-exclamation-triangle"></i>';

                var ligneBtns;
                if (!m.statut_validation || m.statut_validation === 'EN_ATTENTE') {
                    ligneBtns = '<button class="btn btn-xs btn-success btn-valider-ligne" '
                              + 'data-id="' + m.cloture_id + '" data-devise="' + m.devise_code + '" data-guichet="' + g.code_guichet + '">'
                              + '<i class="fas fa-check mr-1"></i>Valider</button> '
                              + '<button class="btn btn-xs btn-danger btn-rejeter-ligne" '
                              + 'data-id="' + m.cloture_id + '" data-devise="' + m.devise_code + '" data-guichet="' + g.code_guichet + '">'
                              + '<i class="fas fa-times mr-1"></i>Rejeter</button>';
                } else if (m.statut_validation === 'VALIDE') {
                    ligneBtns = '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Validé</span>';
                } else {
                    ligneBtns = '<span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Rejeté</span>';
                }

                html += '<tr>'
                      + '<td><strong>' + m.devise_code + '</strong><br><small class="text-muted">' + (m.date || '') + '</small></td>'
                      + '<td class="text-right">' + m.solde_comptable + '</td>'
                      + '<td class="text-right font-weight-bold">' + m.solde_physique + '</td>'
                      + '<td class="text-right ' + ecartClass + '">' + ecartIcon + '&nbsp;' + m.ecart + '</td>'
                      + '<td class="text-center" style="white-space:nowrap">' + ligneBtns + '</td>'
                      + '</tr>';

                if (m.motif_ecart) {
                    html += '<tr class="table-warning"><td colspan="5" class="small py-1"><i class="fas fa-comment mr-1"></i><em>' + $('<div>').text(m.motif_ecart).html() + '</em></td></tr>';
                }
            });

            html += '</tbody></table>';
            html += '</div></div>';
        });

        html += '</div></div>';
        $panel.html(html);
    }

    function rafraichirClotures() {
        $.ajax({
            url     : urlClotures,
            method  : 'GET',
            dataType: 'json',
            headers : { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function(data) {
            if (typeof data === 'string') {
                try { data = JSON.parse(data.replace(/^\uFEFF/, '').trim()); }
                catch(e) {
                    $('#cloturePanelBody').html('<div class="alert alert-danger m-3">Réponse invalide du serveur.</div>');
                    return;
                }
            }
            renderClotures(data);
        }).fail(function(xhr) {
            handleAjaxFail(xhr, 'Chargement clôtures en vérification');
            $('#cloturePanelBody').html(
                '<div class="alert alert-danger m-3"><i class="fas fa-exclamation-triangle mr-1"></i>'
                + 'Erreur lors du chargement des clôtures. Actualisez la page.</div>'
            );
        });
    }

    // Approuver clôture
    $(document).on('click', '.btn-approuver-cloture', function () {
        var id   = $(this).data('id');
        var code = $(this).data('code');
        var btn  = $(this);

        showUniversalConfirm(
            'Valider la clôture du guichet <strong>' + code + '</strong> ? '
            + '<br><small class="text-info">Les fonds seront automatiquement transférés au coffre et le guichet sera fermé.</small>',
            function () {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                $.ajax({
                    url     : urlClotureAction + '/' + id + '/approuver',
                    method  : 'POST',
                    data    : { observations: '' },
                    dataType: 'json'
                }).done(function(r) {
                    if (r && r.success) {
                        showSystemMessage('success', r.message);
                        var $openModal = $('.modal.show');
                        if ($openModal.length) {
                            $openModal.one('hidden.bs.modal', function () {
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                                rafraichirClotures();
                                rafraichirBalances();
                                rafraichirStats();
                            }).modal('hide');
                        } else {
                            rafraichirClotures();
                            rafraichirBalances();
                            rafraichirStats();
                        }
                    } else {
                        showSystemMessage('error', (r && r.message) ? r.message : 'Erreur.');
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Valider');
                    }
                }).fail(function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Valider');
                    handleAjaxFail(xhr, 'Approbation clôture guichet');
                });
            },
            {
                title      : 'Valider la clôture',
                btnLabel   : 'Confirmer la réception',
                btnClass   : 'btn-success',
                icon       : 'fas fa-check-circle',
                bodyIcon   : 'fas fa-handshake fa-3x text-success',
                headerClass: 'bg-success text-white',
                showWarning: false,
            }
        );
    });

    // Rejeter clôture — ouvre modal
    $(document).on('click', '.btn-rejeter-cloture', function () {
        $('#rejetClotureGuichetId').val($(this).data('id'));
        $('#rejetClotureCode').text($(this).data('code'));
        $('#rejetClotureObs').val('');
        $('#rejetClotureErreur').addClass('d-none');
        $('#modalRejetCloture').modal('show');
    });

    $('#btnConfirmRejetCloture').on('click', function () {
        var obs = $('#rejetClotureObs').val().trim();
        if (!obs) {
            $('#rejetClotureErreur').removeClass('d-none');
            return;
        }
        var id = $('#rejetClotureGuichetId').val();
        $(this).prop('disabled', true);

        $.ajax({
            url     : urlClotureAction + '/' + id + '/rejeter',
            method  : 'POST',
            data    : { observations: obs },
            dataType: 'json'
        }).done(function(r) {
            if (r.success) {
                showSystemMessage('warning', r.message || 'Clôture rejetée.');
            } else {
                showSystemMessage('error', r.message || 'Erreur.');
            }
            // Attendre la fin de l'animation Bootstrap avant de rafraîchir
            $('#modalRejetCloture').one('hidden.bs.modal', function () {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                rafraichirClotures();
            }).modal('hide');
        }).fail(function(xhr) {
            handleAjaxFail(xhr, 'Rejet clôture guichet');
        }).always(function() {
            $('#btnConfirmRejetCloture').prop('disabled', false);
        });
    });

    // ── Valider UNE ligne (1 devise) ───────────────────────────
    $(document).on('click', '.btn-valider-ligne', function () {
        var id     = $(this).data('id');
        var devise = $(this).data('devise');
        var guichet= $(this).data('guichet');
        var btn    = $(this);

        showUniversalConfirm(
            'Valider la devise <strong>' + devise + '</strong> du guichet <strong>' + guichet + '</strong> ?'
            + '<br><small class="text-info">Le montant physique sera transféré au coffre pour cette devise.</small>',
            function () {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                $.ajax({
                    url     : urlLigneAction + '/' + id + '/approuver',
                    method  : 'POST',
                    data    : { observations: '' },
                    dataType: 'json'
                }).done(function(r) {
                    if (r && r.success) {
                        showSystemMessage('success', r.message);
                        rafraichirClotures();
                        rafraichirBalances();
                        rafraichirStats();
                    } else {
                        showSystemMessage('error', (r && r.message) ? r.message : 'Erreur.');
                        btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i>Valider');
                    }
                }).fail(function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i>Valider');
                    handleAjaxFail(xhr, 'Validation ligne devise clôture');
                });
            },
            {
                title      : 'Valider la devise',
                btnLabel   : 'Confirmer',
                btnClass   : 'btn-success',
                icon       : 'fas fa-check-circle',
                bodyIcon   : 'fas fa-check-circle fa-3x text-success',
                headerClass: 'bg-success text-white',
                showWarning: false,
            }
        );
    });

    // ── Rejeter UNE ligne (1 devise) — ouvre modal ──────────────
    $(document).on('click', '.btn-rejeter-ligne', function () {
        $('#rejetLigneClotureId').val($(this).data('id'));
        $('#rejetLigneDevise').text($(this).data('devise'));
        $('#rejetLigneGuichet').text($(this).data('guichet'));
        $('#rejetLigneObs').val('');
        $('#rejetLigneErreur').addClass('d-none');
        $('#modalRejetLigne').modal('show');
    });

    $('#btnConfirmRejetLigne').on('click', function () {
        var obs = $('#rejetLigneObs').val().trim();
        if (!obs) { $('#rejetLigneErreur').removeClass('d-none'); return; }
        var id = $('#rejetLigneClotureId').val();
        $(this).prop('disabled', true);

        $.ajax({
            url     : urlLigneAction + '/' + id + '/rejeter',
            method  : 'POST',
            data    : { observations: obs },
            dataType: 'json'
        }).done(function(r) {
            if (r.success) {
                showSystemMessage('warning', r.message || 'Ligne rejetée.');
            } else {
                showSystemMessage('error', r.message || 'Erreur.');
            }
            $('#modalRejetLigne').one('hidden.bs.modal', function () {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                rafraichirClotures();
            }).modal('hide');
        }).fail(function(xhr) {
            handleAjaxFail(xhr, 'Rejet ligne devise clôture');
        }).always(function() {
            $('#btnConfirmRejetLigne').prop('disabled', false);
        });
    });

    $('#btnRefreshClotures').on('click', rafraichirClotures);
    rafraichirClotures();
    setInterval(rafraichirClotures, 45000);

    // ── Statistiques par devise ───────────────────────────────────
    function fmtMontant(n) {
        return n.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function buildStatDeviseHtml(d, isOpen) {
        var opened = isOpen ? ' show' : '';
        var ariaExp = isOpen ? 'true' : 'false';
        return '<div class="stat-devise-block border-bottom" data-devise="' + d.devise_code + '">'
             + '<div class="stat-devise-header d-flex align-items-center justify-content-between px-3 py-2"'
             + ' data-toggle="collapse" data-target="#statDevise' + d.devise_code + '"'
             + ' aria-expanded="' + ariaExp + '" aria-controls="statDevise' + d.devise_code + '">'
             + '<div class="d-flex align-items-center">'
             + '<i class="fas fa-coins mr-2 text-warning"></i>'
             + '<strong class="mr-2">' + d.devise_code + '</strong>'
             + '<span class="badge badge-secondary">' + d.total + ' mouvement(s)</span>'
             + '</div>'
             + '<div class="d-flex align-items-center">'
             + '<span class="badge badge-success mr-1 d-none d-sm-inline">' + d.entrees_count + ' in</span>'
             + '<span class="badge badge-danger mr-2 d-none d-sm-inline">' + d.sorties_count + ' out</span>'
             + '<i class="fas fa-chevron-down stat-chevron text-muted"></i>'
             + '</div></div>'
             + '<div class="collapse' + opened + '" id="statDevise' + d.devise_code + '">'
             + '<div class="px-4 py-3"><div class="row">'
             + '<div class="col-12 col-md-4 mb-2 mb-md-0"><div class="d-flex align-items-center">'
             + '<div class="stat-icon-box bg-success mr-3"><i class="fas fa-arrow-down"></i></div>'
             + '<div><small class="text-muted d-block stat-label">Dégagements reçus</small>'
             + '<strong class="d-block">' + fmtMontant(d.entrees_montant) + ' ' + d.devise_code + '</strong>'
             + '<small class="text-success">' + d.entrees_count + ' transaction(s)</small></div>'
             + '</div></div>'
             + '<div class="col-12 col-md-4 mb-2 mb-md-0"><div class="d-flex align-items-center">'
             + '<div class="stat-icon-box bg-danger mr-3"><i class="fas fa-arrow-up"></i></div>'
             + '<div><small class="text-muted d-block stat-label">Alimentations envoyées</small>'
             + '<strong class="d-block">' + fmtMontant(d.sorties_montant) + ' ' + d.devise_code + '</strong>'
             + '<small class="text-danger">' + d.sorties_count + ' transaction(s)</small></div>'
             + '</div></div>'
             + '<div class="col-12 col-md-4"><div class="d-flex align-items-center">'
             + '<div class="stat-icon-box bg-info mr-3"><i class="fas fa-exchange-alt"></i></div>'
             + '<div><small class="text-muted d-block stat-label">Total flux</small>'
             + '<strong class="d-block">' + d.total + ' mouvement(s)</strong></div>'
             + '</div></div>'
             + '</div></div></div></div>';
    }

    function rafraichirStats() {
        $.get(urlStats).done(function (data) {
            $('#statTotalEntrees').text(data.total_entrees + ' dégag.');
            $('#statTotalSorties').text(data.total_sorties + ' alim.');
            $('#statTotalMvt').text(data.total_mouvements + ' mvt');

            // Devises ouvertes avant le refresh
            var openDevises = [];
            $('#statsDeviseContainer .collapse.show').each(function () {
                openDevises.push($(this).closest('.stat-devise-block').data('devise'));
            });

            var $container = $('#statsDeviseContainer');
            if (!data.par_devise.length) {
                $container.html('<div class="py-3 text-center text-muted small"><i class="fas fa-moon mr-1"></i> Aucun mouvement confirmé aujourd\'hui.</div>');
                return;
            }
            var html = '';
            $.each(data.par_devise, function (i, d) {
                html += buildStatDeviseHtml(d, openDevises.indexOf(d.devise_code) !== -1);
            });
            $container.html(html);
        });
    }

    // ── Balances ──────────────────────────────────────────────────
    function rafraichirBalances() {
        $.get(urlBalances).done(function (data) {
            $.each(data, function (i, s) {
                var fmt = s.solde.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
                $('#coffreBalCard_' + s.devise_code).text(fmt);
            });
        });
    }

    // ── Demandes ───────────────────────────────────────────────────
    function statutLabel(statut) {
        var labels = { EN_ATTENTE: 'En attente', CONFIRME: 'Approuvée', ANNULE: 'Rejetée' };
        return labels[statut] || statut;
    }

    function renderDemandes(demandes) {
        var tbody = $('#tbodyDemandes');
        tbody.empty();

        var filtered = filtreActif
            ? demandes.filter(function(d){ return d.statut === filtreActif; })
            : demandes;

        if (filtered.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-inbox mr-1"></i> Aucune demande trouvée.</td></tr>');
            return;
        }

        $.each(filtered, function(i, d) {
            var isAttente = (d.statut === 'EN_ATTENTE');
            var actions   = '';

            if (isAttente) {
                actions = '<button class="btn btn-xs btn-success mr-1 btn-approuver" data-id="' + d.id + '" title="Approuver">'
                        + '<i class="fas fa-check"></i></button>'
                        + '<button class="btn btn-xs btn-danger btn-rejeter" data-id="' + d.id + '" title="Rejeter">'
                        + '<i class="fas fa-times"></i></button>';
            } else {
                actions = '<span class="text-muted small">—</span>';
            }

            var motif = d.motif ? ('<span title="' + $('<div>').text(d.motif).html() + '">' + (d.motif.length > 30 ? d.motif.substring(0,30)+'…' : d.motif) + '</span>') : '<span class="text-muted">—</span>';

            tbody.append(
                '<tr class="' + (isAttente ? 'row-en-attente' : '') + '">'
                + '<td>' + d.id + '</td>'
                + '<td><small class="text-monospace">' + (d.reference || '—') + '</small></td>'
                + '<td><strong>' + d.guichet_code + '</strong><br><small class="text-muted">' + d.guichet_intitule + '</small></td>'
                + '<td>' + d.agent_nom + '<br><small class="text-muted">' + d.agent_matricule + '</small></td>'
                + '<td><strong>' + d.montant_fmt + '</strong></td>'
                + '<td>' + motif + '</td>'
                + '<td><small>' + (d.date || '—') + '</small></td>'
                + '<td><span class="badge badge-statut-' + d.statut + '">' + statutLabel(d.statut) + '</span></td>'
                + '<td class="text-center">' + actions + '</td>'
                + '</tr>'
            );
        });
    }

    function rafraichirDemandes() {
        $.get(urlDemandes)
        .done(function(data) {
            // Si le serveur renvoie du HTML au lieu de JSON (ex : page d'erreur Laravel)
            if (typeof data === 'string') {
                try { data = JSON.parse(data.replace(/^\uFEFF/, '').trim()); }
                catch(e) {
                    $('#tbodyDemandes').html(
                        '<tr><td colspan="9" class="text-danger py-2 px-3 small">'
                        + '<i class="fas fa-exclamation-triangle mr-1"></i>'
                        + '<strong>Réponse non-JSON reçue :</strong><br>'
                        + '<code style="white-space:pre-wrap;font-size:.75rem">'
                        + $('<div>').text(data.substring(0, 500)).html()
                        + '</code></td></tr>'
                    );
                    return;
                }
            }

            toutesLesDemandes = data;
            var nbAttente = data.filter(function(d){ return d.statut === 'EN_ATTENTE'; }).length;
            if (nbAttente > 0) { $('#badgeDemandesCount').text(nbAttente).show(); }
            else                { $('#badgeDemandesCount').hide(); }
            $('#badgeFiltreAttente').text(nbAttente);
            renderDemandes(data);
        })
        .fail(function(xhr) {
            handleAjaxFail(xhr, 'Chargement demandes approvisionnement');
            $('#tbodyDemandes').html(
                '<tr><td colspan="9" class="text-center py-4 text-muted">'
                + '<i class="fas fa-exclamation-triangle mr-1 text-danger"></i>'
                + ' Erreur lors du chargement. Actualisez la page.'
                + '</td></tr>'
            );
        });
    }

    // ── Filtres ────────────────────────────────────────────────────
    $(document).on('click', '.filtre-statut', function() {
        $('.filtre-statut').removeClass('active');
        $(this).addClass('active');
        filtreActif = $(this).data('statut');
        renderDemandes(toutesLesDemandes);
    });

    // ── Approuver ──────────────────────────────────────────────────
    $(document).on('click', '.btn-approuver', function() {
        var id  = $(this).data('id');
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url     : urlApprouver + '/' + id + '/approuver',
            method  : 'POST',
            data    : { _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json'
        }).done(function(r) {
            if (r.success) {
                showSystemMessage('success', r.message);
                rafraichirDemandes();
                rafraichirBalances();
                rafraichirStats();
            } else {
                showSystemMessage('error', r.message || 'Erreur lors de l\'approbation.');
            }
        }).fail(function(xhr) {
            handleAjaxFail(xhr, 'Approbation demande approvisionnement');
        }).always(function() {
            // Réinitialiser le bouton dans tous les cas
            btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
        });
    });

    // ── Rejeter ────────────────────────────────────────────────────
    $(document).on('click', '.btn-rejeter', function() {
        $('#rejetDemandeId').val($(this).data('id'));
        $('#rejetObservations').val('');
        $('#rejetErreur').addClass('d-none');
        $('#modalRejet').modal('show');
    });

    $('#btnConfirmRejet').on('click', function() {
        var obs = $('#rejetObservations').val().trim();
        if (!obs) {
            $('#rejetErreur').removeClass('d-none');
            return;
        }
        var id = $('#rejetDemandeId').val();
        $('#btnConfirmRejet').prop('disabled', true);

        $.post(urlApprouver + '/' + id + '/rejeter', { observations: obs }).done(function(r) {
            showSystemMessage('warning', r.message);
            $('#modalRejet').modal('hide');
            rafraichirDemandes();
            rafraichirStats();
        }).fail(function(xhr) {
            handleAjaxFail(xhr, 'Rejet demande approvisionnement');
        }).always(function() {
            $('#btnConfirmRejet').prop('disabled', false);
        });
    });

    // ── Initialisation ─────────────────────────────────────────────
    $('#btnRefreshBalances').on('click', rafraichirBalances);
    $('#btnRefreshDemandes').on('click', rafraichirDemandes);
    $('#btnRefreshStats').on('click', rafraichirStats);

    rafraichirDemandes();
    rafraichirStats();

    // Actualisation automatique toutes les 60s
    setInterval(rafraichirDemandes, 60000);
    setInterval(rafraichirStats,   60000);
});
</script>
@endpush
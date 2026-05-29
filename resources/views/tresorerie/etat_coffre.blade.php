
@extends('layouts.app')

@section('page_title', 'État du Coffre')
@section('breadcrumb_parent', 'Trésorerie')
@section('breadcrumb', 'État du Coffre')

@section('content')
<div class="container-fluid">

    @if(!$coffre)
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Coffre central introuvable.</strong>
                Aucun guichet de type <code>CENTRAL</code> n'existe dans la table <code>tb_caisses_guichets</code>.
                Veuillez créer un guichet avec <code>type_guichet = 'CENTRAL'</code> pour utiliser cette page.
            </div>
        </div>
    </div>
    @else

    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-warning card-outline shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2 coffre-header-row collapsible-card-header"
                     data-toggle="collapse" data-target="#coffreBalancesSection"
                     aria-expanded="true" aria-controls="coffreBalancesSection">
                    <h5 class="mb-0">
                        <i class="fas fa-lock mr-2 text-warning"></i>
                        <strong>COFFRE-FORT CENTRAL</strong>
                        <span class="badge badge-primary ml-2">{{ $coffre->code_guichet }}</span>
                        <span class="badge badge-success ml-1">{{ $coffre->statut_operationnel }}</span>
                    </h5>
                    <div class="d-flex align-items-center coffre-header-tools">
                        <small class="text-muted mr-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            Source unique de fonds — toute alimentation guichet débite ce coffre
                        </small>
                        <button class="btn btn-xs btn-outline-warning stop-card-toggle" id="btnRefreshBalances">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <i class="fas fa-chevron-down ml-2 collapse-chevron"></i>
                    </div>
                </div>
                <div class="collapse show" id="coffreBalancesSection">
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
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-secondary shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2 coffre-header-row collapsible-card-header"
                     data-toggle="collapse" data-target="#coffreActivitySection"
                     aria-expanded="true" aria-controls="coffreActivitySection">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar mr-2 text-secondary"></i>
                        <strong>Activité du jour</strong>
                        <small class="text-muted font-weight-normal ml-2" style="font-size:.78rem">{{ now()->format('d/m/Y') }}</small>
                    </h5>
                    <div class="d-flex align-items-center coffre-activity-tools">
                        <span class="badge badge-success mr-1" id="statTotalEntrees">{{ $stats['total_entrees'] }} dégag.</span>
                        <span class="badge badge-danger mr-1" id="statTotalSorties">{{ $stats['total_sorties'] }} alim.</span>
                        <span class="badge badge-info mr-2" id="statTotalMvt">{{ $stats['total_mouvements'] }} mvt</span>
                        <button class="btn btn-xs btn-outline-secondary stop-card-toggle" id="btnRefreshStats" title="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <i class="fas fa-chevron-down ml-2 collapse-chevron"></i>
                    </div>
                </div>
                <div class="collapse show" id="coffreActivitySection">
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
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-outline card-info shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2 coffre-header-row collapsible-card-header"
                     data-toggle="collapse" data-target="#coffreCloturesSection"
                     aria-expanded="true" aria-controls="coffreCloturesSection">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-half mr-2 text-info"></i>
                        <strong>Clôtures en attente de validation</strong>
                        <span class="badge badge-info ml-2" id="badgeCloturesCount" style="display:none"></span>
                    </h5>
                    <div class="d-flex align-items-center">
                    <div class="btn-group btn-group-sm mr-2 stop-card-toggle" role="group" aria-label="Affichage clôtures">
                        <button type="button" class="btn btn-xs btn-info btn-clotures-mode active" data-mode="pending">
                            En attente
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-info btn-clotures-mode" data-mode="history">
                            Historique
                        </button>
                    </div>
                    <button class="btn btn-xs btn-outline-info stop-card-toggle" id="btnRefreshClotures" title="Actualiser">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <i class="fas fa-chevron-down ml-2 collapse-chevron"></i>
                    </div>
                </div>
                <div class="collapse show" id="coffreCloturesSection">
                {{-- Barre filtre date — visible uniquement en mode Historique --}}
                <div class="px-3 py-2 border-bottom d-none" id="cloturesHistoriqueFiltre" style="background:rgba(23,162,184,.07)">
                    <div class="form-row align-items-end">
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Du</label>
                            <input type="date" class="form-control form-control-sm" id="clotureHistDateDebut">
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Au</label>
                            <input type="date" class="form-control form-control-sm" id="clotureHistDateFin">
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-end">
                            <button class="btn btn-sm btn-info mr-1" id="btnAppliquerFiltreHist">
                                <i class="fas fa-search mr-1"></i>Rechercher
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="btnResetFiltreHist" title="30 derniers jours">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-end">
                            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Inclut les guichets fermés</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" id="cloturePanelBody">
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Chargement…
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2 collapsible-card-header"
                     data-toggle="collapse" data-target="#coffreDemandesSection"
                     aria-expanded="true" aria-controls="coffreDemandesSection">
                    <h5 class="mb-0">
                        <i class="fas fa-bell mr-2 text-primary"></i>
                        <strong>Demandes d'approvisionnement</strong>
                        <span class="badge badge-danger ml-2" id="badgeDemandesCount" style="display:none"></span>
                    </h5>
                    <div class="d-flex align-items-center gap-2 coffre-demandes-tools">
                        {{-- Filtres statut --}}
                        <div class="btn-group btn-group-sm mr-2" role="group">
                            <button type="button" class="btn btn-outline-secondary filtre-statut active" data-statut="">Toutes</button>
                            <button type="button" class="btn btn-outline-warning filtre-statut" data-statut="EN_ATTENTE">
                                En attente <span class="badge badge-warning" id="badgeFiltreAttente">0</span>
                            </button>
                            <button type="button" class="btn btn-outline-success filtre-statut" data-statut="CONFIRME">Approuvées</button>
                            <button type="button" class="btn btn-outline-danger filtre-statut" data-statut="ANNULE">Rejetées</button>
                        </div>
                        <button class="btn btn-sm btn-outline-primary stop-card-toggle" id="btnRefreshDemandes">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <i class="fas fa-chevron-down ml-2 collapse-chevron"></i>
                    </div>
                </div>
                <div class="collapse show" id="coffreDemandesSection">
                <div class="card-body p-0">
                    <div class="px-3 py-2 border-bottom" style="background:rgba(255,255,255,.03)">
                        <div class="form-row align-items-end">
                            <div class="col-12 col-md-3 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Du</label>
                                <input type="date" class="form-control form-control-sm" id="filtreDemDateDebut">
                            </div>
                            <div class="col-12 col-md-3 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Au</label>
                                <input type="date" class="form-control form-control-sm" id="filtreDemDateFin">
                            </div>
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Recherche (réf, guichet, agent, motif)</label>
                                <input type="text" class="form-control form-control-sm" id="filtreDemSearch" placeholder="Ex: G010, REQ-..., David">
                            </div>
                            <div class="col-12 col-md-2 d-flex justify-content-md-end">
                                <button class="btn btn-sm btn-primary mr-1" id="btnAppliquerFiltresDemandes"><i class="fas fa-search mr-1"></i>Rechercher</button>
                                <button class="btn btn-sm btn-outline-secondary" id="btnResetFiltresDemandes" title="Réinitialiser"><i class="fas fa-undo"></i></button>
                            </div>
                        </div>
                    </div>
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
                                        <i class="fas fa-filter mr-1"></i> Choisissez vos critères puis cliquez sur <strong>Rechercher</strong>.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-outline card-warning shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2 collapsible-card-header"
                     data-toggle="collapse" data-target="#coffreDemandesTxSection"
                     aria-expanded="true" aria-controls="coffreDemandesTxSection">
                    <h5 class="mb-0">
                        <i class="fas fa-edit mr-2 text-warning"></i>
                        <strong>Demandes de modification / suppression des transactions</strong>
                        <span class="badge badge-warning ml-2" id="badgeTxDemandesPending" style="display:none"></span>
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-secondary mr-2" id="badgeTxDemandesTotal">0</span>
                        <button class="btn btn-sm btn-outline-warning stop-card-toggle" id="btnRefreshDemandesTransactions">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <i class="fas fa-chevron-down ml-2 collapse-chevron"></i>
                    </div>
                </div>
                <div class="collapse show" id="coffreDemandesTxSection">
                <div class="card-body p-0">
                    <div class="px-3 py-2 border-bottom" style="background:rgba(255,255,255,.03)">
                        <div class="form-row align-items-end">
                            <div class="col-12 col-md-2 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Statut</label>
                                <select class="form-control form-control-sm" id="filtreTxStatut">
                                    <option value="tous">Tous</option>
                                    <option value="EN_ATTENTE">En attente</option>
                                    <option value="APPROUVEE">Approuvée</option>
                                    <option value="REJETEE">Rejetée</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Type</label>
                                <select class="form-control form-control-sm" id="filtreTxType">
                                    <option value="tous">Tous</option>
                                    <option value="MODIFICATION">Modification</option>
                                    <option value="SUPPRESSION">Suppression</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Du</label>
                                <input type="date" class="form-control form-control-sm" id="filtreTxDateDebut">
                            </div>
                            <div class="col-12 col-md-2 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Au</label>
                                <input type="date" class="form-control form-control-sm" id="filtreTxDateFin">
                            </div>
                            <div class="col-12 col-md-3 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Recherche (réf, client, compte, agent)</label>
                                <input type="text" class="form-control form-control-sm" id="filtreTxSearch" placeholder="Ex: OP-..., client, 243-...">
                            </div>
                            <div class="col-12 col-md-1 d-flex justify-content-md-end">
                                <button class="btn btn-sm btn-primary mr-1" id="btnAppliquerFiltresTx"><i class="fas fa-search"></i></button>
                                <button class="btn btn-sm btn-outline-secondary" id="btnResetFiltresTx" title="Réinitialiser"><i class="fas fa-undo"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="tableDemandesTransactions">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Référence</th>
                                    <th>Guichet / Agent</th>
                                    <th>Client / Compte</th>
                                    <th>Ancien Montant</th>
                                    <th>Nouveau Montant</th>
                                    <th>Motif</th>
                                    <th>Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyDemandesTransactions">
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="fas fa-filter mr-1"></i> Choisissez vos critères puis cliquez sur <strong>Rechercher</strong>.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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

{{-- Modal Approbation Demande Transaction --}}
<div class="modal fade" id="modalTxApprouver" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title mb-0"><i class="fas fa-check-circle mr-1"></i> Approuver la demande</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txAppId">
                <p class="small text-muted mb-2" id="txAppResume">—</p>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Commentaire (optionnel)</label>
                    <textarea class="form-control form-control-sm" id="txAppCommentaire" rows="3" maxlength="500"
                              placeholder="Commentaire du superviseur…"></textarea>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm btn-success" id="btnConfirmTxApprouver">
                    <i class="fas fa-check mr-1"></i>Approuver
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Rejet Demande Transaction --}}
<div class="modal fade" id="modalTxRejeter" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title mb-0"><i class="fas fa-times-circle mr-1"></i> Rejeter la demande</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txRejId">
                <p class="small text-muted mb-2" id="txRejResume">—</p>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Motif du rejet <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm" id="txRejCommentaire" rows="3" maxlength="500"
                              placeholder="Expliquez le rejet…"></textarea>
                    <small class="text-danger d-none" id="txRejErreur">Le motif est obligatoire (min 5 caractères).</small>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmTxRejeter">
                    <i class="fas fa-times mr-1"></i>Rejeter
                </button>
            </div>
        </div>
    </div>
</div>
@endif
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

    .badge-dm-EN_ATTENTE { background-color: #17a2b8; color:#fff; }
    .badge-dm-APPROUVEE  { background-color: #28a745; color:#fff; }
    .badge-dm-REJETEE    { background-color: #dc3545; color:#fff; }

    #tableDemandes th { font-size: .78rem; white-space: nowrap; }
    #tableDemandes td { font-size: .82rem; vertical-align: middle; }
    #tableDemandesTransactions th { font-size: .78rem; white-space: nowrap; }
    #tableDemandesTransactions td { font-size: .82rem; vertical-align: middle; }

    /* Ligne EN_ATTENTE légèrement surlignée */
    tr.row-en-attente { background-color: rgba(255,193,7,.06) !important; }

    
    .cloture-guichet-card {
        border-left: 4px solid #17a2b8;
        border-radius: 6px;
        background: rgba(23,162,184,.07);
        padding: 1rem;
    }
    .cloture-ecart-ok     { color: #5dd879; font-weight: 700; }
    .cloture-ecart-warn   { color: #ffd54f; font-weight: 700; }
    .cloture-ecart-danger { color: #f87272; font-weight: 700; }

    
    .stat-devise-header {
        cursor: pointer;
        user-select: none;
        transition: background .15s;
    }
    .stat-devise-header:hover { background: rgba(255,255,255,.05); }
    .stat-devise-header[aria-expanded="true"] .stat-chevron { transform: rotate(180deg); }
    .stat-chevron { transition: transform .25s ease; }
    .collapsible-card-header {
        cursor: pointer;
        user-select: none;
    }
    .collapsible-card-header:hover {
        background: rgba(255,255,255,.03);
    }
    .collapsible-card-header[aria-expanded="false"] .collapse-chevron {
        transform: rotate(-90deg);
    }
    .collapsible-card-header[aria-expanded="true"] .collapse-chevron {
        transform: rotate(0deg);
    }
    .collapse-chevron {
        transition: transform .2s ease;
        color: #adb5bd;
    }
    .stat-icon-box {
        width: 38px; height: 38px; min-width: 38px;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
    }
    .stat-icon-box i { color: #fff; font-size: .85rem; }
    .stat-label { font-size: .75rem; }

    @media (max-width: 767.98px) {
        .coffre-header-row,
        .coffre-header-tools,
        .coffre-activity-tools,
        .coffre-demandes-tools {
            flex-wrap: wrap;
            align-items: flex-start !important;
        }

        .coffre-header-tools small {
            margin-right: 0 !important;
            width: 100%;
        }

        .coffre-activity-tools,
        .coffre-demandes-tools {
            width: 100%;
            gap: .5rem;
        }

        .coffre-activity-tools .badge,
        .coffre-demandes-tools .btn-group,
        .coffre-demandes-tools > .btn {
            margin-right: 0 !important;
        }

        .coffre-demandes-tools .btn-group {
            width: 100%;
        }

        .coffre-demandes-tools .btn-group .btn {
            flex: 1 1 48%;
        }

        .stat-devise-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: .5rem;
        }

        .stat-devise-header > div:last-child {
            width: 100%;
            justify-content: space-between;
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
            'Accept'       : 'application/json'
        }
    });

    $(document).on('click', '.stop-card-toggle', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', '.coffre-demandes-tools .btn-group .btn', function (e) {
        e.stopPropagation();
    });

    $(document).on('show.bs.collapse hide.bs.collapse', '#coffreBalancesSection, #coffreActivitySection, #coffreCloturesSection, #coffreDemandesSection, #coffreDemandesTxSection', function (e) {
        var $header = $(this).prev('.collapsible-card-header');
        $header.attr('aria-expanded', e.type === 'show');
    });

    var urlBalances   = '{{ route("tresorerie.coffre.balances") }}';
    var urlDemandes   = '{{ route("tresorerie.coffre.demandes") }}';
    var urlCount      = '{{ route("tresorerie.coffre.demandes.count") }}';
    var urlApprouver  = '{{ url("tresorerie/coffre/demandes") }}';
    var urlTxDemandesData = '{{ route("caisses.demandes.modification.data") }}';
    var urlTxApprouver    = '{{ route("caisses.demandes.modification.approuver", ["id" => "__ID__"]) }}';
    var urlTxRejeter      = '{{ route("caisses.demandes.modification.rejeter", ["id" => "__ID__"]) }}';
    var urlClotures       = '{{ route("tresorerie.coffre.clotures") }}';
    var urlClotureAction  = '{{ url("tresorerie/coffre/clotures") }}';
    var urlLigneAction    = '{{ url("tresorerie/coffre/clotures/ligne") }}';
    var urlStats          = '{{ route("tresorerie.coffre.stats") }}';
    var cloturesMode  = 'pending';
    var filtreActif   = '';

    function initHistDateFilters() {
        var today = todayIso();
        var d30   = (function() {
            var d = new Date(); d.setDate(d.getDate() - 30);
            return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
        })();
        $('#clotureHistDateDebut').val(d30);
        $('#clotureHistDateFin').val(today);
    }
    initHistDateFilters();
    var toutesLesDemandes = [];
    var toutesLesDemandesTx = [];
    var demandesChargees = false;
    var demandesTxChargees = false;

    function escHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function todayIso() {
        var now = new Date();
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function setDateFiltersToToday() {
        var today = todayIso();
        $('#filtreDemDateDebut').val(today);
        $('#filtreDemDateFin').val(today);
        $('#filtreTxDateDebut').val(today);
        $('#filtreTxDateFin').val(today);
    }

    function paramsDemandesAppro() {
        return {
            statut    : filtreActif || '',
            date_debut: $('#filtreDemDateDebut').val() || '',
            date_fin  : $('#filtreDemDateFin').val() || '',
            search    : $.trim($('#filtreDemSearch').val() || ''),
            limit     : 200
        };
    }

    function paramsDemandesTx() {
        return {
            statut      : $('#filtreTxStatut').val() || 'tous',
            type_demande: $('#filtreTxType').val() || 'tous',
            date_debut  : $('#filtreTxDateDebut').val() || '',
            date_fin    : $('#filtreTxDateFin').val() || '',
            search      : $.trim($('#filtreTxSearch').val() || ''),
            limit       : 200
        };
    }

   
    function renderClotures(data) {
        var $panel = $('#cloturePanelBody');
        if (!data.length) {
            var messageVide = (cloturesMode === 'history')
                ? 'Aucun historique de clôture disponible.'
                : 'Aucun guichet en attente de validation.';
            $panel.html(
                '<div class="py-4 text-center text-muted">'
                + '<i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>'
                + messageVide
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
                  + '<br><small class="text-info">Affiché: ' + g.nb_lignes + ' ligne(s)' + (cloturesMode === 'history' ? ' / En attente: ' + (g.pending_count || 0) : '') + '</small>'
                  + '</div>';

            if (g.statut_guichet === 'FERME') {
                html += '<span class="badge badge-secondary badge-pill py-1 px-2"><i class="fas fa-lock mr-1"></i>Fermé</span>';
            } else {
                html += '<span class="badge badge-warning badge-pill py-1 px-2"><i class="fas fa-clock mr-1"></i>En vérification</span>';
            }
            html += '</div>';

            // Tableau des devises
            html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0" style="font-size:.85rem">'
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
                if (cloturesMode === 'history' && m.statut_validation !== 'EN_ATTENTE' && (m.validateur_matricule || m.date_validation)) {
                    var obsHtml = m.observations_superviseur ? ' — <em>' + $('<div>').text(m.observations_superviseur).html() + '</em>' : '';
                    html += '<tr class="table-active"><td colspan="5" class="small py-1 text-muted">'
                          + '<i class="fas fa-user-check mr-1"></i>' + (m.validateur_matricule || '') + ' · ' + (m.date_validation || '') + obsHtml
                          + '</td></tr>';
                }
            });

            html += '</tbody></table></div>';
            html += '</div></div>';
        });

        html += '</div></div>';
        $panel.html(html);
    }

    function rafraichirClotures() {
        $.ajax({
            url     : urlClotures,
            method  : 'GET',
            data    : {
                include_history: cloturesMode === 'history' ? 1 : 0,
                date_debut     : cloturesMode === 'history' ? ($('#clotureHistDateDebut').val() || '') : '',
                date_fin       : cloturesMode === 'history' ? ($('#clotureHistDateFin').val() || '') : ''
            },
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

    $(document).on('click', '.btn-clotures-mode', function () {
        var mode = $(this).data('mode');
        if (mode === cloturesMode) {
            return;
        }

        cloturesMode = mode;
        $('.btn-clotures-mode').removeClass('active btn-info').addClass('btn-outline-info');
        $(this).addClass('active btn-info').removeClass('btn-outline-info');

        if (mode === 'history') {
            $('#cloturesHistoriqueFiltre').removeClass('d-none');
            initHistDateFilters();
        } else {
            $('#cloturesHistoriqueFiltre').addClass('d-none');
        }

        rafraichirClotures();
    });

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
    $('#btnAppliquerFiltreHist').on('click', rafraichirClotures);
    $('#btnResetFiltreHist').on('click', function() {
        initHistDateFilters();
        rafraichirClotures();
    });
    rafraichirClotures();
    setInterval(rafraichirClotures, 45000);

    
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

    
    function rafraichirBalances() {
        $.get(urlBalances).done(function (data) {
            $.each(data, function (i, s) {
                var fmt = s.solde.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
                $('#coffreBalCard_' + s.devise_code).text(fmt);
            });
        });
    }

   
    function statutLabel(statut) {
        var labels = { EN_ATTENTE: 'En attente', CONFIRME: 'Approuvée', ANNULE: 'Rejetée' };
        return labels[statut] || statut;
    }

    function renderDemandes(demandes) {
        var tbody = $('#tbodyDemandes');
        tbody.empty();

        if (!demandes.length) {
            tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-inbox mr-1"></i> Aucune demande trouvée.</td></tr>');
            return;
        }

        $.each(demandes, function(i, d) {
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

    function dmTypeBadge(typeDemande) {
        if (typeDemande === 'SUPPRESSION') {
            return '<span class="badge badge-danger">Suppression</span>';
        }
        return '<span class="badge badge-warning text-dark">Modification</span>';
    }

    function dmStatutBadge(statut) {
        var label = statut;
        if (statut === 'EN_ATTENTE') label = 'En attente';
        if (statut === 'APPROUVEE')  label = 'Approuvée';
        if (statut === 'REJETEE')    label = 'Rejetée';
        return '<span class="badge badge-dm-' + statut + '">' + label + '</span>';
    }

    function renderDemandesTransactions(demandes) {
        var $tbody = $('#tbodyDemandesTransactions');
        $tbody.empty();

        if (!demandes.length) {
            $tbody.html('<tr><td colspan="10" class="text-center py-4 text-muted"><i class="fas fa-inbox mr-1"></i> Aucune demande de transaction.</td></tr>');
            return;
        }

        $.each(demandes, function (i, d) {
            var actions = '<span class="text-muted small">—</span>';
            if (d.statut === 'EN_ATTENTE') {
                actions = '<button class="btn btn-xs btn-success mr-1 btn-tx-approuver" data-id="' + d.id + '" data-resume="' + escHtml((d.reference_operation || '—') + ' — ' + (d.client_nom || '—')) + '" title="Approuver"><i class="fas fa-check"></i></button>'
                        + '<button class="btn btn-xs btn-danger btn-tx-rejeter" data-id="' + d.id + '" data-resume="' + escHtml((d.reference_operation || '—') + ' — ' + (d.client_nom || '—')) + '" title="Rejeter"><i class="fas fa-times"></i></button>';
            } else if (d.traitee_le) {
                actions = '<small class="text-muted">' + escHtml(d.traitee_le) + '</small>';
            }

            var nouvMontant = d.nouveau_montant
                ? '<strong class="text-primary">' + escHtml(d.nouveau_montant) + '</strong>'
                : '<span class="text-muted">—</span>';

            $tbody.append(
                '<tr class="' + (d.statut === 'EN_ATTENTE' ? 'row-en-attente' : '') + '">' +
                '<td>' + d.id + '</td>' +
                '<td class="text-center">' + dmTypeBadge(d.type_demande) + '</td>' +
                '<td><small class="text-monospace">' + escHtml(d.reference_operation || '—') + '</small><br><small class="text-muted">' + escHtml(d.type_operation || '—') + '</small></td>' +
                '<td><strong>' + escHtml(d.guichet || '—') + '</strong><br><small class="text-muted">' + escHtml(d.agent_nom || d.agent_matricule || '—') + '</small></td>' +
                '<td><small>' + escHtml(d.client_nom || '—') + '</small><br><small class="text-monospace text-muted">' + escHtml(d.compte_code || '—') + '</small></td>' +
                '<td><strong>' + escHtml(d.ancien_montant || '—') + '</strong></td>' +
                '<td>' + nouvMontant + '</td>' +
                '<td><small>' + escHtml(d.motif || '—') + '</small></td>' +
                '<td>' + dmStatutBadge(d.statut) + '</td>' +
                '<td class="text-center">' + actions + '</td>' +
                '</tr>'
            );
        });
    }

    function rafraichirDemandesTransactions() {
        $.get(urlTxDemandesData, paramsDemandesTx())
        .done(function (data) {
            if (typeof data === 'string') {
                try { data = JSON.parse(data.replace(/^\uFEFF/, '').trim()); }
                catch(e) {
                    $('#tbodyDemandesTransactions').html('<tr><td colspan="10" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Réponse invalide du serveur.</td></tr>');
                    return;
                }
            }

            demandesTxChargees = true;
            toutesLesDemandesTx = Array.isArray(data) ? data : [];
            var pending = toutesLesDemandesTx.filter(function (d) { return d.statut === 'EN_ATTENTE'; }).length;

            $('#badgeTxDemandesTotal').text(toutesLesDemandesTx.length);
            if (pending > 0) {
                $('#badgeTxDemandesPending').text(pending + ' en attente').show();
            } else {
                $('#badgeTxDemandesPending').hide();
            }

            renderDemandesTransactions(toutesLesDemandesTx);
        })
        .fail(function (xhr) {
            handleAjaxFail(xhr, 'Chargement demandes modification/suppression');
            $('#tbodyDemandesTransactions').html('<tr><td colspan="10" class="text-center py-4 text-muted"><i class="fas fa-exclamation-triangle mr-1 text-danger"></i> Erreur lors du chargement.</td></tr>');
        });
    }

    function rafraichirDemandes() {
        $.get(urlDemandes, paramsDemandesAppro())
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

            demandesChargees = true;
            toutesLesDemandes = Array.isArray(data) ? data : [];
            var nbAttente = toutesLesDemandes.filter(function(d){ return d.statut === 'EN_ATTENTE'; }).length;
            if (nbAttente > 0) { $('#badgeDemandesCount').text(nbAttente).show(); }
            else                { $('#badgeDemandesCount').hide(); }
            $('#badgeFiltreAttente').text(nbAttente);
            renderDemandes(toutesLesDemandes);
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

    
    $(document).on('click', '.filtre-statut', function() {
        $('.filtre-statut').removeClass('active');
        $(this).addClass('active');
        filtreActif = $(this).data('statut');
        rafraichirDemandes();
    });

    $('#btnAppliquerFiltresDemandes').on('click', function () {
        rafraichirDemandes();
    });

    $('#btnResetFiltresDemandes').on('click', function () {
        filtreActif = '';
        $('.filtre-statut').removeClass('active');
        $('.filtre-statut[data-statut=""]').addClass('active');
        $('#filtreDemSearch').val('');
        setDateFiltersToToday();
        rafraichirDemandes();
    });

    $('#filtreDemDateDebut, #filtreDemDateFin, #filtreDemSearch').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            rafraichirDemandes();
        }
    });

    $('#btnAppliquerFiltresTx').on('click', function () {
        rafraichirDemandesTransactions();
    });

    $('#btnResetFiltresTx').on('click', function () {
        $('#filtreTxStatut').val('tous');
        $('#filtreTxType').val('tous');
        $('#filtreTxSearch').val('');
        setDateFiltersToToday();
        rafraichirDemandesTransactions();
    });

    $('#filtreTxStatut, #filtreTxType').on('change', function () {
        rafraichirDemandesTransactions();
    });

    $('#filtreTxDateDebut, #filtreTxDateFin, #filtreTxSearch').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            rafraichirDemandesTransactions();
        }
    });

    
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

    $(document).on('click', '.btn-tx-approuver', function () {
        $('#txAppId').val($(this).data('id'));
        $('#txAppResume').text($(this).data('resume') || '—');
        $('#txAppCommentaire').val('');
        $('#modalTxApprouver').modal('show');
    });

    $('#btnConfirmTxApprouver').on('click', function () {
        var id = $('#txAppId').val();
        if (!id) return;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Traitement…');

        $.post(urlTxApprouver.replace('__ID__', id), {
            commentaire: $('#txAppCommentaire').val()
        }).done(function (r) {
            $('#modalTxApprouver').modal('hide');
            showSystemMessage('success', r.message || 'Demande approuvée.');
            rafraichirDemandesTransactions();
        }).fail(function (xhr) {
            handleAjaxFail(xhr, 'Approbation demande transaction');
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i>Approuver');
        });
    });

    $(document).on('click', '.btn-tx-rejeter', function () {
        $('#txRejId').val($(this).data('id'));
        $('#txRejResume').text($(this).data('resume') || '—');
        $('#txRejCommentaire').val('');
        $('#txRejErreur').addClass('d-none');
        $('#modalTxRejeter').modal('show');
    });

    $('#btnConfirmTxRejeter').on('click', function () {
        var id = $('#txRejId').val();
        var commentaire = $.trim($('#txRejCommentaire').val());

        if (!commentaire || commentaire.length < 5) {
            $('#txRejErreur').removeClass('d-none');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Traitement…');

        $.post(urlTxRejeter.replace('__ID__', id), {
            commentaire: commentaire
        }).done(function (r) {
            $('#modalTxRejeter').modal('hide');
            showSystemMessage('warning', r.message || 'Demande rejetée.');
            rafraichirDemandesTransactions();
        }).fail(function (xhr) {
            handleAjaxFail(xhr, 'Rejet demande transaction');
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-times mr-1"></i>Rejeter');
        });
    });

   
    $('#btnRefreshBalances').on('click', rafraichirBalances);
    $('#btnRefreshDemandes').on('click', rafraichirDemandes);
    $('#btnRefreshDemandesTransactions').on('click', rafraichirDemandesTransactions);
    $('#btnRefreshStats').on('click', rafraichirStats);

    setDateFiltersToToday();
    rafraichirStats();
    rafraichirDemandes();
    rafraichirDemandesTransactions();

    // Actualisation automatique toutes les 60s
    setInterval(function () {
        if (demandesChargees) rafraichirDemandes();
    }, 60000);
    setInterval(function () {
        if (demandesTxChargees) rafraichirDemandesTransactions();
    }, 60000);
    setInterval(rafraichirStats,   60000);
});
</script>
@endpush
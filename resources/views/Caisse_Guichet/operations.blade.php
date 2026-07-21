{{-- ============================================================
    Opérations de Caisse — Guichetier
    Saisie : Dépôt, Retrait, Change, Paiement, Remboursement
    Permissions : EBEN-PER10 (voir) | EBEN-PER11 (saisir) | EBEN-PER25 (annuler)
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'Opérations de Caisse' . (($zoneRestriction['active'] ?? false) && !empty($zoneRestriction['zone_label']) ? ' (' . $zoneRestriction['zone_label'] . ')' : ''))
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Opérations')

@section('content')
<div class="container-fluid">

    @if(!$guichet)
    {{-- ── Aucun guichet affecté ──────────────────────────────── --}}
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-warning card-outline shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-cash-register fa-4x text-muted mb-3"></i>
                    <h4>Aucun guichet assigné</h4>
                    <p class="text-muted">Votre compte n'est associé à aucun guichet actif.<br>
                    Contactez un administrateur pour l'affectation.</p>
                </div>
            </div>
        </div>
    </div>

    @else
    @php
        $statut  = $guichet->statut_operationnel;
        $typeGch = $guichet->type_guichet;   // FIXE | MOBILE | CENTRAL
        $guichetOuvert = ($statut === 'OUVERT');
        $isMobileGuichet = strtoupper((string) $typeGch) === 'MOBILE';
    @endphp

    {{-- ── Bandeau statut guichet ────────────────────────────── --}}
    @if(!$guichetOuvert)
    <div class="alert alert-{{ $statut === 'SUSPENDU' ? 'warning' : ($statut === 'EN_VERIFICATION' ? 'info' : 'danger') }} shadow mb-3 py-2">
        <i class="fas fa-{{ $statut === 'SUSPENDU' ? 'pause-circle' : ($statut === 'EN_VERIFICATION' ? 'hourglass-half' : 'lock') }} mr-2"></i>
        Guichet <strong>{{ $statut }}</strong> — La saisie d'opérations est impossible.
        Rendez-vous sur <a href="{{ route('caisses.ouverture') }}" class="alert-link">Ouverture / Fermeture</a> pour changer l'état du guichet.
    </div>
    @endif

    {{-- ── Soldes actuels ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center flex-wrap gap-2 mb-3 operation-soldes-bar">
        <small class="text-muted text-uppercase" style="letter-spacing:.08em;">
            <i class="fas fa-wallet mr-1"></i> Soldes :
        </small>
        @foreach($guichet->soldes->sortBy('devise_code') as $s)
        <span class="badge badge-pill px-3 py-2 solde-pill" id="soldePill_{{ $s->devise_code }}"
              style="font-size:.92rem; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15);">
            <strong>{{ $s->devise_code }}</strong>
            <span class="solde-val">{{ number_format($s->solde_en_caisse, 2, ',', ' ') }}</span>
        </span>
        @endforeach
        <span class="badge badge-pill px-2 py-2 ml-1"
              style="background:rgba(23,162,184,.2); border:1px solid rgba(23,162,184,.4); font-size:.85rem;">
            <i class="fas fa-{{ $typeGch === 'MOBILE' ? 'mobile-alt' : 'desktop' }} mr-1 text-info"></i>
            {{ $typeGch }}
        </span>
    </div>

    {{-- ── Corps principal ────────────────────────────────────── --}}
    <div class="row">

        {{-- ── Formulaire de saisie (gauche) ──────────────────── --}}
        <div class="col-lg-4 col-md-5 mb-3">
            <div class="card card-outline card-primary shadow">
                <div class="card-header py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-plus-circle mr-1 text-primary"></i>
                        Nouvelle opération
                    </h6>
                </div>
                <div class="card-body">

                    @if($isMobileGuichet)
                    <div class="alert alert-warning py-2 small">
                        <i class="fas fa-mobile-alt mr-1"></i>
                        Guichet mobile : seules les opérations <strong>Dépôt</strong> et <strong>Change</strong> sont autorisées.
                    </div>
                    @endif

                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em;">
                            Type d'opération <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="selTypeOp" {{ !$guichetOuvert ? 'disabled' : '' }}>
                            <option value="">— Sélectionnez —</option>
                            @foreach(($operationTypeOptions ?? []) as $operationType)
                            <option value="{{ $operationType['value'] }}">{{ $operationType['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ── Bloc remboursement crédit (visible seulement si REMBOURSEMENT) ── --}}
                    {{-- ── Bloc compte (visible seulement si DEPOT / RETRAIT) ── --}}
                    <div id="blocCompte" class="d-none mb-2">
                        <div class="alert alert-primary py-1 mb-2 small">
                            <i class="fas fa-university mr-1"></i>
                            <strong>Compte client requis</strong> — code compte ou nom du titulaire.
                        </div>
                        <div class="form-group mb-1">
                            <label class="font-weight-bold" style="font-size:.85rem;">
                                Compte <span class="text-danger">*</span>
                            </label>
                            <select id="selCompte" class="form-control form-control-sm" style="width:100%"
                                    {{ !$guichetOuvert ? 'disabled' : '' }}>
                                <option value="">— Sélectionner un compte —</option>
                                @foreach($comptes as $cpt)
                                <option value="{{ $cpt->code_compte }}"
                                        data-devise="{{ $cpt->devise }}"
                                        data-type="{{ $cpt->type }}"
                                        data-solde="{{ number_format($cpt->solde_reel ?? 0, 2, '.', '') }}"
                                        data-client="{{ optional($cpt->client)->nom }} {{ optional($cpt->client)->postnom }}"
                                        data-prenom="{{ optional($cpt->client)->prenom }}"
                                        data-matricule="{{ optional($cpt->client)->matricule }}"
                                        data-telephone="{{ optional($cpt->client)->telephone }}"
                                        data-sexe="{{ optional($cpt->client)->sexe }}"
                                        data-photo="{{ optional($cpt->client)->photo ? basename(optional($cpt->client)->photo) : '' }}">
                                    [{{ $cpt->devise }}] {{ $cpt->type }} — {{ optional($cpt->client)->nom }} {{ optional($cpt->client)->postnom }} — {{ $cpt->code_compte }}
                                </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="selectedCompteCode">
                        </div>
                    </div>

                    {{-- ── Devise + Montant source ──────────── --}}
                    <div class="form-row mb-2">
                        <div class="col-5">
                            <label class="font-weight-bold" style="font-size:.85rem;" id="labelDevise">
                                Devise <span class="text-danger">*</span>
                            </label>
                            <select class="form-control form-control-sm" id="selDevise"
                                    {{ !$guichetOuvert ? 'disabled' : '' }}>
                                <option value="">—</option>
                                @foreach($guichet->soldes->sortBy('devise_code') as $s)
                                <option value="{{ $s->devise_code }}">{{ $s->devise_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-7">
                            <label class="font-weight-bold" style="font-size:.85rem;" id="labelMontant">
                                Montant <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-sm" id="inpMontant"
                                   placeholder="0.00" min="0.01" step="any"
                                   {{ !$guichetOuvert ? 'disabled' : '' }}>
                        </div>
                    </div>

                    {{-- ── Bloc change (visible seulement si CHANGE) ── --}}
                    <div id="blocChange" class="d-none">
                        <div class="alert alert-info py-1 mb-2 small">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Change :</strong> le client donne <em>Devise source</em>,
                            le guichet donne <em>Devise destination</em>, au <strong>taux actif</strong> défini par la Trésorerie.
                        </div>
                        <div class="form-row mb-2">
                            <div class="col-5">
                                <label class="font-weight-bold" style="font-size:.85rem;">Devise dest. <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" id="selDeviseDest">
                                    <option value="">—</option>
                                    @foreach($guichet->soldes->sortBy('devise_code') as $s)
                                    <option value="{{ $s->devise_code }}">{{ $s->devise_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-7">
                                <label class="font-weight-bold" style="font-size:.85rem;">Montant destination</label>
                                <div class="alert alert-success py-1 px-2 mb-0 text-center" style="font-weight:bold;" id="inpMontantDest">—</div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:.85rem;">Taux de change ACTIF</label>
                            <div class="alert alert-warning py-1 px-2 mb-1 text-center" style="font-weight:bold;" id="inpTaux">—</div>
                            <small class="text-muted" id="tauxInfoLabel"></small>
                        </div>
                    </div>

                    {{-- ── Observations ─────────────────────── --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold" style="font-size:.85rem;">Observations</label>
                        <input type="text" class="form-control form-control-sm" id="inpObservations"
                               placeholder="Remarque optionnelle…" maxlength="500"
                               {{ !$guichetOuvert ? 'disabled' : '' }}>
                    </div>

                    <div class="alert alert-info py-2 small mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Confirmation :</strong> Une fenêtre de confirmation apparaîtra avant l'enregistrement pour choisir d'imprimer le bordereau.
                    </div>

                    <div id="blocCommissionPreview" class="card mb-3 d-none operation-preview-card">
                        <div class="card-header py-1 px-2 d-flex align-items-center justify-content-between">
                            <span class="small font-weight-bold text-uppercase" style="letter-spacing:.05em;">
                                <i class="fas fa-calculator mr-1 text-info"></i>Simulation client
                            </span>
                            <span class="badge badge-light" id="previewRuleStatus">--</span>
                        </div>
                        <div class="card-body py-2 px-2">
                            <div class="small text-muted mb-1" id="previewHint">Saisissez type, compte, devise et montant.</div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Commission</span>
                                <strong id="previewCommissionAmount">0,00</strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span id="previewImpactLabel">Impact client</span>
                                <strong id="previewImpactAmount">0,00</strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Solde avant</span>
                                <strong id="previewSoldeAvant">--</strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Solde apres</span>
                                <strong id="previewSoldeApres">--</strong>
                            </div>
                            <div class="small text-muted mt-2" id="previewFormule"></div>
                            <div class="small text-danger mt-1 d-none" id="previewAlerteInsuffisant">
                                <i class="fas fa-exclamation-circle mr-1"></i>Solde client insuffisant pour ce retrait.
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary btn-block" id="btnEnregistrerOp"
                            {{ !$guichetOuvert ? 'disabled' : '' }}>
                        <i class="fas fa-check-circle mr-1"></i> Enregistrer
                    </button>

                </div>
            </div>
        </div>

        {{-- ── Tableau opérations du jour (droite) ───────────── --}}
        <div class="col-lg-8 col-md-7 mb-3">
            <div class="card card-outline card-secondary shadow">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-list-ul mr-1 text-secondary"></i>
                        Opérations du jour
                        <span class="badge badge-secondary ml-1" id="opCount">{{ $operations->count() }}</span>
                    </h6>
                    <div class="d-flex align-items-center" style="gap:.45rem;">
                        <small class="text-muted" title="Les opérations avec demande en attente sont affichées en premier">
                            <i class="fas fa-sort-amount-down-alt mr-1"></i>Tri : demandes en attente d'abord
                        </small>
                        <div class="input-group input-group-sm" style="width: 250px; margin-left: auto;">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="searchOpsInput" placeholder="Réf., client, compte...">
                        </div>
                        <button class="btn btn-xs btn-outline-secondary" id="btnRefreshOps" title="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="tableOpsJour">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:30px;"></th>
                                    <th>Réf.</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>État demande</th>
                                    <th style="width:100px;"></th>
                                </tr>
                            </thead>
                            <tbody id="tbodyOps">
                                @forelse($operations as $op)
                                @php
                                    $dmd = ($latestDemandesByTx ?? [])[$op->id] ?? null;
                                    $demandeBloquee = $dmd && in_array($dmd->statut, ['EN_ATTENTE', 'APPROUVEE'], true);
                                @endphp
                                <tr class="{{ $op->statut === 'ANNULE' ? 'text-muted' : '' }}" id="opRow_{{ $op->id }}">
                                    <td class="text-center">
                                        <i class="fas {{ \App\Models\Caisse\Transaction::typeIcon($op->type) }} fa-sm"></i>
                                    </td>
                                    <td>
                                        <small class="text-monospace">{{ $op->reference }}</small><br>
                                        <span class="badge badge-pill badge-sm {{ \App\Models\Caisse\Transaction::typeBadgeClass($op->type) }}">
                                            {{ \App\Models\Caisse\Transaction::typeLabel($op->type) }}
                                        </span>
                                        @if($op->compte_code)
                                        <br><small class="text-muted"><i class="fas fa-university fa-xs"></i> {{ $op->compte_code }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($op->compte && $op->compte->client)
                                            <span class="text-info client-name-text"><i class="fas fa-user fa-xs"></i> {{ $op->compte->client->full_name }}</span>
                                        @else
                                            <small class="text-muted">—</small>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold">
                                        {{ number_format($op->montant, 2, ',', ' ') }} {{ $op->devise_code }}
                                        @if($op->type === 'CHANGE' && $op->montant_dest)
                                        <br><small class="text-info">→ {{ number_format($op->montant_dest, 2, ',', ' ') }} {{ $op->devise_dest }}</small>
                                        @endif
                                    </td>
                                    <td><small>{{ $op->date_operation?->format('H:i') }}</small></td>
                                    <td>
                                        @if($op->statut === 'ANNULE')
                                        <span class="badge badge-secondary">Annulée</span>
                                        @else
                                        <span class="badge badge-success">Confirmé</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dmd)
                                            @if($dmd->statut === 'EN_ATTENTE')
                                                <span class="badge badge-pill badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i>En attente #{{ $dmd->id }}</span>
                                            @elseif($dmd->statut === 'APPROUVEE')
                                                <span class="badge badge-pill badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>Approuvée #{{ $dmd->id }}</span>
                                            @else
                                                <span class="badge badge-pill badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i>Rejetée #{{ $dmd->id }}</span>
                                            @endif
                                        @else
                                            <span class="badge badge-light">Aucune demande</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="op-actions">
                                            @if($op->statut === 'CONFIRME')
                                            @if($canDeleteOperation && !$demandeBloquee)
                                            <button class="btn btn-xs btn-outline-danger btn-annuler"
                                                    data-id="{{ $op->id }}" title="Annuler">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                            @if(!$demandeBloquee)
                                                <button class="btn btn-xs btn-outline-warning btn-demande-modif"
                                                        data-id="{{ $op->id }}"
                                                        data-ref="{{ $op->reference }}"
                                                        data-montant="{{ $op->montant }}"
                                                        data-type="{{ $op->type }}"
                                                        data-devise="{{ $op->devise_code }}"
                                                        title="Demander modification / suppression">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-xs btn-outline-secondary" disabled
                                                        title="Demande déjà en cours/traitée">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif
                                            @endif
                                            <a href="{{ route('caisses.operations.bordereau', ['id' => $op->id]) }}"
                                               target="_blank"
                                               class="btn btn-xs btn-outline-info"
                                               title="Imprimer le bordereau PDF">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr id="trVide">
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Aucune opération aujourd'hui.
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
    @endif

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — Vérification identité client avant opération
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalIdentiteClient" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content shadow-lg" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header py-2" style="background:linear-gradient(90deg,#2563eb 0%,#1d4ed8 100%);">
                <h6 class="modal-title text-white mb-0">
                    <i class="fas fa-user-shield mr-2"></i>Vérification d'identité du client
                </h6>
            </div>
            <div class="modal-body p-0">
                {{-- Bandeau alerte --}}
                <div class="alert alert-warning mb-0 py-2 px-3 small rounded-0">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Confirmez que la personne se présentant correspond bien au titulaire ci-dessous.
                </div>
                <div class="p-3">
                    <div class="d-flex align-items-center identite-client-layout">
                        {{-- Photo du client --}}
                         <div class="mr-3 flex-shrink-0 identite-client-media">
                             @if($guichet && $guichet->type_guichet === 'FIXE')
                             <img id="photoIdentiteClient"
                                  src="{{ asset('images_projet/default_user.png') }}"
                                  alt="Photo client"
                                  style="width:110px;height:130px;object-fit:cover;border-radius:10px;
                                         border:3px solid #3b82f6;background:#e2e8f0;">
                             @endif
                         </div>
                        {{-- Infos client --}}
                        <div class="flex-grow-1">
                            <div class="mb-1">
                                <span class="badge badge-primary badge-pill px-2 py-1" id="badgeSexeClient" style="font-size:.78rem;">—</span>
                            </div>
                            <h5 class="mb-1 font-weight-bold text-dark" id="nomCompletIdentite">—</h5>
                            <div class="text-muted small mb-2" id="prenomIdentite">—</div>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0" style="font-size:.87rem;">
                                    <tr>
                                        <td class="py-0 text-muted pl-0" style="width:110px;"><i class="fas fa-id-badge mr-1 text-primary"></i>Matricule</td>
                                        <td class="py-0 font-weight-bold" id="matriculeIdentite">—</td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 text-muted pl-0"><i class="fas fa-university mr-1 text-primary"></i>Compte</td>
                                        <td class="py-0"><code id="compteIdentite">—</code></td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 text-muted pl-0"><i class="fas fa-phone mr-1 text-primary"></i>Téléphone</td>
                                        <td class="py-0" id="telephoneIdentite">—</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-3 pb-2">
                    <div class="alert alert-info py-1 mb-0 small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Solde disponible&nbsp;: <strong id="soldeIdentite">—</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 justify-content-between" style="border-top:1px solid #e2e8f0;">
                <button type="button" class="btn btn-danger btn-sm" id="btnNonIdentite">
                    <i class="fas fa-times mr-1"></i> Non, ce n'est pas cette personne
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btnOuiIdentite">
                    <i class="fas fa-check mr-1"></i> Oui, identité confirmée
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — Demande de modification / suppression d'opération
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDemandeModif" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content shadow-lg" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header py-2 bg-warning">
                <h6 class="modal-title font-weight-bold mb-0">
                    <i class="fas fa-edit mr-2"></i>Demande de modification / suppression
                </h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Votre demande sera transmise au superviseur pour approbation.
                    Opération : <strong id="demandeRef">—</strong>
                    &bull; Type : <strong id="demandeType">—</strong>
                    &bull; Montant original : <strong id="demandeAncienMontant">—</strong>
                </div>
                <div id="etatDemandeInfo" class="alert alert-secondary py-2 small mb-3 d-none">
                    <div class="font-weight-bold mb-1"><i class="fas fa-stream mr-1"></i> État de la demande</div>
                    <div id="etatDemandeMessage">—</div>
                    <div id="etatDemandeDetails" class="text-muted mt-1">—</div>
                </div>
                <div class="form-group mb-2">
                    <label class="small font-weight-bold">Type de demande <span class="text-danger">*</span></label>
                    <select id="selTypeDemande" class="form-control form-control-sm">
                        <option value="MODIFICATION">Modification du montant</option>
                        <option value="SUPPRESSION">Suppression (annulation)</option>
                    </select>
                </div>
                <div id="blocNouveauMontant" class="form-group mb-2">
                    <label class="small font-weight-bold">Nouveau montant <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <input type="number" id="inpNouveauMontant" class="form-control" step="0.01" min="0.01"
                               placeholder="0.00">
                        <div class="input-group-append">
                            <span class="input-group-text" id="demandeDevise">—</span>
                        </div>
                    </div>
                </div>
                <div id="blocNouvObservations" class="form-group mb-2">
                    <label class="small font-weight-bold">Nouvelles observations</label>
                    <textarea id="inpNouvObservations" class="form-control form-control-sm" rows="2"
                              placeholder="(optionnel)" maxlength="500"></textarea>
                </div>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Motif de la demande <span class="text-danger">*</span></label>
                    <textarea id="inpMotifDemande" class="form-control form-control-sm" rows="2"
                              placeholder="Décrivez clairement la raison de votre demande (obligatoire)..."
                              maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm btn-warning font-weight-bold" id="btnSoumettreDemandeModif">
                    <i class="fas fa-paper-plane mr-1"></i>Soumettre la demande
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — Confirmation d'enregistrement d'opération
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalConfirmOperation" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content shadow-lg" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header py-2" style="background:linear-gradient(90deg,#2563eb 0%,#1d4ed8 100%);">
                <h6 class="modal-title text-white mb-0">
                    <i class="fas fa-check-circle mr-2"></i>Confirmation de l'opération
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-3">
                <div class="text-center mb-3">
                    <div class="mb-2">
                        <span class="badge badge-pill px-3 py-2" id="modalConfirmType" style="font-size:.95rem; background:rgba(37,99,235,.15); border:1px solid rgba(37,99,235,.3);">—</span>
                    </div>
                    <div class="font-weight-bold" style="font-size:1.3rem;" id="modalConfirmMontant">—</div>
                </div>
                <hr class="my-2">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="chkImprimerBordereauModal" checked>
                    <label class="custom-control-label" for="chkImprimerBordereauModal">
                        <i class="fas fa-file-pdf mr-1 text-danger"></i>Imprimer le bordereau après l'enregistrement
                    </label>
                </div>
            </div>
            <div class="modal-footer py-2 justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Annuler
                </button>
                <button type="button" class="btn btn-sm btn-success font-weight-bold" id="btnConfirmOp">
                    <i class="fas fa-check mr-1"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

@endsection


@push('css')
<style>
    .solde-pill { transition: background .2s; }
    #tableOpsJour td {
        font-size: .96rem;
        line-height: 1.35;
        padding-top: .55rem;
        padding-bottom: .55rem;
        vertical-align: middle;
    }
    #tableOpsJour th {
        font-size: .90rem;
        line-height: 1.25;
        padding-top: .60rem;
        padding-bottom: .60rem;
    }
    #tableOpsJour .client-name-text {
        font-size: 1.08rem;
        font-weight: 600;
        line-height: 1.35;
    }
    .badge-sm { font-size: .72rem; padding: .15em .45em; }
    .btn-xs { padding: .15rem .45rem; font-size: .78rem; }
    .gap-2 { gap: .5rem; }
    .op-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: .25rem;
    }

    .operation-preview-card {
        border: 1px solid rgba(23, 162, 184, .35);
        background: rgba(23, 162, 184, .08);
    }

    .operation-preview-card .card-header {
        background: rgba(23, 162, 184, .14);
        border-bottom: 1px solid rgba(23, 162, 184, .28);
    }

    @media (max-width: 767.98px) {
        .operation-soldes-bar {
            display: grid !important;
            grid-template-columns: 1fr;
            align-items: stretch !important;
        }

        .operation-soldes-bar small,
        .operation-soldes-bar .solde-pill,
        .operation-soldes-bar > .badge {
            width: 100%;
            margin-left: 0 !important;
        }

        .operation-soldes-bar .solde-pill,
        .operation-soldes-bar > .badge {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .op-actions {
            justify-content: flex-start;
        }

        #modalIdentiteClient .identite-client-layout {
            flex-direction: column;
            align-items: flex-start !important;
        }

        #modalIdentiteClient .identite-client-media {
            margin-right: 0 !important;
            margin-bottom: 1rem;
        }

        #modalIdentiteClient #photoIdentiteClient {
            width: 88px !important;
            height: 108px !important;
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

    @if($guichet)
    var urlStore         = '{{ route("caisses.operations.store") }}';
    var urlJournal        = '{{ route("caisses.journal.data") }}';
    var urlAnnuler        = '{{ route("caisses.operations.annuler", ["id" => "__ID__"]) }}';
    var urlDemandeStatut = '{{ route("caisses.operations.demande.statut", ["id" => "__ID__"]) }}';
    var urlDemandeModif  = '{{ route("caisses.operations.demande.modification", ["id" => "__ID__"]) }}';
    var urlBordereau     = '{{ route("caisses.operations.bordereau", ["id" => "__ID__"]) }}';
    var urlSearchCompte   = '{{ route("caisses.operations.comptes.search") }}';
    var urlCommissionPreview = '{{ route("caisses.operations.commission.preview") }}';
    var urlTauxActif      = '{{ route("administration.devises-taux.actif") }}';
    var urlClientPhoto    = '{{ url("/clients/photo") }}';
    
    // ══════════════════════════════════════════════════════════════
    // Permission annulation : EBEN-PER25 (transactions)
    // ══════════════════════════════════════════════════════════════
    var canDeleteOperation = {{ $canDeleteOperation ? 'true' : 'false' }};

    // ── Select2 — Recherche compte client (chargé côté serveur) ──
    var _typeColors = { CC: 'primary', RMB: 'success', GTC: 'warning', DAT: 'info', EAV: 'secondary' };

    function _compteBadge(type) {
        var color = _typeColors[type] || 'light';
        return '<span class="badge badge-' + color + ' mr-1" style="font-size:.75em;vertical-align:middle">' + (type || '?') + '</span>';
    }

    function _compteMatcher(params, data) {
        if (!params.term || params.term.trim() === '') return data;
        var term = params.term.trim().toUpperCase();
        var text  = (data.text || '').toUpperCase();
        var $opt  = data.element ? $(data.element) : null;
        var matricule = ($opt ? ($opt.data('matricule') || '') : '').toUpperCase();
        if (text.indexOf(term) > -1 || matricule.indexOf(term) > -1) return data;
        return null;
    }

    $('#selCompte').select2({
        theme         : 'bootstrap4',
        width         : '100%',
        dropdownParent: $('body'),
        placeholder   : '— Sélectionner un compte —',
        allowClear    : true,
        matcher       : _compteMatcher,
        language      : { noResults: function () { return 'Aucun compte trouvé.'; } },
        templateResult: function (data) {
            if (!data.id) return data.text;
            var $opt = data.element ? $(data.element) : null;
            var type = $opt ? ($opt.data('type') || '') : '';
            var parts = (data.text || '').split(' — ');
            // parts[0] = "[USD] CC", parts[1] = nom, parts[2] = code_compte
            var devise = '';
            var nomPart = parts.length > 1 ? parts[1] : '';
            var codePart = parts.length > 2 ? parts[2] : '';
            var devisePart = parts[0] || '';
            return $('<span>' + _compteBadge(type) + '<strong>' + devisePart + '</strong> ' + nomPart + (codePart ? ' <small class="text-muted">' + codePart + '</small>' : '') + '</span>');
        },
        templateSelection: function (data) {
            if (!data.id) return data.text;
            var $opt = data.element ? $(data.element) : null;
            var type = $opt ? ($opt.data('type') || '') : '';
            return $('<span>' + _compteBadge(type) + data.text + '</span>');
        }
    });

    var _pendingCompteCode  = null;  // Code en attente de confirmation
    var _pendingDevise      = null;
    var _previewTimer       = null;
    var _previewSeq         = 0;

    function fmtAmount(v, devise) {
        var num = parseFloat(v || 0);
        if (isNaN(num)) num = 0;
        return num.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + (devise ? ' ' + devise : '');
    }

    function resetPreview(message) {
        $('#blocCommissionPreview').removeClass('d-none');
        $('#previewRuleStatus').removeClass('badge-success badge-warning badge-secondary').addClass('badge-light').text('--');
        $('#previewHint').text(message || 'Saisissez type, compte, devise et montant.');
        $('#previewCommissionAmount').text('0,00');
        $('#previewImpactAmount').text('0,00');
        $('#previewSoldeAvant').text('--');
        $('#previewSoldeApres').text('--');
        $('#previewImpactLabel').text('Impact client');
        $('#previewFormule').text('');
        $('#previewAlerteInsuffisant').addClass('d-none');
    }

    function renderPreview(resp, devise) {
        $('#blocCommissionPreview').removeClass('d-none');

        if (!resp || !resp.ready) {
            resetPreview((resp && resp.message) ? resp.message : 'Simulation indisponible pour le moment.');
            return;
        }

        var commission = (resp.commission && resp.commission.montant) ? parseFloat(resp.commission.montant) : 0;
        var impactTotal = (resp.impact && resp.impact.total_client !== null && resp.impact.total_client !== undefined)
            ? parseFloat(resp.impact.total_client)
            : 0;
        var hasRule = !!(resp.commission && resp.commission.has_rule);
        var ruleLabel = (resp.commission && resp.commission.libelle) ? resp.commission.libelle : 'Aucune regle de commission applicable';

        $('#previewRuleStatus')
            .removeClass('badge-light badge-success badge-warning')
            .addClass(hasRule ? 'badge-success' : 'badge-warning')
            .text(hasRule ? 'Règle trouvée' : 'Commission par défaut (0)');

        $('#previewHint').text(ruleLabel);
        $('#previewCommissionAmount').text(fmtAmount(commission, devise));
        $('#previewImpactLabel').text((resp.impact && resp.impact.sens === 'DEBIT') ? 'Total debite client' : 'Net credite client');
        $('#previewImpactAmount').text(fmtAmount(impactTotal, devise));

        if (resp.compte) {
            $('#previewSoldeAvant').text(fmtAmount(resp.compte.solde_avant, resp.compte.devise || devise));
            $('#previewSoldeApres').text(fmtAmount(resp.compte.solde_apres, resp.compte.devise || devise));
            if (resp.compte.insuffisant) {
                $('#previewAlerteInsuffisant').removeClass('d-none');
            } else {
                $('#previewAlerteInsuffisant').addClass('d-none');
            }
        } else {
            $('#previewSoldeAvant').text('--');
            $('#previewSoldeApres').text('--');
            $('#previewAlerteInsuffisant').addClass('d-none');
        }

        $('#previewFormule').text((resp.impact && resp.impact.formule) ? resp.impact.formule : '');
    }

    function requestPreview() {
        var type = $('#selTypeOp').val();
        var devise = $('#selDevise').val();
        var montant = parseFloat($('#inpMontant').val() || '0');
        var compteCode = $('#selectedCompteCode').val();
        var requiresCompte = (type === 'DEPOT' || type === 'RETRAIT');

        if (!type) {
            resetPreview('Selectionnez un type d\'operation.');
            return;
        }

        if (requiresCompte && !compteCode) {
            resetPreview('Selectionnez et confirmez un compte client pour simuler.');
            return;
        }

        if (!devise || !montant || montant <= 0) {
            resetPreview('Renseignez la devise et un montant valide pour simuler la commission.');
            return;
        }

        var reqId = ++_previewSeq;
        $('#previewHint').text('Calcul en cours...');
        $('#blocCommissionPreview').removeClass('d-none');

        $.ajax({
            url: urlCommissionPreview,
            method: 'GET',
            dataType: 'json',
            cache: false,
            data: {
                type_operation: type,
                devise_code: devise,
                montant: montant,
                compte_code: compteCode || ''
            }
        }).done(function (resp) {
            if (reqId !== _previewSeq) return;
            renderPreview(resp, devise);
        }).fail(function (xhr) {
            if (reqId !== _previewSeq) return;
            var msg = 'Simulation indisponible.';
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            resetPreview(msg);
        });
    }

    function queuePreview() {
        if (_previewTimer) {
            clearTimeout(_previewTimer);
        }
        _previewTimer = setTimeout(requestPreview, 250);
    }

    $('#selCompte').on('select2:select', function () {
        var $opt     = $(this).find('option:selected');
        var code     = $opt.val();
        var devise   = $opt.data('devise');
        var solde    = $opt.data('solde');
        var nomCl    = $.trim($opt.data('client'));
        var prenom   = $.trim($opt.data('prenom'));
        var matric   = $opt.data('matricule');
        var tel      = $opt.data('telephone') || '—';
        var sexe     = $opt.data('sexe') || '';
        var photo    = $opt.data('photo') || '';

        _pendingCompteCode = code;
        _pendingDevise     = devise;

        // Remplir le modal d'identité
        $('#nomCompletIdentite').text(nomCl || '—');
        $('#prenomIdentite').text(prenom || '—');
        $('#matriculeIdentite').text(matric || '—');
        $('#compteIdentite').text(code);
        $('#telephoneIdentite').text(tel);
        $('#soldeIdentite').text(parseFloat(solde).toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' ' + devise);

        var badgeSexe = sexe === 'F' ? '<i class="fas fa-female mr-1"></i>Femme' : '<i class="fas fa-male mr-1"></i>Homme';
        var badgeClass = sexe === 'F' ? 'badge-pink' : 'badge-primary';
        $('#badgeSexeClient').removeClass('badge-primary badge-pink badge-secondary')
            .addClass(sexe === 'F' ? 'badge-danger' : 'badge-primary').html(badgeSexe);

        if (photo) {
            $('#photoIdentiteClient').attr('src', urlClientPhoto + '/' + encodeURIComponent(photo));
        } else {
            $('#photoIdentiteClient').attr('src', '{{ asset("vendor/adminlte/dist/img/user2-160x160.jpg") }}');
        }

        $('#modalIdentiteClient').modal('show');
    });

    // Confirmation identité — OUI
    $('#btnOuiIdentite').on('click', function () {
        if (_pendingCompteCode) {
            $('#selectedCompteCode').val(_pendingCompteCode);
            $('#selDevise').val(_pendingDevise).prop('disabled', true);
            queuePreview();
        }
        $('#modalIdentiteClient').modal('hide');
    });

    // Confirmation identité — NON
    $('#btnNonIdentite').on('click', function () {
        _pendingCompteCode = null;
        _pendingDevise = null;
        $('#selectedCompteCode').val('');
        $('#selCompte').val(null).trigger('change');
        $('#selDevise').prop('disabled', false);
        $('#modalIdentiteClient').modal('hide');
        resetPreview('Selectionnez et confirmez un compte client pour simuler.');
        showSystemMessage('warning', 'Opération annulée — identité non confirmée.');
    });

    $('#selCompte').on('select2:unselect select2:clear', function () {
        _pendingCompteCode = null;
        _pendingDevise = null;
        $('#selectedCompteCode').val('');
        $('#selDevise').prop('disabled', false);
        resetPreview('Selectionnez et confirmez un compte client pour simuler.');
    });

    function clearCompteSelection() {
        _pendingCompteCode = null;
        _pendingDevise = null;
        $('#selectedCompteCode').val('');
        $('#selCompte').val(null).trigger('change');
        $('#selDevise').prop('disabled', false);
        resetPreview();
    }

    // ─ Type opération → affichage dynamique ─────────────────────
    $('#selTypeOp').on('change', function () {
        var type = $(this).val();
        var avecCompte = (type === 'DEPOT' || type === 'RETRAIT');

        // Bloc compte
        if (avecCompte) {
            $('#blocCompte').removeClass('d-none');
            resetPreview('Selectionnez et confirmez un compte client pour simuler.');
        } else {
            $('#blocCompte').addClass('d-none');
            clearCompteSelection();
            resetPreview('Cette operation ne necessite pas de compte client.');
        }

        // Bloc change
        if (type === 'CHANGE') {
            $('#blocChange').removeClass('d-none');
            $('#labelDevise').html('Devise source <span class="text-danger">*</span>');
            $('#labelMontant').html('Montant source <span class="text-danger">*</span>');
        } else {
            $('#blocChange').addClass('d-none');
            $('#labelDevise').html('Devise <span class="text-danger">*</span>');
            $('#labelMontant').html('Montant <span class="text-danger">*</span>');
        }
        $('#inpMontant').attr('placeholder', type === 'CHANGE' ? 'Montant donné par le client' : '0.00');
        queuePreview();
    });

    $('#selDevise, #inpMontant').on('change input', queuePreview);

    // ── Taux de change ACTIF (Change au guichet) — jamais saisi librement ──
    window._tauxActifCourant = null;

    function fmtNombreCaisse(n, decimales) {
        return parseFloat(n).toLocaleString('fr-FR', { minimumFractionDigits: decimales, maximumFractionDigits: decimales });
    }

    function recalculerMontantDest() {
        var montant = parseFloat($('#inpMontant').val());
        var dest = $('#selDeviseDest').val();
        if (window._tauxActifCourant && montant > 0) {
            $('#inpMontantDest').text(fmtNombreCaisse(montant * window._tauxActifCourant, 2) + (dest ? ' ' + dest : ''));
        } else {
            $('#inpMontantDest').text('—');
        }
    }

    function chargerTauxActif() {
        var source = $('#selDevise').val();
        var dest   = $('#selDeviseDest').val();

        window._tauxActifCourant = null;
        $('#inpTaux').text('—');
        $('#inpMontantDest').text('—');
        $('#tauxInfoLabel').text('').removeClass('text-danger');

        if ($('#selTypeOp').val() !== 'CHANGE' || !source || !dest) { return; }
        if (source === dest) {
            $('#tauxInfoLabel').addClass('text-danger').text('Les deux devises doivent être différentes.');
            return;
        }

        $.getJSON(urlTauxActif, { source: source, destination: dest })
            .done(function (r) {
                if (r.success) {
                    window._tauxActifCourant = parseFloat(r.taux);
                    $('#inpTaux').html('1 ' + source + ' = <strong>' + fmtNombreCaisse(r.taux, 4) + '</strong> ' + dest);
                    $('#tauxInfoLabel').removeClass('text-danger')
                        .text(r.date_fin ? ('Actif du ' + r.date_debut + ' au ' + r.date_fin) : ('Actif depuis le ' + (r.date_debut || '—')));
                    recalculerMontantDest();
                }
            })
            .fail(function (xhr) {
                $('#inpTaux').text('—');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Aucun taux actif trouvé pour cette paire de devises.';
                $('#tauxInfoLabel').addClass('text-danger').text(msg);
            });
    }

    $('#selDevise, #selDeviseDest').on('change', chargerTauxActif);
    $('#inpMontant').on('change input', recalculerMontantDest);

    // ── Enregistrer une opération ────────────────────────────────
    $('#btnEnregistrerOp').on('click', function () {
        var type    = $('#selTypeOp').val();
        var devise  = $('#selDevise').val();
        var montant = $('#inpMontant').val();

        if (!type)    { showSystemMessage('error', 'Sélectionnez un type d\'opération.'); return; }
        if ((type === 'DEPOT' || type === 'RETRAIT') && !$('#selectedCompteCode').val()) {
            showSystemMessage('error', 'Recherchez et sélectionnez le compte client.'); return;
        }
        if (type === 'REMBOURSEMENT' && !$('#selectedDossierCreditId').val()) {
            showSystemMessage('error', 'Sélectionnez un dossier crédit.'); return;
        }
        if (!devise)  { showSystemMessage('error', 'Sélectionnez une devise.'); return; }
        if (!montant || parseFloat(montant) <= 0) { showSystemMessage('error', 'Entrez un montant valide.'); return; }

        if (type === 'CHANGE') {
            var deviseDest = $('#selDeviseDest').val();
            if (!deviseDest) { showSystemMessage('error', 'Sélectionnez la devise destination.'); return; }
            if (deviseDest === devise) { showSystemMessage('error', 'Les deux devises doivent être différentes.'); return; }
            if (!window._tauxActifCourant) {
                showSystemMessage('error', 'Aucun taux de change actif n\'est défini pour cette paire de devises. Contactez la Trésorerie.');
                return;
            }
        }

        var payload = {
            type_operation: type,
            devise_code:    devise,
            montant:        montant,
            observations:   $.trim($('#inpObservations').val()),
        };

        // Ajouter le compte client pour DEPOT et RETRAIT
        if (type === 'DEPOT' || type === 'RETRAIT') {
            payload.compte_code = $('#selectedCompteCode').val();
        }

        if (type === 'CHANGE') {
            // montant_dest et taux_change sont désormais calculés et verrouillés côté serveur
            // à partir du taux ACTIF (tb_taux_echanges) — jamais envoyés en saisie libre.
            payload.devise_dest = $('#selDeviseDest').val();
        }

        // Afficher le modal de confirmation avec option bordereau
        $('#modalConfirmOperation').modal('show');
        $('#modalConfirmType').text(type);
        $('#modalConfirmMontant').text(parseFloat(montant).toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' ' + devise);
        $('#modalConfirmDevise').text(devise);

        // Stocker le payload pour utilisation après confirmation
        window._pendingPayload = payload;
    });

    // ─ Confirmation de l'opération via modal ─────────────────────
    $('#btnConfirmOp').on('click', function () {
        var imprimerBordereau = $('#chkImprimerBordereauModal').is(':checked');
        var $btn = $('#btnEnregistrerOp');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enregistrement…');

        var payload = window._pendingPayload || {};
        payload.imprimer_bordereau = imprimerBordereau;

        $('#modalConfirmOperation').modal('hide');

        $.ajax({
            url: urlStore, method: 'POST', data: payload, dataType: 'json'
        })
        .done(function (r) {
            showSystemMessage('success', r.message || 'Opération enregistrée.');

            if (r.client_operation) {
                var detailsClient = 'Compte: ' + (r.client_operation.compte_code || 'N/A')
                    + '\nSolde avant: ' + (r.client_operation.solde_avant_fmt || '0,00')
                    + '\nImpact client: ' + (r.client_operation.montant_total_client_fmt || '0,00')
                    + '\nSolde apres: ' + (r.client_operation.solde_apres_fmt || '0,00');
                setTimeout(function () {
                    showSystemMessage('info', detailsClient, 'Information client');
                }, 200);
            }

            if (imprimerBordereau && r.bordereau_url) {
                setTimeout(function () { window.open(r.bordereau_url, '_blank'); }, 400);
            }
            
            // Actualisation automatique et fluide sans rechargement complet de la page
            if (r.soldes) {
                majSoldes(r.soldes);
            }
            chargerOpsJour();
            resetForm();
            
            $('#btnEnregistrerOp').prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Enregistrer');
            $('#modalConfirmOperation').modal('hide');
        })
        .fail(function (xhr) {
            handleAjaxFail(xhr, 'Enregistrement opération caisse');
            $('#btnEnregistrerOp').prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Enregistrer');
        });
    });

    function resetForm() {
        $('#selTypeOp').val('');
        $('#selDevise, #selDeviseDest').val('').prop('disabled', false);
        $('#inpMontant, #inpObservations').val('');
        $('#inpMontantDest, #inpTaux').text('—');
        $('#tauxInfoLabel').text('').removeClass('text-danger');
        window._tauxActifCourant = null;
        $('#chkImprimerBordereauModal').prop('checked', true);
        $('#blocChange').addClass('d-none');
        $('#blocCompte').addClass('d-none');
        clearCompteSelection();
        $('#labelDevise').html('Devise <span class="text-danger">*</span>');
        $('#labelMontant').html('Montant <span class="text-danger">*</span>');
        resetPreview();
    }

    resetPreview();

    // ── Mise à jour des soldes en temps réel ─────────────────────
    function majSoldes(soldes) {
        if (!soldes) return;
        $.each(soldes, function(i, s) {
            $('#soldePill_' + s.devise_code + ' .solde-val').text(s.solde_en_caisse);
        });
    }

    // ── Charger / rafraîchir le tableau des opérations ───────────
    function chargerOpsJour() {
        $.getJSON(urlJournal, { date: '{{ today()->toDateString() }}' })
        .done(function (data) {
            var ops = data.operations || [];

            // Priorité d'affichage des demandes: EN_ATTENTE -> APPROUVEE -> REJETEE -> aucune
            function demandePriority(op) {
                if (!op || !op.demande_statut) return 3;
                if (op.demande_statut === 'EN_ATTENTE') return 0;
                if (op.demande_statut === 'APPROUVEE') return 1;
                if (op.demande_statut === 'REJETEE') return 2;
                return 3;
            }

            ops.sort(function (a, b) {
                var pa = demandePriority(a);
                var pb = demandePriority(b);
                if (pa !== pb) return pa - pb;

                var idA = parseInt(a && a.id ? a.id : 0, 10);
                var idB = parseInt(b && b.id ? b.id : 0, 10);
                return idB - idA;
            });

            $('#opCount').text(ops.length);
            var tbody = $('#tbodyOps');
            if (!ops.length) {
                tbody.html('<tr id="trVide"><td colspan="8" class="text-center py-4 text-muted">'
                    + '<i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune opération aujourd\'hui.</td></tr>');
                return;
            }
            tbody.empty();
            $.each(ops, function(i, op) {
                var montantExtra = op.montant_dest_fmt
                    ? '<br><small class="text-info">→ ' + op.montant_dest_fmt + '</small>' : '';
                var clientCell = op.client_full_name
                    ? '<span class="text-info client-name-text"><i class="fas fa-user fa-xs"></i> ' + $('<div>').text(op.client_full_name).html() + '</span>'
                    : '<small class="text-muted">—</small>';
                var hasPendingDemande = op.demande_statut === 'EN_ATTENTE';
                var hasApprovedDemande = op.demande_statut === 'APPROUVEE';
                var demandeBloquee = hasPendingDemande || hasApprovedDemande;

                var demandeBtnActif = canDeleteOperation
                    ? '<button class="btn btn-xs btn-outline-warning btn-demande-modif ml-1" data-id="' + op.id + '" data-ref="' + op.reference + '" data-montant="' + op.montant + '" data-type="' + op.type + '" data-devise="' + (op.devise_code || '') + '" title="Demander modification/suppression"><i class="fas fa-edit"></i></button>'
                    : '<button class="btn btn-xs btn-outline-secondary btn-demande-modif" data-id="' + op.id + '" data-ref="' + op.reference + '" data-montant="' + op.montant + '" data-type="' + op.type + '" data-devise="' + (op.devise_code || '') + '" title="Demander modification/suppression"><i class="fas fa-edit"></i> Demander</button>';

                var demandeBtnBloque = hasPendingDemande
                    ? '<button class="btn btn-xs btn-outline-secondary ml-1" disabled title="Demande en attente"><i class="fas fa-lock"></i> En attente</button>'
                    : '<button class="btn btn-xs btn-outline-secondary ml-1" disabled title="Demande déjà approuvée"><i class="fas fa-lock"></i> Traité</button>';

                var annulerBtn = '';
                if (op.statut === 'CONFIRME' && !demandeBloquee) {
                    annulerBtn = canDeleteOperation
                        ? '<button class="btn btn-xs btn-outline-danger btn-annuler" data-id="' + op.id + '" data-ref="' + op.reference + '" data-montant="' + op.montant + '" data-type="' + op.type + '" data-devise="' + (op.devise_code || '') + '" title="Annuler"><i class="fas fa-times"></i></button>' + demandeBtnActif
                        : demandeBtnActif;
                } else if (op.statut === 'CONFIRME' && demandeBloquee) {
                    annulerBtn = demandeBtnBloque;
                }
                var bordereauBtn = '<a href="' + urlBordereau.replace('__ID__', op.id) + '" target="_blank" class="btn btn-xs btn-outline-info ml-1" title="Bordereau PDF"><i class="fas fa-file-invoice"></i></a>';
                var statutBadge = op.statut === 'ANNULE'
                    ? '<span class="badge badge-secondary">Annulée</span>'
                    : '<span class="badge badge-success">Confirmé</span>';
                var demandeBadge = '<span class="badge badge-light">Aucune demande</span>';
                if (op.demande_statut) {
                    if (op.demande_statut === 'EN_ATTENTE') {
                        demandeBadge = '<span class="badge badge-pill badge-warning px-2 py-1" title="Demande #' + (op.demande_id || '') + '"><i class="fas fa-clock mr-1"></i>Demande en attente #' + (op.demande_id || '') + '</span>';
                    } else if (op.demande_statut === 'APPROUVEE') {
                        demandeBadge = '<span class="badge badge-pill badge-success px-2 py-1" title="Demande #' + (op.demande_id || '') + '"><i class="fas fa-check mr-1"></i>Demande approuvée #' + (op.demande_id || '') + '</span>';
                    } else if (op.demande_statut === 'REJETEE') {
                        demandeBadge = '<span class="badge badge-pill badge-danger px-2 py-1" title="Demande #' + (op.demande_id || '') + '"><i class="fas fa-times mr-1"></i>Demande rejetée #' + (op.demande_id || '') + '</span>';
                    }
                }
                var rowClass = op.statut === 'ANNULE' ? ' class="text-muted"' : '';
                tbody.append(
                    '<tr' + rowClass + ' id="opRow_' + op.id + '">'
                    + '<td class="text-center"><i class="fas ' + op.icon + ' fa-sm"></i></td>'
                    + '<td><small class="text-monospace">' + op.reference + '</small><br>'
                    + '<span class="badge badge-pill badge-sm ' + op.badge_class + '">' + op.type_label + '</span>'
                    + (op.compte_code ? '<br><small class="text-muted"><i class="fas fa-university fa-xs"></i> ' + op.compte_code + '</small>' : '')
                    + '</td>'
                    + '<td>' + clientCell + '</td>'
                    + '<td class="font-weight-bold">' + op.montant_fmt + montantExtra + '</td>'
                    + '<td><small>' + (op.date ? op.date.substr(11,5) : '') + '</small></td>'
                    + '<td>' + statutBadge + '</td>'
                    + '<td>' + demandeBadge + '</td>'
                    + '<td>' + annulerBtn + bordereauBtn + '</td>'
                    + '</tr>'
                );
            });
        });
    }

    $('#btnRefreshOps').on('click', chargerOpsJour);
    chargerOpsJour();
    setInterval(chargerOpsJour, 45000);

    // ── Demander modification / suppression ──────────────────────
    var _demandeOpId = null;
    var _demandeCanSubmit = true;

    function setDemandeFormEnabled(enabled, message) {
        _demandeCanSubmit = !!enabled;

        $('#selTypeDemande, #inpNouveauMontant, #inpNouvObservations, #inpMotifDemande').prop('disabled', !enabled);

        if (enabled) {
            $('#btnSoumettreDemandeModif')
                .prop('disabled', false)
                .html('<i class="fas fa-paper-plane mr-1"></i>Soumettre la demande');
        } else {
            $('#btnSoumettreDemandeModif')
                .prop('disabled', true)
                .html('<i class="fas fa-lock mr-1"></i>Demande bloquée');
        }

        if (message) {
            showSystemMessage(enabled ? 'info' : 'warning', message);
        }
    }

    function renderEtatDemande(state) {
        var $box = $('#etatDemandeInfo');
        var $msg = $('#etatDemandeMessage');
        var $details = $('#etatDemandeDetails');

        $box.removeClass('d-none alert-secondary alert-success alert-info alert-warning alert-danger');
        $msg.text(state.message || 'État non disponible.');

        if (state.latest_demande) {
            var d = state.latest_demande;
            var detail = 'Dernière demande #' + d.id
                + ' • ' + (d.type_demande || '—')
                + ' • ' + (d.statut || '—');
            if (d.demandee_le) detail += ' • demandée le ' + d.demandee_le;
            if (d.traitee_le) detail += ' • traitée le ' + d.traitee_le;
            $details.text(detail);
        } else {
            $details.text('Aucune demande précédente pour cette opération.');
        }

        if (state.can_submit) {
            if (state.reason === 'DERNIERE_REJETEE') {
                $box.addClass('alert-info');
            } else {
                $box.addClass('alert-success');
            }
            setDemandeFormEnabled(true);
            return;
        }

        if (state.reason === 'DEMANDE_EN_ATTENTE' || state.reason === 'DEMANDE_DEJA_TRAITEE_APPROUVEE' || state.reason === 'OP_NON_CONFIRMEE') {
            $box.addClass('alert-danger');
        } else {
            $box.addClass('alert-warning');
        }

        setDemandeFormEnabled(false);
    }

    function chargerEtatDemande(operationId) {
        var $box = $('#etatDemandeInfo');
        $box.removeClass('d-none alert-success alert-info alert-warning alert-danger').addClass('alert-secondary');
        $('#etatDemandeMessage').text('Vérification de l\'état de la demande en cours...');
        $('#etatDemandeDetails').text('Veuillez patienter.');
        setDemandeFormEnabled(false);

        $.getJSON(urlDemandeStatut.replace('__ID__', operationId))
            .done(function (resp) {
                renderEtatDemande(resp || {});
            })
            .fail(function (xhr) {
                var msg = 'Impossible de vérifier l\'état de la demande pour le moment.';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                renderEtatDemande({
                    can_submit: false,
                    reason: 'ETAT_INDISPONIBLE',
                    message: msg,
                    latest_demande: null
                });
            });
    }

    $(document).on('click', '.btn-demande-modif', function () {
        _demandeOpId = $(this).data('id');

        $('#demandeRef').text($(this).data('ref') || '—');
        $('#demandeType').text($(this).data('type') || '—');
        var montant = $(this).data('montant');
        var devise  = $(this).data('devise') || '';
        $('#demandeAncienMontant').text(parseFloat(montant).toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' ' + devise);
        $('#demandeDevise').text(devise);
        $('#inpNouveauMontant').val('');
        $('#inpNouvObservations').val('');
        $('#inpMotifDemande').val('');
        $('#selTypeDemande').val('MODIFICATION').trigger('change');
        $('#modalDemandeModif').modal('show');
        chargerEtatDemande(_demandeOpId);
    });

    $('#selTypeDemande').on('change', function () {
        if ($(this).val() === 'SUPPRESSION') {
            $('#blocNouveauMontant').addClass('d-none');
        } else {
            $('#blocNouveauMontant').removeClass('d-none');
        }
    });

    $('#btnSoumettreDemandeModif').on('click', function () {
        if (!_demandeCanSubmit) {
            showSystemMessage('warning', 'Demande non autorisée pour cette opération. Consultez l\'état affiché dans le formulaire.');
            return;
        }

        var typeDemande   = $('#selTypeDemande').val();
        var motif         = $.trim($('#inpMotifDemande').val());
        var nouveauMontant = $('#inpNouveauMontant').val();
        var nouvObs       = $.trim($('#inpNouvObservations').val());

        if (!motif) {
            alert('Le motif de la demande est obligatoire.');
            $('#inpMotifDemande').focus();
            return;
        }
        if (typeDemande === 'MODIFICATION' && (!nouveauMontant || parseFloat(nouveauMontant) <= 0)) {
            alert('Veuillez saisir le nouveau montant.');
            $('#inpNouveauMontant').focus();
            return;
        }

        var url = urlDemandeModif.replace('__ID__', _demandeOpId);
        var payload = { type_demande: typeDemande, motif: motif };
        if (typeDemande === 'MODIFICATION') payload.nouveau_montant = nouveauMontant;
        if (nouvObs) payload.nouvelles_observations = nouvObs;

        $('#btnSoumettreDemandeModif').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Envoi...');
        $.post(url, payload)
        .done(function (r) {
            $('#modalDemandeModif').modal('hide');
            showSystemMessage('success', r.message || 'Demande envoyée au superviseur.');
        })
        .fail(function (xhr) {
            if (xhr && xhr.status === 422 && xhr.responseJSON) {
                var rj = xhr.responseJSON;
                if (rj.message) {
                    showSystemMessage('warning', rj.message);
                }
                chargerEtatDemande(_demandeOpId);
                return;
            }
            handleAjaxFail(xhr, 'Demande modification opération');
        })
        .always(function () {
            if (_demandeCanSubmit) {
                $('#btnSoumettreDemandeModif').prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i>Soumettre la demande');
            } else {
                $('#btnSoumettreDemandeModif').prop('disabled', true).html('<i class="fas fa-lock mr-1"></i>Demande bloquée');
            }
        });
    });

    // ── Annuler une opération (via demande de suppression) ─────────────────────────────
    $(document).on('click', '.btn-annuler', function () {
        var $btn = $(this);
        _demandeOpId = $btn.data('id');

        $('#demandeRef').text($btn.data('ref') || '—');
        $('#demandeType').text($btn.data('type') || '—');
        var montant = $btn.data('montant');
        var devise  = $btn.data('devise') || '';
        $('#demandeAncienMontant').text(parseFloat(montant).toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' ' + devise);
        $('#demandeDevise').text(devise);
        $('#inpNouveauMontant').val('');
        $('#inpNouvObservations').val('');
        $('#inpMotifDemande').val('');
        $('#selTypeDemande').val('SUPPRESSION').trigger('change');
        $('#modalDemandeModif').modal('show');
        chargerEtatDemande(_demandeOpId);
    });

    // ── Recherche progressive dans le tableau des opérations ─────────────────────────────
    $('#searchOpsInput').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('#tbodyOps tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(searchTerm) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    @endif
});
</script>
@endpush

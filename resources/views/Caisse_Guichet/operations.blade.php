{{-- ============================================================
     Opérations de Caisse — Guichetier
     Saisie : Dépôt, Retrait, Change, Paiement, Remboursement
     Permissions : EBEN-PER10 (voir) | EBEN-PER11 (saisir)
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
                                        data-solde="{{ number_format($cpt->solde_reel ?? 0, 2, '.', '') }}"
                                        data-client="{{ optional($cpt->client)->nom }} {{ optional($cpt->client)->postnom }}"
                                        data-prenom="{{ optional($cpt->client)->prenom }}"
                                        data-matricule="{{ optional($cpt->client)->matricule }}"
                                        data-telephone="{{ optional($cpt->client)->telephone }}"
                                        data-sexe="{{ optional($cpt->client)->sexe }}"
                                        data-photo="{{ optional($cpt->client)->photo ? basename(optional($cpt->client)->photo) : '' }}">
                                    [{{ $cpt->devise }}] {{ optional($cpt->client)->nom }} {{ optional($cpt->client)->postnom }} — {{ $cpt->code_compte }}
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
                            le guichet donne <em>Devise destination</em>.
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
                                <label class="font-weight-bold" style="font-size:.85rem;">Montant dest. <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm" id="inpMontantDest"
                                       placeholder="0.00" min="0.01" step="any">
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:.85rem;">Taux appliqué</label>
                            <input type="number" class="form-control form-control-sm" id="inpTaux"
                                   placeholder="Ex : 2850.00" min="0" step="any">
                        </div>
                    </div>

                    {{-- ── Observations ─────────────────────── --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold" style="font-size:.85rem;">Observations</label>
                        <input type="text" class="form-control form-control-sm" id="inpObservations"
                               placeholder="Remarque optionnelle…" maxlength="500"
                               {{ !$guichetOuvert ? 'disabled' : '' }}>
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
                    <button class="btn btn-xs btn-outline-secondary" id="btnRefreshOps" title="Actualiser">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:30px;"></th>
                                    <th>Réf.</th>
                                    <th>Montant</th>
                                    <th>Heure</th>
                                    <th>Statut</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="tbodyOps">
                                @forelse($operations as $op)
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
                                        <div class="op-actions">
                                            @if($op->statut === 'CONFIRME')
                                            <button class="btn btn-xs btn-outline-danger btn-annuler"
                                                    data-id="{{ $op->id }}" title="Annuler">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <button class="btn btn-xs btn-outline-warning btn-demande-modif"
                                                    data-id="{{ $op->id }}"
                                                    data-ref="{{ $op->reference }}"
                                                    data-montant="{{ $op->montant }}"
                                                    data-type="{{ $op->type }}"
                                                    data-devise="{{ $op->devise_code }}"
                                                    title="Demander modification / suppression">
                                                <i class="fas fa-edit"></i>
                                            </button>
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
                                    <td colspan="6" class="text-center py-4 text-muted">
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
                            <img id="photoIdentiteClient"
                                 src="{{ asset('images_projet/default_user.png') }}"
                                 alt="Photo client"
                                 style="width:110px;height:130px;object-fit:cover;border-radius:10px;
                                        border:3px solid #3b82f6;background:#e2e8f0;">
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

@endsection


@push('css')
<style>
    .solde-pill { transition: background .2s; }
    .table-sm td { font-size: .88rem; vertical-align: middle; }
    .table-sm th { font-size: .82rem; }
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
    var urlDemandeModif  = '{{ route("caisses.operations.demande.modification", ["id" => "__ID__"]) }}';
    var urlBordereau     = '{{ route("caisses.operations.bordereau", ["id" => "__ID__"]) }}';
    var urlSearchCompte   = '{{ route("caisses.operations.comptes.search") }}';
    var urlCommissionPreview = '{{ route("caisses.operations.commission.preview") }}';
    var urlClientPhoto    = '{{ url("/clients/photo") }}';

    // ── Select2 — Recherche compte client (chargé côté serveur) ──
    $('#selCompte').select2({
        theme         : 'bootstrap4',
        width         : '100%',
        dropdownParent: $('body'),
        placeholder   : '— Sélectionner un compte —',
        allowClear    : true,
        language      : { noResults: function () { return 'Aucun compte trouvé.'; } }
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

    // ── Type opération → affichage dynamique ─────────────────────
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

    // ── Enregistrer une opération ────────────────────────────────
    $('#btnEnregistrerOp').on('click', function () {
        var type    = $('#selTypeOp').val();
        var devise  = $('#selDevise').val();
        var montant = $('#inpMontant').val();

        if (!type)    { showSystemMessage('error', 'Sélectionnez un type d\'opération.'); return; }
        if ((type === 'DEPOT' || type === 'RETRAIT') && !$('#selectedCompteCode').val()) {
            showSystemMessage('error', 'Recherchez et sélectionnez le compte client.'); return;
        }
        if (!devise)  { showSystemMessage('error', 'Sélectionnez une devise.'); return; }
        if (!montant || parseFloat(montant) <= 0) { showSystemMessage('error', 'Entrez un montant valide.'); return; }

        if (type === 'CHANGE') {
            var deviseDest = $('#selDeviseDest').val();
            var montDest   = $('#inpMontantDest').val();
            if (!deviseDest) { showSystemMessage('error', 'Sélectionnez la devise destination.'); return; }
            if (!montDest || parseFloat(montDest) <= 0) { showSystemMessage('error', 'Entrez le montant destination.'); return; }
            if (deviseDest === devise) { showSystemMessage('error', 'Les deux devises doivent être différentes.'); return; }
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enregistrement…');

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
            payload.devise_dest  = $('#selDeviseDest').val();
            payload.montant_dest = $('#inpMontantDest').val();
            payload.taux_change  = $('#inpTaux').val() || null;
        }

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

            // Ouvrir automatiquement le bordereau dans un nouvel onglet
            if (r.bordereau_url) {
                setTimeout(function () { window.open(r.bordereau_url, '_blank'); }, 400);
            }
            setTimeout(function () { location.reload(); }, 1000);
        })
        .fail(function (xhr) {
            handleAjaxFail(xhr, 'Enregistrement opération caisse');
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Enregistrer');
        });
    });

    function resetForm() {
        $('#selTypeOp').val('');
        $('#selDevise, #selDeviseDest').val('').prop('disabled', false);
        $('#inpMontant, #inpMontantDest, #inpTaux, #inpObservations').val('');
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
            $('#opCount').text(ops.length);
            var tbody = $('#tbodyOps');
            if (!ops.length) {
                tbody.html('<tr id="trVide"><td colspan="7" class="text-center py-4 text-muted">'
                    + '<i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune opération aujourd\'hui.</td></tr>');
                return;
            }
            tbody.empty();
            $.each(ops, function(i, op) {
                var montantExtra = op.montant_dest_fmt
                    ? '<br><small class="text-info">→ ' + op.montant_dest_fmt + '</small>' : '';
                var annulerBtn = op.statut === 'CONFIRME'
                    ? '<button class="btn btn-xs btn-outline-danger btn-annuler" data-id="' + op.id + '" title="Annuler"><i class="fas fa-times"></i></button>'
                    + '<button class="btn btn-xs btn-outline-warning btn-demande-modif ml-1" data-id="' + op.id + '" data-ref="' + op.reference + '" data-montant="' + op.montant + '" data-type="' + op.type + '" data-devise="' + (op.devise_code || '') + '" title="Demander modification/suppression"><i class="fas fa-edit"></i></button>'
                    : '';
                var bordereauBtn = '<a href="' + urlBordereau.replace('__ID__', op.id) + '" target="_blank" class="btn btn-xs btn-outline-info ml-1" title="Bordereau PDF"><i class="fas fa-file-invoice"></i></a>';
                var statutBadge = op.statut === 'ANNULE'
                    ? '<span class="badge badge-secondary">Annulée</span>'
                    : '<span class="badge badge-success">Confirmé</span>';
                var rowClass = op.statut === 'ANNULE' ? ' class="text-muted"' : '';
                tbody.append(
                    '<tr' + rowClass + ' id="opRow_' + op.id + '">'
                    + '<td class="text-center"><i class="fas ' + op.icon + ' fa-sm"></i></td>'
                    + '<td><small class="text-monospace">' + op.reference + '</small><br>'
                    + '<span class="badge badge-pill badge-sm ' + op.badge_class + '">' + op.type_label + '</span>'
                    + (op.compte_code ? '<br><small class="text-muted"><i class="fas fa-university fa-xs"></i> ' + op.compte_code + '</small>' : '')
                    + '</td>'
                    + '<td class="font-weight-bold">' + op.montant_fmt + montantExtra + '</td>'
                    + '<td><small>' + (op.date ? op.date.substr(11,5) : '') + '</small></td>'
                    + '<td>' + statutBadge + '</td>'
                    + '<td>' + annulerBtn + bordereauBtn + '</td>'
                    + '</tr>'
                );
            });
        });
    }

    $('#btnRefreshOps').on('click', chargerOpsJour);
    setInterval(chargerOpsJour, 45000);

    // ── Demander modification / suppression ──────────────────────
    var _demandeOpId = null;
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
    });

    $('#selTypeDemande').on('change', function () {
        if ($(this).val() === 'SUPPRESSION') {
            $('#blocNouveauMontant').addClass('d-none');
        } else {
            $('#blocNouveauMontant').removeClass('d-none');
        }
    });

    $('#btnSoumettreDemandeModif').on('click', function () {
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
            handleAjaxFail(xhr, 'Demande modification opération');
        })
        .always(function () {
            $('#btnSoumettreDemandeModif').prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i>Soumettre la demande');
        });
    });

    // ── Annuler une opération ─────────────────────────────────────
    $(document).on('click', '.btn-annuler', function () {
        var id = $(this).data('id');
        showUniversalConfirm('Annuler cette opération ? Le solde du guichet sera recalculé.', function () {
            $.post(urlAnnuler.replace('__ID__', id))
            .done(function (r) {
                showSystemMessage('success', r.message || 'Opération annulée.');
                setTimeout(function () { location.reload(); }, 1000);
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Annulation opération caisse');
            });
        }, {
            title: 'Annuler l\'opération',
            btnLabel: 'Oui, annuler',
            btnClass: 'btn-danger',
            icon: 'fas fa-undo',
            headerClass: 'bg-danger text-white',
        });
    });

    @endif
});
</script>
@endpush

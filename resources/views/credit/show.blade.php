@extends('layouts.app')

@section('page_title', 'Dossier Crédit – ' . $demande->numero_dossier)
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Détail dossier')

@section('content')
<section class="content">
<div class="container-fluid">

@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
@endif
@if(session('deblocage_refs'))
    @php $refs = session('deblocage_refs'); @endphp
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <i class="fas fa-receipt mr-1"></i>
        Réf. déblocage 100% RMB: <strong>{{ $refs['reference_deblocage'] ?? '-' }}</strong>
        | Réf. transfert caution 20% RMB → GTC: <strong>{{ $refs['reference_caution'] ?? '-' }}</strong>
        | Réf. dépôt GTC 20% (bloqué): <strong>{{ $refs['reference_transfert_gtc'] ?? '-' }}</strong>
        | Réf. frais 4% non remboursables: <strong>{{ $refs['reference_frais'] ?? '-' }}</strong>
        @if(!empty($refs['transaction_deblocage_id']))
            | <a href="{{ route('caisses.operations.bordereau', ['id' => $refs['transaction_deblocage_id']]) }}" target="_blank" rel="noopener">Imprimer bordereau 100%</a>
        @endif
        @if(!empty($refs['transaction_gtc_id']))
            | <a href="{{ route('caisses.operations.bordereau', ['id' => $refs['transaction_gtc_id']]) }}" target="_blank" rel="noopener">Imprimer bordereau transfert RMB → GTC</a>
        @endif
        @if(!empty($refs['transaction_gtc_depot_id']))
            | <a href="{{ route('caisses.operations.bordereau', ['id' => $refs['transaction_gtc_depot_id']]) }}" target="_blank" rel="noopener">Imprimer bordereau dépôt GTC</a>
        @endif
        @if(!empty($refs['transaction_frais_id']))
            | <a href="{{ route('caisses.operations.bordereau', ['id' => $refs['transaction_frais_id']]) }}" target="_blank" rel="noopener">Imprimer bordereau frais 4%</a>
        @endif
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    </div>
@endif

{{-- ── Header --}}
<div class="card card-outline card-success mb-3">
    <div class="card-header">
        <h4 class="card-title mb-0 d-flex align-items-center gap-2 flex-wrap">
            <i class="fas fa-file-alt mr-2"></i>
            {{ $demande->numero_dossier }}
            {!! $demande->badgeStatut() !!}
            @if(isset($soldeRmb) && $soldeRmb > 0)
                <span class="badge badge-info ml-2" title="Solde du compte RMB">
                    <i class="fas fa-wallet mr-1"></i>Solde RMB : {{ number_format($soldeRmb, 2, ',', ' ') }} {{ $demande->devise }}
                </span>
            @elseif(isset($soldeRmb))
                <span class="badge badge-secondary ml-2" title="Solde du compte RMB">
                    <i class="fas fa-wallet mr-1"></i>Solde RMB : 0,00 {{ $demande->devise }}
                </span>
            @endif
        </h4>
        <div class="card-tools d-flex gap-1">
            {{-- Action buttons --}}
            @if($demande->statut === 'BROUILLON')
                @if(in_array('EBEN-PER56', $userPermCodes ?? []) || in_array('EBEN-PER53', $userPermCodes ?? []))
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalSoumettre">
                    <i class="fas fa-paper-plane mr-1"></i>Soumettre
                </button>
                @endif
            @endif

            @if(in_array($demande->statut, ['SOUMIS', 'EN_ANALYSE']))
                @if(in_array('EBEN-PER61', $userPermCodes ?? []))
                @if($demande->statut === 'SOUMIS' && !$demande->agent_analyse_matricule)
                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalAffecterAnalyse">
                    <i class="fas fa-user-check mr-1"></i>Affecter agent crédit
                </button>
                @endif
                @endif

                @if(in_array('EBEN-PER58', $userPermCodes ?? []))
                @if(($authUser?->agent?->matricule ?? null) === $demande->agent_analyse_matricule)
                <a href="{{ route('credit.analyse', $demande) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-search mr-1"></i>Analyser
                </a>
                @endif
                @endif
            @endif

            @if(in_array($demande->statut, ['EN_VALIDATION','PRET_A_DEBLOQUER']))
                @if(in_array('EBEN-PER60', $userPermCodes ?? []) || in_array('EBEN-PER61', $userPermCodes ?? []) ||
                    in_array('EBEN-PER62', $userPermCodes ?? []) || in_array('EBEN-PER63', $userPermCodes ?? []))
                <a href="{{ route('credit.validation', $demande) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-stamp mr-1"></i>Valider
                </a>
                @endif
            @endif

            @if($demande->statut === 'PRET_A_DEBLOQUER')
                @if(in_array('EBEN-PER64', $userPermCodes ?? []))
                <a href="{{ route('credit.deblocage', $demande) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-unlock mr-1"></i>Débloquer
                </a>
                @endif
            @endif

            @if(in_array($demande->statut, ['DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD']))
                @if(in_array('EBEN-PER10', $userPermCodes ?? []) || in_array('EBEN-PER111', $userPermCodes ?? []))
                <a href="{{ route('credit.remboursement', $demande) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-money-bill-wave mr-1"></i>Remboursement
                </a>
                @endif
            @endif

            @if(in_array($demande->statut, ['BROUILLON','SOUMIS','EN_ANALYSE','EN_VALIDATION']) && in_array('EBEN-PER66', $userPermCodes ?? []))
            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalAnnuler">
                <i class="fas fa-times-circle mr-1"></i>Annuler
            </button>
            @endif

            @if(in_array($demande->statut, ['DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','EN_VALIDATION','EN_ANALYSE','SOUMIS']) && in_array('EBEN-PER67', $userPermCodes ?? []))
            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalSuspendre">
                <i class="fas fa-pause mr-1"></i>Suspendre
            </button>
            @endif

            @if(!in_array($demande->statut, ['ANNULE','SUSPECT','SOLDE']) && in_array('EBEN-PER68', $userPermCodes ?? []))
            <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#modalSignalerSuspect">
                <i class="fas fa-exclamation-triangle mr-1"></i>Signaler suspect
            </button>
            @endif
        </div>
    </div>
</div>

{{-- ── Tabs --}}
<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs nav-pills nav-pills-sm" id="tabs">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_infos">Informations</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_analyse">Analyse</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_validations">Validations</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_pieces">Pièces &amp; docs</a></li>
            @if($demande->deblocages->count())
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_deblocage">Déblocage</a></li>
            @endif
            @if($demande->echeancier)
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_echeancier">Échéancier</a></li>
            @endif
            @if($demande->remboursements->count())
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_remboursements">Remboursements</a></li>
            @endif
            @if($canViewAudit ?? false)
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_audit">Journal</a></li>
            @endif
            @if($demande->echeancier && in_array($demande->statut, ['SOUMIS','EN_ANALYSE','EN_VALIDATION','PRET_A_DEBLOQUER','DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','SOLDE']) && in_array('EBEN-PER71', $userPermCodes ?? []))
            <li class="nav-item ml-auto">
                <a href="{{ route('credit.pdf.echeancier', $demande) }}" class="nav-link text-primary" target="_blank">
                    <i class="fas fa-print mr-1"></i>PDF Échéancier
                </a>
            </li>
            @endif

            @if(in_array($demande->statut, ['SOUMIS','EN_ANALYSE','EN_VALIDATION','PRET_A_DEBLOQUER','DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','SOLDE']) && in_array('EBEN-PER71', $userPermCodes ?? []))
            <li class="nav-item">
                <a href="{{ route('credit.pdf.fiche', $demande) }}" class="nav-link text-danger" target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i>Dossier + analyse PDF
                </a>
            </li>
            @endif
        </ul>
    </div>
    <div class="card-body">
    <div class="tab-content">

        {{-- ── TAB INFOS ──────────────────────────────────────── --}}
        <div class="tab-pane active" id="tab_infos">
        <div class="row">
            <div class="col-md-6">
                @php
                    $clientFullName = trim(($demande->client->nom ?? '').' '.($demande->client->postnom ?? '').' '.($demande->client->prenom ?? ''));
                    $clientPhotoUrl = !empty(optional($demande->client)->photo)
                        ? route('clients.photo', basename($demande->client->photo))
                        : asset('vendor/adminlte/dist/img/user2-160x160.jpg');
                @endphp
                <div class="d-flex align-items-start mb-3">
                    <img src="{{ $clientPhotoUrl }}"
                         alt="Photo client"
                         class="img-thumbnail mr-3"
                         style="width:96px;height:112px;object-fit:cover;border-radius:8px;">
                    <div>
                        <div class="small text-muted">Client bénéficiaire</div>
                        <div class="font-weight-bold">{{ $clientFullName !== '' ? $clientFullName : $demande->client_matricule }}</div>
                        <div class="small text-muted">{{ $demande->client_matricule }}</div>
                    </div>
                </div>
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Numéro dossier</th><td><strong>{{ $demande->numero_dossier }}</strong></td></tr>
                    <tr><th>Statut</th><td>{!! $demande->badgeStatut() !!}</td></tr>
                    <tr><th>Type de crédit</th><td>{{ $demande->type_credit }}</td></tr>
                    <tr><th>Client</th><td>
                        @if($demande->client)
                            {{ trim(($demande->client->nom ?? '').' '.($demande->client->postnom ?? '').' '.($demande->client->prenom ?? '')) }}
                            <small class="text-muted">({{ $demande->client_matricule }})</small>
                        @else {{ $demande->client_matricule }}
                        @endif
                    </td></tr>
                    <tr><th>Zone</th><td>{{ $demande->code_zone ?? '–' }}</td></tr>
                    <tr><th>Demande créée par</th><td>
                        @if(!empty($demandeurMeta['nom_complet']))
                            {{ $demandeurMeta['nom_complet'] }}
                            <small class="text-muted">({{ $demandeurMeta['matricule'] ?? '-' }})</small>
                        @else
                            {{ $demande->agent_createur_matricule ?? '–' }}
                        @endif
                    </td></tr>
                    <tr><th>Rôle du demandeur</th><td>
                        {{ $demandeurMeta['role_nom'] ?? $demandeurMeta['role_code'] ?? '–' }}
                    </td></tr>
                    <tr><th>Agent crédit affecté</th><td>
                        @if($demande->agentAnalyse)
                            {{ $demande->agentAnalyse->full_name ?: trim(($demande->agentAnalyse->nom ?? '').' '.($demande->agentAnalyse->postnom ?? '').' '.($demande->agentAnalyse->prenom ?? '')) }}
                            <small class="text-muted">({{ $demande->agent_analyse_matricule }})</small>
                        @else
                            <span class="text-muted">Non affecté</span>
                        @endif
                    </td></tr>
                    <tr><th>Portefeuille crédit</th><td>
                        @if($demande->portefeuille)
                            {{ $demande->portefeuille->nom_portefeuille }}
                            <small class="text-muted">(#{{ $demande->portefeuille_id }})</small>
                        @elseif($demande->portefeuille_id)
                            <span class="text-muted">Portefeuille #{{ $demande->portefeuille_id }}</span>
                        @else
                            <span class="text-muted">Non défini</span>
                        @endif
                    </td></tr>
                    @if($demande->service_provenance)
                    <tr><th>Service référent</th><td>
                        <span class="badge badge-info"><i class="fas fa-building mr-1"></i>{{ $demande->service_provenance }}</span>
                        @if($demande->referent_nom)
                            <br><small class="text-muted">Référent : {{ $demande->referent_nom }}</small>
                        @endif
                    </td></tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Montant demandé</th>
                        <td><strong>{{ number_format($demande->montant_demande, 2, ',', ' ') }} {{ $demande->devise }}</strong></td></tr>
                    <tr><th>Montant accordé</th>
                        <td>{{ $demande->montant_accorde ? number_format($demande->montant_accorde, 2, ',', ' ').' '.$demande->devise : '–' }}</td></tr>
                    <tr><th>Durée</th><td>{{ $demande->duree_mois }} mois</td></tr>
                    <tr><th>Taux mensuel</th><td>{{ number_format((float) $demande->taux_interet_mensuel, 1, '.', '') }} %</td></tr>
                    <tr><th>Frais de dossier</th>
                        <td>{{ $demande->frais_dossier ? number_format($demande->frais_dossier, 2, ',', ' ').' '.$demande->devise : '–' }}</td></tr>
                    <tr><th>Date soumission</th><td>{{ optional($demande->date_soumission)->format('d/m/Y') ?? '–' }}</td></tr>
                    <tr><th>Date déblocage</th><td>{{ optional($demande->date_deblocage)->format('d/m/Y') ?? '–' }}</td></tr>
                    @if(in_array($demande->statut_global, ['DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD']))
                    <tr>
                        <th>Prélèvement auto (RMB)</th>
                        <td>
                            @if($demande->prelevement_auto_autorise)
                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Autorisé</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-times-circle mr-1"></i>Non autorisé</span>
                            @endif
                            @if(in_array('EBEN-PER113', $userPermCodes ?? []))
                                <form method="POST" action="{{ route('credit.prelevement_auto.toggle', $demande) }}" class="d-inline ml-1"
                                      onsubmit="return confirm('{{ $demande->prelevement_auto_autorise ? 'Révoquer' : 'Autoriser' }} le prélèvement automatique pour ce dossier ?');">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-outline-{{ $demande->prelevement_auto_autorise ? 'danger' : 'success' }}">
                                        {{ $demande->prelevement_auto_autorise ? 'Révoquer' : 'Autoriser' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="col-12">
                <strong>Objet :</strong> {{ $demande->objet_credit }}<br>
                @if($demande->garantie_description)
                    <strong>Garanties :</strong> {{ $demande->garantie_description }}
                @endif
                @if($demande->motif_rejet)
                    <div class="alert alert-danger mt-2 mb-0"><strong>Motif de rejet :</strong> {{ $demande->motif_rejet }}</div>
                @endif
                @if($demande->motif_annulation)
                    <div class="alert alert-secondary mt-2 mb-0"><strong>Motif annulation :</strong> {{ $demande->motif_annulation }}</div>
                @endif
            </div>
        </div>
        </div>

        {{-- ── TAB ANALYSE ──────────────────────────────────────── --}}
        <div class="tab-pane" id="tab_analyse">
        @if($demande->analyse)
            @php $a = $demande->analyse @endphp
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <tr><th class="bg-light" colspan="2">Analyse économique</th></tr>
                        <tr><th>Activité principale</th><td>{{ $a->activite_principale ?? '–' }}</td></tr>
                        <tr><th>Revenu mensuel net</th><td>{{ $a->revenu_mensuel_net ? number_format($a->revenu_mensuel_net,2,',',' ').' '.$demande->devise : '–' }}</td></tr>
                        <tr><th>Taux endettement</th><td>{{ $a->taux_endettement ?? '–' }} %</td></tr>
                        <tr><th>Capacité remboursement</th><td>{{ $a->capacite_remboursement ? number_format($a->capacite_remboursement,2,',',' ').' '.$demande->devise : '–' }}</td></tr>
                        <tr><th>Valeur garantie</th><td>{{ $a->valeur_garantie ? number_format($a->valeur_garantie,2,',',' ') : '–' }}</td></tr>
                        <tr><th>Score risque</th><td>{{ $a->score_risque ?? '–' }}/100</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <tr><th class="bg-light" colspan="2">Avis &amp; Recommandations</th></tr>
                        <tr><th>Statut analyse</th><td>
                            @if($a->statut_analyse === 'COMPLETE')
                                <span class="badge badge-success">Complétée</span>
                            @else
                                <span class="badge badge-warning">En cours</span>
                            @endif
                        </td></tr>
                        <tr><th>Recommandation</th><td>{{ $a->recommandation ?? '–' }}</td></tr>
                        <tr><th>Analysé par</th><td>{{ optional($a->analyseur)->nom_complet ?? '–' }}</td></tr>
                        <tr><th>Date analyse</th><td>{{ optional($a->date_analyse)->format('d/m/Y H:i') ?? '–' }}</td></tr>
                    </table>
                    @if($a->observations)
                    <div class="alert alert-light border"><strong>Observations :</strong><br>{{ $a->observations }}</div>
                    @endif
                </div>
            </div>
        @else
            <p class="text-muted text-center py-4">
                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                Aucune analyse enregistrée pour ce dossier.
            </p>
        @endif
        </div>

        {{-- ── TAB VALIDATIONS ──────────────────────────────────── --}}
        <div class="tab-pane" id="tab_validations">
        <div class="row">
            @php
                $vMap  = $demande->validations->keyBy('type_validateur');
                $types = ['AGENT_CREDIT','CONTROLEUR','CHARGE_OPERATIONS','GERANT'];
                $labels = ['AGENT_CREDIT'=>'Agent crédit','CONTROLEUR'=>'Contrôleur','CHARGE_OPERATIONS'=>'Chargé opérations','GERANT'=>'Gérant'];
                $colors = ['APPROUVE'=>'success','APPROUVE_AVEC_RESERVE'=>'warning','REJETE'=>'danger','EN_ATTENTE'=>'secondary'];
            @endphp
            @foreach($types as $type)
                @php $v = $vMap[$type] ?? null @endphp
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-outline card-{{ $v ? ($colors[$v->decision] ?? 'secondary') : 'secondary' }} h-100">
                        <div class="card-header text-sm py-2">
                            <strong>{{ $labels[$type] }}</strong>
                        </div>
                        <div class="card-body py-2">
                            @if($v)
                                <p class="mb-1">
                                    <span class="badge badge-{{ $colors[$v->decision] ?? 'secondary' }}">
                                        {{ str_replace('_',' ', $v->decision) }}
                                    </span>
                                </p>
                                @if($v->decision !== 'EN_ATTENTE')
                                    <small><strong>Par :</strong> {{ optional($v->validateur)->nom_complet ?? $v->nom_signataire ?? $v->signature_agent ?? $v->validateur_matricule ?? '–' }}</small><br>
                                    <small><strong>Le :</strong> {{ optional($v->date_validation)->format('d/m/Y H:i') ?? '–' }}</small><br>
                                @endif
                                @if($v->montant_propose)
                                    <small><strong>Montant proposé :</strong><br>{{ number_format($v->montant_propose,2,',',' ') }} {{ $demande->devise }}</small><br>
                                @endif
                                @if($v->duree_mois_validee)
                                    <small><strong>Durée validée :</strong><br>{{ $v->duree_mois_validee }} mois</small><br>
                                @endif
                                @if($v->commentaire)
                                    <p class="mt-1 mb-0 small text-muted">{{ $v->commentaire }}</p>
                                @endif
                                @if($v->conditions)
                                    <p class="mt-1 mb-0 small"><strong>Conditions :</strong> {{ $v->conditions }}</p>
                                @endif
                            @else
                                <span class="badge badge-secondary">Non commencé</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        </div>

        {{-- ── TAB PIECES ──────────────────────────────────────── --}}
        @php
            $piecesModifiables = !in_array($demande->statut, ['PRET_A_DEBLOQUER','DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','SOLDE','ANNULE']);
            $peutGererPieces = in_array('EBEN-PER73', $userPermCodes ?? []) && $piecesModifiables;
        @endphp
        <div class="tab-pane" id="tab_pieces">
        @if($demande->pieces->count())
            <table class="table table-sm table-hover">
                <thead><tr>
                    <th>Type de pièce</th><th>Référence</th>
                    <th>Statut</th><th>Commentaire</th>
                    @if($peutGererPieces)<th></th>@endif
                </tr></thead>
                <tbody>
                @foreach($demande->pieces as $p)
                <tr>
                    <td>{{ $p->type_piece }}</td>
                    <td>
                        @if($p->nom_fichier && str_starts_with($p->nom_fichier, 'credits/pieces/'))
                            <a href="{{ route('credit.pieces.fichier', [$demande, $p]) }}" target="_blank" title="Voir le document (PDF)">
                                <i class="fas fa-file-pdf text-danger mr-1"></i>Voir le document
                            </a>
                        @else
                            {{ $p->reference ?? '–' }}
                        @endif
                    </td>
                    <td>
                        @if($p->fourni)
                            <span class="badge badge-success">Fourni</span>
                        @else
                            <span class="badge badge-warning">Manquant</span>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $p->commentaire ?? '–' }}</small></td>
                    @if($peutGererPieces)
                    <td class="text-right">
                        <button type="button" class="btn btn-xs btn-outline-info" data-toggle="modal" data-target="#modalPiece{{ $p->id }}">
                            <i class="fas fa-edit mr-1"></i>Éditer
                        </button>
                    </td>
                    @endif
                </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted text-center py-3">Aucune pièce enregistrée.</p>
        @endif
        @if(!$piecesModifiables && in_array('EBEN-PER73', $userPermCodes ?? []) && $demande->pieces->count())
            <small class="text-muted"><i class="fas fa-lock mr-1"></i>Les pièces ne sont plus modifiables à ce stade du dossier.</small>
        @endif
        </div>

        @if($peutGererPieces)
            @foreach($demande->pieces as $p)
            <div class="modal fade" id="modalPiece{{ $p->id }}" tabindex="-1">
                <div class="modal-dialog"><div class="modal-content">
                    <form method="POST" action="{{ route('credit.pieces.update', [$demande, $p]) }}" enctype="multipart/form-data">@csrf
                        <div class="modal-header bg-info">
                            <h5 class="modal-title">Pièce : {{ $p->type_piece }}</h5>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-3">{{ $p->libelle }}</p>

                            @if($p->nom_fichier && str_starts_with($p->nom_fichier, 'credits/pieces/'))
                            <p class="mb-3">
                                <a href="{{ route('credit.pieces.fichier', [$demande, $p]) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-file-pdf mr-1"></i>Voir le document actuel
                                </a>
                            </p>
                            @endif

                            <div class="form-group">
                                <label>Photo / scan du document</label>
                                <div class="custom-file">
                                    <input type="file" name="fichier" class="custom-file-input" id="fichierPiece{{ $p->id }}" accept=".jpg,.jpeg,.png,.pdf" capture="environment">
                                    <label class="custom-file-label" for="fichierPiece{{ $p->id }}">Prendre une photo ou choisir un fichier...</label>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>Pour une pièce comme la carte d'électeur : prenez simplement une photo (téléphone/webcam) ou un PDF déjà scanné.
                                    Le document sera automatiquement enregistré au format PDF{{ $p->nom_fichier ? ' (remplace le fichier actuel)' : '' }}.
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="est_recu" value="0">
                                    <input type="checkbox" name="est_recu" value="1" class="custom-control-input" id="estRecu{{ $p->id }}" {{ $p->fourni ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="estRecu{{ $p->id }}">Pièce fournie par le client</label>
                                </div>
                            </div>
                            @php $aUnFichierReel = $p->nom_fichier && str_starts_with($p->nom_fichier, 'credits/pieces/'); @endphp
                            @if(!$aUnFichierReel)
                            <div class="form-group">
                                <label>Référence / nom du document (si pas de photo/fichier joint)</label>
                                <input type="text" name="nom_fichier" class="form-control" maxlength="255" value="{{ $p->reference }}" placeholder="Ex: CNI_KAYEMBE_2026.pdf">
                                <small class="text-muted">Simple texte libre, ignoré si une photo/fichier est joint ci-dessus.</small>
                            </div>
                            @else
                                {{-- Un vrai fichier est déjà attaché : on ne permet pas d'écraser sa référence
                                     par du texte libre (cela romprait le lien vers le document). Envoyer une
                                     nouvelle photo/fichier ci-dessus pour le remplacer. --}}
                                <input type="hidden" name="nom_fichier" value="{{ $p->nom_fichier }}">
                            @endif
                            <div class="form-group">
                                <label>Commentaire</label>
                                <textarea name="observations" class="form-control" rows="2" maxlength="500" placeholder="Observation éventuelle (ex: photocopie non lisible)">{{ $p->commentaire }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-info">Enregistrer</button>
                        </div>
                    </form>
                </div></div>
            </div>
            @endforeach
        @endif

        {{-- ── TAB DEBLOCAGE ───────────────────────────────────── --}}
        @if($demande->deblocages->count())
        <div class="tab-pane" id="tab_deblocage">
            @foreach($demande->deblocages as $d)
            @php
                $operateurNom = optional($d->operateur)->nom_complet;
                if (!$operateurNom) {
                    $operateurNom = trim((optional($d->operateur)->prenom ?? '') . ' ' . (optional($d->operateur)->nom ?? ''));
                }
                if (!$operateurNom) {
                    $operateurNom = optional($d->operateur)->matricule ?? $d->agent_matricule ?? 'Système';
                }
            @endphp
            <table class="table table-sm table-bordered" style="max-width:600px">
                <tr><th>Montant débloqué</th><td><strong>{{ number_format($d->montant_debloque,2,',',' ') }} {{ $demande->devise }}</strong></td></tr>
                <tr><th>Date déblocage</th><td>{{ optional($d->date_deblocage)->format('d/m/Y') }}</td></tr>
                <tr><th>Date 1er remboursement</th><td>{{ optional($d->date_premier_remboursement)->format('d/m/Y') }}</td></tr>
                <tr><th>Compte débit</th><td>{{ $d->compte_debit_id ?? '–' }}</td></tr>
                <tr><th>Compte crédit client</th><td>{{ optional($d->compteCredit)->code_compte ?? '–' }}</td></tr>
                <tr><th>Opéré par</th><td>{{ $operateurNom }}</td></tr>
            </table>

            <div class="card card-outline card-info mb-3" style="max-width:600px">
                <div class="card-header py-2">
                    <h6 class="mb-0"><i class="fas fa-receipt mr-1"></i>Journal déblocage</h6>
                </div>
                <div class="card-body p-2">
                    <div class="small mb-1">
                        <strong>Référence déblocage :</strong>
                            {{ $d->reference_transaction ?? '–' }}
                    </div>
                    <div class="small mb-1">
                            <strong>Référence transfert 20% RMB → GTC :</strong>
                        {{ $d->numero_ordre ?? '–' }}
                    </div>
                    <div class="small mb-1">
                        <strong>Date/heure opération :</strong>
                        {{ optional($d->debloque_le)->format('d/m/Y H:i') ?? optional($d->date_deblocage)->format('d/m/Y') ?? '–' }}
                    </div>
                    <div class="small mb-0">
                        <strong>Agent opérateur :</strong> {{ $operateurNom }}
                    </div>
                    <div class="small mt-2">
                        <strong>Impression / réimpression :</strong>
                        @if($d->reference_transaction && optional($d->compteCredit)->code_compte)
                            <a href="{{ route('comptes.historique', optional($d->compteCredit)->code_compte) }}" target="_blank" rel="noopener">voir l'historique du compte et réimprimer les bordereaux</a>
                        @else
                            <span class="text-muted">Références indisponibles</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── TAB ECHEANCIER ─────────────────────────────────── --}}
        @if($demande->echeancier)
        <div class="tab-pane" id="tab_echeancier">
            @php $ech = $demande->echeancier @endphp
            <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                <div>
                    <strong>Montant total :</strong> {{ number_format($ech->montant_total, 2, ',', ' ') }} {{ $demande->devise }}&nbsp;&nbsp;
                    <strong>Total intérêts :</strong> {{ number_format($ech->total_interets, 2, ',', ' ') }} {{ $demande->devise }}&nbsp;&nbsp;
                    <strong>Commission totale :</strong> {{ number_format($ech->total_commission ?? 0, 2, ',', ' ') }} {{ $demande->devise }}
                </div>
                <a href="{{ route('credit.pdf.echeancier', $demande) }}" target="_blank" class="btn btn-sm btn-outline-danger mr-1">
                    <i class="fas fa-calendar-alt mr-1"></i>Échéancier PDF
                </a>
                <a href="{{ route('credit.pdf.releve', $demande) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-invoice mr-1"></i>Relevé de compte
                </a>
            </div>
            <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover small">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th><th>Date</th><th>Cap. restant déb.</th>
                        <th>Capital</th><th>Intérêt</th><th>Commission</th><th>Total</th>
                        <th>Reste dû</th>
                        <th>Cap. restant fin</th><th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                 @foreach($ech->echeances as $e)
                 <tr>
                     <td>{{ $e->numero_echeance }}</td>
                     <td>{{ optional($e->date_echeance)->format('d/m/Y') }}</td>
                     <td class="text-right">{{ number_format($e->capital_restant_debut, 2, ',', ' ') }}</td>
                     <td class="text-right">{{ number_format($e->montant_capital, 2, ',', ' ') }}</td>
                     <td class="text-right text-danger">{{ number_format($e->montant_interet, 2, ',', ' ') }}</td>
                     <td class="text-right text-info">{{ number_format($e->montant_commission ?? 0, 2, ',', ' ') }}</td>
                     <td class="text-right font-weight-bold">{{ number_format($e->montant_total, 2, ',', ' ') }}</td>
                     @php
                         $montantRestantDuLigne = max(0, (float)$e->total_echeance - (float)$e->montant_paye);
                         $dateEchStr = optional($e->date_echeance)->toDateString();
                         $ligneEnRetardReel = in_array($e->statut, ['EN_ATTENTE', 'PARTIELLEMENT_PAYE'])
                             && $dateEchStr && $dateEchStr < \Carbon\Carbon::today()->toDateString();
                     @endphp
                     <td class="text-right font-weight-bold {{ $montantRestantDuLigne > 0.01 ? 'text-danger' : 'text-success' }}">
                         {{ number_format($montantRestantDuLigne, 2, ',', ' ') }}
                     </td>
                     <td class="text-right">{{ number_format($e->capital_restant_fin, 2, ',', ' ') }}</td>
                      <td class="text-center">
                          @php
                              // Badge UNIQUE et sans ambiguïté (cf. même logique que
                              // credit/remboursement.blade.php) : le montant exact est
                              // déjà dans "Reste dû", inutile d'empiler 2 badges.
                              if ($e->statut === 'PAYE') {
                                  $lbl = 'PAYE'; $badgeClass = 'success';
                              } elseif ($ligneEnRetardReel) {
                                  $lbl = 'EN RETARD'; $badgeClass = 'danger';
                              } elseif ($e->statut === 'PARTIELLEMENT_PAYE') {
                                  $lbl = 'PARTIELLEMENT'; $badgeClass = 'info';
                              } else {
                                  $lbl = 'EN ATTENTE'; $badgeClass = 'secondary';
                              }

                              $montantRestantDu = $montantRestantDuLigne;
                              // Bouton éclair : règlement automatique depuis le solde RMB déjà déposé
                              // par le client, sans montant à saisir ni guichet requis (aucun argent
                              // liquide n'est encaissé). Disponible pour toute échéance non soldée
                              // (EN_ATTENTE, EN_RETARD, PARTIELLEMENT_PAYE), pas seulement en retard.
                              $echeanceEligibleReglementAuto = in_array($e->statut, ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE']);
                              $peutRemboursement = in_array('EBEN-PER10', $userPermCodes ?? []) || in_array('EBEN-PER111', $userPermCodes ?? []);
                              $peutReglerAuto = $peutRemboursement && $echeanceEligibleReglementAuto && ($soldeRmb >= $montantRestantDu) && $montantRestantDu > 0;
                          @endphp
                          <span class="badge badge-{{ $badgeClass }}">{{ $lbl }}</span>

                          @if($peutReglerAuto)
                              <form id="form-reglement-auto-{{ $e->id }}" method="POST" action="{{ route('credit.reglement.auto.echeance', $dossier) }}" style="display:inline;">
                                  @csrf
                                  <input type="hidden" name="echeance_id" value="{{ $e->id }}">
                                  <button type="button" class="btn btn-sm btn-outline-warning ml-1" title="Régler automatiquement depuis le solde RMB" onclick="confirmReglementAuto({{ $e->id }}, {{ number_format($montantRestantDu, 2, '.', '') }}, '{{ $demande->devise }}')">
                                      <i class="fas fa-bolt"></i>
                                  </button>
                              </form>
                          @endif
                      </td>
                 </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
        @endif

        {{-- ── TAB REMBOURSEMENTS ──────────────────────────────── --}}
        @if($demande->remboursements->count())
        <div class="tab-pane" id="tab_remboursements">
            <table class="table table-sm table-hover table-bordered">
                <thead><tr>
                    <th>Date paiement</th><th>Montant reçu</th>
                    <th>Capital payé</th><th>Intérêt payé</th>
                    <th>Mode paiement</th><th>Réf.</th>
                    <th>Saisi par</th>
                </tr></thead>
                <tbody>
                @foreach($demande->remboursements->sortByDesc('date_paiement') as $r)
                <tr>
                    <td>{{ optional($r->date_paiement)->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($r->montant_recu, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($r->montant_capital_paye, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($r->montant_interet_paye, 2, ',', ' ') }}</td>
                    <td>{{ $r->mode_paiement ?? '–' }}</td>
                    <td><small>{{ $r->reference_paiement ?? '–' }}</small></td>
                    <td><small>{{ optional($r->caissier)->nom_complet ?? '–' }}</small></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── TAB AUDIT ──────────────────────────────────────── --}}
        @if($canViewAudit ?? false)
        <div class="tab-pane" id="tab_audit">
            <table class="table table-sm small">
                <thead><tr><th>Date/Heure</th><th>Action</th><th>Utilisateur</th><th>Commentaire</th></tr></thead>
                <tbody>
                @forelse($demande->audits->sortByDesc('created_at') as $a)
                <tr>
                    <td class="text-nowrap">{{ $a->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="badge badge-secondary">{{ $a->labelAction() }}</span></td>
                    <td>{{ optional($a->utilisateur)->nom_complet ?? 'Système' }}</td>
                    <td class="text-muted">{{ $a->commentaire ?? '–' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">Aucune entrée dans le journal.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @endif

    </div>{{-- /.tab-content --}}
    </div>
</div>

</div>
</section>

{{-- ── Modals --}}
@if(in_array($demande->statut, ['BROUILLON','SOUMIS','EN_ANALYSE','EN_VALIDATION']) && in_array('EBEN-PER66', $userPermCodes ?? []))
<div class="modal fade" id="modalAnnuler" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('credit.annuler', $demande) }}">@csrf
        <div class="modal-header bg-danger text-white"><h5 class="modal-title">Annuler le dossier</h5></div>
        <div class="modal-body">
            <div class="form-group">
                <label>Motif d'annulation <span class="text-danger">*</span></label>
                <textarea name="motif" class="form-control" rows="3" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
        </div>
        </form>
    </div></div>
</div>
@endif

@if(in_array('EBEN-PER67', $userPermCodes ?? []))
<div class="modal fade" id="modalSuspendre" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('credit.suspendre', $demande) }}">@csrf
        <div class="modal-header bg-warning"><h5 class="modal-title">Suspendre le dossier</h5></div>
        <div class="modal-body">
            <div class="form-group">
                <label>Motif de suspension <span class="text-danger">*</span></label>
                <textarea name="motif" class="form-control" rows="3" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            <button type="submit" class="btn btn-warning">Confirmer la suspension</button>
        </div>
        </form>
    </div></div>
</div>
@endif

@if(!in_array($demande->statut, ['ANNULE','SUSPECT','SOLDE']) && in_array('EBEN-PER68', $userPermCodes ?? []))
<div class="modal fade" id="modalSignalerSuspect" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('credit.signaler_suspect', $demande) }}">@csrf
        <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Signaler ce dossier comme suspect</h5></div>
        <div class="modal-body">
            <p class="text-muted small">Le dossier passera au statut <strong>SUSPECT</strong> et sera bloqué jusqu'à levée de la suspicion (permission EBEN-PER69).</p>
            <div class="form-group">
                <label>Motif du signalement <span class="text-danger">*</span></label>
                <textarea name="motif" class="form-control" rows="3" required placeholder="Ex: incohérence sur les justificatifs, indice de fraude..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            <button type="submit" class="btn btn-danger">Confirmer le signalement</button>
        </div>
        </form>
    </div></div>
</div>
@endif

@if($demande->statut === 'SOUMIS' && !$demande->agent_analyse_matricule && in_array('EBEN-PER61', $userPermCodes ?? []))
<div class="modal fade" id="modalAffecterAnalyse" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('credit.affecter_analyse', $demande) }}">@csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Affecter un agent de crédit</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Agent de crédit <span class="text-danger">*</span></label>
                    <select name="agent_analyse_matricule" id="selectAgentAnalyse" class="form-control select2-search" required>
                        <option value="">-- Sélectionner un agent --</option>
                        @foreach(($assignableAgents ?? collect()) as $a)
                            <option value="{{ $a->matricule }}"
                                data-portefeuilles='@json($a->portefeuilles_actifs ?? [])'
                                data-default-portefeuille="{{ $a->portefeuille_actif_unique_id ?? '' }}"
                                {{ $demande->agent_analyse_matricule === $a->matricule ? 'selected' : '' }}>
                                {{ trim(($a->nom ?? '').' '.($a->postnom ?? '').' '.($a->prenom ?? '')) }} ({{ $a->matricule }})
                                @if(!empty($a->portefeuille_actif_resume))
                                    — {{ $a->portefeuille_actif_resume }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Portefeuille du dossier <span class="text-danger">*</span></label>
                    <div id="labelPortefeuilleAnalyse" class="form-control bg-light" style="cursor:not-allowed;">
                        <span class="text-muted">-- Sélectionner d'abord un agent --</span>
                    </div>
                    <input type="hidden" name="portefeuille_id" id="inputPortefeuilleAnalyse" value="">
                    <small id="hintPortefeuilleAnalyse" class="text-muted d-block mt-1">
                        <i class="fas fa-info-circle mr-1"></i>Le dossier sera rattaché au portefeuille actif de l'agent sélectionné.
                    </small>
                </div>
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Tapez pour chercher un agent</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <button type="submit" class="btn btn-warning">Enregistrer</button>
            </div>
        </form>
    </div></div>
</div>
@endif

@if($demande->statut === 'BROUILLON' && (in_array('EBEN-PER56', $userPermCodes ?? []) || in_array('EBEN-PER53', $userPermCodes ?? [])))
<div class="modal fade" id="modalSoumettre" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('credit.soumettre', $demande) }}">@csrf
        <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-paper-plane mr-2"></i>Soumettre le dossier</h5></div>
        <div class="modal-body">
            <p>Êtes-vous certain de vouloir <strong>soumettre ce dossier</strong> ? Une fois soumis, il sera envoyé aux opérationnels pour analyse.</p>
            <p class="mb-0 text-muted small"><i class="fas fa-info-circle mr-1"></i>Cette action ne pourra pas être annulée directement.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Confirmer la soumission</button>
        </div>
        </form>
    </div></div>
</div>
@endif
@endsection

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .select2-container .select2-selection--single { height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; padding-left: 12px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 37px !important; right: 1px; }
</style>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // ── Label dynamique pour les inputs "fichier" des pièces justificatives ──
        $(document).on('change', '.custom-file-input', function () {
            const fileName = this.files.length ? this.files[0].name : 'Prendre une photo ou choisir un fichier...';
            $(this).next('.custom-file-label').html(fileName);
        });

        const $agentSelect = $('#selectAgentAnalyse');
        const $pfLabel = $('#labelPortefeuilleAnalyse');
        const $pfInput = $('#inputPortefeuilleAnalyse');
        const $pfHint = $('#hintPortefeuilleAnalyse');

        const renderPortefeuilles = function() {
            if (!$agentSelect.length || !$pfLabel.length) {
                return;
            }

            const selected = $agentSelect.find('option:selected');
            const payload = selected.attr('data-portefeuilles');
            let portefeuilles = [];

            if (payload) {
                try {
                    portefeuilles = JSON.parse(payload);
                } catch (e) {
                    portefeuilles = [];
                }
            }

            $pfInput.val('');

            if (!selected.val()) {
                $pfLabel.html('<span class="text-muted">-- Sélectionner d\'abord un agent --</span>');
                $pfHint.text('Le dossier sera rattaché au portefeuille actif de l\'agent sélectionné.');
                return;
            }

            if (!Array.isArray(portefeuilles) || portefeuilles.length === 0) {
                $pfLabel.html('<span class="text-danger">-- Aucun portefeuille actif trouvé --</span>');
                $pfHint.text('Cet agent n\'a pas de portefeuille actif.');
                return;
            }

            const noms = portefeuilles.map(pf => `${pf.nom_portefeuille} (#${pf.id})`).join(', ');
            $pfLabel.html(`<span>${noms}</span>`);

            const defPf = selected.attr('data-default-portefeuille') || (portefeuilles.length === 1 ? portefeuilles[0].id : '');
            $pfInput.val(defPf || portefeuilles[0].id);

            if (portefeuilles.length > 1) {
                $pfHint.text('Cet agent a plusieurs portefeuilles actifs. Le dossier sera rattaché à : ' + noms + '.');
            } else {
                $pfHint.text('Portefeuille actif de l\'agent, rattaché automatiquement au dossier.');
            }
        };

        $agentSelect.select2({
            placeholder: "Chercher par nom ou matricule...",
            allowClear: true,
            language: "fr",
            width: '100%'
        });

        $agentSelect.on('change', renderPortefeuilles);

        $('#modalAffecterAnalyse').on('show.bs.modal', function() {
            renderPortefeuilles();
            setTimeout(() => {
                $agentSelect.select2('open');
            }, 100);
        });

        // Activer automatiquement l'onglet ciblé par le hash (ex: #tab_echeancier après règlement auto)
        if (window.location.hash) {
            var hash = window.location.hash;
            var $tab = $('.nav-tabs a[href="' + hash + '"]');
            if ($tab.length) {
                $tab.tab('show');
            }
        }
    });

    // Fonction de confirmation standardisée pour le règlement automatique
    window.confirmReglementAuto = function(echeanceId, montant, devise) {
        var message = 'Confirmer le prélèvement automatique de <strong>' + montant + ' ' + devise + '</strong> sur le compte RMB pour régler cette échéance ?';
        showUniversalConfirm(message, function() {
            document.getElementById('form-reglement-auto-' + echeanceId).submit();
        }, { showWarning: true });
    };
</script>
@endpush

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
@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    </div>
@endif

{{-- ── Header --}}
<div class="card card-outline card-success mb-3">
    <div class="card-header">
        <h4 class="card-title mb-0">
            <i class="fas fa-file-alt mr-2"></i>
            {{ $demande->numero_dossier }}
            &nbsp; {!! $demande->badgeStatut() !!}
        </h4>
        <div class="card-tools d-flex gap-1">
            {{-- Action buttons --}}
            @if($demande->statut === 'BROUILLON')
                @if(in_array('EBEN-PER56', $userPermCodes ?? []))
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalSoumettre">
                    <i class="fas fa-paper-plane mr-1"></i>Soumettre
                </button>
                @endif
            @endif

            @if($demande->statut === 'SOUMIS')
                @if(in_array('EBEN-PER61', $userPermCodes ?? []))
                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalAffecterAnalyse">
                    <i class="fas fa-user-check mr-1"></i>Affecter agent crédit
                </button>
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
                @if(in_array('EBEN-PER65', $userPermCodes ?? []))
                <a href="{{ route('credit.remboursement', $demande) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-money-bill-wave mr-1"></i>Remboursement
                </a>
                @endif
            @endif

            @if(in_array($demande->statut, ['BROUILLON','SOUMIS','EN_ANALYSE','EN_VALIDATION']) && in_array('EBEN-PER54', $userPermCodes ?? []))
            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalAnnuler">
                <i class="fas fa-times-circle mr-1"></i>Annuler
            </button>
            @endif

            @if(in_array($demande->statut, ['DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','EN_VALIDATION','EN_ANALYSE','SOUMIS']) && in_array('EBEN-PER67', $userPermCodes ?? []))
            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalSuspendre">
                <i class="fas fa-pause mr-1"></i>Suspendre
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
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Montant demandé</th>
                        <td><strong>{{ number_format($demande->montant_demande, 2, ',', ' ') }} {{ $demande->devise }}</strong></td></tr>
                    <tr><th>Montant accordé</th>
                        <td>{{ $demande->montant_accorde ? number_format($demande->montant_accorde, 2, ',', ' ').' '.$demande->devise : '–' }}</td></tr>
                    <tr><th>Durée</th><td>{{ $demande->duree_mois }} mois</td></tr>
                    <tr><th>Taux mensuel</th><td>{{ $demande->taux_interet_mensuel }} %</td></tr>
                    <tr><th>Frais de dossier</th>
                        <td>{{ $demande->frais_dossier ? number_format($demande->frais_dossier, 2, ',', ' ').' '.$demande->devise : '–' }}</td></tr>
                    <tr><th>Date soumission</th><td>{{ optional($demande->date_soumission)->format('d/m/Y') ?? '–' }}</td></tr>
                    <tr><th>Date déblocage</th><td>{{ optional($demande->date_deblocage)->format('d/m/Y') ?? '–' }}</td></tr>
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
                        <tr><th>Revenu mensuel net</th><td>{{ $a->revenu_mensuel_net ? number_format($a->revenu_mensuel_net,2,',',' ') : '–' }}</td></tr>
                        <tr><th>Taux endettement</th><td>{{ $a->taux_endettement ?? '–' }} %</td></tr>
                        <tr><th>Capacité remboursement</th><td>{{ $a->capacite_remboursement ? number_format($a->capacite_remboursement,2,',',' ') : '–' }}</td></tr>
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
                $types = ['AGENT_CREDIT','CHARGE_OPERATIONS','CONTROLEUR','GERANT'];
                $labels = ['AGENT_CREDIT'=>'Agent crédit','CHARGE_OPERATIONS'=>'Chargé opérations','CONTROLEUR'=>'Contrôleur','GERANT'=>'Gérant'];
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
                                    <small><strong>Par :</strong> {{ optional($v->validateur)->nom_complet ?? '–' }}</small><br>
                                    <small><strong>Le :</strong> {{ optional($v->date_validation)->format('d/m/Y H:i') ?? '–' }}</small><br>
                                @endif
                                @if($v->montant_propose)
                                    <small><strong>Montant proposé :</strong><br>{{ number_format($v->montant_propose,2,',',' ') }}</small><br>
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
        <div class="tab-pane" id="tab_pieces">
        @if($demande->pieces->count())
            <table class="table table-sm table-hover">
                <thead><tr>
                    <th>Type de pièce</th><th>Référence</th>
                    <th>Statut</th><th>Commentaire</th>
                </tr></thead>
                <tbody>
                @foreach($demande->pieces as $p)
                <tr>
                    <td>{{ $p->type_piece }}</td>
                    <td>{{ $p->reference ?? '–' }}</td>
                    <td>
                        @if($p->fourni)
                            <span class="badge badge-success">Fourni</span>
                        @else
                            <span class="badge badge-warning">Manquant</span>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $p->commentaire ?? '–' }}</small></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted text-center py-3">Aucune pièce enregistrée.</p>
        @endif
        </div>

        {{-- ── TAB DEBLOCAGE ───────────────────────────────────── --}}
        @if($demande->deblocages->count())
        <div class="tab-pane" id="tab_deblocage">
            @foreach($demande->deblocages as $d)
            <table class="table table-sm table-bordered" style="max-width:600px">
                <tr><th>Montant débloqué</th><td><strong>{{ number_format($d->montant_debloque,2,',',' ') }} {{ $demande->devise }}</strong></td></tr>
                <tr><th>Date déblocage</th><td>{{ optional($d->date_deblocage)->format('d/m/Y') }}</td></tr>
                <tr><th>Date 1er remboursement</th><td>{{ optional($d->date_premier_remboursement)->format('d/m/Y') }}</td></tr>
                <tr><th>Compte débit</th><td>{{ optional($d->compteDebit)->code_compte ?? '–' }}</td></tr>
                <tr><th>Compte crédit client</th><td>{{ optional($d->compteCredit)->code_compte ?? '–' }}</td></tr>
                <tr><th>Opéré par</th><td>{{ optional($d->operateur)->nom_complet ?? '–' }}</td></tr>
                @if($d->reference_comptable)
                <tr><th>Réf. comptable</th><td>{{ $d->reference_comptable }}</td></tr>
                @endif
            </table>
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
                    <strong>Total intérêts :</strong> {{ number_format($ech->total_interets, 2, ',', ' ') }} {{ $demande->devise }}
                </div>
                <a href="{{ route('credit.pdf.echeancier', $demande) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-print mr-1"></i>Imprimer PDF
                </a>
            </div>
            <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover small">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th><th>Date</th><th>Cap. restant déb.</th>
                        <th>Capital</th><th>Intérêt</th><th>Total</th>
                        <th>Cap. restant fin</th><th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($ech->echeances as $e)
                <tr class="{{ $e->statut === 'EN_RETARD' ? 'table-danger' : ($e->statut === 'PAYE' ? 'table-success' : '') }}">
                    <td>{{ $e->numero_echeance }}</td>
                    <td>{{ optional($e->date_echeance)->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($e->capital_restant_debut, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($e->montant_capital, 2, ',', ' ') }}</td>
                    <td class="text-right text-danger">{{ number_format($e->montant_interet, 2, ',', ' ') }}</td>
                    <td class="text-right font-weight-bold">{{ number_format($e->montant_total, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($e->capital_restant_fin, 2, ',', ' ') }}</td>
                    <td>
                        @php
                            $sc = ['EN_ATTENTE'=>'secondary','PAYE'=>'success','PARTIELLEMENT_PAYE'=>'info','EN_RETARD'=>'danger'];
                            $lbl = str_replace('_',' ', $e->statut);
                        @endphp
                        <span class="badge badge-{{ $sc[$e->statut] ?? 'secondary' }}">{{ $lbl }}</span>
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

@if($demande->statut === 'SOUMIS' && in_array('EBEN-PER61', $userPermCodes ?? []))
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
                            <option value="{{ $a->matricule }}" {{ $demande->agent_analyse_matricule === $a->matricule ? 'selected' : '' }}>
                                {{ trim(($a->nom ?? '').' '.($a->postnom ?? '').' '.($a->prenom ?? '')) }} ({{ $a->matricule }})
                            </option>
                        @endforeach
                    </select>
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

@if($demande->statut === 'BROUILLON' && in_array('EBEN-PER56', $userPermCodes ?? []))
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
        $('#selectAgentAnalyse').select2({
            placeholder: "Chercher par nom ou matricule...",
            allowClear: true,
            language: "fr",
            width: '100%'
        });
        
        // Déboucher Select2 quand le modal s'ouvre
        $('#modalAffecterAnalyse').on('show.bs.modal', function() {
            setTimeout(() => {
                $('#selectAgentAnalyse').select2('open');
            }, 100);
        });
    });
</script>
@endpush

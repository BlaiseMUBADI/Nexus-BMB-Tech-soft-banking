@extends('layouts.app')

@section('page_title', 'Validation – ' . $demande->numero_dossier)
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Validation du dossier')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0"><i class="fas fa-stamp mr-2 text-warning"></i>Validation multi-niveaux</h5>
    <div>
        <a href="{{ route('credit.show', $demande) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye mr-1"></i>Voir dossier
        </a>
        <a href="{{ route('credit.index') }}" class="btn btn-sm btn-outline-secondary ml-1">
            <i class="fas fa-list mr-1"></i>Liste crédits
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        {{ session('error') }}
    </div>
@endif

@php
    $viewErrors = session('errors');
@endphp
@if($viewErrors instanceof \Illuminate\Support\ViewErrorBag && $viewErrors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <strong>Impossible d'enregistrer la validation :</strong>
        <ul class="mb-0 mt-2 pl-3">
            @foreach($viewErrors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ── Mini-résumé --}}
<div class="card card-outline card-light mb-3">
    <div class="card-body py-2 px-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 col-lg-3 mb-2 mb-lg-0">
                <div><strong>Dossier :</strong> {{ $demande->numero_dossier }}</div>
                <div class="mt-1">{!! $demande->badgeStatut() !!}</div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-2 mb-lg-0">
                <strong>Client :</strong><br>
                {{ optional($demande->client)->nom }} {{ optional($demande->client)->prenom }}
            </div>
            <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                <strong>Montant demandé :</strong><br>
                {{ number_format($demande->montant_demande,2,',',' ') }} {{ $demande->devise }}
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-2 mb-lg-0">
                <strong>Conditions retenues :</strong><br>
                {{ number_format($conditionsRetenues['montant'],2,',',' ') }} {{ $demande->devise }} / {{ $conditionsRetenues['duree_mois'] }} mois
            </div>
            <div class="col-12 col-lg-1 text-lg-right">
                <div class="mb-2 mb-lg-1"><strong>Taux :</strong> {{ number_format((float) $demande->taux_interet_mensuel, 1, '.', '') }} %/mois</div>
                <a href="{{ route('credit.show', $demande) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-eye mr-1"></i>Détail complet
                </a>
            </div>
        </div>
    </div>
</div>

@if($previewEcheancier)
<div class="card card-outline card-secondary mb-3 collapsed-card">
    <div class="card-header py-2">
        <h6 class="mb-0"><i class="fas fa-table mr-2"></i>Aperçu de l'échéancier retenu</h6>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Replier / déplier">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="px-3 py-2 small bg-light border-bottom">
            <div class="row">
                <div class="col-md-3 mb-2 mb-md-0">
                    <strong>Montant retenu :</strong><br>
                    {{ number_format($conditionsRetenues['montant'], 2, ',', ' ') }} {{ $demande->devise }}
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <strong>Durée retenue :</strong><br>
                    {{ $conditionsRetenues['duree_mois'] }} mois
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <strong>Total intérêts :</strong><br>
                    {{ number_format($previewEcheancier['total_interets'], 2, ',', ' ') }} {{ $demande->devise }}
                </div>
                <div class="col-md-4">
                    <strong>Total général :</strong><br>
                    {{ number_format($previewEcheancier['total_general'], 2, ',', ' ') }} {{ $demande->devise }}
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered mb-0 small">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Capital début</th>
                        <th>Capital</th>
                        <th>Intérêt</th>
                        <th>Total</th>
                        <th>Capital fin</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($previewEcheancier['echeances'] as $ligne)
                    <tr>
                        <td>{{ $ligne['numero'] }}</td>
                        <td>{{ $ligne['date']->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($ligne['capital_restant_debut'], 2, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($ligne['capital'], 2, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($ligne['interet'], 2, ',', ' ') }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($ligne['total'], 2, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($ligne['capital_restant_fin'], 2, ',', ' ') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ── 4 blocs de validation --}}
@php
    $connectedUser = auth()->user();
    $connectedAgent = $connectedUser?->agent;
    $signatureCompte = $connectedAgent?->matricule ?: ('USR-' . ($connectedUser->id ?? 'INCONNU'));
    $signatureNom = trim(($connectedAgent?->nom ?? '') . ' ' . ($connectedAgent?->postnom ?? '') . ' ' . ($connectedAgent?->prenom ?? ''));
    $types = [
        'AGENT_CREDIT'       => ['label'=>'Agent crédit',       'perm'=>'EBEN-PER60', 'color'=>'primary'],
        'CONTROLEUR'         => ['label'=>'Contrôleur',         'perm'=>'EBEN-PER62', 'color'=>'warning'],
        'CHARGE_OPERATIONS'  => ['label'=>'Chargé opérations',  'perm'=>'EBEN-PER61', 'color'=>'info'],
        'GERANT'             => ['label'=>'Gérant',             'perm'=>'EBEN-PER63', 'color'=>'success'],
    ];
    $vMap  = $demande->validations->keyBy('type_validateur');
    $dcol  = ['APPROUVE'=>'success','APPROUVE_AVEC_RESERVE'=>'warning','REJETE'=>'danger','EN_ATTENTE'=>'secondary'];
@endphp

<div class="row">
@foreach($types as $type => $cfg)
@php
    $v       = $vMap[$type] ?? null;
    $decision = $v ? $v->decision : 'NON_COMMENCÉ';
    $isOpen  = $v && $v->etape_precedente_ok && $v->decision === 'EN_ATTENTE';
    $canEdit = $isOpen && in_array($cfg['perm'], $userPermCodes ?? []);
    $bcolor  = $dcol[$decision] ?? 'light';
@endphp
<div class="col-md-6 col-xl-3 mb-3">
    <div class="card card-outline card-{{ $cfg['color'] }} h-100">
        <div class="card-header py-2 bg-{{ $cfg['color'] }} text-white">
            <h6 class="mb-0">
                <i class="fas fa-user-check mr-1"></i>{{ $cfg['label'] }}
            </h6>
        </div>
        <div class="card-body">

            {{-- Statut actuel --}}
            <div class="mb-2 text-center">
                <span class="badge badge-{{ $bcolor }} badge-lg px-3 py-2">
                    {{ str_replace('_',' ', $decision) }}
                </span>
            </div>

            @if($v && $v->decision !== 'EN_ATTENTE')
                {{-- Décision enregistrée --}}
                <table class="table table-xs table-borderless small mb-2">
                    <tr><th>Par</th><td>{{ optional($v->validateur)->nom_complet ?? $v->nom_signataire ?? $v->signature_agent ?? $v->validateur_matricule ?? '–' }}</td></tr>
                    <tr><th>Le</th><td>{{ optional($v->date_validation)->format('d/m/Y H:i') }}</td></tr>
                    <tr><th>Signature</th><td><code>{{ $v->signature_agent ?? $v->validateur_matricule ?? '—' }}</code></td></tr>
                    @if($v->montant_propose)
                    <tr><th>Montant</th><td>{{ number_format($v->montant_propose,2,',',' ') }} {{ $demande->devise }}</td></tr>
                    @endif
                    @if($v->duree_mois_validee)
                    <tr><th>Durée</th><td>{{ $v->duree_mois_validee }} mois</td></tr>
                    @endif
                </table>
                @if($v->commentaire)
                    <p class="small text-muted mb-1"><strong>Commentaire :</strong> {{ $v->commentaire }}</p>
                @endif
                @if($v->conditions)
                    <p class="small mb-0"><strong>Conditions :</strong> {{ $v->conditions }}</p>
                @endif

            @elseif(!$v || !$v->etape_precedente_ok)
                {{-- Étape précédente pas encore validée --}}
                <div class="text-center text-muted small py-2">
                    <i class="fas fa-lock fa-2x mb-2 d-block"></i>
                    En attente de l'étape précédente
                </div>

            @elseif($isOpen && !$canEdit)
                {{-- L'étape est ouverte mais l'utilisateur n'a pas la permission --}}
                <div class="text-center text-warning small py-2">
                    <i class="fas fa-hourglass-half fa-2x mb-2 d-block"></i>
                    En attente de votre intervenant
                </div>

            @else
                {{-- !! Formulaire de validation !! --}}
                <form method="POST" action="{{ route('credit.validation.store', $demande) }}">
                @csrf
                <input type="hidden" name="type_validateur" value="{{ $type }}">

                <div class="form-group">
                    <label class="small">Décision <span class="text-danger">*</span></label>
                    <select name="decision" class="form-control form-control-sm" required>
                        <option value="">-- Choisir --</option>
                        <option value="APPROUVE">APPROUVÉ</option>
                        <option value="APPROUVE_AVEC_RESERVE">APPROUVÉ AVEC RÉSERVE</option>
                        <option value="REJETE">REJETÉ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small">Montant validé ({{ $demande->devise }}) <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <input type="number" name="montant_valide" class="form-control form-control-sm"
                               step="0.01" min="0"
                               value="{{ old('montant_valide') }}"
                               placeholder="{{ number_format($conditionsRetenues['montant'],0,',',' ') }}">
                        <div class="input-group-append"><span class="input-group-text">{{ $demande->devise }}</span></div>
                    </div>
                    <small class="text-muted">Obligatoire pour <strong>Approuvé</strong> ou <strong>Approuvé avec réserve</strong>.</small>
                </div>

                @if($type === 'GERANT')
                <div class="form-group">
                    <label class="small">Durée validée (mois)</label>
                    <input type="number" name="duree_mois_validee" class="form-control form-control-sm"
                           min="1" max="360"
                           value="{{ old('duree_mois_validee', $conditionsRetenues['duree_mois']) }}"
                           placeholder="{{ $conditionsRetenues['duree_mois'] }}">
                    <small class="text-muted">Le gérant peut ajuster la durée; cette trace restera liée à sa décision.</small>
                </div>
                @endif

                <div class="form-group">
                    <label class="small">Commentaire du validateur <span class="text-danger">*</span></label>
                    <textarea name="observations" class="form-control form-control-sm" rows="2" required>{{ old('observations') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="small">Conditions particulières</label>
                    <textarea name="conditions" class="form-control form-control-sm" rows="2">{{ old('conditions') }}</textarea>
                </div>

                <div class="border rounded p-2 mb-2 bg-light">
                    <div class="small font-weight-bold mb-1">Signature électronique (compte agent)</div>
                    <div class="small">Compte : <code>{{ $signatureCompte }}</code></div>
                    <div class="small text-muted">Agent : {{ $signatureNom !== '' ? $signatureNom : 'Non renseigné' }}</div>
                    <div class="custom-control custom-checkbox mt-2">
                        <input class="custom-control-input" type="checkbox" id="signature_confirm_{{ $type }}" name="signature_confirm" value="1" required>
                        <label for="signature_confirm_{{ $type }}" class="custom-control-label small">
                            Je confirme signer cette validation avec mon compte agent
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-sm btn-{{ $cfg['color'] }} btn-block">
                    <i class="fas fa-stamp mr-1"></i>Enregistrer ma décision
                </button>
                </form>
            @endif

        </div>
    </div>
</div>
@endforeach
</div>

{{-- ── Note si toutes approuvées --}}
@if($demande->statut === 'PRET_A_DEBLOQUER')
<div class="alert alert-success">
    <i class="fas fa-check-double mr-2"></i>
    <strong>Toutes les validations sont approuvées.</strong> Le dossier est prêt au déblocage.
    @if(in_array('EBEN-PER64', $userPermCodes ?? []))
    <a href="{{ route('credit.deblocage', $demande) }}" class="btn btn-success btn-sm ml-3">
        <i class="fas fa-unlock mr-1"></i>Procéder au déblocage
    </a>
    @endif
</div>
@endif

</div>
</section>
@endsection

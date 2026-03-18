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

{{-- ── Mini-résumé --}}
<div class="alert alert-light border mb-3 d-flex flex-wrap gap-3 align-items-center">
    <span><strong>Dossier :</strong> {{ $demande->numero_dossier }}</span>
    <span>{!! $demande->badgeStatut() !!}</span>
    <span><strong>Client :</strong> {{ optional($demande->client)->nom }} {{ optional($demande->client)->prenom }}</span>
    <span><strong>Montant :</strong> {{ number_format($demande->montant_demande,2,',',' ') }} {{ $demande->devise }}</span>
    <span><strong>Durée :</strong> {{ $demande->duree_mois }} mois @ {{ $demande->taux_interet_mensuel }} %/mois</span>
    <a href="{{ route('credit.show', $demande) }}" class="btn btn-xs btn-outline-secondary ml-auto">
        <i class="fas fa-eye mr-1"></i>Détail complet
    </a>
</div>

{{-- ── 4 blocs de validation --}}
@php
    $types = [
        'AGENT_CREDIT'       => ['label'=>'Agent crédit',       'perm'=>'EBEN-PER60', 'color'=>'primary'],
        'CHARGE_OPERATIONS'  => ['label'=>'Chargé opérations',  'perm'=>'EBEN-PER61', 'color'=>'info'],
        'CONTROLEUR'         => ['label'=>'Contrôleur',         'perm'=>'EBEN-PER62', 'color'=>'warning'],
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
                    <tr><th>Par</th><td>{{ optional($v->validateur)->nom_complet ?? '–' }}</td></tr>
                    <tr><th>Le</th><td>{{ optional($v->date_validation)->format('d/m/Y H:i') }}</td></tr>
                    @if($v->montant_propose)
                    <tr><th>Montant</th><td>{{ number_format($v->montant_propose,2,',',' ') }}</td></tr>
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
                    <label class="small">Montant proposé</label>
                    <input type="number" name="montant_valide" class="form-control form-control-sm"
                           step="0.01" min="0"
                           value="{{ old('montant_valide') }}"
                           placeholder="{{ number_format($demande->montant_demande,0,',',' ') }}">
                </div>

                <div class="form-group">
                    <label class="small">Commentaire</label>
                    <textarea name="observations" class="form-control form-control-sm" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label class="small">Conditions particulières</label>
                    <textarea name="conditions" class="form-control form-control-sm" rows="2"></textarea>
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

@extends('layouts.app')

@section('page_title', 'Analyse – ' . $demande->numero_dossier)
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Analyse du dossier')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0"><i class="fas fa-search mr-2 text-info"></i>Analyse du dossier {{ $demande->numero_dossier }}</h5>
    <div>
        <a href="{{ route('credit.show', $demande) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye mr-1"></i>Voir dossier
        </a>
        <a href="{{ route('credit.index') }}" class="btn btn-sm btn-outline-secondary ml-1">
            <i class="fas fa-list mr-1"></i>Liste crédits
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        {{ session('error') }}
    </div>
@endif

<div class="row">

{{-- ── Résumé dossier ── --}}
<div class="col-md-4">
    <div class="card card-outline card-primary">
        <div class="card-header"><h6 class="mb-0"><i class="fas fa-file mr-1"></i>Dossier</h6></div>
        <div class="card-body p-2">
            <table class="table table-sm table-borderless small mb-0">
                <tr><th>N°</th><td>{{ $demande->numero_dossier }}</td></tr>
                <tr><th>Statut</th><td>{!! $demande->badgeStatut() !!}</td></tr>
                <tr><th>Client</th><td>
                    {{ optional($demande->client)->nom }} {{ optional($demande->client)->prenom }}<br>
                    <small class="text-muted">{{ $demande->client_matricule }}</small>
                </td></tr>
                <tr><th>Type</th><td>{{ $demande->type_credit }}</td></tr>
                <tr><th>Montant</th><td>
                    <strong>{{ number_format($demande->montant_demande, 2, ',', ' ') }} {{ $demande->devise }}</strong>
                </td></tr>
                <tr><th>Durée</th><td>{{ $demande->duree_mois }} mois</td></tr>
                <tr><th>Taux</th><td>{{ $demande->taux_interet_mensuel }} % / mois</td></tr>
            </table>
            <div class="mt-2 p-2 bg-light rounded">
                <strong class="small">Objet :</strong><br>
                <p class="small mb-0">{{ $demande->objet_credit }}</p>
            </div>
        </div>
    </div>

    <div class="card card-outline card-warning">
        <div class="card-header"><h6 class="mb-0"><i class="fas fa-file-alt mr-1"></i>Pièces requises</h6></div>
        <div class="card-body p-2">
            <table class="table table-xs small mb-0">
                @foreach($demande->pieces as $p)
                <tr>
                    <td>{{ $p->type_piece }}</td>
                    <td>
                        @if($p->fourni)
                            <span class="badge badge-success">OK</span>
                        @else
                            <span class="badge badge-danger">Manquant</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

{{-- ── Formulaire d'analyse ── --}}
<div class="col-md-8">
<div class="card card-outline card-info">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-search mr-2"></i>Analyse du dossier
        </h5>
    </div>
    <div class="card-body">
        @php
            $formErrors = [];
            if (isset($errors)) {
                if (is_object($errors) && method_exists($errors, 'all')) {
                    $formErrors = $errors->all();
                } elseif (is_array($errors)) {
                    $formErrors = $errors;
                }
            }
        @endphp
        @if(count($formErrors))
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($formErrors as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('credit.analyse.store', $demande) }}" id="formAnalyse">
        @csrf

        @php $a = $demande->analyse @endphp

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3">
            Situation économique du client
        </h6>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Historique / activité principale</label>
                <textarea name="historique_credit" class="form-control" rows="2">{{ old('historique_credit', optional($a)->historique_credit) }}</textarea>
            </div>
            <div class="form-group col-md-3">
                <label>Revenu mensuel net</label>
                <input type="number" name="revenu_mensuel_verifie" class="form-control" step="0.01" min="0"
                       value="{{ old('revenu_mensuel_verifie', optional($a)->revenu_mensuel_verifie) }}">
            </div>
            <div class="form-group col-md-3">
                <label>Taux d'endettement (%)</label>
                <div class="input-group">
                    <input type="number" name="ratio_endettement" class="form-control" step="0.01" min="0" max="100"
                           value="{{ old('ratio_endettement', optional($a)->ratio_endettement) }}">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Capacité de remboursement</label>
                <input type="number" name="capacite_remboursement" class="form-control" step="0.01" min="0"
                       value="{{ old('capacite_remboursement', optional($a)->capacite_remboursement) }}">
                <small class="text-muted">Montant max mensuel supportable</small>
            </div>
            <div class="form-group col-md-4">
                <label>Garanties évaluées</label>
                <textarea name="garanties_evaluees" class="form-control" rows="2">{{ old('garanties_evaluees', optional($a)->garanties_evaluees) }}</textarea>
            </div>
            <div class="form-group col-md-4">
                <label>Score de risque</label>
                <select name="score_risque" class="form-control">
                    <option value="">-- Sélectionner --</option>
                    @foreach(['FAIBLE','MOYEN','ELEVE','TRES_ELEVE'] as $v)
                    <option value="{{ $v }}" {{ old('score_risque', optional($a)->score_risque)==$v?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3 mt-2">
            Évaluation qualitative
        </h6>

        <div class="form-row">
            <div class="form-group col-md-12">
                <label>Notes qualitatives</label>
                <small class="text-muted d-block">Utilisez les observations ci-dessous pour détailler les points qualitatifs de votre analyse.</small>
            </div>
        </div>

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3 mt-2">
            Avis &amp; Recommandation
        </h6>

        <div class="form-group">
            <label>Recommandation <span class="text-danger">*</span></label>
            <select name="recommandation" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                @foreach(['FAVORABLE','FAVORABLE_AVEC_RESERVE','DEFAVORABLE'] as $v)
                <option value="{{ $v }}" {{ old('recommandation', optional($a)->recommandation)==$v?'selected':'' }}>
                    {{ str_replace('_',' ',$v) }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Montant proposé (si différent)</label>
                  <input type="number" name="montant_recommande" class="form-control" step="0.01" min="0"
                      value="{{ old('montant_recommande', optional($a)->montant_recommande) }}">
            </div>
            <div class="form-group col-md-6">
                  <label>État de l'analyse</label>
                  <input type="text" class="form-control" value="{{ optional($a)->statut ?? 'EN_COURS' }}" readonly>
            </div>
        </div>

        <div class="form-group">
            <label>Observations</label>
            <textarea name="observations" class="form-control" rows="3">{{ old('observations', optional($a)->observations) }}</textarea>
        </div>

        <input type="hidden" name="action" id="inp_action" value="SAUVER">

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-warning"
                    onclick="document.getElementById('inp_action').value='SAUVER'">
                <i class="fas fa-save mr-1"></i>Sauvegarder (en cours)
            </button>
            @if(in_array('EBEN-PER59', $userPermCodes ?? []))
            <button type="submit" class="btn btn-success ml-2"
                    onclick="document.getElementById('inp_action').value='COMPLETER'"
                    id="btnCompleter">
                <i class="fas fa-check mr-1"></i>Compléter et transmettre à la validation
            </button>
            @else
            <button type="button" class="btn btn-success ml-2" disabled title="Permission EBEN-PER59 requise">
                <i class="fas fa-lock mr-1"></i>Compléter et transmettre à la validation
            </button>
            @endif
            <a href="{{ route('credit.show', $demande) }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </a>
        </div>

        </form>
    </div>
</div>
</div>

</div>
</div>
</section>
@endsection

@section('scripts')
<script>
// Aucun script requis pour cette version du formulaire.
</script>
@endsection

@extends('layouts.app')

@section('page_title', 'Déblocage – ' . $demande->numero_dossier)
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Déblocage du crédit')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0"><i class="fas fa-unlock-alt mr-2 text-success"></i>Mise en place du crédit</h5>
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

<div class="row justify-content-center">
<div class="col-md-8">

{{-- ── Résumé dossier --}}
<div class="card card-outline card-success mb-3">
    <div class="card-header bg-gradient-success text-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-unlock mr-2"></i>Déblocage – {{ $demande->numero_dossier }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless small">
                    <tr><th>Client</th>
                        <td>{{ optional($demande->client)->nom }} {{ optional($demande->client)->prenom }}
                        <br><small class="text-muted">{{ $demande->client_matricule }}</small></td></tr>
                    <tr><th>Type crédit</th><td>{{ $demande->type_credit }}</td></tr>
                    <tr><th>Objet</th><td>{{ $demande->objet_credit }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless small">
                    <tr><th>Montant accordé</th>
                        <td><strong class="text-success">
                            {{ number_format($demande->montant_accorde ?? $demande->montant_demande, 2, ',', ' ') }}
                            {{ $demande->devise }}
                        </strong></td></tr>
                    <tr><th>Durée</th><td>{{ $demande->duree_mois }} mois</td></tr>
                    <tr><th>Taux mensuel</th><td>{{ $demande->taux_interet_mensuel }} %</td></tr>
                </table>
            </div>
        </div>

        {{-- Validations synthèse --}}
        <div class="border-top pt-2 mt-1">
            <small class="text-muted font-weight-bold">Validations :</small>
            <div class="d-flex flex-wrap gap-2 mt-1">
                @foreach($demande->validations as $val)
                @php $bdc=['APPROUVE'=>'success','APPROUVE_AVEC_RESERVE'=>'warning','REJETE'=>'danger','EN_ATTENTE'=>'secondary'] @endphp
                <span class="badge badge-{{ $bdc[$val->decision] ?? 'secondary' }}">
                    {{ str_replace('_',' ', $val->type_validateur) }}:
                    {{ str_replace('_',' ', $val->decision) }}
                </span>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Formulaire de déblocage --}}
<div class="card card-outline card-warning">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-cog mr-1"></i>Paramètres de déblocage</h6>
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

            $comptesDebitList = is_iterable($comptesDebit ?? null) ? $comptesDebit : [];
        @endphp
        @if(count($formErrors))
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($formErrors as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('credit.deblocage.store', $demande) }}" id="formDeblocage">
        @csrf

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Montant à débloquer <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="montant_debloque" class="form-control"
                           step="0.01" min="1" required
                           value="{{ old('montant_debloque', $demande->montant_accorde ?? $demande->montant_demande) }}">
                    <div class="input-group-append"><span class="input-group-text">{{ $demande->devise }}</span></div>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Date de déblocage <span class="text-danger">*</span></label>
                <input type="date" name="date_deblocage" class="form-control" required
                       value="{{ old('date_deblocage', date('Y-m-d')) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Date du 1er remboursement <span class="text-danger">*</span></label>
            <input type="date" name="date_premier_remboursement" class="form-control" required
                   value="{{ old('date_premier_remboursement') }}"
                   min="{{ date('Y-m-d', strtotime('+1 month')) }}">
            <small class="text-muted">Le mois suivant la mise en place est recommandé.</small>
        </div>

        <div class="alert alert-info py-2 small">
            <i class="fas fa-info-circle mr-1"></i>
            Le compte RMB du client sera recherché automatiquement dans la devise du dossier, puis créé si aucun compte adapté n'existe encore.
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Compte à débiter (ressources) <span class="text-danger">*</span></label>
                <select name="compte_debit_id" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    @foreach($comptesDebitList as $c)
                    @if(is_object($c))
                    <option value="{{ $c->code_compte }}" {{ old('compte_debit_id') == $c->code_compte ? 'selected' : '' }}>
                        {{ $c->code_compte }} – {{ $c->type }}
                        (Solde: {{ number_format($c->solde_reel, 0, ',', ' ') }} {{ $c->devise }})
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Frais de dossier</label>
                <div class="input-group">
                    <input type="number" name="frais_dossier" class="form-control"
                           step="0.01" min="0"
                           value="{{ old('frais_dossier', $demande->frais_dossier ?? 0) }}">
                    <div class="input-group-append"><span class="input-group-text">{{ $demande->devise }}</span></div>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Référence comptable</label>
                <input type="text" name="reference_comptable" class="form-control"
                       value="{{ old('reference_comptable') }}" maxlength="100">
            </div>
        </div>

        <div class="form-group">
            <label>Observations</label>
            <textarea name="observations" class="form-control" rows="2">{{ old('observations') }}</textarea>
        </div>

        <hr>

        <div class="callout callout-warning">
            <h6><i class="fas fa-exclamation-triangle mr-1"></i>Confirmation</h6>
            <p class="mb-1 small">En validant, vous confirmez débloquer les fonds et générer l'échéancier de remboursement. Cette opération est irréversible.</p>
            <div class="custom-control custom-checkbox mt-2">
                <input type="checkbox" class="custom-control-input" id="chkConfirm" required>
                <label class="custom-control-label" for="chkConfirm">Je confirme cette opération de déblocage</label>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-success" id="btnValider" disabled>
                <i class="fas fa-unlock mr-1"></i>Valider le déblocage
            </button>
            <a href="{{ route('credit.show', $demande) }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i>Annuler
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
document.getElementById('chkConfirm').addEventListener('change', function() {
    document.getElementById('btnValider').disabled = !this.checked;
});
</script>
@endsection

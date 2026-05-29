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
                    <tr><th>Taux mensuel</th><td>{{ number_format((float) $demande->taux_interet_mensuel, 1, '.', '') }} %</td></tr>
                </table>
            </div>
        </div>

        {{-- Validations synthèse --}}
        @php
            $vLabels = [
                'AGENT_CREDIT'      => 'Agent crédit',
                'CONTROLEUR'        => 'Contrôleur',
                'CHARGE_OPERATIONS' => 'Chargé opérations',
                'GERANT'            => 'Gérant',
            ];
            $vColors = ['APPROUVE'=>'success','APPROUVE_AVEC_RESERVE'=>'warning','REJETE'=>'danger','EN_ATTENTE'=>'secondary'];
            $vMap = $demande->validations->keyBy('type_validateur');
        @endphp
        <div class="border-top pt-2 mt-1">
            <small class="text-muted font-weight-bold d-block mb-2">Validations :</small>
            <div class="row">
                @foreach($vLabels as $typeKey => $typeLabel)
                @php $val = $vMap[$typeKey] ?? null @endphp
                <div class="col-6 col-md-3 mb-2">
                    <div class="card card-outline card-{{ $val ? ($vColors[$val->decision] ?? 'secondary') : 'secondary' }} mb-0 h-100">
                        <div class="card-header py-1 px-2 bg-{{ $val ? ($vColors[$val->decision] ?? 'secondary') : 'secondary' }} text-white">
                            <small class="font-weight-bold">{{ $typeLabel }}</small>
                        </div>
                        <div class="card-body py-1 px-2 small">
                            @if($val && $val->decision !== 'EN_ATTENTE')
                                <span class="badge badge-{{ $vColors[$val->decision] ?? 'secondary' }} mb-1">
                                    {{ str_replace('_',' ', $val->decision) }}
                                </span><br>
                                <strong>Par :</strong>
                                {{ optional($val->validateur)->nom_complet ?? $val->nom_signataire ?? $val->signature_agent ?? $val->validateur_matricule ?? '–' }}<br>
                                <strong>Le :</strong>
                                {{ optional($val->date_validation)->format('d/m/Y H:i') ?? '–' }}<br>
                                @if($val->signature_agent ?? $val->validateur_matricule)
                                <strong>Signature :</strong>
                                <code>{{ $val->signature_agent ?? $val->validateur_matricule }}</code><br>
                                @endif
                                @if($val->montant_propose)
                                <strong>Montant :</strong>
                                {{ number_format($val->montant_propose, 2, ',', ' ') }} {{ $demande->devise }}<br>
                                @endif
                                @if($val->commentaire)
                                <span class="text-muted"><em>{{ $val->commentaire }}</em></span>
                                @endif
                            @else
                                <span class="badge badge-secondary">
                                    {{ $val ? 'En attente' : 'Non commencé' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
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

            $deviseDemande = strtoupper((string) ($demande->devise ?? ''));
            $comptesDebitList = collect(is_iterable($comptesDebit ?? null) ? $comptesDebit : []);
            $comptesDebitEligibles = $comptesDebitList->filter(function ($c) use ($deviseDemande) {
                return is_object($c)
                    && strtoupper((string) ($c->devise_code ?? '')) === $deviseDemande;
            });
        @endphp
        @if(count($formErrors))
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($formErrors as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('credit.deblocage.store', $demande) }}" id="formDeblocage">
        @csrf
        {{-- Montants calculés côté serveur — non modifiables par l'utilisateur --}}
        <input type="hidden" name="montant_debloque" value="{{ $montantTotal }}">
        <input type="hidden" name="frais_dossier" value="{{ $fraisTotal }}">

        {{-- Tableau récapitulatif automatique --}}
        <div class="card card-outline card-primary mb-3 deblocage-breakdown-card">
            <div class="card-header py-2">
                <h6 class="mb-0"><i class="fas fa-calculator mr-1"></i>Répartition automatique du montant approuvé</h6>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-bordered mb-0 deblocage-breakdown-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Poste</th>
                            <th class="text-right">%</th>
                            <th class="text-right">Montant ({{ $demande->devise }})</th>
                            <th>Destination</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="row-total">
                            <td><strong>Montant approuvé (gérant)</strong></td>
                            <td class="text-right">100%</td>
                            <td class="text-right"><strong>{{ number_format($montantTotal, 2, ',', ' ') }}</strong></td>
                            <td><span class="badge badge-light border text-dark">Coffre central → réparti</span></td>
                        </tr>
                        <tr class="row-net">
                            <td><i class="fas fa-university mr-1"></i>Entrée brute déblocage sur RMB</td>
                            <td class="text-right">100%</td>
                            <td class="text-right amount-net"><strong>{{ number_format($montantTotal, 2, ',', ' ') }}</strong></td>
                            <td><span class="badge badge-light border text-success">Compte RMB client (crédit initial)</span></td>
                        </tr>
                        <tr class="row-caution">
                            <td><i class="fas fa-lock mr-1"></i>Transfert caution RMB → GTC (bloquée)</td>
                            <td class="text-right">20%</td>
                            <td class="text-right amount-caution"><strong>{{ number_format($caution, 2, ',', ' ') }}</strong></td>
                            <td><span class="badge badge-light border text-warning">Retrait RMB + dépôt GTC (bordereau imprimable)</span></td>
                        </tr>
                        <tr class="row-fees">
                            <td><i class="fas fa-file-invoice-dollar mr-1"></i>Frais de dossier (1%)</td>
                            <td class="text-right">1%</td>
                            <td class="text-right amount-fees">{{ number_format($fraisDossier, 2, ',', ' ') }}</td>
                            <td><span class="badge badge-light border text-danger">Prélevés sur RMB client (non remboursables)</span></td>
                        </tr>
                        <tr class="row-fees">
                            <td><i class="fas fa-search-dollar mr-1"></i>Frais d'étude (3%)</td>
                            <td class="text-right">3%</td>
                            <td class="text-right amount-fees">{{ number_format($fraisEtude, 2, ',', ' ') }}</td>
                            <td><span class="badge badge-light border text-danger">Prélevés sur RMB client (non remboursables)</span></td>
                        </tr>
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <td colspan="2"><small class="text-muted">Net disponible RMB après transfert 20% et frais 4% (frais non remboursables)</small></td>
                            <td class="text-right"><strong>{{ number_format($netVerse, 2, ',', ' ') }}</strong></td>
                            <td><small class="text-muted">La caution 20% est remboursable en fin de crédit selon conditions</small></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="alert {{ ($rmbPreconditionOk ?? false) ? 'alert-success' : 'alert-danger' }} py-2 small mb-3">
            <div><strong>Vérification préalable RMB (24%)</strong></div>
            <div>
                Compte RMB ({{ $demande->devise }}) :
                <strong>{{ ($rmbCompteExiste ?? false) ? 'Oui' : 'Non' }}</strong>
                @if($rmbCompteExiste ?? false)
                    | Solde actuel : <strong>{{ number_format((float) ($rmbSoldeActuel ?? 0), 2, ',', ' ') }} {{ $demande->devise }}</strong>
                @endif
                | Dépôt minimum requis avant déblocage :
                <strong>{{ number_format($provisionRmbMin, 2, ',', ' ') }} {{ $demande->devise }}</strong>
            </div>
            @if($rmbPreconditionOk ?? false)
                <div class="mt-1"><i class="fas fa-check-circle mr-1"></i>Condition respectée: le dépôt minimum est déjà disponible.</div>
            @else
                <div class="mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>
                    Condition non respectée:
                    @if(!($rmbCompteExiste ?? false))
                        créez d'abord le compte RMB du client, puis effectuez le dépôt initial.
                    @else
                        il manque <strong>{{ number_format((float) ($rmbMontantManquant ?? 0), 2, ',', ' ') }} {{ $demande->devise }}</strong> à déposer.
                    @endif
                </div>
            @endif
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Date de déblocage <span class="text-danger">*</span></label>
                <input type="date" name="date_deblocage" class="form-control" required
                       value="{{ old('date_deblocage', date('Y-m-d')) }}">
                <small class="text-muted">Précondition: RMB client doit contenir au moins {{ number_format($provisionRmbMin, 2, ',', ' ') }} {{ $demande->devise }} (20% caution + 4% frais).</small>
            </div>
            <div class="form-group col-md-6">
                <label>Date du 1er remboursement <span class="text-danger">*</span></label>
                <input type="date" name="date_premier_remboursement" class="form-control" required
                       value="{{ old('date_premier_remboursement') }}"
                       min="{{ date('Y-m-d', strtotime('+1 month')) }}">
                <small class="text-muted">Le mois suivant la mise en place est recommandé.</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Source des fonds — Coffre central <span class="text-danger">*</span></label>
                @if($comptesDebitList->count() === 0)
                <div class="alert alert-danger py-2 small mb-1">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Aucun solde suffisant dans le coffre central pour couvrir
                    <strong>{{ number_format($montantTotal, 2, ',', ' ') }} {{ $demande->devise }}</strong>.
                </div>
                <select name="coffre_solde_id" class="form-control" required disabled>
                    <option value="">-- Coffre insuffisant --</option>
                </select>
                @else
                @if($comptesDebitEligibles->count() === 0)
                <div class="alert alert-warning py-2 small mb-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Dossier en <strong>{{ $deviseDemande }}</strong> : seuls les coffres en <strong>{{ $deviseDemande }}</strong> sont sélectionnables.
                    Les autres devises sont affichées en gris pour information.
                </div>
                @endif
                <select name="coffre_solde_id" class="form-control" required>
                    <option value="">-- Sélectionner un coffre en {{ $deviseDemande }} --</option>
                    @foreach($comptesDebitList as $c)
                    @if(is_object($c))
                    @php
                        $deviseCoffre = strtoupper((string) ($c->devise_code ?? ''));
                        $isDeviseEligible = $deviseCoffre === $deviseDemande;
                    @endphp
                    <option value="{{ $c->id }}"
                            {{ old('coffre_solde_id') == $c->id && $isDeviseEligible ? 'selected' : '' }}
                            {{ $isDeviseEligible ? '' : 'disabled' }}>
                        {{ $c->guichet->intitule ?? 'Coffre' }} —
                        {{ $c->devise_code }}
                        (Solde : {{ number_format((float)$c->solde_en_caisse, 2, ',', ' ') }} {{ $c->devise_code }})
                        {{ $isDeviseEligible ? '' : ' — non sélectionnable (devise différente du dossier)' }}
                    </option>
                    @endif
                    @endforeach
                </select>
                <small class="text-muted">Le coffre sera débité de <strong>{{ number_format($montantTotal, 2, ',', ' ') }} {{ $demande->devise }}</strong>. Seule la devise du dossier est autorisée.</small>
                @endif
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
            <p class="mb-1 small">
                En validant, le coffre sera débité de <strong>{{ number_format($montantTotal, 2, ',', ' ') }} {{ $demande->devise }}</strong> :
                <strong>{{ number_format($montantTotal, 2, ',', ' ') }}</strong> entrera d'abord sur RMB,
                puis <strong>{{ number_format($caution, 2, ',', ' ') }}</strong> seront transférés vers GTC (20% caution bloquée, bordereau imprimable),
                et <strong>{{ number_format($fraisTotal, 2, ',', ' ') }}</strong> seront retirés du RMB comme frais <strong>non remboursables</strong> (bordereau imprimable).
                Cette opération est irréversible.
            </p>
            <div class="custom-control custom-checkbox mt-2">
                <input type="checkbox" class="custom-control-input" id="chkConfirm" required {{ ($rmbPreconditionOk ?? false) ? '' : 'disabled' }}>
                <label class="custom-control-label" for="chkConfirm">Je confirme cette opération de déblocage</label>
            </div>
            @if(!($rmbPreconditionOk ?? false))
                <small class="text-danger d-block mt-2">
                    Validation bloquée tant que le compte RMB n'existe pas avec le dépôt minimum requis ({{ number_format($provisionRmbMin, 2, ',', ' ') }} {{ $demande->devise }}).
                </small>
            @endif
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-success" id="btnValider" disabled {{ ($rmbPreconditionOk ?? false) ? '' : 'title=\"Précondition RMB non remplie\"' }}>
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

@push('js')
<style>
    .deblocage-breakdown-table th,
    .deblocage-breakdown-table td {
        vertical-align: middle;
    }

    .deblocage-breakdown-table .row-total {
        background-color: rgba(13, 110, 253, 0.12);
    }

    .deblocage-breakdown-table .row-net {
        background-color: rgba(25, 135, 84, 0.14);
    }

    .deblocage-breakdown-table .row-caution {
        background-color: rgba(255, 193, 7, 0.12);
    }

    .deblocage-breakdown-table .row-fees {
        background-color: rgba(220, 53, 69, 0.12);
    }

    .deblocage-breakdown-table .amount-net {
        color: #28a745;
        font-weight: 700;
    }

    .deblocage-breakdown-table .amount-caution {
        color: #d39e00;
        font-weight: 700;
    }

    .deblocage-breakdown-table .amount-fees {
        color: #dc3545;
        font-weight: 700;
    }
</style>
<script>
(() => {
    const chk = document.getElementById('chkConfirm');
    const btn = document.getElementById('btnValider');
    const rmbPreconditionOk = @json((bool) ($rmbPreconditionOk ?? false));

    if (!chk || !btn) {
        return;
    }

    const syncState = () => {
        if (!rmbPreconditionOk) {
            btn.disabled = true;
            return;
        }
        btn.disabled = !chk.checked;
    };

    chk.addEventListener('change', syncState);
    syncState();
})();
</script>
@endpush

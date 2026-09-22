@extends('layouts.app')

@section('page_title', "Importer un ancien dossier de crédit")
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Import ancien dossier')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="row">
<div class="col-md-9">

<div class="card card-outline card-warning">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-history text-warning mr-2"></i>
            Import d'un ancien dossier (historique)
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

        <div class="alert alert-warning py-2 small">
            <i class="fas fa-info-circle mr-1"></i>
            Ce formulaire sert à enregistrer un dossier <strong>déjà débloqué dans le passé</strong>.
            Le dossier, le déblocage et l'échéancier sont créés aux dates indiquées. Les échéances
            antérieures à aujourd'hui sont marquées <strong>en retard</strong>. Les remboursements
            se feront ensuite normalement. Le compte RMB du client est créé automatiquement.
        </div>

        <form method="POST" action="{{ route('credit.import_ancien.store') }}" id="formImportAncien">
        @csrf

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3">
            <i class="fas fa-user mr-1"></i> Identification du client
        </h6>

        <div class="form-row">
            <div class="form-group col-md-8">
                <label>Client <span class="text-danger">*</span></label>
                @php($selectedClientValue = old('client_matricule', $selectedClientMatricule ?? ''))
                <select name="client_matricule" id="sel_client_import" class="form-control" required style="width:100%">
                    <option value="">-- Sélectionner un client --</option>
                    {{-- Seul le client présélectionné est rendu côté serveur ;
                         les autres viennent de la recherche AJAX. --}}
                    @if(!empty($selectedClient))
                    <option value="{{ $selectedClient->matricule }}" selected>
                        {{ $selectedClient->full_name }} – {{ $selectedClient->matricule }}
                    </option>
                    @endif
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>Type de crédit <span class="text-danger">*</span></label>
                <select name="type_credit" class="form-control" required>
                    <option value="INDIVIDUEL" {{ old('type_credit','INDIVIDUEL')=='INDIVIDUEL'?'selected':'' }}>Individuel</option>
                    <option value="SOLIDAIRE"  {{ old('type_credit')=='SOLIDAIRE'?'selected':'' }}>Solidaire / Groupe</option>
                    <option value="PME"        {{ old('type_credit')=='PME'?'selected':'' }}>PME / Entreprise</option>
                </select>
            </div>
        </div>

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3 mt-3">
            <i class="fas fa-calculator mr-1"></i> Paramètres du crédit
        </h6>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Montant demandé <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="montant_demande" id="inp_montant_import"
                           class="form-control" step="0.01" min="1"
                           value="{{ old('montant_demande') }}" required>
                    <div class="input-group-append">
                        <select name="devise" id="inp_devise_import" class="form-control">
                            <option value="CDF" {{ old('devise','CDF')=='CDF'?'selected':'' }}>CDF</option>
                            <option value="USD" {{ old('devise')=='USD'?'selected':'' }}>USD</option>
                            <option value="EUR" {{ old('devise')=='EUR'?'selected':'' }}>EUR</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Durée (mois) <span class="text-danger">*</span></label>
                <input type="number" name="duree_mois" class="form-control" min="1" max="360"
                       value="{{ old('duree_mois', 12) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Taux d'intérêt mensuel (%) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="taux_interet_mensuel" class="form-control"
                           step="0.01" min="0.01" max="100"
                           value="{{ old('taux_interet_mensuel', 5.5) }}" required>
                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Objet du crédit <span class="text-danger">*</span></label>
                <input type="text" name="objet_credit" class="form-control"
                       maxlength="500" value="{{ old('objet_credit') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label>Garantie (optionnel)</label>
            <textarea name="garantie_description" class="form-control" rows="2">{{ old('garantie_description') }}</textarea>
        </div>

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3 mt-3">
            <i class="fas fa-user-tie mr-1"></i> Affectation (comme un dossier normal)
        </h6>

        <div class="alert alert-info py-2 small">
            <i class="fas fa-info-circle mr-1"></i>
            L'agent de crédit sélectionné sera affecté au dossier. L'analyse et les 4 validations
            (Agent crédit → Contrôleur → Chargé opérations → Gérant) sont créées automatiquement
            et approuvées, comme si le dossier avait suivi le circuit normal.
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Agent de crédit concerné <span class="text-danger">*</span></label>
                <select name="agent_analyse_matricule" id="sel_agent_analyse_import"
                        class="form-control" required style="width:100%">
                    <option value="">-- Sélectionner un agent --</option>
                    @foreach($agentsAnalyse as $a)
                    <option value="{{ $a->matricule }}" {{ old('agent_analyse_matricule') == $a->matricule ? 'selected' : '' }}>
                        {{ trim(($a->nom ?? '') . ' ' . ($a->postnom ?? '') . ' ' . ($a->prenom ?? '')) }} – {{ $a->matricule }}
                        @if($a->portefeuille_actif_resume) ({{ $a->portefeuille_actif_resume }}) @endif
                    </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Seuls les agents avec le profil « Analyse crédit » apparaissent.</small>
            </div>
            <div class="form-group col-md-6">
                <label>Portefeuille (optionnel)</label>
                <select name="portefeuille_id" class="form-control" style="width:100%">
                    <option value="">-- Auto (déduit de l'agent) --</option>
                    @foreach($portefeuilles as $p)
                    <option value="{{ $p->id }}" {{ old('portefeuille_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nom_portefeuille }}
                    </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Laisser vide : déduit automatiquement du portefeuille actif de l'agent.</small>
            </div>
        </div>

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3 mt-3">
            <i class="fas fa-percentage mr-1"></i> Retenues au déblocage <small class="text-normal">(modifiables selon l'agence)</small>
        </h6>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Caution (%)</label>
                <div class="input-group">
                    <input type="number" name="pourcentage_caution" class="form-control"
                           step="0.01" min="0" max="100" value="{{ old('pourcentage_caution', 20) }}">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label>Frais de dossier (%)</label>
                <div class="input-group">
                    <input type="number" name="pourcentage_frais_dossier" class="form-control"
                           step="0.01" min="0" max="100" value="{{ old('pourcentage_frais_dossier', 1) }}">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label>Frais d'étude (%)</label>
                <div class="input-group">
                    <input type="number" name="pourcentage_frais_etude" class="form-control"
                           step="0.01" min="0" max="100" value="{{ old('pourcentage_frais_etude', 3) }}">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                </div>
            </div>
        </div>

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3 mt-3">
            <i class="fas fa-calendar-alt mr-1"></i> Dates historiques
        </h6>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Date de création <span class="text-danger">*</span></label>
                <input type="date" name="date_creation" class="form-control"
                       value="{{ old('date_creation') }}" required>
            </div>
            <div class="form-group col-md-4">
                <label>Date de déblocage <span class="text-danger">*</span></label>
                <input type="date" name="date_deblocage" class="form-control"
                       value="{{ old('date_deblocage') }}" required>
            </div>
            <div class="form-group col-md-4">
                <label>1<sup>re</sup> échéance le <span class="text-danger">*</span></label>
                <input type="date" name="date_premier_remboursement" class="form-control"
                       value="{{ old('date_premier_remboursement') }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Montant débloqué (brut) <span class="text-danger">*</span></label>
                <input type="number" name="montant_debloque" class="form-control"
                       step="0.01" min="1" value="{{ old('montant_debloque') }}" required>
                <small class="form-text text-muted">Les retenues ci-dessus sont calculées automatiquement sur ce montant.</small>
            </div>
            <div class="form-group col-md-6">
                <label>Agent ayant traité (optionnel)</label>
                <select name="agent_matricule" id="sel_agent_traite_import" class="form-control" style="width:100%">
                    <option value="">-- Système (par défaut) --</option>
                    @foreach($agentsTous as $a)
                    <option value="{{ $a->matricule }}" {{ old('agent_matricule') == $a->matricule ? 'selected' : '' }}>
                        {{ trim(($a->nom ?? '') . ' ' . ($a->postnom ?? '') . ' ' . ($a->prenom ?? '')) }} – {{ $a->matricule }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>N° de dossier d'origine (optionnel)</label>
            <input type="text" name="numero_dossier" class="form-control"
                   maxlength="30" value="{{ old('numero_dossier') }}"
                   placeholder="Laissez vide pour générer automatiquement">
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save mr-1"></i> Importer le dossier
            </button>
            <a href="{{ route('credit.index') }}" class="btn btn-default ml-2">Annuler</a>
        </div>
        </form>
    </div>
</div>

</div>
</div>
</section>

@push('js')
<script>
    $(function () {
        if (!$.fn.select2) return;

        $('#sel_agent_analyse_import, #sel_agent_traite_import').select2({
            theme: 'bootstrap4',
            width: '100%',
            allowClear: true,
            language: {
                noResults: function () { return 'Aucun résultat trouvé.'; }
            }
        });

        // Client : recherche AJAX (2 200+ clients en <option> = page lourde)
        $('#sel_client_import').select2({
            theme: 'bootstrap4',
            width: '100%',
            allowClear: true,
            minimumInputLength: 2,
            placeholder: '-- Sélectionner un client (nom, postnom, prénom...) --',
            language: {
                noResults: function () { return 'Aucun client trouvé.'; },
                inputTooShort: function () { return 'Tapez au moins 2 caractères (nom, postnom, prénom ou matricule).'; }
            },
            ajax: {
                url: '{{ route("credit.clients.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) {
                    return {
                        results: data.map(function (c) {
                            return { id: c.matricule, text: c.full_name + ' – ' + c.matricule };
                        })
                    };
                }
            }
        });
    });
</script>
@endpush

{{-- Après enregistrement : modal système de succès + rester sur le formulaire --}}
@if(session('success'))
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof showSystemMessage === 'function') {
            showSystemMessage('success', @json(session('success')));
        }
    });
</script>
@endpush
@endif
@endsection

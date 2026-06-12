@extends('layouts.app')

@section('page_title', 'Modifier Demande – ' . $dossier->numero_dossier)
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Modifier brouillon')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="row">
<div class="col-md-8">

<div class="card card-outline card-warning">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-edit text-warning mr-2"></i>
            Modifier le brouillon
            <code class="ml-2 small">{{ $dossier->numero_dossier }}</code>
        </h5>
        <a href="{{ route('credit.show', $dossier) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-times mr-1"></i>Annuler
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-warning py-2 small mb-3">
            <i class="fas fa-info-circle mr-1"></i>
            Vous modifiez un dossier en <strong>brouillon</strong>. Ces modifications sont possibles uniquement avant la soumission.
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('credit.update', $dossier) }}" id="formEdit">
        @csrf
        @method('PUT')

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3">
            <i class="fas fa-user mr-1"></i> Identification du client
        </h6>

        <div class="form-row">
            <div class="form-group col-md-8">
                <label>Client <span class="text-danger">*</span></label>
                <select name="client_matricule" id="sel_client" class="form-control select2" required>
                    <option value="">-- Sélectionner un client --</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->matricule }}"
                            data-nom="{{ trim(($c->nom ?? '') . ' ' . ($c->postnom ?? '') . ' ' . ($c->prenom ?? '')) }}"
                            {{ old('client_matricule', $dossier->client_matricule) == $c->matricule ? 'selected' : '' }}>
                        {{ trim(($c->nom ?? '') . ' ' . ($c->postnom ?? '') . ' ' . ($c->prenom ?? '')) }} – {{ $c->matricule }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>Type de crédit <span class="text-danger">*</span></label>
                <select name="type_credit" class="form-control" required>
                    <option value="INDIVIDUEL" {{ old('type_credit', $dossier->type_credit)=='INDIVIDUEL'?'selected':'' }}>Individuel</option>
                    <option value="SOLIDAIRE"  {{ old('type_credit', $dossier->type_credit)=='SOLIDAIRE'?'selected':'' }}>Solidaire / Groupe</option>
                    <option value="PME"        {{ old('type_credit', $dossier->type_credit)=='PME'?'selected':'' }}>PME / Entreprise</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Portefeuille crédit <span class="text-danger">*</span></label>
                <select name="portefeuille_id" class="form-control" required>
                    <option value="">-- Sélectionner un portefeuille --</option>
                    @foreach(($portefeuillesDisponibles ?? collect()) as $pf)
                        <option value="{{ $pf->id }}" {{ old('portefeuille_id', $dossier->portefeuille_id) == $pf->id ? 'selected' : '' }}>
                            {{ $pf->nom_portefeuille }} (#{{ $pf->id }})
                            @if(!empty($pf->agent_matricule)) — {{ $pf->agent_matricule }} @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3 mt-3">
            <i class="fas fa-calculator mr-1"></i> Paramètres du crédit
        </h6>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Montant demandé <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="montant_demande" id="inp_montant"
                           class="form-control" step="0.01" min="1"
                           value="{{ old('montant_demande', $dossier->montant_demande) }}" required
                           oninput="simuler()">
                    <div class="input-group-append">
                        <select name="devise" id="inp_devise" class="form-control" onchange="simuler()">
                            <option value="CDF" {{ old('devise', $dossier->devise)=='CDF'?'selected':'' }}>CDF</option>
                            <option value="USD" {{ old('devise', $dossier->devise)=='USD'?'selected':'' }}>USD</option>
                            <option value="EUR" {{ old('devise', $dossier->devise)=='EUR'?'selected':'' }}>EUR</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label>Durée (mois) <span class="text-danger">*</span></label>
                <input type="number" name="duree_mois" id="inp_duree"
                       class="form-control" min="1" max="360"
                       value="{{ old('duree_mois', $dossier->duree_mois) }}" required
                       oninput="simuler()">
            </div>
            <div class="form-group col-md-4">
                <label>Taux d'intérêt mensuel (%) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="taux_interet_mensuel" id="inp_taux"
                           class="form-control" step="0.01" min="0.01" max="100"
                           value="{{ old('taux_interet_mensuel', $dossier->taux_interet_mensuel) }}" required
                           oninput="simuler()">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Objet du crédit <span class="text-danger">*</span></label>
            <textarea name="objet_credit" class="form-control" rows="2"
                      maxlength="500" required>{{ old('objet_credit', $dossier->objet_credit) }}</textarea>
        </div>

        <div class="form-group">
            <label>Description des garanties</label>
            <textarea name="garantie_description" class="form-control" rows="2">{{ old('garantie_description', $dossier->garantie_description) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Service référent</label>
                    <input type="text" name="service_provenance" class="form-control"
                           maxlength="100" placeholder="Ex: Service RH…"
                           value="{{ old('service_provenance', $dossier->service_provenance) }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nom du référent</label>
                    <input type="text" name="referent_nom" class="form-control"
                           maxlength="120" placeholder="Nom complet de la personne référente"
                           value="{{ old('referent_nom', $dossier->referent_nom) }}">
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save mr-1"></i> Enregistrer les modifications
            </button>
            <a href="{{ route('credit.show', $dossier) }}" class="btn btn-secondary ml-2">
                <i class="fas fa-times mr-1"></i> Annuler
            </a>
        </div>

        </form>
    </div>
</div>

</div>

{{-- Simulation Échéancier --}}
<div class="col-md-4">
    <div class="card card-outline card-primary sticky-top" style="top:80px;">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-table mr-2 text-primary"></i>Simulation échéancier dégressif
            </h5>
        </div>
        <div class="card-body" id="zone_simulation">
            <p class="text-muted text-center py-3">
                <i class="fas fa-calculator fa-2x mb-2 d-block"></i>
                Modifiez les paramètres pour actualiser la simulation.
            </p>
        </div>
    </div>
</div>

</div>
</div>
</section>
@endsection

@push('js')
<script>
let simulTimer = null;
function simuler() {
    clearTimeout(simulTimer);
    simulTimer = setTimeout(() => {
        const montant = document.getElementById('inp_montant').value;
        const duree   = document.getElementById('inp_duree').value;
        const taux    = document.getElementById('inp_taux').value;
        const devise  = document.getElementById('inp_devise').value;
        if (!montant || !duree || !taux) return;
        fetch(`{{ route("credit.ajax.simuler") }}?montant=${montant}&taux=${taux}&duree=${duree}`)
            .then(r => r.json())
            .then(data => {
                if (data.errors) return;
                let html = `
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong>Capital total :</strong></span>
                        <span class="text-success font-weight-bold">${parseFloat(data.total_capital).toLocaleString('fr',{minimumFractionDigits:2})} ${devise}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong>Total intérêts :</strong></span>
                        <span class="text-danger font-weight-bold">${parseFloat(data.total_interets).toLocaleString('fr',{minimumFractionDigits:2})} ${devise}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-top pt-2">
                        <span><strong>TOTAL GÉNÉRAL :</strong></span>
                        <span class="text-dark font-weight-bold">${parseFloat(data.total_general).toLocaleString('fr',{minimumFractionDigits:2})} ${devise}</span>
                    </div>
                    <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                    <table class="table table-xs table-bordered small mb-0">
                        <thead class="thead-dark"><tr><th>#</th><th>Cap. restant</th><th>Capital</th><th>Intérêt</th><th>Total</th></tr></thead>
                        <tbody>`;
                data.echeances.forEach(e => {
                    html += `<tr>
                        <td>${e.numero}</td>
                        <td class="text-right">${parseFloat(e.capital_restant_debut).toLocaleString('fr',{minimumFractionDigits:2})}</td>
                        <td class="text-right">${parseFloat(e.capital).toLocaleString('fr',{minimumFractionDigits:2})}</td>
                        <td class="text-right text-danger">${parseFloat(e.interet).toLocaleString('fr',{minimumFractionDigits:2})}</td>
                        <td class="text-right font-weight-bold">${parseFloat(e.total).toLocaleString('fr',{minimumFractionDigits:2})}</td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                document.getElementById('zone_simulation').innerHTML = html;
            });
    }, 400);
}

window.addEventListener('DOMContentLoaded', () => {
    if (window.jQuery && $.fn.select2) {
        $('#sel_client').select2({ theme: 'bootstrap4', width: '100%', placeholder: '-- Sélectionner un client --', allowClear: true });
    }
    simuler();
});
</script>
@endpush

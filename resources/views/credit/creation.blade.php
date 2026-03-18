@extends('layouts.app')

@section('page_title', 'Nouvelle Demande de Crédit')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Nouvelle demande')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="row">
<div class="col-md-8">

<div class="card card-outline card-success">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-plus-circle text-success mr-2"></i>
            Nouvelle Demande de Crédit
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

        <form method="POST" action="{{ route('credit.store') }}" id="formCreation">
        @csrf

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3">
            <i class="fas fa-user mr-1"></i> Identification du client
        </h6>

        <div class="form-row">
            <div class="form-group col-md-8">
                <label>Client <span class="text-danger">*</span></label>
                <select name="client_matricule" id="sel_client" class="form-control select2"
                        required onchange="chargerComptes(this.value)">
                    <option value="">-- Sélectionner un client --</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->matricule }}" {{ old('client_matricule') == $c->matricule ? 'selected' : '' }}>
                        {{ $c->nom }} {{ $c->prenom }} – {{ $c->matricule }}
                    </option>
                    @endforeach
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

        <div class="form-group">
            <label>Compte de déblocage <span class="text-danger">*</span></label>
            <select name="compte_id" id="sel_compte" class="form-control" required>
                <option value="">-- Sélectionner d'abord un client --</option>
            </select>
            <small class="text-muted">Compte CC ou RMB du client (fonds versés sur ce compte)</small>
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
                           value="{{ old('montant_demande') }}" required
                           oninput="simuler()">
                    <div class="input-group-append">
                        <select name="devise" id="inp_devise" class="form-control" onchange="simuler()">
                            <option value="CDF" {{ old('devise','CDF')=='CDF'?'selected':'' }}>CDF</option>
                            <option value="USD" {{ old('devise')=='USD'?'selected':'' }}>USD</option>
                            <option value="EUR" {{ old('devise')=='EUR'?'selected':'' }}>EUR</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label>Durée (mois) <span class="text-danger">*</span></label>
                <input type="number" name="duree_mois" id="inp_duree"
                       class="form-control" min="1" max="360"
                       value="{{ old('duree_mois', 12) }}" required
                       oninput="simuler()">
            </div>
            <div class="form-group col-md-4">
                <label>Taux d'intérêt mensuel (%) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="taux_interet_mensuel" id="inp_taux"
                           class="form-control" step="0.01" min="0.01" max="100"
                           value="{{ old('taux_interet_mensuel', 5.5) }}" required
                           oninput="simuler()">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Objet du crédit <span class="text-danger">*</span></label>
            <textarea name="objet_credit" class="form-control" rows="2"
                      maxlength="500" required>{{ old('objet_credit') }}</textarea>
        </div>

        <div class="form-group">
            <label>Description des garanties</label>
            <textarea name="garantie_description" class="form-control" rows="2">{{ old('garantie_description') }}</textarea>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i> Créer le dossier en brouillon
            </button>
            <a href="{{ route('credit.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-times mr-1"></i> Annuler
            </a>
        </div>

        </form>
    </div>
</div>

</div>

{{-- ── Simulation Échéancier ──────────────────────────── --}}
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
                Remplissez le montant, la durée et le taux pour voir l'échéancier simulé.
            </p>
        </div>
    </div>
</div>

</div>
</div>
</section>
@endsection

@section('scripts')
<script>
function chargerComptes(matricule) {
    const sel = document.getElementById('sel_compte');
    sel.innerHTML = '<option value="">Chargement...</option>';
    if (!matricule) {
        sel.innerHTML = '<option value="">-- Sélectionner d\'abord un client --</option>';
        return;
    }
    fetch('{{ route("credit.ajax.comptes_client") }}?client_matricule=' + encodeURIComponent(matricule))
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                sel.innerHTML = '<option value="">Aucun compte CC/RMB disponible</option>';
                return;
            }
            sel.innerHTML = '<option value="">-- Sélectionner un compte --</option>';
            data.forEach(c => {
                sel.innerHTML += `<option value="${c.code_compte}">${c.code_compte} (${c.type}) – Solde: ${parseFloat(c.solde_reel).toLocaleString('fr')} ${c.devise}</option>`;
            });
        });
}

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
                        <thead class="thead-dark">
                            <tr><th>#</th><th>Cap. restant</th><th>Capital</th><th>Intérêt</th><th>Total</th></tr>
                        </thead><tbody>`;

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

// Pré-charger si valeur sauvegardée
window.addEventListener('DOMContentLoaded', () => {
    const mat = document.getElementById('sel_client').value;
    if (mat) chargerComptes(mat);
    simuler();
});
</script>
@endsection

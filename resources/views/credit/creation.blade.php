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
                @php($selectedClientValue = old('client_matricule', $selectedClientMatricule ?? request('client_matricule')))
                <select name="client_matricule" id="sel_client" class="form-control select2" required>
                    <option value="">-- Sélectionner un client --</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->matricule }}"
                            data-nom="{{ trim(($c->nom ?? '') . ' ' . ($c->postnom ?? '') . ' ' . ($c->prenom ?? '')) }}"
                            data-prenom="{{ $c->prenom ?? '' }}"
                            data-telephone="{{ $c->telephone ?? '' }}"
                            data-photo="{{ $c->photo ? basename($c->photo) : '' }}"
                            data-sexe="{{ $c->sexe ?? '' }}"
                            {{ $selectedClientValue == $c->matricule ? 'selected' : '' }}>
                        {{ trim(($c->nom ?? '') . ' ' . ($c->postnom ?? '') . ' ' . ($c->prenom ?? '')) }} – {{ $c->matricule }}
                    </option>
                    @endforeach
                </select>
                <input type="hidden" id="selectedClientMatriculeConfirmed" value="{{ $selectedClientValue }}">
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

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Portefeuille crédit <span class="text-danger">*</span></label>
                <select name="portefeuille_id" class="form-control" required>
                    <option value="">-- Sélectionner un portefeuille --</option>
                    @foreach(($portefeuillesDisponibles ?? collect()) as $pf)
                        <option value="{{ $pf->id }}" {{ (string) old('portefeuille_id') === (string) $pf->id ? 'selected' : '' }}>
                            {{ $pf->nom_portefeuille }} (#{{ $pf->id }})
                            @if(!empty($pf->agent_matricule))
                                — {{ $pf->agent_matricule }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <small class="text-muted d-block mt-1">
                    Le dossier est rattaché au portefeuille sélectionné dès sa création.
                </small>
            </div>
        </div>

        <div class="alert alert-info py-2 small">
            <i class="fas fa-info-circle mr-1"></i>
            Le compte du client n'est pas demandé à cette étape. Un compte RMB sera rattaché automatiquement lors du déblocage, si nécessaire.
        </div>

        <h6 class="text-muted text-uppercase font-weight-bold border-bottom pb-1 mb-3 mt-3">
            <i class="fas fa-calculator mr-1"></i> Paramètres du crédit
        </h6>

        <div class="form-row">
            <div class="form-group col-md-6">
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
            <div class="form-group col-md-6">
                <label>Durée (mois) <span class="text-danger">*</span></label>
                <input type="number" name="duree_mois" id="inp_duree"
                       class="form-control" min="1" max="360"
                       value="{{ old('duree_mois', 12) }}" required
                       oninput="simuler()">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Taux d'intérêt mensuel (%) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="taux_interet_mensuel" id="inp_taux"
                           class="form-control" step="0.01" min="0.01" max="100"
                           value="{{ old('taux_interet_mensuel', 5.5) }}" required
                           oninput="simuler()">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Commission <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="commission_totale" id="inp_commission"
                           class="form-control" step="0.01" min="0"
                           value="{{ old('commission_totale', 0) }}" required
                           oninput="simuler()">
                    <div class="input-group-append">
                        <span class="input-group-text" id="symbole_commission">Fc</span>
                    </div>
                </div>
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Calculée selon la grille, modifiable</small>
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

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Service référent <small class="text-muted">(si le client a été envoyé par un service)</small></label>
                    <input type="text" name="service_provenance" class="form-control"
                           maxlength="100" placeholder="Ex: Service RH, Département Commercial…"
                           value="{{ old('service_provenance') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nom du référent</label>
                    <input type="text" name="referent_nom" class="form-control"
                           maxlength="120" placeholder="Nom complet de la personne référente"
                           value="{{ old('referent_nom') }}">
                </div>
            </div>
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

<div class="modal fade" id="modalIdentiteClientCredit" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content shadow-lg" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header py-2" style="background:linear-gradient(90deg,#2563eb 0%,#1d4ed8 100%);">
                <h6 class="modal-title text-white mb-0">
                    <i class="fas fa-user-check mr-2"></i>Confirmer l'identité du client
                </h6>
            </div>
            <div class="modal-body p-0">
                <div class="alert alert-warning mb-0 py-2 px-3 small rounded-0">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Vérifiez la photo et les informations avant de continuer la demande de crédit.
                </div>
                <div class="p-3 d-flex align-items-center">
                    <div class="mr-3 flex-shrink-0">
                        <img id="photoIdentiteClientCredit"
                             src="{{ asset('vendor/adminlte/dist/img/user2-160x160.jpg') }}"
                             alt="Photo client"
                             style="width:110px;height:130px;object-fit:cover;border-radius:10px;border:3px solid #3b82f6;background:#e2e8f0;">
                    </div>
                    <div class="flex-grow-1">
                        <div class="mb-1">
                            <span class="badge badge-primary badge-pill px-2 py-1" id="badgeSexeClientCredit" style="font-size:.78rem;">—</span>
                        </div>
                        <h5 class="mb-1 font-weight-bold text-dark" id="nomCompletIdentiteCredit">—</h5>
                        <div class="text-muted small mb-2" id="prenomIdentiteCredit">—</div>
                        <table class="table table-sm table-borderless mb-0" style="font-size:.87rem;">
                            <tr>
                                <td class="py-0 text-muted pl-0" style="width:110px;"><i class="fas fa-id-badge mr-1 text-primary"></i>Matricule</td>
                                <td class="py-0 font-weight-bold" id="matriculeIdentiteCredit">—</td>
                            </tr>
                            <tr>
                                <td class="py-0 text-muted pl-0"><i class="fas fa-phone mr-1 text-primary"></i>Téléphone</td>
                                <td class="py-0" id="telephoneIdentiteCredit">—</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 justify-content-between" style="border-top:1px solid #e2e8f0;">
                <button type="button" class="btn btn-danger btn-sm" id="btnNonIdentiteClientCredit">
                    <i class="fas fa-times mr-1"></i> Non
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btnOuiIdentiteClientCredit">
                    <i class="fas fa-check mr-1"></i> Oui, confirmer
                </button>
            </div>
        </div>
    </div>
</div>

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
        const commission = document.getElementById('inp_commission').value || 0;

        // Mettre à jour le symbole de la commission selon la devise
        const symboles = { 'CDF': 'Fc', 'USD': '$', 'EUR': '€' };
        const symboleEl = document.getElementById('symbole_commission');
        if (symboleEl) symboleEl.textContent = symboles[devise] || 'Fc';

        if (!montant || !duree || !taux) return;

        fetch(`{{ route("credit.ajax.simuler") }}?montant=${montant}&taux=${taux}&duree=${duree}&commission=${commission}`)
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
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong>Commission totale :</strong></span>
                        <span class="text-info font-weight-bold">${parseFloat(data.total_commission || 0).toLocaleString('fr',{minimumFractionDigits:2})} ${devise}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-top pt-2">
                        <span><strong>TOTAL GÉNÉRAL :</strong></span>
                        <span class="text-dark font-weight-bold">${parseFloat(data.total_general).toLocaleString('fr',{minimumFractionDigits:2})} ${devise}</span>
                    </div>
                    <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                    <table class="table table-xs table-bordered small mb-0">
                        <thead class="thead-dark">
                            <tr><th>#</th><th>Cap. restant</th><th>Capital</th><th>Intérêt</th><th>Commission</th><th>Total</th></tr>
                        </thead><tbody>`;

                data.echeances.forEach(e => {
                    html += `<tr>
                        <td>${e.numero}</td>
                        <td class="text-right">${parseFloat(e.capital_restant_debut).toLocaleString('fr',{minimumFractionDigits:2})}</td>
                        <td class="text-right">${parseFloat(e.capital).toLocaleString('fr',{minimumFractionDigits:2})}</td>
                        <td class="text-right text-danger">${parseFloat(e.interet).toLocaleString('fr',{minimumFractionDigits:2})}</td>
                        <td class="text-right text-info">${parseFloat(e.commission || 0).toLocaleString('fr',{minimumFractionDigits:2})}</td>
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
    const urlClientPhoto = '{{ url("/clients/photo") }}';
    let pendingClient = null;

    if (window.jQuery && $.fn.select2) {
        $('#sel_client').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: '-- Sélectionner un client --',
            allowClear: true,
            language: {
                noResults: function () { return 'Aucun client trouvé'; }
            }
        });

        $('#sel_client').on('select2:select', function() {
            const opt = $(this).find('option:selected');
            pendingClient = {
                matricule: opt.val() || '',
                nom: $.trim(opt.data('nom') || ''),
                prenom: $.trim(opt.data('prenom') || ''),
                telephone: $.trim(opt.data('telephone') || '') || '—',
                photo: $.trim(opt.data('photo') || ''),
                sexe: $.trim(opt.data('sexe') || ''),
            };

            $('#nomCompletIdentiteCredit').text(pendingClient.nom || '—');
            $('#prenomIdentiteCredit').text(pendingClient.prenom || '—');
            $('#matriculeIdentiteCredit').text(pendingClient.matricule || '—');
            $('#telephoneIdentiteCredit').text(pendingClient.telephone);

            const isF = pendingClient.sexe === 'F';
            $('#badgeSexeClientCredit')
                .removeClass('badge-primary badge-danger')
                .addClass(isF ? 'badge-danger' : 'badge-primary')
                .html(isF ? '<i class="fas fa-female mr-1"></i>Femme' : '<i class="fas fa-male mr-1"></i>Homme');

            if (pendingClient.photo) {
                $('#photoIdentiteClientCredit').attr('src', urlClientPhoto + '/' + encodeURIComponent(pendingClient.photo));
            } else {
                $('#photoIdentiteClientCredit').attr('src', '{{ asset("vendor/adminlte/dist/img/user2-160x160.jpg") }}');
            }

            $('#modalIdentiteClientCredit').modal('show');
        });

        $('#sel_client').on('select2:clear', function() {
            pendingClient = null;
            $('#selectedClientMatriculeConfirmed').val('');
        });

        $('#btnOuiIdentiteClientCredit').on('click', function() {
            if (pendingClient && pendingClient.matricule) {
                $('#selectedClientMatriculeConfirmed').val(pendingClient.matricule);
            }
            $('#modalIdentiteClientCredit').modal('hide');
        });

        $('#btnNonIdentiteClientCredit').on('click', function() {
            pendingClient = null;
            $('#selectedClientMatriculeConfirmed').val('');
            $('#sel_client').val(null).trigger('change');
            $('#modalIdentiteClientCredit').modal('hide');
            if (typeof showSystemMessage === 'function') {
                showSystemMessage('warning', 'Sélection annulée. Veuillez choisir le bon client.');
            }
        });

        $('#formCreation').on('submit', function(e) {
            const selected = $('#sel_client').val();
            const confirmed = $('#selectedClientMatriculeConfirmed').val();

            if (selected && selected !== confirmed) {
                e.preventDefault();
                if (typeof showSystemMessage === 'function') {
                    showSystemMessage('error', 'Veuillez confirmer l\'identité du client sélectionné avant de créer la demande.');
                }
            }
        });
    }

    simuler();
});
</script>
@endpush

@extends('layouts.app')

@section('page_title', 'Remboursement – ' . $demande->numero_dossier)
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Enregistrer un remboursement')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-2 text-success"></i>Encaissement remboursement</h5>
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

<div class="row">

{{-- ── Panneau gauche : info dossier + formulaire ─────────── --}}
<div class="col-md-5">

    {{-- Info dossier --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-header py-2">
            <h6 class="mb-0"><i class="fas fa-file-alt mr-1"></i>Dossier {{ $demande->numero_dossier }}</h6>
        </div>
        <div class="card-body p-2">
            <table class="table table-sm table-borderless small mb-0">
                <tr><th>Client</th>
                    <td>{{ optional($demande->client)->nom }} {{ optional($demande->client)->prenom }}</td></tr>
                <tr><th>Montant initial</th>
                    <td>{{ number_format($demande->montant_accorde ?? $demande->montant_demande, 2, ',', ' ') }} {{ $demande->devise }}</td></tr>
                <tr><th>Capital restant</th>
                    <td><strong class="text-danger">{{ number_format($demande->capital_restant ?? 0, 2, ',', ' ') }} {{ $demande->devise }}</strong></td></tr>
                <tr><th>Taux mensuel</th>
                    <td>{{ number_format((float) $demande->taux_interet_mensuel, 1, '.', '') }} %</td></tr>
            </table>
        </div>
    </div>

    {{-- Formulaire --}}
    <div class="card card-outline card-success">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-money-bill-wave mr-1 text-success"></i>Enregistrer un paiement</h6>
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

                $comptesInstitutionList = is_iterable($comptesInstitution ?? null) ? $comptesInstitution : [];
            @endphp
            @if(count($formErrors))
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($formErrors as $err)<li>{{ $err }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('credit.remboursement.store', $demande) }}">
            @csrf

            <div class="form-group">
                <label>Échéance concernée <span class="text-danger">*</span></label>
                <select name="echeance_id" class="form-control" id="sel_echeance"
                        onchange="remplirMontant(this)">
                    <option value="">-- Sélectionner une échéance --</option>
                    @foreach($echeancier->echeances->whereIn('statut', ['EN_ATTENTE','EN_RETARD','PARTIELLEMENT_PAYE']) as $e)
                    <option value="{{ $e->id }}"
                            data-capital="{{ $e->montant_capital }}"
                            data-interet="{{ $e->montant_interet }}"
                            data-total="{{ $e->montant_total }}"
                            {{ old('echeance_id') == $e->id ? 'selected' : '' }}>
                        # {{ $e->numero_echeance }}
                        – {{ optional($e->date_echeance)->format('d/m/Y') }}
                        – {{ number_format($e->montant_total, 2, ',', ' ') }} {{ $demande->devise }}
                        @if($e->statut === 'EN_RETARD') [RETARD] @endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div id="zone_detail_ech" class="alert alert-light border small" style="display:none">
                Capital : <span id="lbl_capital">–</span> &nbsp;|&nbsp;
                Intérêt : <span id="lbl_interet">–</span> &nbsp;|&nbsp;
                <strong>Total : <span id="lbl_total">–</span></strong>
            </div>

            <div class="form-group">
                <label>Montant reçu <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="montant_recu" id="inp_montant_recu"
                           class="form-control" step="0.01" min="0.01" required
                           value="{{ old('montant_recu') }}">
                    <div class="input-group-append"><span class="input-group-text">{{ $demande->devise }}</span></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Dont capital <span class="text-danger">*</span></label>
                    <input type="number" name="dont_capital" id="inp_dont_capital" class="form-control" step="0.01" min="0" required value="{{ old('dont_capital', 0) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Dont intérêt <span class="text-danger">*</span></label>
                    <input type="number" name="dont_interet" id="inp_dont_interet" class="form-control" step="0.01" min="0" required value="{{ old('dont_interet', 0) }}">
                </div>
            </div>

            <input type="hidden" name="dont_penalite" value="{{ old('dont_penalite', 0) }}">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Date de paiement <span class="text-danger">*</span></label>
                    <input type="date" name="date_paiement" class="form-control" required
                           value="{{ old('date_paiement', date('Y-m-d')) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Type de remboursement</label>
                    <select name="type_remboursement" class="form-control">
                        <option value="ECHEANCE" {{ old('type_remboursement','ECHEANCE')=='ECHEANCE'?'selected':'' }}>Échéance normale</option>
                        <option value="PARTIEL" {{ old('type_remboursement')=='PARTIEL'?'selected':'' }}>Paiement partiel</option>
                        <option value="ANTICIPE" {{ old('type_remboursement')=='ANTICIPE'?'selected':'' }}>Remboursement anticipé</option>
                        <option value="PENALITE" {{ old('type_remboursement')=='PENALITE'?'selected':'' }}>Pénalité</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Référence de paiement</label>
                <input type="text" name="reference_caisse" class="form-control"
                       value="{{ old('reference_caisse') }}" maxlength="100"
                       placeholder="N° reçu, réf. virement…">
            </div>

            <div class="form-group">
                <label>Compte de destination</label>
                <select name="compte_destination_id" class="form-control">
                    <option value="">-- Compte de caisse/ressources --</option>
                    @foreach($comptesInstitutionList as $c)
                    @if(is_object($c))
                    <option value="{{ $c->code_compte }}" {{ old('compte_destination_id')==$c->code_compte?'selected':'' }}>
                        {{ $c->code_compte }} – {{ $c->type }}
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Observations</label>
                <textarea name="observations" class="form-control" rows="2">{{ old('observations') }}</textarea>
            </div>

            <button type="submit" class="btn btn-success btn-block">
                <i class="fas fa-save mr-1"></i>Enregistrer le paiement
            </button>
            </form>
        </div>
    </div>

</div>

{{-- ── Panneau droit : échéancier complet ─────────────────── --}}
<div class="col-md-7">
<div class="card card-outline card-info">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-table mr-1"></i>Échéancier de remboursement</h6>
        <a href="{{ route('credit.pdf.echeancier', $demande) }}" target="_blank"
           class="btn btn-xs btn-outline-danger">
            <i class="fas fa-print mr-1"></i>PDF
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-sm table-hover small mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>#</th><th>Date</th>
                    <th class="text-right">Capital</th>
                    <th class="text-right">Intérêt</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Cap. restant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            @php
                $sc = ['EN_ATTENTE'=>'secondary','PAYE'=>'success','PARTIELLEMENT_PAYE'=>'info','EN_RETARD'=>'danger'];
                $totalPaye = 0; $totalRestant = 0;
            @endphp
            @foreach($echeancier->echeances as $e)
            @php
                if($e->statut === 'PAYE') $totalPaye += $e->montant_total;
                else $totalRestant += $e->montant_total;
            @endphp
            <tr class="{{ $e->statut === 'EN_RETARD' ? 'table-danger' : ($e->statut === 'PAYE' ? 'table-success' : ($e->statut === 'PARTIELLEMENT_PAYE' ? 'table-info' : '')) }}">
                <td>{{ $e->numero_echeance }}</td>
                <td class="text-nowrap">{{ optional($e->date_echeance)->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($e->montant_capital, 2, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($e->montant_interet, 2, ',', ' ') }}</td>
                <td class="text-right font-weight-bold">{{ number_format($e->montant_total, 2, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($e->capital_restant_fin, 2, ',', ' ') }}</td>
                <td>
                    <span class="badge badge-{{ $sc[$e->statut] ?? 'secondary' }}">
                        {{ str_replace('_',' ', $e->statut) }}
                    </span>
                </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <td colspan="4" class="text-right"><strong>Total payé / Restant :</strong></td>
                    <td class="text-right text-success"><strong>{{ number_format($totalPaye, 2, ',', ' ') }}</strong></td>
                    <td class="text-right text-danger"><strong>{{ number_format($totalRestant, 2, ',', ' ') }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
</div>

{{-- Historique remboursements --}}
@if($demande->remboursements->count())
<div class="card card-outline card-secondary">
    <div class="card-header py-2">
        <h6 class="mb-0"><i class="fas fa-history mr-1"></i>Historique des paiements enregistrés</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-xs table-hover small mb-0">
            <thead class="bg-light"><tr>
                <th>Date</th><th>Montant reçu</th><th>Mode</th><th>Réf.</th><th>Caissier</th>
            </tr></thead>
            <tbody>
            @foreach($demande->remboursements->sortByDesc('date_paiement') as $r)
            <tr>
                <td>{{ optional($r->date_paiement)->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($r->montant_recu, 2, ',', ' ') }}</td>
                <td>{{ $r->mode_paiement ?? '–' }}</td>
                <td><small>{{ $r->reference_paiement ?? '–' }}</small></td>
                <td><small>{{ optional($r->caissier)->nom_complet ?? '–' }}</small></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

</div>{{-- /.col-md-7 --}}

</div>{{-- /.row --}}
</div>
</section>
@endsection

@section('scripts')
<script>
function remplirMontant(sel) {
    const opt = sel.options[sel.selectedIndex];
    const zone = document.getElementById('zone_detail_ech');
    if (!opt.value) {
        zone.style.display = 'none';
        return;
    }
    const capital = parseFloat(opt.dataset.capital);
    const interet = parseFloat(opt.dataset.interet);
    const total   = parseFloat(opt.dataset.total);
    document.getElementById('lbl_capital').textContent = capital.toLocaleString('fr',{minimumFractionDigits:2});
    document.getElementById('lbl_interet').textContent = interet.toLocaleString('fr',{minimumFractionDigits:2});
    document.getElementById('lbl_total').textContent   = total.toLocaleString('fr',{minimumFractionDigits:2});
    document.getElementById('inp_montant_recu').value  = total.toFixed(2);
    document.getElementById('inp_dont_capital').value  = capital.toFixed(2);
    document.getElementById('inp_dont_interet').value  = interet.toFixed(2);
    zone.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('sel_echeance');
    if (sel.value) remplirMontant(sel);
});
</script>
@endsection

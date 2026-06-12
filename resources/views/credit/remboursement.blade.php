@extends('layouts.app')

@section('page_title', 'Remboursement – ' . $demande->numero_dossier)
@section('breadcrumb_parent', 'Caisse / Guichet')
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
        <a href="{{ route('caisses.remboursements.liste') }}" class="btn btn-sm btn-outline-secondary ml-1">
            <i class="fas fa-list mr-1"></i>Liste remboursements
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

@if($guichet)
<div class="d-flex align-items-center flex-wrap gap-2 mb-3 operation-soldes-bar">
    <small class="text-muted text-uppercase" style="letter-spacing:.08em;">
        <i class="fas fa-wallet mr-1"></i> Soldes :
    </small>
    @foreach($guichet->soldes->sortBy('devise_code') as $s)
    <span class="badge badge-pill px-3 py-2 solde-pill" id="soldePill_{{ $s->devise_code }}"
          style="font-size:.92rem; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15);">
        <strong>{{ $s->devise_code }}</strong>
        <span class="solde-val">{{ number_format($s->solde_en_caisse, 2, ',', ' ') }}</span>
    </span>
    @endforeach
    <span class="badge badge-pill px-2 py-2 ml-1"
          style="background:rgba(23,162,184,.2); border:1px solid rgba(23,162,184,.4); font-size:.85rem;">
        <i class="fas fa-{{ $guichet->type_guichet === 'MOBILE' ? 'mobile-alt' : 'desktop' }} mr-1 text-info"></i>
        {{ $guichet->type_guichet }}
    </span>
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

    {{-- Solde RMB du client --}}
    @if($soldeRmbActuel > 0)
    <div class="alert alert-info py-2 mb-3">
        <i class="fas fa-wallet mr-1"></i>
        <strong>Solde RMB actuel du client :</strong>
        <span class="badge badge-info badge-pill ml-1" style="font-size:1rem;">
            {{ number_format($soldeRmbActuel, 2, ',', ' ') }} {{ $demande->devise }}
        </span>
        <small class="d-block mt-1 text-muted">
            <i class="fas fa-info-circle mr-1"></i>Ce montant sera ajouté à ce que le client verse pour calculer le total disponible.
        </small>
    </div>
    @else
    <div class="alert alert-warning py-2 mb-3">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        <strong>Solde RMB du client : 0,00 {{ $demande->devise }}</strong>
        <small class="d-block mt-1 text-muted">
            Le client n'a pas de solde sur son compte RMB. Seul le montant versé sera utilisé.
        </small>
    </div>
    @endif

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

            <form method="POST" action="{{ route('caisses.remboursement.store', $demande) }}" id="formRemboursement">
            @csrf

            {{-- Échéance cible automatique (masquée mais envoyée au backend) --}}
            @php
                $premiereEcheanceImpayee = $echeancesImpayees->first();
                $echeanceIdCible = $premiereEcheanceImpayee ? $premiereEcheanceImpayee->id : '';
            @endphp
            <input type="hidden" name="echeance_id" id="echeance_id_cible" value="{{ $echeanceIdCible }}">

            @if($premiereEcheanceImpayee)
            <div class="alert alert-secondary py-2 mb-3">
                <i class="fas fa-bullseye mr-1 text-primary"></i>
                <strong>Échéance ciblée automatiquement :</strong> 
                Échéance n°{{ $premiereEcheanceImpayee->numero_echeance }} du {{ \Carbon\Carbon::parse($premiereEcheanceImpayee->date_echeance)->format('d/m/Y') }}
                (Reste à payer : <strong>{{ number_format(max(0, ($premiereEcheanceImpayee->montant_capital + $premiereEcheanceImpayee->montant_interet) - ($premiereEcheanceImpayee->montant_paye ?? 0)), 2, ',', ' ') }} {{ $demande->devise }}</strong>)
            </div>
            @else
            <div class="alert alert-success py-2 mb-3">
                <i class="fas fa-check-circle mr-1"></i>
                <strong>Toutes les échéances sont soldées.</strong> Aucun remboursement n'est nécessaire.
            </div>
            @endif

            <div class="form-group" style="display:none;">
                <label>Échéance concernée <span class="text-danger">*</span></label>
                <select name="echeance_id_old" class="form-control" id="sel_echeance" disabled>
                    <option value="{{ $echeanceIdCible }}" selected>Automatique</option>
                </select>
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

            <input type="hidden" name="dont_interet" id="inp_dont_interet" value="0">
            <input type="hidden" name="dont_capital" id="inp_dont_capital" value="0">
            <input type="hidden" name="dont_penalite" value="0">
            <input type="hidden" name="type_remboursement" id="inp_type_remb" value="ECHEANCE">
            <input type="hidden" name="montant_a_appliquer" id="inp_montant_a_appliquer" value="{{ old('montant_recu') }}">

            <div class="form-group">
                <label>Date de paiement <span class="text-danger">*</span></label>
                <input type="date" name="date_paiement" class="form-control" required
                       value="{{ old('date_paiement', date('Y-m-d')) }}">
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
            @if($echeancier)
            @php
                $sc = ['EN_ATTENTE'=>'secondary','PAYE'=>'success','PARTIELLEMENT_PAYE'=>'info','EN_RETARD'=>'danger'];
                $totalPaye = 0; $totalRestant = 0;
                $devise = $demande->devise ?? 'CDF';
            @endphp
            @foreach($echeancier->echeances as $e)
            @php
                if($e->statut === 'PAYE') {
                    $totalPaye += $e->montant_total;
                } elseif($e->statut === 'PARTIELLEMENT_PAYE') {
                    $totalPaye += $e->montant_paye ?? 0;
                    $totalRestant += ($e->montant_total - ($e->montant_paye ?? 0));
                } else {
                    $totalRestant += $e->montant_total;
                }
            @endphp
            <tr style="background:{{ $e->statut === 'EN_RETARD' ? 'rgba(220,53,69,0.15)' : ($e->statut === 'PAYE' ? 'rgba(40,167,69,0.15)' : ($e->statut === 'PARTIELLEMENT_PAYE' ? 'rgba(23,162,184,0.15)' : 'transparent')) }}">
                <td>{{ $e->numero_echeance }}</td>
                <td class="text-nowrap">{{ optional($e->date_echeance)->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($e->montant_capital, 2, ',', ' ') }} <small class="text-muted">{{ $devise }}</small></td>
                <td class="text-right">{{ number_format($e->montant_interet, 2, ',', ' ') }} <small class="text-muted">{{ $devise }}</small></td>
                <td class="text-right font-weight-bold">{{ number_format($e->montant_total, 2, ',', ' ') }} <small class="text-muted">{{ $devise }}</small></td>
                <td class="text-right">{{ number_format($e->capital_restant_fin, 2, ',', ' ') }} <small class="text-muted">{{ $devise }}</small></td>
                <td>
                    <span class="badge badge-{{ $sc[$e->statut] ?? 'secondary' }}">
                        {{ str_replace('_',' ', $e->statut) }}
                    </span>
                </td>
            </tr>
            @endforeach
            @endif
            </tbody>
            <tfoot style="background:#1a202c">
                <tr>
                    <td colspan="4" class="text-right"><strong>Total payé / Restant :</strong></td>
                    <td class="text-right text-success"><strong>{{ number_format($totalPaye, 2, ',', ' ') }} <small>{{ $devise }}</small></strong></td>
                    <td class="text-right text-danger"><strong>{{ number_format($totalRestant, 2, ',', ' ') }} <small>{{ $devise }}</small></strong></td>
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

@push('js')
<script>
const echeancesImpayees = @json($echeancesImpayees ?? []);
const soldeRmbActuel    = {{ $soldeRmbActuel ?? 0 }};

/**
 * WRAPPER PROMISE pour showUniversalConfirm.
 * Résout TRUE = l'utilisateur a confirmé.
 * Résout FALSE = l'utilisateur a annulé ou fermé la modale.
 */
function askModal(message, options) {
    return new Promise(function(resolve) {
        var confirmed = false;

        showUniversalConfirm(message, function() {
            confirmed = true;
        }, $.extend({ showWarning: false }, options || {}));

        $('#universalConfirmModal').one('hidden.bs.modal', function() {
            resolve(confirmed);
        });
    });
}

(function() {
    var form = document.getElementById('formRemboursement');
    if (!form) return;

    var btn = form.querySelector('button[type="submit"]');
    if (!btn) return;

    // Bloquer la soumission native du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
    });

    // Toute la logique est sur le clic du bouton (compatible async)
    btn.addEventListener('click', async function(e) {
        e.preventDefault();

        var montantRecu = parseFloat(document.getElementById('inp_montant_recu').value) || 0;
        if (montantRecu <= 0) {
            showSystemMessage('warning', 'Veuillez saisir un montant valide.');
            return;
        }

        // Filtrer les échéances ayant un solde dû
        var echeancesValides = echeancesImpayees.filter(function(ech) {
            var mt = parseFloat(ech.capital_echeance || 0) + parseFloat(ech.interet_echeance || 0);
            var dp = parseFloat(ech.montant_paye) || 0;
            return (mt - dp) > 0.01;
        });

        var totalDisponible    = soldeRmbActuel + montantRecu;
        var montantTotalTraite = 0;

        function soumettre() {
            document.getElementById('inp_montant_a_appliquer').value = montantTotalTraite.toFixed(2);
            form.submit();
        }

        // ── ÉTAPE 1 : Dépôt simple OU Règlement d'échéancier ? ─────────────────────
        var msgRmb = soldeRmbActuel > 0.01
            ? '<br><small class="text-info">dont <strong>' + soldeRmbActuel.toFixed(2) + ' {{ $demande->devise }}</strong> depuis le compte RMB</small>'
            : '';

        var etape1 = await askModal(
            'Montant disponible : <strong>' + totalDisponible.toFixed(2) + ' {{ $demande->devise }}</strong>' + msgRmb + '<br><br>Que souhaitez-vous faire ?',
            {
                title    : 'Dépôt ou Remboursement ?',
                btnLabel : "Régler l'échéancier",
                btnClass : 'btn-success',
                icon     : 'fas fa-exchange-alt',
                headerClass: 'bg-primary text-white'
            }
        );

        if (!etape1) {
            // Dépôt simple : tout crédité sur le RMB par le backend
            soumettre();
            return;
        }

        // ── ÉTAPE 2 : Régler automatiquement la première échéance ──────────────────
        if (echeancesValides.length === 0) {
            showSystemMessage('info', 'Aucune échéance en attente. Dépôt sur le compte RMB.');
            soumettre();
            return;
        }

        var ech0     = echeancesValides[0];
        var mt0      = parseFloat(ech0.capital_echeance || 0) + parseFloat(ech0.interet_echeance || 0);
        var dp0      = parseFloat(ech0.montant_paye) || 0;
        var resteDu0 = Math.max(0, mt0 - dp0);
        var aApp0    = Math.min(totalDisponible, resteDu0);

        montantTotalTraite += aApp0;
        totalDisponible    -= aApp0;

        // ── ÉTAPE 3 : Proposer chaque échéance suivante (async/await) ───────────────
        for (var i = 1; i < echeancesValides.length; i++) {
            if (totalDisponible <= 0.01) break;

            var ech     = echeancesValides[i];
            var mt      = parseFloat(ech.capital_echeance || 0) + parseFloat(ech.interet_echeance || 0);
            var dp      = parseFloat(ech.montant_paye) || 0;
            var resteDu = Math.max(0, mt - dp);
            if (resteDu <= 0.01) continue;

            var aMax       = Math.min(totalDisponible, resteDu);
            var dateEch    = new Date(ech.date_echeance).toLocaleDateString('fr-FR');

            var continuer = await askModal(
                'Il reste <strong>' + totalDisponible.toFixed(2) + ' {{ $demande->devise }}</strong> disponible.<br><br>' +
                'Appliquer <strong>' + aMax.toFixed(2) + ' {{ $demande->devise }}</strong> ' +
                "à l'échéance n°<strong>" + ech.numero_echeance + '</strong> (Date : ' + dateEch + ') ?',
                {
                    title    : 'Remboursement anticipé',
                    btnLabel : 'Oui, appliquer',
                    btnClass : 'btn-success',
                    icon     : 'fas fa-forward',
                    headerClass: 'bg-info text-white'
                }
            );

            if (!continuer) break; // NON → reste crédité sur le RMB (Étape 4)

            montantTotalTraite += aMax;
            totalDisponible    -= aMax;
        }

        // ── ÉTAPE 4 : Soumettre — le backend crédite automatiquement le reste sur RMB
        soumettre();
    });
})();
</script>
@endpush
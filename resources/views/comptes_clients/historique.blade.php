@extends('layouts.app')

@section('page_title', 'Historique — ' . $compte->code_compte)
@section('breadcrumb_parent', 'Gestion des comptes')
@section('breadcrumb', 'Historique des mouvements')

@push('css')
<style>
    .badge-DEPOT        { background:#d1fae5; color:#065f46; }
    .badge-RETRAIT      { background:#fee2e2; color:#991b1b; }
    .badge-VIREMENT     { background:#dbeafe; color:#1e40af; }
    .badge-CHANGE       { background:#fef3c7; color:#92400e; }
    .badge-PAIEMENT     { background:#ede9fe; color:#5b21b6; }
    .badge-REMBOURSEMENT{ background:#fce7f3; color:#9d174d; }
    .badge-CONFIRME     { background:#d1fae5; color:#065f46; }
    .badge-ANNULE       { background:#fee2e2; color:#991b1b; }
    .type-badge         { font-size:.78em; font-weight:600; padding:3px 9px; border-radius:20px; letter-spacing:.4px; }
    .compte-info-card   { border:1px solid #4b5563; background:#1f2937; }
    .montant-depot      { color:#059669; font-weight:700; }
    .montant-retrait    { color:#dc2626; font-weight:700; }
    .filter-bar         { background:#1f2937; border:1px solid #4b5563; border-radius:10px; padding:12px 16px; margin-bottom:16px; }
    .filter-bar label   { color:#d1d5db; }
    .filter-bar .form-control { background:#111827; color:#f9fafb; border-color:#4b5563; }
    .filter-bar .form-control:focus { border-color:#60a5fa; box-shadow:none; }
    .table thead        { background:#0f172a !important; }
    .table thead th     { color:#cbd5e1 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ── En-tête compte ──────────────────────────────────────────────── --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm compte-info-card">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 font-weight-bold text-white">
                                {{ $compte->code_compte }}
                            </h5>
                            <div class="text-light small mt-1">
                                <i class="fas fa-user mr-1"></i>
                                {{ $compte->client->nom ?? '—' }} {{ $compte->client->postnom ?? '' }} {{ $compte->client->prenom ?? '' }}
                                &nbsp;&bull;&nbsp;
                                <span class="badge badge-secondary">{{ $compte->type }}</span>
                                &nbsp;&bull;&nbsp;
                                Devise&nbsp;: <strong>{{ $compte->devise }}</strong>
                                &nbsp;&bull;&nbsp;
                                Solde&nbsp;: <strong class="{{ $compte->solde_reel >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($compte->solde_reel, 2, ',', ' ') }}
                                    {{ $devise->symbole ?? $compte->devise }}
                                </strong>
                            </div>
                        </div>
                        <div class="d-flex mt-2 mt-md-0" style="gap:6px;">
                            <a href="{{ route('comptes.releve.pdf', ['code_compte' => $compte->code_compte, 'date_debut' => request('date_debut', now()->startOfMonth()->toDateString()), 'date_fin' => request('date_fin', now()->toDateString())]) }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="fas fa-file-pdf mr-1"></i>Imprimer relevé bancaire
                            </a>
                            <a href="{{ route('comptes.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres ──────────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('comptes.historique', $compte->code_compte) }}" id="formFiltre">
        <div class="filter-bar">
            <div class="row g-2 align-items-end">
                <div class="col-sm-3 col-6">
                    <label class="small font-weight-bold mb-1">Du</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                           value="{{ request('date_debut', now()->startOfMonth()->toDateString()) }}">
                </div>
                <div class="col-sm-3 col-6">
                    <label class="small font-weight-bold mb-1">Au</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                           value="{{ request('date_fin', now()->toDateString()) }}">
                </div>
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Type</label>
                    <select name="type" class="form-control form-control-sm">
                        <option value="tous" @selected(request('type','tous')==='tous')>Tous</option>
                        @foreach(($operationTypeOptions ?? []) as $t)
                            <option value="{{ $t['value'] }}" @selected(request('type') === $t['value'])>{{ $t['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Statut</label>
                    <select name="statut" class="form-control form-control-sm">
                        <option value="tous" @selected(request('statut','tous')==='tous')>Tous</option>
                        <option value="CONFIRME" @selected(request('statut')==='CONFIRME')>Confirmé</option>
                        <option value="ANNULE"   @selected(request('statut')==='ANNULE')>Annulé</option>
                    </select>
                </div>
                <div class="col-sm-2 col-12 d-flex gap-1" style="gap:6px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter mr-1"></i>Filtrer
                    </button>
                    <a href="{{ route('comptes.historique', $compte->code_compte) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Tableau des mouvements ──────────────────────────────────────── --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center py-2" style="background:#1e293b;">
            <span class="font-weight-bold text-white">
                <i class="fas fa-history mr-2"></i>Mouvements
                <span class="badge badge-light ml-1">{{ $transactions->total() }}</span>
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.91em;">
                    <thead style="background:#f1f5f9;">
                        <tr class="text-uppercase text-muted" style="font-size:.78em; letter-spacing:.4px;">
                            <th class="pl-3">Date / Heure</th>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Guichet</th>
                            <th class="text-right">Débit</th>
                            <th class="text-right">Crédit</th>
                            <th>Statut</th>
                            <th>Observations</th>
                            <th class="text-center">Bordereau</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        @php
                            $estVirementRecu = $t->type === 'VIREMENT' && $t->compte_dest_code === $compte->code_compte;
                            $estVirementEnvoye = $t->type === 'VIREMENT' && $t->compte_code === $compte->code_compte;
                            $isCredit = in_array($t->type, ['DEPOT','PAIEMENT']) || $estVirementRecu;
                            $montantAffiche = $estVirementRecu
                                ? number_format($t->montant_dest, 2, ',', ' ') . ' ' . $t->devise_dest
                                : number_format($t->montant, 2, ',', ' ') . ' ' . ($devise->symbole ?? $compte->devise);
                        @endphp
                        <tr class="{{ $t->statut === 'ANNULE' ? 'table-light text-muted' : '' }}">
                            <td class="pl-3 text-nowrap">
                                {{ \Carbon\Carbon::parse($t->date_operation)->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($t->date_operation)->format('H:i:s') }}</small>
                            </td>
                            <td class="text-nowrap">
                                <code style="font-size:.82em;">{{ $t->reference ?? '—' }}</code>
                            </td>
                            <td>
                                <span class="type-badge badge-{{ $t->type }}">{{ $t->type }}</span>
                                @if($estVirementRecu)
                                    <br><small class="text-success"><i class="fas fa-arrow-down mr-1"></i>Reçu de {{ $t->compte_code }}</small>
                                @elseif($estVirementEnvoye)
                                    <br><small class="text-danger"><i class="fas fa-arrow-up mr-1"></i>Envoyé vers {{ $t->compte_dest_code }}</small>
                                @endif
                            </td>
                            <td class="text-nowrap small">
                                {{ $t->guichet->intitule ?? $t->guichet_id ?? '—' }}
                            </td>
                            <td class="text-right text-nowrap">
                                @if(!$isCredit && $t->statut !== 'ANNULE')
                                    <span class="montant-retrait">{{ $montantAffiche }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-right text-nowrap">
                                @if($isCredit && $t->statut !== 'ANNULE')
                                    <span class="montant-depot">{{ $montantAffiche }}</span>
                                @elseif($t->statut !== 'ANNULE')
                                    <span class="text-muted">—</span>
                                @else
                                    <del class="text-muted small">{{ $montantAffiche }}</del>
                                @endif
                            </td>
                            <td>
                                <span class="type-badge badge-{{ $t->statut }}">
                                    {{ $t->statut === 'CONFIRME' ? 'Confirmé' : 'Annulé' }}
                                </span>
                            </td>
                            <td class="small text-muted" style="max-width:180px; word-break:break-word;">
                                {{ $t->observations ?? '—' }}
                            </td>
                            <td class="text-center text-nowrap">
                                @if(auth()->user()?->hasPermission('EBEN-PER11') && !empty($t->id) && !empty($t->reference) && (str_starts_with((string) $t->reference, 'DEB-') || str_starts_with((string) $t->reference, 'OP-')))
                                    <a href="{{ route('caisses.operations.bordereau', ['id' => $t->id]) }}" class="btn btn-xs btn-outline-primary" target="_blank" rel="noopener" title="Imprimer / Réimprimer le bordereau">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Aucun mouvement pour les critères sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer py-2 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Affichage {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }}
                sur {{ $transactions->total() }} mouvements
            </small>
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

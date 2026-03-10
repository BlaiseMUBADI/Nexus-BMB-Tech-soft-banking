@extends('layouts.app')

@section('page_title', 'Apports Agents Mobiles')
@section('breadcrumb_parent', 'Trésorerie')
@section('breadcrumb', 'Rapport Agents Mobiles')

@push('css')
<style>
    .agent-card         { border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; margin-bottom:16px; }
    .agent-card-header  { background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 100%); color:#fff; padding:10px 16px; }
    .devise-row         { display:flex; gap:10px; flex-wrap:wrap; margin-top:8px; }
    .devise-chip        { background:#f1f5f9; border:1px solid #e2e8f0; border-radius:8px; padding:6px 12px; font-size:.87em; }
    .filter-card        { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:14px 18px; margin-bottom:18px; }
    .stat-block         { background:#fff; border-radius:10px; border:1px solid #e5e7eb; padding:14px; text-align:center; }
    .text-entree        { color:#059669; font-weight:700; }
    .text-sortie        { color:#dc2626; font-weight:700; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ── Barre de filtres ──────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('tresorerie.agents.mobiles') }}" id="formFiltre">
        <div class="filter-card">
            <div class="row g-2 align-items-end">
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Du</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                           value="{{ request('date_debut', now()->startOfMonth()->toDateString()) }}">
                </div>
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Au</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                           value="{{ request('date_fin', now()->toDateString()) }}">
                </div>
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Agent</label>
                    <select name="agent_matricule" class="form-control form-control-sm">
                        <option value="">Tous les agents</option>
                        @foreach($agents as $ag)
                            <option value="{{ $ag->matricule }}" @selected(request('agent_matricule') === $ag->matricule)>
                                {{ $ag->prenom }} {{ $ag->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Zone</label>
                    <select name="code_zone" class="form-control form-control-sm">
                        <option value="">Toutes les zones</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->code_zone }}" @selected(request('code_zone') === $z->code_zone)>
                                {{ $z->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Type compte</label>
                    <select name="type_compte" class="form-control form-control-sm">
                        <option value="tous" @selected(request('type_compte','tous')==='tous')>Tous</option>
                        @foreach(['CC','RMB','GTC','DAT','EAV'] as $t)
                            <option value="{{ $t }}" @selected(request('type_compte') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Type opération</label>
                    <select name="type_operation" class="form-control form-control-sm">
                        <option value="tous" @selected(request('type_operation','tous')==='tous')>Tous</option>
                        @foreach(['DEPOT','RETRAIT','VIREMENT','CHANGE','PAIEMENT','REMBOURSEMENT'] as $t)
                            <option value="{{ $t }}" @selected(request('type_operation') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 col-6">
                    <label class="small font-weight-bold mb-1">Devise</label>
                    <select name="devise_code" class="form-control form-control-sm">
                        <option value="tous" @selected(request('devise_code','tous')==='tous')>Toutes</option>
                        @foreach($devises as $d)
                            <option value="{{ $d->code_iso }}" @selected(request('devise_code') === $d->code_iso)>
                                {{ $d->code_iso }} — {{ $d->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 col-12 d-flex" style="gap:6px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter mr-1"></i>Filtrer
                    </button>
                    <a href="{{ route('tresorerie.agents.mobiles') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                <div class="col-sm-2 col-12">
                    <button type="button" id="btnImprimer" class="btn btn-sm btn-danger w-100">
                        <i class="fas fa-file-pdf mr-1"></i>Imprimer PDF
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Statistiques globales ───────────────────────────────────────── --}}
    @php
        $totalOps      = $transactions->count();
        $totalEntrees  = $transactions->whereIn('type', ['DEPOT','PAIEMENT'])->sum('montant');
        $totalSorties  = $transactions->whereIn('type', ['RETRAIT','REMBOURSEMENT'])->sum('montant');
        $nbAgents      = $parAgent->count();
    @endphp
    <div class="row mb-3">
        <div class="col-sm-3 col-6 mb-2">
            <div class="stat-block">
                <div class="text-muted small">Agents actifs</div>
                <div class="h4 font-weight-bold text-primary mb-0">{{ $nbAgents }}</div>
            </div>
        </div>
        <div class="col-sm-3 col-6 mb-2">
            <div class="stat-block">
                <div class="text-muted small">Total opérations</div>
                <div class="h4 font-weight-bold mb-0">{{ $totalOps }}</div>
            </div>
        </div>
        <div class="col-sm-3 col-6 mb-2">
            <div class="stat-block">
                <div class="text-muted small">Total entrées</div>
                <div class="h5 font-weight-bold text-entree mb-0">{{ number_format($totalEntrees, 2, ',', ' ') }}</div>
            </div>
        </div>
        <div class="col-sm-3 col-6 mb-2">
            <div class="stat-block">
                <div class="text-muted small">Total sorties</div>
                <div class="h5 font-weight-bold text-sortie mb-0">{{ number_format($totalSorties, 2, ',', ' ') }}</div>
            </div>
        </div>
    </div>

    {{-- ── Tableau récapitulatif par agent ──────────────────────────────── --}}
    @if($parAgent->isEmpty())
        <div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>Aucune opération trouvée pour les critères sélectionnés.</div>
    @else
    <div class="card shadow-sm mb-4">
        <div class="card-header py-2" style="background:#1e293b;">
            <span class="font-weight-bold text-white">
                <i class="fas fa-users mr-2"></i>Récapitulatif par agent
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.91em;">
                    <thead style="background:#f1f5f9;">
                        <tr class="text-uppercase text-muted" style="font-size:.78em;">
                            <th class="pl-3">Agent</th>
                            <th>Matricule</th>
                            <th>Guichet</th>
                            <th class="text-center">Nb opér.</th>
                            <th>Devise</th>
                            <th class="text-right">Entrées</th>
                            <th class="text-right">Sorties</th>
                            <th class="text-right">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parAgent as $ag)
                            @foreach($ag['par_devise'] as $i => $d)
                            <tr>
                                @if($i === 0)
                                <td class="pl-3 font-weight-bold" rowspan="{{ count($ag['par_devise']) }}">
                                    {{ $ag['nom_complet'] }}
                                </td>
                                <td class="small text-muted" rowspan="{{ count($ag['par_devise']) }}">
                                    <code>{{ $ag['matricule'] }}</code>
                                </td>
                                <td class="small" rowspan="{{ count($ag['par_devise']) }}">
                                    {{ $ag['guichet'] }}
                                </td>
                                <td class="text-center" rowspan="{{ count($ag['par_devise']) }}">
                                    <span class="badge badge-secondary">{{ $ag['nb_operations'] }}</span>
                                </td>
                                @endif
                                <td><span class="badge badge-light border">{{ $d['devise'] }}</span></td>
                                <td class="text-right text-entree text-nowrap">{{ number_format($d['total_entrees'], 2, ',', ' ') }}</td>
                                <td class="text-right text-sortie text-nowrap">{{ number_format($d['total_sorties'], 2, ',', ' ') }}</td>
                                <td class="text-right font-weight-bold text-nowrap {{ $d['net'] >= 0 ? 'text-entree' : 'text-sortie' }}">
                                    {{ number_format($d['net'] ?? ($d['total_entrees'] - $d['total_sorties']), 2, ',', ' ') }}
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Détail des opérations ─────────────────────────────────────── --}}
    <div class="card shadow-sm">
        <div class="card-header py-2" style="background:#1e293b;">
            <span class="font-weight-bold text-white">
                <i class="fas fa-list mr-2"></i>Détail des opérations
                <span class="badge badge-light ml-1">{{ $transactions->count() }}</span>
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.89em;">
                    <thead style="background:#f1f5f9;">
                        <tr class="text-uppercase text-muted" style="font-size:.77em;">
                            <th class="pl-3">Date</th>
                            <th>Référence</th>
                            <th>Agent</th>
                            <th>Guichet</th>
                            <th>Type opér.</th>
                            <th>Compte</th>
                            <th>Type cpt</th>
                            <th>Client</th>
                            <th class="text-right">Montant</th>
                            <th>Devise</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $t)
                        <tr>
                            <td class="pl-3 text-nowrap small">
                                {{ \Carbon\Carbon::parse($t->date_operation)->format('d/m/Y H:i') }}
                            </td>
                            <td><code style="font-size:.82em;">{{ $t->reference ?? '—' }}</code></td>
                            <td class="small">{{ $t->agent_matricule ?? '—' }}</td>
                            <td class="small">{{ $t->guichet?->intitule ?? '—' }}</td>
                            <td>
                                <span class="badge badge-secondary" style="font-size:.78em;">{{ $t->type }}</span>
                            </td>
                            <td class="small"><code style="font-size:.82em;">{{ $t->compte_code ?? '—' }}</code></td>
                            <td class="small">{{ $t->compte?->type ?? '—' }}</td>
                            <td class="small">
                                @if($t->compte && $t->compte->client)
                                    {{ $t->compte->client->nom }} {{ $t->compte->client->prenom ?? '' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-right font-weight-bold text-nowrap {{ in_array($t->type, ['DEPOT','PAIEMENT']) ? 'text-entree' : 'text-sortie' }}">
                                {{ number_format($t->montant, 2, ',', ' ') }}
                            </td>
                            <td><span class="badge badge-light border">{{ $t->devise_code }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('js')
<script>
(function(){
    'use strict';
    var pdfUrl = "{{ route('tresorerie.agents.mobiles.pdf') }}";

    $('#btnImprimer').on('click', function () {
        var params = $('#formFiltre').serialize();
        window.open(pdfUrl + '?' + params, '_blank');
    });
})();
</script>
@endpush

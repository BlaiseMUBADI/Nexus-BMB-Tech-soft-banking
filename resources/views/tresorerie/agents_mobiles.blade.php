@extends('layouts.app')

@section('page_title', 'Rapport Agents Terrain')
@section('breadcrumb_parent', 'Trésorerie')
@section('breadcrumb', 'Agents Terrain')

@section('content')
<div class="container-fluid">

    
    <form method="GET" action="{{ route('tresorerie.agents.mobiles') }}" id="formFiltre">
        <div class="card card-warning card-outline shadow elevation-2 mb-3">
            <div class="card-header d-flex align-items-center justify-content-between py-2">
                <h5 class="mb-0">
                    <i class="fas fa-filter mr-2 text-warning"></i>
                    <strong>Filtres</strong>
                </h5>
                <div class="d-flex" style="gap:6px;">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search mr-1"></i>Filtrer
                    </button>
                    <a href="{{ route('tresorerie.agents.mobiles') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                    <button type="button" id="btnImprimer" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i>PDF
                    </button>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <label class="small font-weight-bold mb-1">Du</label>
                        <input type="date" name="date_debut" class="form-control form-control-sm"
                               value="{{ request('date_debut', now()->startOfMonth()->toDateString()) }}">
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <label class="small font-weight-bold mb-1">Au</label>
                        <input type="date" name="date_fin" class="form-control form-control-sm"
                               value="{{ request('date_fin', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
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
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
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
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <label class="small font-weight-bold mb-1">Type compte</label>
                        <select name="type_compte" class="form-control form-control-sm">
                            <option value="tous" @selected(request('type_compte','tous')==='tous')>Tous</option>
                            @foreach(['CC','RMB','GTC','DAT','EAV'] as $tc)
                                <option value="{{ $tc }}" @selected(request('type_compte') === $tc)>{{ $tc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <label class="small font-weight-bold mb-1">Type opération</label>
                        <select name="type_operation" class="form-control form-control-sm">
                            <option value="tous" @selected(request('type_operation','tous')==='tous')>Tous</option>
                            @foreach(['DEPOT','RETRAIT','VIREMENT','CHANGE','PAIEMENT','REMBOURSEMENT'] as $to)
                                <option value="{{ $to }}" @selected(request('type_operation') === $to)>{{ $to }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
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
                </div>
            </div>
        </div>
    </form>

   
   @php
        $totalOps      = $transactions->count();
        $totalEntrees  = $transactions->whereIn('type', ['DEPOT','PAIEMENT'])->sum('montant');
        $totalSorties  = $transactions->whereIn('type', ['RETRAIT','REMBOURSEMENT'])->sum('montant');
        $nbAgents      = $parAgent->count();
    @endphp
    <div class="row mb-3">
        <div class="col-lg-3 col-sm-6 col-12 mb-2">
            <div class="small-box bg-info shadow elevation-2">
                <div class="inner">
                    <h4>{{ $nbAgents }}</h4>
                    <p>Agents actifs</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-2">
            <div class="small-box bg-primary shadow elevation-2">
                <div class="inner">
                    <h4>{{ $totalOps }}</h4>
                    <p>Total opérations</p>
                </div>
                <div class="icon"><i class="fas fa-exchange-alt"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-2">
            <div class="small-box bg-success shadow elevation-2">
                <div class="inner">
                    <h4>{{ number_format($totalEntrees, 2, ',', ' ') }}</h4>
                    <p>Total entrées</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-2">
            <div class="small-box bg-danger shadow elevation-2">
                <div class="inner">
                    <h4>{{ number_format($totalSorties, 2, ',', ' ') }}</h4>
                    <p>Total sorties</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-up"></i></div>
            </div>
        </div>
    </div>

    
    @if($parAgent->isEmpty())
        <div class="alert alert-info shadow elevation-1">
            <i class="fas fa-info-circle mr-2"></i>Aucune opération trouvée pour les critères sélectionnés.
        </div>
    @else
    <div class="card card-outline card-primary shadow elevation-2 mb-3">
        <div class="card-header d-flex align-items-center justify-content-between py-2">
            <h5 class="mb-0">
                <i class="fas fa-users mr-2 text-primary"></i>
                <strong>Récapitulatif par agent</strong>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-0" style="font-size:.91em;">
                    <thead class="thead-light">
                        <tr class="text-uppercase text-muted" style="font-size:.78em;">
                            <th class="pl-3">Agent</th>
                            <th>Matricule</th>
                            <th>Guichet</th>
                            <th class="text-center">Nb opér.</th>
                            <th>Devise</th>
                            <th class="text-right">Entrées</th>
                            <th class="text-right">Sorties</th>
                            <th class="text-right pr-3">Net</th>
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
                                <td class="text-right text-success text-nowrap font-weight-bold">{{ number_format($d['total_entrees'], 2, ',', ' ') }}</td>
                                <td class="text-right text-danger text-nowrap font-weight-bold">{{ number_format($d['total_sorties'], 2, ',', ' ') }}</td>
                                <td class="text-right font-weight-bold text-nowrap pr-3 {{ ($d['net'] ?? ($d['total_entrees'] - $d['total_sorties'])) >= 0 ? 'text-success' : 'text-danger' }}">
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

    
    <div class="card card-outline card-secondary shadow elevation-2">
        <div class="card-header d-flex align-items-center justify-content-between py-2">
            <h5 class="mb-0">
                <i class="fas fa-list mr-2 text-secondary"></i>
                <strong>Détail des opérations</strong>
                <span class="badge badge-primary ml-2">{{ $transactions->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-0" style="font-size:.89em;">
                    <thead class="thead-light">
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
                            <th class="pr-3">Devise</th>
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
                                <span class="badge badge-{{ in_array($t->type, ['DEPOT','PAIEMENT']) ? 'success' : (in_array($t->type, ['RETRAIT','REMBOURSEMENT']) ? 'danger' : 'info') }}" style="font-size:.78em;">
                                    {{ $t->type }}
                                </span>
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
                            <td class="text-right font-weight-bold text-nowrap {{ in_array($t->type, ['DEPOT','PAIEMENT']) ? 'text-success' : 'text-danger' }}">
                                {{ number_format($t->montant, 2, ',', ' ') }}
                            </td>
                            <td class="pr-3"><span class="badge badge-light border">{{ $t->devise_code }}</span></td>
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

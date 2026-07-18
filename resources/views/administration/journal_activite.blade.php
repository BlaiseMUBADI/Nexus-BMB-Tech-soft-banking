@extends('layouts.app')

@section('page_title', 'Journal d\'activité')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Journal d\'activité')

@section('content')
<section class="content">
<div class="container-fluid">

    <div class="alert alert-info py-2">
        <i class="fas fa-info-circle mr-2"></i>
        Journal transversal : <strong>Crédit</strong> (workflow complet), <strong>Caisse</strong> (opérations/annulations), <strong>Client</strong> et <strong>Compte</strong> (création/modification/suppression). Les modules RH et Trésorerie ne sont pas encore tracés.
    </div>

    <div class="card mb-3">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0"><i class="fas fa-filter mr-2"></i>Filtres</h6>
        </div>
        <div class="card-body pb-2">
            <form method="GET" action="{{ route('administration.journal_activite') }}">
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-0">Module</label>
                        <select name="module" class="form-control form-control-sm">
                            <option value="">Tous</option>
                            @foreach($modules as $code => $label)
                                <option value="{{ $code }}" {{ request('module') == $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-0">Type d'action</label>
                        <select name="type_action" class="form-control form-control-sm">
                            <option value="">Toutes</option>
                            @foreach($typesAction as $code => $label)
                                <option value="{{ $code }}" {{ request('type_action') == $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-0">Agent</label>
                        <select name="acteur_matricule" class="form-control form-control-sm">
                            <option value="">Tous</option>
                            @foreach($agents as $a)
                                <option value="{{ $a->matricule }}" {{ request('acteur_matricule') == $a->matricule ? 'selected' : '' }}>{{ $a->nom }} {{ $a->prenom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-0">N° Dossier (crédit)</label>
                        <input type="text" name="dossier" class="form-control form-control-sm" value="{{ request('dossier') }}" placeholder="CRD-...">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-0">Du</label>
                        <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-0">Au</label>
                        <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ request('date_fin') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm btn-primary mr-1"><i class="fas fa-search mr-1"></i>Filtrer</button>
                        <a href="{{ route('administration.journal_activite') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times mr-1"></i>Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-history mr-2 text-warning"></i>Historique des actions <span class="badge badge-light">{{ $logs->total() }}</span></h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date/Heure</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Référence</th>
                            <th>Agent</th>
                            <th>Détails</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        @php
                            $moduleColors = ['CREDIT' => 'warning', 'CAISSE' => 'success', 'CLIENT' => 'primary', 'COMPTE' => 'info'];
                            $moduleColor = $moduleColors[$log['module']] ?? 'secondary';
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($log['date'])->format('d/m/Y H:i') }}</td>
                            <td><span class="badge badge-{{ $moduleColor }}">{{ $modules[$log['module']] ?? $log['module'] }}</span></td>
                            <td>{{ $log['label_action'] }}</td>
                            <td>
                                @if($log['reference'])
                                    @if($log['reference_url'])
                                        <a href="{{ $log['reference_url'] }}">{{ $log['reference'] }}</a>
                                    @else
                                        {{ $log['reference'] }}
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $log['acteur'] }}</td>
                            <td class="small">{{ $log['description'] ?? '-' }}</td>
                            <td class="small text-muted">{{ $log['ip'] ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucune action trouvée pour ces critères.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer">{{ $logs->links() }}</div>
        @endif
    </div>

</div>
</section>
@endsection

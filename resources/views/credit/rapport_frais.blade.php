@extends('layouts.app')

@section('page_title', 'Rapport Frais de Déblocage')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Rapport Frais Déblocage')

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- Filtres --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filtres</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('credit.rapport_frais') }}" class="form-inline flex-wrap gap-2">
                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">Du</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}">
                </div>
                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">Au</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ request('date_fin') }}">
                </div>
                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">Devise</label>
                    <select name="devise" class="form-control form-control-sm">
                        <option value="">Toutes</option>
                        @foreach(['CDF','USD','EUR'] as $d)
                            <option value="{{ $d }}" {{ request('devise') == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm mb-2">
                    <i class="fas fa-search mr-1"></i> Filtrer
                </button>
                <a href="{{ route('credit.rapport_frais') }}" class="btn btn-secondary btn-sm mb-2 ml-1">
                    <i class="fas fa-times mr-1"></i> Réinitialiser
                </a>
            </form>
        </div>
    </div>

    {{-- Totaux par devise --}}
    @if($totaux->isNotEmpty())
    <div class="row mb-3">
        @foreach($totaux as $t)
        <div class="col-md-4">
            <div class="card card-outline card-success">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-muted">{{ $t->devise }}</strong>
                        <span class="badge badge-secondary">{{ $t->nb }} déblocage(s)</span>
                    </div>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td>Montant brut total</td>
                            <td class="text-right font-weight-bold">{{ number_format($t->total_brut, 2, ',', ' ') }}</td>
                        </tr>
                        <tr>
                            <td>Caution 20% → GTC (bloqué)</td>
                            <td class="text-right text-warning font-weight-bold">{{ number_format($t->total_caution, 2, ',', ' ') }}</td>
                        </tr>
                        <tr>
                            <td>Frais dossier 4% → coffre</td>
                            <td class="text-right text-danger font-weight-bold">{{ number_format($t->total_frais, 2, ',', ' ') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Total retenues (24%)</strong></td>
                            <td class="text-right font-weight-bold">
                                {{ number_format(($t->total_caution ?? 0) + ($t->total_frais ?? 0), 2, ',', ' ') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Net versé clients (76%)</td>
                            <td class="text-right">{{ number_format($t->total_net, 2, ',', ' ') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Tableau détaillé --}}
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table mr-1"></i>
                Détail des déblocages — {{ $deblocages->total() }} enregistrement(s)
            </h3>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>N° Dossier</th>
                        <th>Client</th>
                        <th>Devise</th>
                        <th class="text-right">Montant brut</th>
                        <th class="text-right">Caution 20%<br><small class="text-warning">→ GTC bloqué</small></th>
                        <th class="text-right">Frais 4%<br><small class="text-danger">→ coffre</small></th>
                        <th class="text-right">Total retenues<br><small>24%</small></th>
                        <th class="text-right">Net client<br><small>76%</small></th>
                        <th>Opérateur</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deblocages as $d)
                    @php
                        $caution = (float)($d->montant_caution ?? 0);
                        $frais   = (float)($d->frais_dossier ?? 0);
                        $coffre  = $caution + $frais;
                    @endphp
                    <tr>
                        <td>{{ optional($d->debloque_le)->format('d/m/Y') }}</td>
                        <td>
                            @if($d->demande)
                                <a href="{{ route('credit.show', $d->demande) }}">{{ $d->demande->numero_dossier }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($d->demande?->client)
                                {{ $d->demande->client->nom_complet ?? ($d->demande->client->nom . ' ' . $d->demande->client->prenom) }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $d->devise }}</td>
                        <td class="text-right">{{ number_format($d->montant_debloque, 2, ',', ' ') }}</td>
                        <td class="text-right text-warning">{{ number_format($caution, 2, ',', ' ') }}</td>
                        <td class="text-right text-danger">{{ number_format($frais, 2, ',', ' ') }}</td>
                        <td class="text-right font-weight-bold text-success">{{ number_format($coffre, 2, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($d->montant_net_verse, 2, ',', ' ') }}</td>
                        <td>{{ $d->operateur?->nom_complet ?? $d->agent_matricule }}</td>
                        <td><small class="text-muted">{{ $d->reference_transaction }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">Aucun déblocage trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deblocages->hasPages())
        <div class="card-footer">
            {{ $deblocages->links() }}
        </div>
        @endif
    </div>

</div>
</section>
@endsection

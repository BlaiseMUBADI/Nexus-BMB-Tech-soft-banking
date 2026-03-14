@extends('layouts.app')

@section('page_title', 'Grand Livre')
@section('breadcrumb_parent', 'Comptabilite')
@section('breadcrumb', 'Grand Livre')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-info shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="form-row">
                <div class="col-md-4 mb-2">
                    <label class="small mb-1">Compte comptable</label>
                    <select class="form-control form-control-sm" name="numero_compte" required>
                        <option value="">Selectionner...</option>
                        @foreach($comptes as $c)
                            <option value="{{ $c->numero_compte }}" {{ $numeroCompte === $c->numero_compte ? 'selected' : '' }}>
                                {{ $c->numero_compte }} - {{ $c->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Date debut</label>
                    <input type="date" class="form-control form-control-sm" name="date_debut" value="{{ $dateDebut }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Date fin</label>
                    <input type="date" class="form-control form-control-sm" name="date_fin" value="{{ $dateFin }}">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button class="btn btn-sm btn-primary btn-block" type="submit"><i class="fas fa-search mr-1"></i>Afficher</button>
                </div>
            </form>
        </div>
    </div>

    @if($numeroCompte)
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($resume['total_debit'], 2, ',', ' ') }}</h3>
                        <p>Total debit</p>
                    </div>
                    <div class="icon"><i class="fas fa-arrow-down"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($resume['total_credit'], 2, ',', ' ') }}</h3>
                        <p>Total credit</p>
                    </div>
                    <div class="icon"><i class="fas fa-arrow-up"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box {{ $resume['solde'] >= 0 ? 'bg-primary' : 'bg-warning' }}">
                    <div class="inner">
                        <h3>{{ number_format($resume['solde'], 2, ',', ' ') }}</h3>
                        <p>Solde</p>
                    </div>
                    <div class="icon"><i class="fas fa-balance-scale"></i></div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Reference journal</th>
                                <th>Operation</th>
                                <th>Libelle</th>
                                <th>Devise</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mouvements as $line)
                                <tr>
                                    <td>{{ $line->journal?->date_ecriture?->format('d/m/Y H:i:s') }}</td>
                                    <td class="text-monospace">{{ $line->journal?->reference_piece }}</td>
                                    <td>{{ $line->journal?->transaction?->reference ?? 'N/A' }}</td>
                                    <td>{{ $line->libelle_ligne }}</td>
                                    <td>{{ $line->devise_code ?? 'N/A' }}</td>
                                    <td class="text-right">{{ number_format((float) $line->debit, 2, ',', ' ') }}</td>
                                    <td class="text-right">{{ number_format((float) $line->credit, 2, ',', ' ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Aucun mouvement pour ce compte.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('page_title', 'Comptabilite OHADA')
@section('breadcrumb_parent', 'Comptabilite')
@section('breadcrumb', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['journaux_jour']) }}</h3>
                    <p>Journaux du jour</p>
                </div>
                <div class="icon"><i class="fas fa-book"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($stats['ecritures_jour']) }}</h3>
                    <p>Lignes comptables</p>
                </div>
                <div class="icon"><i class="fas fa-list-ol"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['debit_jour'], 2, ',', ' ') }}</h3>
                    <p>Total debit du jour</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($stats['credit_jour'], 2, ',', ' ') }}</h3>
                    <p>Total credit du jour</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-up"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-secondary shadow-sm">
        <div class="card-header">
            <h3 class="card-title mb-0"><i class="fas fa-history mr-1"></i>Derniers journaux comptables</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference journal</th>
                            <th>Operation</th>
                            <th>Type piece</th>
                            <th>Lignes</th>
                            <th>Devise</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($journalRecent as $journal)
                            <tr>
                                <td>{{ $journal->date_ecriture?->format('d/m/Y H:i:s') }}</td>
                                <td class="text-monospace">{{ $journal->reference_piece }}</td>
                                <td>
                                    {{ $journal->transaction?->reference ?? 'N/A' }}
                                    @if($journal->transaction)
                                        <small class="text-muted d-block">{{ \App\Models\Caisse\Transaction::typeLabel($journal->transaction->type) }}</small>
                                    @endif
                                </td>
                                <td><span class="badge badge-info">{{ $journal->type_piece }}</span></td>
                                <td>{{ $journal->ecritures->count() }}</td>
                                <td>{{ $journal->devise_code ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucune ecriture comptable enregistree.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

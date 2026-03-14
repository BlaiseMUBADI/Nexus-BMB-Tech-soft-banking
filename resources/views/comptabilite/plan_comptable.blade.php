@extends('layouts.app')

@section('page_title', 'Plan Comptable OHADA')
@section('breadcrumb_parent', 'Comptabilite')
@section('breadcrumb', 'Plan OHADA')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title mb-0"><i class="fas fa-sitemap mr-1"></i>Plan comptable</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Classe</th>
                            <th>Numero</th>
                            <th>Libelle</th>
                            <th>Type</th>
                            <th>Niveau</th>
                            <th>Mvt</th>
                            <th class="text-right">Debit cumule</th>
                            <th class="text-right">Credit cumule</th>
                            <th class="text-right">Solde</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td><span class="badge badge-dark">{{ $account->classe_ohada ?? '-' }}</span></td>
                                <td class="text-monospace font-weight-bold">{{ $account->numero_compte }}</td>
                                <td>
                                    {{ $account->libelle }}
                                    @if($account->parent_compte)
                                        <small class="d-block text-muted">Parent: {{ $account->parent_compte }}</small>
                                    @endif
                                </td>
                                <td><span class="badge badge-secondary">{{ $account->type_compte }}</span></td>
                                <td>{{ $account->niveau }}</td>
                                <td>
                                    @if($account->est_mouvementable)
                                        <span class="badge badge-success">Oui</span>
                                    @else
                                        <span class="badge badge-light">Non</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($account->total_debit, 2, ',', ' ') }}</td>
                                <td class="text-right">{{ number_format($account->total_credit, 2, ',', ' ') }}</td>
                                <td class="text-right font-weight-bold {{ $account->solde >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($account->solde, 2, ',', ' ') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Aucun compte comptable trouve.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

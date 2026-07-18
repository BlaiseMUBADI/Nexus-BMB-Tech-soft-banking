<div class="card card-outline card-secondary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-balance-scale mr-2 text-info"></i>Balance générale <span class="badge badge-light">{{ $balance->count() }} compte(s)</span></h6>
        <div>
            <span class="badge badge-success">Débit : {{ number_format($totaux['debit'], 2, ',', ' ') }}</span>
            <span class="badge badge-danger ml-1">Crédit : {{ number_format($totaux['credit'], 2, ',', ' ') }}</span>
            @if(abs($totaux['debit'] - $totaux['credit']) < 0.01)
                <span class="badge badge-primary ml-1"><i class="fas fa-check mr-1"></i>Équilibrée</span>
            @else
                <span class="badge badge-warning ml-1"><i class="fas fa-exclamation-triangle mr-1"></i>Déséquilibre</span>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Compte</th><th>Libellé</th><th class="text-center">Classe</th>
                        <th class="text-right">Débit</th><th class="text-right">Crédit</th><th class="text-right">Solde</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($balance as $b)
                    <tr>
                        <td class="text-monospace">{{ $b->numero_compte }}</td>
                        <td>{{ $b->libelle }}</td>
                        <td class="text-center">{{ $b->classe_ohada }}</td>
                        <td class="text-right text-success">{{ number_format($b->total_debit, 2, ',', ' ') }}</td>
                        <td class="text-right text-danger">{{ number_format($b->total_credit, 2, ',', ' ') }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($b->solde, 2, ',', ' ') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun mouvement enregistré.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold bg-light">
                        <td colspan="3" class="text-right">TOTAUX</td>
                        <td class="text-right text-success">{{ number_format($totaux['debit'], 2, ',', ' ') }}</td>
                        <td class="text-right text-danger">{{ number_format($totaux['credit'], 2, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($totaux['debit'] - $totaux['credit'], 2, ',', ' ') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

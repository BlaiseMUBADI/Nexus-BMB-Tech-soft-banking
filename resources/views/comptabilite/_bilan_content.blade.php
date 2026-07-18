<div class="row mb-3">
    <div class="col-md-6">
        <div class="small-box bg-primary">
            <div class="inner"><h3>{{ number_format($totalActif, 2, ',', ' ') }}</h3><p>Total Actif</p></div>
            <div class="icon"><i class="fas fa-landmark"></i></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="small-box {{ abs($totalActif - $totalPassif) < 0.01 ? 'bg-success' : 'bg-warning' }}">
            <div class="inner"><h3>{{ number_format($totalPassif, 2, ',', ' ') }}</h3><p>Total Passif {{ abs($totalActif - $totalPassif) < 0.01 ? '(équilibré)' : '(déséquilibre !)' }}</p></div>
            <div class="icon"><i class="fas fa-balance-scale"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card card-outline card-primary shadow-sm h-100">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-landmark text-primary mr-2"></i>ACTIF</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        @forelse($actif as $a)
                        <tr><td class="text-monospace small">{{ $a->numero_compte }}</td><td>{{ $a->libelle }}</td><td class="text-right font-weight-bold">{{ number_format($a->montant, 2, ',', ' ') }}</td></tr>
                        @empty
                        <tr><td class="text-center text-muted py-3">Aucune donnée.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot><tr class="font-weight-bold bg-light"><td colspan="2" class="text-right">TOTAL ACTIF</td><td class="text-right">{{ number_format($totalActif, 2, ',', ' ') }}</td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card card-outline card-secondary shadow-sm h-100">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-balance-scale text-secondary mr-2"></i>PASSIF</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        @forelse($passif as $p)
                        <tr><td class="text-monospace small">{{ $p->numero_compte }}</td><td>{{ $p->libelle }}</td><td class="text-right font-weight-bold">{{ number_format($p->montant, 2, ',', ' ') }}</td></tr>
                        @empty
                        <tr><td class="text-center text-muted py-3">Aucune donnée.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot><tr class="font-weight-bold bg-light"><td colspan="2" class="text-right">TOTAL PASSIF</td><td class="text-right">{{ number_format($totalPassif, 2, ',', ' ') }}</td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

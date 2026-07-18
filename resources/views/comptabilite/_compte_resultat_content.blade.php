<div class="row mb-3">
    <div class="col-md-4">
        <div class="small-box bg-danger">
            <div class="inner"><h3>{{ number_format($totalCharges, 2, ',', ' ') }}</h3><p>Total Charges (classe 6)</p></div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ number_format($totalProduits, 2, ',', ' ') }}</h3><p>Total Produits (classe 7)</p></div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="small-box {{ $resultatNet >= 0 ? 'bg-primary' : 'bg-warning' }}">
            <div class="inner"><h3>{{ number_format($resultatNet, 2, ',', ' ') }}</h3><p>Résultat net ({{ $resultatNet >= 0 ? 'Bénéfice' : 'Perte' }})</p></div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card card-outline card-danger shadow-sm h-100">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-arrow-down text-danger mr-2"></i>Charges</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        @forelse($charges as $c)
                        <tr><td class="text-monospace small">{{ $c->numero_compte }}</td><td>{{ $c->libelle }}</td><td class="text-right font-weight-bold text-danger">{{ number_format($c->montant, 2, ',', ' ') }}</td></tr>
                        @empty
                        <tr><td class="text-center text-muted py-3">Aucune charge.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot><tr class="font-weight-bold bg-light"><td colspan="2" class="text-right">TOTAL</td><td class="text-right text-danger">{{ number_format($totalCharges, 2, ',', ' ') }}</td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card card-outline card-success shadow-sm h-100">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-arrow-up text-success mr-2"></i>Produits</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        @forelse($produits as $p)
                        <tr><td class="text-monospace small">{{ $p->numero_compte }}</td><td>{{ $p->libelle }}</td><td class="text-right font-weight-bold text-success">{{ number_format($p->montant, 2, ',', ' ') }}</td></tr>
                        @empty
                        <tr><td class="text-center text-muted py-3">Aucun produit.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot><tr class="font-weight-bold bg-light"><td colspan="2" class="text-right">TOTAL</td><td class="text-right text-success">{{ number_format($totalProduits, 2, ',', ' ') }}</td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

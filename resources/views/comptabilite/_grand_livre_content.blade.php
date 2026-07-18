@if($numeroCompte)
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($resume['total_debit'], 2, ',', ' ') }}</h3>
                    <p>Total débit @if($compte)— {{ $compte->libelle }}@endif</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($resume['total_credit'], 2, ',', ' ') }}</h3>
                    <p>Total crédit</p>
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
                            <th>Référence journal</th>
                            <th>Opération</th>
                            <th>Libellé</th>
                            <th>Devise</th>
                            <th class="text-right">Débit</th>
                            <th class="text-right">Crédit</th>
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
@else
    <div class="text-center text-muted py-5">
        <i class="fas fa-search fa-2x mb-2"></i><br>
        Sélectionnez un compte pour afficher son grand livre.
    </div>
@endif

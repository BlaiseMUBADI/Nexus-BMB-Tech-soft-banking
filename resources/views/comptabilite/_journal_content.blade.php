<div class="card card-outline card-secondary shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Journal</th>
                        <th>Opération</th>
                        <th>Type</th>
                        <th>Libellé</th>
                        <th>Totaux</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journaux as $journal)
                        @php
                            $debit = (float) $journal->ecritures->sum('debit');
                            $credit = (float) $journal->ecritures->sum('credit');
                        @endphp
                        <tr>
                            <td>{{ $journal->date_ecriture?->format('d/m/Y H:i:s') }}</td>
                            <td class="text-monospace">{{ $journal->reference_piece }}</td>
                            <td>
                                {{ $journal->transaction?->reference ?? 'N/A' }}
                                @if($journal->transaction)
                                    <small class="d-block text-muted">{{ \App\Models\Caisse\Transaction::typeLabel($journal->transaction->type) }}</small>
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ $journal->type_piece }}</span></td>
                            <td>{{ $journal->libelle }}</td>
                            <td>
                                <small class="d-block text-success">D: {{ number_format($debit, 2, ',', ' ') }}</small>
                                <small class="d-block text-danger">C: {{ number_format($credit, 2, ',', ' ') }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="bg-light py-1">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Compte</th>
                                                <th>Libellé ligne</th>
                                                <th>Devise</th>
                                                <th class="text-right">Débit</th>
                                                <th class="text-right">Crédit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($journal->ecritures as $line)
                                                <tr>
                                                    <td class="text-monospace">{{ $line->numero_compte }}</td>
                                                    <td>{{ $line->libelle_ligne }}</td>
                                                    <td>{{ $line->devise_code }}</td>
                                                    <td class="text-right">{{ number_format((float) $line->debit, 2, ',', ' ') }}</td>
                                                    <td class="text-right">{{ number_format((float) $line->credit, 2, ',', ' ') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucun journal comptable.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $journaux->links() }}
    </div>
</div>

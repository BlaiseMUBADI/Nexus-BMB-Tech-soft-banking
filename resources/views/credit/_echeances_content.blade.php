{{-- ── Bandeau total à recouvrir ─────────────────────────── --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info py-2 mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center mr-3">
                    <strong>Échéances filtrées :</strong> {{ $totalEcheances }} échéance(s)
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @foreach($totauxParDevise as $devise => $totaux)
                        @php
                            $symbole = match($devise) { 'USD' => '$', 'EUR' => '€', default => 'Fc' };
                        @endphp
                        <div class="d-flex align-items-center px-2" style="border-right: 1px solid rgba(255,255,255,0.3);"
                             data-toggle="tooltip"
                             title="Devise: {{ $devise }}&#10;Montant total: {{ number_format($totaux['montant_total'], 0, ',', ' ') }}{{ $symbole }}&#10;Payé: {{ number_format($totaux['montant_paye'], 0, ',', ' ') }}{{ $symbole }}&#10;Reste dû: {{ number_format($totaux['reste_du'], 0, ',', ' ') }}{{ $symbole }}"
                             data-html="true" style="cursor:pointer;">
                            <span class="badge badge-light px-2 mr-2">{{ $devise }}</span>
                            <i class="fas fa-coins text-warning mr-2"></i>
                            <strong class="text-white">{{ number_format($totaux['reste_du'], 0, ',', ' ') }}{{ $symbole }}</strong>
                            <small class="text-white ml-1">à recouvrir ({{ $totaux['count'] }})</small>
                        </div>
                    @endforeach
                    @if(empty($totauxParDevise))
                        <span class="text-white small">Aucun montant à recouvrir pour ces critères.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Répartition par zone / portefeuille ───────────────── --}}
<div class="row mb-3">
    <div class="col-md-6 mb-2">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0"><i class="fas fa-map-marker-alt text-danger mr-2"></i>Reste à recouvrir par zone</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        @forelse($totauxParZone as $zoneNom => $t)
                            <tr>
                                <td>{{ $zoneNom }}</td>
                                <td class="text-muted text-center" style="width:70px;">{{ $t['count'] }} éch.</td>
                                <td class="text-right font-weight-bold text-danger" style="width:150px;">{{ number_format($t['reste_du'], 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-3">Aucune donnée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-2">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0"><i class="fas fa-briefcase text-primary mr-2"></i>Reste à recouvrir par portefeuille</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        @forelse($totauxParPortefeuille as $pfNom => $t)
                            <tr>
                                <td>{{ $pfNom }}</td>
                                <td class="text-muted text-center" style="width:70px;">{{ $t['count'] }} éch.</td>
                                <td class="text-right font-weight-bold text-primary" style="width:150px;">{{ number_format($t['reste_du'], 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-3">Aucune donnée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="card-title mb-0">
            <i class="fas fa-calendar-alt mr-2 text-warning"></i>
            Tombée d'échéances
            <span class="badge badge-light ml-1">{{ $echeances->total() }} échéance(s)</span>
        </h5>
    </div>

    {{-- ── Tableau ────────────────────────────────────────── --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Date échéance</th>
                        <th>N° Éch.</th>
                        <th>Dossier</th>
                        <th>Client</th>
                        <th>Zone</th>
                        <th>Portefeuille</th>
                        <th class="text-right">Montant échéance</th>
                        <th class="text-right">Payé</th>
                        <th class="text-right">Reste dû</th>
                        <th class="text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($echeances as $ech)
                        @php
                            $demande = $ech->echeancier->demande ?? null;
                            $client = $demande->client ?? null;
                            $resteDu = max(0, (float) $ech->total_echeance - (float) $ech->montant_paye);
                            $badgeStatut = [
                                'EN_ATTENTE' => 'badge-secondary',
                                'EN_RETARD' => 'badge-danger',
                                'PARTIELLEMENT_PAYE' => 'badge-warning',
                                'PAYE' => 'badge-success',
                                'REPORTE' => 'badge-info',
                            ][$ech->statut] ?? 'badge-secondary';
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($ech->date_echeance)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $ech->numero_echeance }}</td>
                            <td>
                                @if($demande)
                                    <a href="{{ route('credit.show', $demande->id) }}" class="font-weight-bold text-primary text-decoration-none">
                                        <i class="fas fa-file-invoice mr-1"></i>{{ $demande->numero_dossier }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $client ? trim(($client->nom ?? '').' '.($client->postnom ?? '').' '.($client->prenom ?? '')) : '-' }}</td>
                            <td>{{ $demande->zone->nom ?? '-' }}</td>
                            <td>{{ $demande->portefeuille->nom_portefeuille ?? '-' }}</td>
                            <td class="text-right">{{ number_format($ech->total_echeance, 2, ',', ' ') }} <small>{{ $demande->devise ?? '' }}</small></td>
                            <td class="text-right text-success">{{ number_format($ech->montant_paye, 2, ',', ' ') }}</td>
                            <td class="text-right text-danger font-weight-bold">{{ number_format($resteDu, 2, ',', ' ') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $badgeStatut }}">{{ $ech->statut }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                                Aucune échéance ne correspond aux critères sélectionnés.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($echeances->hasPages())
    <div class="card-footer">
        {{ $echeances->links() }}
    </div>
    @endif
</div>

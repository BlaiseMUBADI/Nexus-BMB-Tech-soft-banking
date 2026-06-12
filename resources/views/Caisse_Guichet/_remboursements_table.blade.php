<div id="tableContainer">
@if($dossiers->isEmpty())
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>Aucun dossier en remboursement.
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-sm mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>N° Dossier</th>
                            <th>Client</th>
                            <th class="text-right">Total Dû (Capital + Int.)</th>
                            <th class="text-right">Déjà remboursé</th>
                            <th class="text-right">Reste dû</th>
                            <th>Prochaine échéance</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dossiers as $dossier)
                            @php
                                $totalRembourse = $dossier->remboursements->sum('montant_recu');
                                $totalDu = $dossier->montant_total_echeances ?? $dossier->montant_approuve;
                                $resteDu = max(0, $totalDu - $totalRembourse);
                                $devise = $dossier->devise ?? 'CDF';
                                $prochaineEcheance = $dossier->echeancier && $dossier->echeancier->echeances->whereIn('statut', ['EN_ATTENTE','EN_RETARD'])->isNotEmpty()
                                    ? $dossier->echeancier->echeances()->whereIn('statut', ['EN_ATTENTE','EN_RETARD'])->orderBy('numero_echeance')->first()->date_echeance->format('d/m/Y')
                                    : '—';
                            @endphp
                            <tr>
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('credit.show', $dossier) }}" class="font-weight-bold text-dark text-decoration-none">
                                        <code class="small">{{ $dossier->numero_dossier }}</code>
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ $dossier->client->nom }} {{ $dossier->client->postnom }} {{ $dossier->client->prenom }}</strong>
                                    <br><small class="text-muted">{{ $dossier->client->matricule }}</small>
                                </td>
                                <td class="text-right font-weight-bold">
                                    {{ number_format($dossier->montant_total_echeances ?? $dossier->montant_approuve, 0, ',', ' ') }} <small class="text-muted">{{ $devise }}</small>
                                </td>
                                <td class="text-right text-success">
                                    {{ number_format($totalRembourse, 0, ',', ' ') }} <small class="text-muted">{{ $devise }}</small>
                                </td>
                                <td class="text-right text-danger font-weight-bold">
                                    {{ number_format($resteDu, 0, ',', ' ') }} <small class="text-muted">{{ $devise }}</small>
                                </td>
                                <td>{{ $prochaineEcheance }}</td>
                                <td class="text-center">
                                    <a href="{{ route('credit.show', $dossier) }}" class="btn btn-xs btn-info" title="Voir le dossier">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('credit.pdf.releve', $dossier) }}" target="_blank" class="btn btn-xs btn-outline-primary" title="Relevé de compte crédit">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    <a href="{{ route('caisses.remboursement', $dossier) }}" class="btn btn-xs btn-success" title="Enregistrer un remboursement">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap py-2">
            <small class="text-muted" id="searchCount">
                Affichage de {{ $dossiers->firstItem() ?? 0 }}–{{ $dossiers->lastItem() ?? 0 }}
                sur {{ $dossiers->total() }} dossier(s)
            </small>
            @if($dossiers->hasPages())
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    @if($dossiers->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $dossiers->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                    @endif

                    @foreach($dossiers->getUrlRange(1, $dossiers->lastPage()) as $page => $url)
                        @if($page == $dossiers->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if($dossiers->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $dossiers->nextPageUrl() }}" rel="next">&raquo;</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
@endif
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.15) !important;
        cursor: pointer;
        transition: background-color 0.1s ease;
    }
    .table-hover tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.15) !important;
    }
    .table-hover tbody tr:hover {
        border-left-color: rgba(255, 255, 255, 0.3) !important;
    }
</style>
<div class="table-responsive">
     <table class="table table-sm table-hover mb-0">
        <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:40px">#</th>
                <th style="width:155px">N° Dossier</th>
                <th>Client</th>
                <th>Type</th>
                <th class="text-right">Montant demandé</th>
                <th class="text-right">Montant approuvé</th>
                <th class="text-center">Durée</th>
                <th class="text-center">Taux</th>
                <th>Statut</th>
                <th>Zone / Portefeuille</th>
                <th>Date dossier</th>
                <th class="text-center" style="width:120px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dossiers as $d)
            @php
                $borderColor = '#dee2e6';
                $alerteBadge = null;
                if ($d->statut_global === 'EN_RETARD') {
                    $borderColor = '#dc3545';
                    $alerteBadge = ['danger', 'fa-clock', 'Retard'];
                } elseif ($d->est_suspect) {
                    $borderColor = '#dc3545';
                    $alerteBadge = ['danger', 'fa-user-secret', 'Suspect'];
                } elseif ($d->est_suspendu) {
                    $borderColor = '#ffc107';
                    $alerteBadge = ['warning', 'fa-pause-circle', 'Suspendu'];
                } elseif ($d->statut_global === 'SOLDE') {
                    $borderColor = '#198754';
                } elseif ($d->statut_global === 'PRET_A_DEBLOQUER') {
                    $borderColor = '#fd7e14';
                }
            @endphp
            <tr style="border-left:4px solid {{ $borderColor }}; transition: background 0.15s;">
                <td class="text-center text-muted small">{{ ($dossiers->currentPage() - 1) * $dossiers->perPage() + $loop->iteration }}</td>
                <td>
                    <a href="{{ route('credit.show', $d) }}" class="font-weight-bold text-dark text-decoration-none">
                        <code class="small">{{ $d->numero_dossier }}</code>
                    </a>
                </td>
                <td>
                    <span class="font-weight-bold">{{ $d->client?->nom }} {{ $d->client?->prenom }}</span>
                    <br><small class="text-muted">{{ $d->client_matricule }}</small>
                </td>
                <td>
                    <span class="badge badge-{{ $d->type_credit === 'PME' ? 'primary' : ($d->type_credit === 'SOLIDAIRE' ? 'info' : 'secondary') }}">
                        {{ $d->type_credit }}
                    </span>
                </td>
                <td class="text-right text-nowrap font-weight-bold">
                    {{ number_format($d->montant_demande, 0, ',', ' ') }}
                    <small class="text-muted">{{ $d->devise === 'USD' ? '$' : ($d->devise === 'CDF' ? 'Fc' : '€') }}</small>
                </td>
                <td class="text-right text-nowrap">
                    @if($d->montant_approuve)
                        <strong>{{ number_format($d->montant_approuve, 0, ',', ' ') }}</strong>
                        <small class="text-muted">{{ $d->devise === 'USD' ? '$' : ($d->devise === 'CDF' ? 'Fc' : '€') }}</small>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="text-center">{{ $d->duree_mois }}<small class="text-muted"> mois</small></td>
                <td class="text-center">{{ number_format((float) $d->taux_interet_mensuel, 1, '.', '') }}<small>%</small></td>
                <td>
                    {!! $d->badgeStatut() !!}
                    @if($alerteBadge)
                        <span class="badge badge-{{ $alerteBadge[0] }} d-inline-block mt-1">
                            <i class="fas {{ $alerteBadge[1] }} mr-1"></i>{{ $alerteBadge[2] }}
                        </span>
                    @endif
                </td>
                <td>
                    <small class="d-block">
                        <i class="fas fa-map-marker-alt text-muted mr-1"></i>
                        {{ $d->zone?->nom ?? $d->zone?->nom_zone ?? ('Zone ' . $d->code_zone) ?? '—' }}
                    </small>
                    @if($d->portefeuille)
                    <small class="text-muted d-block">
                        <i class="fas fa-briefcase mr-1"></i>{{ $d->portefeuille->nom_portefeuille }}
                    </small>
                    @endif
                </td>
                <td>
                    <small>{{ $d->created_at->format('d/m/Y') }}</small>
                    @if($d->soumis_le)
                    <br><small class="text-muted">Soumis: {{ $d->soumis_le->format('d/m/Y') }}</small>
                    @endif
                </td>
                <td class="text-center text-nowrap">
                    <a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-info mb-1" title="Voir le dossier">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($d->statut_global === 'BROUILLON' && in_array('EBEN-PER55', $userPermCodes ?? []))
                        <a href="{{ route('credit.edit', $d) }}" class="btn btn-xs btn-secondary mb-1" title="Modifier brouillon">
                            <i class="fas fa-edit"></i>
                        </a>
                    @endif
                    @if(in_array($d->statut_global, ['SOUMIS','EN_ANALYSE']) && in_array('EBEN-PER58', $userPermCodes ?? []))
                        <a href="{{ route('credit.analyse', $d) }}" class="btn btn-xs btn-primary mb-1" title="Analyser">
                            <i class="fas fa-search-dollar"></i>
                        </a>
                    @endif
                    @if($d->statut_global === 'EN_VALIDATION' && array_intersect(['EBEN-PER60','EBEN-PER61','EBEN-PER62','EBEN-PER63'], $userPermCodes ?? []))
                        <a href="{{ route('credit.validation', $d) }}" class="btn btn-xs btn-warning mb-1" title="Valider">
                            <i class="fas fa-check-double"></i>
                        </a>
                    @endif
                    @if($d->statut_global === 'PRET_A_DEBLOQUER' && in_array('EBEN-PER64', $userPermCodes ?? []))
                        <a href="{{ route('credit.deblocage', $d) }}" class="btn btn-xs btn-success mb-1" title="Débloquer">
                            <i class="fas fa-unlock-alt"></i>
                        </a>
                    @endif
                    @if(in_array($d->statut_global, ['DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD']) && in_array('EBEN-PER111', $userPermCodes ?? []))
                        <a href="{{ route('credit.remboursement', $d) }}"
                           class="btn btn-xs btn-{{ $d->statut_global === 'EN_RETARD' ? 'danger' : 'success' }} mb-1"
                           title="{{ $d->statut_global === 'EN_RETARD' ? 'Rembourser (retard !)' : 'Enregistrer remboursement' }}">
                            <i class="fas fa-money-bill-wave"></i>
                        </a>
                    @endif
                    @if($d->echeancier && in_array('EBEN-PER71', $userPermCodes ?? []))
                        <a href="{{ route('credit.pdf.echeancier', $d) }}" target="_blank" class="btn btn-xs btn-outline-danger mb-1" title="PDF échéancier">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <a href="{{ route('credit.pdf.releve', $d) }}" target="_blank" class="btn btn-xs btn-outline-primary mb-1" title="Relevé de compte crédit">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open fa-3x mb-3 d-block text-secondary"></i>
                    <strong>Aucun dossier crédit trouvé</strong>
                    @if(request()->anyFilled(['numero','client_matricule','statut','type_credit','zone','devise','portefeuille_id','agent_analyse','date_debut','date_fin','alerte']))
                        <br><small>Essayez de modifier ou <a href="{{ route('credit.index') }}">réinitialiser</a> les filtres.</small>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap py-2">
    <small class="text-muted">
        Affichage de {{ $dossiers->firstItem() ?? 0 }}–{{ $dossiers->lastItem() ?? 0 }}
        sur {{ $dossiers->total() }} dossier(s)
    </small>
    @if($dossiers->hasPages())
    <nav>
        <ul class="pagination pagination-sm mb-0">
            {{-- Précédent --}}
            @if($dossiers->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $dossiers->previousPageUrl() }}" rel="prev">&laquo;</a></li>
            @endif

            {{-- Numéros de pages --}}
            @foreach($dossiers->getUrlRange(1, $dossiers->lastPage()) as $page => $url)
                @if($page == $dossiers->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            {{-- Suivant --}}
            @if($dossiers->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $dossiers->nextPageUrl() }}" rel="next">&raquo;</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
    @endif
</div>

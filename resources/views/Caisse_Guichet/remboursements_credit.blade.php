@extends('layouts.app')

@section('page_title', 'Remboursements Crédit')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Remboursements')

@section('content')
<section class="content">
    <div class="container-fluid">

        @if(!$guichet)
        {{-- ── Aucun guichet affecté ──────────────────────────────── --}}
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <div class="card card-warning card-outline shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-cash-register fa-4x text-muted mb-3"></i>
                        <h4>Aucun guichet assigné</h4>
                        <p class="text-muted">Votre compte n'est associé à aucun guichet actif.<br>
                        Contactez un administrateur pour l'affectation.</p>
                    </div>
                </div>
            </div>
        </div>

        @else
        @php
            $statut  = $guichet->statut_operationnel;
            $typeGch = $guichet->type_guichet;   // FIXE | MOBILE | CENTRAL
            $guichetOuvert = ($statut === 'OUVERT');
        @endphp

        {{-- ── Bandeau statut guichet ────────────────────────────── --}}
        @if(!$guichetOuvert)
        <div class="alert alert-{{ $statut === 'SUSPENDU' ? 'warning' : ($statut === 'EN_VERIFICATION' ? 'info' : 'danger') }} shadow mb-3 py-2">
            <i class="fas fa-{{ $statut === 'SUSPENDU' ? 'pause-circle' : ($statut === 'EN_VERIFICATION' ? 'hourglass-half' : 'lock') }} mr-2"></i>
            Guichet <strong>{{ $statut }}</strong> — Enregistrement impossible.
            Rendez-vous sur <a href="{{ route('caisses.ouverture') }}" class="alert-link">Ouverture / Fermeture</a> pour changer l'état du guichet.
        </div>
        @endif

        {{-- ── Soldes actuels ─────────────────────────────────────── --}}
        <div class="d-flex align-items-center flex-wrap gap-2 mb-3 operation-soldes-bar">
            <small class="text-muted text-uppercase" style="letter-spacing:.08em;">
                <i class="fas fa-wallet mr-1"></i> Soldes :
            </small>
            @foreach($guichet->soldes->sortBy('devise_code') as $s)
            <span class="badge badge-pill px-3 py-2 solde-pill" id="soldePill_{{ $s->devise_code }}"
                  style="font-size:.92rem; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15);">
                <strong>{{ $s->devise_code }}</strong>
                <span class="solde-val">{{ number_format($s->solde_en_caisse, 2, ',', ' ') }}</span>
            </span>
            @endforeach
            <span class="badge badge-pill px-2 py-2 ml-1"
                  style="background:rgba(23,162,184,.2); border:1px solid rgba(23,162,184,.4); font-size:.85rem;">
                <i class="fas fa-{{ $typeGch === 'MOBILE' ? 'mobile-alt' : 'desktop' }} mr-1 text-info"></i>
                {{ $typeGch }}
            </span>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <h2><i class="fas fa-money-bill-wave text-primary mr-2"></i>Dossiers en cours de remboursement</h2>
            </div>
        </div>

        {{-- ── Zone de recherche progressive ────────────────────────── --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="searchInput" class="form-control"
                           placeholder="Rechercher par nom, matricule client, n° dossier..."
                           autocomplete="off"
                           value="{{ request('search') }}">
                    <div class="input-group-append">
                        <span class="input-group-text" id="searchCount">
                            <small class="text-muted">{{ $dossiers->total() }} résultat(s)</small>
                        </span>
                    </div>
                </div>
                <small class="text-muted" id="searchHint">
                    @if(request('search'))
                        {{ $dossiers->total() }} résultat(s) pour "{{ request('search') }}"
                    @else
                        Tapez pour filtrer instantanément...
                    @endif
                </small>
            </div>
        </div>

        <div id="tableContainer">
            @include('Caisse_Guichet._remboursements_table', ['dossiers' => $dossiers])
        </div>

        {{-- ── Tableau des opérations de remboursement enregistrées ──── --}}
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history mr-2"></i>Opérations de remboursement enregistrées
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="tableOpsRemboursement">
                        <thead class="thead-dark">
                            <tr>
                                <th>Réf.</th>
                                <th>Date</th>
                                <th>Dossier</th>
                                <th>Client</th>
                                <th class="text-right">Montant</th>
                                <th>Guichet</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($operationsRemboursement ?? [] as $op)
                            <tr>
                                <td><code>{{ $op->reference }}</code></td>
                                <td>{{ optional($op->date_operation)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($op->dossierCredit)
                                        <strong>{{ $op->dossierCredit->numero_dossier }}</strong>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($op->dossierCredit && $op->dossierCredit->client)
                                        {{ $op->dossierCredit->client->nom }} {{ $op->dossierCredit->client->postnom }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold">
                                    {{ number_format($op->montant, 2, ',', ' ') }} <small class="text-muted">{{ $op->devise_code }}</small>
                                </td>
                                <td>{{ $op->guichet->code_guichet ?? '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('caisses.operations.bordereau', ['id' => $op->id]) }}" target="_blank" class="btn btn-xs btn-outline-info" title="Bordereau PDF">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Aucune opération de remboursement enregistrée.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    </div>
</section>
@endsection

@push('js')
<script>
(function() {
    let currentController = null;
    const searchInput = document.getElementById('searchInput');
    const tableContainer = document.getElementById('tableContainer');
    const searchCount = document.getElementById('searchCount');
    const searchHint = document.getElementById('searchHint');

    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Annuler la requête précédente si encore en cours
        if (currentController) {
            currentController.abort();
        }
        currentController = new AbortController();

        if (query.length === 0) {
            loadResults('');
            searchHint.textContent = 'Tapez pour filtrer instantanément...';
            return;
        }

        searchHint.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Recherche...';
        loadResults(query);
    });

    function loadResults(query) {
        const url = new URL(window.location.href);
        if (query) {
            url.searchParams.set('search', query);
            url.searchParams.delete('page');
        } else {
            url.searchParams.delete('search');
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            signal: currentController.signal
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('tableContainer');
            if (newContainer) {
                tableContainer.innerHTML = newContainer.innerHTML;
            }
            const newCount = doc.getElementById('searchCount');
            if (newCount) {
                searchCount.innerHTML = newCount.innerHTML;
            }
            if (query) {
                const count = searchCount.textContent.match(/\d+/);
                searchHint.textContent = count
                    ? count[0] + ' résultat(s) pour "' + query + '"'
                    : 'Aucun résultat';
            }
        })
        .catch(function(err) {
            // Ignorer les erreurs d'annulation (l'utilisateur a retapé)
            if (err.name !== 'AbortError') {
                searchHint.textContent = 'Erreur de recherche';
                console.error(err);
            }
        });
    }

    // Pagination AJAX
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (!paginationLink) return;
        e.preventDefault();

        const url = new URL(paginationLink.href);
        const currentSearch = searchInput.value.trim();
        if (currentSearch) {
            url.searchParams.set('search', currentSearch);
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(function(response) { return response.text(); })
        .then(function(html) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('tableContainer');
            if (newContainer) {
                tableContainer.innerHTML = newContainer.innerHTML;
            }
            const newCount = doc.getElementById('searchCount');
            if (newCount) {
                searchCount.innerHTML = newCount.innerHTML;
            }
            tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();

// Afficher la modale de succès et le lien vers le bordereau si une transaction vient d'être créée
@if(session('success') && session('transaction_id'))
    $(document).ready(function() {
        const transactionId = {{ session('transaction_id') }};
        const message = '{{ addslashes(session('success')) }}';
        const bordereauUrl = '{{ url("caisses/operations") }}/' + transactionId + '/bordereau';
        
        // Afficher la modale de succès réutilisable
        showSystemMessage('success', message + '<br><a href="' + bordereauUrl + '" target="_blank" class="btn btn-sm btn-info mt-2"><i class="fas fa-print mr-1"></i>Imprimer le bordereau</a>', 'Opération réussie');
    });
@endif
</script>
@endpush

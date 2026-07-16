@extends('layouts.app')

@section('page_title', 'Dossiers Crédits')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Liste des dossiers')

@php
    $userPermCodes = Auth::user()?->getPermissionCodes() ?? [];
    $activeAlerte  = request('alerte');
    $activeStatut  = request('statut');
    // Symbole de devise basé sur le filtre sélectionné
    $deviseFiltre  = request('devise') ?? 'CDF';
    $deviseSymbole = $deviseFiltre === 'USD' ? '$' : ($deviseFiltre === 'CDF' ? 'Fc' : '€');
@endphp

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- ── Totaux selon les filtres actifs ───────────────────── --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info py-2 mb-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center mr-3">
                        <strong>Résultats filtrés :</strong> {{ $totauxFiltres['count'] }} dossier(s)
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        @php
                            $parDevise = $totauxFiltres['par_devise'] ?? [];
                        @endphp
                        @foreach($parDevise as $devise => $totaux)
                            @php
                                $symbole = match($devise) {
                                    'USD' => '$',
                                    'EUR' => '€',
                                    default => 'Fc'
                                };
                                $tooltipContent = "Devise: {$devise}\n" .
                                    "Demandé: " . number_format($totaux['montant_demande'], 0, ',', ' ') . "{$symbole}\n" .
                                    "Approuvé: " . number_format($totaux['montant_approuve'], 0, ',', ' ') . "{$symbole}\n" .
                                    "Décaissé: " . number_format($totaux['montant_net_verse'], 0, ',', ' ') . "{$symbole}\n" .
                                    "En retard: {$totaux['en_retard']} dossier(s)";
                                if ($totaux['montant_en_retard'] > 0) {
                                    $tooltipContent .= "\nMontant en retard: " . number_format($totaux['montant_en_retard'], 0, ',', ' ') . "{$symbole}";
                                }
                            @endphp
                            <div class="d-flex align-items-center px-2" style="border-right: 1px solid rgba(255,255,255,0.3);" 
                                 data-toggle="tooltip" 
                                 title="{{ $tooltipContent }}"
                                 data-html="true"
                                 style="cursor: pointer;">
                                <span class="badge badge-light px-2 mr-2">{{ $devise }}</span>
                                <i class="fas fa-file-invoice-dollar text-white mr-2" title="Montant demandé"></i>
                                <strong class="text-white mr-2">{{ number_format($totaux['montant_demande'], 0, ',', ' ') }}{{ $symbole }}</strong>
                                <i class="fas fa-check-circle text-success mr-2" title="Montant approuvé"></i>
                                <strong class="text-success mr-2">{{ number_format($totaux['montant_approuve'], 0, ',', ' ') }}{{ $symbole }}</strong>
                                <i class="fas fa-hand-holding-usd text-info mr-2" title="Décaissé"></i>
                                <strong class="text-info mr-2">{{ number_format($totaux['montant_net_verse'], 0, ',', ' ') }}{{ $symbole }}</strong>
                                @if($totaux['en_retard'] > 0)
                                    <i class="fas fa-exclamation-triangle text-warning" title="En retard"></i>
                                    <strong class="text-warning ml-1">{{ $totaux['en_retard'] }}</strong>
                                @endif
                            </div>
                        @endforeach
                        @if($totauxFiltres['montant_rembourse'] > 0)
                            <div class="d-flex align-items-center px-2"
                                 data-toggle="tooltip"
                                 title="Remboursé: {{ number_format($totauxFiltres['montant_rembourse'], 0, ',', ' ') }}"
                                 style="cursor: pointer;">
                                <i class="fas fa-undo text-light mr-2"></i>
                                <strong class="text-light">{{ number_format($totauxFiltres['montant_rembourse'], 0, ',', ' ') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Flash messages ──────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- ── KPI rapides ─────────────────────────────────────── --}}
    <div class="row mb-3">
        <div class="col-6 col-sm-4 col-lg-2 mb-2">
            <a href="{{ route('credit.index', ['statut' => 'SOUMIS']) }}" class="text-decoration-none">
            <div class="info-box mb-0 bg-info text-white" style="min-height:60px">
                <span class="info-box-icon" style="width:50px;height:60px;line-height:60px;font-size:1.3rem">
                    <i class="fas fa-paper-plane"></i></span>
                <div class="info-box-content py-1">
                    <span class="info-box-text small">En cours</span>
                    <span class="info-box-number">{{ $compteurs['en_cours'] }}</span>
                </div>
            </div></a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2 mb-2">
            <a href="{{ route('credit.index', ['alerte' => 'retard']) }}" class="text-decoration-none">
            <div class="info-box mb-0 bg-danger text-white" style="min-height:60px">
                <span class="info-box-icon" style="width:50px;height:60px;line-height:60px;font-size:1.3rem">
                    <i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content py-1">
                    <span class="info-box-text small">En retard</span>
                    <span class="info-box-number">{{ $compteurs['en_retard'] }}</span>
                </div>
            </div></a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2 mb-2">
            <a href="{{ route('credit.index', ['statut' => 'EN_REMBOURSEMENT']) }}" class="text-decoration-none">
            <div class="info-box mb-0 bg-primary text-white" style="min-height:60px">
                <span class="info-box-icon" style="width:50px;height:60px;line-height:60px;font-size:1.3rem">
                    <i class="fas fa-coins"></i></span>
                <div class="info-box-content py-1">
                    <span class="info-box-text small">Actifs (remb.)</span>
                    <span class="info-box-number">{{ $compteurs['actifs'] }}</span>
                </div>
            </div></a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2 mb-2">
            <a href="{{ route('credit.index', ['statut' => 'SOLDE']) }}" class="text-decoration-none">
            <div class="info-box mb-0 bg-dark text-white" style="min-height:60px">
                <span class="info-box-icon" style="width:50px;height:60px;line-height:60px;font-size:1.3rem">
                    <i class="fas fa-check-double"></i></span>
                <div class="info-box-content py-1">
                    <span class="info-box-text small">Soldés</span>
                    <span class="info-box-number">{{ $compteurs['soldes'] }}</span>
                </div>
            </div></a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2 mb-2">
            <a href="{{ route('credit.index', ['alerte' => 'alertes']) }}" class="text-decoration-none">
            <div class="info-box mb-0 bg-warning text-white" style="min-height:60px">
                <span class="info-box-icon" style="width:50px;height:60px;line-height:60px;font-size:1.3rem">
                    <i class="fas fa-shield-alt"></i></span>
                <div class="info-box-content py-1">
                    <span class="info-box-text small">Alertes</span>
                    <span class="info-box-number">{{ $compteurs['alertes'] }}</span>
                </div>
            </div></a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2 mb-2">
            <a href="{{ route('credit.index', ['statut' => 'ANNULE']) }}" class="text-decoration-none">
            <div class="info-box mb-0 bg-secondary text-white" style="min-height:60px">
                <span class="info-box-icon" style="width:50px;height:60px;line-height:60px;font-size:1.3rem">
                    <i class="fas fa-ban"></i></span>
                <div class="info-box-content py-1">
                    <span class="info-box-text small">Annulés</span>
                    <span class="info-box-number">{{ $compteurs['annules'] }}</span>
                </div>
            </div></a>
        </div>
    </div>

    <div class="card" id="printZone">
        <div class="print-only text-center mb-3">
            <h3 class="font-weight-bold">Coopec EBEN - Liste des Dossiers Crédit</h3>
            <p class="text-muted mb-1">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
            <p class="mb-0"><strong>{{ $totauxFiltres['count'] }} dossier(s)</strong></p>
            <hr>
        </div>
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 no-print">
            <h5 class="card-title mb-0">
                <i class="fas fa-folder-open mr-2 text-warning"></i>
                Dossiers Crédit
                @if($activeAlerte === 'retard')
                    <span class="badge badge-danger ml-1">Retards</span>
                @elseif($activeAlerte === 'alertes')
                    <span class="badge badge-warning ml-1">Alertes</span>
                @elseif($activeStatut)
                    <span class="badge badge-info ml-1">{{ $activeStatut }}</span>
                @endif
                <span class="badge badge-light ml-1">{{ $dossiers->total() }} dossier(s)</span>
            </h5>
            <div class="d-flex gap-1">
                @if(in_array('EBEN-PER70', $userPermCodes))
                <a href="{{ route('credit.dashboard') }}" class="btn btn-outline-info btn-sm mr-1">
                    <i class="fas fa-chart-line mr-1"></i>Dashboard
                </a>
                @endif
                @if(in_array('EBEN-PER54', $userPermCodes))
                <a href="{{ route('credit.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus-circle mr-1"></i>Nouvelle demande
                </a>
                @endif
            </div>
        </div>

        {{-- ── Filtres avancés ───────────────────────────────── --}}
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('credit.index') }}" id="form-filtres">
                {{-- Ligne 1 --}}
                <div class="row">
                    <div class="col-md-3 col-sm-12 mb-2">
                        <label class="small font-weight-bold mb-0"><i class="fas fa-search mr-1 text-primary"></i>Recherche rapide</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" 
                               placeholder="N° dossier, matricule, compte, nom client..."
                               id="searchInput"
                               autocomplete="off">
                        <small class="text-muted">Tapez pour rechercher progressivement...</small>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Statut</label>
                        <select name="statut" class="form-control form-control-sm" id="filterStatut">
                            <option value="">Tous les statuts</option>
                            @foreach([
                                'BROUILLON'        => 'Brouillon',
                                'SOUMIS'           => 'Soumis',
                                'EN_ANALYSE'       => 'En analyse',
                                'EN_VALIDATION'    => 'En validation',
                                'PRET_A_DEBLOQUER' => 'Prêt à débloquer',
                                'DEBLOQUE'         => 'Débloqué',
                                'EN_REMBOURSEMENT' => 'En remboursement',
                                'EN_RETARD'        => 'En retard',
                                'SOLDE'            => 'Soldé',
                                'ANNULE'           => 'Annulé',
                                'SUSPENDU'         => 'Suspendu',
                                'SUSPECT'          => 'Suspect',
                            ] as $val => $lab)
                            <option value="{{ $val }}" {{ request('statut') == $val ? 'selected' : '' }}>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Type crédit</label>
                        <select name="type_credit" class="form-control form-control-sm" id="filterType">
                            <option value="">Tous types</option>
                            <option value="INDIVIDUEL" {{ request('type_credit')=='INDIVIDUEL'?'selected':'' }}>Individuel</option>
                            <option value="SOLIDAIRE"  {{ request('type_credit')=='SOLIDAIRE'?'selected':'' }}>Solidaire</option>
                            <option value="PME"        {{ request('type_credit')=='PME'?'selected':'' }}>PME</option>
                        </select>
                    </div>
                    <div class="col-md-1 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Devise</label>
                        <select name="devise" class="form-control form-control-sm" id="filterDevise">
                            <option value="">Toutes</option>
                            <option value="CDF" {{ request('devise')=='CDF'?'selected':'' }}>CDF</option>
                            <option value="USD" {{ request('devise')=='USD'?'selected':'' }}>USD</option>
                            <option value="EUR" {{ request('devise')=='EUR'?'selected':'' }}>EUR</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Zone</label>
                        <select name="zone" class="form-control form-control-sm" id="filterZone">
                            <option value="">Toutes zones</option>
                            @foreach($zones as $z)
                            <option value="{{ $z->code_zone }}" {{ request('zone') == $z->code_zone ? 'selected' : '' }}>
                                {{ $z->nom ?? $z->nom_zone ?? $z->code_zone }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-block" data-toggle="modal" data-target="#modalImpressionCredit" title="Imprimer la liste">
                            <i class="fas fa-print mr-1"></i> Imprimer
                        </button>
                    </div>
                </div>
                {{-- Ligne 2 (filtres avancés) --}}
                <div class="row" id="filtres-avances">
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Portefeuille</label>
                        <select name="portefeuille_id" class="form-control form-control-sm">
                            <option value="">Tous portefeuilles</option>
                            @foreach($portefeuilles as $p)
                            <option value="{{ $p->id }}" {{ request('portefeuille_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nom_portefeuille }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Agent créateur</label>
                        <select name="agent_createur" class="form-control form-control-sm">
                            <option value="">Tous agents</option>
                            @foreach($agentsCreateur as $a)
                            <option value="{{ $a->matricule }}" {{ request('agent_createur') == $a->matricule ? 'selected' : '' }}>
                                {{ $a->nom }} {{ $a->postnom }} ({{ $a->matricule }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @if($estSuperviseur && $agentsAnalyse->isNotEmpty())
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Agent d'analyse</label>
                        <select name="agent_analyse" class="form-control form-control-sm">
                            <option value="">Tous agents</option>
                            @foreach($agentsAnalyse as $ag)
                            <option value="{{ $ag->matricule }}" {{ request('agent_analyse') == $ag->matricule ? 'selected' : '' }}>
                                {{ $ag->nom }} {{ $ag->postnom }} ({{ $ag->matricule }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Créé à partir du</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Jusqu'au</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                               class="form-control form-control-sm">
                    </div>
                    {{-- Filtre alerte rapide (hidden) --}}
                    @if($activeAlerte)
                        <input type="hidden" name="alerte" value="{{ $activeAlerte }}">
                    @endif
                </div>
            </form>
        </div>

        {{-- ── Tableau des dossiers (mis à jour par AJAX) ── --}}
        <div class="card-body p-0" id="table-container">
            @include('credit._table')
        </div>
    </div>

</div>
</section>

{{-- ===== Modal Impression Liste ===== --}}
<div class="modal fade" id="modalImpressionCredit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formImpressionCredit" action="{{ route('credit.print.liste') }}" method="GET" target="_blank">
                <input type="hidden" name="output" id="printOutputMode" value="stream">
                <input type="hidden" name="export_format" id="printExportFormat" value="pdf">
                <div id="printFiltersContainer"></div>
                <div class="modal-body text-center py-4">
                    <p class="mb-2">Les filtres actuels de la liste seront appliqués.</p>
                    <p class="text-muted small mb-0">Choisissez le format de sortie :</p>
                </div>
                <div class="modal-footer py-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary js-print-action" data-output="stream" data-format="pdf">
                        <i class="fas fa-file-pdf mr-1"></i> Ouvrir PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-primary js-print-action" data-output="download" data-format="pdf">
                        <i class="fas fa-download mr-1"></i> Télécharger PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-success js-print-action" data-output="download" data-format="csv">
                        <i class="fas fa-file-csv mr-1"></i> Télécharger CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
(function() {
    'use strict';

    function initFilters() {
        const form = document.getElementById('form-filtres');
        const container = document.getElementById('table-container');
        if (!form || !container) return;

        let isSubmitting = false;

        // ── Token CSRF ──
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.content : '';

        // ── Recherche progressive sur le champ "search" ──
        const searchInput = document.getElementById('searchInput');
        let searchTimeout = null;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    // Mettre à jour l'URL pour permettre le partage du lien filtré
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData).toString();
                    const newUrl = form.action + (params ? '?' + params : '');
                    window.history.pushState({ path: newUrl }, '', newUrl);
                    
                    fetchTable();
                }, 400); // Délai de 400ms après la dernière frappe
            });
        }

        // ── Changement automatique sur les filtres dropdown ──
        ['filterStatut', 'filterType', 'filterDevise', 'filterZone'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function() {
                    fetchTable();
                });
            }
        });

        // ── AJAX : mise à jour du tableau uniquement ─
        function fetchTable() {
            if (isSubmitting) return;
            isSubmitting = true;

            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            const url = form.action + (params ? '?' + params : '');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.ok ? response.text() : Promise.reject())
            .then(html => {
                container.innerHTML = html;
                bindPaginationLinks();
                isSubmitting = false;
            })
            .catch(() => {
                isSubmitting = false;
                form.submit();
            });
        }

        // ── Pagination AJAX ──
        function bindPaginationLinks() {
            container.querySelectorAll('.pagination a, .pagination button').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (!href || href === '#') return;

                    fetch(href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(r => r.ok ? r.text() : Promise.reject())
                    .then(html => {
                        container.innerHTML = html;
                        bindPaginationLinks();
                    })
                    .catch(() => { window.location.href = href; });
                });
            });
        }

        // ── Selects & dates : changement immédiat ──
        form.addEventListener('change', function(e) {
            const el = e.target;
            if (el.tagName === 'SELECT' || el.type === 'date') {
                fetchTable();
            }
        });

        // ── Texte : recherche INSTANTANÉE (pas de délai) ─
        form.addEventListener('input', function(e) {
            if (e.target.type === 'text') fetchTable();
        });

        // ── Entrée = recherche immédiate, Echap = vider ──
        form.addEventListener('keydown', function(e) {
            if (e.target.type !== 'text') return;
            if (e.key === 'Enter') { e.preventDefault(); fetchTable(); }
            if (e.key === 'Escape') { e.preventDefault(); e.target.value = ''; fetchTable(); }
        });

        // ── Select2 fallback ──
        if (window.jQuery && jQuery.fn.select2) {
            jQuery(form).on('select2:select select2:clear', 'select', fetchTable);
        }

        bindPaginationLinks();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFilters);
    } else {
        initFilters();
    }

    /* ─── Sortie impression / téléchargement ─── */
    // Reconstruit les champs cachés du formulaire d'impression à partir
    // des valeurs ACTUELLES du formulaire de filtres (même non soumises,
    // car cette page utilise la recherche AJAX sans rechargement complet).
    function syncPrintFilters() {
        const container = document.getElementById('printFiltersContainer');
        if (!container) return;
        container.innerHTML = '';
        const filterForm = document.getElementById('form-filtres');
        if (!filterForm) return;
        filterForm.querySelectorAll('input[name], select[name]').forEach(function (field) {
            if (!field.name || field.value === '' || field.value === null) return;
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = field.name;
            hidden.value = field.value;
            container.appendChild(hidden);
        });
    }

    $('#modalImpressionCredit').on('show.bs.modal', syncPrintFilters);

    $(document).on('click', '.js-print-action', function () {
        syncPrintFilters();
        $('#printOutputMode').val($(this).data('output') || 'stream');
        $('#printExportFormat').val($(this).data('format') || 'pdf');
    });
})();
</script>
@endpush

@push('css')
<style>
@media print {
    body * { visibility: hidden; }
    #printZone, #printZone * { visibility: visible; }
    #printZone { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print, .no-print * { display: none !important; }
    .print-only { display: block !important; }
    .alert-info { background: #f8f9fa !important; color: #000 !important; border: 1px solid #dee2e6 !important; }
    .card { border: none !important; box-shadow: none !important; }
    .table { font-size: 0.75rem; }
    .table th, .table td { padding: 3px 5px !important; }
    @page { margin: 1cm; size: A4 landscape; }
}
.print-only { display: none; }
</style>
@endpush

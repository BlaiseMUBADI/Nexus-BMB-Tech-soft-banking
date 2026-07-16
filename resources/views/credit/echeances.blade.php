@extends('layouts.app')

@section('page_title', 'Tombée d\'échéances')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Tombée d\'échéances')

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- ── Filtres (recherche progressive, sans bouton) ──────── --}}
    <div class="card mb-3">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0"><i class="fas fa-filter mr-2"></i>Filtres</h6>
        </div>
        <div class="card-body pb-2">
            <form method="GET" action="{{ route('credit.echeances') }}" id="form-filtres-echeances">
                <div class="row">
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Date d'échéance précise</label>
                        <input type="date" name="date_echeance" value="{{ request('date_echeance') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Du</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Au</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Devise</label>
                        <select name="devise" class="form-control form-control-sm">
                            <option value="">Toutes</option>
                            <option value="CDF" {{ request('devise')=='CDF'?'selected':'' }}>CDF</option>
                            <option value="USD" {{ request('devise')=='USD'?'selected':'' }}>USD</option>
                            <option value="EUR" {{ request('devise')=='EUR'?'selected':'' }}>EUR</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Zone</label>
                        <select name="zone" class="form-control form-control-sm">
                            <option value="">Toutes zones</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->code_zone }}" {{ request('zone') == $z->code_zone ? 'selected' : '' }}>{{ $z->nom ?? $z->code_zone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-block" data-toggle="modal" data-target="#modalImpressionEcheances" title="Imprimer la liste">
                            <i class="fas fa-print mr-1"></i> Imprimer
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Portefeuille</label>
                        <select name="portefeuille_id" class="form-control form-control-sm">
                            <option value="">Tous portefeuilles</option>
                            @foreach($portefeuilles as $p)
                                <option value="{{ $p->id }}" {{ request('portefeuille_id') == $p->id ? 'selected' : '' }}>{{ $p->nom_portefeuille }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <label class="small font-weight-bold mb-0">Statut échéance</label>
                        <select name="statut_echeance" class="form-control form-control-sm">
                            <option value="">En attente + En retard + Partiel</option>
                            <option value="EN_ATTENTE" {{ request('statut_echeance')=='EN_ATTENTE'?'selected':'' }}>En attente</option>
                            <option value="EN_RETARD" {{ request('statut_echeance')=='EN_RETARD'?'selected':'' }}>En retard</option>
                            <option value="PARTIELLEMENT_PAYE" {{ request('statut_echeance')=='PARTIELLEMENT_PAYE'?'selected':'' }}>Partiellement payé</option>
                            <option value="PAYE" {{ request('statut_echeance')=='PAYE'?'selected':'' }}>Payé</option>
                            <option value="REPORTE" {{ request('statut_echeance')=='REPORTE'?'selected':'' }}>Reporté</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2 d-flex align-items-end">
                        <small class="text-muted"><i class="fas fa-bolt mr-1 text-warning"></i>La recherche s'applique automatiquement dès qu'un critère change.</small>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Résultats (rafraîchis en AJAX) ────────────────────── --}}
    <div id="echeances-results-container">
        @include('credit._echeances_content')
    </div>

</div>
</section>

{{-- ===== Modal Impression Tombée d'échéances ===== --}}
<div class="modal fade" id="modalImpressionEcheances" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formImpressionEcheances" action="{{ route('credit.echeances.print') }}" method="GET" target="_blank">
                <input type="hidden" name="output" id="echPrintOutputMode" value="stream">
                <input type="hidden" name="export_format" id="echPrintExportFormat" value="pdf">
                <div id="echPrintFiltersContainer"></div>
                <div class="modal-body text-center py-4">
                    <p class="mb-2">Les filtres actuels seront appliqués à l'export.</p>
                    <p class="text-muted small mb-0">Choisissez le format de sortie :</p>
                </div>
                <div class="modal-footer py-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary js-ech-print-action" data-output="stream" data-format="pdf">
                        <i class="fas fa-file-pdf mr-1"></i> Ouvrir PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-primary js-ech-print-action" data-output="download" data-format="pdf">
                        <i class="fas fa-download mr-1"></i> Télécharger PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-success js-ech-print-action" data-output="download" data-format="csv">
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
(function () {
    'use strict';

    function initEcheancesFilters() {
        const form = document.getElementById('form-filtres-echeances');
        const container = document.getElementById('echeances-results-container');
        if (!form || !container) return;

        let isFetching = false;
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.content : '';

        function fetchResults() {
            if (isFetching) return;
            isFetching = true;

            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            const url = form.action + (params ? '?' + params : '');

            // Met à jour l'URL pour permettre le partage du lien filtré
            window.history.pushState({ path: url }, '', url);

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
                if (window.jQuery) { jQuery('[data-toggle="tooltip"]').tooltip(); }
                isFetching = false;
            })
            .catch(() => {
                isFetching = false;
                form.submit();
            });
        }

        function bindPaginationLinks() {
            container.querySelectorAll('.pagination a, .pagination button').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (!href || href === '#') return;

                    window.history.pushState({ path: href }, '', href);

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
                        if (window.jQuery) { jQuery('[data-toggle="tooltip"]').tooltip(); }
                    })
                    .catch(() => { window.location.href = href; });
                });
            });
        }

        // ── Changement immédiat sur tous les champs (dates, selects) ──
        form.addEventListener('change', function () {
            fetchResults();
        });

        // ── Select2 fallback ──
        if (window.jQuery && jQuery.fn.select2) {
            jQuery(form).on('select2:select select2:clear', 'select', fetchResults);
        }

        bindPaginationLinks();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEcheancesFilters);
    } else {
        initEcheancesFilters();
    }

    /* ─── Sortie impression / téléchargement ─── */
    function syncEchPrintFilters() {
        const container = document.getElementById('echPrintFiltersContainer');
        if (!container) return;
        container.innerHTML = '';
        const filterForm = document.getElementById('form-filtres-echeances');
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

    if (window.jQuery) {
        jQuery('#modalImpressionEcheances').on('show.bs.modal', syncEchPrintFilters);

        jQuery('.js-ech-print-action').on('click', function () {
            syncEchPrintFilters();
            jQuery('#echPrintOutputMode').val(jQuery(this).data('output') || 'stream');
            jQuery('#echPrintExportFormat').val(jQuery(this).data('format') || 'pdf');
        });

        jQuery('[data-toggle="tooltip"]').tooltip();
    }
})();
</script>
@endpush

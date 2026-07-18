@extends('layouts.app')

@section('page_title', 'Journal Comptable')
@section('breadcrumb_parent', 'Comptabilite')
@section('breadcrumb', 'Journal')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-info shadow-sm mb-3">
        <div class="card-body">
            <form id="form-filtres-journal-compta" class="form-row">
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Date début</label>
                    <input type="date" class="form-control form-control-sm" name="date_debut" value="{{ $filters['date_debut'] }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Date fin</label>
                    <input type="date" class="form-control form-control-sm" name="date_fin" value="{{ $filters['date_fin'] }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Type pièce</label>
                    <select class="form-control form-control-sm" name="type_piece">
                        <option value="">Tous</option>
                        @foreach(['OPERATION', 'ANNULATION', 'REGULARISATION'] as $type)
                            <option value="{{ $type }}" {{ $filters['type_piece'] === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="small mb-1">Référence</label>
                    <input type="text" class="form-control form-control-sm" name="reference" value="{{ $filters['reference'] }}" placeholder="Référence journal ou opération">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-block" data-toggle="modal" data-target="#modalImpressionJournalCompta">
                        <i class="fas fa-print mr-1"></i>Imprimer
                    </button>
                </div>
            </form>
            <small class="text-muted"><i class="fas fa-bolt text-warning mr-1"></i>La recherche s'applique automatiquement dès qu'un critère change.</small>
        </div>
    </div>

    <div id="journal-compta-results">
        @include('comptabilite._journal_content')
    </div>
</div>

{{-- ===== Modal Impression Journal Comptable ===== --}}
<div class="modal fade" id="modalImpressionJournalCompta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formImpressionJournalCompta" action="{{ route('comptabilite.journal.print') }}" method="GET" target="_blank">
                <input type="hidden" name="output" id="jcPrintOutputMode" value="stream">
                <input type="hidden" name="export_format" id="jcPrintExportFormat" value="pdf">
                <div id="jcPrintFiltersContainer"></div>
                <div class="modal-body text-center py-4">
                    <p class="mb-2">Les filtres actuels seront appliqués à l'export.</p>
                    <p class="text-muted small mb-0">Choisissez le format de sortie :</p>
                </div>
                <div class="modal-footer py-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary js-jc-print-action" data-output="stream" data-format="pdf">
                        <i class="fas fa-file-pdf mr-1"></i> Ouvrir PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-primary js-jc-print-action" data-output="download" data-format="pdf">
                        <i class="fas fa-download mr-1"></i> Télécharger PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-success js-jc-print-action" data-output="download" data-format="csv">
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
    const form = document.getElementById('form-filtres-journal-compta');
    const container = document.getElementById('journal-compta-results');

    function fetchResults(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(r => r.text())
            .then(html => { container.innerHTML = html; bindPagination(); })
            .catch(() => { window.location.href = url; });
    }

    function currentUrl() {
        const params = new URLSearchParams(new FormData(form));
        const url = '{{ route("comptabilite.journal") }}' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', url);
        return url;
    }

    function bindPagination() {
        container.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                if (!href) return;
                window.history.pushState({}, '', href);
                fetchResults(href);
            });
        });
    }

    form.addEventListener('change', () => fetchResults(currentUrl()));
    bindPagination();

    function syncPrintFilters() {
        const c = document.getElementById('jcPrintFiltersContainer');
        c.innerHTML = '';
        form.querySelectorAll('input[name], select[name]').forEach(f => {
            if (!f.value) return;
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = f.name; h.value = f.value;
            c.appendChild(h);
        });
    }
    $('#modalImpressionJournalCompta').on('show.bs.modal', syncPrintFilters);
    $('.js-jc-print-action').on('click', function () {
        syncPrintFilters();
        $('#jcPrintOutputMode').val($(this).data('output') || 'stream');
        $('#jcPrintExportFormat').val($(this).data('format') || 'pdf');
    });
})();
</script>
@endpush

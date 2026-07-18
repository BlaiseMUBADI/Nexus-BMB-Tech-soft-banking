@extends('layouts.app')

@section('page_title', 'Balance Générale')
@section('breadcrumb_parent', 'Comptabilite')
@section('breadcrumb', 'Balance Générale')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-info shadow-sm mb-3">
        <div class="card-body">
            <form id="form-filtres-balance" class="form-row">
                <div class="col-md-3 mb-2">
                    <label class="small mb-1">Exercice comptable</label>
                    <select class="form-control form-control-sm" name="exercice_id">
                        <option value="">— Dates libres —</option>
                        @foreach($exercices as $ex)
                            <option value="{{ $ex->id }}">{{ $ex->annee }} ({{ $ex->statut }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small mb-1">Date début</label>
                    <input type="date" class="form-control form-control-sm" name="date_debut">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small mb-1">Date fin</label>
                    <input type="date" class="form-control form-control-sm" name="date_fin">
                </div>
                <div class="col-md-3 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-block" data-toggle="modal" data-target="#modalImpressionBalance">
                        <i class="fas fa-print mr-1"></i>Imprimer
                    </button>
                </div>
            </form>
            <small class="text-muted"><i class="fas fa-bolt text-warning mr-1"></i>La recherche s'applique automatiquement dès qu'un critère change. Laissez les dates vides pour la balance depuis l'origine.</small>
        </div>
    </div>

    <div id="balance-results">
        @include('comptabilite._balance_content')
    </div>
</div>

<div class="modal fade" id="modalImpressionBalance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formImpressionBalance" action="{{ route('comptabilite.balance.print') }}" method="GET" target="_blank">
                <input type="hidden" name="output" id="balPrintOutputMode" value="stream">
                <input type="hidden" name="export_format" id="balPrintExportFormat" value="pdf">
                <div id="balPrintFiltersContainer"></div>
                <div class="modal-body text-center py-4">
                    <p class="text-muted small mb-0">Choisissez le format de sortie :</p>
                </div>
                <div class="modal-footer py-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary js-bal-print-action" data-output="stream" data-format="pdf"><i class="fas fa-file-pdf mr-1"></i> Ouvrir PDF</button>
                    <button type="submit" class="btn btn-sm btn-outline-primary js-bal-print-action" data-output="download" data-format="pdf"><i class="fas fa-download mr-1"></i> Télécharger PDF</button>
                    <button type="submit" class="btn btn-sm btn-success js-bal-print-action" data-output="download" data-format="csv"><i class="fas fa-file-csv mr-1"></i> Télécharger CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    const form = document.getElementById('form-filtres-balance');
    const container = document.getElementById('balance-results');

    function fetchResults(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(r => r.text()).then(html => { container.innerHTML = html; })
            .catch(() => { window.location.href = url; });
    }
    function currentUrl() {
        const params = new URLSearchParams(new FormData(form));
        const url = '{{ route("comptabilite.balance") }}' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', url);
        return url;
    }
    form.addEventListener('change', () => fetchResults(currentUrl()));

    function syncPrintFilters() {
        const c = document.getElementById('balPrintFiltersContainer');
        c.innerHTML = '';
        form.querySelectorAll('input[name]').forEach(f => {
            if (!f.value) return;
            const h = document.createElement('input'); h.type = 'hidden'; h.name = f.name; h.value = f.value;
            c.appendChild(h);
        });
    }
    $('#modalImpressionBalance').on('show.bs.modal', syncPrintFilters);
    $('.js-bal-print-action').on('click', function () {
        syncPrintFilters();
        $('#balPrintOutputMode').val($(this).data('output') || 'stream');
        $('#balPrintExportFormat').val($(this).data('format') || 'pdf');
    });
})();
</script>
@endpush

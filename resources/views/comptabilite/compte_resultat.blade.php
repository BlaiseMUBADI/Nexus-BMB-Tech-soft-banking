@extends('layouts.app')

@section('page_title', 'Compte de Résultat')
@section('breadcrumb_parent', 'Comptabilite')
@section('breadcrumb', 'Compte de Résultat')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-info shadow-sm mb-3">
        <div class="card-body">
            <form id="form-filtres-cr" class="form-row">
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
                    <button type="button" class="btn btn-sm btn-outline-primary btn-block" data-toggle="modal" data-target="#modalImpressionCR">
                        <i class="fas fa-print mr-1"></i>Imprimer
                    </button>
                </div>
            </form>
            <small class="text-muted"><i class="fas fa-bolt text-warning mr-1"></i>La recherche s'applique automatiquement dès qu'un critère change.</small>
        </div>
    </div>

    <div id="cr-results">
        @include('comptabilite._compte_resultat_content')
    </div>
</div>

<div class="modal fade" id="modalImpressionCR" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formImpressionCR" action="{{ route('comptabilite.compte-resultat.print') }}" method="GET" target="_blank">
                <input type="hidden" name="output" id="crPrintOutputMode" value="stream">
                <div id="crPrintFiltersContainer"></div>
                <div class="modal-body text-center py-4">
                    <p class="text-muted small mb-0">Ce document est disponible uniquement en PDF.</p>
                </div>
                <div class="modal-footer py-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary js-cr-print-action" data-output="stream"><i class="fas fa-file-pdf mr-1"></i> Ouvrir PDF</button>
                    <button type="submit" class="btn btn-sm btn-outline-primary js-cr-print-action" data-output="download"><i class="fas fa-download mr-1"></i> Télécharger PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    const form = document.getElementById('form-filtres-cr');
    const container = document.getElementById('cr-results');
    function fetchResults(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(r => r.text()).then(html => { container.innerHTML = html; })
            .catch(() => { window.location.href = url; });
    }
    function currentUrl() {
        const params = new URLSearchParams(new FormData(form));
        const url = '{{ route("comptabilite.compte-resultat") }}' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', url);
        return url;
    }
    form.addEventListener('change', () => fetchResults(currentUrl()));

    function syncPrintFilters() {
        const c = document.getElementById('crPrintFiltersContainer');
        c.innerHTML = '';
        form.querySelectorAll('input[name]').forEach(f => {
            if (!f.value) return;
            const h = document.createElement('input'); h.type = 'hidden'; h.name = f.name; h.value = f.value;
            c.appendChild(h);
        });
    }
    $('#modalImpressionCR').on('show.bs.modal', syncPrintFilters);
    $('.js-cr-print-action').on('click', function () {
        syncPrintFilters();
        $('#crPrintOutputMode').val($(this).data('output') || 'stream');
    });
})();
</script>
@endpush

@extends('layouts.app')

@section('page_title', 'Grand Livre')
@section('breadcrumb_parent', 'Comptabilite')
@section('breadcrumb', 'Grand Livre')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-info shadow-sm mb-3">
        <div class="card-body">
            <form id="form-filtres-grand-livre" class="form-row">
                <div class="col-md-5 mb-2">
                    <label class="small mb-1">Compte comptable</label>
                    <select class="form-control" style="width:100%;" id="selCompteGrandLivre" name="numero_compte">
                        <option value="">— Sélectionner un compte —</option>
                        @foreach($comptes as $c)
                            <option value="{{ $c->numero_compte }}" {{ $numeroCompte === $c->numero_compte ? 'selected' : '' }}>
                                {{ $c->numero_compte }} — {{ $c->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Date début</label>
                    <input type="date" class="form-control form-control-sm" name="date_debut" value="{{ $dateDebut }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Date fin</label>
                    <input type="date" class="form-control form-control-sm" name="date_fin" value="{{ $dateFin }}">
                </div>
                <div class="col-md-3 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-block" data-toggle="modal" data-target="#modalImpressionGrandLivre">
                        <i class="fas fa-print mr-1"></i>Imprimer
                    </button>
                </div>
            </form>
            <small class="text-muted"><i class="fas fa-bolt text-warning mr-1"></i>La recherche s'applique automatiquement dès qu'un critère change.</small>
        </div>
    </div>

    <div id="grand-livre-results">
        @include('comptabilite._grand_livre_content', ['compte' => $numeroCompte ? \App\Models\Comptabilite\PlanComptable::find($numeroCompte) : null])
    </div>
</div>

{{-- ===== Modal Impression Grand Livre ===== --}}
<div class="modal fade" id="modalImpressionGrandLivre" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formImpressionGrandLivre" action="{{ route('comptabilite.grand-livre.print') }}" method="GET" target="_blank">
                <input type="hidden" name="output" id="glPrintOutputMode" value="stream">
                <input type="hidden" name="export_format" id="glPrintExportFormat" value="pdf">
                <div id="glPrintFiltersContainer"></div>
                <div class="modal-body text-center py-4">
                    <p class="mb-2">Le grand livre du compte sélectionné sera imprimé.</p>
                    <p class="text-muted small mb-0">Choisissez le format de sortie :</p>
                </div>
                <div class="modal-footer py-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary js-gl-print-action" data-output="stream" data-format="pdf">
                        <i class="fas fa-file-pdf mr-1"></i> Ouvrir PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-primary js-gl-print-action" data-output="download" data-format="pdf">
                        <i class="fas fa-download mr-1"></i> Télécharger PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-success js-gl-print-action" data-output="download" data-format="csv">
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
    function _matcher(params, data) {
        if (!params.term || params.term.trim() === '') return data;
        const term = params.term.trim().toUpperCase();
        const text = (data.text || '').toUpperCase();
        return text.indexOf(term) > -1 ? data : null;
    }
    $('#selCompteGrandLivre').select2({
        theme: 'bootstrap4', width: '100%', dropdownParent: $('body'),
        placeholder: '— Sélectionner un compte —', allowClear: true, matcher: _matcher,
        language: { noResults: function () { return 'Aucun compte trouvé.'; } },
    });

    const form = document.getElementById('form-filtres-grand-livre');
    const container = document.getElementById('grand-livre-results');

    function fetchResults(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(r => r.text())
            .then(html => { container.innerHTML = html; })
            .catch(() => { window.location.href = url; });
    }

    function currentUrl() {
        const params = new URLSearchParams(new FormData(form));
        const url = '{{ route("comptabilite.grand-livre") }}' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', url);
        return url;
    }

    form.addEventListener('change', () => fetchResults(currentUrl()));
    $('#selCompteGrandLivre').on('change', () => fetchResults(currentUrl()));

    function syncPrintFilters() {
        const c = document.getElementById('glPrintFiltersContainer');
        c.innerHTML = '';
        form.querySelectorAll('input[name], select[name]').forEach(f => {
            if (!f.value) return;
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = f.name; h.value = f.value;
            c.appendChild(h);
        });
    }
    $('#modalImpressionGrandLivre').on('show.bs.modal', syncPrintFilters);
    $('.js-gl-print-action').on('click', function () {
        syncPrintFilters();
        $('#glPrintOutputMode').val($(this).data('output') || 'stream');
        $('#glPrintExportFormat').val($(this).data('format') || 'pdf');
    });
})();
</script>
@endpush

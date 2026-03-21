{{-- ============================================================
     Journal des Opérations de Caisse
     Historique filtrable : date, type, statut
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'Journal de Caisse')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Journal des Opérations')

@section('content')
<div class="container-fluid">

    @if(!$guichet)
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-warning card-outline shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-book fa-4x text-muted mb-3"></i>
                    <h4>Aucun guichet assigné</h4>
                    <p class="text-muted">Contactez un administrateur.</p>
                </div>
            </div>
        </div>
    </div>
    @else

    {{-- ── Filtres ─────────────────────────────────────────────── --}}
    <div class="card card-outline card-info shadow mb-3">
        <div class="card-body py-2">
            <div class="row align-items-end journal-filter-row">
                <div class="col-12 col-sm-6 col-md-auto mb-2 mb-md-0">
                    <label class="mb-1 small font-weight-bold">Date</label>
                    <input type="date" class="form-control form-control-sm journal-filter-control" id="filtreDate"
                           value="{{ today()->toDateString() }}" style="width:160px;">
                </div>
                <div class="col-12 col-sm-6 col-md-auto mb-2 mb-md-0">
                    <label class="mb-1 small font-weight-bold">Type</label>
                    <select class="form-control form-control-sm journal-filter-control" id="filtreType" style="width:190px;">
                        <option value="TOUS">— Tous les types —</option>
                        @foreach(($operationTypeOptions ?? []) as $operationType)
                            <option value="{{ $operationType['value'] }}">{{ $operationType['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto journal-filter-actions">
                    <button class="btn btn-info btn-sm" id="btnFiltrer">
                        <i class="fas fa-search mr-1"></i> Filtrer
                    </button>
                    <button class="btn btn-outline-secondary btn-sm ml-1" id="btnExportCSV" title="Export CSV">
                        <i class="fas fa-file-csv mr-1"></i> CSV
                    </button>
                </div>
                <div class="col-12 col-md-auto ml-md-auto journal-meta-wrap">
                    <small class="text-muted" id="journalMeta"></small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Totaux par type ─────────────────────────────────────── --}}
    <div id="journalTotaux" class="row mb-3"></div>

    {{-- ── Tableau ─────────────────────────────────────────────── --}}
    <div class="card card-outline card-secondary shadow">
        <div class="card-header d-flex align-items-center justify-content-between py-2">
            <h6 class="mb-0">
                <i class="fas fa-book-open mr-1 text-secondary"></i>
                Opérations
                <span class="badge badge-secondary ml-1" id="journalCount">0</span>
            </h6>
            <button class="btn btn-xs btn-outline-secondary" id="btnRefreshJournal">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="tableJournal">
                    <thead class="thead-dark">
                        <tr>
                            <th></th>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Compte</th>
                            <th>Client</th>
                            <th>Devise</th>
                            <th>Montant</th>
                            <th>Destination</th>
                            <th>Heure</th>
                            <th>Statut</th>
                            <th>Obs.</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyJournal">
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Chargement…
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @endif
</div>
@endsection


@push('css')
<style>
    #tableJournal td, #tableJournal th { vertical-align: middle; }
    #tableJournal td {
        font-size: .95rem;
        line-height: 1.35;
        padding-top: .55rem;
        padding-bottom: .55rem;
    }
    #tableJournal th {
        font-size: .90rem;
        line-height: 1.25;
        padding-top: .60rem;
        padding-bottom: .60rem;
    }
    #tableJournal .client-name-text {
        font-size: 1.08rem;
        font-weight: 600;
        line-height: 1.35;
    }
    .badge-sm { font-size: .72rem; padding: .15em .4em; }
    .btn-xs   { padding: .15rem .45rem; font-size: .78rem; }
    /* Stat mini-cards totaux */
    .stat-type-card {
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.10);
        border-radius: 8px;
        padding: .6rem 1rem;
        text-align: center;
        min-width: 130px;
    }
    .stat-type-card .st-count { font-size: 1.4rem; font-weight: 700; }
    .stat-type-card .st-label { font-size: .8rem; text-transform: uppercase; letter-spacing: .06em; color: #8a9bb0; }
    .stat-type-card .st-devises { font-size: .78rem; color: #adb5bd; margin-top: 2px; }

    @media (max-width: 767.98px) {
        .journal-filter-control {
            width: 100% !important;
        }

        .journal-filter-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .journal-filter-actions .btn {
            flex: 1 1 auto;
            margin-left: 0 !important;
        }

        .journal-meta-wrap {
            margin-top: .25rem;
        }

        #journalTotaux .col-auto {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .stat-type-card {
            min-width: 0;
            width: 100%;
        }
    }
</style>
@endpush


@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' } });

    @if($guichet)
    var urlJournal = '{{ route("caisses.journal.data") }}';
    var _currentData = [];

    // ── Chargement du journal ─────────────────────────────────────
    function chargerJournal() {
        var date = $('#filtreDate').val();
        var type = $('#filtreType').val();

        $('#btnRefreshJournal').find('i').addClass('fa-spin');

        $.getJSON(urlJournal, { date: date, type: type })
        .done(function (data) {
            _currentData = data.operations || [];
            afficherJournal(_currentData);
            afficherTotaux(data.totaux || []);
            $('#journalMeta').text(data.total_count + ' résultat(s) pour le ' + (data.date || date));
        })
        .fail(function (xhr) {
            logFrontendError('Erreur de chargement journal', 'Chargement journal caisse', xhr.status);
            $('#tbodyJournal').html('<tr><td colspan="11" class="text-danger text-center py-3">Erreur de chargement.</td></tr>');
        })
        .always(function () {
            $('#btnRefreshJournal').find('i').removeClass('fa-spin');
        });
    }

    function afficherJournal(ops) {
        $('#journalCount').text(ops.length);
        var tbody = $('#tbodyJournal');
        if (!ops.length) {
            tbody.html('<tr><td colspan="11" class="text-center py-5 text-muted">'
                + '<i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune opération.</td></tr>');
            return;
        }
        tbody.empty();
        $.each(ops, function(i, op) {
            var rowClass = op.statut === 'ANNULE' ? ' class="text-muted strikethrough-row"' : '';
            var statutBadge = op.statut === 'ANNULE'
                ? '<span class="badge badge-secondary badge-sm">Annulée</span>'
                : '<span class="badge badge-success badge-sm">OK</span>';
            var dest = op.montant_dest_fmt
                ? '<span class="text-info">→ ' + op.montant_dest_fmt + '</span>' : '—';
            var obs = op.observations
                ? '<span title="' + $('<div>').text(op.observations).html() + '" style="cursor:help;"><i class="fas fa-comment-alt text-info fa-sm"></i></span>' : '';
            var compteCell = '—';
            if (op.compte_code) {
                compteCell = '<small class="text-muted"><i class="fas fa-university fa-xs mr-1"></i>' + $('<div>').text(op.compte_code).html() + '</small>';
            }
            var clientCell = op.client_full_name
                ? '<span class="text-info client-name-text"><i class="fas fa-user fa-xs mr-1"></i>' + $('<div>').text(op.client_full_name).html() + '</span>'
                : '—';
            tbody.append(
                '<tr' + rowClass + '>'
                + '<td class="text-center"><i class="fas ' + op.icon + ' fa-sm"></i></td>'
                + '<td><small class="text-monospace">' + op.reference + '</small></td>'
                + '<td><span class="badge ' + op.badge_class + ' badge-sm">' + op.type_label + '</span></td>'
                + '<td>' + compteCell + '</td>'
                + '<td>' + clientCell + '</td>'
                + '<td>' + op.devise + '</td>'
                + '<td class="font-weight-bold">' + op.montant_fmt + '</td>'
                + '<td><small>' + dest + '</small></td>'
                + '<td><small>' + (op.date ? op.date.substr(11,8) : '') + '</small></td>'
                + '<td>' + statutBadge + '</td>'
                + '<td class="text-center">' + obs + '</td>'
                + '</tr>'
            );
        });
    }

    function afficherTotaux(totaux) {
        var icons = { DEPOT:'fa-arrow-down text-success', RETRAIT:'fa-arrow-up text-danger',
            CHANGE:'fa-exchange-alt text-info', PAIEMENT:'fa-file-invoice-dollar text-primary',
            REMBOURSEMENT:'fa-undo text-warning' };
        var container = $('#journalTotaux');
        container.empty();
        if (!totaux.length) return;
        $.each(totaux, function(i, t) {
            var devStr = '';
            if (t.par_devise) {
                $.each(t.par_devise, function(j, d) {
                    devStr += d.fmt + (j < t.par_devise.length - 1 ? ', ' : '');
                });
            }
            container.append(
                '<div class="col-auto mb-2">'
                + '<div class="stat-type-card">'
                + '<div class="st-count"><i class="fas ' + (icons[t.type] || 'fa-circle') + ' fa-sm mr-1"></i>' + t.count + '</div>'
                + '<div class="st-label">' + t.type_label + '</div>'
                + (devStr ? '<div class="st-devises">' + devStr + '</div>' : '')
                + '</div></div>'
            );
        });
    }

    // ── Export CSV basique ────────────────────────────────────────
    $('#btnExportCSV').on('click', function () {
        if (!_currentData.length) { return; }
        var csvLines = ['"Référence","Type","Compte","Client","Devise","Montant","DeviseDest","MontantDest","Statut","Date"'];
        $.each(_currentData, function(i, op) {
            csvLines.push([
                '"' + op.reference + '"',
                '"' + op.type_label + '"',
                '"' + (op.compte_code || '') + '"',
                '"' + (op.client_full_name || '') + '"',
                '"' + op.devise + '"',
                '"' + op.montant + '"',
                '"' + (op.devise_dest || '') + '"',
                '"' + (op.montant_dest_fmt || '') + '"',
                '"' + op.statut + '"',
                '"' + (op.date || '') + '"'
            ].join(','));
        });
        var blob = new Blob([csvLines.join('\n')], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'journal_caisse_' + $('#filtreDate').val() + '.csv';
        link.click();
    });

    $('#btnFiltrer, #btnRefreshJournal').on('click', chargerJournal);
    $('#filtreDate, #filtreType').on('change', chargerJournal);

    // Premier chargement
    chargerJournal();
    setInterval(chargerJournal, 60000);

    @endif
});
</script>
@endpush

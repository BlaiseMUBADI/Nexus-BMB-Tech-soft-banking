@extends('layouts.app')

@section('page_title', 'Historique - Recouvrement Automatique')
@section('breadcrumb_parent', 'Crédit')
@section('breadcrumb', 'Historique Recouvrement')

@section('content')
<section class="content">
<div class="container-fluid pt-3">

    <div class="row mb-3 no-print">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group shadow-sm" role="group">
                    <a href="{{ route('recouvrement.index') }}" class="btn btn-outline-primary btn-sm px-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Dossiers en retard
                    </a>
                    <button type="button" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-history mr-1"></i> Historique
                        <span class="badge badge-light ml-1">{{ $historique->total() }}</span>
                    </button>
                </div>
                <div class="text-right">
                    <span class="badge badge-success badge-lg px-3 py-2 mr-2" style="font-size:1rem;">
                        <i class="fas fa-coins mr-1"></i> Total récupéré : {{ number_format($totalRecupere ?? 0, 2, ',', ' ') }} CDF
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="card shadow-sm mb-3 no-print">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0"><i class="fas fa-filter mr-2"></i>Filtres</h6>
        </div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('recouvrement.historique') }}" id="formFiltres">
                <div class="row">
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Date début</label>
                        <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Date fin</label>
                        <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ request('date_fin') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Zone</label>
                        <select name="zone" class="form-control form-control-sm">
                            <option value="">-- Toutes --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->code_zone }}" {{ request('zone') == $zone->code_zone ? 'selected' : '' }}>{{ $zone->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalImpressionHistorique">
                            <i class="fas fa-print mr-1"></i> Imprimer
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Portefeuille</label>
                        <select name="portefeuille" class="form-control form-control-sm">
                            <option value="">-- Tous --</option>
                            @foreach($portefeuilles as $pf)
                                <option value="{{ $pf->id }}" {{ request('portefeuille') == $pf->id ? 'selected' : '' }}>{{ $pf->nom_portefeuille }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Agent déclencheur</label>
                        <select name="agent_declencheur" class="form-control form-control-sm">
                            <option value="">-- Tous --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->matricule }}" {{ request('agent_declencheur') == $agent->matricule ? 'selected' : '' }}>{{ $agent->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Devise</label>
                        <select name="devise" class="form-control form-control-sm">
                            <option value="">-- Toutes --</option>
                            <option value="CDF" {{ request('devise') == 'CDF' ? 'selected' : '' }}>CDF</option>
                            <option value="USD" {{ request('devise') == 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm btn-primary mr-2">
                            <i class="fas fa-search mr-1"></i> Filtrer
                        </button>
                        <a href="{{ route('recouvrement.historique') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times mr-1"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE HISTORIQUE --}}
    <div class="card shadow-sm" id="printZone">
        <div class="print-only text-center mb-3" style="display:none;">
            <h3 class="font-weight-bold">Coopec EBEN - Historique des Recouvrements Automatiques</h3>
            <p class="text-muted">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
            <hr>
        </div>
        <div class="card-header d-flex justify-content-between align-items-center bg-light py-2 no-print">
            <h6 class="mb-0">
                <i class="fas fa-history text-primary mr-2"></i>
                Recouvrements effectués
                <span class="badge badge-primary ml-2">{{ $historique->total() }}</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:140px;">Date/Heure</th>
                            <th>Dossier</th>
                            <th>Client</th>
                            <th>Zone</th>
                            <th>Portefeuille</th>
                            <th class="text-right">Montant prélevé</th>
                            <th class="text-right">Solde avant</th>
                            <th class="text-right">Solde après</th>
                            <th>Échéance</th>
                            <th class="text-center">Statut</th>
                            <th>Référence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historique as $tx)
                        @php
                            $client = $tx->compte->client ?? null;
                            $zone = $client->zone ?? null;
                            $portefeuille = $tx->compte->portefeuille ?? null;
                            $echNumero = '';
                            if (preg_match('/Ech\.\s*(\d+)/', $tx->observations ?? '', $m)) {
                                $echNumero = $m[1];
                            }
                            $isPartiel = stripos($tx->observations ?? '', 'partiel') !== false;
                        @endphp
                        <tr>
                            <td class="text-nowrap small">{{ $tx->date_operation->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $numeroDossier = '';
                                    if (preg_match('/AUTO-REC-(.+?)-\d+-/', $tx->reference ?? '', $m)) {
                                        $numeroDossier = $m[1];
                                    }
                                @endphp
                                @if($numeroDossier)
                                    <a href="{{ route('credit.show', ['dossier' => $numeroDossier]) }}" class="font-weight-bold text-primary text-decoration-none" title="Voir le dossier">
                                        <i class="fas fa-file-invoice mr-1"></i>{{ $numeroDossier }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $client ? $client->full_name : '-' }}</td>
                            <td>{{ $zone ? $zone->nom : '-' }}</td>
                            <td>{{ $portefeuille ? $portefeuille->nom_portefeuille : '-' }}</td>
                            <td class="text-right text-success font-weight-bold">{{ number_format($tx->montant, 2, ',', ' ') }} <small>{{ $tx->devise_code }}</small></td>
                            <td class="text-right text-muted">{{ number_format($tx->solde_compte_avant, 2, ',', ' ') }}</td>
                            <td class="text-right text-muted">{{ number_format($tx->solde_compte_apres, 2, ',', ' ') }}</td>
                            <td class="text-center">Éch. {{ $echNumero ?: '-' }}</td>
                            <td class="text-center">
                                @if($isPartiel)
                                    <span class="badge badge-warning" title="Recouvrement partiel"><i class="fas fa-hourglass-half mr-1"></i>Partiel</span>
                                @else
                                    <span class="badge badge-success" title="Recouvrement total"><i class="fas fa-check mr-1"></i>Total</span>
                                @endif
                            </td>
                            <td class="small text-muted" style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $tx->reference }}">{{ $tx->reference }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Aucun recouvrement automatique enregistré.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($historique->hasPages())
        <div class="card-footer bg-light py-2">
            {{ $historique->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
</section>

{{-- ===== Modal Impression Historique ===== --}}
<div class="modal fade" id="modalImpressionHistorique" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formImpressionHistorique" action="{{ route('recouvrement.historique.print') }}" method="GET" target="_blank">
                <input type="hidden" name="output" id="histPrintOutputMode" value="stream">
                <input type="hidden" name="export_format" id="histPrintExportFormat" value="pdf">
                <div id="histPrintFiltersContainer"></div>
                <div class="modal-body text-center py-4">
                    <p class="mb-2">Les filtres actuels seront appliqués à l'export.</p>
                    <p class="text-muted small mb-0">Choisissez le format de sortie :</p>
                </div>
                <div class="modal-footer py-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary js-hist-print-action" data-output="stream" data-format="pdf">
                        <i class="fas fa-file-pdf mr-1"></i> Ouvrir PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-primary js-hist-print-action" data-output="download" data-format="pdf">
                        <i class="fas fa-download mr-1"></i> Télécharger PDF
                    </button>
                    <button type="submit" class="btn btn-sm btn-success js-hist-print-action" data-output="download" data-format="csv">
                        <i class="fas fa-file-csv mr-1"></i> Télécharger CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
.print-only { display: none !important; }
@media print {
    body * { visibility: hidden; }
    #printZone, #printZone * { visibility: visible; }
    #printZone { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print, .no-print * { display: none !important; }
    .print-only { display: block !important; }
    .card { border: none !important; box-shadow: none !important; }
    .table { font-size: 0.8rem; }
    .table th, .table td { padding: 4px 6px !important; }
    @page { margin: 1cm; size: landscape; }
}
</style>
@endpush

@push('js')
<script>
$(function () {
    // Reconstruit les champs cachés du formulaire d'impression à partir
    // des valeurs ACTUELLES du formulaire de filtres (même non encore soumises).
    function syncHistPrintFilters() {
        var $container = $('#histPrintFiltersContainer');
        $container.empty();
        $('#formFiltres').find('input[name], select[name]').each(function () {
            var name = $(this).attr('name');
            var value = $(this).val();
            if (value === null || value === undefined || value === '') return;
            $container.append(
                $('<input>').attr({ type: 'hidden', name: name, value: value })
            );
        });
    }

    // Synchronise à l'ouverture du modal ET juste avant l'envoi (sécurité)
    $('#modalImpressionHistorique').on('show.bs.modal', syncHistPrintFilters);

    $('.js-hist-print-action').on('click', function () {
        syncHistPrintFilters();
        $('#histPrintOutputMode').val($(this).data('output') || 'stream');
        $('#histPrintExportFormat').val($(this).data('format') || 'pdf');
    });
});
</script>
@endpush

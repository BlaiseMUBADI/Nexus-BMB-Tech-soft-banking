@extends('layouts.app')

@section('page_title', 'Approvisionnement Trésorerie')
@section('breadcrumb_parent', 'Trésorerie')
@section('breadcrumb', 'Approvisionnement / Intercaisse')

@section('content')
<div class="container-fluid">

    @if(!$coffre)
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Coffre central introuvable.</strong>
                Configurez un guichet avec <code>type_guichet = CENTRAL</code>.
            </div>
        </div>
    </div>
    @else

    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-warning card-outline shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-lock mr-2 text-warning"></i>
                        <strong>COFFRE-FORT CENTRAL</strong>
                        <span class="badge badge-primary ml-2">{{ $coffre->code_guichet }}</span>
                        <span class="badge badge-success ml-1">{{ $coffre->statut_operationnel }}</span>
                    </h5>
                    <button class="btn btn-xs btn-outline-warning" id="btnRefreshBalances" title="Actualiser soldes">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body py-3">
                    <div class="row" id="coffreBalancesRow">
                        @forelse($coffre->soldes as $sc)
                        <div class="col-6 col-md-3 col-lg-2 mb-3">
                            <div class="coffre-balance-card text-center p-3" data-devise="{{ $sc->devise_code }}">
                                <div class="coffre-devise-code">
                                    <i class="fas fa-coins mr-1 text-warning"></i>
                                    {{ $sc->devise->symbole ?? $sc->devise_code }}
                                </div>
                                <div class="coffre-montant" id="coffreBalCard_{{ $sc->devise_code }}">
                                    {{ number_format($sc->solde_en_caisse, 2, ',', ' ') }}
                                </div>
                                <small class="text-muted">{{ $sc->devise_code }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Aucun solde configuré.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3" id="section-approvisionnement">
        <div class="col-lg-6 mb-3">
            <div class="card card-outline card-success shadow elevation-2 h-100">
                <div class="card-header py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-piggy-bank mr-2 text-success"></i>
                        <strong>Approvisionnement du coffre central</strong>
                    </h5>
                </div>
                <div class="card-body">
                    <form id="formApproCoffre">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Devise</label>
                                <select class="form-control form-control-sm" id="appro_devise_code" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach($devises as $d)
                                        <option value="{{ $d->code_iso }}">{{ $d->code_iso }} @if($d->symbole)({{ $d->symbole }})@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Montant</label>
                                <input type="number" step="0.01" min="1" class="form-control form-control-sm" id="appro_montant" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Source</label>
                                <select class="form-control form-control-sm" id="appro_source">
                                    <option value="">Non precisee</option>
                                    <option value="BANQUE">Banque</option>
                                    <option value="CAPITAL">Capital</option>
                                    <option value="PARTENAIRE">Partenaire</option>
                                    <option value="AUTRE">Autre</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Observations</label>
                                <input type="text" maxlength="255" class="form-control form-control-sm" id="appro_observations" placeholder="Reference, motif...">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success" id="btnSubmitApproCoffre">
                            <i class="fas fa-plus-circle mr-1"></i> Approvisionner le coffre
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3" id="section-intercaisse">
            <div class="card card-outline card-primary shadow elevation-2 h-100">
                <div class="card-header py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-random mr-2 text-primary"></i>
                        <strong>Alimentation Intercaisse (Coffre -> Guichet)</strong>
                    </h5>
                </div>
                <div class="card-body">
                    <form id="formIntercaisse">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Guichet destinataire</label>
                                <select class="form-control form-control-sm" id="inter_guichet_id" required>
                                    <option value="">Selectionner...</option>
                                    @foreach($guichetsAlimentables as $g)
                                        <option value="{{ $g->id }}" @if($g->statut_operationnel !== 'OUVERT') disabled @endif>
                                            {{ $g->code_guichet }} - {{ $g->intitule }} [{{ $g->type_guichet }}] ({{ $g->statut_operationnel }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Seuls les guichets <strong>OUVERTS</strong> sont alimentables.</small>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="small font-weight-bold">Devise</label>
                                <select class="form-control form-control-sm" id="inter_devise_code" required>
                                    <option value="">-</option>
                                    @foreach($devises as $d)
                                        <option value="{{ $d->code_iso }}">{{ $d->code_iso }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="small font-weight-bold">Montant</label>
                                <input type="number" step="0.01" min="1" class="form-control form-control-sm" id="inter_montant" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Observations</label>
                            <input type="text" maxlength="255" class="form-control form-control-sm" id="inter_observations" placeholder="Motif operationnel...">
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary" id="btnSubmitIntercaisse">
                            <i class="fas fa-paper-plane mr-1"></i> Alimenter le guichet
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-outline card-warning shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt mr-2 text-warning"></i>
                        <strong>Historique des alimentations intercaisses</strong>
                    </h5>
                    <button class="btn btn-xs btn-outline-warning" id="btnRefreshAlimentations" title="Actualiser">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="tableAlimentations">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Reference</th>
                                    <th>Source</th>
                                    <th>Type source</th>
                                    <th>Destination</th>
                                    <th>Type destination</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Initiateur</th>
                                    <th>Observation</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyAlimentations">
                                <tr>
                                    <td colspan="10" class="text-center py-3 text-muted">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Chargement...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
</div>
@endsection

@push('css')
<style>
    .coffre-balance-card {
        background: rgba(255, 193, 7, 0.10);
        border: 2px solid rgba(255, 193, 7, 0.40);
        border-radius: 10px;
        transition: border-color .2s;
    }
    .coffre-balance-card:hover { border-color: #ffc107; }
    .coffre-devise-code  { font-weight: 700; font-size: 1rem; color: #ffc107; }
    .coffre-montant      { font-size: 1.4rem; font-weight: 800; color: #fff; word-break: break-all; }
</style>
@endpush

@push('js')
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept'       : 'application/json'
        }
    });

    var urlBalances       = '{{ route("tresorerie.coffre.balances") }}';
    var urlApprovisionner = '{{ route("tresorerie.coffre.approvisionner") }}';
    var urlAlimenter      = '{{ route("tresorerie.coffre.alimenter") }}';
    var urlAlimentations  = '{{ route("tresorerie.coffre.alimentations") }}';
    var moduleActif       = '{{ $module ?? "approvisionnement" }}';

    function fmtMontant(n) {
        return n.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function rafraichirBalances() {
        $.get(urlBalances).done(function (data) {
            $.each(data, function (i, s) {
                var fmt = s.solde.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
                $('#coffreBalCard_' + s.devise_code).text(fmt);
            });
        });
    }

    function renderAlimentations(items) {
        var $tbody = $('#tbodyAlimentations');
        $tbody.empty();

        if (!items || !items.length) {
            $tbody.html('<tr><td colspan="10" class="text-center py-3 text-muted"><i class="fas fa-inbox mr-1"></i> Aucun mouvement inter-caisse.</td></tr>');
            return;
        }

        $.each(items.slice(0, 120), function(i, m) {
            var obs = m.observations ? $('<div>').text(m.observations).html() : '-';
            $tbody.append(
                '<tr>'
                + '<td>' + m.id + '</td>'
                + '<td><small class="text-monospace">' + (m.reference || '-') + '</small></td>'
                + '<td>' + (m.guichet_source || '-') + '</td>'
                + '<td><span class="badge badge-secondary">' + (m.guichet_source_type || '-') + '</span></td>'
                + '<td><strong>' + (m.guichet_dest || '-') + '</strong></td>'
                + '<td><span class="badge badge-info">' + (m.guichet_dest_type || '-') + '</span></td>'
                + '<td><strong>' + fmtMontant(parseFloat(m.montant || 0)) + ' ' + (m.devise_code || '') + '</strong></td>'
                + '<td><small>' + (m.date || '-') + '</small></td>'
                + '<td><small>' + (m.initiateur || '-') + '</small></td>'
                + '<td><small>' + obs + '</small></td>'
                + '</tr>'
            );
        });
    }

    function rafraichirAlimentations() {
        $.get(urlAlimentations)
            .done(function(data) { renderAlimentations(data); })
            .fail(function(xhr) { handleAjaxFail(xhr, 'Chargement historique inter-caisse'); });
    }

    $('#formApproCoffre').on('submit', function(e) {
        e.preventDefault();

        var payload = {
            devise_code  : $('#appro_devise_code').val(),
            montant      : $('#appro_montant').val(),
            source       : $('#appro_source').val(),
            observations : $('#appro_observations').val()
        };

        $('#btnSubmitApproCoffre').prop('disabled', true);
        $.ajax({
            url      : urlApprovisionner,
            method   : 'POST',
            data     : payload,
            dataType : 'json'
        }).done(function(r) {
            if (r.success) {
                showSystemMessage('success', r.message || 'Approvisionnement effectue.');
                $('#formApproCoffre')[0].reset();
                rafraichirBalances();
                rafraichirAlimentations();
            } else {
                showSystemMessage('error', r.message || 'Erreur lors de l\'approvisionnement.');
            }
        }).fail(function(xhr) {
            handleAjaxFail(xhr, 'Approvisionnement coffre');
        }).always(function() {
            $('#btnSubmitApproCoffre').prop('disabled', false);
        });
    });

    $('#formIntercaisse').on('submit', function(e) {
        e.preventDefault();

        var payload = {
            guichet_id    : $('#inter_guichet_id').val(),
            devise_code   : $('#inter_devise_code').val(),
            montant       : $('#inter_montant').val(),
            observations  : $('#inter_observations').val()
        };

        $('#btnSubmitIntercaisse').prop('disabled', true);
        $.ajax({
            url      : urlAlimenter,
            method   : 'POST',
            data     : payload,
            dataType : 'json'
        }).done(function(r) {
            if (r.success) {
                showSystemMessage('success', r.message || 'Alimentation inter-caisse effectuee.');
                $('#inter_montant').val('');
                $('#inter_observations').val('');
                rafraichirBalances();
                rafraichirAlimentations();
            } else {
                showSystemMessage('error', r.message || 'Erreur lors de l\'alimentation inter-caisse.');
            }
        }).fail(function(xhr) {
            handleAjaxFail(xhr, 'Alimentation inter-caisse');
        }).always(function() {
            $('#btnSubmitIntercaisse').prop('disabled', false);
        });
    });

    $('#btnRefreshBalances').on('click', rafraichirBalances);
    $('#btnRefreshAlimentations').on('click', rafraichirAlimentations);

    rafraichirAlimentations();

    if (moduleActif === 'intercaisse') {
        var cible = document.getElementById('section-intercaisse');
        if (cible) {
            setTimeout(function () {
                cible.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 150);
        }
    }

    setInterval(rafraichirAlimentations, 60000);
});
</script>
@endpush

@extends('layouts.app')

@section('page_title', 'Change de devises – Coffre central')
@section('breadcrumb_parent', 'Trésorerie')
@section('breadcrumb', 'Change de devises')

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

    <div class="row mb-3">
        <div class="col-lg-6 mb-3">
            <div class="card card-outline card-primary shadow elevation-2 h-100">
                <div class="card-header py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt mr-2 text-primary"></i>
                        <strong>Convertir une devise du coffre</strong>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Cette opération convertit un montant d'une devise vers une autre
                        <strong>au sein du même coffre central</strong> — aucun argent
                        n'entre ni ne sort réellement de l'institution. Le taux appliqué
                        est toujours le <strong>taux de change officiel actif</strong>
                        (configuré dans Trésorerie &gt; Taux de Change / Devises), jamais
                        saisi librement, pour garantir la fiabilité comptable.
                    </div>
                    <form id="formChangeDevise">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Devise source (à débiter)</label>
                                <select class="form-control form-control-sm" id="chg_devise_source" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach($devises as $d)
                                        <option value="{{ $d->code_iso }}">{{ $d->code_iso }} @if($d->symbole)({{ $d->symbole }})@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Devise destination (à créditer)</label>
                                <select class="form-control form-control-sm" id="chg_devise_dest" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach($devises as $d)
                                        <option value="{{ $d->code_iso }}">{{ $d->code_iso }} @if($d->symbole)({{ $d->symbole }})@endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Montant (devise source)</label>
                                <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" id="chg_montant" placeholder="0.00" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">Contre-valeur estimée</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="chg_contrevaleur" placeholder="—" readonly>
                            </div>
                        </div>

                        <div class="form-group mb-2" id="chg_taux_info" style="display:none;">
                            <span class="badge badge-light border">
                                <i class="fas fa-balance-scale mr-1 text-primary"></i>
                                Taux appliqué : <strong id="chg_taux_valeur">-</strong>
                            </span>
                        </div>
                        <div class="alert alert-warning py-2 px-3 small" id="chg_taux_absent" style="display:none;">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Aucun taux de change actif</strong> pour cette paire de devises.
                            Le change est bloqué : configurez d'abord le taux dans
                            <a href="{{ route('administration.devises-taux.index') }}">Trésorerie &gt; Taux de Change / Devises</a>.
                        </div>

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Observations</label>
                            <input type="text" maxlength="255" class="form-control form-control-sm" id="chg_observations" placeholder="Motif (ex: besoin de liquidités USD pour décaissements)...">
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary" id="btnSubmitChangeDevise">
                            <i class="fas fa-random mr-1"></i> Effectuer le change
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card card-outline card-secondary shadow elevation-2 h-100">
                <div class="card-header py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-book mr-2 text-secondary"></i>
                        <strong>Taux de change actifs</strong>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        Les taux sont gérés dans
                        <a href="{{ route('administration.devises-taux.index') }}">Trésorerie &gt; Taux de Change / Devises</a>.
                        Si aucun taux actif n'existe pour la paire choisie, le change sera refusé.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-outline card-warning shadow elevation-2">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-history mr-2 text-warning"></i>
                        <strong>Historique des changes de devises (coffre central)</strong>
                    </h5>
                    <button class="btn btn-xs btn-outline-warning" id="btnRefreshHistorique" title="Actualiser">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="tableHistoriqueChange">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Référence</th>
                                    <th>Débit</th>
                                    <th>Crédit</th>
                                    <th>Taux</th>
                                    <th>Agent</th>
                                    <th>Date</th>
                                    <th>Observation</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyHistoriqueChange">
                                <tr>
                                    <td colspan="8" class="text-center py-3 text-muted">
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

    var urlBalances    = '{{ route("tresorerie.coffre.balances") }}';
    var urlChange       = '{{ route("tresorerie.change-devise.store") }}';
    var urlHistorique   = '{{ route("tresorerie.change-devise.historique") }}';

    // Taux officiels actifs (server-side) — clé "SRC->DEST" => taux.
    // Valeur indicative pour l'estimation ; le taux FAIT FOI reste recalculé
    // et validé côté serveur au moment du POST.
    var tauxPaires = @json($tauxPaires);

    function fmtMontant(n) {
        return (n || 0).toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function getTauxPaire(src, dest) {
        if (!src || !dest || src === dest) return null;
        return tauxPaires[src + '->' + dest] || null;
    }

    function majEstimation() {
        var src = $('#chg_devise_source').val();
        var dest = $('#chg_devise_dest').val();
        var montant = parseFloat($('#chg_montant').val()) || 0;
        var taux = getTauxPaire(src, dest);
        var $btn = $('#btnSubmitChangeDevise');

        if (src && dest && src !== dest && taux === null) {
            // Paire sans taux actif : bloquer + avertir
            $('#chg_taux_info').hide();
            $('#chg_taux_absent').show();
            $('#chg_contrevaleur').val('');
            $btn.prop('disabled', true);
            return;
        }

        $('#chg_taux_absent').hide();
        $btn.prop('disabled', false);

        if (taux !== null) {
            $('#chg_taux_info').show();
            $('#chg_taux_valeur').text('1 ' + src + ' = ' + taux + ' ' + dest);
        } else {
            $('#chg_taux_info').hide();
        }

        if (taux !== null && montant > 0) {
            var contre = montant * taux;
            $('#chg_contrevaleur').val(fmtMontant(contre) + ' ' + dest);
        } else {
            $('#chg_contrevaleur').val('');
        }
    }

    function rafraichirBalances() {
        $.get(urlBalances).done(function (data) {
            $.each(data, function (i, s) {
                var fmt = s.solde.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
                $('#coffreBalCard_' + s.devise_code).text(fmt);
            });
        });
    }

    function renderHistorique(items) {
        var $tbody = $('#tbodyHistoriqueChange');
        $tbody.empty();

        if (!items || !items.length) {
            $tbody.html('<tr><td colspan="8" class="text-center py-3 text-muted"><i class="fas fa-inbox mr-1"></i> Aucun change effectué.</td></tr>');
            return;
        }

        $.each(items, function(i, m) {
            var obs = m.observations ? $('<div>').text(m.observations).html() : '-';
            $tbody.append(
                '<tr>'
                + '<td>' + m.id + '</td>'
                + '<td><small class="text-monospace">' + (m.reference || '-') + '</small></td>'
                + '<td><span class="badge badge-danger">-' + fmtMontant(m.montant) + ' ' + (m.devise_source || '') + '</span></td>'
                + '<td><span class="badge badge-success">+' + fmtMontant(m.montant_dest) + ' ' + (m.devise_dest || '') + '</span></td>'
                + '<td>' + (m.taux_change || '-') + '</td>'
                + '<td><small>' + (m.agent || '-') + '</small></td>'
                + '<td><small>' + (m.date || '-') + '</small></td>'
                + '<td><small>' + obs + '</small></td>'
                + '</tr>'
            );
        });
    }

    function rafraichirHistorique() {
        $.get(urlHistorique)
            .done(function(data) { renderHistorique(data); })
            .fail(function(xhr) { handleAjaxFail(xhr, 'Chargement historique change de devises'); });
    }

    $('#chg_montant, #chg_devise_source, #chg_devise_dest').on('input change', majEstimation);
    majEstimation();

    $('#formChangeDevise').on('submit', function(e) {
        e.preventDefault();

        var payload = {
            devise_source : $('#chg_devise_source').val(),
            devise_dest   : $('#chg_devise_dest').val(),
            montant       : $('#chg_montant').val(),
            observations  : $('#chg_observations').val()
        };

        if (payload.devise_source && payload.devise_source === payload.devise_dest) {
            showSystemMessage('error', 'La devise source et la devise destination doivent être différentes.');
            return;
        }

        $('#btnSubmitChangeDevise').prop('disabled', true);
        $.ajax({
            url      : urlChange,
            method   : 'POST',
            data     : payload,
            dataType : 'json'
        }).done(function(r) {
            if (r.success) {
                showSystemMessage('success', r.message || 'Change effectué.');
                $('#formChangeDevise')[0].reset();
                $('#chg_contrevaleur').val('');
                majEstimation();
                rafraichirBalances();
                rafraichirHistorique();
            } else {
                showSystemMessage('error', r.message || 'Erreur lors du change de devises.');
            }
        }).fail(function(xhr) {
            handleAjaxFail(xhr, 'Change de devises coffre');
        }).always(function() {
            $('#btnSubmitChangeDevise').prop('disabled', false);
        });
    });

    $('#btnRefreshBalances').on('click', rafraichirBalances);
    $('#btnRefreshHistorique').on('click', rafraichirHistorique);

    rafraichirHistorique();
    setInterval(rafraichirHistorique, 60000);
});
</script>
@endpush

{{-- ============================================================
     Opérations de Caisse — Guichetier
     Saisie : Dépôt, Retrait, Change, Paiement, Remboursement
     Permissions : EBEN-PER10 (voir) | EBEN-PER11 (saisir)
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'Opérations de Caisse')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Opérations')

@section('content')
<div class="container-fluid">

    @if(!$guichet)
    {{-- ── Aucun guichet affecté ──────────────────────────────── --}}
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-warning card-outline shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-cash-register fa-4x text-muted mb-3"></i>
                    <h4>Aucun guichet assigné</h4>
                    <p class="text-muted">Votre compte n'est associé à aucun guichet actif.<br>
                    Contactez un administrateur pour l'affectation.</p>
                </div>
            </div>
        </div>
    </div>

    @else
    @php
        $statut  = $guichet->statut_operationnel;
        $typeGch = $guichet->type_guichet;   // FIXE | MOBILE | CENTRAL
        $guichetOuvert = ($statut === 'OUVERT');
    @endphp

    {{-- ── Bandeau statut guichet ────────────────────────────── --}}
    @if(!$guichetOuvert)
    <div class="alert alert-{{ $statut === 'SUSPENDU' ? 'warning' : ($statut === 'EN_VERIFICATION' ? 'info' : 'danger') }} shadow mb-3 py-2">
        <i class="fas fa-{{ $statut === 'SUSPENDU' ? 'pause-circle' : ($statut === 'EN_VERIFICATION' ? 'hourglass-half' : 'lock') }} mr-2"></i>
        Guichet <strong>{{ $statut }}</strong> — La saisie d'opérations est impossible.
        Rendez-vous sur <a href="{{ route('caisses.ouverture') }}" class="alert-link">Ouverture / Fermeture</a> pour changer l'état du guichet.
    </div>
    @endif

    {{-- ── Soldes actuels ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
        <small class="text-muted text-uppercase" style="letter-spacing:.08em;">
            <i class="fas fa-wallet mr-1"></i> Soldes :
        </small>
        @foreach($guichet->soldes->sortBy('devise_code') as $s)
        <span class="badge badge-pill px-3 py-2 solde-pill" id="soldePill_{{ $s->devise_code }}"
              style="font-size:.92rem; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15);">
            <strong>{{ $s->devise_code }}</strong>
            <span class="solde-val">{{ number_format($s->solde_en_caisse, 2, ',', ' ') }}</span>
        </span>
        @endforeach
        <span class="badge badge-pill px-2 py-2 ml-1"
              style="background:rgba(23,162,184,.2); border:1px solid rgba(23,162,184,.4); font-size:.85rem;">
            <i class="fas fa-{{ $typeGch === 'MOBILE' ? 'mobile-alt' : 'desktop' }} mr-1 text-info"></i>
            {{ $typeGch }}
        </span>
    </div>

    {{-- ── Corps principal ────────────────────────────────────── --}}
    <div class="row">

        {{-- ── Formulaire de saisie (gauche) ──────────────────── --}}
        <div class="col-lg-4 col-md-5 mb-3">
            <div class="card card-outline card-primary shadow">
                <div class="card-header py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-plus-circle mr-1 text-primary"></i>
                        Nouvelle opération
                    </h6>
                </div>
                <div class="card-body">

                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em;">
                            Type d'opération <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="selTypeOp" {{ !$guichetOuvert ? 'disabled' : '' }}>
                            <option value="">— Sélectionnez —</option>
                            <option value="DEPOT">💰 Dépôt (compte client)</option>
                            <option value="RETRAIT">💸 Retrait (compte client)</option>
                            <option value="CHANGE">🔄 Change de devises</option>
                            <option value="PAIEMENT">🧾 Paiement facture/service</option>
                            <option value="REMBOURSEMENT">↩ Remboursement</option>
                        </select>
                    </div>

                    {{-- ── Bloc compte (visible seulement si DEPOT / RETRAIT) ── --}}
                    <div id="blocCompte" class="d-none mb-2">
                        <div class="alert alert-primary py-1 mb-2 small">
                            <i class="fas fa-university mr-1"></i>
                            <strong>Compte client requis</strong> — code compte ou nom du titulaire.
                        </div>
                        <div class="form-group mb-1">
                            <label class="font-weight-bold" style="font-size:.85rem;">
                                Compte <span class="text-danger">*</span>
                            </label>
                            <select id="selCompte" class="form-control form-control-sm" style="width:100%"
                                    {{ !$guichetOuvert ? 'disabled' : '' }}>
                                <option value="">— Sélectionner un compte —</option>
                                @foreach($comptes as $cpt)
                                <option value="{{ $cpt->code_compte }}"
                                        data-devise="{{ $cpt->devise }}"
                                        data-solde="{{ number_format($cpt->solde_reel ?? 0, 2, '.', '') }}"
                                        data-client="{{ optional($cpt->client)->nom }} {{ optional($cpt->client)->postnom }}">
                                    [{{ $cpt->devise }}] {{ optional($cpt->client)->nom }} {{ optional($cpt->client)->postnom }} — {{ $cpt->code_compte }}
                                </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="selectedCompteCode">
                        </div>
                    </div>

                    {{-- ── Devise + Montant source ──────────── --}}
                    <div class="form-row mb-2">
                        <div class="col-5">
                            <label class="font-weight-bold" style="font-size:.85rem;" id="labelDevise">
                                Devise <span class="text-danger">*</span>
                            </label>
                            <select class="form-control form-control-sm" id="selDevise"
                                    {{ !$guichetOuvert ? 'disabled' : '' }}>
                                <option value="">—</option>
                                @foreach($guichet->soldes->sortBy('devise_code') as $s)
                                <option value="{{ $s->devise_code }}">{{ $s->devise_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-7">
                            <label class="font-weight-bold" style="font-size:.85rem;" id="labelMontant">
                                Montant <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-sm" id="inpMontant"
                                   placeholder="0.00" min="0.01" step="any"
                                   {{ !$guichetOuvert ? 'disabled' : '' }}>
                        </div>
                    </div>

                    {{-- ── Bloc change (visible seulement si CHANGE) ── --}}
                    <div id="blocChange" class="d-none">
                        <div class="alert alert-info py-1 mb-2 small">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Change :</strong> le client donne <em>Devise source</em>,
                            le guichet donne <em>Devise destination</em>.
                        </div>
                        <div class="form-row mb-2">
                            <div class="col-5">
                                <label class="font-weight-bold" style="font-size:.85rem;">Devise dest. <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" id="selDeviseDest">
                                    <option value="">—</option>
                                    @foreach($guichet->soldes->sortBy('devise_code') as $s)
                                    <option value="{{ $s->devise_code }}">{{ $s->devise_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-7">
                                <label class="font-weight-bold" style="font-size:.85rem;">Montant dest. <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm" id="inpMontantDest"
                                       placeholder="0.00" min="0.01" step="any">
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:.85rem;">Taux appliqué</label>
                            <input type="number" class="form-control form-control-sm" id="inpTaux"
                                   placeholder="Ex : 2850.00" min="0" step="any">
                        </div>
                    </div>

                    {{-- ── Observations ─────────────────────── --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold" style="font-size:.85rem;">Observations</label>
                        <input type="text" class="form-control form-control-sm" id="inpObservations"
                               placeholder="Remarque optionnelle…" maxlength="500"
                               {{ !$guichetOuvert ? 'disabled' : '' }}>
                    </div>

                    <button class="btn btn-primary btn-block" id="btnEnregistrerOp"
                            {{ !$guichetOuvert ? 'disabled' : '' }}>
                        <i class="fas fa-check-circle mr-1"></i> Enregistrer
                    </button>

                </div>
            </div>
        </div>

        {{-- ── Tableau opérations du jour (droite) ───────────── --}}
        <div class="col-lg-8 col-md-7 mb-3">
            <div class="card card-outline card-secondary shadow">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-list-ul mr-1 text-secondary"></i>
                        Opérations du jour
                        <span class="badge badge-secondary ml-1" id="opCount">{{ $operations->count() }}</span>
                    </h6>
                    <button class="btn btn-xs btn-outline-secondary" id="btnRefreshOps" title="Actualiser">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:30px;"></th>
                                    <th>Réf.</th>
                                    <th>Montant</th>
                                    <th>Heure</th>
                                    <th>Statut</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="tbodyOps">
                                @forelse($operations as $op)
                                <tr class="{{ $op->statut === 'ANNULE' ? 'text-muted' : '' }}" id="opRow_{{ $op->id }}">
                                    <td class="text-center">
                                        <i class="fas {{ \App\Models\Transaction::typeIcon($op->type) }} fa-sm"></i>
                                    </td>
                                    <td>
                                        <small class="text-monospace">{{ $op->reference }}</small><br>
                                        <span class="badge badge-pill badge-sm {{ \App\Models\Transaction::typeBadgeClass($op->type) }}">
                                            {{ \App\Models\Transaction::typeLabel($op->type) }}
                                        </span>
                                        @if($op->compte_code)
                                        <br><small class="text-muted"><i class="fas fa-university fa-xs"></i> {{ $op->compte_code }}</small>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold">
                                        {{ number_format($op->montant, 2, ',', ' ') }} {{ $op->devise_code }}
                                        @if($op->type === 'CHANGE' && $op->montant_dest)
                                        <br><small class="text-info">→ {{ number_format($op->montant_dest, 2, ',', ' ') }} {{ $op->devise_dest }}</small>
                                        @endif
                                    </td>
                                    <td><small>{{ $op->date_operation?->format('H:i') }}</small></td>
                                    <td>
                                        @if($op->statut === 'ANNULE')
                                        <span class="badge badge-secondary">Annulée</span>
                                        @else
                                        <span class="badge badge-success">Confirmé</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($op->statut === 'CONFIRME')
                                        <button class="btn btn-xs btn-outline-danger btn-annuler"
                                                data-id="{{ $op->id }}" title="Annuler">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr id="trVide">
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Aucune opération aujourd'hui.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}
    @endif

</div>
@endsection


@push('css')
<style>
    .solde-pill { transition: background .2s; }
    .table-sm td { font-size: .88rem; vertical-align: middle; }
    .table-sm th { font-size: .82rem; }
    .badge-sm { font-size: .72rem; padding: .15em .45em; }
    .btn-xs { padding: .15rem .45rem; font-size: .78rem; }
    .gap-2 { gap: .5rem; }
</style>
@endpush


@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    @if($guichet)
    var urlStore         = '{{ route("caisses.operations.store") }}';
    var urlJournal        = '{{ route("caisses.journal.data") }}';
    var urlAnnuler        = '{{ route("caisses.operations.annuler", ["id" => "__ID__"]) }}';
    var urlSearchCompte   = '{{ route("caisses.operations.comptes.search") }}';

    // ── Select2 — Recherche compte client (chargé côté serveur) ──
    $('#selCompte').select2({
        theme         : 'bootstrap4',
        width         : '100%',
        dropdownParent: $('body'),
        placeholder   : '— Sélectionner un compte —',
        allowClear    : true,
        language      : { noResults: function () { return 'Aucun compte trouvé.'; } }
    });

    $('#selCompte').on('select2:select', function () {
        var $opt = $(this).find('option:selected');
        $('#selectedCompteCode').val($opt.val());
        $('#selDevise').val($opt.data('devise')).prop('disabled', true);
    });

    $('#selCompte').on('select2:unselect select2:clear', function () {
        $('#selectedCompteCode').val('');
        $('#selDevise').prop('disabled', false);
    });

    function clearCompteSelection() {
        $('#selectedCompteCode').val('');
        $('#selCompte').val(null).trigger('change');
        $('#selDevise').prop('disabled', false);
    }

    // ── Type opération → affichage dynamique ─────────────────────
    $('#selTypeOp').on('change', function () {
        var type = $(this).val();
        var avecCompte = (type === 'DEPOT' || type === 'RETRAIT');

        // Bloc compte
        if (avecCompte) {
            $('#blocCompte').removeClass('d-none');
        } else {
            $('#blocCompte').addClass('d-none');
            clearCompteSelection();
        }

        // Bloc change
        if (type === 'CHANGE') {
            $('#blocChange').removeClass('d-none');
            $('#labelDevise').html('Devise source <span class="text-danger">*</span>');
            $('#labelMontant').html('Montant source <span class="text-danger">*</span>');
        } else {
            $('#blocChange').addClass('d-none');
            $('#labelDevise').html('Devise <span class="text-danger">*</span>');
            $('#labelMontant').html('Montant <span class="text-danger">*</span>');
        }
        $('#inpMontant').attr('placeholder', type === 'CHANGE' ? 'Montant donné par le client' : '0.00');
    });

    // ── Enregistrer une opération ────────────────────────────────
    $('#btnEnregistrerOp').on('click', function () {
        var type    = $('#selTypeOp').val();
        var devise  = $('#selDevise').val();
        var montant = $('#inpMontant').val();

        if (!type)    { showSystemMessage('error', 'Sélectionnez un type d\'opération.'); return; }
        if ((type === 'DEPOT' || type === 'RETRAIT') && !$('#selectedCompteCode').val()) {
            showSystemMessage('error', 'Recherchez et sélectionnez le compte client.'); return;
        }
        if (!devise)  { showSystemMessage('error', 'Sélectionnez une devise.'); return; }
        if (!montant || parseFloat(montant) <= 0) { showSystemMessage('error', 'Entrez un montant valide.'); return; }

        if (type === 'CHANGE') {
            var deviseDest = $('#selDeviseDest').val();
            var montDest   = $('#inpMontantDest').val();
            if (!deviseDest) { showSystemMessage('error', 'Sélectionnez la devise destination.'); return; }
            if (!montDest || parseFloat(montDest) <= 0) { showSystemMessage('error', 'Entrez le montant destination.'); return; }
            if (deviseDest === devise) { showSystemMessage('error', 'Les deux devises doivent être différentes.'); return; }
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enregistrement…');

        var payload = {
            type_operation: type,
            devise_code:    devise,
            montant:        montant,
            observations:   $.trim($('#inpObservations').val()),
        };

        // Ajouter le compte client pour DEPOT et RETRAIT
        if (type === 'DEPOT' || type === 'RETRAIT') {
            payload.compte_code = $('#selectedCompteCode').val();
        }

        if (type === 'CHANGE') {
            payload.devise_dest  = $('#selDeviseDest').val();
            payload.montant_dest = $('#inpMontantDest').val();
            payload.taux_change  = $('#inpTaux').val() || null;
        }

        $.ajax({
            url: urlStore, method: 'POST', data: payload, dataType: 'json'
        })
        .done(function (r) {
            showSystemMessage('success', r.message || 'Opération enregistrée.');
            setTimeout(function () { location.reload(); }, 1000);
        })
        .fail(function (xhr) {
            handleAjaxFail(xhr, 'Enregistrement opération caisse');
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Enregistrer');
        });
    });

    function resetForm() {
        $('#selTypeOp').val('');
        $('#selDevise, #selDeviseDest').val('').prop('disabled', false);
        $('#inpMontant, #inpMontantDest, #inpTaux, #inpObservations').val('');
        $('#blocChange').addClass('d-none');
        $('#blocCompte').addClass('d-none');
        clearCompteSelection();
        $('#labelDevise').html('Devise <span class="text-danger">*</span>');
        $('#labelMontant').html('Montant <span class="text-danger">*</span>');
    }

    // ── Mise à jour des soldes en temps réel ─────────────────────
    function majSoldes(soldes) {
        if (!soldes) return;
        $.each(soldes, function(i, s) {
            $('#soldePill_' + s.devise_code + ' .solde-val').text(s.solde_en_caisse);
        });
    }

    // ── Charger / rafraîchir le tableau des opérations ───────────
    function chargerOpsJour() {
        $.getJSON(urlJournal, { date: '{{ today()->toDateString() }}' })
        .done(function (data) {
            var ops = data.operations || [];
            $('#opCount').text(ops.length);
            var tbody = $('#tbodyOps');
            if (!ops.length) {
                tbody.html('<tr id="trVide"><td colspan="7" class="text-center py-4 text-muted">'
                    + '<i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune opération aujourd\'hui.</td></tr>');
                return;
            }
            tbody.empty();
            $.each(ops, function(i, op) {
                var montantExtra = op.montant_dest_fmt
                    ? '<br><small class="text-info">→ ' + op.montant_dest_fmt + '</small>' : '';
                var annulerBtn = op.statut === 'CONFIRME'
                    ? '<button class="btn btn-xs btn-outline-danger btn-annuler" data-id="' + op.id + '" title="Annuler"><i class="fas fa-times"></i></button>' : '';
                var statutBadge = op.statut === 'ANNULE'
                    ? '<span class="badge badge-secondary">Annulée</span>'
                    : '<span class="badge badge-success">Confirmé</span>';
                var rowClass = op.statut === 'ANNULE' ? ' class="text-muted"' : '';
                tbody.append(
                    '<tr' + rowClass + ' id="opRow_' + op.id + '">'
                    + '<td class="text-center"><i class="fas ' + op.icon + ' fa-sm"></i></td>'
                    + '<td><small class="text-monospace">' + op.reference + '</small><br>'
                    + '<span class="badge badge-pill badge-sm ' + op.badge_class + '">' + op.type_label + '</span>'
                    + (op.compte_code ? '<br><small class="text-muted"><i class="fas fa-university fa-xs"></i> ' + op.compte_code + '</small>' : '')
                    + '</td>'
                    + '<td class="font-weight-bold">' + op.montant_fmt + montantExtra + '</td>'
                    + '<td><small>' + (op.date ? op.date.substr(11,5) : '') + '</small></td>'
                    + '<td>' + statutBadge + '</td>'
                    + '<td>' + annulerBtn + '</td>'
                    + '</tr>'
                );
            });
        });
    }

    $('#btnRefreshOps').on('click', chargerOpsJour);
    setInterval(chargerOpsJour, 45000);

    // ── Annuler une opération ─────────────────────────────────────
    $(document).on('click', '.btn-annuler', function () {
        var id = $(this).data('id');
        showUniversalConfirm('Annuler cette opération ? Le solde du guichet sera recalculé.', function () {
            $.post(urlAnnuler.replace('__ID__', id))
            .done(function (r) {
                showSystemMessage('success', r.message || 'Opération annulée.');
                setTimeout(function () { location.reload(); }, 1000);
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Annulation opération caisse');
            });
        }, {
            title: 'Annuler l\'opération',
            btnLabel: 'Oui, annuler',
            btnClass: 'btn-danger',
            icon: 'fas fa-undo',
            headerClass: 'bg-danger text-white',
        });
    });

    @endif
});
</script>
@endpush

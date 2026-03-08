{{-- ============================================================
     Gestion Départ / Retour — Guichet MOBILE
     Demande de dotation (matin) + Déclaration reversement (soir)
     ============================================================ --}}
@extends('layouts.app')

@section('page_title', 'Gestion Mobile')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Départ / Retour Mobile')

@section('content')
<div class="container-fluid">

    @if(!$guichet)
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-warning card-outline shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-mobile-alt fa-4x text-muted mb-3"></i>
                    <h4>Aucun guichet mobile affecté</h4>
                    <p class="text-muted">Contactez un administrateur.</p>
                </div>
            </div>
        </div>
    </div>
    @elseif($guichet->type_guichet !== 'MOBILE')
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-danger card-outline shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-ban fa-4x text-danger mb-3"></i>
                    <h4>Page réservée aux guichets mobiles</h4>
                    <p class="text-muted">Votre guichet est de type <strong>{{ $guichet->type_guichet }}</strong>.</p>
                </div>
            </div>
        </div>
    </div>
    @else
    @php $guichetOuvert = ($guichet->statut_operationnel === 'OUVERT'); @endphp

    {{-- ── En-tête identité guichet ───────────────────────────── --}}
    <div class="callout callout-info mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">
                    <i class="fas fa-mobile-alt mr-2 text-info"></i>
                    {{ $guichet->code_guichet }} — {{ $guichet->intitule }}
                </h5>
                <p class="mb-0 small text-muted">
                    Session du {{ now()->isoFormat('dddd D MMMM YYYY') }}
                </p>
            </div>
            <span class="badge badge-{{ $guichet->statut_operationnel === 'OUVERT' ? 'success' : 'secondary' }} px-3 py-2"
                  style="font-size:1rem;">
                {{ $guichet->statut_operationnel }}
            </span>
        </div>
    </div>

    {{-- ── Soldes actuels ─────────────────────────────────────── --}}
    @if($guichet->soldes->isNotEmpty())
    <div class="d-flex flex-wrap mb-3" style="gap:.5rem;">
        <small class="text-muted text-uppercase align-self-center" style="letter-spacing:.08em;">Soldes :</small>
        @foreach($guichet->soldes->sortBy('devise_code') as $s)
        <span class="badge badge-pill px-3 py-2"
              style="font-size:.9rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.15);">
            <strong>{{ $s->devise_code }}</strong>
            {{ number_format($s->solde_en_caisse, 2, ',', ' ') }}
        </span>
        @endforeach
    </div>
    @endif

    {{-- ── Formulaires Départ / Retour ────────────────────────── --}}
    <div class="row">

        {{-- ═══ DÉPART — Demande de dotation ═══ --}}
        <div class="col-lg-6 mb-3">
            <div class="card card-outline card-info shadow h-100">
                <div class="card-header py-2 bg-transparent">
                    <h6 class="mb-0 text-info">
                        <i class="fas fa-route mr-1"></i>
                        Demande de Dotation (Départ)
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Indiquez les fonds dont vous avez besoin pour votre mission.
                        Le trésorier sera notifié pour l'approvisionnement depuis le coffre.
                    </p>

                    <div id="departLignes">
                        {{-- Ligne dynamique --}}
                        <div class="depart-ligne form-row mb-2">
                            <div class="col-4">
                                <select class="form-control form-control-sm sel-devise-depart">
                                    <option value="">Devise</option>
                                    @foreach($guichet->soldes->sortBy('devise_code') as $s)
                                    <option value="{{ $s->devise_code }}">{{ $s->devise_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm inp-montant-depart"
                                       placeholder="Montant demandé" min="1" step="any">
                            </div>
                            <div class="col-2 text-center">
                                <button class="btn btn-xs btn-outline-danger btn-retirer-ligne" title="Retirer">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-xs btn-outline-info mb-3" id="btnAjouterLigneDepart">
                        <i class="fas fa-plus mr-1"></i> Ajouter une devise
                    </button>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Observations</label>
                        <input type="text" class="form-control form-control-sm" id="departObs"
                               placeholder="Zone de mission, type d'opérations prévues…" maxlength="500">
                    </div>

                    <button class="btn btn-info btn-block" id="btnEnvoyerDepart"
                            {{ !$guichetOuvert ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane mr-1"></i> Envoyer la demande au trésorier
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══ RETOUR — Déclaration de reversement ═══ --}}
        <div class="col-lg-6 mb-3">
            <div class="card card-outline card-success shadow h-100">
                <div class="card-header py-2 bg-transparent">
                    <h6 class="mb-0 text-success">
                        <i class="fas fa-home mr-1"></i>
                        Déclaration de Reversement (Retour)
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Déclarez les fonds que vous ramenez en fin de mission.
                        Le trésorier confirmera la réception physique des espèces.
                    </p>

                    <div id="retourLignes">
                        <div class="retour-ligne form-row mb-2">
                            <div class="col-4">
                                <select class="form-control form-control-sm sel-devise-retour">
                                    <option value="">Devise</option>
                                    @foreach($guichet->soldes->sortBy('devise_code') as $s)
                                    <option value="{{ $s->devise_code }}">{{ $s->devise_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm inp-montant-retour"
                                       placeholder="Montant remis" min="0" step="any">
                            </div>
                            <div class="col-2 text-center">
                                <button class="btn btn-xs btn-outline-danger btn-retirer-ligne-retour" title="Retirer">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-xs btn-outline-success mb-3" id="btnAjouterLigneRetour">
                        <i class="fas fa-plus mr-1"></i> Ajouter une devise
                    </button>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Observations</label>
                        <input type="text" class="form-control form-control-sm" id="retourObs"
                               placeholder="Bilan de la mission, anomalies…" maxlength="500">
                    </div>

                    <button class="btn btn-success btn-block" id="btnEnvoyerRetour"
                            {{ !$guichetOuvert ? 'disabled' : '' }}>
                        <i class="fas fa-check-circle mr-1"></i> Déclarer le reversement
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /row formulaires --}}

    {{-- ── Historique mouvements mobile ──────────────────────── --}}
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card card-outline card-secondary shadow">
                <div class="card-header d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-history mr-1 text-secondary"></i>
                        Historique Dotations &amp; Reversements
                    </h6>
                    <button class="btn btn-xs btn-outline-secondary" id="btnRefreshHistorique">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Type</th>
                                    <th>Devise</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Observations</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyHistorique">
                                @forelse($historique as $h)
                                <tr>
                                    <td>
                                        <span class="badge {{ $h['badge_class'] }}">{{ $h['type_label'] }}</span>
                                    </td>
                                    <td>{{ $h['devise'] }}</td>
                                    <td class="font-weight-bold">{{ $h['montant_fmt'] }}</td>
                                    <td>
                                        <span class="badge {{ $h['statut'] === 'EN_ATTENTE' ? 'badge-warning text-dark' : ($h['statut'] === 'CONFIRME' ? 'badge-success' : 'badge-secondary') }}">
                                            {{ $h['statut'] === 'EN_ATTENTE' ? 'En attente' : ($h['statut'] === 'CONFIRME' ? 'Confirmé' : $h['statut']) }}
                                        </span>
                                    </td>
                                    <td><small>{{ $h['date'] }}</small></td>
                                    <td><small class="text-muted">{{ $h['obs'] ?? '—' }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox mr-1"></i> Aucun mouvement mobile enregistré.
                                    </td>
                                </tr>
                                @endforelse
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
    .table-sm td, .table-sm th { font-size: .88rem; vertical-align: middle; }
    .badge-sm { font-size:.72rem; padding:.15em .4em; }
    .btn-xs { padding:.15rem .45rem; font-size:.78rem; }
</style>
@endpush


@push('js')
<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' } });

    @if($guichet && $guichet->type_guichet === 'MOBILE')
    var urlDepart  = '{{ route("caisses.mobile.depart") }}';
    var urlRetour  = '{{ route("caisses.mobile.retour") }}';

    // ── Template ligne devise ─────────────────────────────────────
    var deviseOptions = '<option value="">Devise</option>'
        + '@foreach($guichet->soldes->sortBy("devise_code") as $s)<option value="{{ $s->devise_code }}">{{ $s->devise_code }}</option>@endforeach';

    // ── Ajouter une ligne de devise (DÉPART) ─────────────────────
    $('#btnAjouterLigneDepart').on('click', function () {
        var ligne = '<div class="depart-ligne form-row mb-2">'
            + '<div class="col-4"><select class="form-control form-control-sm sel-devise-depart">' + deviseOptions + '</select></div>'
            + '<div class="col-6"><input type="number" class="form-control form-control-sm inp-montant-depart" placeholder="Montant demandé" min="1" step="any"></div>'
            + '<div class="col-2 text-center"><button class="btn btn-xs btn-outline-danger btn-retirer-ligne"><i class="fas fa-times"></i></button></div>'
            + '</div>';
        $('#departLignes').append(ligne);
    });

    // ── Ajouter une ligne de devise (RETOUR) ─────────────────────
    $('#btnAjouterLigneRetour').on('click', function () {
        var ligne = '<div class="retour-ligne form-row mb-2">'
            + '<div class="col-4"><select class="form-control form-control-sm sel-devise-retour">' + deviseOptions + '</select></div>'
            + '<div class="col-6"><input type="number" class="form-control form-control-sm inp-montant-retour" placeholder="Montant remis" min="0" step="any"></div>'
            + '<div class="col-2 text-center"><button class="btn btn-xs btn-outline-danger btn-retirer-ligne-retour"><i class="fas fa-times"></i></button></div>'
            + '</div>';
        $('#retourLignes').append(ligne);
    });

    // ── Retirer une ligne ─────────────────────────────────────────
    $(document).on('click', '.btn-retirer-ligne', function () {
        if ($('#departLignes .depart-ligne').length > 1) $(this).closest('.depart-ligne').remove();
    });
    $(document).on('click', '.btn-retirer-ligne-retour', function () {
        if ($('#retourLignes .retour-ligne').length > 1) $(this).closest('.retour-ligne').remove();
    });

    // ── Envoyer demande de dotation ───────────────────────────────
    $('#btnEnvoyerDepart').on('click', function () {
        var dotations = [];
        var hasErr = false;
        $('#departLignes .depart-ligne').each(function () {
            var devise  = $(this).find('.sel-devise-depart').val();
            var montant = $(this).find('.inp-montant-depart').val();
            if (devise && montant && parseFloat(montant) > 0) {
                dotations.push({ devise_code: devise, montant: montant });
            }
        });

        if (!dotations.length) {
            showSystemMessage('error', 'Ajoutez au moins une devise avec un montant.');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');

        $.ajax({
            url: urlDepart, method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ dotations: dotations, observations: $.trim($('#departObs').val()) }),
            dataType: 'json'
        })
        .done(function (r) {
            if (r.success) {
                showSystemMessage('success', r.message || 'Demande envoyée.');
                // Réinitialiser les lignes
                $('#departLignes').html($('#departLignes .depart-ligne').first().clone());
                $('#departLignes .sel-devise-depart').val('');
                $('#departLignes .inp-montant-depart').val('');
                $('#departObs').val('');
                location.reload();
            } else {
                showSystemMessage('error', r.message || 'Erreur.');
            }
        })
        .fail(function (xhr) {
            handleAjaxFail(xhr, 'Envoi demande dotation mobile');
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Envoyer la demande au trésorier');
        });
    });

    // ── Déclarer un reversement ───────────────────────────────────
    $('#btnEnvoyerRetour').on('click', function () {
        var reversements = [];
        $('#retourLignes .retour-ligne').each(function () {
            var devise  = $(this).find('.sel-devise-retour').val();
            var montant = $(this).find('.inp-montant-retour').val();
            if (devise && montant !== '' && parseFloat(montant) >= 0) {
                reversements.push({ devise_code: devise, montant: montant });
            }
        });

        if (!reversements.length) {
            showSystemMessage('error', 'Ajoutez au moins une devise.');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Déclaration…');

        $.ajax({
            url: urlRetour, method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ reversements: reversements, observations: $.trim($('#retourObs').val()) }),
            dataType: 'json'
        })
        .done(function (r) {
            if (r.success) {
                showSystemMessage('success', r.message || 'Reversement déclaré.');
                $('#retourLignes .sel-devise-retour').val('');
                $('#retourLignes .inp-montant-retour').val('');
                $('#retourObs').val('');
                setTimeout(function () { location.reload(); }, 1500);
            } else {
                showSystemMessage('error', r.message || 'Erreur.');
            }
        })
        .fail(function (xhr) {
            handleAjaxFail(xhr, 'Déclaration reversement mobile');
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Déclarer le reversement');
        });
    });

    @endif
});
</script>
@endpush

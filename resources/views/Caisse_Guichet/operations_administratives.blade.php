@extends('layouts.app')

@section('page_title', 'Opérations Administratives')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Opérations Administratives')

@section('content')
<section class="content">
<div class="container-fluid">

    @if(!$guichet)
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
        $typeGch = $guichet->type_guichet;
        $guichetOuvert = $guichet->statut_operationnel === 'OUVERT';
    @endphp

    @if(!$guichetOuvert)
    <div class="alert alert-danger shadow mb-3 py-2">
        <i class="fas fa-lock mr-2"></i>
        Guichet <strong>{{ $guichet->statut_operationnel }}</strong> — La saisie d'opérations est impossible.
        Rendez-vous sur <a href="{{ route('caisses.ouverture') }}" class="alert-link">Ouverture / Fermeture</a> pour changer l'état du guichet.
    </div>
    @endif

    {{-- ── Soldes actuels (identique au module Opérations) ──────── --}}
    <div class="d-flex align-items-center flex-wrap gap-2 mb-3 operation-soldes-bar">
        <small class="text-muted text-uppercase" style="letter-spacing:.08em;">
            <i class="fas fa-wallet mr-1"></i> Soldes :
        </small>
        @foreach($guichet->soldes->sortBy('devise_code') as $s)
        <span class="badge badge-pill px-3 py-2 solde-pill"
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

    <div class="row">
        {{-- ── Formulaire unifié Dépense / Recette ─────────────────────────── --}}
        <div class="col-lg-4 col-md-5 mb-3">
            <div class="card card-outline card-primary shadow" id="cardFormOpAdmin">
                <div class="card-header py-2">
                    <h6 class="mb-0" id="cardFormOpAdminTitle">
                        <i class="fas fa-plus-circle mr-1 text-primary"></i>
                        Nouvelle opération administrative
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em;">
                            Type d'opération <span class="text-danger">*</span>
                        </label>
                        <select id="selSensOp" class="form-control" {{ !$guichetOuvert ? 'disabled' : '' }}>
                            <option value="SORTIE">↓ Dépense (Sortie)</option>
                            <option value="ENTREE">↑ Recette (Entrée)</option>
                        </select>
                    </div>

                    <form id="formOpAdmin" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-2">
                            <label class="font-weight-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em;">
                                Catégorie <span class="text-danger">*</span>
                            </label>
                            <select name="categorie_id" id="selCategorieOp" class="form-control" style="width:100%;" required {{ !$guichetOuvert ? 'disabled' : '' }}>
                                <option value="">— Sélectionner une catégorie —</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8 mb-2">
                                <label class="font-weight-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em;">
                                    Montant <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="montant" step="0.01" min="0.01" class="form-control" required {{ !$guichetOuvert ? 'disabled' : '' }}>
                            </div>
                            <div class="form-group col-md-4 mb-2">
                                <label class="font-weight-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em;">
                                    Devise <span class="text-danger">*</span>
                                </label>
                                <select name="devise_code" class="form-control" required {{ !$guichetOuvert ? 'disabled' : '' }}>
                                    <option value="CDF">CDF</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em;">
                                Motif <span class="text-danger">*</span>
                            </label>
                            <textarea name="motif" rows="2" class="form-control" required maxlength="500" {{ !$guichetOuvert ? 'disabled' : '' }}></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em;">
                                Pièce justificative <small class="text-muted font-weight-normal" style="letter-spacing:normal;">(facture/reçu, optionnel)</small>
                            </label>
                            <input type="file" name="piece_justificative" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" {{ !$guichetOuvert ? 'disabled' : '' }}>
                        </div>
                        <button type="submit" id="btnSubmitOpAdmin" class="btn btn-danger btn-block" {{ !$guichetOuvert ? 'disabled' : '' }}>
                            <i class="fas fa-save mr-1"></i><span id="btnSubmitOpAdminLabel">Enregistrer la dépense</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Listes (onglets) ─────────────────────────── --}}
        <div class="col-lg-8 col-md-7 mb-3">
            <div class="card shadow-sm">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabsOpAdmin">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabDepenses">
                                <i class="fas fa-arrow-down text-danger mr-1"></i>Sorties (Dépenses) <span class="badge badge-light">{{ $depenses->total() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabRecettes">
                                <i class="fas fa-arrow-up text-success mr-1"></i>Entrées (Recettes) <span class="badge badge-light">{{ $recettes->total() }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content">
                        {{-- Onglet Dépenses --}}
                        <div class="tab-pane active" id="tabDepenses">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th><th>Référence</th><th>Catégorie</th>
                                            <th class="text-right">Montant</th><th>Motif</th><th>Agent</th>
                                            <th class="text-center">Statut</th><th class="text-center">Justif.</th><th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($depenses as $dep)
                                        <tr>
                                            <td class="text-nowrap small">{{ $dep->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="small">{{ $dep->transaction->reference ?? '-' }}</td>
                                            <td>{{ $dep->categorie->libelle ?? '-' }}</td>
                                            <td class="text-right font-weight-bold text-danger">{{ number_format($dep->transaction->montant ?? 0, 2, ',', ' ') }} {{ $dep->transaction->devise_code ?? '' }}</td>
                                            <td class="small">{{ \Illuminate\Support\Str::limit($dep->motif, 35) }}</td>
                                            <td class="small">{{ $dep->agent ? $dep->agent->nom . ' ' . $dep->agent->prenom : $dep->agent_matricule }}</td>
                                            <td class="text-center">
                                                @if(($dep->transaction->statut ?? '') === 'ANNULE')
                                                    <span class="badge badge-secondary">Annulée</span>
                                                @else
                                                    <span class="badge badge-success">Confirmée</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($dep->piece_justificative)
                                                    <a href="{{ Storage::url($dep->piece_justificative) }}" target="_blank"><i class="fas fa-file-alt text-info"></i></a>
                                                @else <span class="text-muted">-</span> @endif
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="{{ route('caisses.depenses.recu', $dep->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="Imprimer le reçu"><i class="fas fa-print"></i></a>
                                                @if(($dep->transaction->statut ?? '') !== 'ANNULE')
                                                    <button type="button" class="btn btn-xs btn-outline-danger btn-annuler-op" data-type="depense" data-id="{{ $dep->id }}" title="Annuler"><i class="fas fa-undo"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="9" class="text-center text-muted py-4">Aucune dépense enregistrée.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($depenses->hasPages())<div class="card-footer">{{ $depenses->links() }}</div>@endif
                        </div>

                        {{-- Onglet Recettes --}}
                        <div class="tab-pane" id="tabRecettes">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th><th>Référence</th><th>Catégorie</th>
                                            <th class="text-right">Montant</th><th>Motif</th><th>Agent</th>
                                            <th class="text-center">Statut</th><th class="text-center">Justif.</th><th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recettes as $rec)
                                        <tr>
                                            <td class="text-nowrap small">{{ $rec->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="small">{{ $rec->transaction->reference ?? '-' }}</td>
                                            <td>{{ $rec->categorie->libelle ?? '-' }}</td>
                                            <td class="text-right font-weight-bold text-success">{{ number_format($rec->transaction->montant ?? 0, 2, ',', ' ') }} {{ $rec->transaction->devise_code ?? '' }}</td>
                                            <td class="small">{{ \Illuminate\Support\Str::limit($rec->motif, 35) }}</td>
                                            <td class="small">{{ $rec->agent ? $rec->agent->nom . ' ' . $rec->agent->prenom : $rec->agent_matricule }}</td>
                                            <td class="text-center">
                                                @if(($rec->transaction->statut ?? '') === 'ANNULE')
                                                    <span class="badge badge-secondary">Annulée</span>
                                                @else
                                                    <span class="badge badge-success">Confirmée</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($rec->piece_justificative)
                                                    <a href="{{ Storage::url($rec->piece_justificative) }}" target="_blank"><i class="fas fa-file-alt text-info"></i></a>
                                                @else <span class="text-muted">-</span> @endif
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="{{ route('caisses.recettes.recu', $rec->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="Imprimer le reçu"><i class="fas fa-print"></i></a>
                                                @if(($rec->transaction->statut ?? '') !== 'ANNULE')
                                                    <button type="button" class="btn btn-xs btn-outline-danger btn-annuler-op" data-type="recette" data-id="{{ $rec->id }}" title="Annuler"><i class="fas fa-undo"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="9" class="text-center text-muted py-4">Aucune recette enregistrée.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($recettes->hasPages())<div class="card-footer">{{ $recettes->links() }}</div>@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
</div>
</section>
@endsection

@push('css')
<style>
@media (max-width: 767.98px) {
    .operation-soldes-bar {
        display: grid !important;
        grid-template-columns: 1fr;
        align-items: stretch !important;
    }
    .operation-soldes-bar small,
    .operation-soldes-bar .solde-pill,
    .operation-soldes-bar > .badge {
        width: 100%;
        margin-left: 0 !important;
    }
    .operation-soldes-bar .solde-pill,
    .operation-soldes-bar > .badge {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
}
</style>
@endpush

@push('js')
<script>
$(function () {
    const categoriesDepenses = @json($categoriesDepenses->map(fn($c) => ['id' => $c->id, 'libelle' => $c->libelle, 'compte' => $c->numero_compte_charge]));
    const categoriesRecettes = @json($categoriesRecettes->map(fn($c) => ['id' => $c->id, 'libelle' => $c->libelle, 'compte' => $c->numero_compte_produit]));

    // ── Recherche progressive (Select2) sur la catégorie, comme dans le module Opérations ──
    function _categorieMatcher(params, data) {
        if (!params.term || params.term.trim() === '') return data;
        const term = params.term.trim().toUpperCase();
        const text = (data.text || '').toUpperCase();
        return text.indexOf(term) > -1 ? data : null;
    }

    function initCategorieSelect2() {
        if ($('#selCategorieOp').hasClass('select2-hidden-accessible')) {
            $('#selCategorieOp').select2('destroy');
        }
        $('#selCategorieOp').select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('#cardFormOpAdmin'),
            placeholder: '— Sélectionner une catégorie —',
            allowClear: true,
            matcher: _categorieMatcher,
            language: { noResults: function () { return 'Aucune catégorie trouvée.'; } },
        });
    }

    function refreshCategorieOptions() {
        const sens = $('#selSensOp').val();
        const list = sens === 'SORTIE' ? categoriesDepenses : categoriesRecettes;
        const $sel = $('#selCategorieOp');

        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
        $sel.empty().append('<option value="">— Sélectionner une catégorie —</option>');
        list.forEach(c => {
            $sel.append(`<option value="${c.id}">${c.libelle} — compte ${c.compte}</option>`);
        });
        initCategorieSelect2();

        if (sens === 'SORTIE') {
            $('#cardFormOpAdmin').removeClass('card-success').addClass('card-danger');
            $('#cardFormOpAdminTitle i').removeClass('fa-plus-circle text-success').addClass('fa-arrow-down text-danger');
            $('#cardFormOpAdminTitle').html('<i class="fas fa-arrow-down text-danger mr-1"></i> Nouvelle dépense (Sortie)');
            $('#btnSubmitOpAdmin').removeClass('btn-success').addClass('btn-danger');
            $('#btnSubmitOpAdminLabel').text('Enregistrer la dépense');
        } else {
            $('#cardFormOpAdmin').removeClass('card-danger').addClass('card-success');
            $('#cardFormOpAdminTitle').html('<i class="fas fa-arrow-up text-success mr-1"></i> Nouvelle recette (Entrée)');
            $('#btnSubmitOpAdmin').removeClass('btn-danger').addClass('btn-success');
            $('#btnSubmitOpAdminLabel').text('Enregistrer la recette');
        }
    }

    $('#selSensOp').on('change', refreshCategorieOptions);
    refreshCategorieOptions();

    $('#formOpAdmin').on('submit', function (e) {
        e.preventDefault();
        const sens = $('#selSensOp').val();
        const url = sens === 'SORTIE' ? '{{ route("caisses.depenses.store") }}' : '{{ route("caisses.recettes.store") }}';
        const formData = new FormData(this);

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    if (window.showSystemMessage) { showSystemMessage('success', res.message); }
                    else { alert(res.message); }

                    // Impression du reçu : optionnelle, demandée à l'utilisateur.
                    // La page ne se recharge qu'après la réponse de l'utilisateur (pas de timeout
                    // arbitraire qui pourrait fermer la boîte de dialogue avant qu'il ait répondu).
                    if (res.recu_url) {
                        if (window.showUniversalConfirm) {
                            // Recharge la page une seule fois, quelle que soit la façon dont la
                            // boîte de dialogue est fermée (Imprimer, Annuler, Echap, clic extérieur).
                            $('#universalConfirmModal').one('hidden.bs.modal', function () {
                                location.reload();
                            });
                            showUniversalConfirm(
                                'Voulez-vous imprimer le reçu de cette opération ?',
                                () => window.open(res.recu_url, '_blank'),
                                { title: 'Impression du reçu', btnLabel: 'Imprimer', btnClass: 'btn-primary', icon: 'fas fa-print', showWarning: false }
                            );
                        } else if (confirm('Voulez-vous imprimer le reçu de cette opération ?')) {
                            window.open(res.recu_url, '_blank');
                            location.reload();
                        } else {
                            location.reload();
                        }
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Erreur lors de l\'enregistrement.';
                if (window.showSystemMessage) { showSystemMessage('danger', msg); }
                else { alert(msg); }
            }
        });
    });

    $('.btn-annuler-op').on('click', function () {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const routeBase = type === 'depense' ? '{{ url("caisses/depenses") }}' : '{{ url("caisses/recettes") }}';
        const action = () => {
            $.post(`${routeBase}/${id}/annuler`, { _token: '{{ csrf_token() }}' })
                .done(res => { if (res.success) location.reload(); })
                .fail(xhr => alert(xhr.responseJSON?.message || 'Erreur.'));
        };
        const libelle = type === 'depense' ? 'cette dépense' : 'cette recette';
        if (window.showUniversalConfirm) {
            showUniversalConfirm(`Confirmer l'annulation de ${libelle} ?`, action, { title: 'Annulation', btnClass: 'btn-danger' });
        } else if (confirm(`Confirmer l'annulation de ${libelle} ?`)) {
            action();
        }
    });
});
</script>
@endpush

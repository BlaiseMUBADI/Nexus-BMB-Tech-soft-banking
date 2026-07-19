@extends('layouts.app')

@section('title', 'Virements Bancaires')
@section('page_title', 'Virements Bancaires')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
<li class="breadcrumb-item"><a href="{{ route('comptabilite.dashboard') }}">Comptabilité</a></li>
<li class="breadcrumb-item active">Virements bancaires</li>
@endsection

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0 text-muted"><i class="fas fa-exchange-alt mr-1"></i>Demandes de virement entre comptes clients</h6>
        @if(in_array('EBEN-PER119', $userPermCodes ?? []))
            <a href="{{ route('comptabilite.virements.creer') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i>Proposer un virement
            </a>
        @endif
    </div>

    {{-- ── Filtre statut ────────────────────────────────────────── --}}
    <div class="card card-outline card-info mb-3 shadow-sm">
        <div class="card-body py-2">
            <form method="GET" class="form-inline">
                <label class="small font-weight-bold mr-2 mb-0">Statut</label>
                <select name="statut" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="">— Tous —</option>
                    <option value="EN_ATTENTE" {{ request('statut') === 'EN_ATTENTE' ? 'selected' : '' }}>En attente</option>
                    <option value="APPROUVEE" {{ request('statut') === 'APPROUVEE' ? 'selected' : '' }}>Approuvée</option>
                    <option value="REJETEE" {{ request('statut') === 'REJETEE' ? 'selected' : '' }}>Rejetée</option>
                </select>
            </form>
        </div>
    </div>

    <div class="card card-outline card-info shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="thead-dark" style="font-size:.80rem;">
                        <tr>
                            <th>Réf.</th>
                            <th>Compte source</th>
                            <th>Compte destination</th>
                            <th class="text-right">Montant source</th>
                            <th class="text-right">Montant destination</th>
                            <th>Motif</th>
                            <th>Proposé par</th>
                            <th>Date</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:.85rem;">
                        @forelse($demandes as $d)
                            <tr>
                                <td><small class="text-monospace">DVIR-{{ $d->id }}</small></td>
                                <td>
                                    <small class="text-monospace">{{ $d->compte_source_code }}</small><br>
                                    <small class="text-muted">{{ $d->clientSource?->full_name ?? $d->client_source_matricule }}</small>
                                </td>
                                <td>
                                    <small class="text-monospace">{{ $d->compte_dest_code }}</small><br>
                                    <small class="text-muted">{{ $d->clientDest?->full_name ?? $d->client_dest_matricule }}</small>
                                </td>
                                <td class="text-right font-weight-bold">{{ number_format((float) $d->montant_source, 2, ',', ' ') }} {{ $d->devise_source }}</td>
                                <td class="text-right">
                                    {{ number_format((float) $d->montant_dest, 2, ',', ' ') }} {{ $d->devise_dest }}
                                    @if($d->taux_change)
                                        <br><small class="text-muted">Taux : {{ $d->taux_change }}</small>
                                    @endif
                                </td>
                                <td><small style="max-width:160px; display:block; white-space:normal;">{{ $d->motif }}</small></td>
                                <td><small>{{ $d->comptable?->prenom }} {{ $d->comptable?->nom }}</small></td>
                                <td><small>{{ $d->propose_le?->format('d/m/Y H:i') }}</small></td>
                                <td class="text-center"><span class="badge {{ $d->statutBadgeClass() }}">{{ $d->statutLabel() }}</span></td>
                                <td class="text-center" style="min-width:110px;">
                                    @if($d->statut === 'EN_ATTENTE' && in_array('EBEN-PER120', $userPermCodes ?? []))
                                        <button class="btn btn-xs btn-success btn-approuver mr-1" data-id="{{ $d->id }}"
                                            data-resume="{{ $d->compte_source_code }} → {{ $d->compte_dest_code }} : {{ number_format((float) $d->montant_source, 2, ',', ' ') }} {{ $d->devise_source }}"
                                            title="Approuver"><i class="fas fa-check"></i></button>
                                        <button class="btn btn-xs btn-danger btn-rejeter" data-id="{{ $d->id }}"
                                            data-resume="{{ $d->compte_source_code }} → {{ $d->compte_dest_code }} : {{ number_format((float) $d->montant_source, 2, ',', ' ') }} {{ $d->devise_source }}"
                                            title="Rejeter"><i class="fas fa-times"></i></button>
                                    @elseif($d->statut === 'APPROUVEE')
                                        <a href="{{ route('comptabilite.virements.recu', $d->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="Reçu PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @elseif($d->statut === 'REJETEE')
                                        <small class="text-muted d-block" title="{{ $d->commentaire_validateur }}"><em>{{ Str::limit($d->commentaire_validateur, 30) }}</em></small>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune demande de virement trouvée.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($demandes->hasPages())
            <div class="card-footer py-2">{{ $demandes->links() }}</div>
        @endif
    </div>
</div>

{{-- ── Modal Rejeter (utilise showUniversalConfirm pour Approuver — voir JS) ──── --}}
<div class="modal fade" id="modalRejeter" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header py-2 bg-danger text-white">
                <h6 class="modal-title mb-0"><i class="fas fa-times-circle mr-2"></i>Rejeter le virement</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="small mb-2 text-muted" id="rejeterResume">—</p>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Motif du rejet <span class="text-danger">*</span></label>
                    <textarea id="inpCommentaireRej" class="form-control form-control-sm" rows="3" maxlength="500"
                        placeholder="Expliquez pourquoi vous rejetez cette demande…"></textarea>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                <button class="btn btn-sm btn-danger font-weight-bold" id="btnConfirmerRej">
                    <i class="fas fa-times mr-1"></i>Rejeter
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' } });

    var urlApprouver = '{{ route("comptabilite.virements.approuver", ["id" => "__ID__"]) }}';
    var urlRejeter   = '{{ route("comptabilite.virements.rejeter", ["id" => "__ID__"]) }}';

    // ── Approuver : modal de confirmation standard de l'application ──
    $(document).on('click', '.btn-approuver', function () {
        var appId = $(this).data('id');
        var resume = $(this).data('resume') || '—';
        var $btn = $(this);

        showUniversalConfirm(
            'Approuver le virement <strong>' + resume + '</strong> ?'
            + '<br><small class="text-warning">Cette action exécute réellement le mouvement d\'argent entre les deux comptes.</small>',
            function () {
                $btn.prop('disabled', true);
                var url = urlApprouver.replace('__ID__', appId);
                $.post(url)
                    .done(function (r) {
                        showSystemMessage('success', r.message || 'Virement approuvé.');
                        setTimeout(function () { window.location.reload(); }, 1200);
                    })
                    .fail(function (xhr) {
                        $btn.prop('disabled', false);
                        handleAjaxFail(xhr, 'Approbation virement bancaire');
                    });
            },
            {
                title:       'Approuver le virement',
                btnLabel:    'Confirmer l\'approbation',
                btnClass:    'btn-success',
                icon:        'fas fa-check-circle',
                bodyIcon:    'fas fa-hand-holding-usd fa-3x text-success',
                headerClass: 'bg-success text-white',
                showWarning: true,
            }
        );
    });

    // ── Rejeter : modal custom (nécessite la saisie d'un motif obligatoire) ──
    var _rejId = null;
    $(document).on('click', '.btn-rejeter', function () {
        _rejId = $(this).data('id');
        $('#rejeterResume').text($(this).data('resume') || '—');
        $('#inpCommentaireRej').val('');
        $('#modalRejeter').modal('show');
    });
    $('#btnConfirmerRej').on('click', function () {
        if (!_rejId) return;
        var commentaire = $.trim($('#inpCommentaireRej').val());
        if (!commentaire) { showSystemMessage('warning', 'Le motif du rejet est obligatoire.'); return; }
        var url = urlRejeter.replace('__ID__', _rejId);
        $('#btnConfirmerRej').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Traitement…');
        $.post(url, { commentaire_validateur: commentaire })
            .done(function (r) {
                $('#modalRejeter').modal('hide');
                showSystemMessage('success', r.message || 'Demande rejetée.');
                setTimeout(function () { window.location.reload(); }, 1200);
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Rejet virement bancaire');
            })
            .always(function () {
                $('#btnConfirmerRej').prop('disabled', false).html('<i class="fas fa-times mr-1"></i>Rejeter');
            });
    });
});
</script>
@endpush

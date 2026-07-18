@extends('layouts.app')

@section('page_title', 'Dépenses de caisse')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Dépenses')

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

    <div class="row">
        {{-- ── Formulaire de saisie ─────────────────────────── --}}
        <div class="col-lg-4 mb-3">
            <div class="card card-danger card-outline shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-receipt mr-2"></i>Nouvelle dépense</h5>
                </div>
                <div class="card-body">
                    @if($guichet->statut_operationnel !== 'OUVERT')
                        <div class="alert alert-warning py-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Guichet non ouvert. Ouvrez votre session pour saisir une dépense.
                        </div>
                    @endif

                    <form id="formDepense">
                        @csrf
                        <div class="form-group">
                            <label>Catégorie <span class="text-danger">*</span></label>
                            <select name="categorie_id" class="form-control form-control-sm" required {{ $guichet->statut_operationnel !== 'OUVERT' ? 'disabled' : '' }}>
                                <option value="">-- Choisir --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->libelle }} <small>({{ $cat->numero_compte_charge }})</small></option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label>Montant <span class="text-danger">*</span></label>
                                <input type="number" name="montant" step="0.01" min="0.01" class="form-control form-control-sm" required {{ $guichet->statut_operationnel !== 'OUVERT' ? 'disabled' : '' }}>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Devise <span class="text-danger">*</span></label>
                                <select name="devise_code" class="form-control form-control-sm" required {{ $guichet->statut_operationnel !== 'OUVERT' ? 'disabled' : '' }}>
                                    <option value="CDF">CDF</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Motif <span class="text-danger">*</span></label>
                            <textarea name="motif" rows="2" class="form-control form-control-sm" required maxlength="500" {{ $guichet->statut_operationnel !== 'OUVERT' ? 'disabled' : '' }}></textarea>
                        </div>
                        <div class="form-group">
                            <label>Pièce justificative <small class="text-muted">(facture/reçu, optionnel)</small></label>
                            <input type="file" name="piece_justificative" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" {{ $guichet->statut_operationnel !== 'OUVERT' ? 'disabled' : '' }}>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block" {{ $guichet->statut_operationnel !== 'OUVERT' ? 'disabled' : '' }}>
                            <i class="fas fa-save mr-1"></i>Enregistrer la dépense
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Liste des dépenses ─────────────────────────── --}}
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list mr-2"></i>Dépenses enregistrées <span class="badge badge-light">{{ $depenses->total() }}</span></h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Catégorie</th>
                                    <th class="text-right">Montant</th>
                                    <th>Motif</th>
                                    <th>Agent</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Justif.</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($depenses as $dep)
                                <tr>
                                    <td class="text-nowrap small">{{ $dep->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="small">{{ $dep->transaction->reference ?? '-' }}</td>
                                    <td>{{ $dep->categorie->libelle ?? '-' }}</td>
                                    <td class="text-right font-weight-bold text-danger">
                                        {{ number_format($dep->transaction->montant ?? 0, 2, ',', ' ') }} {{ $dep->transaction->devise_code ?? '' }}
                                    </td>
                                    <td class="small">{{ \Illuminate\Support\Str::limit($dep->motif, 40) }}</td>
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
                                            <a href="{{ Storage::url($dep->piece_justificative) }}" target="_blank" title="Voir le justificatif">
                                                <i class="fas fa-file-alt text-info"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($dep->transaction->statut ?? '') !== 'ANNULE')
                                            <button type="button" class="btn btn-xs btn-outline-danger btn-annuler-depense" data-id="{{ $dep->id }}" title="Annuler cette dépense">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="text-center text-muted py-4">Aucune dépense enregistrée.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($depenses->hasPages())
                <div class="card-footer">{{ $depenses->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    @endif
</div>
</section>
@endsection

@push('js')
<script>
$(function () {
    $('#formDepense').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: '{{ route("caisses.depenses.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    if (window.showSystemMessage) { showSystemMessage('success', res.message); }
                    else { alert(res.message); }
                    setTimeout(() => location.reload(), 1200);
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Erreur lors de l\'enregistrement.';
                if (window.showSystemMessage) { showSystemMessage('danger', msg); }
                else { alert(msg); }
            }
        });
    });

    $('.btn-annuler-depense').on('click', function () {
        const id = $(this).data('id');
        const action = () => {
            $.post(`{{ url('caisses/depenses') }}/${id}/annuler`, { _token: '{{ csrf_token() }}' })
                .done(res => { if (res.success) location.reload(); })
                .fail(xhr => alert(xhr.responseJSON?.message || 'Erreur.'));
        };
        if (window.showUniversalConfirm) {
            showUniversalConfirm('Confirmer l\'annulation de cette dépense ?', action, { title: 'Annuler la dépense', btnClass: 'btn-danger' });
        } else if (confirm('Confirmer l\'annulation de cette dépense ?')) {
            action();
        }
    });
});
</script>
@endpush

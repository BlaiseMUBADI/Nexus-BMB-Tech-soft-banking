@extends('layouts.app')

@section('page_title', 'Catégories de recettes')
@section('breadcrumb_parent', 'Comptabilité')
@section('breadcrumb', 'Catégories de recettes')

@section('content')
<section class="content">
<div class="container-fluid">

    <div class="alert alert-info py-2">
        <i class="fas fa-info-circle mr-2"></i>
        Chaque catégorie est mappée à un <strong>compte de produit OHADA</strong> (classe 7, mouvementable). Ce mapping pilote automatiquement l'écriture comptable générée lors de la saisie d'une recette. Modifier le mapping n'affecte jamais les écritures déjà comptabilisées.
    </div>

    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-plus-circle mr-2"></i>Nouvelle catégorie</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comptabilite.categories-recettes.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>Libellé <span class="text-danger">*</span></label>
                            <input type="text" name="libelle" class="form-control form-control-sm" required maxlength="150">
                        </div>
                        <div class="form-group">
                            <label>Compte de produit OHADA <span class="text-danger">*</span></label>
                            <select name="numero_compte_produit" class="form-control select-compte-ohada" style="width:100%;" required>
                                <option value="">— Sélectionner un compte —</option>
                                @foreach($comptesEligibles as $c)
                                    <option value="{{ $c->numero_compte }}">{{ $c->numero_compte }} — {{ $c->libelle }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Seuls les comptes de produit (classe 7) mouvementables sont proposés. Recherchez par numéro ou libellé.</small>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm btn-block"><i class="fas fa-save mr-1"></i>Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header"><h6 class="mb-0"><i class="fas fa-list mr-2"></i>Catégories existantes <span class="badge badge-light">{{ $categories->count() }}</span></h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr><th>Libellé</th><th>Compte OHADA</th><th class="text-center">Statut</th><th class="text-center">Actions</th></tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $cat)
                                <tr>
                                    <td>{{ $cat->libelle }}</td>
                                    <td><span class="badge badge-secondary">{{ $cat->numero_compte_produit }}</span> {{ $cat->compteProduit->libelle ?? '' }}</td>
                                    <td class="text-center">
                                        @if($cat->est_actif)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Inactive</span>@endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-primary" data-toggle="modal" data-target="#modalEditRec{{ $cat->id }}"><i class="fas fa-edit"></i></button>
                                        <form method="POST" action="{{ route('comptabilite.categories-recettes.destroy', $cat) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEditRec{{ $cat->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('comptabilite.categories-recettes.update', $cat) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-header"><h5 class="modal-title">Modifier « {{ $cat->libelle }} »</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Libellé</label>
                                                        <input type="text" name="libelle" class="form-control form-control-sm" value="{{ $cat->libelle }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Compte de produit OHADA</label>
                                                        <select name="numero_compte_produit" class="form-control select-compte-ohada" style="width:100%;" required>
                                                            @foreach($comptesEligibles as $c)
                                                                <option value="{{ $c->numero_compte }}" {{ $cat->numero_compte_produit == $c->numero_compte ? 'selected' : '' }}>{{ $c->numero_compte }} — {{ $c->libelle }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="actifRec{{ $cat->id }}" name="est_actif" value="1" {{ $cat->est_actif ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="actifRec{{ $cat->id }}">Catégorie active</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-success btn-sm">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Aucune catégorie configurée.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</section>
@endsection

@push('js')
<script>
$(function () {
    function _compteMatcher(params, data) {
        if (!params.term || params.term.trim() === '') return data;
        const term = params.term.trim().toUpperCase();
        const text = (data.text || '').toUpperCase();
        return text.indexOf(term) > -1 ? data : null;
    }

    $('.select-compte-ohada').each(function () {
        const $parentModal = $(this).closest('.modal');
        $(this).select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $parentModal.length ? $parentModal : $('body'),
            placeholder: '— Sélectionner un compte —',
            allowClear: true,
            matcher: _compteMatcher,
            language: { noResults: function () { return 'Aucun compte trouvé.'; } },
        });
    });
});
</script>
@endpush

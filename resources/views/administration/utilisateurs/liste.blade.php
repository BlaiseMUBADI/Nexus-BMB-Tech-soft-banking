@extends('layouts.app')

@section('page_title', 'Utilisateurs')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Utilisateurs')

@section('content')
<div class="container-fluid">

    {{-- MINI-DASHBOARD --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total</span>
                    <span class="info-box-number">{{ $users->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Actifs</span>
                    <span class="info-box-number">{{ $users->where('etat', 'actif')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-user-slash"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Inactifs</span>
                    <span class="info-box-number">{{ $users->where('etat', '!=', 'actif')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Avec agent</span>
                    <span class="info-box-number">{{ $users->filter(fn($u) => $u->agent)->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- LISTE --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users mr-2"></i> Liste des utilisateurs
                    </h3>
                    <a href="{{ route('administration.utilisateurs.nouveau') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-user-plus mr-1"></i> Ajouter
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="px-3 pt-3 pb-1">
                        <input type="text" id="searchUsers" class="form-control form-control-sm"
                               placeholder="🔍 Rechercher un utilisateur…">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm mb-0" id="usersTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Agent</th>
                                    <th>Login</th>
                                    <th>Email</th>
                                    <th class="text-center" style="width:90px">État</th>
                                    <th class="text-center" style="width:110px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $i => $user)
                                <tr id="row-user-{{ $user->id }}">
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        @if($user->agent)
                                            <span class="font-weight-bold">{{ $user->agent->nom }}</span>
                                            <small class="text-muted ml-1">{{ $user->agent->postnom }} {{ $user->agent->prenom }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        @if($user->etat === 'actif')
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('administration.utilisateurs.show', $user->id) }}"
                                           class="btn btn-xs btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.utilisateurs.edit', $user->id) }}"
                                           class="btn btn-xs btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-xs btn-danger btn-delete-user"
                                            data-id="{{ $user->id }}"
                                            data-nom="{{ $user->name }}"
                                            title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Aucun utilisateur enregistré.
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

</div>
@endsection

@push('css')
<style>
    #usersTable tbody tr:hover > td {
        background-color: rgba(0, 123, 255, 0.13) !important;
        color: #fff !important;
    }
</style>
@endpush

@push('js')
<script>
(function () {
    'use strict';
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $(function () {

        // ── Live search ────────────────────────────────────────────────
        $('#searchUsers').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#usersTable tbody tr').each(function () {
                $(this).toggle(q === '' || $(this).text().toLowerCase().indexOf(q) !== -1);
            });
        });

        // ── Supprimer utilisateur ──────────────────────────────────────
        $(document).on('click', '.btn-delete-user', function () {
            var id  = $(this).data('id');
            var nom = $(this).data('nom');
            var url = '{{ route("administration.utilisateurs.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id);
            showUniversalConfirm(
                'Supprimer l\'utilisateur <strong>' + nom + '</strong> ?'
                + '<br><small class="text-danger">Cette action est <u>irréversible</u>.</small>',
                function () {
                    $.ajax({ url: url, type: 'POST', data: { _method: 'DELETE' } })
                        .done(function (resp) {
                            showSystemMessage('success', resp.message || 'Utilisateur supprimé.');
                            $('#row-user-' + id).fadeOut(400, function () { $(this).remove(); });
                        })
                        .fail(function (xhr) {
                            showSystemMessage('error',
                                (xhr.responseJSON && xhr.responseJSON.message)
                                    ? xhr.responseJSON.message : 'Erreur lors de la suppression.');
                        });
                },
                'Confirmer la suppression'
            );
        });

    });
}());
</script>
@endpush

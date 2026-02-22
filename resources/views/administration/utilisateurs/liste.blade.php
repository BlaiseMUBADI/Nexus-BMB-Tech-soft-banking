@extends('layouts.app')

@section('page_title', 'Liste des utilisateurs')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Liste des utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
                <a href="{{ route('administration.utilisateurs.nouveau') }}" class="btn btn-primary mb-3">Ajouter un utilisateur</a>
        </div>
        <div class="card-body">
            <!-- Conteneur fusionné pour boutons et recherche DataTables -->
            <div id="users-table-toolbar" class="d-flex flex-wrap align-items-center justify-content-between mb-3 p-2 rounded shadow-sm" style="background: #232a32; border: 1px solid #444; min-height: 56px;">
                <div id="users-table-buttons" class="mb-2 mb-md-0 "></div>
                <div id="users-table-search" class="datatable-search ms-md-3 "></div>
            </div>

            <div class="table-responsive">
                <table id="users-table" class="table table-bordered table-striped datatable" data-buttons-container="#users-table-buttons">
                    <thead>
                        <tr>
                            <th>N°</th>                            
                            <th>Agent</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $loopIndex => $user)
                        <tr>
                            <td>{{ $loopIndex + 1 }}</td>
                            <td>
                                @if($user->agent)
                                    {{ $user->agent->nom }} {{ $user->agent->postnom }} {{ $user->agent->prenom }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->etat === 'actif')
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-secondary">Inactif</span>
                                @endif
                            </td>
                            
                            <td>
                                <a href="{{ route('administration.utilisateurs.show', $user->id) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-warning btn-edit-user" data-id="{{ $user->id }}" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('administration.utilisateurs.destroy', $user->id) }}" method="POST" class="d-inline delete-user-form" data-user-id="{{ $user->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-user" data-id="{{ $user->id }}" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function() {
    let formToDelete = null;
    // Gestion du bouton supprimer utilisateur
    $('.btn-delete-user').on('click', function(e) {
        e.preventDefault();
        formToDelete = $(this).closest('form');
        if(typeof showUniversalConfirm === 'function') {
            showUniversalConfirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?<br><span class="fw-bold" style="color:#ffc107; font-size:1.1em; text-shadow:0 1px 2px #000;">Cette action est <u>irréversible</u>.</span>', function() {
                if(formToDelete) formToDelete.submit();
            }, 'Confirmation de suppression');
        } else {
            if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                if(formToDelete) formToDelete.submit();
            }
        }
    });
});
</script>
@endpush

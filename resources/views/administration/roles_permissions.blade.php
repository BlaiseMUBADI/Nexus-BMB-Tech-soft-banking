@push('js')
<script>
    // Setup global AJAX CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function () {
        // Disparition automatique de l'alerte de succès après 2,5s
        setTimeout(function () {
            $('.alert-success').alert('close');
        }, 2500);
    });

</script>
@extends('layouts.app')

@section('page_title', 'Gestion des rôles et permissions')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Rôles & Permissions')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs" id="rolesPermissionsTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="roles-tab" data-toggle="tab" href="#roles" role="tab"
                            aria-controls="roles" aria-selected="true">Rôles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="permissions-tab" data-toggle="tab" href="#permissions" role="tab"
                            aria-controls="permissions" aria-selected="false">Permissions</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="rolesPermissionsTabsContent">
                    <div class="tab-pane fade show active" id="roles" role="tabpanel" aria-labelledby="roles-tab">
                        <h4>Liste des rôles</h4>
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form id="addRoleForm" class="mb-4">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="roleName">Nom du rôle</label>
                                    <input type="text" name="nom" id="roleName" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="roleDesc">Description</label>
                                    <input type="text" name="description" id="roleDesc" class="form-control">
                                </div>
                                <div class="form-group col-md-2 align-self-end">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="fas fa-plus-circle mr-1"></i>Ajouter</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="roles-table" class="table table-bordered table-striped"
                                data-buttons-container="#roles-table-buttons">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $loopIndex => $role)
                                        <tr>
                                            <td>{{ $loopIndex + 1 }}</td>
                                            <td>{{ $role->code }}</td>
                                            <td>{{ $role->nom }}</td>
                                            <td>{{ $role->description }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning btn-edit-role"
                                                    data-id="{{ $role->code }}" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('administration.roles.destroy', $role->code) }}"
                                                    method="POST" class="d-inline delete-role-form"
                                                    data-role-id="{{ $role->code }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-role"
                                                        data-id="{{ $role->id }}" title="Supprimer">
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



                    <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
                        <h4>Liste des permissions</h4>
                        <form id="addPermissionForm" class="mb-4">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="permissionName">Nom de la permission</label>
                                    <input type="text" name="nom" id="permissionName" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="permissionDesc">Description</label>
                                    <input type="text" name="description" id="permissionDesc" class="form-control">
                                </div>
                                <div class="form-group col-md-2 align-self-end">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="fas fa-plus-circle mr-1"></i>Ajouter</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="permissions-table" class="table table-bordered table-striped"
                                data-buttons-container="#permissions-table-buttons">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $loopIndex => $permission)
                                        <tr>
                                            <td>{{ $loopIndex + 1 }}</td>
                                            <td>{{ $permission->code }}</td>
                                            <td>{{ $permission->nom }}</td>
                                            <td>{{ $permission->description }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning btn-edit-permission"
                                                    data-id="{{ $permission->code }}" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>
@endsection


@section('css')
    <style>
        /* Couleurs modernes pour la sélection et le survol des lignes DataTable (universel pour cette page) */
        table.dataTable tbody tr.datatable-selected-row {
            background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
            transition: background 0.3s, color 0.3s;
        }

        table.dataTable tbody tr:hover:not(.datatable-selected-row) {
            background: linear-gradient(90deg, #06b6d4 0%, #3b82f6 100%) !important;
            color: #fff !important;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(59, 130, 246, 0.10);
            transition: background 0.3s, color 0.3s;
        }
    </style>
@endsection

@push('js')
    <script>
        var baseUrl = "{{ url('') }}";
        $(document).ready(function () {
            // Soumission AJAX simplifiée du formulaire d'ajout de rôle
            $('#addRoleForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = baseUrl + '/administration/roles-permissions';
                var formData = form.serialize();
                $.post(url, formData)
                    .done(function (response) {
                        showAppModal('success', 'Rôle ajouté avec succès.');
                        reloadRolesTable();
                        // Réinitialise le formulaire
                        form[0].reset();
                    })
                    .fail(function (xhr) {
                        let msg = 'Erreur lors de l\'ajout du rôle.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg += '<ul>';
                            $.each(xhr.responseJSON.errors, function (k, v) { msg += '<li>' + v + '</li>'; });
                            msg += '</ul>';
                        }
                        showAppModal('error', msg);
                    });
            });


            // Soumission AJAX simplifiée du formulaire d'ajout de permission
            $('#addPermissionForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = baseUrl + '/administration/permissions';
                var formData = form.serialize();
                $.post(url, formData)
                    .done(function (response) {
                        showAppModal('success', 'Permission ajoutée avec succès.');
                        reloadPermissionsTable();
                        form[0].reset();
                    })
                    .fail(function (xhr) {
                        let msg = "Erreur lors de l'ajout de la permission.";
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg += '<ul>';
                            $.each(xhr.responseJSON.errors, function (k, v) { msg += '<li>' + v + '</li>'; });
                            msg += '</ul>';
                        }
                        showAppModal('error', msg);
                    });
            });


            
            // DataTable init pour le tableau des rôles (évite la réinitialisation)
            if (!$.fn.DataTable.isDataTable('#roles-table')) {
                $('#roles-table').DataTable({
                    paging: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
                    language: {
                        url: baseUrl + '/plugins/datatables/i18n/fr-FR.json',
                        paginate: {
                            first: "Premier",
                            last: "Dernier",
                            next: "Suivant",
                            previous: "Précédent"
                        },
                        search: "Recherche :",
                        info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        infoEmpty: "Aucune entrée à afficher",
                        infoFiltered: "(filtré à partir de _MAX_ entrées)",
                        lengthMenu: "Afficher _MENU_ entrées",
                    }
                });
            }
            // DataTable init pour le tableau des permissions
            if (!$.fn.DataTable.isDataTable('#permissions-table')) {
                $('#permissions-table').DataTable({
                    paging: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
                    language: {
                        url: baseUrl + '/plugins/datatables/i18n/fr-FR.json',
                        paginate: {
                            first: "Premier",
                            last: "Dernier",
                            next: "Suivant",
                            previous: "Précédent"
                        },
                        search: "Recherche :",
                        info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        infoEmpty: "Aucune entrée à afficher",
                        infoFiltered: "(filtré à partir de _MAX_ entrées)",
                        lengthMenu: "Afficher _MENU_ entrées",
                    }
                });
            }
            // Sélection d'un rôle
            $('#roles-table tbody').on('click', 'tr', function () {
                $('#roles-table tbody tr').removeClass('datatable-selected-row');
                $(this).addClass('datatable-selected-row');
            });

            // Selection d'une permission
            $('#permissions-table tbody').on('click', 'tr', function () {
                $('#permissions-table tbody tr').removeClass('datatable-selected-row');
                $(this).addClass('datatable-selected-row');
            });


        });

        // Fonction utilitaire pour recharger le tableau des rôles et réinitialiser DataTable
        function reloadRolesTable() {
            var reloadUrl = baseUrl + '/administration/roles-table';
            $.get(reloadUrl, function (tableHtml) {
                var $tableContainer = $('#roles-table').closest('.table-responsive');
                $tableContainer.html(tableHtml);
                $('#roles-table').DataTable({
                    paging: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
                    language: {
                        url: baseUrl + '/plugins/datatables/i18n/fr-FR.json',
                        paginate: {
                            first: "Premier",
                            last: "Dernier",
                            next: "Suivant",
                            previous: "Précédent"
                        },
                        search: "Recherche :",
                        info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        infoEmpty: "Aucune entrée à afficher",
                        infoFiltered: "(filtré à partir de _MAX_ entrées)",
                        lengthMenu: "Afficher _MENU_ entrées",
                    }
                });
            });
        }

        // Fonction utilitaire pour recharger le tableau des permissions et réinitialiser DataTable
        function reloadPermissionsTable() {
            var reloadUrl = baseUrl + '/administration/permissions-table';
            $.get(reloadUrl, function (tableHtml) {
                var $tableContainer = $('#permissions-table').closest('.table-responsive');
                $tableContainer.html(tableHtml);
                $('#permissions-table').DataTable({
                    paging: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
                    language: {
                        url: baseUrl + '/plugins/datatables/i18n/fr-FR.json',
                        paginate: {
                            first: "Premier",
                            last: "Dernier",
                            next: "Suivant",
                            previous: "Précédent"
                        },
                        search: "Recherche :",
                        info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        infoEmpty: "Aucune entrée à afficher",
                        infoFiltered: "(filtré à partir de _MAX_ entrées)",
                        lengthMenu: "Afficher _MENU_ entrées",
                    }
                });
            });
        }
    </script>
@endpush
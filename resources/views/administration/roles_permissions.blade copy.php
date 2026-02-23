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
                            <div id="permissions-table-container">
                                @include('administration.partials.permissions_table', ['permissions' => $permissions])
                            </div>
                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>
@endsection


@section('css')
    <style>
        /* Couleurs modernes pour la sélection et le survol des rôles */
        #roles-table tbody tr.datatable-selected-row {
            background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
            transition: background 0.3s, color 0.3s;
        }

        #roles-table tbody tr:hover:not(.datatable-selected-row) {
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
        $(document).ready(function () {
            var baseUrl = "{{ url('') }}";
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

            // Soumission AJAX du formulaire d'ajout de rôle
            $('#addRoleForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serializeArray();
                // Ajoute aussi le champ 'name' pour la validation Laravel si besoin
                var nomValue = form.find('input[name="nom"]').val();
                formData.push({ name: 'name', value: nomValue });
                var url = baseUrl + '/administration/roles-permissions';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $.param(formData),
                    dataType: 'json',
                    headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                    success: function (response) {
                        showSystemMessage('success', 'Rôle ajouté avec succès.');
                        location.reload();
                        setTimeout(function () { location.reload(); }, 1000);
                    },
                    error: function (xhr) {
                        let msg = 'Erreur lors de l\'ajout du rôle.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg += '<ul>';
                            $.each(xhr.responseJSON.errors, function (k, v) { msg += '<li>' + v + '</li>'; });
                            msg += '</ul>';
                        }
                        showSystemMessage('error', msg);
                    }
                });
            });

            // Soumission AJAX du formulaire d'ajout de permission
            $('#addPermissionForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serializeArray();
                var nomValue = form.find('input[name="nom"]').val();
                formData.push({ name: 'name', value: nomValue });
                var url = baseUrl + '/administration/permissions';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $.param(formData),
                    dataType: 'json',
                    headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                    success: function (response) {
                        showSystemMessage('success', 'Permission ajoutée avec succès.');
                        // Rafraîchit le tableau des permissions sans recharger la page
                        $.get(baseUrl + '/administration/permissions-table', function (html) {
                            $('#permissions-table-container').html(html);
                            // Réinitialise DataTable si besoin
                            if ($.fn.DataTable.isDataTable('#permissions-table')) {
                                $('#permissions-table').DataTable().destroy();
                            }
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
                    },
                    error: function (xhr) {
                        let msg = 'Erreur lors de l\'ajout de la permission.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg += '<ul>';
                            $.each(xhr.responseJSON.errors, function (k, v) { msg += '<li>' + v + '</li>'; });
                            msg += '</ul>';
                        }
                        showSystemMessage('error', msg);
                    }
                });
            });
        });

        function showSystemMessage(type, message) {
            var alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';
            var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                message +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';
            var container = $('.container-fluid');
            container.prepend(alertHtml);
            setTimeout(function () {
                $('.alert').alert('close');
            }, 2500);
        }
    </script>
@endpush
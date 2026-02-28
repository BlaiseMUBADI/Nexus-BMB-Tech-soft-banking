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
@endpush
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
                    <li class="nav-item">
                        <a class="nav-link" id="attribution-tab" data-toggle="tab" href="#attribution" role="tab"
                            aria-controls="attribution" aria-selected="false">Attribution</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="user-roles-tab" data-toggle="tab" href="#user-roles" role="tab"
                            aria-controls="user-roles" aria-selected="false">Users / Roles</a>
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




                    <div class="tab-pane fade" id="attribution" role="tabpanel" aria-labelledby="attribution-tab">
                        <h4>Attribution des permissions à un rôle</h4>
                        <form id="attachPermissionsForm">
                            <div class="form-group">
                                <label for="selectRole">Choisir un rôle :</label>
                                <select id="selectRole" name="role_code" class="form-control select2" style="width: 100%;" required>
                                    <option value="">-- Sélectionner un rôle --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->code }}">[{{ $role->code }}] {{ $role->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="permissionsListContainer" class="card card-primary card-outline shadow-sm p-3 mb-2 bg-white rounded permissions-card">
                                <div class="permissions-title mb-2">Permissions disponibles :</div>
                                <div class="mb-3">
                                    <label style="font-weight:600; color:#e02424; cursor:pointer;">
                                        <input type="checkbox" id="disableAllPermissions" style="margin-right:0.5em; accent-color:#e02424;"> Désactiver tout
                                    </label>
                                </div>
                                <div class="permissions-list">
                                    @php if (!isset($rolePermissions)) $rolePermissions = collect(); @endphp
                                    @foreach($permissions as $permission)
                                        <label class="permission-item">
                                            <input type="checkbox" class="permission-checkbox perm-checkbox" data-perm-code="{{ $permission->code }}" @if($rolePermissions->contains($permission->code)) checked @endif>
                                            {{ $permission->nom }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                    

                    <div class="tab-pane fade" id="user-roles" role="tabpanel" aria-labelledby="user-roles-tab">
                        <h4>Attribution des rôles à un utilisateur</h4>
                        <form id="attachRolesToUserForm">
                            <div class="form-group">
                                <label for="selectUser">Choisir un utilisateur :</label>
                                <select id="selectUser" name="user_id" class="form-control select2" style="width: 100%;" required>
                                    <option value="">-- Sélectionner un utilisateur --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            [{{ $user->agent ? $user->agent->matricule : 'N/A' }}] 
                                            {{ $user->agent ? ($user->agent->nom . ' ' . $user->agent->postnom . ' ' . $user->agent->prenom) : '(Agent inconnu)'}}
                                            | Login: {{ $user->name }}
                                            | {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="rolesListContainer" class="card card-primary card-outline shadow-sm p-3 mb-2 bg-white rounded">
                                <!-- La liste des rôles à cocher sera chargée ici en AJAX -->
                            </div>
                        </form>
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


            // Attribution des permissions à un rôle (onglet Attribution)
            $('#selectRole').on('change', function () {
                var roleCode = $(this).val();
                if (!roleCode) {
                    $('#permissionsListContainer').html('');
                    return;
                }
                $.get(baseUrl + '/administration/role-permissions/' + roleCode, function (html) {
                    $('#permissionsListContainer').html(html);
                });
            });

            // Délégation d'événement pour les cases à cocher (car contenu AJAX)
            $('#permissionsListContainer').on('change', '.perm-checkbox', function () {
                var roleCode = $('#selectRole').val();
                var permCode = $(this).data('perm-code');
                var checked = $(this).is(':checked');
                var url = checked ? baseUrl + '/administration/role-permissions/attach' : baseUrl + '/administration/role-permissions/detach';
                $.post(url, {
                    role_code: roleCode,
                    permission_code: permCode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function () {
                    showAppModal('success', checked ? 'Permission attachée.' : 'Permission détachée.');
                })
                .fail(function () {
                    showAppModal('error', 'Erreur lors de la mise à jour.');
                });
            });


            // Désactiver tout : coche/décoche toutes les permissions
            $(document).on('change', '#disableAllPermissions', function() {
                var checked = $(this).is(':checked');
                $('.perm-checkbox').prop('checked', !checked).trigger('change');
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

        
    // Initialisation Select2 sur le select des rôles (avec recherche code/nom)
    $(document).ready(function() {
        // Select2 pour le select des rôles
        $('#selectRole').select2({
            theme: 'bootstrap4',
            placeholder: 'Rechercher un rôle par code ou nom',
            allowClear: true,
            width: 'resolve',
            language: {
                noResults: function() { return "Aucun résultat trouvé"; }
            },
            matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;
                if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                    return data;
                }
                return null;
            }
        });

        // Select2 pour le select des utilisateurs (identique à rôles)
        $('#selectUser').select2({
            theme: 'bootstrap4',
            placeholder: 'Rechercher un utilisateur par nom, email ou ID',
            allowClear: true,
            width: 'resolve',
            language: {
                noResults: function() { return "Aucun résultat trouvé"; }
            },
            matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;
                if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                    return data;
                }
                return null;
            }
        });

        // Chargement dynamique des rôles/permissions de l'utilisateur sélectionné
        $('#selectUser').on('change', function() {
            var userId = $(this).val();
            if (!userId) {
                $('#rolesListContainer').html('<div class="alert alert-info">Aucun utilisateur sélectionné.</div>');
                return;
            }
            $.get(baseUrl + '/administration/user-roles-permissions/' + userId, function(html) {
                $('#rolesListContainer').html(html);
            });
        });
    });

     // Attribution/suppression de rôles à l'utilisateur (onglet Users / Roles)
        $('#rolesListContainer').on('change', '.user-role-checkbox', function() {
            var userId = $('#selectUser').val();
            var roleCode = $(this).data('role-code');
            var checked = $(this).is(':checked');
            var url = checked ? baseUrl + '/administration/user-roles/attach' : baseUrl + '/administration/user-roles/detach';
            $.post(url, {
                user_id: userId,
                role_code: roleCode,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function () {
                showAppModal('success', checked ? 'Rôle attribué.' : 'Rôle retiré.');
                // Recharge la liste pour mettre à jour les permissions héritées
                $.get(baseUrl + '/administration/user-roles-permissions/' + userId, function(html) {
                    $('#rolesListContainer').html(html);
                });
            })
            .fail(function () {
                showAppModal('error', 'Erreur lors de la mise à jour.');
            });
        });
    </script>
@endpush

@extends('layouts.app')

@section('page_title', 'Rôles & Permissions')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Rôles & Permissions')

@section('content')
<div class="container-fluid">

    {{-- FLASH --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="icon fas fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif

    {{-- MINI-DASHBOARD --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-shield-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Rôles</span>
                    <span class="info-box-number">{{ $stats['total_roles'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-key"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Permissions</span>
                    <span class="info-box-number">{{ $stats['total_permissions'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-link"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Liaisons Rôle→Perm.</span>
                    <span class="info-box-number">{{ $stats['total_liaisons'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users-cog"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Utilisateurs affectés</span>
                    <span class="info-box-number">{{ $stats['users_avec_role'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ONGLETS --}}
    <div class="card card-primary card-outline">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="rbacTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-roles">
                        <i class="fas fa-shield-alt mr-1"></i> Rôles
                        <span class="badge badge-primary badge-pill ml-1">{{ $stats['total_roles'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-permissions">
                        <i class="fas fa-key mr-1"></i> Permissions
                        <span class="badge badge-success badge-pill ml-1">{{ $stats['total_permissions'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-attribution">
                        <i class="fas fa-link mr-1"></i> Attribution Rôle→Perm.
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-users">
                        <i class="fas fa-users-cog mr-1"></i> Utilisateurs→Rôles
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
        <div class="tab-content" id="rbacTabsContent">

            {{-- ═══════════════════════ ONGLET RÔLES ═══════════════════════════ --}}
            <div class="tab-pane fade show active" id="tab-roles">
                <div class="row">
                    {{-- Formulaire ajout rôle --}}
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> Nouveau rôle</h3>
                            </div>
                            <div class="card-body">
                                <form id="addRoleForm">
                                    @csrf
                                    <div class="form-group">
                                        <label><i class="fas fa-tag mr-1 text-primary"></i> Nom du rôle <span class="text-danger">*</span></label>
                                        <input type="text" name="nom" class="form-control form-control-sm" placeholder="ex : Caissier, Directeur…" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-align-left mr-1 text-muted"></i> Description</label>
                                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Description du rôle…"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary" id="btnAddRole">
                                        <i class="fas fa-plus-circle mr-1"></i> Ajouter le rôle
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Tableau des rôles --}}
                    <div class="col-md-8">
                        <div class="card card-info card-outline">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h3 class="card-title mb-0"><i class="fas fa-list mr-2"></i> Liste des rôles</h3>
                                <span class="badge badge-info badge-pill">{{ $stats['total_roles'] }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="px-2 pt-2">
                                    <input type="text" id="searchRoles" class="form-control form-control-sm" placeholder="🔍 Rechercher un rôle…">
                                </div>
                                <div class="table-responsive mt-1" style="max-height:420px;overflow-y:auto">
                                    <table id="rolesTable" class="table table-sm rbac-table mb-0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width:35px">#</th>
                                                <th style="width:140px">Code</th>
                                                <th>Nom</th>
                                                <th>Description</th>
                                                <th class="text-center" style="width:80px">Perms.</th>
                                                <th class="text-center" style="width:65px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rolesTbody">
                                            @forelse($roles as $role)
                                            <tr>
                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                <td><code class="text-primary">{{ $role->code }}</code></td>
                                                <td><strong>{{ $role->nom }}</strong></td>
                                                <td class="text-muted small">{{ $role->description ?: '—' }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $role->permissions_count > 0 ? 'success' : 'secondary' }}">
                                                        {{ $role->permissions_count }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-xs btn-danger btn-delete-role"
                                                            data-id="{{ $role->code }}"
                                                            data-nom="{{ $role->nom }}"
                                                            title="Supprimer ce rôle">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucun rôle défini.
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
            </div>{{-- /tab-roles --}}

            {{-- ══════════════════════ ONGLET PERMISSIONS ══════════════════════ --}}
            <div class="tab-pane fade" id="tab-permissions">
                <div class="row">
                    {{-- Formulaire ajout permission --}}
                    <div class="col-md-4">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> Nouvelle permission</h3>
                            </div>
                            <div class="card-body">
                                <form id="addPermissionForm">
                                    @csrf
                                    <div class="form-group">
                                        <label><i class="fas fa-tag mr-1 text-success"></i> Nom <span class="text-danger">*</span></label>
                                        <input type="text" name="nom" class="form-control form-control-sm" placeholder="ex : VOIR_CAISSE, VALIDER_TX…" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-align-left mr-1 text-muted"></i> Description</label>
                                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Description…"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success" id="btnAddPerm">
                                        <i class="fas fa-plus-circle mr-1"></i> Ajouter la permission
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Tableau des permissions --}}
                    <div class="col-md-8">
                        <div class="card card-success card-outline">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h3 class="card-title mb-0"><i class="fas fa-list mr-2"></i> Liste des permissions</h3>
                                <span class="badge badge-success badge-pill">{{ $stats['total_permissions'] }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="px-2 pt-2">
                                    <input type="text" id="searchPermissions" class="form-control form-control-sm" placeholder="🔍 Rechercher une permission…">
                                </div>
                                <div class="table-responsive mt-1" style="max-height:420px;overflow-y:auto">
                                    <table id="permissionsTable" class="table table-sm rbac-table mb-0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width:35px">#</th>
                                                <th style="width:160px">Code</th>
                                                <th>Nom</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="permsTbody">
                                            @forelse($permissions as $perm)
                                            <tr>
                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                <td><code class="text-success">{{ $perm->code }}</code></td>
                                                <td><strong>{{ $perm->nom }}</strong></td>
                                                <td class="text-muted small">{{ $perm->description ?: '—' }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucune permission définie.
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
            </div>{{-- /tab-permissions --}}

            {{-- ═════════════════════ ONGLET ATTRIBUTION ════════════════════════ --}}
            <div class="tab-pane fade" id="tab-attribution">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-shield-alt mr-1"></i> Sélectionner un rôle</h3>
                            </div>
                            <div class="card-body">
                                <select id="selectRole" name="role_code" class="form-control form-control-sm select2" style="width:100%" required>
                                    <option value="">— Choisir un rôle —</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->code }}">[{{ $role->code }}] {{ $role->nom }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Cochez/décochez les permissions à associer au rôle sélectionné.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-key mr-1"></i> Permissions du rôle</h3>
                            </div>
                            <div class="card-body" id="permissionsListContainer">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-hand-point-left fa-2x mb-2 d-block"></i>
                                    Sélectionnez un rôle à gauche.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /tab-attribution --}}

            {{-- ══════════════════════ ONGLET USERS / RÔLES ════════════════════ --}}
            <div class="tab-pane fade" id="tab-users">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user mr-1"></i> Sélectionner un utilisateur</h3>
                            </div>
                            <div class="card-body">
                                <select id="selectUser" name="user_id" class="form-control form-control-sm select2" style="width:100%" required>
                                    <option value="">— Choisir un utilisateur —</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            @if($user->agent)
                                                [{{ $user->agent->matricule }}]
                                                {{ $user->agent->nom }} {{ $user->agent->postnom }}
                                            @else
                                                (Agent inconnu)
                                            @endif
                                             — {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Cochez/décochez les rôles à assigner à l'utilisateur.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-shield-alt mr-1"></i> Rôles de l'utilisateur</h3>
                            </div>
                            <div class="card-body" id="rolesListContainer">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-hand-point-left fa-2x mb-2 d-block"></i>
                                    Sélectionnez un utilisateur à gauche.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /tab-users --}}

        </div>{{-- /tab-content --}}
        </div>{{-- /card-body --}}
    </div>{{-- /card --}}

</div>{{-- /container-fluid --}}
@endsection

@push('css')
<style>
    /* ═══ RBAC Tables ═══ */
    .rbac-table thead th {
        background-color: #2c3136 !important;
        color: #c2c7d0 !important;
        border-color: #3d4349 !important;
        font-size: .8rem;
        white-space: nowrap;
        vertical-align: middle;
    }
    .rbac-table tbody tr:hover > td {
        background-color: rgba(0, 123, 255, 0.12) !important;
    }
    .rbac-table td {
        vertical-align: middle;
        font-size: .85rem;
    }
    /* ═══ Info-box consistency ═══ */
    .info-box { min-height: 72px; }
    .info-box-icon { line-height: 72px; width: 70px; font-size: 1.6rem; }
    .info-box-content { padding: 8px 10px; }
    /* ═══ Live-search highlight ═══ */
    .rbac-table tbody tr.d-none { display: none !important; }
    /* ═══ Select2 dark compatibility ═══ */
    .select2-container--bootstrap4 .select2-selection {
        font-size: .85rem;
    }
</style>
@endpush

@push('js')
<script>
(function () {
    'use strict';

    // ── CSRF global ────────────────────────────────────────────────────────────
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(function () {

        // Auto-close flash
        // (géré par showSystemMessage — pas besoin de timeout)

        // ── LIVE SEARCH : Rôles ──────────────────────────────────────────────
        $('#searchRoles').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#rolesTbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
            });
        });

        // ── LIVE SEARCH : Permissions ────────────────────────────────────────
        $('#searchPermissions').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#permsTbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
            });
        });

        // ── AJOUTER UN RÔLE ──────────────────────────────────────────────────
        $('#addRoleForm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnAddRole').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');
            $.post('{{ route("administration.roles_permissions.store") }}', $(this).serialize())
                .done(function () {
                    showSystemMessage('success', 'Rôle ajouté avec succès.');
                    setTimeout(function () { location.reload(); }, 900);
                })
                .fail(function (xhr) {
                    showSystemMessage('error', 'Erreur : ' + (xhr.responseJSON?.message ?? 'impossible d\'ajouter le rôle.'));
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter le rôle');
                });
        });

        // ── SUPPRIMER UN RÔLE ────────────────────────────────────────────────
        $(document).on('click', '.btn-delete-role', function () {
            var id  = $(this).data('id');
            var nom = $(this).data('nom');
            var url = '{{ route("administration.roles.destroy", ["role" => "__ID__"]) }}'.replace('__ID__', id);
            showUniversalConfirm('Supprimer le rôle <strong>« ' + nom + ' »</strong> ?<br><small class="text-danger">Toutes ses attributions seront perdues.</small>', function () {
                $.post(url, { _method: 'DELETE' })
                    .done(function () {
                        showSystemMessage('success', 'Rôle supprimé.');
                        setTimeout(function () { location.reload(); }, 900);
                    })
                    .fail(function (xhr) {
                        showSystemMessage('error', 'Erreur : ' + (xhr.responseJSON?.message ?? 'suppression impossible.'));
                    });
            }, 'Confirmer la suppression');
        });

        // ── AJOUTER UNE PERMISSION ────────────────────────────────────────────
        $('#addPermissionForm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnAddPerm').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');
            $.post('{{ route("administration.permissions.store") }}', $(this).serialize())
                .done(function () {
                    showSystemMessage('success', 'Permission ajoutée avec succès.');
                    setTimeout(function () { location.reload(); }, 900);
                })
                .fail(function (xhr) {
                    showSystemMessage('error', 'Erreur : ' + (xhr.responseJSON?.message ?? 'impossible d\'ajouter la permission.'));
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter la permission');
                });
        });

        // ── SELECT 2 ──────────────────────────────────────────────────────────
        var select2Opts = {
            theme: 'bootstrap4',
            allowClear: true,
            width: 'resolve',
            language: { noResults: function () { return 'Aucun résultat'; } }
        };
        $('#selectRole').select2($.extend({}, select2Opts, { placeholder: 'Rechercher un rôle…' }));
        $('#selectUser').select2($.extend({}, select2Opts, { placeholder: 'Rechercher un utilisateur…' }));

        // ── ATTRIBUTION : charger les permissions d'un rôle ───────────────────
        $('#selectRole').on('change', function () {
            var roleCode = $(this).val();
            if (!roleCode) {
                $('#permissionsListContainer').html(
                    '<div class="text-center text-muted py-4"><i class="fas fa-hand-point-left fa-2x mb-2 d-block"></i>Sélectionnez un rôle à gauche.</div>'
                );
                return;
            }
            $('#permissionsListContainer').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x text-info"></i></div>');
            $.get('{{ route("administration.role-permissions.list", ["role_code" => "__CODE__"]) }}'.replace('__CODE__', roleCode))
                .done(function (html) {
                    $('#permissionsListContainer').html(html);
                })
                .fail(function () {
                    $('#permissionsListContainer').html('<div class="alert alert-danger">Erreur de chargement.</div>');
                });
        });

        // ── ATTRIBUTION : cocher/décocher une permission ──────────────────────
        $(document).on('change', '#permissionsListContainer .perm-checkbox', function () {
            var roleCode = $('#selectRole').val();
            var permCode = $(this).data('perm-code');
            var checked  = $(this).is(':checked');
            var url      = checked
                           ? '{{ route("administration.role-permissions.attach") }}'
                           : '{{ route("administration.role-permissions.detach") }}';
            $.post(url, { role_code: roleCode, permission_code: permCode })
                .done(function () {
                    showSystemMessage('success', checked ? 'Permission attribuée.' : 'Permission retirée.');
                })
                .fail(function () {
                    showSystemMessage('error', 'Erreur de mise à jour.');
                });
        });

        // ── ATTRIBUTION : désactiver tout ─────────────────────────────────────
        $(document).on('change', '#disableAllPermissions', function () {
            var uncheck = $(this).is(':checked');
            $('#permissionsListContainer .perm-checkbox').prop('checked', !uncheck).trigger('change');
        });

        // ── USERS/RÔLES : charger les rôles d'un utilisateur ──────────────────
        $('#selectUser').on('change', function () {
            var userId = $(this).val();
            if (!userId) {
                $('#rolesListContainer').html(
                    '<div class="text-center text-muted py-4"><i class="fas fa-hand-point-left fa-2x mb-2 d-block"></i>Sélectionnez un utilisateur à gauche.</div>'
                );
                return;
            }
            $('#rolesListContainer').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x text-warning"></i></div>');
            $.get('{{ route("administration.user-roles-permissions", ["user_id" => "__ID__"]) }}'.replace('__ID__', userId))
                .done(function (html) {
                    $('#rolesListContainer').html(html);
                })
                .fail(function () {
                    $('#rolesListContainer').html('<div class="alert alert-danger">Erreur de chargement.</div>');
                });
        });

        // ── USERS/RÔLES : cocher/décocher un rôle ─────────────────────────────
        $(document).on('change', '#rolesListContainer .user-role-checkbox', function () {
            var userId   = $('#selectUser').val();
            var roleCode = $(this).data('role-code');
            var checked  = $(this).is(':checked');
            var url      = checked
                           ? '{{ route("administration.user-roles.attach") }}'
                           : '{{ route("administration.user-roles.detach") }}';
            $.post(url, { user_id: userId, role_code: roleCode })
                .done(function () {
                    showSystemMessage('success', checked ? 'Rôle attribué.' : 'Rôle retiré.');
                    $.get('{{ route("administration.user-roles-permissions", ["user_id" => "__ID__"]) }}'.replace('__ID__', userId), function (html) {
                        $('#rolesListContainer').html(html);
                    });
                })
                .fail(function () {
                    showSystemMessage('error', 'Erreur de mise à jour.');
                });
        });

    }); // /document.ready
}());
</script>
@endpush

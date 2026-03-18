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
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <input type="text" id="searchUsers" class="form-control form-control-sm flex-grow-1"
                                   placeholder="🔍 Rechercher un utilisateur…" style="min-width: 260px; max-width: 520px;">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="toggleGroupUsers" checked>
                                <label class="custom-control-label small font-weight-bold" for="toggleGroupUsers">
                                    Regrouper par service
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm mb-0" id="usersTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Agent</th>
                                    <th>Login</th>
                                    <th>Email</th>
                                    <th>Service / Poste</th>
                                    <th class="text-center" style="width:90px">État</th>
                                    <th class="text-center" style="width:110px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $i => $user)
                                @php
                                    $affActive = $user->agent?->affectations->first();
                                    $serviceNom = ($affActive && $affActive->poste) ? ($affActive->poste->service?->nom ?? null) : null;
                                    $posteNom = ($affActive && $affActive->poste) ? $affActive->poste->nom : null;
                                    $serviceDisplay = $serviceNom ?: 'Sans service';
                                    $serviceSortKey = strtolower(trim($serviceDisplay));
                                    $posteSortKey = strtolower(trim($posteNom ?? ''));
                                    $agentSortKey = strtolower(trim(($user->agent->nom ?? '') . ' ' . ($user->agent->postnom ?? '') . ' ' . ($user->agent->prenom ?? '') . ' ' . ($user->name ?? '')));
                                @endphp
                                <tr class="user-row" id="row-user-{{ $user->id }}"
                                    data-original-index="{{ $i }}"
                                    data-service-label="{{ $serviceSortKey }}"
                                    data-service-display="{{ $serviceDisplay }}"
                                    data-poste-label="{{ $posteSortKey }}"
                                    data-agent-label="{{ $agentSortKey }}">
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
                                    <td>
                                        @if($affActive && $affActive->poste)
                                            <span class="badge badge-info">{{ $serviceNom ?? '—' }}</span>
                                            / {{ $posteNom }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
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
                                <tr class="empty-row">
                                    <td colspan="7" class="text-center text-muted py-5">
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
    #usersTable tbody tr.user-row:hover > td {
        background-color: rgba(0, 123, 255, 0.13) !important;
        color: #fff !important;
    }

    #usersTable tbody tr.group-row td {
        background: rgba(23, 162, 184, 0.14);
        border-top: 1px solid rgba(23, 162, 184, 0.45);
        border-bottom: 1px solid rgba(23, 162, 184, 0.25);
        color: #8fd8e3;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
</style>
@endpush

@push('js')
<script>
(function () {
    'use strict';
    $.ajaxSetup({ headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept'      : 'application/json'
    } });

    $(function () {
        var $tbody = $('#usersTable tbody');

        function compareByData(a, b, key) {
            var av = String($(a).attr('data-' + key) || '');
            var bv = String($(b).attr('data-' + key) || '');
            if (av < bv) return -1;
            if (av > bv) return 1;
            return 0;
        }

        function reorderRows() {
            var rows = $tbody.find('tr.user-row').get();
            if (!rows.length) return;

            if ($('#toggleGroupUsers').is(':checked')) {
                rows.sort(function (a, b) {
                    var byService = compareByData(a, b, 'service-label');
                    if (byService !== 0) return byService;

                    var byPoste = compareByData(a, b, 'poste-label');
                    if (byPoste !== 0) return byPoste;

                    return compareByData(a, b, 'agent-label');
                });
            } else {
                rows.sort(function (a, b) {
                    return Number($(a).attr('data-original-index')) - Number($(b).attr('data-original-index'));
                });
            }

            $tbody.append(rows);
        }

        function applySearchFilter() {
            var q = $.trim($('#searchUsers').val() || '').toLowerCase();
            $tbody.find('tr.user-row').each(function () {
                var haystack = $(this).text().toLowerCase();
                $(this).toggle(q === '' || haystack.indexOf(q) !== -1);
            });
        }

        function renderGroupRows() {
            $tbody.find('tr.group-row').remove();
            if (!$('#toggleGroupUsers').is(':checked')) return;

            var $visibleRows = $tbody.find('tr.user-row:visible');
            var lastService = null;

            $visibleRows.each(function () {
                var $row = $(this);
                var serviceDisplay = String($row.attr('data-service-display') || 'Sans service');
                var serviceKey = String($row.attr('data-service-label') || 'sans service');

                if (serviceKey !== lastService) {
                    $('<tr class="group-row"><td colspan="7"><i class="fas fa-layer-group mr-1"></i>'
                        + serviceDisplay
                        + '</td></tr>').insertBefore($row);
                    lastService = serviceKey;
                }
            });
        }

        function renumberVisibleRows() {
            var i = 1;
            $tbody.find('tr.user-row:visible').each(function () {
                $(this).children('td').first().text(i++);
            });
        }

        function ensureEmptyState() {
            if ($tbody.find('tr.user-row').length > 0) {
                $tbody.find('tr.empty-row').remove();
                return;
            }

            if ($tbody.find('tr.empty-row').length === 0) {
                $tbody.append(
                    '<tr class="empty-row">'
                    + '<td colspan="7" class="text-center text-muted py-5">'
                    + '<i class="fas fa-inbox fa-2x mb-2 d-block"></i>'
                    + 'Aucun utilisateur enregistré.'
                    + '</td></tr>'
                );
            }
        }

        function refreshUsersTableView() {
            reorderRows();
            applySearchFilter();
            renderGroupRows();
            renumberVisibleRows();
            ensureEmptyState();
        }

        $('#searchUsers').on('input', function () {
            refreshUsersTableView();
        });

        $('#toggleGroupUsers').on('change', function () {
            refreshUsersTableView();
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
                    $.ajax({ url: url, type: 'POST', data: { _method: 'DELETE' }, dataType: 'json' })
                        .done(function (resp) {
                            if (resp.success) {
                                showSystemMessage('success', resp.message || 'Utilisateur supprimé.');
                                $('#row-user-' + id).fadeOut(400, function () {
                                    $(this).remove();
                                    refreshUsersTableView();
                                });
                            } else {
                                showSystemMessage('error', resp.message || 'Erreur.');
                            }
                        })
                        .fail(function (xhr) {
                            handleAjaxFail(xhr, 'Suppression utilisateur');
                        });
                },
                'Confirmer la suppression'
            );
        });

        refreshUsersTableView();
    });
}());
</script>
@endpush

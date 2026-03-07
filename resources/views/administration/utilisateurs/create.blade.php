
@extends('layouts.app')

@section('page_title', 'Ajout utilisateur')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Ajouter un utilisateur')

@section('content')
<div class="container-fluid">

    {{-- ===== Ligne principale : Agents | Formulaire ===== --}}
    <div class="row">

        {{-- ======== Colonne Agents (gauche) ======== --}}
        <div class="col-lg-5 col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <h3 class="card-title mb-0"><i class="fas fa-users mr-2"></i>Liste des agents</h3>
                    <div class="input-group input-group-sm mt-1 mt-md-0" style="width:200px;">
                        <input type="text" id="searchAgents" class="form-control" placeholder="Rechercher...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:420px;overflow-y:auto;">
                        <table id="agentsTable" class="table table-bordered table-striped table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Nom complet</th>
                                    <th>Service / Poste</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agents as $agent)
                                    @php
                                        $affectation = \App\Models\Affectation::with(['poste.service'])
                                            ->where('agent_matricule', $agent->matricule)
                                            ->orderByDesc('date_debut')
                                            ->first();
                                    @endphp
                                    <tr data-agent-matricule="{{ $agent->matricule }}"
                                        style="cursor:pointer;"
                                        @if(isset($selectedAgent) && $selectedAgent->matricule == $agent->matricule) class="table-primary" @endif>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <i class="fas fa-user-tie mr-1 text-muted"></i>
                                            {{ $agent->nom }} {{ $agent->postnom }} {{ $agent->prenom }}
                                        </td>
                                        <td>
                                            @if($affectation && $affectation->poste && $affectation->poste->service)
                                                <span class="badge badge-info">{{ $affectation->poste->service->nom }}</span>
                                                <small class="text-muted">/ {{ $affectation->poste->nom }}</small>
                                            @elseif($affectation && $affectation->poste)
                                                <span class="badge badge-secondary">{{ $affectation->poste->nom }}</span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            <i class="fas fa-inbox mr-1"></i> Aucun agent enregistré.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-muted small">
                    <i class="fas fa-hand-pointer mr-1"></i> Cliquez sur un agent pour créer son compte.
                </div>
            </div>
        </div>

        {{-- ======== Colonne Formulaire (droite) ======== --}}
        <div class="col-lg-7 col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer un compte utilisateur
                    </h3>
                </div>
                <div class="card-body">

                    {{-- Alerte d'invite --}}
                    <div id="agentSelectAlert" class="alert alert-info">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Sélectionnez un agent dans la liste pour créer son compte utilisateur.
                    </div>

                    {{-- Badge agent sélectionné --}}
                    <div id="agentInfoBadge" class="alert alert-success d-none">
                        <i class="fas fa-user-check mr-2"></i>
                        Agent sélectionné : <strong id="agentInfoNom"></strong>
                    </div>

                    {{-- Formulaire (caché par défaut) --}}
                    <form id="userCreateForm" method="POST" action="{{ route('administration.utilisateurs.store') }}" style="display:none;">
                        @csrf
                        <input type="hidden" name="agent_matricule" id="agentMatricule">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="login"><i class="fas fa-user mr-1"></i> Login</label>
                                    <input type="text" class="form-control" id="login" name="login" required
                                           placeholder="Nom d'utilisateur">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email"><i class="fas fa-envelope mr-1"></i> Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                           placeholder="adresse@exemple.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password"><i class="fas fa-lock mr-1"></i> Mot de passe</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                               required placeholder="••••••••">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-pwd" type="button"
                                                    data-target="#password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small id="passwordHelp" class="form-text mt-1"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation"><i class="fas fa-lock mr-1"></i> Confirmer</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation"
                                               name="password_confirmation" required placeholder="••••••••">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-pwd" type="button"
                                                    data-target="#password_confirmation">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small id="passwordConfirmHelp" class="form-text mt-1"></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="etat"><i class="fas fa-toggle-on mr-1"></i> État du compte</label>
                            <select class="form-control" id="etat" name="etat" required>
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-save mr-1"></i> Créer le compte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Tableau des comptes utilisateurs ===== --}}
    <div class="row" id="userAccountsRow">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list mr-2"></i>Comptes utilisateurs existants
                    </h3>
                    <button class="btn btn-sm btn-info" id="refreshUsersTable">
                        <i class="fas fa-sync-alt mr-1"></i> Actualiser
                    </button>
                </div>
                @include('administration.utilisateurs.tableau_compte')
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    #agentsTable tbody tr:hover:not(.agent-selected) {
        background: rgba(23, 162, 184, 0.12) !important;
        cursor: pointer;
    }
    #agentsTable tbody tr.agent-selected {
        background: linear-gradient(90deg, rgba(0,123,255,0.18) 0%, rgba(0,63,128,0.12) 100%) !important;
        border-left: 3px solid #007bff;
    }
</style>
@endpush

@push('js')
<script>
(function () {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept'      : 'application/json'
        }
    });

    $(function () {

        /* ---- Recherche live agents ---- */
        $('#searchAgents').on('input', function () {
            var q = $(this).val().toLowerCase().trim();
            $('#agentsTable tbody tr').each(function () {
                $(this).toggle(!q || $(this).text().toLowerCase().indexOf(q) !== -1);
            });
        });

        /* ---- Afficher/masquer mot de passe ---- */
        $(document).on('click', '.toggle-pwd', function () {
            var target = $(this).data('target');
            var input  = $(target);
            var icon   = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        /* ---- Sélection agent ---- */
        $('#agentsTable tbody').on('click', 'tr[data-agent-matricule]', function () {
            var agentMatricule = $(this).data('agent-matricule');
            if (!agentMatricule) return;

            $('#agentsTable tbody tr').removeClass('agent-selected');
            $(this).addClass('agent-selected');

            var url = '{{ route("administration.utilisateurs.agentInfo", ["matricule" => "__MAT__"]) }}'
                        .replace('__MAT__', agentMatricule);

            $.ajax({
                url     : url,
                type    : 'GET',
                dataType: 'json'
            })
            .done(function (agent) {
                $('#agentMatricule').val(agent.matricule);
                $('#email').val(agent.email || '');
                $('#agentInfoNom').text(agent.nom + ' ' + (agent.postnom || '') + ' ' + (agent.prenom || ''));
                $('#agentInfoBadge').removeClass('d-none');
                $('#agentSelectAlert').hide();
                $('#userCreateForm').show();
                $('#userAccountsRow').show();
            })
            .fail(function (xhr) {
                if (xhr.status === 200) {
                    try {
                        var d = JSON.parse(xhr.responseText.replace(/^\uFEFF/, '').trim());
                        if (d && d.matricule) {
                            $('#agentMatricule').val(d.matricule);
                            $('#email').val(d.email || '');
                            $('#agentInfoNom').text(d.nom + ' ' + (d.postnom || '') + ' ' + (d.prenom || ''));
                            $('#agentInfoBadge').removeClass('d-none');
                            $('#agentSelectAlert').hide();
                            $('#userCreateForm').show();
                            $('#userAccountsRow').show();
                            return;
                        }
                    } catch(e) { /* NOOP */ }
                }
                $('#agentSelectAlert').show()
                    .html('<i class="fas fa-exclamation-triangle mr-2"></i>Erreur lors du chargement de l\'agent.');
                $('#userCreateForm').hide();
            });
        });

        /* ---- Force du mot de passe ---- */
        function checkPasswordStrength(val) {
            var errors = [];
            if (val.length < 8)            errors.push('8 caractères min.');
            if (!/[a-z]/.test(val))        errors.push('une minuscule');
            if (!/[A-Z]/.test(val))        errors.push('une majuscule');
            if (!/[0-9]/.test(val))        errors.push('un chiffre');
            if (!/[^A-Za-z0-9]/.test(val)) errors.push('un caractère spécial');
            return errors;
        }

        $('#password').on('input', function () {
            var errors = checkPasswordStrength($(this).val());
            var $help  = $('#passwordHelp');
            if (errors.length === 0) {
                $help.html('<span class="text-success"><i class="fas fa-check-circle mr-1"></i>Mot de passe fort.</span>');
            } else {
                $help.html('<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Requis : ' + errors.join(', ') + '.</span>');
            }
            $('#password_confirmation').trigger('input');
        });

        $('#password_confirmation').on('input', function () {
            var $help = $('#passwordConfirmHelp');
            var conf  = $(this).val();
            if (!conf.length) { $help.text(''); return; }
            if (conf === $('#password').val()) {
                $help.html('<span class="text-success"><i class="fas fa-check-circle mr-1"></i>Les mots de passe correspondent.</span>');
            } else {
                $help.html('<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Les mots de passe ne correspondent pas.</span>');
            }
        });

        /* ---- Soumission AJAX ---- */
        $('#userCreateForm').on('submit', function (e) {
            e.preventDefault();
            var pass  = $('#password').val();
            var conf  = $('#password_confirmation').val();
            var errors = checkPasswordStrength(pass);
            if (errors.length) {
                $('#password').focus();
                return;
            }
            if (pass !== conf) {
                $('#password_confirmation').focus();
                return;
            }
            $.ajax({
                url     : $(this).attr('action'),
                type    : 'POST',
                data    : $(this).serialize(),
                dataType: 'json'
            })
            .done(function (response) {
                if (response.success) {
                    showSystemMessage('success', response.message || 'Utilisateur créé avec succès.');
                    $('#userCreateForm')[0].reset();
                    $('#passwordHelp, #passwordConfirmHelp').text('');
                    $('#systemMessageModal').one('hidden.bs.modal', function () {
                        $('#refreshUsersTable').trigger('click');
                    });
                } else {
                    showSystemMessage('error', response.message || 'Erreur.');
                }
            })
            .fail(function (xhr) {
                if (xhr.status === 200) {
                    try {
                        var d = JSON.parse(xhr.responseText.replace(/^\uFEFF/, '').trim());
                        if (d && d.success) {
                            showSystemMessage('success', d.message || 'Utilisateur créé avec succès.');
                            $('#userCreateForm')[0].reset();
                            $('#passwordHelp, #passwordConfirmHelp').text('');
                            $('#systemMessageModal').one('hidden.bs.modal', function () {
                                $('#refreshUsersTable').trigger('click');
                            });
                            return;
                        }
                        showSystemMessage('error', d.message || 'Erreur.');
                        return;
                    } catch(e) { /* NOOP */ }
                }
                var msg = 'Erreur lors de la création du compte.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showSystemMessage('error', msg);
            });
        });

    });
}());
</script>
@endpush

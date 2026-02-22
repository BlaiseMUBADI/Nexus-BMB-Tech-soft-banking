
@extends('layouts.app')

@section('page_title', 'Ajout utilisateur')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Ajouter un utilisateur')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header pb-0">
                <h5>Agents</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 350px; min-height: 200px; overflow-y: auto;">
                    <table id="agentsTable" class="table table-bordered table-striped mb-0 agents-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
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
                                <tr data-agent-matricule="{{ $agent->matricule }}" @if(isset($selectedAgent) && $selectedAgent->matricule == $agent->matricule) class="datatable-selected-row" @endif>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $agent->nom }} {{ $agent->postnom }} {{ $agent->prenom }}</td>
                                    <td>
                                        @if($affectation && $affectation->poste && $affectation->poste->service)
                                            <span class="badge badge-info">{{ $affectation->poste->service->nom }} / {{ $affectation->poste->nom }}</span>
                                        @elseif($affectation && $affectation->poste)
                                            <span class="badge badge-info">- / {{ $affectation->poste->nom }}</span>
                                        @else
                                            <span class="text-muted">Aucune affectation</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucun agent enregistré.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header pb-0">
                <h5>Créer un compte utilisateur</h5>
            </div>
            <div class="card-body">
                <div id="agentFormContainer">
                    <form id="userCreateForm" method="POST" action="{{ route('administration.utilisateurs.store') }}" style="display:none">
                        @csrf
                        <input type="hidden" name="agent_matricule" id="agentMatricule">
                        <div class="form-group">
                            <label for="login">Login</label>
                            <input type="text" class="form-control" id="login" name="login" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small id="passwordHelp" class="form-text" style="display:block;"></small>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <small id="passwordConfirmHelp" class="form-text" style="display:block;"></small>
                        </div>
                        <div class="form-group">
                            <label for="etat">État</label>
                            <select class="form-control" id="etat" name="etat" required>
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Enregistrer</button>
                    </form>
                    <div id="agentSelectAlert" class="alert alert-info">Sélectionnez un agent à gauche pour créer un compte utilisateur.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6" id="userAccountsCol" style="display:none;">
        <div class="card">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Comptes utilisateurs</h5>
                <button class="btn btn-sm btn-primary" id="refreshUsersTable"><i class="fas fa-sync-alt"></i> Actualiser</button>
            </div>
            
            @include('administration.utilisateurs.tableau_compte')
            
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    #agentsTable tbody tr.datatable-selected-row {
        background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
        color: #fff !important;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
        transition: background 0.3s, color 0.3s;
    }
    #agentsTable tbody tr.datatable-hover-row:not(.datatable-selected-row) {
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
        // Sélection agent
        $('#agentsTable tbody').on('click', 'tr', function () {
            $('#agentsTable tbody tr').removeClass('datatable-selected-row');
            $(this).addClass('datatable-selected-row');
            var agentMatricule = $(this).data('agent-matricule');
            if(agentMatricule) {
                var url = baseUrl + '/administration/utilisateurs/agent-info/' + agentMatricule;
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(agent) {
                        $('#agentMatricule').val(agent.matricule);
                        $('#email').val(agent.email || '');
                        $('#agentInfo').show();
                        $('#userCreateForm').show();
                        $('#agentSelectAlert').hide();
                        // Affiche le tableau des comptes
                        $('#userAccountsCol').show();
                    },
                    error: function() {
                        $('#agentInfo').hide();
                        $('#userCreateForm').hide();
                        $('#agentSelectAlert').show().text('Erreur lors du chargement des informations de l\'agent.');
                        // Masque le tableau des comptes
                        $('#userAccountsCol').hide();
                    }
                });
            }
        });
        // Survol
        $('#agentsTable tbody').on('mouseenter', 'tr', function () {
            if (!$(this).hasClass('datatable-selected-row')) {
                $(this).addClass('datatable-hover-row');
            }
        });
        $('#agentsTable tbody').on('mouseleave', 'tr', function () {
            $(this).removeClass('datatable-hover-row');
        });
        // Hide form and accounts table initially
        $('#agentInfo').hide();
        $('#userCreateForm').hide();
        $('#agentSelectAlert').show();
        $('#userAccountsCol').hide();

        // Validation temps réel mot de passe
        function checkPasswordStrength(val) {
            var regex = {
                lowercase: /[a-z]/,
                uppercase: /[A-Z]/,
                digit: /[0-9]/,
                special: /[^A-Za-z0-9]/
            };
            var errors = [];
            if(val.length < 8) errors.push('au moins 8 caractères');
            if(!regex.lowercase.test(val)) errors.push('une minuscule');
            if(!regex.uppercase.test(val)) errors.push('une majuscule');
            if(!regex.digit.test(val)) errors.push('un chiffre');
            if(!regex.special.test(val)) errors.push('un caractère spécial');
            return errors;
        }
        $('#password').on('input', function() {
            var val = $(this).val();
            var errors = checkPasswordStrength(val);
            if(errors.length === 0) {
                $('#passwordHelp').text('Mot de passe fort.').css({color:'green',fontWeight:'bold',background:'#e6ffe6',padding:'4px 8px',borderRadius:'4px'});
            } else {
                $('#passwordHelp').text('Le mot de passe doit contenir : ' + errors.join(', ') + '.')
                    .css({color:'#c00',fontWeight:'bold',background:'#ffeaea',padding:'4px 8px',borderRadius:'4px'});
            }
            // Vérifie la confirmation aussi
            $('#password_confirmation').trigger('input');
        });
        $('#password_confirmation').on('input', function() {
            var pass = $('#password').val();
            var conf = $(this).val();
            if(conf.length === 0) {
                $('#passwordConfirmHelp').text('');
            } else if(pass === conf && conf.length >= 8) {
                $('#passwordConfirmHelp').text('Les mots de passe correspondent.')
                    .css({color:'green',fontWeight:'bold',background:'#e6ffe6',padding:'4px 8px',borderRadius:'4px'});
            } else {
                $('#passwordConfirmHelp').text('Les mots de passe ne correspondent pas.')
                    .css({color:'#c00',fontWeight:'bold',background:'#ffeaea',padding:'4px 8px',borderRadius:'4px'});
            }
        });
        // Soumission AJAX du formulaire utilisateur
        $('#userCreateForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();
            // Validation JS avant envoi
            var pass = $('#password').val();
            var conf = $('#password_confirmation').val();
            var errors = checkPasswordStrength(pass);
            if(errors.length > 0) {
                $('#passwordHelp').text('Le mot de passe doit contenir : ' + errors.join(', ') + '.').css('color','red');
                $('#password').focus();
                return false;
            }
            if(pass !== conf) {
                $('#passwordConfirmHelp').text('Les mots de passe ne correspondent pas.').css('color','red');
                $('#password_confirmation').focus();
                return false;
            }
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    showSystemMessage('success', 'Utilisateur créé avec succès.');
                    // Ne pas réinitialiser l'agent sélectionné
                    form[0].reset();
                    $('#passwordHelp').text('');
                    $('#passwordConfirmHelp').text('');
                    // Déclenche le bouton d'actualisation après fermeture du modal succès
                    $('#systemMessageModal').one('hidden.bs.modal', function() {
                        $('#refreshUsersTable').trigger('click');
                    });
                },
                error: function(xhr) {
                    let msg = 'Erreur lors de la création du compte.';
                    if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    showSystemMessage('error', msg);
                }
            });
        });
    });
</script>
@endpush

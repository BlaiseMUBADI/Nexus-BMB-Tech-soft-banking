@extends('layouts.app')

@section('page_title', 'Éditer utilisateur')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Éditer un utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 col-md-10">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-edit mr-2"></i>
                        Modifier le compte utilisateur
                    </h3>
                </div>
                <div class="card-body">
                    <form id="userEditForm" method="POST" action="{{ route('administration.utilisateurs.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Infos Agent --}}
                        <div class="form-group">
                            <label for="agent_info"><i class="fas fa-user-tie mr-1"></i> Agent associé</label>
                            <div class="form-control-plaintext border rounded p-2 bg-light">
                                @if($user->agent)
                                    <strong>{{ $user->agent->nom }} {{ $user->agent->postnom }} {{ $user->agent->prenom }}</strong>
                                    <small class="text-muted d-block">Matricule: {{ $user->agent->matricule }}</small>
                                @else
                                    <span class="text-muted">Aucun agent associé</span>
                                @endif
                            </div>
                        </div>

                        {{-- Login et Email --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="login"><i class="fas fa-user mr-1"></i> Login</label>
                                    <input type="text" class="form-control @error('login') is-invalid @enderror" 
                                           id="login" name="login" value="{{ old('login', $user->name) }}" 
                                           required placeholder="Nom d'utilisateur">
                                    @error('login')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email"><i class="fas fa-envelope mr-1"></i> Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" 
                                           placeholder="adresse@exemple.com">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- État du compte --}}
                        <div class="form-group">
                            <label for="etat"><i class="fas fa-toggle-on mr-1"></i> État du compte</label>
                            <select class="form-control @error('etat') is-invalid @enderror" 
                                    id="etat" name="etat" required>
                                <option value="actif" {{ old('etat', $user->etat) === 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="inactif" {{ old('etat', $user->etat) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('etat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Section Réinitialiser Mot de Passe --}}
                        <hr class="my-3">
                        <h5 class="mb-3">
                            <i class="fas fa-key mr-2 text-warning"></i> Réinitialiser le mot de passe
                        </h5>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Laissez vide si vous ne souhaitez pas modifier le mot de passe.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password"><i class="fas fa-lock mr-1"></i> Nouveau mot de passe</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password" placeholder="••••••••">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-pwd" type="button"
                                                    data-target="#password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 6 caractères</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation"><i class="fas fa-lock mr-1"></i> Confirmer</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation" placeholder="••••••••">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-pwd" type="button"
                                                    data-target="#password_confirmation">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('administration.utilisateurs.liste') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Retour
                                </a>
                                <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
                                    <i class="fas fa-save mr-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Infos supplémentaires --}}
        <div class="col-lg-4 col-md-10">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-circle-info mr-1"></i> Informations
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="d-block"><i class="fas fa-calendar mr-1 text-muted"></i> Créé le :</strong>
                        <small class="text-muted">{{ $user->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</small>
                    </div>
                    <div class="mb-3">
                        <strong class="d-block"><i class="fas fa-clock mr-1 text-muted"></i> Modifié le :</strong>
                        <small class="text-muted">{{ $user->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</small>
                    </div>
                    <hr>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Note :</strong> Un mot de passe oublié peut être réinitialisé ici. Assurez-vous de communiquer le nouveau mot de passe à l'utilisateur de manière sécurisée.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
        // ---- Stocker les valeurs initiales ----
        var initialValues = {
            login: $('#login').val(),
            email: $('#email').val(),
            etat: $('#etat').val(),
            password: $('#password').val(),
            password_confirmation: $('#password_confirmation').val()
        };

        // ---- Fonction pour vérifier les changements ----
        function checkForChanges() {
            var currentValues = {
                login: $('#login').val(),
                email: $('#email').val(),
                etat: $('#etat').val(),
                password: $('#password').val(),
                password_confirmation: $('#password_confirmation').val()
            };

            // Vérifier s'il y a une différence
            var hasChanges = JSON.stringify(initialValues) !== JSON.stringify(currentValues);

            // Activer ou désactiver le bouton
            $('#submitBtn').prop('disabled', !hasChanges);
        }

        // ---- Détecter les changements sur tous les champs ----
        $('#userEditForm').on('input change', 'input, select', function () {
            checkForChanges();
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

        /* ---- Validation du formulaire ---- */
        $('#userEditForm').on('submit', function (e) {
            var password = $('#password').val();
            var passwordConfirm = $('#password_confirmation').val();

            // Si un mot de passe est entré, valider la confirmation
            if (password && password !== passwordConfirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }

            // Si le mot de passe est trop court
            if (password && password.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au minimum 6 caractères.');
                return false;
            }
        });
    });
})();
</script>
@endpush

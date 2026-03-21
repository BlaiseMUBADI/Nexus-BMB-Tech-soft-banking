@extends('layouts.app')

@section('page_title', 'Détail utilisateur')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Détail utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user mr-2"></i>
                        Informations du compte
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th style="width: 170px;">ID utilisateur</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Login</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>État</th>
                                    <td>
                                        @if($user->etat === 'actif')
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-secondary">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th style="width: 170px;">Matricule agent</th>
                                    <td>{{ $user->agent?->matricule ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Agent lié</th>
                                    <td>
                                        @if($user->agent)
                                            {{ trim(($user->agent->nom ?? '') . ' ' . ($user->agent->postnom ?? '') . ' ' . ($user->agent->prenom ?? '')) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Créé le</th>
                                    <td>{{ $user->created_at?->format('d/m/Y H:i') ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Mis à jour le</th>
                                    <td>{{ $user->updated_at?->format('d/m/Y H:i') ?: '—' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Rôles
                    </h3>
                </div>
                <div class="card-body">
                    @php($roles = $user->getRoleCodes())

                    @if(count($roles))
                        @foreach($roles as $roleCode)
                            <span class="badge badge-info mr-1 mb-1">{{ $roleCode }}</span>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Aucun rôle assigné.</p>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between mt-2">
                <a href="{{ route('administration.utilisateurs.liste') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
                <a href="{{ route('administration.utilisateurs.edit', $user->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-1"></i> Modifier
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
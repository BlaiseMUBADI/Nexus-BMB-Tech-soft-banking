@extends('layouts.app')

@section('page_title', 'Mon Profil')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Mon Profil')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mt-2">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <div class="mb-3">
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('dist/img/user2-160x160.jpg') }}"
                             alt="Photo de profil" style="width:120px;height:120px;object-fit:cover;">
                    </div>
                    <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
                    <p class="text-muted text-center">{{ Auth::user()->email }}</p>
                    <p class="text-muted text-center">{{ Auth::user()->telephone ?? '' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Modifier mes informations</h3>
                </div>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        ...existing code...
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <div class="card card-info mb-4">
                <div class="card-header bg-info">
                    <h5 class="card-title mb-0"><i class="fas fa-briefcase mr-2"></i> Affectations, Poste & Service</h5>
                </div>
                <div class="card-body">
                    @if($agent)
                        <p><strong>Matricule agent :</strong> {{ $agent->matricule }}</p>
                        <p><strong>Poste actuel :</strong> {{ $poste ? $poste->nom : 'N/A' }}</p>
                        <p><strong>Service :</strong> {{ $service ? $service->nom : 'N/A' }}</p>
                        <hr>
                        <h6 class="mb-2"><i class="fas fa-history mr-1"></i> Historique des affectations</h6>
                        <ul class="list-group">
                            @forelse($affectations as $aff)
                                <li class="list-group-item">
                                    <strong>{{ $aff->poste->nom ?? 'Poste inconnu' }}</strong> -
                                    <span class="text-muted">Service : {{ $aff->poste->service->nom ?? 'N/A' }}</span><br>
                                    <span>Date début : {{ $aff->date_debut }}</span>
                                    @if($aff->date_fin)
                                        <span> | Date fin : {{ $aff->date_fin }}</span>
                                    @endif
                                    <span class="badge badge-{{ $aff->Etat === 'Actif' ? 'success' : 'secondary' }} ml-2">{{ $aff->Etat }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">Aucune affectation trouvée.</li>
                            @endforelse
                        </ul>
                    @else
                        <p class="text-muted">Aucune information d'agent disponible.</p>
                    @endif
                </div>
            </div>

            <div class="card card-warning mb-4">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0"><i class="fas fa-user-shield mr-2"></i> Rôles & Permissions</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-2"><i class="fas fa-user-tag mr-1"></i> Rôles attribués</h6>
                    <ul class="list-group mb-3">
                        @forelse($roles as $role)
                            @if($userRoles->contains($role->code))
                                <li class="list-group-item">{{ $role->nom }}</li>
                            @endif
                        @empty
                            <li class="list-group-item">Aucun rôle attribué.</li>
                        @endforelse
                    </ul>
                    <h6 class="mb-2"><i class="fas fa-key mr-1"></i> Permissions héritées</h6>
                    <ul class="list-group">
                        @forelse($permissions as $perm)
                            @if($userPermissions->contains($perm->code))
                                <li class="list-group-item">{{ $perm->nom }}</li>
                            @endif
                        @empty
                            <li class="list-group-item">Aucune permission héritée.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('page_title', 'Modifier l\'agent')
@section('breadcrumb_parent', 'Ressources Humaines')
@section('breadcrumb', 'Modification de l\'agent')

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
        <div class="col-md-10">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Modifier l'agent : <span class="fw-bold">{{ $agent->matricule }}</span></h3>
                </div>
                <form method="POST" action="{{ route('agents.update', $agent->matricule) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="nom">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $agent->nom) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="postnom">Postnom</label>
                                <input type="text" class="form-control" id="postnom" name="postnom" value="{{ old('postnom', $agent->postnom) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="prenom">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom', $agent->prenom) }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Sexe</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sexe" id="sexeM" value="M" @if(old('sexe', $agent->sexe)==='M') checked @endif required>
                                    <label class="form-check-label" for="sexeM">Masculin</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sexe" id="sexeF" value="F" @if(old('sexe', $agent->sexe)==='F') checked @endif required>
                                    <label class="form-check-label" for="sexeF">Féminin</label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="date_naissance">Date de naissance</label>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $agent->date_naissance) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telephone">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone', $agent->telephone) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $agent->email) }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="adresse">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse', $agent->adresse) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="date_embauche">Date d'embauche</label>
                                <input type="date" class="form-control" id="date_embauche" name="date_embauche" value="{{ old('date_embauche', $agent->date_embauche) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="statut">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="actif" @if(old('statut', $agent->statut)==='actif') selected @endif>Actif</option>
                                    <option value="inactif" @if(old('statut', $agent->statut)==='inactif') selected @endif>Inactif</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="photo">Photo</label>
                                <input type="file" class="form-control-file" id="photo" name="photo" accept="image/*">
                                @if($agent->photo)
                                    <div class="mt-2">
                                        <img src="{{ route('agents.photo', basename($agent->photo)) }}" alt="Photo actuelle" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        <a href="{{ route('agents.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

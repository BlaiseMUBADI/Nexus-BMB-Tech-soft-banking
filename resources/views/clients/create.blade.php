@extends('layouts.app')

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
                    <h3 class="card-title">Ajouter un client</h3>
                </div>
                <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <!-- Identité -->
                        <div class="card card-info mb-4">
                            <div class="card-header bg-info">
                                <h5 class="card-title mb-0"><i class="fas fa-user mr-2"></i>1. Informations d'identité</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="nom">Nom</label>
                                        <input type="text" class="form-control" id="nom" name="nom" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="postnom">Postnom</label>
                                        <input type="text" class="form-control" id="postnom" name="postnom" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="prenom">Prénom</label>
                                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="sexe">Sexe</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                            </div>
                                            <select class="form-control" id="sexe" name="sexe" required>
                                                <option value="">Choisir...</option>
                                                <option value="M">Masculin</option>
                                                <option value="F">Féminin</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="date_naissance">Date de naissance</label>
                                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="lieu_naissance">Lieu de naissance</label>
                                        <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="adresse">Adresse</label>
                                        <input type="text" class="form-control" id="adresse" name="adresse" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Etat civil -->
                        <div class="card card-secondary mb-4">
                            <div class="card-header bg-secondary">
                                <h5 class="card-title mb-0"><i class="fas fa-ring mr-2"></i>2. État civil</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="etat_civil">État civil</label>
                                        <input type="text" class="form-control" id="etat_civil" name="etat_civil" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="nom_conjoint">Nom du conjoint</label>
                                        <input type="text" class="form-control" id="nom_conjoint" name="nom_conjoint">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="zone">Zone</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            </div>
                                            <select class="form-control" id="zone" name="zone" required>
                                                <option value="">Choisir...</option>
                                                <option value="Urbain">Urbain</option>
                                                <option value="Rural">Rural</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pièce d'identité -->
                        <div class="card card-warning mb-4">
                            <div class="card-header bg-warning">
                                <h5 class="card-title mb-0"><i class="fas fa-id-card mr-2"></i>3. Pièce d'identité</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="type_piece_identite">Type de pièce</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            </div>
                                            <select class="form-control" id="type_piece_identite" name="type_piece_identite" required>
                                                <option value="">Choisir...</option>
                                                <option value="Carte nationale d'identité">Carte nationale d'identité</option>
                                                <option value="Permis de conduire">Permis de conduire</option>
                                                <option value="Passeport">Passeport</option>
                                                <option value="Carte d'électeur">Carte d'électeur</option>
                                                <option value="Autre">Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="numero_piece_identite">Numéro</label>
                                        <input type="text" class="form-control" id="numero_piece_identite" name="numero_piece_identite" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="lieu_delivrance_piece">Lieu de délivrance</label>
                                        <input type="text" class="form-control" id="lieu_delivrance_piece" name="lieu_delivrance_piece" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="date_delivrance_piece">Date de délivrance</label>
                                        <input type="date" class="form-control" id="date_delivrance_piece" name="date_delivrance_piece" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Photo -->
                        <div class="card card-success mb-4">
                            <div class="card-header bg-success">
                                <h5 class="card-title mb-0"><i class="fas fa-camera mr-2"></i>4. Photo</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="photo">Photo du client</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="photo" name="photo" accept="image/*">
                                                <label class="custom-file-label" for="photo">Choisir une photo</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Activité économique -->
                        <div class="card card-primary mb-4">
                            <div class="card-header bg-primary">
                                <h5 class="card-title mb-0"><i class="fas fa-briefcase mr-2"></i>5. Activité économique</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="secteur_activite">Secteur d’activité</label>
                                        <input type="text" class="form-control" id="secteur_activite" name="secteur_activite">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="type_activite">Type d’activité</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-industry"></i></span>
                                            </div>
                                            <select class="form-control" id="type_activite" name="type_activite">
                                                <option value="">Choisir...</option>
                                                <option value="Commerce">Commerce</option>
                                                <option value="Agriculture">Agriculture</option>
                                                <option value="Artisanat">Artisanat</option>
                                                <option value="Service">Service</option>
                                                <option value="Autre">Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="statut_entreprise">Statut de l’entreprise</label>
                                        <input type="text" class="form-control" id="statut_entreprise" name="statut_entreprise">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="nom_entreprise">Nom de l’entreprise</label>
                                        <input type="text" class="form-control" id="nom_entreprise" name="nom_entreprise">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="adresse_entreprise">Adresse de l’entreprise</label>
                                        <input type="text" class="form-control" id="adresse_entreprise" name="adresse_entreprise">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="telephone_entreprise">Téléphone de l’entreprise</label>
                                        <input type="text" class="form-control" id="telephone_entreprise" name="telephone_entreprise">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="nombre_annees_experience">Nombre d’années d’expérience</label>
                                        <input type="number" min="0" class="form-control" id="nombre_annees_experience" name="nombre_annees_experience">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="revenu_mensuel">Revenu mensuel (FCFA)</label>
                                        <input type="number" min="0" step="0.01" class="form-control" id="revenu_mensuel" name="revenu_mensuel">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="autres_details_activite">Autres détails</label>
                                        <input type="text" class="form-control" id="autres_details_activite" name="autres_details_activite">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

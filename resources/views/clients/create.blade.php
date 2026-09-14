@extends('layouts.app')

@section('page_title', 'Ajout client')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Ajouter un client')

@section('content')
   <div class="container-fluid ">

      @if(session('success'))
         @push('js')
            <script>$(function () { showSystemMessage('success', '{{ addslashes(session("success")) }}'); });</script>
         @endpush
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
                  <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Ajouter un client</h3>
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
                              <div class="form-group col-md-3">
                                 <label for="nom">Nom</label>
                                 <input type="text" class="form-control" id="nom" name="nom" required>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="postnom">Postnom</label>
                                 <input type="text" class="form-control" id="postnom" name="postnom" required>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="prenom">Prénom</label>
                                 <input type="text" class="form-control" id="prenom" name="prenom" required>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="email">Email</label>
                                 <input type="email" class="form-control" id="email" name="email">
                              </div>
                           </div>
                           <div class="form-row">
                              <div class="form-group col-md-3">
                                 <label>Sexe</label>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sexe" id="sexeM" value="M" required>
                                    <label class="form-check-label" for="sexeM">Masculin</label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sexe" id="sexeF" value="F" required>
                                    <label class="form-check-label" for="sexeF">Féminin</label>
                                 </div>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="date_naissance">Date de naissance</label>
                                 <input type="date" class="form-control" id="date_naissance" name="date_naissance"
                                    required>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="lieu_naissance">Lieu de naissance</label>
                                 <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance"
                                    required>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="telephone">Téléphone</label>
                                 <input type="text" class="form-control" id="telephone" name="telephone">
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
                                 <select class="form-control" id="etat_civil" name="etat_civil" required>
                                    <option value="">Choisir...</option>
                                    <option value="Célibataire">Célibataire</option>
                                    <option value="Marié">Marié</option>
                                    <option value="Divorcé">Divorcé</option>
                                    <option value="Veuf">Veuf</option>
                                 </select>
                              </div>
                              <div class="form-group col-md-4" id="nom_conjoint_group" style="display:none;">
                                 <label for="nom_conjoint">Nom du conjoint</label>
                                 <input type="text" class="form-control" id="nom_conjoint" name="nom_conjoint">
                              </div>
                              @push('js')
                                 <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                       var etatCivil = document.getElementById('etat_civil');
                                       var nomConjointGroup = document.getElementById('nom_conjoint_group');
                                       function toggleNomConjoint() {
                                          if (etatCivil.value === 'Marié') {
                                             nomConjointGroup.style.display = '';
                                          } else {
                                             nomConjointGroup.style.display = 'none';
                                             document.getElementById('nom_conjoint').value = '';
                                          }
                                       }
                                       etatCivil.addEventListener('change', toggleNomConjoint);
                                       toggleNomConjoint();
                                    });
                                 </script>
                              @endpush
                              <div class="form-group col-md-4">
                                 <label for="code_zone">Zone</label>
                                 <div class="input-group">
                                    <div class="input-group-prepend">
                                       <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <select class="form-control" id="code_zone" name="code_zone" required>
                                       <option value="">Choisir...</option>
                                       @foreach($zones as $zone)
                                          <option value="{{ $zone->code_zone }}">{{ $zone->nom }}</option>
                                       @endforeach
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
                                    <select class="form-control" id="type_piece_identite" name="type_piece_identite"
                                       required>
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
                                 <input type="text" class="form-control" id="numero_piece_identite"
                                    name="numero_piece_identite" required>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="lieu_delivrance_piece">Lieu de délivrance</label>
                                 <input type="text" class="form-control" id="lieu_delivrance_piece"
                                    name="lieu_delivrance_piece" required>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="date_delivrance_piece">Date de délivrance</label>
                                 <input type="date" class="form-control" id="date_delivrance_piece"
                                    name="date_delivrance_piece" required>
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
                                 <label>Photo du client</label>
                                 @include('partials.photo_cropper', ['inputName' => 'photo'])
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
                                 <input type="text" class="form-control" id="telephone_entreprise"
                                    name="telephone_entreprise">
                              </div>
                           </div>
                           <div class="form-row">
                              <div class="form-group col-md-4">
                                 <label for="nombre_annees_experience">Nombre d’années d’expérience</label>
                                 <input type="number" min="0" class="form-control" id="nombre_annees_experience"
                                    name="nombre_annees_experience">
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="revenu_mensuel">Revenu mensuel</label>
                                 <div class="input-group">
                                    <input type="number" min="0" step="0.01" class="form-control" id="revenu_mensuel"
                                       name="revenu_mensuel">
                                    <select class="form-select" id="revenu_mensuel_devise" name="revenu_mensuel_devise"
                                       style="max-width: 90px;">
                                       <option value="FC">FC</option>
                                       <option value="USD">USD</option>
                                    </select>
                                 </div>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="autres_details_activite">Autres détails</label>
                                 <input type="text" class="form-control" id="autres_details_activite"
                                    name="autres_details_activite">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Enregistrer
                     </button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
@endsection
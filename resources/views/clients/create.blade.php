@extends('layouts.app')

@section('page_title', 'Ajout client')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Ajouter un client')

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
                                 <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                              </div>
                              <div class="form-group col-md-3">
                                 <label for="lieu_naissance">Lieu de naissance</label>
                                 <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" required>
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
                                 <label for="photo">Photo du client</label>
                                 <div class="input-group align-items-start">
                                    <div class="custom-file" style="max-width: 220px;">
                                       <input type="file" class="custom-file-input" id="photo" name="photo"
                                          accept="image/*">
                                       <label class="custom-file-label" for="photo">Choisir une photo</label>
                                    </div>
                                    <div id="photo-preview-panel" style="margin-left:15px; display:none;">
                                       <img id="photo-preview" src="#" alt="Aperçu"
                                          style="max-width:150px; max-height:150px; border:1px solid #ccc; border-radius:6px; background:#f8f9fa;" />
                                       <div id="photo-error" style="color:#dc3545; font-size:0.9em; margin-top:4px;"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     
                     <script>
                        document.addEventListener('DOMContentLoaded', function () {
                           const input = document.getElementById('photo');
                           const previewPanel = document.getElementById('photo-preview-panel');
                           const preview = document.getElementById('photo-preview');
                           const errorDiv = document.getElementById('photo-error');
                           input.addEventListener('change', function (e) {
                              errorDiv.textContent = '';
                              previewPanel.style.display = 'none';
                              if (!input.files || !input.files[0]) return;
                              const file = input.files[0];
                              // Vérification type
                              if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                                 errorDiv.textContent = 'Le format de la photo doit être JPEG, PNG ou GIF.';
                                 input.value = '';
                                 return;
                              }
                              // Redimensionnement côté client (max 600x600)
                              const img = new window.Image();
                              const reader = new FileReader();
                              reader.onload = function (ev) {
                                 img.onload = function () {
                                    let width = img.width;
                                    let height = img.height;
                                    const maxDim = 600;
                                    if (width > maxDim || height > maxDim) {
                                       if (width > height) {
                                          height = Math.round(height * maxDim / width);
                                          width = maxDim;
                                       } else {
                                          width = Math.round(width * maxDim / height);
                                          height = maxDim;
                                       }
                                    }
                                    const canvas = document.createElement('canvas');
                                    canvas.width = width;
                                    canvas.height = height;
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(img, 0, 0, width, height);
                                    // Compression JPEG à 80% (ou PNG/GIF sans compression supplémentaire)
                                    let mime = file.type;
                                    let quality = 0.8;
                                    let dataUrl;
                                    if (mime === 'image/jpeg') {
                                       dataUrl = canvas.toDataURL('image/jpeg', quality);
                                    } else if (mime === 'image/png') {
                                       dataUrl = canvas.toDataURL('image/png');
                                    } else if (mime === 'image/gif') {
                                       // GIF non supporté par toDataURL, on garde l'original
                                       dataUrl = ev.target.result;
                                    }
                                    // Aperçu
                                    preview.src = dataUrl;
                                    previewPanel.style.display = 'block';
                                    // Remplacement du fichier dans l'input (Blob -> File)
                                    fetch(dataUrl)
                                       .then(res => res.arrayBuffer())
                                       .then(buf => {
                                          const ext = mime.split('/')[1];
                                          const newFile = new File([buf], file.name.replace(/\.[^.]+$/, '.'+ext), {type: mime});
                                          // Vérification taille (1 Mo max)
                                          if (newFile.size > 1 * 1024 * 1024) {
                                             errorDiv.textContent = 'La taille de la photo redimensionnée dépasse 1 Mo.';
                                             input.value = '';
                                             previewPanel.style.display = 'none';
                                             return;
                                          }
                                          // Remplacement du fichier dans l'input
                                          const dt = new DataTransfer();
                                          dt.items.add(newFile);
                                          input.files = dt.files;
                                       });
                                 };
                                 img.onerror = function () {
                                    errorDiv.textContent = 'Impossible de lire l\'image.';
                                    input.value = '';
                                 };
                                 img.src = ev.target.result;
                              };
                              reader.readAsDataURL(file);
                           });
                        });
                     </script>



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
                                    <input type="number" min="0" step="0.01" class="form-control" id="revenu_mensuel" name="revenu_mensuel">
                                    <select class="form-select" id="revenu_mensuel_devise" name="revenu_mensuel_devise" style="max-width: 90px;">
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
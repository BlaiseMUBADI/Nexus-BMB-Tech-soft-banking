@extends('layouts.app')

@section('page_title', 'Modifier le client')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Modification du client')

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
						<h3 class="card-title">Modifier le client : <span class="fw-bold">{{ $client->matricule }}</span></h3>
					</div>
					<form method="POST" action="{{ route('clients.update', $client->matricule) }}" enctype="multipart/form-data">
						@csrf
						@method('PUT')
						<div class="card-body">
							<!-- Identité -->
							<div class="card card-info mb-4">
								<div class="card-header bg-info">
									<h5 class="card-title mb-0"><i class="fas fa-user mr-2"></i>1. Informations d'identité</h5>
								</div>
								<div class="card-body">
									<div class="form-row">
										<div class="form-group col-md-3">
											<label for="nom">Nom </label>
											<input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $client->nom) }}" required>
										</div>
										<div class="form-group col-md-3">
											<label for="postnom">Postnom</label>
											<input type="text" class="form-control" id="postnom" name="postnom" value="{{ old('postnom', $client->postnom) }}" required>
										</div>
										<div class="form-group col-md-3">
											<label for="prenom">Prénom</label>
											<input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom', $client->prenom) }}" required>
										</div>
										<div class="form-group col-md-3">
											<label for="email">Email</label>
											<input type="email" class="form-control" id="email" name="email" value="{{ old('email', $client->email) }}">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-3">
											<label>Sexe</label>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="sexe" id="sexeM" value="M" @if(old('sexe', $client->sexe)==='M') checked @endif required>
												<label class="form-check-label" for="sexeM">Masculin</label>
											</div>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="sexe" id="sexeF" value="F" @if(old('sexe', $client->sexe)==='F') checked @endif required>
												<label class="form-check-label" for="sexeF">Féminin</label>
											</div>
										</div>
										<div class="form-group col-md-3">
											<label for="date_naissance">Date de naissance</label>
											<input type="date" class="form-control" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $client->date_naissance) }}" required>
										</div>
										<div class="form-group col-md-3">
											<label for="lieu_naissance">Lieu de naissance</label>
											<input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance', $client->lieu_naissance) }}" required>
										</div>
										<div class="form-group col-md-3">
											<label for="telephone">Téléphone</label>
											<input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone', $client->telephone) }}">
										</div>
										<div class="form-group col-md-3">
											<label for="adresse">Adresse</label>
											<input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse', $client->adresse) }}" required>
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
												<option value="Célibataire" @if(old('etat_civil', $client->etat_civil)==='Célibataire') selected @endif>Célibataire</option>
												<option value="Marié" @if(old('etat_civil', $client->etat_civil)==='Marié') selected @endif>Marié</option>
												<option value="Divorcé" @if(old('etat_civil', $client->etat_civil)==='Divorcé') selected @endif>Divorcé</option>
												<option value="Veuf" @if(old('etat_civil', $client->etat_civil)==='Veuf') selected @endif>Veuf</option>
											</select>
										</div>
										<div class="form-group col-md-4" id="nom_conjoint_group" style="display:none;">
											<label for="nom_conjoint">Nom du conjoint</label>
											<input type="text" class="form-control" id="nom_conjoint" name="nom_conjoint" value="{{ old('nom_conjoint', $client->nom_conjoint) }}">
										</div>
										<div class="form-group col-md-4">
											<label for="zone">Zone</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
												</div>
												<select class="form-control" id="zone" name="zone" required>
													<option value="">Choisir...</option>
													<option value="Urbain" @if(old('zone', $client->zone)==='Urbain') selected @endif>Urbain</option>
													<option value="Rural" @if(old('zone', $client->zone)==='Rural') selected @endif>Rural</option>
												</select>
											</div>
										</div>
									</div>
								</div>
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
													<option value="Carte nationale d'identité" @if(old('type_piece_identite', $client->type_piece_identite)=="Carte nationale d'identité") selected @endif>Carte nationale d'identité</option>
													<option value="Permis de conduire" @if(old('type_piece_identite', $client->type_piece_identite)=="Permis de conduire") selected @endif>Permis de conduire</option>
													<option value="Passeport" @if(old('type_piece_identite', $client->type_piece_identite)=="Passeport") selected @endif>Passeport</option>
													<option value="Carte d'électeur" @if(old('type_piece_identite', $client->type_piece_identite)=="Carte d'électeur") selected @endif>Carte d'électeur</option>
													<option value="Autre" @if(old('type_piece_identite', $client->type_piece_identite)=="Autre") selected @endif>Autre</option>
												</select>
											</div>
										</div>
										<div class="form-group col-md-3">
											<label for="numero_piece_identite">Numéro</label>
											<input type="text" class="form-control" id="numero_piece_identite" name="numero_piece_identite" value="{{ old('numero_piece_identite', $client->numero_piece_identite) }}" required>
										</div>
										<div class="form-group col-md-3">
											<label for="lieu_delivrance_piece">Lieu de délivrance</label>
											<input type="text" class="form-control" id="lieu_delivrance_piece" name="lieu_delivrance_piece" value="{{ old('lieu_delivrance_piece', $client->lieu_delivrance_piece) }}" required>
										</div>
										<div class="form-group col-md-3">
											<label for="date_delivrance_piece">Date de délivrance</label>
											<input type="date" class="form-control" id="date_delivrance_piece" name="date_delivrance_piece" value="{{ old('date_delivrance_piece', $client->date_delivrance_piece) }}" required>
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
													<input type="file" class="custom-file-input" id="photo" name="photo" accept="image/*">
													<label class="custom-file-label" for="photo">Choisir une photo</label>
												</div>
												<div id="photo-preview-panel" style="margin-left:15px; display:none;">
													<img id="photo-preview" src="#" alt="Aperçu" style="max-width:150px; max-height:150px; border:1px solid #ccc; border-radius:6px; background:#f8f9fa;" />
													<div id="photo-error" style="color:#dc3545; font-size:0.9em; margin-top:4px;"></div>
												</div>
												@if($client->photo)
													<div id="photo-current-panel" style="margin-left:15px;">
														<img src="{{ url('/clients/photo/' . basename($client->photo)) }}" alt="Photo actuelle" class="img-thumbnail mt-2" style="max-width: 120px;">
													</div>
												@endif
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
								const currentPanel = document.getElementById('photo-current-panel');
								input.addEventListener('change', function (e) {
									errorDiv.textContent = '';
									previewPanel.style.display = 'none';
									if (currentPanel) currentPanel.style.display = 'none';
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
											<input type="text" class="form-control" id="secteur_activite" name="secteur_activite" value="{{ old('secteur_activite', $client->secteur_activite) }}">
										</div>
										<div class="form-group col-md-4">
											<label for="type_activite">Type d’activité</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fas fa-industry"></i></span>
												</div>
												<select class="form-control" id="type_activite" name="type_activite">
													<option value="">Choisir...</option>
													<option value="Commerce" @if(old('type_activite', $client->type_activite)=="Commerce") selected @endif>Commerce</option>
													<option value="Agriculture" @if(old('type_activite', $client->type_activite)=="Agriculture") selected @endif>Agriculture</option>
													<option value="Artisanat" @if(old('type_activite', $client->type_activite)=="Artisanat") selected @endif>Artisanat</option>
													<option value="Service" @if(old('type_activite', $client->type_activite)=="Service") selected @endif>Service</option>
													<option value="Autre" @if(old('type_activite', $client->type_activite)=="Autre") selected @endif>Autre</option>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label for="statut_entreprise">Statut de l’entreprise</label>
											<input type="text" class="form-control" id="statut_entreprise" name="statut_entreprise" value="{{ old('statut_entreprise', $client->statut_entreprise) }}">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-4">
											<label for="nom_entreprise">Nom de l’entreprise</label>
											<input type="text" class="form-control" id="nom_entreprise" name="nom_entreprise" value="{{ old('nom_entreprise', $client->nom_entreprise) }}">
										</div>
										<div class="form-group col-md-4">
											<label for="adresse_entreprise">Adresse de l’entreprise</label>
											<input type="text" class="form-control" id="adresse_entreprise" name="adresse_entreprise" value="{{ old('adresse_entreprise', $client->adresse_entreprise) }}">
										</div>
										<div class="form-group col-md-4">
											<label for="telephone_entreprise">Téléphone de l’entreprise</label>
											<input type="text" class="form-control" id="telephone_entreprise" name="telephone_entreprise" value="{{ old('telephone_entreprise', $client->telephone_entreprise) }}">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-4">
											<label for="nombre_annees_experience">Nombre d’années d’expérience</label>
											<input type="number" min="0" class="form-control" id="nombre_annees_experience" name="nombre_annees_experience" value="{{ old('nombre_annees_experience', $client->nombre_annees_experience) }}">
										</div>
										<div class="form-group col-md-4">
											<label for="revenu_mensuel">Revenu mensuel</label>
											<div class="input-group">
												<input type="number" min="0" step="0.01" class="form-control" id="revenu_mensuel" name="revenu_mensuel" value="{{ old('revenu_mensuel', $client->revenu_mensuel) }}">
												<select class="form-select" id="revenu_mensuel_devise" name="revenu_mensuel_devise" style="max-width: 90px;">
													<option value="FC" @if(old('revenu_mensuel_devise', $client->revenu_mensuel_devise)=="FC") selected @endif>FC</option>
													<option value="USD" @if(old('revenu_mensuel_devise', $client->revenu_mensuel_devise)=="USD") selected @endif>USD</option>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label for="autres_details_activite">Autres détails</label>
											<input type="text" class="form-control" id="autres_details_activite" name="autres_details_activite" value="{{ old('autres_details_activite', $client->autres_details_activite) }}">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-primary" id="btn-save" disabled>Enregistrer les modifications</button>
							<a href="{{ url('/clients/' . $client->matricule) }}" class="btn btn-secondary ms-2">Annuler</a>
						</div>
					</form>
					<script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const form = document.querySelector('form[action*="clients"]');
                            const btn = document.getElementById('btn-save');
                            if (!form || !btn) return;
                            // Stocke les valeurs initiales
                            const initial = {};
                            form.querySelectorAll('input, select, textarea').forEach(el => {
                                if (el.type === 'checkbox' || el.type === 'radio') {
                                    initial[el.name] = form.querySelectorAll(`[name='${el.name}']`).length > 1
                                        ? Array.from(form.querySelectorAll(`[name='${el.name}']`)).map(e => e.checked)
                                        : el.checked;
                                } else if (el.type === 'file') {
                                    initial[el.name] = '';
                                } else {
                                    initial[el.name] = el.value;
                                }
                            });
                            function checkChanged() {
                                let changed = false;
                                form.querySelectorAll('input, select, textarea').forEach(el => {
                                    if (el.type === 'checkbox' || el.type === 'radio') {
                                        const current = form.querySelectorAll(`[name='${el.name}']`).length > 1
                                            ? Array.from(form.querySelectorAll(`[name='${el.name}']`)).map(e => e.checked)
                                            : el.checked;
                                        if (JSON.stringify(current) !== JSON.stringify(initial[el.name])) changed = true;
                                    } else if (el.type === 'file') {
                                        if (el.files && el.files.length > 0) changed = true;
                                    } else {
                                        if (el.value !== initial[el.name]) changed = true;
                                    }
                                });
                                btn.disabled = !changed;
                            }
                            form.querySelectorAll('input, select, textarea').forEach(el => {
                                el.addEventListener('input', checkChanged);
                                el.addEventListener('change', checkChanged);
                            });
                            checkChanged();
                        });
                    </script>
				</div>
			</div>
		</div>
	</div>
@endsection

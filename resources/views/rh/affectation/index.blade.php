@extends('layouts.app')

@section('page_title', 'Affectations')
@section('breadcrumb_parent', 'Ressources Humaines')
@section('breadcrumb', 'Affectations')

@section('content')
	<div class="container-fluid border">

		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card mt-4">
					<div class="card-header pb-0">
						<h5>Agents</h5>
					</div>
					<div class="card-body">
						<div class="table-responsive" style="max-height: 350px; min-height: 200px; overflow-y: auto;">
							<table id="agentsTable" class="table table-bordered table-striped mb-0 app-table">
								<thead>
									<tr>
										<th>#</th>
										<th>Matricule</th>
										<th>Nom</th>
								</thead>
								<tbody>
									@foreach($agents as $agent)
										<tr>
											<td>{{ $loop->iteration }}</td>
											<td>{{ $agent->matricule }}</td>
											<td>{{ $agent->nom }} {{ $agent->postnom }} {{ $agent->prenom }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card mt-4">
					<div class="card-header pb-0">
						<h5>Postes</h5>
					</div>
					<div class="card-body">
						<div class="table-responsive" style="max-height: 350px; min-height: 200px; overflow-y: auto;">
							<table id="postesTable" class="table table-bordered table-striped mb-0 app-table">
								<thead>
									<tr>
										<th>#</th>
										<th>Nom du poste</th>
									</tr>
								</thead>
								<tbody>
									@php $i = 1; @endphp
									@foreach($postes->groupBy('service_id') as $serviceId => $postesService)
										@php $serviceObj = $postesService->first()->service ?? null; @endphp
										<tr class="service-group-row">
											<td colspan="2"><strong>Service :
													{{ $serviceObj ? $serviceObj->nom : 'Inconnu' }}</strong></td>
										</tr>
										@foreach($postesService as $poste)
											<tr data-id="{{ $poste->id }}">
												<td>{{ $i++ }}</td>
												<td>{{ $poste->nom }}</td>
											</tr>
										@endforeach
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>


			<div class="col-md-4">
				<div class="card mt-4">
					<div class="card-header pb-0">
						<h5>Affectaions</h5>
					</div>
					<div class="card-body">
						<div class="table-responsive" style="max-height: 350px; min-height: 200px; overflow-y: auto;">
							<table id="affectationsTable" class="table table-bordered table-striped mb-0 app-table">
								<thead>
									<tr>
										<th>#</th>
										<th>Agent</th>
										<th>Poste</th>
										<th>Début</th>
										<th>Fin</th>
										<th>Etat</th>
									</tr>
								</thead>
								<tbody>
									@foreach($affectations as $affectation)
										<tr>
											<td>{{ $loop->iteration }}</td>
											<td>{{ $affectation->agent ? $affectation->agent->nom . ' ' . $affectation->agent->postnom . ' ' . $affectation->agent->prenom : $affectation->agent_matricule }}</td>
											<td>{{ $affectation->poste ? $affectation->poste->nom : $affectation->poste_id }}</td>
											<td>{{ $affectation->date_debut }}</td>
											<td>{{ $affectation->date_fin }}</td>
											<td>{{ $affectation->etat }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>


			<!-- Modal d'affectation -->
			<div class="modal fade show-modal-animation" id="affectationModal" tabindex="-1"
				aria-labelledby="affectationModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-lg">
					<div class="modal-content border-0 shadow-lg animate__animated animate__fadeInDown">
						<div class="modal-header bg-gradient-primary text-white border-0">
							<h5 class="modal-title fw-bold" id="affectationModalLabel">
								<i class="fas fa-user-check me-2"></i> Affecter un agent à un poste
							</h5>
						</div>
						<div class="modal-body bg-light">
							<div class="mb-4 p-3 rounded bg-white animate__animated animate__fadeIn">
								<div class="mb-2">
									<span class="badge bg-gradient-primary mb-2">
										<i class="fas fa-info-circle me-1"></i>
										Vous affectez <span id="labelAgent"></span> dans le service <span
											id="labelService"></span> au poste <span id="labelPoste"></span>
									</span>
								</div>
								<div class="mb-2">
									<strong class="text-primary">Agent sélectionné :</strong>
									<span id="selectedAgentInfo" class="ms-2 fw-semibold"></span>
								</div>
								<div class="mb-2">
									<strong class="text-success">Poste sélectionné :</strong>
									<span id="selectedPosteInfo" class="ms-2 fw-semibold"></span>
									<br>
									<small class="text-secondary"><span id="selectedPosteService" class="fw-semibold"></span></small>
								</div>
							</div>
							<form id="affectationForm">
								<div class="row mb-3">
									<div class="col-md-6">
										<label for="dateDebut" class="form-label">Date début</label>
										<input type="date" class="form-control" id="dateDebut" name="dateDebut" required>
									</div>
									<div class="col-md-6">
										<label for="dateFin" class="form-label">Date fin</label>
										<input type="date" class="form-control" id="dateFin" name="dateFin">
									</div>
								</div>
								<div class="mb-3">
									<label for="etat" class="form-label">Etat</label>
									<select class="form-select" id="etat" name="etat" required>
										<option value="actif">Actif</option>
										<option value="non-actif">Non actif</option>
									</select>
								</div>
							</form>
						</div>
						<div class="modal-footer bg-gradient-light border-0">
							<button type="button" class="btn btn-outline-secondary animate__animated animate__fadeInLeft"
								id="btnRetour">
								<i class="fas fa-arrow-left me-1"></i> Retour
							</button>
							<button type="button" class="btn btn-gradient-primary animate__animated animate__fadeInRight"
								id="confirmAffectationBtn">
								<i class="fas fa-check me-1"></i> Affecter
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

@endsection

	@section('css')
		<style>
			.service-group-row td {
				background: #6d78ed !important;
				color: #fff !important;
				font-weight: bold;
				border: none !important;
			}

			.app-table tbody tr.datatable-selected-row {
				background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
				color: #fff !important;
				box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
				transition: background 0.3s, color 0.3s;
			}

			.app-table tbody tr:hover:not(.datatable-selected-row) {
				background: linear-gradient(90deg, #06b6d4 0%, #3b82f6 100%) !important;
				color: #fff !important;
				cursor: pointer;
				box-shadow: 0 1px 4px rgba(59, 130, 246, 0.10);
				transition: background 0.3s, color 0.3s;
			}

			/* Animation et style modal Bootstrap */
			.show-modal-animation .modal-content {
				animation: fadeInDown 0.5s;
			}

			@keyframes fadeInDown {
				0% {
					opacity: 0;
					transform: translateY(-50px);
				}

				100% {
					opacity: 1;
					transform: translateY(0);
				}
			}

			.bg-gradient-primary {
				background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
				color: #fff !important;
			}

			.btn-gradient-primary {
				background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
				color: #fff !important;
				border: none;
			}

			.bg-gradient-light {
				background: linear-gradient(90deg, #f3f4f6 0%, #e0e7ef 100%) !important;
			}

			.fw-bold {
				font-weight: bold;
			}

			.fw-semibold {
				font-weight: 600;
			}

			.btn-close-white {
				filter: invert(1);
			}

			.modal-lg {
				max-width: 600px;
			}
		</style>
	@endsection

	@push('js')
		<script>
			// Définition globale du chemin de base Laravel (pour AJAX)
			window.baseUrl = "{{ url('') }}";
			// Setup global AJAX CSRF token pour toutes les requêtes
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			$(function () {
				setTimeout(function () {
					$('.alert-success').alert('close');
				}, 2500);
			});


			$(document).ready(function () {
				$('#agentsTable').DataTable({
					paging: true,
					searching: true,
					info: true,
					lengthChange: true,
					lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
					language: {
						url: '/plugins/datatables/i18n/fr-FR.json',
						paginate: {
							first: "Premier",
							last: "Dernier",
							next: "Suivant",
							previous: "Précédent"
						},
						search: "Recherche :",
						info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
						infoEmpty: "Aucune entrée à afficher",
						infoFiltered: "(filtré à partir de _MAX_ entrées)",
						lengthMenu: "Afficher _MENU_ entrées",
					}
				});
				// Sélection moderne
				$('#agentsTable tbody').on('click', 'tr', function () {
					if (!$(this).hasClass('service-group-row')) {
						$('#agentsTable tbody tr').removeClass('datatable-selected-row');
						$(this).addClass('datatable-selected-row');
						// Stocker l'agent sélectionné
						window.selectedAgent = {
							matricule: $(this).find('td').eq(1).text(),
							nom: $(this).find('td').eq(2).text()
						};
						// Vérifier si un poste est sélectionné
						if (window.selectedPoste) {
							showAffectationModal();
						}
					}
				});
				$('#postesTable tbody').on('click', 'tr', function () {
					if (!$(this).hasClass('service-group-row')) {
						$('#postesTable tbody tr').removeClass('datatable-selected-row');
						$(this).addClass('datatable-selected-row');
						// Stocker le poste sélectionné
						var service = $(this).prevAll('.service-group-row').first().find('strong').text().replace('Service : ', '');
						window.selectedPoste = {
							nom: $(this).find('td').eq(1).text(),
							service: service
						};
						// Vérifier si un agent est sélectionné
						if (window.selectedAgent) {
							showAffectationModal();
						}
					}
				});
			});
			// Fonction pour afficher le modal
			function showAffectationModal() {
				$('#selectedAgentInfo').text(window.selectedAgent.matricule + ' - ' + window.selectedAgent.nom);
				$('#selectedPosteInfo').text(window.selectedPoste.nom);
				$('#selectedPosteService').text(window.selectedPoste.service ? window.selectedPoste.service : 'Inconnu');
				$('#labelAgent').text(window.selectedAgent.nom);
				$('#labelPoste').text(window.selectedPoste.nom);
				$('#labelService').text(window.selectedPoste.service ? window.selectedPoste.service : 'Inconnu');
				$('#affectationModal').modal('show');
			}
			// Bouton de confirmation
			$('#confirmAffectationBtn').on('click', function () {
				// Récupération des données sélectionnées
				let agentMatricule = window.selectedAgent.matricule;
				let posteNom = window.selectedPoste.nom;
				let posteId = null;
				// Chercher l'ID du poste dans le DOM (ajoute data-id côté Blade si besoin)
				$('#postesTable tbody tr').each(function() {
					if (!$(this).hasClass('service-group-row')) {
						if ($(this).find('td').eq(1).text() === posteNom) {
							posteId = $(this).data('id') || $(this).attr('data-id');
						}
					}
				});
				
				
				var url = baseUrl + '/rh/affectations';
				// Construction des données à envoyer
				var data = {
					agent_matricule: agentMatricule,
					poste_id: posteId,
					date_debut: $('#dateDebut').val(),
					date_fin: $('#dateFin').val(),
					etat: $('#etat').val()
				};
				// Envoi AJAX
				$.post(url, data)
					.done(function(response) {
						// Afficher un message de succès générique (ou showSystemMessage si tu veux)
						showSystemMessage('success', 'Affectation enregistrée avec succès !');
						// Optionnel : recharger la page ou la liste des affectations dynamiquement ici
					})
					.fail(function(xhr) {
						let msg = 'Erreur lors de l\'enregistrement de l\'affectation.';
						if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
						showSystemMessage('error', msg);
					})
					.always(function() {
						$('#affectationModal').modal('hide');
						window.selectedAgent = null;
						window.selectedPoste = null;
						$('#agentsTable tbody tr').removeClass('datatable-selected-row');
						$('#postesTable tbody tr').removeClass('datatable-selected-row');
					});
			});
			$('#btnRetour').on('click', function () {
				$('#affectationModal').modal('hide');
			});
			
			$(document).ready(function () {
				$('#postesTable').DataTable({
					paging: true,
					searching: true,
					info: true,
					lengthChange: true,
					lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
					language: {
						url: '/plugins/datatables/i18n/fr-FR.json',
						paginate: {
							first: "Premier",
							last: "Dernier",
							next: "Suivant",
							previous: "Précédent"
						},
						search: "Recherche :",
						info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
						infoEmpty: "Aucune entrée à afficher",
						infoFiltered: "(filtré à partir de _MAX_ entrées)",
						lengthMenu: "Afficher _MENU_ entrées",
					}
				});
				// Sélection moderne
				$('#postesTable tbody').on('click', 'tr', function () {
					if (!$(this).hasClass('service-group-row')) {
						$('#postesTable tbody tr').removeClass('datatable-selected-row');
						$(this).addClass('datatable-selected-row');
					}
				});
				$('#postesTable tbody').on('click', 'tr', function () {
					if (!$(this).hasClass('service-group-row')) {
						$('#postesTable tbody tr').removeClass('datatable-selected-row');
						$(this).addClass('datatable-selected-row');
					}
				});
			});
		</script>
	@endpush
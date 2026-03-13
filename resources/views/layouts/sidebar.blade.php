{{--
sidebar.blade.php
Rôle : Affiche le menu latéral (sidebar) de l’interface AdminLTE.
--}}


<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="{{ route('dashboard') }}" class="brand-link">
		<img src="{{ asset('dist/img/vrailogoeben_redimensionner 1 .png') }}" alt="Logo"
			class="brand-image img-circle elevation-3">
		<span class="brand-text font-weight-light">Coopec EBEN</span>
	</a>

	<div class="sidebar">
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu">

		
				@if(in_array('EBEN-PER15', $userPermCodes ?? []))
					<li class="nav-item {{ request()->routeIs('clients.*') ? 'menu-open' : '' }}">
						<a href="#" class="nav-link parent-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
							<i class="nav-icon fas fa-users text-success"></i>
							<p>
								Clients / Membres
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview custom-sub-menu">
							<li class="nav-item">
								<a href="{{ route('clients.index') }}"
									class="nav-link sub-link {{ request()->routeIs('clients.index') ? 'active' : '' }}">
									<i class="fas fa-list nav-icon"></i>
									<p>Liste des membres</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="{{ route('clients.create') }}"
									class="nav-link sub-link {{ request()->routeIs('clients.create') ? 'active' : '' }}">
									<i class="fas fa-plus nav-icon"></i>
									<p>Ajouter un membre</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

				
				
				@if(in_array('EBEN-PER18', $userPermCodes ?? []))
					<li class="nav-item {{ request()->routeIs('comptes.*') ? 'menu-open' : '' }}">
						<a href="#" class="nav-link parent-link {{ request()->routeIs('comptes.*') ? 'active' : '' }}">
							<i class="nav-icon fas fa-university text-success"></i>
							<p>
								Compte / Client
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview custom-sub-menu">
							<li class="nav-item">
								<a href="{{ route('comptes.index') }}"
									class="nav-link sub-link {{ request()->routeIs('comptes.index') ? 'active' : '' }}">
									<i class="fas fa-list nav-icon"></i>
									<p>Liste des comptes</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="{{ route('comptes.create') }}"
									class="nav-link sub-link {{ request()->routeIs('comptes.create') ? 'active' : '' }}">
									<i class="fas fa-plus nav-icon"></i>
									<p>Ouverture de compte</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="#"
									class="nav-link sub-link {{ request()->routeIs('comptes.etat') ? 'active' : '' }}">
									<i class="fas fa-balance-scale nav-icon"></i>
									<p>États des soldes</p>
								</a>
							</li>
							<li class="nav-item submenu-indent">
								<a href="#"
									class="nav-link sub-link {{ request()->routeIs('comptes.blocage') ? 'active' : '' }}">
									<i class="fas fa-lock nav-icon"></i>
									<p>Blocage & Activation</p>
								</a>
							</li>
							<li class="nav-item submenu-indent">
								<a href="#"
									class="nav-link sub-link {{ request()->routeIs('comptes.cloture') ? 'active' : '' }}">
									<i class="fas fa-times nav-icon"></i>
									<p>Clôture de compte</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

				
				
				@if(in_array('EBEN-PER10', $userPermCodes ?? []))
					<li class="nav-item {{ request()->is('caisse*') ? 'menu-open' : '' }}">
						<a href="#" class="nav-link parent-link {{ request()->is('caisse*') ? 'active' : '' }}">
							<i class="nav-icon fas fa-cash-register text-success"></i>
							<p>
								Caisse / Guichet
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview custom-sub-menu">
							{{-- 1. Ouverture / Fermeture --}}
							<li class="nav-item">
								<a href="{{ route('caisses.ouverture') }}"
									class="nav-link sub-link {{ request()->is('caisses/ouverture*') ? 'active' : '' }}">
									<i class="fas fa-key nav-icon"></i>
									<p>Ouverture / Fermeture</p>
								</a>
							</li>

							{{-- 2. Opérations de Caisse --}}
							<li class="nav-item">
								<a href="{{ route('caisses.operations.index') }}"
									class="nav-link sub-link {{ request()->is('caisses/operations') ? 'active' : '' }}">
									<i class="fas fa-exchange-alt nav-icon"></i>
									<p>Opérations</p>
								</a>
							</li>

							{{-- 3. Journal des Opérations --}}
							<li class="nav-item">
								<a href="{{ route('caisses.journal.page') }}"
									class="nav-link sub-link {{ request()->is('caisses/operations/journal') ? 'active' : '' }}">
									<i class="fas fa-book-open nav-icon"></i>
									<p>Jrnl des Opérations</p>
								</a>
							</li>

							{{-- 4. Rapport de Fin de Journée (guichets FIXE / bureau) --}}
							<li class="nav-item">
								<a href="{{ route('caisses.rapport.fin.journee') }}"
									class="nav-link sub-link {{ request()->is('caisses/operations/rapport') ? 'active' : '' }}">
									<i class="fas fa-chart-bar nav-icon text-info"></i>
									<p>Rapport Journalier</p>
								</a>
							</li>

							{{-- 5. Gestion Mobile Départ / Retour (guichets MOBILE) --}}
							<li class="nav-item">
								<a href="{{ route('caisses.mobile.index') }}"
									class="nav-link sub-link {{ request()->is('caisses/mobile*') ? 'active' : '' }}">
									<i class="fas fa-mobile-alt nav-icon text-warning"></i>
									<p>Gestion Mobile</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

				
				@if(in_array('EBEN-PER6', $userPermCodes ?? []) || in_array('EBEN-PER7', $userPermCodes ?? []) || in_array('EBEN-PER8', $userPermCodes ?? []) || in_array('EBEN-PER9', $userPermCodes ?? []))
					<li class="nav-item has-treeview {{ request()->is('rh*') ? 'menu-open' : '' }}">
						<a href="#" class="nav-link parent-link {{ request()->is('rh*') ? 'active' : '' }}">
							<i class="nav-icon fas fa-briefcase text-warning"></i>
							<p>
								Ressources Humaines
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview custom-sub-menu">
							<li class="nav-item">
								<a href="{{ route('agents.index') }}"
									class="nav-link sub-link {{ request()->is('rh/agents*') ? 'active' : '' }}">
									<i class="fas fa-list nav-icon"></i>
									<p>Liste des agents</p>
								</a>
							</li>
							@if(in_array('EBEN-PER7', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('agents.create') }}"
										class="nav-link sub-link {{ request()->is('rh/agents/create') ? 'active' : '' }}">
										<i class="fas fa-plus nav-icon"></i>
										<p>Ajouter un agent</p>
									</a>
								</li>
							@endif
							@if(in_array('EBEN-PER9', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('affectations.index') }}"
										class="nav-link sub-link {{ request()->is('rh/affectations*') ? 'active' : '' }}">
										<i class="fas fa-user-friends nav-icon"></i>
										<p>Affectations</p>
									</a>
								</li>
							@endif
							<li class="nav-item">
								<a href="{{ route('services.index') }}"
									class="nav-link sub-link {{ request()->is('rh/services*') ? 'active' : '' }}">
									<i class="fas fa-briefcase nav-icon"></i>
									<p>Services/Postes</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

				
				
				@if(in_array('EBEN-PER44', $userPermCodes ?? []))
					<li class="nav-item {{ request()->is('tresorerie*') ? 'menu-open' : '' }}">
						<a href="#" class="nav-link parent-link {{ request()->is('tresorerie*') ? 'active' : '' }}">
							<i class="nav-icon fas fa-coins text-warning"></i>
							<p>
								Trésorerie / Coffre
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview custom-sub-menu">
							<li class="nav-item">
								<a href="{{ route('tresorerie.etat-coffre') }}"
									class="nav-link sub-link "
									title="État du Coffre">
									<i class="fas fa-lock fa-fw nav-icon text-warning"></i>
									<p>État du Coffre</p>
								</a>
							</li>
							
							<li class="nav-item">
								<a href="{{ route('tresorerie.agents.mobiles') }}" class="nav-link sub-link {{ request()->is('tresorerie/agents*') ? 'active' : '' }}"
									title="Rapport Agents Terrain">
									<i class="fas fa-mobile-alt fa-fw nav-icon text-warning"></i>
									<p>Agents Terrain</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

				
				
				@php $hasAdminAccess = count(array_intersect(['EBEN-PER1', 'EBEN-PER2', 'EBEN-PER3', 'EBEN-PER4', 'EBEN-PER5', 'EBEN-PER20', 'EBEN-PER21'], $userPermCodes ?? [])) > 0; @endphp
				@if($hasAdminAccess)
					<li class="nav-item {{ request()->routeIs('administration.*') ? 'menu-open' : '' }}">
						<a href="#"
							class="nav-link parent-link {{ request()->routeIs('administration.*') ? 'active' : '' }}">
							<i class="nav-icon fas fa-user-shield text-danger"></i>
							<p>
								Administration
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview custom-sub-menu">
							<li class="nav-item">
								<a href="{{ route('administration.utilisateurs.liste') }}"
									class="nav-link sub-link {{ request()->is('administration/utilisateurs*') ? 'active' : '' }}">
									<i class="fas fa-list nav-icon"></i>
									<p>Liste des utilisateurs</p>
								</a>
							</li>
							@if(in_array('EBEN-PER1', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('administration.utilisateurs.nouveau') }}"
										class="nav-link sub-link {{ request()->is('administration/utilisateurs/nouveau') ? 'active' : '' }}">
										<i class="fas fa-plus nav-icon"></i>
										<p>Ajouter un utilisateur</p>
									</a>
								</li>
							@endif
							@if(in_array('EBEN-PER2', $userPermCodes ?? []) || in_array('EBEN-PER3', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('administration.roles_permissions') }}"
										class="nav-link sub-link {{ request()->is('administration/roles-permissions*') ? 'active' : '' }}">
										<i class="fas fa-user-shield nav-icon"></i>
										<p>Roles & Permissions</p>
									</a>
								</li>
							@endif
							@if(in_array('EBEN-PER1', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('administration.zones.index') }}"
										class="nav-link sub-link {{ request()->is('administration/zones-portfeuille*') ? 'active' : '' }}">
										<i class="fas fa-map-marker-alt nav-icon"></i>
										<p>Zones/Portefeuille</p>
									</a>
								</li>
							@endif
							@if(in_array('EBEN-PER20', $userPermCodes ?? []) || in_array('EBEN-PER21', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('administration.devises-taux.index') }}"
										class="nav-link sub-link {{ request()->is('administration/devises-taux*') ? 'active' : '' }}">
										<i class="fas fa-coins nav-icon"></i>
										<p>Taux / Devises</p>
									</a>
								</li>
							@endif
							@if(in_array('EBEN-PER1', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('administration.guichets.index') }}"
										class="nav-link sub-link {{ request()->is('administration/guichets*') ? 'active' : '' }}">
										<i class="fas fa-store-alt nav-icon"></i>
										<p>Config. Guichets</p>
									</a>
								</li>
							@endif
							<li class="nav-item"> <a href="#"
									class="nav-link sub-link {{ request()->is('admin/logs') ? 'active' : '' }}">
									<i class="fas fa-book nav-icon"></i>
									<p>Journal d’activité</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="#" class="nav-link sub-link {{ request()->is('admin/security') ? 'active' : '' }}">
									<i class="fas fa-lock nav-icon"></i>
									<p>Paramètres de sécurité</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

			</ul>
		</nav>
	</div>
</aside>

<style>
	/* --- STYLE MODERNE SIDEBAR --- */

	/* 1. Style des Menus Parents */
	.nav-sidebar .nav-item>.parent-link.active {
		background-color: #3f474e !important;
		/* Gris foncé pro */
		color: #ffffff !important;
		border-left: 4px solid #ffc107;
		/* Ligne jaune/or sur le côté */
		font-weight: 600;
	}

	/* 2. Style des Sous-Menus (Le conteneur) */
	.custom-sub-menu {
		background-color: #2c3136 !important;
		/* Plus sombre que le fond principal */
		margin: 5px 0;
	}

	/* 3. Style des Liens de Sous-Menu (Enfants) */
	.nav-sidebar .nav-treeview>.nav-item>.sub-link {
		color: #c2c7d0 !important;
		transition: all 0.3s;
		border-radius: 4px;
		margin: 2px 8px;
	}

	/* 4. État Actif du Sous-Menu (La couleur différente que tu voulais) */
	.nav-sidebar .nav-treeview>.nav-item>.sub-link.active {
		background-color: #007bff !important;
		/* Bleu moderne pour le sous-menu */
		color: #ffffff !important;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
	}

	/* 5. Effet au survol (Hover) */
	.sub-link:hover {
		background-color: rgba(255, 255, 255, 0.1);
		color: #fff !important;
		transform: translateX(5px);
	}

	/* Indentation propre */
	.nav-child-indent .nav-treeview {
		padding-left: 10px;
	}
</style>
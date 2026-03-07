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

				
				{{-- ══════════════════════════════════════════════════════
				1. CLIENTS / MEMBRES — EBEN-PER15
				══════════════════════════════════════════════════════ --}}
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

				{{-- ══════════════════════════════════════════════════════
				2. COMPTE / CLIENT — EBEN-PER18
				══════════════════════════════════════════════════════ --}}
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

				{{-- ══════════════════════════════════════════════════════
				3. CAISSE / GUICHET — EBEN-PER10
				══════════════════════════════════════════════════════ --}}
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
							<li class="nav-item">
								<a href="{{ route('caisses.ouverture') }}"
									class="nav-link sub-link {{ request()->is('caisses/ouverture*') ? 'active' : '' }}">
									<i class="fas fa-key nav-icon"></i>
									<p>Ouverture / Fermeture</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="#"
									class="nav-link sub-link {{ request()->is('caisse/operations*') ? 'active' : '' }}">
									<i class="fas fa-exchange-alt nav-icon"></i>
									<p>Opérations</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="#"
									class="nav-link sub-link {{ request()->is('caisse/transferts*') ? 'active' : '' }}">
									<i class="fas fa-truck-loading nav-icon"></i>
									<p>Mouvements de fonds</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="#"
									class="nav-link sub-link {{ request()->is('caisse/comptabilite*') ? 'active' : '' }}">
									<i class="fas fa-list-alt nav-icon"></i>
									<p>Jrnl des Opérations</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

				{{-- ══════════════════════════════════════════════════════
				4. RESSOURCES HUMAINES — EBEN-PER6/7/8/9
				══════════════════════════════════════════════════════ --}}
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

				{{-- ══════════════════════════════════════════════════════
				5. TRÉSORERIE / COFFRE — EBEN-PER44
				(en premier : le gérant vérifie le coffre avant tout)
				══════════════════════════════════════════════════════ --}}
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
								<a href="{{ route('tresorerie.coffre.index') }}"
									class="nav-link sub-link {{ request()->is('tresorerie/coffre*') ? 'active' : '' }}"
									title="État du Coffre">
									<i class="fas fa-lock fa-fw nav-icon text-warning"></i>
									<p>État du Coffre</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="{{ route('tresorerie.coffre.index') }}#approvisionnement" class="nav-link sub-link"
									title="Approvisionnement">
									<i class="fas fa-arrow-circle-down fa-fw nav-icon text-warning"></i>
									<p>Approvisionnement</p>
								</a>
							</li>
							<li class="nav-item">
							<a href="{{ route('tresorerie.coffre.index') }}#tab-intercaisses"
								class="nav-link sub-link"
								title="Inter-caisses">
									<i class="fas fa-exchange-alt fa-fw nav-icon text-warning"></i>
									<p>Inter-caisses</p>
								</a>
							</li>
							<li class="nav-item">
								<a href="{{ route('tresorerie.coffre.index') }}#journal" class="nav-link sub-link"
									title="Journal central">
									<i class="fas fa-book-open fa-fw nav-icon text-warning"></i>
									<p>Journal central</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

				{{-- ══════════════════════════════════════════════════════
				6. ADMINISTRATION — EBEN-PER1/2/3/4/5/20/21
				(configuration technique — en dernier)
				══════════════════════════════════════════════════════ --}}
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
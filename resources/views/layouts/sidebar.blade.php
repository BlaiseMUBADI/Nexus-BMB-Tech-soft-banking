{{--
sidebar.blade.php
Rôle : Affiche le menu latéral (sidebar) de l’interface AdminLTE.
--}}

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<!-- Brand Logo -->
	<a href="{{ url('/dashboard') }}" class="brand-link">
		<img src="{{ asset('dist/img/vrailogoeben_redimensionner 1 .png') }}" alt="AdminLTE Logo"
			class="brand-image img-circle elevation-3" style="opacity: .8">
		<span class="brand-text font-weight-light">Coopec EBEN</span>
	</a>

	<!-- Sidebar -->
	<div class="sidebar">
		<!-- Sidebar Menu -->
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

				<!-- Ajoutez des icônes aux liens en utilisant la classe .nav-icon avec font-awesome ou toute autre bibliothèque de polices d'icônes -->

				<!--Clients / Membres -->

				<li class="nav-item has-treeview {{ request()->is('clients*') ? 'menu-open' : '' }}">
					<a href="#" class="nav-link {{ request()->is('clients*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-users"></i>
						<p>
							Clients / Membres
							<i class="right fas fa-angle-left"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('/clients') }}" class="nav-link {{ request()->is('clients') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Liste des clients</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('/clients/create') }}" class="nav-link {{ request()->is('clients/create') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Ajouter un client</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('/test') }}" class="nav-link {{ request()->is('test') ? 'active' : '' }}">
								<i class="nav-icon fas fa-vial"></i>
								<p>Test</p>
							</a>
						</li>
					</ul>
				</li>
				<!-- Fin Clients / Membres -->

				<!-- Ressources Humaines -->
				<li class="nav-item has-treeview {{ request()->is('rh*') ? 'menu-open' : '' }}">
					<a href="#" class="nav-link {{ request()->is('rh*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-briefcase"></i>
						<p>
							Ressources Humaines
							<i class="right fas fa-angle-left"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('/rh/agents') }}" class="nav-link {{ request()->is('rh/agents') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Liste des agents</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('/rh/agents/create') }}" class="nav-link {{ request()->is('rh/agents/create') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Ajouter un agent</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('/rh/affectations') }}" class="nav-link {{ request()->is('rh/affectations') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Affectations</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('/rh/services') }}" class="nav-link {{ request()->is('rh/services') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Services/Postes</p>
							</a>
						</li>
					</ul>
				</li>
				<!-- Fin Ressources Humaines -->

				<!-- Administration -->
				<li class="nav-item has-treeview {{ request()->is('admin*') ? 'menu-open' : '' }}">
					<a href="#" class="nav-link {{ request()->is('admin*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-user-shield"></i>
						<p>
							Administration
							<i class="right fas fa-angle-left"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('/administration/utilisateurs') }}" class="nav-link {{ request()->is('administration/utilisateurs') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Liste des utilisateurs</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('/administration/utilisateurs/nouveau') }}" class="nav-link {{ request()->is('administration/utilisateurs/create') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Ajouter un utilisateur</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('administration.roles_permissions') }}" class="nav-link {{ request()->is('administration/roles-permissions') ? 'active' : '' }}">
								<i class="nav-icon fas fa-user-shield"></i>
								<p>Roles & Permissions</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('/admin/logs') }}" class="nav-link {{ request()->is('admin/logs') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Journal d’activité</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('/admin/security') }}" class="nav-link {{ request()->is('admin/security') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Paramètres de sécurité</p>
							</a>
						</li>
					</ul>
				</li>
				<!-- Fin Administration -->
			</ul>
		</nav>
		<!-- /.sidebar-menu -->
	</div>
	<!-- /.sidebar -->
</aside>

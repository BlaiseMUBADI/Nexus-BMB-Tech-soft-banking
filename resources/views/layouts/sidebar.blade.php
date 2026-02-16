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
				<!-- Clients / Membres -->

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
			</ul>
		</nav>
		<!-- /.sidebar-menu -->
	</div>
	<!-- /.sidebar -->
</aside>

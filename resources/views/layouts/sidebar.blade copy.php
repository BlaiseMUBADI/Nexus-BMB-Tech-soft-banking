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


				<li class="nav-item has-treeview {{ request()->is('pages/layout*') ? 'menu-open' : '' }}">
					<a href="#" class="nav-link {{ request()->is('pages/layout*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-copy"></i>
						<p>
							Layout Options
							<i class="fas fa-angle-left right"></i>
							<span class="badge badge-info right">6</span>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('pages/layout/top-nav') }}" class="nav-link {{ request()->is('pages/layout/top-nav') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Top Navigation</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/layout/top-nav-sidebar') }}" class="nav-link {{ request()->is('pages/layout/top-nav-sidebar') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Top Navigation + Sidebar</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/layout/boxed') }}" class="nav-link {{ request()->is('pages/layout/boxed') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Boxed</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/layout/fixed-sidebar') }}" class="nav-link {{ request()->is('pages/layout/fixed-sidebar') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Fixed Sidebar</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/layout/fixed-sidebar-custom') }}" class="nav-link {{ request()->is('pages/layout/fixed-sidebar-custom') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Fixed Sidebar <small>+ Custom Area</small></p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/layout/fixed-topnav') }}" class="nav-link {{ request()->is('pages/layout/fixed-topnav') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Fixed Navbar</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/layout/fixed-footer') }}" class="nav-link {{ request()->is('pages/layout/fixed-footer') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Fixed Footer</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/layout/collapsed-sidebar') }}" class="nav-link {{ request()->is('pages/layout/collapsed-sidebar') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Collapsed Sidebar</p>
							</a>
						</li>
					</ul>
				</li>

				<li class="nav-item has-treeview {{ request()->is('pages/charts*') ? 'menu-open' : '' }}">
					<a href="#" class="nav-link {{ request()->is('pages/charts*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-chart-pie"></i>
						<p>
							Charts
							<i class="right fas fa-angle-left"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('pages/charts/chartjs') }}" class="nav-link {{ request()->is('pages/charts/chartjs') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>ChartJS</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/charts/flot') }}" class="nav-link {{ request()->is('pages/charts/flot') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Flot</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/charts/inline') }}" class="nav-link {{ request()->is('pages/charts/inline') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Inline</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/charts/uplot') }}" class="nav-link {{ request()->is('pages/charts/uplot') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>uPlot</p>
							</a>
						</li>
					</ul>
				</li>

				<li class="nav-item has-treeview {{ request()->is('pages/UI*') ? 'menu-open' : '' }}">
					<a href="#" class="nav-link {{ request()->is('pages/UI*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-tree"></i>
						<p>
							UI Elements
							<i class="fas fa-angle-left right"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('pages/UI/general') }}" class="nav-link {{ request()->is('pages/UI/general') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>General</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/UI/icons') }}" class="nav-link {{ request()->is('pages/UI/icons') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Icons</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/UI/buttons') }}" class="nav-link {{ request()->is('pages/UI/buttons') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Buttons</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/UI/sliders') }}" class="nav-link {{ request()->is('pages/UI/sliders') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Sliders</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/UI/modals') }}" class="nav-link {{ request()->is('pages/UI/modals') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Modals & Alerts</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/UI/navbar') }}" class="nav-link {{ request()->is('pages/UI/navbar') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Navbar & Tabs</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/UI/timeline') }}" class="nav-link {{ request()->is('pages/UI/timeline') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Timeline</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/UI/ribbons') }}" class="nav-link {{ request()->is('pages/UI/ribbons') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Ribbons</p>
							</a>
						</li>
					</ul>
				</li>

				<li class="nav-item has-treeview {{ request()->is('pages/forms*') ? 'menu-open' : '' }}">
					<a href="#" class="nav-link {{ request()->is('pages/forms*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-edit"></i>
						<p>
							Forms
							<i class="fas fa-angle-left right"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('pages/forms/general') }}" class="nav-link {{ request()->is('pages/forms/general') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>General Elements</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/forms/advanced') }}" class="nav-link {{ request()->is('pages/forms/advanced') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Advanced Elements</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/forms/editors') }}" class="nav-link {{ request()->is('pages/forms/editors') ? 'active' : '' }}">
								<i class="far fa-circle nav-icon"></i>
								<p>Editors</p>
							</a>
						</li>
					</ul>
				</li>

				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon fas fa-table"></i>
						<p>
							Tables
							<i class="fas fa-angle-left right"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('pages/tables/simple') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Simple Tables</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/tables/data') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>DataTables</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/tables/jsgrid') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>jsGrid</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-header">EXAMPLES</li>
				<li class="nav-item">
					<a href="{{ url('pages/calendar') }}" class="nav-link">
						<i class="nav-icon fas fa-calendar-alt"></i>
						<p>
							Calendar
							<span class="badge badge-info right">2</span>
						</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="{{ url('pages/gallery') }}" class="nav-link">
						<i class="nav-icon far fa-image"></i>
						<p>
							Gallery
						</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="{{ url('pages/kanban') }}" class="nav-link">
						<i class="nav-icon fas fa-columns"></i>
						<p>
							Kanban Board
						</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon far fa-envelope"></i>
						<p>
							Mailbox
							<i class="fas fa-angle-left right"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('pages/mailbox/mailbox') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Inbox</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/mailbox/compose') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Compose</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/mailbox/read-mail') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Read</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon fas fa-book"></i>
						<p>
							Pages
							<i class="fas fa-angle-left right"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('pages/examples/invoice') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Invoice</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/profile') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Profile</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/e-commerce') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>E-commerce</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/projects') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Projects</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/project-add') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Project Add</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/project-edit') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Project Edit</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/project-detail') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Project Detail</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/contacts') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Contacts</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/faq') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>FAQ</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/contact-us') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Contact us</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon far fa-plus-square"></i>
						<p>
							Extras
							<i class="fas fa-angle-left right"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>
									Login & Register v1
									<i class="fas fa-angle-left right"></i>
								</p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="{{ url('pages/examples/login') }}" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>Login v1</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ url('pages/examples/register') }}" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>Register v1</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ url('pages/examples/forgot-password') }}" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>Forgot Password v1</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ url('pages/examples/recover-password') }}" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>Recover Password v1</p>
									</a>
								</li>
							</ul>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>
									Login & Register v2
									<i class="fas fa-angle-left right"></i>
								</p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="{{ url('pages/examples/login-v2') }}" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>Login v2</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ url('pages/examples/register-v2') }}" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>Register v2</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ url('pages/examples/forgot-password-v2') }}" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>Forgot Password v2</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ url('pages/examples/recover-password-v2') }}" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>Recover Password v2</p>
									</a>
								</li>
							</ul>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/lockscreen') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Lockscreen</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/legacy-user-menu') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Legacy User Menu</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/language-menu') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Language Menu</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/404') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Error 404</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/500') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Error 500</p>
							</a>
						</li>
						<li class="nav-item">
						<li class="nav-item">
							<a href="{{ url('pages/examples/pace') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Pace</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/examples/blank') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Blank Page</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('starter') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Starter Page</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon fas fa-search"></i>
						<p>
							Search
							<i class="fas fa-angle-left right"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('pages/search/simple') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Simple Search</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('pages/search/enhanced') }}" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Enhanced</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-header">MISCELLANEOUS</li>
				<li class="nav-item">
					<a href="{{ url('iframe') }}" class="nav-link">
						<i class="nav-icon fas fa-ellipsis-h"></i>
						<p>Tabbed IFrame Plugin</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="https://adminlte.io/docs/3.1/" class="nav-link" target="_blank">
						<i class="nav-icon fas fa-file"></i>
						<p>Documentation</p>
					</a>
				</li>
				<li class="nav-header">MULTI LEVEL EXAMPLE</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="fas fa-circle nav-icon"></i>
						<p>Level 1</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon fas fa-circle"></i>
						<p>
							Level 1
							<i class="right fas fa-angle-left"></i>
						</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Level 2</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>
									Level 2
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="#" class="nav-link">
										<i class="far fa-dot-circle nav-icon"></i>
										<p>Level 3</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link">
										<i class="far fa-dot-circle nav-icon"></i>
										<p>Level 3</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link">
										<i class="far fa-dot-circle nav-icon"></i>
										<p>Level 3</p>
									</a>
								</li>
							</ul>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="far fa-circle nav-icon"></i>
								<p>Level 2</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="fas fa-circle nav-icon"></i>
						<p>Level 1</p>
					</a>
				</li>
				<li class="nav-header">LABELS</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon far fa-circle text-danger"></i>
						<p class="text">Important</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon far fa-circle text-warning"></i>
						<p>Warning</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">
						<i class="nav-icon far fa-circle text-info"></i>
						<p>Informational</p>
					</a>
				</li>
			</ul>
		</nav>
		<!-- /.sidebar-menu -->
	</div>
	<!-- /.sidebar -->
</aside>

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
							@if(in_array('EBEN-PER76', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('clients.agents-terrain', ['date_debut' => now()->toDateString(), 'date_fin' => now()->toDateString()]) }}"
										class="nav-link sub-link {{ request()->routeIs('clients.agents-terrain') ? 'active' : '' }}"
										title="Rapport Agents Terrain">
										<i class="fas fa-mobile-alt nav-icon text-warning"></i>
										<p>Agents Terrain</p>
									</a>
								</li>
							@endif
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
								<span class="nav-link sub-link disabled-link" title="Fonctionnalité à venir" aria-disabled="true">
									<i class="fas fa-balance-scale nav-icon"></i>
									<p>États des soldes <span class="badge badge-secondary ml-1">Bientôt</span></p>
								</span>
							</li>
							<li class="nav-item submenu-indent">
								<span class="nav-link sub-link disabled-link" title="Fonctionnalité à venir" aria-disabled="true">
									<i class="fas fa-lock nav-icon"></i>
									<p>Blocage & Activation <span class="badge badge-secondary ml-1">Bientôt</span></p>
								</span>
							</li>
							<li class="nav-item submenu-indent">
								<span class="nav-link sub-link disabled-link" title="Fonctionnalité à venir" aria-disabled="true">
									<i class="fas fa-times nav-icon"></i>
									<p>Clôture de compte <span class="badge badge-secondary ml-1">Bientôt</span></p>
								</span>
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
							@if(in_array('EBEN-PER10', $userPermCodes ?? []))
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
                                 {{-- 5. Remboursements --}}
                                  @if(in_array('EBEN-PER111', $userPermCodes ?? []))
                                      <li class="nav-item">
                                          <a href="{{ route('caisses.remboursements.liste') }}"
                                             class="nav-link sub-link {{ request()->routeIs('caisses.remboursements.liste') ? 'active' : '' }}">
                                              <i class="fas fa-money-bill-wave nav-icon text-primary"></i>
                                              <p>Remboursements</p>
                                          </a>
                                      </li>
                                  @endif

                                 {{-- 6. Opérations Administratives (Dépenses + Recettes) — réservées aux guichets FIXE/CENTRAL --}}
                                  @if(strtoupper((string) ($guichetTypeActuel ?? '')) !== 'MOBILE')
                                      <li class="nav-item">
                                          <a href="{{ route('caisses.operations-administratives.index') }}"
                                             class="nav-link sub-link {{ request()->routeIs('caisses.operations-administratives.*') ? 'active' : '' }}">
                                              <i class="fas fa-exchange-alt nav-icon text-danger"></i>
                                              <p>Op. Administratives</p>
                                          </a>
                                      </li>
                                  @endif

							@endif

							@if(in_array('EBEN-PER10', $userPermCodes ?? []))
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
							@endif
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
							@if(in_array('EBEN-PER6', $userPermCodes ?? []))
							<li class="nav-item">
								<a href="{{ route('agents.index') }}"
									class="nav-link sub-link {{ request()->is('rh/agents*') ? 'active' : '' }}">
									<i class="fas fa-list nav-icon"></i>
									<p>Liste des agents</p>
								</a>
							</li>
							@endif
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
							@if(in_array('EBEN-PER6', $userPermCodes ?? []))
							<li class="nav-item">
								<a href="{{ route('services.index') }}"
									class="nav-link sub-link {{ request()->is('rh/services*') ? 'active' : '' }}">
									<i class="fas fa-briefcase nav-icon"></i>
									<p>Services/Postes</p>
								</a>
							</li>
							@endif
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
									class="nav-link sub-link {{ request()->routeIs('tresorerie.etat-coffre') ? 'active' : '' }}"
									title="État du Coffre">
									<i class="fas fa-lock fa-fw nav-icon text-warning"></i>
									<p>État du Coffre</p>
								</a>
							</li>

							<li class="nav-item">
								<a href="{{ route('tresorerie.approvisionnement') }}"
									class="nav-link sub-link {{ request()->routeIs('tresorerie.approvisionnement') ? 'active' : '' }}"
									title="Approvisionnement du coffre central">
									<i class="fas fa-piggy-bank fa-fw nav-icon text-success"></i>
									<p>Approvisionnement</p>
								</a>
							</li>

							<li class="nav-item">
								<a href="{{ route('tresorerie.intercaisse') }}"
									class="nav-link sub-link {{ request()->routeIs('tresorerie.intercaisse') ? 'active' : '' }}"
									title="Transferts inter-caisses">
									<i class="fas fa-random fa-fw nav-icon text-info"></i>
									<p>Intercaisse</p>
								</a>
							</li>
							
							<li class="nav-item">
								<a href="{{ route('tresorerie.commissions.index') }}"
									class="nav-link sub-link {{ request()->routeIs('tresorerie.commissions.*') ? 'active' : '' }}"
									title="Paramétrage des commissions">
									<i class="fas fa-percent fa-fw nav-icon text-danger"></i>
									<p>Commissions</p>
								</a>
							</li>
						</ul>
					</li>
				@endif

				@if(in_array('EBEN-PER49', $userPermCodes ?? []))
					<li class="nav-item {{ request()->is('comptabilite*') ? 'menu-open' : '' }}">
						<a href="#" class="nav-link parent-link {{ request()->is('comptabilite*') ? 'active' : '' }}">
							<i class="nav-icon fas fa-book text-info"></i>
							<p>
								Comptabilite
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview custom-sub-menu">
							<li class="nav-item">
								<a href="{{ route('comptabilite.dashboard') }}"
									class="nav-link sub-link {{ request()->routeIs('comptabilite.dashboard') ? 'active' : '' }}">
									<i class="fas fa-chart-line nav-icon"></i>
									<p>Tableau de bord</p>
								</a>
							</li>

							@if(in_array('EBEN-PER51', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('comptabilite.plan') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.plan') ? 'active' : '' }}">
										<i class="fas fa-sitemap nav-icon"></i>
										<p>Plan OHADA</p>
									</a>
								</li>
							@endif

							@if(in_array('EBEN-PER115', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('comptabilite.categories-depenses.index') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.categories-depenses.*') ? 'active' : '' }}">
										<i class="fas fa-tags nav-icon text-danger"></i>
										<p>Catégories de dépenses</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ route('comptabilite.categories-recettes.index') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.categories-recettes.*') ? 'active' : '' }}">
										<i class="fas fa-tags nav-icon text-success"></i>
										<p>Catégories de recettes</p>
									</a>
								</li>
							@endif

							@if(in_array('EBEN-PER50', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('comptabilite.journal') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.journal') ? 'active' : '' }}">
										<i class="fas fa-book-open nav-icon"></i>
										<p>Journal comptable</p>
									</a>
								</li>
							@endif

						@if(in_array('EBEN-PER52', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('comptabilite.grand-livre') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.grand-livre') ? 'active' : '' }}">
										<i class="fas fa-book-open nav-icon"></i>
										<p>Grand Livre</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ route('comptabilite.balance') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.balance') ? 'active' : '' }}">
										<i class="fas fa-balance-scale nav-icon"></i>
										<p>Balance Générale</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ route('comptabilite.compte-resultat') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.compte-resultat') ? 'active' : '' }}">
										<i class="fas fa-chart-line nav-icon"></i>
										<p>Compte de Résultat</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ route('comptabilite.bilan') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.bilan') ? 'active' : '' }}">
										<i class="fas fa-landmark nav-icon"></i>
										<p>Bilan</p>
									</a>
								</li>
							@endif

							@if(in_array('EBEN-PER49', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('comptabilite.exercices.index') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.exercices.*') ? 'active' : '' }}">
										<i class="fas fa-calendar-check nav-icon text-warning"></i>
										<p>Exercices Comptables</p>
									</a>
								</li>
							@endif

							@if(in_array('EBEN-PER119', $userPermCodes ?? []) || in_array('EBEN-PER120', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('comptabilite.virements.index') }}"
										class="nav-link sub-link {{ request()->routeIs('comptabilite.virements.*') ? 'active' : '' }}">
										<i class="fas fa-exchange-alt nav-icon text-info"></i>
										<p>Virements Bancaires</p>
									</a>
								</li>
							@endif
						</ul>
					</li>
				@endif

				@php $hasCreditAccess = in_array('EBEN-PER53', $userPermCodes ?? []) || in_array('EBEN-PER70', $userPermCodes ?? []) || in_array('EBEN-PER90', $userPermCodes ?? []); @endphp
				@if($hasCreditAccess)
					@php
						$creditStatutMenu = request('statut');
						$creditVueMenu = request('vue');
						$isCreditListBase = request()->routeIs('credit.index') && empty($creditStatutMenu);
						$canCreditSupervision = count(array_intersect(['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64'], $userPermCodes ?? [])) > 0;
					@endphp
					<li class="nav-item {{ (request()->is('credits*') || request()->is('recouvrement*')) ? 'menu-open' : '' }}">
						<a href="#" class="nav-link parent-link {{ (request()->is('credits*') || request()->is('recouvrement*')) ? 'active' : '' }}">
							<i class="nav-icon fas fa-hand-holding-usd" style="color:#e67e22;"></i>
							<p>
								Crédits
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview custom-sub-menu">

							@if($canCreditSupervision)
								<li class="nav-item">
									<a href="{{ route('credit.dashboard') }}"
										class="nav-link sub-link {{ request()->routeIs('credit.dashboard') ? 'active' : '' }}">
										<i class="fas fa-tachometer-alt nav-icon text-warning"></i>
										<p>Tableau de bord</p>
									</a>
								</li>
							@endif
							@if(in_array('EBEN-PER54', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('credit.create') }}"
										class="nav-link sub-link {{ request()->routeIs('credit.create') ? 'active' : '' }}">
										<i class="fas fa-plus-circle nav-icon text-success"></i>
										<p>Nouvelle demande</p>
									</a>
								</li>
							@endif

							@if(in_array('EBEN-PER53', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('credit.index') }}"
										class="nav-link sub-link {{ $isCreditListBase ? 'active' : '' }}">
										<i class="fas fa-list nav-icon"></i>
										<p>Liste des dossiers</p>
									</a>
								</li>
							@endif

							@if(in_array('EBEN-PER58', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('credit.index') }}?vue=analyse"
										class="nav-link sub-link {{ request()->routeIs('credit.analyse') || (request()->routeIs('credit.index') && $creditVueMenu === 'analyse') ? 'active' : '' }}">
										<i class="fas fa-search-dollar nav-icon text-primary"></i>
										<p>Dossiers à analyser</p>
									</a>
								</li>
							@endif

							@if(in_array('EBEN-PER118', $userPermCodes ?? []))
								<li class="nav-item">
									<a href="{{ route('credit.echeances') }}"
										class="nav-link sub-link {{ request()->routeIs('credit.echeances') ? 'active' : '' }}">
										<i class="fas fa-calendar-alt nav-icon text-danger"></i>
										<p>Tombée d'échéances</p>
									</a>
								</li>
							@endif

								@if(in_array('EBEN-PER70', $userPermCodes ?? []))
									<li class="nav-item">
										<a href="{{ route('credit.rapport_frais') }}"
											class="nav-link sub-link {{ request()->routeIs('credit.rapport_frais') ? 'active' : '' }}">
											<i class="fas fa-file-invoice-dollar nav-icon text-info"></i>
											<p>Rapport frais déblocage</p>
										</a>
									</li>
								@endif

								@if(in_array('EBEN-PER60', $userPermCodes ?? []) || in_array('EBEN-PER61', $userPermCodes ?? []) || in_array('EBEN-PER62', $userPermCodes ?? []) || in_array('EBEN-PER63', $userPermCodes ?? []))
									<li class="nav-item">
										<a href="{{ route('credit.index') }}?statut=EN_VALIDATION"
											class="nav-link sub-link {{ request()->routeIs('credit.validation') || (request()->routeIs('credit.index') && $creditStatutMenu === 'EN_VALIDATION') ? 'active' : '' }}">
											<i class="fas fa-check-double nav-icon text-info"></i>
											<p>Dossiers à valider</p>
										</a>
									</li>
								@endif

								@if(in_array('EBEN-PER64', $userPermCodes ?? []))
									<li class="nav-item">
										<a href="{{ route('credit.index') }}?statut=PRET_A_DEBLOQUER"
											class="nav-link sub-link {{ request()->routeIs('credit.deblocage') || (request()->routeIs('credit.index') && $creditStatutMenu === 'PRET_A_DEBLOQUER') ? 'active' : '' }}">
											<i class="fas fa-unlock-alt nav-icon text-success"></i>
											<p>Déblocage en attente</p>
										</a>
									</li>
								@endif

								@if($canCreditSupervision)
									<li class="nav-item">
										<a href="{{ route('credit.supervision') }}"
											class="nav-link sub-link {{ request()->routeIs('credit.supervision') ? 'active' : '' }}">
											<i class="fas fa-eye nav-icon text-danger"></i>
											<p>Supervision</p>
										</a>
									</li>
								@endif

									{{-- Recouvrement Auto (sous-menu de Crédits, réservé Admin/Gérant) --}}
								@if(in_array('EBEN-PER90', $userPermCodes ?? []))
									<li class="nav-item">
										<a href="{{ route('recouvrement.index') }}"
											class="nav-link sub-link {{ request()->is('recouvrement*') ? 'active' : '' }}">
											<i class="fas fa-sync-alt nav-icon text-warning"></i>
											<p>
												Recouvrement Auto
												@if(isset($alerteRecouvrementCount) && $alerteRecouvrementCount > 0)
													<span class="badge badge-danger right">{{ $alerteRecouvrementCount }}</span>
												@endif
											</p>
										</a>
									</li>
								@endif

							

						</ul>
					</li>
				@endif

				@php $hasAdminAccess = count(array_intersect(['EBEN-PER1', 'EBEN-PER2', 'EBEN-PER3', 'EBEN-PER4', 'EBEN-PER5', 'EBEN-PER20', 'EBEN-PER21', 'EBEN-PER42'], $userPermCodes ?? [])) > 0; @endphp
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
							@if(in_array('EBEN-PER1', $userPermCodes ?? []))
							<li class="nav-item">
								<a href="{{ route('administration.utilisateurs.liste') }}"
									class="nav-link sub-link {{ request()->is('administration/utilisateurs*') ? 'active' : '' }}">
									<i class="fas fa-list nav-icon"></i>
									<p>Liste des utilisateurs</p>
								</a>
							</li>
							@endif
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
									<a href="{{ route('administration.sms_test.index') }}"
										class="nav-link sub-link {{ request()->routeIs('administration.sms_test.*') ? 'active' : '' }}">
										<i class="fas fa-sms nav-icon"></i>
										<p>Test SMS</p>
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
							@if(in_array('EBEN-PER42', $userPermCodes ?? []))
							<li class="nav-item">
								<a href="{{ route('administration.journal_activite') }}"
									class="nav-link sub-link {{ request()->routeIs('administration.journal_activite') ? 'active' : '' }}">
									<i class="fas fa-book nav-icon"></i>
									<p>Journal d'activité</p>
								</a>
							</li>
							@endif
						<li class="nav-item">
							<span class="nav-link sub-link disabled-link" title="Fonctionnalité à venir" aria-disabled="true">
								<i class="fas fa-lock nav-icon"></i>
								<p>Paramètres de sécurité <span class="badge badge-secondary ml-1">Bientôt</span></p>
							</span>
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

	/* Liens désactivés (fonctionnalités pas encore développées) */
	.disabled-link {
		opacity: 0.5;
		cursor: not-allowed !important;
		pointer-events: none;
	}
	.disabled-link:hover {
		background-color: transparent !important;
		transform: none !important;
	}
</style>

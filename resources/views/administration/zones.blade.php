@extends('layouts.app')

@section('page_title', 'Zones & Portefeuilles')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Zones & Portefeuilles')

@section('content')
<div class="container-fluid">

    {{-- FLASH --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="icon fas fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif

    {{-- MINI-DASHBOARD --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-map-marker-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Zones</span>
                    <span class="info-box-number">{{ $stats['total_zones'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Zones avec agent</span>
                    <span class="info-box-number">{{ $stats['zones_avec_agent'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-wallet"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Portefeuilles</span>
                    <span class="info-box-number">{{ $stats['total_portefeuilles'] }}</span>
                    <small class="text-muted">Affectés: {{ $stats['portefeuilles_avec_agent'] ?? 0 }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-percent"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Taux comm. moyen</span>
                    <span class="info-box-number">{{ number_format($stats['taux_moyen'], 2, ',', ' ') }} %</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ONGLETS --}}
    <div class="card card-primary card-outline">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="zpTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-zones">
                        <i class="fas fa-map-marker-alt mr-1"></i> Zones
                        <span class="badge badge-primary badge-pill ml-1">{{ $stats['total_zones'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-portefeuilles">
                        <i class="fas fa-wallet mr-1"></i> Portefeuilles
                        <span class="badge badge-info badge-pill ml-1">{{ $stats['total_portefeuilles'] }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
        <div class="tab-content" id="zpTabsContent">

            {{-- ════════════════════ ONGLET ZONES ════════════════════ --}}
            <div class="tab-pane fade show active" id="tab-zones">
                <div class="row">

                    {{-- Formulaire ajout zone --}}
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> Nouvelle zone</h3>
                            </div>
                            <div class="card-body">
                                <form id="zoneForm">
                                    @csrf
                                    <div class="form-group">
                                        <label><i class="fas fa-tag mr-1 text-primary"></i> Nom de la zone <span class="text-danger">*</span></label>
                                        <input type="text" name="nom" class="form-control form-control-sm"
                                               placeholder="ex : Zone Nord, Zone Centre…" required autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-user-tie mr-1 text-muted"></i> Agent commercial <span class="text-muted">(optionnel)</span></label>
                                        <select name="agent_commercial_matricule" id="agentMatricule"
                                                class="form-control form-control-sm select2">
                                            <option value="">— Affecter plus tard —</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->matricule }}">
                                                    [{{ $agent->matricule }}] {{ $agent->nom }} {{ $agent->postnom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="zone-affectation-fields">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label><i class="fas fa-calendar-alt mr-1 text-muted"></i> Date début</label>
                                                <input type="date" name="date_debut" class="form-control form-control-sm"
                                                       value="{{ now()->toDateString() }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fas fa-calendar-times mr-1 text-muted"></i> Date fin</label>
                                                <input type="date" name="date_fin" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-toggle-on mr-1 text-muted"></i> Etat</label>
                                            <select name="etat_affectation" class="form-control form-control-sm">
                                                <option value="ACTIF" selected>ACTIF</option>
                                                <option value="INACTIF">INACTIF</option>
                                                <option value="TERMINE">TERMINE</option>
                                                <option value="EXPIRE">EXPIRE</option>
                                            </select>
                                            <small class="form-text text-muted">Si aucun agent n'est choisi, ces champs sont ignorés.</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-city mr-1 text-muted"></i> Commune <span class="text-danger">*</span></label>
                                        <select name="commune" id="communeSelect"
                                                class="form-control form-control-sm" required>
                                            <option value="">— Sélectionner —</option>
                                            <option value="Kanange">Kananga</option>
                                            <option value="Nganza">Nganza</option>
                                            <option value="Lukongo">Lukongo</option>
                                            <option value="Ndesha">Ndesha</option>
                                            <option value="Katoka">Katoka</option>
                                            <option value="autre">Autre…</option>
                                        </select>
                                        <input type="text" name="commune_autre" id="communeAutre"
                                               class="form-control form-control-sm mt-2"
                                               placeholder="Saisir la commune"
                                               style="display:none">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-comment-dots mr-1 text-muted"></i> Motif <span class="text-muted">(optionnel)</span></label>
                                        <input type="text" name="motif" class="form-control form-control-sm" maxlength="255"
                                               placeholder="Ex: Création initiale de la zone">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary" id="btnAddZone">
                                        <i class="fas fa-plus-circle mr-1"></i> Ajouter la zone
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Tableau zones --}}
                    <div class="col-md-8">
                        <div class="card card-info card-outline">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h3 class="card-title mb-0"><i class="fas fa-list mr-2"></i> Liste des zones</h3>
                                <span class="badge badge-primary badge-pill">{{ $stats['total_zones'] }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="px-2 pt-2">
                                    <input type="text" id="searchZones" class="form-control form-control-sm"
                                           placeholder="🔍 Rechercher une zone…">
                                </div>
                                <div class="table-responsive mt-1" style="max-height:420px;overflow-y:auto">
                                    <table class="table table-sm zp-table mb-0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width:35px">#</th>
                                                <th>Nom</th>
                                                <th>Agent commercial</th>
                                                <th>Commune</th>
                                                <th class="text-center" style="width:110px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="zonesTbody">
                                            @forelse($zones as $zone)
                                            @php
                                                $agentActif = $zone->affectationActive->agent ?? $zone->agent;
                                                $agentMatriculeActif = $zone->affectationActive->agent_matricule ?? $zone->agent_commercial_matricule;
                                                $affectationEdition = $zone->affectationActive ?: $zone->affectations->first();
                                                $etatZone = strtoupper((string) ($affectationEdition->Etat ?? 'NON_ASSIGNE'));
                                                $badgeZone = match ($etatZone) {
                                                    'ACTIF' => 'success',
                                                    'INACTIF' => 'secondary',
                                                    'TERMINE' => 'warning',
                                                    'EXPIRE' => 'danger',
                                                    default => 'light',
                                                };
                                            @endphp
                                            <tr class="js-zone-row" data-zone-code="{{ $zone->code_zone }}" data-zone-nom="{{ $zone->nom }}">
                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                <td><strong>{{ $zone->nom }}</strong></td>
                                                <td>
                                                    @if($agentActif)
                                                        <span class="badge badge-secondary mr-1">{{ $agentActif->matricule }}</span>
                                                        {{ $agentActif->nom }} {{ $agentActif->postnom }}
                                                        <div class="small text-muted mt-1">
                                                            <span class="badge badge-{{ $badgeZone }}">{{ $etatZone }}</span>
                                                            <span class="ml-1">
                                                                {{ optional($affectationEdition?->date_debut)->format('d/m/Y') ?? '—' }}
                                                                →
                                                                {{ optional($affectationEdition?->date_fin)->format('d/m/Y') ?? 'En cours' }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-danger small"><i class="fas fa-exclamation-circle mr-1"></i>Non assigné</span>
                                                    @endif
                                                </td>
                                                <td>{{ $zone->commune }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-xs btn-warning btn-edit-zone mr-1"
                                                            data-id="{{ $zone->code_zone }}"
                                                            data-nom="{{ $zone->nom }}"
                                                            data-commune="{{ $zone->commune }}"
                                                            data-agent="{{ $agentMatriculeActif }}"
                                                            data-date-debut="{{ optional($affectationEdition?->date_debut)->format('Y-m-d') ?? now()->toDateString() }}"
                                                            data-date-fin="{{ optional($affectationEdition?->date_fin)->format('Y-m-d') }}"
                                                            data-etat="{{ $affectationEdition->Etat ?? 'ACTIF' }}"
                                                            data-motif="{{ $affectationEdition->motif ?? '' }}"
                                                            title="Modifier / Réaffecter">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-xs btn-danger btn-delete-zone"
                                                            data-id="{{ $zone->code_zone }}"
                                                            data-nom="{{ $zone->nom }}"
                                                            title="Supprimer cette zone">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucune zone enregistrée.
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-history mr-1"></i>
                                    Affectations de la zone sélectionnée
                                    <span id="selectedZoneLabel" class="badge badge-dark ml-2">Aucune zone sélectionnée</span>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height:260px;overflow-y:auto">
                                    <table class="table table-sm mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Zone</th>
                                                <th>Agent</th>
                                                <th>Début</th>
                                                <th>Fin</th>
                                                <th>Etat</th>
                                                <th>Motif</th>
                                                <th>Créé le</th>
                                            </tr>
                                        </thead>
                                        <tbody id="zoneAffectationsByZoneBody">
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-3">Clique sur une zone pour afficher ses affectations.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /tab-zones --}}

            {{-- ══════════════════ ONGLET PORTEFEUILLES ══════════════════ --}}
            <div class="tab-pane fade" id="tab-portefeuilles">
                <div class="row">

                    {{-- Formulaire ajout portefeuille --}}
                    <div class="col-md-4">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> Nouveau portefeuille</h3>
                            </div>
                            <div class="card-body">
                                <form id="portefeuilleFm">
                                    @csrf
                                    <div class="form-group">
                                        <label><i class="fas fa-wallet mr-1 text-info"></i> Nom <span class="text-danger">*</span></label>
                                        <input type="text" name="nom_portefeuille" class="form-control form-control-sm"
                                               placeholder="ex : Portefeuille A" required autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-user-tie mr-1 text-muted"></i> Agent commercial <span class="text-muted">(optionnel)</span></label>
                                        <select name="agent_matricule" id="agentPortefeuille"
                                                class="form-control form-control-sm select2">
                                            <option value="">— Affecter plus tard —</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->matricule }}">
                                                    [{{ $agent->matricule }}] {{ $agent->nom }} {{ $agent->postnom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="portefeuille-affectation-fields">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label><i class="fas fa-calendar-alt mr-1 text-muted"></i> Date début</label>
                                                <input type="date" name="date_debut" class="form-control form-control-sm"
                                                       value="{{ now()->toDateString() }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label><i class="fas fa-calendar-times mr-1 text-muted"></i> Date fin</label>
                                                <input type="date" name="date_fin" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-toggle-on mr-1 text-muted"></i> Etat</label>
                                            <select name="etat_affectation" class="form-control form-control-sm">
                                                <option value="ACTIF" selected>ACTIF</option>
                                                <option value="INACTIF">INACTIF</option>
                                                <option value="TERMINE">TERMINE</option>
                                                <option value="EXPIRE">EXPIRE</option>
                                            </select>
                                            <small class="form-text text-muted">Si aucun agent n'est choisi, ces champs sont ignorés.</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-percent mr-1 text-muted"></i> Taux de commission (%) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" name="taux_commission_agent"
                                               class="form-control form-control-sm" placeholder="0.00" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-comment-dots mr-1 text-muted"></i> Motif <span class="text-muted">(optionnel)</span></label>
                                        <input type="text" name="motif" class="form-control form-control-sm" maxlength="255"
                                               placeholder="Ex: Création initiale du portefeuille">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-info" id="btnAddPortefeuille">
                                        <i class="fas fa-plus-circle mr-1"></i> Ajouter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Tableau portefeuilles --}}
                    <div class="col-md-8">
                        <div class="card card-info card-outline">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h3 class="card-title mb-0"><i class="fas fa-list mr-2"></i> Liste des portefeuilles</h3>
                                <span class="badge badge-info badge-pill">{{ $stats['total_portefeuilles'] }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="px-2 pt-2">
                                    <input type="text" id="searchPortefeuilles" class="form-control form-control-sm"
                                           placeholder="🔍 Rechercher un portefeuille…">
                                </div>
                                <div class="table-responsive mt-1" style="max-height:420px;overflow-y:auto">
                                    <table class="table table-sm zp-table mb-0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width:35px">#</th>
                                                <th>Nom</th>
                                                <th>Agent</th>
                                                <th class="text-center" style="width:100px">Taux (%)</th>
                                                <th class="text-center" style="width:110px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="portefeuillesTbody">
                                            @forelse($portefeuilles as $pf)
                                            @php
                                                $agentPfActif = $pf->affectationActive->agent ?? $pf->agent;
                                                $agentPfMatriculeActif = $pf->affectationActive->agent_matricule ?? $pf->agent_matricule;
                                                $affectationPfEdition = $pf->affectationActive ?: $pf->affectations->first();
                                                $etatPortefeuille = strtoupper((string) ($affectationPfEdition->Etat ?? 'NON_ASSIGNE'));
                                                $badgePortefeuille = match ($etatPortefeuille) {
                                                    'ACTIF' => 'success',
                                                    'INACTIF' => 'secondary',
                                                    'TERMINE' => 'warning',
                                                    'EXPIRE' => 'danger',
                                                    default => 'light',
                                                };
                                            @endphp
                                            <tr class="js-portefeuille-row" data-portefeuille-id="{{ $pf->id }}" data-portefeuille-nom="{{ $pf->nom_portefeuille }}">
                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                <td><strong>{{ $pf->nom_portefeuille }}</strong></td>
                                                <td>
                                                    @if($agentPfActif)
                                                        <span class="badge badge-secondary mr-1">{{ $agentPfActif->matricule }}</span>
                                                        {{ $agentPfActif->nom }} {{ $agentPfActif->prenom }}
                                                        <div class="small text-muted mt-1">
                                                            <span class="badge badge-{{ $badgePortefeuille }}">{{ $etatPortefeuille }}</span>
                                                            <span class="ml-1">
                                                                {{ optional($affectationPfEdition?->date_debut)->format('d/m/Y') ?? '—' }}
                                                                →
                                                                {{ optional($affectationPfEdition?->date_fin)->format('d/m/Y') ?? 'En cours' }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-danger small"><i class="fas fa-exclamation-circle mr-1"></i>Non assigné</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $pf->taux_commission_agent > 0 ? 'success' : 'secondary' }}">
                                                        {{ number_format($pf->taux_commission_agent, 2, ',', ' ') }} %
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-xs btn-warning btn-edit-portefeuille mr-1"
                                                            data-id="{{ $pf->id }}"
                                                            data-nom="{{ $pf->nom_portefeuille }}"
                                                            data-agent="{{ $agentPfMatriculeActif }}"
                                                            data-taux="{{ $pf->taux_commission_agent }}"
                                                            data-date-debut="{{ optional($affectationPfEdition?->date_debut)->format('Y-m-d') ?? now()->toDateString() }}"
                                                            data-date-fin="{{ optional($affectationPfEdition?->date_fin)->format('Y-m-d') }}"
                                                            data-etat="{{ $affectationPfEdition->Etat ?? 'ACTIF' }}"
                                                            data-motif="{{ $affectationPfEdition->motif ?? '' }}"
                                                            title="Modifier / Réaffecter">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-xs btn-danger btn-delete-portefeuille"
                                                            data-id="{{ $pf->id }}"
                                                            data-nom="{{ $pf->nom_portefeuille }}"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucun portefeuille.
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-history mr-1"></i>
                                    Affectations du portefeuille sélectionné
                                    <span id="selectedPortefeuilleLabel" class="badge badge-dark ml-2">Aucun portefeuille sélectionné</span>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height:260px;overflow-y:auto">
                                    <table class="table table-sm mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Portefeuille</th>
                                                <th>Agent</th>
                                                <th>Début</th>
                                                <th>Fin</th>
                                                <th>Etat</th>
                                                <th>Motif</th>
                                                <th>Créé le</th>
                                            </tr>
                                        </thead>
                                        <tbody id="portefeuilleAffectationsByPortefeuilleBody">
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-3">Clique sur un portefeuille pour afficher ses affectations.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /tab-portefeuilles --}}

        </div>{{-- /tab-content --}}
        </div>{{-- /card-body --}}
    </div>{{-- /card --}}

</div>{{-- /container-fluid --}}

<div class="modal fade" id="editZoneModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> Modifier / Réaffecter la zone</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="zoneEditForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_zone_code" name="code_zone">

                    <div class="form-group">
                        <label>Code zone</label>
                        <input type="text" id="edit_zone_code_label" class="form-control form-control-sm" readonly>
                    </div>

                    <div class="form-group">
                        <label>Nom de la zone</label>
                        <input type="text" id="edit_nom" name="nom" class="form-control form-control-sm" required>
                    </div>

                    <div class="form-group">
                        <label>Commune</label>
                        <input type="text" id="edit_commune" name="commune" class="form-control form-control-sm" required>
                    </div>

                    <div class="form-group">
                        <label>Agent commercial</label>
                        <select id="edit_agent" name="agent_commercial_matricule" class="form-control form-control-sm">
                            <option value="">— Affecter plus tard —</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->matricule }}">
                                    [{{ $agent->matricule }}] {{ $agent->nom }} {{ $agent->postnom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="zone-affectation-fields-edit">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Date début</label>
                                <input type="date" id="edit_date_debut" name="date_debut" class="form-control form-control-sm">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Date fin</label>
                                <input type="date" id="edit_date_fin" name="date_fin" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Etat</label>
                            <select id="edit_etat_affectation" name="etat_affectation" class="form-control form-control-sm">
                                <option value="ACTIF">ACTIF</option>
                                <option value="INACTIF">INACTIF</option>
                                <option value="TERMINE">TERMINE</option>
                                <option value="EXPIRE">EXPIRE</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label>Motif (optionnel)</label>
                        <input type="text" id="edit_motif" name="motif" class="form-control form-control-sm" maxlength="255"
                               placeholder="Ex: Réaffectation commerciale trimestrielle">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-warning" id="btnUpdateZone">
                        <i class="fas fa-save mr-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editPortefeuilleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> Modifier / Réaffecter le portefeuille</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="portefeuilleEditForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_pf_id" name="id">

                    <div class="form-group">
                        <label>ID portefeuille</label>
                        <input type="text" id="edit_pf_id_label" class="form-control form-control-sm" readonly>
                    </div>

                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" id="edit_pf_nom" name="nom_portefeuille" class="form-control form-control-sm" required>
                    </div>

                    <div class="form-group">
                        <label>Agent commercial <span class="text-muted">(optionnel)</span></label>
                        <select id="edit_pf_agent" name="agent_matricule" class="form-control form-control-sm">
                            <option value="">— Affecter plus tard —</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->matricule }}">
                                    [{{ $agent->matricule }}] {{ $agent->nom }} {{ $agent->postnom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="portefeuille-affectation-fields-edit">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Date début</label>
                                <input type="date" id="edit_pf_date_debut" name="date_debut" class="form-control form-control-sm">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Date fin</label>
                                <input type="date" id="edit_pf_date_fin" name="date_fin" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Etat</label>
                            <select id="edit_pf_etat_affectation" name="etat_affectation" class="form-control form-control-sm">
                                <option value="ACTIF">ACTIF</option>
                                <option value="INACTIF">INACTIF</option>
                                <option value="TERMINE">TERMINE</option>
                                <option value="EXPIRE">EXPIRE</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Taux de commission (%)</label>
                        <input type="number" step="0.01" min="0" id="edit_pf_taux" name="taux_commission_agent" class="form-control form-control-sm" required>
                    </div>

                    <div class="form-group mb-0">
                        <label>Motif (optionnel)</label>
                        <input type="text" id="edit_pf_motif" name="motif" class="form-control form-control-sm" maxlength="255"
                               placeholder="Ex: Rééquilibrage portefeuille">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-info" id="btnUpdatePortefeuille">
                        <i class="fas fa-save mr-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .zp-table thead th {
        background-color: #2c3136 !important;
        color: #c2c7d0 !important;
        border-color: #3d4349 !important;
        font-size: .8rem;
        white-space: nowrap;
        vertical-align: middle;
    }
    .zp-table tbody tr:hover > td {
        background-color: rgba(0, 123, 255, 0.12) !important;
    }
    .zp-table td { vertical-align: middle; font-size: .85rem; }
    .info-box { min-height: 72px; }
    .info-box-icon { line-height: 72px; width: 70px; font-size: 1.6rem; }
    .info-box-content { padding: 8px 10px; }
    .assignment-fields-disabled {
        opacity: .6;
        pointer-events: none;
    }
    .js-zone-row.zone-row-selected > td {
        background-color: rgba(255, 193, 7, 0.18) !important;
    }
    .js-portefeuille-row.portefeuille-row-selected > td {
        background-color: rgba(23, 162, 184, 0.18) !important;
    }
</style>
@endpush

@php
    $zoneAffectationsByZoneData = $zones->mapWithKeys(function ($zone) {
        return [
            $zone->code_zone => [
                'code_zone' => $zone->code_zone,
                'nom' => $zone->nom,
                'affectations' => $zone->affectations->map(function ($affectation) {
                    return [
                        'agent_matricule' => $affectation->agent_matricule,
                        'agent_nom' => $affectation->agent ? $affectation->agent->nom : null,
                        'agent_postnom' => $affectation->agent ? $affectation->agent->postnom : null,
                        'date_debut' => optional($affectation->date_debut)->format('Y-m-d'),
                        'date_fin' => optional($affectation->date_fin)->format('Y-m-d'),
                        'etat' => strtoupper((string) $affectation->Etat),
                        'motif' => $affectation->motif,
                        'created_at' => optional($affectation->created_at)->format('Y-m-d H:i:s'),
                    ];
                })->values()->all(),
            ],
        ];
    })->toArray();

    $portefeuilleAffectationsByPortefeuilleData = $portefeuilles->mapWithKeys(function ($portefeuille) {
        return [
            (string) $portefeuille->id => [
                'id' => (string) $portefeuille->id,
                'nom' => $portefeuille->nom_portefeuille,
                'affectations' => $portefeuille->affectations->map(function ($affectation) {
                    return [
                        'agent_matricule' => $affectation->agent_matricule,
                        'agent_nom' => $affectation->agent ? $affectation->agent->nom : null,
                        'agent_postnom' => $affectation->agent ? $affectation->agent->postnom : null,
                        'date_debut' => optional($affectation->date_debut)->format('Y-m-d'),
                        'date_fin' => optional($affectation->date_fin)->format('Y-m-d'),
                        'etat' => strtoupper((string) $affectation->Etat),
                        'motif' => $affectation->motif,
                        'created_at' => optional($affectation->created_at)->format('Y-m-d H:i:s'),
                    ];
                })->values()->all(),
            ],
        ];
    })->toArray();
@endphp

@push('js')
<script>
(function () {
    'use strict';

    var zoneAffectationsByZone = @json($zoneAffectationsByZoneData);
    var portefeuilleAffectationsByPortefeuille = @json($portefeuilleAffectationsByPortefeuilleData);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept'      : 'application/json'
        }
    });

    $(function () {

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatDateFr(dateStr) {
            if (!dateStr) return '—';
            var datePart = String(dateStr).split(' ')[0];
            var parts = datePart.split('-');
            if (parts.length !== 3) return dateStr;
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }

        function formatDateTimeFr(dateStr) {
            if (!dateStr) return '—';
            var chunks = String(dateStr).split(' ');
            var dateLabel = formatDateFr(chunks[0]);
            return chunks[1] ? (dateLabel + ' ' + chunks[1].slice(0, 5)) : dateLabel;
        }

        function etatBadgeClass(etat) {
            var value = String(etat || '').toUpperCase();
            if (value === 'ACTIF') return 'success';
            if (value === 'INACTIF') return 'secondary';
            if (value === 'TERMINE') return 'warning';
            if (value === 'EXPIRE') return 'danger';
            return 'light';
        }

        function renderZoneAffectations(codeZone, nomZone) {
            var container = $('#zoneAffectationsByZoneBody');
            var label = $('#selectedZoneLabel');
            var zoneData = zoneAffectationsByZone[codeZone] || null;
            var affectations = zoneData ? (zoneData.affectations || []) : [];

            label.text((nomZone || codeZone || 'Zone') + (codeZone ? (' [' + codeZone + ']') : ''));

            if (!affectations.length) {
                container.html('<tr><td colspan="7" class="text-center text-muted py-3">Aucune affectation trouvée pour cette zone.</td></tr>');
                return;
            }

            var rows = affectations.map(function (a) {
                var etat = String(a.etat || '').toUpperCase() || '—';
                var agentLabel = a.agent_nom
                    ? ('<span class="badge badge-secondary mr-1">' + escapeHtml(a.agent_matricule || '—') + '</span>' + escapeHtml((a.agent_nom || '') + ' ' + (a.agent_postnom || '')))
                    : escapeHtml(a.agent_matricule || '—');

                return '<tr>'
                    + '<td>' + escapeHtml(nomZone || codeZone || '—') + '</td>'
                    + '<td>' + agentLabel + '</td>'
                    + '<td>' + escapeHtml(formatDateFr(a.date_debut)) + '</td>'
                    + '<td>' + escapeHtml(a.date_fin ? formatDateFr(a.date_fin) : 'En cours') + '</td>'
                    + '<td><span class="badge badge-' + etatBadgeClass(etat) + '">' + escapeHtml(etat) + '</span></td>'
                    + '<td>' + escapeHtml(a.motif || '—') + '</td>'
                    + '<td>' + escapeHtml(formatDateTimeFr(a.created_at)) + '</td>'
                    + '</tr>';
            }).join('');

            container.html(rows);
        }

        function renderPortefeuilleAffectations(portefeuilleId, nomPortefeuille) {
            var container = $('#portefeuilleAffectationsByPortefeuilleBody');
            var label = $('#selectedPortefeuilleLabel');
            var portefeuilleData = portefeuilleAffectationsByPortefeuille[String(portefeuilleId)] || null;
            var affectations = portefeuilleData ? (portefeuilleData.affectations || []) : [];

            label.text((nomPortefeuille || ('#' + portefeuilleId) || 'Portefeuille') + (portefeuilleId ? (' [#' + portefeuilleId + ']') : ''));

            if (!affectations.length) {
                container.html('<tr><td colspan="7" class="text-center text-muted py-3">Aucune affectation trouvée pour ce portefeuille.</td></tr>');
                return;
            }

            var rows = affectations.map(function (a) {
                var etat = String(a.etat || '').toUpperCase() || '—';
                var agentLabel = a.agent_nom
                    ? ('<span class="badge badge-secondary mr-1">' + escapeHtml(a.agent_matricule || '—') + '</span>' + escapeHtml((a.agent_nom || '') + ' ' + (a.agent_postnom || '')))
                    : escapeHtml(a.agent_matricule || '—');

                return '<tr>'
                    + '<td>' + escapeHtml(nomPortefeuille || ('#' + portefeuilleId) || '—') + '</td>'
                    + '<td>' + agentLabel + '</td>'
                    + '<td>' + escapeHtml(formatDateFr(a.date_debut)) + '</td>'
                    + '<td>' + escapeHtml(a.date_fin ? formatDateFr(a.date_fin) : 'En cours') + '</td>'
                    + '<td><span class="badge badge-' + etatBadgeClass(etat) + '">' + escapeHtml(etat) + '</span></td>'
                    + '<td>' + escapeHtml(a.motif || '—') + '</td>'
                    + '<td>' + escapeHtml(formatDateTimeFr(a.created_at)) + '</td>'
                    + '</tr>';
            }).join('');

            container.html(rows);
        }

        // Auto-close flash
        // Auto-close flash
        // (géré par showSystemMessage — pas besoin de timeout)

        // ── Select2 ──────────────────────────────────────────────────────────
        var s2 = { theme: 'bootstrap4', allowClear: true, width: '100%',
                   language: { noResults: function () { return 'Aucun résultat'; } } };
        $('#agentMatricule').select2($.extend({}, s2, { placeholder: 'Affecter plus tard…' }));
        $('#agentPortefeuille').select2($.extend({}, s2, { placeholder: 'Affecter plus tard…' }));
        $('#edit_agent').select2($.extend({}, s2, { placeholder: 'Affecter plus tard…', dropdownParent: $('#editZoneModal') }));
        $('#edit_pf_agent').select2($.extend({}, s2, { placeholder: 'Affecter plus tard…', dropdownParent: $('#editPortefeuilleModal') }));

        function toggleAssignmentFields(selectSelector, wrapperSelector) {
            var hasAgent = !!$(selectSelector).val();
            $(wrapperSelector).toggleClass('assignment-fields-disabled', !hasAgent);
            $(wrapperSelector).find('input, select').prop('disabled', !hasAgent);
        }

        toggleAssignmentFields('#agentMatricule', '.zone-affectation-fields');
        toggleAssignmentFields('#agentPortefeuille', '.portefeuille-affectation-fields');
        toggleAssignmentFields('#edit_agent', '.zone-affectation-fields-edit');
        toggleAssignmentFields('#edit_pf_agent', '.portefeuille-affectation-fields-edit');

        $('#agentMatricule').on('change', function () {
            toggleAssignmentFields('#agentMatricule', '.zone-affectation-fields');
        });

        $('#agentPortefeuille').on('change', function () {
            toggleAssignmentFields('#agentPortefeuille', '.portefeuille-affectation-fields');
        });

        $('#edit_agent').on('change', function () {
            toggleAssignmentFields('#edit_agent', '.zone-affectation-fields-edit');
        });

        $('#edit_pf_agent').on('change', function () {
            toggleAssignmentFields('#edit_pf_agent', '.portefeuille-affectation-fields-edit');
        });

        // ── Commune "Autre" ──────────────────────────────────────────────────
        $('#communeSelect').on('change', function () {
            $('#communeAutre').toggle(this.value === 'autre');
            if (this.value !== 'autre') $('#communeAutre').val('');
        });

        // ── Live search Zones ────────────────────────────────────────────────
        $('#searchZones').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#zonesTbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
            });
        });

        // ── Clic sur une zone -> affichage de ses affectations ─────────────
        $(document).on('click', '.js-zone-row', function (e) {
            if ($(e.target).closest('button, a, input, select, .btn').length) {
                return;
            }

            var codeZone = $(this).data('zone-code');
            var nomZone = $(this).data('zone-nom');

            $('.js-zone-row').removeClass('zone-row-selected');
            $(this).addClass('zone-row-selected');

            renderZoneAffectations(codeZone, nomZone);
        });

        // ── Live search Portefeuilles ────────────────────────────────────────
        $('#searchPortefeuilles').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#portefeuillesTbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
            });
        });

        // ── Clic sur un portefeuille -> affichage de ses affectations ─────
        $(document).on('click', '.js-portefeuille-row', function (e) {
            if ($(e.target).closest('button, a, input, select, .btn').length) {
                return;
            }

            var portefeuilleId = $(this).data('portefeuille-id');
            var nomPortefeuille = $(this).data('portefeuille-nom');

            $('.js-portefeuille-row').removeClass('portefeuille-row-selected');
            $(this).addClass('portefeuille-row-selected');

            renderPortefeuilleAffectations(portefeuilleId, nomPortefeuille);
        });

        // ── AJOUTER une zone ─────────────────────────────────────────────────
        $('#zoneForm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnAddZone').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');
            $.ajax({
                type    : 'POST',
                url     : '{{ route("administration.zones.store") }}',
                data    : $(this).serialize(),
                dataType: 'json'
            })
            .done(function (data) {
                if (data.success) {
                    showSystemMessage('success', data.message || 'Zone ajoutée avec succès.');
                    setTimeout(function () { location.reload(); }, 900);
                } else {
                    showSystemMessage('error', data.message || 'Erreur.');
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter la zone');
                }
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Ajout zone');
                $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter la zone');
            });
        });

        // ── SUPPRIMER une zone ───────────────────────────────────────────────
        $(document).on('click', '.btn-delete-zone', function () {
            var id  = $(this).data('id');
            var nom = $(this).data('nom');
            var $tr = $(this).closest('tr');
            var url = '{{ route("administration.zones.destroy", ["code_zone" => "__ID__"]) }}'.replace('__ID__', id);
            showUniversalConfirm('Supprimer la zone <strong>« ' + nom + ' »</strong> ?', function () {
                $.ajax({
                    type    : 'POST',
                    url     : url,
                    data    : { _method: 'DELETE' },
                    dataType: 'json'
                })
                .done(function (data) {
                    if (data.success) {
                        showSystemMessage('success', data.message || 'Zone supprimée.');
                        $tr.fadeOut(400, function () { $(this).remove(); });
                    } else {
                        showSystemMessage('error', data.message || 'Erreur.');
                    }
                })
                .fail(function (xhr) {
                    handleAjaxFail(xhr, 'Suppression zone');
                });
            }, 'Confirmation');
        });

        // ── OUVRIR le modal de modification zone ────────────────────────────
        $(document).on('click', '.btn-edit-zone', function () {
            $('#edit_zone_code').val($(this).data('id'));
            $('#edit_zone_code_label').val($(this).data('id'));
            $('#edit_nom').val($(this).data('nom'));
            $('#edit_commune').val($(this).data('commune'));
            $('#edit_agent').val($(this).data('agent')).trigger('change');
            $('#edit_date_debut').val($(this).data('date-debut'));
            $('#edit_date_fin').val($(this).data('date-fin'));
            $('#edit_etat_affectation').val($(this).data('etat'));
            $('#edit_motif').val($(this).data('motif'));

            $('#editZoneModal').modal('show');
        });

        // ── MODIFIER / RÉAFFECTER une zone ─────────────────────────────────
        $('#zoneEditForm').on('submit', function (e) {
            e.preventDefault();

            var codeZone = $('#edit_zone_code').val();
            var url = '{{ route("administration.zones.update", ["code_zone" => "__ID__"]) }}'.replace('__ID__', codeZone);
            var $btn = $('#btnUpdateZone').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enregistrement…');

            $.ajax({
                type: 'POST',
                url: url,
                data: $(this).serialize(),
                dataType: 'json'
            })
            .done(function (data) {
                if (data.success) {
                    showSystemMessage('success', data.message || 'Zone mise à jour.');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    showSystemMessage('error', data.message || 'Erreur de mise à jour.');
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Enregistrer');
                }
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Modification zone');
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Enregistrer');
            });
        });

        // ── AJOUTER un portefeuille ──────────────────────────────────────────
        $('#portefeuilleFm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnAddPortefeuille').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');
            $.ajax({
                type    : 'POST',
                url     : '{{ route("administration.portefeuilles.store") }}',
                data    : $(this).serialize(),
                dataType: 'json'
            })
            .done(function (data) {
                if (data.success) {
                    showSystemMessage('success', data.message || 'Portefeuille enregistré.');
                    setTimeout(function () { location.reload(); }, 900);
                } else {
                    showSystemMessage('error', data.message || 'Erreur.');
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter');
                }
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Ajout portefeuille');
                $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter');
            });
        });

        // ── OUVRIR le modal de modification portefeuille ───────────────────
        $(document).on('click', '.btn-edit-portefeuille', function () {
            $('#edit_pf_id').val($(this).data('id'));
            $('#edit_pf_id_label').val($(this).data('id'));
            $('#edit_pf_nom').val($(this).data('nom'));
            $('#edit_pf_agent').val($(this).data('agent')).trigger('change');
            $('#edit_pf_taux').val($(this).data('taux'));
            $('#edit_pf_date_debut').val($(this).data('date-debut'));
            $('#edit_pf_date_fin').val($(this).data('date-fin'));
            $('#edit_pf_etat_affectation').val($(this).data('etat'));
            $('#edit_pf_motif').val($(this).data('motif'));

            $('#editPortefeuilleModal').modal('show');
        });

        // ── MODIFIER / RÉAFFECTER un portefeuille ──────────────────────────
        $('#portefeuilleEditForm').on('submit', function (e) {
            e.preventDefault();

            var id = $('#edit_pf_id').val();
            var url = '{{ route("administration.portefeuilles.update", ["id" => "__ID__"]) }}'.replace('__ID__', id);
            var $btn = $('#btnUpdatePortefeuille').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enregistrement…');

            $.ajax({
                type: 'POST',
                url: url,
                data: $(this).serialize(),
                dataType: 'json'
            })
            .done(function (data) {
                if (data.success) {
                    showSystemMessage('success', data.message || 'Portefeuille mis à jour.');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    showSystemMessage('error', data.message || 'Erreur de mise à jour.');
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Enregistrer');
                }
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Modification portefeuille');
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Enregistrer');
            });
        });

        // ── SUPPRIMER un portefeuille ────────────────────────────────────────
        $(document).on('click', '.btn-delete-portefeuille', function () {
            var id  = $(this).data('id');
            var nom = $(this).data('nom');
            var $tr = $(this).closest('tr');
            var url = '{{ route("administration.portefeuilles.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id);
            showUniversalConfirm('Supprimer le portefeuille <strong>« ' + nom + ' »</strong> ?', function () {
                $.ajax({
                    type    : 'POST',
                    url     : url,
                    data    : { _method: 'DELETE' },
                    dataType: 'json'
                })
                .done(function (data) {
                    if (data.success) {
                        showSystemMessage('success', data.message || 'Portefeuille supprimé.');
                        $tr.fadeOut(400, function () { $(this).remove(); });
                    } else {
                        showSystemMessage('error', data.message || 'Erreur.');
                    }
                })
                .fail(function (xhr) {
                    handleAjaxFail(xhr, 'Suppression portefeuille');
                });
            }, 'Confirmation');
        });

        // ── Activer le bon onglet si paramètre URL #tab-portefeuilles ────────
        var hash = window.location.hash;
        if (hash) {
            $('#zpTabs a[href="' + hash + '"]').tab('show');
        }

    });
}());
</script>
@endpush

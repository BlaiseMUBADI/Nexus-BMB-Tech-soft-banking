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
                                        <label><i class="fas fa-user-tie mr-1 text-muted"></i> Agent commercial <span class="text-danger">*</span></label>
                                        <select name="agent_commercial_matricule" id="agentMatricule"
                                                class="form-control form-control-sm select2" required>
                                            <option value="">— Sélectionner un agent —</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->matricule }}">
                                                    [{{ $agent->matricule }}] {{ $agent->nom }} {{ $agent->postnom }}
                                                </option>
                                            @endforeach
                                        </select>
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
                                                <th class="text-center" style="width:65px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="zonesTbody">
                                            @forelse($zones as $zone)
                                            <tr>
                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                <td><strong>{{ $zone->nom }}</strong></td>
                                                <td>
                                                    @if($zone->agent)
                                                        <span class="badge badge-secondary mr-1">{{ $zone->agent->matricule }}</span>
                                                        {{ $zone->agent->nom }} {{ $zone->agent->postnom }}
                                                    @else
                                                        <span class="text-danger small"><i class="fas fa-exclamation-circle mr-1"></i>Non assigné</span>
                                                    @endif
                                                </td>
                                                <td>{{ $zone->commune }}</td>
                                                <td class="text-center">
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
                                        <label><i class="fas fa-user-tie mr-1 text-muted"></i> Agent commercial <span class="text-danger">*</span></label>
                                        <select name="agent_matricule" id="agentPortefeuille"
                                                class="form-control form-control-sm select2" required>
                                            <option value="">— Sélectionner un agent —</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->matricule }}">
                                                    [{{ $agent->matricule }}] {{ $agent->nom }} {{ $agent->postnom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-percent mr-1 text-muted"></i> Taux de commission (%) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" name="taux_commission_agent"
                                               class="form-control form-control-sm" placeholder="0.00" required>
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
                                                <th class="text-center" style="width:65px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="portefeuillesTbody">
                                            @forelse($portefeuilles as $pf)
                                            <tr>
                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                <td><strong>{{ $pf->nom_portefeuille }}</strong></td>
                                                <td>
                                                    @if($pf->agent)
                                                        <span class="badge badge-secondary mr-1">{{ $pf->agent_matricule }}</span>
                                                        {{ $pf->agent->nom }} {{ $pf->agent->prenom }}
                                                    @else
                                                        <span class="text-danger small">[{{ $pf->agent_matricule }}] Inconnu</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $pf->taux_commission_agent > 0 ? 'success' : 'secondary' }}">
                                                        {{ number_format($pf->taux_commission_agent, 2, ',', ' ') }} %
                                                    </span>
                                                </td>
                                                <td class="text-center">
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
            </div>{{-- /tab-portefeuilles --}}

        </div>{{-- /tab-content --}}
        </div>{{-- /card-body --}}
    </div>{{-- /card --}}

</div>{{-- /container-fluid --}}
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
</style>
@endpush

@push('js')
<script>
(function () {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(function () {

        // Auto-close flash
        // Auto-close flash
        // (géré par showSystemMessage — pas besoin de timeout)

        // ── Select2 ──────────────────────────────────────────────────────────
        var s2 = { theme: 'bootstrap4', allowClear: true, width: '100%',
                   language: { noResults: function () { return 'Aucun résultat'; } } };
        $('#agentMatricule').select2($.extend({}, s2, { placeholder: 'Sélectionner un agent…' }));
        $('#agentPortefeuille').select2($.extend({}, s2, { placeholder: 'Sélectionner un agent…' }));

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

        // ── Live search Portefeuilles ────────────────────────────────────────
        $('#searchPortefeuilles').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#portefeuillesTbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
            });
        });

        // ── AJOUTER une zone ─────────────────────────────────────────────────
        $('#zoneForm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnAddZone').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');
            $.post('{{ route("administration.zones.store") }}', $(this).serialize())
                .done(function () {
                    showSystemMessage('success', 'Zone ajoutée avec succès.');
                    setTimeout(function () { location.reload(); }, 900);
                })
                .fail(function (xhr) {
                    showSystemMessage('error', xhr.responseJSON?.message ?? 'Erreur lors de l\'ajout.');
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter la zone');
                });
        });

        // ── SUPPRIMER une zone ───────────────────────────────────────────────
        $(document).on('click', '.btn-delete-zone', function () {
            var id  = $(this).data('id');
            var nom = $(this).data('nom');
            var url = '{{ route("administration.zones.destroy", ["code_zone" => "__ID__"]) }}'.replace('__ID__', id);
            showUniversalConfirm('Supprimer la zone <strong>« ' + nom + ' »</strong> ?', function () {
                $.post(url, { _method: 'DELETE' })
                    .done(function () {
                        showSystemMessage('success', 'Zone supprimée.');
                        setTimeout(function () { location.reload(); }, 900);
                    })
                    .fail(function (xhr) {
                        showSystemMessage('error', xhr.responseJSON?.message ?? 'Suppression impossible.');
                    });
            }, 'Confirmation');
        });

        // ── AJOUTER un portefeuille ──────────────────────────────────────────
        $('#portefeuilleFm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnAddPortefeuille').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');
            $.post('{{ route("administration.portefeuilles.store") }}', $(this).serialize())
                .done(function () {
                    showSystemMessage('success', 'Portefeuille enregistré.');
                    setTimeout(function () { location.reload(); }, 900);
                })
                .fail(function (xhr) {
                    showSystemMessage('error', xhr.responseJSON?.message ?? 'Erreur lors de l\'enregistrement.');
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter');
                });
        });

        // ── SUPPRIMER un portefeuille ────────────────────────────────────────
        $(document).on('click', '.btn-delete-portefeuille', function () {
            var id  = $(this).data('id');
            var nom = $(this).data('nom');
            var url = '{{ route("administration.portefeuilles.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id);
            showUniversalConfirm('Supprimer le portefeuille <strong>« ' + nom + ' »</strong> ?', function () {
                $.post(url, { _method: 'DELETE' })
                    .done(function () {
                        showSystemMessage('success', 'Portefeuille supprimé.');
                        setTimeout(function () { location.reload(); }, 900);
                    })
                    .fail(function (xhr) {
                        showSystemMessage('error', xhr.responseJSON?.message ?? 'Suppression impossible.');
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

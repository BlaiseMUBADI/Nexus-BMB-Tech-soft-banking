@extends('layouts.app')

@section('page_title', 'Affectations des agents')
@section('breadcrumb_parent', 'Ressources Humaines')
@section('breadcrumb', 'Affectations')



@section('content')
<div class="container-fluid">

    {{-- ── Messages flash ────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="icon fas fa-check mr-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ── Barre de sélection en cours ────────────────────────── --}}
    <div id="selectionBar" class="alert border mb-3 py-2">
        <div class="d-flex align-items-center flex-wrap" style="gap:12px">
            <small class="text-muted font-weight-bold mr-2">Sélection :</small>
            <span class="sel-chip" id="chipAgent">
                <i class="fas fa-user mr-1 text-primary"></i>
                <span id="chipAgentText">Aucun agent</span>
            </span>
            <i class="fas fa-link text-muted"></i>
            <span class="sel-chip" id="chipPoste">
                <i class="fas fa-briefcase mr-1 text-success"></i>
                <span id="chipPosteText">Aucun poste</span>
            </span>
            <button class="btn btn-sm btn-outline-secondary ml-auto" id="btnReset" style="display:none">
                <i class="fas fa-times mr-1"></i> Réinitialiser
            </button>
        </div>
    </div>

    {{-- ── Ligne 1 : Agents (gauche) + Postes (droite) ─────────── --}}
    <div class="row">

        {{-- Agents --}}
        <div class="col-lg-6 col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i> Agents
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary badge-pill">{{ $agents->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-2 pt-2">
                        <input type="text" id="searchAgents" class="form-control form-control-sm"
                               placeholder="🔍 Rechercher un agent…">
                    </div>
                    <div class="card-table-scroll mt-1">
                        <table id="agentsTable" class="table table-sm affectation-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:35px">#</th>
                                    <th style="width:140px">Matricule</th>
                                    <th>Nom complet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agents as $agent)
                                    <tr data-matricule="{{ $agent->matricule }}"
                                        data-nom="{{ $agent->nom }} {{ $agent->postnom }} {{ $agent->prenom }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td><code class="text-primary">{{ $agent->matricule }}</code></td>
                                        <td>{{ $agent->nom }} {{ $agent->postnom }} {{ $agent->prenom }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Postes --}}
        <div class="col-lg-6 col-md-12">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-briefcase mr-2"></i> Postes par service
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success badge-pill">{{ $postes->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-2 pt-2">
                        <input type="text" id="searchPostes" class="form-control form-control-sm"
                               placeholder="🔍 Rechercher un poste…">
                    </div>
                    <div class="card-table-scroll mt-1">
                        <table id="postesTable" class="table table-sm affectation-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:35px">#</th>
                                    <th>Poste</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach($postes->groupBy('service_id') as $serviceId => $postesService)
                                    @php $serviceObj = $postesService->first()->service ?? null; @endphp
                                    <tr class="service-group">
                                        <td colspan="2">
                                            <i class="fas fa-building mr-1"></i>
                                            {{ $serviceObj ? $serviceObj->nom : 'Service inconnu' }}
                                        </td>
                                    </tr>
                                    @foreach($postesService as $poste)
                                        <tr data-id="{{ $poste->id }}"
                                            data-nom="{{ $poste->nom }}"
                                            data-service="{{ $serviceObj ? $serviceObj->nom : '' }}"
                                            data-is-guichet="{{ str_contains(strtolower($poste->nom), 'guichet') ? '1' : '0' }}">
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

    </div>{{-- /row agents+postes --}}

    {{-- ── Ligne 2 : Historique affectations ────────────────────── --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list mr-2"></i> Historique des affectations
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info badge-pill">{{ $affectations->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-2 pt-2">
                        <input type="text" id="searchAffectations" class="form-control form-control-sm"
                               placeholder="🔍 Rechercher dans les affectations…">
                    </div>
                    <div class="table-responsive mt-1">
                        <table id="affectationsTable" class="table table-bordered table-striped table-sm mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-user mr-1"></i> Agent</th>
                                    <th><i class="fas fa-briefcase mr-1"></i> Poste</th>
                                    <th><i class="fas fa-cash-register mr-1"></i> Guichet</th>
                                    <th><i class="fas fa-calendar-plus mr-1"></i> Début</th>
                                    <th><i class="fas fa-calendar-minus mr-1"></i> Fin</th>
                                    <th><i class="fas fa-toggle-on mr-1"></i> État</th>
                                    <th style="width:130px;white-space:nowrap"><i class="fas fa-cogs mr-1"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($affectations as $affectation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <code class="text-primary small">{{ $affectation->agent_matricule }}</code><br>
                                            <span>{{ $affectation->agent ? $affectation->agent->nom . ' ' . $affectation->agent->postnom : '—' }}</span>
                                        </td>
                                        <td>{{ $affectation->poste ? $affectation->poste->nom : '—' }}</td>
                                        <td>
                                            @if($affectation->guichet)
                                                <span class="badge badge-info">
                                                    <i class="fas fa-cash-register mr-1"></i>
                                                    {{ $affectation->guichet->code_guichet }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="date-badge">
                                                {{ $affectation->date_debut ? \Carbon\Carbon::parse($affectation->date_debut)->format('d/m/Y') : '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($affectation->date_fin)
                                                <span class="date-badge">
                                                    {{ \Carbon\Carbon::parse($affectation->date_fin)->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted small">En cours</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $etatClass = match($affectation->Etat) {
                                                    'ACTIF'    => 'success',
                                                    'TERMINE'  => 'secondary',
                                                    'SUSPENDU' => 'warning',
                                                    default    => 'light',
                                                };
                                            @endphp
                                            <span class="badge badge-{{ $etatClass }}">
                                                {{ $affectation->Etat }}
                                            </span>
                                        </td>
                                        <td class="text-center" style="white-space:nowrap">
                                            {{-- Voir --}}
                                            <button type="button"
                                                    class="btn btn-xs btn-info btn-voir-affectation mr-1"
                                                    data-id="{{ $affectation->id }}"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            {{-- Modifier état --}}
                                            <button type="button"
                                                    class="btn btn-xs btn-warning btn-edit-etat mr-1"
                                                    data-id="{{ $affectation->id }}"
                                                    data-etat="{{ $affectation->Etat }}"
                                                    data-datefin="{{ $affectation->date_fin ?? '' }}"
                                                    title="Modifier l'état">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            {{-- Supprimer --}}
                                            <button type="button"
                                                    class="btn btn-xs btn-danger btn-delete-affectation"
                                                    data-id="{{ $affectation->id }}"
                                                    data-etat="{{ $affectation->Etat }}"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Aucune affectation enregistrée
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- /row affectations --}}

</div>{{-- /container-fluid --}}
@endsection

@push('modals')
{{-- ── Modal d'affectation ─────────────────────────── --}}
<div class="modal fade" id="affectationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-check mr-2"></i> Nouvelle affectation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                {{-- Récapitulatif --}}
                <div class="callout callout-primary mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2"
                                      style="width:32px;height:32px;flex-shrink:0">
                                    <i class="fas fa-user" style="font-size:.75rem"></i>
                                </span>
                                <div>
                                    <small class="text-muted d-block">Agent sélectionné</small>
                                    <strong id="selectedAgentInfo" class="text-primary">—</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <span class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-2"
                                      style="width:32px;height:32px;flex-shrink:0">
                                    <i class="fas fa-briefcase" style="font-size:.75rem"></i>
                                </span>
                                <div>
                                    <small class="text-muted d-block">Poste — <span id="selectedPosteService" class="font-italic"></span></small>
                                    <strong id="selectedPosteInfo" class="text-success">—</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulaire --}}
                <form id="affectationForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-plus mr-1 text-primary"></i> Date début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="dateDebut" name="date_debut" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-minus mr-1 text-secondary"></i> Date fin <small class="text-muted">(optionnel)</small></label>
                                <input type="date" class="form-control" id="dateFin" name="date_fin">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-toggle-on mr-1 text-warning"></i> État <span class="text-danger">*</span></label>
                                <select class="form-control" id="etat" name="etat" required>
                                    <option value="ACTIF">Actif</option>
                                    <option value="TERMINE">Terminé</option>
                                    <option value="SUSPENDU">Suspendu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="guichetGroup">
                            <div class="form-group">
                                <label><i class="fas fa-cash-register mr-1 text-info"></i> Guichet assigné <span class="text-danger">*</span></label>
                                <select class="form-control" id="guichet_id" name="guichet_id">
                                    <option value="">— Sélectionner un guichet —</option>
                                    @foreach($guichets as $g)
                                        <option value="{{ $g->id }}">[{{ $g->code_guichet }}] {{ $g->intitule }} ({{ $g->statut_operationnel }})</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">L'agent deviendra titulaire de ce guichet.</small>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Annuler
                </button>
                <button type="button" class="btn btn-primary" id="confirmAffectationBtn">
                    <i class="fas fa-check mr-1"></i> Valider l'affectation
                </button>
            </div>

        </div>
    </div>
</div>
{{-- ── Modal VOIR DÉTAILS ───────────────────────────────────────── --}}
<div class="modal fade" id="voirModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye mr-2"></i> Détails de l'affectation</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="voirModalBody">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal MODIFIER ÉTAT ──────────────────────────────────────── --}}
<div class="modal fade" id="editEtatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i> Modifier l'état</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editEtatId">
                <div class="form-group">
                    <label><i class="fas fa-toggle-on mr-1 text-warning"></i> Nouvel état</label>
                    <select class="form-control" id="editEtatVal">
                        <option value="ACTIF">Actif</option>
                        <option value="SUSPENDU">Suspendu</option>
                        <option value="TERMINE">Terminé</option>
                    </select>
                </div>
                <div class="form-group" id="editDateFinGroup" style="display:none">
                    <label><i class="fas fa-calendar-minus mr-1 text-secondary"></i> Date de fin</label>
                    <input type="date" class="form-control" id="editDateFin">
                </div>
                <div class="callout callout-warning py-2 mb-0" id="editEtatInfo" style="display:none">
                    <small id="editEtatInfoText"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Annuler
                </button>
                <button type="button" class="btn btn-warning" id="confirmEditEtatBtn">
                    <i class="fas fa-save mr-1"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('css')
<style>

    /* ══ DARK ADMINLTE — Sélection de ligne ═══════════════════════
       On cible les <td> car AdminLTE dark fixe background sur td  */
    .affectation-table tbody tr.row-selected > td {
        background-color: #0062cc !important;
        color: #fff !important;
    }
    .affectation-table tbody tr.row-selected > td code {
        color: #cce5ff !important;
    }

    /* ── Survol (hover) ────────────────────────────────────────── */
    .affectation-table tbody tr:not(.service-group):not(.row-selected):hover > td {
        background-color: rgba(0, 123, 255, 0.22) !important;
        color: #fff !important;
        cursor: pointer;
    }
    /* Désactiver le hover Bootstrap natif pour ne pas cumuler */
    .affectation-table.table-hover tbody tr:hover {
        background-color: transparent !important;
    }

    /* ── En-tête groupe service ──────────────────────────────── */
    .service-group > td {
        background-color: #2c3136 !important;
        color: #adb5bd !important;
        font-weight: 600;
        font-size: .78rem;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 5px 10px !important;
        border-top: 2px solid #3d4349 !important;
    }
    .service-group:hover > td { background-color: #2c3136 !important; cursor: default !important; }

    /* ── Barre de sélection ──────────────────────────────────── */
    #selectionBar {
        background-color: rgba(255,255,255,0.04) !important;
        border-color: rgba(255,255,255,0.1) !important;
    }
    #selectionBar .sel-chip {
        display: inline-block;
        background: rgba(255,255,255,0.07);
        border-radius: 20px;
        padding: 3px 14px 3px 8px;
        font-size: .85rem;
        font-weight: 600;
        border: 2px dashed rgba(255,255,255,0.25);
        color: #adb5bd;
        min-width: 160px;
    }
    #selectionBar .sel-chip.filled {
        border-color: #28a745;
        border-style: solid;
        color: #75e096;
        background: rgba(40, 167, 69, 0.18);
    }

    /* ── Étapes ──────────────────────────────────────────────── */
    .step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px; height: 24px;
        border-radius: 50%;
        font-size: .78rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* ── Hauteur tableau ──────────────────────────────────────── */
    .card-table-scroll { max-height: 340px; overflow-y: auto; }

    /* ── Thead dark-friendly (thead-light → override) ─────────── */
    .affectation-table thead th {
        background-color: #2c3136 !important;
        color: #c2c7d0 !important;
        border-color: #3d4349 !important;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    /* ── Badges dates (badge-secondary au lieu de badge-light) ─── */
    .date-badge {
        background-color: rgba(255,255,255,0.1);
        color: #c2c7d0;
        border-radius: 4px;
        padding: 2px 7px;
        font-size: .78rem;
        border: 1px solid rgba(255,255,255,0.15);
    }

    /* ── Callout modal dark ───────────────────────────────────── */
    .callout.callout-primary {
        background-color: rgba(0, 98, 204, 0.12) !important;
        border-color: #0062cc !important;
    }
</style>
@endpush



@push('js')
<script>
$(document).ready(function () {

    // ── CSRF ──────────────────────────────────────────────────
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept'      : 'application/json'
        }
    });

    // ── Auto-fermeture alertes flash ──────────────────────────
    setTimeout(function () { $('.alert-success').alert('close'); }, 3000);

    // ── Recherche live : Agents (pas DataTables — évite bug _DT_CellIndex) ──
    $('#searchAgents').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('#agentsTable tbody tr').each(function () {
            $(this).toggle(q === '' || $(this).text().toLowerCase().indexOf(q) !== -1);
        });
    });

    // ── Recherche live : Postes (évite conflit colspan dans service-group) ──
    $('#searchPostes').on('input', function () {
        var q = $(this).val().toLowerCase();
        var lastGroup = null;
        $('#postesTable tbody tr').each(function () {
            if ($(this).hasClass('service-group')) {
                lastGroup = $(this).hide();
                lastGroup.data('hasVisible', false);
            } else {
                var visible = q === '' || $(this).text().toLowerCase().indexOf(q) !== -1;
                $(this).toggle(visible);
                if (visible && lastGroup) { lastGroup.data('hasVisible', true); }
            }
        });
        $('#postesTable tbody tr.service-group').each(function () {
            $(this).toggle(q === '' || $(this).data('hasVisible') === true);
        });
    });

    // ── Recherche live : Affectations ──────────────────────────────
    $('#searchAffectations').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('#affectationsTable tbody tr').each(function () {
            $(this).toggle(q === '' || $(this).text().toLowerCase().indexOf(q) !== -1);
        });
    });

    // ── Helpers inline style (contourne AdminLTE dark !important) ──
    function highlightRow($tr, color) {
        $tr.find('td').attr('style',
            'background-color: ' + color + ' !important; color: #fff !important;');
    }
    function clearHighlight($tds) {
        $tds.removeAttr('style');
    }

    // ── Hover via JS (CSS seul insuffisant en dark mode) ──────────
    $('#agentsTable, #postesTable').on('mouseenter', 'tbody tr:not(.service-group)', function () {
        if (!$(this).hasClass('row-selected')) {
            $(this).find('td').attr('style',
                'background-color: rgba(0,123,255,0.28) !important; color: #fff !important; cursor: pointer !important;');
        }
    }).on('mouseleave', 'tbody tr:not(.service-group)', function () {
        if (!$(this).hasClass('row-selected')) {
            clearHighlight($(this).find('td'));
        }
    });

    // ── Sélection Agent ───────────────────────────────────────────
    $('#agentsTable').on('click', 'tbody tr', function () {
        var $tr = $(this);
        if ($tr.hasClass('service-group')) return;

        // Retirer surbrillance précédente
        clearHighlight($('#agentsTable tbody tr.row-selected').find('td'));
        $('#agentsTable tbody tr').removeClass('row-selected');

        // Appliquer sur la nouvelle ligne
        $tr.addClass('row-selected');
        highlightRow($tr, '#0062cc');

        window.selectedAgent = {
            matricule: $.trim($tr.attr('data-matricule') || $tr.find('td').eq(1).text()),
            nom:       $.trim($tr.attr('data-nom')       || $tr.find('td').eq(2).text()),
        };

        updateSelectionBar();
        if (window.selectedPoste) { openAffectationModal(); }
    });

    // ── Sélection Poste ───────────────────────────────────────────
    $('#postesTable').on('click', 'tbody tr', function () {
        var $tr = $(this);
        if ($tr.hasClass('service-group')) return;

        // Retirer surbrillance précédente
        clearHighlight($('#postesTable tbody tr.row-selected').find('td'));
        $('#postesTable tbody tr').removeClass('row-selected');

        // Appliquer sur la nouvelle ligne
        $tr.addClass('row-selected');
        highlightRow($tr, '#1e7e34');

        window.selectedPoste = {
            id:        $tr.attr('data-id'),
            nom:       $.trim($tr.attr('data-nom')     || $tr.find('td').eq(1).text()),
            service:   $.trim($tr.attr('data-service') || $tr.prevAll('.service-group').first().find('td').text()),
            isGuichet: $tr.attr('data-is-guichet') === '1',
        };

        updateSelectionBar();
        if (window.selectedAgent) { openAffectationModal(); }
    });

    // ── Réinitialiser ─────────────────────────────────────────
    $('#btnReset').on('click', function () { resetSelection(); });

    // ── Confirmer l'affectation ───────────────────────────────
    $('#confirmAffectationBtn').on('click', function () {
        if (!window.selectedAgent || !window.selectedPoste) return;

        var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> En cours…');

        $.ajax({
            url     : '{{ route("affectations.store") }}',
            method  : 'POST',
            data    : {
                agent_matricule: window.selectedAgent.matricule,
                poste_id:        window.selectedPoste.id,
                guichet_id:      $('#guichet_id').val() || null,
                date_debut:      $('#dateDebut').val(),
                date_fin:        $('#dateFin').val(),
                etat:            $('#etat').val(),
            },
            dataType: 'json'
        })
        .done(function (response) {
            $('#affectationModal').modal('hide');
            showSystemMessage('success', (response && response.message) ? response.message : 'Affectation enregistrée avec succès !');
            setTimeout(function () { location.reload(); }, 1200);
        })
        .fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur inattendue est survenue (' + xhr.status + ').';
            showSystemMessage('error', msg);
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Valider l\'affectation');
        });
    });

    // ── Fermer modal = reset formulaire ───────────────────────
    $('#affectationModal').on('hidden.bs.modal', function () {
        $('#affectationForm')[0].reset();
    });

});// end document.ready

// ── Ouvrir le modal ────────────────────────────────────────────
function openAffectationModal() {
    $('#selectedAgentInfo').text(window.selectedAgent.matricule + ' — ' + window.selectedAgent.nom);
    $('#selectedPosteInfo').text(window.selectedPoste.nom);
    $('#selectedPosteService').text(window.selectedPoste.service || '');

    // Pré-remplir la date début avec aujourd'hui
    var today = new Date().toISOString().split('T')[0];
    if (!$('#dateDebut').val()) { $('#dateDebut').val(today); }

    $('#affectationModal').modal('show');
}

// ── Mettre à jour la barre de sélection ────────────────────────
function updateSelectionBar() {
    var hasAgent = !!window.selectedAgent;
    var hasPoste = !!window.selectedPoste;

    if (hasAgent) {
        $('#chipAgent').addClass('filled');
        $('#chipAgentText').text(window.selectedAgent.matricule + ' · ' + window.selectedAgent.nom);
    } else {
        $('#chipAgent').removeClass('filled');
        $('#chipAgentText').text('Aucun agent');
    }

    if (hasPoste) {
        $('#chipPoste').addClass('filled');
        $('#chipPosteText').text(window.selectedPoste.nom);
    } else {
        $('#chipPoste').removeClass('filled');
        $('#chipPosteText').text('Aucun poste');
    }

    $('#btnReset').toggle(hasAgent || hasPoste);
}

// ── Réinitialiser sélection ────────────────────────────────────
function resetSelection() {
    window.selectedAgent = null;
    window.selectedPoste = null;
    var $rows = $('#agentsTable tbody tr, #postesTable tbody tr');
    $rows.removeClass('row-selected');
    $rows.find('td').removeAttr('style');
    updateSelectionBar();
}

// ══════════════════════════════════════════════════════════════
//  ACTIONS HISTORIQUE AFFECTATIONS
// ══════════════════════════════════════════════════════════════

// ── VOIR détails ──────────────────────────────────────────────
$(document).on('click', '.btn-voir-affectation', function () {
    var id = $(this).data('id');
    $('#voirModalBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
    $('#voirModal').modal('show');

    $.get('{{ route("affectations.show", ["affectation" => "__ID__"]) }}'.replace('__ID__', id))
        .done(function (d) {
            var etatColors = { ACTIF: 'success', SUSPENDU: 'warning', TERMINE: 'secondary' };
            var html = `
                <table class="table table-sm table-borderless mb-0">
                    <tr><th style="width:40%">Matricule</th>
                        <td><code class="text-primary">${d.agent_matricule}</code></td></tr>
                    <tr><th>Agent</th><td>${d.agent_nom}</td></tr>
                    <tr><th>Service</th><td>${d.service}</td></tr>
                    <tr><th>Poste</th><td>${d.poste}</td></tr>
                    <tr><th>Guichet</th>
                        <td>${d.guichet ? '<span class="badge badge-info">' + d.guichet + ' — ' + (d.guichet_intitule||'') + '</span>' : '<span class="text-muted">—</span>'}</td></tr>
                    <tr><th>Date début</th><td>${d.date_debut || '—'}</td></tr>
                    <tr><th>Date fin</th><td>${d.date_fin || '<span class="text-muted">En cours</span>'}</td></tr>
                    <tr><th>État</th>
                        <td><span class="badge badge-${etatColors[d.etat]||'light'}">${d.etat}</span></td></tr>
                </table>
                ${!d.can_delete ? '<div class="callout callout-warning mt-3 mb-0 py-2"><small><i class="fas fa-lock mr-1"></i>' + d.delete_reason + '</small></div>' : ''}
            `;
            $('#voirModalBody').html(html);
        })
        .fail(function () {
            $('#voirModalBody').html('<div class="text-center text-danger py-3">Erreur de chargement.</div>');
        });
});

// ── MODIFIER état ─────────────────────────────────────────────
$(document).on('click', '.btn-edit-etat', function () {
    var id      = $(this).data('id');
    var etat    = $(this).data('etat');
    var dateFin = $(this).data('datefin');

    $('#editEtatId').val(id);
    $('#editEtatVal').val(etat);
    $('#editDateFin').val(dateFin || '');
    $('#editDateFinGroup').toggle(etat === 'TERMINE');
    $('#editEtatInfo').hide();

    // Info contextuelle selon l'état courant
    if (etat === 'ACTIF') {
        $('#editEtatInfoText').text('Attenttion : passer en SUSPENDU ou TERMINÉ désactivera l\'agent sur ce poste.');
        $('#editEtatInfo').show();
    }

    $('#editEtatModal').modal('show');
});

$('#editEtatVal').on('change', function () {
    $('#editDateFinGroup').toggle($(this).val() === 'TERMINE');
});

$('#confirmEditEtatBtn').on('click', function () {
    var id   = $('#editEtatId').val();
    var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>');

    $.ajax({
        url     : '{{ route("affectations.updateEtat", ["affectation" => "__ID__"]) }}'.replace('__ID__', id),
        method  : 'POST',
        data    : {
            _method:  'PATCH',
            etat:     $('#editEtatVal').val(),
            date_fin: $('#editDateFin').val() || null,
        },
        dataType: 'json'
    })
    .done(function (r) {
        $('#editEtatModal').modal('hide');
        showSystemMessage('success', r.message || 'État modifié avec succès.');
        setTimeout(function () { location.reload(); }, 1200);
    })
    .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur inattendue est survenue (' + xhr.status + ').';
        showSystemMessage('error', msg);
    })
    .always(function () {
        $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Enregistrer');
    });
});

// ── SUPPRIMER ─────────────────────────────────────────────────
$(document).on('click', '.btn-delete-affectation', function () {
    var id   = $(this).data('id');
    var etat = $(this).data('etat');
    var $btn = $(this);

    // Blocage rapide côté client si encore actif
    if (etat === 'ACTIF') {
        showSystemMessage('error',
            'Impossible de supprimer une affectation <strong>ACTIVE</strong>.<br>Modifiez l\'état d\'abord.',
            'Suppression impossible');
        return;
    }

    showUniversalConfirm(
        'Voulez-vous supprimer cette affectation ?',
        function () {
            $btn.prop('disabled', true);
            $.ajax({
                url     : '{{ route("affectations.destroy", ["affectation" => "__ID__"]) }}'.replace('__ID__', id),
                method  : 'POST',
                data    : { _method: 'DELETE' },
                dataType: 'json'
            })
            .done(function (r) {
                showSystemMessage('success', r.message || 'Affectation supprimée avec succès.');
                setTimeout(function () { location.reload(); }, 1200);
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Suppression impossible (' + xhr.status + ').';
                showSystemMessage('error', msg);
                $btn.prop('disabled', false);
            });
        }
    );
});
</script>
@endpush
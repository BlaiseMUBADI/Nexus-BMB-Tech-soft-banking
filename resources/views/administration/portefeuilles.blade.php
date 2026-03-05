@extends('layouts.app')

@section('page_title', 'Portefeuilles d\'agents')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Portefeuilles')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('administration.zones.index') }}" class="btn btn-info">
            <i class="fas fa-map-marker-alt mr-1"></i> Retour à la liste des zones
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Ajouter un portefeuille</h5>
                </div>
                <div class="card-body">
                    <form id="portefeuilleFm">
                        @csrf
                        <div class="form-group">
                            <label for="nomPortefeuille">Nom du portefeuille</label>
                            <input type="text" name="nom_portefeuille" class="form-control" id="nomPortefeuille" 
                                   placeholder="Ex: Portefeuille A" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="agentCreditMatricule">Agent commercial</label>
                            <select name="agent_matricule" id="agentCreditMatricule" class="form-control select2" required>
                                <option value="">Sélectionner un agent</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->matricule }}">
                                        [{{ $agent->matricule }}] {{ $agent->nom }} {{ $agent->postnom }} {{ $agent->prenom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tauxCommissionAgent">Taux de commission (%)</label>
                            <input type="number" step="0.01" name="taux_commission_agent" class="form-control" 
                                   id="tauxCommissionAgent" placeholder="0.00" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i>Ajouter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Liste des portefeuilles</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 700px; min-height: 200px; overflow-y: auto;">
                        <table class="table table-bordered table-striped mb-0" id="portefeuillesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom du portefeuille</th>
                                    <th>Agent</th>
                                    <th>Taux (%)</th>
                                    <th style="width: 50px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($portefeuilles as $portefeuille)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $portefeuille->nom_portefeuille }}</td>
                                        <td>
                                            @if($portefeuille->agent)
                                                [{{ $portefeuille->agent_matricule }}] {{ $portefeuille->agent->nom }} {{ $portefeuille->agent->prenom }}
                                            @else
                                                <span class="text-danger">[{{ $portefeuille->agent_matricule }}] Inconnu</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($portefeuille->taux_commission_agent, 2, ',', ' ') }}</td>
                                        <td>
                                            <form action="{{ route('administration.portefeuilles.destroy', $portefeuille->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete-portefeuille">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Aucun portefeuille enregistré.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <style>
        .app-table tbody tr.datatable-selected-row {
            background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
            color: #fff !important;
        }
        .app-table tbody tr:hover:not(.datatable-selected-row) {
            background: linear-gradient(90deg, #06b6d4 0%, #3b82f6 100%) !important;
            color: #fff !important;
            cursor: pointer;
        }
    </style>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // 1. Configuration AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // 2. Select2
    $('#agentCreditMatricule').select2({
        theme: 'bootstrap4',
        placeholder: 'Sélectionner un agent',
        allowClear: true,
        width: '100%'
    });

    // 3. DataTable
    /*var $table = $('#portefeuillesTable');
    $table.addClass('app-table');
    var dataTable = $table.DataTable({
        language: { url: '/plugins/datatables/i18n/fr-FR.json' },
        pageLength: 10
    });*/

    // Sélection de ligne
    /*$table.on('click', 'tbody tr', function () {
        $(this).addClass('datatable-selected-row').siblings().removeClass('datatable-selected-row');
    });*/

    // 4. Ajout AJAX
    $('#portefeuilleFm').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        showSystemMessage('info', 'Traitement en cours...');

        $.post("{{ route('administration.portefeuilles.store') }}", $form.serialize())
            .done(function(response) {
                showSystemMessage('success', 'Portefeuille enregistré !');
                setTimeout(() => { window.location.reload(); }, 1000);
            })
            .fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Erreur lors de l\'enregistrement.';
                showSystemMessage('error', msg);
            });
    });

    // 5. Suppression AJAX (Version sécurisée pour éviter l'erreur de méthode)
    $(document).on('click', '.btn-delete-portefeuille', function(e) {
        e.preventDefault();
        let $form = $(this).closest('form');
        let url = $form.attr('action');
        
        showUniversalConfirm("Supprimer ce portefeuille ?", function() {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: { _token: $('input[name="_token"]').val() },
                success: function(response) {
                    showSystemMessage('success', 'Suppression réussie');
                    setTimeout(() => { window.location.reload(); }, 800);
                },
                error: function(xhr) {
                    showSystemMessage('error', 'Erreur lors de la suppression.');
                }
            });
        });
    });
});
</script>
@endpush
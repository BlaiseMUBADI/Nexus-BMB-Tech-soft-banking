@extends('layouts.app')

@section('page_title', 'Zones / Portfeuille')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Zones / Portfeuille')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('administration.portefeuilles.index') }}" class="btn btn-info">
                <i class="fas fa-wallet mr-1"></i> Gérer les portefeuilles d'agents
            </a>
        </div>
        <div>
            <div class="row">
                <!-- Bloc formulaire à gauche -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Ajouter une zone</h5>
                        </div>
                        <div class="card-body">
                            <form id="zoneForm">
                                @csrf
                                <div class="form-group">
                                    <label for="zoneName">Nom de la zone</label>
                                    <input type="text" name="nom" class="form-control" id="zoneName"
                                        placeholder="Entrer le nom de la zone" required autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="agentMatricule">Agent commercial</label>
                                    <select name="agent_commercial_matricule" id="agentMatricule"
                                        class="form-control select2">
                                        <option value="">Sélectionner un agent</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->matricule }}">
                                                [{{ $agent->matricule }}] {{ $agent->nom }} {{ $agent->postnom }}
                                                {{ $agent->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="commune">Commune</label>
                                    <select name="commune" id="commune" class="form-control"
                                        onchange="if(this.value==='autre'){document.getElementById('commune_autre').style.display='block';}else{document.getElementById('commune_autre').style.display='none';}">
                                        <option value="">Sélectionner une commune</option>
                                        <option value="Kanange">Kananga</option>
                                        <option value="Nganza">Nganza</option>
                                        <option value="Lukongo">Lukongo</option>
                                        <option value="Ndesha">Ndesha</option>
                                        <option value="Katoka">Katoka</option>
                                        <option value="autre">Autre...</option>
                                    </select>
                                    <input type="text" name="commune_autre" id="commune_autre" class="form-control mt-2"
                                        placeholder="Entrer une autre commune" style="display:none;">
                                </div>
                                <button type="submit" class="btn btn-primary"><i
                                        class="fas fa-plus-circle mr-1"></i>Ajouter</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Bloc tableau à droite -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Liste des zones</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 700px; min-height: 200px; overflow-y: auto;">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nom de la zone</th>
                                            <th>Agent commercial</th>
                                            <th>Commune</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($zones as $zone)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $zone->nom }}</td>
                                                <td>
                                                    @if($zone->agent)
                                                        [{{ $zone->agent->matricule }}] {{ $zone->agent->nom }} {{ $zone->agent->postnom }} {{ $zone->agent->prenom }}
                                                    @else
                                                        <span class="text-danger">Agent inconnu</span>
                                                    @endif
                                                </td>
                                                <td>{{ $zone->commune }}</td>
                                                <td>
                                                    <form action="{{ route('administration.zones.destroy', $zone->code_zone) }}" method="POST" class="delete-zone-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-zone">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                             @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Aucune zone enregistrée.</td>
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
    </div>
@endsection


@section('css')
    <style>
        .app-table tbody tr.datatable-selected-row {
            background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
            transition: background 0.3s, color 0.3s;
        }

        .app-table tbody tr:hover:not(.datatable-selected-row) {
            background: linear-gradient(90deg, #06b6d4 0%, #3b82f6 100%) !important;
            color: #fff !important;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(59, 130, 246, 0.10);
            transition: background 0.3s, color 0.3s;
        }
    </style>
@endsection


@push('js')
<script>
$(document).ready(function () {
    // 1. Configuration globale AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 2. Initialisation Select2 (Corrigé pour correspondre à ton ID HTML)
    $('#agentMatricule').select2({
        theme: 'bootstrap4',
        placeholder: 'Sélectionner un agent',
        allowClear: true,
        width: '100%'
    });

    // 3. Initialisation DataTable
    /*var $zonesTable = $('.table');
    $zonesTable.addClass('app-table');
    /*var zonesDataTable = $zonesTable.DataTable({
        language: { url: '/plugins/datatables/i18n/fr-FR.json' },
        pageLength: 10
    });*/
    
    // Sélection visuelle de ligne
    $('.table').on('click', 'tbody tr', function () {
        $(this).addClass('datatable-selected-row').siblings().removeClass('datatable-selected-row');
    });

    // 4. Soumission AJAX Formulaire ZONE
    $('#zoneForm').on('submit', function (e) {
        e.preventDefault();
        let $form = $(this);
        let url = "{{ route('administration.zones.store') }}";

        showSystemMessage('info', 'Enregistrement en cours...');

        $.post(url, $form.serialize())
            .done(function (response) {
                showSystemMessage('success', 'Zone ajoutée avec succès !');
                $form[0].reset();
                $('#agentMatricule').val(null).trigger('change'); // Reset Select2
                setTimeout(() => { window.location.reload(); }, 1000);
            })
            .fail(function (xhr) {
                if (typeof handleAjaxError === 'function') {
                    handleAjaxError(xhr, 'Erreur lors de l\'enregistrement.');
                } else {
                    let msg = xhr.responseJSON?.message || 'Erreur lors de l\'enregistrement.';
                    showSystemMessage('error', msg);
                }
            });
    });

    // 5. Suppression AJAX d'une zone
    $(document).on('click', '.btn-delete-zone', function(e) {
        e.preventDefault();
        let $form = $(this).closest('form');
        let url = $form.attr('action'); // L'URL contient l'ID de la zone
        
        showUniversalConfirm(
            "Êtes-vous sûr de vouloir supprimer cette zone ?",
            function() {
                $.ajax({
                    url: url,
                    type: 'DELETE', // On envoie directement en DELETE
                    data: {
                        // On envoie manuellement le token pour être sûr
                        _token: $('input[name="_token"]').val() 
                    },
                    success: function(response) {
                        showSystemMessage('success', 'Zone supprimée avec succès !');
                        setTimeout(() => { window.location.reload(); }, 800);
                    },
                    error: function(xhr) {
                        // Si Laravel renvoie encore une erreur de méthode
                        if(xhr.status === 405) {
                            showSystemMessage('error', 'Erreur de méthode HTTP (405).');
                        } else {
                            let msg = xhr.responseJSON?.message || 'Erreur lors de la suppression.';
                            showSystemMessage('error', msg);
                        }
                    }
                });
            },
            "Confirmer la suppression"
        );
        });
});
</script>
@endpush
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
        <!-- Bloc formulaire portefeuille à gauche -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Ajouter un portefeuille d'agent</h5>
                </div>
                <div class="card-body">
                    <form id="portefeuilleFm">
                        @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="nomPortefeuille">Nom du portefeuille</label>
                            <input type="text" name="nom_portefeuille" class="form-control" id="nomPortefeuille" placeholder="Entrer le nom du portefeuille" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="agentCreditMatricule">Agent</label>
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
                            <input type="number" step="0.01" name="taux_commission_agent" class="form-control" id="tauxCommissionAgent" placeholder="0.00" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle mr-1"></i>Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bloc tableau portefeuille à droite -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Liste des portefeuilles d'agents</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 700px; min-height: 200px; overflow-y: auto;">
                        <table class="table table-bordered table-striped mb-0 app-table" id="portefeuillesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom du portefeuille</th>
                                    <th>Agent</th>
                                    <th>Taux de commission (%)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($portefeuilles as $portefeuille)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $portefeuille->nom_portefeuille }}</td>
                                        <td>
                                            [{{ $portefeuille->agent_matricule }}]
                                            @if($portefeuille->agent)
                                                {{ $portefeuille->agent->nom }} {{ $portefeuille->agent->postnom }} {{ $portefeuille->agent->prenom }}
                                            @endif
                                        </td>
                                        <td>{{ number_format($portefeuille->taux_commission_agent, 2, ',', ' ') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" data-id="{{ $portefeuille->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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
    $(document).ready(function() {
        // Initialisation Select2 si besoin
        $('#agentCreditMatricule').select2({
            theme: 'bootstrap4',
            placeholder: 'Rechercher un agent par matricule ou nom',
            allowClear: true,
            width: 'resolve',
            language: {
                noResults: function() { return "Aucun résultat trouvé"; }
            },
            matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;
                if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                    return data;
                }
                return null;
            }
        });

        // DataTable + sélection de ligne
        var $portefeuillesTable = $('#portefeuillesTable');
        $portefeuillesTable.addClass('app-table');
        var portefeuillesDataTable = $portefeuillesTable.DataTable({
            paging: true,
            searching: true,
            info: true,
            lengthChange: true,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
            language: {
                url: '/plugins/datatables/i18n/fr-FR.json',
                paginate: {
                    first: "Premier",
                    last: "Dernier",
                    next: "Suivant",
                    previous: "Précédent"
                },
                search: "Recherche :",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                infoEmpty: "Aucune entrée à afficher",
                infoFiltered: "(filtré à partir de _MAX_ entrées)",
                lengthMenu: "Afficher _MENU_ entrées",
            }
        });
        $portefeuillesTable.on('click', 'tbody tr', function () {
            $portefeuillesTable.find('tbody tr').removeClass('datatable-selected-row');
            $(this).addClass('datatable-selected-row');
        });

        // Soumission AJAX du formulaire portefeuille
        $('#portefeuilleFm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = "{{ route('administration.portefeuilles.store') }}";
            var formData = form.serialize();
            $.post(url, formData)
                .done(function(response) {
                    showSystemMessage('success', 'Portefeuille ajouté avec succès !');
                    form[0].reset();
                    setTimeout(function() { window.location.reload(); }, 1500);
                })
                .fail(function(xhr) {
                    let msg = 'Erreur lors de l\'enregistrement du portefeuille.';
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = '';
                        $.each(xhr.responseJSON.errors, function(key, errors) {
                            errors.forEach(function(error) {
                                msg += '<div>' + error + '</div>';
                            });
                        });
                    } else if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showSystemMessage('error', msg);
                });
        });
        // Suppression portefeuille
        $('#portefeuillesTable').on('click', '.btn-danger', function(e) {
            e.preventDefault();
            var btn = $(this);
            var portefeuilleId = btn.data('id');
            var deleteUrl = "{{ route('administration.portefeuilles.destroy', ['id' => 'PORTF_ID']) }}".replace('PORTF_ID', portefeuilleId);
            showUniversalConfirm('Voulez-vous vraiment supprimer ce portefeuille ?', function() {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        showSystemMessage('success', response.message || 'Portefeuille supprimé avec succès !');
                        setTimeout(function() { window.location.reload(); }, 1200);
                    },
                    error: function(xhr) {
                        let msg = 'Erreur lors de la suppression.';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showSystemMessage('error', msg);
                    }
                });
            }, 'Confirmation suppression');
        });
    });
    </script>
@endpush


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
                                                    <form action="{{ route('administration.zones.destroy', $zone->code_zone) }}" method="POST" class="d-inline delete-zone-form" data-zone-id="{{ $zone->code_zone }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-zone" data-id="{{ $zone->code_zone }}" title="Supprimer">
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
        // Suppression AJAX d'une zone avec confirmation universelle
        let formToDeleteZone = null;
        $('.table').on('click', '.btn-delete-zone', function(e) {
            e.preventDefault();
            formToDeleteZone = $(this).closest('form');
            showUniversalConfirm(
                "Êtes-vous sûr de vouloir supprimer cette zone ?",
                function() {
                    if(formToDeleteZone) {
                        $.ajax({
                            url: formToDeleteZone.attr('action'),
                            type: 'POST',
                            data: formToDeleteZone.serialize(),
                            success: function(response) {
                                showSystemMessage('success', 'Zone supprimée avec succès !');
                                setTimeout(function() { window.location.reload(); }, 500);
                            },
                            error: function(xhr) {
                                let msg = 'Erreur lors de la suppression de la zone.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                showSystemMessage('error', msg);
                            }
                        });
                    }
                },
                "Confirmer la suppression"
            );
        });
        // Setup global AJAX CSRF token pour toutes les requêtes
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            // Initialisation Select2 pour agent zone
            $('#agentMatricule').select2({
                theme: 'bootstrap4',
                placeholder: 'Rechercher un agent par matricule ou nom',
                allowClear: true,
                width: 'resolve',
                language: {
                    noResults: function () { return "Aucun résultat trouvé"; }
                },
                matcher: function (params, data) {
                    if ($.trim(params.term) === '') return data;
                    if (typeof data.text === 'undefined') return null;
                    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                        return data;
                    }
                    return null;
                }
            });

            // Initialisation Select2 pour agent portefeuille
            $('#agentCreditMatricule').select2({
                theme: 'bootstrap4',
                placeholder: 'Rechercher un agent par matricule ou nom',
                allowClear: true,
                width: 'resolve',
                language: {
                    noResults: function () { return "Aucun résultat trouvé"; }
                },
                matcher: function (params, data) {
                    if ($.trim(params.term) === '') return data;
                    if (typeof data.text === 'undefined') return null;
                    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                        return data;
                    }
                    return null;
                }
            });

            // DataTable + sélection de ligne
            var $zonesTable = $('.table.table-bordered');
            $zonesTable.addClass('app-table');
            var zonesDataTable = $zonesTable.DataTable({
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
            $zonesTable.on('click', 'tbody tr', function () {
                $zonesTable.find('tbody tr').removeClass('datatable-selected-row');
                $(this).addClass('datatable-selected-row');
            });




            // Soumission AJAX du formulaire zone
            $('#zoneForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = "{{ route('administration.zones.store') }}";
                var formData = form.serialize();
                $.post(url, formData)
                    .done(function (response) {
                        showSystemMessage('success', 'Zone ajoutée avec succès !');
                        form[0].reset();
                        /*setTimeout(function() {
                             window.location.reload();
                        }, 300);*/
                    })
                    .fail(function (xhr) {
                        let msg = 'Erreur lors de l\'enregistrement de la zone.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = '';
                            $.each(xhr.responseJSON.errors, function (key, errors) {
                                errors.forEach(function (error) {
                                    msg += '<div>' + error + '</div>';
                                });
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showSystemMessage('error', msg);
                    });
            });

            // Soumission AJAX du formulaire portefeuille
            $('#portefeuilleForm').on('submit', function (e) {
                e.preventDefault();
                console.log('Soumission portefeuille AJAX interceptée');
                var form = $(this);
                showSystemMessage('info', 'Enregistrement en cours...');
                var url = "{{ route('administration.portefeuilles.store') }}";
                var formData = form.serialize();
                $.post(url, formData)
                    .done(function (response) {
                        showSystemMessage('success', 'Portefeuille ajouté avec succès !');
                        form[0].reset();
                        setTimeout(function () {
                            window.location.reload();
                        }, 300);
                    })
                    .fail(function (xhr) {
                        let msg = 'Erreur lors de l\'enregistrement du portefeuille.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = '';
                            $.each(xhr.responseJSON.errors, function (key, errors) {
                                errors.forEach(function (error) {
                                    msg += '<div>' + error + '</div>';
                                });
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showSystemMessage('error', msg);
                    });
            });
        });
    </script>
@endpush
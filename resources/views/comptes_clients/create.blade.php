@extends('layouts.app')

@section('page_title', 'Ouverture de compte bancaire')
@section('breadcrumb_parent', 'Gestion des comptes')
@section('breadcrumb', 'Ouverture de compte')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Bloc formulaire à gauche -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Ouverture de compte bancaire</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="compteForm">
                        @csrf
                        <div class="form-group">
                            <label for="client_matricule">Client</label>
                            <select name="client_matricule" id="client_matricule" class="form-control select2" required>
                                <option value="">-- Sélectionner un client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->matricule }}">{{ $client->nom }} {{ $client->postnom }} {{ $client->prenom }} ({{ $client->matricule }})</option>
                                @endforeach
                            </select>
                            @error('client_matricule')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="type">Type de compte</label>
                            <select name="type" id="type" class="form-control select2" required>
                                <option value="">-- Sélectionner le type --</option>
                                <option value="COURANT">Courant</option>
                                <option value="EPARGNE_LIBRE">Épargne libre</option>
                                <option value="EPARGNE_BLOQUEE">Épargne bloquée</option>
                                <option value="CAUTION_CREDIT">Caution crédit</option>
                            </select>
                            @error('type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle mr-1"></i>Ouvrir le compte</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bloc tableau à droite -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Liste des comptes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 700px; min-height: 200px; overflow-y: auto;">
                        <table class="table table-bordered app-table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Code Compte</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th>Solde réel</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comptes as $compte)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $compte->code_compte }}</td>
                                        <td>{{ $compte->client->nom }} {{ $compte->client->postnom }} {{ $compte->client->prenom }}</td>
                                        <td>{{ $compte->type }}</td>
                                        <td>{{ number_format($compte->solde_reel, 2, ',', ' ') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" data-id="{{ $compte->code_compte }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Aucun compte enregistré.</td>
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
        $('#client_matricule').select2({
            theme: 'bootstrap4',
            placeholder: 'Rechercher un client',
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
        $('#type').select2({
            theme: 'bootstrap4',
            placeholder: 'Rechercher le type de compte',
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

        // Setup global AJAX CSRF token pour toutes les requêtes
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Soumission AJAX du formulaire
        $('#compteForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = "{{ route('comptes.store') }}";
            var formData = form.serialize();
            $.post(url, formData)
                .done(function(response) {
                    showSystemMessage('success', 'Compte ouvert avec succès !');
                    form[0].reset();
                   /* setTimeout(function() {
                        window.location.reload();
                    }, 300);*/
                })
                .fail(function(xhr) {
                    let msg = 'Erreur lors de l\'ouverture du compte.';
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

        // DataTable + sélection de ligne
        var $comptesTable = $('.table.app-table');
        $comptesTable.addClass('app-table');
        
        var comptesDataTable = $comptesTable.DataTable({
       
            paging: true,
            searching: true,
            info: true,
            lengthChange: true,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
            language: {
                url: window.DATATABLES_LANG_URL || '/plugins/datatables/i18n/fr-FR.json',
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
        $comptesTable.on('click', 'tbody tr', function () {
            $comptesTable.find('tbody tr').removeClass('datatable-selected-row');
            $(this).addClass('datatable-selected-row');
        });
    });
</script>
@endpush
@endsection

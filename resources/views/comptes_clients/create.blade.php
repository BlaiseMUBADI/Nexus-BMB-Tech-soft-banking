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
                        <div class="form-group" id="portefeuille_group" style="display:none">
                            <label for="portefeuille_id">Agent gestionnaire (Portefeuille)</label>
                            <select name="portefeuille_id" id="portefeuille_id" class="form-control select2">
                                <option value="">-- Sélectionner l'agent --</option>
                                @foreach($portefeuilles as $portefeuille)
                                    <option value="{{ $portefeuille->id }}">
                                        ({{ $portefeuille->agent_matricule }}) {{ $portefeuille->agent_nom }} {{ $portefeuille->agent_prenom }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Obligatoire pour les comptes de type Caution Crédit.</small>
                        </div>

                        
                        <div class="form-group">
                            <label for="devise">Devise</label>
                            <select name="devise" id="devise" class="form-control select2" required>
                                <option value="">-- Sélectionner la devise --</option>
                                @foreach($devises as $devise)
                                    <option value="{{ $devise->code_iso }}">{{ $devise->nom }} ({{ $devise->symbole }})</option>
                                @endforeach
                            </select>
                            @error('devise')
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
                                    <th>Devise</th>
                                    <th>Portefeuille</th>
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
                                        <td>{{ $compte->devise }}</td>
                                        <td>
                                            @if($compte->portefeuille_id)
                                                @php
                                                    $portefeuille = $portefeuilles->where('id', $compte->portefeuille_id)->first();
                                                @endphp
                                                ({{ $portefeuille->agent->matricule ?? '-' }}) {{ $portefeuille->agent->nom ?? '-' }} {{ $portefeuille->agent->prenom ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            
                                            <button class="btn btn-sm btn-danger delete-compte-btn" data-id="{{ $compte->code_compte }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Aucun compte enregistré.</td>
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
        // 1. Initialisation de Select2
        $('#client_matricule, #type, #portefeuille_id, #devise').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Sélectionner une option',
            allowClear: true,
            language: {
                noResults: function() { return "Aucun résultat trouvé"; }
            }
        });

        // 2. Logique pour afficher/masquer le portefeuille (Gestion Crédit)
        $('#type').on('change', function() {
            var selectedType = $(this).val();
            var $portefeuilleGroup = $('#portefeuille_group');
            var $portefeuilleSelect = $('#portefeuille_id');

            if (selectedType === 'CAUTION_CREDIT') {
                $portefeuilleGroup.fadeIn();
                $portefeuilleSelect.prop('required', true);
            } else {
                $portefeuilleGroup.fadeOut();
                $portefeuilleSelect.prop('required', false).val(null).trigger('change');
            }
        });

        // 3. Setup global AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 4. Soumission AJAX du formulaire
        $('#compteForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = "{{ route('comptes.store') }}";
            var formData = form.serialize();

            $.post(url, formData)
                .done(function(response) {
                    showSystemMessage('success', 'Compte ouvert avec succès !');
                    
                    // Reset complet du formulaire et des Select2
                    form[0].reset();
                    $('.select2').val(null).trigger('change'); 
                    setTimeout(function () { window.location.reload(); }, 1500);
                    
                    // Optionnel : Recharger la table si vous n'utilisez pas de push temps réel
                    // comptesDataTable.ajax.reload(); 
                })
                .fail(function(xhr) {
                    let msg = 'Erreur lors de l\'ouverture du compte.';
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showSystemMessage('error', msg);
                });
        });

        // 5. Initialisation DataTable
        var $comptesTable = $('.table.app-table');
        var comptesDataTable = $comptesTable.DataTable({
            paging: true,
            searching: true,
            info: true,
            lengthChange: true,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
            language: {
                url: "{{ asset('plugins/datatables/i18n/fr-FR.json') }}" // Assurez-vous du chemin
            }
        });

        // Sélection de ligne au clic
        $comptesTable.on('click', 'tbody tr', function () {
            $(this).addClass('datatable-selected-row').siblings().removeClass('datatable-selected-row');
        });

        // Suppression AJAX d'un compte
        $('.table.app-table').on('click', '.delete-compte-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var codeCompte = btn.data('id');
            var deleteUrl = "{{ route('comptes.destroy', ['code_compte' => 'CODE_COMPTE']) }}".replace('CODE_COMPTE', codeCompte);
            showUniversalConfirm('Voulez-vous vraiment supprimer ce compte ?', function() {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        showSystemMessage('success', response.message || 'Compte supprimé avec succès !');
                        setTimeout(function () { window.location.reload(); }, 1200);
                    },
                    error: function(xhr) {
                        let msg = 'Erreur lors de la suppression du compte.';
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

@endsection

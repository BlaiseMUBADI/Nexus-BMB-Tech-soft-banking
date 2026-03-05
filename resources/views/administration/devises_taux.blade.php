@extends('layouts.app')

@section('page_title', 'Gestion des devises et taux')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Devises / Taux')

@section('content')
    <div class="container-fluid">
        <ul class="nav nav-tabs mb-3" id="devisesTauxTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="devise-tab" data-toggle="tab" href="#devise" role="tab"
                    aria-controls="devise" aria-selected="true">Devise</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="taux-tab" data-toggle="tab" href="#taux" role="tab" aria-controls="taux"
                    aria-selected="false">Taux d'échange</a>
            </li>
        </ul>
        <div class="tab-content" id="devisesTauxTabsContent">
            <div class="tab-pane fade show active" id="devise" role="tabpanel" aria-labelledby="devise-tab">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card mb-4">
                            <div class="card-header pb-0">
                                <h5>Ajouter une devise</h5>
                            </div>
                            <div class="card-body">
                                <form id="deviseForm">
                                    @csrf
                                    <div class="form-group">
                                        <label for="code_iso">Code ISO</label>
                                        <input type="text" name="code_iso" id="code_iso" class="form-control" maxlength="3"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="nom">Nom</label>
                                        <input type="text" name="nom" id="nom" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="symbole">Symbole</label>
                                        <input type="text" name="symbole" id="symbole" class="form-control" maxlength="5"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="est_reference">Devise de référence ?</label>
                                        <select name="est_reference" id="est_reference" class="form-control">
                                            <option value="0">Non</option>
                                            <option value="1">Oui</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h5>Liste des devises</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="devisesTable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Code ISO</th>
                                                <th>Nom</th>
                                                <th>Symbole</th>
                                                <th>Référence</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($devises as $devise)
                                                <tr>
                                                    <td>{{ $devise->code_iso }}</td>
                                                    <td>{{ $devise->nom }}</td>
                                                    <td>{{ $devise->symbole }}</td>
                                                    <td>{{ $devise->est_reference ? 'Oui' : 'Non' }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger delete-devise-btn"
                                                            data-id="{{ $devise->code_iso }}" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="taux" role="tabpanel" aria-labelledby="taux-tab">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card mb-4">
                            <div class="card-header pb-0">
                                <h5>Ajouter un taux de change</h5>
                            </div>
                            <div class="card-body">
                                <form id="tauxForm">
                                    @csrf
                                    <div class="form-group">
                                        <label for="devise_source">Devise source</label>
                                        <select name="devise_source" id="devise_source" class="form-control" required>
                                            @foreach($devises as $devise)
                                                <option value="{{ $devise->code_iso }}">{{ $devise->nom }}
                                                    ({{ $devise->code_iso }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="devise_destination">Devise destination</label>
                                        <select name="devise_destination" id="devise_destination" class="form-control"
                                            required>
                                            @foreach($devises as $devise)
                                                <option value="{{ $devise->code_iso }}">{{ $devise->nom }}
                                                    ({{ $devise->code_iso }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="taux">Taux</label>
                                        <input type="number" step="0.0001" name="taux" id="taux" class="form-control"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h5>Liste des taux de change</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tauxTable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Source</th>
                                                <th>Destination</th>
                                                <th>Taux</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($taux as $t)
                                                <tr>
                                                    <td>{{ $t->devise_source }}</td>
                                                    <td>{{ $t->devise_destination }}</td>
                                                    <td>{{ $t->taux }}</td>
                                                    <td>{{ $t->date_application }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger delete-taux-btn"
                                                            data-id="{{ $t->id }}" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
        $(function () {
            // 1. Initialisation de la table (déplacé ici pour être sûr que le DOM est prêt)
            var $devisesTable = $('#devisesTable');
            $devisesTable.addClass('app-table');
            
            $devisesTable.on('click', 'tbody tr', function () {
                $devisesTable.find('tbody tr').removeClass('datatable-selected-row');
                $(this).addClass('datatable-selected-row');
            });

            var $tauxTable = $('#tauxTable');
            $tauxTable.addClass('app-table');
            
            $tauxTable.on('click', 'tbody tr', function () {
                $tauxTable.find('tbody tr').removeClass('datatable-selected-row');
                $(this).addClass('datatable-selected-row');
            });

            // 2. Soumission AJAX du formulaire devise
            $('#deviseForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = "{{ route('administration.devises-taux.storeDevise') }}";
                var formData = form.serialize();
                
                $.post(url, formData)
                    .done(function (response) {
                        showSystemMessage('success', 'Devise ajoutée avec succès !');
                        form[0].reset();
                        setTimeout(function () { window.location.reload(); }, 1500);
                    })
                    .fail(function (xhr) {
                        let msg = 'Erreur lors de l\'enregistrement de la devise.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = '';
                            $.each(xhr.responseJSON.errors, function (key, errors) {
                                errors.forEach(function (error) {
                                    msg += '<div>' + error + '</div>';
                                });
                            });
                        }
                        showSystemMessage('error', msg);
                    });
            });

            // 3. Soumission AJAX du formulaire taux d'échange
            $('#tauxForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = "{{ route('administration.devises-taux.storeTaux') }}";
                var formData = form.serialize();
                
                $.post(url, formData)
                    .done(function (response) {
                        showSystemMessage('success', 'Taux ajouté avec succès !');
                        form[0].reset();
                        setTimeout(function () { window.location.reload(); }, 1500);
                    })
                     .fail(function (xhr) {
                        let msg = 'Erreur lors de l\'enregistrement du taux.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = '';
                            $.each(xhr.responseJSON.errors, function (key, errors) {
                                errors.forEach(function (error) {
                                    msg += '<div>' + error + '</div>';
                                });
                            });
                        }
                        showSystemMessage('error', msg);
                    });
            });

            // 4. Suppression AJAX d'un taux d'échange
            // Utilisation de @csrf pour le token de suppression
            $('#devisesTauxTabsContent').on('click', '.delete-taux-btn', function (e) {
                e.preventDefault();
                var btn = $(this);
                var tauxId = btn.data('id');
                var deleteUrl = "{{ route('administration.devises-taux.destroyTaux', ['id' => 'TAUX_ID']) }}".replace('TAUX_ID', tauxId);
                
                showUniversalConfirm('Voulez-vous vraiment supprimer ce taux ?', function () {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" }, // Plus fiable que d'aller chercher un input
                        success: function (response) {
                            if (typeof showSystemMessage === 'function') {
                                showSystemMessage('success', response.message || 'Supprimé !');
                            }
                            setTimeout(function () { window.location.reload(); }, 1200);
                        },
                        error: function (xhr) { /* ... */ }
                    });
                });
            });

            // 5. Suppression AJAX d'une devise
            $('#devisesTable').on('click', '.delete-devise-btn', function (e) {
                e.preventDefault();
                var codeIso = $(this).data('id');
                var deleteUrl = "{{ route('administration.devises-taux.destroyDevise', ['code_iso' => 'DEV_CODE']) }}".replace('DEV_CODE', codeIso);
                
                showUniversalConfirm('Voulez-vous vraiment supprimer cette devise ?', function () {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (response) {
                            if (typeof showSystemMessage === 'function') {
                                showSystemMessage('success', 'Devise supprimée !');
                            }
                            setTimeout(function () { window.location.reload(); }, 1500);
                        }
                    });
                });
            });
        });
    </script>
@endpush
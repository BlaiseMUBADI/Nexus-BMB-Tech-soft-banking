@push('js')
<script>
$(function() {
    // Disparition automatique de l'alerte de succès après 2,5s
    setTimeout(function() {
        $('.alert-success').alert('close');
    }, 2500);
});
</script>
@endpush
@extends('layouts.app')

@section('page_title', 'Services / Postes')
@section('breadcrumb_parent', 'Services')
@section('breadcrumb', 'Postes')

@section('content')


@push('js')
<script>
$(document).ready(function() {
    // DataTable init (pagination + recherche, sans boutons d'export)
    $('#servicesTable').DataTable({
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

    // Sélection d'un service
    $('#servicesTable tbody').on('click', 'tr', function() {
        var serviceId = $(this).data('service-id');
        if (!serviceId) return;
        
        // Highlight moderne (sans dépendre de table-primary)
        $('#servicesTable tbody tr').removeClass('datatable-selected-row');
        $(this).addClass('datatable-selected-row');
        // Charger les postes via AJAX
        $.get('/rh/services/' + serviceId + '/postes-ajax', function(data) {
            $('#postesSection').html(data);
        });
    });

    // Soumission AJAX du formulaire d'ajout de poste
    $(document).on('submit', '.form-ajout-poste', function(e) {
        e.preventDefault();
        var form = $(this);
        var serviceId = form.data('service-id');
        var url = form.attr('action');
        var formData = form.serialize();
        $.post(url, formData)
            .done(function(response) {
                // Afficher le modal de succès
                $('#confirmationModalBody').text(response.message);
                $('#confirmationModal').modal('show');
                // Recharger la liste des postes
                $.get('/rh/services/' + serviceId + '/postes-ajax', function(data) {
                    $('#postesSection').html(data);
                });
            })
            .fail(function(xhr) {
                let msg = 'Erreur lors de l\'ajout du poste.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                $('#confirmationModalBody').text(msg);
                $('#confirmationModal').modal('show');
            });
    });

    // Suppression d'un service (avec modal de confirmation universel)
    $('.btn-delete-service').on('click', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        showUniversalConfirm('Voulez-vous vraiment supprimer ce service ?', function() {
            $.ajax({
                url: '/rh/services/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showSystemMessage('success', 'Service supprimé avec succès.');
                    setTimeout(function() {
                        $('#systemMessageModal').modal('hide');
                        location.reload();
                    }, 1200);
                },
                error: function(xhr) {
                    let msg = 'Erreur lors de la suppression du service.';
                    if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    // Affiche aussi le message technique pour le débogage
                    let tech = '';
                    if(xhr.responseText && xhr.responseText.length > 0) {
                        tech = '<br><small style="color:#aaa;word-break:break-all;">' + xhr.responseText + '</small>';
                    }
                    showSystemMessage('error', msg + tech);
                }
            });
        }, 'Confirmation de suppression');
    });
    // Soumission AJAX du formulaire d'ajout de service
    $('#formAjoutService').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();
        $.post(url, formData)
            .done(function(response) {
                // Message stylé avec icône
                $('#confirmationModalBody').html('<span class="text-success"><i class="fas fa-check-circle fa-2x mr-2"></i>Service ajouté avec succès.</span>');
                $('#confirmationModal').modal('show');
                // Recharger la page automatiquement après 1,5s sans attendre le clic sur OK
                setTimeout(function() {
                    $('#confirmationModal').modal('hide');
                    location.reload();
                }, 1500);
            })
            .fail(function(xhr) {
                let msg = 'Erreur lors de l\'ajout du service.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                $('#confirmationModalBody').html('<span class="text-danger"><i class="fas fa-times-circle fa-2x mr-2"></i>' + msg + '</span>');
                $('#confirmationModal').modal('show');
            });
    });
});
</script>
@endpush


    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Services</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive" style="max-height: 350px; min-height: 200px; overflow-y: auto;">
                        <table id="servicesTable" class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom du service</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                <tr data-service-id="{{ $service->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $service->nom }}</td>
                                    <td>{{ $service->description }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger btn-delete-service" data-id="{{ $service->id }}">Supprimer</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucun service enregistré.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <form id="formAjoutService" method="POST" action="{{ route('services.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="serviceName">Nom du service</label>
                            <input type="text" name="nom" class="form-control" id="serviceName" placeholder="Entrer le nom du service" required>
                        </div>
                        <div class="form-group">
                            <label for="serviceDesc">Description</label>
                            <textarea name="description" class="form-control" id="serviceDesc" rows="3" placeholder="Entrer la description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle mr-1"></i>Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Postes</h5>
                </div>
                <div class="card-body">
                    <div id="postesSection">
                        <p class="text-muted">Sélectionnez un service à gauche pour afficher ses postes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('css')
<style>
/* Couleurs modernes pour la sélection et le survol */
#servicesTable tbody tr.datatable-selected-row {
    background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%) !important;
    color: #fff !important;
    box-shadow: 0 2px 8px rgba(99,102,241,0.12);
    transition: background 0.3s, color 0.3s;
}
#servicesTable tbody tr:hover:not(.datatable-selected-row) {
    background: linear-gradient(90deg, #06b6d4 0%, #3b82f6 100%) !important;
    color: #fff !important;
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(59,130,246,0.10);
    transition: background 0.3s, color 0.3s;
}
</style>
@endsection




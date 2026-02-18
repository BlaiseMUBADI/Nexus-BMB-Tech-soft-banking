@extends('layouts.app')

@section('page_title', 'Test route')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Test')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-4 text-success">Test OK !</h1>
    <p class="lead">La route personnalisée fonctionne parfaitement.</p>
</div>
@endsection


@push('js')
<script>
$(document).ready(function() {
    // DataTable init (pagination + recherche, sans boutons d'export)
    $('#servicesTable').DataTable({
        paging: true,
        searching: true,
        info: true,
        lengthChange: false,
        language: {
            url: '/plugins/datatables/i18n/fr-FR.json',
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent"
            },
            search: "Recherche :",
        }
    });

    // Sélection d'un service
    $('#servicesTable tbody').on('click', 'tr', function() {
        var serviceId = $(this).data('service-id');
        if (!serviceId) return;
        // Highlight (synchronise avec DataTables)
        $('#servicesTable tbody tr').removeClass('table-primary datatable-selected-row');
        $(this).addClass('table-primary datatable-selected-row');
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

    // Suppression d'un service (à compléter côté backend)
    $('.btn-delete-service').on('click', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        if(confirm('Supprimer ce service ?')) {
            // À compléter : AJAX ou formulaire
        }
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

@section('css')
<style>
    /*
/* Couleur moderne pour la sélection (harmonisée avec clients) 
#servicesTable tbody tr.table-primary,
#servicesTable tbody tr.datatable-selected-row {
    background: linear-gradient(90deg, #6366f1 0%, #60a5fa 100%) !important;
    color: #fff !important;
    transition: background 0.3s, color 0.3s;
}
/* Couleur fluide pour le survol 
#servicesTable tbody tr:not(.table-primary):hover {
    background: linear-gradient(90deg, #06b6d4 0%, #3b82f6 100%) !important;
    color: #fff !important;
    cursor: pointer;
    transition: background 0.3s, color 0.3s;
}*/
</style>
@endsection

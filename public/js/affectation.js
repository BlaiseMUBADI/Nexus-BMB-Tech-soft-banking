// affectation.js : JS spécifique pour la page affectation

$(function () {
    setTimeout(function () {
        $('.alert-success').alert('close');
    }, 2500);
});

let selectedAgent = null;

$(document).ready(function () {
    $('#agentsTable').DataTable({
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
    // Sélection moderne
    $('#agentsTable tbody').on('click', 'tr', function () {
        if (!$(this).hasClass('service-group-row')) {
            $('#agentsTable tbody tr').removeClass('datatable-selected-row');
            $(this).addClass('datatable-selected-row');
            selectedAgent = $(this);
        }
    });
    $('#postesTable').DataTable({
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
    $('#postesTable tbody').on('click', 'tr', function () {
        if (!$(this).hasClass('service-group-row')) {
            $('#postesTable tbody tr').removeClass('datatable-selected-row');
            $(this).addClass('datatable-selected-row');
            if (selectedAgent) {
                $('#affectationModal').modal('show');
            }
        }
    });
});

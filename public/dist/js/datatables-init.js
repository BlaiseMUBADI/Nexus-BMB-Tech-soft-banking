        // Style pour surligner la ligne au survol
        var style3 = document.createElement('style');
        style3.innerHTML = '.datatable-hover tbody tr:hover:not(.datatable-selected-row) { background: #42a5f5 !important; color: #fff !important; cursor: pointer; box-shadow: 0 1px 4px rgba(66,165,245,0.10); }';
        document.head.appendChild(style3);
    // Style pour aligner le label et l'input sur la même ligne
    var style2 = document.createElement('style');
    style2.innerHTML = '.dataTables_filter label { display: flex; align-items: center; gap: 6px; margin-bottom: 0; } .dataTables_filter input { display: inline-block; width: 180px; margin-bottom: 0; vertical-align: middle; }';
    document.head.appendChild(style2);
$(function () {
    // Style pour la ligne sélectionnée
    var style = document.createElement('style');
    style.innerHTML = '.datatable-selected-row { background: #1565c0 !important; color: #fff !important; box-shadow: 0 2px 8px rgba(21,101,192,0.12); }';
    document.head.appendChild(style);

    $('.datatable').each(function () {
        $(this).addClass('datatable-hover');
        var $table = $(this);
        var btnContainer = $table.data('buttons-container') || null;
        var dt = $table.DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copier'
                },
                {
                    extend: 'csv',
                    text: 'CSV'
                },
                {
                    extend: 'excel',
                    text: 'Excel'
                },
                {
                    extend: 'pdf',
                    text: 'PDF'
                },
                {
                    extend: 'print',
                    text: 'Imprimer'
                },
                {
                    extend: 'colvis',
                    text: 'Colonnes'
                }
            ],
            language: {
                url: '/plugins/datatables/i18n/fr-FR.json',
                emptyTable: "Aucune donnée disponible dans le tableau",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
                infoFiltered: "(filtré à partir de _MAX_ entrées au total)",
                lengthMenu: "Afficher _MENU_ entrées",
                loadingRecords: "Chargement...",
                processing: "Traitement...",
                search: "Recherche :",
                zeroRecords: "Aucun enregistrement correspondant trouvé",
                paginate: {
                    first: "Premier",
                    last: "Dernier",
                    next: "Suivant",
                    previous: "Précédent"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                },
                buttons: {
                    copy: "Copier",
                    csv: "CSV",
                    excel: "Excel",
                    pdf: "PDF",
                    print: "Imprimer",
                    colvis: "Colonnes"
                }
            },
            dom: 'Bfrtip' // Pour que la barre de recherche soit générée
        });
        // Placer les boutons et la barre de recherche dans le même bloc stylé
        setTimeout(function() {
            if (btnContainer) {
                dt.buttons().container().appendTo(btnContainer);
                $(btnContainer).show();
            } else {
                dt.buttons().container().insertBefore($table);
            }
            // Déplacer dynamiquement tout le bloc .dataTables_filter dans le conteneur fusionné (préserve le JS natif)
            var $search = $table.closest('.card-body').find('.dataTables_filter');
            if ($search.length && $('#clients-table-search').length) {
                // Modifier uniquement le texte du label sans toucher à l'input
                var $label = $search.find('label');
                if ($label.length) {
                    var nodes = $label.contents().toArray();
                    if (nodes.length > 0 && nodes[0].nodeType === 3) {
                        nodes[0].textContent = 'Recherche : ';
                    }
                }
                $('#clients-table-search').empty().append($search);
            }
            // Sélection visuelle de la ligne au clic sur une cellule
            $table.find('tbody').on('click', 'td', function() {
                $table.find('tr').removeClass('datatable-selected-row');
                $(this).closest('tr').addClass('datatable-selected-row');
            });
        }, 100);
    });
});

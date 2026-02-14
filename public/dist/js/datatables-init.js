$(function () {
    // Style pour la ligne sélectionnée
    var style = document.createElement('style');
    style.innerHTML = '.datatable-selected-row { background: #1e88e5 !important; color: #fff !important; }';
    document.head.appendChild(style);

    $('.datatable').each(function () {
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
                url: '/plugins/datatables/i18n/fr-FR.json'
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
            // Déplacer dynamiquement la barre de recherche dans le conteneur fusionné
            var $search = $table.closest('.card-body').find('.dataTables_filter');
            if ($search.length && $('#clients-table-search').length) {
                // On veut le label et l'input strictement sur la même ligne
                var $label = $search.find('label');
                var $input = $label.find('input');
                $label.html('');
                $label.append('<span style="margin-right:6px;">Recherche :</span>');
                $input.css({'display':'inline-block','width':'180px','margin-bottom':'0','vertical-align':'middle'});
                $label.append($input);
                $label.css({'display':'flex','align-items':'center','gap':'6px','margin-bottom':'0'});
                $('#clients-table-search').empty().append($label);
                $search.remove();
            }
            // Sélection visuelle de la ligne au clic sur une cellule
            $table.find('tbody').on('click', 'td', function() {
                $table.find('tr').removeClass('datatable-selected-row');
                $(this).closest('tr').addClass('datatable-selected-row');
            });
        }, 100);
    });
});

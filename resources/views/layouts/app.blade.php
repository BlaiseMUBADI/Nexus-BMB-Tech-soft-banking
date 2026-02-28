<!DOCTYPE html>
<html lang="fr">

<head>
    @include('layouts.head')
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        @include('layouts.navbar')
        @include('layouts.sidebar')

        <main class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page_title', 'Dashboarde')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">@yield('breadcrumb_parent', 'Accueil')</a></li>
                                <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            @yield('content')

            <!-- Modal système universel (succès, erreur, alerte, info) -->
            <div class="modal fade" id="systemMessageModal" tabindex="-1" role="dialog"
                aria-labelledby="systemMessageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header" id="systemMessageModalHeader">
                            <h5 class="modal-title" id="systemMessageModalLabel">Message</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body d-flex align-items-center" id="systemMessageModalBody">
                            <!-- Icône + message injectés par JS -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmation universel -->
            <div class="modal fade" id="universalConfirmModal" tabindex="-1" role="dialog"
                aria-labelledby="universalConfirmModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header"
                            style="background: #f3f4f6; color: #222; border-bottom: 2px solid #fde68a;">
                            <h5 class="modal-title" id="universalConfirmModalLabel">Confirmation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body d-flex align-items-center" id="universalConfirmModalBody"
                            style="background: #fff; color: #222; min-height: 70px;">
                            <!-- Icône + message injectés par JS -->
                        </div>
                        <div class="modal-footer" style="background: #fff; border-top: 1px solid #eee;">
                            <button type="button" class="btn"
                                style="background: #cbd5e1; color: #222; border-radius: 4px; min-width: 110px; border: 1px solid #cbd5e1; font-weight: 500;"
                                data-dismiss="modal">
                                <i class="fas fa-times-circle mr-1"></i> Annuler
                            </button>
                            <button type="button" class="btn" id="universalConfirmModalOk"
                                style="background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%); color: #fff; border-radius: 4px; min-width: 120px; border: none; font-weight: 500;">
                                <i class="fas fa-check-circle mr-1"></i> Confirmer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
        @include('layouts.footer')
    </div>

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>

    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="{{ asset('plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

    <!-- DataTables JS (global) -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('dist/js/pages/dashboard2.js') }}"></script>

    <!-- DataTables global init -->
    <script>
        window.DATATABLES_LANG_URL = "{{ asset('plugins/datatables/i18n/fr-FR.json') }}";
    </script>
    <script src="{{ asset('dist/js/datatables-init.js') }}"></script>

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Select2 JS -->
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- Formulaire logout caché pour POST -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        /*
        * Gestion de l'inactivité : déconnexion après 2 minutes d'inactivité (POST)
        */
        let inactivityTimeout;
        function resetInactivityTimer() {
            clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(function() {
                document.getElementById('logout-form').submit();
            }, 60 * 60 * 1000); // 1 hour
        }
        window.onload = resetInactivityTimer;
        document.onmousemove = resetInactivityTimer;
        document.onkeypress = resetInactivityTimer;
        document.onclick = resetInactivityTimer;
        document.onscroll = resetInactivityTimer;

        /**
         * Affiche un message système global (succès, erreur, alerte, info)
         * @param {string} type - success | error | warning | info
         * @param {string} message - Le texte à afficher
         * @param {string} [title] - Titre du modal (optionnel)
         */
        function showSystemMessage(type, message, title) {
            let header = $('#systemMessageModalHeader');
            let label = $('#systemMessageModalLabel');
            let body = $('#systemMessageModalBody');
            let icon = '';
            let bg = '';
            let defaultTitle = '';
            switch (type) {
                case 'success':
                    icon = '<i class="fas fa-check-circle fa-2x text-success mr-2"></i>';
                    bg = 'bg-success text-white';
                    defaultTitle = 'Succès';
                    break;
                case 'error':
                    icon = '<i class="fas fa-times-circle fa-2x text-danger mr-2"></i>';
                    bg = 'bg-danger text-white';
                    defaultTitle = 'Erreur';
                    break;
                case 'warning':
                    icon = '<i class="fas fa-exclamation-triangle fa-2x text-warning mr-2"></i>';
                    bg = 'bg-warning text-dark';
                    defaultTitle = 'Alerte';
                    break;
                default:
                    icon = '<i class="fas fa-info-circle fa-2x text-info mr-2"></i>';
                    bg = 'bg-info text-white';
                    defaultTitle = 'Information';
            }
            header.removeClass().addClass('modal-header ' + bg);
            label.text(title || defaultTitle);
            body.html(icon + '<span>' + message + '</span>');
            $('#systemMessageModal').modal('show');
        }
        // Alias pour compatibilité : showAppModal = showSystemMessage
        window.showAppModal = showSystemMessage;

        /**
         * Affiche un modal de confirmation universel
         * @param {string} message - Le texte à afficher
         * @param {function} onConfirm - Callback appelé si l'utilisateur confirme
         * @param {string} [title] - Titre du modal (optionnel)
         */
        function showUniversalConfirm(message, onConfirm, title) {
            $('#universalConfirmModalLabel').text(title || 'Confirmation');
            $('#universalConfirmModalBody').html('<i class="fas fa-question-circle fa-2x" style="color:#fbbf24; margin-right:12px;"></i><span>' + message + '</span>');
            $('#universalConfirmModal').modal('show');
            // Nettoyer les anciens handlers
            $('#universalConfirmModalOk').off('click');
            // Ajouter le handler de confirmation
            $('#universalConfirmModalOk').on('click', function () {
                $('#universalConfirmModal').modal('hide');
                if (typeof onConfirm === 'function') onConfirm();
            });
        }
    </script>
    @stack('js')
</body>

</html>

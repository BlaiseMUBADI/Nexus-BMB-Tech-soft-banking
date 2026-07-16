<!DOCTYPE html>
<html lang="fr">

<head>
    @include('layouts.head')
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed">
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

            {{-- ============================================================
                 MODALS SYSTÈME GLOBAUX — utilisables depuis n'importe quelle page
                 via : showSystemMessage(type, message) et showUniversalConfirm(...)
                 ============================================================ --}}

            {{-- Modal système : succès / erreur / alerte / info --}}
            <div class="modal fade" id="systemMessageModal" tabindex="-1" role="dialog"
                 aria-labelledby="systemMessageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content border-0 shadow-lg">

                        {{-- Header : couleur et icône injectées par JS --}}
                        <div class="modal-header rounded-top" id="systemMessageModalHeader"
                             style="border-bottom:none; padding-bottom:8px;">
                            <div class="d-flex align-items-center">
                                <span id="systemMessageModalIcon" class="mr-2" style="font-size:1.4rem;"></span>
                                <h5 class="modal-title mb-0 font-weight-bold" id="systemMessageModalLabel">Message</h5>
                            </div>
                            <button type="button" class="close ml-auto" id="systemMessageModalClose"
                                    data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        {{-- Body : message textuel --}}
                        <div class="modal-body pt-2 pb-2" id="systemMessageModalBody"
                             style="font-size:0.97rem; line-height:1.5;">
                            {{-- contenu injecté par JS --}}
                        </div>

                        {{-- Footer --}}
                        <div class="modal-footer justify-content-center" style="border-top:none; padding-top:4px;">
                            <button type="button" class="btn btn-sm px-4" id="systemMessageModalBtn"
                                    data-dismiss="modal">OK</button>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Modal de confirmation universel (suppression / action irréversible) --}}
            <div class="modal fade" id="universalConfirmModal" tabindex="-1" role="dialog"
                 aria-labelledby="universalConfirmModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-lg">

                        {{-- Header dynamique (danger par défaut) --}}
                        <div class="modal-header rounded-top" id="universalConfirmModalHeader"
                             style="border-bottom:none;">
                            <div class="d-flex align-items-center">
                                <span id="universalConfirmModalHeaderIcon" class="mr-2"
                                      style="font-size:1.3rem;">
                                    <i class="fas fa-trash-alt"></i>
                                </span>
                                <h5 class="modal-title mb-0 font-weight-bold" id="universalConfirmModalLabel">
                                    Confirmer la suppression
                                </h5>
                            </div>
                            <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="modal-body text-center py-3" id="universalConfirmModalBody">
                            <div class="mb-3" id="universalConfirmModalBodyIcon">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                            </div>
                            <p class="mb-1" id="universalConfirmModalText"
                               style="font-size:0.97rem; line-height:1.5;">
                                Êtes-vous sûr de vouloir effectuer cette action ?
                            </p>
                            <small id="universalConfirmModalWarning"
                                   class="font-weight-bold text-warning">
                                Cette action est <u>irréversible</u>.
                            </small>
                        </div>

                        {{-- Footer --}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> Annuler
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="universalConfirmModalOk">
                                <i class="fas fa-check mr-1" id="universalConfirmModalOkIcon"></i>
                                <span id="universalConfirmModalOkLabel">Supprimer</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Modal expiration de session --}}
            <div class="modal fade" id="sessionExpiryModal" tabindex="-1" role="dialog"
                 aria-labelledby="sessionExpiryModalLabel" aria-hidden="true"
                 data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-warning text-dark rounded-top"
                             style="border-bottom:none;">
                            <div class="d-flex align-items-center">
                                <span class="mr-2" style="font-size:1.4rem;">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <h5 class="modal-title mb-0 font-weight-bold" id="sessionExpiryModalLabel">
                                    Session sur le point d&apos;expirer
                                </h5>
                            </div>
                        </div>
                        <div class="modal-body text-center py-3">
                            <i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>
                            <p class="mb-2" style="font-size:0.97rem; line-height:1.5;">
                                Vous serez d&eacute;connect&eacute;(e) dans
                            </p>
                            <div id="sessionExpiryCountdown"
                                 style="font-size:2.2rem; font-weight:bold; color:#e67e22;">60</div>
                            <p class="mt-1 mb-0 text-muted" style="font-size:0.85rem;">
                                seconde(s) pour inactivit&eacute;
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center" style="border-top:none;">
                            <button type="button" class="btn btn-success btn-sm px-4"
                                    id="sessionExpiryStayBtn">
                                <i class="fas fa-check mr-1"></i> Rester connect&eacute;(e)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Emplacement pour les modals spécifiques aux pages --}}
            @stack('modals')

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
         * ================================================================
         * FIX GLOBAL — BOM UTF-8 + jQuery/Laravel JSON parsing
         * ================================================================
         * Laravel retourne parfois HTTP 200 avec un BOM (\uFEFF) ou des
         * espaces avant le JSON → jQuery échoue à parser → route vers .fail()
         * même si la réponse est un succès. Ce converter nettoie
         * automatiquement toutes les réponses JSON de l'application.
         * ================================================================
         */
        $.ajaxSetup({
            converters: {
                'text json': function (data) {
                    return JSON.parse(data.replace(/^\uFEFF/, '').trim());
                }
            }
        });

        /*
        * =============================================================
        * GESTION DE L'INACTIVITÉ — architecture mixte serveur + client
        * • Serveur  : middleware CheckInactivity (timeout configurable)
        * • Client   : avertissement modal 60s avant expiration
        *              bouton "Rester connecté" → appel heartbeat
        *              décompte visible, déconnexion propre si aucune action
        * =============================================================
        */
        (function () {
            var TIMEOUT_MS    = {{ (int) config('session.inactivity_timeout', 600) }} * 1000;
            var WARNING_MS    = 60 * 1000;   // avertir 60s avant
            var ACTIVE_DELAY  = 20 * 1000;   // débounce événements activité

            var warnTimer     = null;
            var logoutTimer   = null;
            var countdownInt  = null;
            var lastActivity  = Date.now();
            var warningActive = false;

            function resetTimers() {
                if (warningActive) return; // ne pas interrompre l'avertissement
                lastActivity = Date.now();
                clearTimeout(warnTimer);
                clearTimeout(logoutTimer);
                warnTimer   = setTimeout(showWarning,  TIMEOUT_MS - WARNING_MS);
                logoutTimer = setTimeout(doLogout,     TIMEOUT_MS);
            }

            function showWarning() {
                warningActive = true;
                var secs = Math.round(WARNING_MS / 1000);
                $('#sessionExpiryCountdown').text(secs);
                $('#sessionExpiryModal').modal('show');
                clearInterval(countdownInt);
                countdownInt = setInterval(function () {
                    secs--;
                    $('#sessionExpiryCountdown').text(Math.max(secs, 0));
                    if (secs <= 0) {
                        clearInterval(countdownInt);
                    }
                }, 1000);
            }

            function doLogout() {
                clearInterval(countdownInt);
                $('#sessionExpiryModal').modal('hide');
                document.getElementById('logout-form').submit();
            }

            $('#sessionExpiryStayBtn').on('click', function () {
                clearInterval(countdownInt);
                $('#sessionExpiryModal').modal('hide');
                // Appel heartbeat pour raffraîchir la session côté serveur
                $.ajax({
                    url  : '{{ route("session.heartbeat") }}',
                    type : 'POST',
                    data : { _token: '{{ csrf_token() }}' },
                    success: function () {
                        warningActive = false;
                        resetTimers();
                    },
                    error: function () {
                        // Si le heartbeat échoue, la session est probablement déjà expirée
                        doLogout();
                    }
                });
            });

            // Surveiller l'activité utilisateur (débounce)
            var activityDebounce = null;
            function onActivity() {
                if (warningActive) return;
                clearTimeout(activityDebounce);
                activityDebounce = setTimeout(resetTimers, ACTIVE_DELAY);
            }
            ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function (ev) {
                document.addEventListener(ev, onActivity, { passive: true });
            });

            // Démarrage
            resetTimers();
        })();

        /**
         * ================================================================
         * MODALS SYSTÈME — fonctions globales disponibles sur toutes les pages
         * ================================================================
         */

        /**
         * Affiche un modal système typé (succès, erreur, alerte, info)
         *
         * @param {string} type    - 'success' | 'error' | 'warning' | 'info'
         * @param {string} message - Texte du message
         * @param {string} [title] - Titre (optionnel, sinon titre par défaut selon type)
         *
         * Usage : showSystemMessage('success', 'Agent créé avec succès.')
         *         showSystemMessage('error',   'Une erreur est survenue.', 'Erreur critique')
         */
        function showSystemMessage(type, message, title) {
            var configs = {
                success: {
                    bg:         'bg-success',
                    textHeader: 'text-white',
                    icon:       'fas fa-check-circle',
                    iconColor:  '#28a745',
                    btnClass:   'btn-success',
                    closeColor: '#fff',
                    title:      'Succès'
                },
                error: {
                    bg:         'bg-danger',
                    textHeader: 'text-white',
                    icon:       'fas fa-times-circle',
                    iconColor:  '#dc3545',
                    btnClass:   'btn-danger',
                    closeColor: '#fff',
                    title:      'Erreur'
                },
                warning: {
                    bg:         'bg-warning',
                    textHeader: 'text-dark',
                    icon:       'fas fa-exclamation-triangle',
                    iconColor:  '#ffc107',
                    btnClass:   'btn-warning',
                    closeColor: '#212529',
                    title:      'Avertissement'
                },
                info: {
                    bg:         'bg-info',
                    textHeader: 'text-white',
                    icon:       'fas fa-info-circle',
                    iconColor:  '#17a2b8',
                    btnClass:   'btn-info',
                    closeColor: '#fff',
                    title:      'Information'
                }
            };

            var cfg = configs[type] || configs['info'];

            // Header
            $('#systemMessageModalHeader')
                .attr('class', 'modal-header rounded-top ' + cfg.bg + ' ' + cfg.textHeader)
                .css('border-bottom', 'none');

            // Icône dans le header
            $('#systemMessageModalIcon').html('<i class="' + cfg.icon + '"></i>');

            // Bouton fermer header (couleur adaptée)
            $('#systemMessageModalClose').css('color', cfg.closeColor);

            // Titre
            $('#systemMessageModalLabel').text(title || cfg.title);

            // Body : message
            $('#systemMessageModalBody').html(
                '<i class="' + cfg.icon + ' fa-2x mr-3" style="color:' + cfg.iconColor + '; flex-shrink:0;"></i>' +
                '<span>' + message + '</span>'
            ).css({ 'display': 'flex', 'align-items': 'center' });

            // Bouton OK
            $('#systemMessageModalBtn')
                .attr('class', 'btn btn-sm px-4 ' + cfg.btnClass);

            $('#systemMessageModal').modal('show');
        }

        // Alias de compatibilité
        window.showAppModal = showSystemMessage;

        /**
         * ================================================================
         * GESTIONNAIRES D'ERREURS AJAX — disponibles sur toutes les pages
         * ================================================================
         */

        /**
         * Envoie silencieusement une erreur dans storage/logs/laravel.log
         * Sans afficher de modal — idéal pour le polling et les tableaux.
         *
         * @param {string} msg    - Message d'erreur
         * @param {string} ctx    - Contexte de l'action (ex: 'Chargement demandes')
         * @param {number} status - Code HTTP (ex: 500)
         */
        function logFrontendError(msg, ctx, status) {
            $.post('{{ route("log.frontend.error") }}', {
                message : msg,
                context : ctx    || '',
                status  : status || 0
            }).fail(function() { /* silencieux */ });
        }
        window.logFrontendError = logFrontendError;

        /**
         * Gestionnaire global des erreurs AJAX.
         * - Affiche showSystemMessage('error', message)
         * - Envoie l'erreur dans les logs Laravel
         *
         * Usage : .fail(function(xhr) { handleAjaxFail(xhr, 'Contexte'); })
         *
         * @param {jqXHR}  xhr - L'objet XHR du .fail()
         * @param {string} ctx - Description de l'action qui a échoué
         */
        function handleAjaxFail(xhr, ctx) {
            var msg = 'Une erreur est survenue.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
            } else if (xhr.status === 401 || xhr.status === 403) {
                msg = 'Accès non autorisé (code ' + xhr.status + ').';
            } else if (xhr.status === 419) {
                msg = 'Session expirée. Rechargez la page.';
            } else if (xhr.status === 0) {
                msg = 'Connexion perdue. Vérifiez votre réseau.';
            }
            showSystemMessage('error', msg);
            logFrontendError(msg, ctx || '', xhr.status);
        }
        window.handleAjaxFail = handleAjaxFail;

        /**
         * Affiche un modal de confirmation universel
         *
         * @param {string}   message   - Question/description de l'action
         * @param {function} onConfirm - Callback exécuté si l'utilisateur confirme
         * @param {object}   [opts]    - Options facultatives :
         *   opts.title       {string}  Titre du modal        (défaut: 'Confirmer la suppression')
         *   opts.btnLabel    {string}  Libellé bouton OK     (défaut: 'Supprimer')
         *   opts.btnClass    {string}  Classe CSS bouton OK  (défaut: 'btn-danger')
         *   opts.icon        {string}  Classe Font Awesome   (défaut: 'fas fa-trash-alt')
         *   opts.bodyIcon    {string}  Grande icône body     (défaut: 'fas fa-exclamation-triangle text-danger')
         *   opts.headerClass {string}  Classe header bg      (défaut: 'bg-danger text-white')
         *   opts.showWarning {boolean} Afficher avertissement irréversible (défaut: true)
         *
         * Usage (suppression) :
         *   showUniversalConfirm('Supprimer cet agent ?', function() { /* ... *\/ });
         *
         * Usage (action personnalisée) :
         *   showUniversalConfirm('Désactiver ce compte ?', function() { /* ... *\/ }, {
         *       title: 'Confirmer la désactivation',
         *       btnLabel: 'Désactiver',
         *       btnClass: 'btn-warning',
         *       icon: 'fas fa-ban',
         *       bodyIcon: 'fas fa-ban fa-3x text-warning',
         *       headerClass: 'bg-warning text-dark',
         *       showWarning: false
         *   });
         */
        function showUniversalConfirm(message, onConfirm, opts) {
            opts = opts || {};

            var title       = opts.title       || 'Confirmer la suppression';
            var btnLabel    = opts.btnLabel     || 'Supprimer';
            var btnClass    = opts.btnClass     || 'btn-danger';
            var hdrIcon     = opts.icon         || 'fas fa-trash-alt';
            var bodyIcon    = opts.bodyIcon     || 'fas fa-exclamation-triangle fa-3x text-danger';
            var headerClass = opts.headerClass  || 'bg-danger text-white';
            var showWarn    = (opts.showWarning !== false);

            // Header
            $('#universalConfirmModalHeader')
                .attr('class', 'modal-header rounded-top ' + headerClass)
                .css('border-bottom', 'none');

            // Icône dans le header
            $('#universalConfirmModalHeaderIcon').html('<i class="' + hdrIcon + '"></i>');

            // Titre
            $('#universalConfirmModalLabel').text(title);

            // Icône corps
            $('#universalConfirmModalBodyIcon').html('<i class="' + bodyIcon + '"></i>');

            // Texte
            $('#universalConfirmModalText').html(message);

            // Avertissement irréversible
            $('#universalConfirmModalWarning').toggle(showWarn);

            // Bouton OK
            $('#universalConfirmModalOk')
                .attr('class', 'btn btn-sm ' + btnClass);
            $('#universalConfirmModalOkLabel').text(btnLabel);

            // Close button color selon header
            var isLight = headerClass.indexOf('text-dark') !== -1;
            $('#universalConfirmModal .modal-header .close').css('color', isLight ? '#212529' : '#fff');

            // Nettoyer l'ancien handler puis enregistrer le nouveau
            $('#universalConfirmModalOk').off('click.universalConfirm').on('click.universalConfirm', function () {
                $('#universalConfirmModal').modal('hide');
                if (typeof onConfirm === 'function') onConfirm();
            });

            $('#universalConfirmModal').modal('show');
        }
    </script>
    <!-- Toastr (notifications) -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        toastr.options = {
            closeButton    : true,
            progressBar    : true,
            positionClass  : 'toast-top-right',
            timeOut        : 4000,
            extendedTimeOut: 1500,
        };
    </script>
    @stack('js')
</body>

</html>

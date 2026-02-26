<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NBTB') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon Banque -->
        <link rel="icon" type="image/png" href="{{ asset('dist/img/icon_vrailogoeben.png') }}" sizes="38x38">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
</div>

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

    <!-- jQuery et Bootstrap JS nécessaires pour le modal -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
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
        window.showAppModal = showSystemMessage;
    </script>
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    </body>
</html>

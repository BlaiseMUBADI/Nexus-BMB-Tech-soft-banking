<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ma Banque | AdminLTE</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
        <div class="content-wrapper">
            <div class="content-header">
            <div class="container-fluid">
                @yield('content')
            </div>
            </div>
        </div>
        </div>

        <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    </body>
</html>
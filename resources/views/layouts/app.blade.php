<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Banque Pro | @yield('title')</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
        @yield('css') 
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/" class="brand-link text-center">
            <span class="brand-text font-weight-bold">MA BANQUE</span>
            </a>
            <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link">
                    <i class="nav-icon fas fa-home"></i><p>Accueil</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/clients') }}" class="nav-link">
                    <i class="nav-icon fas fa-users"></i><p>Clients</p>
                    </a>
                </li>
                </ul>
            </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="content-header">
            <div class="container-fluid">
                @yield('content') </div>
            </div>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">Version 1.0</div>
            <strong>Copyright &copy; 2026.</strong>
        </footer>
        </div>

        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
        @yield('js') 
    </body>
</html>
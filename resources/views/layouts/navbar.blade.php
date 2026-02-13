{{--
    navbar.blade.php
    Rôle : Affiche la barre de navigation supérieure (navbar) de l’interface AdminLTE.
--}}
<nav class="main-header navbar navbar-expand navbar-dark">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ url('/') }}" class="nav-link">Accueil</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ url('/page1') }}" class="nav-link">Comptes</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ url('/page2') }}" class="nav-link">Transactions</a>
            </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
</nav>

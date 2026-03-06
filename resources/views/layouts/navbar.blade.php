{{--
    navbar.blade.php
    Rôle : Affiche la barre de navigation supérieure (navbar) de l’interface AdminLTE.
--}}


<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Tableau de bord</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- User Dropdown (placé à la fin) -->
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{ asset('dist/img/user1-128x128.jpg') }}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Brad Diesel
                                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">Appelez-moi quand vous pouvez...</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> Il y a 4 heures</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{ asset('dist/img/user8-128x128.jpg') }}" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                David KAYEMBE
                                <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">J'ai reçu ton message, frère</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> Il y a 4 heures</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{ asset('dist/img/user3-128x128.jpg') }}" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Nora Silvester
                                <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">Le sujet va ici</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> Il y a 4 heures</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">Voir tous les messages</a>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 nouveaux messages
                    <span class="float-right text-muted text-sm">3 minutes</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 demandes d'amis
                    <span class="float-right text-muted text-sm">12 heures</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 nouveaux rapports
                    <span class="float-right text-muted text-sm">2 jours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">Voir toutes les notifications</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
        <!-- User Dropdown -->
        @php /** @var \App\Models\User|null $authUser */ $authUser = Auth::user(); @endphp
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                @if($authUser && $authUser->agent && $authUser->agent->photo)
                    <img src="{{ route('agents.photo', basename($authUser->agent->photo)) }}?v={{ time() }}"
                         alt="Photo de profil"
                         class="img-size-32 img-circle mr-2"
                         style="height:32px;width:32px;object-fit:cover;">
                @else
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" alt="User Image" class="img-size-32 img-circle mr-2" style="height:32px;width:32px;object-fit:cover;">
                @endif
                <span class="d-none d-md-inline">
                    @if($authUser && $authUser->agent)
                        {{ $authUser->agent->prenom ?? '' }} {{ $authUser->agent->nom ?? 'Agent' }}
                    @else
                        Agent
                    @endif
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                @auth
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profil
                </a>
                @endauth
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0 m-0 border-0 bg-transparent">
                    @csrf
                    <button type="submit" class="btn btn-link dropdown-item" style="padding: 0; margin: 0; color: inherit;">
                        <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

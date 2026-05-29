{{--
    navbar.blade.php
    Rôle : Affiche la barre de navigation supérieure (navbar) de l’interface AdminLTE.
--}}

@php
    /** @var \App\Models\User|null $typedAuthUser */
    $typedAuthUser = $authUser instanceof \App\Models\User ? $authUser : null;
    $authAgent = $typedAuthUser?->agent;

    $resolveNotificationCategory = function ($notification) {
        $category = data_get($notification->data, 'category');
        if ($category) {
            return $category;
        }

        $actionUrl = (string) data_get($notification->data, 'action_url', '');
        $title = strtolower((string) data_get($notification->data, 'title', ''));
        $message = strtolower((string) data_get($notification->data, 'message', ''));
        $haystack = $actionUrl . ' ' . $title . ' ' . $message;

        return match (true) {
            str_contains($haystack, 'credit') => 'credit',
            str_contains($haystack, 'tresorerie') || str_contains($haystack, 'coffre') => 'tresorerie',
            str_contains($haystack, 'caisses') || str_contains($haystack, 'guichet') || str_contains($haystack, 'operation') => 'caisse',
            str_contains($haystack, 'role') || str_contains($haystack, 'permission') || str_contains($haystack, 'administration') => 'administration',
            default => 'systeme',
        };
    };

    $notificationCategoryUi = function (string $category) {
        return match ($category) {
            'credit' => ['label' => 'Credit', 'badge' => 'success'],
            'tresorerie' => ['label' => 'Tresorerie', 'badge' => 'warning'],
            'caisse' => ['label' => 'Caisse', 'badge' => 'info'],
            'administration' => ['label' => 'Administration', 'badge' => 'secondary'],
            default => ['label' => 'Systeme', 'badge' => 'dark'],
        };
    };
@endphp


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
                @if(($actionNotificationCount ?? 0) > 0)
                    <span class="badge badge-danger navbar-badge">{{ min(($actionNotificationCount ?? 0), 99) }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                @php
                    $actionNotifications = collect($latestUnreadNotifications ?? [])->filter(function ($notification) {
                        return in_array(data_get($notification->data, 'type'), ['warning', 'danger', 'action_required'], true);
                    });
                @endphp

                <span class="dropdown-item dropdown-header">{{ min(($actionNotificationCount ?? 0), 99) }} Actions requises</span>
                <div class="dropdown-divider"></div>

                @forelse($actionNotifications as $notification)
                    @php
                        $title = data_get($notification->data, 'title', 'Notification');
                        $message = data_get($notification->data, 'message', '');
                        $icon = data_get($notification->data, 'icon', 'fas fa-exclamation-circle');
                        $actionUrl = data_get($notification->data, 'action_url');
                        $category = $resolveNotificationCategory($notification);
                        $categoryUi = $notificationCategoryUi($category);
                    @endphp
                    <div class="dropdown-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pr-2">
                                <h3 class="dropdown-item-title mb-1">
                                    <i class="{{ $icon }} text-danger mr-1"></i>{{ \Illuminate\Support\Str::limit($title, 34) }}
                                </h3>
                                <p class="mb-1"><span class="badge badge-{{ $categoryUi['badge'] }}">{{ $categoryUi['label'] }}</span></p>
                                <p class="text-sm mb-1">{{ \Illuminate\Support\Str::limit($message, 56) }}</p>
                                <p class="text-sm text-muted mb-0"><i class="far fa-clock mr-1"></i>{{ optional($notification->created_at)->diffForHumans() }}</p>
                            </div>
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-secondary">Lu</button>
                            </form>
                        </div>
                        @if($actionUrl)
                            <a href="{{ $actionUrl }}" class="btn btn-link btn-sm p-0 mt-1">Ouvrir</a>
                        @endif
                    </div>
                    <div class="dropdown-divider"></div>
                @empty
                    <span class="dropdown-item text-muted">Aucune action requise pour le moment.</span>
                    <div class="dropdown-divider"></div>
                @endforelse

                <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer">Voir le centre de notifications</a>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @if(($unreadNotificationCount ?? 0) > 0)
                    <span class="badge badge-warning navbar-badge">{{ min(($unreadNotificationCount ?? 0), 99) }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ min(($unreadNotificationCount ?? 0), 99) }} Notifications non lues</span>
                <div class="dropdown-divider"></div>

                @if(($unreadNotificationCategoryCounts ?? collect())->isNotEmpty())
                    <div class="dropdown-item text-sm">
                        <div class="d-flex flex-wrap" style="gap: 6px;">
                            @foreach(($unreadNotificationCategoryCounts ?? collect()) as $category => $count)
                                @php
                                    $categoryUi = $notificationCategoryUi((string) $category);
                                @endphp
                                <span class="badge badge-{{ $categoryUi['badge'] }}">{{ $categoryUi['label'] }}: {{ $count }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                @endif

                @forelse(($latestUnreadNotifications ?? collect()) as $notification)
                    @php
                        $title = data_get($notification->data, 'title', 'Notification');
                        $message = data_get($notification->data, 'message', '');
                        $icon = data_get($notification->data, 'icon', 'fas fa-bell');
                        $type = data_get($notification->data, 'type', 'info');
                        $typeColor = $type === 'danger' ? 'danger' : ($type === 'warning' ? 'warning' : 'info');
                        $actionUrl = data_get($notification->data, 'action_url');
                        $category = $resolveNotificationCategory($notification);
                        $categoryUi = $notificationCategoryUi($category);
                    @endphp
                    <div class="dropdown-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pr-2">
                                <h3 class="dropdown-item-title mb-1">
                                    <i class="{{ $icon }} mr-1 text-{{ $typeColor }}"></i>{{ \Illuminate\Support\Str::limit($title, 34) }}
                                </h3>
                                <p class="mb-1"><span class="badge badge-{{ $categoryUi['badge'] }}">{{ $categoryUi['label'] }}</span></p>
                                <p class="text-sm mb-1">{{ \Illuminate\Support\Str::limit($message, 56) }}</p>
                                <p class="text-sm text-muted mb-0"><i class="far fa-clock mr-1"></i>{{ optional($notification->created_at)->diffForHumans() }}</p>
                            </div>
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-secondary">Lu</button>
                            </form>
                        </div>
                        @if($actionUrl)
                            <a href="{{ $actionUrl }}" class="btn btn-link btn-sm p-0 mt-1">Ouvrir</a>
                        @endif
                    </div>
                    <div class="dropdown-divider"></div>
                @empty
                    <span class="dropdown-item text-muted">Aucune notification non lue.</span>
                    <div class="dropdown-divider"></div>
                @endforelse

                <div class="dropdown-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-outline-primary">Tout marquer lu</button>
                        </form>
                        <a href="{{ route('notifications.index') }}" class="btn btn-xs btn-light">Voir tout</a>
                    </div>
                    @if(($unreadNotificationCategoryCounts ?? collect())->isNotEmpty())
                    <div class="d-flex flex-wrap" style="gap:4px;">
                        @foreach(($unreadNotificationCategoryCounts ?? collect()) as $cat => $cnt)
                            @php
                                $ui = $notificationCategoryUi((string) $cat);
                            @endphp
                            <a href="{{ route('notifications.index', ['category' => $cat]) }}"
                               class="badge badge-{{ $ui['badge'] }}"
                               style="text-decoration:none;">
                                {{ $ui['label'] }} <span class="badge badge-light">{{ $cnt }}</span>
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>
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
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                @if($authAgent && $authAgent->photo)
                    <img src="{{ route('agents.photo', basename($authAgent->photo)) }}?v={{ time() }}"
                         alt="Photo de profil"
                         class="img-size-32 img-circle mr-2"
                         style="height:32px;width:32px;object-fit:cover;">
                @else
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" alt="User Image" class="img-size-32 img-circle mr-2" style="height:32px;width:32px;object-fit:cover;">
                @endif
                <span class="d-none d-md-inline">
                    @if($authAgent)
                        {{ $authAgent->prenom ?? '' }} {{ $authAgent->nom ?? 'Agent' }}
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

@extends('layouts.app')

@section('page_title', 'Notifications')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Notifications')

@section('content')
@php
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
<section class="content">
    <div class="container-fluid">
        @if(($notificationCategoryCounts ?? collect())->isNotEmpty())
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                @php
                                    $allSelected = ($selectedNotificationCategory ?? 'tous') === 'tous';
                                @endphp
                                <a href="{{ route('notifications.index', ['category' => 'tous']) }}"
                                   class="badge {{ $allSelected ? 'badge-primary' : 'badge-light' }} p-2 border">Tous</a>
                                @foreach(($notificationCategoryCounts ?? collect()) as $category => $count)
                                    @php
                                        $categoryUi = $notificationCategoryUi((string) $category);
                                    @endphp
                                    <a href="{{ route('notifications.index', ['category' => $category]) }}"
                                       class="badge {{ ($selectedNotificationCategory ?? 'tous') === (string) $category ? 'badge-' . $categoryUi['badge'] : 'badge-light border' }} p-2">
                                        {{ $categoryUi['label'] }}: {{ $count }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Centre de notifications</h5>
                @if($unreadNotifications->count() > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check-double mr-1"></i>Tout marquer comme lu
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Non lues ({{ $unreadNotifications->total() }})</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($unreadNotifications->isNotEmpty())
                        @foreach($unreadNotifications as $notification)
                            @php
                                $type = data_get($notification->data, 'type', 'info');
                                $icon = data_get($notification->data, 'icon', 'fas fa-bell');
                                $title = data_get($notification->data, 'title', 'Notification');
                                $message = data_get($notification->data, 'message', '');
                                $actionUrl = data_get($notification->data, 'action_url');
                                $category = $resolveNotificationCategory($notification);
                                $categoryUi = $notificationCategoryUi($category);
                            @endphp
                            <div class="border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="pr-2">
                                        <strong><i class="{{ $icon }} mr-1 text-{{ $type === 'danger' ? 'danger' : ($type === 'warning' ? 'warning' : 'info') }}"></i>{{ $title }}</strong>
                                        <div class="mt-1"><span class="badge badge-{{ $categoryUi['badge'] }}">{{ $categoryUi['label'] }}</span></div>
                                        <p class="mb-1 mt-1">{{ $message }}</p>
                                        <small class="text-muted">{{ optional($notification->created_at)->diffForHumans() }}</small>
                                    </div>
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-secondary">Lu</button>
                                    </form>
                                </div>
                                @if($actionUrl)
                                    <a href="{{ $actionUrl }}" class="btn btn-link btn-sm p-0 mt-2">Ouvrir</a>
                                @endif
                            </div>
                        @endforeach
                        @else
                            <div class="p-3 text-muted">Aucune notification non lue.</div>
                        @endif
                    </div>
                    <div class="card-footer">
                        {{ $unreadNotifications->links() }}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Historique ({{ $readNotifications->total() }})</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($readNotifications->isNotEmpty())
                        @foreach($readNotifications as $notification)
                            @php
                                $icon = data_get($notification->data, 'icon', 'fas fa-bell');
                                $title = data_get($notification->data, 'title', 'Notification');
                                $message = data_get($notification->data, 'message', '');
                                $actionUrl = data_get($notification->data, 'action_url');
                                $category = $resolveNotificationCategory($notification);
                                $categoryUi = $notificationCategoryUi($category);
                            @endphp
                            <div class="border-bottom p-3 bg-light">
                                <strong><i class="{{ $icon }} mr-1 text-muted"></i>{{ $title }}</strong>
                                <div class="mt-1"><span class="badge badge-{{ $categoryUi['badge'] }}">{{ $categoryUi['label'] }}</span></div>
                                <p class="mb-1 mt-1">{{ $message }}</p>
                                <small class="text-muted">{{ optional($notification->created_at)->diffForHumans() }}</small>
                                @if($actionUrl)
                                    <div>
                                        <a href="{{ $actionUrl }}" class="btn btn-link btn-sm p-0 mt-2">Ouvrir</a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @else
                            <div class="p-3 text-muted">Aucun historique de notification.</div>
                        @endif
                    </div>
                    <div class="card-footer">
                        {{ $readNotifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

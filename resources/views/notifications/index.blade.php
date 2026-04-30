@extends('layouts.app')

@section('page_title', 'Notifications')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Notifications')

@section('content')
<section class="content">
    <div class="container-fluid">
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
                        @forelse($unreadNotifications as $notification)
                            @php
                                $type = data_get($notification->data, 'type', 'info');
                                $icon = data_get($notification->data, 'icon', 'fas fa-bell');
                                $title = data_get($notification->data, 'title', 'Notification');
                                $message = data_get($notification->data, 'message', '');
                                $actionUrl = data_get($notification->data, 'action_url');
                            @endphp
                            <div class="border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="pr-2">
                                        <strong><i class="{{ $icon }} mr-1 text-{{ $type === 'danger' ? 'danger' : ($type === 'warning' ? 'warning' : 'info') }}"></i>{{ $title }}</strong>
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
                        @empty
                            <div class="p-3 text-muted">Aucune notification non lue.</div>
                        @endforelse
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
                        @forelse($readNotifications as $notification)
                            @php
                                $icon = data_get($notification->data, 'icon', 'fas fa-bell');
                                $title = data_get($notification->data, 'title', 'Notification');
                                $message = data_get($notification->data, 'message', '');
                                $actionUrl = data_get($notification->data, 'action_url');
                            @endphp
                            <div class="border-bottom p-3 bg-light">
                                <strong><i class="{{ $icon }} mr-1 text-muted"></i>{{ $title }}</strong>
                                <p class="mb-1 mt-1">{{ $message }}</p>
                                <small class="text-muted">{{ optional($notification->created_at)->diffForHumans() }}</small>
                                @if($actionUrl)
                                    <div>
                                        <a href="{{ $actionUrl }}" class="btn btn-link btn-sm p-0 mt-2">Ouvrir</a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-3 text-muted">Aucun historique de notification.</div>
                        @endforelse
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

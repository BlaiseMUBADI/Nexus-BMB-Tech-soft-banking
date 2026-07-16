{{--
    head.blade.php
    Rôle : Contient les balises <head> (CSS, JS, meta) pour AdminLTE.
--}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name', 'NBTB') }}@hasSection('title') | @yield('title')@endif</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
<link rel="icon" type="image/png" sizes="38x38" href="{{ asset('dist/img/icon_vrailogoeben.png') }}?v=2">
<meta name="theme-color" content="#1a7a4a">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

<!-- DataTables CSS (global) -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

<!-- Toastr (notifications) -->
<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

<style>
    :root {
        --app-warning-main: #0f766e;
        --app-warning-soft: #134e4a;
        --app-warning-border: #115e59;
        --app-warning-text: #ecfeff;
    }

    /* Palette warning unifiee pour supprimer l'effet jaune global. */
    .text-warning {
        color: var(--app-warning-main) !important;
    }

    .btn-warning,
    .bg-warning,
    .badge-warning,
    .alert-warning,
    .card-warning:not(.card-outline) > .card-header {
        background-color: var(--app-warning-main) !important;
        border-color: var(--app-warning-border) !important;
        color: var(--app-warning-text) !important;
    }

    .btn-outline-warning {
        color: var(--app-warning-main) !important;
        border-color: var(--app-warning-main) !important;
    }

    .btn-outline-warning:hover,
    .btn-outline-warning:focus {
        background-color: var(--app-warning-main) !important;
        color: var(--app-warning-text) !important;
    }

    .table-warning,
    .table-warning > th,
    .table-warning > td {
        background-color: rgba(15, 118, 110, 0.20) !important;
        color: #d1fae5 !important;
    }

    .main-sidebar .text-warning {
        color: #7dd3fc !important;
    }

    /*
     * Refonte globale du badge "warning" pour améliorer la lisibilité.
     * Impact: tous les <span class="badge badge-warning"> du projet.
     */
    .badge.badge-warning,
    .badge.badge-warning.text-dark {
        background: linear-gradient(135deg, #0f766e 0%, #0e7490 100%) !important;
        color: #f8fafc !important;
        border: 1px solid rgba(255, 255, 255, .28);
        font-weight: 700;
        letter-spacing: .2px;
        text-shadow: 0 1px 0 rgba(0, 0, 0, .18);
        box-shadow: 0 1px 2px rgba(2, 6, 23, .35);
    }

    .badge.badge-warning.badge-pill {
        border-radius: 999px;
        padding: .24em .62em;
    }

    .navbar-badge.badge-warning {
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .35);
    }

    .content-wrapper,
    .content-header,
    .main-footer {
        overflow-x: hidden;
    }

    /* Empêcher tout content-wrapper ::before/::after décoratif qui pourrait déborder */
    .content-wrapper::before,
    .content-wrapper::after {
        display: none !important;
    }

    /* S'assurer que .content ne déborde pas */
    .content {
        min-height: auto;
        overflow: visible;
    }

    /* Empêcher un logo ou icône agrandie de déborder la page */
    .content .container-fluid {
        max-width: 100%;
        overflow: hidden;
    }

    /* Empêcher les icônes débordant dans les pages de pagination */
    .content-wrapper .pagination + * {
        clear: both;
    }

    /* FIX: chevrons du sidebar positionnés par rapport au body au lieu du lien parent */
    .main-sidebar .nav-link {
        position: relative;
    }

    /* FIX: confine le scrollbar OverlayScrollbars dans la sidebar */
    .main-sidebar {
        overflow: hidden;
    }

    /* FIX: empêche le handle OverlayScrollbars de déborder dans le content */
    .os-scrollbar {
        z-index: 1000;
    }

    @media (max-width: 767.98px) {
        .content-header .container-fluid,
        .content .container-fluid {
            padding-left: .85rem;
            padding-right: .85rem;
        }

        .content-header .row {
            row-gap: .35rem;
        }

        .content-header h1 {
            font-size: 1.35rem;
            line-height: 1.2;
            white-space: normal;
            word-break: break-word;
        }

        .content-header .breadcrumb {
            float: none !important;
            justify-content: flex-start;
            flex-wrap: wrap;
            margin-bottom: 0;
            padding-left: 0;
        }

        .card-header,
        .card-header > .d-flex,
        .card-header .card-tools {
            gap: .5rem;
        }

        .card-header > .d-flex,
        .card-header .card-tools {
            width: 100%;
            flex-wrap: wrap;
            justify-content: flex-start !important;
        }

        .btn-group {
            flex-wrap: wrap;
        }

        .btn-group > .btn {
            flex: 1 1 auto;
        }

        .small-box .icon {
            display: none;
        }

        .small-box .inner {
            padding-right: 1rem;
        }

        .small-box .inner h3,
        .small-box .inner h4 {
            font-size: 1.35rem;
        }

        .modal-dialog {
            margin: .5rem;
        }
    }
</style>

@stack('css')



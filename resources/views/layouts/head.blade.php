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
    .content-wrapper,
    .content-header,
    .main-footer {
        overflow-x: hidden;
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



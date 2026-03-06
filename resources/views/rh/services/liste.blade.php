@extends('layouts.app')

@section('page_title', 'Services / Postes')
@section('breadcrumb_parent', 'Ressources Humaines')
@section('breadcrumb', 'Services & Postes')

@section('content')
<div class="container-fluid">

    {{-- ── Messages flash ─────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="icon fas fa-check mr-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         MINI-DASHBOARD STATISTIQUES
         ══════════════════════════════════════════════════════════ --}}
    <div class="row mb-3">

        {{-- Total services --}}
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1">
                    <i class="fas fa-building"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Services</span>
                    <span class="info-box-number">{{ $stats['total_services'] }}</span>
                </div>
            </div>
        </div>

        {{-- Total postes --}}
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-briefcase"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Postes</span>
                    <span class="info-box-number">{{ $stats['total_postes'] }}</span>
                </div>
            </div>
        </div>

        {{-- Services avec postes --}}
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1">
                    <i class="fas fa-sitemap"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Services pourvus</span>
                    <span class="info-box-number">{{ $stats['services_avec_postes'] }}</span>
                    <span class="progress-description">
                        sur {{ $stats['total_services'] }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Postes guichet --}}
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1">
                    <i class="fas fa-cash-register"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Postes Guichet</span>
                    <span class="info-box-number">{{ $stats['postes_guichet'] }}</span>
                </div>
            </div>
        </div>

    </div>{{-- /dashboard --}}

    <div class="row">

        {{-- ── Colonne Services ──────────────────────────────────── --}}
        <div class="col-lg-6 col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building mr-2"></i> Services
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary badge-pill">{{ $services->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">

                    <div class="px-2 pt-2">
                        <input type="text" id="searchServices" class="form-control form-control-sm"
                               placeholder="🔍 Rechercher un service…">
                    </div>

                    <div class="card-table-scroll mt-1">
                        <table id="servicesTable" class="table table-sm services-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:35px">#</th>
                                    <th>Nom du service</th>
                                    <th>Description</th>
                                    <th style="width:80px" class="text-center">Postes</th>
                                    <th style="width:55px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                    <tr data-service-id="{{ $service->id }}"
                                        data-nom="{{ $service->nom }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $service->nom }}</strong></td>
                                        <td class="text-muted small">{{ $service->description ?: '—' }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $service->postes_count > 0 ? 'success' : 'secondary' }}">
                                                {{ $service->postes_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-xs btn-danger btn-delete-service"
                                                    data-id="{{ $service->id }}"
                                                    title="Supprimer ce service">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Aucun service enregistré.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-3 py-3 border-top">
                        <p class="text-muted small font-weight-bold mb-2">
                            <i class="fas fa-plus-circle mr-1 text-primary"></i> Ajouter un service
                        </p>
                        <form id="formAjoutService" method="POST" action="{{ route('services.store') }}">
                            @csrf
                            <div class="form-group mb-2">
                                <input type="text" name="nom" class="form-control form-control-sm"
                                       placeholder="Nom du service *" required>
                            </div>
                            <div class="form-group mb-2">
                                <textarea name="description" class="form-control form-control-sm" rows="2"
                                          placeholder="Description (optionnel)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus-circle mr-1"></i> Ajouter
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Colonne Postes ─────────────────────────────────────── --}}
        <div class="col-lg-6 col-md-12">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title" id="postesCardTitle">
                        <i class="fas fa-briefcase mr-2"></i> Postes
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div id="postesSection" class="p-4">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-hand-point-left fa-2x mb-2 d-block"></i>
                            Sélectionnez un service pour afficher ses postes.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

</div>{{-- /container-fluid --}}
@endsection


@push('css')
<style>

    /* ══ DARK ADMINLTE — Sélection de ligne ════════════════════ */
    .services-table tbody tr.row-selected > td {
        background-color: #0062cc !important;
        color: #fff !important;
    }
    .services-table tbody tr.row-selected > td strong {
        color: #fff !important;
    }

    /* ── Survol ──────────────────────────────────────────────── */
    .services-table tbody tr:not(.row-selected):hover > td {
        background-color: rgba(0, 123, 255, 0.22) !important;
        color: #fff !important;
        cursor: pointer;
    }

    /* ── Thead dark ──────────────────────────────────────────── */
    .services-table thead th {
        background-color: #2c3136 !important;
        color: #c2c7d0 !important;
        border-color: #3d4349 !important;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    /* ── Hauteur tableau ─────────────────────────────────────── */
    .card-table-scroll {
        max-height: 300px;
        overflow-y: auto;
    }

    /* ── Section postes chargée dynamiquement ────────────────── */
    #postesSection .postes-table thead th {
        background-color: #2c3136 !important;
        color: #c2c7d0 !important;
        border-color: #3d4349 !important;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    #postesSection .postes-table tbody tr:hover > td {
        background-color: rgba(40, 167, 69, 0.22) !important;
        color: #fff !important;
    }

</style>
@endpush

@push('js')
<script>
$(document).ready(function () {

    // ── CSRF ────────────────────────────────────────────────────
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ── Auto-fermeture alertes flash ─────────────────────────────
    setTimeout(function () { $('.alert-success').alert('close'); }, 3000);

    // ── Recherche live : Services ────────────────────────────────
    $('#searchServices').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('#servicesTable tbody tr').each(function () {
            $(this).toggle(q === '' || $(this).text().toLowerCase().indexOf(q) !== -1);
        });
    });

    // ── Sélection d'un service → charger ses postes ──────────────
    $('#servicesTable tbody').on('click', 'tr', function () {
        var serviceId   = $(this).data('service-id');
        var serviceName = $(this).data('nom');
        if (!serviceId) return;

        $('#servicesTable tbody tr').removeClass('row-selected');
        $(this).addClass('row-selected');

        $('#postesCardTitle').html('<i class="fas fa-briefcase mr-2"></i> Postes — <span class="text-success">' + serviceName + '</span>');
        $('#postesSection').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');

        $.get('{{ route("postes.ajaxListe", ["service" => "__sID__"]) }}'.replace('__sID__', serviceId))
            .done(function (data) { $('#postesSection').html(data); })
            .fail(function () {
                $('#postesSection').html('<div class="p-3 text-danger">Erreur lors du chargement des postes.</div>');
            });
    });

    // ── Suppression d'un service ─────────────────────────────────
    $(document).on('click', '.btn-delete-service', function (e) {
        e.stopPropagation();
        var id  = $(this).data('id');
        var $tr = $(this).closest('tr');
        showUniversalConfirm('Voulez-vous vraiment supprimer ce service ?', function () {
            $.ajax({
                url:  '{{ route("services.ajaxDestroy", ["service" => "__ID__"]) }}'.replace('__ID__', id),
                type: 'POST',
                data: { _method: 'DELETE' },
            })
            .done(function (response) {
                showSystemMessage('success', response.message);
                $tr.fadeOut(400, function () { $(this).remove(); });
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Erreur lors de la suppression du service.';
                showSystemMessage('error', msg);
            });
        }, 'Confirmation de suppression');
    });

    // ── Ajout d'un service ───────────────────────────────────────
    $('#formAjoutService').on('submit', function (e) {
        e.preventDefault();
        var $btn = $(this).find('[type=submit]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Ajout…');

        $.post($(this).attr('action'), $(this).serialize())
            .done(function () {
                showSystemMessage('success', 'Service ajouté avec succès.');
                setTimeout(function () { location.reload(); }, 1200);
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Erreur lors de l\'ajout du service.';
                showSystemMessage('error', msg);
                $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter');
            });
    });

    // ── Suppression d'un poste (section chargée dynamiquement) ───
    $(document).on('click', '.btn-delete-poste', function () {
        var serviceId = $(this).data('service-id');
        var posteId   = $(this).data('poste-id');
        var $tr       = $(this).closest('tr');
        showUniversalConfirm('Voulez-vous vraiment supprimer ce poste ?', function () {
            $.ajax({
                url:  '{{ route("postes.ajaxDestroy", ["service" => "__sID__", "poste" => "__pID__"]) }}'.replace('__sID__', serviceId).replace('__pID__', posteId),
                type: 'POST',
                data: { _method: 'DELETE' },
            })
            .done(function (response) {
                showSystemMessage('success', response.message);
                $tr.fadeOut(400, function () { $(this).remove(); });
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Erreur lors de la suppression du poste.';
                showSystemMessage('error', msg);
            });
        }, 'Confirmation de suppression');
    });

    // ── Ajout d'un poste (section chargée dynamiquement) ─────────
    $(document).on('submit', '.form-ajout-poste', function (e) {
        e.preventDefault();
        var form      = $(this);
        var serviceId = form.data('service-id');
        var $btn      = form.find('[type=submit]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Ajout…');

        $.post(form.attr('action'), form.serialize())
            .done(function (response) {
                showSystemMessage('success', response.message);
                $.get('{{ route("postes.ajaxListe", ["service" => "__sID__"]) }}'.replace('__sID__', serviceId))
                    .done(function (data) { $('#postesSection').html(data); });
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Erreur lors de l\'ajout du poste.';
                showSystemMessage('error', msg);
                $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter');
            });
    });

});
</script>
@endpush
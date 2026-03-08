@extends('layouts.app')

@section('page_title', 'Devises & Taux de change')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Devises / Taux')

@section('content')
<div class="container-fluid">

    {{-- MINI-DASHBOARD --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Devises</span>
                    <span class="info-box-number">{{ $stats['total_devises'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-star"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Devise Référence</span>
                    <span class="info-box-number">{{ $stats['devise_reference'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-exchange-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Taux</span>
                    <span class="info-box-number">{{ $stats['total_taux'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Dernier taux</span>
                    <span class="info-box-number" style="font-size:.95rem;">{{ $stats['dernier_taux'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ONGLETS --}}
    <div class="card card-primary card-outline">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="dtTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-devises" role="tab">
                        <i class="fas fa-money-bill-wave mr-1"></i> Devises
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-taux" role="tab">
                        <i class="fas fa-exchange-alt mr-1"></i> Taux de change
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">

                {{-- ─── Onglet DEVISES ─── --}}
                <div class="tab-pane fade show active" id="tab-devises">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-primary card-outline">
                                <div class="card-header pb-1">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle mr-1 text-primary"></i> Ajouter une devise</h5>
                                </div>
                                <div class="card-body">
                                    <form id="deviseForm">
                                        @csrf
                                        <div class="form-group">
                                            <label>Code ISO <span class="text-danger">*</span></label>
                                            <input type="text" name="code_iso" id="code_iso" class="form-control text-uppercase" maxlength="3" required placeholder="Ex : USD, CDF">
                                        </div>
                                        <div class="form-group">
                                            <label>Nom <span class="text-danger">*</span></label>
                                            <input type="text" name="nom" id="nom" class="form-control" required placeholder="Ex : Dollar américain">
                                        </div>
                                        <div class="form-group">
                                            <label>Symbole <span class="text-danger">*</span></label>
                                            <input type="text" name="symbole" id="symbole" class="form-control" maxlength="5" required placeholder="Ex : $, FC">
                                        </div>
                                        <div class="form-group">
                                            <label>Devise de référence ?</label>
                                            <select name="est_reference" id="est_reference" class="form-control">
                                                <option value="0">Non</option>
                                                <option value="1">Oui</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block" id="btnAddDevise">
                                            <i class="fas fa-plus-circle mr-1"></i> Ajouter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-info card-outline">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h3 class="card-title mb-0"><i class="fas fa-list mr-2"></i> Liste des devises</h3>
                                    <span class="badge badge-info badge-pill">{{ $devises->count() }}</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="px-2 pt-2">
                                        <input type="text" id="searchDevises" class="form-control form-control-sm" placeholder="🔍 Rechercher une devise…">
                                    </div>
                                    <div class="table-responsive mt-1">
                                        <table class="table table-bordered table-striped table-sm mb-0" id="devisesTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="width:40px">#</th>
                                                    <th>Code ISO</th>
                                                    <th>Nom</th>
                                                    <th>Symbole</th>
                                                    <th class="text-center">Référence</th>
                                                    <th class="text-center" style="width:70px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($devises as $devise)
                                                <tr>
                                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                                    <td><strong>{{ $devise->code_iso }}</strong></td>
                                                    <td>{{ $devise->nom }}</td>
                                                    <td>{{ $devise->symbole }}</td>
                                                    <td class="text-center">
                                                        @if($devise->est_reference)
                                                            <span class="badge badge-success"><i class="fas fa-star mr-1"></i>Oui</span>
                                                        @else
                                                            <span class="badge badge-secondary">Non</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-xs btn-danger btn-delete-devise"
                                                            data-id="{{ $devise->code_iso }}"
                                                            data-nom="{{ $devise->nom }}"
                                                            title="Supprimer">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="6" class="text-center text-muted py-5">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucune devise.
                                                </td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- /tab-devises --}}

                {{-- ─── Onglet TAUX ─── --}}
                <div class="tab-pane fade" id="tab-taux">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-warning card-outline">
                                <div class="card-header pb-1">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle mr-1 text-warning"></i> Nouveau taux</h5>
                                </div>
                                <div class="card-body">
                                    <form id="tauxForm">
                                        @csrf
                                        <div class="form-group">
                                            <label>Devise source <span class="text-danger">*</span></label>
                                            <select name="devise_source" id="devise_source" class="form-control" required>
                                                @foreach($devises as $d)
                                                    <option value="{{ $d->code_iso }}">{{ $d->nom }} ({{ $d->code_iso }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Devise destination <span class="text-danger">*</span></label>
                                            <select name="devise_destination" id="devise_destination" class="form-control" required>
                                                @foreach($devises as $d)
                                                    <option value="{{ $d->code_iso }}">{{ $d->nom }} ({{ $d->code_iso }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Taux <span class="text-danger">*</span></label>
                                            <input type="number" step="0.0001" name="taux" id="taux" class="form-control" required placeholder="Ex : 2850.0000">
                                        </div>
                                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size:.82rem;">
                                            <i class="fas fa-info-circle mr-1"></i> Le taux inverse sera créé automatiquement.
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-block text-dark" id="btnAddTaux">
                                            <i class="fas fa-plus-circle mr-1"></i> Ajouter le taux
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-info card-outline">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h3 class="card-title mb-0"><i class="fas fa-list mr-2"></i> Taux de change</h3>
                                    <span class="badge badge-info badge-pill">{{ $taux->count() }}</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="px-2 pt-2">
                                        <input type="text" id="searchTaux" class="form-control form-control-sm" placeholder="🔍 Rechercher un taux…">
                                    </div>
                                    <div class="table-responsive mt-1" style="max-height:500px; overflow-y:auto;">
                                        <table class="table table-bordered table-striped table-sm mb-0" id="tauxTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="width:40px">#</th>
                                                    <th>Source</th>
                                                    <th>Destination</th>
                                                    <th>Taux</th>
                                                    <th>Date</th>
                                                    <th class="text-center" style="width:70px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($taux as $t)
                                                <tr>
                                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                                    <td><span class="badge badge-secondary">{{ $t->devise_source }}</span></td>
                                                    <td><span class="badge badge-secondary">{{ $t->devise_destination }}</span></td>
                                                    <td><strong>{{ number_format($t->taux, 4, '.', ' ') }}</strong></td>
                                                    <td><small>{{ $t->date_application ?? '—' }}</small></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-xs btn-danger btn-delete-taux"
                                                            data-id="{{ $t->id }}"
                                                            data-info="{{ $t->devise_source }} → {{ $t->devise_destination }}"
                                                            title="Supprimer">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="6" class="text-center text-muted py-5">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucun taux enregistré.
                                                </td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- /tab-taux --}}

            </div>{{-- /tab-content --}}
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    #devisesTable tbody tr:hover > td,
    #tauxTable tbody tr:hover > td {
        background-color: rgba(0, 123, 255, 0.13) !important;
        color: #fff !important;
    }
</style>
@endpush

@push('js')
<script>
(function () {
    'use strict';
    $.ajaxSetup({ headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept'      : 'application/json'
    } });

    $(function () {

        $('#searchDevises').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#devisesTable tbody tr').each(function () {
                $(this).toggle(q === '' || $(this).text().toLowerCase().indexOf(q) !== -1);
            });
        });

        $('#searchTaux').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#tauxTable tbody tr').each(function () {
                $(this).toggle(q === '' || $(this).text().toLowerCase().indexOf(q) !== -1);
            });
        });

        $('#deviseForm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnAddDevise').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');
            $.ajax({
                type    : 'POST',
                url     : '{{ route("administration.devises-taux.storeDevise") }}',
                data    : $(this).serialize(),
                dataType: 'json'
            })
            .done(function (data) {
                if (data.success) {
                    showSystemMessage('success', data.message || 'Devise ajoutée avec succès.');
                    setTimeout(function () { location.reload(); }, 900);
                } else {
                    showSystemMessage('error', data.message || 'Erreur.');
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter');
                }
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Ajout devise');
                $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter');
            });
        });

        $('#tauxForm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnAddTaux').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi…');
            $.ajax({
                type    : 'POST',
                url     : '{{ route("administration.devises-taux.storeTaux") }}',
                data    : $(this).serialize(),
                dataType: 'json'
            })
            .done(function (data) {
                if (data.success) {
                    showSystemMessage('success', data.message || 'Taux ajouté (et inverse si applicable).');
                    setTimeout(function () { location.reload(); }, 900);
                } else {
                    showSystemMessage('error', data.message || 'Erreur.');
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter le taux');
                }
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Ajout taux');
                $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> Ajouter le taux');
            });
        });

        $(document).on('click', '.btn-delete-devise', function () {
            var id  = $(this).data('id');
            var nom = $(this).data('nom');
            var $tr = $(this).closest('tr');
            var url = '{{ route("administration.devises-taux.destroyDevise", ["code_iso" => "__ID__"]) }}'.replace('__ID__', id);
            showUniversalConfirm(
                'Supprimer la devise <strong>' + nom + ' (' + id + ')</strong> ?<br><small class="text-danger">Les taux associés seront supprimés.</small>',
                function () {
                    $.ajax({
                        type    : 'POST',
                        url     : url,
                        data    : { _method: 'DELETE' },
                        dataType: 'json'
                    })
                    .done(function (data) {
                        if (data.success) {
                            showSystemMessage('success', data.message || 'Devise supprimée.');
                            $tr.fadeOut(400, function () { $(this).remove(); });
                        } else {
                            showSystemMessage('error', data.message || 'Erreur.');
                        }
                    })
                    .fail(function (xhr) {
                        handleAjaxFail(xhr, 'Suppression devise');
                    });
                }, 'Confirmer la suppression'
            );
        });

        $(document).on('click', '.btn-delete-taux', function () {
            var id   = $(this).data('id');
            var info = $(this).data('info');
            var $tr  = $(this).closest('tr');
            var url  = '{{ route("administration.devises-taux.destroyTaux", ["id" => "__ID__"]) }}'.replace('__ID__', id);
            showUniversalConfirm(
                'Supprimer le taux <strong>' + info + '</strong> ?',
                function () {
                    $.ajax({
                        type    : 'POST',
                        url     : url,
                        data    : { _method: 'DELETE' },
                        dataType: 'json'
                    })
                    .done(function (data) {
                        if (data.success) {
                            showSystemMessage('success', data.message || 'Taux supprimé.');
                            $tr.fadeOut(400, function () { $(this).remove(); });
                        } else {
                            showSystemMessage('error', data.message || 'Erreur.');
                        }
                    })
                    .fail(function (xhr) {
                        handleAjaxFail(xhr, 'Suppression taux');
                    });
                }, 'Confirmer la suppression'
            );
        });

        var hash = window.location.hash;
        if (hash) { $('#dtTabs a[href="' + hash + '"]').tab('show'); }

    });
}());
</script>
@endpush

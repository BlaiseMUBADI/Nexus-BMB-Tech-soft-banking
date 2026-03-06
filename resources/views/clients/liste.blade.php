@extends('layouts.app')

@section('page_title', 'Liste des clients')
@section('breadcrumb_parent', 'Clients / Membres')
@section('breadcrumb', 'Liste des clients')

@section('content')
{{-- ===== Mini-dashboard ===== --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-6 col-sm-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total clients</span>
                    <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-male"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Hommes</span>
                    <span class="info-box-number">{{ $stats['hommes'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-female"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Femmes</span>
                    <span class="info-box-number">{{ $stats['femmes'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-camera"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Avec photo</span>
                    <span class="info-box-number">{{ $stats['avec_photo'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Card principale ===== --}}
    <div class="card card-primary card-outline">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
            <h3 class="card-title mb-0"><i class="fas fa-users mr-2"></i>Liste des clients</h3>
            <div class="d-flex align-items-center mt-1 mt-md-0">
                <div class="input-group input-group-sm mr-2" style="width:220px;">
                    <input type="text" id="searchClients" class="form-control" placeholder="Rechercher...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus mr-1"></i> Nouveau client
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Matricule</th>
                            <th>Zone</th>
                            <th>Nom</th>
                            <th>Postnom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th style="width:70px;">Photo</th>
                            <th style="width:100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="clientsTableBody">
                        @forelse($clients as $loopIndex => $client)
                            <tr>
                                <td>{{ $loopIndex + 1 }}</td>
                                <td><code>{{ $client->matricule }}</code></td>
                                <td>{{ $client->zone->nom ?? '—' }}</td>
                                <td>{{ $client->nom }}</td>
                                <td>{{ $client->postnom ?? '' }}</td>
                                <td>{{ $client->prenom }}</td>
                                <td>{{ $client->email ?? '—' }}</td>
                                <td>{{ $client->telephone ?? '—' }}</td>
                                <td class="text-center">
                                    @if($client->photo)
                                        <img src="{{ route('clients.photo', basename($client->photo)) }}"
                                             alt="Photo"
                                             class="client-photo-thumb"
                                             data-photo-url="{{ route('clients.photo', basename($client->photo)) }}"
                                             data-client-nom="{{ $client->nom }} {{ $client->postnom }} {{ $client->prenom }}"
                                             style="width:46px;height:46px;object-fit:cover;border-radius:4px;cursor:pointer;">
                                    @else
                                        <span class="badge badge-secondary">Aucune</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('clients.show', $client->matricule) }}"
                                       class="btn btn-xs btn-info mr-1" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client->matricule) }}"
                                       class="btn btn-xs btn-warning mr-1" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-xs btn-danger btn-delete-client"
                                            data-url="{{ route('clients.destroy', $client->matricule) }}"
                                            data-nom="{{ $client->nom }} {{ $client->postnom }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucun client trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total affiché : <span id="countVisible">{{ $clients->count() }}</span> client(s)
        </div>
    </div>
</div>

{{-- ===== Modal photo partagée ===== --}}
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="photoModalLabel">Photo du client</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="photoModalImg" src="" alt="Photo du client"
                     class="img-fluid rounded shadow"
                     style="max-width:100%;max-height:80vh;background:#222;">
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
(function () {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(function () {

        /* ---- Recherche live ---- */
        $('#searchClients').on('input', function () {
            var q = $(this).val().toLowerCase().trim();
            var count = 0;
            $('#clientsTableBody tr').each(function () {
                var text = $(this).text().toLowerCase();
                var match = !q || text.indexOf(q) !== -1;
                $(this).toggle(match);
                if (match) count++;
            });
            $('#countVisible').text(count);
        });

        /* ---- Affichage photo modal ---- */
        $(document).on('click', '.client-photo-thumb', function () {
            var url  = $(this).data('photo-url');
            var nom  = $(this).data('client-nom');
            $('#photoModalImg').attr('src', url);
            $('#photoModalLabel').text('Photo — ' + nom);
            $('#photoModal').modal('show');
        });

        $('#photoModal').on('hidden.bs.modal', function () {
            $('#photoModalImg').attr('src', '');
        });

        /* ---- Suppression AJAX ---- */
        var deleteUrl = null;

        $(document).on('click', '.btn-delete-client', function () {
            deleteUrl = $(this).data('url');
            var nom   = $(this).data('nom');
            showUniversalConfirm(
                'Supprimer le client <strong>' + nom + '</strong> ? Cette action est irréversible.',
                function () {
                    var $row = $('button[data-url="' + deleteUrl + '"]').closest('tr');
                    $.post(deleteUrl, { _method: 'DELETE' })
                        .done(function (res) {
                            $row.fadeOut(300, function () { $(this).remove(); });
                            var c = parseInt($('#countVisible').text(), 10) - 1;
                            $('#countVisible').text(c);
                            showSystemMessage('success', res.message || 'Client supprimé avec succès.');
                        })
                        .fail(function () {
                            showSystemMessage('error', 'Impossible de supprimer le client. Veuillez réessayer.');
                        });
                },
                'Confirmer la suppression'
            );
        });

    });
}());
</script>
@endpush
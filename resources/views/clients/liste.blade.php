@extends('layouts.app')

@section('page_title', 'Liste des clients')
@section('breadcrumb_parent', 'Clients / Membres')
@section('breadcrumb', 'Liste des clients')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <a href="{{ url('/clients/create') }}" class="btn btn-primary float-right">
                <i class="fas fa-user-plus mr-1"></i> Ajouter un client
            </a>
        </div>
        <div class="card-body">
            <!-- Conteneur fusionné pour boutons et recherche DataTables -->
            <div id="clients-table-toolbar" class="d-flex flex-wrap align-items-center justify-content-between mb-3 p-2 rounded shadow-sm" style="background: #232a32; border: 1px solid #444; min-height: 56px;">
                <div id="clients-table-buttons" class="mb-2 mb-md-0 "></div>
                <div id="clients-table-search" class="datatable-search ms-md-3 "></div>
            </div>

            <div class="table-responsive">
                <table id="clients-table" class="table table-bordered table-striped datatable" data-buttons-container="#clients-table-buttons">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Matricule</th>
                            <th>Zone</th>
                            <th>Nom</th>
                            <th>Postnom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Photo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $loopIndex => $client)
                        <tr>
                            <td>{{ $loopIndex + 1 }}</td>
                            <td>{{ $client->matricule }}</td>
                            <td>{{ $client->zone ?? '' }}</td>
                            <td>{{ $client->nom }}</td>
                            <td>{{ $client->postnom ?? '' }}</td>
                            <td>{{ $client->prenom }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->telephone }}</td>
                            <td>
                                @if($client->photo)
                                    <img src="{{ route('clients.photo', basename($client->photo)) }}?v={{ time() }}" alt="Photo" class="client-photo-thumb" data-photo-src="{{ route('clients.photo', basename($client->photo)) }}?v={{ time() }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; cursor:pointer;" data-toggle="modal" data-target="#photoModal-{{ $client->matricule }}">
                                    <!-- Modal pour chaque client -->
                                    <div class="modal fade" id="photoModal-{{ $client->matricule }}" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel-{{ $client->matricule }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content bg-dark">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title text-white" id="photoModalLabel-{{ $client->matricule }}">Photo du client </h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ route('clients.photo', basename($client->photo)) }}" alt="Photo du client" class="img-fluid rounded shadow photo-modal-img" style="max-width: 100%; max-height: 80vh; background: #222;" data-matricule="{{ $client->matricule }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Aucune</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('/clients/' . $client->matricule) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/clients/' . $client->matricule . '/edit') }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ url('/clients/' . $client->matricule) }}" method="POST" class="d-inline delete-client-form" data-client-matricule="{{ $client->matricule }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-client" data-matricule="{{ $client->matricule }}" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- L'initialisation DataTables est maintenant globale -->
        </div>
    </div>
</div>
@endsection

@push('js')
<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteClientModal" tabindex="-1" role="dialog" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteClientModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <span class="display-4 text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                </div>
                <p class="mb-0">Êtes-vous sûr de vouloir supprimer ce client ?<br>
                    <span class="fw-bold" style="color:#ffc107; font-size:1.1em; text-shadow:0 1px 2px #000;">Cette action est <u>irréversible</u>.</span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteClientBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
        let formToDelete = null;
        // Gestion du bouton supprimer
        $('.btn-delete-client').on('click', function(e) {
                e.preventDefault();
                formToDelete = $(this).closest('form');
                $('#deleteClientModal').modal('show');
        });
        $('#confirmDeleteClientBtn').on('click', function() {
                if(formToDelete) {
                        formToDelete.submit();
                }
                $('#deleteClientModal').modal('hide');
        });
        // Quand une miniature est cliquée, afficher le mode dans la modale
        $('.client-photo-thumb').on('click', function() {
                var matricule = $(this).data('target').replace('#photoModal-', '');
                var src = $(this).attr('src');
                var mode = '';
                if (src) {
                        var ext = src.split('.').pop().toLowerCase();
                        switch(ext) {
                                case 'jpg':
                                case 'jpeg':
                                        mode = 'JPEG';
                                        break;
                                case 'png':
                                        mode = 'PNG';
                    break;
                case 'gif':
                    mode = 'GIF';
                    break;
                case 'bmp':
                    mode = 'BMP';
                    break;
                case 'webp':
                    mode = 'WebP';
                    break;
                default:
                    mode = ext.toUpperCase();
            }
        }
        $('#photo-mode-' + matricule).text(mode ? '(' + mode + ')' : '');
    });
    // Nettoyer le mode à la fermeture de la modale
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('[id^="photo-mode-"]').text('');
    });
});
</script>
@endpush

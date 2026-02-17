
@extends('layouts.app')

@section('page_title', 'Liste des agents')
@section('breadcrumb_parent', 'Ressources Humaines')
@section('breadcrumb', 'Liste des agents')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <a href="{{ route('agents.create') }}" class="btn btn-primary float-right">Ajouter un agent</a>
        </div>
        <div class="card-body">
            <div id="agents-table-toolbar" class="d-flex flex-wrap align-items-center justify-content-between mb-3 p-2 rounded shadow-sm" style="background: #232a32; border: 1px solid #444; min-height: 56px;">
                <div id="agents-table-buttons" class="mb-2 mb-md-0 "></div>
                <div id="agents-table-search" class="datatable-search ms-md-3 "></div>
            </div>
            <div class="table-responsive">
                <table id="agents-table" class="table table-bordered table-striped datatable" data-buttons-container="#agents-table-buttons">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Postnom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Poste</th>
                            <th>Service</th>
                            <th>Photo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $loopIndex => $agent)
                        <tr>
                            <td>{{ $loopIndex + 1 }}</td>
                            <td>{{ $agent->matricule }}</td>
                            <td>{{ $agent->nom }}</td>
                            <td>{{ $agent->postnom ?? '' }}</td>
                            <td>{{ $agent->prenom }}</td>
                            <td>{{ $agent->email }}</td>
                            <td>{{ $agent->telephone }}</td>
                            <td>{{ $agent->poste->libelle ?? '' }}</td>
                            <td>{{ $agent->service->libelle ?? '' }}</td>
                            <td>
                                @if($agent->photo)
                                    <img src="{{ asset('images_projet/agents/' . $agent->photo) }}?v={{ time() }}" alt="Photo" class="agent-photo-thumb" data-photo-src="{{ asset('images_projet/agents/' . $agent->photo) }}?v={{ time() }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; cursor:pointer;" data-toggle="modal" data-target="#photoModal-{{ $agent->matricule }}">
                                    <!-- Modal pour chaque agent -->
                                    <div class="modal fade" id="photoModal-{{ $agent->matricule }}" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel-{{ $agent->matricule }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content bg-dark">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title text-white" id="photoModalLabel-{{ $agent->matricule }}">Photo de l'agent <small class="text-info ms-2" id="photo-mode-{{ $agent->matricule }}" style="font-size: 0.9em;"></small></h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ asset('images_projet/agents/' . $agent->photo) }}" alt="Photo de l'agent" class="img-fluid rounded shadow photo-modal-img" style="max-width: 100%; max-height: 80vh; background: #222;" data-matricule="{{ $agent->matricule }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Aucune</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('agents.show', $agent->matricule) }}" class="btn btn-sm btn-info">Voir</a>
                                <a href="{{ route('agents.edit', $agent->matricule) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="{{ route('agents.destroy', $agent->matricule) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet agent ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function() {
    // Quand une miniature est cliquée, afficher le mode dans la modale
    $('.agent-photo-thumb').on('click', function() {
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

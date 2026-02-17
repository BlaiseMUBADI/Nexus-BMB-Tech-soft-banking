@extends('layouts.app')

@section('page_title', 'Détail du client')
@section('breadcrumb_parent', 'Clients / Membres')
@section('breadcrumb', 'Détail du client')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 w-100">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-5 text-center mb-3 mb-md-0">
                            <img src="{{ url('/clients/photo/' . basename($client->photo)) }}" alt="Photo du client" class="img-fluid rounded-circle border border-3 border-success shadow" style="max-width: 320px; max-height: 320px; object-fit: cover; background: #f8f9fa; cursor: pointer;" id="client-photo" data-toggle="modal" data-target="#photoModal">
                        </div>
                        <div class="col-lg-8 col-md-7">
                            <h2 class="mb-2" style="color:#0d6efd;font-weight:bold;">{{ $client->nom }} {{ $client->postnom }} {{ $client->prenom }}</h2>
                            <h5 class="mb-3" style="color:#20c997;font-weight:bold;">Matricule : {{ $client->matricule }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item"><strong>Sexe :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->sexe }}</span></li>
                                        <li class="list-group-item"><strong>Date de naissance :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->date_naissance }}</span></li>
                                        <li class="list-group-item"><strong>Lieu de naissance :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->lieu_naissance }}</span></li>
                                        <li class="list-group-item"><strong>Email :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->email }}</span></li>
                                        <li class="list-group-item"><strong>Téléphone :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->telephone }}</span></li>
                                        <li class="list-group-item"><strong>Adresse :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->adresse }}</span></li>
                                        <li class="list-group-item"><strong>Zone :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->zone }}</span></li>
                                        <li class="list-group-item"><strong>État civil :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->etat_civil }}</span></li>
                                        <li class="list-group-item"><strong>Nom du conjoint :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->nom_conjoint }}</span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item"><strong>Type pièce d'identité :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->type_piece_identite }}</span></li>
                                        <li class="list-group-item"><strong>Numéro pièce d'identité :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->numero_piece_identite }}</span></li>
                                        <li class="list-group-item"><strong>Lieu délivrance pièce :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->lieu_delivrance_piece }}</span></li>
                                        <li class="list-group-item"><strong>Date délivrance pièce :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->date_delivrance_piece }}</span></li>
                                        <li class="list-group-item"><strong>Secteur d'activité :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->secteur_activite }}</span></li>
                                        <li class="list-group-item"><strong>Type d'activité :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->type_activite }}</span></li>
                                        <li class="list-group-item"><strong>Nom entreprise :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->nom_entreprise }}</span></li>
                                        <li class="list-group-item"><strong>Adresse entreprise :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->adresse_entreprise }}</span></li>
                                        <li class="list-group-item"><strong>Téléphone entreprise :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->telephone_entreprise }}</span></li>
                                        <li class="list-group-item"><strong>Statut entreprise :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->statut_entreprise }}</span></li>
                                        <li class="list-group-item"><strong>Nombre années expérience :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->nombre_annees_experience }}</span></li>
                                        <li class="list-group-item"><strong>Revenu mensuel :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->revenu_mensuel }} {{ $client->revenu_mensuel_devise }}</span></li>
                                        <li class="list-group-item"><strong>Autres détails activité :</strong> <span style="color:#20c997;font-weight:bold;">{{ $client->autres_details_activite }}</span></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ url('/clients/' . $client->matricule . '/edit') }}" class="btn btn-warning me-2">Modifier</a>
                            <form action="{{ url('/clients/' . $client->matricule) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger me-2">Supprimer</button>
                            </form>
                            <a href="{{ url('/clients') }}" class="btn btn-secondary">Retour à la liste</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Bootstrap pour afficher l'image en grand -->
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
                <img src="{{ url('/clients/photo/' . basename($client->photo)) }}" alt="Photo du client" class="img-fluid rounded shadow" style="max-width: 100%; max-height: 80vh; background: #222;">
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
$(function() {
    $('#client-photo').on('click', function() {
        $('#photoModal').modal('show');
    });
    // Pour l'accessibilité : rendre le focus au body à la fermeture de la modal
    $('#photoModal').on('hidden.bs.modal', function () {
        document.body.focus();
    });
});
</script>
@endpush

@endsection

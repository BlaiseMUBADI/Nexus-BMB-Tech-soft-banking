@extends('layouts.app')

@section('page_title', 'Détail de l\'agent')
@section('breadcrumb_parent', 'Ressources Humaines')
@section('breadcrumb', 'Détail de l\'agent')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 w-100">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-5 text-center mb-3 mb-md-0">
                            @if($agent->photo)
                                <img src="{{ asset('images_projet/' . $agent->photo) }}" alt="Photo de l'agent" class="img-fluid rounded-circle border border-3 border-success shadow" style="max-width: 320px; max-height: 320px; object-fit: cover; background: #f8f9fa; cursor: pointer;" id="agent-photo" data-toggle="modal" data-target="#photoModal">
                            @else
                                <span class="text-muted">Aucune photo</span>
                            @endif
                        </div>
                        <div class="col-lg-8 col-md-7">
                            <h2 class="mb-2" style="color:#0d6efd;font-weight:bold;">{{ $agent->nom }} {{ $agent->postnom }} {{ $agent->prenom }}</h2>
                            <h5 class="mb-3" style="color:#20c997;font-weight:bold;">Matricule : {{ $agent->matricule }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item"><strong>Sexe :</strong> <span style="color:#20c997;font-weight:bold;">{{ $agent->sexe }}</span></li>
                                        <li class="list-group-item"><strong>Date de naissance :</strong> <span style="color:#20c997;font-weight:bold;">{{ $agent->date_naissance }}</span></li>
                                        <li class="list-group-item"><strong>Email :</strong> <span style="color:#20c997;font-weight:bold;">{{ $agent->email }}</span></li>
                                        <li class="list-group-item"><strong>Téléphone :</strong> <span style="color:#20c997;font-weight:bold;">{{ $agent->telephone }}</span></li>
                                        <li class="list-group-item"><strong>Adresse :</strong> <span style="color:#20c997;font-weight:bold;">{{ $agent->adresse }}</span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item"><strong>Date d'embauche :</strong> <span style="color:#20c997;font-weight:bold;">{{ $agent->date_embauche }}</span></li>
                                        <li class="list-group-item"><strong>Statut :</strong> <span style="color:#20c997;font-weight:bold;">{{ $agent->statut }}</span></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('agents.edit', $agent->matricule) }}" class="btn btn-warning me-2">Modifier</a>
                            <a href="{{ route('agents.index') }}" class="btn btn-secondary">Retour à la liste</a>
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
                    <h5 class="modal-title text-white" id="photoModalLabel">Photo de l'agent</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    @if($agent->photo)
                        <img src="{{ asset('images_projet/' . $agent->photo) }}" alt="Photo de l'agent" class="img-fluid rounded shadow" style="max-width: 100%; max-height: 80vh; background: #222;">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
$(function() {
    $('#agent-photo').on('click', function() {
        $('#photoModal').modal('show');
    });
    $('#photoModal').on('hidden.bs.modal', function () {
        document.body.focus();
    });
});
</script>
@endpush
@endsection

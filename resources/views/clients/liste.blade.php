@extends('layouts.app')

@section('page_title', 'Liste des clients')
@section('breadcrumb_parent', 'Clients / Membres')
@section('breadcrumb', 'Liste des clients')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Liste des clients</h3>
            <a href="{{ url('/clients/create') }}" class="btn btn-primary float-right">Ajouter un client</a>
        </div>
        <div class="card-body">
            <!-- Conteneur pour les boutons DataTables -->
            <div id="clients-table-buttons" class="mb-2 bg-light p-2 rounded border"></div>
            
            <div class="table-responsive">
                <table id="clients-table" class="table table-bordered table-striped datatable" data-buttons-container="#clients-table-buttons">
                    <thead>
                        <tr>
                            <th>ID</th>
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
                        @foreach($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>{{ $client->matricule }}</td>
                            <td>{{ $client->zone ?? '' }}</td>
                            <td>{{ $client->nom }}</td>
                            <td>{{ $client->postnom ?? '' }}</td>
                            <td>{{ $client->prenom }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->telephone }}</td>
                            <td>
                                @if($client->photo)
                                    <img src="{{ route('clients.photo', basename($client->photo)) }}" alt="Photo" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <span class="text-muted">Aucune</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('/clients/' . $client->id) }}" class="btn btn-sm btn-info">Voir</a>
                                <a href="{{ url('/clients/' . $client->id . '/edit') }}" class="btn btn-sm btn-warning">Modifier</a>
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

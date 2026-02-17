@extends('layouts.app')

@section('page_title', 'Postes du service : ' . $service->nom)
@section('breadcrumb_parent', 'Services')
@section('breadcrumb', 'Postes')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header pb-0">
            <h5>Service : <span class="text-primary">{{ $service->nom }}</span></h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if($postes->count() == 0)
                <div class="alert alert-info mb-3">Aucun poste pour ce service.</div>
                <div class="card mb-3">
                    <div class="card-header">Ajouter un poste à ce service</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('postes.ajaxStore', $service->id) }}" class="form-ajout-poste" data-service-id="{{ $service->id }}">
                            @csrf
                            <div class="form-row">
                                <div class="col-md-4">
                                    <input type="text" name="nom" class="form-control" placeholder="Nom du poste" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="description" class="form-control" placeholder="Description">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-block">Ajouter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="mb-3">
                    <form method="POST" action="{{ route('postes.ajaxStore', $service->id) }}" class="form-ajout-poste" data-service-id="{{ $service->id }}">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-4">
                                <input type="text" name="nom" class="form-control" placeholder="Nom du poste" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="description" class="form-control" placeholder="Description">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">Ajouter</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom du poste</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($postes as $poste)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $poste->nom }}</td>
                                <td>{{ $poste->description }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection

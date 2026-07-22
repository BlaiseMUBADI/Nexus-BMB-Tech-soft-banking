@extends('layouts.app')

@section('page_title', 'Détail de la permission')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', $permission->code)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-key mr-2"></i>{{ $permission->code }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered mb-0">
                        <tr>
                            <th style="width:35%;" class="bg-light">Code</th>
                            <td><code class="text-primary">{{ $permission->code }}</code></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Nom</th>
                            <td><strong>{{ $permission->nom }}</strong></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Description</th>
                            <td>{{ $permission->description ?: '—' }}</td>
                        </tr>
                    </table>
                    <a href="{{ route('administration.roles_permissions') }}" class="btn btn-secondary btn-sm mt-3">
                        <i class="fas fa-arrow-left mr-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

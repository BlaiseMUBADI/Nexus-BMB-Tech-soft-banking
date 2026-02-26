@extends('layouts.app')

@section('page_title', 'Mon Profil')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Mes informations</div>
                <div class="card-body">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary mb-3">Modifier mes informations</a>
                    <table class="table table-bordered">
                        <tr><th>Nom</th><td>{{ Auth::user()->name }}</td></tr>
                        <tr><th>Email</th><td>{{ Auth::user()->email }}</td></tr>
                        <!-- Ajoute d'autres champs ici si besoin -->
                    </table>
                    <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Supprimer définitivement votre profil ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

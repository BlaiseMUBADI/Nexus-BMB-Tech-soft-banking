@extends('layouts.app')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="text-success mb-4"><i class="fas fa-university"></i> Logiciel de Banque Prêt !</h1>
                <p class="mb-4">Bienvenue sur votre application bancaire. Utilisez le menu pour naviguer entre les comptes et les transactions.</p>
                <a href="{{ url('/page1') }}" class="btn btn-primary m-2">Voir les comptes</a>
                <a href="{{ url('/page2') }}" class="btn btn-info m-2">Voir les transactions</a>
            </div>
        </div>
    </div>
</section>
@endsection
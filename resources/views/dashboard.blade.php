@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Dashboard Bancaire</h1>
    <p>Bienvenue sur votre tableau de bord. Ici vous pourrez gérer les opérations bancaires, consulter les comptes, et accéder aux fonctionnalités principales.</p>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Comptes</h5>
                    <p class="card-text">Voir et gérer les comptes bancaires.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Transactions</h5>
                    <p class="card-text">Effectuer et consulter les transactions.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text">Gérer les utilisateurs et les droits d'accès.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

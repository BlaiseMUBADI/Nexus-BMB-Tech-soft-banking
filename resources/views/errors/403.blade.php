@extends('layouts.app')

@section('title', 'Accès refusé')

@section('content')
<div class="content-wrapper d-flex align-items-center justify-content-center" style="min-height:60vh;">
    <div class="text-center">
        <div class="error-page">
            <h2 class="headline text-warning" style="font-size:100px;font-weight:700;">403</h2>
            <div class="error-content">
                <h3>
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                    Accès refusé
                </h3>
                <p class="text-muted">
                    Vous n'avez pas la permission d'accéder à cette page.<br>
                    Contactez votre administrateur si vous pensez que c'est une erreur.
                </p>
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                   class="btn btn-warning mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-default">
                    <i class="fas fa-home mr-1"></i> Tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

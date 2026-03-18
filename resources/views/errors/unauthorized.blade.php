@extends('layouts.app')

@section('content')
<div style="
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 120px);
    padding: 20px;
">
    <div style="
        max-width: 500px;
        text-align: center;
        padding: 30px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    ">
        <!-- Icône lucide X avec cercle rouge -->
        <div style="
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #f8d7da;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #c00;
        ">
            <i class="fas fa-lock" style="font-size: 40px;"></i>
        </div>

        <!-- Titre -->
        <h2 style="
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0 0 10px 0;
        ">
            Accès Refusé
        </h2>

        <!-- Sous-titre -->
        <p style="
            font-size: 14px;
            color: #666;
            margin: 0 0 20px 0;
            line-height: 1.5;
        ">
            Vous n'avez pas l'autorisation nécessaire pour effectuer cette action.
        </p>

        <!-- Message détaillé -->
        <div style="
            background: #f9f9f9;
            border-left: 4px solid #c00;
            padding: 12px 15px;
            margin-bottom: 20px;
            text-align: left;
            border-radius: 3px;
        ">
            <p style="
                font-size: 13px;
                color: #555;
                margin: 0;
                line-height: 1.6;
            ">
                <strong>Permission requise :</strong> {{ $permission ?? 'Supprimer une opération de caisse' }}<br>
                @if($details ?? null)
                    <strong>Détails :</strong> {{ $details }}
                @endif
            </p>
        </div>

        <!-- Bouton retour -->
        <div style="margin-top: 25px;">
            <a href="{{ url()->previous() }}" style="
                display: inline-block;
                padding: 10px 25px;
                background: #1a7a4a;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-size: 14px;
                font-weight: 500;
                transition: background 0.3s;
            "
            onmouseover="this.style.background='#145a3b'"
            onmouseout="this.style.background='#1a7a4a'"
            >
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </a>
        </div>

        <!-- Contact support -->
        <p style="
            font-size: 12px;
            color: #999;
            margin-top: 20px;
        ">
            Si vous pensez que c'est une erreur, veuillez contacter votre superviseur ou l'administrateur système.
        </p>
    </div>
</div>
@endsection

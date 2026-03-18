@extends('layouts.app')

@section('title', 'Accès refusé')

@section('content')
<div style="
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 120px);
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
">
    <div style="
        max-width: 550px;
        width: 100%;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        padding: 40px 30px;
        text-align: center;
    ">
        <!-- Numéro erreur -->
        <div style="
            font-size: 120px;
            font-weight: 700;
            color: #ffc107;
            line-height: 1;
            margin-bottom: 15px;
            text-shadow: 2px 2px 0 rgba(0,0,0,0.05);
        ">
            403
        </div>

        <!-- Icône warning -->
        <div style="
            margin: 15px 0 25px 0;
        ">
            <i class="fas fa-lock" style="
                font-size: 48px;
                color: #dc3545;
            "></i>
        </div>

        <!-- Titre -->
        <h2 style="
            font-size: 26px;
            font-weight: 600;
            color: #333;
            margin: 0 0 12px 0;
        ">
            Accès Refusé
        </h2>

        <!-- Sous-titre -->
        <p style="
            font-size: 15px;
            color: #666;
            margin: 0 0 25px 0;
            line-height: 1.6;
        ">
            Vous n'avez pas l'autorisation d'accéder à cette page.<br>
            Contactez votre administrateur si vous pensez que c'est une erreur.
        </p>

        <!-- Boîte info -->
        <div style="
            background: #f9f9f9;
            border-left: 4px solid #dc3545;
            padding: 12px 15px;
            margin-bottom: 25px;
            border-radius: 3px;
            text-align: left;
        ">
            <p style="
                font-size: 13px;
                color: #555;
                margin: 0;
                line-height: 1.5;
            ">
                <strong>Raison:</strong> Permission insuffisante pour exécuter cette action.<br>
                <strong>Action:</strong> Demandez à votre superviseur ou administrateur.
            </p>
        </div>

        <!-- Boutons -->
        <div style="
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        ">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
               style="
                   display: inline-block;
                   padding: 10px 24px;
                   background: #ffc107;
                   color: #333;
                   text-decoration: none;
                   border-radius: 4px;
                   font-weight: 500;
                   font-size: 14px;
                   transition: all 0.3s;
                   border: none;
               "
               onmouseover="this.style.background='#e0a800'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.15)'"
               onmouseout="this.style.background='#ffc107'; this.style.boxShadow='none'"
            >
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </a>
            <a href="{{ route('dashboard') }}"
               style="
                   display: inline-block;
                   padding: 10px 24px;
                   background: #1a7a4a;
                   color: white;
                   text-decoration: none;
                   border-radius: 4px;
                   font-weight: 500;
                   font-size: 14px;
                   transition: all 0.3s;
                   border: none;
               "
               onmouseover="this.style.background='#145a3b'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.15)'"
               onmouseout="this.style.background='#1a7a4a'; this.style.boxShadow='none'"
            >
                <i class="fas fa-home mr-1"></i> Tableau de bord
            </a>
        </div>

        <!-- Footer info -->
        <div style="
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        ">
            <p style="
                font-size: 12px;
                color: #999;
                margin: 0;
            ">
                Pour plus d'assistance, contactez le support technique.
            </p>
        </div>
    </div>
</div>
@endsection

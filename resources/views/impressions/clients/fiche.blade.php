@extends('impressions._layout')

@section('titre', 'Fiche Client')

@section('contenu')

@php
    $typesPiece = [
        'CNI'       => 'Carte Nationale d\'Identité',
        'PASSEPORT' => 'Passeport',
        'PERMIS'    => 'Permis de conduire',
        'AUTRE'     => 'Autre pièce',
    ];
    $typesCompte = [
        'COURANT'         => 'Compte Courant',
        'EPARGNE_LIBRE'   => 'Épargne Libre',
        'EPARGNE_BLOQUEE' => 'Épargne Bloquée',
        'CAUTION_CREDIT'  => 'Caution Crédit',
    ];
@endphp

{{-- ── Bloc identité + photo ── --}}
<div style="display:table; width:100%; margin-bottom:12px;">
    {{-- Colonne info --}}
    <div style="display:table-cell; vertical-align:top; width:75%;">
        <div class="section">
            <div class="section-title">Identité du membre</div>
            <table class="info-table">
                <tr>
                    <td class="label">Matricule</td>
                    <td><strong>{{ $client->matricule }}</strong></td>
                    <td class="label">Sexe</td>
                    <td>{{ $client->sexe === 'M' ? 'Masculin' : 'Féminin' }}</td>
                </tr>
                <tr>
                    <td class="label">Nom complet</td>
                    <td colspan="3">
                        <strong>
                            {{ strtoupper($client->nom) }}
                            {{ strtoupper($client->postnom ?? '') }}
                            {{ ucfirst(strtolower($client->prenom ?? '')) }}
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td class="label">Date de naissance</td>
                    <td>{{ $client->date_naissance ? \Carbon\Carbon::parse($client->date_naissance)->format('d/m/Y') : '—' }}</td>
                    <td class="label">Lieu de naissance</td>
                    <td>{{ $client->lieu_naissance ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">État civil</td>
                    <td>{{ $client->etat_civil ?? '—' }}</td>
                    <td class="label">Conjoint(e)</td>
                    <td>{{ $client->nom_conjoint ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Adresse</td>
                    <td colspan="3">{{ $client->adresse ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Téléphone</td>
                    <td>{{ $client->telephone ?? '—' }}</td>
                    <td class="label">Email</td>
                    <td>{{ $client->email ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Zone / Secteur</td>
                    <td colspan="3">{{ $client->zone->nom ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>
    {{-- Colonne photo --}}
    <div style="display:table-cell; vertical-align:top; width:25%; padding-left:10px; text-align:center;">
        @if($photoBase64)
            <img src="{{ $photoBase64 }}" alt="Photo"
                 style="width:90px; height:110px; object-fit:cover; border:2px solid #1a7a4a; border-radius:4px;">
        @else
            <div style="width:90px; height:110px; border:2px dashed #aaa; border-radius:4px;
                        margin:0 auto; line-height:110px; font-size:9px; color:#aaa; text-align:center;">
                Pas de photo
            </div>
        @endif
        <div style="font-size:8px; color:#666; margin-top:4px;">{{ $client->matricule }}</div>
    </div>
</div>

{{-- ── Pièce d'identité ── --}}
<div class="section">
    <div class="section-title">Pièce d'identité</div>
    <table class="info-table">
        <tr>
            <td class="label" style="width:25%;">Type</td>
            <td>{{ $typesPiece[$client->type_piece_identite] ?? $client->type_piece_identite ?? '—' }}</td>
            <td class="label" style="width:25%;">Numéro</td>
            <td>{{ $client->numero_piece_identite ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Date de délivrance</td>
            <td>{{ $client->date_delivrance_piece ? \Carbon\Carbon::parse($client->date_delivrance_piece)->format('d/m/Y') : '—' }}</td>
            <td class="label">Lieu de délivrance</td>
            <td>{{ $client->lieu_delivrance_piece ?? '—' }}</td>
        </tr>
    </table>
</div>

{{-- ── Activité économique ── --}}
@if($client->secteur_activite || $client->nom_entreprise)
<div class="section">
    <div class="section-title">Activité économique</div>
    <table class="info-table">
        <tr>
            <td class="label" style="width:30%;">Secteur d'activité</td>
            <td>{{ $client->secteur_activite ?? '—' }}</td>
            <td class="label" style="width:20%;">Type</td>
            <td>{{ $client->type_activite ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Entreprise / Employeur</td>
            <td>{{ $client->nom_entreprise ?? '—' }}</td>
            <td class="label">Statut</td>
            <td>{{ $client->statut_entreprise ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Adresse entreprise</td>
            <td>{{ $client->adresse_entreprise ?? '—' }}</td>
            <td class="label">Tél. entreprise</td>
            <td>{{ $client->telephone_entreprise ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Années d'expérience</td>
            <td>{{ $client->nombre_annees_experience ?? '—' }}</td>
            <td class="label">Revenu mensuel</td>
            <td>
                @if($client->revenu_mensuel)
                    {{ number_format($client->revenu_mensuel, 2, ',', ' ') }}
                    {{ $client->revenu_mensuel_devise ?? '' }}
                @else —
                @endif
            </td>
        </tr>
    </table>
</div>
@endif

{{-- ── Comptes ── --}}
<div class="section">
    <div class="section-title">Comptes COOPEC EBEN ({{ $client->comptes->count() }})</div>
    @if($client->comptes->isEmpty())
        <table class="info-table">
            <tr><td colspan="5" style="text-align:center; color:#999; padding:10px;">Aucun compte enregistré</td></tr>
        </table>
    @else
        <table class="info-table">
            <thead>
                <tr style="background:#1a7a4a; color:#fff;">
                    <th style="padding:5px 8px; font-size:9px;">Code compte</th>
                    <th style="padding:5px 8px; font-size:9px;">Type</th>
                    <th style="padding:5px 8px; font-size:9px;">Devise</th>
                    <th style="padding:5px 8px; font-size:9px; text-align:right;">Solde réel</th>
                    <th style="padding:5px 8px; font-size:9px; text-align:right;">Solde bloqué</th>
                    <th style="padding:5px 8px; font-size:9px;">Ouvert le</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->comptes as $compte)
                <tr>
                    <td style="font-family:DejaVu Sans Mono,monospace; font-size:9px;">{{ $compte->code_compte }}</td>
                    <td>{{ $typesCompte[$compte->type] ?? $compte->type }}</td>
                    <td>{{ $compte->devise }}</td>
                    <td style="text-align:right;">{{ number_format($compte->solde_reel ?? 0, 2, ',', ' ') }}</td>
                    <td style="text-align:right;">{{ number_format($compte->solde_bloque ?? 0, 2, ',', ' ') }}</td>
                    <td>{{ \Carbon\Carbon::parse($compte->created_at)->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ── Adhésion ── --}}
<div style="margin-top:6px; font-size:9px; color:#666; text-align:right;">
    Membre depuis le : <strong>{{ \Carbon\Carbon::parse($client->created_at)->format('d/m/Y') }}</strong>
</div>

{{-- ── Signatures ── --}}
<div style="margin-top:28px; display:table; width:100%;">
    <div style="display:table-cell; text-align:center; width:33%;">
        <div style="border-top:1px solid #999; width:130px; margin:0 auto; padding-top:4px; font-size:9px; color:#666;">
            Signature du membre
        </div>
    </div>
    <div style="display:table-cell; text-align:center; width:34%;">
        <div style="border-top:1px solid #999; width:130px; margin:0 auto; padding-top:4px; font-size:9px; color:#666;">
            Agent responsable
        </div>
    </div>
    <div style="display:table-cell; text-align:center; width:33%;">
        <div style="border-top:1px solid #999; width:130px; margin:0 auto; padding-top:4px; font-size:9px; color:#666;">
            Cachet &amp; Signature COOPEC EBEN
        </div>
    </div>
</div>

@endsection

@extends('impressions._layout')

@section('titre', 'Liste des Comptes')

@section('contenu')

@php
    $typesLabels = [
        'CC'  => 'Compte Courant',
        'RMB' => 'Remboursement',
        'GTC' => 'Caution',
        'DAT' => 'Dépôt à Terme',
        'EAV' => 'Épargne & Vie',
    ];
    $libelles = [
        'type'           => 'Type',
        'devise'         => 'Devise',
        'date_debut'     => 'Du',
        'date_fin'       => 'Au',
        'solde_min'      => 'Solde >=',
        'solde_max'      => 'Solde <=',
        'etat_solde'     => 'État solde',
        'code_zone'      => 'Zone',
        'portefeuille_id'=> 'Portefeuille',
    ];
    $actifs = array_filter($filtres, fn($v) => $v !== null && $v !== '' && $v !== 'tous');
    $portefeuille = $portefeuille ?? null;
    $totalSolde = $comptes->sum('solde_reel');
    $totalBloque = $comptes->sum('solde_bloque');
@endphp

{{-- ── Résumé des filtres ── --}}
<div style="background:#f0f4f8; border:1px solid #c8d4e0; padding:5px 10px; margin-bottom:10px;
            font-size:8.5px; color:#444; border-radius:3px;">
    <strong>Critères :</strong>
    @if(empty($actifs))
        Tous les comptes
    @else
        @foreach($actifs as $key => $val)
            <span style="background:#1a7a4a; color:#fff; padding:2px 5px; border-radius:10px; margin-right:3px;">
                {{ $libelles[$key] ?? $key }} :
                @if($key === 'type') {{ $typesLabels[$val] ?? $val }}
                @elseif($key === 'code_zone') {{ $zone->nom ?? $val }}
                @elseif($key === 'portefeuille_id')
                    @if($val === 'aucun') Sans portefeuille
                    @elseif($portefeuille) {{ $portefeuille->nom_portefeuille }}
                    @else {{ $val }}
                    @endif
                @elseif($key === 'etat_solde')
                    {{ ['positif'=>'Positif','negatif'=>'Négatif','nul'=>'Nul'][$val] ?? $val }}
                @elseif(in_array($key, ['date_debut','date_fin'])) {{ \Carbon\Carbon::parse($val)->format('d/m/Y') }}
                @else {{ $val }}
                @endif
            </span>
        @endforeach
    @endif
    &nbsp;&nbsp;<strong>Total :</strong> {{ $comptes->count() }} compte(s)
</div>

{{-- ── Tableau ── --}}
@if($comptes->isEmpty())
    <div style="text-align:center; padding:20px; color:#999;">Aucun compte ne correspond aux critères sélectionnés.</div>
@else
<table class="info-table" style="font-size:8.5px;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff;">
            <th style="padding:4px 5px; width:18px;">#</th>
            <th style="padding:4px 5px;">Code Compte</th>
            <th style="padding:4px 5px;">Titulaire</th>
            <th style="padding:4px 5px;">Zone</th>
            <th style="padding:4px 5px;">Type</th>
            <th style="padding:4px 5px; text-align:right;">Solde réel</th>
            <th style="padding:4px 5px; text-align:right;">Solde bloqué</th>
            <th style="padding:4px 5px; text-align:center;">Devise</th>
            <th style="padding:4px 5px;">Ouvert le</th>
        </tr>
    </thead>
    <tbody>
        @foreach($comptes as $i => $compte)
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f7f9fc;' }}">
            <td style="padding:3px 5px; text-align:center;">{{ $i + 1 }}</td>
            <td style="padding:3px 5px; font-family:DejaVu Sans Mono,monospace; font-size:8px;">{{ $compte->code_compte }}</td>
            <td style="padding:3px 5px;">
                @if($compte->client)
                    {{ strtoupper($compte->client->nom) }}
                    {{ strtoupper($compte->client->postnom ?? '') }}
                    {{ ucfirst(strtolower($compte->client->prenom ?? '')) }}
                @else —
                @endif
            </td>
            <td style="padding:3px 5px; font-size:8px;">{{ $compte->client->zone->nom ?? '—' }}</td>
            <td style="padding:3px 5px; font-size:8px;">{{ $typesLabels[$compte->type] ?? $compte->type }}</td>
            <td style="padding:3px 5px; text-align:right; font-weight:bold;
                       color:{{ ($compte->solde_reel ?? 0) >= 0 ? '#1a5e1a' : '#c00' }};">
                {{ number_format($compte->solde_reel ?? 0, 2, ',', ' ') }}
            </td>
            <td style="padding:3px 5px; text-align:right;">
                {{ number_format($compte->solde_bloque ?? 0, 2, ',', ' ') }}
            </td>
            <td style="padding:3px 5px; text-align:center;">{{ $compte->devise }}</td>
            <td style="padding:3px 5px;">{{ \Carbon\Carbon::parse($compte->created_at)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#1a7a4a; color:#fff; font-weight:bold;">
            <td colspan="5" style="padding:4px 5px; text-align:right; font-size:9px;">TOTAUX</td>
            <td style="padding:4px 5px; text-align:right; font-size:9px;">
                {{ number_format($totalSolde, 2, ',', ' ') }}
            </td>
            <td style="padding:4px 5px; text-align:right; font-size:9px;">
                {{ number_format($totalBloque, 2, ',', ' ') }}
            </td>
            <td colspan="2" style="padding:4px 5px;"></td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

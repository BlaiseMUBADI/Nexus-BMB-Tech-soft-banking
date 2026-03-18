@extends('impressions._layout')

@section('titre', 'Liste des Clients')

@section('contenu')

@php
    $libelles = [
        'code_zone'    => 'Zone',
        'sexe'         => 'Sexe',
        'date_debut'   => 'Du',
        'date_fin'     => 'Au',
        'avec_photo'   => 'Photo',
        'avec_comptes' => 'Comptes',
        'etat_civil'   => 'État civil',
    ];
    $actifs = array_filter($filtres, fn($v) => $v !== null && $v !== '' && $v !== 'tous');
@endphp

{{-- ── Résumé des filtres actifs ── --}}
<div style="background:#f0f4f8; border:1px solid #c8d4e0; padding:6px 12px; margin-bottom:10px;
            font-size:9px; color:#444; border-radius:3px;">
    <strong>Critères :</strong>
    @if(empty($actifs))
        Tous les clients
    @else
        @foreach($actifs as $key => $val)
            <span style="background:#1a7a4a; color:#fff; padding:2px 6px; border-radius:10px; margin-right:4px;">
                {{ $libelles[$key] ?? $key }} :
                @if($key === 'sexe') {{ $val === 'M' ? 'Hommes' : 'Femmes' }}
                @elseif($key === 'code_zone') {{ $zone->nom ?? $val }}
                @elseif($key === 'avec_photo') {{ $val === 'oui' ? 'Avec photo' : 'Sans photo' }}
                @elseif($key === 'avec_comptes') {{ $val === 'oui' ? 'Avec comptes' : 'Sans compte' }}
                @elseif(in_array($key, ['date_debut','date_fin'])) {{ \Carbon\Carbon::parse($val)->format('d/m/Y') }}
                @else {{ $val }}
                @endif
            </span>
        @endforeach
    @endif
    &nbsp;&nbsp; <strong>Total :</strong> {{ $clients->count() }} client(s)
</div>

{{-- ── Tableau ── --}}
@if($clients->isEmpty())
    <div style="text-align:center; padding:20px; color:#999;">Aucun client ne correspond aux critères sélectionnés.</div>
@else
<table class="info-table" style="font-size:9.2px; border:2px solid #333; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:6px 8px; width:4%; border:2.5px solid #333; text-align:center;">#</th>
            <th style="padding:6px 8px; width:13%; border:2.5px solid #333;">Matricule</th>
            <th style="padding:6px 8px; width:20%; border:2.5px solid #333;">Nom complet</th>
            <th style="padding:6px 8px; width:5%; border:2.5px solid #333; text-align:center;">Sexe</th>
            <th style="padding:6px 8px; width:10%; border:2.5px solid #333;">Zone</th>
            <th style="padding:6px 8px; width:12%; border:2.5px solid #333;">Téléphone</th>
            <th style="padding:6px 8px; width:20%; border:2.5px solid #333;">Email</th>
            <th style="padding:6px 8px; width:8%; border:2.5px solid #333; text-align:center;">Comptes</th>
            <th style="padding:6px 8px; width:8%; border:2.5px solid #333;">Membre depuis</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clients as $i => $client)
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333; font-size:8.8px;">
            <td style="padding:4px 6px; line-height:1.15; text-align:center; vertical-align:middle; border:2px solid #333; color:#111;">{{ $i + 1 }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; font-family:DejaVu Sans Mono,monospace; font-size:8.8px; border:2px solid #333; color:#111;">{{ $client->matricule }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">
                {{ strtoupper($client->nom) }}
                {{ strtoupper($client->postnom ?? '') }}
                {{ ucfirst(strtolower($client->prenom ?? '')) }}
            </td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:center; white-space:nowrap; border:2px solid #333; color:#111;">{{ $client->sexe === 'M' ? 'H' : 'F' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; white-space:nowrap; border:2px solid #333; color:#111;">{{ $client->zone->nom ?? '—' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; white-space:nowrap; border:2px solid #333; color:#111;">{{ $client->telephone ?? '—' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; font-size:8.8px; white-space:nowrap; border:2px solid #333; color:#111;">{{ $client->email ?? '—' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:center; white-space:nowrap; border:2px solid #333; color:#111;">{{ $client->comptes_count }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; white-space:nowrap; border:2px solid #333; color:#111;">{{ \Carbon\Carbon::parse($client->created_at)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#d9e8e0; font-weight:bold; border:2.5px solid #333;">
            <td colspan="7" style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">TOTAL</td>
            <td style="padding:6px 8px; text-align:center; border:2.5px solid #333; color:#111;">{{ $clients->sum('comptes_count') }}</td>
            <td style="padding:6px 8px; border:2.5px solid #333;"></td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

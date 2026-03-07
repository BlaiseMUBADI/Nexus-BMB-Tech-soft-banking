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
<table class="info-table" style="font-size:9.5px;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff;">
            <th style="padding:5px 6px; width:20px;">#</th>
            <th style="padding:5px 6px;">Matricule</th>
            <th style="padding:5px 6px;">Nom complet</th>
            <th style="padding:5px 6px;">Sexe</th>
            <th style="padding:5px 6px;">Zone</th>
            <th style="padding:5px 6px;">Téléphone</th>
            <th style="padding:5px 6px;">Email</th>
            <th style="padding:5px 6px; text-align:center;">Comptes</th>
            <th style="padding:5px 6px;">Membre depuis</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clients as $i => $client)
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f7f9fc;' }}">
            <td style="padding:4px 6px; text-align:center;">{{ $i + 1 }}</td>
            <td style="padding:4px 6px; font-family:DejaVu Sans Mono,monospace; font-size:8.5px;">{{ $client->matricule }}</td>
            <td style="padding:4px 6px;">
                {{ strtoupper($client->nom) }}
                {{ strtoupper($client->postnom ?? '') }}
                {{ ucfirst(strtolower($client->prenom ?? '')) }}
            </td>
            <td style="padding:4px 6px; text-align:center;">{{ $client->sexe === 'M' ? 'H' : 'F' }}</td>
            <td style="padding:4px 6px;">{{ $client->zone->nom ?? '—' }}</td>
            <td style="padding:4px 6px;">{{ $client->telephone ?? '—' }}</td>
            <td style="padding:4px 6px; font-size:8px;">{{ $client->email ?? '—' }}</td>
            <td style="padding:4px 6px; text-align:center;">{{ $client->comptes->count() }}</td>
            <td style="padding:4px 6px;">{{ \Carbon\Carbon::parse($client->created_at)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#e8eef5; font-weight:bold;">
            <td colspan="7" style="padding:5px 6px; text-align:right;">TOTAL</td>
            <td style="padding:5px 6px; text-align:center;">{{ $clients->sum(fn($c) => $c->comptes->count()) }}</td>
            <td style="padding:5px 6px;"></td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

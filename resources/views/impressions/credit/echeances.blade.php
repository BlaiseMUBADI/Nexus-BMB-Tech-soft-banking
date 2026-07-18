@extends('impressions._layout')

@section('titre', 'Tombée d\'échéances')

@section('contenu')

@php
    $libelles = [
        'date_echeance'   => 'Date échéance',
        'date_debut'      => 'Du',
        'date_fin'        => 'Au',
        'devise'          => 'Devise',
        'zone'            => 'Zone',
        'portefeuille_id' => 'Portefeuille',
        'statut_echeance' => 'Statut',
    ];
    $actifs = $filtres ?? [];
@endphp

<div style="background:#f0f4f8; border:1px solid #c8d4e0; padding:6px 12px; margin-bottom:10px;
            font-size:9px; color:#444; border-radius:3px;">
    <strong>Critères :</strong>
    @if(empty($actifs))
        Toutes les échéances en attente, en retard ou partiellement payées
    @else
        @foreach($actifs as $key => $val)
            <span style="background:#1a7a4a; color:#fff; padding:2px 6px; border-radius:10px; margin-right:4px;">
                {{ $libelles[$key] ?? $key }} :
                @if($key === 'zone')
                    {{ $zoneObj->nom ?? $val }}
                @elseif($key === 'portefeuille_id')
                    {{ $portefeuilleObj->nom_portefeuille ?? $val }}
                @elseif(in_array($key, ['date_debut','date_fin','date_echeance']))
                    {{ \Carbon\Carbon::parse($val)->format('d/m/Y') }}
                @else
                    {{ $val }}
                @endif
            </span>
        @endforeach
    @endif
    &nbsp;&nbsp; <strong>Total :</strong> {{ $echeances->count() }} échéance(s)
</div>

<div style="background:#e8f5e9; border:1px solid #a5d6a7; padding:6px 12px; margin-bottom:10px;
            font-size:9px; color:#2e7d32; border-radius:3px;">
    <strong>Reste à recouvrir par devise :</strong>
    @foreach($totauxParDevise as $devise => $t)
        @php $symbole = match($devise) { 'USD' => '$', 'EUR' => '€', default => 'Fc' }; @endphp
        <span style="background:#1a7a4a; color:#fff; padding:2px 6px; border-radius:10px; margin-right:4px;">
            {{ $devise }} : {{ number_format($t['reste_du'], 0, ',', ' ') }}{{ $symbole }} ({{ $t['count'] }} éch.)
        </span>
    @endforeach
</div>

@if($echeances->isEmpty())
    <div style="text-align:center; padding:20px; color:#999;">Aucune échéance ne correspond aux critères sélectionnés.</div>
@else
<table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333;">Date éch.</th>
            <th style="padding:5px 6px; width:5%; border:2.5px solid #333; text-align:center;">N°</th>
            <th style="padding:5px 6px; width:24%; border:2.5px solid #333;">Client</th>
            <th style="padding:5px 6px; width:12%; border:2.5px solid #333;">Zone</th>
            <th style="padding:5px 6px; width:13%; border:2.5px solid #333;">Portefeuille</th>
            <th style="padding:5px 6px; width:14%; border:2.5px solid #333; text-align:right;">Montant éch.</th>
            <th style="padding:5px 6px; width:11%; border:2.5px solid #333; text-align:right;">Payé</th>
            <th style="padding:5px 6px; width:11%; border:2.5px solid #333; text-align:right;">Reste dû</th>
        </tr>
    </thead>
    <tbody>
        @foreach($echeances as $i => $ech)
        @php
            $demande = $ech->echeancier->demande ?? null;
            $client = $demande->client ?? null;
            $resteDu = max(0, (float) $ech->total_echeance - (float) $ech->montant_paye);
        @endphp
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333; font-size:8.5px;">
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">{{ \Carbon\Carbon::parse($ech->date_echeance)->format('d/m/Y') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:center; border:2px solid #333; color:#111;">{{ $ech->numero_echeance }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">
                {{ $client ? strtoupper($client->nom ?? '') . ' ' . strtoupper($client->postnom ?? '') . ' ' . ucfirst(strtolower($client->prenom ?? '')) : '-' }}
            </td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">{{ $demande->zone->nom ?? '-' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">{{ $demande->portefeuille->nom_portefeuille ?? '-' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#111;">{{ number_format($ech->total_echeance, 0, ',', ' ') }} {{ $demande->devise ?? '' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#2e7d32;">{{ number_format($ech->montant_paye, 0, ',', ' ') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#c62828; font-weight:bold;">{{ number_format($resteDu, 0, ',', ' ') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#d9e8e0; font-weight:bold; border:2.5px solid #333;">
            <td colspan="7" style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">RESTE À RECOUVRIR (TOUTES DEVISES)</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#c62828;">{{ number_format($resteATotalGeneral, 0, ',', ' ') }}</td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

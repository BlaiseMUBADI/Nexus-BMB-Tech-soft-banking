@extends('impressions._layout')

@section('titre', 'Historique des Recouvrements Automatiques')

@section('contenu')

@php
    $totalRecupere = $historique->sum('montant');
@endphp

<div style="background:#f0f4f8; border:1px solid #c8d4e0; padding:6px 12px; margin-bottom:10px;
            font-size:9px; color:#444; border-radius:3px;">
    <strong>Total récupéré :</strong> {{ number_format($totalRecupere, 2, ',', ' ') }} CDF
    &nbsp;&nbsp; <strong>Opérations :</strong> {{ $historique->count() }}
</div>

@if($historique->isEmpty())
    <div style="text-align:center; padding:20px; color:#999;">Aucun recouvrement automatique enregistré.</div>
@else
<table class="info-table" style="font-size:8px; border:2px solid #333; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:5px 6px; width:12%; border:2.5px solid #333;">Date/Heure</th>
            <th style="padding:5px 6px; width:12%; border:2.5px solid #333;">Dossier</th>
            <th style="padding:5px 6px; width:16%; border:2.5px solid #333;">Client</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333;">Zone</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333;">Portefeuille</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Montant</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Solde avant</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Solde après</th>
            <th style="padding:5px 6px; width:5%; border:2.5px solid #333; text-align:center;">Éch.</th>
            <th style="padding:5px 6px; width:5%; border:2.5px solid #333; text-align:center;">Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($historique as $i => $tx)
        @php
            $client = $tx->compte->client ?? null;
            $zone = $client->zone ?? null;
            $portefeuille = $tx->compte->portefeuille ?? null;
            $echNumero = '';
            if (preg_match('/Ech\.\s*(\d+)/', $tx->observations ?? '', $m)) {
                $echNumero = $m[1];
            }
            $isPartiel = stripos($tx->observations ?? '', 'partiel') !== false;
            $numeroDossier = '';
            if (preg_match('/AUTO-REC-(.+?)-\d+-/', $tx->reference ?? '', $m)) {
                $numeroDossier = $m[1];
            }
        @endphp
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333; font-size:8px;">
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">{{ $tx->date_operation->format('d/m/Y H:i') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; font-family:DejaVu Sans Mono,monospace; font-size:7.5px; border:2px solid #333; color:#111;">{{ $numeroDossier ?: '-' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">{{ $client ? $client->full_name : '-' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">{{ $zone ? $zone->nom : '-' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">{{ $portefeuille ? $portefeuille->nom_portefeuille : '-' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#1a7a4a; font-weight:bold;">{{ number_format($tx->montant, 2, ',', ' ') }} {{ $tx->devise_code }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#111;">{{ number_format($tx->solde_compte_avant, 2, ',', ' ') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#111;">{{ number_format($tx->solde_compte_apres, 2, ',', ' ') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:center; border:2px solid #333; color:#111;">{{ $echNumero ?: '-' }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:center; border:2px solid #333;">
                @if($isPartiel)
                    <span style="background:#ff9800; color:#fff; padding:1px 4px; border-radius:6px; font-size:7px; font-weight:bold;">Partiel</span>
                @else
                    <span style="background:#1a7a4a; color:#fff; padding:1px 4px; border-radius:6px; font-size:7px; font-weight:bold;">Total</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#d9e8e0; font-weight:bold; border:2.5px solid #333;">
            <td colspan="5" style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">TOTAL</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#1a7a4a;">{{ number_format($totalRecupere, 2, ',', ' ') }} CDF</td>
            <td colspan="4" style="padding:6px 8px; border:2.5px solid #333;"></td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

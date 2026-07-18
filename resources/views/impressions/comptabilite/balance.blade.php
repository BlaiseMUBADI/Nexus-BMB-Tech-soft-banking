@extends('impressions._layout')

@section('titre', 'Balance Générale')

@section('contenu')

<div style="background:#e8f5e9; border:1px solid #a5d6a7; padding:6px 12px; margin-bottom:10px; font-size:9px; color:#2e7d32; border-radius:3px;">
    <strong>Total débit :</strong> {{ number_format($totaux['debit'], 2, ',', ' ') }}
    &nbsp;&nbsp; <strong>Total crédit :</strong> {{ number_format($totaux['credit'], 2, ',', ' ') }}
    &nbsp;&nbsp; <strong>{{ abs($totaux['debit'] - $totaux['credit']) < 0.01 ? 'BALANCE ÉQUILIBRÉE' : 'ATTENTION : DÉSÉQUILIBRE' }}</strong>
</div>

<table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:5px 6px; width:12%; border:2.5px solid #333;">Compte</th>
            <th style="padding:5px 6px; width:8%; border:2.5px solid #333; text-align:center;">Classe</th>
            <th style="padding:5px 6px; width:40%; border:2.5px solid #333;">Libellé</th>
            <th style="padding:5px 6px; width:13%; border:2.5px solid #333; text-align:right;">Débit</th>
            <th style="padding:5px 6px; width:13%; border:2.5px solid #333; text-align:right;">Crédit</th>
            <th style="padding:5px 6px; width:14%; border:2.5px solid #333; text-align:right;">Solde</th>
        </tr>
    </thead>
    <tbody>
        @foreach($balance as $i => $b)
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333; font-size:8.5px;">
            <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; border:2px solid #333; color:#111;">{{ $b->numero_compte }}</td>
            <td style="padding:4px 6px; text-align:center; border:2px solid #333; color:#111;">{{ $b->classe_ohada }}</td>
            <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $b->libelle }}</td>
            <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#2e7d32;">{{ number_format($b->total_debit, 2, ',', ' ') }}</td>
            <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#c62828;">{{ number_format($b->total_credit, 2, ',', ' ') }}</td>
            <td style="padding:4px 6px; text-align:right; font-weight:bold; border:2px solid #333; color:#111;">{{ number_format($b->solde, 2, ',', ' ') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#d9e8e0; font-weight:bold; border:2.5px solid #333;">
            <td colspan="3" style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">TOTAUX</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#2e7d32;">{{ number_format($totaux['debit'], 2, ',', ' ') }}</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#c62828;">{{ number_format($totaux['credit'], 2, ',', ' ') }}</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">{{ number_format($totaux['debit'] - $totaux['credit'], 2, ',', ' ') }}</td>
        </tr>
    </tfoot>
</table>

@endsection

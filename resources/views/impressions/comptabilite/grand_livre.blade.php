@extends('impressions._layout')

@section('titre', 'Grand Livre' . ($compte ? ' — ' . $compte->numero_compte . ' ' . $compte->libelle : ''))

@section('contenu')

<div style="background:#e8f5e9; border:1px solid #a5d6a7; padding:6px 12px; margin-bottom:10px; font-size:9px; color:#2e7d32; border-radius:3px;">
    <strong>Compte :</strong> {{ $numeroCompte }} — {{ $compte->libelle ?? '' }}
    &nbsp;&nbsp; <strong>Total débit :</strong> {{ number_format($resume['total_debit'], 2, ',', ' ') }}
    &nbsp;&nbsp; <strong>Total crédit :</strong> {{ number_format($resume['total_credit'], 2, ',', ' ') }}
    &nbsp;&nbsp; <strong>Solde :</strong> {{ number_format($resume['solde'], 2, ',', ' ') }}
</div>

@if($mouvements->isEmpty())
    <div style="text-align:center; padding:20px; color:#999;">Aucun mouvement pour ce compte.</div>
@else
<table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:5px 6px; width:14%; border:2.5px solid #333;">Date</th>
            <th style="padding:5px 6px; width:18%; border:2.5px solid #333;">Référence</th>
            <th style="padding:5px 6px; width:38%; border:2.5px solid #333;">Libellé</th>
            <th style="padding:5px 6px; width:8%; border:2.5px solid #333;">Devise</th>
            <th style="padding:5px 6px; width:11%; border:2.5px solid #333; text-align:right;">Débit</th>
            <th style="padding:5px 6px; width:11%; border:2.5px solid #333; text-align:right;">Crédit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mouvements as $i => $line)
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333; font-size:8.5px;">
            <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $line->journal?->date_ecriture?->format('d/m/Y H:i') }}</td>
            <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; font-size:7.8px; border:2px solid #333; color:#111;">{{ $line->journal?->reference_piece }}</td>
            <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $line->libelle_ligne }}</td>
            <td style="padding:4px 6px; text-align:center; border:2px solid #333; color:#111;">{{ $line->devise_code }}</td>
            <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#2e7d32;">{{ $line->debit > 0 ? number_format($line->debit, 2, ',', ' ') : '' }}</td>
            <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#c62828;">{{ $line->credit > 0 ? number_format($line->credit, 2, ',', ' ') : '' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#d9e8e0; font-weight:bold; border:2.5px solid #333;">
            <td colspan="4" style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">TOTAUX</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#2e7d32;">{{ number_format($resume['total_debit'], 2, ',', ' ') }}</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#c62828;">{{ number_format($resume['total_credit'], 2, ',', ' ') }}</td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

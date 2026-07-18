@extends('impressions._layout')

@section('titre', 'Journal Comptable')

@section('contenu')

@php
    $actifs = array_filter($filters ?? [], fn($v) => $v !== null && $v !== '');
    $libelles = ['date_debut' => 'Du', 'date_fin' => 'Au', 'type_piece' => 'Type', 'reference' => 'Référence'];
    $totalDebit = 0; $totalCredit = 0;
    foreach ($journaux as $j) { $totalDebit += $j->ecritures->sum('debit'); $totalCredit += $j->ecritures->sum('credit'); }
@endphp

<div style="background:#f0f4f8; border:1px solid #c8d4e0; padding:6px 12px; margin-bottom:10px; font-size:9px; color:#444; border-radius:3px;">
    <strong>Critères :</strong>
    @if(empty($actifs))
        Toutes les pièces comptables
    @else
        @foreach($actifs as $key => $val)
            <span style="background:#1a7a4a; color:#fff; padding:2px 6px; border-radius:10px; margin-right:4px;">{{ $libelles[$key] ?? $key }} : {{ $val }}</span>
        @endforeach
    @endif
    &nbsp;&nbsp; <strong>Total :</strong> {{ $journaux->count() }} pièce(s)
</div>

@if($journaux->isEmpty())
    <div style="text-align:center; padding:20px; color:#999;">Aucune pièce comptable pour ces critères.</div>
@else
<table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:5px 6px; width:12%; border:2.5px solid #333;">Date</th>
            <th style="padding:5px 6px; width:14%; border:2.5px solid #333;">Référence pièce</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333;">Type</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333;">Compte</th>
            <th style="padding:5px 6px; width:34%; border:2.5px solid #333;">Libellé</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Débit</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Crédit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($journaux as $i => $j)
            @foreach($j->ecritures as $k => $e)
            <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333; font-size:8.5px;">
                <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $k === 0 ? $j->date_ecriture?->format('d/m/Y H:i') : '' }}</td>
                <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; font-size:7.8px; border:2px solid #333; color:#111;">{{ $k === 0 ? $j->reference_piece : '' }}</td>
                <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $k === 0 ? $j->type_piece : '' }}</td>
                <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; border:2px solid #333; color:#111;">{{ $e->numero_compte }}</td>
                <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $e->libelle_ligne ?: $j->libelle }}</td>
                <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#2e7d32;">{{ $e->debit > 0 ? number_format($e->debit, 2, ',', ' ') : '' }}</td>
                <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#c62828;">{{ $e->credit > 0 ? number_format($e->credit, 2, ',', ' ') : '' }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#d9e8e0; font-weight:bold; border:2.5px solid #333;">
            <td colspan="5" style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">TOTAUX</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#2e7d32;">{{ number_format($totalDebit, 2, ',', ' ') }}</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#c62828;">{{ number_format($totalCredit, 2, ',', ' ') }}</td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

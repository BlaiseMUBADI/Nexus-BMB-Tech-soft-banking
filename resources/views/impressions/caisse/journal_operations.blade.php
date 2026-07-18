@extends('impressions._layout')

@section('titre', 'Journal de Caisse')

@php
    $typeLabels = [
        'DEPOT' => 'Dépôt', 'RETRAIT' => 'Retrait', 'CHANGE' => 'Change',
        'PAIEMENT' => 'Paiement', 'REMBOURSEMENT' => 'Remboursement',
        'DEPENSE' => 'Dépense', 'RECETTE' => 'Recette', 'VIREMENT' => 'Virement',
    ];
@endphp

@section('contenu')

<div style="background:#f0f4f8; border:1px solid #c8d4e0; padding:6px 12px; margin-bottom:10px; font-size:9px; color:#444; border-radius:3px;">
    <strong>Guichet :</strong> {{ $guichet->intitule ?? '-' }} ({{ $guichet->code_guichet ?? '-' }})
    &nbsp;&nbsp; <strong>Date :</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
    &nbsp;&nbsp; <strong>Type :</strong> {{ $type === 'TOUS' ? 'Tous types' : ($typeLabels[$type] ?? $type) }}
    &nbsp;&nbsp; <strong>Total :</strong> {{ $operations->count() }} opération(s)
</div>

<div style="background:#e8f5e9; border:1px solid #a5d6a7; padding:6px 12px; margin-bottom:10px; font-size:9px; color:#2e7d32; border-radius:3px;">
    <strong>Totaux par type :</strong>
    @foreach($totauxParType as $t => $totaux)
        <span style="background:#1a7a4a; color:#fff; padding:2px 6px; border-radius:10px; margin-right:4px;">
            {{ $typeLabels[$t] ?? $t }} : {{ $totaux['count'] }} op. — {{ number_format($totaux['montant'], 0, ',', ' ') }}
        </span>
    @endforeach
</div>

@if($operations->isEmpty())
    <div style="text-align:center; padding:20px; color:#999;">Aucune opération pour ces critères.</div>
@else
<table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:5px 6px; width:9%; border:2.5px solid #333;">Heure</th>
            <th style="padding:5px 6px; width:14%; border:2.5px solid #333;">Référence</th>
            <th style="padding:5px 6px; width:11%; border:2.5px solid #333;">Type</th>
            <th style="padding:5px 6px; width:13%; border:2.5px solid #333;">Compte</th>
            <th style="padding:5px 6px; width:20%; border:2.5px solid #333;">Client / Motif</th>
            <th style="padding:5px 6px; width:7%; border:2.5px solid #333;">Devise</th>
            <th style="padding:5px 6px; width:12%; border:2.5px solid #333; text-align:right;">Montant</th>
            <th style="padding:5px 6px; width:8%; border:2.5px solid #333;">Statut</th>
            <th style="padding:5px 6px; width:6%; border:2.5px solid #333;">Agent</th>
        </tr>
    </thead>
    <tbody>
        @foreach($operations as $i => $op)
        @php
            $client = $op->compte?->client;
            $clientNom = $client ? trim(strtoupper($client->nom ?? '') . ' ' . ucfirst(strtolower($client->prenom ?? ''))) : ($op->observations ?? '-');
        @endphp
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333; font-size:8.5px;">
            <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $op->date_operation?->format('H:i:s') }}</td>
            <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; font-size:7.8px; border:2px solid #333; color:#111;">{{ $op->reference }}</td>
            <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $typeLabels[$op->type] ?? $op->type }}</td>
            <td style="padding:4px 6px; font-size:7.8px; border:2px solid #333; color:#111;">{{ $op->compte_code ?: '-' }}</td>
            <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $clientNom }}</td>
            <td style="padding:4px 6px; text-align:center; border:2px solid #333; color:#111;">{{ $op->devise_code }}</td>
            <td style="padding:4px 6px; text-align:right; font-weight:bold; border:2px solid #333; color:#111;">{{ number_format($op->montant, 2, ',', ' ') }}</td>
            <td style="padding:4px 6px; text-align:center; border:2px solid #333;">
                <span style="background:{{ $op->statut === 'ANNULE' ? '#9e9e9e' : '#4caf50' }}; color:#fff; padding:1px 4px; border-radius:6px; font-size:7px;">{{ $op->statut }}</span>
            </td>
            <td style="padding:4px 6px; font-size:7.5px; border:2px solid #333; color:#111;">{{ $op->agent_matricule }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#d9e8e0; font-weight:bold; border:2.5px solid #333;">
            <td colspan="6" style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">TOTAL GÉNÉRAL</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">{{ number_format($operations->sum('montant'), 2, ',', ' ') }}</td>
            <td colspan="2" style="padding:6px 8px; border:2.5px solid #333;"></td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

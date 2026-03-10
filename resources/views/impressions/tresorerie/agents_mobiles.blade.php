@extends('impressions._layout')

@section('titre', 'Rapport Agents Mobiles')

@section('contenu')

<div style="text-align:center; margin-bottom:14px;">
    <div style="display:inline-block; background:#1e3a5f; color:#fff; padding:5px 28px;
                border-radius:4px; font-size:13px; font-weight:bold; letter-spacing:1px;">
        RAPPORT APPORTS AGENTS COMMERCIAUX — GUICHETS MOBILES
    </div>
    @if(!empty($filtres['date_debut']) || !empty($filtres['date_fin']))
    <div style="font-size:9px; color:#555; margin-top:4px;">
        @if(!empty($filtres['date_debut']))Période du <strong>{{ \Carbon\Carbon::parse($filtres['date_debut'])->isoFormat('D MMMM YYYY') }}</strong>@endif
        @if(!empty($filtres['date_fin'])) au <strong>{{ \Carbon\Carbon::parse($filtres['date_fin'])->isoFormat('D MMMM YYYY') }}</strong>@endif
    </div>
    @endif
    <div style="font-size:8px; color:#888; margin-top:2px;">
        Généré le {{ now()->isoFormat('D MMMM YYYY à HH:mm') }}
    </div>
</div>

{{-- ── Résumé global ── --}}
@php
    $totalEntrees = $transactions->whereIn('type', ['DEPOT','PAIEMENT'])->sum('montant');
    $totalSorties = $transactions->whereIn('type', ['RETRAIT','REMBOURSEMENT'])->sum('montant');
    $nbAgents     = $parAgent->count();
@endphp
<table style="width:100%; border-collapse:collapse; margin-bottom:14px; font-size:9px;">
    <tr style="background:#e8f0fe;">
        <td style="padding:6px 10px; border:1px solid #c7d2e9; font-weight:bold; text-align:center;">
            Agents<br><span style="font-size:13px; color:#1e3a5f;">{{ $nbAgents }}</span>
        </td>
        <td style="padding:6px 10px; border:1px solid #c7d2e9; font-weight:bold; text-align:center;">
            Total opérations<br><span style="font-size:13px; color:#333;">{{ $transactions->count() }}</span>
        </td>
        <td style="padding:6px 10px; border:1px solid #c7d2e9; font-weight:bold; text-align:center; color:#065f46;">
            Total entrées<br><span style="font-size:12px;">{{ number_format($totalEntrees, 2, ',', ' ') }}</span>
        </td>
        <td style="padding:6px 10px; border:1px solid #c7d2e9; font-weight:bold; text-align:center; color:#991b1b;">
            Total sorties<br><span style="font-size:12px;">{{ number_format($totalSorties, 2, ',', ' ') }}</span>
        </td>
    </tr>
</table>

{{-- ── Récapitulatif par agent ── --}}
<div style="font-size:10px; font-weight:bold; color:#1e3a5f; margin-bottom:4px; border-bottom:2px solid #1e3a5f; padding-bottom:2px;">
    RÉCAPITULATIF PAR AGENT
</div>
<table style="width:100%; border-collapse:collapse; font-size:8.5px; margin-bottom:16px;">
    <thead>
        <tr style="background:#1e3a5f; color:#fff; text-transform:uppercase; font-size:7.5px;">
            <th style="padding:5px 7px; text-align:left; border:1px solid #2d4f7a;">Agent</th>
            <th style="padding:5px 7px; text-align:left; border:1px solid #2d4f7a;">Matricule</th>
            <th style="padding:5px 7px; text-align:left; border:1px solid #2d4f7a;">Guichet</th>
            <th style="padding:5px 7px; text-align:center; border:1px solid #2d4f7a;">Nb opér.</th>
            <th style="padding:5px 7px; text-align:center; border:1px solid #2d4f7a;">Devise</th>
            <th style="padding:5px 7px; text-align:right; border:1px solid #2d4f7a;">Entrées</th>
            <th style="padding:5px 7px; text-align:right; border:1px solid #2d4f7a;">Sorties</th>
            <th style="padding:5px 7px; text-align:right; border:1px solid #2d4f7a;">Net</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parAgent as $idx => $ag)
            @foreach($ag['par_devise'] as $i => $d)
            <tr style="background:{{ $idx % 2 === 0 ? '#f9fafb' : '#fff' }};">
                @if($i === 0)
                <td style="padding:4px 7px; border:1px solid #e5e7eb; font-weight:bold;" rowspan="{{ count($ag['par_devise']) }}">
                    {{ $ag['nom_complet'] }}
                </td>
                <td style="padding:4px 7px; border:1px solid #e5e7eb; font-family:monospace;" rowspan="{{ count($ag['par_devise']) }}">
                    {{ $ag['matricule'] }}
                </td>
                <td style="padding:4px 7px; border:1px solid #e5e7eb;" rowspan="{{ count($ag['par_devise']) }}">
                    {{ $ag['guichet'] }}
                </td>
                <td style="padding:4px 7px; border:1px solid #e5e7eb; text-align:center;" rowspan="{{ count($ag['par_devise']) }}">
                    {{ $ag['nb_operations'] }}
                </td>
                @endif
                <td style="padding:4px 7px; border:1px solid #e5e7eb; text-align:center; font-weight:bold;">{{ $d['devise'] }}</td>
                <td style="padding:4px 7px; border:1px solid #e5e7eb; text-align:right; color:#065f46;">{{ number_format($d['total_entrees'], 2, ',', ' ') }}</td>
                <td style="padding:4px 7px; border:1px solid #e5e7eb; text-align:right; color:#991b1b;">{{ number_format($d['total_sorties'], 2, ',', ' ') }}</td>
                @php $net = $d['total_entrees'] - $d['total_sorties']; @endphp
                <td style="padding:4px 7px; border:1px solid #e5e7eb; text-align:right; font-weight:bold; color:{{ $net >= 0 ? '#065f46' : '#991b1b' }};">
                    {{ number_format($net, 2, ',', ' ') }}
                </td>
            </tr>
            @endforeach
        @endforeach
        @if($parAgent->isEmpty())
        <tr>
            <td colspan="8" style="text-align:center; padding:12px; color:#888; font-style:italic;">
                Aucun agent mobile trouvé pour les critères sélectionnés.
            </td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ── Détail des opérations ── --}}
<div style="font-size:10px; font-weight:bold; color:#1e3a5f; margin-bottom:4px; border-bottom:2px solid #1e3a5f; padding-bottom:2px;">
    DÉTAIL DES OPÉRATIONS ({{ $transactions->count() }})
</div>
<table style="width:100%; border-collapse:collapse; font-size:7.8px;">
    <thead>
        <tr style="background:#1e3a5f; color:#fff; text-transform:uppercase; font-size:7px;">
            <th style="padding:4px 6px; text-align:left; border:1px solid #2d4f7a;">Date / Heure</th>
            <th style="padding:4px 6px; text-align:left; border:1px solid #2d4f7a;">Référence</th>
            <th style="padding:4px 6px; text-align:left; border:1px solid #2d4f7a;">Agent</th>
            <th style="padding:4px 6px; text-align:left; border:1px solid #2d4f7a;">Guichet</th>
            <th style="padding:4px 6px; text-align:left; border:1px solid #2d4f7a;">Type</th>
            <th style="padding:4px 6px; text-align:left; border:1px solid #2d4f7a;">Compte</th>
            <th style="padding:4px 6px; text-align:left; border:1px solid #2d4f7a;">Client</th>
            <th style="padding:4px 6px; text-align:right; border:1px solid #2d4f7a;">Montant</th>
            <th style="padding:4px 6px; text-align:center; border:1px solid #2d4f7a;">Devise</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $i => $t)
        <tr style="background:{{ $i % 2 === 0 ? '#f9fafb' : '#fff' }};">
            <td style="padding:3px 6px; border:1px solid #e5e7eb; white-space:nowrap;">
                {{ \Carbon\Carbon::parse($t->date_operation)->format('d/m/Y H:i') }}
            </td>
            <td style="padding:3px 6px; border:1px solid #e5e7eb; font-family:monospace; font-size:7px;">{{ $t->reference ?? '—' }}</td>
            <td style="padding:3px 6px; border:1px solid #e5e7eb; font-size:7px;">{{ $t->agent_matricule ?? '—' }}</td>
            <td style="padding:3px 6px; border:1px solid #e5e7eb;">{{ $t->guichet?->intitule ?? '—' }}</td>
            <td style="padding:3px 6px; border:1px solid #e5e7eb; font-weight:bold; color:{{ in_array($t->type, ['DEPOT','PAIEMENT']) ? '#065f46' : '#991b1b' }};">
                {{ $t->type }}
            </td>
            <td style="padding:3px 6px; border:1px solid #e5e7eb; font-family:monospace; font-size:7px;">{{ $t->compte_code ?? '—' }}</td>
            <td style="padding:3px 6px; border:1px solid #e5e7eb;">
                @if($t->compte && $t->compte->client)
                    {{ $t->compte->client->nom }} {{ $t->compte->client->prenom ?? '' }}
                @else —
                @endif
            </td>
            <td style="padding:3px 6px; border:1px solid #e5e7eb; text-align:right; font-weight:bold; color:{{ in_array($t->type, ['DEPOT','PAIEMENT']) ? '#065f46' : '#991b1b' }};">
                {{ number_format($t->montant, 2, ',', ' ') }}
            </td>
            <td style="padding:3px 6px; border:1px solid #e5e7eb; text-align:center;">{{ $t->devise_code }}</td>
        </tr>
        @endforeach
        @if($transactions->isEmpty())
        <tr>
            <td colspan="9" style="text-align:center; padding:14px; color:#888; font-style:italic;">
                Aucune opération pour les critères sélectionnés.
            </td>
        </tr>
        @endif
    </tbody>
</table>

@endsection

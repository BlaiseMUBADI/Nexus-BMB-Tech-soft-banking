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
    $nbAgents = $parAgent->count();
    $totauxParDevise = $transactions->groupBy('devise_code')->map(function ($items, $devise) {
        $totalEntrees = (float) $items->whereIn('type', ['DEPOT', 'PAIEMENT'])->sum('montant');
        $totalSorties = (float) $items->whereIn('type', ['RETRAIT', 'REMBOURSEMENT'])->sum('montant');

        return [
            'devise' => $devise ?: '—',
            'nb_operations' => $items->count(),
            'total_entrees' => $totalEntrees,
            'total_sorties' => $totalSorties,
            'net' => $totalEntrees - $totalSorties,
        ];
    })->sortBy('devise')->values();
@endphp
<table style="width:100%; border-collapse:collapse; margin-bottom:14px; font-size:9px; border:2px solid #333;">
    <tr style="background:#d9e8e0; border:2px solid #333;">
        <td style="padding:6px 10px; border:2px solid #333; font-weight:bold; text-align:center; color:#111;">
            Agents<br><span style="font-size:13px; color:#1a7a4a;">{{ $nbAgents }}</span>
        </td>
        <td style="padding:6px 10px; border:2px solid #333; font-weight:bold; text-align:center; color:#111;">
            Total opérations<br><span style="font-size:13px; color:#111;">{{ $transactions->count() }}</span>
        </td>
        <td style="padding:6px 10px; border:2px solid #333; font-weight:bold; text-align:center; color:#065f46;">
            Total entrées par devise
            <div style="font-size:10px; line-height:1.45; margin-top:4px;">
                @forelse($totauxParDevise as $resume)
                    <div><strong>{{ $resume['devise'] }}</strong> : {{ number_format($resume['total_entrees'], 2, ',', ' ') }}</div>
                @empty
                    <span style="font-size:12px;">0,00</span>
                @endforelse
            </div>
        </td>
        <td style="padding:6px 10px; border:2px solid #333; font-weight:bold; text-align:center; color:#991b1b;">
            Total sorties par devise
            <div style="font-size:10px; line-height:1.45; margin-top:4px;">
                @forelse($totauxParDevise as $resume)
                    <div><strong>{{ $resume['devise'] }}</strong> : {{ number_format($resume['total_sorties'], 2, ',', ' ') }}</div>
                @empty
                    <span style="font-size:12px;">0,00</span>
                @endforelse
            </div>
        </td>
    </tr>
</table>

@if($totauxParDevise->isNotEmpty())
<div style="font-size:10px; font-weight:bold; color:#1e3a5f; margin-bottom:4px; border-bottom:2px solid #1e3a5f; padding-bottom:2px;">
    SYNTHÈSE GLOBALE PAR DEVISE
</div>
<table style="width:100%; border-collapse:collapse; font-size:8.5px; margin-bottom:16px; border:2px solid #333;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; text-transform:uppercase; font-size:7.5px; border:2.5px solid #333;">
            <th style="padding:6px 8px; text-align:center; border:2.5px solid #333;">Devise</th>
            <th style="padding:6px 8px; text-align:center; border:2.5px solid #333;">Nb opér.</th>
            <th style="padding:6px 8px; text-align:right; border:2.5px solid #333;">Entrées</th>
            <th style="padding:6px 8px; text-align:right; border:2.5px solid #333;">Sorties</th>
            <th style="padding:6px 8px; text-align:right; border:2.5px solid #333;">Net</th>
        </tr>
    </thead>
    <tbody>
        @foreach($totauxParDevise as $i => $resume)
        <tr style="background:{{ $i % 2 === 0 ? '#fff' : '#f9f9f9' }}; border:2px solid #333;">
            <td style="padding:5px 8px; border:2px solid #333; text-align:center; font-weight:bold; color:#111;">{{ $resume['devise'] }}</td>
            <td style="padding:5px 8px; border:2px solid #333; text-align:center; color:#111;">{{ $resume['nb_operations'] }}</td>
            <td style="padding:5px 8px; border:2px solid #333; text-align:right; color:#065f46;">{{ number_format($resume['total_entrees'], 2, ',', ' ') }}</td>
            <td style="padding:5px 8px; border:2px solid #333; text-align:right; color:#991b1b;">{{ number_format($resume['total_sorties'], 2, ',', ' ') }}</td>
            <td style="padding:5px 8px; border:2px solid #333; text-align:right; font-weight:bold; color:{{ $resume['net'] >= 0 ? '#065f46' : '#991b1b' }};">{{ number_format($resume['net'], 2, ',', ' ') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── Récapitulatif par agent ── --}}
<div style="font-size:10px; font-weight:bold; color:#1e3a5f; margin-bottom:4px; border-bottom:2px solid #1e3a5f; padding-bottom:2px;">
    RÉCAPITULATIF PAR AGENT
</div>
<table style="width:100%; border-collapse:collapse; font-size:8.5px; margin-bottom:16px; border:2px solid #333;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; text-transform:uppercase; font-size:7.5px; border:2.5px solid #333;">
            <th style="padding:6px 8px; text-align:left; border:2.5px solid #333;">Agent</th>
            <th style="padding:6px 8px; text-align:left; border:2.5px solid #333;">Matricule</th>
            <th style="padding:6px 8px; text-align:left; border:2.5px solid #333;">Guichet</th>
            <th style="padding:6px 8px; text-align:center; border:2.5px solid #333;">Nb opér.</th>
            <th style="padding:6px 8px; text-align:center; border:2.5px solid #333;">Devise</th>
            <th style="padding:6px 8px; text-align:right; border:2.5px solid #333;">Entrées</th>
            <th style="padding:6px 8px; text-align:right; border:2.5px solid #333;">Sorties</th>
            <th style="padding:6px 8px; text-align:right; border:2.5px solid #333;">Net</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parAgent as $idx => $ag)
            @foreach($ag['par_devise'] as $i => $d)
            <tr style="background:{{ $idx % 2 === 0 ? '#fff' : '#f9f9f9' }}; border:2px solid #333;">
                @if($i === 0)
                <td style="padding:5px 8px; border:2px solid #333; font-weight:bold; color:#111;" rowspan="{{ count($ag['par_devise']) }}">
                    {{ $ag['nom_complet'] }}
                </td>
                <td style="padding:5px 8px; border:2px solid #333; font-family:monospace; color:#111;" rowspan="{{ count($ag['par_devise']) }}">
                    {{ $ag['matricule'] }}
                </td>
                <td style="padding:5px 8px; border:2px solid #333; color:#111;" rowspan="{{ count($ag['par_devise']) }}">
                    {{ $ag['guichet'] }}
                </td>
                <td style="padding:5px 8px; border:2px solid #333; text-align:center; color:#111;" rowspan="{{ count($ag['par_devise']) }}">
                    {{ $ag['nb_operations'] }}
                </td>
                @endif
                <td style="padding:5px 8px; border:2px solid #333; text-align:center; font-weight:bold; color:#111;">{{ $d['devise'] }}</td>
                <td style="padding:5px 8px; border:2px solid #333; text-align:right; color:#065f46;">{{ number_format($d['total_entrees'], 2, ',', ' ') }}</td>
                <td style="padding:5px 8px; border:2px solid #333; text-align:right; color:#991b1b;">{{ number_format($d['total_sorties'], 2, ',', ' ') }}</td>
                @php $net = $d['total_entrees'] - $d['total_sorties']; @endphp
                <td style="padding:5px 8px; border:2px solid #333; text-align:right; font-weight:bold; color:{{ $net >= 0 ? '#065f46' : '#991b1b' }};">
                    {{ number_format($net, 2, ',', ' ') }}
                </td>
            </tr>
            @endforeach
        @endforeach
        @if($parAgent->isEmpty())
        <tr>
            <td colspan="8" style="text-align:center; padding:12px; color:#888; font-style:italic; border:2px solid #333;">
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
@php
    $transactionsParDevise = $transactions
        ->groupBy(fn($t) => $t->devise_code ?: 'N/A')
        ->sortKeys();
@endphp

@if($transactionsParDevise->isEmpty())
    <div style="text-align:center; padding:14px; color:#888; font-style:italic; border:2px solid #333;">
        Aucune opération pour les critères sélectionnés.
    </div>
@else
    @foreach($transactionsParDevise as $devise => $ops)
    <div style="font-size:9px; font-weight:bold; color:#1e3a5f; margin:8px 0 4px 0; background:#eef4fb; border:2px solid #333; padding:4px 6px;">
        DEVISE {{ $devise }} ({{ $ops->count() }} opération(s))
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:7.8px; margin-bottom:6px;">
        <thead>
            <tr style="background:#1e3a5f; color:#fff; text-transform:uppercase; font-size:7px;">
                <th style="padding:4px 6px; text-align:left; border:2px solid #333;">Date / Heure</th>
                <th style="padding:4px 6px; text-align:left; border:2px solid #333;">Référence</th>
                <th style="padding:4px 6px; text-align:left; border:2px solid #333;">Agent</th>
                <th style="padding:4px 6px; text-align:left; border:2px solid #333;">Guichet</th>
                <th style="padding:4px 6px; text-align:left; border:2px solid #333;">Type</th>
                <th style="padding:4px 6px; text-align:left; border:2px solid #333;">Compte</th>
                <th style="padding:4px 6px; text-align:left; border:2px solid #333;">Client</th>
                <th style="padding:4px 6px; text-align:right; border:2px solid #333;">Montant ({{ $devise }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ops as $i => $t)
            <tr style="background:{{ $i % 2 === 0 ? '#f9fafb' : '#fff' }};">
                <td style="padding:3px 6px; border:2px solid #333; white-space:nowrap; color:#111;">
                    {{ \Carbon\Carbon::parse($t->date_operation)->format('d/m/Y H:i') }}
                </td>
                <td style="padding:3px 6px; border:2px solid #333; font-family:monospace; font-size:7px; color:#111;">{{ $t->reference ?? '—' }}</td>
                <td style="padding:3px 6px; border:2px solid #333; font-size:7px; color:#111;">{{ $t->agent_matricule ?? '—' }}</td>
                <td style="padding:3px 6px; border:2px solid #333; color:#111;">{{ $t->guichet?->intitule ?? '—' }}</td>
                <td style="padding:3px 6px; border:2px solid #333; font-weight:bold; color:{{ in_array($t->type, ['DEPOT','PAIEMENT']) ? '#065f46' : '#991b1b' }};">
                    {{ $t->type }}
                </td>
                <td style="padding:3px 6px; border:2px solid #333; font-family:monospace; font-size:7px; color:#111;">{{ $t->compte_code ?? '—' }}</td>
                <td style="padding:3px 6px; border:2px solid #333; color:#111;">
                    @if($t->compte && $t->compte->client)
                        {{ $t->compte->client->full_name }}
                    @else —
                    @endif
                </td>
                <td style="padding:3px 6px; border:2px solid #333; text-align:right; font-weight:bold; color:{{ in_array($t->type, ['DEPOT','PAIEMENT']) ? '#065f46' : '#991b1b' }};">
                    {{ number_format($t->montant, 2, ',', ' ') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
@endif

@endsection

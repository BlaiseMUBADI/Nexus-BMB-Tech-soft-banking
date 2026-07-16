@extends('impressions._layout')

@section('titre', 'Liste des Dossiers Crédit')

@section('contenu')

@php
    $libelles = [
        'statut_global'      => 'Statut',
        'type_credit'        => 'Type',
        'devise'             => 'Devise',
        'code_zone'          => 'Zone',
        'portefeuille_id'    => 'Portefeuille',
        'agent_analyse_matricule' => 'Agent analyse',
        'agent_createur_matricule' => 'Agent créateur',
        'date_debut'         => 'Du',
        'date_fin'           => 'Au',
    ];
    $actifs = array_filter($filtres ?? [], fn($v) => $v !== null && $v !== '' && $v !== 'tous');

    $totauxParDevise = [];
    foreach ($dossiers->groupBy('devise') as $devise => $group) {
        $totauxParDevise[$devise] = [
            'count'             => $group->count(),
            'montant_demande'   => $group->sum('montant_demande'),
            'montant_approuve'  => $group->sum('montant_approuve'),
            'montant_net_verse' => $group->sum(function($d) { return $d->deblocage?->montant_net_verse ?? 0; }),
            'en_retard'         => $group->where('statut_global', 'EN_RETARD')->count(),
        ];
    }
@endphp

{{-- Résumé des filtres actifs --}}
<div style="background:#f0f4f8; border:1px solid #c8d4e0; padding:6px 12px; margin-bottom:10px;
            font-size:9px; color:#444; border-radius:3px;">
    <strong>Critères :</strong>
    @if(empty($actifs))
        Tous les dossiers
    @else
        @foreach($actifs as $key => $val)
            <span style="background:#1a7a4a; color:#fff; padding:2px 6px; border-radius:10px; margin-right:4px;">
                {{ $libelles[$key] ?? $key }} :
                @if($key === 'statut_global')
                    @php
                        $statutLabels = [
                            'BROUILLON' => 'Brouillon', 'SOUMIS' => 'Soumis', 'EN_ANALYSE' => 'En analyse',
                            'EN_VALIDATION' => 'En validation', 'PRET_A_DEBLOQUER' => 'Prêt à débloquer',
                            'DEBLOQUE' => 'Débloqué', 'EN_REMBOURSEMENT' => 'En remboursement',
                            'EN_RETARD' => 'En retard', 'SOLDE' => 'Soldé', 'ANNULE' => 'Annulé',
                            'SUSPENDU' => 'Suspendu', 'SUSPECT' => 'Suspect',
                        ];
                    @endphp
                    {{ $statutLabels[$val] ?? $val }}
                @elseif($key === 'code_zone')
                    {{ $zone->nom ?? $val }}
                @elseif($key === 'portefeuille_id')
                    {{ $portefeuille->nom_portefeuille ?? $val }}
                @elseif($key === 'agent_analyse_matricule')
                    {{ $agentAnalyse->full_name ?? $val }}
                @elseif($key === 'agent_createur_matricule')
                    {{ $agentCreateur->full_name ?? $val }}
                @elseif(in_array($key, ['date_debut','date_fin']))
                    {{ \Carbon\Carbon::parse($val)->format('d/m/Y') }}
                @else
                    {{ $val }}
                @endif
            </span>
        @endforeach
    @endif
    &nbsp;&nbsp; <strong>Total :</strong> {{ $dossiers->count() }} dossier(s)
</div>

{{-- Totaux par devise --}}
@if(!empty($totauxParDevise))
<div style="background:#e8f5e9; border:1px solid #a5d6a7; padding:6px 12px; margin-bottom:10px;
            font-size:9px; color:#2e7d32; border-radius:3px;">
    <strong>Totaux par devise :</strong>
    @foreach($totauxParDevise as $devise => $totaux)
        @php
            $symbole = match($devise) { 'USD' => '$', 'EUR' => '€', default => 'Fc' };
        @endphp
        <span style="background:#1a7a4a; color:#fff; padding:2px 6px; border-radius:10px; margin-right:4px;">
            {{ $devise }}: {{ number_format($totaux['montant_demande'], 0, ',', ' ') }}{{ $symbole }}
            ({{ $totaux['count'] }} dossiers)
        </span>
    @endforeach
</div>
@endif

{{-- Tableau --}}
@if($dossiers->isEmpty())
    <div style="text-align:center; padding:20px; color:#999;">Aucun dossier ne correspond aux critères sélectionnés.</div>
@else
<table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:5px 6px; width:4%; border:2.5px solid #333; text-align:center;">#</th>
            <th style="padding:5px 6px; width:12%; border:2.5px solid #333;">N° Dossier</th>
            <th style="padding:5px 6px; width:18%; border:2.5px solid #333;">Client</th>
            <th style="padding:5px 6px; width:8%; border:2.5px solid #333;">Devise</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Demandé</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Approuvé</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Décaissé</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333; text-align:right;">Remboursé</th>
            <th style="padding:5px 6px; width:10%; border:2.5px solid #333;">Statut</th>
            <th style="padding:5px 6px; width:8%; border:2.5px solid #333;">Créé le</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dossiers as $i => $dossier)
        @php
            $statutColors = [
                'BROUILLON' => '#9e9e9e', 'SOUMIS' => '#2196f3', 'EN_ANALYSE' => '#ff9800',
                'EN_VALIDATION' => '#ff9800', 'PRET_A_DEBLOQUER' => '#4caf50',
                'DEBLOQUE' => '#1a7a4a', 'EN_REMBOURSEMENT' => '#1a7a4a',
                'EN_RETARD' => '#f44336', 'SOLDE' => '#4caf50', 'ANNULE' => '#9e9e9e',
                'SUSPENDU' => '#ff5722', 'SUSPECT' => '#f44336',
            ];
            $statutColor = $statutColors[$dossier->statut_global] ?? '#666';
            $montantRembourse = $dossier->remboursements?->sum('montant_recu') ?? 0;
        @endphp
        <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333; font-size:8.5px;">
            <td style="padding:4px 6px; line-height:1.15; text-align:center; vertical-align:middle; border:2px solid #333; color:#111;">{{ $i + 1 }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; font-family:DejaVu Sans Mono,monospace; font-size:8.5px; border:2px solid #333; color:#111;">{{ $dossier->numero_dossier }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333; color:#111;">
                {{ strtoupper($dossier->client->nom ?? '') }}
                {{ strtoupper($dossier->client->postnom ?? '') }}
                {{ ucfirst(strtolower($dossier->client->prenom ?? '')) }}
            </td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:center; border:2px solid #333; color:#111;">{{ $dossier->devise }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#111;">{{ number_format($dossier->montant_demande, 0, ',', ' ') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#111;">{{ number_format($dossier->montant_approuve ?? 0, 0, ',', ' ') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#111;">{{ number_format($dossier->deblocage?->montant_net_verse ?? 0, 0, ',', ' ') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; text-align:right; border:2px solid #333; color:#111;">{{ number_format($montantRembourse, 0, ',', ' ') }}</td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; border:2px solid #333;">
                <span style="background:{{ $statutColor }}; color:#fff; padding:2px 5px; border-radius:8px; font-size:7.5px; font-weight:bold; text-transform:uppercase;">
                    {{ $dossier->statut_global }}
                </span>
            </td>
            <td style="padding:4px 6px; line-height:1.15; vertical-align:middle; white-space:nowrap; border:2px solid #333; color:#111;">{{ \Carbon\Carbon::parse($dossier->created_at)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#d9e8e0; font-weight:bold; border:2.5px solid #333;">
            <td colspan="4" style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">TOTAL</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">{{ number_format($dossiers->sum('montant_demande'), 0, ',', ' ') }}</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">{{ number_format($dossiers->sum('montant_approuve'), 0, ',', ' ') }}</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">{{ number_format($dossiers->sum(function($d) { return $d->deblocage?->montant_net_verse ?? 0; }), 0, ',', ' ') }}</td>
            <td style="padding:6px 8px; text-align:right; border:2.5px solid #333; color:#111;">{{ number_format($dossiers->sum(function($d) { return $d->remboursements?->sum('montant_recu') ?? 0; }), 0, ',', ' ') }}</td>
            <td colspan="2" style="padding:6px 8px; border:2.5px solid #333;"></td>
        </tr>
    </tfoot>
</table>
@endif

@endsection

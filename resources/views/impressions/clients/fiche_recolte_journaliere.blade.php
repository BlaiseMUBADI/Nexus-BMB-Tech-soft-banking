@extends('impressions._layout')

@section('titre', 'Fiche de Récolte Journalière')

@section('contenu')
@php
    $zoneLabel = $zone?->nom ? strtoupper($zone->nom) : 'TOUTES LES ZONES';
    $rows = $clients->values();
    $minRows = 5;
    $totalRows = max($rows->count(), $minRows);

    $nomCompletClient = function ($client) {
        return trim(
            strtoupper((string) ($client->nom ?? '')) . ' ' .
            strtoupper((string) ($client->postnom ?? '')) . ' ' .
            strtoupper((string) ($client->prenom ?? ''))
        );
    };

    $matriculeClient = function ($client) {
        return (string) ($client->matricule ?? '');
    };
@endphp

<div class="section">
    <div class="section-title">Informations de collecte</div>
    <table class="info-table" style="border:2px solid #333; border-collapse:collapse;">
        <tr style="border:2px solid #333;">
            <td class="label" style="width:25%; border:2px solid #333;">Zone</td>
            <td style="width:30%; border:2px solid #333;"><strong style="color:#111; font-size:11px;">{{ $zoneLabel }}</strong></td>
            <td class="label" style="width:20%; border:2px solid #333;">Date de récolte</td>
            <td style="width:25%; border:2px solid #333;"><strong style="color:#111; font-size:11px;">{{ $dateRecolte }}</strong></td>
        </tr>
        <tr style="border:2px solid #333;">
            <td class="label" style="border:2px solid #333;">Agent commercial</td>
            <td style="border:2px solid #333;"><strong style="color:#111; font-size:11px;">{{ $agentCommercialNom }}</strong></td>
            <td class="label" style="border:2px solid #333;">Total clients ciblés</td>
            <td style="border:2px solid #333;"><strong style="color:#111; font-size:11px;">{{ $rows->count() }}</strong></td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Détail journalier de récolte</div>
    <table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
            <th style="padding:3px 5px; width:5%; text-align:center; border:2.5px solid #333;">N°</th>
            <th style="padding:3px 5px; width:20%; border:2.5px solid #333;">Matricule client</th>
            <th style="padding:3px 5px; width:38%; border:2.5px solid #333;">Noms</th>
            <th style="padding:3px 5px; width:11%; text-align:center; border:2.5px solid #333;">EAC</th>
            <th style="padding:3px 5px; width:11%; text-align:center; border:2.5px solid #333;">RMB</th>
            <th style="padding:3px 5px; width:15%; text-align:center; border:2.5px solid #333;">Signature</th>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < $totalRows; $i++)
            @php($client = $rows->get($i))
            <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:1.5px solid #333;">
                <td style="padding:3px 5px; font-size:8.5px; line-height:1.1; text-align:center; border:1.5px solid #333; color:#888;">{{ $i + 1 }}</td>
                <td style="padding:3px 5px; font-family:DejaVu Sans Mono, monospace; font-size:8.5px; line-height:1.1; border:1.5px solid #333; color:#111;">
                    {{ $client ? $matriculeClient($client) : '' }}
                </td>
                <td style="padding:3px 5px; font-size:8.5px; line-height:1.1; border:1.5px solid #333; color:#111;">
                    {{ $client ? $nomCompletClient($client) : '' }}
                </td>
                <td style="padding:3px 5px; font-size:8.5px; line-height:1.1; border:1.5px solid #333; color:#111;">&nbsp;</td>
                <td style="padding:3px 5px; font-size:8.5px; line-height:1.1; border:1.5px solid #333; color:#111;">&nbsp;</td>
                <td style="padding:3px 5px; font-size:8.5px; line-height:1.1; border:1.5px solid #333; color:#111;">&nbsp;</td>
            </tr>
        @endfor
        <tr style="background:#d9e8e0; font-weight:700; border:2.5px solid #333;">
            <td colspan="3" style="padding:4px 5px; text-align:right; border:2.5px solid #333; color:#111;">TOTAL RECOLTE DU JOUR</td>
            <td style="padding:4px 5px; text-align:center; border:2.5px solid #333; color:#111;">&nbsp;</td>
            <td style="padding:4px 5px; text-align:center; border:2.5px solid #333; color:#111;">&nbsp;</td>
            <td style="padding:4px 5px; text-align:center; border:2.5px solid #333; color:#111;">&nbsp;</td>
        </tr>
    </tbody>
    </table>
</div>

<div class="section" style="margin-top:10px;">
    <table class="info-table" style="font-size:9.5px; border:2px solid #333; border-collapse:collapse;">
        <tr style="border:2px solid #333;">
            <td class="label" style="width:30%; border:2px solid #333;">Total EAC</td>
            <td style="width:20%; border:2px solid #333; color:#111; font-size:10.5px; line-height:1.35; padding:8px 6px;">&nbsp;</td>
            <td class="label" style="width:30%; border:2px solid #333;">Total RMB</td>
            <td style="width:20%; border:2px solid #333; color:#111; font-size:10.5px; line-height:1.35; padding:8px 6px;">&nbsp;</td>
        </tr>
        <tr style="border:2px solid #333;">
            <td class="label" style="border:2px solid #333;">Total global recolte</td>
            <td colspan="3" style="border:2px solid #333; color:#111; font-size:10.5px; line-height:1.35; padding:8px 6px;">&nbsp;</td>
        </tr>
    </table>
</div>

<div style="margin-top:14px; font-size:9px; color:#555;">
    <strong>Observation :</strong> Feuille de collecte journalière à compléter par l'agent commercial,
    puis à valider et archiver au guichet.
</div>

<div style="margin-top:28px; display:table; width:100%;">
    <div style="display:table-cell; text-align:center; width:33%;">
        <div style="border-top:1px solid #999; width:140px; margin:0 auto; padding-top:4px; font-size:9px; color:#666;">
            Agent commercial
        </div>
    </div>
    <div style="display:table-cell; text-align:center; width:34%;">
        <div style="border-top:1px solid #999; width:140px; margin:0 auto; padding-top:4px; font-size:9px; color:#666;">
            Caissier(ère)
        </div>
    </div>
    <div style="display:table-cell; text-align:center; width:33%;">
        <div style="border-top:1px solid #999; width:140px; margin:0 auto; padding-top:4px; font-size:9px; color:#666;">
            Cachet &amp; Visa COOPEC EBEN
        </div>
    </div>
</div>
@endsection

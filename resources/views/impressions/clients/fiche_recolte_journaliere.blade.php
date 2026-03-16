@extends('impressions._layout')

@section('titre', 'Fiche de Récolte Journalière')

@section('contenu')
@php
    $zoneLabel = $zone?->nom ? strtoupper($zone->nom) : 'TOUTES LES ZONES';
    $rows = $clients->values();
    $minRows = 20;
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
    <table class="info-table">
        <tr>
            <td class="label" style="width:25%;">Zone</td>
            <td style="width:30%;"><strong>{{ $zoneLabel }}</strong></td>
            <td class="label" style="width:20%;">Date de récolte</td>
            <td style="width:25%;"><strong>{{ $dateRecolte }}</strong></td>
        </tr>
        <tr>
            <td class="label">Agent commercial</td>
            <td><strong>{{ $agentCommercialNom }}</strong></td>
            <td class="label">Total clients ciblés</td>
            <td><strong>{{ $rows->count() }}</strong></td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Détail journalier de récolte</div>
    <table class="info-table" style="font-size:9.5px;">
    <thead>
        <tr>
            <th style="padding:5px 6px; width:23%;">Matricule client</th>
            <th style="padding:5px 6px; width:43%;">Noms</th>
            <th style="padding:5px 6px; width:11%; text-align:center;">EAC</th>
            <th style="padding:5px 6px; width:11%; text-align:center;">RMB</th>
            <th style="padding:5px 6px; width:12%; text-align:center;">Signature</th>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < $totalRows; $i++)
            @php($client = $rows->get($i))
            <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f7f9fc;' }}">
                <td style="padding:5px 6px; font-family:DejaVu Sans Mono, monospace; font-size:9px;">
                    {{ $client ? $matriculeClient($client) : '' }}
                </td>
                <td style="padding:5px 6px;">
                    {{ $client ? $nomCompletClient($client) : '' }}
                </td>
                <td style="padding:5px 6px;">&nbsp;</td>
                <td style="padding:5px 6px;">&nbsp;</td>
                <td style="padding:5px 6px;">&nbsp;</td>
            </tr>
        @endfor
        <tr style="background:#eef3fa; font-weight:700;">
            <td colspan="2" style="padding:6px; text-align:right;">TOTAL RECOLTE DU JOUR</td>
            <td style="padding:6px; text-align:center;">&nbsp;</td>
            <td style="padding:6px; text-align:center;">&nbsp;</td>
            <td style="padding:6px; text-align:center;">&nbsp;</td>
        </tr>
    </tbody>
    </table>
</div>

<div class="section" style="margin-top:10px;">
    <table class="info-table" style="font-size:9.5px;">
        <tr>
            <td class="label" style="width:30%;">Total EAC</td>
            <td style="width:20%;">&nbsp;</td>
            <td class="label" style="width:30%;">Total RMB</td>
            <td style="width:20%;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label">Total global recolte</td>
            <td colspan="3">&nbsp;</td>
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

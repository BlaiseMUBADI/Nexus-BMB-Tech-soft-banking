@extends('impressions._layout')

@section('titre', 'Echeancier - ' . $demande->numero_dossier)

@section('contenu')

<div class="doc-title">Echeancier de remboursement</div>

<div class="section">
    <div class="section-title">Informations du dossier</div>
    <table class="info-table">
        <tr>
            <td class="label">Numero dossier</td>
            <td>{{ $demande->numero_dossier }}</td>
            <td class="label">Date edition</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Client</td>
            <td>
                {{ optional($demande->client)->nom }} {{ optional($demande->client)->prenom }}
                @if($demande->client_matricule)
                    ({{ $demande->client_matricule }})
                @endif
            </td>
            <td class="label">Type credit</td>
            <td>{{ $demande->type_credit }}</td>
        </tr>
        <tr>
            <td class="label">Montant</td>
            <td>{{ number_format($demande->montant_accorde ?? $demande->montant_demande, 2, ',', ' ') }} {{ $demande->devise }}</td>
            <td class="label">Duree / Taux</td>
            <td>{{ $demande->duree_mois }} mois / {{ number_format($demande->taux_interet_mensuel, 2, ',', ' ') }} % mensuel</td>
        </tr>
        <tr>
            <td class="label">Date deblocage</td>
            <td>{{ optional($demande->date_deblocage)->format('d/m/Y') ?? '-' }}</td>
            <td class="label">Date 1er remboursement</td>
            <td>{{ optional(optional($demande->deblocages->first())->date_premier_remboursement)->format('d/m/Y') ?? '-' }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Plan d'amortissement degressif</div>
    <table class="info-table" style="font-size: 10px;">
        <thead>
            <tr style="background:#eef7f2;">
                <td class="label" style="width:6%; text-align:center;">#</td>
                <td class="label" style="width:13%; text-align:center;">Date echeance</td>
                <td class="label" style="width:16%; text-align:center;">Capital restant debut</td>
                <td class="label" style="width:14%; text-align:center;">Capital</td>
                <td class="label" style="width:14%; text-align:center;">Interet</td>
                <td class="label" style="width:14%; text-align:center;">Total echeance</td>
                <td class="label" style="width:16%; text-align:center;">Capital restant fin</td>
                <td class="label" style="width:7%; text-align:center;">Statut</td>
            </tr>
        </thead>
        <tbody>
            @php
                $sCap = 0; $sInt = 0; $sTot = 0;
            @endphp
            @foreach($echeancier->echeances as $e)
                @php
                    $sCap += $e->montant_capital;
                    $sInt += $e->montant_interet;
                    $sTot += $e->montant_total;
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $e->numero_echeance }}</td>
                    <td style="text-align:center;">{{ optional($e->date_echeance)->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($e->capital_restant_debut, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($e->montant_capital, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($e->montant_interet, 2, ',', ' ') }}</td>
                    <td class="text-right"><strong>{{ number_format($e->montant_total, 2, ',', ' ') }}</strong></td>
                    <td class="text-right">{{ number_format($e->capital_restant_fin, 2, ',', ' ') }}</td>
                    <td style="text-align:center; font-size:9px;">{{ str_replace('_',' ', $e->statut) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#eef7f2; font-weight:bold;">
                <td colspan="3" style="text-align:right;">Totaux :</td>
                <td class="text-right">{{ number_format($sCap, 2, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($sInt, 2, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($sTot, 2, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($echeancier->echeances->last()->capital_restant_fin ?? 0, 2, ',', ' ') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="section">
    <div class="two-col">
        <div class="col">
            <table class="info-table" style="font-size:10px;">
                <tr><td class="label">Total capital</td><td class="text-right">{{ number_format($sCap, 2, ',', ' ') }} {{ $demande->devise }}</td></tr>
                <tr><td class="label">Total interets</td><td class="text-right">{{ number_format($sInt, 2, ',', ' ') }} {{ $demande->devise }}</td></tr>
                <tr><td class="label">Montant global</td><td class="text-right"><strong>{{ number_format($sTot, 2, ',', ' ') }} {{ $demande->devise }}</strong></td></tr>
            </table>
        </div>
        <div class="col">
            <table class="info-table" style="font-size:10px;">
                <tr><td class="label">Nombre d'echeances</td><td>{{ $echeancier->echeances->count() }}</td></tr>
                <tr><td class="label">Periodicite</td><td>Mensuelle</td></tr>
                <tr><td class="label">Methode</td><td>Amortissement degressif (capital constant)</td></tr>
            </table>
        </div>
    </div>
</div>

@endsection

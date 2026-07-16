@extends('impressions._layout')

@section('titre', 'Releve de compte credit - ' . $dossier->numero_dossier)

@section('contenu')

@php
    $devise = $dossier->devise ?? 'USD';
    $nbMouvements = count($mouvements);
@endphp

<div class="doc-title">Releve de compte credit</div>

{{-- En-tete dossier --}}
<div class="section pdf-keep">
    <div class="section-title">Identification du dossier</div>
    <table class="info-table">
        <tr>
            <td class="label">Numero dossier</td>
            <td><strong>{{ $dossier->numero_dossier }}</strong></td>
            <td class="label">Client</td>
            <td>{{ $clientFullName }}</td>
        </tr>
        <tr>
            <td class="label">Montant accorde</td>
            <td>{{ number_format($dossier->montant_approuve ?? 0, 2, ',', ' ') }} {{ $devise }}</td>
            <td class="label">Statut actuel</td>
            <td><strong>{{ $dossier->statut_global }}</strong></td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td>{{ optional($dossier->deblocage?->created_at)->format('d/m/Y') ?? '-' }} au {{ now()->format('d/m/Y') }}</td>
            <td class="label">Nb mouvements</td>
            <td>{{ $nbMouvements }}</td>
        </tr>
    </table>
</div>

{{-- Resume financier --}}
<div class="section pdf-keep">
    <div class="section-title">Resume financier</div>
    <table class="info-table">
        <tr>
            <td class="label">Capital initial</td>
            <td class="text-right">{{ number_format($dossier->montant_approuve ?? 0, 2, ',', ' ') }}</td>
            <td class="label">Capital rembourse</td>
            <td class="text-right">{{ number_format($totalCapitalPaye, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <td class="label">Capital restant du</td>
            <td class="text-right text-danger"><strong>{{ number_format($capitalRestant, 2, ',', ' ') }}</strong></td>
            <td class="label">Interets payes</td>
            <td class="text-right">{{ number_format($totalInteretsPayes, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <td class="label">Commission totale</td>
            <td class="text-right">{{ number_format($dossier->commission_totale ?? 0, 2, ',', ' ') }}</td>
            <td class="label">Commission payee</td>
            <td class="text-right">{{ number_format($totalCommissionPayee ?? 0, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <td class="label">Caution bloquee (GTC)</td>
            <td class="text-right text-warning">{{ number_format($cautionBloquee, 2, ',', ' ') }}</td>
            <td class="label">Total debits</td>
            <td class="text-right">{{ number_format($totalDebits, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <td class="label">Prochaine echeance</td>
            <td>
                @if($prochaineEcheance)
                    {{ optional($prochaineEcheance->date_echeance)->format('d/m/Y') }} - {{ number_format($prochaineEcheance->montant_total, 2, ',', ' ') }} {{ $devise }}
                @else
                    <span class="text-success">Aucune - Credit termine</span>
                @endif
            </td>
            <td class="label">Total credits</td>
            <td class="text-right">{{ number_format($totalCredits, 2, ',', ' ') }}</td>
        </tr>
    </table>
</div>

{{-- Tableau des mouvements --}}
<div class="section pdf-keep">
    <div class="section-title">Mouvements du compte</div>
    <table class="movement-table">
        <thead>
            <tr>
                <th style="width:85px;">Date</th>
                <th>Libelle de l'operation</th>
                <th style="width:80px;" class="text-right">Debit</th>
                <th style="width:80px;" class="text-right">Credit</th>
                <th style="width:90px;" class="text-right">Solde</th>
            </tr>
        </thead>
        <tbody>
            {{-- Solde d'ouverture --}}
            <tr class="opening-row">
                <td></td>
                <td><strong>Solde d'ouverture</strong></td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right"><strong>{{ number_format($soldeOuverture, 2, ',', ' ') }}</strong></td>
            </tr>

            @foreach($mouvements as $mvt)
            <tr class="movement-row type-{{ $mvt['type'] }}">
                <td>{{ optional($mvt['date'])->format('d/m/Y') }}</td>
                <td>
                    @if($mvt['type'] === 'frais')
                        <span class="badge-frais">Frais</span>
                    @elseif($mvt['type'] === 'caution')
                        <span class="badge-caution">Caution</span>
                    @elseif($mvt['type'] === 'deblocage')
                        <span class="badge-deblocage">Deblocage</span>
                    @elseif($mvt['type'] === 'remboursement')
                        <span class="badge-remboursement">Remboursement</span>
                    @elseif($mvt['type'] === 'restitution')
                        <span class="badge-restitution">Restitution</span>
                    @endif
                    {{ $mvt['libelle'] }}
                </td>
                <td class="text-right debit">{{ $mvt['debit'] > 0 ? number_format($mvt['debit'], 2, ',', ' ') : '' }}</td>
                <td class="text-right credit">{{ $mvt['credit'] > 0 ? number_format($mvt['credit'], 2, ',', ' ') : '' }}</td>
                <td class="text-right solde"><strong>{{ number_format($mvt['solde'], 2, ',', ' ') }}</strong></td>
            </tr>
            @endforeach

            {{-- Solde de cloture --}}
            <tr class="closing-row">
                <td></td>
                <td><strong>Solde de cloture</strong></td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right"><strong>{{ number_format($soldeCloture, 2, ',', ' ') }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Pied de page --}}
<div class="section pdf-keep" style="margin-top:16px;">
    <div class="footer-info">
        <div class="footer-col">
            <strong>Document genere le</strong><br>
            {{ now()->format('d/m/Y H:i') }}
        </div>
        <div class="footer-col">
            <strong>Credit solde</strong><br>
            <span class="{{ $dossier->statut_global === 'SOLDE' ? 'text-success' : 'text-warning' }}">
                {{ $dossier->statut_global === 'SOLDE' ? 'OUI - Credit termine' : 'NON - En cours de remboursement' }}
            </span>
        </div>
        <div class="footer-col">
            <strong>Caution GTC</strong><br>
            <span class="{{ $cautionBloquee > 0 ? 'text-warning' : 'text-success' }}">
                {{ $cautionBloquee > 0 ? number_format($cautionBloquee, 2, ',', ' ') . ' ' . $devise . ' (bloquee)' : 'Restituee au client' }}
            </span>
        </div>
    </div>
</div>

@endsection

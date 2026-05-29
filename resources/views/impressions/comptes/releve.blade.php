@extends('impressions._layout')

@section('titre', 'Relevé Bancaire — ' . $compte->code_compte)

@section('contenu')

{{-- ── Titre du document ── --}}
<div style="text-align:center; margin-bottom:9px;">
    <div style="display:inline-block; background:#1a7a4a; color:#fff; padding:5px 28px;
                border-radius:4px; font-size:11px; font-weight:bold; letter-spacing:.8px;">
        RELEVÉ DE COMPTE BANCAIRE
    </div>
    <div style="font-size:8px; color:#555; margin-top:3px;">
        Période du <strong>{{ $dateDebut->isoFormat('D MMMM YYYY') }}</strong>
        au <strong>{{ $dateFin->isoFormat('D MMMM YYYY') }}</strong>
    </div>
</div>

{{-- ── Titulaire + Photo ── --}}
<div style="display:table; width:100%; margin-bottom:9px; border:1px solid #c8e6c9;
            border-radius:5px; overflow:hidden;">
    @if($photoBase64)
    <div style="display:table-cell; width:70px; padding:6px; vertical-align:top;
                background:#f0fdf4; border-right:1px solid #c8e6c9;">
        <img src="{{ $photoBase64 }}" alt="Photo"
             style="width:58px; height:72px; object-fit:cover; border-radius:4px;
                    border:2px solid #1a7a4a;">
    </div>
    @endif
    <div style="display:table-cell; padding:6px 9px; vertical-align:top; background:#f0fdf4;">
        <div style="font-size:11px; font-weight:bold; color:#1a7a4a; margin-bottom:3px;">
            {{ strtoupper($client->nom ?? '—') }}
            {{ strtoupper($client->postnom ?? '') }}
            {{ ucfirst(strtolower($client->prenom ?? '')) }}
        </div>
        <table style="font-size:8.3px; border-collapse:collapse;">
            <tr>
                <td style="color:#555; padding-right:8px; padding-bottom:2px;">Matricule :</td>
                <td style="font-weight:bold;">{{ $client->matricule ?? '—' }}</td>
                <td style="color:#555; padding-left:18px; padding-right:8px;">Tél :</td>
                <td>{{ $client->telephone ?? '—' }}</td>
            </tr>
            <tr>
                <td style="color:#555; padding-right:8px; padding-bottom:2px;">Adresse :</td>
                <td colspan="3">{{ $client->adresse ?? '—' }}</td>
            </tr>
            <tr>
                <td style="color:#555; padding-right:8px;">Pièce ID :</td>
                <td colspan="3">
                    {{ $client->type_piece_identite ?? '—' }}
                    @if($client->numero_piece_identite) n° {{ $client->numero_piece_identite }} @endif
                </td>
            </tr>
        </table>
    </div>
    <div style="display:table-cell; width:165px; padding:6px 9px; vertical-align:top;
                background:#e6f4ea; border-left:1px solid #c8e6c9; text-align:center;">
        <div style="font-size:8px; color:#555; margin-bottom:2px; text-transform:uppercase;
                    letter-spacing:.5px;">Compte</div>
        <div style="font-size:9px; font-weight:bold; font-family:'Courier New', monospace;
                    color:#1a7a4a; margin-bottom:4px; word-break:break-all;">
            {{ $compte->code_compte }}
        </div>
        @php
            $types = ['CC'=>'Compte Courant','RMB'=>'Remboursement','GTC'=>'Caution',
                      'DAT'=>'Dépôt à Terme','EAV'=>'Épargne & Vie'];
        @endphp
        <div style="font-size:8px; color:#333; margin-bottom:3px;">
            {{ $types[$compte->type] ?? $compte->type }}
        </div>
        <div style="font-size:8px; color:#555;">Devise : <strong>{{ $compte->devise }}</strong></div>
    </div>
</div>

{{-- ── Solde d'ouverture ── --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:9px; font-size:8.3px;">
    <tr>
        <td style="background:#f8fafc; border:1px solid #dde; padding:4px 7px; color:#555;">
            Solde au {{ $dateDebut->isoFormat('D MMMM YYYY') }}
        </td>
        <td style="background:#f8fafc; border:1px solid #dde; padding:4px 7px;
                   text-align:right; font-weight:bold; color:{{ $soldeOuverture >= 0 ? '#1a7a4a' : '#c0392b' }};">
            {{ number_format($soldeOuverture, 2, ',', ' ') }} {{ $compte->devise }}
        </td>
        <td style="background:#f8fafc; border:1px solid #dde; padding:4px 7px; color:#555;">
            Nombre d'opérations
        </td>
        <td style="background:#f8fafc; border:1px solid #dde; padding:4px 7px;
                   text-align:right; font-weight:bold;">
            {{ $transactions->count() }}
        </td>
    </tr>
</table>

{{-- ── Tableau des opérations ── --}}
@if($transactions->count() > 0)
@php
    $solde = $soldeOuverture;
    $totalDebit = 0;
    $totalCredit = 0;
@endphp
<table style="width:100%; border-collapse:collapse; font-size:8px; margin-bottom:9px;">
    <thead>
        <tr style="background:#1a7a4a; color:#fff;">
            <th style="padding:4px 5px; text-align:left; width:50px;">Date</th>
            <th style="padding:4px 5px; text-align:left; width:110px;">Référence</th>
            <th style="padding:4px 5px; text-align:left; width:55px;">Opération</th>
            <th style="padding:4px 5px; text-align:left;">Observ.</th>
            <th style="padding:4px 5px; text-align:right; width:62px;">Débit</th>
            <th style="padding:4px 5px; text-align:right; width:62px;">Crédit</th>
            <th style="padding:4px 5px; text-align:right; width:66px;">Solde</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $i => $t)
        @php
            $isDebit  = in_array($t->type, ['RETRAIT', 'VIREMENT', 'REMBOURSEMENT']);
            $isCredit = in_array($t->type, ['DEPOT', 'PAIEMENT']);
            $montant  = (float) $t->montant;
            if ($isDebit) {
                $solde -= $montant;
                $totalDebit += $montant;
            } else {
                $solde += $montant;
                $totalCredit += $montant;
            }
            $typeLabels = ['DEPOT'=>'Dépôt','RETRAIT'=>'Retrait','VIREMENT'=>'Virement',
                           'REMBOURSEMENT'=>'Remboursement','CHANGE'=>'Change','PAIEMENT'=>'Paiement'];
            $rowBg = $i % 2 === 0 ? '#fff' : '#f5faf6';
        @endphp
        <tr style="background:{{ $rowBg }}; border-bottom:1px solid #e0ede0;">
            <td style="padding:3px 5px; white-space:nowrap;">
                {{ $t->date_operation?->format('d/m/Y') }}
            </td>
            <td style="padding:3px 5px; font-family:'Courier New',monospace; font-size:7px;">
                {{ $t->reference }}
            </td>
            <td style="padding:3px 5px;">
                {{ $typeLabels[$t->type] ?? $t->type }}
                @if($t->type === 'CHANGE' && $t->devise_dest)
                <br><span style="font-size:7px; color:#555;">→ {{ number_format($t->montant_dest,2,',','') }} {{ $t->devise_dest }}</span>
                @endif
            </td>
            <td style="padding:3px 5px; font-size:7px; color:#666;">
                {{ \Illuminate\Support\Str::limit($t->observations ?? '', 50) }}
            </td>
            <td style="padding:3px 5px; text-align:right; color:{{ $isDebit ? '#c0392b' : '' }}; font-weight:{{ $isDebit ? 'bold' : 'normal' }};">
                {{ $isDebit ? number_format($montant, 2, ',', ' ') : '' }}
            </td>
            <td style="padding:3px 5px; text-align:right; color:{{ $isCredit ? '#1a7a4a' : '' }}; font-weight:{{ $isCredit ? 'bold' : 'normal' }};">
                {{ $isCredit ? number_format($montant, 2, ',', ' ') : '' }}
            </td>
            <td style="padding:3px 5px; text-align:right; font-weight:bold;
                       color:{{ $solde >= 0 ? '#1a7a4a' : '#c0392b' }};">
                {{ number_format($solde, 2, ',', ' ') }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#e6f4ea; font-weight:bold; border-top:2px solid #1a7a4a;">
            <td colspan="4" style="padding:4px 5px; text-align:right; font-size:8px;">TOTAUX DE LA PÉRIODE</td>
            <td style="padding:4px 5px; text-align:right; color:#c0392b;">
                {{ number_format($totalDebit, 2, ',', ' ') }}
            </td>
            <td style="padding:4px 5px; text-align:right; color:#1a7a4a;">
                {{ number_format($totalCredit, 2, ',', ' ') }}
            </td>
            <td style="padding:4px 5px;"></td>
        </tr>
    </tfoot>
</table>
@else
<div style="text-align:center; padding:12px; border:1px dashed #c8e6c9; border-radius:4px;
            color:#555; margin-bottom:9px; font-size:8.5px;">
    Aucune opération confirmée sur cette période.
</div>
@endif

{{-- ── Solde de clôture ── --}}
<table style="width:100%; border-collapse:collapse; font-size:8.5px; margin-bottom:12px;">
    <tr>
        <td style="background:#1a7a4a; color:#fff; padding:5px 8px; border-radius:4px 0 0 4px; font-weight:bold;">
            SOLDE AU {{ $dateFin->isoFormat('D MMMM YYYY') }}
        </td>
        <td style="background:#e6f4ea; border:1px solid #1a7a4a; padding:5px 8px;
                   text-align:right; font-size:10.5px; font-weight:bold;
                   color:{{ $soldeActuel >= 0 ? '#1a7a4a' : '#c0392b' }};
                   border-radius:0 4px 4px 0;">
            {{ number_format($soldeActuel, 2, ',', ' ') }} {{ $compte->devise }}
        </td>
    </tr>
</table>

{{-- ── Notice légale ── --}}
<div style="font-size:7px; color:#888; border-top:1px solid #dde; padding-top:5px; text-align:center;">
    Ce relevé est établi à la date du {{ now()->isoFormat('D MMMM YYYY') }} et ne vaut que pour les opérations confirmées.
    En cas de contestation, veuillez vous rapprocher de votre agence dans les 30 jours suivant la réception.
</div>

@endsection

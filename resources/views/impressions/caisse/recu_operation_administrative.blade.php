@extends('impressions._layout')

@section('titre', 'Reçu — ' . $transaction->reference)

@php
    $estDepense = $sens === 'DEPENSE';
    $typeLabel = $estDepense ? 'DÉPENSE (SORTIE DE CAISSE)' : 'RECETTE (ENTRÉE DE CAISSE)';
    $couleur = $estDepense ? ['bg' => '#c0392b', 'light' => '#fdf0ee'] : ['bg' => '#1a7a4a', 'light' => '#e8f5ee'];
    $estAnnule = $transaction->statut === 'ANNULE';
    $montantFmt = number_format((float) $transaction->montant, 2, ',', ' ') . ' ' . $transaction->devise_code;
    $lineColor = '#333333';
    $lineSoftColor = '#555555';
@endphp

@section('contenu')

<div style="width:100%; border:2px solid {{ $lineColor }}; border-radius:6px; overflow:hidden;">

    <div style="background:{{ $couleur['bg'] }}; color:#fff; padding:7px 14px; display:table; width:100%;">
        <div style="display:table-cell; vertical-align:middle;">
            <span style="font-size:14px; font-weight:bold; letter-spacing:1.5px;">
                REÇU DE {{ $typeLabel }}
            </span>
            @if($estAnnule)
            <span style="background:#e74c3c; color:#fff; font-size:9px; font-weight:bold; padding:2px 8px; border-radius:10px; margin-left:10px;">
                ✕ OPÉRATION ANNULÉE
            </span>
            @endif
        </div>
        <div style="display:table-cell; text-align:right; vertical-align:middle; font-size:10px; opacity:.95;">
            Pièce comptable interne
        </div>
    </div>

    <div style="display:table; width:100%; padding:10px 14px; background:{{ $couleur['light'] }};">

        {{-- Colonne gauche : infos opération --}}
        <div style="display:table-cell; width:55%; vertical-align:top; padding-right:14px;">

            <div style="background:#fff; border:1.5px solid {{ $lineSoftColor }}; border-radius:5px; padding:7px 12px; margin-bottom:8px; text-align:center;">
                <div style="font-size:9px; color:#444; text-transform:uppercase; letter-spacing:.8px; margin-bottom:2px;">
                    Référence de l'opération
                </div>
                <div style="font-size:16px; font-weight:bold; font-family:'Courier New',monospace; color:{{ $couleur['bg'] }}; letter-spacing:1px;">
                    {{ $transaction->reference }}
                </div>
                <div style="font-size:10px; color:#333; margin-top:3px;">
                    {{ $transaction->date_operation ? $transaction->date_operation->isoFormat('dddd D MMMM YYYY [à] HH:mm:ss') : now()->isoFormat('dddd D MMMM YYYY [à] HH:mm:ss') }}
                </div>
            </div>

            <div style="background:{{ $couleur['bg'] }}; color:#fff; border-radius:5px; padding:8px 12px; text-align:center; margin-bottom:8px;">
                <div style="font-size:10px; text-transform:uppercase; letter-spacing:.7px; opacity:.9; margin-bottom:2px;">
                    Montant de l'opération
                </div>
                <div style="font-size:22px; font-weight:bold; letter-spacing:1px;">{{ $montantFmt }}</div>
            </div>

            <table style="width:100%; border-collapse:collapse; font-size:10.5px;">
                <tr>
                    <td style="color:#222; padding:4px 6px; width:38%; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Type :</td>
                    <td style="font-weight:bold; color:#111; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">{{ $typeLabel }}</td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Catégorie :</td>
                    <td style="font-weight:bold; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">{{ $categorie->libelle ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Compte OHADA imputé :</td>
                    <td style="font-family:'Courier New',monospace; font-weight:bold; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ $estDepense ? ($categorie->numero_compte_charge ?? '-') : ($categorie->numero_compte_produit ?? '-') }}
                        — {{ $estDepense ? ($categorie->compteCharge->libelle ?? '') : ($categorie->compteProduit->libelle ?? '') }}
                    </td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Statut :</td>
                    <td style="font-weight:bold; color:{{ $estAnnule ? '#7a1f14' : '#1f4f32' }}; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ $estAnnule ? 'ANNULÉ' : 'CONFIRMÉ' }}
                    </td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; vertical-align:top; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Motif :</td>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">{{ $motif }}</td>
                </tr>
            </table>
        </div>

        {{-- Colonne droite : guichet + signatures --}}
        <div style="display:table-cell; width:45%; vertical-align:top; border-left:1.5px dashed {{ $lineSoftColor }}; padding-left:14px;">

            <div style="background:#fff; border:1.5px solid {{ $lineSoftColor }}; border-radius:5px; padding:7px 10px; margin-bottom:8px; font-size:8.5px;">
                <div style="font-size:9px; color:#444; text-transform:uppercase; letter-spacing:.8px; margin-bottom:4px; border-bottom:1px solid {{ $lineSoftColor }}; padding-bottom:3px;">
                    Guichet / Caissier
                </div>
                <div><span style="color:#666;">Guichet :</span> <strong>{{ $guichet?->intitule ?? '—' }}</strong>
                    @if($guichet?->code_guichet)<span style="color:#888;"> ({{ $guichet->code_guichet }})</span>@endif
                </div>
                <div style="margin-top:2px;"><span style="color:#666;">Agent :</span> <strong>{{ $agentNom }}</strong>
                    @if($transaction->agent_matricule)<span style="color:#888;"> — {{ $transaction->agent_matricule }}</span>@endif
                </div>
            </div>

            @if($pieceJustificative)
            <div style="background:#fff; border:1.5px solid {{ $lineSoftColor }}; border-radius:5px; padding:6px 10px; margin-bottom:8px; font-size:8.5px;">
                <i>Pièce justificative jointe au dossier.</i>
            </div>
            @endif

            <div style="display:table; width:100%; margin-bottom:6px;">
                <div style="display:table-cell; width:50%; padding-right:4px; vertical-align:top;">
                    <div style="border:1.5px solid {{ $lineColor }}; border-radius:4px; padding:6px 8px; text-align:center; font-size:9px; color:#222; height:48px;">
                        Signature du caissier
                        <div style="margin-top:16px; font-size:7.5px; color:#444; font-weight:bold;">{{ $agentNom }}</div>
                    </div>
                </div>
                <div style="display:table-cell; width:50%; padding-left:4px; vertical-align:top;">
                    <div style="border:1.5px solid {{ $lineColor }}; border-radius:4px; padding:6px 8px; text-align:center; font-size:9px; color:#222; height:48px;">
                        {{ $estDepense ? 'Signature du bénéficiaire' : 'Signature du déposant' }}
                    </div>
                </div>
            </div>

            <div style="font-size:7.5px; color:#aaa; text-align:center;">
                Réf : {{ $transaction->reference }} &bull; Imprimé par {{ $imprimeParNom }} le {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

@endsection

@extends('impressions._layout')

@section('titre', 'Reçu de virement — ' . ($demande->transaction->reference ?? ('DVIR-' . $demande->id)))

@php
    $couleur = ['bg' => '#1f5fa8', 'light' => '#eaf1fb'];
    $lineColor = '#333333';
    $lineSoftColor = '#555555';
    $montantSrcFmt = number_format((float) $demande->montant_source, 2, ',', ' ') . ' ' . $demande->devise_source;
    $montantDstFmt = number_format((float) $demande->montant_dest, 2, ',', ' ') . ' ' . $demande->devise_dest;
    $memeDevise = $demande->devise_source === $demande->devise_dest;
    $comptableNom = trim(($demande->comptable->prenom ?? '') . ' ' . ($demande->comptable->nom ?? '')) ?: $demande->comptable_matricule;
    $validateurNom = trim(($demande->validateur->prenom ?? '') . ' ' . ($demande->validateur->nom ?? '')) ?: $demande->validateur_matricule;
@endphp

@section('contenu')

<div style="width:100%; border:2px solid {{ $lineColor }}; border-radius:6px; overflow:hidden;">

    <div style="background:{{ $couleur['bg'] }}; color:#fff; padding:7px 14px; display:table; width:100%;">
        <div style="display:table-cell; vertical-align:middle;">
            <span style="font-size:14px; font-weight:bold; letter-spacing:1.5px;">
                REÇU DE VIREMENT BANCAIRE
            </span>
        </div>
        <div style="display:table-cell; text-align:right; vertical-align:middle; font-size:10px; opacity:.95;">
            Pièce comptable interne
        </div>
    </div>

    <div style="display:table; width:100%; padding:10px 14px; background:{{ $couleur['light'] }};">

        <div style="display:table-cell; width:55%; vertical-align:top; padding-right:14px;">

            <div style="background:#fff; border:1.5px solid {{ $lineSoftColor }}; border-radius:5px; padding:7px 12px; margin-bottom:8px; text-align:center;">
                <div style="font-size:9px; color:#444; text-transform:uppercase; letter-spacing:.8px; margin-bottom:2px;">
                    Référence de l'opération
                </div>
                <div style="font-size:16px; font-weight:bold; font-family:'Courier New',monospace; color:{{ $couleur['bg'] }}; letter-spacing:1px;">
                    {{ $demande->transaction->reference ?? ('DVIR-' . $demande->id) }}
                </div>
                <div style="font-size:10px; color:#333; margin-top:3px;">
                    {{ $demande->traite_le ? $demande->traite_le->isoFormat('dddd D MMMM YYYY [à] HH:mm:ss') : now()->isoFormat('dddd D MMMM YYYY [à] HH:mm:ss') }}
                </div>
            </div>

            <div style="background:{{ $couleur['bg'] }}; color:#fff; border-radius:5px; padding:8px 12px; text-align:center; margin-bottom:8px;">
                <div style="font-size:10px; text-transform:uppercase; letter-spacing:.7px; opacity:.9; margin-bottom:2px;">
                    Montant transféré
                </div>
                <div style="font-size:20px; font-weight:bold; letter-spacing:1px;">{{ $montantSrcFmt }}</div>
                @if(!$memeDevise)
                    <div style="font-size:11px; margin-top:2px;">soit {{ $montantDstFmt }} (taux : {{ $demande->taux_change }})</div>
                @endif
            </div>

            <table style="width:100%; border-collapse:collapse; font-size:10.5px;">
                <tr>
                    <td style="color:#222; padding:4px 6px; width:38%; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Compte source :</td>
                    <td style="font-weight:bold; color:#111; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff; font-family:'Courier New',monospace;">
                        {{ $demande->compte_source_code }} <br><span style="font-family:inherit; font-weight:normal;">{{ $demande->clientSource->full_name ?? $demande->client_source_matricule }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Compte destination :</td>
                    <td style="font-weight:bold; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff; font-family:'Courier New',monospace;">
                        {{ $demande->compte_dest_code }} <br><span style="font-family:inherit; font-weight:normal;">{{ $demande->clientDest->full_name ?? $demande->client_dest_matricule }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Statut :</td>
                    <td style="font-weight:bold; color:#1f4f32; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ $demande->statutLabel() }}
                    </td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; vertical-align:top; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Motif :</td>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">{{ $demande->motif }}</td>
                </tr>
            </table>
        </div>

        <div style="display:table-cell; width:45%; vertical-align:top; border-left:1.5px dashed {{ $lineSoftColor }}; padding-left:14px;">

            <div style="background:#fff; border:1.5px solid {{ $lineSoftColor }}; border-radius:5px; padding:7px 10px; margin-bottom:8px; font-size:8.5px;">
                <div style="font-size:9px; color:#444; text-transform:uppercase; letter-spacing:.8px; margin-bottom:4px; border-bottom:1px solid {{ $lineSoftColor }}; padding-bottom:3px;">
                    Traçabilité
                </div>
                <div><span style="color:#666;">Proposé par :</span> <strong>{{ $comptableNom }}</strong></div>
                <div style="margin-top:2px;"><span style="color:#666;">Approuvé par :</span> <strong>{{ $validateurNom }}</strong></div>
                <div style="margin-top:2px;"><span style="color:#666;">Date de proposition :</span> {{ $demande->propose_le?->format('d/m/Y H:i') }}</div>
                <div style="margin-top:2px;"><span style="color:#666;">Date d'exécution :</span> {{ $demande->traite_le?->format('d/m/Y H:i') }}</div>
            </div>

            <div style="display:table; width:100%; margin-bottom:6px;">
                <div style="display:table-cell; width:50%; padding-right:4px; vertical-align:top;">
                    <div style="border:1.5px solid {{ $lineColor }}; border-radius:4px; padding:6px 8px; text-align:center; font-size:9px; color:#222; height:48px;">
                        Signature du Comptable
                        <div style="margin-top:16px; font-size:7.5px; color:#444; font-weight:bold;">{{ $comptableNom }}</div>
                    </div>
                </div>
                <div style="display:table-cell; width:50%; padding-left:4px; vertical-align:top;">
                    <div style="border:1.5px solid {{ $lineColor }}; border-radius:4px; padding:6px 8px; text-align:center; font-size:9px; color:#222; height:48px;">
                        Signature du Validateur
                        <div style="margin-top:16px; font-size:7.5px; color:#444; font-weight:bold;">{{ $validateurNom }}</div>
                    </div>
                </div>
            </div>

            <div style="font-size:7.5px; color:#aaa; text-align:center;">
                Réf : {{ $demande->transaction->reference ?? ('DVIR-' . $demande->id) }} &bull; Imprimé le {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

@endsection

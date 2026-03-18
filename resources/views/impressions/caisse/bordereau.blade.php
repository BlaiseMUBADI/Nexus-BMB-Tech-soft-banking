@extends('impressions._layout')

@section('titre', 'Bordereau — ' . $op->reference)

@php
    $typeLabel = [
        'DEPOT'         => 'DÉPÔT',
        'RETRAIT'       => 'RETRAIT',
        'CHANGE'        => 'CHANGE DE DEVISES',
        'PAIEMENT'      => 'PAIEMENT',
        'REMBOURSEMENT' => 'REMBOURSEMENT',
        'VIREMENT'      => 'VIREMENT',
    ][$op->type] ?? strtoupper($op->type);

    $typeColors = [
        'DEPOT'         => ['bg' => '#1a7a4a', 'light' => '#e8f5ee'],
        'RETRAIT'       => ['bg' => '#c0392b', 'light' => '#fdf0ee'],
        'CHANGE'        => ['bg' => '#1a5276', 'light' => '#eaf2fa'],
        'PAIEMENT'      => ['bg' => '#6c3483', 'light' => '#f5eef8'],
        'REMBOURSEMENT' => ['bg' => '#d35400', 'light' => '#fdf2e5'],
        'VIREMENT'      => ['bg' => '#117a65', 'light' => '#e8f8f5'],
    ][$op->type] ?? ['bg' => '#1a7a4a', 'light' => '#e8f5ee'];

    $estAnnule   = $op->statut === 'ANNULE';
    $montantFmt  = number_format((float)$op->montant, 2, ',', ' ') . ' ' . $op->devise_code;
    $clientNomSignature = trim(
        strtoupper($client->nom ?? '') . ' ' .
        strtoupper($client->postnom ?? '') . ' ' .
        ucfirst(strtolower($client->prenom ?? ''))
    );
    $clientNomSignature = $clientNomSignature !== '' ? $clientNomSignature : 'Client';
    $commission = $op->commissions->sortByDesc('id')->first();
    $commissionMontant = max(0, (float) ($op->montant_commission_total ?? ($commission->montant_commission ?? 0)));
    $commissionDevise = $commission->devise_code ?? $op->devise_code;
    $commissionMode = strtoupper((string) ($commission->mode_calcul ?? ''));
    $commissionTaux = $commission && $commissionMode === 'POURCENTAGE'
        ? number_format((float) ($commission->valeur_snapshot ?? 0), 2, ',', ' ') . '%'
        : null;
    $commissionDisplay = $commissionTaux
        ? $commissionTaux . ' (' . number_format($commissionMontant, 2, ',', ' ') . ' ' . $commissionDevise . ')'
        : ($commission && $commissionMode === 'FIXE'
            ? 'FIXE (' . number_format($commissionMontant, 2, ',', ' ') . ' ' . $commissionDevise . ')'
            : number_format($commissionMontant, 2, ',', ' ') . ' ' . $commissionDevise);

    $montantTotalClientCalcule = null;
    $montantNetClientCalcule = null;
    if ($op->compte_code && $op->type === 'RETRAIT') {
        $montantTotalClientCalcule = round((float) $op->montant + $commissionMontant, 2);
        $montantNetClientCalcule = -$montantTotalClientCalcule;
    } elseif ($op->compte_code && $op->type === 'DEPOT') {
        $montantTotalClientCalcule = round((float) $op->montant - $commissionMontant, 2);
        $montantNetClientCalcule = $montantTotalClientCalcule;
    }

    $soldeAvantCompte = $op->solde_compte_avant !== null ? (float) $op->solde_compte_avant : null;
    $montantTotalClient = $montantTotalClientCalcule;
    if ($montantTotalClient === null && $op->montant_total_client !== null) {
        $montantTotalClient = (float) $op->montant_total_client;
    }

    $soldeApresCompte = $op->solde_compte_apres !== null
        ? (float) $op->solde_compte_apres
        : (($soldeAvantCompte !== null && $montantNetClientCalcule !== null)
            ? round($soldeAvantCompte + $montantNetClientCalcule, 2)
            : ($compte ? (float) $compte->solde_reel : null));

    $impactClientLabel = $op->type === 'DEPOT' ? 'Montant net credite' : 'Montant total debite';
    $lineColor = '#333333';
    $lineSoftColor = '#555555';
    $panelBg = '#ffffff';
@endphp

@section('contenu')

{{-- ════════════════════════════════════════════════════════════
     COPY 1 — EXEMPLAIRE CLIENT
     ════════════════════════════════════════════════════════════ --}}
<div style="width:100%; border:2px solid {{ $lineColor }}; border-radius:6px;
            overflow:hidden; margin-bottom:0;">

    {{-- ── Bandeau titre ── --}}
    <div style="background:{{ $typeColors['bg'] }}; color:#fff; padding:7px 14px;
                display:table; width:100%;">
        <div style="display:table-cell; vertical-align:middle;">
            <span style="font-size:14px; font-weight:bold; letter-spacing:1.5px;">
                BORDEREAU DE {{ $typeLabel }}
            </span>
            @if($estAnnule)
            <span style="background:#e74c3c; color:#fff; font-size:9px; font-weight:bold;
                         padding:2px 8px; border-radius:10px; margin-left:10px;">
                ✕ OPÉRATION ANNULÉE
            </span>
            @endif
        </div>
        <div style="display:table-cell; text-align:right; vertical-align:middle; font-size:10px; opacity:.95;">
            Exemplaire client
        </div>
    </div>

    <div style="display:table; width:100%; padding:10px 14px; background:{{ $typeColors['light'] }};">

        {{-- ── Colonne gauche : infos opération ── --}}
        <div style="display:table-cell; width:55%; vertical-align:top; padding-right:14px;">

            {{-- Référence grande --}}
            <div style="background:{{ $panelBg }}; border:1.5px solid {{ $lineSoftColor }}; border-radius:5px;
                        padding:7px 12px; margin-bottom:8px; text-align:center;">
                <div style="font-size:9px; color:#444; text-transform:uppercase; letter-spacing:.8px; margin-bottom:2px;">
                    Référence de l'opération
                </div>
                <div style="font-size:16px; font-weight:bold; font-family:'Courier New',monospace;
                            color:{{ $typeColors['bg'] }}; letter-spacing:1px;">
                    {{ $op->reference }}
                </div>
                <div style="font-size:10px; color:#333; margin-top:3px;">
                    {{ $op->date_operation ? $op->date_operation->isoFormat('dddd D MMMM YYYY [à] HH:mm:ss') : now()->isoFormat('dddd D MMMM YYYY [à] HH:mm:ss') }}
                </div>
            </div>

            {{-- Montant --}}
            <div style="background:{{ $typeColors['bg'] }}; color:#fff; border-radius:5px;
                        padding:8px 12px; text-align:center; margin-bottom:8px;">
                <div style="font-size:10px; text-transform:uppercase; letter-spacing:.7px; opacity:.9; margin-bottom:2px;">
                    Montant de l'opération
                </div>
                <div style="font-size:22px; font-weight:bold; letter-spacing:1px;">
                    {{ $montantFmt }}
                </div>
                @if($op->type === 'CHANGE' && $op->montant_dest)
                <div style="font-size:10px; opacity:.9; margin-top:3px;">
                    → {{ number_format((float)$op->montant_dest, 2, ',', ' ') }} {{ $op->devise_dest }}
                    @if($op->taux_change)
                    &nbsp;·&nbsp; Taux : {{ number_format((float)$op->taux_change, 4, ',', ' ') }}
                    @endif
                </div>
                @endif
            </div>

            {{-- Détails opération --}}
            <table style="width:100%; border-collapse:collapse; font-size:10.5px;">
                <tr>
                    <td style="color:#222; padding:4px 6px; width:38%; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Type d'opération :</td>
                    <td style="font-weight:bold; color:#111; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">{{ $typeLabel }}</td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Devise :</td>
                    <td style="font-weight:bold; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">{{ $op->devise_code }}</td>
                </tr>
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Statut :</td>
                    <td style="font-weight:bold; color:{{ $estAnnule ? '#7a1f14' : '#1f4f32' }}; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ $estAnnule ? 'ANNULÉ' : 'CONFIRMÉ' }}
                    </td>
                </tr>
                @if($op->compte_code || $commission || $op->montant_commission_total !== null)
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Commission :</td>
                    <td style="font-weight:bold; color:#111; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ $commissionDisplay }}
                    </td>
                </tr>
                @endif
                @if($op->compte_code)
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">N° Compte :</td>
                    <td style="font-family:'Courier New',monospace; font-size:9.5px; font-weight:bold; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ $op->compte_code }}
                    </td>
                </tr>
                @endif
                @if($op->compte_code && $montantTotalClient !== null)
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">{{ $impactClientLabel }} :</td>
                    <td style="font-weight:bold; color:#111; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ number_format($montantTotalClient, 2, ',', ' ') }} {{ $op->devise_code }}
                    </td>
                </tr>
                @endif
                @if($op->compte_code && $soldeAvantCompte !== null)
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Solde reel avant :</td>
                    <td style="font-weight:bold; color:#111; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ number_format($soldeAvantCompte, 2, ',', ' ') }} {{ $op->devise_code }}
                    </td>
                </tr>
                @endif
                @if($op->compte_code && $soldeApresCompte !== null)
                <tr>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Solde reel apres :</td>
                    <td style="font-weight:bold; color:#111; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">
                        {{ number_format($soldeApresCompte, 2, ',', ' ') }} {{ $op->devise_code }}
                    </td>
                </tr>
                @endif
                @if($op->observations)
                <tr>
                    <td style="color:#222; padding:4px 6px; vertical-align:top; border:1px solid {{ $lineColor }}; background:#f1f1f1;">Observations :</td>
                    <td style="color:#222; padding:4px 6px; border:1px solid {{ $lineColor }}; background:#fff;">{{ $op->observations }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- ── Colonne droite : client + guichet ── --}}
        <div style="display:table-cell; width:45%; vertical-align:top;
                border-left:1.5px dashed {{ $lineSoftColor }}; padding-left:14px;">

            {{-- Client --}}
            @if($client)
            <div style="background:{{ $panelBg }}; border:1.5px solid {{ $lineSoftColor }}; border-radius:5px;
                        padding:7px 10px; margin-bottom:8px;">
                <div style="font-size:9px; color:#444; text-transform:uppercase; letter-spacing:.8px;
                            margin-bottom:5px; border-bottom:1px solid {{ $lineSoftColor }}; padding-bottom:3px;">
                    Titulaire du compte
                </div>
                <div style="display:table; width:100%;">
                    @if($photoBase64)
                    <div style="display:table-cell; width:42px; vertical-align:top; padding-right:7px;">
                        <img src="{{ $photoBase64 }}" alt="photo"
                             style="width:40px; height:48px; object-fit:cover; border-radius:3px;
                                    border:1.5px solid {{ $typeColors['bg'] }};"/>
                    </div>
                    @endif
                    <div style="display:table-cell; vertical-align:top;">
                        <div style="font-size:11px; font-weight:bold; color:#1a2e1e; line-height:1.3;">
                            {{ strtoupper($client->nom ?? '') }}
                            {{ strtoupper($client->postnom ?? '') }}
                            {{ ucfirst(strtolower($client->prenom ?? '')) }}
                        </div>
                        @if($client->matricule)
                        <div style="font-size:8.5px; color:#555; margin-top:2px;">
                            Matr. : <strong>{{ $client->matricule }}</strong>
                        </div>
                        @endif
                        @if($client->telephone)
                        <div style="font-size:8.5px; color:#555;">
                            Tél : {{ $client->telephone }}
                        </div>
                        @endif
                        <div style="font-size:8.5px; color:#555;">
                            Zone : <strong>{{ $zoneNom ?: '—' }}</strong>
                        </div>
                    </div>
                </div>
                @if($compte)
                <div style="margin-top:5px; padding-top:4px; border-top:1px solid #eee;">
                    @php
                        $types = ['CC'=>'Compte Courant','RMB'=>'Remboursement',
                                  'GTC'=>'Caution','DAT'=>'Dépôt à Terme','EAV'=>'Épargne & Vie'];
                    @endphp
                    <div style="font-size:8.5px; color:#555;">
                        {{ $types[$compte->type ?? ''] ?? ($compte->type ?? '') }}
                        @if($compte->code_compte)
                            — N° compte : <strong>{{ $compte->code_compte }}</strong>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Guichet / agent --}}
            <div style="background:{{ $panelBg }}; border:1.5px solid {{ $lineSoftColor }}; border-radius:5px;
                        padding:7px 10px; margin-bottom:8px; font-size:8.5px;">
                <div style="font-size:9px; color:#444; text-transform:uppercase; letter-spacing:.8px;
                            margin-bottom:4px; border-bottom:1px solid {{ $lineSoftColor }}; padding-bottom:3px;">
                    Guichet / Caissier
                </div>
                <div><span style="color:#666;">Guichet :</span>
                    <strong>{{ $guichet?->intitule ?? '—' }}</strong>
                    @if($guichet?->code_guichet)
                    <span style="color:#888;"> ({{ $guichet->code_guichet }})</span>
                    @endif
                </div>
                <div style="margin-top:2px;"><span style="color:#666;">Caissier :</span>
                    <strong>{{ $agentNom }}</strong>
                    @if($op->agent_matricule)
                    <span style="color:#888;"> — {{ $op->agent_matricule }}</span>
                    @endif
                </div>
            </div>

            {{-- Cases signatures --}}
            <div style="display:table; width:100%; margin-bottom:6px;">
                <div style="display:table-cell; width:50%; padding-right:4px; vertical-align:top;">
                    <div style="border:1.5px solid {{ $lineColor }}; border-radius:4px; padding:6px 8px; text-align:center;
                                font-size:9px; color:#222; height:48px;">
                        Signature du caissier
                        <div style="margin-top:16px; font-size:7.5px; color:#444; font-weight:bold;">
                            {{ $agentNom }}
                        </div>
                    </div>
                </div>
                <div style="display:table-cell; width:50%; padding-left:4px; vertical-align:top;">
                    <div style="border:1.5px solid {{ $lineColor }}; border-radius:4px; padding:6px 8px; text-align:center;
                                font-size:9px; color:#222; height:48px;">
                        Signature du client
                        <div style="margin-top:16px; font-size:7.5px; color:#444; font-weight:bold;">
                            {{ $clientNomSignature }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Référence --}}
            <div style="font-size:7.5px; color:#aaa; text-align:center;">
                Réf : {{ $op->reference }} &bull; {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

{{-- ══ Ligne de découpe ══════════════════════════════════════════════════════ --}}
<div style="margin:8px 0; border-top:1.5px dashed {{ $lineSoftColor }}; text-align:center;
            color:#555; font-size:9px; letter-spacing:1px; padding-top:3px;">
    ✂ &nbsp; EXEMPLAIRE BANQUE &nbsp; ✂
</div>

{{-- ════════════════════════════════════════════════════════════
     COPY 2 — EXEMPLAIRE BANQUE (condensé)
     ════════════════════════════════════════════════════════════ --}}
<div style="width:100%; border:1.5px solid {{ $lineColor }}; border-radius:5px; overflow:hidden;">
    <div style="background:{{ $typeColors['bg'] }}; color:#fff; padding:5px 14px; font-size:10px;
                font-weight:bold; letter-spacing:1px; display:table; width:100%;">
        <div style="display:table-cell;">BORDEREAU DE {{ $typeLabel }} — Copie Banque</div>
        <div style="display:table-cell; text-align:right; font-size:8px; opacity:.8; font-weight:normal;">
            @if($estAnnule)<span style="background:#e74c3c; padding:1px 6px; border-radius:8px;">ANNULÉ</span>@endif
        </div>
    </div>
    <div style="padding:8px 14px; background:#fafafa; display:table; width:100%; font-size:10px;">
        <div style="display:table-cell; width:33%; vertical-align:top;">
            <div style="color:#333; margin-bottom:1px; font-weight:bold;">Référence</div>
            <div style="font-weight:bold; font-family:'Courier New',monospace; font-size:10px;">{{ $op->reference }}</div>
            <div style="color:#333; margin-top:3px;">{{ $op->date_operation?->format('d/m/Y H:i:s') ?? now()->format('d/m/Y H:i:s') }}</div>
        </div>
        <div style="display:table-cell; width:33%; vertical-align:top; border-left:1.5px solid {{ $lineSoftColor }}; padding-left:10px;">
            <div style="color:#333; margin-bottom:1px; font-weight:bold;">Montant</div>
            <div style="font-weight:bold; font-size:13px; color:#111;">{{ $montantFmt }}</div>
            @if($op->type === 'CHANGE' && $op->montant_dest)
            <div style="color:#333;">→ {{ number_format((float)$op->montant_dest, 2, ',', ' ') }} {{ $op->devise_dest }}</div>
            @endif
            @if($op->compte_code && $montantTotalClient !== null)
            <div style="color:#333; margin-top:3px;">
                {{ $impactClientLabel }} :
                <strong>{{ number_format($montantTotalClient, 2, ',', ' ') }} {{ $op->devise_code }}</strong>
            </div>
            @endif
            @if($op->compte_code || $commission || $op->montant_commission_total !== null)
            <div style="color:#111; margin-top:2px;">
                Commission :
                <strong>{{ $commissionDisplay }}</strong>
            </div>
            @endif
            @if($op->compte_code && $soldeApresCompte !== null)
            <div style="color:#111; margin-top:2px;">
                Solde reel apres :
                <strong>{{ number_format($soldeApresCompte, 2, ',', ' ') }} {{ $op->devise_code }}</strong>
            </div>
            @endif
        </div>
        <div style="display:table-cell; width:34%; vertical-align:top; border-left:1.5px solid {{ $lineSoftColor }}; padding-left:10px;">
            @if($client)
            <div style="color:#333; margin-bottom:1px; font-weight:bold;">Client</div>
            <div style="font-weight:bold;">
                {{ strtoupper($client->nom ?? '') }} {{ strtoupper($client->postnom ?? '') }}
                {{ ucfirst(strtolower($client->prenom ?? '')) }}
            </div>
            @if($op->compte_code)
            <div style="font-size:9px; color:#333; font-family:'Courier New',monospace;">
                {{ $op->compte_code }}
            </div>
            @endif
            <div style="font-size:9px; color:#333;">
                Zone : <strong>{{ $zoneNom ?: '—' }}</strong>
            </div>
            @else
            <div style="color:#333; margin-bottom:1px; font-weight:bold;">Guichet</div>
            <div style="font-weight:bold;">{{ $guichet?->intitule ?? '—' }}</div>
            @endif
            <div style="margin-top:3px; color:#333;">Caissier : <strong>{{ $agentNom }}</strong></div>
        </div>
    </div>

    <div style="padding:0 14px 8px 14px; background:#fafafa;">
        <div style="display:table; width:100%;">
            <div style="display:table-cell; width:50%; padding-right:4px; vertical-align:top;">
                <div style="border:1.5px solid {{ $lineColor }}; border-radius:4px; padding:5px 8px; text-align:center;
                            font-size:9px; color:#222; height:44px;">
                    Signature du caissier
                    <div style="margin-top:14px; font-size:7.2px; color:#444; font-weight:bold;">
                        {{ $agentNom }}
                    </div>
                </div>
            </div>
            <div style="display:table-cell; width:50%; padding-left:4px; vertical-align:top;">
                <div style="border:1.5px solid {{ $lineColor }}; border-radius:4px; padding:5px 8px; text-align:center;
                            font-size:9px; color:#222; height:44px;">
                    Signature du client
                    <div style="margin-top:14px; font-size:7.2px; color:#444; font-weight:bold;">
                        {{ $clientNomSignature }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

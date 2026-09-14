<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Carte membre PVC — {{ $client->full_name }}</title>
<style>
    @page { margin: 0; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        width: 85.6mm;
        height: 53.9mm;
        margin: 0;
        padding: 0;
        background: url(data:image/png;base64,{{ $bgGradientBase64 ?? '' }}) no-repeat center;
        background-size: 100% 100%;
        page-break-inside: avoid;
    }
    table.carte {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        color: #ffffff;
        padding: 1mm 1.8mm;
        page-break-inside: avoid;
    }
    table.carte tr,
    table.carte td {
        page-break-inside: avoid;
    }

    /* ── Header : plaque logo + institution name + badge ─────── */
    .logo-plaque {
        background: #ffffff;
        border-radius: 1mm;
        padding: 0.5mm 1.2mm;
        display: inline-block;
        vertical-align: middle;
    }
    .logo-plaque img { height: 4.5mm; display: block; }
    .institution-name {
        display: inline-block;
        vertical-align: middle;
        margin-left: 1.5mm;
    }
    .institution-name .main-title {
        display: block;
        font-size: 6pt;
        font-weight: bold;
        letter-spacing: 0.3mm;
        color: #ffffff;
        text-transform: uppercase;
        line-height: 1.1;
    }
    .institution-name .sub-title {
        display: block;
        font-size: 4.2pt;
        font-weight: bold;
        color: rgba(255,255,255,0.95);
        letter-spacing: 0.1mm;
        margin-top: 0.2mm;
    }
    .badge-membre {
        background: #f2c94c;
        color: #1c1c1c;
        font-size: 5pt;
        font-weight: bold;
        letter-spacing: 0.4mm;
        padding: 0.6mm 1.5mm;
        border-radius: 2.5mm;
        display: inline-block;
    }

    /* ── Photo + infos + QR ───────────────────────────────────── */
    .photo-block {
        width: 15mm;
        height: 18mm;
        object-fit: cover;
        border-radius: 1mm;
        border: 0.4mm solid #ffffff;
    }
    .photo-placeholder {
        width: 15mm;
        height: 18mm;
        border-radius: 1mm;
        background: rgba(255,255,255,0.18);
        border: 0.4mm solid #ffffff;
    }
    .infos {
        font-size: 4.6pt;
        line-height: 1.3;
        padding: 0 1.8mm;
        vertical-align: middle;
    }
    .infos .nom {
        font-size: 7.5pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.15mm;
        color: #ffffff;
        margin-bottom: 0.5mm;
        /* Sécurité supplémentaire : $nomCarteMembre est déjà tronqué côté
           serveur, mais on empêche aussi tout retour à la ligne ici (DomPDF
           gère mal text-overflow/ellipsis, mieux vaut ne jamais en dépendre). */
        white-space: nowrap;
        overflow: hidden;
    }
    .infos .champ-label {
        color: rgba(255,255,255,0.85);
        font-size: 3.8pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.15mm;
        margin-top: 0.5mm;
    }
    .infos .champ-valeur {
        font-weight: bold;
        font-family: 'Courier', monospace;
        font-size: 5.4pt;
        color: #ffffff;
        letter-spacing: 0.1mm;
    }
    .qr-cell { width: 16mm; text-align: center; vertical-align: middle; }
    .qr-img {
        width: 14mm;
        height: 14mm;
        background: #ffffff;
        padding: 0.5mm;
        border-radius: 0.8mm;
    }
    .qr-cell small {
        display: block;
        font-size: 3.2pt;
        color: rgba(255,255,255,0.9);
        font-weight: bold;
        margin-top: 0.4mm;
    }

    /* ── Slogan + mentions ────────────────────────────────────── */
    .slogan-card {
        text-align: center;
        font-size: 5pt;
        font-weight: bold;
        letter-spacing: 0.4mm;
        color: #f2c94c;
        text-transform: uppercase;
        margin: 0.6mm 0 0.4mm;
    }
    .separateur {
        border-top: 0.15mm solid rgba(255,255,255,0.4);
        margin: 0.3mm 0;
    }
    .mentions {
        font-size: 3.6pt;
        line-height: 1.25;
        color: rgba(255,255,255,0.9);
        text-align: center;
        font-weight: bold;
    }

    /* ── Footer : signature + infos-footer ────────────────────── */
    .footer-signature {
        width: 22mm;
        text-align: center;
        font-size: 3.6pt;
        vertical-align: bottom;
    }
    .signature-image { height: 5mm; max-width: 18mm; display: inline-block; }
    .footer-signature .ligne {
        width: 14mm;
        border-top: 0.12mm solid rgba(255,255,255,0.7);
        margin: 0.3mm auto 0.3mm;
    }
    .footer-signature .role {
        color: rgba(255,255,255,0.95);
        letter-spacing: 0.15mm;
        font-weight: bold;
        font-size: 4pt;
    }
    .footer-signature .tag-exemple {
        display: block;
        font-size: 2.8pt;
        color: rgba(255,255,255,0.6);
        font-style: italic;
    }
    .infos-footer {
        text-align: right;
        font-size: 3.8pt;
        color: rgba(255,255,255,0.9);
        line-height: 1.3;
        font-weight: bold;
        vertical-align: bottom;
    }
    .infos-footer strong {
        font-family: 'Courier', monospace;
        font-size: 5pt;
        color: #ffffff;
        display: block;
        letter-spacing: 0.3mm;
        margin-top: 0.2mm;
    }
</style>
</head>
<body>
<table class="carte">
    <tr>
        <td style="padding: 1.8mm 3mm 0;">
            <table style="width:100%;">
                <tr>
                    <td style="vertical-align:middle; width:65%;">
                        <span class="logo-plaque">
                            @if($logoBase64 ?? false)
                                <img src="{{ $logoBase64 }}" alt="Logo">
                            @else
                                <img src="{{ public_path('dist/img/vrailogoeben-removebg-preview.png') }}" alt="Logo">
                            @endif
                        </span>
                        <span class="institution-name">
                            <span class="main-title">COOPEC EBEN</span>
                            <span class="sub-title">Coopérative d'Épargne et de Crédit</span>
                        </span>
                    </td>
                    <td style="text-align:right; vertical-align:middle;">
                        <span class="badge-membre">CARTE MEMBRE</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding: 1.2mm 3mm 0;">
            <table style="width:100%;">
                <tr>
                    <td style="width:17mm; vertical-align:middle;">
                        @if($photoBase64 ?? false)
                            <img class="photo-block" src="{{ $photoBase64 }}" alt="Photo">
                        @else
                            <div class="photo-placeholder"></div>
                        @endif
                    </td>
                    <td class="infos">
                        <div class="nom">{{ $nomCarteMembre ?? $client->full_name }}</div>
                        <table style="width:100%; margin-bottom: 0.2mm;">
                            <tr>
                                <td style="width:50%; vertical-align:top; padding-right:1.2mm;">
                                    <div class="champ-label">Code client</div>
                                    <div class="champ-valeur">{{ $client->matricule }}</div>
                                </td>
                                <td style="vertical-align:top;">
                                    <div class="champ-label">Téléphone</div>
                                    <div class="champ-valeur">{{ $client->telephone ?? '—' }}</div>
                                </td>
                            </tr>
                        </table>
                        <div class="champ-label">Agence / Zone</div>
                        <div class="champ-valeur">{{ $client->zone->nom ?? '—' }}</div>
                    </td>
                    <td class="qr-cell">
                        @if(!empty($qrSvgBase64))
                            <img class="qr-img" src="data:image/svg+xml;base64,{{ $qrSvgBase64 }}" alt="QR">
                            <small>Vérification</small>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding: 0.8mm 3mm 0;">
            <div class="separateur"></div>
            <div class="slogan-card">« Votre prospérité, notre priorité »</div>
            <div class="mentions">
                Carte personnelle et non cessible — présentation obligatoire pour toute opération en agence.
            </div>
        </td>
    </tr>

    <tr>
        <td style="padding: 0.4mm 3mm 1.2mm;">
            <table style="width:100%;">
                <tr>
                    <td class="footer-signature">
                        @if($signatureBase64 ?? false)
                            <img class="signature-image" src="{{ $signatureBase64 }}" alt="Signature">
                        @else
                            <div style="height:5mm;"></div>
                        @endif
                        <div class="ligne"></div>
                        <div class="role">Le Gérant</div>
                        @if(!($signatureBase64 ?? false))
                            <span class="tag-exemple">(à configurer)</span>
                        @endif
                    </td>
                    <td class="infos-footer">
                        Émise le {{ $carte->created_at->format('d/m/Y') }}
                        <strong>N° {{ str_pad((string) $carte->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

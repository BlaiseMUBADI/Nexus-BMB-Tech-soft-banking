<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('titre', 'Document')</title>
    <style>
        /* ── Page PDF (DomPDF) ── */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        /* ── Reset ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1a2e1e;
            background: #fff;
            margin: 16mm 15mm 24mm 15mm;
        }

        /* ── En-tête banque ── */
        .doc-header {
            border-bottom: 3px solid #1a7a4a;
            padding-bottom: 10px;
            margin-bottom: 18px;
            display: table;
            width: 100%;
        }
        .doc-header .logo-col {
            display: table-cell;
            width: 160px;
            vertical-align: middle;
        }
        .doc-header .bank-col {
            display: table-cell;
            vertical-align: middle;
            padding-left: 14px;
        }
        .doc-header .right-col {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 180px;
        }
        .bank-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a7a4a;
            letter-spacing: 1px;
        }
        .bank-tagline {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }
        .doc-date {
            font-size: 9px;
            color: #555;
        }
        .doc-logo-placeholder {
            width: 80px;
            height: 40px;
            background: #1a7a4a;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            line-height: 40px;
            border-radius: 4px;
        }

        /* ── Titre document ── */
        .doc-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #1a7a4a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 14px 0 16px 0;
            padding: 8px 0;
            border-top: 1px solid #b2dfcb;
            border-bottom: 1px solid #b2dfcb;
        }

        /* ── Sections ── */
        .section {
            margin-bottom: 14px;
        }
        .section-title {
            background: #1a7a4a;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 10px;
            margin-bottom: 0;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.info-table td {
            border: 1px solid #c3ddd0;
            padding: 6px 10px;
            vertical-align: top;
        }
        table.info-table td.label {
            background: #eef7f2;
            font-weight: bold;
            width: 38%;
            color: #1a7a4a;
        }

        /* ── IBAN ── */
        .iban-box {
            background: #eef7f2;
            border: 2px solid #1a7a4a;
            border-radius: 4px;
            padding: 10px 14px;
            text-align: center;
            margin: 12px 0;
        }
        .iban-label {
            font-size: 9px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .iban-value {
            font-family: DejaVu Sans Mono, Courier New, monospace;
            font-size: 12px;
            font-weight: bold;
            color: #1a7a4a;
            letter-spacing: 1.5px;
            margin-top: 3px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .bic-value {
            font-family: Courier New, monospace;
            font-size: 12px;
            color: #444;
            margin-top: 4px;
        }

        /* ── Pied de page ── */
        .doc-footer {
            position: fixed;
            bottom: 5mm;
            left: 15mm;
            right: 15mm;
            border-top: 1px solid #b2dfcb;
            padding: 5px 0;
            text-align: center;
            font-size: 7.5px;
            color: #777;
            word-wrap: break-word;
            white-space: normal;
        }
        .doc-footer .confidential {
            color: #c00;
            font-weight: bold;
        }

        /* ── Utilitaires ── */
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-muted  { color: #888; }
        .two-col { display: table; width: 100%; }
        .two-col .col { display: table-cell; vertical-align: top; padding-right: 12px; }
        .two-col .col:last-child { padding-right: 0; padding-left: 12px; }
    </style>
</head>
<body>

    {{-- ── En-tête banque ── --}}
    @php
        /** @var \App\Models\User|null $printedByUser */
        $printedByUser = auth()->user();

        $imprimeParNom = $imprimeParNom ?? null;
        $imprimeParProfil = $imprimeParProfil ?? null;

        if (empty($imprimeParNom) && $printedByUser) {
            $printedByUser->loadMissing('agent');

            if ($printedByUser->agent) {
                $a = $printedByUser->agent;
                $imprimeParNom = trim(($a->prenom ?? '') . ' ' . ($a->nom ?? ''));
            }

            if (empty($imprimeParNom)) {
                $imprimeParNom = $printedByUser->name ?? $printedByUser->agent_matricule ?? null;
            }
        }

        if (empty($imprimeParProfil) && $printedByUser && method_exists($printedByUser, 'getRoleCodes')) {
            $roles = (array) $printedByUser->getRoleCodes();
            $imprimeParProfil = $roles[0] ?? null;
        }

        $logoPath = public_path('dist/img/vrailogoeben-removebg-preview.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;
    @endphp
    <div class="doc-header">
        <div class="logo-col">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="EBEN" style="height:55px; max-width:130px;">
            @else
                <div class="doc-logo-placeholder">EBEN</div>
            @endif
        </div>
        <div class="bank-col">
            <div class="bank-name">COOPEC EBEN</div>
            <div class="bank-tagline" style="font-weight:bold; color:#444;">Coopérative d'Épargne et de Crédit EBEN</div>
            <div class="bank-tagline">Siège social : Avenue LULUA N° 4, Q/Malandji, C/KANANGA</div>
            <div class="bank-tagline">Tél : (+243) 995 977 523 &nbsp;/&nbsp; 852 924 454</div>
            <div class="bank-tagline">Email : contact@coopeceben.com &nbsp;|&nbsp; Web : www.coopeceben.com</div>
        </div>
        <div class="right-col">
            <div class="doc-date">
                Édité le {{ \Carbon\Carbon::now()->format('d/m/Y') }}<br>
                à {{ \Carbon\Carbon::now()->format('H:i') }}
                @if(!empty($imprimeParNom))
                    <br>Imprimé par : {{ $imprimeParNom }}
                    @if(!empty($imprimeParProfil))
                        <br>Profil : {{ $imprimeParProfil }}
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ── Titre ── --}}
    <div class="doc-title">@yield('titre', 'Document')</div>

    {{-- ── Contenu ── --}}
    @yield('contenu')

    {{-- ── Pied de page ── --}}
    <div class="doc-footer">
        <span class="confidential">DOCUMENT CONFIDENTIEL</span>
        &nbsp;—&nbsp; Coopérative d'Épargne et de Crédit EBEN &nbsp;—&nbsp;
        contact@coopeceben.com &nbsp;|&nbsp; (+243) 995 977 523 &nbsp;|&nbsp; www.coopeceben.com &nbsp;—&nbsp;
        Toute reproduction non autorisée est interdite.
    </div>

</body>
</html>

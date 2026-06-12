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
            line-height: 1.2;
            color: #111;
            background: #fff;
            margin: 12mm 12mm 18mm 12mm;
        }

        /* ── En-tête banque ── */
        .doc-header {
            border-bottom: 3px solid #1a7a4a;
            padding-bottom: 7px;
            margin-bottom: 12px;
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
            font-size: 14px;
            font-weight: bold;
            color: #1a7a4a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 10px 0 12px 0;
            padding: 6px 0;
            border-top: 1px solid #b2dfcb;
            border-bottom: 1px solid #b2dfcb;
        }

        /* ── Sections ── */
        .section {
            margin-bottom: 9px;
        }
        .section-title {
            background: #1a7a4a;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 8px;
            margin-bottom: 0;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.info-table th {
            border: 2.5px solid #333333;
            padding: 5px 7px;
            vertical-align: middle;
            font-weight: bold;
            color: #fff;
            text-align: left;
        }
        table.info-table td {
            border: 2px solid #333333;
            padding: 4px 7px;
            vertical-align: top;
            color: #111;
        }
        table.info-table td.label {
            background: #eef7f2;
            font-weight: bold;
            width: 38%;
            color: #1a7a4a;
            border: 2px solid #333333;
        }
        table.info-table tbody tr {
            border-bottom: 2px solid #333333;
        }
        table.info-table tfoot tr {
            border-top: 3px solid #333333;
            font-weight: bold;
        }

        /* ── IBAN ── */
        .iban-box {
            background: #eef7f2;
            border: 2px solid #1a7a4a;
            border-radius: 4px;
            padding: 7px 10px;
            text-align: center;
            margin: 8px 0;
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
            padding: 3px 0;
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

        /* ── Tableau des mouvements (Releve de compte) ── */
        table.movement-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 8px;
        }
        table.movement-table thead th {
            background: #1a7a4a;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1.5px solid #333;
            vertical-align: middle;
        }
        table.movement-table tbody td {
            padding: 4px 6px;
            border: 1.5px solid #ccc;
            vertical-align: middle;
            color: #111;
        }
        table.movement-table tbody tr.opening-row,
        table.movement-table tbody tr.closing-row {
            background: #f0f7f0;
        }
        table.movement-table tbody tr.opening-row td,
        table.movement-table tbody tr.closing-row td {
            font-weight: bold;
            border: 2px solid #1a7a4a;
        }
        table.movement-table tbody tr.movement-row {
            background: #fff;
        }
        table.movement-table tbody tr.movement-row:nth-child(even) {
            background: #fafafa;
        }
        table.movement-table tbody tr.movement-row.type-frais {
            background: #fff8e1;
        }
        table.movement-table tbody tr.movement-row.type-caution {
            background: #fff3e0;
        }
        table.movement-table tbody tr.movement-row.type-deblocage {
            background: #e8f5e9;
        }
        table.movement-table tbody tr.movement-row.type-remboursement {
            background: #e3f2fd;
        }
        table.movement-table tbody tr.movement-row.type-restitution {
            background: #e8f5e9;
        }
        table.movement-table tbody td.debit {
            color: #c62828;
            font-weight: bold;
        }
        table.movement-table tbody td.credit {
            color: #2e7d32;
            font-weight: bold;
        }
        table.movement-table tbody td.solde {
            color: #1a7a4a;
            font-weight: bold;
        }

        /* ── Badges type mouvement ── */
        .badge-frais,
        .badge-caution,
        .badge-deblocage,
        .badge-remboursement,
        .badge-restitution {
            display: inline-block;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 1px 4px;
            border-radius: 2px;
            margin-right: 4px;
            vertical-align: middle;
        }
        .badge-frais { background: #fff8e1; color: #f57f17; border: 1px solid #f57f17; }
        .badge-caution { background: #fff3e0; color: #e65100; border: 1px solid #e65100; }
        .badge-deblocage { background: #e8f5e9; color: #2e7d32; border: 1px solid #2e7d32; }
        .badge-remboursement { background: #e3f2fd; color: #1565c0; border: 1px solid #1565c0; }
        .badge-restitution { background: #e8f5e9; color: #2e7d32; border: 1px solid #2e7d32; }

        /* ── Pied de page releve ── */
        .footer-info {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .footer-info .footer-col {
            display: table-cell;
            width: 33.33%;
            padding: 6px 8px;
            border: 1.5px solid #ccc;
            vertical-align: top;
            font-size: 10px;
        }
        .footer-info .footer-col strong {
            color: #1a7a4a;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
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

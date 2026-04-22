<?php

declare(strict_types=1);

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Facade;

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Facade::setFacadeApplication($app);

$invoiceNumber = 'LIC-NBTB-' . date('Ymd-His');
$deliveryDate = '13/03/2026';
$invoiceDate = '16/03/2026';
$dueDate = date('d/m/Y', strtotime('+7 days'));

$developerName = 'Blaise MUBADI Bakajika';
$developerPhone = '+243992463511';
$developerEmail = 'blaisemubadibakajika@uka.ac.cd';

$clientName = 'COOPEC EBEN – Coopérative d\'Épargne et de Crédit EBEN';
$clientPhone = '(+243) 995 977 523 / 852 924 454';
$clientEmail = 'contact@coopaeben.com';
$clientAddress = 'Avenue LULUA N° 4, Q/Malandji, C/KANANGA – RD Congo';

$softwareName = 'NEXUS BMB Tech-Soft Banking';
$licenceType = 'Licence perpétuelle d\'utilisation – logiciel de gestion bancaire/COOPEC';

// Signature image en base64
$signaturePath = __DIR__ . '/../../public/factures/Ma signature.png';
$signatureBase64 = '';
if (file_exists($signaturePath)) {
    $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath));
}

$amountPhase1 = 1000.00;
$amountPhase2 = 800.00;
$amountDomain = 200.00;
$phase1PaidAt = '15/03/2026';
$totalLicence = $amountPhase1 + $amountPhase2;
$total = $totalLicence + $amountDomain;
$alreadyPaid = $amountPhase1 + $amountDomain;
$remaining = $amountPhase2;

$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
.header { border-bottom: 2px solid #1a7a4a; padding-bottom: 10px; margin-bottom: 16px; }
.title { font-size: 18px; color: #1a7a4a; font-weight: bold; margin-bottom: 4px; }
.subtitle { font-size: 13px; color: #1a7a4a; margin-bottom: 2px; }
.small { font-size: 11px; color: #444; }
.box { border: 1px solid #ddd; padding: 10px; margin-top: 10px; }
.table { width: 100%; border-collapse: collapse; margin-top: 12px; }
.table th, .table td { border: 1px solid #ccc; padding: 8px; }
.table th { background: #f2f7f4; }
.right { text-align: right; }
.total { font-weight: bold; background: #f8f8f8; }
.footer { margin-top: 20px; font-size: 11px; color: #444; }
.badge-paid { color: #0b7a2a; font-weight: bold; }
.badge-due { color: #b26a00; font-weight: bold; }
</style>
</head>
<body>
<div class="header">
    <div class="title">FACTURE DE LICENCE LOGICIELLE</div>
    <div class="subtitle">' . htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') . '</div>
    <div class="small">N° Facture : ' . $invoiceNumber . '</div>
    <div class="small">Date de livraison : ' . $deliveryDate . '</div>
    <div class="small">Date d\'émission : ' . $invoiceDate . '</div>
</div>

<div class="box">
    <strong>Émetteur de la facture (Développeur) :</strong> ' . htmlspecialchars($developerName, ENT_QUOTES, 'UTF-8') . '<br>
    <strong>Téléphone :</strong> ' . htmlspecialchars($developerPhone, ENT_QUOTES, 'UTF-8') . '&nbsp;&nbsp;
    <strong>Email :</strong> ' . htmlspecialchars($developerEmail, ENT_QUOTES, 'UTF-8') . '
</div>

<div class="box">
    <strong>Client :</strong> ' . htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8') . '<br>
    <strong>Adresse :</strong> ' . htmlspecialchars($clientAddress, ENT_QUOTES, 'UTF-8') . '<br>
    <strong>Téléphone :</strong> ' . htmlspecialchars($clientPhone, ENT_QUOTES, 'UTF-8') . '<br>
    <strong>Email :</strong> ' . htmlspecialchars($clientEmail, ENT_QUOTES, 'UTF-8') . '<br>
    <strong>Objet :</strong> ' . htmlspecialchars($licenceType, ENT_QUOTES, 'UTF-8') . '
</div>

<table class="table">
    <thead>
        <tr>
            <th style="width: 55%;">Description</th>
            <th style="width: 15%;">Statut</th>
            <th style="width: 15%;" class="right">Montant (USD)</th>
            <th style="width: 15%;" class="right">À payer (USD)</th>
        </tr>
    </thead>
    <tbody>
        <tr style="background:#fff8ee;">
            <td>Achat nom de domaine (1 an)</td>
            <td class="badge-paid">Payé le ' . $phase1PaidAt . '</td>
            <td class="right">' . number_format($amountDomain, 2, ',', ' ') . '</td>
            <td class="right">0,00</td>
        </tr>
        <tr>
            <td>1ère tranche de licence – Modules Caisse, Trésorerie et Ressources Humaines</td>
            <td class="badge-paid">Payé le ' . $phase1PaidAt . '</td>
            <td class="right">' . number_format($amountPhase1, 2, ',', ' ') . '</td>
            <td class="right">0,00</td>
        </tr>
        <tr>
            <td>2ème tranche de licence – Modules Crédit et Comptabilité (à régler avant présentation)</td>
            <td class="badge-due">En attente</td>
            <td class="right">' . number_format($amountPhase2, 2, ',', ' ') . '</td>
            <td class="right">' . number_format($amountPhase2, 2, ',', ' ') . '</td>
        </tr>
        <tr class="total">
            <td colspan="2" class="right">Total général</td>
            <td class="right">' . number_format($total, 2, ',', ' ') . '</td>
            <td class="right">' . number_format($remaining, 2, ',', ' ') . '</td>
        </tr>
    </tbody>
</table>

<div class="box">
    <div><strong>Déjà payé :</strong> ' . number_format($alreadyPaid, 2, ',', ' ') . ' USD (1ère tranche + nom de domaine, réglés le ' . $phase1PaidAt . ')</div>
    <div><strong>Reste à payer :</strong> ' . number_format($remaining, 2, ',', ' ') . ' USD (2ème tranche)</div>
    <div><strong>Échéance du solde :</strong> Avant la présentation du logiciel (indicatif: ' . $dueDate . ')</div>
</div>

<div class="footer">
    <strong>Conditions de licence :</strong> Licence d\'utilisation perpétuelle, non exclusive, non transférable, limitée à l\'entité licenciée (COOPEC EBEN).<br>
    Le code source reste la propriété exclusive du concédant. Toute reproduction ou redistribution est interdite sans accord écrit.<br>
    Facture émise par un prestataire individuel indépendant – TVA non applicable.
</div>

<div style="margin-top: 24px; text-align: right; padding-right: 20px;">
    <div style="font-size: 11px; color: #333; margin-bottom: 4px;">Fait à Kananga, le ' . $invoiceDate . '</div>
    ' . ($signatureBase64 ? '<img src="' . $signatureBase64 . '" style="height:95px; max-width:240px; display:block; margin-left:auto;">' : '<div style="height:95px;"></div>') . '
    <div style="border-top:1px solid #555; width:240px; margin-left:auto; padding-top:4px; font-size:11px; text-align:center;">
        <strong>' . htmlspecialchars($developerName, ENT_QUOTES, 'UTF-8') . '</strong>
    </div>
</div>
</body>
</html>';

$outputDir = __DIR__ . '/../../public/factures';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$outputFile = $outputDir . '/licence_logicielle_NBTB_' . date('Ymd_His') . '.pdf';

Pdf::loadHTML($html)
    ->setPaper('A4', 'portrait')
    ->save($outputFile);

echo 'PDF_GENERE=' . $outputFile . PHP_EOL;

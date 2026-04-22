<?php

declare(strict_types=1);

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Facade;

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Facade::setFacadeApplication($app);

// ─── Expéditeur ──────────────────────────────────────────────
$senderName    = 'Blaise MUBADI Bakajika';
$senderTitle   = 'Concepteur – Développeur logiciel indépendant';
$senderPhone   = '+243 992 463 511';
$senderEmail   = 'blaisemubadibakajika@uka.ac.cd';
$senderCity    = 'Kananga';

// ─── Destinataire ────────────────────────────────────────────
$destCivilite  = 'Monsieur le Président';
$destOrg       = 'COOPEC EBEN';
$destAddress   = 'Avenue LULUA N° 4, Q/Malandji';
$destCity      = 'KANANGA';

// ─── Lettre ──────────────────────────────────────────────────
$letterRef     = 'Réf. : BMB/NBTB/2026/003';
$letterDate    = 'Kananga, le 27 mars 2026';
$softwareName  = 'NEXUS BMB Tech-Soft Banking';

// ─── Signature ───────────────────────────────────────────────
$signaturePath   = __DIR__ . '/Ma signature.png';
$signatureBase64 = '';
if (file_exists($signaturePath)) {
    $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath));
}

$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
@page {
    size: A4 portrait;
    margin: 25mm;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 12px;
    color: #111;
    line-height: 1.60;
    margin: 0;
}

/* ── Barre verte en haut ── */
.top-bar {
    background: #1a5e35;
    height: 6px;
    width: 100%;
    margin-bottom: 18px;
}

/* ── En-tête bicolonne ── */
.header-wrap {
    display: table;
    width: 100%;
    margin-bottom: 24px;
}
.col-sender {
    display: table-cell;
    width: 50%;
    vertical-align: top;
    font-size: 11px;
    color: #333;
    border-right: 1px solid #ccc;
    padding-right: 14px;
}
.col-dest {
    display: table-cell;
    width: 50%;
    vertical-align: top;
    padding-left: 18px;
    font-size: 12px;
}
.sender-name {
    font-size: 14px;
    font-weight: bold;
    color: #1a5e35;
    margin-bottom: 2px;
}
.sender-sub {
    font-size: 10px;
    color: #555;
    margin-bottom: 6px;
    font-style: italic;
}
.dest-label {
    font-size: 10px;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.dest-name {
    font-weight: bold;
    font-size: 12px;
}

/* ── Références / date ── */
.meta-wrap {
    display: table;
    width: 100%;
    margin-bottom: 18px;
    border-top: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
    padding: 7px 0;
}
.meta-left  { display: table-cell; width: 55%; vertical-align: middle; font-size: 11px; color: #444; }
.meta-right { display: table-cell; width: 45%; text-align: right; vertical-align: middle; font-size: 11px; color: #444; }

/* ── Objet ── */
.objet-wrap {
    margin-bottom: 20px;
    background: #f4f9f6;
    border-left: 4px solid #1a5e35;
    padding: 7px 12px;
    font-size: 12px;
}
.objet-label { font-weight: bold; color: #1a5e35; }

/* ── Corps ── */
.salut { margin-bottom: 14px; }
p { margin-bottom: 10px; text-align: justify; }

.article-title {
    font-weight: bold;
    color: #1a5e35;
    font-size: 12px;
    margin: 16px 0 5px 0;
    border-bottom: 1px dotted #1a5e35;
    padding-bottom: 2px;
}
.article-body { margin-left: 8px; margin-bottom: 8px; }
.art-point { margin-bottom: 7px; }
.art-label { font-weight: bold; }

/* ── Ligne de séparation ── */
.rule { border: none; border-top: 1px solid #ccc; margin: 18px 0; }

/* ── Signature ── */
.closing { margin-bottom: 14px; text-align: justify; }
.sign-wrap { margin-top: 24px; text-align: right; padding-right: 20px; }
.sign-place { font-size: 11px; color: #333; margin-bottom: 4px; }
.sign-name {
    border-top: 1px solid #555;
    width: 240px;
    margin-left: auto;
    padding-top: 4px;
    font-size: 11px;
    text-align: center;
}

/* ── Pied de page ── */
.footer {
    margin-top: 30px;
    border-top: 1px solid #ccc;
    padding-top: 6px;
    font-size: 9px;
    color: #888;
    text-align: center;
}
.bottom-bar {
    background: #1a5e35;
    height: 4px;
    width: 100%;
    margin-top: 10px;
}
</style>
</head>
<body>

<div class="top-bar"></div>

<!-- EN-TÊTE -->
<div class="header-wrap">
    <div class="col-sender">
        <div class="sender-name">' . htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8') . '</div>
        <div class="sender-sub">' . htmlspecialchars($senderTitle, ENT_QUOTES, 'UTF-8') . '</div>
        Tél&nbsp;: ' . htmlspecialchars($senderPhone, ENT_QUOTES, 'UTF-8') . '<br>
        Email&nbsp;: ' . htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8') . '<br>
        Ville&nbsp;: ' . htmlspecialchars($senderCity, ENT_QUOTES, 'UTF-8') . ' – RD Congo
    </div>
    <div class="col-dest">
        <div class="dest-label">À l\'attention de</div>
        <div class="dest-name">' . htmlspecialchars($destCivilite, ENT_QUOTES, 'UTF-8') . '</div>
        <strong>' . htmlspecialchars($destOrg, ENT_QUOTES, 'UTF-8') . '</strong><br>
        ' . htmlspecialchars($destAddress, ENT_QUOTES, 'UTF-8') . '<br>
        ' . htmlspecialchars($destCity, ENT_QUOTES, 'UTF-8') . ' – RD Congo
    </div>
</div>

<!-- RÉFÉRENCES -->
<div class="meta-wrap">
    <div class="meta-left">
        <strong>' . htmlspecialchars($letterRef, ENT_QUOTES, 'UTF-8') . '</strong>
    </div>
    <div class="meta-right">
        ' . htmlspecialchars($letterDate, ENT_QUOTES, 'UTF-8') . '
    </div>
</div>

<!-- OBJET -->
<div class="objet-wrap">
    <span class="objet-label">Objet&nbsp;:</span>
    Observations et propositions d\'amendement du Contrat de sécurité des données
    relatif au logiciel <em>' . htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') . '</em>
</div>

<!-- CORPS -->
<p class="salut">Monsieur le Président,</p>

<p>
    J\'ai l\'honneur d\'accuser réception du contrat de sécurité des données encadrant
    notre collaboration pour le développement et la mise en production du logiciel de gestion
    bancaire <strong>' . htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') . '</strong>.
    Après lecture attentive de l\'ensemble des clauses, je me permets de vous soumettre,
    par la présente, les observations et propositions d\'amendements ci-après, dans
    l\'intérêt commun des deux parties. Il est entendu que ce contrat doit fixer de manière
    claire et équilibrée les droits, devoirs et obligations réciproques de la COOPEC EBEN
    et du concepteur du logiciel.
</p>

<div class="article-title">Article 12 – Responsabilité</div>
<div class="article-body">
    <div class="art-point">
        <span class="art-label">a) Périmètre de responsabilité :</span>
        La responsabilité du développeur doit être limitée aux défauts résultant
        directement d\'une erreur de conception de sa part, à l\'exclusion des dommages
        causés par une mauvaise utilisation du logiciel, une modification non autorisée
        du code ou une défaillance d\'infrastructure gérée par votre institution.
    </div>
    <div class="art-point">
        <span class="art-label">b) Limitation financière :</span>
        La responsabilité totale du prestataire sera plafonnée au montant effectivement
        perçu au titre du présent contrat, à l\'exclusion de tout préjudice indirect.
    </div>
    <div class="art-point">
        <span class="art-label">c) Force majeure :</span>
        Aucune des parties ne pourra être tenue responsable en cas de force majeure
        (catastrophe naturelle, coupure prolongée d\'électricité, acte de guerre,
        décision gouvernementale ou tout événement imprévisible et irrésistible).
    </div>
</div>

<div class="article-title">Article 13 – Sous-traitance</div>
<div class="article-body">
    <div class="art-point">
        <span class="art-label">a) Sous-traitance de spécialité :</span>
        Le recours à des prestataires techniques spécialisés (audit de sécurité,
        hébergement certifié, tests) sera possible sans autorisation préalable,
        sous réserve que ces sous-traitants soient soumis aux mêmes obligations de
        confidentialité et que le développeur reste seul interlocuteur de la COOPEC EBEN.
    </div>
    <div class="art-point">
        <span class="art-label">b) Sous-traitance principale :</span>
        Toute délégation substantielle restera soumise à l\'approbation écrite préalable
        de votre institution.
    </div>
</div>

<div class="article-title">Article 14 – Propriété des données et propriété intellectuelle</div>
<div class="article-body">
    <div class="art-point">
        La propriété exclusive des données de la COOPEC EBEN est pleinement reconnue.
        Il convient toutefois d\'y ajouter explicitement la clause suivante :
        le logiciel <em>' . htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') . '</em>,
        son code source, son architecture et sa documentation technique constituent
        une œuvre intellectuelle protégée dont la propriété demeure exclusivement celle
        du développeur. La COOPEC EBEN bénéficie d\'une <strong>licence d\'utilisation
        à durée indéterminée, non exclusive et non transférable</strong>, sans que cela ne lui
        confère aucun droit de reproduction, de modification ou de cession à des tiers.
    </div>
</div>

<div class="article-title">Article 15 – Réversibilité en fin de contrat</div>
<div class="article-body">
    <div class="art-point">
        <span class="art-label">a) Données :</span>
        L\'ensemble des données opérationnelles sera restitué dans un format exploitable
        (export SQL ou CSV) dans un délai raisonnable suivant la fin du contrat.
    </div>
    <div class="art-point">
        <span class="art-label">b) Logiciel :</span>
        Étant propriété intellectuelle du développeur, le logiciel ne pourra être
        supprimé ni cédé. La licence d\'utilisation accordée à la COOPEC EBEN est
        consentie pour une durée indéterminée, sous réserve du respect des obligations
        contractuelles convenues entre les deux parties.
    </div>
    <div class="art-point">
        <span class="art-label">c) Maintenance et évolutions :</span>
        Toute mise à jour, évolution fonctionnelle, module complémentaire ou prestation
        de maintenance au-delà de la garantie initiale restera à la charge de la COOPEC EBEN
        et fera l\'objet, le cas échéant, d\'une facturation ou d\'un devis séparé.
    </div>
    <div class="art-point">
        <span class="art-label">d) Période de garantie :</span>
        Une garantie de correction des défauts techniques est accordée pour une durée
        de <strong>trois (3) mois</strong> à compter de la date de livraison.
        Au-delà, toute intervention sera facturée.
    </div>
</div>

<div class="article-title">Article 17 – Sanctions et règlement des litiges</div>
<div class="article-body">
    <div class="art-point">
        <span class="art-label">a) Proportionnalité :</span>
        Toute sanction sera proportionnelle à la gravité du manquement et ne pourra
        être appliquée qu\'après notification écrite et expiration d\'un délai de mise
        en demeure de <strong>quinze (15) jours</strong>.
    </div>
    <div class="art-point">
        <span class="art-label">b) Médiation préalable :</span>
        Avant tout recours judiciaire, les parties s\'engagent à rechercher une solution
        amiable dans un délai de <strong>trente (30) jours</strong> à compter de la
        naissance du litige.
    </div>
    <div class="art-point">
        <span class="art-label">c) Juridiction compétente :</span>
        À défaut de résolution amiable, tout différend sera soumis aux juridictions
        compétentes du ressort de Kananga, selon le droit congolais applicable.
    </div>
</div>

<hr class="rule">

<p class="closing">
    Ces propositions ne remettent aucunement en cause la volonté de collaboration loyale
    et durable qui caractérise nos rapports professionnels. Elles visent à établir un cadre
    contractuel équilibré, protecteur tant pour votre institution que pour moi-même en tant
    que concepteur du logiciel. Je reste entièrement disponible pour un échange en vue de
    finaliser les amendements nécessaires.
</p>

<p>
    Je vous prie d\'agréer, Monsieur le Président, l\'expression de ma haute considération.
</p>

<!-- SIGNATURE -->
<div class="sign-wrap">
    <div class="sign-place">' . htmlspecialchars($letterDate, ENT_QUOTES, 'UTF-8') . '</div>
    ' . ($signatureBase64
        ? '<img src="' . $signatureBase64 . '" style="height:95px; max-width:240px; display:block; margin-left:auto;">'
        : '<div style="height:95px;"></div>') . '
    <div class="sign-name">
        <strong>' . htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8') . '</strong>
    </div>
</div>

<!-- PIED DE PAGE -->
<div class="footer">
    ' . htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8') . ' &nbsp;|&nbsp;
    ' . htmlspecialchars($senderPhone, ENT_QUOTES, 'UTF-8') . ' &nbsp;|&nbsp;
    ' . htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8') . ' &nbsp;|&nbsp;
    Kananga – RD Congo &nbsp;|&nbsp; ' . htmlspecialchars($letterRef, ENT_QUOTES, 'UTF-8') . '
</div>
<div class="bottom-bar"></div>

</body>
</html>';

$outputDir = __DIR__ . '/../Contrat';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$outputFile = $outputDir . '/lettre_administrative_NBTB_' . date('Ymd_His') . '.pdf';

Pdf::loadHTML($html)
    ->setPaper('A4', 'portrait')
    ->save($outputFile);

echo 'PDF_GENERE=' . $outputFile . PHP_EOL;

<?php

declare(strict_types=1);

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Facade;

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Facade::setFacadeApplication($app);

$senderName    = 'Blaise MUBADI Bakajika';
$senderTitle   = 'Concepteur – Développeur logiciel';
$senderPhone   = '+243 992 463 511';
$senderEmail   = 'blaisemubadibakajika@uka.ac.cd';

$clientTitle   = 'Monsieur le Président';
$clientOrg     = 'COOPEC EBEN';
$clientAddress = 'Avenue LULUA N° 4, Q/Malandji, C/KANANGA';

$letterDate    = 'Kananga, le 27 mars 2026';
$softwareName  = 'NEXUS BMB Tech-Soft Banking';

// Signature
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
@page       { size: A4 portrait; margin: 25mm; }
body        { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; line-height: 1.7; margin: 0; }
.header-sender { font-size: 11px; color: #333; margin-bottom: 16px; }
.header-dest   { margin-bottom: 20px; font-size: 12px; }
.date-place    { text-align: right; font-size: 11px; color: #444; margin-bottom: 20px; }
.object        { font-weight: bold; text-decoration: underline; margin-bottom: 20px; font-size: 12px; }
.salutation    { margin-bottom: 14px; }
p              { margin: 0 0 10px 0; }
.article       { margin: 14px 0 6px 0; font-weight: bold; color: #1a4a7a; font-size: 12px; }
.article-body  { margin-left: 10px; margin-bottom: 10px; }
.closing       { margin-top: 20px; }
.signature-block { margin-top: 24px; text-align: right; padding-right: 20px; }
.rule          { border-top: 1px solid #999; margin: 20px 0 10px; }
strong         { color: #111; }
</style>
</head>
<body>

<div class="header-sender">
    <strong>' . htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8') . '</strong><br>
    ' . htmlspecialchars($senderTitle, ENT_QUOTES, 'UTF-8') . '<br>
    Tél : ' . htmlspecialchars($senderPhone, ENT_QUOTES, 'UTF-8') . '<br>
    Email : ' . htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8') . '
</div>

<div class="header-dest">
    <strong>' . htmlspecialchars($clientTitle, ENT_QUOTES, 'UTF-8') . '</strong><br>
    ' . htmlspecialchars($clientOrg, ENT_QUOTES, 'UTF-8') . '<br>
    ' . htmlspecialchars($clientAddress, ENT_QUOTES, 'UTF-8') . '
</div>

<div class="date-place">' . htmlspecialchars($letterDate, ENT_QUOTES, 'UTF-8') . '</div>

<div class="object">
    Objet : Observations et propositions d\'amendement du Contrat de sécurité des données –
    Logiciel ' . htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') . '
</div>

<div class="salutation">Monsieur le Président,</div>

<p>
    J\'ai l\'honneur d\'accuser réception du contrat de sécurité des données encadrant notre
    collaboration pour le développement et la mise en production du logiciel de gestion
    bancaire <strong>' . htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') . '</strong>.
    Après lecture attentive et réflexion approfondie, je souhaite formuler les observations
    et propositions d\'amendements ci-après, dans l\'intérêt commun de la COOPEC EBEN et
    de la pérennité du logiciel. Ce contrat doit, à cet effet, définir avec précision les
    droits, devoirs et obligations réciproques des deux parties.
</p>

<div class="article">Article 12 – Responsabilité</div>
<div class="article-body">
<p>
    La rédaction actuelle semble imputer au prestataire une responsabilité générale et
    illimitée en cas de faille ou de dommage. Je propose les précisions suivantes :
</p>
<p>
    <strong>(a) Périmètre de responsabilité :</strong> La responsabilité du développeur ne
    peut couvrir que les défauts ou failles résultant directement d\'une erreur de conception
    ou de développement de sa part. Elle exclut tout dommage causé par une mauvaise
    utilisation du logiciel par les utilisateurs, une modification non autorisée du code,
    ou une défaillance d\'infrastructure (serveur, réseau, courant électrique) gérée
    par la COOPEC EBEN.
</p>
<p>
    <strong>(b) Limitation financière :</strong> En toute hypothèse, la responsabilité
    totale du prestataire est plafonnée au montant effectivement perçu au titre du
    présent contrat, à l\'exclusion de tout préjudice indirect ou immatériel.
</p>
<p>
    <strong>(c) Force majeure :</strong> Aucune des parties ne saurait être tenue responsable
    en cas de force majeure (catastrophe naturelle, coupure prolongée d\'électricité,
    acte de guerre ou d\'insurrection, décision gouvernementale, etc.).
</p>
</div>

<div class="article">Article 13 – Sous-traitance</div>
<div class="article-body">
<p>
    L\'exigence d\'autorisation préalable pour toute sous-traitance est compréhensible.
    Je propose de l\'assouplir par une distinction entre :
</p>
<p>
    <strong>(a) Sous-traitance de spécialité :</strong> Le recours à des prestataires
    techniques spécialisés (audit de sécurité, tests d\'intrusion, hébergement certifié)
    pourra se faire sans autorisation préalable, à condition que le développeur reste
    le seul interlocuteur de la COOPEC EBEN et que ces sous-traitants soient soumis aux
    mêmes exigences de confidentialité.
</p>
<p>
    <strong>(b) Sous-traitance principale :</strong> Toute délégation substantielle du
    développement restera soumise à l\'approbation écrite de la COOPEC EBEN.
</p>
</div>

<div class="article">Article 14 – Propriété des données et propriété intellectuelle</div>
<div class="article-body">
<p>
    La propriété exclusive des données de la COOPEC EBEN par cette dernière est pleinement
    acceptée. Je propose toutefois d\'ajouter explicitement la clause ci-après :
</p>
<p>
    <strong>Propriété intellectuelle du logiciel :</strong> Le logiciel
    <em>' . htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') . '</em>, son code source,
    son architecture, ses algorithmes et sa documentation technique constituent une œuvre
    intellectuelle protégée, dont la propriété reste exclusivement celle du développeur,
    conformément aux dispositions légales applicables en matière de droit d\'auteur.
    La COOPEC EBEN bénéficie d\'une <strong>licence d\'utilisation à durée indéterminée,
    non exclusive et non transférable</strong> du logiciel dans le cadre de ses activités propres.
    Cette licence ne confère en aucun cas un droit de reproduction, de
    modification, de cession ou de sous-licence du logiciel à des tiers.
</p>
</div>

<div class="article">Article 15 – Réversibilité en fin de contrat</div>
<div class="article-body">
<p>
    Je souscris pleinement à l\'obligation de restitution des données de la COOPEC EBEN
    en cas de cessation de la collaboration. Toutefois, il convient de distinguer clairement :
</p>
<p>
    <strong>(a) Données :</strong> L\'ensemble des données opérationnelles de la COOPEC EBEN
    (membres, comptes, transactions, etc.) sera restitué dans un format exploitable
    (export SQL ou CSV) dans un délai raisonnable suivant la fin du contrat.
</p>
<p>
    <strong>(b) Logiciel :</strong> Le logiciel étant une propriété intellectuelle
    du développeur, il ne pourra être supprimé, neutralisé ou cédé. En revanche,
    la licence d\'utilisation accordée à la COOPEC EBEN est consentie pour une durée
    indéterminée, sous réserve du respect des obligations contractuelles arrêtées
    d\'un commun accord entre les deux parties.
</p>
<p>
    <strong>(c) Maintenance et évolutions :</strong> Toute mise à jour,
    évolution fonctionnelle, module complémentaire ou prestation de maintenance
    après la période de garantie constitue une charge de la COOPEC EBEN et donne lieu,
    le cas échéant, à une prestation distincte faisant l\'objet d\'un devis séparé.
</p>
<p>
    <strong>(d) Période de garantie :</strong> Une garantie de correction des défauts
    techniques est accordée pour une durée de <strong>trois (3) mois</strong>
    à compter de la date de livraison du logiciel. Au-delà de cette période, toute
    intervention est facturée au tarif en vigueur.
</p>
</div>

<div class="article">Article 17 – Sanctions et règlement des litiges</div>
<div class="article-body">
<p>
    Afin d\'éviter des procédures judiciaires longues et coûteuses pour les deux parties,
    je propose d\'insérer les dispositions suivantes :
</p>
<p>
    <strong>(a) Proportionnalité des sanctions :</strong> Toute sanction devra être
    proportionnelle à la gravité du manquement constaté et ne pourra être appliquée
    qu\'après notification écrite et expiration d\'un délai de mise en demeure
    de <strong>quinze (15) jours</strong>.
</p>
<p>
    <strong>(b) Médiation préalable :</strong> Avant tout recours judiciaire, les parties
    s\'engagent à rechercher une solution amiable, par voie de médiation ou de conciliation,
    dans un délai de <strong>trente (30) jours</strong> à compter de la naissance du litige.
</p>
<p>
    <strong>(c) Juridiction compétente :</strong> À défaut de résolution amiable,
    tout différend sera soumis aux juridictions compétentes du ressort de Kananga,
    selon le droit congolais applicable.
</p>
</div>

<div class="rule"></div>

<p>
    Je tiens à souligner que ces propositions ne remettent aucunement en cause la
    volonté de collaboration loyale et durable qui caractérise nos rapports. Elles
    visent à établir un cadre contractuel équilibré, protecteur tant pour la COOPEC EBEN
    que pour moi-même en tant que concepteur du logiciel.
</p>

<p>
    Je reste disponible à tout moment pour un échange en vue de finaliser ces amendements,
    et vous propose, si vous en êtes d\'accord, de convenir d\'une séance de travail
    pour relire conjointement les articles concernés.
</p>

<div class="closing">
    Veuillez agréer, Monsieur le Président, l\'expression de ma considération distinguée.
</div>

<div class="signature-block">
    <div style="font-size: 11px; color: #333; margin-bottom: 4px;">' . htmlspecialchars($letterDate, ENT_QUOTES, 'UTF-8') . '</div>
    ' . ($signatureBase64
        ? '<img src="' . $signatureBase64 . '" style="height:95px; max-width:240px; display:block; margin-left:auto;">'
        : '<div style="height:95px;"></div>') . '
    <div style="border-top:1px solid #555; width:240px; margin-left:auto; padding-top:4px; font-size:11px; text-align:center;">
        <strong>' . htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8') . '</strong>
    </div>
</div>

</body>
</html>';

$outputDir = __DIR__ . '/../Contrat';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$outputFile = $outputDir . '/lettre_reponse_contrat_NBTB_' . date('Ymd_His') . '.pdf';

Pdf::loadHTML($html)
    ->setPaper('A4', 'portrait')
    ->save($outputFile);

echo 'PDF_GENERE=' . $outputFile . PHP_EOL;

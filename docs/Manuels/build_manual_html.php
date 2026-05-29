<?php
/**
 * Génère un HTML autonome et stylé à partir de MANUEL_UTILISATEUR.md.
 * Convient à la visualisation navigateur et à l'export PDF (Dompdf).
 *
 * Exécution :
 *   php docs/Manuels/build_manual_html.php
 */

require __DIR__ . '/../../vendor/autoload.php';

use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

$mdPath   = __DIR__ . '/MANUEL_UTILISATEUR.md';
$htmlPath = __DIR__ . '/MANUEL_UTILISATEUR.html';

if (!is_file($mdPath)) {
    fwrite(STDERR, "Introuvable : {$mdPath}\n");
    exit(1);
}

$markdown = file_get_contents($mdPath);

$env = new Environment(['html_input' => 'allow', 'allow_unsafe_links' => false]);
$env->addExtension(new CommonMarkCoreExtension());
$env->addExtension(new GithubFlavoredMarkdownExtension());
$converter = new MarkdownConverter($env);
$body = $converter->convert($markdown)->getContent();

$css = <<<'CSS'
@page {
    size: A4;
    margin: 22mm 18mm 24mm 18mm;
}
@page {
    /* Pied de page avec numéro courant / total */
    @bottom-right {
        content: "Page " counter(page) " / " counter(pages);
        font-family: "Segoe UI", Arial, sans-serif;
        font-size: 9pt;
        color: #6b7280;
    }
    @bottom-left {
        content: "Manuel Utilisateur — Coopec EBEN";
        font-family: "Segoe UI", Arial, sans-serif;
        font-size: 9pt;
        color: #6b7280;
        font-style: italic;
    }
}
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body {
    font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    font-size: 11pt;
    color: #1f2937;
    line-height: 1.55;
    background: #ffffff;
}

/* === COUVERTURE === */
.cover {
    height: 247mm;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    page-break-after: always;
    background: linear-gradient(160deg, #1e3a8a 0%, #1e40af 50%, #0c2d6b 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 30mm;
    position: relative;
}
.cover .brand {
    font-size: 14pt;
    letter-spacing: 6px;
    text-transform: uppercase;
    opacity: 0.9;
    margin-bottom: 14mm;
}
.cover h1 {
    font-size: 38pt;
    line-height: 1.1;
    margin: 0 0 6mm 0;
    font-weight: 700;
    border: none;
    color: #ffffff;
    page-break-before: avoid;
}
.cover .sub {
    font-size: 17pt;
    font-weight: 300;
    margin-bottom: 6mm;
    opacity: 0.95;
}
.cover .institution {
    font-size: 13pt;
    font-weight: 600;
    margin-bottom: 18mm;
    opacity: 0.95;
    letter-spacing: 2px;
}
.cover .meta {
    font-size: 11pt;
    opacity: 0.9;
    border-top: 1px solid rgba(255,255,255,0.3);
    border-bottom: 1px solid rgba(255,255,255,0.3);
    padding: 6mm 0;
    width: 70%;
    line-height: 1.9;
}
.cover .site {
    margin-top: 10mm;
    font-size: 11pt;
    opacity: 0.9;
}
.cover .site a { color: #ffffff; text-decoration: none; border-bottom: 1px dotted #ffffff; }
.cover .footer-cover {
    position: absolute;
    bottom: 18mm;
    font-size: 9pt;
    opacity: 0.75;
    width: 100%;
    left: 0;
    text-align: center;
}

/* === SOMMAIRE / contenu === */
.content { padding: 0; }
h1, h2, h3, h4 {
    color: #1e3a8a;
    page-break-after: avoid;
    margin-top: 1.4em;
    margin-bottom: 0.5em;
    line-height: 1.25;
}
h1 {
    font-size: 24pt;
    border-bottom: 3px solid #1e3a8a;
    padding-bottom: 4mm;
    margin-top: 0;
    page-break-before: always;
}
h1:first-of-type { page-break-before: avoid; }
h2 {
    font-size: 17pt;
    border-bottom: 1px solid #d1d5db;
    padding-bottom: 2mm;
}
h3 { font-size: 13pt; color: #1e40af; }
h4 { font-size: 11pt; color: #374151; text-transform: uppercase; letter-spacing: 0.5px; }
p { margin: 0 0 0.7em 0; text-align: justify; }
a { color: #1e3a8a; text-decoration: none; }
ul, ol { margin: 0 0 0.8em 1.5em; padding: 0; }
li { margin: 0.15em 0; }
strong { color: #0f172a; }
em { color: #334155; }
code {
    font-family: "Cascadia Mono", "Consolas", "Courier New", monospace;
    font-size: 9.5pt;
    background: #f1f5f9;
    color: #be185d;
    padding: 1px 5px;
    border-radius: 3px;
}
pre {
    background: #0f172a;
    color: #e2e8f0;
    padding: 4mm 5mm;
    border-radius: 4px;
    font-size: 9pt;
    line-height: 1.45;
    overflow-x: auto;
    page-break-inside: avoid;
    margin: 0.6em 0 1em 0;
}
pre code {
    background: transparent;
    color: inherit;
    padding: 0;
    font-size: 9pt;
}
blockquote {
    margin: 0.6em 0 1em 0;
    padding: 3mm 5mm;
    background: #eff6ff;
    border-left: 4px solid #1e3a8a;
    color: #0c2d6b;
    border-radius: 0 4px 4px 0;
    page-break-inside: avoid;
}
blockquote p { margin: 0.2em 0; }
table {
    width: 100%;
    border-collapse: collapse;
    margin: 0.8em 0 1.2em 0;
    font-size: 10pt;
    page-break-inside: avoid;
}
th, td {
    border: 1px solid #d1d5db;
    padding: 5px 8px;
    text-align: left;
    vertical-align: top;
}
th {
    background: #1e3a8a;
    color: #ffffff;
    font-weight: 600;
}
tbody tr:nth-child(even) { background: #f8fafc; }
hr {
    border: none;
    border-top: 1px dashed #cbd5e1;
    margin: 1.5em 0;
}
img { max-width: 100%; height: auto; }

/* Sommaire (le premier <ol>/<ul> du document après le titre h1) */
.toc-wrap {
    page-break-after: always;
}
.toc-wrap h2 {
    border-bottom: 3px solid #1e3a8a;
    padding-bottom: 3mm;
}
.toc-wrap ol {
    list-style: none;
    counter-reset: toc;
    margin: 0;
    padding: 0;
}
.toc-wrap ol > li {
    counter-increment: toc;
    margin: 4mm 0;
    padding: 2mm 4mm;
    border-bottom: 1px dotted #cbd5e1;
    font-size: 11pt;
}
.toc-wrap ol > li::before {
    content: counter(toc, decimal-leading-zero) ".";
    color: #1e3a8a;
    font-weight: 700;
    margin-right: 4mm;
}
.toc-wrap a {
    color: #1f2937;
    text-decoration: none;
}

.footer-doc {
    text-align: center;
    font-size: 8.5pt;
    color: #6b7280;
    font-style: italic;
    margin-top: 4em;
    border-top: 1px solid #e5e7eb;
    padding-top: 6mm;
}
CSS;

$cover = <<<HTML
<section class="cover">
  <div class="brand">Nexus BMB Tech</div>
  <h1>Manuel Utilisateur</h1>
  <div class="sub">Système de gestion bancaire et financier</div>
  <div class="institution">COOPEC EBEN</div>
  <div class="meta">
    <div><strong>Version</strong> : 3.0.0</div>
    <div><strong>Date</strong> : Mai 2026</div>
    <div><strong>Public visé</strong> : Caissiers, Chargés de crédit, Trésoriers,<br>Comptables, Administrateurs &amp; Direction</div>
  </div>
  <div class="site">Site officiel : <a href="https://coopaeben.info/">https://coopaeben.info/</a></div>
  <div class="footer-cover">&copy; 2026 Nexus BMB Tech &mdash; Tous droits r&eacute;serv&eacute;s</div>
</section>
HTML;

/*
 * Encadre le sommaire dans une <div class="toc-wrap"> pour la stylisation
 * et le saut de page. On repère le bloc :
 *   <h2>Sommaire</h2><ol>...</ol>
 */
$body = preg_replace(
    '#(<h2>Sommaire</h2>\s*<ol>.*?</ol>)#s',
    '<div class="toc-wrap">$1</div>',
    $body,
    1
);

$html = <<<HTML
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Manuel Utilisateur — Coopec EBEN</title>
<style>{$css}</style>
</head>
<body>
{$cover}
<main class="content">
{$body}
</main>
<div class="footer-doc">Manuel Utilisateur &mdash; Coopec EBEN &middot; v3.0.0 &middot; &copy; 2026 Nexus BMB Tech</div>
</body>
</html>
HTML;

file_put_contents($htmlPath, $html);
echo "HTML écrit : {$htmlPath}\n";
echo "Taille    : " . number_format(strlen($html)) . " octets\n";

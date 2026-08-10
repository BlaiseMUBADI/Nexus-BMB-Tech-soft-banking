<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 15px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 6px; }
        .header strong { font-size: 13px; }
        .meta { color: #666; margin-bottom: 10px; }
        .photo-wrap { text-align: center; }
        .photo-wrap img { max-width: 100%; max-height: 700px; border: 1px solid #ccc; }
        .footer { margin-top: 10px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <strong>Coopec EBEN — Pièce justificative crédit</strong><br>
        Dossier : {{ $dossier->numero_dossier }} — Pièce : {{ $piece->type_piece }} ({{ $piece->libelle }})
    </div>
    <div class="meta">
        Client : {{ $dossier->client?->nom }} {{ $dossier->client?->prenom }} — Scannée le {{ now()->format('d/m/Y à H:i') }}
    </div>
    <div class="photo-wrap">
        <img src="{{ $imageData }}">
    </div>
    <div class="footer">Document généré automatiquement à partir d'une photo scannée — Coopec EBEN.</div>
</body>
</html>

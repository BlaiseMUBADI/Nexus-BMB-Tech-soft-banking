@extends('layouts.app')

@section('title', 'Aperçu carte membre — ' . $client->full_name)

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <h4 class="mb-0"><i class="fas fa-id-card text-success"></i> Aperçu de la carte membre</h4>
            <small class="text-muted">Rendu fidèle au format PVC — impression directe sur carte ID-1 (85,6 × 53,9 mm).</small>
        </div>
        <div>
            <a href="{{ route('clients.show', $client->matricule) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour fiche client
            </a>
            <button type="button" class="btn btn-success btn-sm" onclick="window.open('{{ route('clients.carte-membre.imprimer', $client->matricule) }}?output=stream', '_blank')">
                <i class="fas fa-print"></i> Imprimer sur PVC
            </button>
            <a href="{{ route('clients.carte-membre.imprimer', $client->matricule) }}?output=download" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download"></i> Télécharger PDF
            </a>
        </div>
    </div>

    <div class="carte-stage">
        <div class="carte-membre">
            <div class="header-row">
                <div class="brand-container">
                    <div class="logo-plaque">
                        @if($logoBase64 ?? false)
                            <img src="{{ $logoBase64 }}" alt="Logo COOPEC EBEN">
                        @else
                            <img src="{{ asset('dist/img/vrailogoeben-removebg-preview.png') }}" alt="Logo COOPEC EBEN">
                        @endif
                    </div>
                    <div class="institution-name">
                        <span class="main-title">COOPEC EBEN</span>
                        <span class="sub-title">Coopérative d'Épargne et de Crédit</span>
                    </div>
                </div>
                <div class="badge-membre">CARTE MEMBRE</div>
            </div>

            <div class="corps">
                <div class="photo-block">
                    @if($photoBase64 ?? false)
                        <img src="{{ $photoBase64 }}" alt="Photo client">
                    @else
                        <div class="photo-placeholder"><i class="fas fa-user"></i></div>
                    @endif
                </div>
                <div class="infos">
                    <div class="nom" title="{{ $client->full_name }}">{{ $nomCarteMembre ?? $client->full_name }}</div>
                    <div class="deux-col">
                        <div>
                            <div class="champ-label">Code client</div>
                            <div class="champ-valeur">{{ $client->matricule }}</div>
                        </div>
                        <div>
                            <div class="champ-label">Téléphone</div>
                            <div class="champ-valeur">{{ $client->telephone ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="champ-label">Agence / Zone</div>
                    <div class="champ-valeur">{{ $client->zone->nom ?? '—' }}</div>
                </div>
                @if(!empty($qrSvgBase64))
                <div class="qr-block">
                    <img src="data:image/svg+xml;base64,{{ $qrSvgBase64 }}" alt="QR code">
                    <small>Vérification agence</small>
                </div>
                @endif
            </div>

            <div class="separateur"></div>
            <div class="slogan-card">« Votre prospérité, notre priorité »</div>

            <div class="mentions">
                Carte personnelle et non cessible — présentation obligatoire pour toute opération en agence.
            </div>

            <div class="footer-row">
                <div class="signature-block">
                    @if($signatureBase64 ?? false)
                        <img class="signature-image" src="{{ $signatureBase64 }}" alt="Signature du gérant">
                    @else
                        <div style="height:30px;"></div>
                    @endif
                    <div class="ligne"></div>
                    <div class="role">Le Gérant</div>
                    @if(!($signatureBase64 ?? false))
                        <span class="tag-exemple">(signature à configurer en Administration)</span>
                    @endif
                </div>
                <div class="infos-footer">
                    Membre depuis {{ $carte->created_at->format('m/Y') }}
                    <strong>N° {{ str_pad((string) $carte->id, 6, '0', STR_PAD_LEFT) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3 no-print">
        <small class="text-muted">
            <i class="fas fa-ruler"></i> Dimensions réelles : 85,6 × 53,9 mm (format ID-1, ISO/IEC 7810)
        </small>
    </div>
</div>

<style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .carte-stage {
        display: flex;
        justify-content: center;
        padding: 30px 0;
        background: #eef1f4;
        border-radius: 12px;
    }
    .carte-membre {
        width: 620px;
        height: 390px;
        border-radius: 18px;
        background: linear-gradient(135deg, #0b6e4f 0%, #0a5c42 45%, #0d3b2e 100%);
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        position: relative;
        overflow: hidden;
        color: #fff;
        padding: 22px 28px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .carte-membre::before {
        content: "";
        position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .carte-membre::after {
        content: "";
        position: absolute;
        left: -80px; bottom: -90px;
        width: 260px; height: 260px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    .brand-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .logo-plaque {
        background: #fff;
        border-radius: 10px;
        padding: 5px 12px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
    }
    .logo-plaque img { height: 36px; object-fit: contain; }
    .institution-name {
        display: flex;
        flex-direction: column;
    }
    .institution-name .main-title {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0.8px;
        color: #fff;
        text-transform: uppercase;
    }
    .institution-name .sub-title {
        font-size: 10.5px;
        font-weight: 700;
        color: rgba(255,255,255,0.95);
        letter-spacing: 0.3px;
        margin-top: 1px;
    }
    .badge-membre {
        background: linear-gradient(135deg, #f2c94c, #d9a521);
        color: #1c1c1c;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 6px 14px;
        border-radius: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }

    .corps {
        display: flex;
        gap: 20px;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    .photo-block { width: 95px; flex-shrink: 0; }
    .photo-block img {
        width: 95px; height: 112px;
        object-fit: cover;
        border-radius: 10px;
        border: 3px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .photo-placeholder {
        width: 95px; height: 112px;
        border-radius: 10px;
        background: rgba(255,255,255,0.15);
        border: 3px solid #fff;
        text-align: center;
        line-height: 112px;
        font-size: 32px;
        color: #fff;
    }
    .infos { flex-grow: 1; line-height: 1.35; }
    .infos .nom {
        font-size: 19px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #fff;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .infos .champ-label {
        color: rgba(255,255,255,0.85);
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 5px;
    }
    .infos .champ-valeur {
        font-weight: 700;
        font-family: 'Consolas', monospace;
        font-size: 13.5px;
        color: #fff;
        letter-spacing: 0.4px;
    }
    .infos .deux-col { display: flex; gap: 18px; }

    .qr-block { width: 80px; flex-shrink: 0; text-align: center; }
    .qr-block img {
        width: 76px; height: 76px;
        background: #fff;
        padding: 4px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .qr-block small {
        display: block;
        margin-top: 4px;
        font-size: 8px;
        color: rgba(255,255,255,0.9);
        font-weight: 600;
    }

    .separateur {
        border-top: 1px solid rgba(255,255,255,0.28);
        position: relative;
        z-index: 2;
        margin: 2px 0;
    }
    .slogan-card {
        text-align: center;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #f2c94c;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }
    .mentions {
        font-size: 8.5px;
        line-height: 1.25;
        color: rgba(255,255,255,0.9);
        position: relative;
        z-index: 2;
        text-align: center;
        font-weight: 500;
    }
    .footer-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        position: relative;
        z-index: 2;
    }
    .signature-block { text-align: center; font-size: 8.5px; width: 130px; }
    .signature-image {
        height: 30px;
        max-width: 110px;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }
    .signature-block .ligne {
        width: 100px;
        border-top: 1px solid rgba(255,255,255,0.7);
        margin: 2px auto 3px;
    }
    .signature-block .role {
        color: rgba(255,255,255,0.95);
        letter-spacing: 0.5px;
        font-weight: 700;
    }
    .signature-block .tag-exemple {
        display: block;
        font-size: 6px;
        opacity: 0.7;
        font-style: italic;
        margin-top: 1px;
    }
    .infos-footer {
        text-align: right;
        font-size: 9px;
        color: rgba(255,255,255,0.9);
        line-height: 1.4;
        font-weight: 600;
    }
    .infos-footer strong {
        font-family: 'Consolas', monospace;
        font-size: 11.5px;
        color: #fff;
        display: block;
        letter-spacing: 0.8px;
        margin-top: 1px;
    }

    @media print {
        @page { size: 85.6mm 53.9mm; margin: 0; }
        body { background: none !important; padding: 0 !important; margin: 0 !important; }
        .no-print, .main-header, .main-sidebar, .main-footer { display: none !important; }
        .carte-stage { background: #fff !important; padding: 0 !important; }
        .carte-membre {
            width: 85.6mm !important;
            height: 53.9mm !important;
            border-radius: 3mm !important;
            box-shadow: none !important;
            padding: 3mm 4mm !important;
        }
    }
</style>
@endsection

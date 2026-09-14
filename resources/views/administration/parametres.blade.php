@extends('layouts.app')

@section('page_title', 'Paramètres généraux')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Paramètres généraux')

@section('content')
<section class="content">
<div class="container-fluid">

@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-signature mr-2"></i>Signature du gérant</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Cette signature est utilisée automatiquement sur les documents et cartes imprimés
                    (carte membre, etc.) — aucune signature n'est codée en dur dans l'application.
                    Le fond de l'image (blanc, gris clair…) est retiré automatiquement et le tracé
                    est repeint en <strong>blanc</strong>, quelle que soit la couleur d'encre d'origine
                    (noire, bleue…), pour s'intégrer directement à la carte membre (fond vert).
                </p>

                @if($signatureUrl)
                    <div class="mb-3 text-center p-3" style="background:#0a5c42;border-radius:8px;">
                        <img src="{{ $signatureUrl }}" alt="Signature du gérant actuelle" style="max-height:80px;">
                    </div>
                    <form method="POST" action="{{ route('administration.parametres.signature.destroy') }}" class="mb-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer la signature actuelle ?');">
                            <i class="fas fa-trash-alt mr-1"></i>Supprimer la signature actuelle
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning py-2 small">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Aucune signature configurée — les documents imprimés afficheront un espace vide.
                    </div>
                @endif

                <hr>

                <label class="font-weight-bold small text-uppercase">
                    {{ $signatureUrl ? 'Remplacer par une nouvelle signature' : 'Téléverser la signature' }}
                </label>

                <div id="sigEditorWrap">
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnChooseSigFile">
                            <i class="fas fa-image mr-1"></i> Choisir une photo/scan de la signature
                        </button>
                        <input type="file" id="inpSigFile" accept="image/png,image/jpeg" class="d-none">
                        <small class="form-text text-muted">
                            Idéalement : signature à l'encre foncée sur papier blanc, bien éclairée. PNG ou JPEG, 4 Mo max.
                        </small>
                    </div>

                    <div id="sigEditorArea" class="d-none">
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    1. Recadrer <small class="text-normal">(glisser pour déplacer, coins pour redimensionner)</small>
                                </label>
                                <div id="sigCropStage" style="position:relative;background:#111;overflow:hidden;max-width:100%;border-radius:4px;">
                                    <canvas id="sigSourceCanvas" style="display:block;max-width:100%;"></canvas>
                                    <div id="sigCropRect" style="position:absolute;border:2px dashed #f2c94c;cursor:move;box-shadow:0 0 0 9999px rgba(0,0,0,.55);">
                                        <div class="sig-handle" data-corner="nw" style="position:absolute;left:-7px;top:-7px;width:14px;height:14px;background:#f2c94c;border-radius:2px;cursor:nwse-resize;"></div>
                                        <div class="sig-handle" data-corner="ne" style="position:absolute;right:-7px;top:-7px;width:14px;height:14px;background:#f2c94c;border-radius:2px;cursor:nesw-resize;"></div>
                                        <div class="sig-handle" data-corner="sw" style="position:absolute;left:-7px;bottom:-7px;width:14px;height:14px;background:#f2c94c;border-radius:2px;cursor:nesw-resize;"></div>
                                        <div class="sig-handle" data-corner="se" style="position:absolute;right:-7px;bottom:-7px;width:14px;height:14px;background:#f2c94c;border-radius:2px;cursor:nwse-resize;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="small font-weight-bold text-uppercase text-muted">2. Aperçu (fond retiré)</label>
                                <div class="mb-2 text-center" id="sigCheckerBg" style="border-radius:6px;padding:10px;">
                                    <canvas id="sigPreviewTransparent" style="max-width:100%;"></canvas>
                                </div>
                                <div class="mb-2 text-center p-2" style="background:#0a5c42;border-radius:6px;">
                                    <canvas id="sigPreviewGreen" style="max-width:100%;"></canvas>
                                </div>
                                <label class="small font-weight-bold mb-0">Sensibilité du retrait de fond</label>
                                <input type="range" id="sigSensibilite" min="0" max="100" value="50" class="w-100">
                                <small class="text-muted d-block">
                                    Augmentez si des traces de fond restent visibles ; diminuez si le tracé
                                    de la signature est rongé/troué.
                                </small>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-success btn-sm" id="btnValiderSig">
                                <i class="fas fa-check mr-1"></i> Valider et enregistrer
                            </button>
                            <button type="button" class="btn btn-default btn-sm" id="btnAnnulerSig">
                                <i class="fas fa-times mr-1"></i> Annuler
                            </button>
                            <span class="ml-2 small text-muted" id="sigTraitementInfo"></span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('administration.parametres.signature.update') }}"
                      enctype="multipart/form-data" id="formSigUpdate" class="d-none">
                    @csrf
                    <input type="hidden" name="signature_data" id="inpSignatureData">
                    <input type="hidden" name="sensibilite" id="inpSensibiliteHidden" value="50">
                </form>
            </div>
        </div>
    </div>
</div>

</div>
</section>

<style>
    #sigCheckerBg {
        background-image:
            linear-gradient(45deg, #555 25%, transparent 25%),
            linear-gradient(-45deg, #555 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, #555 75%),
            linear-gradient(-45deg, transparent 75%, #555 75%);
        background-size: 16px 16px;
        background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
        background-color: #777;
    }
    .sig-handle:hover { transform: scale(1.15); }
</style>

@push('js')
<script>
$(function () {
    const fileInput   = document.getElementById('inpSigFile');
    const btnChoose   = document.getElementById('btnChooseSigFile');
    const editorArea  = document.getElementById('sigEditorArea');
    const cropStage   = document.getElementById('sigCropStage');
    const srcCanvas   = document.getElementById('sigSourceCanvas');
    const cropRect    = document.getElementById('sigCropRect');
    const previewT    = document.getElementById('sigPreviewTransparent');
    const previewG    = document.getElementById('sigPreviewGreen');
    const slider      = document.getElementById('sigSensibilite');
    const btnValider  = document.getElementById('btnValiderSig');
    const btnAnnuler  = document.getElementById('btnAnnulerSig');
    const infoSpan    = document.getElementById('sigTraitementInfo');
    const formUpdate  = document.getElementById('formSigUpdate');
    const inpData     = document.getElementById('inpSignatureData');
    const inpSensHid  = document.getElementById('inpSensibiliteHidden');

    const MAX_DISPLAY_W = 520;
    const MAX_DISPLAY_H = 420;

    let img = null;           // Image() source, résolution naturelle
    let displayScale = 1;      // ratio affichage / résolution naturelle
    let crop = { x: 0, y: 0, w: 0, h: 0 }; // en coordonnées d'AFFICHAGE (canvas)

    btnChoose.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            img = new Image();
            img.onload = function () {
                initEditor();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    function initEditor() {
        displayScale = Math.min(1, MAX_DISPLAY_W / img.naturalWidth, MAX_DISPLAY_H / img.naturalHeight);
        const dw = Math.round(img.naturalWidth * displayScale);
        const dh = Math.round(img.naturalHeight * displayScale);

        srcCanvas.width = dw;
        srcCanvas.height = dh;
        cropStage.style.width = dw + 'px';
        cropStage.style.height = dh + 'px';
        const ctx = srcCanvas.getContext('2d');
        ctx.clearRect(0, 0, dw, dh);
        ctx.drawImage(img, 0, 0, dw, dh);

        // Sélection initiale : 85% centrée
        const cw = Math.round(dw * 0.85);
        const ch = Math.round(dh * 0.85);
        crop = { x: Math.round((dw - cw) / 2), y: Math.round((dh - ch) / 2), w: cw, h: ch };
        applyCropRectStyle();

        editorArea.classList.remove('d-none');
        updatePreview();
    }

    function applyCropRectStyle() {
        cropRect.style.left = crop.x + 'px';
        cropRect.style.top = crop.y + 'px';
        cropRect.style.width = crop.w + 'px';
        cropRect.style.height = crop.h + 'px';
    }

    // ── Interaction souris/tactile : déplacer + redimensionner le cadre ──
    let dragMode = null; // 'move' | 'nw' | 'ne' | 'sw' | 'se' | null
    let dragStart = null;

    function pointerPos(e) {
        const rect = cropStage.getBoundingClientRect();
        const p = e.touches ? e.touches[0] : e;
        return { x: p.clientX - rect.left, y: p.clientY - rect.top };
    }

    cropRect.querySelectorAll('.sig-handle').forEach(function (handle) {
        handle.addEventListener('mousedown', function (e) {
            e.stopPropagation();
            dragMode = handle.dataset.corner;
            dragStart = { pointer: pointerPos(e), crop: Object.assign({}, crop) };
        });
    });
    cropRect.addEventListener('mousedown', function (e) {
        if (e.target.classList.contains('sig-handle')) return;
        dragMode = 'move';
        dragStart = { pointer: pointerPos(e), crop: Object.assign({}, crop) };
    });
    cropStage.addEventListener('mousedown', function (e) {
        if (e.target !== srcCanvas) return;
        const p = pointerPos(e);
        crop = { x: p.x, y: p.y, w: 1, h: 1 };
        dragMode = 'se';
        dragStart = { pointer: p, crop: Object.assign({}, crop) };
        applyCropRectStyle();
    });

    document.addEventListener('mousemove', function (e) {
        if (!dragMode || !img) return;
        const p = pointerPos(e);
        const dx = p.x - dragStart.pointer.x;
        const dy = p.y - dragStart.pointer.y;
        const dw = srcCanvas.width, dh = srcCanvas.height;
        let c = Object.assign({}, dragStart.crop);

        if (dragMode === 'move') {
            c.x = clamp(dragStart.crop.x + dx, 0, dw - dragStart.crop.w);
            c.y = clamp(dragStart.crop.y + dy, 0, dh - dragStart.crop.h);
        } else {
            // Redimensionnement depuis le coin concerné
            if (dragMode.includes('n')) { c.y = dragStart.crop.y + dy; c.h = dragStart.crop.h - dy; }
            if (dragMode.includes('s')) { c.h = dragStart.crop.h + dy; }
            if (dragMode.includes('w')) { c.x = dragStart.crop.x + dx; c.w = dragStart.crop.w - dx; }
            if (dragMode.includes('e')) { c.w = dragStart.crop.w + dx; }

            if (c.w < 20) { c.w = 20; if (dragMode.includes('w')) c.x = dragStart.crop.x + dragStart.crop.w - 20; }
            if (c.h < 20) { c.h = 20; if (dragMode.includes('n')) c.y = dragStart.crop.y + dragStart.crop.h - 20; }
            c.x = clamp(c.x, 0, dw - c.w);
            c.y = clamp(c.y, 0, dh - c.h);
            c.w = Math.min(c.w, dw - c.x);
            c.h = Math.min(c.h, dh - c.y);
        }
        crop = c;
        applyCropRectStyle();
    });
    document.addEventListener('mouseup', function () {
        if (dragMode) { dragMode = null; updatePreview(); }
    });

    function clamp(v, min, max) { return Math.max(min, Math.min(max, v)); }

    slider.addEventListener('input', updatePreview);

    // Plafond de résolution de l'image envoyée au serveur : une signature
    // n'a jamais besoin de plus que ça (elle s'affiche en quelques dizaines
    // de px sur la carte/le PDF), et ça évite de saturer post_max_size /
    // memory_limit côté serveur si la photo source est très haute résolution
    // (ex : 4000×3000 px depuis un smartphone).
    const MAX_EXPORT_DIM = 1000;

    /**
     * Extrait la zone de recadrage en résolution naturelle (pas la résolution
     * d'affichage réduite de l'éditeur), pour ne pas perdre en qualité au
     * recadrage — puis la limite à MAX_EXPORT_DIM si nécessaire. C'est CETTE
     * image (non traitée) qui sera envoyée au serveur ; le retrait de fond
     * définitif est fait côté serveur (SignatureProcessor) avec la
     * sensibilité choisie ici.
     */
    function extractCroppedFullRes() {
        const nx = crop.x / displayScale;
        const ny = crop.y / displayScale;
        const nw = crop.w / displayScale;
        const nh = crop.h / displayScale;

        const exportScale = Math.min(1, MAX_EXPORT_DIM / Math.max(nw, nh));
        const outW = Math.max(1, Math.round(nw * exportScale));
        const outH = Math.max(1, Math.round(nh * exportScale));

        const canvas = document.createElement('canvas');
        canvas.width = outW;
        canvas.height = outH;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, nx, ny, nw, nh, 0, 0, outW, outH);
        return canvas;
    }

    /**
     * Retrait de fond APPROXIMATIF côté navigateur, pour l'aperçu instantané
     * uniquement (même principe que SignatureProcessor::removeBackground côté
     * serveur : couleur de fond estimée sur les bords, rampe de transparence
     * selon la distance de couleur, puis recoloration du tracé en blanc pur
     * — cohérent avec le fichier réellement enregistré, traité côté serveur
     * avec le même algorithme, pas par ce code).
     */
    function apercuSansFond(canvas, sensibilite) {
        const w = canvas.width, h = canvas.height;
        const ctx = canvas.getContext('2d');
        const imageData = ctx.getImageData(0, 0, w, h);
        const data = imageData.data;

        const border = Math.max(4, Math.min(25, Math.round(Math.min(w, h) * 0.05)));
        let sumR = 0, sumG = 0, sumB = 0, n = 0;
        function sample(x, y) {
            const idx = (y * w + x) * 4;
            sumR += data[idx]; sumG += data[idx + 1]; sumB += data[idx + 2]; n++;
        }
        const stepX = Math.max(1, Math.floor(w / 80));
        const stepY = Math.max(1, Math.floor(h / 80));
        for (let x = 0; x < w; x += stepX) {
            for (let yy = 0; yy < border; yy++) { sample(x, yy); sample(x, h - 1 - yy); }
        }
        for (let y = 0; y < h; y += stepY) {
            for (let xx = 0; xx < border; xx++) { sample(xx, y); sample(w - 1 - xx, y); }
        }
        const bgR = n ? sumR / n : 255, bgG = n ? sumG / n : 255, bgB = n ? sumB / n : 255;

        const seuilBas = 60 - (sensibilite * 0.45);
        const seuilHaut = seuilBas + 35;

        for (let i = 0; i < data.length; i += 4) {
            const r = data[i], g = data[i + 1], b = data[i + 2];
            const dist = Math.sqrt((r - bgR) ** 2 + (g - bgG) ** 2 + (b - bgB) ** 2);
            let alpha;
            if (dist <= seuilBas) alpha = 0;
            else if (dist >= seuilHaut) alpha = 255;
            else alpha = Math.round((dist - seuilBas) / (seuilHaut - seuilBas) * 255);
            data[i + 3] = Math.min(data[i + 3], alpha);
            // Tracé repeint en blanc pur (comme le traitement serveur), quelle
            // que soit la couleur d'encre d'origine, pour s'intégrer à la carte.
            data[i] = 255; data[i + 1] = 255; data[i + 2] = 255;
        }
        ctx.putImageData(imageData, 0, 0);
        return canvas;
    }

    function updatePreview() {
        if (!img) return;
        const sensibilite = parseInt(slider.value, 10);
        inpSensHid.value = sensibilite;

        const cropped = extractCroppedFullRes();

        // Aperçu réduit pour rester fluide même sur une grosse photo
        const PREVIEW_MAX_W = 300;
        const scalePreview = Math.min(1, PREVIEW_MAX_W / cropped.width);
        const pw = Math.round(cropped.width * scalePreview);
        const ph = Math.round(cropped.height * scalePreview);

        [previewT, previewG].forEach(function (c) { c.width = pw; c.height = ph; });

        const tmp = document.createElement('canvas');
        tmp.width = pw; tmp.height = ph;
        tmp.getContext('2d').drawImage(cropped, 0, 0, pw, ph);
        apercuSansFond(tmp, sensibilite);

        previewT.getContext('2d').clearRect(0, 0, pw, ph);
        previewT.getContext('2d').drawImage(tmp, 0, 0);
        previewG.getContext('2d').clearRect(0, 0, pw, ph);
        previewG.getContext('2d').drawImage(tmp, 0, 0);

        infoSpan.textContent = cropped.width + '×' + cropped.height + ' px';
    }

    btnValider.addEventListener('click', function () {
        if (!img) return;
        const cropped = extractCroppedFullRes();
        inpData.value = cropped.toDataURL('image/png');
        inpSensHid.value = slider.value;
        formUpdate.submit();
    });

    btnAnnuler.addEventListener('click', function () {
        img = null;
        fileInput.value = '';
        editorArea.classList.add('d-none');
    });
});
</script>
@endpush
@endsection

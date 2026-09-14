{{--
    Partial réutilisable : sélection + recadrage (Cropper.js) d'une photo,
    à inclure DANS un formulaire multipart existant (create/edit client,
    profil agent...). Le fichier final (déjà cadré, carré) remplace le
    contenu de l'input file avant la soumission du formulaire englobant —
    aucun bouton de soumission propre ici.

    Paramètres :
      - inputName        (défaut 'photo') : name de l'input file à générer
      - existingPhotoUrl (optionnel)      : URL de la photo actuelle, si modification
      - label            (défaut 'Choisir une photo')
      - helpText         (optionnel)
--}}
@php
    $inputName = $inputName ?? 'photo';
    $uid = 'pc_' . $inputName . '_' . substr(md5($inputName . microtime()), 0, 6);
@endphp

<div id="{{ $uid }}_wrap">
    @if(!empty($existingPhotoUrl))
        <div class="mb-2">
            <img src="{{ $existingPhotoUrl }}" alt="Photo actuelle" class="rounded" style="width:90px;height:90px;object-fit:cover;border:1px solid #444;">
            <small class="d-block text-muted">Photo actuelle — choisissez un fichier ci-dessous pour la remplacer.</small>
        </div>
    @endif

    <button type="button" class="btn btn-outline-secondary btn-sm" id="{{ $uid }}_btn">
        <i class="fas fa-image mr-1"></i>{{ $label ?? 'Choisir une photo' }}
    </button>
    <input type="file" class="d-none" id="{{ $uid }}_file" name="{{ $inputName }}" accept="image/*">
    <small class="form-text text-muted d-block">{{ $helpText ?? "Jusqu'à 10 Mo. Vous pourrez cadrer la zone à garder." }}</small>

    <div class="mt-2">
        <img id="{{ $uid }}_preview" src="#" alt="Aperçu" class="rounded d-none" style="width:90px;height:90px;object-fit:cover;border:2px solid #007bff;">
        <div id="{{ $uid }}_error" class="text-danger small mt-1"></div>
        <button type="button" id="{{ $uid }}_recadrer" class="btn btn-xs btn-outline-secondary mt-1 d-none">
            <i class="fas fa-crop mr-1"></i>Recadrer
        </button>
    </div>

    {{-- Modal de cadrage (carré déplaçable/zoomable) --}}
    <div class="modal fade" id="{{ $uid }}_modal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="fas fa-crop-alt mr-1"></i>Cadrer la photo</h6>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-2">
                        Déplacez et redimensionnez le carré pour sélectionner la partie à garder
                        (utile si la photo est en pied). Molette pour zoomer.
                    </p>
                    <div style="max-height:60vh; overflow:hidden;">
                        <img id="{{ $uid }}_target" src="" alt="À cadrer" style="max-width:100%;display:block;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="{{ $uid }}_valider">
                        <i class="fas fa-check mr-1"></i>Valider le cadrage
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    @endpush
    @push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    @endpush
@endonce

@push('js')
<script>
(function () {
    var uid = '{{ $uid }}';
    var fileInput = document.getElementById(uid + '_file');
    var btnChoose = document.getElementById(uid + '_btn');
    var targetImg = document.getElementById(uid + '_target');
    var modalEl   = document.getElementById(uid + '_modal');
    var btnValider = document.getElementById(uid + '_valider');
    var btnRecadrer = document.getElementById(uid + '_recadrer');
    var preview = document.getElementById(uid + '_preview');
    var errorDiv = document.getElementById(uid + '_error');

    var img = null;
    var originalDataUrl = null;
    var cropper = null;
    var confirmed = false;
    var MAX_EXPORT_DIM = 900;

    btnChoose.addEventListener('click', function () { fileInput.click(); });

    function destroyCropper() { if (cropper) { cropper.destroy(); cropper = null; } }

    fileInput.addEventListener('change', function () {
        var file = this.files && this.files[0];
        errorDiv.textContent = '';
        confirmed = false;
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            errorDiv.textContent = 'Veuillez sélectionner une image.';
            this.value = ''; return;
        }
        if (file.size > 10 * 1024 * 1024) {
            errorDiv.textContent = 'La photo ne doit pas dépasser 10 Mo.';
            this.value = ''; return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            originalDataUrl = e.target.result;
            img = new Image();
            img.onload = function () {
                targetImg.src = originalDataUrl;
                $('#' + uid + '_modal').modal('show');
            };
            img.src = originalDataUrl;
        };
        reader.readAsDataURL(file);
    });

    btnRecadrer.addEventListener('click', function () {
        if (!originalDataUrl) return;
        targetImg.src = originalDataUrl;
        $('#' + uid + '_modal').modal('show');
    });

    $('#' + uid + '_modal').on('shown.bs.modal', function () {
        destroyCropper();
        cropper = new Cropper(targetImg, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 0.9,
            movable: true,
            zoomable: true,
            scalable: false,
            rotatable: false,
            responsive: true,
            background: false,
        });
    });

    $('#' + uid + '_modal').on('hidden.bs.modal', function () {
        destroyCropper();
        if (!confirmed) {
            fileInput.value = '';
            preview.classList.add('d-none');
            btnRecadrer.classList.add('d-none');
        }
    });

    btnValider.addEventListener('click', function () {
        if (!cropper) return;
        cropper.getCroppedCanvas({
            width: MAX_EXPORT_DIM,
            height: MAX_EXPORT_DIM,
            imageSmoothingQuality: 'high',
        }).toBlob(function (blob) {
            if (!blob) { errorDiv.textContent = 'Erreur lors du cadrage, réessayez.'; return; }

            var croppedFile = new File([blob], 'photo-cadree.jpg', { type: 'image/jpeg' });
            var dt = new DataTransfer();
            dt.items.add(croppedFile);
            fileInput.files = dt.files;

            preview.src = URL.createObjectURL(blob);
            preview.classList.remove('d-none');
            btnRecadrer.classList.remove('d-none');
            errorDiv.textContent = '';

            confirmed = true;
            $('#' + uid + '_modal').modal('hide');
        }, 'image/jpeg', 0.9);
    });
})();
</script>
@endpush

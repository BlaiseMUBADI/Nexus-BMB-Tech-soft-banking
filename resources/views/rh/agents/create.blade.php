@extends('layouts.app')

@section('page_title', 'Ajout agent')
@section('breadcrumb_parent', 'Ressources Humaines')
@section('breadcrumb', 'Ajouter un agent')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mt-2">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Ajouter un agent</h3>
                </div>
                <form method="POST" action="{{ route('agents.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="nom">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="postnom">Postnom</label>
                                <input type="text" class="form-control" id="postnom" name="postnom">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="prenom">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Sexe</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sexe" id="sexeM" value="M" required>
                                    <label class="form-check-label" for="sexeM">Masculin</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sexe" id="sexeF" value="F" required>
                                    <label class="form-check-label" for="sexeF">Féminin</label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="date_naissance">Date de naissance</label>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telephone">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="adresse">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="date_embauche">Date d'embauche</label>
                                <input type="date" class="form-control" id="date_embauche" name="date_embauche">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="statut">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="photo">Photo de l'agent</label>
                                <div class="input-group align-items-start">
                                    <div class="custom-file" style="max-width: 220px;">
                                        <input type="file" class="custom-file-input" id="photo" name="photo" accept="image/*">
                                        <label class="custom-file-label" for="photo">Choisir une photo</label>
                                    </div>
                                    <div id="photo-preview-panel" style="margin-left:15px; display:none;">
                                        <img id="photo-preview" src="#" alt="Aperçu" style="max-width:150px; max-height:150px; border:1px solid #ccc; border-radius:6px; background:#f8f9fa;" />
                                    </div>
                                </div>
                                <div id="photo-error" style="color:#dc3545; font-size:0.9em; margin-top:4px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('photo');
    const previewPanel = document.getElementById('photo-preview-panel');
    const preview = document.getElementById('photo-preview');
    const errorDiv = document.getElementById('photo-error');
    input.addEventListener('change', function (e) {
        errorDiv.textContent = '';
        previewPanel.style.display = 'none';
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            errorDiv.textContent = 'Le format de la photo doit être JPEG, PNG ou GIF.';
            input.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            errorDiv.textContent = 'La taille de la photo ne doit pas dépasser 2 Mo.';
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            previewPanel.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush

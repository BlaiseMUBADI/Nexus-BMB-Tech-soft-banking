<<<<<<< HEAD
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Nom du poste</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($postes as $poste)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $poste->nom }}</td>
            <td>{{ $poste->description }}</td>
            <td>
                <button class="btn btn-sm btn-danger btn-delete-poste"
                    data-service-id="{{ $service->id }}"
                    data-poste-id="{{ $poste->id }}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center text-muted">Aucun poste pour ce service.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<hr>
<form class="form-ajout-poste mt-3" method="POST" action="{{ url('/rh/services/' . $service->id . '/postes-ajax') }}" data-service-id="{{ $service->id }}">
    @csrf
    <div class="form-group">
        <label for="posteNom">Nom du poste</label>
        <input type="text" name="nom" class="form-control" id="posteNom" placeholder="Entrer le nom du poste" required>
=======

<h6 class="mb-3 font-weight-bold">Postes pour <span class="text-primary">{{ $service->nom }}</span></h6>

@if(count($postes) > 0)
<div class="table-responsive" style="max-height: 300px; min-height: 120px; overflow-y: auto;">
    <table class="table table-bordered table-striped mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom du poste</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($postes as $poste)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $poste->nom }}</td>
                <td>{{ $poste->description }}</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-delete-poste" data-id="{{ $poste->id }}">Supprimer</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class="text-muted">Aucun poste pour ce service.</p>
@endif
<hr>
<form class="form-ajout-poste" method="POST" action="{{ route('postes.store', $service->id) }}" data-service-id="{{ $service->id }}">
    @csrf
    <div class="form-group">
        <label for="posteName">Nom du poste</label>
        <input type="text" name="nom" class="form-control" id="posteName" placeholder="Entrer le nom du poste" required>
>>>>>>> b5584ae2ee773478b4afc47877f6e2200fd29a75
    </div>
    <div class="form-group">
        <label for="posteDesc">Description</label>
        <textarea name="description" class="form-control" id="posteDesc" rows="2" placeholder="Entrer la description"></textarea>
    </div>
<<<<<<< HEAD
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle mr-1"></i>Ajouter le poste</button>
=======
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle mr-1"></i>Ajouter</button>
>>>>>>> b5584ae2ee773478b4afc47877f6e2200fd29a75
</form>

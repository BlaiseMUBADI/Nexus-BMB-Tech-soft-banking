
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
<form class="form-ajout-poste mt-3" method="POST" action="{{ route('postes.ajaxStore', ['service' => $service->id]) }}" data-service-id="{{ $service->id }}">
    @csrf
    <div class="form-group">
        <label for="posteNom">Nom du poste</label>
        <input type="text" name="nom" class="form-control" id="posteNom" placeholder="Entrer le nom du poste" required>

    </div>
    <div class="form-group">
        <label for="posteDesc">Description</label>
        <textarea name="description" class="form-control" id="posteDesc" rows="2" placeholder="Entrer la description"></textarea>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle mr-1"></i>Ajouter le poste</button>
</form>

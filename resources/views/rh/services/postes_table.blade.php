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
    </div>
    <div class="form-group">
        <label for="posteDesc">Description</label>
        <textarea name="description" class="form-control" id="posteDesc" rows="2" placeholder="Entrer la description"></textarea>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle mr-1"></i>Ajouter</button>
</form>

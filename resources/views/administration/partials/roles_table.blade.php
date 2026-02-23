<table id="roles-table" class="table table-bordered table-striped" data-buttons-container="#roles-table-buttons">
    <thead>
        <tr>
            <th>N°</th>
            <th>Code</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $loopIndex => $role)
            <tr>
                <td>{{ $loopIndex + 1 }}</td>
                <td>{{ $role->code }}</td>
                <td>{{ $role->nom }}</td>
                <td>{{ $role->description }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning btn-edit-role" data-id="{{ $role->code }}" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('administration.roles.destroy', $role->code) }}" method="POST" class="d-inline delete-role-form" data-role-id="{{ $role->code }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger btn-delete-role" data-id="{{ $role->id }}" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<table id="permissions-table" class="table table-bordered table-striped" data-buttons-container="#permissions-table-buttons">
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
        @foreach($permissions as $loopIndex => $permission)
            <tr>
                <td>{{ $loopIndex + 1 }}</td>
                <td>{{ $permission->code }}</td>
                <td>{{ $permission->nom }}</td>
                <td>{{ $permission->description }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning btn-edit-permission" data-id="{{ $permission->code }}" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

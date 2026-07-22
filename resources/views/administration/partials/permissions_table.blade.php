{{-- Partial AJAX : tableau plat des permissions, utilisé pour rafraîchir la liste après ajout/suppression --}}
<table id="permissionsTable" class="table table-sm rbac-table mb-0">
    <thead class="thead-dark">
        <tr>
            <th style="width:35px">#</th>
            <th style="width:160px">Code</th>
            <th style="width:220px">Nom</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody id="permissionsTbody">
        @forelse($permissions as $permission)
        <tr>
            <td class="text-muted">{{ $loop->iteration }}</td>
            <td><code class="text-primary">{{ $permission->code }}</code></td>
            <td><strong>{{ $permission->nom }}</strong></td>
            <td class="text-muted small">{{ $permission->description ?: '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucune permission définie.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

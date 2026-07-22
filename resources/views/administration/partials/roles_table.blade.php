{{-- Partial AJAX : tableau des rôles, utilisé pour rafraîchir la liste après ajout/suppression --}}
<table id="rolesTable" class="table table-sm rbac-table mb-0">
    <thead class="thead-dark">
        <tr>
            <th style="width:35px">#</th>
            <th style="width:140px">Code</th>
            <th>Nom</th>
            <th>Description</th>
            <th class="text-center" style="width:80px">Perms.</th>
            <th class="text-center" style="width:65px">Action</th>
        </tr>
    </thead>
    <tbody id="rolesTbody">
        @forelse($roles as $role)
        <tr>
            <td class="text-muted">{{ $loop->iteration }}</td>
            <td><code class="text-primary">{{ $role->code }}</code></td>
            <td><strong>{{ $role->nom }}</strong></td>
            <td class="text-muted small">{{ $role->description ?: '—' }}</td>
            <td class="text-center">
                <span class="badge badge-{{ ($role->permissions_count ?? $role->permissions()->count()) > 0 ? 'success' : 'secondary' }}">
                    {{ $role->permissions_count ?? $role->permissions()->count() }}
                </span>
            </td>
            <td class="text-center">
                <button class="btn btn-xs btn-danger btn-delete-role"
                        data-id="{{ $role->code }}"
                        data-nom="{{ $role->nom }}"
                        title="Supprimer ce rôle">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucun rôle défini.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

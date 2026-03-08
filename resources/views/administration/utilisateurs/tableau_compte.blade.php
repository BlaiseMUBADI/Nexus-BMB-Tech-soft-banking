
    <div class="card-body">
        <div class="table-responsive">
            <table id="usersTable" class="table table-bordered table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Login</th>
                        <th>Email</th>
                        <th>État</th>
                        <th>Agent</th>
                        <th>Service / Poste</th>
                        <th></th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rempli dynamiquement par AJAX -->
                </tbody>
            </table>
        </div>
    </div>

@push('js')
<script>
function renderUsersTable(users) {
    let tbody = '';
    users.forEach(function(user, idx) {
        var aff     = (user.agent && user.agent.affectations && user.agent.affectations.length)
                      ? user.agent.affectations[0] : null;
        var serviceNom = (aff && aff.poste && aff.poste.service) ? aff.poste.service.nom : null;
        var poste   = (aff && aff.poste) ? aff.poste.nom : null;
        var serviceCell = serviceNom
            ? '<span class="badge badge-info">' + serviceNom + '</span> / ' + (poste || '—')
            : '<span class="text-muted">—</span>';
        var agentNom = user.agent
            ? '<strong>' + user.agent.nom + '</strong> <small class="text-muted">' + (user.agent.postnom || '') + ' ' + (user.agent.prenom || '') + '</small>'
            : '—';
        tbody += '<tr>'
            + '<td>' + (idx+1) + '</td>'
            + '<td>' + user.name + '</td>'
            + '<td>' + (user.email || '') + '</td>'
            + '<td><span class="badge badge-' + (user.etat === 'actif' ? 'success' : 'secondary') + '">' + user.etat + '</span></td>'
            + '<td>' + agentNom + '</td>'
            + '<td>' + serviceCell + '</td>'
            + '<td>'
            +   '<button class="btn btn-sm btn-info editUserBtn" data-id="' + user.id + '" title="Modifier"><i class="fas fa-edit"></i></button> '
            +   '<button class="btn btn-sm btn-danger deleteUserBtn" data-id="' + user.id + '" title="Supprimer"><i class="fas fa-trash"></i></button>'
            + '</td>'
            + '</tr>';
    });
    $('#usersTable tbody').html(tbody);
}

function loadUsersTable() {
    $.ajax({
        url     : "{{ route('administration.utilisateurs.liste') }}",
        type    : 'GET',
        dataType: 'json'
    })
    .done(function (data) {
        renderUsersTable(data.users || []);
    })
    .fail(function (xhr) {
        handleAjaxFail(xhr, 'Chargement utilisateurs');
        $('#usersTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Erreur de chargement des utilisateurs.</td></tr>');
    });
}

$(document).ready(function() {
    loadUsersTable();
    $('#refreshUsersTable').on('click', loadUsersTable);

    // ── Suppression utilisateur ───────────────────────────────────
    $(document).on('click', '.deleteUserBtn', function() {
        var userId = $(this).data('id');
        showUniversalConfirm('Voulez-vous vraiment supprimer cet utilisateur ?', function() {
            $.ajax({
                url     : '{{ route("administration.utilisateurs.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', userId),
                type    : 'POST',
                data    : { _method: 'DELETE' },
                dataType: 'json'
            })
            .done(function (resp) {
                if (resp.success) {
                    showSystemMessage('success', resp.message || 'Utilisateur supprimé.');
                    loadUsersTable();
                } else {
                    showSystemMessage('error', resp.message || 'Erreur.');
                }
            })
            .fail(function (xhr) {
                handleAjaxFail(xhr, 'Suppression utilisateur');
            });
        }, 'Confirmation de suppression');
    });

    // ── Édition utilisateur (inline) ──────────────────────────────
    $(document).on('click', '.editUserBtn', function() {
        var tr     = $(this).closest('tr');
        var userId = $(this).data('id');
        var name   = tr.find('td:eq(1)').text();
        var email  = tr.find('td:eq(2)').text();
        var etat   = tr.find('span.badge').text().trim();
        tr.html(`
            <td></td>
            <td><input type='text' class='form-control form-control-sm' value='${name}' id='editName'></td>
            <td><input type='email' class='form-control form-control-sm' value='${email}' id='editEmail'></td>
            <td>
                <select class='form-control form-control-sm' id='editEtat'>
                    <option value='actif' ${etat==='actif'?'selected':''}>actif</option>
                    <option value='inactif' ${etat==='inactif'?'selected':''}>inactif</option>
                </select>
            </td>
            <td></td>
            <td></td>
            <td>
                <button class='btn btn-sm btn-success saveEditUserBtn' data-id='${userId}'><i class='fas fa-check'></i></button>
                <button class='btn btn-sm btn-secondary cancelEditUserBtn'><i class='fas fa-times'></i></button>
            </td>
        `);
    });

    // ── Annuler édition ───────────────────────────────────────────
    $(document).on('click', '.cancelEditUserBtn', function() {
        loadUsersTable();
    });

    // ── Sauvegarder édition ───────────────────────────────────────
    $(document).on('click', '.saveEditUserBtn', function() {
        var tr     = $(this).closest('tr');
        var userId = $(this).data('id');
        $.ajax({
            url     : '{{ route("administration.utilisateurs.update", ["id" => "__ID__"]) }}'.replace('__ID__', userId),
            type    : 'POST',
            data    : {
                _method: 'PUT',
                login  : tr.find('#editName').val(),
                email  : tr.find('#editEmail').val(),
                etat   : tr.find('#editEtat').val()
            },
            dataType: 'json'
        })
        .done(function (resp) {
            if (resp.success) {
                showSystemMessage('success', resp.message || 'Utilisateur modifié.');
                loadUsersTable();
            } else {
                showSystemMessage('error', resp.message || 'Erreur.');
            }
        })
        .fail(function (xhr) {
            handleAjaxFail(xhr, 'Modification utilisateur');
        });
    });
});
</script>
@endpush

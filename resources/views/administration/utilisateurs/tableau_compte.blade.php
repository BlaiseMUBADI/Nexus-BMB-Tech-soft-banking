
    <div class="card-body">
        <div class="table-responsive">
            <table id="usersTable" class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Login</th>
                        <th>Email</th>
                        <th>État</th>
                        <th>Agent</th>
                        <th>Actions</th>
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
function loadUsersTable() {
    $.ajax({
        url: "{{ route('administration.utilisateurs.liste') }}",
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let tbody = '';
            data.users.forEach(function(user, idx) {
                tbody += `<tr>
                    <td>${idx+1}</td>
                    <td>${user.name}</td>
                    <td>${user.email || ''}</td>
                    <td><span class="badge badge-${user.etat === 'actif' ? 'success' : 'secondary'}">${user.etat}</span></td>
                    <td>${user.agent ? user.agent.nom + ' ' + user.agent.postnom : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-info editUserBtn" data-id="${user.id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger deleteUserBtn" data-id="${user.id}"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`;
            });
            $('#usersTable tbody').html(tbody);
        },
        error: function() {
            $('#usersTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Erreur de chargement des utilisateurs.</td></tr>');
        }
    });
}
$(document).ready(function() {
    loadUsersTable();
    $('#refreshUsersTable').on('click', loadUsersTable);

    // Suppression utilisateur
    var baseUrl = "{{ url('') }}";
    $(document).on('click', '.deleteUserBtn', function() {
        var userId = $(this).data('id');
        showUniversalConfirm('Voulez-vous vraiment supprimer cet utilisateur ?', function() {
            $.ajax({
                url: baseUrl + '/administration/utilisateurs/' + userId,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resp) {
                    showSystemMessage('success', resp.message || 'Utilisateur supprimé.');
                    loadUsersTable();
                },
                error: function() {
                    showSystemMessage('error', 'Erreur lors de la suppression.');
                }
            });
        }, 'Confirmation de suppression');
    });

    // Edition utilisateur (inline simple)
    $(document).on('click', '.editUserBtn', function() {
        var tr = $(this).closest('tr');
        var userId = $(this).data('id');
        var name = tr.find('td:eq(1)').text();
        var email = tr.find('td:eq(2)').text();
        var etat = tr.find('span.badge').text();
        // Remplace la ligne par un formulaire inline
        tr.html(`
            <td></td>
            <td><input type='text' class='form-control' value='${name}' id='editName'></td>
            <td><input type='email' class='form-control' value='${email}' id='editEmail'></td>
            <td>
                <select class='form-control' id='editEtat'>
                    <option value='actif' ${etat.trim()==='actif'?'selected':''}>actif</option>
                    <option value='inactif' ${etat.trim()==='inactif'?'selected':''}>inactif</option>
                </select>
            </td>
            <td></td>
            <td>
                <button class='btn btn-sm btn-success saveEditUserBtn' data-id='${userId}'><i class='fas fa-check'></i></button>
                <button class='btn btn-sm btn-secondary cancelEditUserBtn'><i class='fas fa-times'></i></button>
            </td>
        `);
    });

    // Annuler édition
    $(document).on('click', '.cancelEditUserBtn', function() {
        loadUsersTable();
    });

    // Sauvegarder édition
    $(document).on('click', '.saveEditUserBtn', function() {
        var tr = $(this).closest('tr');
        var userId = $(this).data('id');
        var login = tr.find('#editName').val();
        var email = tr.find('#editEmail').val();
        var etat = tr.find('#editEtat').val();
        $.ajax({
            url: '/administration/utilisateurs/' + userId,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                login: login,
                email: email,
                etat: etat
            },
            success: function(resp) {
                showSystemMessage('success', resp.message || 'Utilisateur modifié.');
                loadUsersTable();
            },
            error: function(xhr) {
                let msg = 'Erreur lors de la modification.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showSystemMessage('error', msg);
            }
        });
    });
});
</script>
@endpush

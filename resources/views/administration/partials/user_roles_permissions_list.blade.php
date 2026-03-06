{{-- Partial AJAX : rôles cochables pour un utilisateur + permissions héritées --}}
{{-- Reçoit : $user, $roles (Collection), $permissions (Collection), $userRoles (Collection codes), $userPermissions (Collection codes) --}}

<div class="mb-3">
    <strong class="text-warning">
        <i class="fas fa-user mr-1"></i>
        @if($user->agent)
            [{{ $user->agent->matricule }}] {{ $user->agent->nom }} {{ $user->agent->postnom }}
        @else
            {{ $user->email }}
        @endif
    </strong>
    <span class="badge badge-warning ml-2">{{ $userRoles->count() }} rôle(s) attribué(s)</span>
</div>

@if($roles->isEmpty())
    <div class="text-center text-muted py-3">
        <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucun rôle défini.
    </div>
@else
    <div class="row mb-3">
        @foreach($roles as $role)
        <div class="col-md-6 mb-2">
            <div class="custom-control custom-checkbox">
                <input type="checkbox"
                       class="custom-control-input user-role-checkbox"
                       id="role_{{ $role->code }}"
                       data-role-code="{{ $role->code }}"
                       {{ $userRoles->contains($role->code) ? 'checked' : '' }}>
                <label class="custom-control-label" for="role_{{ $role->code }}">
                    <span class="font-weight-bold text-primary small">{{ $role->code }}</span><br>
                    <span class="text-muted small">{{ $role->nom }}</span>
                </label>
            </div>
        </div>
        @endforeach
    </div>

    @if($userPermissions->isNotEmpty())
    <hr>
    <div class="mb-1">
        <small class="text-muted font-weight-bold">
            <i class="fas fa-key mr-1"></i> Permissions héritées via les rôles ({{ $userPermissions->count() }}) :
        </small>
    </div>
    <div class="d-flex flex-wrap gap-1">
        @foreach($userPermissions as $permCode)
            <span class="badge badge-secondary mb-1">{{ $permCode }}</span>
        @endforeach
    </div>
    @endif
@endif

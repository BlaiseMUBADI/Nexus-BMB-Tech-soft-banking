{{-- Affiche les rôles et permissions d'un utilisateur --}}
@if($user)
    <div class="mb-2"><strong>Rôles attribués :</strong></div>
    <div class="row mb-3">
        @foreach($roles as $role)
            <div class="col-md-4">
                <div class="form-check">
                    <input class="form-check-input user-role-checkbox" type="checkbox" id="role_{{ $role->code }}"
                        data-role-code="{{ $role->code }}" {{ $userRoles->contains($role->code) ? 'checked' : '' }}>
                    <label class="form-check-label d-flex align-items-center" for="role_{{ $role->code }}" style="width:100%">
                        <span style="font-size:0.95em; white-space:nowrap; display: flex; align-items: center;">
                            <strong>[{{ $role->code }}]</strong> {{ $role->nom }}
                            @if($userRoles->contains($role->code))
                                <span class="badge badge-pill badge-success ml-2" style="font-size:0.75em; min-width:40px;">Hérité</span>
                            @endif
                        </span>
                    </label>
                </div>
            </div>
        @endforeach
    </div>
    {{-- Permissions héritées supprimées à la demande --}}
@else
    <div class="alert alert-info">Aucun utilisateur sélectionné.</div>
@endif

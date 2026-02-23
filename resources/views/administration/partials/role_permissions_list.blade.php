<div class="form-group">
    <label>Permissions disponibles :</label>
    <div class="mb-3">
        <label style="font-weight:600; color:#e02424; cursor:pointer;">
            <input type="checkbox" id="disableAllPermissions" style="margin-right:0.5em; accent-color:#e02424;"> Désactiver tout
        </label>
    </div>
    <div class="row">
        @foreach($allPermissions as $permission)
            <div class="col-md-4">
                <div class="form-check">
                    <input class="form-check-input perm-checkbox" type="checkbox" id="perm_{{ $permission->code }}"
                        data-perm-code="{{ $permission->code }}" {{ in_array($permission->code, $attached) ? 'checked' : '' }}>
                    <label class="form-check-label" for="perm_{{ $permission->code }}">
                        {{ $permission->nom }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</div>

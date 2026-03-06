{{-- Partial AJAX : permissions cochables pour un rôle --}}
{{-- Reçoit : $role, $allPermissions (Collection), $attached (array de codes) --}}

<div class="d-flex align-items-center justify-content-between mb-2">
    <strong class="text-primary">
        <i class="fas fa-shield-alt mr-1"></i> {{ $role->nom }}
    </strong>
    <span class="badge badge-{{ count($attached) > 0 ? 'success' : 'secondary' }}">
        {{ count($attached) }} / {{ $allPermissions->count() }} permission(s)
    </span>
</div>

@if($allPermissions->isEmpty())
    <div class="text-center text-muted py-3">
        <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucune permission définie.
    </div>
@else
    <div class="d-flex align-items-center justify-content-between mb-2">
        <input type="text"
               id="searchPermList"
               class="form-control form-control-sm mr-2"
               placeholder="🔍 Filtrer les permissions…"
               style="max-width:260px">
        <label class="text-danger font-weight-bold mb-0" style="cursor:pointer;white-space:nowrap">
            <input type="checkbox" id="disableAllPermissions" style="accent-color:#e02424">
            &nbsp;Tout décocher
        </label>
    </div>
    <div class="row" id="permListRows">
        @foreach($allPermissions as $perm)
        <div class="col-md-6 mb-1 perm-item"
             data-search="{{ strtolower($perm->code . ' ' . $perm->nom) }}">
            <div class="custom-control custom-checkbox">
                <input type="checkbox"
                       class="custom-control-input perm-checkbox"
                       id="perm_{{ $perm->code }}"
                       data-perm-code="{{ $perm->code }}"
                       {{ in_array($perm->code, $attached) ? 'checked' : '' }}>
                <label class="custom-control-label" for="perm_{{ $perm->code }}">
                    <span class="font-weight-bold text-success small">{{ $perm->code }}</span><br>
                    <span class="text-muted small">{{ $perm->nom }}</span>
                </label>
            </div>
        </div>
        @endforeach
    </div>
    <div id="permNoResult" class="text-center text-muted py-2 d-none">
        <i class="fas fa-search mr-1"></i> Aucune permission correspondante.
    </div>
    <script>
    (function () {
        var input = document.getElementById('searchPermList');
        if (!input) return;
        input.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('#permListRows .perm-item');
            var visible = 0;
            items.forEach(function (el) {
                var match = !q || el.dataset.search.indexOf(q) > -1;
                el.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            var noResult = document.getElementById('permNoResult');
            noResult.classList.toggle('d-none', visible > 0);
        });
    })();
    </script>
@endif

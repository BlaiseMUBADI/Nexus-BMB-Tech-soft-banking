{{-- Partial AJAX : permissions cochables pour un rôle, groupées par module --}}
{{-- Reçoit : $role, $allPermissions, $attached (array), $grouped (Collection), $moduleMap (array) --}}

<div class="d-flex align-items-center justify-content-between mb-3">
    <strong class="text-primary">
        <i class="fas fa-shield-alt mr-1"></i> {{ $role->nom }}
    </strong>
    <span class="badge badge-{{ count($attached) > 0 ? 'success' : 'secondary' }} badge-pill px-2 py-1">
        <span id="attachedCount">{{ count($attached) }}</span> / {{ $allPermissions->count() }} permission(s)
    </span>
</div>

@if($allPermissions->isEmpty())
    <div class="text-center text-muted py-3">
        <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Aucune permission définie.
    </div>
@else
    {{-- Barre de recherche + tout cocher / tout décocher --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <input type="text"
               id="searchPermList"
               class="form-control form-control-sm mr-2"
               placeholder="🔍 Filtrer les permissions…"
               style="max-width:250px">
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" id="checkAllPermissions" class="btn btn-outline-success">
                <i class="fas fa-check-double mr-1"></i> Tout cocher
            </button>
            <button type="button" id="uncheckAllPermissions" class="btn btn-outline-danger">
                <i class="fas fa-times mr-1"></i> Tout décocher
            </button>
        </div>
    </div>

    <div id="permModuleAccordion">
        @foreach($grouped->sortKeys() as $moduleNum => $perms)
        @php
            $attached   ??= [];
            $mod        = $moduleMap[$moduleNum] ?? ['label' => 'Autre', 'icon' => 'fa-puzzle-piece', 'color' => 'secondary'];
            $modTotal   = $perms->count();
            $modChecked = $perms->filter(fn($p) => in_array($p->code, $attached))->count();
            $allChecked = ($modChecked === $modTotal);
            $collapseId = 'module_' . $moduleNum;
        @endphp
        <div class="card card-{{ $mod['color'] }} card-outline mb-1 perm-module-card"
             data-module="{{ strtolower($mod['label']) }}">
            <div class="card-header p-0">
                <a class="d-flex align-items-center justify-content-between p-2 text-reset text-decoration-none"
                   data-toggle="collapse"
                   href="#{{ $collapseId }}"
                   role="button">
                    <span>
                        <i class="fas {{ $mod['icon'] }} text-{{ $mod['color'] }} mr-2"></i>
                        <strong>{{ $mod['label'] }}</strong>
                    </span>
                    <span class="d-flex align-items-center">
                        <span class="badge badge-{{ $allChecked ? $mod['color'] : 'secondary' }} badge-pill mr-2"
                              id="counter_{{ $collapseId }}">
                            {{ $modChecked }}/{{ $modTotal }}
                        </span>
                        <i class="fas fa-chevron-down text-muted small"></i>
                    </span>
                </a>
            </div>
            <div id="{{ $collapseId }}" class="collapse {{ $modChecked > 0 ? 'show' : '' }}">
                <div class="card-body pt-2 pb-2">
                    <div class="mb-2 text-right">
                        <label class="small text-muted mb-0" style="cursor:pointer">
                            <input type="checkbox"
                                   class="module-master-checkbox"
                                   data-target="{{ $collapseId }}"
                                   {{ $allChecked ? 'checked' : '' }}>
                            &nbsp;Tout le module
                        </label>
                    </div>
                    <div class="row">
                        @foreach($perms as $perm)
                        <div class="col-md-6 mb-1 perm-item"
                             data-search="{{ strtolower($perm->code . ' ' . $perm->nom . ' ' . $mod['label']) }}">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input perm-checkbox"
                                       id="perm_{{ $perm->code }}"
                                       data-perm-code="{{ $perm->code }}"
                                       data-module-id="{{ $collapseId }}"
                                       {{ in_array($perm->code, $attached) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="perm_{{ $perm->code }}">
                                    <span class="font-weight-bold text-{{ $mod['color'] }} small">{{ $perm->code }}</span><br>
                                    <span class="text-muted small">{{ $perm->nom }}</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="permNoResult" class="text-center text-muted py-2 d-none">
        <i class="fas fa-search mr-1"></i> Aucune permission correspondante.
    </div>

    <script>
    (function () {
        // ── Recherche live ──────────────────────────────────────────────────
        var input = document.getElementById('searchPermList');
        if (input) {
            input.addEventListener('input', function () {
                var q = this.value.toLowerCase().trim();
                var totalVisible = 0;
                document.querySelectorAll('.perm-module-card').forEach(function(card) {
                    var items = card.querySelectorAll('.perm-item');
                    var visInCard = 0;
                    items.forEach(function(el) {
                        var match = !q || el.dataset.search.indexOf(q) > -1;
                        el.style.display = match ? '' : 'none';
                        if (match) visInCard++;
                    });
                    card.style.display = visInCard > 0 ? '' : 'none';
                    if (visInCard > 0) totalVisible++;
                    if (q && visInCard > 0) {
                        $('#' + card.querySelector('.collapse').id).collapse('show');
                    }
                });
                document.getElementById('permNoResult').classList.toggle('d-none', totalVisible > 0);
            });
        }

        // ── Tout cocher ─────────────────────────────────────────────────────
        document.getElementById('checkAllPermissions').addEventListener('click', function() {
            document.querySelectorAll('#permModuleAccordion .perm-checkbox:not(:checked)').forEach(function(cb) {
                cb.checked = true;
                cb.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        // ── Tout décocher ───────────────────────────────────────────────────
        document.getElementById('uncheckAllPermissions').addEventListener('click', function() {
            document.querySelectorAll('#permModuleAccordion .perm-checkbox:checked').forEach(function(cb) {
                cb.checked = false;
                cb.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        // ── Master checkbox de module ────────────────────────────────────────
        document.querySelectorAll('.module-master-checkbox').forEach(function(master) {
            master.addEventListener('change', function() {
                var targetId = this.dataset.target;
                var checked  = this.checked;
                document.querySelectorAll('#' + targetId + ' .perm-checkbox').forEach(function(cb) {
                    if (cb.checked !== checked) {
                        cb.checked = checked;
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            });
        });

        // ── Mise à jour compteurs après chaque toggle AJAX ───────────────────
        document.addEventListener('perm:updated', function(e) {
            var moduleId = e.detail ? e.detail.moduleId : null;
            if (!moduleId) return;
            var collapseEl = document.getElementById(moduleId);
            if (!collapseEl) return;
            var allCbs     = collapseEl.querySelectorAll('.perm-checkbox');
            var checkedCbs = collapseEl.querySelectorAll('.perm-checkbox:checked');
            var counter    = document.getElementById('counter_' + moduleId);
            var color      = collapseEl.closest('.perm-module-card').dataset.module;
            if (counter) counter.textContent = checkedCbs.length + '/' + allCbs.length;
            var master = document.querySelector('.module-master-checkbox[data-target="' + moduleId + '"]');
            if (master) master.checked = (checkedCbs.length === allCbs.length);
        });

        // ── Compteur global ─────────────────────────────────────────────────
        document.addEventListener('perm:updated', function() {
            var total   = document.querySelectorAll('#permModuleAccordion .perm-checkbox').length;
            var checked = document.querySelectorAll('#permModuleAccordion .perm-checkbox:checked').length;
            var el = document.getElementById('attachedCount');
            if (el) el.textContent = checked;
        });
    })();
    </script>
@endif

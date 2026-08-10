@extends('layouts.app')

@section('page_title', 'En Cours – Crédits')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'En Cours')

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- ── Titre ────────────────────────────────────────────── --}}
    <div class="row mb-3">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="mb-0"><i class="fas fa-coins mr-2 text-warning"></i>En Cours – Capital chez les clients &amp; intérêts perçus</h4>
        </div>
        <div class="col-12">
            <small class="text-muted">
                Dossiers actuellement débloqués / en remboursement / en retard. Le <strong>capital restant</strong> diminue
                à chaque remboursement ; l'<strong>intérêt perçu</strong> et la <strong>commission perçue</strong> augmentent
                à chaque mensualité réglée.
            </small>
        </div>
    </div>

    {{-- ── Filtre dynamique ─────────────────────────────────── --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-header py-2">
            <h6 class="mb-0"><i class="fas fa-filter mr-1"></i>Filtrer</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('credit.en_cours') }}" class="form-row" id="formFiltreEnCours">
                <div class="col-6 col-md-2 mb-2">
                    <label class="small mb-1">Devise</label>
                    <select name="devise" class="form-control form-control-sm filtre-auto">
                        <option value="">Toutes</option>
                        @foreach(['CDF','USD'] as $d)
                            <option value="{{ $d }}" {{ request('devise') === $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="small mb-1">Statut</label>
                    <select name="statut" class="form-control form-control-sm filtre-auto">
                        <option value="">Tous (en cours)</option>
                        <option value="DEBLOQUE" {{ request('statut') === 'DEBLOQUE' ? 'selected' : '' }}>Débloqué</option>
                        <option value="EN_REMBOURSEMENT" {{ request('statut') === 'EN_REMBOURSEMENT' ? 'selected' : '' }}>En remboursement</option>
                        <option value="EN_RETARD" {{ request('statut') === 'EN_RETARD' ? 'selected' : '' }}>En retard</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="small mb-1">Zone</label>
                    <select name="zone" class="form-control form-control-sm filtre-auto">
                        <option value="">Toutes</option>
                        @foreach($zonesDisponibles as $z)
                            <option value="{{ $z->code_zone }}" {{ request('zone') === $z->code_zone ? 'selected' : '' }}>{{ $z->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="small mb-1">Agent analyste</label>
                    <select name="agent_analyse" class="form-control form-control-sm filtre-auto">
                        <option value="">Tous</option>
                        @foreach($agentsAnalystes as $mat)
                            <option value="{{ $mat }}" {{ request('agent_analyse') === $mat ? 'selected' : '' }}>{{ $mat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="small mb-1">Type de crédit</label>
                    <select name="type_credit" class="form-control form-control-sm filtre-auto">
                        <option value="">Tous</option>
                        @foreach(['INDIVIDUEL','SOLIDAIRE','PME'] as $t)
                            <option value="{{ $t }}" {{ request('type_credit') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="small mb-1">Recherche (n° dossier / client)</label>
                    <input type="text" name="search" class="form-control form-control-sm filtre-auto-debounce" value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="small mb-1">Déboursé depuis le</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm filtre-auto" value="{{ request('date_debut') }}">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="small mb-1">jusqu'au</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm filtre-auto" value="{{ request('date_fin') }}">
                </div>
            </form>
        </div>
    </div>

    {{-- ── Cartes par devise ────────────────────────────────── --}}
    @forelse($totauxParDevise as $devise => $t)
    <div class="card card-outline card-success mb-3">
        <div class="card-header py-2 bg-gradient-dark">
            <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-2"></i>{{ $devise }} — {{ $t['nombre_dossiers'] }} dossier(s) en cours</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-6 col-md-2 mb-3">
                    <div class="text-muted small">Capital décaissé <i class="fas fa-info-circle" title="Montant approuvé = dette contractuelle du client, avant retenue caution/frais"></i></div>
                    <div class="h4 mb-0">{{ number_format($t['capital_decaisse'], 2, ',', ' ') }}</div>
                    <small class="text-muted">{{ $devise }}</small>
                    @if(($t['net_verse'] ?? 0) > 0 && $t['net_verse'] != $t['capital_decaisse'])
                        <div class="text-muted" style="font-size:.7rem;">dont {{ number_format($t['net_verse'], 2, ',', ' ') }} net remis en main</div>
                    @endif
                </div>
                <div class="col-6 col-md-2 mb-3">
                    <div class="text-muted small">Capital remboursé</div>
                    <div class="h4 mb-0 text-success">{{ number_format($t['capital_rembourse'], 2, ',', ' ') }}</div>
                    <small class="text-muted">{{ $devise }}</small>
                </div>
                <div class="col-6 col-md-2 mb-3">
                    <div class="text-muted small"><i class="fas fa-hand-holding-usd mr-1"></i>Capital chez les clients</div>
                    <div class="h3 mb-0 text-danger font-weight-bold">{{ number_format($t['capital_restant'], 2, ',', ' ') }}</div>
                    <small class="text-muted">{{ $devise }}</small>
                </div>
                <div class="col-6 col-md-2 mb-3">
                    <div class="text-muted small"><i class="fas fa-chart-line mr-1"></i>Intérêt perçu</div>
                    <div class="h3 mb-0 text-info font-weight-bold">{{ number_format($t['interet_percu'], 2, ',', ' ') }}</div>
                    <small class="text-muted">{{ $devise }}</small>
                </div>
                <div class="col-6 col-md-2 mb-3">
                    <div class="text-muted small">Commission perçue</div>
                    <div class="h4 mb-0 text-warning">{{ number_format($t['commission_percu'], 2, ',', ' ') }}</div>
                    <small class="text-muted">{{ $devise }}</small>
                </div>
                <div class="col-6 col-md-2 mb-3">
                    <div class="text-muted small">Total perçu (cap+int+com+pén)</div>
                    <div class="h4 mb-0">{{ number_format($t['total_percu'], 2, ',', ' ') }}</div>
                    <small class="text-muted">{{ $devise }}</small>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info">Aucun dossier en cours ne correspond à ce filtre.</div>
    @endforelse

    {{-- ── Liste détaillée ──────────────────────────────────── --}}
    <div class="card card-outline card-secondary">
        <div class="card-header py-2">
            <h6 class="mb-0"><i class="fas fa-list mr-1"></i>Détail des dossiers en cours</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>N° Dossier</th><th>Client</th><th>Zone</th>
                        <th class="text-right">Montant approuvé</th>
                        <th class="text-right">Capital restant</th>
                        <th class="text-right">Intérêt perçu</th>
                        <th>Statut</th><th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($dossiersEnCours as $d)
                    @php
                        // Dette contractuelle du client (montant_approuve), pas le net remis en main.
                        $capitalDecaisseD = (float) ($d->montant_approuve ?? 0);
                        $capitalRembourseD = (float) $d->remboursements->sum('dont_capital');
                        $interetPercuD = (float) $d->remboursements->sum('dont_interet');
                        $sc = ['EN_REMBOURSEMENT'=>'primary','EN_RETARD'=>'danger','DEBLOQUE'=>'success'];
                    @endphp
                    <tr>
                        <td><a href="{{ route('credit.show', $d) }}">{{ $d->numero_dossier }}</a></td>
                        <td>{{ $d->client?->nom }} {{ $d->client?->prenom }}</td>
                        <td><small>{{ $d->zone?->nom ?? $d->code_zone }}</small></td>
                        <td class="text-right">{{ number_format($d->montant_approuve ?? $d->montant_demande, 2, ',', ' ') }} {{ $d->devise }}</td>
                        <td class="text-right font-weight-bold text-danger">{{ number_format(max(0, $capitalDecaisseD - $capitalRembourseD), 2, ',', ' ') }} {{ $d->devise }}</td>
                        <td class="text-right text-info">{{ number_format($interetPercuD, 2, ',', ' ') }} {{ $d->devise }}</td>
                        <td><span class="badge badge-{{ $sc[$d->statut_global] ?? 'secondary' }}">{{ str_replace('_',' ', $d->statut_global) }}</span></td>
                        <td><a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-3">Aucun dossier en cours.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $dossiersEnCours->links() }}
        </div>
    </div>

</div>
</section>
@endsection

@push('js')
<script>
(function () {
    var form = document.getElementById('formFiltreEnCours');
    if (!form) return;

    // Selects + dates : soumission immédiate dès qu'une valeur change
    form.querySelectorAll('.filtre-auto').forEach(function (el) {
        el.addEventListener('change', function () {
            form.submit();
        });
    });

    // Champ texte (recherche) : soumission après une courte pause de frappe,
    // pour éviter de renvoyer une requête à chaque lettre tapée.
    var debounceTimer = null;
    form.querySelectorAll('.filtre-auto-debounce').forEach(function (el) {
        el.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                form.submit();
            }, 600);
        });
    });
})();
</script>
@endpush

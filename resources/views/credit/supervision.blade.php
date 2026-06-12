@extends('layouts.app')

@section('page_title', 'Supervision des Crédits')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Supervision')

@section('content')
<section class="content">
<div class="container-fluid">

{{-- ── Compteurs d'alertes ──────────────────────────────────────────── --}}
<div class="row mb-3">
    <div class="col-md-3">
        <div class="small-box bg-gradient-danger">
            <div class="inner">
                <h3>{{ $stats['total_retard'] ?? 0 }}</h3>
                <p>Échéances en retard</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-gradient-warning">
            <div class="inner">
                <h3>{{ $stats['total_suspects'] ?? 0 }}</h3>
                <p>Dossiers suspects</p>
            </div>
            <div class="icon"><i class="fas fa-user-secret"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-gradient-secondary">
            <div class="inner">
                <h3>{{ $stats['total_suspendus'] ?? 0 }}</h3>
                <p>Dossiers suspendus</p>
            </div>
            <div class="icon"><i class="fas fa-pause-circle"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-gradient-success">
            <div class="inner">
                <h3>{{ $stats['total_pret_debloquer'] ?? 0 }}</h3>
                <p>Prêts au déblocage</p>
            </div>
            <div class="icon"><i class="fas fa-unlock"></i></div>
        </div>
    </div>
</div>

<div class="row">

{{-- ── Dossiers en retard ──────────────────────────────────────────── --}}
<div class="col-12 mb-3">
<div class="card card-outline card-danger">
    <div class="card-header">
        <h5 class="card-title mb-0 text-danger">
            <i class="fas fa-exclamation-circle mr-2"></i>Dossiers actifs avec échéances en retard
        </h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($dossiers_retard->count())
        <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>N° Dossier</th><th>Client</th><th>Zone</th>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Nombre d'échéances dont la date est dépassée et qui n'ont pas encore été payées (statut ≠ PAYE). Chaque unité représente une échéance en souffrance.">Échéances retard</th>
                    <th class="text-center">Montant impayé</th>
                    <th>Dernière éch. due</th><th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($dossiers_retard as $d)
            <tr>
                <td><strong>{{ $d->numero_dossier }}</strong></td>
                <td>
                    {{ optional($d->client)->nom }} {{ optional($d->client)->prenom }}
                    <br><small class="text-muted">{{ $d->client_matricule }}</small>
                </td>
                <td>{{ $d->zone?->nom ?? $d->zone?->nom_zone ?? $d->code_zone ?? '–' }}</td>
                <td class="text-center">
                    <span class="badge badge-danger"
                          data-toggle="tooltip" data-placement="top"
                          title="{{ $d->nb_echeances_retard }} échéance(s) non payée(s) et dépassée(s) sur un total de {{ $d->echeancier?->echeances->count() ?? 0 }} échéances. Montant total impayé : {{ number_format($d->montant_impaye ?? 0, 0, ',', ' ') }}{{ $d->devise === 'USD' ? '$' : ($d->devise === 'CDF' ? 'Fc' : '€') }}.">
                        {{ $d->nb_echeances_retard ?? 0 }}
                    </span>
                </td>
                <td class="text-center text-danger font-weight-bold">
                    {{ number_format($d->montant_impaye ?? 0, 0, ',', ' ') }}{{ $d->devise === 'USD' ? '$' : ($d->devise === 'CDF' ? 'Fc' : '€') }}
                </td>
                <td>{{ $d->date_derniere_echeance_due ? \Carbon\Carbon::parse($d->date_derniere_echeance_due)->format('d/m/Y') : '–' }}</td>
                <td class="text-center">
                    <a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Voir le détail">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if(in_array('EBEN-PER111', $userPermCodes ?? []))
                    <a href="{{ route('credit.remboursement', $d) }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Enregistrer un remboursement">
                        <i class="fas fa-money-bill-wave"></i>
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @else
            <div class="p-3 text-muted text-center">
                <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                Aucun dossier en retard.
            </div>
        @endif
    </div>
</div>
</div>

{{-- ── Tableau unifié : Alertes & Débloquages ────────────────────────── --}}
<div class="col-12 mb-3">
<div class="card card-outline card-warning">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-exclamation-triangle mr-2"></i>Dossiers suspects, suspendus &amp; prêts au déblocage
        </h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        @php
            $dossiers_unis = $dossiers_alertes->merge($dossiers_pret_debloquer)->sortByDesc('created_at');
        @endphp
        @if($dossiers_unis->count())
        <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>N° Dossier</th><th>Client</th>
                    <th class="text-center">Statut</th>
                    <th class="text-center">Montant</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($dossiers_unis as $d)
            <tr>
                <td><strong>{{ $d->numero_dossier }}</strong></td>
                <td>{{ optional($d->client)->nom }} {{ optional($d->client)->prenom }}
                    <br><small class="text-muted">{{ $d->client_matricule }}</small></td>
                <td class="text-center">
                    @if($d->statut_global === 'SUSPECT')
                        <span class="badge badge-danger"
                              data-toggle="tooltip" data-placement="top"
                              title="Dossier signalé comme suspect – vérification approfondie requise avant toute poursuite.">
                            <i class="fas fa-user-secret mr-1"></i>SUSPECT
                        </span>
                    @elseif($d->statut_global === 'SUSPENDU')
                        <span class="badge badge-warning"
                              data-toggle="tooltip" data-placement="top"
                              title="Dossier suspendu temporairement par l'administration – en attente de levée de suspension.">
                            <i class="fas fa-pause-circle mr-1"></i>SUSPENDU
                        </span>
                    @elseif($d->statut_global === 'PRET_A_DEBLOQUER')
                        <span class="badge badge-success"
                              data-toggle="tooltip" data-placement="top"
                              title="Toutes les validations (Agent, Contrôleur, ChOps, Gérant) sont approuvées – prêt à être débloqué.">
                            <i class="fas fa-unlock-alt mr-1"></i>PRÊT À DÉBLOQUER
                        </span>
                    @else
                        {!! $d->badgeStatut() !!}
                    @endif
                </td>
                <td class="text-center font-weight-bold">
                    {{ number_format($d->montant_demande, 0, ',', ' ') }}{{ $d->devise === 'USD' ? '$' : ($d->devise === 'CDF' ? 'Fc' : '€') }}
                </td>
                <td class="text-center">
                    <a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-info mr-1"
                       data-toggle="tooltip" data-placement="top" title="Voir le détail complet du dossier">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($d->statut_global === 'PRET_A_DEBLOQUER' && in_array('EBEN-PER64', $userPermCodes ?? []))
                    <a href="{{ route('credit.deblocage', $d) }}" class="btn btn-xs btn-success mr-1"
                       data-toggle="tooltip" data-placement="top" title="Procéder au déblocage des fonds">
                        <i class="fas fa-unlock-alt"></i>
                    </a>
                    @endif
                    @if($d->statut_global === 'SUSPECT' && in_array('EBEN-PER69', $userPermCodes ?? []))
                    <form method="POST" action="{{ route('credit.lever_suspicion', $d) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-outline-warning mr-1"
                                onclick="return confirm('Lever la suspicion sur ce dossier ?')"
                                data-toggle="tooltip" data-placement="top" title="Lever le signalement suspect">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif
                    @if($d->statut_global === 'SUSPENDU' && in_array('EBEN-PER69', $userPermCodes ?? []))
                    <form method="POST" action="{{ route('credit.lever_suspension', $d) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-outline-secondary"
                                onclick="return confirm('Lever la suspension de ce dossier ?')"
                                data-toggle="tooltip" data-placement="top" title="Reprendre le traitement du dossier">
                            <i class="fas fa-play"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @else
            <div class="p-3 text-muted text-center">
                <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                Aucun dossier en alerte ni en attente de déblocage.
            </div>
        @endif
    </div>
</div>
</div>

</div>{{-- /.row --}}

{{-- ── Stats par zone ──────────────────────────────────────────────── --}}
@if(!empty($stats_zones) && count($stats_zones) > 0)
<div class="card card-outline card-info">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Statistiques par zone</h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Zone</th>
                    <th class="text-center">Total dossiers</th>
                    <th class="text-center">Actifs</th>
                    <th class="text-center">Retard</th>
                    <th class="text-right">Encours total</th>
                    <th class="text-right">Impayés</th>
                    <th class="text-center">Taux recouvrement</th>
                    <th class="text-center" style="width:50px"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($stats_zones as $z)
            <tr>
                <td>
                    <a href="{{ route('credit.index', ['zone' => $z->code_zone]) }}"
                       class="text-decoration-none text-white"
                       data-toggle="tooltip" data-placement="top"
                       title="Filtrer les dossiers de cette zone">
                        <i class="fas fa-external-link-alt mr-1 small" style="opacity:0.5"></i>
                        {{ $z->zone_nom ?? $z->code_zone ?? '–' }}
                    </a>
                </td>
                <td class="text-center" style="white-space:nowrap">{!! $z->total_texte !!}</td>
                <td class="text-center" style="white-space:nowrap">{!! $z->actifs_texte !!}</td>
                <td class="text-center" style="white-space:nowrap">{!! $z->retard_texte !!}</td>
                <td class="text-right" style="white-space:nowrap">{!! $z->encours_texte ?? '—' !!}</td>
                <td class="text-right text-danger" style="white-space:nowrap">{!! $z->impayes_texte ?? '—' !!}</td>
                <td class="text-center">
                    @php
                        $totalEncours = array_sum($z->encours_par_devise ?? []);
                        $totalImpayes = array_sum($z->impayes_par_devise ?? []);
                        $taux = $totalEncours > 0 ? number_format(100 - ($totalImpayes / $totalEncours * 100), 1) : 100;
                    @endphp
                    <span class="badge badge-{{ $taux >= 90 ? 'success' : ($taux >= 70 ? 'warning' : 'danger') }}">
                        {{ $taux }} %
                    </span>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endif

</div>
</section>
@endsection

@push('js')
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        trigger: 'hover focus',
        delay: { show: 200, hide: 100 }
    });
});
</script>
@endpush

@extends('layouts.app')

@section('page_title', 'Supervision des Crédits')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Supervision')

@section('content')
<section class="content">
<div class="container-fluid">

{{-- ── Compteurs d'alertes --}}
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

{{-- ── Dossiers en retard ─────────────────────────────────────── --}}
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
                    <th>Échéances retard</th><th>Montant impayé</th>
                    <th>Dernière éch. due</th><th>Actions</th>
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
                <td>{{ $d->code_zone ?? '–' }}</td>
                <td><span class="badge badge-danger">{{ $d->nb_echeances_retard ?? '?' }}</span></td>
                <td class="text-right text-danger font-weight-bold">
                    {{ number_format($d->montant_impaye ?? 0, 2, ',', ' ') }} {{ $d->devise }}
                </td>
                <td>{{ $d->date_derniere_echeance_due ?? '–' }}</td>
                <td>
                    <a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if(in_array('EBEN-PER65', $userPermCodes ?? []))
                    <a href="{{ route('credit.remboursement', $d) }}" class="btn btn-xs btn-success">
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

{{-- ── Prêts au déblocage ─────────────────────────────────────── --}}
<div class="col-md-6 mb-3">
<div class="card card-outline card-success">
    <div class="card-header">
        <h5 class="card-title mb-0 text-success">
            <i class="fas fa-unlock mr-2"></i>Prêts au déblocage
        </h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($dossiers_pret_debloquer->count())
        <table class="table table-sm table-hover mb-0">
            <thead class="bg-light"><tr>
                <th>N° Dossier</th><th>Client</th><th>Montant</th><th></th>
            </tr></thead>
            <tbody>
            @foreach($dossiers_pret_debloquer as $d)
            <tr>
                <td><strong>{{ $d->numero_dossier }}</strong></td>
                <td>{{ optional($d->client)->nom }} {{ optional($d->client)->prenom }}</td>
                <td class="text-right">{{ number_format($d->montant_demande, 0, ',', ' ') }} {{ $d->devise }}</td>
                <td>
                    @if(in_array('EBEN-PER64', $userPermCodes ?? []))
                    <a href="{{ route('credit.deblocage', $d) }}" class="btn btn-xs btn-success">
                        <i class="fas fa-unlock mr-1"></i>Débloquer
                    </a>
                    @else
                    <a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
            <p class="text-muted text-center py-3">Aucun dossier en attente de déblocage.</p>
        @endif
    </div>
</div>
</div>

{{-- ── Dossiers suspects / suspendus ────────────────────────── --}}
<div class="col-md-6 mb-3">
<div class="card card-outline card-warning">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-exclamation-triangle mr-2"></i>Dossiers suspects &amp; suspendus
        </h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($dossiers_alertes->count())
        <table class="table table-sm table-hover mb-0">
            <thead class="bg-light"><tr>
                <th>N°</th><th>Client</th><th>Statut</th><th>Actions</th>
            </tr></thead>
            <tbody>
            @foreach($dossiers_alertes as $d)
            <tr class="{{ $d->statut === 'SUSPECT' ? 'table-warning' : 'table-secondary' }}">
                <td>{{ $d->numero_dossier }}</td>
                <td>{{ optional($d->client)->nom }} {{ optional($d->client)->prenom }}</td>
                <td>{!! $d->badgeStatut() !!}</td>
                <td>
                    <a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($d->statut === 'SUSPECT' && in_array('EBEN-PER71', $userPermCodes ?? []))
                    <form method="POST" action="{{ route('credit.lever_suspicion', $d) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-outline-warning"
                                onclick="return confirm('Lever la suspicion ?')" title="Lever suspicion">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif
                    @if($d->statut === 'SUSPENDU' && in_array('EBEN-PER70', $userPermCodes ?? []))
                    <form method="POST" action="{{ route('credit.lever_suspension', $d) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-outline-secondary"
                                onclick="return confirm('Lever la suspension ?')" title="Lever suspension">
                            <i class="fas fa-play"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
            <p class="text-muted text-center py-3">Aucun dossier en alerte.</p>
        @endif
    </div>
</div>
</div>

</div>{{-- /.row --}}

{{-- ── Stats par zone ─────────────────────────────────────────── --}}
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
                </tr>
            </thead>
            <tbody>
            @foreach($stats_zones as $z)
            <tr>
                <td><strong>{{ $z->code_zone ?? '–' }}</strong></td>
                <td class="text-center">{{ $z->total_dossiers }}</td>
                <td class="text-center"><span class="badge badge-success">{{ $z->dossiers_actifs }}</span></td>
                <td class="text-center"><span class="badge badge-{{ $z->en_retard > 0 ? 'danger' : 'success' }}">{{ $z->en_retard }}</span></td>
                <td class="text-right">{{ number_format($z->encours ?? 0, 0, ',', ' ') }}</td>
                <td class="text-right text-danger">{{ number_format($z->impayes ?? 0, 0, ',', ' ') }}</td>
                <td class="text-center">
                    @php
                        $taux = $z->encours > 0 ? number_format(100 - ($z->impayes / $z->encours * 100), 1) : 100;
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

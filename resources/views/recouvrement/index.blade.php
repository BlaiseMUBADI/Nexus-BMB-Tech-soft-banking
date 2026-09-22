@extends('layouts.app')

@section('page_title', 'Tableau de Bord - Recouvrement Automatique')
@section('breadcrumb_parent', 'Crédit')
@section('breadcrumb', 'Recouvrement Auto')

@section('content')
<section class="content">
<div class="container-fluid pt-3"> {{-- Réduit l'espace en haut avec pt-3 au lieu de mt-4 --}}
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Dossiers en retard
                        <span class="badge badge-light ml-1">{{ $dossiers->total() }}</span>
                    </button>
                    <a href="{{ route('recouvrement.historique') }}" class="btn btn-outline-primary btn-sm px-3">
                        <i class="fas fa-history mr-1"></i> Historique
                    </a>
                </div>

                {{-- Recherche : nom complet en ordre libre / matricule / n° dossier --}}
                <form method="GET" action="{{ route('recouvrement.index') }}" class="form-inline">
                    <div class="input-group input-group-sm" style="width:320px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control" autocomplete="off"
                               placeholder="Nom complet, matricule, n° dossier...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-primary">Rechercher</button>
                            @if(request('search'))
                                <a href="{{ route('recouvrement.index') }}" class="btn btn-outline-secondary" title="Effacer">&times;</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sync-alt text-warning mr-2"></i>
                        Recouvrement Automatique RMB
                    </h5>
                    <form action="{{ route('recouvrement.run') }}" method="POST" id="formRecouvrement">
                        @csrf
                        <button type="button" class="btn btn-warning btn-sm font-weight-bold" id="btnLancerRecouvrement">
                            <i class="fas fa-bolt mr-1"></i> Lancer le recouvrement
                        </button>
                    </form>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted mb-3 small">
                        Ce module prélève automatiquement les montants dus sur les comptes RMB des clients autorisés. 
                        La liste est triée par urgence : <span class="badge badge-danger">Retard</span> > <span class="badge badge-warning">Du Jour</span> > <span class="badge badge-info">Proche</span>.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">N°</th>
                                    <th>Dossier</th>
                                    <th>Client</th>
                                    <th>Prochaine Échéance</th>
                                    <th class="text-center">Jours de retard</th>
                                    <th class="text-right">Dette en retard</th>
                                    <th class="text-right">Reste à finaliser</th>
                                    <th class="text-right">Solde RMB</th>
                                    <th class="text-center" style="width: 50px;">Auth.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dossiers as $dossier)
                                @php
                                    // Deux montants distincts et complémentaires :
                                    //  - "Dette en retard"    : uniquement les échéances dont la date est
                                    //                           dépassée ET non intégralement réglées (ce
                                    //                           que le recouvrement auto va réellement
                                    //                           prélever aujourd'hui).
                                    //  - "Reste à finaliser"  : TOUT ce qu'il reste à payer sur le crédit,
                                    //                           échéances futures comprises (exposition
                                    //                           totale restante sur le dossier).
                                    $resteDuTotal = 0;
                                    $detteEnRetard = 0;
                                    $aujourdhuiTbl = \Carbon\Carbon::today()->toDateString();
                                    $prochaineDate = $dossier->prochaine_echeance_date;
                                    foreach(($dossier->echeancier->echeances ?? []) as $ech) {
                                        // PARTIELLEMENT_PAYE inclus : un règlement partiel ne solde pas
                                        // l'échéance, le reste dû doit continuer à être compté ici.
                                        if(in_array($ech->statut, ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE'])) {
                                            $resteLigne = max(0, (float)$ech->total_echeance - (float)$ech->montant_paye);
                                            $resteDuTotal += $resteLigne;

                                            $dateEchTbl = optional($ech->date_echeance)->toDateString();
                                            if ($ech->statut === 'EN_RETARD' || ($dateEchTbl && $dateEchTbl < $aujourdhuiTbl)) {
                                                $detteEnRetard += $resteLigne;
                                            }
                                        }
                                    }
                                    
                                    $compteRmb = \App\Models\Clients\Compte::where('client_matricule', $dossier->client_matricule)
                                        ->where('type', 'RMB')
                                        ->where('devise', $dossier->devise)
                                        ->first();
                                    $soldeRmb = $compteRmb ? (float)$compteRmb->solde_reel : 0;

                                    // Calcul des jours ouvrables de retard.
                                    // Règle métier : la banque ouvre du LUNDI au SAMEDI —
                                    // le samedi est donc un jour ouvrable comme un autre,
                                    // seul le DIMANCHE est exclu du comptage.
                                    $joursRetard = 0;
                                    if ($prochaineDate) {
                                        $dateEcheance = \Carbon\Carbon::parse($prochaineDate);
                                        $today = \Carbon\Carbon::today();
                                        if ($dateEcheance->lt($today)) {
                                            $current = $dateEcheance->copy()->addDay();
                                            while ($current->lte($today)) {
                                                if ($current->dayOfWeekIso <= 6) { // 1=Lundi ... 6=Samedi (dimanche=7 exclu)
                                                    $joursRetard++;
                                                }
                                                $current->addDay();
                                            }
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('credit.show', $dossier->id) }}" class="font-weight-bold text-primary text-decoration-none" title="Voir les détails du dossier">
                                            <i class="fas fa-file-invoice mr-1"></i>{{ $dossier->numero_dossier }}
                                        </a>
                                    </td>
                                    <td>{{ optional($dossier->client)->full_name }}</td>
                                    <td class="text-nowrap">{{ $prochaineDate ? \Carbon\Carbon::parse($prochaineDate)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">
                                        @if($joursRetard > 0)
                                            <span class="badge badge-danger font-weight-bold" title="{{ $joursRetard }} jour(s) ouvrable(s) de retard (lundi-samedi, dimanche exclu)">
                                                <i class="fas fa-calendar-times mr-1"></i>{{ $joursRetard }} j
                                            </span>
                                        @elseif($prochaineDate && \Carbon\Carbon::parse($prochaineDate)->lt(\Carbon\Carbon::today()))
                                            {{-- Dossier réellement en retard (date dépassée) mais
                                                 uniquement des dimanches depuis l'échéance (la banque
                                                 ouvre lun-sam) : 0 jour OUVRABLE comptabilisé —
                                                 affiché explicitement au lieu d'un tiret ambigu. --}}
                                            <span class="badge badge-danger" title="En retard depuis le {{ \Carbon\Carbon::parse($prochaineDate)->format('d/m/Y') }} — dimanche non compté en jours ouvrables (banque ouverte lun-sam)">
                                                <i class="fas fa-calendar-times mr-1"></i>0 j
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right text-danger font-weight-bold">{{ number_format($detteEnRetard, 2, ',', ' ') }} <small>{{ $dossier->devise }}</small></td>
                                    <td class="text-right text-warning font-weight-bold">{{ number_format($resteDuTotal, 2, ',', ' ') }} <small>{{ $dossier->devise }}</small></td>
                                    <td class="text-right text-success font-weight-bold">{{ number_format($soldeRmb, 2, ',', ' ') }} <small>{{ $dossier->devise }}</small></td>
                                    <td class="text-center">
                                        @if(in_array('EBEN-PER113', $userPermCodes ?? []))
                                            <form method="POST" action="{{ route('credit.prelevement_auto.toggle', $dossier) }}" class="d-inline"
                                                  onsubmit="return confirm('{{ $dossier->prelevement_auto_autorise ? 'Révoquer' : 'Autoriser' }} le prélèvement automatique pour {{ $dossier->numero_dossier }} ?');">
                                                @csrf
                                                <button type="submit" class="btn btn-link p-0" title="Cliquer pour {{ $dossier->prelevement_auto_autorise ? 'révoquer' : 'autoriser' }}">
                                                    @if($dossier->prelevement_auto_autorise)
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @else
                                                        <i class="fas fa-times-circle text-muted"></i>
                                                    @endif
                                                </button>
                                            </form>
                                        @else
                                            @if($dossier->prelevement_auto_autorise)
                                                <i class="fas fa-check-circle text-success" title="Prélèvement autorisé"></i>
                                            @else
                                                <i class="fas fa-times-circle text-muted" title="Non autorisé"></i>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                                        Aucun dossier en attente de recouvrement.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $dossiers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
@endsection

@push('js')
<script>
$(function () {
    $('#btnLancerRecouvrement').on('click', function () {
        showUniversalConfirm(
            'Êtes-vous sûr de vouloir lancer le recouvrement automatique sur tous les dossiers éligibles ?',
            function () {
                $('#formRecouvrement').submit();
            },
            {
                title: 'Lancer le recouvrement',
                btnLabel: 'Lancer',
                btnClass: 'btn-warning',
                icon: 'fas fa-bolt',
                bodyIcon: 'fas fa-exclamation-triangle fa-3x text-warning',
                headerClass: 'bg-warning text-dark',
                showWarning: true
            }
        );
    });
});
</script>
@endpush
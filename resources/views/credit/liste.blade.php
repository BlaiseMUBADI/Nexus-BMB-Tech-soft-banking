@extends('layouts.app')

@section('page_title', 'Dossiers Crédits')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Liste des dossiers')

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- ── Flash messages ──────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0"><i class="fas fa-folder-open mr-2 text-warning"></i>Dossiers Crédit</h5>
            @if(in_array('EBEN-PER54', $userPermCodes ?? []))
            <a href="{{ route('credit.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle mr-1"></i> Nouvelle demande
            </a>
            @endif
        </div>

        {{-- ── Filtres ──────────────────────────────────────── --}}
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('credit.index') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="small font-weight-bold">N° Dossier</label>
                    <input type="text" name="numero" value="{{ request('numero') }}"
                           class="form-control form-control-sm" placeholder="CRD-EBEN-...">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Matricule Client</label>
                    <input type="text" name="client_matricule" value="{{ request('client_matricule') }}"
                           class="form-control form-control-sm" placeholder="CL-EBENKGA-...">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Statut</label>
                    <select name="statut" class="form-control form-control-sm">
                        <option value="">Tous</option>
                        @foreach([
                            'BROUILLON'        => 'Brouillon',
                            'SOUMIS'           => 'Soumis',
                            'EN_ANALYSE'       => 'En analyse',
                            'EN_VALIDATION'    => 'En validation',
                            'PRET_A_DEBLOQUER' => 'Prêt à débloquer',
                            'DEBLOQUE'         => 'Débloqué',
                            'EN_REMBOURSEMENT' => 'En remboursement',
                            'EN_RETARD'        => 'En retard',
                            'SOLDE'            => 'Soldé',
                            'ANNULE'           => 'Annulé',
                            'SUSPENDU'         => 'Suspendu',
                            'SUSPECT'          => 'Suspect',
                        ] as $val => $lab)
                        <option value="{{ $val }}" {{ request('statut') == $val ? 'selected' : '' }}>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Type</label>
                    <select name="type_credit" class="form-control form-control-sm">
                        <option value="">Tous</option>
                        <option value="INDIVIDUEL" {{ request('type_credit')=='INDIVIDUEL'?'selected':'' }}>Individuel</option>
                        <option value="SOLIDAIRE"  {{ request('type_credit')=='SOLIDAIRE'?'selected':'' }}>Solidaire</option>
                        <option value="PME"        {{ request('type_credit')=='PME'?'selected':'' }}>PME</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Zone</label>
                    <select name="zone" class="form-control form-control-sm">
                        <option value="">Toutes</option>
                        @foreach($zones as $z)
                        <option value="{{ $z->code_zone }}" {{ request('zone') == $z->code_zone ? 'selected' : '' }}>{{ $z->nom ?? $z->nom_zone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm mr-1"><i class="fas fa-search"></i> Filtrer</button>
                    <a href="{{ route('credit.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 mt-2">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>N° Dossier</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Montant demandé</th>
                            <th>Durée</th>
                            <th>Taux</th>
                            <th>Statut</th>
                            <th>Zone</th>
                            <th>Créé le</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dossiers as $d)
                        <tr class="{{ $d->est_suspect ? 'table-danger' : ($d->est_suspendu ? 'table-warning' : '') }}">
                            <td>
                                <code>{{ $d->numero_dossier }}</code>
                                @if($d->est_suspect)  <i class="fas fa-user-secret text-danger ml-1" title="Suspect"></i> @endif
                                @if($d->est_suspendu) <i class="fas fa-pause-circle text-warning ml-1" title="Suspendu"></i> @endif
                            </td>
                            <td>
                                <span class="font-weight-bold">{{ $d->client?->nom }} {{ $d->client?->prenom }}</span><br>
                                <small class="text-muted">{{ $d->client_matricule }}</small>
                            </td>
                            <td><span class="badge badge-secondary">{{ $d->type_credit }}</span></td>
                            <td class="text-right">{{ number_format($d->montant_demande, 2, ',', ' ') }}<small class="text-muted ml-1">{{ $d->devise }}</small></td>
                            <td class="text-center">{{ $d->duree_mois }} mois</td>
                            <td class="text-center">{{ $d->taux_interet_mensuel }}%</td>
                            <td>{!! $d->badgeStatut() !!}</td>
                            <td><small>{{ $d->zone?->nom ?? $d->zone?->nom_zone ?? $d->code_zone }}</small></td>
                            <td><small>{{ $d->created_at->format('d/m/Y') }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('credit.show', $d) }}" class="btn btn-xs btn-info" title="Voir détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($d->statut_global === 'SOUMIS')
                                    <a href="{{ route('credit.analyse', $d) }}" class="btn btn-xs btn-primary" title="Analyser">
                                        <i class="fas fa-search-dollar"></i>
                                    </a>
                                @endif
                                @if($d->statut_global === 'EN_VALIDATION')
                                    <a href="{{ route('credit.validation', $d) }}" class="btn btn-xs btn-warning" title="Valider">
                                        <i class="fas fa-check-double"></i>
                                    </a>
                                @endif
                                @if($d->statut_global === 'PRET_A_DEBLOQUER')
                                    <a href="{{ route('credit.deblocage', $d) }}" class="btn btn-xs btn-success" title="Débloquer">
                                        <i class="fas fa-unlock-alt"></i>
                                    </a>
                                @endif
                                @if(in_array($d->statut_global, ['DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD']))
                                    <a href="{{ route('credit.remboursement', $d) }}" class="btn btn-xs btn-success" title="Rembourser">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                Aucun dossier crédit trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $dossiers->links() }}
        </div>
    </div>

</div>
</section>
@endsection

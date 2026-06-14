@extends('layouts.app')

@section('page_title', 'Tableau de Bord - Recouvrement Automatique')
@section('breadcrumb_parent', 'Crédit')
@section('breadcrumb', 'Recouvrement Auto')

@section('content')
<div class="container-fluid">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-sync-alt text-warning mr-2"></i>
                        Recouvrement Automatique RMB
                    </h3>
                    <form action="{{ route('recouvrement.run') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir lancer le recouvrement automatique sur tous les dossiers éligibles ?');">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-play mr-1"></i> Lancer le recouvrement maintenant
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Ce module prélève automatiquement les montants dus sur les comptes RMB des clients ayant donné leur autorisation. 
                        Il commence par les échéances les plus en retard.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Priorité</th>
                                    <th>Dossier</th>
                                    <th>Client</th>
                                    <th>Prochaine Échéance</th>
                                    <th>Reste Dû</th>
                                    <th>Solde RMB</th>
                                    <th>Statut Autorisation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dossiers as $dossier)
                                @php
                                    // Calcul du reste dû total pour l'affichage
                                    $resteDuTotal = 0;
                                    $prochaineDate = $dossier->prochaine_echeance_date;
                                    foreach($dossier->echeancier->echeances as $ech) {
                                        if(in_array($ech->statut, ['EN_ATTENTE', 'EN_RETARD'])) {
                                            $resteDuTotal += max(0, (float)$ech->total_echeance - (float)$ech->montant_paye);
                                        }
                                    }
                                    
                                    // Récupération du solde RMB (on utilise le compte lié au dossier)
                                    $compteRmb = \App\Models\Clients\Compte::where('client_matricule', $dossier->client_matricule)
                                        ->where('type', 'RMB')
                                        ->where('devise', $dossier->devise)
                                        ->first();
                                    $soldeRmb = $compteRmb ? (float)$compteRmb->solde_reel : 0;

                                    // Détermination de la couleur de priorité
                                    $badgeClass = 'badge-secondary';
                                    $prioriteLibelle = 'À JOUR';
                                    if ($dossier->priorite_score == 1) {
                                        $badgeClass = 'badge-danger';
                                        $prioriteLibelle = 'EN RETARD';
                                    } elseif ($dossier->priorite_score == 2) {
                                        $badgeClass = 'badge-warning';
                                        $prioriteLibelle = 'DU JOUR';
                                    } elseif ($dossier->priorite_score == 3) {
                                        $badgeClass = 'badge-info';
                                        $prioriteLibelle = 'PROCHE';
                                    }
                                @endphp
                                <tr>
                                    <td><span class="badge {{ $badgeClass }}">{{ $prioriteLibelle }}</span></td>
                                    <td>{{ $dossier->numero_dossier }}</td>
                                    <td>{{ optional($dossier->client)->nom }} {{ optional($dossier->client)->prenom }}</td>
                                    <td>{{ $prochaineDate ? \Carbon\Carbon::parse($prochaineDate)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-right text-danger font-weight-bold">{{ number_format($resteDuTotal, 2, ',', ' ') }} {{ $dossier->devise }}</td>
                                    <td class="text-right text-success font-weight-bold">{{ number_format($soldeRmb, 2, ',', ' ') }} {{ $dossier->devise }}</td>
                                    <td class="text-center">
                                        @if($dossier->prelevement_auto_autorise)
                                            <i class="fas fa-check-circle text-success" title="Autorisé"></i>
                                        @else
                                            <i class="fas fa-times-circle text-muted" title="Non autorisé"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Aucun dossier en attente de recouvrement.</td>
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
@endsection
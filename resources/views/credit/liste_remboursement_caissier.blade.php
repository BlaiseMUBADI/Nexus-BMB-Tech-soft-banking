@extends('layouts.app')

@section('page_title', 'Liste des Remboursements (Caissier)')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Remboursements')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Clients en cours de remboursement</h2>
            </div>
        </div>

        @if($dossiers->isEmpty())
            <div class="alert alert-info">Aucun dossier en remboursement.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Numéro dossier</th>
                            <th>Client</th>
                            <th>Montant restant</th>
                            <th>Prochaine échéance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dossiers as $dossier)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dossier->numero_dossier }}</td>
                                <td>
                                    {{ $dossier->client->nom }} {{ $dossier->client->postnom }} {{ $dossier->client->prenom }}
                                </td>
                                <td>
                                    {{ number_format($dossier->montant_approuve - $dossier->creditRemboursements->sum('montant_recu'), 0, ',', ' ') }} Fc
                                </td>
                                <td>
                                    @if($dossier->echeancier && $dossier->echeancier->echeances->whereIn('statut', ['EN_ATTENTE','EN_RETARD'])->isNotEmpty())
                                        {{ $dossier->echeancier->echeances()->whereIn('statut', ['EN_ATTENTE','EN_RETARD'])->orderBy('numero_echeance')->first()->date_echeance->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $dossiers->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
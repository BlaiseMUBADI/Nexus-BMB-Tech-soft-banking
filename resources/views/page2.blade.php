@extends('layouts.app')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2>Historique des Transactions</h2>
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2026-02-10</td>
                            <td>Virement reçu</td>
                            <td>+1 000 €</td>
                            <td>Validé</td>
                        </tr>
                        <tr>
                            <td>2026-02-11</td>
                            <td>Retrait distributeur</td>
                            <td>-200 €</td>
                            <td>Validé</td>
                        </tr>
                        <tr>
                            <td>2026-02-12</td>
                            <td>Paiement carte</td>
                            <td>-50 €</td>
                            <td>En attente</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

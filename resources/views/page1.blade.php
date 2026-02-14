@section('page_title', 'Comptes Bancaires')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Comptes')
@extends('layouts.app')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2>Comptes Bancaires</h2>
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom du compte</th>
                            <th>Solde</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Compte Courant</td>
                            <td>5 000 €</td>
                            <td>Courant</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Compte Épargne</td>
                            <td>12 000 €</td>
                            <td>Épargne</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

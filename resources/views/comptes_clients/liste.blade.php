@extends('layouts.app')

@section('page_title', 'Liste des comptes')
@section('breadcrumb_parent', 'Gestion des comptes')
@section('breadcrumb', 'Liste des comptes')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <a href="{{ route('comptes.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus-circle mr-1"></i> Ouvrir un compte
            </a>
        </div>
        <div class="card-body">
            <div id="comptes-table-toolbar" class="d-flex flex-wrap align-items-center justify-content-between mb-3 p-2 rounded shadow-sm" style="background: #232a32; border: 1px solid #444; min-height: 56px;">
                <div id="comptes-table-buttons" class="mb-2 mb-md-0 "></div>
                <div id="comptes-table-search" class="datatable-search ms-md-3 "></div>
            </div>
            <div class="table-responsive">
                <table id="comptes-table" class="table table-bordered table-striped datatable" data-buttons-container="#comptes-table-buttons">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code Compte</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Solde réel</th>
                            <th>Devise</th>
                            <th>Portefeuille</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comptes as $loopIndex => $compte)
                        <tr>
                            <td>{{ $loopIndex + 1 }}</td>
                            <td>{{ $compte->code_compte }}</td>
                            <td>{{ $compte->client->nom }} {{ $compte->client->postnom }} {{ $compte->client->prenom }}</td>
                            <td>{{ $compte->type }}</td>
                            <td>{{ number_format($compte->solde_reel, 2, ',', ' ') }}</td>
                            <td>{{ $compte->devise }}</td>
                            <td>
                                @if($compte->portefeuille_id && $compte->portefeuille && $compte->portefeuille->agent)
                                    ({{ $compte->portefeuille->agent->matricule }}) {{ $compte->portefeuille->agent->nom }} {{ $compte->portefeuille->agent->prenom }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('comptes.show', $compte->code_compte) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('comptes.edit', $compte->code_compte) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('comptes.destroy', $compte->code_compte) }}" method="POST" class="d-inline delete-compte-form" data-code-compte="{{ $compte->code_compte }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-compte" data-id="{{ $compte->code_compte }}" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="contextMenuCompte" style="display:none; position: absolute; z-index: 9999;">
    <div class="menu-header">Actions sur le compte</div>
    <div class="menu-section">
        <ul>
            <li><a href="#" class="dropdown-item"><i class="fas fa-file-alt"></i> Imprimer RIB / IBAN</a></li>
            <li><a href="#" class="dropdown-item"><i class="fas fa-file-contract"></i> Imprimer Convention de Compte</a></li>
            <li><a href="#" class="dropdown-item "><i class="fas fa-certificate"></i> Imprimer Certificat d'Ouverture</a></li>
            <li><a href="#" class="dropdown-item "><i class="fas fa-list-alt"></i> Imprimer Relevé de Compte</a></li>
            <li><a href="#" class="dropdown-item"><i class="fas fa-receipt"></i> Imprimer Avis d'Opération</a></li>
            <li><a href="#" class="dropdown-item "><i class="fas fa-chart-line"></i> Imprimer Échelle d'Intérêts</a></li>
            <li><a href="#" class="dropdown-item"><i class="fas fa-table"></i> Imprimer Tableau d'Amortissement</a></li>
            <li><a href="#" class="dropdown-item"><i class="fas fa-balance-scale"></i> Attestation de Solde</a></li>
            <li><a href="#" class="dropdown-item "><i class="fas fa-check-circle"></i> Attestation de Non-Redevance</a></li>
            <li><a href="#" class="dropdown-item "><i class="fas fa-id-card"></i> Fiche Client (KYC)</a></li>
            <li><a href="#" class="dropdown-item "><i class="fas fa-pen-nib"></i> Spécimen de Signature</a></li>
        </ul>
    </div>
</div>
@endsection


@section('css')
    <style>
        #contextMenuCompte {
        background: #fff;
        box-shadow: 0 8px 32px rgba(60, 72, 88, 0.18);
        border-radius: 16px;
        min-width: 260px;
        font-family: 'Segoe UI', Arial, sans-serif;
        border: none;
        overflow: hidden;
    }
    #contextMenuCompte .menu-header {
        padding: 16px 20px;
        font-weight: 600;
        font-size: 1.1em;
        background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%);
        color: #fff;
        border-radius: 16px 16px 0 0;
        border-bottom: none;
        letter-spacing: 0.5px;
    }
    #contextMenuCompte .menu-section {
        background: #f8fafc;
        padding: 14px 20px 10px 20px;
    }
    #contextMenuCompte .menu-section-title {
        font-weight: 500;
        color: #6366f1;
        margin-bottom: 8px;
        font-size: 1em;
        letter-spacing: 0.2px;
    }
    #contextMenuCompte ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    #contextMenuCompte ul li {
        margin-bottom: 4px;
    }
    #contextMenuCompte a.dropdown-item {
        display: flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 8px;
        color: #334155;
        font-size: 0.98em;
        text-decoration: none;
        background: none;
        transition: background 0.18s, color 0.18s;
        font-weight: 500;
    }
    #contextMenuCompte a.dropdown-item:hover, #contextMenuCompte a.dropdown-item.active {
        background: #e0e7ff;
        color: #4338ca;
    }
    #contextMenuCompte a.dropdown-item.disabled {
        color: #cbd5e1 !important;
        pointer-events: none;
        background: none;
        font-weight: 400;
    }
    #contextMenuCompte i {
        margin-right: 8px;
        font-size: 1em;
        color: #6366f1;
    }


    </style>
@endsection


@push('js')
<script>
$(function() {
    // Affichage du menu contextuel sur clic droit
    $('#comptes-table tbody').on('contextmenu', 'tr', function(e) {
        e.preventDefault();
        var compteId = $(this).find('.btn-delete-compte').data('id');
        // Positionne et affiche le menu
        $('#contextMenuCompte').css({
            left: e.pageX + 'px',
            top: e.pageY + 'px',
            display: 'block'
        }).data('compte-id', compteId);
        // Ferme le menu au clic ailleurs
        $(document).on('click.contextMenuCompte', function() {
            $('#contextMenuCompte').hide();
            $(document).off('click.contextMenuCompte');
        });
    });
    // Actions du menu
    $('#menuVoirCompte').on('click', function(e) {
        e.preventDefault();
        var compteId = $('#contextMenuCompte').data('compte-id');
        window.location.href = '/comptes-clients/comptes/' + compteId;
    });
    $('#menuEditCompte').on('click', function(e) {
        e.preventDefault();
        var compteId = $('#contextMenuCompte').data('compte-id');
        window.location.href = '/comptes-clients/comptes/' + compteId + '/edit';
    });
    $('#menuDeleteCompte').on('click', function(e) {
        e.preventDefault();
        var compteId = $('#contextMenuCompte').data('compte-id');
        $('.btn-delete-compte[data-id=' + compteId + ']').click();
        $('#contextMenuCompte').hide();
    });
});
</script>
@endpush

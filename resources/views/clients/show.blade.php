@extends('layouts.app')

@section('page_title', 'Détail du client')
@section('breadcrumb_parent', 'Clients / Membres')
@section('breadcrumb', 'Détail du client')

@section('content')
<div class="container-fluid">

    {{-- Flash session --}}
    @if(session('success'))
        @push('js')
        <script>
            $(function () { showSystemMessage('success', '{{ addslashes(session("success")) }}'); });
        </script>
        @endpush
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
            <h3 class="card-title mb-0">
                <i class="fas fa-user mr-2"></i>
                {{ $client->nom }} {{ $client->postnom }} {{ $client->prenom }}
                <small class="ml-2 text-muted">— <code>{{ $client->matricule }}</code></small>
            </h3>
            <div class="mt-1 mt-md-0">
                <a href="{{ route('clients.edit', $client->matricule) }}" class="btn btn-sm btn-warning mr-1">
                    <i class="fas fa-edit mr-1"></i> Modifier
                </a>
                <button type="button" class="btn btn-sm btn-danger mr-1" id="btnDeleteClient"
                        data-url="{{ route('clients.destroy', $client->matricule) }}"
                        data-nom="{{ $client->nom }} {{ $client->postnom }}">
                    <i class="fas fa-trash-alt mr-1"></i> Supprimer
                </button>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                {{-- === Col photo === --}}
                <div class="col-md-3 text-center mb-3">
                    @if($client->photo)
                        <img src="{{ route('clients.photo', basename($client->photo)) }}"
                             id="clientPhotoThumb"
                             alt="Photo du client"
                             class="img-thumbnail shadow"
                             data-toggle="modal" data-target="#photoModal"
                             style="max-width:200px;max-height:200px;object-fit:cover;cursor:pointer;">
                        <p class="text-muted small mt-1">Cliquer pour agrandir</p>
                    @else
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center mx-auto"
                             style="width:150px;height:150px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <p class="text-muted small mt-1">Aucune photo</p>
                    @endif
                </div>

                {{-- === Col infos === --}}
                <div class="col-md-9">

                    {{-- Section 1 : Identité --}}
                    <div class="card card-info card-outline mb-3">
                        <div class="card-header py-2">
                            <h5 class="card-title mb-0"><i class="fas fa-user mr-2"></i>Identité</h5>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-sm-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><th style="width:50%">Nom</th><td>{{ $client->nom }}</td></tr>
                                        <tr><th>Postnom</th><td>{{ $client->postnom ?? '—' }}</td></tr>
                                        <tr><th>Prénom</th><td>{{ $client->prenom }}</td></tr>
                                        <tr><th>Sexe</th><td>
                                            @if($client->sexe === 'M')
                                                <span class="badge badge-info">Masculin</span>
                                            @elseif($client->sexe === 'F')
                                                <span class="badge badge-warning">Féminin</span>
                                            @else
                                                {{ $client->sexe ?? '—' }}
                                            @endif
                                        </td></tr>
                                        <tr><th>Date de naissance</th><td>{{ $client->date_naissance ?? '—' }}</td></tr>
                                        <tr><th>Lieu de naissance</th><td>{{ $client->lieu_naissance ?? '—' }}</td></tr>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><th style="width:50%">Email</th><td>{{ $client->email ?? '—' }}</td></tr>
                                        <tr><th>Téléphone</th><td>{{ $client->telephone ?? '—' }}</td></tr>
                                        <tr><th>Adresse</th><td>{{ $client->adresse ?? '—' }}</td></tr>
                                        <tr><th>Zone</th><td>{{ optional($client->zone)->nom ?? $client->code_zone ?? '—' }}</td></tr>
                                        <tr><th>État civil</th><td>{{ $client->etat_civil ?? '—' }}</td></tr>
                                        <tr><th>Conjoint</th><td>{{ $client->nom_conjoint ?? '—' }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2 : Pièce d'identité --}}
                    <div class="card card-warning card-outline mb-3">
                        <div class="card-header py-2">
                            <h5 class="card-title mb-0"><i class="fas fa-id-card mr-2"></i>Pièce d'identité</h5>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-sm-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><th style="width:50%">Type</th><td>{{ $client->type_piece_identite ?? '—' }}</td></tr>
                                        <tr><th>Numéro</th><td>{{ $client->numero_piece_identite ?? '—' }}</td></tr>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><th style="width:50%">Lieu de délivrance</th><td>{{ $client->lieu_delivrance_piece ?? '—' }}</td></tr>
                                        <tr><th>Date de délivrance</th><td>{{ $client->date_delivrance_piece ?? '—' }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3 : Activité économique --}}
                    @if($client->secteur_activite || $client->type_activite || $client->nom_entreprise || $client->revenu_mensuel)
                    <div class="card card-success card-outline mb-0">
                        <div class="card-header py-2">
                            <h5 class="card-title mb-0"><i class="fas fa-briefcase mr-2"></i>Activité économique</h5>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-sm-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><th style="width:50%">Secteur d'activité</th><td>{{ $client->secteur_activite ?? '—' }}</td></tr>
                                        <tr><th>Type d'activité</th><td>{{ $client->type_activite ?? '—' }}</td></tr>
                                        <tr><th>Nom entreprise</th><td>{{ $client->nom_entreprise ?? '—' }}</td></tr>
                                        <tr><th>Adresse entreprise</th><td>{{ $client->adresse_entreprise ?? '—' }}</td></tr>
                                        <tr><th>Tél. entreprise</th><td>{{ $client->telephone_entreprise ?? '—' }}</td></tr>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><th style="width:50%">Statut entreprise</th><td>{{ $client->statut_entreprise ?? '—' }}</td></tr>
                                        <tr><th>Années d'expérience</th><td>{{ $client->nombre_annees_experience ?? '—' }}</td></tr>
                                        <tr><th>Revenu mensuel</th><td>
                                            @if($client->revenu_mensuel)
                                                {{ number_format($client->revenu_mensuel, 2, ',', '.') }} {{ $client->revenu_mensuel_devise ?? '' }}
                                            @else
                                                —
                                            @endif
                                        </td></tr>
                                        <tr><th>Autres détails</th><td>{{ $client->autres_details_activite ?? '—' }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>{{-- /col-md-9 --}}
            </div>{{-- /row --}}

            {{-- ===================== Section Comptes Bancaires ===================== --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-0">
                        <div class="card-header py-2 d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-wallet mr-2"></i>Comptes bancaires
                                <span class="badge badge-primary ml-2">{{ $client->comptes->count() }}</span>
                            </h5>
                            @can('permission', 'EBEN-PER19')
                            <a href="{{ route('comptes.create') }}?client={{ $client->matricule }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-plus mr-1"></i> Nouveau compte
                            </a>
                            @endcan
                        </div>
                        <div class="card-body p-0">
                            @if($client->comptes->isEmpty())
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <p class="mb-0">Aucun compte bancaire enregistré pour ce client.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Code compte</th>
                                                <th>Type</th>
                                                <th>Devise</th>
                                                <th class="text-right">Solde réel</th>
                                                <th class="text-right">Solde bloqué</th>
                                                <th>Ouvert le</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($client->comptes as $compte)
                                            <tr>
                                                <td><code>{{ $compte->code_compte }}</code></td>
                                                <td>
                                                    @php
                                                        $typeLabels = [
                                                            'CC'  => ['label' => 'Compte Courant',   'class' => 'badge-primary'],
                                                            'RMB' => ['label' => 'RMB',              'class' => 'badge-success'],
                                                            'GTC' => ['label' => 'Caution (GTC)',    'class' => 'badge-warning'],
                                                            'DAT' => ['label' => 'Dépôt à Terme',   'class' => 'badge-info'],
                                                            'EAV' => ['label' => 'Épargne',          'class' => 'badge-secondary'],
                                                        ];
                                                        $t = $typeLabels[$compte->type] ?? ['label' => $compte->type, 'class' => 'badge-dark'];
                                                    @endphp
                                                    <span class="badge {{ $t['class'] }}">{{ $t['label'] }}</span>
                                                </td>
                                                <td>{{ $compte->devise ?? '—' }}</td>
                                                <td class="text-right font-weight-bold">
                                                    {{ number_format((float)($compte->solde_reel ?? 0), 2, ',', '.') }}
                                                </td>
                                                <td class="text-right {{ ($compte->solde_bloque ?? 0) > 0 ? 'text-warning font-weight-bold' : '' }}">
                                                    {{ number_format((float)($compte->solde_bloque ?? 0), 2, ',', '.') }}
                                                </td>
                                                <td>{{ $compte->created_at ? \Carbon\Carbon::parse($compte->created_at)->format('d/m/Y') : '—' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('comptes.show', $compte->code_compte) }}"
                                                       class="btn btn-xs btn-info mr-1" title="Détail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('comptes.historique', $compte->code_compte) }}"
                                                       class="btn btn-xs btn-secondary mr-1" title="Historique">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                    <a href="{{ route('comptes.rib', $compte->code_compte) }}"
                                                       class="btn btn-xs btn-light" title="Imprimer RIB" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- ==================== /Section Comptes Bancaires ==================== --}}

        </div>{{-- /card-body --}}
    </div>{{-- /card --}}
</div>{{-- /container-fluid --}}

{{-- Modal photo --}}
@if($client->photo)
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">Photo — {{ $client->nom }} {{ $client->postnom }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ route('clients.photo', basename($client->photo)) }}"
                     alt="Photo du client"
                     class="img-fluid rounded shadow"
                     style="max-width:100%;max-height:80vh;">
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('js')
<script>
(function () {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(function () {
        /* ---- Suppression AJAX ---- */
        $('#btnDeleteClient').on('click', function () {
            var url = $(this).data('url');
            var nom = $(this).data('nom');
            showUniversalConfirm(
                'Supprimer définitivement le client <strong>' + nom + '</strong> ainsi que tous ses comptes liés ?',
                function () {
                    $.post(url, { _method: 'DELETE' })
                        .done(function (res) {
                            showSystemMessage('success', res.message || 'Client supprimé avec succès.');
                            setTimeout(function () {
                                window.location.href = '{{ route("clients.index") }}';
                            }, 1500);
                        })
                        .fail(function () {
                            showSystemMessage('error', 'Impossible de supprimer le client. Veuillez réessayer.');
                        });
                },
                'Confirmer la suppression'
            );
        });
    });
}());
</script>
@endpush

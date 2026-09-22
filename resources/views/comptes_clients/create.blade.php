@extends('layouts.app')

@section('page_title', 'Ouverture de compte bancaire')
@section('breadcrumb_parent', 'Gestion des comptes')
@section('breadcrumb', 'Ouverture de compte')

@push('css')
<style>
    .select2-container .select2-selection--single { height: 38px !important; }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { line-height: 36px !important; }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow { height: 36px !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Bloc formulaire à gauche -->
        <div class="col-lg-5">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-university mr-2"></i>Ouverture de compte bancaire</h3>
                </div>
                <div class="card-body">
                    <form id="compteForm" method="POST" action="{{ route('comptes.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="client_matricule">Client</label>
                            <select name="client_matricule" id="client_matricule" class="form-control select2" required>
                                <option value="">-- Sélectionner un client --</option>
                                {{-- Seul le client déjà choisi est rendu côté serveur ;
                                     les autres viennent de la recherche AJAX. --}}
                                @if(!empty($selectedClient))
                                    <option value="{{ $selectedClient->matricule }}" selected>
                                        {{ $selectedClient->full_name }} ({{ $selectedClient->matricule }})
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type de compte</label>
                            <select name="type" id="type" class="form-control select2" required>
                                <option value="">-- Sélectionner le type --</option>
                                <option value="CC">Compte Courant</option>
                                <option value="RMB">Compte Remboursement</option>
                                <option value="GTC">Compte Caution</option>
                                <option value="DAT">Dépôt à Terme</option>
                                <option value="EAV">Épargne & Vie</option>
                            </select>
                        </div>
                        <div class="form-group" id="portefeuille_group" style="display:none">
                            <label for="portefeuille_id">Agent gestionnaire (Portefeuille)</label>
                            <select name="portefeuille_id" id="portefeuille_id" class="form-control select2">
                                <option value="">-- Sélectionner l'agent --</option>
                                @foreach($portefeuilles as $pf)
                                    @php
                                        $pfAgent = $pf->affectationActive->agent ?? $pf->agent;
                                    @endphp
                                    <option value="{{ $pf->id }}">
                                        @if($pfAgent)
                                            ({{ $pfAgent->matricule }}) {{ $pfAgent->nom }} {{ $pfAgent->prenom }}
                                        @else
                                            Portefeuille #{{ $pf->id }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Obligatoire pour les comptes de type Caution (GTC).</small>
                        </div>
                        <div class="form-group">
                            <label for="devise">Devise</label>
                            <select name="devise" id="devise" class="form-control select2" required>
                                <option value="">-- Sélectionner la devise --</option>
                                @foreach($devises as $devise)
                                    <option value="{{ $devise->code_iso }}">{{ $devise->nom }} ({{ $devise->symbole }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus-circle mr-1"></i>Ouvrir le compte
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bloc tableau à droite -->
        <div class="col-lg-7">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-2"></i>Comptes ouverts</h3>
                    <div class="card-tools">
                        <form method="GET" action="{{ route('comptes.create') }}" class="form-inline">
                            <div class="input-group input-group-sm" style="width:240px;">
                                <input type="text" name="q" value="{{ $rechercheComptes ?? '' }}" class="form-control"
                                       placeholder="N° compte, client, matricule…" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                                    @if(!empty($rechercheComptes))
                                        <a href="{{ route('comptes.create') }}" class="btn btn-outline-secondary" title="Effacer">&times;</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:520px; overflow-y:auto;">
                        <table class="table table-bordered table-striped table-hover mb-0" id="comptesCreateTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:35px">#</th>
                                    <th>Code Compte</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th>Solde réel</th>
                                    <th>Devise</th>
                                    <th>Portefeuille</th>
                                    <th style="width:60px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comptes as $compte)
                                    @php
                                        $pfAgent = $compte->portefeuille?->affectationActive?->agent ?? $compte->portefeuille?->agent;
                                    @endphp
                                    <tr data-search="{{ strtolower(trim($compte->code_compte . ' ' . ($compte->client?->full_name ?? '') . ' ' . $compte->type . ' ' . $compte->devise . ' ' . ($pfAgent?->full_name ?? '') . ' ' . ($pfAgent?->matricule ?? ''))) }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td><code>{{ $compte->code_compte }}</code></td>
                                        <td>
                                            @if($compte->client)
                                                {{ $compte->client->full_name }}
                                            @else
                                                <span class="text-muted">–</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ match($compte->type) {
                                                'CC' => 'badge-info',
                                                'RMB' => 'badge-secondary',
                                                'GTC' => 'badge-primary',
                                                'DAT' => 'badge-warning',
                                                'EAV' => 'badge-success',
                                                default => 'badge-secondary',
                                            } }}">
                                                {{ $compte->type }} - {{ match($compte->type) {
                                                    'CC' => 'Compte Courant',
                                                    'RMB' => 'Remboursement',
                                                    'GTC' => 'Caution',
                                                    'DAT' => 'Dépôt à Terme',
                                                    'EAV' => 'Épargne & Vie',
                                                    default => $compte->type,
                                                } }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($compte->solde_reel, 2, ',', ' ') }}</td>
                                        <td><span class="badge badge-secondary">{{ $compte->devise }}</span></td>
                                        <td>
                                            @if($pfAgent)
                                                <small>({{ $pfAgent->matricule }})
                                                {{ $pfAgent->nom }}</small>
                                            @else
                                                <span class="text-muted">–</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-xs btn-danger delete-compte-btn"
                                                    data-id="{{ $compte->code_compte }}"
                                                    data-url="{{ route('comptes.destroy', $compte->code_compte) }}"
                                                    title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-3">
                                            <i class="fas fa-inbox fa-2x mb-1 d-block"></i>Aucun compte enregistré.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($comptes->hasPages())
                    <div class="card-footer py-1">
                        {{ $comptes->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    'use strict';
    $.ajaxSetup({ headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept'      : 'application/json'
    } });

    $(function () {

        /* ── Select2 (visible fields only – portefeuille init lazily) ─── */
        var s2Opts = {
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Sélectionner une option',
            allowClear: true,
            language: { noResults: function () { return 'Aucun résultat trouvé'; } }
        };
        $('#type, #devise').select2(s2Opts);

        // Client : recherche AJAX (plus de 2 200 <option> rendues côté serveur)
        $('#client_matricule').select2($.extend({}, s2Opts, {
            placeholder: '-- Sélectionner un client (nom, postnom, prénom...) --',
            minimumInputLength: 2,
            language: {
                noResults: function () { return 'Aucun client trouvé'; },
                inputTooShort: function () { return 'Tapez au moins 2 caractères (nom, postnom, prénom ou matricule).'; }
            },
            ajax: {
                url: '{{ route("comptes.clients.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) {
                    return {
                        results: data.map(function (c) {
                            return { id: c.matricule, text: c.full_name + ' (' + c.matricule + ')' };
                        })
                    };
                }
            }
        }));

        /* ── Afficher / masquer portefeuille ─────────── */
        $('#type').on('change', function () {
            if ($(this).val() === 'GTC') {
                $('#portefeuille_group').fadeIn(300, function () {
                    // Init Select2 the first time the group becomes visible
                    if (!$('#portefeuille_id').data('select2')) {
                        $('#portefeuille_id').select2(s2Opts);
                    }
                });
                $('#portefeuille_id').prop('required', true);
            } else {
                $('#portefeuille_group').fadeOut(300, function () {
                    $('#portefeuille_id').prop('required', false);
                    if ($('#portefeuille_id').data('select2')) {
                        $('#portefeuille_id').val(null).trigger('change');
                    }
                });
            }
        });

        /* ── Recherche dans la table : faite côté serveur (paramètre q) ── */

        /* ── Soumission AJAX formulaire ──────────────── */
        $('#compteForm').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var $btn = form.find('[type=submit]');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Traitement…');

            $.ajax({ url: "{{ route('comptes.store') }}", type: 'POST', data: form.serialize(), dataType: 'json' })
                .done(function (res) {
                    if (res.success) {
                        form[0].reset();
                        $('.select2').val(null).trigger('change');
                        $('#portefeuille_group').hide();
                        showSystemMessage('success', res.message || 'Compte ouvert avec succès !');
                        $('#systemMessageModal').one('hidden.bs.modal', function () {
                            window.location.reload();
                        });
                    } else {
                        showSystemMessage('error', res.message || 'Erreur.');
                    }
                })
                .fail(function (xhr) {
                    handleAjaxFail(xhr, 'Ouverture compte');
                })
                .always(function () {
                    $btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i>Ouvrir le compte');
                });
        });

        /* ── Suppression AJAX ────────────────────────── */
        $(document).on('click', '.delete-compte-btn', function () {
            var code = $(this).data('id');
            var url  = $(this).data('url');
            showUniversalConfirm(
                'Voulez-vous vraiment supprimer le compte <strong>' + code + '</strong> ?',
                function () {
                    $.ajax({ url: url, type: 'POST', data: { _method: 'DELETE' }, dataType: 'json' })
                        .done(function (res) {
                            if (res.success) {
                                showSystemMessage('success', res.message || 'Compte supprimé avec succès.');
                                $('#systemMessageModal').one('hidden.bs.modal', function () {
                                    window.location.reload();
                                });
                            } else {
                                showSystemMessage('error', res.message || 'Erreur.');
                            }
                        })
                        .fail(function (xhr) {
                            handleAjaxFail(xhr, 'Suppression compte');
                        });
                },
                'Confirmation suppression'
            );
        });

    });
}());
</script>
@endpush

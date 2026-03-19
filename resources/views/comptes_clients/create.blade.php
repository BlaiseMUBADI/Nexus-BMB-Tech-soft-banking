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
                                @foreach($clients as $client)
                                    <option value="{{ $client->matricule }}">{{ $client->full_name }} ({{ $client->matricule }})</option>
                                @endforeach
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
                                    <option value="{{ $pf->id }}">
                                        @if($pf->agent)
                                            ({{ $pf->agent->matricule }}) {{ $pf->agent->nom }} {{ $pf->agent->prenom }}
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
                        <div class="input-group input-group-sm" style="width:200px;">
                            <input type="text" id="searchComptesCreate" class="form-control" placeholder="Rechercher…">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
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
                                    <tr data-search="{{ strtolower(trim($compte->code_compte . ' ' . ($compte->client?->full_name ?? '') . ' ' . $compte->type . ' ' . $compte->devise . ' ' . ($compte->portefeuille?->agent?->full_name ?? '') . ' ' . ($compte->portefeuille?->agent?->matricule ?? ''))) }}">
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
                                            @php
                                                $typeLabels = [
                                                    'CC'  => ['label' => 'Compte Courant',        'badge' => 'badge-info'],
                                                    'RMB' => ['label' => 'Remboursement',          'badge' => 'badge-secondary'],
                                                    'GTC' => ['label' => 'Caution',                'badge' => 'badge-primary'],
                                                    'DAT' => ['label' => 'Dépôt à Terme',          'badge' => 'badge-warning'],
                                                    'EAV' => ['label' => 'Épargne & Vie',          'badge' => 'badge-success'],
                                                ];
                                                $typeBadge = $typeLabels[$compte->type]['badge'] ?? 'badge-secondary';
                                                $typeLabel = $typeLabels[$compte->type]['label'] ?? $compte->type;
                                            @endphp
                                            <span class="badge {{ $typeBadge }}">{{ $compte->type }} - {{ $typeLabel }}</span>
                                        </td>
                                        <td class="text-right">{{ number_format($compte->solde_reel, 2, ',', ' ') }}</td>
                                        <td><span class="badge badge-secondary">{{ $compte->devise }}</span></td>
                                        <td>
                                            @if($compte->portefeuille && $compte->portefeuille->agent)
                                                <small>({{ $compte->portefeuille->agent->matricule }})
                                                {{ $compte->portefeuille->agent->nom }}</small>
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
        $('#client_matricule, #type, #devise').select2(s2Opts);

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

        /* ── Live search dans la table ───────────────── */
        $('#searchComptesCreate').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#comptesCreateTable tbody tr').each(function () {
                var haystack = ($(this).data('search') || $(this).text()).toString().toLowerCase();
                $(this).toggle(haystack.indexOf(q) !== -1);
            });
        });

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

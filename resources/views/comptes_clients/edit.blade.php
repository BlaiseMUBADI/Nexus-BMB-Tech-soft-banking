@extends('layouts.app')

@section('page_title', 'Modifier le compte')
@section('breadcrumb_parent', 'Gestion des comptes')
@section('breadcrumb', 'Modifier ' . $compte->code_compte)

@push('css')
<style>
    .select2-container .select2-selection--single { height: 38px !important; }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { line-height: 36px !important; }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow { height: 36px !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Modifier le compte {{ $compte->code_compte }}</h3>
                </div>
                <div class="card-body">

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Par sécurité, seul le <strong>portefeuille / agent gestionnaire</strong> peut être réaffecté ici.
                        Le solde, le type de compte, la devise et le client titulaire ne sont jamais modifiables
                        directement — ils ne peuvent évoluer qu'à travers de véritables opérations (dépôt, retrait,
                        virement, clôture), afin de préserver l'intégrité comptable.
                    </div>

                    {{-- ── Informations non modifiables (lecture seule) ────────────── --}}
                    <table class="table table-sm table-bordered mb-4">
                        <tr>
                            <th style="width:35%;" class="bg-light">Client</th>
                            <td>{{ $compte->client?->full_name ?? '—' }} <small class="text-muted">({{ $compte->client_matricule }})</small></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Type de compte</th>
                            <td>
                                <span class="badge {{ match($compte->type) {
                                    'CC' => 'badge-info', 'RMB' => 'badge-secondary', 'GTC' => 'badge-primary',
                                    'DAT' => 'badge-warning', 'EAV' => 'badge-success', default => 'badge-secondary',
                                } }}">
                                    {{ $compte->type }} - {{ match($compte->type) {
                                        'CC' => 'Compte Courant', 'RMB' => 'Remboursement', 'GTC' => 'Caution',
                                        'DAT' => 'Dépôt à Terme', 'EAV' => 'Épargne & Vie', default => $compte->type,
                                    } }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Devise</th>
                            <td><span class="badge badge-secondary">{{ $compte->devise }}</span></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Solde réel</th>
                            <td class="font-weight-bold">{{ number_format((float) $compte->solde_reel, 2, ',', ' ') }} {{ $compte->devise }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Solde bloqué</th>
                            <td>{{ number_format((float) $compte->solde_bloque, 2, ',', ' ') }} {{ $compte->devise }}</td>
                        </tr>
                    </table>

                    <form id="compteEditForm" method="POST" action="{{ route('comptes.update', $compte->code_compte) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="portefeuille_id">Agent gestionnaire (Portefeuille)</label>
                            <select name="portefeuille_id" id="portefeuille_id" class="form-control select2" {{ $compte->type === 'GTC' ? 'required' : '' }}>
                                <option value="">-- Aucun --</option>
                                @foreach($portefeuilles as $pf)
                                    @php
                                        $pfAgent = $pf->affectationActive->agent ?? $pf->agent;
                                    @endphp
                                    <option value="{{ $pf->id }}" @selected($compte->portefeuille_id == $pf->id)>
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

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('comptes.show', $compte->code_compte) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>

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
        $('#portefeuille_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Sélectionner une option',
            allowClear: true,
            language: { noResults: function () { return 'Aucun résultat trouvé'; } }
        });

        $('#compteEditForm').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var $btn = form.find('[type=submit]');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Enregistrement…');

            $.ajax({ url: form.attr('action'), type: 'POST', data: form.serialize(), dataType: 'json' })
                .done(function (res) {
                    if (res.success) {
                        showSystemMessage('success', res.message || 'Compte modifié avec succès.');
                        $('#systemMessageModal').one('hidden.bs.modal', function () {
                            window.location.href = "{{ route('comptes.show', $compte->code_compte) }}";
                        });
                    } else {
                        showSystemMessage('error', res.message || 'Erreur.');
                    }
                })
                .fail(function (xhr) {
                    handleAjaxFail(xhr, 'Modification compte');
                })
                .always(function () {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Enregistrer les modifications');
                });
        });
    });
}());
</script>
@endpush

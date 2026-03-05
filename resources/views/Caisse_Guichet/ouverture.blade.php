{{-- Vue Caisse : Ouverture / Fermeture physique des Guichets (multi-devises) --}}
@extends('layouts.app')

@section('page_title', 'Ouverture / Fermeture Guichet')
@section('breadcrumb_parent', 'Caisse / Guichet')
@section('breadcrumb', 'Ouverture / Fermeture')

@section('content')
<div class="container-fluid">

    {{-- ===== EN-TÃŠTE AVEC DATE ET HEURE ===== --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="callout callout-info">
                <h5><i class="fas fa-calendar-day mr-2"></i> Session du {{ now()->isoFormat('dddd D MMMM YYYY') }}</h5>
                <p class="mb-0">
                    Cliquez sur <strong>Ouvrir</strong> pour dÃ©marrer votre session caisse,
                    ou sur <strong>Fermer</strong> pour clÃ´turer votre guichet en fin de journÃ©e.
                </p>
            </div>
        </div>
    </div>

    {{-- ===== CARTES GUICHETS ===== --}}
    <div class="row">
        @forelse($guichets as $g)

        {{-- Couleur de bordure selon statut --}}
        @php
            $couleur    = match($g->statut_operationnel) {
                'OUVERT'   => 'success',
                'SUSPENDU' => 'warning',
                default    => 'danger',
            };
        @php
            $couleur = match($g->statut_operationnel) {
                'OUVERT'   => 'success',
                'SUSPENDU' => 'warning',
                default    => 'danger',
            };
        @endphp

        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-sm border-top-{{ $couleur }}">
                <div class="card-body">

                    {{-- â”€â”€ En-tÃªte carte â”€â”€ --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="font-weight-bold mb-0">
                                <i class="fas fa-cash-register mr-1 text-secondary"></i>
                                {{ $g->code_guichet }}
                            </h5>
                            <small class="text-muted">{{ $g->intitule }}</small>
                        </div>
                        <span class="badge badge-{{ $couleur }} px-3 py-2">
                            @if($g->statut_operationnel === 'OUVERT')
                                <i class="fas fa-door-open mr-1"></i> OUVERT
                            @elseif($g->statut_operationnel === 'SUSPENDU')
                                <i class="fas fa-pause-circle mr-1"></i> SUSPENDU
                            @else
                                <i class="fas fa-door-closed mr-1"></i> FERMÃ‰
                            @endif
                        </span>
                    </div>

                    <hr class="my-2">

                    {{-- â”€â”€ Informations â”€â”€ --}}
                    <table class="table table-sm table-borderless mb-2 small">
                        {{-- Agent titulaire (depuis tb_affectations) --}}
                        <tr>
                            <td class="text-muted pl-0" style="width:35%">
                                <i class="fas fa-user mr-1"></i> Agent
                            </td>
                            <td class="font-weight-bold">
                                @if($g->affectationActive && $g->affectationActive->agent)
                                    {{ $g->affectationActive->agent->nom }}
                                    {{ $g->affectationActive->agent->postnom }}
                                @else
                                    <span class="text-warning">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Non affectÃ©
                                    </span>
                                @endif
                            </td>
                        </tr>

                        {{-- Soldes multi-devises --}}
                        <tr>
                            <td class="text-muted pl-0 align-top">
                                <i class="fas fa-coins mr-1"></i> Soldes
                            </td>
                            <td>
                                @forelse($g->soldes as $s)
                                <span class="badge badge-light border px-2 py-1 mr-1 mb-1">
                                    <span class="text-secondary font-weight-bold">{{ $s->devise_code }}</span>
                                    <span class="ml-1">{{ number_format($s->solde_en_caisse, 2, ',', ' ') }}</span>
                                </span>
                                @empty
                                <span class="text-muted small">â€”</span>
                                @endforelse
                            </td>
                        </tr>
                    </table>

                    {{-- â”€â”€ Boutons Ouvrir / Fermer â”€â”€ --}}
                    <div class="d-flex justify-content-between mt-3">
                        @if($g->statut_operationnel !== 'OUVERT')
                        <button class="btn btn-success btn-changer-statut flex-fill mr-1"
                            data-id="{{ $g->id }}" data-statut="OUVERT"
                            data-code="{{ $g->code_guichet }}">
                            <i class="fas fa-door-open mr-1"></i> Ouvrir
                        </button>
                        @else
                        <button class="btn btn-success flex-fill mr-1" disabled>
                            <i class="fas fa-check-circle mr-1"></i> DÃ©jÃ  ouvert
                        </button>
                        @endif

                        @if($g->statut_operationnel !== 'FERME')
                        <button class="btn btn-danger btn-changer-statut flex-fill ml-1"
                            data-id="{{ $g->id }}" data-statut="FERME"
                            data-code="{{ $g->code_guichet }}">
                            <i class="fas fa-door-closed mr-1"></i> Fermer
                        </button>
                        @else
                        <button class="btn btn-danger flex-fill ml-1" disabled>
                            <i class="fas fa-lock mr-1"></i> DÃ©jÃ  fermÃ©
                        </button>
                        @endif
                    </div>

                    {{-- â”€â”€ Bouton Suspendre (si ouvert) â”€â”€ --}}
                    @if($g->statut_operationnel === 'OUVERT')
                    <button class="btn btn-warning btn-changer-statut btn-sm btn-block mt-2"
                        data-id="{{ $g->id }}" data-statut="SUSPENDU"
                        data-code="{{ $g->code_guichet }}">
                        <i class="fas fa-pause-circle mr-1"></i> Suspendre temporairement
                    </button>
                    @endif

                    {{-- Alerte si aucun agent affectÃ© --}}
                    @if(!$g->affectationActive)
                    <div class="alert alert-warning py-1 px-2 mt-2 mb-0 small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Aucun agent actif.
                        <a href="{{ route('affectations.index') }}" class="alert-link">Affecter via RH</a>
                    </div>
                    @endif

                </div>{{-- /card-body --}}
            </div>
        </div>

        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                Aucun guichet configurÃ©. Veuillez d'abord crÃ©er des guichets dans
                <a href="{{ route('administration.guichets.index') }}" class="alert-link">
                    Administration â†’ Guichets
                </a>.
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection


@section('css')
<style>
    .card { transition: box-shadow 0.2s; }
    .card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.18) !important; }
    .table-sm td { padding: 0.2rem 0; }
    /* Bordure colorée en haut de chaque carte guichet */
    .border-top-success  { border-top: 4px solid #28a745 !important; }
    .border-top-warning  { border-top: 4px solid #ffc107 !important; }
    .border-top-danger   { border-top: 4px solid #dc3545 !important; }
</style>
@endsection


@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ===== CHANGER STATUT (OUVRIR / FERMER / SUSPENDRE) =====
    $(document).on('click', '.btn-changer-statut', function () {
        let id     = $(this).data('id');
        let statut = $(this).data('statut');
        let code   = $(this).data('code');
        let labels = { 'OUVERT': 'ouvrir', 'FERME': 'fermer', 'SUSPENDU': 'suspendre' };

        showUniversalConfirm(
            `ÃŠtes-vous sÃ»r de vouloir <strong>${labels[statut]}</strong> le guichet <strong>${code}</strong> ?`,
            function () {
                $.post(`{{ url('caisses/changer-statut') }}/${id}`, { statut: statut })
                    .done(function (response) {
                        showSystemMessage('success', response.message);
                        setTimeout(() => location.reload(), 800);
                    })
                    .fail(function (xhr) {
                        showSystemMessage('error', xhr.responseJSON?.message || 'Erreur lors du changement de statut.');
                    });
            },
            'Confirmer l\'action'
        );
    });

});
</script>
@endpush

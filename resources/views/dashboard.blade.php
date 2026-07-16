@extends('layouts.app')

@section('page_title', 'Accueil')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Tableau de bord')

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- ================================================================
         HERO -- Logo + Titre
    ================================================================ --}}
    <div class="row justify-content-center mt-4 mb-3">
        <div class="col-12 text-center">
            <img src="{{ asset('dist/img/vrailogoeben_redimensionner 1 .png') }}"
                 alt="Logo Coopec EBEN"
                 style="height:110px; width:auto; filter:drop-shadow(0 4px 12px rgba(0,0,0,.35));">
            <h2 class="mt-3 mb-0 font-weight-bold" style="letter-spacing:.5px;">
                Coopec EBEN
            </h2>
            <p class="text-muted mb-0" style="font-size:.95rem;">
                Syst&egrave;me de Gestion Bancaire et Financier &mdash; <em>Nexus BMB Tech Soft Banking</em>
            </p>
            <hr class="mt-3 mb-4" style="max-width:500px; border-color:rgba(255,255,255,.12);">
        </div>
    </div>

    {{-- ================================================================
         MESSAGE DE BIENVENUE
    ================================================================ --}}
    @php
        /** @var \App\Models\User $authUser */
        $authUser   = Auth::user();
        $agent      = $authUser->agent;
        $nomComplet = $agent
            ? trim(collect([$agent->prenom, $agent->nom, $agent->postnom])->filter()->implode(' '))
            : $authUser->name;

        $roleCodes  = $authUser->getRoleCodes();
        $rolesLabel = \App\Models\RH\Role::whereIn('code', $roleCodes)
                        ->pluck('nom')->join(', ') ?: 'Utilisateur';
    @endphp

    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 col-md-10">
            <div class="card card-outline card-primary elevation-2">
                <div class="card-body text-center py-4">
                    <i class="fas fa-hand-holding-heart fa-2x text-primary mb-3"></i>
                    <h4 class="font-weight-bold mb-1">
                        Bienvenue, {{ $nomComplet }} !
                    </h4>
                    <p class="text-muted mb-2" style="font-size:.95rem;">
                        Vous &ecirc;tes connect&eacute;(e) en tant que
                        <strong>{{ $rolesLabel }}</strong>.
                    </p>
                    <p class="mb-0" style="font-size:.88rem; opacity:.7;">
                        {{ now()->isoFormat('dddd D MMMM YYYY') }} &mdash; {{ now()->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         ALERTE -- Dossiers credit en retard (recouvrement automatique)
    ================================================================ --}}
    @if(isset($alerteRecouvrementCount) && $alerteRecouvrementCount > 0 && \Illuminate\Support\Facades\Route::has('recouvrement.index'))
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 col-md-10">
            <div class="alert d-flex align-items-center justify-content-between flex-wrap"
                 style="background:rgba(180,30,30,.35); border:1px solid rgba(220,53,69,.8); border-radius:.5rem; box-shadow:0 0 18px rgba(220,53,69,.25);">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3" style="animation:pulse-alert 1.4s infinite;"></i>
                    <div>
                        <strong class="text-danger">Dossiers en retard &mdash; Action requise !</strong>
                        <p class="mb-0" style="font-size:.88rem;">
                            <strong>{{ $alerteRecouvrementCount }}</strong> dossier(s) cr&eacute;dit ont au moins une
                            &eacute;ch&eacute;ance <strong class="text-danger">en retard</strong> (date d&eacute;pass&eacute;e).
                            Cliquez sur le bouton pour consulter et traiter les recouvrements.
                        </p>
                    </div>
                </div>
                <a href="{{ route('recouvrement.index') }}" class="btn btn-danger flex-shrink-0">
                    <i class="fas fa-bolt mr-1"></i> Lancer le recouvrement
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ================================================================
         MODULES ACCESSIBLES (acces rapide selon permissions)
    ================================================================ --}}
    @php
        $modules = [
            [
                'perm'  => 'EBEN-PER15',
                'route' => 'clients.index',
                'icon'  => 'fas fa-users',
                'color' => 'info',
                'label' => 'Clients &amp; Membres',
                'desc'  => 'Gestion des membres et dossiers clients',
            ],
            [
                'perm'  => 'EBEN-PER18',
                'route' => 'comptes.index',
                'icon'  => 'fas fa-piggy-bank',
                'color' => 'success',
                'label' => 'Comptes Clients',
                'desc'  => 'Ouverture et gestion des comptes',
            ],
            [
                'perm'  => 'EBEN-PER10',
                'route' => 'caisses.operations.index',
                'icon'  => 'fas fa-cash-register',
                'color' => 'warning',
                'label' => 'Caisse &amp; Guichet',
                'desc'  => 'Op&eacute;rations de caisse et transactions',
            ],
            [
                'perm'  => 'EBEN-PER6',
                'route' => 'agents.index',
                'icon'  => 'fas fa-id-badge',
                'color' => 'danger',
                'label' => 'Ressources Humaines',
                'desc'  => 'Agents, postes, services et affectations',
            ],
            [
                'perm'  => 'EBEN-PER44',
                'route' => 'tresorerie.etat-coffre',
                'icon'  => 'fas fa-vault',
                'color' => 'secondary',
                'label' => 'Tr&eacute;sorerie &amp; Coffre',
                'desc'  => 'Mouvements inter-caisses et coffre central',
            ],
            [
                'perm'  => 'EBEN-PER1',
                'route' => 'administration.utilisateurs.liste',
                'icon'  => 'fas fa-user-cog',
                'color' => 'dark',
                'label' => 'Administration',
                'desc'  => 'Utilisateurs, r&ocirc;les et permissions',
            ],
        ];
        $userPerms = $userPermCodes ?? [];
    @endphp

    <div class="row justify-content-center">
        @foreach($modules as $mod)
            @if(in_array($mod['perm'], $userPerms) && \Illuminate\Support\Facades\Route::has($mod['route']))
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route($mod['route']) }}" class="text-decoration-none">
                    <div class="card card-outline card-{{ $mod['color'] }} elevation-2 h-100 text-center accueil-module-card">
                        <div class="card-body py-4">
                            <i class="{{ $mod['icon'] }} fa-2x text-{{ $mod['color'] }} mb-3"></i>
                            <h5 class="font-weight-bold mb-1" style="font-size:.97rem;">
                                {!! $mod['label'] !!}
                            </h5>
                            <p class="text-muted mb-0" style="font-size:.82rem; line-height:1.4;">
                                {!! $mod['desc'] !!}
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        @endforeach
    </div>

    {{-- ================================================================
         PIED DE PAGE DISCRET
    ================================================================ --}}
    <div class="row mt-2 mb-4">
        <div class="col-12 text-center">
            <small class="text-muted" style="font-size:.78rem; opacity:.55;">
                &copy; {{ date('Y') }} Coopec EBEN &mdash; Nexus BMB Tech Soft Banking.
                Tous droits r&eacute;serv&eacute;s.
            </small>
        </div>
    </div>

</div>
</section>
@endsection

@push('css')
<style>
.accueil-module-card {
    transition: transform .18s ease, box-shadow .18s ease;
    cursor: pointer;
}
.accueil-module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 28px rgba(0,0,0,.35) !important;
}
@keyframes pulse-alert {
    0%   { opacity: 1; }
    50%  { opacity: .4; }
    100% { opacity: 1; }
}
</style>
@endpush
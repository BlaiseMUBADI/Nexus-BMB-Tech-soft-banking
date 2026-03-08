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
        $agent      = Auth::user()->agent;
        $nomComplet = $agent
            ? trim(collect([$agent->prenom, $agent->nom, $agent->postnom])->filter()->implode(' '))
            : Auth::user()->name;

        $roleCodes  = Auth::user()->getRoleCodes();
        $rolesLabel = \App\Models\Role::whereIn('code', $roleCodes)
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
                'route' => 'comptes.liste',
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
                'route' => 'rh.services.liste',
                'icon'  => 'fas fa-id-badge',
                'color' => 'danger',
                'label' => 'Ressources Humaines',
                'desc'  => 'Agents, postes, services et affectations',
            ],
            [
                'perm'  => 'EBEN-PER44',
                'route' => 'tresorerie.coffre',
                'icon'  => 'fas fa-vault',
                'color' => 'secondary',
                'label' => 'Tr&eacute;sorerie &amp; Coffre',
                'desc'  => 'Mouvements inter-caisses et coffre central',
            ],
            [
                'perm'  => 'EBEN-PER1',
                'route' => 'admin.utilisateurs.liste',
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
            @if(in_array($mod['perm'], $userPerms))
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
</style>
@endpush
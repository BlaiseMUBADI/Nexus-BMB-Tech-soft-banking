@extends('layouts.app')

@section('page_title', 'Mon Profil')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Mon Profil')



@section('content')

@php
    $photoUrl = ($agent && $agent->photo)
        ? route('agents.photo', basename($agent->photo))
        : null;
    $fullName = $agent
        ? trim($agent->nom . ' ' . ($agent->postnom ?? '') . ' ' . ($agent->prenom ?? ''))
        : $user->name;
    $activeAff = $affectations->firstWhere('Etat', 'Actif');
@endphp

<div class="container-fluid px-0">

    {{-- ══ Bandeau de couverture ═══════════════════════════════════ --}}
    <div class="profile-cover d-flex align-items-end" style="gap:1.2rem;">
        <div class="mr-3 pb-0">
            @if($photoUrl)
                <img src="{{ $photoUrl }}" class="profile-cover-avatar" id="coverPhoto"
                     data-toggle="modal" data-target="#photoZoomModal" alt="Photo">
            @else
                <div class="profile-cover-avatar-placeholder">
                    <i class="fas fa-user-tie"></i>
                </div>
            @endif
        </div>
        <div class="profile-cover-info pb-3 flex-grow-1">
            <h4>{{ $fullName }}</h4>
            <div class="d-flex flex-wrap align-items-center" style="gap:.5rem;">
                @if($agent)
                    <span class="badge-matricule"><i class="fas fa-id-card mr-1"></i>{{ $agent->matricule }}</span>
                @endif
                @if($activeAff && $activeAff->poste)
                    <span class="badge badge-info" style="border-radius:50px;font-size:.75rem;">
                        <i class="fas fa-briefcase mr-1"></i>{{ $activeAff->poste->nom }}
                    </span>
                @endif
                @if($activeAff && $activeAff->poste && $activeAff->poste->service)
                    <span class="badge badge-secondary" style="border-radius:50px;font-size:.75rem;">
                        <i class="fas fa-building mr-1"></i>{{ $activeAff->poste->service->nom }}
                    </span>
                @endif
                @if($agent)
                    <span class="badge {{ $agent->statut === 'Actif' ? 'badge-success' : 'badge-danger' }}" style="border-radius:50px;font-size:.75rem;">
                        {{ $agent->statut ?? 'N/A' }}
                    </span>
                @endif
            </div>
            <div class="mt-1" style="font-size:.82rem; color:rgba(255,255,255,.6);">
                <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                &nbsp;·&nbsp;
                <i class="fas fa-clock mr-1"></i>Compte créé le {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}
            </div>
        </div>
        <div class="pb-3 d-none d-md-block text-right" style="min-width:160px;">
            <div style="font-size:.8rem;color:rgba(255,255,255,.5);">Compte utilisateur</div>
            <div style="color:#fff;font-weight:600;">{{ $user->name }}</div>
            <span class="badge {{ ($user->etat ?? 'actif') === 'actif' ? 'badge-success' : 'badge-danger' }}" style="border-radius:50px;font-size:.75rem;">
                {{ ucfirst($user->etat ?? 'actif') }}
            </span>
        </div>
    </div>

    {{-- ══ Onglets ══════════════════════════════════════════════════ --}}
    <div class="card card-dark card-outline shadow-sm" style="border-radius:0 0 .5rem .5rem; border-top:none;">
        <div class="card-header p-0 border-bottom border-secondary">
            <ul class="nav profile-tabs px-3" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-identite" role="tab">
                        <i class="fas fa-user mr-1"></i>Identité agent
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-fonctions" role="tab">
                        <i class="fas fa-sitemap mr-1"></i>Fonctions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-acces" role="tab">
                        <i class="fas fa-shield-alt mr-1"></i>Rôles & Accès
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-compte" role="tab">
                        <i class="fas fa-cog mr-1"></i>Mon compte
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">

                {{-- ── Onglet 1 : Identité agent ──────────────────────── --}}
                <div class="tab-pane fade show active" id="tab-identite" role="tabpanel">
                    @if($agent)
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" class="img-fluid rounded-circle border border-secondary shadow"
                                     style="width:160px;height:160px;object-fit:cover;cursor:pointer;"
                                     data-toggle="modal" data-target="#photoZoomModal">
                                <div class="mt-2 text-muted" style="font-size:.78rem;">Cliquer pour agrandir</div>
                            @else
                                <div class="rounded-circle border border-secondary d-inline-flex align-items-center justify-content-center"
                                     style="width:160px;height:160px;background:#2d3748;font-size:4rem;color:#4a5568;">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="mt-2 text-muted" style="font-size:.78rem;">Aucune photo</div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Matricule</div>
                                    <div class="info-value"><code>{{ $agent->matricule }}</code></div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Nom</div>
                                    <div class="info-value">{{ $agent->nom }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Post-nom</div>
                                    <div class="info-value">{{ $agent->postnom ?? '–' }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Prénom</div>
                                    <div class="info-value">{{ $agent->prenom ?? '–' }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Sexe</div>
                                    <div class="info-value">
                                        @if($agent->sexe === 'M')
                                            <i class="fas fa-mars text-info mr-1"></i>Masculin
                                        @elseif($agent->sexe === 'F')
                                            <i class="fas fa-venus mr-1" style="color:#e91e8c;"></i>Féminin
                                        @else
                                            {{ $agent->sexe ?? '–' }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Date de naissance</div>
                                    <div class="info-value">
                                        {{ $agent->date_naissance ? \Carbon\Carbon::parse($agent->date_naissance)->format('d/m/Y') : '–' }}
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Téléphone</div>
                                    <div class="info-value">{{ $agent->telephone ?? '–' }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">E-mail agent</div>
                                    <div class="info-value">{{ $agent->email ?? '–' }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Date d'embauche</div>
                                    <div class="info-value">
                                        {{ $agent->date_embauche ? \Carbon\Carbon::parse($agent->date_embauche)->format('d/m/Y') : '–' }}
                                    </div>
                                </div>
                                <div class="col-sm-8 mb-3">
                                    <div class="info-label">Adresse</div>
                                    <div class="info-value">{{ $agent->adresse ?? '–' }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="info-label">Statut</div>
                                    <div class="info-value">
                                        <span class="badge {{ $agent->statut === 'Actif' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $agent->statut ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3 d-block"></i>
                            Aucun dossier agent lié à ce compte utilisateur.
                        </div>
                    @endif
                </div>

                {{-- ── Onglet 2 : Fonctions & Affectations ─────────────── --}}
                <div class="tab-pane fade" id="tab-fonctions" role="tabpanel">
                    @if($agent)
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card card-primary card-outline mb-3">
                                <div class="card-body text-center py-3">
                                    <div class="text-primary mb-1"><i class="fas fa-briefcase fa-2x"></i></div>
                                    <div class="info-label">Poste actuel</div>
                                    <div class="info-value mt-1">{{ $activeAff && $activeAff->poste ? $activeAff->poste->nom : '–' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-info card-outline mb-3">
                                <div class="card-body text-center py-3">
                                    <div class="text-info mb-1"><i class="fas fa-building fa-2x"></i></div>
                                    <div class="info-label">Service</div>
                                    <div class="info-value mt-1">{{ $activeAff && $activeAff->poste && $activeAff->poste->service ? $activeAff->poste->service->nom : '–' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-success card-outline mb-3">
                                <div class="card-body text-center py-3">
                                    <div class="text-success mb-1"><i class="fas fa-calendar-check fa-2x"></i></div>
                                    <div class="info-label">Début affectation</div>
                                    <div class="info-value mt-1">
                                        {{ $activeAff ? \Carbon\Carbon::parse($activeAff->date_debut)->format('d/m/Y') : '–' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted mb-3"><i class="fas fa-history mr-1"></i>Historique des affectations</h6>
                    <div class="timeline-aff">
                        @forelse($affectations as $aff)
                            <div class="timeline-aff-item {{ $aff->Etat !== 'Actif' ? 'aff-inactive' : '' }}">
                                <div class="d-flex align-items-start justify-content-between flex-wrap">
                                    <div>
                                        <strong>{{ $aff->poste->nom ?? 'Poste inconnu' }}</strong>
                                        @if($aff->poste && $aff->poste->service)
                                            <span class="text-muted ml-1">— {{ $aff->poste->service->nom }}</span>
                                        @endif
                                    </div>
                                    <span class="badge {{ $aff->Etat === 'Actif' ? 'badge-success' : 'badge-secondary' }}">{{ $aff->Etat }}</span>
                                </div>
                                <div class="text-muted" style="font-size:.8rem;">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ \Carbon\Carbon::parse($aff->date_debut)->format('d/m/Y') }}
                                    @if($aff->date_fin)
                                        → {{ \Carbon\Carbon::parse($aff->date_fin)->format('d/m/Y') }}
                                    @else
                                        → <em>en cours</em>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">Aucune affectation enregistrée.</div>
                        @endforelse
                    </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-sitemap fa-3x mb-3 d-block"></i>
                            Aucune fonction disponible — pas de dossier agent lié.
                        </div>
                    @endif
                </div>

                {{-- ── Onglet 3 : Rôles & Accès ────────────────────────── --}}
                <div class="tab-pane fade" id="tab-acces" role="tabpanel">
                    <div class="row">
                        <div class="col-md-5 mb-4">
                            <div class="card card-dark card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-sign-in-alt mr-2"></i>Informations de connexion</h3>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="info-label">Nom d'utilisateur</div>
                                        <div class="info-value">{{ $user->name }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="info-label">Adresse e-mail</div>
                                        <div class="info-value">{{ $user->email }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="info-label">État du compte</div>
                                        <div class="info-value">
                                            <span class="badge {{ ($user->etat ?? 'actif') === 'actif' ? 'badge-success' : 'badge-danger' }}">
                                                {{ ucfirst($user->etat ?? 'actif') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="info-label">Compte créé le</div>
                                        <div class="info-value">{{ $user->created_at ? $user->created_at->format('d/m/Y à H:i') : '–' }}</div>
                                    </div>
                                    @if($agent)
                                    <div>
                                        <div class="info-label">Matricule agent lié</div>
                                        <div class="info-value"><code>{{ $agent->matricule }}</code></div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 mb-4">
                            <div class="card card-primary card-outline mb-3">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i>Rôles attribués</h3>
                                    <div class="card-tools">
                                        <span class="badge badge-primary">{{ $userRoles->count() }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php $myRoles = $roles->whereIn('code', $userRoles->all()); @endphp
                                    @if($myRoles->isEmpty())
                                        <span class="text-muted">Aucun rôle attribué.</span>
                                    @else
                                        @foreach($myRoles as $role)
                                            <span class="role-badge"><i class="fas fa-tag mr-1"></i>{{ $role->nom }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="card card-success card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-key mr-2"></i>Permissions héritées</h3>
                                    <div class="card-tools">
                                        <span class="badge badge-success">{{ $userPermissions->count() }}</span>
                                    </div>
                                </div>
                                <div class="card-body" style="max-height:220px;overflow-y:auto;">
                                    @php $myPerms = $permissions->whereIn('code', $userPermissions->all()); @endphp
                                    @if($myPerms->isEmpty())
                                        <span class="text-muted">Aucune permission héritée.</span>
                                    @else
                                        @foreach($myPerms as $perm)
                                            <span class="perm-badge"><i class="fas fa-check mr-1"></i>{{ $perm->nom }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Onglet 4 : Modifier le compte ──────────────────── --}}
                <div class="tab-pane fade" id="tab-compte" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Informations du compte</h3>
                                </div>
                                <form method="POST" action="{{ route('profile.update') }}">
                                    @csrf @method('PUT')
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Nom d'utilisateur</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" name="name"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       value="{{ old('name', $user->name) }}" required>
                                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Adresse e-mail</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" name="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       value="{{ old('email', $user->email) }}" required>
                                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Enregistrer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-lock mr-2"></i>Changer le mot de passe</h3>
                                </div>
                                <form method="POST" action="{{ route('profile.update') }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="_change_password" value="1">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Mot de passe actuel</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                </div>
                                                <input type="password" name="current_password" id="current_password"
                                                       class="form-control @error('current_password') is-invalid @enderror">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="#current_password">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Nouveau mot de passe</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                </div>
                                                <input type="password" name="password" id="new_password"
                                                       class="form-control @error('password') is-invalid @enderror">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="#new_password">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mt-1" style="height:4px;border-radius:2px;background:#343a40;">
                                                <div id="pwStrengthBar" class="password-strength" style="width:0;background:#dc3545;"></div>
                                            </div>
                                            <small id="pwStrengthText" class="text-muted"></small>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Confirmer le nouveau mot de passe</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                                                </div>
                                                <input type="password" name="password_confirmation" id="confirm_password"
                                                       class="form-control">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="#confirm_password">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-key mr-1"></i>Changer le mot de passe
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /.tab-content --}}
        </div>{{-- /.card-body --}}
    </div>{{-- /.card --}}
</div>

{{-- ══ Modal zoom photo ══════════════════════════════════════════ --}}
@if($photoUrl)
<div class="modal fade" id="photoZoomModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header bg-dark border-secondary py-2">
                <h6 class="modal-title text-white"><i class="fas fa-user-circle mr-2"></i>{{ $fullName }}</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-2 text-center">
                <img src="{{ $photoUrl }}" class="img-fluid rounded shadow" style="max-height:70vh;object-fit:contain;">
            </div>
        </div>
    </div>
</div>
@endif

{{-- Données PHP → JS sans aucune directive Blade dans un <script> --}}
<script id="_profileData" type="application/json"><?php echo json_encode(['status' => session('status'), 'errorKeys' => $errors->keys()]); ?></script>

@endsection


@push('css')
<style>
    /* ── Bandeau de couverture ──────────────────────────── */
    .profile-cover {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border-radius: .5rem .5rem 0 0;
        padding: 2rem 2rem 0;
    }
    .profile-cover-avatar {
        width: 110px; height: 110px;
        border-radius: 50%;
        border: 4px solid #fff;
        object-fit: cover;
        box-shadow: 0 4px 16px rgba(0,0,0,.5);
        cursor: pointer;
        transition: transform .2s;
    }
    .profile-cover-avatar:hover { transform: scale(1.05); }
    .profile-cover-avatar-placeholder {
        width: 110px; height: 110px;
        border-radius: 50%;
        border: 4px solid #fff;
        background: #2d3748;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; color: #a0aec0;
        box-shadow: 0 4px 16px rgba(0,0,0,.5);
    }
    .profile-cover-info { color: #fff; }
    .profile-cover-info h4 { margin-bottom: .15rem; font-weight: 700; }
    .badge-matricule {
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.3);
        color: #fff;
        font-size: .78rem;
        padding: .3em .7em;
        border-radius: 50px;
    }
    /* ── Onglets profil ─────────────────────────────────── */
    .profile-tabs .nav-link {
        color: #adb5bd;
        font-size: .85rem;
        padding: .6rem 1rem;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        transition: color .2s, border-color .2s;
    }
    .profile-tabs .nav-link.active,
    .profile-tabs .nav-link:hover { color: #fff; border-bottom-color: #007bff; }
    /* ── Infos ──────────────────────────────────────────── */
    .info-label { font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; margin-bottom: .1rem; }
    .info-value { font-size: .92rem; font-weight: 500; word-break: break-word; }
    /* ── Badges rôles/permissions ───────────────────────── */
    .role-badge {
        display: inline-block;
        background: rgba(0,123,255,.15);
        border: 1px solid rgba(0,123,255,.35);
        color: #74b9ff;
        border-radius: 50px;
        padding: .25em .75em;
        font-size: .8rem;
        margin: .2rem .15rem;
    }
    .perm-badge {
        display: inline-block;
        background: rgba(40,167,69,.12);
        border: 1px solid rgba(40,167,69,.3);
        color: #55efc4;
        border-radius: 50px;
        padding: .2em .6em;
        font-size: .75rem;
        margin: .15rem .1rem;
    }
    /* ── Timeline affectations ──────────────────────────── */
    .timeline-aff { position: relative; padding-left: 1.5rem; }
    .timeline-aff::before {
        content: '';
        position: absolute; left: .45rem; top: .4rem; bottom: .4rem;
        width: 2px; background: #343a40;
    }
    .timeline-aff-item { position: relative; padding-bottom: 1.2rem; }
    .timeline-aff-item::before {
        content: '';
        position: absolute; left: -1.5rem; top: .35rem;
        width: 10px; height: 10px;
        border-radius: 50%;
        background: #007bff;
        border: 2px solid #1a1a2e;
    }
    .timeline-aff-item.aff-inactive::before { background: #6c757d; }
    /* ── Force mot de passe ─────────────────────────────── */
    .password-strength { height: 4px; border-radius: 2px; transition: width .3s, background .3s; }
</style>
@endpush

@push('js')
<script>
var _d             = JSON.parse(document.getElementById('_profileData').textContent);
var _profileStatus    = _d.status;
var _profileErrorKeys = _d.errorKeys;
$(function () {
    /* ── Notifications flash ────────────────────────────── */
    if (_profileStatus === 'profile-updated') {
        showSystemMessage('success', 'Informations du compte mises à jour.');
        $('#profileTabs a[href="#tab-compte"]').tab('show');
    }
    if (_profileStatus === 'password-updated') {
        showSystemMessage('success', 'Mot de passe changé avec succès.');
        $('#profileTabs a[href="#tab-compte"]').tab('show');
    }

    /* ── Ouvrir onglet compte si erreurs de validation ─── */
    var _compteFields = ['name','email','current_password','password'];
    if (_compteFields.some(function(k){ return _profileErrorKeys.indexOf(k) !== -1; })) {
        $('#profileTabs a[href="#tab-compte"]').tab('show');
    }

    /* ── Afficher/masquer mot de passe ─────────────────── */
    $(document).on('click', '.toggle-pw', function () {
        var $input = $($(this).data('target'));
        $input.attr('type', $input.attr('type') === 'text' ? 'password' : 'text');
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    /* ── Jauge de force du mot de passe ────────────────── */
    $('#new_password').on('input', function () {
        var val = $(this).val();
        var score = 0;
        if (val.length >= 8)          score++;
        if (/[A-Z]/.test(val))        score++;
        if (/[0-9]/.test(val))        score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        var colors = ['#dc3545','#dc3545','#ffc107','#28a745','#1e7e34'];
        var labels = ['','Faible','Moyen','Fort','Très fort'];
        $('#pwStrengthBar').css({ width: (score * 25) + '%', background: colors[score] });
        $('#pwStrengthText').text(val.length ? labels[score] : '').css('color', colors[score]);
    });
});
</script>
@endpush

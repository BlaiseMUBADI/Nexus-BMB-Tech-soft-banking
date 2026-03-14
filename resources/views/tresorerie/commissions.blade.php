@extends('layouts.app')

@section('page_title', 'Paramétrage des commissions')
@section('breadcrumb_parent', 'Trésorerie')
@section('breadcrumb', 'Commissions')

@section('content')
<div class="container-fluid commission-page">
    @php
        $editingRule = $editingRule ?? null;
        $isEditing = (bool) $editingRule;
        $formAction = $isEditing ? route('tresorerie.commissions.update', $editingRule) : route('tresorerie.commissions.store');
        $formTitle = $isEditing ? 'Modifier une règle' : 'Nouvelle règle';
        $selectedRule = $editingRule;

        $validationErrors = session('errors');
        $validationErrorItems = [];
        if (is_object($validationErrors) && method_exists($validationErrors, 'all')) {
            $validationErrorItems = $validationErrors->all();
        } elseif (is_array($validationErrors)) {
            $validationErrorItems = $validationErrors;
        }
    @endphp

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(!empty($validationErrorItems))
        <div class="alert alert-danger shadow-sm">
            <div class="font-weight-bold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Veuillez corriger les champs signalés.</div>
            <ul class="mb-0 pl-3">
                @foreach($validationErrorItems as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="commission-hero mb-3">
        <div>
            <h4 class="mb-1">Pilotage des commissions</h4>
            <p class="mb-0 text-muted">Définis des règles par opération, guichet, compte, devise, zone et portefeuille avec priorité et période d'effet.</p>
        </div>
        <div class="commission-hero-badge">
            <i class="fas fa-shield-alt"></i>
            <span>Calcul sécurisé et traçable</span>
        </div>
    </div>

    <div class="row mb-3 commission-kpis">
        <div class="col-xl-3 col-md-6 col-12 mb-2">
            <div class="small-box bg-warning shadow-sm mb-0">
                <div class="inner">
                    <h4>{{ $stats['total'] }}</h4>
                    <p>Règles configurées</p>
                </div>
                <div class="icon"><i class="fas fa-sliders-h"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-2">
            <div class="small-box bg-success shadow-sm mb-0">
                <div class="inner">
                    <h4>{{ $stats['actives'] }}</h4>
                    <p>Règles actives</p>
                </div>
                <div class="icon"><i class="fas fa-check"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-2">
            <div class="small-box bg-info shadow-sm mb-0">
                <div class="inner">
                    <h4>{{ $stats['pourcentages'] }}</h4>
                    <p>Règles en %</p>
                </div>
                <div class="icon"><i class="fas fa-percentage"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-2">
            <div class="small-box bg-primary shadow-sm mb-0">
                <div class="inner">
                    <h4>{{ $stats['fixes'] }}</h4>
                    <p>Montants fixes</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-5 col-xl-5 col-12 mb-3">
            <div class="card card-warning card-outline shadow-sm h-100 commission-form-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0"><i class="fas fa-percent mr-2 text-warning"></i>{{ $formTitle }}</h5>
                        <small class="text-muted">Une règle plus prioritaire est appliquée avant les autres.</small>
                    </div>
                    @if($isEditing)
                        <a href="{{ route('tresorerie.commissions.index') }}" class="btn btn-sm btn-outline-secondary">Annuler</a>
                    @endif
                </div>
                <form method="POST" action="{{ $formAction }}">
                    @csrf
                    @if($isEditing)
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="commission-section-title">
                            <i class="fas fa-tag"></i>
                            <span>Identification</span>
                        </div>
                        <div class="form-group">
                            <label>Libellé</label>
                            <input type="text" name="libelle" class="form-control @error('libelle') is-invalid @enderror"
                                   value="{{ old('libelle', $selectedRule->libelle ?? '') }}" required>
                        </div>

                        <div class="commission-section-title mt-3">
                            <i class="fas fa-crosshairs"></i>
                            <span>Périmètre d'application</span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Opération</label>
                                <select name="code_operation" class="form-control @error('code_operation') is-invalid @enderror" required>
                                    @foreach($operationChoices as $choice)
                                        <option value="{{ $choice }}" @selected(old('code_operation', $selectedRule->code_operation ?? 'TOUS') === $choice)>{{ $choice }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Type guichet</label>
                                <select name="type_guichet" class="form-control @error('type_guichet') is-invalid @enderror" required>
                                    @foreach($guichetTypeChoices as $choice)
                                        <option value="{{ $choice }}" @selected(old('type_guichet', $selectedRule->type_guichet ?? 'TOUS') === $choice)>{{ $choice }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Type compte</label>
                                <select name="type_compte" class="form-control @error('type_compte') is-invalid @enderror" required>
                                    @foreach($accountTypeChoices as $choice)
                                        <option value="{{ $choice }}" @selected(old('type_compte', $selectedRule->type_compte ?? 'TOUS') === $choice)>{{ $choice }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Devise</label>
                                <select name="devise_code" class="form-control @error('devise_code') is-invalid @enderror">
                                    <option value="">Toutes</option>
                                    @foreach($devises as $devise)
                                        <option value="{{ $devise->code_iso }}" @selected(old('devise_code', $selectedRule->devise_code ?? null) === $devise->code_iso)>
                                            {{ $devise->code_iso }} - {{ $devise->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Zone</label>
                                <select name="code_zone" class="form-control @error('code_zone') is-invalid @enderror">
                                    <option value="">Toutes</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->code_zone }}" @selected(old('code_zone', $selectedRule->code_zone ?? null) === $zone->code_zone)>
                                            {{ $zone->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Portefeuille</label>
                                <select name="portefeuille_id" class="form-control @error('portefeuille_id') is-invalid @enderror">
                                    <option value="">Tous</option>
                                    @foreach($portefeuilles as $portefeuille)
                                        <option value="{{ $portefeuille->id }}" @selected((string) old('portefeuille_id', $selectedRule->portefeuille_id ?? '') === (string) $portefeuille->id)>
                                            {{ $portefeuille->nom_portefeuille }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="commission-section-title mt-3">
                            <i class="fas fa-calculator"></i>
                            <span>Calcul</span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Mode</label>
                                <select name="mode_calcul" class="form-control @error('mode_calcul') is-invalid @enderror" required>
                                    @foreach($modeChoices as $choice)
                                        <option value="{{ $choice }}" @selected(old('mode_calcul', $selectedRule->mode_calcul ?? 'FIXE') === $choice)>{{ $choice }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Valeur</label>
                                <input type="number" step="0.0001" min="0" name="valeur" class="form-control @error('valeur') is-invalid @enderror"
                                       value="{{ old('valeur', isset($selectedRule) ? (float) $selectedRule->valeur : '') }}" required>
                                <small class="text-muted d-block mt-1">Pourcentage ou montant fixe selon le mode.</small>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Priorité</label>
                                <input type="number" min="1" max="9999" name="priorite" class="form-control @error('priorite') is-invalid @enderror"
                                       value="{{ old('priorite', $selectedRule->priorite ?? $nextPriority) }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Montant min</label>
                                <input type="number" step="0.01" min="0" name="montant_min" class="form-control @error('montant_min') is-invalid @enderror"
                                       value="{{ old('montant_min', isset($selectedRule) && !is_null($selectedRule->montant_min) ? (float) $selectedRule->montant_min : '') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Montant max</label>
                                <input type="number" step="0.01" min="0" name="montant_max" class="form-control @error('montant_max') is-invalid @enderror"
                                       value="{{ old('montant_max', isset($selectedRule) && !is_null($selectedRule->montant_max) ? (float) $selectedRule->montant_max : '') }}">
                            </div>
                        </div>

                        <div class="commission-section-title mt-3">
                            <i class="far fa-calendar-alt"></i>
                            <span>Validité</span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror"
                                       value="{{ old('date_debut', isset($selectedRule) && $selectedRule->date_debut ? $selectedRule->date_debut->toDateString() : now()->toDateString()) }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control @error('date_fin') is-invalid @enderror"
                                       value="{{ old('date_fin', isset($selectedRule) && $selectedRule->date_fin ? $selectedRule->date_fin->toDateString() : '') }}">
                            </div>
                        </div>

                        <div class="commission-section-title mt-3">
                            <i class="far fa-sticky-note"></i>
                            <span>Notes</span>
                        </div>
                        <div class="form-group">
                            <label>Observations</label>
                            <textarea name="observations" rows="3" class="form-control @error('observations') is-invalid @enderror">{{ old('observations', $selectedRule->observations ?? '') }}</textarea>
                        </div>

                        <div class="custom-control custom-switch">
                            <input type="hidden" name="est_actif" value="0">
                            <input type="checkbox" class="custom-control-input" id="commissionActive" name="est_actif" value="1"
                                   @checked(old('est_actif', isset($selectedRule) ? (int) $selectedRule->est_actif : 1))>
                            <label class="custom-control-label" for="commissionActive">Règle active</label>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between flex-wrap commission-form-footer" style="gap:8px;">
                        <small class="text-muted align-self-center">Les règles les plus prioritaires et les plus récentes sont appliquées en premier.</small>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i>{{ $isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xxl-7 col-xl-7 col-12 mb-3">
            <div class="card card-outline card-primary shadow-sm commission-list-card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
                    <h5 class="mb-0"><i class="fas fa-list mr-2 text-primary"></i>Règles enregistrées</h5>
                    <span class="badge badge-light border px-3 py-2">{{ $rules->count() }} règle(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 commission-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th>Portée</th>
                                    <th>Montant</th>
                                    <th>Période</th>
                                    <th>Statut</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $rule->libelle }}</div>
                                            <div class="small text-muted">
                                                {{ $rule->code_operation }} | {{ $rule->type_guichet }} | {{ $rule->type_compte }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $rule->devise_code ?: 'Toutes devises' }}
                                                @if($rule->code_zone)
                                                    | Zone {{ optional($zones->firstWhere('code_zone', $rule->code_zone))->nom ?? $rule->code_zone }}
                                                @endif
                                                @if($rule->portefeuille)
                                                    | {{ $rule->portefeuille->nom_portefeuille }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="small">
                                            <div>Min : {{ is_null($rule->montant_min) ? '0' : number_format((float) $rule->montant_min, 2, ',', ' ') }}</div>
                                            <div>Max : {{ is_null($rule->montant_max) ? 'Illimité' : number_format((float) $rule->montant_max, 2, ',', ' ') }}</div>
                                            <div>Priorité : <span class="badge badge-dark">{{ $rule->priorite }}</span></div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $rule->mode_calcul === 'POURCENTAGE' ? 'badge-info' : 'badge-secondary' }}">
                                                {{ $rule->mode_calcul }}
                                            </span>
                                            <div class="font-weight-bold mt-1">
                                                {{ $rule->mode_calcul === 'POURCENTAGE' ? number_format((float) $rule->valeur, 2, ',', ' ') . ' %' : number_format((float) $rule->valeur, 2, ',', ' ') }}
                                            </div>
                                        </td>
                                        <td class="small">
                                            <div>Du {{ optional($rule->date_debut)->format('d/m/Y') }}</div>
                                            <div>Au {{ $rule->date_fin ? $rule->date_fin->format('d/m/Y') : 'Sans fin' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $rule->est_actif ? 'badge-success' : 'badge-secondary' }}">
                                                {{ $rule->est_actif ? 'ACTIVE' : 'INACTIVE' }}
                                            </span>
                                            <div class="small text-muted mt-1">
                                                Créée par {{ $rule->created_by_agent ?: '—' }}
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="d-inline-flex flex-wrap justify-content-end" style="gap:6px;">
                                                <a href="{{ route('tresorerie.commissions.index', ['edit' => $rule->id]) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form method="POST" action="{{ route('tresorerie.commissions.toggle', $rule) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $rule->est_actif ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                                        <i class="fas {{ $rule->est_actif ? 'fa-pause' : 'fa-play' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Aucune règle de commission n'est encore configurée.
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

@push('styles')
<style>
    .commission-page {
        --commission-accent: #d48b00;
        --commission-accent-soft: rgba(212, 139, 0, 0.12);
        --commission-line: rgba(255, 255, 255, 0.08);
    }

    .commission-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1px solid var(--commission-line);
        background: linear-gradient(110deg, rgba(212, 139, 0, 0.18) 0%, rgba(9, 15, 25, 0.18) 55%, rgba(36, 86, 152, 0.18) 100%);
    }

    .commission-hero h4 {
        font-weight: 700;
        letter-spacing: .2px;
    }

    .commission-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .86rem;
        padding: 7px 12px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        background: rgba(0, 0, 0, 0.18);
    }

    .commission-kpis .small-box {
        border-radius: 10px;
        overflow: hidden;
    }

    .commission-form-card,
    .commission-list-card {
        border-radius: 12px;
        overflow: hidden;
    }

    .commission-form-card .card-header,
    .commission-list-card .card-header {
        border-bottom: 1px solid var(--commission-line);
    }

    .commission-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .85rem;
        text-transform: uppercase;
        letter-spacing: .7px;
        font-weight: 700;
        color: var(--commission-accent);
        margin-bottom: 10px;
    }

    .commission-form-card .form-control {
        border-radius: 8px;
    }

    .commission-form-card .form-group {
        margin-bottom: .9rem;
    }

    .commission-form-footer {
        border-top: 1px solid var(--commission-line);
        background: var(--commission-accent-soft);
    }

    .commission-table td,
    .commission-table th {
        vertical-align: middle;
    }

    .commission-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f4f6f9;
    }

    .commission-table .badge {
        font-size: .73rem;
    }

    .commission-table tbody tr:hover {
        background-color: rgba(255, 193, 7, 0.08);
    }

    @media (min-width: 1200px) {
        .commission-form-card {
            position: sticky;
            top: 78px;
        }
    }

    @media (max-width: 767.98px) {
        .commission-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .commission-table {
            min-width: 860px;
        }
    }
</style>
@endpush
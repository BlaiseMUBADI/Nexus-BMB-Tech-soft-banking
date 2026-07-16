@extends('layouts.app')

@section('page_title', 'Grille de commissions crédit')
@section('breadcrumb_parent', 'Crédits')
@section('breadcrumb', 'Commissions crédit')

@section('content')
<section class="content">
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="fas fa-percentage mr-2 text-warning"></i>Grille de commissions crédit</h5>
        <a href="{{ route('credit.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Retour aux crédits
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row mb-3">
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
        {{-- Formulaire --}}
        <div class="col-lg-4 mb-3">
            <div class="card card-warning card-outline shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle mr-2"></i>Nouvelle règle</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('credit.commissions.store') }}">
                        @csrf

                        <div class="form-group">
                            <label>Libellé <span class="text-danger">*</span></label>
                            <input type="text" name="libelle" class="form-control form-control-sm" required maxlength="200" placeholder="Ex: Commission standard USD">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Devise <span class="text-danger">*</span></label>
                                <select name="devise_code" class="form-control form-control-sm" required>
                                    <option value="CDF">CDF</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Type crédit <span class="text-danger">*</span></label>
                                <select name="type_credit" class="form-control form-control-sm" required>
                                    <option value="TOUS">Tous</option>
                                    <option value="INDIVIDUEL">Individuel</option>
                                    <option value="SOLIDAIRE">Solidaire</option>
                                    <option value="PME">PME</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Zone</label>
                                <select name="code_zone" class="form-control form-control-sm">
                                    <option value="">Toutes zones</option>
                                    @foreach($zones as $z)
                                        <option value="{{ $z->code_zone }}">{{ $z->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Portefeuille</label>
                                <select name="portefeuille_id" class="form-control form-control-sm">
                                    <option value="">Tous portefeuilles</option>
                                    @foreach($portefeuilles as $p)
                                        <option value="{{ $p->id }}">{{ $p->nom_portefeuille }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Montant min</label>
                                <input type="number" name="montant_min" class="form-control form-control-sm" step="0.01" min="0" placeholder="Optionnel">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Montant max</label>
                                <input type="number" name="montant_max" class="form-control form-control-sm" step="0.01" min="0" placeholder="Optionnel">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Mode de calcul <span class="text-danger">*</span></label>
                                <select name="mode_calcul" class="form-control form-control-sm" required>
                                    <option value="FIXE">Montant fixe</option>
                                    <option value="POURCENTAGE">Pourcentage (%)</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Valeur <span class="text-danger">*</span></label>
                                <input type="number" name="valeur" class="form-control form-control-sm" step="0.01" min="0" required placeholder="Montant ou %">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Priorité</label>
                                <input type="number" name="priorite" class="form-control form-control-sm" min="0" value="0">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control form-control-sm">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="est_actif" name="est_actif" value="1" checked>
                                <label class="custom-control-label" for="est_actif">Règle active</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Observations</label>
                            <textarea name="observations" class="form-control form-control-sm" rows="2" placeholder="Notes optionnelles"></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning btn-sm btn-block">
                            <i class="fas fa-save mr-1"></i>Enregistrer la règle
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Liste des règles --}}
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list mr-2"></i>Règles de commission ({{ $rules->total() }})</h6>
                    <form method="GET" action="{{ route('credit.commissions.index') }}" class="form-inline">
                        <select name="devise" class="form-control form-control-sm mr-1" onchange="this.form.submit()">
                            <option value="">Toutes devises</option>
                            <option value="CDF" {{ request('devise')=='CDF'?'selected':'' }}>CDF</option>
                            <option value="USD" {{ request('devise')=='USD'?'selected':'' }}>USD</option>
                            <option value="EUR" {{ request('devise')=='EUR'?'selected':'' }}>EUR</option>
                        </select>
                        <select name="type_credit" class="form-control form-control-sm mr-1" onchange="this.form.submit()">
                            <option value="">Tous types</option>
                            <option value="INDIVIDUEL" {{ request('type_credit')=='INDIVIDUEL'?'selected':'' }}>Individuel</option>
                            <option value="SOLIDAIRE" {{ request('type_credit')=='SOLIDAIRE'?'selected':'' }}>Solidaire</option>
                            <option value="PME" {{ request('type_credit')=='PME'?'selected':'' }}>PME</option>
                        </select>
                        <a href="{{ route('credit.commissions.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th>Devise</th>
                                    <th>Type</th>
                                    <th>Zone</th>
                                    <th>Portefeuille</th>
                                    <th class="text-right">Plage montant</th>
                                    <th class="text-center">Mode</th>
                                    <th class="text-right">Valeur</th>
                                    <th class="text-center">Priorité</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                <tr>
                                    <td>
                                        <strong>{{ $rule->libelle }}</strong>
                                        @if($rule->observations)
                                            <br><small class="text-muted">{{ Str::limit($rule->observations, 50) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-{{ $rule->devise_code === 'USD' ? 'success' : ($rule->devise_code === 'EUR' ? 'info' : 'warning') }}">{{ $rule->devise_code }}</span></td>
                                    <td>{{ $rule->type_credit }}</td>
                                    <td>{{ $rule->zone->nom ?? 'Toutes' }}</td>
                                    <td>{{ $rule->portefeuille->nom_portefeuille ?? 'Tous' }}</td>
                                    <td class="text-right small">
                                        @if($rule->montant_min || $rule->montant_max)
                                            {{ number_format($rule->montant_min ?? 0, 0, ',', ' ') }} - {{ number_format($rule->montant_max ?? 0, 0, ',', ' ') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $rule->mode_calcul === 'FIXE' ? 'primary' : 'info' }}">
                                            {{ $rule->mode_calcul === 'FIXE' ? 'Fixe' : '%' }}
                                        </span>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ number_format($rule->valeur, 2, ',', ' ') }}
                                        @if($rule->mode_calcul === 'POURCENTAGE')%@endif
                                    </td>
                                    <td class="text-center">{{ $rule->priorite }}</td>
                                    <td class="text-center">
                                        @if($rule->est_actif)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Actif</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i> Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('credit.commissions.destroy', $rule) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette règle ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                                        Aucune règle de commission configurée.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($rules->hasPages())
                <div class="card-footer">
                    {{ $rules->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
</section>
@endsection

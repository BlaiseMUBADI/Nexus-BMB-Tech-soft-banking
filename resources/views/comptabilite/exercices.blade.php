@extends('layouts.app')

@section('page_title', 'Exercices Comptables')
@section('breadcrumb_parent', 'Comptabilite')
@section('breadcrumb', 'Exercices Comptables')

@section('content')
<div class="container-fluid">

    <div class="alert alert-info py-2">
        <i class="fas fa-info-circle mr-2"></i>
        La clôture d'un exercice comptable nécessite <strong>deux validations distinctes</strong> (principe des 4 yeux) :
        1) le <strong>Comptable</strong> propose la clôture, 2) un <strong>Gérant/Directeur</strong> valide définitivement. Action irréversible une fois validée.
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><h6 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Liste des exercices</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Année</th><th>Période</th><th class="text-center">Statut</th>
                            <th class="text-right">Résultat net</th><th>Proposé par</th><th>Validé par</th>
                            <th class="text-center">Détails</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exercices as $ex)
                        <tr>
                            <td class="font-weight-bold">{{ $ex->annee }}</td>
                            <td>{{ $ex->date_debut->format('d/m/Y') }} → {{ $ex->date_fin->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if($ex->statut === 'OUVERT')
                                    <span class="badge badge-success">Ouvert</span>
                                @elseif($ex->statut === 'EN_ATTENTE_VALIDATION')
                                    <span class="badge badge-warning">En attente de validation</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i>Clôturé</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($ex->resultat_net_cloture !== null)
                                    <strong class="{{ $ex->resultat_net_cloture >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($ex->resultat_net_cloture, 2, ',', ' ') }}
                                    </strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="small">{{ $ex->propose_par_matricule ?? '-' }}</td>
                            <td class="small">{{ $ex->valide_par_matricule ?? '-' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-outline-info" data-toggle="modal" data-target="#modalDetails{{ $ex->id }}">
                                    <i class="fas fa-eye mr-1"></i>Voir
                                </button>
                            </td>
                            <td class="text-center">
                                @if($ex->statut === 'OUVERT')
                                    @if(in_array('EBEN-PER116', $userPermCodes ?? []))
                                        <button type="button" class="btn btn-sm btn-warning btn-ouvrir-proposer" data-id="{{ $ex->id }}" data-annee="{{ $ex->annee }}" data-debut="{{ $ex->date_debut->toDateString() }}" data-fin="{{ $ex->date_fin->toDateString() }}">
                                            <i class="fas fa-paper-plane mr-1"></i>Proposer la clôture
                                        </button>
                                    @else
                                        <span class="text-muted small">Réservé au Comptable</span>
                                    @endif
                                @elseif($ex->statut === 'EN_ATTENTE_VALIDATION')
                                    @if(in_array('EBEN-PER117', $userPermCodes ?? []))
                                        <button type="button" class="btn btn-sm btn-success btn-valider-cloture" data-id="{{ $ex->id }}" data-annee="{{ $ex->annee }}" data-fin="{{ $ex->date_fin->toDateString() }}">
                                            <i class="fas fa-check-double mr-1"></i>Valider
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-rejeter-cloture" data-id="{{ $ex->id }}" data-annee="{{ $ex->annee }}">
                                            <i class="fas fa-times mr-1"></i>Rejeter
                                        </button>
                                    @else
                                        <span class="text-muted small">En attente d'un Gérant/Directeur</span>
                                    @endif
                                @else
                                    <span class="text-muted small"><i class="fas fa-lock mr-1"></i>Verrouillé</span>
                                @endif
                            </td>
                        </tr>

                        {{-- ── Modal détails complets de l'exercice ────────────── --}}
                        <div class="modal fade" id="modalDetails{{ $ex->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title"><i class="fas fa-calendar-alt mr-2"></i>Exercice {{ $ex->annee }} — Détails complets</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-sm table-bordered mb-3">
                                            <tr>
                                                <th style="width:30%;">Période</th>
                                                <td>{{ $ex->date_debut->format('d/m/Y') }} → {{ $ex->date_fin->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Résultat net {{ $ex->statut === 'OUVERT' ? '(non figé, exercice en cours)' : '(figé à la clôture)' }}</th>
                                                <td>
                                                    @if($ex->resultat_net_cloture !== null)
                                                        <strong class="{{ $ex->resultat_net_cloture >= 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($ex->resultat_net_cloture, 2, ',', ' ') }} ({{ $ex->resultat_net_cloture >= 0 ? 'Bénéfice' : 'Perte' }})
                                                        </strong>
                                                    @else
                                                        <span class="text-muted">Pas encore calculé</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Proposé par (Comptable)</th>
                                                <td>
                                                    @if($ex->propose_par_matricule)
                                                        {{ $agents[$ex->propose_par_matricule]->nom ?? '' }} {{ $agents[$ex->propose_par_matricule]->prenom ?? $ex->propose_par_matricule }}
                                                        <br><small class="text-muted">le {{ $ex->propose_le?->format('d/m/Y à H:i') }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Validé par (Gérant/Directeur)</th>
                                                <td>
                                                    @if($ex->valide_par_matricule)
                                                        {{ $agents[$ex->valide_par_matricule]->nom ?? '' }} {{ $agents[$ex->valide_par_matricule]->prenom ?? $ex->valide_par_matricule }}
                                                        <br><small class="text-muted">le {{ $ex->valide_le?->format('d/m/Y à H:i') }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($ex->rejete_par_matricule)
                                            <tr>
                                                <th>Dernier rejet</th>
                                                <td>
                                                    {{ $agents[$ex->rejete_par_matricule]->nom ?? $ex->rejete_par_matricule }}
                                                    le {{ $ex->rejete_le?->format('d/m/Y à H:i') }}
                                                    @if($ex->observations)<br><em>Motif : {{ $ex->observations }}</em>@endif
                                                </td>
                                            </tr>
                                            @endif
                                        </table>

                                        <h6><i class="fas fa-exchange-alt mr-2 text-warning"></i>Report à nouveau (soldes reçus de l'exercice précédent)</h6>
                                        @if($ex->soldesOuverture->isEmpty())
                                            <p class="text-muted small">Aucun report à nouveau — {{ $ex->annee == $exercices->min('annee') ? "c'est le tout premier exercice du système." : "aucun solde d'ouverture enregistré pour cet exercice." }}</p>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped mb-0">
                                                    <thead class="thead-light"><tr><th>Compte</th><th>Libellé</th><th class="text-right">Solde d'ouverture</th></tr></thead>
                                                    <tbody>
                                                        @foreach($ex->soldesOuverture as $s)
                                                        <tr>
                                                            <td class="text-monospace">{{ $s->numero_compte }}</td>
                                                            <td>{{ $s->compte->libelle ?? '' }}</td>
                                                            <td class="text-right font-weight-bold {{ $s->solde_ouverture >= 0 ? 'text-primary' : 'text-secondary' }}">{{ number_format($s->solde_ouverture, 2, ',', ' ') }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ route('comptabilite.balance', ['exercice_id' => $ex->id]) }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-balance-scale mr-1"></i>Balance</a>
                                        <a href="{{ route('comptabilite.compte-resultat', ['exercice_id' => $ex->id]) }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-chart-line mr-1"></i>Compte de résultat</a>
                                        <a href="{{ route('comptabilite.bilan', ['exercice_id' => $ex->id]) }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-landmark mr-1"></i>Bilan</a>
                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Fermer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucun exercice comptable.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal "Proposer la clôture" — choix de la date de clôture effective --}}
<div class="modal fade" id="modalProposerCloture" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formProposerCloture" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-paper-plane mr-2"></i>Proposer la clôture de l'exercice <span id="anneeProposer"></span></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Le résultat net sera calculé et figé sur la période choisie ci-dessous.</p>
                    <div class="form-group">
                        <label>Date de clôture effective</label>
                        <input type="date" name="date_cloture_effective" id="dateClotureEffective" class="form-control" required>
                        <small class="text-muted" id="hintProposer"></small>
                    </div>
                    <div class="alert alert-info small py-2 mb-0" id="alerteAnticipee" style="display:none;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Clôture <strong>anticipée</strong> : la période de cet exercice sera réduite à cette date. Le nouvel exercice démarrera automatiquement le lendemain — aucun trou de couverture.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Proposer la clôture</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal validation avec saisie "CONFIRMER" (action irréversible) --}}
<div class="modal fade" id="modalValiderCloture" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formValiderCloture" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Clôture définitive — action irréversible</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Vous êtes sur le point de <strong>clôturer définitivement l'exercice <span id="anneeCloture"></span></strong>.</p>
                    <p class="text-danger">Cette action est <strong>irréversible</strong> : plus aucune écriture ne pourra être ajoutée à cet exercice.</p>
                    <div class="form-group">
                        <label>Le nouvel exercice démarrera le <strong id="debutNouvelExercice"></strong>. Choisissez sa date de fin :</label>
                        <input type="date" name="date_fin_nouvel_exercice" id="dateFinNouvelExercice" class="form-control" required>
                        <small class="text-muted">Par défaut : 1 an après le début. Modifiable pour un cycle mensuel, trimestriel, etc.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label>Tapez <strong>CONFIRMER</strong> pour valider :</label>
                        <input type="text" name="confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Clôturer définitivement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function () {
    // ── Ouvrir le modal "Proposer la clôture" avec la date du jour pré-remplie ──
    $('.btn-ouvrir-proposer').on('click', function () {
        const id = $(this).data('id'), annee = $(this).data('annee');
        const debut = $(this).data('debut'), fin = $(this).data('fin');
        const aujourdhui = new Date().toISOString().slice(0, 10);
        const dateParDefaut = aujourdhui < fin ? aujourdhui : fin;

        $('#anneeProposer').text(annee);
        $('#formProposerCloture').attr('action', '/comptabilite/exercices/' + id + '/proposer-cloture').data('fin', fin);
        $('#dateClotureEffective').attr('min', debut).attr('max', fin).val(dateParDefaut);
        $('#hintProposer').text('Période de l\'exercice : ' + debut + ' au ' + fin + '.');
        $('#alerteAnticipee').toggle(dateParDefaut < fin);
        $('#modalProposerCloture').modal('show');
    });

    $('#dateClotureEffective').on('change', function () {
        const fin = $('#formProposerCloture').data('fin');
        $('#alerteAnticipee').toggle($(this).val() < fin);
    });

    // ── Ouvrir le modal "Valider" avec le lendemain de la fin calculé automatiquement ──
    $('.btn-valider-cloture').on('click', function () {
        const id = $(this).data('id'), annee = $(this).data('annee'), fin = $(this).data('fin');
        const debutNouveau = new Date(fin);
        debutNouveau.setDate(debutNouveau.getDate() + 1);
        const debutNouveauStr = debutNouveau.toISOString().slice(0, 10);
        const finSuggeree = new Date(debutNouveau);
        finSuggeree.setFullYear(finSuggeree.getFullYear() + 1);
        finSuggeree.setDate(finSuggeree.getDate() - 1);

        $('#anneeCloture').text(annee);
        $('#debutNouvelExercice').text(debutNouveauStr.split('-').reverse().join('/'));
        $('#dateFinNouvelExercice').attr('min', debutNouveauStr).val(finSuggeree.toISOString().slice(0, 10));
        $('#formValiderCloture').attr('action', '/comptabilite/exercices/' + id + '/valider-cloture');
        $('#modalValiderCloture').modal('show');
    });

    $('.btn-rejeter-cloture').on('click', function () {
        const id = $(this).data('id'), annee = $(this).data('annee');
        const action = () => {
            const form = $('<form method="POST" action="/comptabilite/exercices/' + id + '/rejeter-cloture">')
                .append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            $('body').append(form);
            form.submit();
        };
        if (window.showUniversalConfirm) {
            showUniversalConfirm('Rejeter la proposition de clôture de l\'exercice ' + annee + ' ? Il repassera en statut OUVERT.', action,
                { title: 'Rejeter la clôture', btnLabel: 'Rejeter', btnClass: 'btn-danger', icon: 'fas fa-times' });
        } else if (confirm('Rejeter la proposition de clôture ?')) { action(); }
    });
});
</script>
@endpush

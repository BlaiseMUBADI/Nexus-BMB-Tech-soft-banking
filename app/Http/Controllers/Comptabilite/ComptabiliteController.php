<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Comptabilite\EcritureComptable;
use App\Models\Comptabilite\ExerciceComptable;
use App\Models\Comptabilite\JournalComptable;
use App\Models\Comptabilite\PlanComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComptabiliteController extends Controller
{
    /**
     * Résout les dates de filtrage : si un exercice est choisi, ses dates de début/fin
     * priment sur des dates libres (garantit la conformité au principe d'annualité).
     */
    private function resolveExercicePeriod(Request $request): array
    {
        if ($request->filled('exercice_id')) {
            $exercice = ExerciceComptable::find($request->exercice_id);
            if ($exercice) {
                return [$exercice->date_debut->toDateString(), $exercice->date_fin->toDateString(), $exercice];
            }
        }
        return [$request->input('date_debut'), $request->input('date_fin'), null];
    }
    public function dashboard()
    {
        $today = now()->toDateString();

        $journauxJour = JournalComptable::whereDate('date_ecriture', $today)->count();
        $ecrituresJour = EcritureComptable::whereHas('journal', function ($query) use ($today) {
            $query->whereDate('date_ecriture', $today);
        })->count();

        $totauxJour = EcritureComptable::whereHas('journal', function ($query) use ($today) {
            $query->whereDate('date_ecriture', $today);
        })->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
          ->first();

        $journalRecent = JournalComptable::with(['transaction', 'ecritures'])
            ->orderByDesc('date_ecriture')
            ->limit(20)
            ->get();

        return view('comptabilite.dashboard', [
            'stats' => [
                'journaux_jour' => $journauxJour,
                'ecritures_jour' => $ecrituresJour,
                'debit_jour' => (float) ($totauxJour->total_debit ?? 0),
                'credit_jour' => (float) ($totauxJour->total_credit ?? 0),
            ],
            'journalRecent' => $journalRecent,
        ]);
    }

    public function planComptable()
    {
        $accounts = PlanComptable::query()
            ->where('est_actif', true)
            ->orderBy('classe_ohada')
            ->orderByRaw('CHAR_LENGTH(numero_compte)')
            ->orderBy('numero_compte')
            ->get()
            ->map(function (PlanComptable $account) {
                $totaux = $account->ecritures()
                    ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
                    ->first();

                $account->total_debit = (float) ($totaux->total_debit ?? 0);
                $account->total_credit = (float) ($totaux->total_credit ?? 0);
                $account->solde = $account->total_debit - $account->total_credit;
                return $account;
            });

        return view('comptabilite.plan_comptable', [
            'accounts' => $accounts,
        ]);
    }

    // ── Requête réutilisable pour le Journal comptable (index + AJAX + print) ──
    private function buildJournalQuery(Request $request)
    {
        $query = JournalComptable::with(['transaction', 'ecritures'])
            ->orderByDesc('date_ecriture');

        if ($request->filled('date_debut')) {
            $query->whereDate('date_ecriture', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_ecriture', '<=', $request->date_fin);
        }
        if ($request->filled('type_piece')) {
            $query->where('type_piece', $request->type_piece);
        }
        if ($request->filled('reference')) {
            $reference = trim((string) $request->reference);
            $query->where(function ($sub) use ($reference) {
                $sub->where('reference_piece', 'like', '%' . $reference . '%')
                    ->orWhereHas('transaction', function ($trQuery) use ($reference) {
                        $trQuery->where('reference', 'like', '%' . $reference . '%');
                    });
            });
        }

        return $query;
    }

    public function journal(Request $request)
    {
        $journaux = $this->buildJournalQuery($request)->paginate(40)->withQueryString();

        $filters = [
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'type_piece' => $request->type_piece,
            'reference' => $request->reference,
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return view('comptabilite._journal_content', compact('journaux'))->render();
        }

        return view('comptabilite.journal', compact('journaux', 'filters'));
    }

    public function printJournal(Request $request)
    {
        ini_set('memory_limit', '768M');

        $query = $this->buildJournalQuery($request);
        $exportFormat = strtolower((string) $request->input('export_format', 'pdf'));
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        if ($exportFormat === 'csv') {
            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, ['Date', 'Journal', 'Référence pièce', 'Type', 'Libellé', 'Débit', 'Crédit', 'Statut'], ';');

                $query->chunk(200, function ($journaux) use ($handle) {
                    foreach ($journaux as $j) {
                        foreach ($j->ecritures as $e) {
                            fputcsv($handle, [
                                $j->date_ecriture?->format('d/m/Y H:i'),
                                $j->code_journal,
                                $j->reference_piece,
                                $j->type_piece,
                                $e->libelle_ligne ?: $j->libelle,
                                $e->debit,
                                $e->credit,
                                $j->statut,
                            ], ';');
                        }
                    }
                });
                fclose($handle);
            }, 'Journal_comptable_' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $journaux = $query->get();
        $filters = [
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'type_piece' => $request->type_piece,
            'reference' => $request->reference,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.comptabilite.journal', compact('journaux', 'filters'))
            ->setPaper('a4', 'landscape');

        return $outputMode === 'download'
            ? $pdf->download('Journal_comptable.pdf')
            : $pdf->stream('Journal_comptable.pdf');
    }

    // ── Grand livre ──
    private function buildGrandLivreData(Request $request): array
    {
        $numeroCompte = $request->input('numero_compte');

        $mouvements = collect();
        $resume = ['total_debit' => 0.0, 'total_credit' => 0.0, 'solde' => 0.0];

        if ($numeroCompte) {
            $mouvementQuery = EcritureComptable::with(['journal.transaction'])
                ->where('numero_compte', $numeroCompte)
                ->orderBy('created_at');

            if ($request->filled('date_debut')) {
                $mouvementQuery->whereHas('journal', function ($query) use ($request) {
                    $query->whereDate('date_ecriture', '>=', $request->date_debut);
                });
            }
            if ($request->filled('date_fin')) {
                $mouvementQuery->whereHas('journal', function ($query) use ($request) {
                    $query->whereDate('date_ecriture', '<=', $request->date_fin);
                });
            }

            $mouvements = $mouvementQuery->get();

            $totaux = $mouvements->reduce(function (array $carry, EcritureComptable $line) {
                $carry['total_debit'] += (float) $line->debit;
                $carry['total_credit'] += (float) $line->credit;
                return $carry;
            }, ['total_debit' => 0.0, 'total_credit' => 0.0]);

            $resume = [
                'total_debit' => round($totaux['total_debit'], 2),
                'total_credit' => round($totaux['total_credit'], 2),
                'solde' => round($totaux['total_debit'] - $totaux['total_credit'], 2),
            ];
        }

        return [$numeroCompte, $mouvements, $resume];
    }

    public function grandLivre(Request $request)
    {
        $comptes = PlanComptable::orderBy('numero_compte')->get(['numero_compte', 'libelle', 'type_compte']);
        [$numeroCompte, $mouvements, $resume] = $this->buildGrandLivreData($request);

        if ($request->ajax() || $request->wantsJson()) {
            $compte = $numeroCompte ? PlanComptable::find($numeroCompte) : null;
            return view('comptabilite._grand_livre_content', compact('mouvements', 'resume', 'numeroCompte', 'compte'))->render();
        }

        return view('comptabilite.grand_livre', [
            'comptes' => $comptes,
            'numeroCompte' => $numeroCompte,
            'mouvements' => $mouvements,
            'resume' => $resume,
            'dateDebut' => $request->date_debut,
            'dateFin' => $request->date_fin,
        ]);
    }

    public function printGrandLivre(Request $request)
    {
        ini_set('memory_limit', '768M');

        [$numeroCompte, $mouvements, $resume] = $this->buildGrandLivreData($request);
        $compte = $numeroCompte ? PlanComptable::find($numeroCompte) : null;
        $exportFormat = strtolower((string) $request->input('export_format', 'pdf'));
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        if ($exportFormat === 'csv') {
            return response()->streamDownload(function () use ($mouvements) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, ['Date', 'Référence', 'Libellé', 'Débit', 'Crédit'], ';');
                foreach ($mouvements as $m) {
                    fputcsv($handle, [
                        $m->journal?->date_ecriture?->format('d/m/Y'),
                        $m->journal?->reference_piece,
                        $m->libelle_ligne,
                        $m->debit,
                        $m->credit,
                    ], ';');
                }
                fclose($handle);
            }, 'Grand_livre_' . ($numeroCompte ?: 'compte') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.comptabilite.grand_livre', compact('mouvements', 'resume', 'numeroCompte', 'compte'))
            ->setPaper('a4', 'landscape');

        return $outputMode === 'download'
            ? $pdf->download('Grand_livre_' . ($numeroCompte ?: 'compte') . '.pdf')
            : $pdf->stream('Grand_livre_' . ($numeroCompte ?: 'compte') . '.pdf');
    }

    // ── Balance générale ──
    private function buildBalanceData(Request $request)
    {
        [$dateDebut, $dateFin] = $this->resolveExercicePeriod($request);

        $query = EcritureComptable::query();

        if ($dateDebut) {
            $query->whereHas('journal', fn ($q) => $q->whereDate('date_ecriture', '>=', $dateDebut));
        }
        if ($dateFin) {
            $query->whereHas('journal', fn ($q) => $q->whereDate('date_ecriture', '<=', $dateFin));
        }

        $rows = $query->selectRaw('numero_compte, SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->groupBy('numero_compte')
            ->get();

        $comptesById = PlanComptable::whereIn('numero_compte', $rows->pluck('numero_compte'))->get()->keyBy('numero_compte');

        return $rows->map(function ($row) use ($comptesById) {
            $compte = $comptesById->get($row->numero_compte);
            $row->libelle = $compte->libelle ?? '(compte inconnu)';
            $row->classe_ohada = $compte->classe_ohada ?? '';
            $row->total_debit = round((float) $row->total_debit, 2);
            $row->total_credit = round((float) $row->total_credit, 2);
            $row->solde = round($row->total_debit - $row->total_credit, 2);
            return $row;
        })->filter(fn ($r) => $r->total_debit != 0 || $r->total_credit != 0)
          ->sortBy('numero_compte')
          ->values();
    }

    public function balance(Request $request)
    {
        $balance = $this->buildBalanceData($request);
        $totaux = [
            'debit' => round($balance->sum('total_debit'), 2),
            'credit' => round($balance->sum('total_credit'), 2),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return view('comptabilite._balance_content', compact('balance', 'totaux'))->render();
        }

        $exercices = ExerciceComptable::orderByDesc('annee')->get();
        return view('comptabilite.balance', compact('balance', 'totaux', 'exercices'));
    }

    public function printBalance(Request $request)
    {
        ini_set('memory_limit', '768M');

        $balance = $this->buildBalanceData($request);
        $totaux = ['debit' => round($balance->sum('total_debit'), 2), 'credit' => round($balance->sum('total_credit'), 2)];
        $exportFormat = strtolower((string) $request->input('export_format', 'pdf'));
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        if ($exportFormat === 'csv') {
            return response()->streamDownload(function () use ($balance) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, ['Compte', 'Libellé', 'Débit', 'Crédit', 'Solde'], ';');
                foreach ($balance as $b) {
                    fputcsv($handle, [$b->numero_compte, $b->libelle, $b->total_debit, $b->total_credit, $b->solde], ';');
                }
                fclose($handle);
            }, 'Balance_generale_' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.comptabilite.balance', compact('balance', 'totaux'))->setPaper('a4', 'landscape');

        return $outputMode === 'download' ? $pdf->download('Balance_generale.pdf') : $pdf->stream('Balance_generale.pdf');
    }

    // ── Compte de résultat (Charges classe 6 vs Produits classe 7) ──
    private function buildCompteResultatData(Request $request)
    {
        [$dateDebut, $dateFin] = $this->resolveExercicePeriod($request);

        $comptesCharges = PlanComptable::where('classe_ohada', '6')->pluck('numero_compte');
        $comptesProduits = PlanComptable::where('classe_ohada', '7')->pluck('numero_compte');

        $queryBase = function ($comptes) use ($dateDebut, $dateFin) {
            $q = EcritureComptable::whereIn('numero_compte', $comptes);
            if ($dateDebut) {
                $q->whereHas('journal', fn ($sq) => $sq->whereDate('date_ecriture', '>=', $dateDebut));
            }
            if ($dateFin) {
                $q->whereHas('journal', fn ($sq) => $sq->whereDate('date_ecriture', '<=', $dateFin));
            }
            return $q->selectRaw('numero_compte, SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->groupBy('numero_compte')->get();
        };

        $comptesById = PlanComptable::whereIn('classe_ohada', ['6', '7'])->get()->keyBy('numero_compte');

        $charges = $queryBase($comptesCharges)->map(function ($row) use ($comptesById) {
            $row->libelle = $comptesById->get($row->numero_compte)->libelle ?? '';
            $row->montant = round((float) $row->total_debit - (float) $row->total_credit, 2);
            return $row;
        })->filter(fn ($r) => $r->montant != 0)->sortBy('numero_compte')->values();

        $produits = $queryBase($comptesProduits)->map(function ($row) use ($comptesById) {
            $row->libelle = $comptesById->get($row->numero_compte)->libelle ?? '';
            $row->montant = round((float) $row->total_credit - (float) $row->total_debit, 2);
            return $row;
        })->filter(fn ($r) => $r->montant != 0)->sortBy('numero_compte')->values();

        $totalCharges = round($charges->sum('montant'), 2);
        $totalProduits = round($produits->sum('montant'), 2);
        $resultatNet = round($totalProduits - $totalCharges, 2);

        return compact('charges', 'produits', 'totalCharges', 'totalProduits', 'resultatNet');
    }

    public function compteResultat(Request $request)
    {
        $data = $this->buildCompteResultatData($request);

        if ($request->ajax() || $request->wantsJson()) {
            return view('comptabilite._compte_resultat_content', $data)->render();
        }

        $exercices = ExerciceComptable::orderByDesc('annee')->get();
        return view('comptabilite.compte_resultat', $data + compact('exercices'));
    }

    public function printCompteResultat(Request $request)
    {
        ini_set('memory_limit', '768M');
        $data = $this->buildCompteResultatData($request);
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.comptabilite.compte_resultat', $data);

        return $outputMode === 'download' ? $pdf->download('Compte_resultat.pdf') : $pdf->stream('Compte_resultat.pdf');
    }

    // ── Bilan simplifié (classes 1-5, cumul depuis l'origine des écritures) ──
    private function buildBilanData(Request $request)
    {
        [, $dateFinExercice, $exerciceResolu] = $this->resolveExercicePeriod($request);
        $dateFin = $dateFinExercice ?: ($request->input('date_fin') ?: now()->toDateString());

        // Le résultat net affiché au Passif doit être celui du SEUL exercice sélectionné
        // (pas cumulé sur toute l'histoire) — sinon les résultats des années passées, déjà
        // intégrés aux capitaux propres lors de leur clôture, seraient comptés une 2e fois.
        $exercicePourResultat = $exerciceResolu ?: ExerciceComptable::pourDate($dateFin);
        $requestResultat = clone $request;
        if ($exercicePourResultat) {
            $requestResultat->merge([
                'date_debut' => $exercicePourResultat->date_debut->toDateString(),
                'date_fin' => min($exercicePourResultat->date_fin->toDateString(), $dateFin),
                'exercice_id' => null,
            ]);
        }

        $comptesBilan = PlanComptable::whereIn('classe_ohada', ['1', '2', '3', '4', '5'])
            ->where('est_mouvementable', true)
            ->pluck('numero_compte');

        $rows = EcritureComptable::whereIn('numero_compte', $comptesBilan)
            ->whereHas('journal', fn ($q) => $q->whereDate('date_ecriture', '<=', $dateFin))
            ->selectRaw('numero_compte, SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->groupBy('numero_compte')
            ->get();

        $comptesById = PlanComptable::whereIn('numero_compte', $rows->pluck('numero_compte'))->get()->keyBy('numero_compte');

        $actif = collect();
        $passif = collect();

        foreach ($rows as $row) {
            $compte = $comptesById->get($row->numero_compte);
            if (!$compte) {
                continue;
            }
            $solde = round((float) $row->total_debit - (float) $row->total_credit, 2);
            if ($solde == 0) {
                continue;
            }
            $item = (object) ['numero_compte' => $row->numero_compte, 'libelle' => $compte->libelle, 'montant' => abs($solde)];

            // Classification basée sur le type_compte réel (déjà correctement défini dans le plan
            // comptable), pas sur le seul numéro de classe : un compte "2511 Dépôts clients" est en
            // classe 2 mais reste un PASSIF (dette envers le client), pas un ACTIF.
            if ($compte->type_compte === 'PASSIF') {
                $passif->push($item);
            } elseif ($compte->type_compte === 'ACTIF') {
                $actif->push($item);
            } elseif ($solde > 0) {
                $actif->push($item);
            } else {
                $passif->push($item);
            }
        }

        // Résultat net de la période intégré au Passif (capitaux propres) pour équilibrer le bilan.
        // Un bénéfice AUGMENTE les capitaux propres (+), une perte les DIMINUE (-) : on garde le signe.
        $resultatData = $this->buildCompteResultatData($requestResultat);
        $passif->push((object) [
            'numero_compte' => '13',
            'libelle' => 'Résultat net de la période (' . ($resultatData['resultatNet'] >= 0 ? 'bénéfice' : 'perte') . ')',
            'montant' => $resultatData['resultatNet'],
        ]);

        $totalActif = round($actif->sum('montant'), 2);
        $totalPassif = round($passif->sum('montant'), 2);

        return [
            'actif' => $actif->sortBy('numero_compte')->values(),
            'passif' => $passif->sortBy('numero_compte')->values(),
            'totalActif' => $totalActif,
            'totalPassif' => $totalPassif,
            'resultatNet' => $resultatData['resultatNet'],
            'dateFin' => $dateFin,
        ];
    }

    public function bilan(Request $request)
    {
        $data = $this->buildBilanData($request);

        if ($request->ajax() || $request->wantsJson()) {
            return view('comptabilite._bilan_content', $data)->render();
        }

        $exercices = ExerciceComptable::orderByDesc('annee')->get();
        return view('comptabilite.bilan', $data + compact('exercices'));
    }

    public function printBilan(Request $request)
    {
        ini_set('memory_limit', '768M');
        $data = $this->buildBilanData($request);
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.comptabilite.bilan', $data)->setPaper('a4', 'landscape');

        return $outputMode === 'download' ? $pdf->download('Bilan.pdf') : $pdf->stream('Bilan.pdf');
    }
}

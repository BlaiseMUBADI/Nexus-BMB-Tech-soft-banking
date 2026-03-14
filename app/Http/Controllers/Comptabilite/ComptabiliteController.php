<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Comptabilite\EcritureComptable;
use App\Models\Comptabilite\JournalComptable;
use App\Models\Comptabilite\PlanComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComptabiliteController extends Controller
{
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

    public function journal(Request $request)
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

        $journaux = $query->paginate(40)->withQueryString();

        return view('comptabilite.journal', [
            'journaux' => $journaux,
            'filters' => [
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'type_piece' => $request->type_piece,
                'reference' => $request->reference,
            ],
        ]);
    }

    public function grandLivre(Request $request)
    {
        $numeroCompte = $request->input('numero_compte');

        $comptes = PlanComptable::orderBy('numero_compte')->get(['numero_compte', 'libelle', 'type_compte']);

        $mouvements = collect();
        $resume = [
            'total_debit' => 0.0,
            'total_credit' => 0.0,
            'solde' => 0.0,
        ];

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

        return view('comptabilite.grand_livre', [
            'comptes' => $comptes,
            'numeroCompte' => $numeroCompte,
            'mouvements' => $mouvements,
            'resume' => $resume,
            'dateDebut' => $request->date_debut,
            'dateFin' => $request->date_fin,
        ]);
    }
}

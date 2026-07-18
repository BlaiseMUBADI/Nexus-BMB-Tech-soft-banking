<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Credit\CreditAudit;
use App\Models\RH\Agent;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AuditLogController extends Controller
{
    /**
     * Journal d'activité — fusionne deux sources :
     *  - tb_credit_audits   (module Crédit, historique de workflow)
     *  - tb_activity_logs   (journal transversal : Caisse, Client, Compte...)
     */
    public function index(Request $request)
    {
        $module = $request->input('module'); // CREDIT, CAISSE, CLIENT, COMPTE...

        $entries = collect();

        // ── Source 1 : Crédit ──
        if (!$module || $module === 'CREDIT') {
            $q = CreditAudit::with(['demande', 'utilisateur']);
            if ($request->filled('acteur_matricule')) $q->where('acteur_matricule', $request->acteur_matricule);
            if ($request->filled('date_debut')) $q->whereDate('created_at', '>=', $request->date_debut);
            if ($request->filled('date_fin')) $q->whereDate('created_at', '<=', $request->date_fin);
            if ($request->filled('type_action')) $q->where('type_action', $request->type_action);
            if ($request->filled('dossier')) {
                $q->whereHas('demande', fn ($sub) => $sub->where('numero_dossier', 'LIKE', '%' . $request->dossier . '%'));
            }

            foreach ($q->orderByDesc('created_at')->limit(300)->get() as $log) {
                $entries->push([
                    'date' => $log->created_at,
                    'module' => 'CREDIT',
                    'label_action' => $log->labelAction(),
                    'reference' => $log->demande?->numero_dossier,
                    'reference_url' => $log->demande ? route('credit.show', $log->demande->id) : null,
                    'acteur' => $log->utilisateur ? $log->utilisateur->nom . ' ' . $log->utilisateur->prenom : ($log->acteur_matricule ?? '-'),
                    'description' => $log->details,
                    'ip' => $log->ip_address,
                ]);
            }
        }

        // ── Source 2 : Journal transversal (Caisse, Client, Compte...) ──
        if (!$module || $module !== 'CREDIT') {
            $q2 = ActivityLog::query();
            if ($module) $q2->where('module', $module);
            if ($request->filled('acteur_matricule')) $q2->where('acteur_matricule', $request->acteur_matricule);
            if ($request->filled('date_debut')) $q2->whereDate('created_at', '>=', $request->date_debut);
            if ($request->filled('date_fin')) $q2->whereDate('created_at', '<=', $request->date_fin);
            if ($request->filled('type_action')) $q2->where('type_action', $request->type_action);

            foreach ($q2->orderByDesc('created_at')->limit(300)->get() as $log) {
                $agent = $log->acteur_matricule ? Agent::where('matricule', $log->acteur_matricule)->first() : null;
                $entries->push([
                    'date' => $log->created_at,
                    'module' => $log->module,
                    'label_action' => $log->labelAction(),
                    'reference' => $log->reference,
                    'reference_url' => null,
                    'acteur' => $agent ? $agent->nom . ' ' . $agent->prenom : ($log->acteur_matricule ?? '-'),
                    'description' => $log->description,
                    'ip' => $log->ip_address,
                ]);
            }
        }

        $entries = $entries->sortByDesc('date')->values();

        // Pagination manuelle sur la collection fusionnée
        $perPage = 30;
        $page = max(1, (int) $request->input('page', 1));
        $slice = $entries->slice(($page - 1) * $perPage, $perPage)->values();

        $logs = new LengthAwarePaginator(
            $slice,
            $entries->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $typesAction = array_merge(CreditAudit::$labels, ActivityLog::$labels);
        $agents = Agent::orderBy('nom')->get(['matricule', 'nom', 'prenom']);
        $modules = ['CREDIT' => 'Crédit', 'CAISSE' => 'Caisse', 'CLIENT' => 'Client', 'COMPTE' => 'Compte'];

        return view('administration.journal_activite', compact('logs', 'typesAction', 'agents', 'modules'));
    }
}

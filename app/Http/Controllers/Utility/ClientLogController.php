<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientLogController extends Controller
{
    /**
     * Reçoit une erreur AJAX côté client et l'écrit dans storage/logs/laravel.log.
     * Le client ne connaît pas les détails serveur — il envoie ce qu'il a reçu.
     */
    public function store(Request $request)
    {
        $context = $request->input('context', 'AJAX');
        $status  = $request->input('status',  '?');
        $message = $request->input('message', 'Erreur inconnue');

        // Tronquer pour éviter les injections de logs volumineux
        $message = mb_substr($message, 0, 500);

        Log::warning("[Client AJAX] [{$context}] HTTP {$status} — {$message}", [
            'user'  => Auth::id() ?? 'anonyme',
            'url'   => $request->header('Referer', '—'),
            'ip'    => $request->ip(),
        ]);

        return response()->json(['logged' => true]);
    }
}

<?php

namespace App\Providers;

use App\Events\DepositOnRmbAccount;
use App\Listeners\ProcessAutomaticCreditRepayment;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * DÉSACTIVÉ SUR DEMANDE CLIENT : un dépôt sur le compte RMB doit rester un
     * simple dépôt, sans jamais appliquer automatiquement l'argent à une
     * échéance de crédit. Le règlement d'une échéance depuis le solde RMB ne
     * doit se faire QUE manuellement, via la page "Remboursement" (menu
     * Crédits → Remboursement, CreditController::remboursement()/
     * storeRemboursement(), ou le bouton ⚡ de l'onglet Échéancier).
     *
     * Le listener ProcessAutomaticCreditRepayment reste dans le code
     * (corrigé pour la prise en compte de la commission) mais n'est plus
     * branché sur l'événement DepositOnRmbAccount. Pour le réactiver,
     * décommenter le mapping ci-dessous.
     */
    protected $listen = [
        // DepositOnRmbAccount::class => [
        //     ProcessAutomaticCreditRepayment::class,
        // ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}

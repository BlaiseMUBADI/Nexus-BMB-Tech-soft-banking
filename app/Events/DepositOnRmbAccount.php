<?php

namespace App\Events;

use App\Models\Caisse\Transaction;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DepositOnRmbAccount
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $compte;
    public $montant;

    public function __construct(Transaction $transaction, $compte, float $montant)
    {
        $this->transaction = $transaction;
        $this->compte = $compte;
        $this->montant = $montant;
    }
}

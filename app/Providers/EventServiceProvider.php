<?php

namespace App\Providers;

use App\Events\DepositOnRmbAccount;
use App\Listeners\ProcessAutomaticCreditRepayment;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DepositOnRmbAccount::class => [
            ProcessAutomaticCreditRepayment::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}

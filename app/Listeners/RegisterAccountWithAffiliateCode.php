<?php

namespace App\Listeners;

use App\Events\AffiliateProgramChecking;
use App\Models\Affiliate;
use AWS\CRT\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RegisterAccountWithAffiliateCode
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\AffiliateProgramChecking  $event
     * @return void
     */
    public function handle(AffiliateProgramChecking $event)
    {
        if (!empty($event->code)) {
            //TODO : Chỗ này mai sau làm cộng coin
        }
    }
}

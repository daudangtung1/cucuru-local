<?php

namespace App\Services;

use App\Models\PaymentHistory;
use App\Models\User;

class PaymentHistoryService extends BaseService
{
    public function getById($id, $strict = true)
    {
        return $strict ? PaymentHistory::find($id) : PaymentHistory::findOrFail($id);
    }


    public function createByUser(User $user, $plan_id, $status, $stripe_payment_id = null)
    {
        $user->paymentHistories()->create([
            'plan_id' => $plan_id,
            'status' => $status,
            'stripe_payment_id' => $stripe_payment_id
        ]);
    }

    public function createSuccessByUser(User $user, $plan_id, $stripe_payment_id = null)
    {
        $this->createByUser($user, $plan_id, PaymentHistory::SUCCESS, $stripe_payment_id);
    }

    public function createFalseByUser(User $user, $plan_id, $stripe_payment_id = null)
    {
        $this->createByUser($user, $plan_id, PaymentHistory::FALSE, $stripe_payment_id);
    }
}

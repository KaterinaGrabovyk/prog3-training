<?php

namespace App\Services;

class StripePayment implements PaymentGatewayInterface
{
    public function charge(array $customer,float $amount,float $tax):bool {
        return true;
    }
}

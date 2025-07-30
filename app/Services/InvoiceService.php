<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\SalesTaxService;
use App\Services\EmailService;

class InvoiceService{
    public function __construct(
        protected SalesTaxService $salesTaxServise,
        protected PaymentGatewayInterface $paymentGateway,
        protected EmailService $emailServise,
        )
    {

    }
    public function process(array $customer, float $amount):bool{
    
     //* 1. calculate sales tax
     $tax=$this->salesTaxServise->calculate($amount,$customer); 

     //* 2 process invoice
     if(! $this->paymentGateway->charge($customer,$amount,$tax)){
        return false;
     }

     //* 3 send receipt
     $this->emailServise->send($customer,'receipt');
     echo 'Invoice has been processed <br/>';
     return true;
    }
}
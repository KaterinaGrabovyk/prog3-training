<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\SalesTaxService;
use App\Services\PaymentGatewayService;
use App\Services\EmailService;

class InvoiceService{
    public function __construct(
        protected SalesTaxService $salesTaxServise,
        protected PaymentGatewayService $gatewayServise,
        protected EmailService $emailServise,
        )
    {

    }
    public function process(array $customer, float $amount):bool{
    //  $salesTaxServise=new SalesTaxService();
    //  $gatewayService=new PaymentGatewayService();   
    //  $emailService= new EmailService();
    

     //* 1. calculate sales tax
     $tax=$this->salesTaxServise->calculate($amount,$customer); 

     //* 2 process invoice
     if(! $this->gatewayServise->charge($customer,$amount,$tax)){
        return false;
     }

     //* 3 send receipt
     $this->emailServise->send($customer,'receipt');
     echo 'Invoice has been processed <br/>';
     return true;
    }
}
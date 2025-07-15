<?php

declare(strict_types=1);

namespace Tests\DataProviders;

class RouterDataProvider{
     public function RNFCases() : array {
        return [
            ['/users','put'],
            ['/invoices','post'],
            ['/users','get'],
            ['/users','post']
        ];
    }
}
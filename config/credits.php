<?php

return [
    // Definição dos pacotes de créditos disponíveis para venda.
    // A chave (package_id) deve ser enviada pelo formulário de checkout/compra.
    'packages' => [
        '5_credits' => [
            'amount'   => 5,
            'price'    => 19.90, // em BRL
            'discount' => 0,
            'recommended' => false,
        ],
        '10_credits' => [
            'amount'   => 10,
            'price'    => 34.90,
            'discount' => 12,
            'recommended' => true,
        ],
        '20_credits' => [
            'amount'   => 20,
            'price'    => 59.90,
            'discount' => 25,
            'recommended' => false,
        ],
        '50_credits' => [
            'amount'   => 50,
            'price'    => 129.90,
            'discount' => 35,
            'recommended' => false,
        ],
    ],
]; 
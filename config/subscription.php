<?php

return [
    'pricing' => [
        // Preço base mensal
        'base_monthly' => 150.00,
        // Descontos progressivos por quantidade de meses (0.05 = 5%)
        'discounts' => [
            3 => 0.05,   // 3+ meses: 5%
            6 => 0.10,   // 6+ meses: 10%
            12 => 0.15,  // 12+ meses: 15%
            18 => 0.20,  // 18+ meses: 20%
            24 => 0.25,  // 24 meses: 25%
        ],
    ],
    'plans' => [
        [ 'months' => 1 ],
        [ 'months' => 3 ],
        [ 'months' => 6 ],
        [ 'months' => 12 ],
        [ 'months' => 24 ],
    ],
];



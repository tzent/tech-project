<?php

return [
    'commission' => [
        'cash_in' => [
            'natural' => [
                'fee' => 0.03,
                'min' => 0,
                'max' => 5.0
            ],
            'legal' => [
                'fee' => 0.03,
                'min' => 0,
                'max' => 5.0
            ],
        ],
        'cash_out' => [
            'natural' => [
                'fee' => 0.3,
                'min' => 0,
                'max' => 0
            ],
            'legal' => [
                'fee' => 0.3,
                'min' => 0.5,
                'max' => 0
            ],
        ],
    ],
    'currencies' => [
        'default' => 'EUR',
        'rates' => [
            'EUR' => 1,
            'USD' => 1.1497,
            'JPY' => 129.53,
        ]
    ]
];

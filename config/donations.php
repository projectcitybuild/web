<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Target Funding
    |--------------------------------------------------------------------------
    |
    | The total annual cost of all our services
    |
    */
    'target_funding' => 1350,

    /*
    |--------------------------------------------------------------------------
    | Stripe Price IDs
    |--------------------------------------------------------------------------
    |
    | Price IDs for each donation tier and payment type
    |
    */
    'price_ids' => [
        'copper' => [
            'one_off' => env(key: 'STRIPE_PRICE_ID_COPPER_ONEOFF', default: 'price_1JJL5mAtUyfM4v5ISwJrrVur'),
            'subscription' => env(key: 'STRIPE_PRICE_ID_COPPER_SUBSCRIPTION', default: 'price_1JJL5mAtUyfM4v5IJNHp1Tk2'),
        ],
        'iron' => [
            'one_off' => env(key: 'STRIPE_PRICE_ID_IRON_ONEOFF', default: 'price_1JJL63AtUyfM4v5IoVomtPRZ'),
            'subscription' => env(key: 'STRIPE_PRICE_ID_IRON_SUBSCRIPTION', default: 'price_1JJL63AtUyfM4v5ILyrs2uxw'),
        ],
        'diamond' => [
            'one_off' => env(key: 'STRIPE_PRICE_ID_DIAMOND_ONEOFF', default: 'price_1JJL6RAtUyfM4v5IP77eRPER'),
            'subscription' => env(key: 'STRIPE_PRICE_ID_DIAMOND_SUBSCRIPTION', default: 'price_1JJL6RAtUyfM4v5Ih3kg7UDM'),
        ],
    ],
];

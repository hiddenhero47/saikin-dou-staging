<?php

return [
    'status'=>[
        'pending'  => 'pending',
        'canceled' => 'canceled',
        'accepted' => 'accepted',
        'active'   => 'active',
        'inactive' => 'inactive',
        'approved' => 'approved',
        'rejected' => 'rejected',
        'completed' => 'completed',
        'initiated' => 'initiated',
        'refunded' => 'refunded',
        'assigned' => 'assigned',
        'unassigned' => 'unassigned',
        'delivered' => 'delivered',
        'closed' => 'closed',
        'unshipped' => 'unshipped',
        'shipped' => 'shipped',
        'available' => 'available',
        'unavailable' => 'unavailable',
        'queued' => 'queued',
    ],
    'mail'=>[
        'registration'=>'registration',
        'verification'=>'verification',
        'reset'=>'reset',
        'info'=>'info',
        'invite'=>'invite',
        'custom'=>'custom'
    ],
    'socialite'=>[
        'facebook'=>'facebook',
        'linkedin'=>'linkedin',
        'google'=>'google',
        'apple'=>'apple',
    ],
    'device'=>[
        'android'=>'android',
        'ios'=>'ios',
        'web'=>'web'
    ],
    'notification'=>[
        'type'=>[
            'direct'=>'direct',
            'subscription'=>'subscription'
        ],
        'agent'=>[
            'firebase'=>'firebase',
            'expo'=>'expo'
        ]
    ],
    'visibility'=>[
        'private'=>'private',
        'public'=>'public',
        'protected'=>'protected'
    ],
    'payment'=>[
        'provider'=>[
            'paystack'=>'paystack',
            'flutterwave'=>'flutterwave',
        ],
        'type'=>[
            'standard'=>'standard',
            'collect'=>'collect',
        ],
        'status'=>[
            'pending' => 'pending',
            'success' => 'success',
            'failure' => 'failure',
        ]
    ],
    'browser'=>[
        'status'=>[
            'open'=>'open',
            'idle'=>'idle',
            'closed'=>'closed'
        ]
    ]
];
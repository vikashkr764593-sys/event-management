<?php

$baseUrl = 'https://event-management-44g1.onrender.com/api';

$collection = [
    'info' => [
        'name' => 'Event Management API',
        'description' => 'Complete API collection for Event & Instrument Management System',
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
    ],
    'variable' => [
        [
            'key' => 'base_url',
            'value' => $baseUrl,
            'type' => 'string'
        ],
        [
            'key' => 'token',
            'value' => 'YOUR_AUTH_TOKEN_HERE',
            'type' => 'string'
        ]
    ],
    'item' => []
];

// Helper to create requests
function createRequest($name, $method, $urlPath, $body = null, $auth = true) {
    $req = [
        'name' => $name,
        'request' => [
            'method' => $method,
            'header' => [
                ['key' => 'Accept', 'value' => 'application/json', 'type' => 'text']
            ],
            'url' => [
                'raw' => '{{base_url}}' . $urlPath,
                'host' => ['{{base_url}}'],
                'path' => explode('/', ltrim($urlPath, '/'))
            ]
        ]
    ];

    if ($auth) {
        $req['request']['auth'] = [
            'type' => 'bearer',
            'bearer' => [
                ['key' => 'token', 'value' => '{{token}}', 'type' => 'string']
            ]
        ];
    }

    if ($body) {
        $req['request']['body'] = [
            'mode' => 'raw',
            'raw' => json_encode($body, JSON_PRETTY_PRINT),
            'options' => ['raw' => ['language' => 'json']]
        ];
    }

    return $req;
}

// 1. Auth Folder
$authItems = [
    createRequest('Login', 'POST', '/auth/login', ['email' => 'admin@example.com', 'password' => 'password'], false),
    createRequest('Send OTP', 'POST', '/auth/send-otp', ['phone' => '1234567890'], false),
    createRequest('Verify OTP', 'POST', '/auth/verify-otp', ['phone' => '1234567890', 'otp' => '123456'], false),
    createRequest('Register Initiate', 'POST', '/auth/register/initiate', ['name' => 'Test User', 'email' => 'test@example.com', 'phone' => '1234567890', 'password' => 'password123'], false),
    createRequest('Register Webhook', 'POST', '/auth/register/webhook', ['event' => 'order.paid', 'payload' => ['order' => ['entity' => ['id' => 'order_xyz']]]], false),
    createRequest('Get Current User (Me)', 'GET', '/auth/me'),
    createRequest('Logout', 'POST', '/auth/logout')
];
$collection['item'][] = ['name' => '1. Authentication', 'item' => $authItems];

// 2. Users Folder
$userItems = [
    createRequest('List Users', 'GET', '/users'),
    createRequest('Create User', 'POST', '/users', ['name' => 'New User', 'email' => 'new@test.com', 'password' => 'password123', 'role' => 'user']),
    createRequest('Get User', 'GET', '/users/1'),
    createRequest('Update User', 'PUT', '/users/1', ['name' => 'Updated Name']),
    createRequest('Delete User', 'DELETE', '/users/1')
];
$collection['item'][] = ['name' => '2. Users Admin', 'item' => $userItems];

// 3. Singers Folder
$singerItems = [
    createRequest('List Singers', 'GET', '/singers', null, false),
    createRequest('Create Singer', 'POST', '/singers', ['name' => 'Arijit Singh', 'genre' => 'Bollywood', 'hourly_rate' => 50000, 'bio' => 'Famous singer']),
    createRequest('Get Singer', 'GET', '/singers/1', null, false),
    createRequest('Update Singer', 'PUT', '/singers/1', ['hourly_rate' => 60000]),
    createRequest('Delete Singer', 'DELETE', '/singers/1'),
    createRequest('Get Availability', 'GET', '/singers/1/availability', null, false),
    createRequest('Store Availability', 'POST', '/singers/1/availability', ['date' => '2026-05-15', 'start_time' => '10:00', 'end_time' => '14:00', 'status' => 'available']),
    createRequest('Update Availability', 'PUT', '/singers/1/availability', ['status' => 'booked'])
];
$collection['item'][] = ['name' => '3. Singers', 'item' => $singerItems];

// 4. Instruments Folder
$instrumentItems = [
    createRequest('List Instruments', 'GET', '/instruments', null, false),
    createRequest('Create Instrument', 'POST', '/instruments', ['name' => 'Fender Stratocaster', 'type' => 'Electric Guitar', 'price_per_day' => 1500, 'quantity' => 5, 'description' => 'Classic guitar']),
    createRequest('Get Instrument', 'GET', '/instruments/1', null, false),
    createRequest('Update Instrument', 'PUT', '/instruments/1', ['quantity' => 4]),
    createRequest('Delete Instrument', 'DELETE', '/instruments/1')
];
$collection['item'][] = ['name' => '4. Instruments', 'item' => $instrumentItems];

// 5. Bookings Folder
$bookingItems = [
    createRequest('My Bookings', 'GET', '/bookings/my'),
    createRequest('All Bookings (Admin)', 'GET', '/bookings'),
    createRequest('Create Booking', 'POST', '/bookings', ['singer_id' => 1, 'booking_date' => '2026-06-01', 'start_time' => '18:00', 'end_time' => '22:00', 'venue_address' => 'Taj Hotel, Mumbai', 'total_amount' => 200000]),
    createRequest('Get Booking Details', 'GET', '/bookings/1'),
    createRequest('Update Booking', 'PUT', '/bookings/1', ['venue_address' => 'New Venue']),
    createRequest('Delete Booking', 'DELETE', '/bookings/1'),
    createRequest('Approve Booking', 'POST', '/bookings/1/approve'),
    createRequest('Reject Booking', 'POST', '/bookings/1/reject')
];
$collection['item'][] = ['name' => '5. Singer Bookings', 'item' => $bookingItems];

// 6. Cart Folder
$cartItems = [
    createRequest('Get My Cart', 'GET', '/cart'),
    createRequest('Add to Cart', 'POST', '/cart', ['instrument_id' => 1, 'quantity' => 1, 'rental_days' => 3]),
    createRequest('Update Cart Item', 'PUT', '/cart/1', ['quantity' => 2]),
    createRequest('Remove from Cart', 'DELETE', '/cart/1')
];
$collection['item'][] = ['name' => '6. Instrument Cart', 'item' => $cartItems];

// 7. Orders Folder
$orderItems = [
    createRequest('My Orders', 'GET', '/orders/my'),
    createRequest('All Orders (Admin)', 'GET', '/orders'),
    createRequest('Checkout (Create Order)', 'POST', '/orders', ['shipping_address' => '123 Main St, City', 'payment_method' => 'razorpay']),
    createRequest('Get Order Details', 'GET', '/orders/1'),
    createRequest('Verify Payment', 'POST', '/orders/verify-payment', ['razorpay_order_id' => 'order_xxx', 'razorpay_payment_id' => 'pay_xxx', 'razorpay_signature' => 'sig_xxx']),
    createRequest('Approve Order', 'POST', '/orders/1/approve'),
    createRequest('Reject Order', 'POST', '/orders/1/reject'),
    createRequest('Update Order Status', 'PUT', '/orders/1/status', ['status' => 'shipped'])
];
$collection['item'][] = ['name' => '7. Instrument Orders', 'item' => $orderItems];

// 8. Chat Folder
$chatItems = [
    createRequest('Get Chat by Booking', 'GET', '/chat/1'),
    createRequest('Send Message', 'POST', '/chat/send', ['booking_id' => 1, 'message' => 'Hi, looking forward to the event!'])
];
$collection['item'][] = ['name' => '8. Chat System', 'item' => $chatItems];

// 9. Notifications Folder
$notificationItems = [
    createRequest('Get Notifications', 'GET', '/notifications'),
    createRequest('Mark as Read', 'PUT', '/notifications/1/read')
];
$collection['item'][] = ['name' => '9. Notifications', 'item' => $notificationItems];

// 10. Admin Reports
$reportItems = [
    createRequest('Bookings Report', 'GET', '/reports/bookings'),
    createRequest('Inventory Report', 'GET', '/reports/inventory'),
    createRequest('Orders Report', 'GET', '/reports/orders')
];
$collection['item'][] = ['name' => '10. Admin Reports', 'item' => $reportItems];

file_put_contents('postman_collection.json', json_encode($collection, JSON_PRETTY_PRINT));
echo "Postman collection generated successfully!\n";

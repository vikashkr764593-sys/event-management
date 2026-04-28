<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SidebarController extends Controller
{
    public function getMenuData()
    {
        $menuGroups = [
            [
                'title' => 'Main',
                'items' => [
                    [
                        'icon' => 'grid-icon',
                        'name' => 'Dashboard',
                        'path' => '/',
                    ],
                ]
            ],
            [
                'title' => 'Admin Management',
                'items' => [
                    [
                        'icon' => 'user-circle-icon',
                        'name' => 'Users',
                        'path' => '/admin/users',
                    ],
                    [
                        'icon' => 'bot-icon',
                        'name' => 'Singers',
                        'path' => '/admin/singers',
                    ],
                    [
                        'icon' => 'box-cube-icon',
                        'name' => 'Instruments',
                        'path' => '/admin/instruments',
                    ],
                    [
                        'icon' => 'calendar-icon',
                        'name' => 'Bookings',
                        'path' => '/admin/bookings',
                    ],
                    [
                        'icon' => 'cart-icon',
                        'name' => 'Orders',
                        'path' => '/admin/orders',
                    ]
                ]
            ]
        ];

        return view('components.sidebar', compact('menuGroups'));
    }
}

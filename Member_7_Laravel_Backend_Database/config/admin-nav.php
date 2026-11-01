<?php

return [
    [
        'label' => 'Overview',
        'items' => [
            [
                'label' => 'Dashboard',
                'icon' => 'bi-grid-1x2-fill',
                'route' => 'admin.dashboard',
                'pattern' => 'admin.dashboard',
            ],
        ],
    ],
    [
        'label' => 'Sales & Customers',
        'items' => [
            [
                'label' => 'Orders',
                'icon' => 'bi-bag-check',
                'route' => 'admin.orders.index',
                'pattern' => 'admin.orders.*',
            ],
            [
                'label' => 'Customers',
                'icon' => 'bi-people',
                'route' => 'admin.users.index',
                'pattern' => 'admin.users.*',
            ],
        ],
    ],
    [
        'label' => 'Catalog',
        'items' => [
            [
                'label' => 'Products',
                'icon' => 'bi-box-seam',
                'route' => 'admin.products.index',
                'pattern' => 'admin.products.*',
            ],
            [
                'label' => 'Categories',
                'icon' => 'bi-tags',
                'route' => 'admin.categories.index',
                'pattern' => 'admin.categories.*',
            ],
            [
                'label' => 'Inventory',
                'icon' => 'bi-boxes',
                'route' => 'admin.inventory.index',
                'pattern' => 'admin.inventory.*',
            ],
        ],
    ],
    [
        'label' => 'Content',
        'items' => [
            [
                'label' => 'Homepage / Hero',
                'icon' => 'bi-image',
                'route' => 'admin.hero.edit',
                'pattern' => 'admin.hero.*',
            ],
        ],
    ],
    [
        'label' => 'Communication',
        'items' => [
            [
                'label' => 'Messages',
                'icon' => 'bi-chat-left-text',
                'route' => 'admin.messages.index',
                'pattern' => 'admin.messages.*',
            ],
            [
                'label' => 'Newsletter',
                'icon' => 'bi-envelope-paper',
                'route' => 'admin.newsletter.index',
                'pattern' => 'admin.newsletter.*',
            ],
        ],
    ],
];

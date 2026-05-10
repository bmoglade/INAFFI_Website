<?php
// ============================================================
// inaffi.com — Homepage Static Fallback Data
// ============================================================
// Used ONLY when no featured outfit is set in the database.
// Once an admin features an outfit via the dashboard,
// this file is never loaded.
//
// To update the fallback:
//   1. Replace images in assets/images/landing/
//   2. Edit the mockup data below
//
// Image specs:
//   Outfit image:  800×1000px JPG — assets/images/landing/outfit.jpg
//   Product imgs:  400×400px JPG  — assets/images/landing/product-1.jpg etc.
// ============================================================

$mockup_outfit = [
    'id'           => 0,
    'title'        => 'Summer Festive Look',
    'category'     => 'Festive',
    'image'        => 'assets/images/landing/outfit.jpg',
    'is_published' => 1,
    'is_featured'  => 1,
    'username'     => '',   // no storefront link for fallback
    'display_name' => '',
];

$mockup_products = [
    [
        'id'           => 0,
        'name'         => 'Floral Anarkali Kurta',
        'platform'     => 'Myntra',
        'affiliate_url'=> '#',
        'image'        => 'assets/images/landing/product-1.jpg',
        'in_stock'     => 1,
        'display_order'=> 0,
    ],
    [
        'id'           => 0,
        'name'         => 'Embroidered Dupatta',
        'platform'     => 'Amazon',
        'affiliate_url'=> '#',
        'image'        => 'assets/images/landing/product-2.jpg',
        'in_stock'     => 1,
        'display_order'=> 1,
    ],
    [
        'id'           => 0,
        'name'         => 'Kolhapuri Block Heels',
        'platform'     => 'Nykaa',
        'affiliate_url'=> '#',
        'image'        => 'assets/images/landing/product-3.jpg',
        'in_stock'     => 1,
        'display_order'=> 2,
    ],
    [
        'id'           => 0,
        'name'         => 'Oxidised Silver Jhumkas',
        'platform'     => 'Flipkart',
        'affiliate_url'=> '#',
        'image'        => 'assets/images/landing/product-4.jpg',
        'in_stock'     => 1,
        'display_order'=> 3,
    ],
    [
        'id'           => 0,
        'name'         => 'Potli Clutch Bag',
        'platform'     => 'Ajio',
        'affiliate_url'=> '#',
        'image'        => 'assets/images/landing/product-5.jpg',
        'in_stock'     => 1,
        'display_order'=> 4,
    ],
];

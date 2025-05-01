<?php
class Shop_Taxonomy {
    public function __construct() {
        add_action('init', [$this, 'register_taxonomies']);
    }

    public function register_taxonomies() {
        register_taxonomy('shop_category', 'shop', [
            'labels' => ['name' => 'Shop Categories'],
            'hierarchical' => true,
            'public' => true,
        ]);

        register_taxonomy('shop_city', 'shop', [
            'labels' => ['name' => 'Shop Cities'],
            'hierarchical' => false,
            'public' => true,
        ]);
    }
}
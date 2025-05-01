<?php
class Shop_CPT {
    public function __construct() {
        add_action('init', [$this, 'register_shop_cpt']);
    }

    public function register_shop_cpt() {
        register_post_type('shop', [
            'labels' => ['name' => 'Shops'],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'shop'],
            'supports' => ['title', 'editor'],
            'show_in_rest' => true
        ]);
    }
}
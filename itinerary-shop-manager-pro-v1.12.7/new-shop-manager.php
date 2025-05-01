<?php
/**
 * Plugin Name: new Shop Manager
 * Description: Register shops with gallery, maps, services, offers, etc.
 * Version: 1.10.2
 * Author: NMPCorp
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-shop-cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-shop-taxonomy.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-shop-form.php';

new Shop_CPT();
new Shop_Taxonomy();
new Shop_Form();

// Load custom template for single shop
add_filter('template_include', function ($template) {
    if (is_singular('shop')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-shop.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
});

// Enqueue styles and scripts
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('shop-plugin-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('shop-plugin-js', plugin_dir_url(__FILE__) . 'assets/js/form.js', ['jquery'], null, true);
});
?>


add_action('admin_menu', function () {
    add_menu_page('Edit Shop', 'Edit Shop', 'edit_posts', 'edit-shop-admin', function () {
        echo '<div class="wrap"><h1>Edit Shop</h1>';
        ob_start(); echo do_shortcode('[edit_shop_form]'); $content = ob_get_clean(); echo $content;
        echo '</div>';
    });
});
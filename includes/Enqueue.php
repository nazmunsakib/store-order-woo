<?php
/**
 * Plugin Enqueue Assets
 *
 * Handles the registration and enqueuing of both frontend and admin assets 
 *
 * @package StoreOrderWoo
 * @version 1.0.0
 */

namespace StoreOrderWoo;

defined('ABSPATH') || die();

/**
 * Class Enqueue
 *
 * Manages the enqueueing of CSS and JS files for the admin and frontend.
 */
class Enqueue {

    /**
     * Constructor.
     *
     * Initializes the class and hooks into WordPress to enqueue admin and frontend assets.
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_assets'), 100);
    }

    /**
     * Enqueue Admin Assets.
     *
     * Registers and enqueues styles and scripts for the admin dashboard.
     * Styles and scripts are specific to the Product FAQ WooCommerce plugin’s admin interface.
     *
     * @return void
     */
    public function admin_assets($admin_page ) {

    }

    /**
     * Enqueue Frontend Assets.
     *
     * Registers and enqueues styles and scripts for the frontend of the site.
     * Ensures Product FAQ WooCommerce plugin assets are available on product pages.
     *
     * @return void
     */
    public function frontend_assets() {
        // Register frontend CSS file.
        wp_enqueue_style(
            'fsc-app',
            SOW_ASSETS . '/css/app.css',
            null,
            SOW_VERSION
        );

        wp_enqueue_script(
            'fsc-app',
            SOW_ASSETS . '/js/app.js',
            ['jquery'],
            SOW_VERSION
        );
    }
}

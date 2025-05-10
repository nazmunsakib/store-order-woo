<?php
/**
 * Plugin Name: Store Order for WooCommerce
 * Plugin URI: https://nazmunsakib.com/
 * Description: Store Order for WooCommerce - Sends order data to Hub immediately after an order is placed
 * Version: 1.0.0
 * Author: Nazmun Sakib
 * Author URI: https://nazmunsakib.com
 * License: GPL2
 * Text Domain: store-order-woo
 * Domain Path: /languages
 * 
 * WP Requirement & Test
 * Requires at least: 4.4
 * Tested up to: 6.5
 * Requires PHP: 5.6
 * 
 * WC Requirement & Test
 * WC requires at least: 3.2
 * WC tested up to: 7.9
 * 
 *  @package StoreOrderWoo
 */

defined('ABSPATH') || die();

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Main class for Product FAQs Manager.
 */
final class Store_Order_Woo {

    /**
     * The single instance of the class.
     *
     * @var Store_Order_Woo|null
     */
    private static $instance = null;

    /**
     * Plugin version.
     *
     * @var string
     */
    private static $version = '1.0.1';

    /**
     * Constructor.
     *
     * Initializes the class and hooks necessary actions.
     */
    private function __construct() {
        $this->define_constants();
        $this->add_hooks();
    }

    /**
     * Returns the single instance of the class.
     *
     * @return Store_Order_Woo The single instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Defines plugin constants.
     */
    private function define_constants() {
        define( 'SOW_VERSION', self::$version );
        define( 'SOW_FILE', __FILE__ );
        define( 'SOW_PATH', __DIR__ );
        define( 'SOW_URL', plugins_url( '', SOW_FILE ) );
        define( 'SOW_ASSETS', SOW_URL . '/assets' );
    }

    /**
     * Adds hooks.
     */
    private function add_hooks() {
        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Initializes the plugin.
     */
    public function init() {
        new StoreOrderWoo\Store_Order_Woo_Core();
    }

    /**
     * Loads the plugin's text domain for localization.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'store-order-woo', false, dirname( plugin_basename( SOW_FILE ) ) . '/languages' );
    }

}

/**
 * Initializes the Store_Order_Woo class.
 *
 * @return Store_Order_Woo
 */
function store_order_woo() {
    return Store_Order_Woo::instance();
}

// Initialize the plugin.
store_order_woo();

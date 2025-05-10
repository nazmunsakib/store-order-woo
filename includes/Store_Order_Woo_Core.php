<?php
/**
 * Plugin Main Class
 *
 * @package StoreOrderWoo
 * @version 1.0.0
 */
namespace StoreOrderWoo;

use StoreOrderWoo\Enqueue;
use StoreOrderWoo\Rest_Api;
use StoreOrderWoo\Store_Order_Settings_Tab;
use StoreOrderWoo\Order_Sync;

defined('ABSPATH') || die();

class Store_Order_Woo_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {
        // Include dependencies and initiate them
        $this->includes();
        $this->add_hooks();
	}

    /**
     * Includes all necessary classes.
     */
	private function includes() {
        // Initialize required classes
        new Enqueue();
        new Rest_Api();
        new Order_Sync();
	}

    private function add_hooks(){
        add_filter('woocommerce_get_settings_pages', [$this, 'add_settings_tab']);
    }

    public function add_settings_tab($settings_tabs) {
        $settings_tabs[] = new Store_Order_Settings_Tab();
        return $settings_tabs;
    }

}

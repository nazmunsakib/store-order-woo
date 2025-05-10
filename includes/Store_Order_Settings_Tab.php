<?php
/**
 * Plugin Enqueue Assets
 *
 * Handles Rest_Api
 * for the Product FAQ WooCommerce plugin.
 *
 * @package StoreOrderWoo
 */

 namespace StoreOrderWoo;
 use \WC_Settings_Page;
 use \WC_Admin_Settings;

 defined('ABSPATH') || die();

if (class_exists('WC_Settings_Page')) {
    class Store_Order_Settings_Tab extends WC_Settings_Page {

        public function __construct() {
            $this->id    = 'store_order';
            $this->label = __('Store Order', 'store-order-woo');
            parent::__construct();
        }

        public function get_settings() {
            $settings = array(
                array(
                    'title' => __('Store Order', 'store-order-woo'),
                    'type'  => 'title',
                    'desc'  => __('Settings related to store order.', 'store-order-woo'),
                    'id'    => 'store_order_section_title',
                ),
                array(
                    'title'    => __('Hub URL', 'store-order-woo'),
                    'desc_tip' => __('Enter the base URL of your Hub.', 'store-order-woo'),
                    'id'       => 'sow_hub_url',
                    'type'     => 'text',
                    'default'  => '',
                ),
                array(
                    'title'    => __('Hub API Key', 'store-order-woo'),
                    'desc_tip' => __('Enter your Hub API Key.', 'store-order-woo'),
                    'id'       => 'sow_hub_api_key',
                    'type'     => 'text',
                    'default'  => '',
                ),
                array(
                    'type' => 'sectionend',
                    'id'   => 'store_order_section_title',
                ),
            );
            return apply_filters('woocommerce_store_order_settings', $settings);
        }
        

        public function output() {
            $settings = $this->get_settings();
            WC_Admin_Settings::output_fields($settings);
        }

        public function save() {
            $settings = $this->get_settings();
            WC_Admin_Settings::save_fields($settings);
        }
    }
}
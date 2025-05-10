<?php
/**
 * WooCommerce Order Data Sync to Hub
 * 
 * Sends specific order details to Hub immediately after order placement.
 *
 * @package StoreOrderWoo
 * @version 1.0.0
 */

namespace StoreOrderWoo;

defined('ABSPATH') || exit;

class Order_Sync {

    /**
     * Hub API endpoint URL
     * @var string
     */
    private $hub_url;

    /**
     * Hub API authentication key
     * @var string
     */
    private $hub_api_key;

    /**
     * Initialize the sync handler
     */
    public function __construct() {
        $this->hub_url      = 'http://dev.local/wp-json/hub-order/v1/receive-order'; //esc_url_raw(get_option('sow_hub_url', ''));
        $this->hub_api_key  = 'wwWP UJnA FzaU 13UX Ya7o iowp'; //get_option('sow_hub_api_key', '');

        $this->add_hooks();
        
    }

    private function add_hooks(){
        add_action('woocommerce_checkout_order_created', [$this, 'sync_order_to_hub']);
    }

    /**
     * Immediately sync order data to Hub
     *
     * @param int $order_id WooCommerce order ID
     */
    public function sync_order_to_hub($order_id) {
        $order = wc_get_order($order_id);
        
        if (!$order || !$this->is_configured()) {
            return;
        }

        $order_data = $this->prepare_order_data($order);
        $this->send_to_hub($order_data);
    }

    /**
     * Prepare the required order data for Hub
     *
     * @param \WC_Order $order
     * @return array
     */
    private function prepare_order_data(\WC_Order $order) {
        $order_date = $order->get_date_created();
        $shipping_date = $this->calculate_shipping_date($order_date);
        
        return [
            'order_id'      => $order->get_id(),
            'customer_name' => $order->get_formatted_billing_full_name(),
            'email'         => $order->get_billing_email(),
            'status'        => $order->get_status(),
            'order_date'    => $order_date->format('Y-m-d H:i:s'),
            'shipping_date' => $shipping_date->format('Y-m-d H:i:s'),
            'notes'         => $this->get_order_notes($order)
        ];
    }

    /**
     * Calculate shipping date (2 weeks after order date)
     *
     * @param \WC_DateTime $order_date
     * @return \WC_DateTime
     */
    private function calculate_shipping_date($order_date) {
        $shipping_date = clone $order_date;
        $shipping_date->add(new \DateInterval('P14D')); // Add 14 days
        return $shipping_date;
    }

    /**
     * Get all order notes
     *
     * @param \WC_Order $order
     * @return array
     */
    private function get_order_notes(\WC_Order $order) {
        $notes = [];
        
        foreach ($order->get_customer_order_notes() as $note) {
            $notes[] = [
                'date'    => $note->comment_date,
                'author'  => $note->comment_author,
                'content' => $note->comment_content
            ];
        }
        
        return $notes;
    }

    /**
     * Send data to Hub API
     *
     * @param array $data
     */
    private function send_to_hub(array $data) {
        $args = [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode('sakib:wwWP UJnA FzaU 13UX Ya7o iowp'),
                'Content-Type'  => 'application/json',
                'X-Request-ID'  => wp_generate_uuid4()
            ],
            'body'      => json_encode($data),
            'timeout'   => 10,
            'sslverify' => false // for local dev only
        ];
    
        $response = wp_remote_post($this->hub_url, $args);
    
        if (is_wp_error($response)) {
            error_log('[stor_order] Hub Sync Error: ' . $response->get_error_message());
        }
    }

    /**
     * Check if API is configured
     *
     * @return bool
     */
    private function is_configured() {
        return !empty($this->hub_url) && !empty($this->hub_api_key);
    }
}
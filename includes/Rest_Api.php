<?php
/**
 * WooCommerce to Hub Order Synchronization Handler
 *
 * Facilitates real-time bidirectional synchronization between WooCommerce orders and Hub.
 * Handles immediate order push to Hub on creation and processes updates from Hub.
 *
 * @package     StoreOrderWoo
 * @version     1.0.0
 */

namespace StoreOrderWoo;

defined('ABSPATH') || exit;

/**
 * Rest_Api class.
 *
 * Handles all order synchronization logic between WooCommerce and Hub.
 */
class Rest_Api {

    /**
     * Class constructor.
     *
     * Initializes the API handler and sets up required hooks.
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks.
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        add_action('rest_api_init', [$this, 'register_hub_endpoint']);
    }

    /**
     * Register REST API endpoint for Hub updates.
     *
     * @since 1.0.0
     */
    public function register_hub_endpoint() {
        register_rest_route('store-order-woo/v1', '/update-order', [
            'methods'  => 'POST',
            'callback' => [$this, 'handle_hub_update'],
            'permission_callback' => [$this, 'authenticate_hub_request'],
            'args' => [
                'order_id' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    },
                    'sanitize_callback' => 'absint'
                ],
                'status' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return array_key_exists('wc-'.$param, wc_get_order_statuses()) || 
                               array_key_exists($param, wc_get_order_statuses());
                    }
                ],
                'note' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'timestamp' => [
                    'required' => true,
                    'validate_callback' => 'is_numeric'
                ]
            ]
        ]);
    }

    /**
     * Authenticate Hub requests using Basic Auth.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request
     * @return bool
     */
    public function authenticate_hub_request(\WP_REST_Request $request) {
        // Get the auth header
        $auth_header = $request->get_header('Authorization');
        
        if (empty($auth_header)) {
            return false;
        }

        // Check if it's Basic Auth
        if (strpos($auth_header, 'Basic ') !== 0) {
            return false;
        }

        // Decode the credentials
        $base64_credentials = str_replace('Basic ', '', $auth_header);
        $credentials = base64_decode($base64_credentials);
        list($username, $password) = explode(':', $credentials, 2);

        // Validate against stored credentials
        $valid_username = 'sakib';
        $valid_password = 'wwWP UJnA FzaU 13UX Ya7o iowp';

        return ($username === $valid_username && $password === $valid_password);
    }

    /**
     * Handle incoming updates from Hub.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Full data about the request.
     * @return \WP_REST_Response|\WP_Error
     */
    public function handle_hub_update(\WP_REST_Request $request) {
        try {
            $params = $request->get_params();
            $order  = wc_get_order($params['order_id']);

            if (!$order) {
                throw new \Exception(__('Order not found', 'store-order-woo'), 404);
            }

            
            $current_time = current_time('timestamp');
            if (abs($current_time - $params['timestamp']) > 300) {
                throw new \Exception(__('Request timestamp is too old', 'store-order-woo'), 400);
            }


            if (doing_action('woocommerce_order_status_changed')) {
                return new \WP_REST_Response([
                    'success' => true,
                    'message' => 'Ignored - already processing status change'
                ], 200);
            }


            $status = str_replace('wc-', '', $params['status']);

           
            if ($order->get_status() == $status) {
                $order->update_status(
                    $status,
                    __('Status updated via Hub sync', 'store-order-woo'),
                    true
                );
            }
            
            
            if (!empty($params['note'])) {
                $order->add_order_note(
                    __('Hub update: ', 'store-order-woo') . $params['note'],
                    false,
                    true  
                );
            }

            // Return success response
            return new \WP_REST_Response([
                'success'    => true,
                'order_id'   => $params['order_id'],
                'new_status' => $order->get_status(),
                'timestamp'  => $current_time
            ], 200);

        } catch (\Exception $e) {
            error_log('Hub sync error: ' . $e->getMessage());
            return new \WP_Error(
                'processing_error',
                $e->getMessage(),
                ['status' => $e->getCode() ?: 500]
            );
        }
    }
}
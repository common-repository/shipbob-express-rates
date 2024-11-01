<?php namespace ShipBob\WooRates\Api;

/**
 * @class HttpClient
 *
 * @description Abstract http client class for common methods for connecting to API
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */

use \ShipBob\WooRates\Kernel;
use \Exception;

class HttpClient {

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var object
     */
    protected $config;

    /**
     *  Create a new instance
     *
     * @param Kernel $kernel
     *
     * @return void
     */
    public function __construct( Kernel $kernel, array $config = [] ) {

        // Load the kernel
        $this->kernel = $kernel;

        // Load the configuration options for the client
        $this->config = (object) array_merge( [
            'store_key' => $this->kernel->config->woo->shop_url,
            'endpoint'  => $this->kernel->config->endpoint,
            'debug'     => $this->kernel->config->debug,
        ], $config );

    }

    /**
     *   Returns list of default headers for all requests
     *
     * @return array
     */
    protected function get_default_headers() {
        return [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
        ];
    }

    /**
     *   Returns default data to be passed to all requests
     *
     * @return array
     */
    protected function get_default_data() {

        $locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
        $locale = apply_filters( 'plugin_locale', $locale, 'woocommerce' );

        return [
        'rate' => [
            'storeKey' => ($this->config->store_key ?: $this->kernel->config->woo->shop_url) ?: home_url( '/' ),
            'currency' => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'USD',
            'locale'   => $locale,
            ],
        ];
    }

    /**
     *   Convenience wrapper for HTTP request with credentials
     *   for API added. Returns the parsed JSON response object or false on error
     *
     * @param string $path
     * @param string $method
     * @param array $data
     * @param array $headers
     *
     * @return object|string|false
     */
    public function send_request( $path, $method, array $data = [], array $headers = [] ) {

        // Fetch the response
        $response = $this->request->fetch_url(
            $url = $this->config->endpoint . '/' . $path,
            $data = $data ? json_encode(
                $this->request->mergeArrays( $data, $this->get_default_data() ),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) : [],
            $headers = array_merge( $headers, $this->get_default_headers() ),
            $method
        );

        // Parse the response
        $response_data = json_decode( $response ?: '' );

        // If debug, output to log on failure
        if ( $this->config->debug && ( ! is_object( $response_data ) || isset( $response_data->ERRORCODE ) ) ) {
            error_log(
                'API Error: ' . $method . ' request to ' . $this->config->endpoint . ' failed. ' .
                print_r( [
                    'url'      => $url,
                    'data'     => $data,
                    'headers'  => $headers,
                    'response' => $response,
                ], true )
            );
        }

        // Return the response
        return $response_data;

    }

    /**
     *   Convenience wrapper for GET requests
     *
     * @param string $path
     * @param array $data
     * @param array $headers
     *
     * @return object|false
     */
    public function get( $path, array $data = [], array $headers = [] ) {
        return $this->send_request( $path, 'GET', $data, $headers );
    }

    /**
     *   Convenience wrapper for POST requests
     *
     * @param string $path
     * @param array $data
     * @param array $headers
     *
     * @return object|false
     */
    public function post( $path, array $data = [], array $headers = [] ) {
        return $this->send_request( $path, 'POST', $data, $headers );
    }

    /**
     *   Convenience wrapper for PUT requests
     *
     * @param string $path
     * @param array $data
     * @param array $headers
     *
     * @return object|false
     */
    public function put( $path, array $data = [], array $headers = [] ) {
        return $this->send_request( $path, 'PUT', $data, $headers );
    }

    /**
     *  Dynamically retrieves components from the kernel
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get( $key ) {
        return $this->kernel->$key;
    }

}
<?php namespace ShipBob\WooRates\Services;

/**
 * @class Request
 *
 * @description Provides common methods related to the current request
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class Request extends Service {

    /**
     *   Returns the request URI. Used to redirect local referral links.
     *
     *   The following method is derived from code of the Zend Framework (1.10dev - 2010-01-24)
     *   Code subject to the new BSD license (http://framework.zend.com/license/new-bsd).
     *   Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     *
     * @return string
     */
    public function uri() {
        if ( isset( $_SERVER['HTTP_X_ORIGINAL_URL'] ) ) {
            // IIS with Microsoft Rewrite Module
            return $_SERVER['HTTP_X_ORIGINAL_URL'];
        }

        if ( isset( $_SERVER['HTTP_X_REWRITE_URL'] ) ) {
            // IIS with ISAPI_Rewrite
            return $_SERVER['HTTP_X_REWRITE_URL'];
        }

        if ( isset( $_SERVER['IIS_WasUrlRewritten'] ) && isset( $_SERVER['UNENCODED_URL'] ) ) {
            if ( $_SERVER['IIS_WasUrlRewritten'] == '1' && $_SERVER['UNENCODED_URL'] != '' ) {
                // IIS7 with URL Rewrite: make sure we get the unencoded URL (double slash problem)
                return $_SERVER['UNENCODED_URL'];
            }
        }

        if ( isset( $_SERVER['REQUEST_URI'] ) ) {
            // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path, only use URL path
            return $_SERVER['REQUEST_URI'];
        }

        if ( isset( $_SERVER['ORIG_PATH_INFO'] ) ) {
            // IIS 5.0, PHP as CGI
            if ( $_SERVER['QUERY_STRING'] != '' ) {
                return $_SERVER['ORIG_PATH_INFO'] . '?' . $_SERVER['QUERY_STRING'];
            }

            return $_SERVER['ORIG_PATH_INFO'];
        }

        return $_SERVER['PHP_SELF'];
    }

    /**
     *   Returns the base uri without any query string appended
     *
     * @return string
     */
    public function base_uri() {
        return preg_replace( '#\?(.*)$#ismu', '', $this->uri() );
    }

    /**
     *   Returns the top level domain from the HTTP_HOST
     *
     * @return string
     */
    public function get_tld() {
        $host        = explode( ':', sanitize_text_field( $_SERVER['HTTP_HOST'] ) );
        $domain_list = explode( '.', $host[0] );

        return ( count( $domain_list ) > 1 ) ? implode( '.', array_slice( $domain_list, - 2 ) ) : $host[0];
    }

    /**
     *   Returns the current remote ip address if present and valid
     *
     * @return string
     */
    public function get_ip() {
        $ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );

        return preg_match( '#[0-9\.]+#ismu', $ip ) ? $ip : '';
    }

    /**
     *  Returns input from GET or POST by key or
     *  default if given
     *
     * @param string|array $key
     * @param mixed $default
     * @param callable $sanitizer
     *
     * @return mixed
     */
    public function get_input( $key = null, $default = null, $sanitizer = 'sanitize_text_field' ) {
        if ( is_null( $key ) ) {
            return $this->sanitize_input( $_REQUEST, $sanitizer );
        }
        if ( is_array( $key ) ) {
            return $this->sanitize_input( array_filter( $_REQUEST, function ( $v, $k ) use ( $key ) {
                return in_array( $k, $key );
            }, ARRAY_FILTER_USE_BOTH ), $sanitizer );
        }
        if ( isset( $_REQUEST[ $key ] ) ) {
            return $this->sanitize_input( $_REQUEST[ $key ], $sanitizer );
        }

        return $default;
    }

    /**
     *   Sanitizes input string in recursive manner
     *
     * @param mixed $input
     * @param callable $sanitizer
     *
     * @return mixed
     */
    protected function sanitize_input( $input, $sanitizer = 'sanitize_text_field' ) {

        // Not an array, treat as string
        if ( ! is_array( $input ) ) {
            return call_user_func( $sanitizer, stripslashes( strval( $input ) ) );
        }

        // Return sanitized array
        return array_map( function ( $i ) use ( $sanitizer ) {
            return $this->sanitize_input( $i, $sanitizer );
        }, $input );

    }

    /**
     *  Returns data from COOOKIE by key or
     *  default if given
     *
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function get_cookie( $key = null, $default = null ) {
        if ( is_null( $key ) ) {
            return $this->sanitize_input( $_COOKIE );
        }
        if ( isset( $_COOKIE[ $key ] ) ) {
            return $this->sanitize_input( $_COOKIE[ $key ] );
        }

        return $default;
    }

    /**
     *   Returns an http response from the given url
     *
     * @param string $url
     * @param string|array $data
     * @param array $headers
     *
     * @return string
     */
    public function fetch_url( $url, $data = [], array $headers = [], $method = '' ) {

        // Prepare the request
        $args = [
            'method'      => $method ?: ($data ? 'POST' : 'GET'),
            'timeout'     => 30,
            'httpversion' => '1.1',
            'headers'     => $headers,
            'body'        => $data ?: null,
        ];

        // Send the request
        $response = wp_remote_request( $url, $args );

        // Check the response status
        $status = wp_remote_retrieve_response_code( $response );

        // If no or invalid status, log and return failed
        if ( ! $status || intval( $status ) >= 400 ) {
            error_log(
                __NAMESPACE__ . ' plugin received no or invalid response from ' . $url . ', ' .
                'Status: ' . $status . ', ' .
                ( ( is_wp_error( $response ) ) ? 'WP Error: ' . $response->get_error_message() . ', ' : '' )
            );

            return false;
        }

        // Return the response
        return wp_remote_retrieve_body( $response );

    }

    /**
     *   Returns whether the current request is an ajax request
     *   based on the HTTP request headers received
     *
     * @return bool
     */
    public function is_ajax() {
        $xhr  = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
        $json = isset( $_SERVER['HTTP_ACCEPT'] ) && preg_match( '#^application/json#ismu', $_SERVER['HTTP_ACCEPT'] );

        return $xhr || $json;
    }

    /**
     *   Returns the origin header if present on the request
     *
     * @return string
     */
    public function get_origin() {
        return isset( $_SERVER['HTTP_ORIGIN'] ) ? sanitize_text_field( $_SERVER['HTTP_ORIGIN'] ) : '';
    }

    /**
     *   Returns the request method
     *
     * @return string
     */
    public function get_method() {
        return isset( $_SERVER['REQUEST_METHOD'] ) ? strtolower( sanitize_text_field( $_SERVER['REQUEST_METHOD'] ) ) : '';
    }

    /**
     *   Checks whether the current request is an admin request
     *   allowing for ajax requests from the front end.
     *
     * @return bool
     */
    public function is_admin() {

        // Not an admin request per WP, bail early
        if ( ! is_admin() ) {
            return false;
        }

        // Not doing AJAX, return true
        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
            return true;
        }

        // Not an admin refering page, return false
        if ( ! $this->has_admin_referer() ) {
            return false;
        }

        // Is admin, doing ajax, and has admin referrer, return true
        return true;
    }

    /**
     *   Returns whether the current request is under ssl
     *
     * @return bool
     */
    public function is_ssl() {
        return \is_ssl();
    }

    /**
     *   Returns whether the current request is running in WP_DEBUG
     *   mode
     *
     * @return bool
     */
    public function is_debug() {
        return defined( 'WP_DEBUG' ) && WP_DEBUG;
    }

    /**
     *   Returns whether the referring page was an admin
     *   page.
     *
     */
    public function referer() {
        return isset( $_SERVER['HTTP_REFERER'] ) ? $this->sanitize_input( $_SERVER['HTTP_REFERER'] ) : '';
    }

    /**
     *   Returns whether the refering page was an admin
     *   page.
     *
     */
    public function has_admin_referer() {
        return preg_match( '#^' . preg_quote( \admin_url(), '#' ) . '#ismu', $this->referer() );
    }

    /**
     *  Determines if the current request is using the specified
     *  page template
     *
     * @param string $template
     *
     * @return boolean
     */
    public function is_template( $template ) {

        // Not a valid template slug, return false
        if ( ! is_string( $template ) || ! $template ) {
            return false;
        }

        // Locate the current post id
        $post_id = absint( $this->get_input( 'post', $this->get_input( 'post_ID' ) ) );

        // No post id found, return false
        if ( ! $post_id ) {
            return false;
        }

        // Load the template slug
        $slug = get_page_template_slug( $post_id );

        // No template set, return false
        if ( ! $slug ) {
            return false;
        }

        // Remove the any template slug extension to match against
        $slug = preg_replace( '#\.[^\.]+$#ismu', '', $slug );

        // Return whether matched
        return $template === $slug;

    }

}
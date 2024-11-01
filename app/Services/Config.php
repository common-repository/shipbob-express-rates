<?php namespace ShipBob\WooRates\Services;

/**
 * @class Config
 *
 * @description Provides methods for  loading configuration settings
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */

use \ShipBob\WooRates\Kernel;

class Config extends Service {

    /**
     * @var array
     */
    protected $_options;

    /**
     *  Create a new instance
     *
     * @param Kernel $kernel
     *
     * @return void
     */
    public function __construct( Kernel $kernel ) {

        parent::__construct( $kernel );

        // Set base options needed before init
        $this->_options = [
            'name'          => __( 'ShipBob Express', 'shipbob-express-rates' ),
            'slug'          => md5( Kernel::class ),
            'page_slug'     => 'shipbob-express-rates',
            'prefix'        => hash( 'crc32', Kernel::class ) . '_',
            'filter_prefix' => 'shipbobrates_',
            'debug' => ( $_SERVER['APP_ENV'] ?? null ) === 'testing' && defined('WP_TESTS_DIR'),
        ];

    }

    /**
     *  Initializes and loads all configuration options
     *
     * @return void
     */
    public function init() {

        $this->_options = array_merge( $this->_options, [
            'activated'      => boolval( $this->get_option( 'activated', false ) ),
            'terms_accepted' => boolval( $this->get_option( 'terms_accepted', false ) ),
            'web'            => (object) [
                'login_url' => 'https://web.shipbob.com/app/Merchant/#/Login',
            ],
            'woo'            => (object) [
                'shop_url' => function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' ),
            ],
            'endpoint' => 'https://' . ($this->_options['debug'] ? 'sandbox-api.shipbob.com/CarrierServiceFunction/qa' : 'api.shipbob.com/CarrierServiceFunction') . '/ExpressRates',
        ] );

    }

    /**
     *  Retrieves configuration values from constant if found or checks
     *  for secondary option if found
     *
     * @param string $constant
     * @param string $name
     * @param string $default
     *
     * @return void
     */
    protected function set_from_constant_or_option( $constant, $name, $default = '' ) {
        if ( defined( $constant ) && constant( $constant ) ) {
            $this->_options[ $name ]                 = constant( $constant );
            $this->_options[ $name . '_restricted' ] = true;
        } else {
            $this->_options[ $name ] = $this->get_option( $name, $default );
        }
    }

    /**
     *  Retrieves configuration settings from WP options table in
     *  multisite compatible way
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    protected function get_option( $name, $default = '' ) {
        return get_site_option( $this->slug . '_' . $name, get_option( $this->slug . '_' . $name, $default ) );
    }

    /**
     *  Retrieves configuration values from environment if found or checks
     *  for secondary option if found
     *
     * @param string $constant
     * @param string $name
     * @param string $default
     *
     * @return void
     */
    protected function set_from_env_or_option( $constant, $name, $default = '' ) {
        if ( isset( $_ENV[ $constant ] ) ) {
            $this->_options[ $name ]                 = strval( $_ENV[ $constant ] );
            $this->_options[ $name . '_restricted' ] = true;
        } else {
            $this->_options[ $name ] = $this->get_option( $name, $default );
        }
    }

    /**
     *  Retrieves configuration settings from the environment settings
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    protected function get_from_env( $name, $default = '' ) {
        return isset( $_ENV[ $name ] ) ? strval( $_ENV[ $name ] ) : $default;
    }

    /**
     *   Allows dynamic retrieval of any options loaded
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get( $name ) {

        if ( isset( $this->_options[ $name ] ) ) {
            return $this->_options[ $name ];
        }

        if ( isset( $this->_options[ $this->slug . '_' . $name ] ) ) {
            return $this->_options[ $this->slug . '_' . $name ];
        }

        return null;
    }

}
<?php namespace ShipBob\WooRates\Services;

/**
 * @class Settings
 *
 * @description Handles all UI settings fields in the admin
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */

use \ShipBob\WooRates\Kernel;

class Settings extends Service {

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $group;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     *  Create a new instance
     *
     * @param Kernel
     *
     * @return void
     */
    public function __construct( Kernel $kernel ) {
        parent::__construct( $kernel );
        $this->slug  = $this->config->slug . '-settings';
        $this->group = $this->config->slug . '-group';
    }

    /**
     * Adds action links to the plugin listing
     *
     * @used-by add_filter( 'plugin_action_links_' . plugin_basename( $this->paths->plugin_file() ), [ static::class, 'add_action_links' ], 10, 1 );
     *
     * @param array $links
     *
     * @return array
     */
    public static function add_action_links( array $links ) {
        return array_merge( [
            '<a href="' . esc_attr( admin_url( 'admin.php?page=' . urlencode( Kernel::getInstance()->config->page_slug ) ) ) . '">' . __( 'Settings' ) . '</a>',
        ], $links );
    }

    /**
     *   Adds option page for search widgets to admin
     *   settings under Settings -> Search Widgets
     *
     * @used-by add_action( 'admin_menu', [ static::class, 'add_page' ] );
     *
     * @return void
     */
    public function add_page() {

        add_menu_page(
            __( 'ShipBob Express', 'shipbob-express-rates' ),
            __( 'ShipBob Express', 'shipbob-express-rates' ),
            'manage_options',
            $this->config->page_slug,
            [ $this, 'display_page' ],
            'none',
            80
        );

    }

    /**
     *   Add search option fields to the admin settings
     *
     * @used-by add_action( 'admin_init', [ static::class, 'add_fields' ] );
     *
     * @return void
     */
    public function add_fields() {

        register_setting(
            $this->group . '-terms',
            $this->config->slug . '_terms_accepted',
            [
                'type'              => 'integer',
                'sanitize_callback' => 'intval'
            ]
        );

    }

    /**
     *   Displays the requested admin page
     *
     * @used-by add_submenu_page()
     *
     * @return void
     */
    public function display_page() {

        // Ensure the terms and conditions have been accepted
        if ( ! $this->config->terms_accepted ) {
            return $this->display_terms();
        }

        // Display the settings view
        echo $this->view->render( 'admin/settings', [
            'title'     => __( 'ShipBob Express', 'shipbob-express-rates' ),
            'group'     => $this->group,
            'slug'      => $this->config->slug . '_',
            'login_url' => $this->config->web->login_url,
            'plugin_url' => $this->paths->plugin_url(),
        ] );

    }

    /**
     *   Dispaly the terms and conditions view
     *
     *   return void
     */
    public function display_terms() {

        echo $this->view->render( 'admin/terms', [
            'title'          => __( 'Express Shipping Option Terms & Conditions', 'shipbob-express-rates' ),
            'group'          => $this->group . '-terms',
            'slug'           => $this->config->slug . '_',
            'terms_accepted' => $this->config->terms_accepted,
        ] );

    }

}
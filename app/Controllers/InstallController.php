<?php namespace ShipBob\WooRates\Controllers;

/**
 * @class InstallController
 *
 * @description Handles all actions for activation and deactivation of plugin
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class InstallController extends Controller {

	/**
	 *  Registers all activation/deactivation hooks
	 *
	 * @return void
	 */
	public function register() {

		$plugin = $this->paths->plugin_basename( $this->paths->plugin_file );

		register_activation_hook( $plugin, [ $this, 'onActivate' ] );

	}

	/**
	 *  Action to be ran when plugin is activated
	 *
	 * @used-by register_activation_hook( $plugin, [ static::class, 'onActivate' ] );
	 *
	 * @return void
	 */
	public function onActivate() {
		update_site_option( $this->config->slug . '_activated', true );
	}

	/**
	 *  Action to be ran just after activation
	 *
	 * @used-by add_action( 'admin_init', [ static::class, 'afterActivate' ] );
	 *
	 * @return void
	 */
	public function afterActivate() {
		if ( ! is_admin() ) {
			return;
		}

		// Not marked just activated, bail
		if ( ! $this->config->activated ) {
			return;
		}

		// Unmark as just activated
		delete_site_option( $this->config->slug . '_activated' );

		// Makes sure the plugin is defined before trying to use it
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		// Add thanks notice
		$this->admin->addNotice( 'Thanks for installing ' . esc_html( $this->config->name ) . '!' );
	}

	/**
	 *  Deactivates the plugin
	 *
	 * @return void
	 */
	public function deactivate() {
		add_action( 'admin_init', function () {
			return \deactivate_plugins( $this->paths->plugin_basename( $this->paths->plugin_file ) );
		} );
	}

	/**
	 *  Runs check for any plugin dependencies that must be met
	 *  to allow activation.
	 *
	 * @return bool
	 */
	public function hasDependencies() {

		// PHP 7+ (Required)
		if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {

			$this->admin->addNotice(
				sprintf(
					__( '%s was deactivated. PHP version requirement of 7+ not met.', 'shipbob-express-rates' ),
					esc_html( $this->config->name )
				),
				'error'
			);

			return false;

		}

		// Woocommerce 3+ (Required)
		if ( ! class_exists( '\\WooCommerce' ) || version_compare( \WooCommerce::instance()->version, '3.0', '<' ) ) {

			$this->admin->addNotice(
				sprintf(
					__( '%s was deactivated. WooCommerce 3+ requirement not met.', 'shipbob-express-rates' ),
					esc_html( $this->config->name )
				),
				'error'
			);

			return false;

		}

		return true;
	}

}
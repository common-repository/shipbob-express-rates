<?php namespace ShipBob\WooRates\Controllers;

/**
 * @class AdminController
 *
 * @description Admin controller for load all added functioality to the WP admin
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class AdminController extends Controller {

	/**
	 *  Registers all actions for the admin
	 *
	 * @return AdminController
	 */
	public function registerActions() {

		// Install hooks
		add_action( 'admin_init', [ $this->install, 'afterActivate' ] );

		// Settings hooks
		add_action( 'admin_menu', [ $this->settings, 'add_page' ] );

		if ( preg_match( '#options(-general)?\.php#ismu', $this->request->uri() ) ) {
			add_action( 'admin_init', [ $this->settings, 'add_fields' ] );
		}

		return $this;
	}

	/**
	 *  Registers all filters for the admin
	 *
	 * @return AdminController
	 */
	public function registerFilters() {

		// Settings filters
		add_filter( 'plugin_action_links_' . plugin_basename( $this->paths->plugin_file() ), [
			$this->settings,
			'add_action_links'
		], 10, 1 );

		return $this;
	}

	/**
	 *  Registers assets for the admin
	 *
	 * @return AdminController
	 */
	public function registerAssets() {

		// Add admin stylesheets
		$this->assets->addCss( 'admin.min.css', [], true );

		// Add admin scripts
		$this->assets->addScript( 'admin.min.js', [ 'jquery' ], true );

		return $this;
	}

	/**
	 *  Adds admin notices to be displayed to the user
	 *
	 * @param string $message
	 * @param string $class
	 *
	 * @return void
	 */
	public function addNotice( $message, $class = 'updated' ) {
		$action = is_network_admin() ? 'network_admin_notices' : 'admin_notices';
		add_action( $action, function () use ( $message, $class ) {
			echo $this->view->render( 'admin/notice', [
				'class' => $class,
				'message' => $message,
			] );
		} );
	}

}
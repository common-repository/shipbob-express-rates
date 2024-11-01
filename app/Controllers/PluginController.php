<?php namespace ShipBob\WooRates\Controllers;

/**
 * @class PluginController
 *
 * @description Core controller loading all added functionality for the plugin
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class PluginController extends Controller {

	/**
	 *  Prepare the plugin with items fired write after
	 *  all plugins are loaded
	 *
	 * @used-by add_action( 'plugins_loaded', [ static::class, 'preload' ] );
	 *
	 * @return void
	 */
	public function preload() {

		// Prepare the config
		$this->config;

	}

	/**
	 *   Initialize the plugin
	 *
	 * @return void
	 */
	public function init() {

		// Load any configuraton settings
		$this->config->init();

		// Check all dependecies are installed
		if ( ! $this->install->hasDependencies() ) {
			$this->install->deactivate();

			return;
		}

		// Prepare admin
		if ( is_admin() ) {
			$this->admin
				->registerActions()
				->registerFilters()
				->registerAssets();
		}

		// Prepare core
		$this
			->registerActions()
			->registerFilters();

	}

	/**
	 *  Registers all actions for the plugin
	 *
	 * @return PluginController
	 */
	public function registerActions() {

		// Woo
		add_action( 'woocommerce_shipping_init', [ $this->woo, 'register_shipping_methods' ] );
		add_action( 'woocommerce_order_status_completed', [ $this->woo, 'send_converted_order' ], 10, 1 );

		return $this;
	}

	/**
	 *  Registers all filters for the plugin
	 *
	 * @return PluginController
	 */
	public function registerFilters() {

		// Asset filters
		add_filter( 'script_loader_tag', [ $this->assets, 'render_script_attributes' ], 10, 2 );
		add_filter( 'style_loader_tag', [ $this->assets, 'render_style_attributes' ], 10, 2 );

		// Woo
		add_filter( 'woocommerce_shipping_methods', [ $this->woo, 'add_shipping_methods' ], 10, 1 );

		return $this;
	}

}
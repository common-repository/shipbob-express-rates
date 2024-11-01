<?php namespace ShipBob\WooRates\Services;

/**
 * @class Paths
 *
 * @description Provides methods to quickly access common paths
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class Paths extends Service {

	/**
	 *  Returns an absolute path from a path relative to the plugin
	 *
	 * @parma string $path
	 *
	 * @return string
	 */
	public function plugin( $path = '' ) {
		return realpath( __DIR__ . '/../..' ) . ( ( $path ) ? '/' . $path : '' );
	}

	/**
	 *  Returns the path to the app folder
	 *
	 * @return string
	 */
	public function app() {
		return $this->plugin( 'app' );
	}

	/**
	 *  Returns the path to the shipping folder
	 *
	 * @return string
	 */
	public function shipping() {
		return $this->plugin( 'app/Shipping' );
	}

	/**
	 *  Returns the path to the templates folder
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function templates( $path = '' ) {
		return apply_filters( $this->config->filter_prefix . 'template_path', $this->plugin() . '/templates' ) . ( ( $path ) ? '/' . $path : '' );
	}

	/**
	 *  Returns an absolute path from a relative path to the assets
	 *  folder
	 *
	 * @return string
	 */
	public function assets( $path = '' ) {
		return $this->plugin( 'assets' . ( ( $path ) ? '/' . $path : '' ) );
	}

	/**
	 *  Returns an absolute path from a relative path to the vendor
	 *  folder
	 *
	 * @return string
	 */
	public function vendor( $path = '' ) {
		return $this->plugin( 'vendor' . ( ( $path ) ? '/' . $path : '' ) );
	}

	/**
	 *  Returns the path to the css folder
	 *
	 * @return string
	 */
	public function css() {
		return $this->assets( 'css' );
	}

	/**
	 *  Returns the path to the js folder
	 *
	 * @return string
	 */
	public function js() {
		return $this->assets( 'js' );
	}

	/**
	 *  Returns the absolute path to the plugin loader
	 *
	 * @return string
	 */
	public function plugin_file() {
		return $this->plugin( 'plugin.php' );
	}

	/**
	 *  Returns aboslute url from a path relative to the plugin
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function plugin_url( $path = '' ) {
		return \plugins_url( $path, $this->plugin_file() );
	}

	/**
	 *  Returns the name of the plugin folder
	 *
	 * @return string
	 */
	public function plugin_folder() {
		return basename( $this->plugin_url() );
	}

	/**
	 *  Returns the relative path with the base as the
	 *  plugin folder
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function plugin_basename( $path ) {
		return $this->plugin_folder() . str_replace( $this->plugin(), '', $path );
	}

	/**
	 *  Returns the url for ajax requests
	 *
	 * @return string
	 */
	public function ajax_url() {
		return \admin_url( 'admin-ajax.php' );
	}

	/**
	 *  Dynamically retrieves paths as properties or
	 *  loads components from kernel
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( method_exists( $this, $key ) ) {
			return call_user_func( [ $this, $key ] );
		}

		return $this->kernel->$key;
	}

}
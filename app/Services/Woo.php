<?php namespace ShipBob\WooRates\Services;

use \ShipBob\WooRates\Shipping\ShipBobMethod;

/**
 * @class Woo
 *
 * @description Handles methods and filters for interacting with Woocommerce
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class Woo extends Service {

	/**
	 * @var array
	 */
	public $shipping_methods = [];

	/**
	 *   Registers shipping methods with the kernel
	 *
	 * @used-by add_action( 'woocommerce_shipping_init', [ static::class, 'register_shipping_methods' ] );
	 *
	 * @return void
	 */
	public function register_shipping_methods() {

		// Load list of shipping method classes
		$classes = glob( $this->paths->shipping . '/*Method.php' );

		// Setup default shipping methods container
		$this->shipping_methods = [];

		// Load the classes
		foreach ( $classes as $cls ) {

			// Skip the abstract class
			if ( preg_match( '#/ShippingMethod\.php$#ismu', $cls ) ) {
				continue;
			}

			// Try loading the class
			$cls             = $this->namespace . '\\Shipping\\' . basename( $cls, '.php' );
			$shipping_method = new $cls;

			// Register the method with the kernel
			$this->shipping_methods[ $shipping_method->id ] = get_class( $shipping_method );

		}

	}

	/**
	 *   Adds custom shipping methods to woocommerce
	 *
	 * @used-by add_filter( 'woocommerce_shipping_methods', [ static::class, 'add_shipping_methods' ], 10, 1 );
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	public function add_shipping_methods( array $methods ) {
		return array_merge( $methods, $this->shipping_methods );
	}

	/**
	 *   SEnds completed orders with ShipBob shipping method selected
	 *   to the api for conversion tracking
	 *
	 * @used-by add_action( 'woocommerce_order_status_completed', [ static::class, 'send_converted_order' ], 10, 1 );
	 *
	 * @param integer|string $order_id
	 *
	 * @return void
	 */
	public function send_converted_order( $order_id ) {

		// Fetch the order
		$order = wc_get_order( $order_id );

		// No order found, bail
		if ( ! $order ) {
			return;
		}

		// Check for ShipBob method on order
		foreach ( $order->get_shipping_methods() as $shipping_method ) {
			if ( $shipping_method->get_meta( 'shipbob_express' ) ) {
				$this->api->orders->save_converted( $order_id );

				return;
			}
		}

	}

}
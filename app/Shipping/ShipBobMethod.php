<?php namespace ShipBob\WooRates\Shipping;

use \WC_Product;
use \WC_Product_Variation;

/**
 * @class ShipBobMethod
 *
 * @description Custom shipping method for Woo to obtain rates via ShipBob Express API
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class ShipBobMethod extends ShippingMethod {

	/**
	 * @var float
	 */
	const GRAMS_TO_OZ = 28.3495;

	/**
	 * @var string
	 */
	public $id = 'shipbob_express';

	/**
	 *   Create a new instance
	 *
	 * @param int $instance_id
	 *
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );
		$this->method_title       = __( 'ShipBob Express Shipping', 'shipbob-express-rates' );
		$this->method_description = __( 'Rates provided by the ShipBob Express API', 'shipbob-express-rates' );
		$this->supports           = [
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		];
		$this->init();
	}

	/**
	 *   Initialize the shipping method
	 *
	 * @return void
	 */
	public function init() {

		// Load the settings
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title = $this->get_option( 'title' );

		// Actions
		add_action( 'woocommerce_update_options_shipping_' . $this->id, [ $this, 'process_admin_options' ] );

	}

	/**
	 *   Init form fields
	 *
	 * @return array
	 */
	public function init_form_fields() {
		$this->instance_form_fields = [
			'title' => [
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => $this->method_title,
				'desc_tip'    => true,
			],
		];
	}

	/**
	 *   Returns a destination array for rate quote from a woo package
	 *
	 * @param array $package
	 *
	 * @return array
	 */
	public function get_package_destination( array $package ) {
		return [
			'country'     => $package['destination']['country'] ?? 'US',
			'province'    => $package['destination']['state'] ?? null,
			'postal_code' => $package['destination']['postcode'] ?? null,
			'city'        => $package['destination']['city'] ?? null,
			'address1'    => $package['destination']['address'] ?? null,
			'address2'    => $package['destination']['address_1'] ?? null,
			'address3'    => $package['destination']['address_2'] ?? null,
		];
	}

	/**
	 *   Returns array of items for rate quote from a woo package
	 *
	 * @param array $package
	 *
	 * @return array
	 */
	public function get_package_items( array $package ) {
		return array_values( array_filter( array_map( function ( $item ) {

			$product = $item['data'];

			if ( ! ( $product instanceof WC_Product ) ) {
				return;
			}

			$parent_data = $product instanceof WC_Product_Variation ? $product->get_parent_data() : null;

			return [
				'name'        => $product->get_name(),
				'sku'         => $parent_data ? $parent_data['sku'] : $product->get_sku(),
				'quantity'    => $item['quantity'] ?? 1,
				'grams'       => round( floatval($product->get_weight()) * static::GRAMS_TO_OZ ),
				'price'       => $product->get_price(),
				'taxable'     => $product->get_tax_status() === 'taxable',
				'product_id'  => $item['product_id'] ?? null,
				'variant_id'  => $item['variation_id'] ?? null,
				'variant_sku' => $product->get_sku(),
			];

		}, $package['contents'] ?? [] ) ) );
	}

	/**
	 *   Called to calculate shipping rates for this method. Rates can be added using the add_rate() method.
	 *
	 * @param array $package
	 *
	 * @return void
	 */
	public function calculate_shipping( $package = [] ) {

		// Fetch the current rate quotes for this package
		$rates = $this->kernel->api->rates->get_quote(
			$destination = $this->get_package_destination( $package ),
			$items = $this->get_package_items( $package )
		);

		// Add the rates for this shipping method
		foreach ( $rates as $rate ) {
			$this->add_rate( [
				'id'        => $this->get_rate_id( strtolower( $rate->service_name ?? '' ) ),
				'label'     => trim( ( $rate->service_name ?? '' ) . ( ' ' . $rate->description ?? '' ) ),
				'cost'      => $rate->total_price ?? null,
				'taxes'     => false,
				'package'   => $package,
				'meta_data' => [
					'shipbob_express' => true,
				],
			] );
		}

	}

}
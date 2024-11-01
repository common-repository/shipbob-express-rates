<?php namespace ShipBob\WooRates\Shipping;

use \WC_Shipping_Method;
use \ShipBob\WooRates\Kernel;

/**
 * @class ShippingMethod
 *
 * @description Abstract class shipping method class extending the Woocommerce Shipping Method class
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
abstract class ShippingMethod extends WC_Shipping_Method {

	/**
	 * @var Kernel;
	 */
	protected $kernel;

	/**
	 *   Create a new instance
	 *
	 * @param int $instance_id
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );
		$this->kernel = Kernel::getInstance();
	}

}
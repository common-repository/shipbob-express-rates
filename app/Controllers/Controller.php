<?php namespace ShipBob\WooRates\Controllers;

/**
 * @class Controller
 *
 * @description Abstracted controller class containing common controller methods
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */

use \ShipBob\WooRates\Kernel;

abstract class Controller {

	/**
	 * @var Kernel
	 */
	protected $kernel;

	/**
	 *  Create a new controller instance
	 *
	 * @param Kernel $kernel
	 *
	 * @return void
	 */
	public function __construct( Kernel $kernel ) {
		$this->kernel = $kernel;
	}

	/**
	 *  Dynamicallly retrieves components registered
	 *  with the kernel
	 *
	 * @return null|object
	 */
	public function __get( $key ) {
		return $this->kernel->$key;
	}

}
<?php namespace ShipBob\WooRates\Services;

/**
 * @class Service
 *
 * @description Abstract class for common service methods
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */

use \ShipBob\WooRates\Kernel;

abstract class Service {

	/**
	 * @var Kernel
	 */
	protected $kernel;

	/**
	 *  Create a new instance
	 *
	 * @param Kernel $kernel
	 *
	 * @return void
	 */
	public function __construct( Kernel $kernel ) {
		$this->kernel = $kernel;
	}

	/**
	 *   Recursively merges arrays
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return array
	 */
	public function mergeArrays( array $a, array $b ) {
		foreach ( $b as $bk => $bv ) {
			if ( array_key_exists( $bk, $a ) && is_array( $a[ $bk ] ) && is_array( $bv ) ) {
				$a[ $bk ] = $this->mergeArrays( $a[ $bk ], $bv );
			} else {
				$a[ $bk ] = $bv;
			}
		}

		return $a;
	}

	/**
	 *  Dynamically retrieves components from the kernel
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->kernel->$key;
	}

}
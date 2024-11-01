<?php namespace ShipBob\WooRates\Services;

/**
 * @class Autoloader
 *
 * @description Autoloads classes under the currenct namespace and app folder
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */

use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

class Autoloader {

	/**
	 *   Autoloads app classes
	 *
	 * @param string $cls
	 *
	 * @return void
	 */
	public static function loadClass( $cls ) {

		// Class already loaded, then skip
		if ( class_exists( $cls, false ) ) {
			return;
		}

		// Load the class namespace
		$segments  = explode( "\\", $cls );
		$classname = array_pop( $segments );
		$ns        = implode( '\\', $segments );

		// Load the current namespace
		$namespace = explode( '\\', __NAMESPACE__ );

		// Remove services to get base namespace
		array_pop( $namespace );

		// Requested class not part of base namespace, then skip
		if ( ! preg_match( '#^' . preg_quote( implode( '\\', $namespace ) . '\\', '#' ) . '?#ismu', $ns ) ) {
			return;
		}

		// Prepare the directory iterator
		$dir_iterator = new RecursiveDirectoryIterator( realpath( __DIR__ . '/..' ) );
		$iterator     = new RecursiveIteratorIterator( $dir_iterator, RecursiveIteratorIterator::SELF_FIRST );

		// Loop through and load matching class files
		foreach ( $iterator as $path ) {

			// Path is not a file, skip
			if ( ! $path->isFile() ) {
				continue;
			}

			// Load the path and file names
			$pathname = $path->getPathName();
			$filename = basename( $pathname );

			// Filename does not match expected class name, skip
			if ( ! preg_match( "#^" . preg_quote( $classname ) . "\.php$#", $filename ) ) {
				continue;
			}

			// Load the class file
			$pathname = str_replace( "\\", "/", $pathname );
			require_once( $pathname );

			// If class now exists, bail out of loader
			if ( class_exists( $cls, false ) ) {
				return;
			}

		}
	}

}
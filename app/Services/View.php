<?php namespace ShipBob\WooRates\Services;

/**
 * @class View
 *
 * @description Provides methods for rendering views
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class View extends Service {

	/**
	 *   Returns a rendered view with any given data passed
	 *
	 * @param string $view
	 * @param array $data
	 *
	 * @return string
	 */
	public function render( $view, $data = [] ) {

		// Determine the template path
		$path = apply_filters( $this->config->filter_prefix . 'view_path', $this->paths->templates( $view . '.php' ), $view );

		// No template found, bail
		if ( ! file_exists( $path ) ) {
			return;
		}

		// Load the view
		ob_start();
		extract( $data );
		include( $path );
		$output = ob_get_contents();
		ob_end_clean();

		// Return the rendered view
		return $output;

	}

}
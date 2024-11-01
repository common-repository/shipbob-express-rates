<?php namespace ShipBob\WooRates\Services;

/**
 * @class Assets
 *
 * @description Handles registration of assets
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class Assets extends Service {

	/**
	 * @var array
	 */
	protected $manifests = [];

	/**
	 * @var array
	 */
	protected $handles = [];

	/**
	 * @var array
	 */
	protected $script_attributes = [];

	/**
	 * @var array
	 */
	protected $tag_attributes = [];

	/**
	 *   Adds preset handles to assets to allow common
	 *   handle registration for 3rd party libraries
	 *
	 * @param array $handles
	 *
	 * @return void
	 */
	public function add_handles( array $handles ) {
		$this->handles = array_merge( $this->handles, $handles );
	}

	/**
	 *   Adds tag attributes for script and style tags keyed by handle
	 *
	 * @param array $handles
	 *
	 * @return void
	 */
	public function add_tag_attributes( array $handles ) {
		$this->tag_attributes = array_merge( $this->tag_attributes, $handles );
	}

	/**
	 *   Returns an asset handle to be used when
	 *   regsitering assets based on the path
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function handle( $path ) {

		$filename = basename( $path );

		if ( isset( $this->handles[ $filename ] ) ) {
			return $this->handles[ $filename ];
		}

		if ( preg_match( '#^(https?:)?//#ismu', $path ) ) {
			$path = "ext_" . basename( $path );
		}

		$handle = $this->config->prefix . preg_replace( '#[^a-z0-9_]#ismu', '_', $path );
		$handle = preg_replace( '#[_]{2,}#ismu', '_', $handle );
		$handle = preg_replace( '#_(css|js)$#ismu', '', $handle );

		return $handle;
	}

	/**
	 *  Enqueues assets
	 *
	 * @param string $action
	 * @param string $path
	 * @param array $dependencies
	 * @param string $uri_match
	 *
	 * @return void
	 */
	protected function enqueue( $action, $path, array $dependencies = [], $uri_match = '' ) {
		$handle = $this->handle( $path );
		$uri    = $this->request->uri();

		if ( $uri_match && ! preg_match( '#' . $uri_match . '#ismu', $uri ) ) {
			return;
		}

		if ( preg_match( "#^(https?:)?//#ismu", $path ) ) {
			return $action( $handle, $path, $dependencies, null );
		}

		$path = $this->revision( $path );

		return $action( $handle, $this->paths->plugin_url( $path ), $dependencies, null );
	}

	/**
	 *  Returns the manifest for an asset type
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	protected function manifest( $type ) {
		if ( isset( $this->manifests[ $type ] ) ) {
			return $this->manifests[ $type ];
		}

		$path = $this->paths->assets( $type ) . '/rev-manifest.json';

		if ( ! file_exists( $path ) ) {
			return [];
		}

		return $this->manifests[ $type ] = json_decode( file_get_contents( $path ), true );
	}

	/**
	 *  Returns a specific asset revision path
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function revision( $path ) {
		if ( ! preg_match( '#\.(css|js)#ismu', $path, $match ) ) {
			return $path;
		}

		$manifest = $this->manifest( $match[1] );
		$key      = basename( $path );

		if ( ! isset( $manifest[ $key ] ) ) {
			return $path;
		}

		return preg_replace( '#/[^/]+$#ismu', '/' . $manifest[ $key ], $path );
	}

	/**
	 *  Enqueues stylesheets
	 *
	 * @param string $path
	 * @param array $dependencies
	 * @param bool $admin
	 * @param string $uri_match
	 *
	 * @return void
	 */
	public function addCss( $path, array $dependencies = [], $admin = false, $uri_match = '' ) {
		if ( ! preg_match( '#/#ismu', $path ) ) {
			$path = 'assets/css/' . $path;
		}

		$cb = function () use ( $path, $dependencies, $uri_match ) {
			$this->enqueue( 'wp_enqueue_style', $path, $dependencies, $uri_match );
		};

		if ( $admin ) {
			add_action( 'admin_enqueue_scripts', $cb );
		} else {
			add_action( 'wp_enqueue_scripts', $cb );
		}

	}

	/**
	 *  Enqueues scripts
	 *
	 * @param string $path
	 * @param array $dependencies
	 * @param bool $admin
	 * @param string $uri_match
	 * @param string $local_name
	 * @param array $local_data
	 *
	 * @return void
	 */
	public function addScript( $path, array $dependencies = [], $admin = false, $uri_match = '', $local_name = '', array $local_data = [] ) {
		if ( ! preg_match( '#/#ismu', $path ) ) {
			$path = 'assets/js/' . $path;
		}

		// Set up the callback
		$cb = function () use ( $path, $dependencies, $uri_match ) {
			$this->enqueue( 'wp_enqueue_script', $path, $dependencies, $uri_match );
		};

		// Determine the appropriate action
		$action = $admin ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';

		// Enqueue the script
		add_action( $action, $cb );

		// Local name provided, add localized script to enqueue
		if ( $local_name ) {
			add_action( $action, function () use ( $path, $local_name, $local_data ) {
				$handle = $this->handle( $path );
				wp_localize_script( $handle, $local_name, $local_data );
			} );
		}

	}

	/**
	 *   Returns list of added attributes for a tag by handle
	 *
	 * @param string $handle
	 *
	 * @return string
	 */
	protected function get_tag_attributes( $handle ) {

		// No added attributes found, skip
		if ( ! isset( $this->tag_attributes[ $handle ] ) ) {
			return '';
		}

		// Prepare the attributes
		$attributes = $this->tag_attributes[ $handle ];

		if ( is_array( $attributes ) ) {

			array_walk( $attributes, function ( $v, $k ) {
				return $k . '="' . esc_attr( $v ) . '"';
			} );

			$attributes = implode( ' ', $attributes );
		}

		// Return the attributes
		return $attributes;
	}

	/**
	 *   Adds tag attributes to scripts on render
	 *
	 * @used-by add_filter( 'script_loader_tag', [ static::class, 'render_script_attributes' ], 10, 2 );
	 *
	 * @param string $tag
	 * @param string $handle
	 *
	 * @return void
	 */
	public function render_script_attributes( $tag, $handle ) {

		// Load the attributes
		$attributes = $this->get_tag_attributes( $handle );

		if ( ! $attributes ) {
			return $tag;
		}

		// Add the attributes and return the tag
		return str_replace( ' src=', ' ' . $attributes . ' src=', $tag );
	}

	/**
	 *   Adds tag attributes to scripts on render
	 *
	 * @used-by add_filter( 'style_loader_tag', [ static::class, 'render_style_attributes' ], 10, 2 );
	 *
	 * @param string $tag
	 * @param string $handle
	 *
	 * @return void
	 */
	public function render_style_attributes( $tag, $handle ) {

		// Load the attributes
		$attributes = $this->get_tag_attributes( $handle );

		if ( ! $attributes ) {
			return $tag;
		}

		// Add the attributes and return the tag
		return str_replace( ' href=', ' ' . $attributes . ' href=', $tag );
	}

}
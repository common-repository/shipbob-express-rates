<?php namespace ShipBob\WooRates;

/**
 * @class Kernel
 *
 * @description Registers all components for the plugin
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class Kernel {

	/**
	 * @var Kernel
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected $_namespaces = [];

	/**
	 * @var array
	 */
	protected $_controllers = [];

	/**
	 * @var array
	 */
	protected $_services = [];

	/**
	 * @var array
	 */
	protected $_libraries = [];

	/**
	 * @var string
	 */
	protected $_namespace = __NAMESPACE__;

	/**
	 *  Create an new instance
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	public function __construct( array $params = [] ) {
		if ( static::$instance ) {
			return;
		}

		// Prepare autoloaders
		$this->registerAutoLoaders();

		// Register namespaces to lazy load
		$this->_namespaces = [
			$this->namespace,
			$this->namespace . '\\Controllers',
			$this->namespace . '\\Services',
			$this->namespace . '\\Api',
		];

		if ( isset( $params['namespaces'] ) && is_array( $params['namespaces'] ) ) {
			$this->_namespaces = array_merge(
				array_map( 'strval', $params['namespaces'] ),
				$this->_namespaces
			);
		}

		// Register activation hooks
		$this->install->register();

		// Load and initialize core controller
		$this->core = new Controllers\PluginController( $this );

		// Prepare plugin
		add_action( 'plugins_loaded', [ $this->core, 'preload' ] );
		add_action( 'init', [ $this->core, 'init' ] );

		static::$instance = $this;
	}

	/**
	 *  Registers the vendor and app autoloaders
	 *
	 * @return void
	 */
	protected function registerAutoLoaders() {
		$vendor_path = realpath( __DIR__ . '/../vendor/autoload.php' );

		if ( $vendor_path ) {
			require_once( $vendor_path );
		}

		$autoloader_path = realpath( __DIR__ . '/Services/Autoloader.php' );

		if ( $autoloader_path ) {
			require_once( $autoloader_path );
			spl_autoload_register( [ Services\Autoloader::class, 'loadClass' ] );
		}
	}

	/**
	 *  Dynamically sets controllers, services, and libraries
	 *
	 * @return void
	 */
	public function __set( $key, $value ) {
		if ( $value instanceof Controllers\Controller ) {
			return $this->_controllers[ $key ] = $value;
		}
		if ( $value instanceof Services\Service ) {
			return $this->_services[ $key ] = $value;
		}
		if ( $value instanceof Api\HttpClient ) {
			return $this->api->$key = $value;
		}
		if ( is_object( $value ) ) {
			return $this->_libraries[ $key ] = $value;
		}
	}

	/**
	 *  Dynamically retrieve controllers, services, and libraries. Will attempt
	 *  to lazy autoload items if not loaded.
	 *
	 * @return null|object
	 */
	public function __get( $key ) {

		// Load the api
		if ( $key === 'api' ) {
			return $this->api = new class( $this ) extends Services\Service {
				public function __get( $key ) {
					return $this->kernel->lazy_load( $key, [ __NAMESPACE__ . '\\Api' ] );
				}
			};
		}

		// Load controller if found
		if ( array_key_exists( $key, $this->_controllers ) ) {
			return $this->_controllers[ $key ];
		}

		// Load service if found
		if ( array_key_exists( $key, $this->_services ) ) {
			return $this->_services[ $key ];
		}

		// Load library if found
		if ( array_key_exists( $key, $this->_libraries ) ) {
			return $this->_libraries[ $key ];
		}

		// Load property if found
		if ( property_exists( $this, '_' . $key ) ) {
			return $this->{'_' . $key};
		}

		// Attempt to lazy load a class
		return $this->lazy_load( $key, $this->_namespaces );
	}

	/**
	 *   Will attempt to lazy load a class if requested
	 *
	 * @param string $key
	 * @param array $namespaces
	 *
	 * @return null|object
	 */
	public function lazy_load( $key, $namespaces = [] ) {

		// Determine the namespaces to check
		$namespaces = array_filter( $namespaces, function ( $ns ) use ( $key ) {
			if ( $ns === $this->namespace . '\\Controllers' && isset( $this->_controllers[ $key ] ) ) {
				return false;
			}
			if ( $ns === $this->namespace . '\\Services' && isset( $this->_services[ $key ] ) ) {
				return false;
			}
			if ( $ns === $this->namespace . '\\Api' && isset( $this->_services['api']->$key ) ) {
				return false;
			}

			return in_array( $ns, $this->_namespaces );
		} );

		// Loop through register namespaces
		foreach ( $namespaces as $ns ) {

			// Attempt to lazy load class if found
			$class_name = preg_split( '#[^a-z]+#ismu', strtolower( $key ) );

			$class_name = implode( '', array_map( function ( $word ) {
				return ucwords( $word );
			}, $class_name ) );

			// Load any service or library
			$class_name = $ns . '\\' . $class_name;

			if ( class_exists( $class_name ) ) {
				if ( $ns === $this->namespace . '\\Api' ) {
					return $this->api->$key = new $class_name( $this );
				} else {
					return $this->$key = new $class_name( $this );
				}
			}

			// Load any controller
			$class_name .= 'Controller';

			if ( class_exists( $class_name ) ) {
				return $this->$key = new $class_name( $this );
			}

		}

	}

	/**
	 *  Returns the current plugin kernel instance
	 *
	 * @param array $params
	 *
	 * @return Kernel
	 */
	public static function getInstance( array $params = [] ) {
		if ( static::$instance ) {
			return static::$instance;
		}

		return new static( $params );
	}

}
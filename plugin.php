<?php namespace ShipBob\WooRates;
/**
 * Plugin Name: ShipBob Express Rates
 * Plugin URI: https://www.shipbob.com/
 * Description: This will allow you to offer faster and inexpensive shipping options by enabling real time rates and more at your checkout page. Requirement: Must be a ShipBob user.
 * Version: 2.7.0
 * Author: ShipBob, Inc.
 * Author URI: https://www.shipbob.com/
 * Contributors: ModeEffect
 * Text Domain: shipbob-express-rates
 * Domain Path: /lang/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package ShipBobWooRates
 */

if ( ! function_exists( 'add_action' ) ) {
    exit;
}

// Check for PHP requirement early
if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
    add_action( is_network_admin() ? 'network_admin_notices' : 'admin_notices', function () {
        ?>
        <div class="error">
            <p>
                <strong>
                    <?php _e( 'ShipBob Express Rates was deactivated. PHP version requirement of 7+ not met. Please upgrade your server PHP version to use this plugin.', 'shipbob-express-rates' ); ?>
                </strong>
            </p>
        </div>
        <?php
    } );
    add_action( 'admin_init', function () {
        return deactivate_plugins( __FILE__ );
    } );

    return;
}

// Load the plugin
require_once( 'app/Kernel.php' );
$WooShipBob = Kernel::getInstance();

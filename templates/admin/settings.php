<?php
/**
*   Renders the plugin settings page
*
*   @var string $title
*   @var string $group
*   @var string $slug
*   @var string $login_url
*   @var string $plugin_url
*/
?>
<div class="wrap shipbob settings bootstrap font-awesome">
    <h1><i class="fas fa-truck"></i> <?php echo esc_html( $title ); ?></h1>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#" role="tab" data-toggle="settings">Settings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" role="tab" data-toggle="help">Help</a>
        </li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane settings fade show active" role="tabpanel">

            <form method="post" action="<?php echo esc_attr( admin_url( 'options.php' ) ); ?>">
                <?php settings_fields( $group ); ?>

                <h2><?php _e( 'Connect to ShipBob', 'shipbob-express-rates' ); ?></h2>

                <a href="<?php echo esc_attr($login_url); ?>" class="btn btn-primary mt-2" target="_blank"><?php _e( 'Take me to ShipBob', 'shipbob-express-rates' ); ?></a>

                <p class="text-muted">
                    <small><?php _e('This will redirect you to your ShipBob dashboard.', 'shipbob-express-rates' ); ?></small>
                </p>

                <p>
                    <?php _e('Consumers want fast shipping. Luckily affordable 2 Day shipping is attainable. ShipBob Express allows merchants dynamically provide an affordable two-day ground shipping option for customers who qualify for it.','shipbob-express-rates'); ?><br /><br />
                    <?php echo sprintf(__('Must be a ShipBob user. Not a ShipBob user yet? We got your covered, %ssign up here%s! ','shipbob-express-rates'),'<a href="https://web.shipbob.com/app/merchant/#/SignUp">','</a>'); ?>
                </p>

            </form>

        </div>

        <div class="tab-pane help fade" role="tabpanel">

            <p>
                <?php _e('Based on which fulfillment centers your inventory is in, you will be able to use our technology to add a 2 Day Shipping option to your check out for clients in zip codes where we know we can get to in 2 Days at lower rates. This allows us to offer a 2 Day option that is cheaper than the regular 2 Day services.','shipbob-express-rates'); ?>
            </p>

            <p>
                <?php _e('If the customer’s zip code is within the area of guaranteed 2-day shipping, and the closest fulfillment center has inventory, the customer will see the customized 2-day rate. This lets sellers dynamically provide an affordable two-day ground shipping option for those who qualify while hiding it from customers who are outside of the guaranteed two-day coverage.','shipbob-express-rates'); ?><br /><br />
                <?php echo sprintf(__('Learn more about 2-Day Shipping %shere%s.','shipbob-express-rates'),'<a href="https://www.shipbob.com/blog/guide-offering-affordable-2-day-shipping/?utm_source=app&utm_campaign=dash1115">','</a>'); ?>
            </p>

            <h2 class="mt-5"><?php _e( 'How Does ShipBob\'s 2-Day Express work?', 'shipbob-express-rates' ); ?></h2>

            <p class="mt-3">
                <?php _e('ShipBob owns and operates a network of fulfillment centers to help you reach your customers across the United States more efficiently. You can distribute your inventory across the ShipBob fulfillment centers that are closest to your customers to get your products from point A to point B more quickly and affordably.','shipbob-express-rates'); ?>
            </p>

            <p>
                <?php _e('When a customer checks out on your store and enters their shipping destination, ShipBob verifies the delivery zip code and inventory on-hand at each fulfillment center to display the delivery option in real-time.','shipbob-express-rates'); ?>
            </p>

            <p>
                <?php _e('ShipBob has fulfillment centers in Chicago, New York City, Los Angeles, San Francisco, and Dallas. By storing your inventory in major cities near your customers, you can reduce the distance traveled and time in transit.','shipbob-express-rates'); ?>
            </p>

            <h2 class="mt-5"><?php _e( 'Frequently Asked Questions', 'shipbob-express-rates' ); ?></h2>

            <p class="mt-3">
                <em><?php _e('Can I use this plugin without using ShipBob Fulfillment?','shipbob-express-rates'); ?></em><br />
                <?php echo sprintf(__('ShipBob Express service is exclusive for ShipBob customers. To sign up, %sclick here%s!','shipbob-express-rates'),'<a href="https://web.shipbob.com/app/merchant/#/SignUp">','</a>'); ?>
            </p>

            <p>
                <em><?php _e('How do I contact support?','shipbob-express-rates'); ?></em><br />
                <?php echo sprintf(__('You can contact ShipBob support here at %ssupport@shipbob.com%s','shipbob-express-rates'),'<a href="mailto:mailto:support@shipbob.com">','</a>'); ?>
            </p>

            <p>
                <em><?php _e('Can I adjust the shipping rates and descriptions on the checkout page?','shipbob-express-rates'); ?></em><br />
                <?php _e('The shipping options and rates displayed on the checkout page can be customized based on your requirements. Please contact support to complete setup or update your current settings.','shipbob-express-rates'); ?>
            </p>

            <p>
                <em><?php _e('What version of WooCommerce do I need to use this plugin?','shipbob-express-rates'); ?></em><br />
                <?php _e('We currently support WooCommerce version 3.0 or later for this plugin. It has been tested up to version 3.5 as of the writing of this text. PHP 7+ is also a requirement for this plugin. Please upgrade you server before installing if running an older version of PHP. Check the change log for updates going forward.','shipbob-express-rates'); ?>
            </p>

            <p>
                <em><?php _e('What will my customers see at the checkout? ','shipbob-express-rates'); ?></em><br />
                <?php _e('Once the initial setup is complete and the plugin is active, a 2-Day eligible zip code at checkout will look like - ','shipbob-express-rates'); ?>
            </p>

            <p>
                <img src="<?php esc_attr_e($plugin_url.'/screenshot-2.png'); ?>" alt="" />
            </p>

            <p>
                <em><?php _e('How can I deactivate this service?','shipbob-express-rates'); ?></em><br />
                <?php _e('You can easily deactivate this service by removing the added shipping methods from your WooCommerce settings or by deactivating the plugin in your plugin settings.','shipbob-express-rates'); ?>
            </p>

        </div>

    </div>


</div>
<?php
/**
*   Renders the plugin terms and condition view
*
*   @var string $title
*   @var string $group
*   @var string $slug
*   @var bool $terms_accepted
*
*/
?>
<div class="wrap shipbob terms bootstrap font-awesome">
    <h1><i class="fas fa-truck"></i> <?php echo esc_html( $title ); ?></h1>

    <form method="post" action="<?php echo esc_attr( admin_url( 'options.php' ) ); ?>">
        <?php settings_fields( $group ); ?>

        <div class="row mt-3">
            <div class="col">
                <h2><?php esc_html_e('Acceptance of Terms','shipbob-express-rates'); ?></h2>
                <p>
                    <?php esc_html_e(
                        'For any Vendor that elects to use Express Shipping Option (as defined below), these Express Fulfillment Terms & Conditions (these "EF T&C") are deemed to be part of, and are hereby incorporated into, the TOS. In the event of any conflict or inconsistency between the terms and provision of these EF T&C and those of the TOS, the terms and provisions of these EF T&C will control. All capitalized terms used in these EF T&C that are not defined in these EF T&C shall have the meaning given to such terms in the TOS.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
                <p>
                    <?php esc_html_e(
                        'By electing the Express Shipping Option, you acknowledge that you have selected the Express Shipping Option and you have read, understood, and agree to be bound by these EF T&C.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <h2><?php esc_html_e('Express Shipping Option','shipbob-express-rates'); ?></h2>
                <p>
                    <?php esc_html_e(
                        'ShipBob provides an Express Shipping Option in qualifying geographic areas.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
                <p>
                    <?php esc_html_e(
                        'The "Express Shipping Option" is defined as a shipment with a transit time of two business days once fulfilled by ShipBob, with a cutoff time of 12:00 PM local time Monday through Friday.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
                <p>
                    <?php esc_html_e(
                        'The Express Shipping Option is only available in select zip codes in the United States, the list of which is available upon request. ShipBob reserves the right to modify such list of qualifying zip codes in its sole discretion and shall not be required to provide you with notice of any such modifications. The qualifying zip codes may also fluctuate in connection with the amount of your on-hand inventory.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
                <p>
                    <?php esc_html_e(
                        'ShipBob will be responsible for processing and routing shipments within the time frame set forth above.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <h2><?php esc_html_e('Express Shipping Option Fee','shipbob-express-rates'); ?></h2>
                <p>
                    <?php esc_html_e(
                        'ShipBob provides an Express Shipping Option in qualifying geographic areas.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
                <p>
                    <?php esc_html_e(
                        'Upon receipt of Vendor\'s selection of the Express Shipping Option, ShipBob will advise Vendor of the fee (the "Express Shipping Option Fee") for such service, and Vendor shall pay the Express Shipping Option Fee.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
                <p>
                    <?php esc_html_e(
                        'The Express Shipping Option Fee shall be considered a Service Fee.',
                        'shipbob-express-rates'
                    ); ?>
                </p>
            </div>
        </div>

        <div class="form-group mt-3">

            <div class="form-check form-check-inline">
                <input
                    type="checkbox"
                    class="mr-2"
                    id="<?php echo esc_attr($slug.'terms_accepted'); ?>"
                    name="<?php echo esc_attr($slug.'terms_accepted'); ?>"
                    value="1"
                    <?php checked($terms_accepted); ?>
                />
                <label class="form-check-label agree-text" for="<?php echo esc_attr($slug.'terms_accepted'); ?>">
                    <?php _e( 'I accept the terms and conditions', 'shipbob-express-rates' ); ?>
                </label>
            </div>

        </div>

        <p class="submit">
            <input type="submit" class="btn btn-primary" value="<?php esc_attr_e( 'Continue' ); ?>" <?php disabled($terms_accepted,false); ?> />
        </p>

    </form>

</div>
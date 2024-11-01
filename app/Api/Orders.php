<?php namespace ShipBob\WooRates\Api;

/**
 * @class Orders
 *
 * @description Handles methods for sending completed orders the ShipBob API
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class Orders extends HttpClient {

    /**
     *   Sends request for a completed order to api
     *
     * @param integer $order_id
     *
     * @return string
     */
    public function save_converted( $order_id ) {
        return $this->post( 'SaveConvertedOrder', [
            'orderId'  => $order_id,
        ] );
    }

}
<?php namespace ShipBob\WooRates\Api;

/**
 * @class Rates
 *
 * @description Handles methods for fetching rates from the ShipBob API
 *
 * @author Mode Effect
 *
 * @package ShipBobWooRates
 *
 * @license GPL-2.0+
 */
class Rates extends HttpClient {

	/**
	 *   Sends request for shipping rates quote
	 *
	 * @param array $destination
	 * @param array $items
	 *
	 * @return array
	 */
	public function get_quote( array $destination, array $items ) {
		// Prepare the destination adding defaults
		$destination = array_merge( [
			'country'      => 'US',
			'postal_code'  => null,
			'province'     => null,
			'city'         => null,
			'name'         => null,
			'address1'     => null,
			'address2'     => null,
			'address3'     => null,
			'phone'        => null,
			'fax'          => null,
			'email'        => null,
			'address_type' => null, // residential or commercial?
			'company_name' => null,
		], $destination );

		// Prepare the list of items loading defaults
		$items = array_map( function ( $item ) {
			return array_merge( [
				'name'                => null,
				'sku'                 => null,
				'quantity'            => null,
				'grams'               => null, // integer
				'price'               => null,
				'vendor'              => null, // string name
				'requires_shipping'   => true, // boolean, optional
				'taxable'             => true, // boolean, optional
				'fulfillment_service' => 'manual', // optional
				'properties'          => null, // optional
				'product_id'          => null,
				'variant_id'          => null,
				'variant_sku'         => null,
			], $item );
		}, $items );

		// Fetch the list of rates for the package
		$response = $this->post( 'ProcessCarrierServices', [
			'rate' => [
				'destination' => $destination,
				'items'       => $items,
			],
		] );

		// Return the rates array
		return is_object( $response ) ? ( $response->rates ?? [] ) : [];
	}

}
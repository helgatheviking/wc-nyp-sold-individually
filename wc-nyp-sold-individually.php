<?php
/**
 * Plugin Name: WooCommerce Name Your Price - Sold Individually
 * Plugin URI: https://github.com/kathyisawesome/wc-nyp-sold-individually
 * Description: Stricter enforcement of "Sold Individually" for NYP items
 * Version: 1.0.0
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 * WC requires at least: 4.0.0
 * WC tested up to: 4.1.0
 *
 * Copyright: © 2020 Kathy Darling
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_NYP_Sold_Individually {

	/**
	 * Fire in the hole!
	 */
	public static function init() {
		if( class_exists( 'WC_Name_Your_Price_Helpers' ) ) {
			add_filter( 'woocommerce_cart_id', array( __CLASS__, 'force_sold_individually' ), 10, 5 );
		}
	}

	/**
	 * Regenerate a unique ID for the cart item being added.
	 *
	 * @param string cart item key.
	 * @param int   $product_id - id of the product the key is being generated for.
	 * @param int   $variation_id of the product the key is being generated for.
	 * @param array $variation data for the cart item.
	 * @param array $cart_item_data other cart item data passed which affects this items uniqueness in the cart.
	 * @return string 
	 */
	public static function force_sold_individually( $cart_id, $product_id, $variation_id, $variation, $cart_item_data ) {

		// Get the product.
		$product = wc_get_product( $variation_id ? $variation_id : $product_id );

		if ( $product->is_sold_individually() && WC_Name_Your_Price_Helpers::is_nyp( $product ) ){

			$id_parts = array( $product_id );

			if ( $variation_id && 0 != $variation_id ) {
				$id_parts[] = $variation_id;
			}

			if ( is_array( $variation ) && ! empty( $variation ) ) {
				$variation_key = '';
				foreach ( $variation as $key => $value ) {
					$variation_key .= trim( $key ) . trim( $value );
				}
				$id_parts[] = $variation_key;
			}

			$cart_id = md5( implode( '_', $id_parts ) );

		}

		return $cart_id;
	}


} // End Class.
add_action( 'plugins_loaded', array( 'WC_NYP_Sold_Individually', 'init' ), 20 );
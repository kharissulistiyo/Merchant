<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function merchant_animated_add_to_cart_body_class( $classes ) {

	if ( Merchant_Modules::is_module_active( 'animated-add-to-cart' ) ) {
		$animation = Merchant_Admin_Options::get( 'animated-add-to-cart', 'animation', 'flash' );
		$classes[] = 'merchant-animated-add-to-cart merchant-animated-add-to-cart-' . esc_attr( $animation );
	}	

	return $classes;

}
add_action( 'body_class', 'merchant_animated_add_to_cart_body_class' );

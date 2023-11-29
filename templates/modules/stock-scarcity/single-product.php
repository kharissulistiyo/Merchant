<?php
/**
 * Template for stock scarcity module content on single product.
 *
 * @var $args array template args
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$settings = isset( $args['settings'] ) ? $args['settings'] : array();
?>
<div class="merchant-stock-scarcity">
    <div class="merchant-stock-scarcity-message">
		<?php 
		$low_inventory_text = isset( $settings['low_inventory_text'] ) ? $settings['low_inventory_text'] : '';

		if ( $args['stock'] > 1 ) {
			$low_inventory_text = isset( $settings['low_inventory_text_plural'] ) ? $settings['low_inventory_text_plural'] : '';
		}

		echo ! empty( $low_inventory_text )
			? esc_html( str_replace(
				'{stock}',
				$args['stock'],
				$low_inventory_text
			) )
			: esc_html( 
				/* Translators: 1. Quantity of units */
				sprintf( _n( 'Hurry! Only %s unit left in stock!', 'Hurry! Only %s units left in stock!', $args['stock'], 'merchant' ), $args['stock'] )
			); ?>
    </div>
    <div class="merchant-stock-scarcity-content">
        <div class="merchant-stock-scarcity-progress-bar" style="width: <?php echo esc_attr( $args['percentage'] ) . '%'; ?>"></div>
    </div>
</div>

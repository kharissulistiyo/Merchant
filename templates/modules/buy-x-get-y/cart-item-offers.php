<?php
/**
 * Template for buy x get y module content on cart page.
 *
 * @var $args array template args
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! empty( $args['offers'] ) ) : ?>
	<?php
	foreach ( $args['offers'] as $offer_key => $offer ) :
		if ( $args['quantity'] >= $offer['min_quantity'] ) {
			continue;
		}
		$customer_get_product = wc_get_product( $offer['customer_get_product_ids'] );
		if ( $customer_get_product ) {
			$variations = array();
			if ( $customer_get_product->is_type( 'variable' ) ) {
				foreach ( $customer_get_product->get_variation_attributes() as $attribute => $terms ) {
					$attribute_label = wc_attribute_label( $attribute );
					$variations[]    = array(
						/* Translators: 1. Attribute label */
						'default'   => sprintf( __( 'Select %s', 'merchant' ), $attribute_label ),
						'label'     => $attribute_label,
						'attribute' => $attribute,
						'terms'     => array_map( static function ( $term ) {
							return array(
								'value' => $term,
								'label' => ucfirst( $term ),
							);
						}, $terms ),
					);
				}
			}
			?>
            <div class="bogo-cart-item-offer__container<?php
			echo $customer_get_product->is_type( 'variable' ) ? ' is-variable' : '' ?>" data-variations="<?php
			echo esc_attr( wp_json_encode( $variations ) ) ?>">
                <div class="bogo-cart-item-offer__row">
                    <div class="product_image column column_1">
                        <a href="<?php
						echo esc_url( $customer_get_product->get_permalink() ) ?>" title="<?php
						echo esc_attr( $customer_get_product->get_name() ); ?>"><?php
							echo wp_kses_post( $customer_get_product->get_image( 'medium' ) );
							?></a>
                    </div>
                    <div class="product-details column column_4">
                        <div class="title"><a href="<?php
							echo esc_url( $customer_get_product->get_permalink() ) ?>" title="<?php
							echo esc_attr( $customer_get_product->get_name() ); ?>"><?php
								echo esc_html( $customer_get_product->get_name() ); ?></a></div>
                        <div class="description"><?php
							$extra_quantity = $offer['min_quantity'] - $args['quantity'];
							$offer_quantity = $offer['quantity'];
							$discount       = $offer['discount_type'] === 'percentage'
								? $offer['discount'] . '%'
								: wc_price( $offer['discount'] );
							printf(
							// translators: %1$s: min quantity, %2$s: offer quantity
								esc_html__( 'Buy %1$s Extra, Get %2$s at a %3$s Discount!', 'merchant' ),
								esc_html( $extra_quantity ),
								esc_html( $offer_quantity ),
								wp_kses( $discount, merchant_kses_allowed_tags( array( 'bdi' ) ) )
							);
							?></div>
                    </div>
                    <div class="add-to-cart column column_1">
                        <button class="button add-to-cart-button alt" disabled data-offer-key="<?php
						echo esc_attr( $offer_key ) ?>" type="button" data-offer-product-id="<?php
						echo esc_attr( $customer_get_product->get_id() ); ?>" data-quantity="1" data-cart-item-key="<?php
						echo esc_attr( $args['cart_item_key'] ) ?>" data-nonce="<?php
						echo esc_html( wp_create_nonce() ) ?>"><?php
							echo esc_html( $offer['button_text'] ) ?></button>
                    </div>
                </div>
            </div>
			<?php
		}
	endforeach; ?>
<?php
endif;
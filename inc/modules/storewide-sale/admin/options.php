<?php

/**
 * Frequently Bought Together Options.
 *
 * @package Merchant
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

Merchant_Admin_Options::create( array(
	'title'  => esc_html__( 'Bundle Offers', 'merchant' ),
	'module' => Merchant_Storewide_Sale::MODULE_ID,
	'fields' => array(
		array(
			'id'           => 'offers',
			'type'         => 'flexible_content',
			'button_label' => esc_html__( 'Add New Campaign', 'merchant' ),
			'style'        => Merchant_Storewide_Sale::MODULE_ID . '-style default',
			'sorting'      => true,
			'accordion'    => true,
			'layouts'      => array(
				'offer-details' => array(
					'title'       => esc_html__( 'Storewide Discount Campaign', 'merchant' ),
					'title-field' => 'offer-title', // text field ID to use as title for the layout
					'fields'      => array(
						array(
							'id'      => 'offer-title',
							'type'    => 'text',
							'title'   => esc_html__( 'Offer name', 'merchant' ),
							'default' => esc_html__( 'Storewide Discount Campaign', 'merchant' ),
						),
						array(
							'id'      => 'rules_to_display',
							'type'    => 'select',
							'title'   => esc_html__( 'Trigger', 'merchant' ),
							'options' => array(
								'all'        => esc_html__( 'Any product', 'merchant' ),
								'products'   => esc_html__( 'Specific products', 'merchant' ),
								'categories' => esc_html__( 'Product categories', 'merchant' ),
							),
							'default' => 'all',
						),
						array(
							'id'        => 'product_to_display',
							'type'      => 'products_selector',
							//'title'    => esc_html__( 'Select a product', 'merchant' ),
							'multiple'  => true,
							'desc'      => esc_html__( 'Select the product(s) included in this discount campaign.', 'merchant' ),
							'condition' => array( 'rules_to_display', '==', 'products' ),
						),
						array(
							'id'          => 'category_slugs',
							'type'        => 'select_ajax',
							'title'       => esc_html__( 'Categories', 'merchant' ),
							'source'      => 'options',
							'multiple'    => true,
							'options'     => Merchant_Admin_Options::get_category_select2_choices(),
							'placeholder' => esc_html__( 'Select categories', 'merchant' ),
							'desc'        => esc_html__( 'Select the product categories that will show the offer.', 'merchant' ),
							'condition'   => array( 'rules_to_display', '==', 'categories' ),
						),
						array(
							'id'      => 'discount_type',
							'type'    => 'radio',
							'title'   => esc_html__( 'Discount', 'merchant' ),
							'options' => array(
								'percentage_discount' => esc_html__( 'Percentage', 'merchant' ),
								'fixed_discount'      => esc_html__( 'Fixed', 'merchant' ),
							),
							'default' => 'percentage',
						),
						array(
							'id'      => 'discount_value',
							'type'    => 'number',
							'default' => 10,
						),

						array(
							'id'      => 'user_condition',
							'type'    => 'select',
							'title'   => esc_html__( 'User Condition', 'merchant' ),
							'options' => array(
								'all'       => esc_html__( 'All Users', 'merchant' ),
								'customers' => esc_html__( 'Selected Users', 'merchant' ),
								'roles'     => esc_html__( 'Selected Roles', 'merchant' ),
							),
							'default' => 'all',
						),

						array(
							'id'        => 'user_condition_roles',
							'type'      => 'select_ajax',
							'title'     => esc_html__( 'User Roles', 'merchant' ),
							'desc'      => esc_html__( 'This will limit the offer to users with these roles.', 'merchant' ),
							'source'    => 'options',
							'multiple'  => true,
							'classes'   => array( 'flex-grow' ),
							'options'   => Merchant_Admin_Options::get_user_roles_select2_choices(),
							'condition' => array( 'user_condition', '==', 'roles' ),
						),

						array(
							'id'        => 'user_condition_users',
							'type'      => 'select_ajax',
							'title'     => esc_html__( 'Users', 'merchant' ),
							'desc'      => esc_html__( 'This will limit the offer to the selected customers.', 'merchant' ),
							'source'    => 'user',
							'multiple'  => true,
							'classes'   => array( 'flex-grow' ),
							'condition' => array( 'user_condition', '==', 'customers' ),
						),

						array(
							'id'      => 'availability',
							'type'    => 'radio',
							'title'   => esc_html__( 'Availability', 'merchant' ),
							'options' => array(
								'always'   => esc_html__( 'Always available', 'merchant' ),
								'specific' => esc_html__( 'Specific date', 'merchant' ),
							),
							'default' => 'always',
						),
						array(
							'id'        => 'start_date',
							'type'      => 'date_time',
							'title'     => esc_html__( 'Start at', 'merchant' ),
							'condition' => array( 'availability', '==', 'specific' ),
						),
						array(
							'id'        => 'end_date',
							'type'      => 'date_time',
							'title'     => esc_html__( 'Ends at', 'merchant' ),
							'condition' => array( 'availability', '==', 'specific' ),
						),
					),
				),
			),
			'default'      => array(
				array(
					'layout'         => 'offer-details',
					'min_quantity'   => 2,
					'discount'       => 10,
					'discount_type'  => 'percentage_discount',
					'availability'   => 'always',
					'user_condition' => 'all',
				),
			),
		),
		array(
			'id'          => 'helping_instructions',
			'type'        => 'info_block',
			'description' => esc_html__( 'You can display a sale badge on products eligible for this discount by using Merchant’s Product Labels module.', 'merchant'),
			'button_text' => esc_html__( 'View Product Labels', 'merchant'),
			'button_link' => esc_url( admin_url( 'admin.php?page=merchant&module=product-labels' ) ),
		),
	),
) );
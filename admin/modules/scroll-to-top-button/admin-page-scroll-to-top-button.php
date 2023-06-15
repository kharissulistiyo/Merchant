<?php
/**
 * Merchant - Scroll to Top Button
 */

/**
 * Settings for Desktop
 */
Merchant_Admin_Options::create( array(
	'module'    => 'scroll-to-top-button',
	'title'     => 'Settings for Desktop',
	'fields'    => array(

		array(
			'id'      => 'icon',
			'type'    => 'choices',
			'title'   => esc_html__( 'Icon', 'merchant' ),
			'options' => array(
				'arrow-1' => '%s/arrow-1.svg',
				'arrow-2' => '%s/arrow-2.svg',
				'arrow-3' => '%s/arrow-3.svg',
				'arrow-4' => '%s/arrow-4.svg',
			),
			'default' => 'arrow-1',
		),

		array(
			'id'      => 'type',
			'type'    => 'buttons',
			'title'   => esc_html__( 'Type', 'merchant' ),
			'options' => array(
				'icon'      => esc_html__( 'Icon', 'merchant' ),
				'text+icon' => esc_html__( 'Text + Icon', 'merchant' ),
			),
			'default' => 'icon',
		),

		array(
			'id'        => 'text',
			'type'      => 'text',
			'title'     => 'Text',
			'default'   => esc_html__( 'Back to top', 'merchant' ),
			'condition' => array( 'type', '==', 'text+icon' ),
		),

		array(
			'id'      => 'border-radius',
			'type'    => 'range',
			'title'   => esc_html__( 'Border Radius', 'merchant' ),
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'unit'    => 'px',
			'default' => 30,
		),

		array(
			'id'      => 'position',
			'type'    => 'buttons',
			'title'   => esc_html__( 'Position', 'merchant' ),
			'options' => array(
				'left'  => 'Left',
				'right' => 'Right',
			),
			'default' => 'right',
		),

		array(
			'id'      => 'side-offset',
			'type'    => 'range',
			'title'   => esc_html__( 'Side Offset', 'merchant' ),
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'unit'    => 'px',
			'default' => 30,
		),

		array(
			'id'      => 'bottom-offset',
			'type'    => 'range',
			'title'   => esc_html__( 'Bottom Offset', 'merchant' ),
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'unit'    => 'px',
			'default' => 30,
		),

		array(
			'id'      => 'visibility',
			'type'    => 'select',
			'title'   => esc_html__( 'Visibility', 'merchant' ),
			'options' => array(
				'all'          => esc_html__( 'Show on all devices', 'merchant' ),
				'desktop-only' => esc_html__( 'Desktop Only', 'merchant' ),
				'mobile-only'  => esc_html__( 'Mobile Only', 'merchant' ),
			),
		),

	),
) );

/**
 * Settings for Mobile
 */
Merchant_Admin_Options::create( array(
	'module'    => 'scroll-to-top-button',
	'title'     => 'Settings for Mobile',
	'fields'    => array(

		array(
			'id'      => 'side-offset-mobile',
			'type'    => 'range',
			'title'   => esc_html__( 'Side Offset', 'merchant' ),
			'min'     => 1,
			'max'     => 50,
			'step'    => 1,
			'unit'    => 'px',
			'default' => 30,
		),

		array(
			'id'      => 'bottom-offset-mobile',
			'type'    => 'range',
			'title'   => esc_html__( 'Bottom Offset', 'merchant' ),
			'min'     => 1,
			'max'     => 50,
			'step'    => 1,
			'unit'    => 'px',
			'default' => 30,
		),

	),
) );

/**
 * Style Settings
 */
Merchant_Admin_Options::create( array(
	'module'    => 'scroll-to-top-button',
	'title'     => esc_html__( 'Style Settings', 'merchant' ),
	'fields'    => array(

		array(
			'id'      => 'icon-size',
			'type'    => 'range',
			'title'   => 'Icon Size',
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'unit'    => 'px',
			'default' => 18,
		),

		array(
			'id'      => 'padding',
			'type'    => 'range',
			'title'   => esc_html__( 'Padding', 'merchant' ),
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'unit'    => 'px',
			'default' => 15,
		),

		array(
			'id'      => 'icon-color',
			'type'    => 'color',
			'title'   => esc_html__( 'Icon Color', 'merchant' ),
			'default' => '#FFFFFF',
		),

		array(
			'id'      => 'icon-hover-color',
			'type'    => 'color',
			'title'   => esc_html__( 'Icon Hover Color', 'merchant' ),
			'default' => '#FFFFFF',
		),

		array(
			'id'      => 'background-color',
			'type'    => 'color',
			'title'   => esc_html__( 'Background Color', 'merchant' ),
			'default' => '#212121',
		),

		array(
			'id'      => 'background-hover-color',
			'type'    => 'color',
			'title'   => esc_html__( 'Background Hover Color', 'merchant' ),
			'default' => '#757575',
		),

	),
) );

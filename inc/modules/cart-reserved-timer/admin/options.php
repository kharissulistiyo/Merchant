<?php

/**
 * Cart Reserved Timer Options.
 *
 * @package Merchant
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Variables
$icon_path = MERCHANT_URI . 'assets/images/icons/' . Merchant_Cart_Reserved_timer::MODULE_ID;

// Settings
Merchant_Admin_Options::create( array(
	'title'  => esc_html__( 'Settings', 'merchant' ),
	'module' => Merchant_Cart_Reserved_timer::MODULE_ID,
	'fields' => array(

		array(
			'id'      => 'duration',
			'type'    => 'number',
			'title'   => esc_html__( 'Count down duration minutes', 'merchant' ),
			'default' => 10,
		),

		array(
			'id'      => 'reserved_message',
			'type'    => 'text',
			'title'   => esc_html__( 'Cart reserved message', 'merchant' ),
			'default' => esc_html__( 'An item in your cart is in high demand.', 'merchant' ),
		),

		array(
			'id'      => 'timer_message_minutes',
			'type'    => 'text',
			'title'   => esc_html__( 'Timer message for > 1 min ', 'merchant' ),
			'default' => esc_html__( 'Your cart is saved for {timer} minutes!', 'merchant' ),
		),

		array(
			'id'      => 'timer_message_seconds',
			'type'    => 'text',
			'title'   => esc_html__( 'Timer message for  < 1 min', 'merchant' ),
			'default' => esc_html__( 'Your cart is saved for {timer} seconds!', 'merchant' ),
		),

		array(
			'id'      => 'time_expires',
			'type'    => 'radio',
			'title'   => esc_html__( 'What to do after the timer expires?', 'merchant' ),
			'options' => array(
				'hide-timer' => esc_html__( 'Hide timer', 'merchant' ),
				'clear-cart' => esc_html__( 'Clear cart', 'merchant' ),
			),
			'default' => 'clear-cart'
		),

		array(
			'id'      => 'icon',
			'type'    => 'choices',
			'title'   => esc_html__( 'Choose an icon', 'merchant' ),
			'class'   => 'merchant-module-page-setting-field-choices-icon',
			'options' => array(
				'none'       => $icon_path . '/cancel.svg',
				'fire'       => $icon_path . '/fire.svg',
				'clock'      => $icon_path . '/clock.svg',
				'hour-glass' => $icon_path . '/hour-glass.svg',
			),
			'default' => 'fire'
		),

		array(
			'id'      => 'background_color',
			'type'    => 'color',
			'title'   => esc_html__( 'Choose background Color', 'merchant' ),
			'default' => '#f4f6f8',
		),

	),
) );


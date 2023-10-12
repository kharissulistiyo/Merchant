<?php

/**
 * Buy X Get Y
 *
 * @package Merchant
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Buy X Get Y Class.
 *
 */
class Merchant_Buy_X_Get_Y extends Merchant_Add_Module {

	/**
	 * Module ID.
	 */
	const MODULE_ID = 'buy-x-get-y';

	/**
	 * Module path.
	 */
	const MODULE_DIR = MERCHANT_DIR . 'inc/modules/' . self::MODULE_ID;

	/**
	 * Module template path.
	 */
	const MODULE_TEMPLATES = 'modules/' . self::MODULE_ID;

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		// Module id.
		$this->module_id = self::MODULE_ID;

		// WooCommerce only.
		$this->wc_only = true;

		// Parent construct.
		parent::__construct();

		// Module section.
		$this->module_section = 'boost-revenue';

		// Mount preview url.
		$preview_url = site_url( '/' );

		if ( function_exists( 'wc_get_products' ) ) {
			$products = wc_get_products( array( 'limit' => 1 ) );

			if ( ! empty( $products ) && ! empty( $products[0] ) ) {
				$preview_url = get_permalink( $products[0]->get_id() );
			}
		}

		// Module data.
		$this->module_data                = Merchant_Admin_Modules::$modules_data[ self::MODULE_ID ];
		$this->module_data['preview_url'] = $preview_url;

		// Module options path.
		$this->module_options_path = self::MODULE_DIR . "/admin/options.php";

		// Enqueue admin styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		// Add preview box
		add_filter( 'merchant_module_preview', array( $this, 'render_admin_preview' ), 10, 2 );

		// only applies if module is active or module is not active but admin only
		if ( ! Merchant_Modules::is_module_active( self::MODULE_ID ) && is_admin() || Merchant_Modules::is_module_active( self::MODULE_ID ) ) {
			// Custom CSS.
			add_filter( 'merchant_custom_css', array( $this, 'custom_css' ), 10, 2 );
		}
	}

	/**
	 * Enqueue admin page content scripts.
	 *
	 * @return void
	 */
	public function enqueue_admin_styles() {
		if ( $this->is_module_settings_page() ) {
			// Module styling.
			wp_enqueue_style(
				'merchant-' . self::MODULE_ID,
				MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/' . self::MODULE_ID . '.min.css',
				array(),
				MERCHANT_VERSION
			);

			// Preview-specific styling.
			wp_enqueue_style(
				'merchant-preview-' . self::MODULE_ID,
				MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/admin/preview.min.css',
				array(),
				MERCHANT_VERSION
			);
		}
	}

	/**
	 * Render admin preview
	 *
	 * @param Merchant_Admin_Preview $preview
	 * @param string $module
	 *
	 * @return Merchant_Admin_Preview
	 */
	public function render_admin_preview( $preview, $module ) {
		if ( $module === self::MODULE_ID ) {
			// HTML.
			$preview->set_html( merchant_get_template_part(
				self::MODULE_TEMPLATES,
				'single-product',
				array(
					'offers'   => array(
						array(
							'buy_quantity'   => 2,
							'product'        => array(
								'id'         => 31,
								'image'      => '<img src="' . MERCHANT_URI . 'assets/images/dummy/Pearlville.jpeg" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="">',
								'title'      => 'Vinopure Pore Purifying Gel Cleanser',
								'price_html' => wc_price( 14 ),
								'price'      => 14,
								'permalink'  => '#'
							),
							'quantity'       => 2,
							'discount_value' => 10,
							'layout'         => 'percentage_discount',
							'buy_product'    => array(
								'id'         => 97,
								'image'      => '<img src="' . MERCHANT_URI . 'assets/images/dummy/Glamifiedpeach.jpeg" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="">',
								'title'      => 'Eternal Sunset Collection Lip and Cheek',
								'price_html' => wc_price( 12 ),
								'price'      => 12,
								'permalink'  => '#'
							),
							'total_discount' => 2.8
						)
					),
					'nonce'    => '',
					'settings' => Merchant_Admin_Options::get_all( self::MODULE_ID )
				),
				true
			) );

			// Title Text.
			$preview->set_text( 'title', '.merchant-bogo-title' );

			// Buy Label Text ({quantity} gets replaced with a dummy "2" text)
			$preview->set_text( 'buy_label', '.merchant-bogo-product-buy-label', array(
				array(
					'{quantity}'
				),
				array(
					'2'
				)
			) );

			// Get Label Text ({quantity} gets replaced with a dummy "2" text and {discount} gets replaced with dummy "10%" text)
			$preview->set_text( 'get_label', '.merchant-bogo-product-get-label', array(
				array(
					'{quantity}',
					'{discount}'
				),
				array(
					'2',
					'10%'
				)
			) );

			// Button Text
			$preview->set_text( 'button_text', '.merchant-bogo-add-to-cart' );

			// Title Font Size
			$preview->set_css( 'title_font_size', '.merchant-bogo-title', '--merchant-font-size', 'px' );

			// Title Font Weight
			$preview->set_css( 'title_font_weight', '.merchant-bogo-title', '--merchant-font-weight' );

			// Title Text Color
			$preview->set_css( 'title_text_color', '.merchant-bogo-title', '--merchant-text-color' );

			// Label Background Color
			$preview->set_css( 'label_bg_color', '.merchant-bogo-product-label', '--merchant-bg-color' );

			// Label Text Color
			$preview->set_css( 'label_text_color', '.merchant-bogo-product-label', '--merchant-text-color' );

			// Arrow Background Color
			$preview->set_css( 'arrow_bg_color', '.merchant-bogo-arrow', '--merchant-bg-color' );

			// Arrow Text Color
			$preview->set_css( 'arrow_text_color', '.merchant-bogo-arrow', '--merchant-text-color' );

			// Offer Border Color
			$preview->set_css( 'offer_border_color', '.merchant-bogo-product-y', '--merchant-border-color' );

			// Offer Border Radius
			$preview->set_css( 'offer_border_radius', '.merchant-bogo-product-y', '--merchant-border-radius', 'px' );
		}

		return $preview;
	}

	/**
	 * Custom CSS.
	 *
	 * @param string $css
	 * @param Merchant_Custom_CSS $custom_css
	 *
	 * @return string
	 */
	public function custom_css( $css, $custom_css ) {
		// Title Font Size.
		$css .= $custom_css->get_variable_css( $this->module_id, 'title_font_size', 16, '.merchant-bogo-title', '--merchant-font-size', 'px' );

		// Title Font Weight.
		$css .= $custom_css->get_variable_css( $this->module_id, 'title_font_weight', 'normal', '.merchant-bogo-title', '--merchant-font-weight' );

		// Title Text Color.
		$css .= $custom_css->get_variable_css( $this->module_id, 'title_text_color', '#212121', '.merchant-bogo-title', '--merchant-text-color' );

		// Label Background Color
		$css .= $custom_css->get_variable_css( $this->module_id, 'label_bg_color', '#d61313', '.merchant-bogo-product-label', '--merchant-bg-color' );

		// Label Text Color
		$css .= $custom_css->get_variable_css( $this->module_id, 'label_text_color', '#fff', '.merchant-bogo-product-label', '--merchant-text-color' );

		// Arrow Background Color
		$css .= $custom_css->get_variable_css( $this->module_id, 'arrow_bg_color', '#d61313', '.merchant-bogo-arrow', '--merchant-bg-color' );

		// Arrow Text Color
		$css .= $custom_css->get_variable_css( $this->module_id, 'arrow_text_color', '#fff', '.merchant-bogo-arrow', '--merchant-text-color' );

		// Offer Border Color
		$css .= $custom_css->get_variable_css( $this->module_id, 'offer_border_color', '#cccccc', '.merchant-bogo-product-y', '--merchant-border-color' );

		// Offer Border Radius
		$css .= $custom_css->get_variable_css( $this->module_id, 'offer_border_radius', 5, '.merchant-bogo-product-y', '--merchant-border-radius', 'px' );

		return $css;
	}
}

// Initialize the module.
add_action( 'init', function () {
	Merchant_Modules::create_module( new Merchant_Buy_X_Get_Y() );
} );
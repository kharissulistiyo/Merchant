<?php

/**
 * Payment Logos
 * 
 * @package Merchant
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Payment Logos Class.
 * 
 */
class Merchant_Payment_Logos extends Merchant_Add_Module {

	/**
	 * Module ID.
	 *
	 */
	const MODULE_ID = 'payment-logos';

	/**
	 * Is module preview.
	 * 
	 */
	public static $is_module_preview = false;

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
		$this->module_section = 'build-trust';

		// Module default settings.
		$this->module_default_settings = array(
			'logos' => '',
			'title' => __( '🔒 Safe & Secure Checkout', 'merchant' )
		);

		// Mount preview url.
		$preview_url = site_url( '/' );

		if ( function_exists( 'wc_get_products' ) ) {
			$products = wc_get_products( array( 'limit' => 1 ) );

			if ( ! empty( $products ) && ! empty( $products[0] ) ) {
				$preview_url = get_permalink( $products[0]->get_id() );
			}
		}

		// Module data.
		$this->module_data = Merchant_Admin_Modules::$modules_data[ self::MODULE_ID ];
		$this->module_data[ 'preview_url' ] = $preview_url;

		// Module options path.
		$this->module_options_path = MERCHANT_DIR . 'inc/modules/' . self::MODULE_ID . '/admin/options.php';

		// Is module preview page.
		if ( is_admin() && parent::is_module_settings_page() ) {
			self::$is_module_preview = true;

			// Enqueue admin styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_css' ) );

			// Admin preview box.
			add_filter( 'merchant_module_preview', array( $this, 'render_admin_preview' ), 10, 2 );

			// Custom CSS.
			// The custom CSS should be added here as well due to ensure preview box works properly.
			add_filter( 'merchant_custom_css', array( $this, 'admin_custom_css' ) );

		}

		if ( ! Merchant_Modules::is_module_active( self::MODULE_ID ) ) {
			return;
		}

		// Return early if it's on admin but not in the respective module settings page.
		if ( is_admin() && ! parent::is_module_settings_page() ) {
			return;	
		}

		// Enqueue styles.
		add_action( 'merchant_enqueue_before_main_css_js', array( $this, 'enqueue_css' ) );

		// Add payment logos after add to cart form on single product pages.
		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'payment_logos_output' ) );

		// Custom CSS.
		add_filter( 'merchant_custom_css', array( $this, 'frontend_custom_css' ) );

	}

	/**
	 * Admin enqueue CSS.
	 * 
	 * @return void
	 */
	public function admin_enqueue_css() {
		$page   = ( ! empty( $_GET['page'] ) ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		$module = ( ! empty( $_GET['module'] ) ) ? sanitize_text_field( wp_unslash( $_GET['module'] ) ) : '';

		if ( 'merchant' === $page && self::MODULE_ID === $module ) {
			wp_enqueue_style( 'merchant-' . self::MODULE_ID, MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/payment-logos.min.css', [], MERCHANT_VERSION );
			wp_enqueue_style( 'merchant-admin-' . self::MODULE_ID, MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/admin/preview.min.css', array(), MERCHANT_VERSION );
		}
	}

	/**
	 * Enqueue CSS.
	 * 
	 * @return void
	 */
	public function enqueue_css() {
		if ( ! is_singular( 'product' ) && ! Merchant_Modules::is_module_active( 'quick-view' ) ) {
			return;
		}

		// Specific module styles.
		wp_enqueue_style( 'merchant-' . self::MODULE_ID, MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/payment-logos.min.css', array(), MERCHANT_VERSION );
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
		if ( self::MODULE_ID === $module ) {
			ob_start();
			self::admin_preview_content();
			$content = ob_get_clean();

			// HTML.
			$preview->set_html( $content );

			// Text Above the Logos.
			$preview->set_text( 'title', '.merchant-payment-logos-title > strong' );

		}

		return $preview;
	}

	/**
	 * Admin preview content.
	 * 
	 * @return void
	 */
	public function admin_preview_content() {
		?>

		<div class="mrc-preview-single-product-elements">
			<div class="mrc-preview-left-column">
				<div class="mrc-preview-product-image-wrapper">
					<div class="mrc-preview-product-image"></div>
					<div class="mrc-preview-product-image-thumbs">
						<div class="mrc-preview-product-image-thumb"></div>
						<div class="mrc-preview-product-image-thumb"></div>
						<div class="mrc-preview-product-image-thumb"></div>
					</div>
				</div>
			</div>
			<div class="mrc-preview-right-column">
				<div class="mrc-preview-text-placeholder"></div>
				<div class="mrc-preview-text-placeholder mrc-mw-70"></div>
				<div class="mrc-preview-text-placeholder mrc-mw-30"></div>
				<div class="mrc-preview-text-placeholder mrc-mw-40 mrc-hide-on-smaller-screens"></div>
				<div class="mrc-preview-addtocart-placeholder mrc-hide-on-smaller-screens"></div>
				<?php $this->payment_logos_output(); ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Get logos.
	 * 
	 * @param string $logos
	 * @return array $logos Array of logos.
	 */
	public function get_logos( $logos ) {
		return ! empty( $logos ) 
			? explode( ',', $logos ) 
			: array(
				MERCHANT_URI . 'inc/modules/' . self::MODULE_ID . '/admin/images/visa.svg',
				MERCHANT_URI . 'inc/modules/' . self::MODULE_ID . '/admin/images/master.svg',
				MERCHANT_URI . 'inc/modules/' . self::MODULE_ID . '/admin/images/pp.svg'
			);
	}

	/**
	 * Render payment logos.
	 * TODO: Render through template files.
	 * 
	 * @return void
	 */
	public function payment_logos_output() {
		if ( is_archive() ) {
			return;
		}
		
		$settings		= $this->get_module_settings();
		$is_placeholder = empty( $settings[ 'logos' ] ) ? true : false;

		$logos	= $this->get_logos( $settings[ 'logos' ] );

		?>

		<div class="merchant-payment-logos">

			<?php if ( ! empty( $settings[ 'title' ] ) ) : ?>
				<div class="merchant-payment-logos-title">
					<strong><?php echo esc_html( $settings[ 'title' ] ); ?></strong>
				</div>
			<?php endif; ?>

			<?php if ( ! $is_placeholder ) : ?>

				<div class="merchant-payment-logos-images">

					<?php foreach ( $logos as $image_id ) : ?>

						<?php $imagedata = wp_get_attachment_image_src( $image_id, 'full' ); ?>

						<?php if ( ! empty( $imagedata ) && ! empty( $imagedata[0] ) ) : ?>

							<?php echo sprintf( '<img src="%s" />', esc_url( $imagedata[0] ) ); ?>

						<?php endif; ?>

					<?php endforeach; ?>

				</div>

			<?php else : ?>

				<div class="merchant-payment-logos-images is-placeholder">
					<?php foreach ( $logos as $logo_src ) : ?>
						<img src="<?php echo esc_url( $logo_src ); ?>" />
					<?php endforeach; ?>
				</div>

			<?php endif; ?>

		</div>
			
		<?php 
		
	}
	
	/**
	 * Custom CSS.
	 * 
	 * @return string
	 */
	public function get_module_custom_css() {
		$css = '';

		// Font Size.
		$css .= Merchant_Custom_CSS::get_variable_css( 'payment-logos', 'font-size', 18, '.merchant-payment-logos', '--mrc-plogos-font-size', 'px' );

		// Text Color.
		$css .= Merchant_Custom_CSS::get_variable_css( 'payment-logos', 'text-color', '#212121', '.merchant-payment-logos', '--mrc-plogos-text-color' );

		// Margin Top.
		$css .= Merchant_Custom_CSS::get_variable_css( 'payment-logos', 'margin-top', 20, '.merchant-payment-logos', '--mrc-plogos-margin-top', 'px' );

		// Margin Bottom.
		$css .= Merchant_Custom_CSS::get_variable_css( 'payment-logos', 'margin-bottom', 20, '.merchant-payment-logos', '--mrc-plogos-margin-bottom', 'px' );

		// Align.
		$css .= Merchant_Custom_CSS::get_variable_css( 'payment-logos', 'align', 'flex-start', '.merchant-payment-logos', '--mrc-plogos-align' );

		// Image Max Width.
		$css .= Merchant_Custom_CSS::get_variable_css( 'payment-logos', 'image-max-width', 80, '.merchant-payment-logos', '--mrc-plogos-image-max-width', 'px' );

		// Image Max Height.
		$css .= Merchant_Custom_CSS::get_variable_css( 'payment-logos', 'image-max-height', 100, '.merchant-payment-logos', '--mrc-plogos-image-max-height', 'px' );

		return $css;
	}

	/**
	 * Admin custom CSS.
	 * 
	 * @param string $css The custom CSS.
	 * @return string $css The custom CSS.
	 */
	public function admin_custom_css( $css ) {
		$css .= $this->get_module_custom_css(); 

		return $css;
	}

	/**
	 * Frontend custom CSS.
	 * 
	 * @param string $css The custom CSS.
	 * @return string $css The custom CSS.
	 */
	public function frontend_custom_css( $css ) {
		if ( ! is_singular( 'product' ) && ! Merchant_Modules::is_module_active( 'quick-view' ) ) {
			return $css;
		}
		
		$css .= $this->get_module_custom_css();

		return $css;
	}

}

// Initialize the module.
add_action( 'init', function() {
	new Merchant_Payment_Logos();
} );
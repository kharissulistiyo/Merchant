<?php

/**
 * Product Labels.
 * 
 * @package Merchant_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Product Labels Class.
 * 
 */
class Merchant_Product_Labels extends Merchant_Add_Module {

	/**
	 * Module ID.
	 *
	 */
	const MODULE_ID = 'product-labels';

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
		parent::__construct();

		// Module section.
		$this->module_section = 'convert-more';

		// Module id.
		$this->module_id = self::MODULE_ID;

		// Module default settings.
		$this->module_default_settings = array(
			'label_text' => __( 'Spring Special', 'merchant' ),
			'display_percentage' => 0,
			'percentage_text' => '-{value}%',
			'label_position' => 'top-left',
			'label_shape' => 0,
			'label_text_transform' => 'uppercase'
		);

		// Mount preview url.
		$preview_url = site_url( '/' );

		if ( function_exists( 'wc_get_page_id' ) ) {
			$preview_url = get_permalink( wc_get_page_id( 'shop' ) );
		} 

		// Module data.
		$this->module_data = array(
			'icon'  => '<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.114 0h7.775c.295 0 .577.111.786.31.208.197.325.465.325.745v7.387c0 .139-.027.276-.082.405a1.052 1.052 0 0 1-.24.344l-.322.306h-2.688c-1.768 0-3.463.667-4.713 1.854-1.25 1.188-1.952 2.798-1.952 4.477v2.554l-.322.306a1.115 1.115 0 0 1-.361.23 1.161 1.161 0 0 1-1.216-.23L.329 11.3a1.054 1.054 0 0 1-.244-.343A1.01 1.01 0 0 1 .33 9.803L10.325.306c.104-.098.227-.175.363-.228.135-.052.28-.079.426-.078Zm4.073 5.01c.274.173.596.266.925.266.442 0 .866-.167 1.179-.464.312-.296.488-.699.488-1.119 0-.313-.098-.619-.281-.88a1.65 1.65 0 0 0-.748-.582 1.748 1.748 0 0 0-.963-.09c-.323.06-.62.212-.853.433a1.56 1.56 0 0 0-.456.81 1.51 1.51 0 0 0 .095.915c.126.29.34.536.614.71Z"/><path fill-rule="evenodd" clip-rule="evenodd" d="M17.193 11.75c0-.414-.393-.75-.877-.75-.485 0-.877.336-.877.75v.128c-1.023.309-1.755 1.142-1.755 2.122 0 .76.316 1.327.9 1.702.473.304 1.065.43 1.461.513l.058.013c.483.103.732.168.893.27.074.048.197.137.197.502 0 .41-.385.743-.863.75a1.623 1.623 0 0 1-.285-.055 2.306 2.306 0 0 1-.863-.475.986.986 0 0 0-1.24 0 .676.676 0 0 0 0 1.06 4.132 4.132 0 0 0 1.497.823v.147c0 .414.392.75.877.75.484 0 .877-.336.877-.75v-.128c1.022-.309 1.754-1.142 1.754-2.122 0-.76-.315-1.327-.899-1.702-.474-.303-1.065-.43-1.461-.513l-.058-.013c-.483-.103-.733-.168-.893-.27-.074-.048-.197-.137-.197-.502 0-.41.384-.743.862-.75l.042.004c.05.006.134.02.244.051.215.062.532.192.863.475a.985.985 0 0 0 1.24 0 .676.676 0 0 0 0-1.06 4.131 4.131 0 0 0-1.497-.823v-.147Z"/></svg>',
			'title' => esc_html__( 'Product Labels', 'merchant' ),
			'desc'  => esc_html__( 'Add customizable labels on top of your product images to promote special deals or discounts, highlight new products, or anything else you want.', 'merchant' ),
			'placeholder' => MERCHANT_URI . 'assets/images/modules/product-labels.png',
			'tutorial_url' => 'https://docs.athemes.com/article/product-labels/',
			'preview_url' => $preview_url
		);

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

		// Inject module content in the products.
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_label_output' ) );
		add_action( 'woocommerce_product_thumbnails', array( $this, 'product_label_output' ) );
		add_action( 'woostify_product_images_box_end', array( $this, 'product_label_output' ) );

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
			wp_enqueue_style( 'merchant-' . self::MODULE_ID, MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/product-labels.min.css', [], MERCHANT_VERSION );
			wp_enqueue_style( 'merchant-admin-' . self::MODULE_ID, MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/admin/preview.min.css', array(), MERCHANT_VERSION );
		}
	}

	/**
	 * Enqueue CSS.
	 * 
	 * @return void
	 */
	public function enqueue_css() {

		// Specific module styles.
		wp_enqueue_style( 'merchant-' . self::MODULE_ID, MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/product-labels.min.css', array(), MERCHANT_VERSION );
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

			// Label Text.
			$preview->set_text( 'label_text', '.merchant-onsale' );

			// Position.
			$preview->set_class( 'label_position', '.merchant-onsale', array( 'top-left', 'top-right' ) );

		}

		return $preview;
	}

	/**
	 * Admin preview content.
	 * 
	 * @return void
	 */
	public function admin_preview_content() {
		$settings = $this->get_module_settings();

		$label_text	= ! empty( $settings[ 'display_percentage' ] ) ? str_replace( '{value}', 20, $settings[ 'percentage_text' ] ) : $settings[ 'label_text' ];
	
		?>

		<div class="merchant-product-labels-preview">
			<div class="image-wrapper">
				<span class="merchant-onsale merchant-onsale-<?php echo sanitize_html_class( $settings[ 'label_position' ] ); ?> merchant-onsale-shape-<?php echo sanitize_html_class( $settings[ 'label_shape' ] ) ?>"><?php echo esc_html( $label_text ); ?></span>
			</div>
			<h3><?php echo esc_html__( 'Product Title', 'merchant' ); ?></h3>
			<p><?php echo esc_html__( 'The product description normally goes here.', 'merchant' ); ?></p>
		</div>

		<?php
	}

	/**
	 * Product label output.
	 * 
	 * @return void
	 */
	public function product_label_output() {
		global $product;

		$settings = $this->get_module_settings();
	
		if ( ! empty( $product ) && $product->is_on_sale() ) {
	
			$label_text	= $settings[ 'label_text' ];
	
			if ( ! empty( $settings[ 'display_percentage' ] ) ) {
				
				if ( $product->is_type('variable' ) ) {
	
					$percentages = array();
					$prices      = $product->get_variation_prices();
				
					foreach ( $prices['price'] as $key => $price ) {
						if ( $prices['regular_price'][$key] !== $price ) {
							$percentages[] = round( 100 - ( floatval( $prices['sale_price'][ $key ] ) / floatval( $prices['regular_price'][ $key ] ) * 100 ) );
						}
					}
					
					$percentage = max( $percentages );
					
				} elseif ( $product->is_type('grouped') ) {
					
					$percentages  = array();
					$children_ids = $product->get_children();
					
					foreach ( $children_ids as $child_id ) {
						
						$child_product = wc_get_product($child_id);
						$regular_price = (float) $child_product->get_regular_price();
						$sale_price    = (float) $child_product->get_sale_price();
						
						if ( 0 != $sale_price || ! empty( $sale_price ) ) {
							$percentages[] = round( 100 - ( ( $sale_price / $regular_price ) * 100 ) );
						}
						
					}
					$percentage = max( $percentages );
					
				} else {
					
					$regular_price = (float) $product->get_regular_price();
					$sale_price    = (float) $product->get_sale_price();
					
					if ( 0 != $sale_price || ! empty( $sale_price ) ) {
						$percentage = round( 100 - ( ( $sale_price / $regular_price ) * 100 ) );
					}
					
				}
				
				$label_text = str_replace( '{value}', $percentage, $settings[ 'percentage_text' ] );
				
			}
			
			echo '<span class="merchant-onsale merchant-onsale-' . sanitize_html_class( $settings[ 'label_position' ] ) . ' merchant-onsale-shape-' . sanitize_html_class( $settings[ 'label_shape' ] ) . '">' . esc_html( $label_text ) . '</span>';
	
		}
		
	}

	/**
	 * Custom CSS.
	 * 
	 * @return string
	 */
	public function get_module_custom_css() {
		$css = '';

		// Border Radius.
		$css .= Merchant_Custom_CSS::get_variable_css( 'product-labels', 'label_shape', 0, '.merchant-onsale', '--mrc-pl-border-radius', 'px' );
		
		// Text Transform.
		$css .= Merchant_Custom_CSS::get_variable_css( 'product-labels', 'label_text_transform', 'uppercase', '.merchant-onsale', '--mrc-pl-text-transform' );

		// Padding.
		$css .= Merchant_Custom_CSS::get_variable_css( 'product-labels', 'padding', 8, '.merchant-onsale', '--mrc-pl-padding', 'px' );

		// Font Size.
		$css .= Merchant_Custom_CSS::get_variable_css( 'product-labels', 'font-size', 14, '.merchant-onsale', '--mrc-pl-font-size', 'px' );

		// Text Color.
		$css .= Merchant_Custom_CSS::get_variable_css( 'product-labels', 'text_color', '#ffffff', '.merchant-onsale', '--mrc-pl-text-color' );

		// Background Color.
		$css .= Merchant_Custom_CSS::get_variable_css( 'product-labels', 'background_color', '#212121', '.merchant-onsale', '--mrc-pl-background-color' );

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
		
		// Append module custom CSS.
		$css .= $this->get_module_custom_css();
		
		// Append module custom CSS based on themes (ensure themes compatibility).
		// Detect the current theme.
		$theme 		= wp_get_theme();
		$theme_name = $theme->get( 'Name' );

		// All themes.
		$css .= '
			.woocommerce .onsale { 
				display: none !important; 
			}
		';

		// Astra.
		if ( 'Astra' === $theme_name ) {
			$css .= '
				.woocommerce .ast-onsale-card {
					display: none !important;
				}
			';
		}

		return $css;
	}

}

// Initialize the module.
new Merchant_Product_Labels();

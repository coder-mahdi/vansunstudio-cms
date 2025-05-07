<?php
/**
 * vansunstudio-cms functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package vansunstudio-cms
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function vansunstudio_cms_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on vansunstudio-cms, use a find and replace
		* to change 'vansunstudio-cms' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'vansunstudio-cms', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'vansunstudio-cms' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'vansunstudio_cms_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'vansunstudio_cms_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function vansunstudio_cms_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'vansunstudio_cms_content_width', 640 );
}
add_action( 'after_setup_theme', 'vansunstudio_cms_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function vansunstudio_cms_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'vansunstudio-cms' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'vansunstudio-cms' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'vansunstudio_cms_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function vansunstudio_cms_scripts() {
	wp_enqueue_style( 'vansunstudio-cms-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'vansunstudio-cms-style', 'rtl', 'replace' );

	wp_enqueue_script( 'vansunstudio-cms-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'vansunstudio_cms_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


// Enable WooCommerce Bookings REST API
add_filter('woocommerce_rest_api_enabled', '__return_true');
add_filter('woocommerce_rest_api_enable_bookings', '__return_true');

// Add CORS headers for React frontend
add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        return $value;
    });
}, 15);

// Add support for WooCommerce
function vansunstudio_cms_add_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'vansunstudio_cms_add_woocommerce_support');

/ Enable WooCommerce Bookings REST API
add_filter('woocommerce_rest_api_enabled', '__return_true');
add_filter('woocommerce_rest_api_enable_bookings', '__return_true');

// Add CORS headers for React frontend
add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        return $value;
    });
}, 15);

// Add support for WooCommerce
function vansunstudio_cms_add_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'vansunstudio_cms_add_woocommerce_support');

// Register Custom Booking Endpoint
function register_booking_endpoint() {
    register_rest_route('vansunstudio/v1', '/booking', array(
        'methods' => 'POST',
        'callback' => 'handle_booking_creation',
        'permission_callback' => '__return_true'
    ));
}
add_action('rest_api_init', 'register_booking_endpoint');

// Handle Booking Creation
function handle_booking_creation($request) {
    $params = $request->get_params();
    
    // Debug information
    error_log('Booking endpoint called');
    error_log('Request parameters: ' . print_r($params, true));

    // Validate required fields
    if (empty($params['full_name']) || 
        empty($params['email_address']) || 
        empty($params['phone']) || 
        empty($params['date']) || 
        empty($params['time']) || 
        empty($params['product_id'])) {
        error_log('Missing required fields');
        return new WP_Error('missing_fields', 'All fields are required', array('status' => 400));
    }

    // Get the product
    $product = wc_get_product($params['product_id']);
    if (!$product || !is_a($product, 'WC_Product_Booking')) {
        error_log('Invalid product or not a booking product');
        return new WP_Error('invalid_product', 'Invalid booking product', array('status' => 400));
    }

    // Create booking data
    $booking_data = array(
        'product_id' => $params['product_id'],
        'start_date' => $params['date'] . ' ' . $params['time'],
        'end_date' => $params['date'] . ' ' . $params['time'], // You might want to calculate this based on duration
        'customer_id' => 0, // Guest booking
        'status' => 'confirmed',
        'customer_name' => sanitize_text_field($params['full_name']),
        'customer_email' => sanitize_email($params['email_address']),
        'customer_phone' => sanitize_text_field($params['phone'])
    );

    // Create the booking
    $booking = new WC_Booking();
    $booking->set_props($booking_data);
    $booking->save();

    if (!$booking->get_id()) {
        error_log('Failed to create booking');
        return new WP_Error('booking_creation_failed', 'Failed to create booking', array('status' => 500));
    }

    error_log('Booking created successfully with ID: ' . $booking->get_id());

    return array(
        'success' => true,
        'booking_id' => $booking->get_id(),
        'message' => 'Booking created successfully'
    );
} 
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

// Register Custom Post Type for Booking
function register_booking_post_type() {
    $labels = array(
        'name'                  => _x('Bookings', 'Post Type General Name', 'vancouver'),
        'singular_name'         => _x('Booking', 'Post Type Singular Name', 'vancouver'),
        'menu_name'            => __('Bookings', 'vancouver'),
        'name_admin_bar'       => __('Booking', 'vancouver'),
        'archives'             => __('Booking Archives', 'vancouver'),
        'add_new'              => __('Add New', 'vancouver'),
        'add_new_item'         => __('Add New Booking', 'vancouver'),
        'new_item'             => __('New Booking', 'vancouver'),
        'edit_item'            => __('Edit Booking', 'vancouver'),
        'view_item'            => __('View Booking', 'vancouver'),
        'all_items'            => __('All Bookings', 'vancouver'),
        'search_items'         => __('Search Bookings', 'vancouver'),
        'not_found'            => __('No bookings found.', 'vancouver'),
        'not_found_in_trash'   => __('No bookings found in Trash.', 'vancouver'),
    );

    $args = array(
        'label'               => __('Booking', 'vancouver'),
        'description'         => __('Booking entries', 'vancouver'),
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'custom-fields'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-calendar-alt',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true, // Enable REST API support
        'rest_base'           => 'booking', // REST API base URL
    );

    register_post_type('booking', $args);
}
add_action('init', 'register_booking_post_type');

// Register Custom Booking Endpoint
function register_booking_endpoint() {
    register_rest_route('custom-booking-endpoint/v1', '/create', array(
        'methods' => 'POST',
        'callback' => 'handle_booking_creation',
        'permission_callback' => '__return_true'
    ));
}
add_action('rest_api_init', 'register_booking_endpoint');

// Handle Booking Creation
function handle_booking_creation($request) {
    $params = $request->get_params();
    
    // Validate required fields
    if (empty($params['full_name']) || empty($params['email']) || 
        empty($params['phone']) || empty($params['date']) || 
        empty($params['time']) || empty($params['product_id'])) {
        return new WP_Error('missing_fields', 'All fields are required', array('status' => 400));
    }

    // Create booking post
    $booking_data = array(
        'post_title'    => $params['full_name'] . ' - ' . $params['date'],
        'post_status'   => 'publish',
        'post_type'     => 'booking'
    );

    $booking_id = wp_insert_post($booking_data);

    if (is_wp_error($booking_id)) {
        return new WP_Error('booking_creation_failed', 'Failed to create booking', array('status' => 500));
    }

    // Save booking meta data
    update_post_meta($booking_id, 'full_name', sanitize_text_field($params['full_name']));
    update_post_meta($booking_id, 'email', sanitize_email($params['email']));
    update_post_meta($booking_id, 'phone', sanitize_text_field($params['phone']));
    update_post_meta($booking_id, 'booking_date', sanitize_text_field($params['date']));
    update_post_meta($booking_id, 'booking_time', sanitize_text_field($params['time']));
    update_post_meta($booking_id, 'product_id', intval($params['product_id']));

    return array(
        'success' => true,
        'booking_id' => $booking_id,
        'message' => 'Booking created successfully'
    );		
	
} 
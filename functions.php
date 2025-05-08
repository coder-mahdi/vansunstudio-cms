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

// Add CORS headers for React frontend
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

// Enable WooCommerce Bookings REST API
add_filter('woocommerce_rest_api_enabled', '__return_true');
add_filter('woocommerce_rest_api_enable_bookings', '__return_true');

// Register Custom Post Type for Bookings
function register_booking_post_type() {
    $labels = array(
        'name'               => 'Bookings',
        'singular_name'      => 'Booking',
        'menu_name'          => 'Bookings',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Booking',
        'edit_item'          => 'Edit Booking',
        'new_item'           => 'New Booking',
        'view_item'          => 'View Booking',
        'search_items'       => 'Search Bookings',
        'not_found'          => 'No bookings found',
        'not_found_in_trash' => 'No bookings found in Trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'booking'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'custom-fields')
    );

    register_post_type('booking', $args);
}
add_action('init', 'register_booking_post_type');

// Register ACF Fields for Bookings
function register_booking_acf_fields() {
    if(function_exists('acf_add_local_field_group')):

        acf_add_local_field_group(array(
            'key' => 'group_booking_fields',
            'title' => 'Booking Information',
            'fields' => array(
                array(
                    'key' => 'field_full_name',
                    'label' => 'Full Name',
                    'name' => 'full_name',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_email',
                    'label' => 'Email',
                    'name' => 'email',
                    'type' => 'email',
                    'required' => 1,
                    'validate_format' => 1,
                    'instructions' => 'Please enter a valid email address',
                    'placeholder' => 'example@domain.com',
                ),
                array(
                    'key' => 'field_phone',
                    'label' => 'Phone',
                    'name' => 'phone',
                    'type' => 'text',
                    'required' => 1,
                    'instructions' => 'Please enter a valid phone number (e.g., +989123456789)',
                    'placeholder' => '+989123456789',
                    'validate_format' => 1,
                    'validate_format_message' => 'Please enter a valid phone number',
                ),
                array(
                    'key' => 'field_booking_date',
                    'label' => 'Booking Date',
                    'name' => 'booking_date',
                    'type' => 'date_picker',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_booking_time',
                    'label' => 'Booking Time',
                    'name' => 'booking_time',
                    'type' => 'time_picker',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_product_id',
                    'label' => 'Product ID',
                    'name' => 'product_id',
                    'type' => 'number',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_booking_status',
                    'label' => 'Booking Status',
                    'name' => 'booking_status',
                    'type' => 'select',
                    'choices' => array(
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled'
                    ),
                    'default_value' => 'pending',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_terms_accepted',
                    'label' => 'Terms Accepted',
                    'name' => 'terms_accepted',
                    'type' => 'true_false',
                    'ui' => 1,
                    'required' => 1,
                    'default_value' => 0,
                ),
                array(
                    'key' => 'field_terms_accepted_date',
                    'label' => 'Terms Accepted Date',
                    'name' => 'terms_accepted_date',
                    'type' => 'date_time_picker',
                    'required' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'booking',
                    ),
                ),
            ),
        ));

    endif;
}
add_action('acf/init', 'register_booking_acf_fields');

// Register endpoint for getting booking availability
function register_booking_availability_endpoint() {
    register_rest_route('vansunstudio/v1', '/booking/availability', array(
        'methods' => 'GET',
        'callback' => 'get_booking_availability',
        'permission_callback' => '__return_true',
        'args' => array(
            'product_id' => array(
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ),
            'date' => array(
                'required' => true,
                'type' => 'string',
                'format' => 'date',
                'sanitize_callback' => 'sanitize_text_field'
            )
        )
    ));
}
add_action('rest_api_init', 'register_booking_availability_endpoint');

// Get booking availability
function get_booking_availability($request) {
    error_log('=== Booking Availability Debug ===');
    
    $product_id = $request->get_param('product_id');
    $date = $request->get_param('date');
    
    error_log('Product ID: ' . $product_id);
    error_log('Date: ' . $date);

    $product = wc_get_product($product_id);
    if (!$product) {
        error_log('Product not found');
        return new WP_Error('invalid_product', 'Product not found', array('status' => 400));
    }

    error_log('Product found: ' . get_class($product));

    // Get availability rules
    $availability_rules = get_post_meta($product_id, '_wc_booking_availability', true);
    error_log('Availability rules: ' . print_r($availability_rules, true));

    return array(
        'product_id' => $product_id,
        'date' => $date,
        'availability_rules' => $availability_rules
    );
}

// Register endpoint for getting booking product details
function register_booking_product_endpoint() {
    register_rest_route('vansunstudio/v1', '/booking/product', array(
        'methods' => 'GET',
        'callback' => 'get_booking_product',
        'permission_callback' => '__return_true',
        'args' => array(
            'product_id' => array(
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            )
        )
    ));
}
add_action('rest_api_init', 'register_booking_product_endpoint');

// Get booking product details
function get_booking_product($request) {
    error_log('=== Booking Product Debug ===');
    
    $product_id = $request->get_param('product_id');
    error_log('Product ID: ' . $product_id);

    $product = wc_get_product($product_id);
    if (!$product) {
        error_log('Product not found');
        return new WP_Error('invalid_product', 'Product not found', array('status' => 400));
    }

    error_log('Product found: ' . get_class($product));

    // Get availability rules
    $availability_rules = get_post_meta($product_id, '_wc_booking_availability', true);
    error_log('Availability rules: ' . print_r($availability_rules, true));

    return array(
        'product_id' => $product_id,
        'is_booking' => is_a($product, 'WC_Product_Booking'),
        'availability_rules' => $availability_rules
    );
}

// Register endpoint for creating bookings
function register_create_booking_endpoint() {
    register_rest_route('vansunstudio/v1', '/booking/create', array(
        'methods' => 'POST',
        'callback' => 'create_booking',
        'permission_callback' => '__return_true',
        'args' => array(
            'full_name' => array(
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'email' => array(
                'required' => true,
                'type' => 'string',
                'format' => 'email',
                'sanitize_callback' => 'sanitize_email'
            ),
            'phone' => array(
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'product_id' => array(
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ),
            'booking_date' => array(
                'required' => true,
                'type' => 'string',
                'format' => 'date'
            ),
            'booking_time' => array(
                'required' => true,
                'type' => 'string'
            ),
            /* Temporarily disabled reCAPTCHA
            'recaptcha_token' => array(
                'required' => true,
                'type' => 'string'
            ),
            */
            'terms_accepted' => array(
                'required' => true,
                'type' => 'boolean'
            )
        )
    ));
}
add_action('rest_api_init', 'register_create_booking_endpoint');

// Create booking and store customer information
function create_booking($request) {
    if (!class_exists('WC_Product_Booking')) {
        return new WP_Error('booking_plugin_missing', 'WooCommerce Bookings plugin is not active', array('status' => 500));
    }

    // Verify reCAPTCHA
    /* Temporarily disabled reCAPTCHA
    $recaptcha_token = $request->get_param('recaptcha_token');
    $recaptcha_result = verify_recaptcha($recaptcha_token);
    if (is_wp_error($recaptcha_result)) {
        return $recaptcha_result;
    }
    */

    // Verify terms acceptance
    if (!$request->get_param('terms_accepted')) {
        return new WP_Error('terms_not_accepted', 'Terms and conditions must be accepted', array('status' => 400));
    }

    // Create WooCommerce booking
    $product_id = $request->get_param('product_id');
    $product = wc_get_product($product_id);
    if (!$product || !is_a($product, 'WC_Product_Booking')) {
        return new WP_Error('invalid_product', 'Invalid booking product', array('status' => 400));
    }

    // Create booking data
    $booking_data = array(
        'product_id' => $product_id,
        'start_date' => $request->get_param('booking_date') . ' ' . $request->get_param('booking_time'),
        'end_date' => $request->get_param('booking_date') . ' ' . $request->get_param('booking_time'),
        'customer_id' => 0,
        'status' => 'confirmed'
    );

    try {
        // Create WooCommerce booking
        $booking = new WC_Booking();
        $booking->set_props($booking_data);
        $booking->save();

        // Create custom post type entry
        $post_data = array(
            'post_title'  => 'Booking for ' . $request->get_param('full_name'),
            'post_type'   => 'booking',
            'post_status' => 'publish'
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id) {
            // Store customer information in ACF fields
            update_field('full_name', $request->get_param('full_name'), $post_id);
            update_field('email', $request->get_param('email'), $post_id);
            update_field('phone', $request->get_param('phone'), $post_id);
            update_field('booking_date', $request->get_param('booking_date'), $post_id);
            update_field('booking_time', $request->get_param('booking_time'), $post_id);
            update_field('product_id', $product_id, $post_id);
            update_field('booking_status', 'confirmed', $post_id);
            update_field('terms_accepted', true, $post_id);
            update_field('terms_accepted_date', current_time('mysql'), $post_id);

            return array(
                'success' => true,
                'booking_id' => $booking->get_id(),
                'post_id' => $post_id,
                'message' => 'Booking created successfully'
            );
        }

        return new WP_Error('post_creation_failed', 'Failed to create booking record', array('status' => 500));

    } catch (Exception $e) {
        return new WP_Error('booking_creation_failed', $e->getMessage(), array('status' => 500));
    }
}

// Helper function to verify reCAPTCHA
function verify_recaptcha($token) {
    error_log('=== reCAPTCHA Verification Debug ===');
    error_log('Token received: ' . substr($token, 0, 50) . '...');
    
    $secret_key = get_option('recaptcha_secret_key');
    error_log('Secret key from WordPress: ' . $secret_key);
    
    if (empty($secret_key)) {
        error_log('reCAPTCHA secret key is not configured');
        return new WP_Error('recaptcha_not_configured', 'reCAPTCHA is not configured', array('status' => 500));
    }

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => $secret_key,
        'response' => $token
    );

    error_log('Sending verification request to Google...');
    
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        error_log('Failed to verify reCAPTCHA token');
        return new WP_Error('recaptcha_verification_failed', 'Failed to verify reCAPTCHA token', array('status' => 500));
    }

    error_log('Google response: ' . $response);
    
    $result = json_decode($response);
    
    if (!$result->success) {
        error_log('reCAPTCHA verification failed: ' . print_r($result->{'error-codes'}, true));
        return new WP_Error('invalid_recaptcha', 'Invalid reCAPTCHA token', array('status' => 400));
    }

    error_log('reCAPTCHA verification successful');
    return true;
}

// Register reCAPTCHA settings
function register_recaptcha_settings() {
    register_setting('general', 'recaptcha_site_key', array(
        'type' => 'string',
        'description' => 'Google reCAPTCHA Site Key',
        'sanitize_callback' => 'sanitize_text_field',
        'show_in_rest' => true,
        'default' => '6Lez4zErAAAAAPakygMDjCAZ2yRZt-hVSKbGQNJ0'
    ));

    register_setting('general', 'recaptcha_secret_key', array(
        'type' => 'string',
        'description' => 'Google reCAPTCHA Secret Key',
        'sanitize_callback' => 'sanitize_text_field',
        'show_in_rest' => true,
        'default' => '6Lez4zErAAAAADLh3bipgwi75nB6a9_01BQg9aBI'
    ));

    add_settings_section(
        'recaptcha_settings_section',
        'reCAPTCHA Settings',
        'recaptcha_settings_section_callback',
        'general'
    );

    add_settings_field(
        'recaptcha_site_key',
        'Site Key',
        'recaptcha_site_key_callback',
        'general',
        'recaptcha_settings_section'
    );

    add_settings_field(
        'recaptcha_secret_key',
        'Secret Key',
        'recaptcha_secret_key_callback',
        'general',
        'recaptcha_settings_section'
    );
}
add_action('admin_init', 'register_recaptcha_settings');

function recaptcha_settings_section_callback() {
    echo '<p>Enter your Google reCAPTCHA keys here. You can get these keys from <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a>.</p>';
}

function recaptcha_site_key_callback() {
    $site_key = get_option('recaptcha_site_key');
    echo '<input type="text" name="recaptcha_site_key" value="' . esc_attr($site_key) . '" class="regular-text">';
}

function recaptcha_secret_key_callback() {
    $secret_key = get_option('recaptcha_secret_key');
    echo '<input type="password" name="recaptcha_secret_key" value="' . esc_attr($secret_key) . '" class="regular-text">';
}

// This is a test edit to show that I can modify the file
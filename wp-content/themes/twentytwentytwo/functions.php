<?php
 
/**
 * Twenty Twenty-Two functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Two
 * @since Twenty Twenty-Two 1.0
 */ 

if ( ! function_exists( 'twentytwentytwo_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

	}

endif;

add_action( 'after_setup_theme', 'twentytwentytwo_support' );

if ( ! function_exists( 'twentytwentytwo_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'twentytwentytwo-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'twentytwentytwo-style' );

	}

endif;

add_action( 'wp_enqueue_scripts', 'twentytwentytwo_styles' );

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';


add_action( 'rest_api_init',  'append_taxonomy_meta_data_to_api_callback' , PHP_INT_MAX );

function append_taxonomy_meta_data_to_api_callback()
{

	register_rest_field(
			'post',
			'post name',
			array(
				'get_callback'    => 'custom_meta_data_callback',
				'update_callback' => null,
				'schema'          => null,
			)
		);
}

// Function to execute the custom cron job
function schedule_push_batches() {
	// Get users with 'thirde_reminder_status' set to true
	$users_with_reminder_status = get_users(array(
		'meta_key'     => 'thirde_reminder_status',
		'meta_value'   => true,
		'meta_compare' => '=',
	));

	$current_time = current_time('timestamp'); // Get the current timestamp
	$message_batch = array();
	foreach ($users_with_reminder_status as $user) {
		$user_id = $user->ID;
		
		// Get user meta values
		$time_period = get_user_meta($user_id, 'thirde_time_period', true);
		$last_timestamp = get_user_meta($user_id, 'thirde_last_timestamp', true);

		// Calculate the next expected timestamp based on the time period
		$next_expected_timestamp = strtotime("+$time_period", $last_timestamp);
		
		// Check if it's time to send a message
		if ($next_expected_timestamp <= $current_time) {
			// Time to send a message for this user

			// Get other user meta values
			$device_token = get_user_meta($user_id, 'thirde_device_token', true);
			$title = get_user_meta($user_id, 'thirde_message_title', true);
			$body = get_user_meta($user_id, 'thirde_message_body', true);
			array_push($message_batch, array(
				'token' =>$device_token,
				'data' => array($title,$body)
			));
			// Send the message using $device_token, $title, and $body

			// Update the 'thirde_last_timestamp' to the current time
			update_user_meta($user_id, 'thirde_last_timestamp', $current_time);
		}
	}

}

function custom_meta_data_callback()
{
	return json_encode(array('status' => 'success', 'post' => array()));
}

function my_customize_rest_cors() {
  remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
  add_filter( 'rest_pre_serve_request', function( $value ) {
    header( 'Access-Control-Allow-Origin: *' );
    header( 'Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT' );
    header( 'Access-Control-Allow-Credentials: true' );
    header( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization' );
    header( 'Access-Control-Expose-Headers: Link', false );
    return $value;
  } );
}
add_action( 'rest_api_init', 'my_customize_rest_cors', 15 );

function add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
    header( 'Access-Control-Allow-Origin: *' );
    header( 'Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT' );
    header( 'Access-Control-Allow-Credentials: true' );
    header( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization' );
    header( 'Access-Control-Expose-Headers: Link', false );
}
add_action('init','add_cors_http_header');

add_filter(
    'jwt_auth_cors_allow_headers',
    function ( $headers ) {
        // Modify the headers here.
        return $headers;
    }
);

require_once dirname( __FILE__ ) . '/includes/BalloonController.php';
require_once dirname(__FILE__) . '/vendor/autoload.php'; 
add_filter(
    'jwt_auth_valid_credential_response',
    function ( $response, $user ) {
        // Modify the response here._tenant_post_id
        $user_id = $user->ID;
        $tenent_id = get_user_meta($user_id, '_tenant_post_id');
        $response['data']['default_post_id'] = $tenent_id;
        $response['data']['user_role'] = $user->roles[0];
        return $response;
    },
    10,
    2
);
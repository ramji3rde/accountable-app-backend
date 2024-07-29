<?php

if ( ! class_exists( 'Balloon_Controler', false ) ) : 


class Balloon_Controler {

	function __construct()
	{
		$this->init_action();
	}

	function init_action()
	{
		// api Controller
		require_once dirname( __FILE__ ) . '/api/API_BaseController.php';
		require_once dirname( __FILE__ ) . '/api/API_UserController.php';
		require_once dirname( __FILE__ ) . '/api/API_ProjectsController.php';
		require_once dirname( __FILE__ ) . '/api/API_TenantsController.php';
		require_once dirname( __FILE__ ) . '/api/API_VendorController.php';
		require_once dirname( __FILE__ ) . '/api/API_PhotosController.php';
		require_once dirname( __FILE__ ) . '/api/API_SecurityInfoController.php';
		require_once dirname( __FILE__ ) . '/api/API_EmergencyContactsController.php';
		
		//rest routes
		require_once dirname( __FILE__ ) . '/api/API_RoutesController.php';
		require_once dirname( __FILE__ ) . '/BalloonHelper.php';

		// files
		//require_once dirname( __FILE__ ) . 'BalloonHelper.php';

	}

	public function validate_token( $return_response = true ) {
		/**
		 * Looking for the HTTP_AUTHORIZATION header, if not present just
		 * return the user.
		 */
		$headerkey = apply_filters( 'jwt_auth_authorization_header', 'HTTP_AUTHORIZATION' );
		$auth      = isset( $_SERVER[ $headerkey ] ) ? $_SERVER[ $headerkey ] : false;

		// Double check for different auth header string (server dependent).
		if ( ! $auth ) {
			$auth = isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : false;
		}

		if ( ! $auth ) {
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_no_auth_header',
					'message'    => $this->messages['jwt_auth_no_auth_header'],
					'data'       => array(),
				)
			);
		}

		/**
		 * The HTTP_AUTHORIZATION is present, verify the format.
		 * If the format is wrong return the user.
		 */
		list($token) = sscanf( $auth, 'Bearer %s' );

		if ( ! $token ) {
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_auth_header',
					'message'    => $this->messages['jwt_auth_bad_auth_header'],
					'data'       => array(),
				)
			);
		}

		// Get the Secret Key.
		$secret_key = defined( 'JWT_AUTH_SECRET_KEY' ) ? JWT_AUTH_SECRET_KEY : false;

		if ( ! $secret_key ) {
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'JWT is not configured properly.', 'jwt-auth' ),
					'data'       => array(),
				),
				403
			);
		}

		// Try to decode the token.
		try {
			$alg     = $this->get_alg();
			$payload = JWT::decode( $token, $secret_key, array( $alg ) );

			// The Token is decoded now validate the iss.
			if ( $payload->iss !== $this->get_iss() ) {
				// The iss do not match, return error.
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'jwt_auth_bad_iss',
						'message'    => __( 'The iss do not match with this server.', 'jwt-auth' ),
						'data'       => array(),
					),
					403
				);
			}

			// Check the user id existence in the token.
			if ( ! isset( $payload->data->user->id ) ) {
				// No user id in the token, abort!!
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'jwt_auth_bad_request',
						'message'    => __( 'User ID not found in the token.', 'jwt-auth' ),
						'data'       => array(),
					),
					403
				);
			}

			// So far so good, check if the given user id exists in db.
			$user = get_user_by( 'id', $payload->data->user->id );

			if ( ! $user ) {
				// No user id in the token, abort!!
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'jwt_auth_user_not_found',
						'message'    => __( "User doesn't exist", 'jwt-auth' ),
						'data'       => array(),
					),
					403
				);
			}

			// Check extra condition if exists.
			$failed_msg = apply_filters( 'jwt_auth_extra_token_check', '', $user, $token, $payload );

			if ( ! empty( $failed_msg ) ) {
				// No user id in the token, abort!!
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'jwt_auth_obsolete_token',
						'message'    => __( 'Token is obsolete', 'jwt-auth' ),
						'data'       => array(),
					),
					403
				);
			}

			// Everything looks good, return the payload if $return_response is set to false.
			if ( ! $return_response ) {
				return $payload;
			}

			$response = array(
				'success'    => true,
				'statusCode' => 200,
				'code'       => 'jwt_auth_valid_token',
				'message'    => __( 'Token is valid', 'jwt-auth' ),
				'data'       => array(),
			);

			$response = apply_filters( 'jwt_auth_valid_token_response', $response, $user, $token, $payload );

			// Otherwise, return success response.
			return new WP_REST_Response( $response );
		} catch ( Exception $e ) {
			// Something is wrong when trying to decode the token, return error response.
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_invalid_token',
					'message'    => $e->getMessage(),
					'data'       => array(),
				),
				403
			);
		}
	}


	/**
	 * Determine if given response is an error response.
	 *
	 * @param mixed $response The response.
	 * @return boolean
	 */
	public function is_error_response( $response ) {
		if ( ! empty( $response ) && property_exists( $response, 'data' ) && is_array( $response->data ) ) {
			if ( ! isset( $response->data['success'] ) || ! $response->data['success'] ) {
				return true;
			}
		}

		return false;
	}
}
endif;

new Balloon_Controler();

?>
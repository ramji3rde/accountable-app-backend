<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class API_UserControler extends API_BaseController {

	public function __construct() 
	{  

		$this->init_action(); 
	}

	public function init_action()
	{
		
	} 

	public function check_token_validatation()
	{
	 	$payload = $this->validate_token( false );

	    if ( $this->is_error_response( $payload ) ) {

	    	return $payload;

	    }else{


		}

	} 

	public function user_api_create_vendor_and_client(WP_REST_Request $request)
	{
 

		$response = array();

		$fname    = $request->get_param( 'fname' );
		$lname    = $request->get_param( 'lname' );
		$user_type    = $request->get_param( 'user_type' );
 		$email    = $request->get_param( 'email' );
		$password    = $request->get_param( 'password' ); 
		$property_name    = $request->get_param( 'property_name' );
		$location = $request->get_param( 'location' );
		$response = array();
		 
		if(empty($email)){ 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Please enter email address.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  
		} 
		if(empty($password)){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Please enter password.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
		} 

		if(empty($user_type)){

 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_user_bad_config',
					'message'    => __( 'Please enter user type.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
		}
		if($user_type == 'manager'){
			$API_BaseController = new API_BaseController();
			$logged_user_id = $API_BaseController->custom_validate_token($_SERVER);
			$logged_user_id = (int) $logged_user_id;

			$logged_user_info = get_user_by('ID', $logged_user_id);
			if( isset($logged_user_info->ID )){
				$user_data = get_userdata( $logged_user_info->ID );
	    		$user_roles = $user_data->roles;
	    		if( ! in_array( 'app_admin', $user_roles ) ) {
	    			return new WP_REST_Response(
						array(
							'success'    => false,
							'statusCode' => 403,
							'code'       => 'error',
							'message'    => __( 'You can not create manager.', 'jwt-auth' ),
							'data'       => array(),
						),
						
					);
					exit();	
	    		}
				
			}else{
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'error',
						'message'    => __( 'Invalid loggedin user.', 'jwt-auth' ),
						'data'       => array(),
					),
					
				);
				exit();	  
			}	        
	    }
		//  if ( ($user_type != 'vendor') || ($user_type != 'client') ) {
		// 	// code...

		// 	return new WP_REST_Response(

		// 		array(
		// 			'success'    => false,
		// 			'statusCode' => 403,
		// 			'code'       => 'jwt_auth_bad_config',
		// 			'message'    => __( 'Please enter valid user type : vendor/client.', 'jwt-auth' ),
		// 			'data'       => array(),
		// 		),
		// 		403
		// 	);

		// }

		// check if email exists already
		if ( email_exists( $email ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'This email is already exist.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

		$userReg=wp_insert_user(array(
			'user_login'     => $email,
			'user_email'     => $email,
			'user_pass'      => $password,
			'user_nicename'  => $fname,
			'display_name'   => $fname.' '.$lname,
			'nickname'       => $fname,
			'first_name'     => $fname,
			'last_name'     => $lname, 
		));

		if( isset( $userReg ) && is_numeric( $userReg ) ){

			$u = new WP_User($userReg);
	        $u->remove_role('subscriber');
	        $u->remove_role('customer');

	        if($user_type == 'client'){
	        	$u->add_role('client');
	        	update_user_meta($userReg, 'user_type', 'client');
	        }else if($user_type == 'manager'){
	        	$u->add_role('app_manager');
	        	update_user_meta($userReg, 'user_type', 'app_manager');

	        }else{
	        	$u->add_role('app_admin');
	        	update_user_meta($userReg, 'user_type', 'app_admin');// default role
	        } 
	        update_user_meta($userReg, 'property_name', $property_name);
	        update_user_meta($userReg, 'location', $location);
	        update_user_meta($userReg, 'first_name', $fname);
	        update_user_meta($userReg, 'last_name', $lname); 

	        // create propertyMap for all users
	        $map_args = array(
				'post_title'     => 'oakland',
				'post_type'     => 'project_maps',
				'post_content'     => 'oakland',
				'post_status'   => 'publish',
				'post_author'   => $userReg,
				 
			);
		    
		    $mapRes = wp_insert_post( $map_args );
		    if( isset( $mapRes ) && is_numeric( $mapRes ) ){
				update_user_meta($userReg, 'any_project_map_created', 'yes');
			}

	        return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Sucessfull create user', 'jwt-auth' ),
					'data'       => array('user_id' => $userReg),
				),
				
			); 

		} else {
			$massage ='';
			foreach ($userReg->errors as $key => $errors) {
	            $massage .= $errors[0];
	        } 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( $massage, 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		} 

 		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Something went wrong, Please try again.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	  

	}
	
	public function create_sub_user(WP_REST_Request $request)
	{
 

		$response = array();

		$fname    = $request->get_param( 'fname' );
		$lname    = $request->get_param( 'lname' );
		$user_type    = $request->get_param( 'user_type' );
 		$email    = $request->get_param( 'email' );
		$password    = $request->get_param( 'password' );
		$password    = wp_generate_password(12, true, true );
		$password = base64_encode( $password);
		$property_name    = $request->get_param( 'property_name' );
		$location = $request->get_param( 'location' );
		$response = array();
		 
		if(empty($email)){ 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Please enter email address.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  
		} 
		if(empty($password)){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Please enter password.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
		} 

		if(empty($user_type)){

 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_user_bad_config',
					'message'    => __( 'Please enter user type.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
		}
		 
		$API_BaseController = new API_BaseController();
		$logged_user_id = $API_BaseController->custom_validate_token($_SERVER);
		$logged_user_id = (int) $logged_user_id;
	 
		$logged_user_info = get_user_by('ID', $logged_user_id);
		if( isset($logged_user_info->ID )){
			$user_data = get_userdata( $logged_user_info->ID );
    		$user_roles = $user_data->roles;
    		if( ( ! in_array( 'app_admin', $user_roles ) ) && ( ! in_array( 'administrator', $user_roles ) ) ) {
    			return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'error',
						'message'    => __( 'You can not create manager.', 'jwt-auth' ),
						'data'       => array(),
					),
					
				);
				exit();	
    		}
			
		}else{
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid loggedin user.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
			exit();	  
		}	        
	    
		 

		// check if email exists already
		if ( email_exists( $email ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'This email is already exist.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
	    
		$userReg=wp_insert_user(array(
			'user_login'     => $email,
			'user_email'     => $email,
			'user_pass'      => $password,
			'user_status' => 2,
			'user_nicename'  => $fname,
			'display_name'   => $fname.' '.$lname,
			'nickname'       => $fname,
			'first_name'     => $fname,
			'last_name'     => $lname, 
		));

		if( isset( $userReg ) && is_numeric( $userReg ) ){

			$u = new WP_User($userReg);
	        $u->remove_role('subscriber');
	        $u->remove_role('customer');

	        if( '' != trim($user_type)){
	        	$u->add_role(trim($user_type));
	        	update_user_meta($userReg, 'user_type',  trim($user_type));
	        }else{
	        	$u->add_role('subscriber');
	        	update_user_meta($userReg, 'user_type', 'subscriber');// default role
	        } 
	        update_user_meta($userReg, 'property_name', $property_name);
	        update_user_meta($userReg, 'location', $location);
	        update_user_meta($userReg, 'first_name', $fname);
	        update_user_meta($userReg, 'last_name', $lname); 
	        update_user_meta($userReg, 'user_created_by', $logged_user_id); 
	        update_user_meta($userReg, 'user_login_access', '');
	        $mail_status = $this->send_cloudemail_user_join_link( $userReg );
	        if( $mail_status ){
	        	// create propertyMap for all users
		        // $map_args = array(
				// 	'post_title'     => 'oakland',
				// 	'post_type'     => 'project_maps',
				// 	'post_content'     => 'oakland',
				// 	'post_status'   => 'publish',
				// 	'post_author'   => $userReg,
					 
				// );
			    
			    // $mapRes = wp_insert_post( $map_args );
			    // if( isset( $mapRes ) && is_numeric( $mapRes ) ){
				// 	update_user_meta($userReg, 'any_project_map_created', 'yes');
				// }

		        return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'jwt_auth_bad_config',
						'message'    => __( 'User successfully created. And sent join email.', 'jwt-auth' ),
						'data'       => array('user_id' => $userReg),
					),
					
				); 
	        }else{

		        return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'jwt_auth_bad_config',
						'message'    => __( 'User successfully created. And but join email is not sending.', 'jwt-auth' ),
						'data'       => array('user_id' => $userReg),
					),
					
				); 
	        }
	        

		} else {
			$massage ='';
			foreach ($userReg->errors as $key => $errors) {
	            $massage .= $errors[0];
	        } 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( $massage, 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		} 

 		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Something went wrong, Please try again.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	  

	}
	


	public function access_enable_disable_sub_user(WP_REST_Request $request)
	{
 

		$response = array();

		$user_id    = $request->get_param( 'user_id' );
	 	$user_login_access    = $request->get_param( 'user_login_access' );
	 	$API_BaseController = new API_BaseController();
		$logged_user_id = $API_BaseController->custom_validate_token($_SERVER);
		$logged_user_id = (int) $logged_user_id;
		$response = array();
		 
		if(empty($user_id)){ 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Please enter user id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  
		} 
		 

		if($logged_user_id < 1){

 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_user_bad_config',
					'message'    => __( 'Invalid loggedin user.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
		}
		update_user_meta($user_id, 'user_login_access', $user_login_access);  
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Login access successfully updated', 'jwt-auth' ),
					'data'       => array('user_id' => $user_id),
				),
				
			);  
		 exit();
		
	}

	public function get_sub_users(WP_REST_Request $request)
	{ 
 
		$search_by_keyword    = $request->get_param( 'search_by_keyword' );
    	$paged = ($request->get_param('paged')) ? $request->get_param('paged') : 1;
    	$posts_per_page = ($request->get_param('posts_per_page')) ? $request->get_param('posts_per_page') : 10;
    	$sort_by_field    = $request->get_param( 'sort_by_field' );
    	if( 1 === $paged ):
    		$offset = 0;
    	else:
    		$offset= ($paged-1)*$posts_per_page;
    	endif;
    	$orderby = "date";
    	$order = "ASC";

    	if( 'a-z' == $sort_by_field ):
    		$orderby = "display_name";
    		$order = "ASC";
    	endif;
    	if( 'z-a' == $sort_by_field ):
    		$orderby = "display_name";
    		$order = "DESC";
    	endif;
		$response = array();
 
	 	$API_BaseController = new API_BaseController();
		$logged_user_id = $API_BaseController->custom_validate_token($_SERVER);
		$logged_user_id = (int) $logged_user_id;
		$response = array();

		if($logged_user_id < 1){

 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_user_bad_config',
					'message'    => __( 'Invalid loggedin user.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
		}
		$args = array(
    		'role' => 'app_manager',
    		'number' => $posts_per_page,
    		'offset' => $offset,
    		'order' => $order,
			'orderby' => $orderby,


    	);
    	if( '' != trim( $search_by_keyword ) ):
    		// $args['meta_query'] = array(
    		// 		'relation' => 'OR',
				  //       array(
				  //           'value' => $search_by_keyword,
				  //           'compare' => "LIKE"
				  //       )
				        
				        
				  //   );
    		$args['meta_query'] = array(
    				'relation' => 'AND',
    				array(
				            'key' => 'user_created_by',
				            'value' => $post_author,
				            'compare' => '='
				        ),
				        array(
				        'relation' => 'OR',
						        array(
				            'value' => $logged_user_id,
						            'compare' => "LIKE"
						        )
				    		)
				        
				    );
    	else:
    		$args['meta_query'] = array(
    				'relation' => 'AND',
			        array(
			            'key' => 'user_created_by',
			            'value' => $logged_user_id,
			            'compare' => '='
			        ),
				         
				        
				    );	
    	endif;
		// find sellers according to search keyword
            // $sub_users = get_users( $args );
            $sub_users = new WP_User_Query( $args );
     $total_managers = $sub_users->get_total();
        $all_sub_users = array(); 
        foreach($sub_users->results as $single_user ){
        	$temp = array();
        	$temp = (object) $temp;
        	$temp->ID = $single_user->ID;
        	$temp->user_login = $single_user->user_login;
        	$temp->user_email = $single_user->user_email;
        	$temp->display_name = $single_user->display_name;
        	$temp->first_name = get_user_meta( $single_user->ID, 'first_name', true);
        	$temp->last_name = get_user_meta( $single_user->ID, 'last_name', true);
        	$temp->user_login_access = get_user_meta( $single_user->ID, 'user_login_access', true);
        	$temp->user_registered = $single_user->user_registered;
        	array_push( $all_sub_users, $temp);
        }    
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'get all sub users', 'jwt-auth' ),
					'data'       => $all_sub_users,
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'total_managers' => $total_managers,
				),
				
			);  
		 exit();
		
	}

	public function user_api_forget_password(WP_REST_Request $request)
	{

		$email_or_username = $request->get_param( 'email' ); 

		$response = array();
		 
		if(empty($email_or_username))
		{ 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter username / email address.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  
		} 

		$user_id = 0; 

		if ( email_exists( $email_or_username ) ) 
		{ 
			$user_id = email_exists( $email_or_username ); 
			  
	    }else if(username_exists($email_or_username))
	    {
	    	$user_id = username_exists( $email_or_username ); 
	    }

	    if($user_id > 0)
	    {
	    	$user_data = get_userdata( $user_id );
	    	$user_roles = $user_data->roles;
	    	if ( in_array( 'tenant', $user_roles ) ) {
	    		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( "Invalid email address.", 'jwt-auth' ),
					'data'       => array(''),
				),
				);
	    	}
    	 
	    	$key = wp_generate_password( 20, false );
	    	$key_md5 = md5( $key );
	    	$user_info = get_user_by("ID", $user_id);
    		$user_id_md5 = md5( $user_info->ID);
    		$user_login_md5 = md5( $user_info->user_login);
    		$forgot_password_key = $key_md5."".$user_id_md5."".$user_login_md5;
    		update_user_meta( $user_id, "forgot_password_key", $forgot_password_key );
    		$email_statis  = $this->send_cloudemail_forgot_password_email( $user_id );
    		if( $email_statis ){
    			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Verify mail has been sent successfully', 'jwt-auth' ),
						'data'       => array('user_id' => $user_id),
					),
					
				);
				exit();
    		}else{
    			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( $email_statis, 'jwt-auth' ),
					'data'       => array(''),
				),
				
			);
    			exit();
    		}
	    	

	    }else{
	    	
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Your details are incorrect. please try again.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
	    }

	}

	/**
	 * Send forgot password email from cloudemail 
	 */
	public function send_cloudemail_forgot_password_email( $user_id = 0){
		$user_info = get_user_by( 'ID', $user_id);
		if( ! isset( $user_info->ID ) ){
			return "Invalid User";
		}
	 
		$admin_email =  get_option( 'admin_email' );
		$my_app_url = get_option('my_app_url');
		$from = "charles@getaccountableapp.com";
		 
		$full_name = $user_info->first_name .' '. $user_info->last_name;
		 
		$to =  $user_info->user_email;
		// $to =  "yogendra3rde@gmail.com";
		$site_title = get_bloginfo( 'name' );
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
		$reset_token = get_user_meta( $user_id, "forgot_password_key", true );
		
		// $encrypt_pro = base64_encode( $project_id );
		$path = 'auth/reset_password';
		// $string = ':3000/'.$path.'?reset_token='.$reset_token;
		// $resest_link = site_url($string);
		// $resest_link = str_replace('/:3000', ':3000', $resest_link);

		$string = '/'.$path.'?reset_token='.$reset_token;
		$resest_link = $my_app_url.''.$string;
		// $resest_link = str_replace('/:3000', ':3000', $resest_link);
		$subject = "Password Reset Request for ".$site_title;
		$app_admin_id = get_user_meta( $user_id, 'user_created_by', true );
		$app_admin_id_1 = get_user_meta( $user_id, 'supportteam_user_created_by', true );
		$app_admin_name = get_user_meta( $app_admin_id, 'first_name', true ). ' '.get_user_meta( $app_admin_id, 'last_name', true );
		$app_admin_name_1 = get_user_meta( $app_admin_id_1, 'first_name', true ). ' '.get_user_meta( $app_admin_id_1, 'last_name', true );
		if( '' != trim( $app_admin_name ) ){
			$site_title = trim( $app_admin_name );
		}else if( '' != trim( $app_admin_name_1 ) ){
			$site_title = trim( $app_admin_name_1 );
		}else{
			$site_title = trim( $site_title );
		}


		$message = "<p>Hi ".$full_name.",</p>
		<p>Forgot your password?</p>
		<span>We received a request to reset the password for your account.</span>
		<br/> 
		<p>To reset your password, click on the button below </p>
		<p><a href='".$resest_link."'>Reset Password</a></p>
		
		<p>Or copy and paste the URL into browser</p>
		<p><a href='".$resest_link."'>".$resest_link."</a></p>
		<br/><br/>	
		<p>Thanks<br/><span>". $site_title."</span></p>

		 
		";
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];                     //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];                     //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password'];                               //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $full_name);     //Add a recipient
		    // $mail->addAddress('ellen@example.com');               //Name is optional
		    $mail->addReplyTo( $admin_email, $site_title);
		    // $mail->addCC('cc@example.com');
		    // $mail->addBCC('bcc@example.com');

		    //Attachments
		    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

		    //Content
		    $mail->isHTML(true);                                  //Set email format to HTML
		    $mail->Subject = $subject;
		    $mail->Body    = $message;
		    // $mail->AltBody = '';

		    $mail_status = $mail->send();
		    return $mail_status;
		} catch (Exception $e) {
			$mail_status = 0;
			return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	 	
		// $mail_status = wp_mail( $to, $subject, $message, $headers );
		return $mail_status;

	}

	/**
	 * Send forgot password email from cloudemail 
	 */
	public function send_cloudemail_user_join_link( $user_id = 0){
		$user_info = get_user_by( 'ID', $user_id);
		if( ! isset( $user_info->ID ) ){
			return "Invalid User";
		}
	 
		$admin_email =  get_option( 'admin_email' );
		$my_app_url = get_option('my_app_url');
		$from = "charles@getaccountableapp.com";
		 
		$full_name = $user_info->first_name .' '. $user_info->last_name;
		 
		$to =  $user_info->user_email;
		// $to =  "yogendra3rde@gmail.com";
		$site_title = get_bloginfo( 'name' );
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
		$activation_key = md5($user_id.'@f76hlds'.rand(345678, 987654));
		update_user_meta( $user_id, "_user_activation_key", $activation_key );
		
		// $encrypt_pro = base64_encode( $project_id );
		$path = '/auth/user_join';
		// $string = ':3000/'.$path.'?activation_key='.$activation_key.'&access_key='.base64_encode($user_id);
		$string = $path.'?activation_key='.$activation_key.'&access_key='.base64_encode($user_id);
		// $resest_link = site_url($string);
		$resest_link = $my_app_url.''.$string;
		// $resest_link = str_replace('/:3000', ':3000', $resest_link);
		$subject = "Join  ".$site_title;
		$app_admin_id = get_user_meta( $user_id, 'user_created_by', true );
		$app_admin_id_1 = get_user_meta( $user_id, 'supportteam_user_created_by', true );
		$app_admin_name = get_user_meta( $app_admin_id, 'first_name', true ). ' '.get_user_meta( $app_admin_id, 'last_name', true );
		$app_admin_name_1 = get_user_meta( $app_admin_id_1, 'first_name', true ). ' '.get_user_meta( $app_admin_id_1, 'last_name', true );
		if( '' != trim( $app_admin_name ) ){
			$site_title = trim( $app_admin_name );
		}else if( '' != trim( $app_admin_name_1 ) ){
			$site_title = trim( $app_admin_name_1 );
		}else{
			$site_title = trim( $site_title );
		}
		$message = "<p>Hi ".$full_name.",</p>
		<p>Joining Link</p>
		<span>You can join with us.</span>
		<br/> 
		<p>For the joining, click on the button below </p>
		<p><a href='".$resest_link."'>JOIN NOW</a></p>
		
		<p>Or copy and paste the URL into browser</p>
		<p><a href='".$resest_link."'>".$resest_link."</a></p>
		<br/><br/>	
		<p>Thanks<br/><span>". $site_title."</span></p>

		 
		";
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];                     //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];                    //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password'];                               //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $full_name);     //Add a recipient
		    // $mail->addAddress('ellen@example.com');               //Name is optional
		    $mail->addReplyTo( $admin_email, $site_title);
		    // $mail->addCC('cc@example.com');
		    // $mail->addBCC('bcc@example.com');

		    //Attachments
		    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

		    //Content
		    $mail->isHTML(true);                                  //Set email format to HTML
		    $mail->Subject = $subject;
		    $mail->Body    = $message;
		    // $mail->AltBody = '';

		    $mail_status = $mail->send();
		    return $mail_status;
		} catch (Exception $e) {
			$mail_status = 0;
			return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	 	
		// $mail_status = wp_mail( $to, $subject, $message, $headers );
		return $mail_status;

	}

	/**
	 * Send login detail cloudemail 
	 */
	public function send_cloudemail_user_login_data( $user_id = 0, $password){
		$user_info = get_user_by( 'ID', $user_id);
		if( ! isset( $user_info->ID ) ){
			return "Invalid User";
		}
	 
		$admin_email =  get_option( 'admin_email' );
		$my_app_url = get_option('my_app_url');
		$from = "charles@getaccountableapp.com";
		 
		$full_name = $user_info->first_name .' '. $user_info->last_name;
		 
		$to =  $user_info->user_email;
		// $to =  "yogendra3rde@gmail.com";
		$site_title = get_bloginfo( 'name' );
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
		 
		$subject = "Login Detail  ".$site_title;
		
		$app_admin_id = get_user_meta( $user_id, 'user_created_by', true );
		$app_admin_id_1 = get_user_meta( $user_id, 'supportteam_user_created_by', true );
		$app_admin_name = get_user_meta( $app_admin_id, 'first_name', true ). ' '.get_user_meta( $app_admin_id, 'last_name', true );
		$app_admin_name_1 = get_user_meta( $app_admin_id_1, 'first_name', true ). ' '.get_user_meta( $app_admin_id_1, 'last_name', true );
		if( '' != trim( $app_admin_name ) ){
			$site_title = trim( $app_admin_name );
		}else if( '' != trim( $app_admin_name_1 ) ){
			$site_title = trim( $app_admin_name_1 );
		}else{
			$site_title = trim( $site_title );
		}

		$message = "<p>Hi ".$full_name.",</p>
		<p>Thank you for the account activation.</p>
		<span>You can login details.</span>
		<br/> 
		<p> Username or Email : <strong>".$user_info->user_email."</strong></p>
		<p> Password : <strong>".$password."</strong></p>
		<br/> 
		<p>For the login, click on the button below </p>
		<p><a href='".$my_app_url."'>Login Now</a></p>
		
		<p>Thanks<br/><span>". $site_title."</span></p>

		 
		";
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];                     //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];                     //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password'];                              //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $full_name);     //Add a recipient
		    // $mail->addAddress('ellen@example.com');               //Name is optional
		    $mail->addReplyTo( $admin_email, $site_title);
		    // $mail->addCC('cc@example.com');
		    // $mail->addBCC('bcc@example.com');

		    //Attachments
		    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

		    //Content
		    $mail->isHTML(true);                                  //Set email format to HTML
		    $mail->Subject = $subject;
		    $mail->Body    = $message;
		    // $mail->AltBody = '';

		    $mail_status = $mail->send();
		    return $mail_status;
		} catch (Exception $e) {
			$mail_status = 0;
			return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	 	
		// $mail_status = wp_mail( $to, $subject, $message, $headers );
		return $mail_status;

	}

	public function user_api_forget_password_otp(WP_REST_Request $request)
	{


		
	}
	/**
	 * Deprycated API 
	 */ 
	public function user_api_new_password(WP_REST_Request $request)
	{
  
        $response = array(); 

        $password = $request->get_param( 'password' ); 

        $user_id = $request->get_param( 'user_id' );  

        $user = get_user_by( 'id', $user_id );

        if ( $user ) {

            wp_set_password( $password, $user_id );

            return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'New Password successfully created', 'jwt-auth' ),
					'data'       => array('user_id' => $user_id),
				),
				
			);  

        }  

    	return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Something went wrong, Please try again.', 'jwt-auth' ),
				'data'       => array(),
			),
			
		); 	     
		
	}

	public function user_api_reset_password(WP_REST_Request $request)
	{
  
        $response = array(); 

        $password = $request->get_param( 'password' ); 
        $reset_token = $request->get_param( 'reset_token' );  
       	$user_list = get_users(
                array(
                    'fields'         => 'ids',
                    'meta_query'      => array(
                        'relation' => 'AND',
                        array(
                            'key'       => 'forgot_password_key',
                            'value'          => $reset_token,
                            'compare' => '=',
                        ),
                        
                    )
                )
            );
        if( empty( $user_list )) {
        	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid reset token.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
        }
        $user_id = $user_list[0];	 	
        $user = get_user_by( 'id', $user_id );

        if ( $user ) {

            wp_set_password( $password, $user_id );
            delete_user_meta( $user_id, "forgot_password_key", "");
            return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'New Password successfully created', 'jwt-auth' ),
					'data'       => array('user_id' => $user_id),
				),
				
			);  

        }  

    	return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Something went wrong, Please try again.', 'jwt-auth' ),
				'data'       => array(),
			),
			
		); 	     
		
	}

	/**
	 * Join user as manager. Activate manager.
	 */ 
	public function join_sub_user(WP_REST_Request $request)
	{
//   		ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
        $response = array(); 

        $activation_key = trim( $request->get_param( 'activation_key' ) ); 
        $access_key = trim( $request->get_param( 'access_key' ) );
        if( '' == $access_key ){

        }
        $access_key = base64_decode( $access_key );
       	$user_list = get_users(
                array(
                    'fields'         => 'ids',
                    'include' => array($access_key),
                    'meta_query'      => array(
                        'relation' => 'AND',
                        array(
                            'key'       => '_user_activation_key',
                            'value'          => $activation_key,
                            'compare' => '=',
                        ),
                        
                    )
                )
            );
        if( empty( $user_list )) {
        	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid activation key.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
        }
        $user_id = $user_list[0];	 	
        $user = get_user_by( 'id', $user_id );

        if ( $user ) {
        	$password = wp_generate_password(12, true, true );
            wp_set_password( $password, $user_id );
            delete_user_meta( $user_id, "_user_activation_key", "");
            update_user_meta( $user_id, "user_login_access", "1");
            $mail_status = $this->send_cloudemail_user_login_data( $user_id, $password );
            return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Join successfully.', 'jwt-auth' ),
					'data'       => array('password' => $password, 'email' => $user->user_email, 'mail_status' => $mail_status),
				),
				
			);  

        }  

    	return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'This link is not a valid link.', 'jwt-auth' ),
				'data'       => array(),
			),
			
		); 	     
		
	}
 
   /**
    * Resend user login detail
    */ 
   public function resend_login_detail( WP_REST_Request $request ){
   	$response = array(); 

         $user_id = (int) trim( $request->get_param( 'user_id' ) ); 
        
         
         if( empty( $user_id )) {
        	return new WP_REST_Response(
				array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'error',
						'message'    => __( 'Enter user id.', 'jwt-auth' ),
						'data'       => array(),
					),
					
				); 	
         }
        	$user_info = get_user_by('ID', $user_id);
			if( isset($user_info->ID )){
				   $password = wp_generate_password(12, true, true );
				   wp_set_password( $password, $user_id );
				   $mail_status = $this->resend_cloudemail_user_login_data( $user_id, $password );
				   if( $mail_status ){
				   		return new WP_REST_Response(
								array(
									'success'    => true,
									'statusCode' => 200,
									'code'       => 'success',
									'message'    => __( 'Login detail successfully sent.', 'jwt-auth' ),
									'data'       => array('mail_status' => $mail_status),
								),
								
							);  
				   }
	            
				   return new WP_REST_Response(
						array(
							'success'    => false,
							'statusCode' => 403,
							'code'       => 'error',
							'message'    => __( 'Email is not sending.', 'jwt-auth' ),
							'data'       => array(),
						),
						
					); 
			}
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'User not exists.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
   }


	/**
	 * Resend login detail cloudemail 
	 */
	public function resend_cloudemail_user_login_data( $user_id = 0, $password){
		$user_info = get_user_by( 'ID', $user_id);
		if( ! isset( $user_info->ID ) ){
			return "Invalid User";
		}
	 
		$admin_email =  get_option( 'admin_email' );
		$my_app_url = get_option('my_app_url');
		$from = "charles@getaccountableapp.com";
		 
		$full_name = $user_info->first_name .' '. $user_info->last_name;
		 
		$to =  $user_info->user_email;
		// $to =  "yogendra3rde@gmail.com";
		$site_title = get_bloginfo( 'name' );
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
		 
		$subject = "Login Detail  ".$site_title;
		
		$app_admin_id = get_user_meta( $user_id, 'user_created_by', true );
		$app_admin_id_1 = get_user_meta( $user_id, 'supportteam_user_created_by', true );
		$app_admin_name = get_user_meta( $app_admin_id, 'first_name', true ). ' '.get_user_meta( $app_admin_id, 'last_name', true );
		$app_admin_name_1 = get_user_meta( $app_admin_id_1, 'first_name', true ). ' '.get_user_meta( $app_admin_id_1, 'last_name', true );
		if( '' != trim( $app_admin_name ) ){
			$site_title = trim( $app_admin_name );
		}else if( '' != trim( $app_admin_name_1 ) ){
			$site_title = trim( $app_admin_name_1 );
		}else{
			$site_title = trim( $site_title );
		}

		$message = "<p>Hi ".$full_name.",</p>
		<span>You can login details.</span>
		<br/> 
		<p> Username or Email : <strong>".$user_info->user_email."</strong></p>
		<p> Password : <strong>".$password."</strong></p>
		<br/> 
		<p>For the login, click on the button below </p>
		<p><a href='".$my_app_url."'>Login Now</a></p>
		
		<p>Thanks<br/><span>". $site_title."</span></p>

		 
		";
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];                     //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];                     //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password'];                               //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $full_name);     //Add a recipient
		    // $mail->addAddress('ellen@example.com');               //Name is optional
		    $mail->addReplyTo( $admin_email, $site_title);
		    // $mail->addCC('cc@example.com');
		    // $mail->addBCC('bcc@example.com');

		    //Attachments
		    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

		    //Content
		    $mail->isHTML(true);                                  //Set email format to HTML
		    $mail->Subject = $subject;
		    $mail->Body    = $message;
		    // $mail->AltBody = '';

		    $mail_status = $mail->send();
		    return $mail_status;
		} catch (Exception $e) {
			$mail_status = 0;
			return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	 	
		// $mail_status = wp_mail( $to, $subject, $message, $headers );
		return $mail_status;

	}
	public function get_profile_data(WP_REST_Request $request){
		$API_BaseController = new API_BaseController();
		$user_id = $API_BaseController->custom_validate_token($_SERVER);
		$user_id = (int) $user_id;
		if( $user_id < 1 ){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid loggedin user.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	  
		}
		$user_info = get_user_by('ID', $user_id);
		if( isset($user_info->ID )){
			$user_data = array();
			$user_data = (object) $user_data;
			$user_data->first_name = $user_info->first_name;
			$user_data->last_name = $user_info->last_name;
			$user_data->user_email = $user_info->user_email;
			$user_data->user_login = $user_info->user_login;
			$user_data->property_name = get_user_meta( $user_info->ID, "property_name", true);
			$user_data->location = get_user_meta( $user_info->ID, "location", true);
			
			$user_data->ID = $user_info->ID;
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'User profile data successfully getting.', 'jwt-auth' ),
					'data'       => array($user_data),
				),
				
			); 	 	
		}
		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'User not exists.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	

	}

	public function update_profile_data(WP_REST_Request $request){

		$fname    = $request->get_param( 'fname' );
		$lname    = $request->get_param( 'lname' );
		$user_type    = $request->get_param( 'user_type' );
 		$email    = $request->get_param( 'email' );
		$property_name    = $request->get_param( 'property_name' );
		$location    = $request->get_param( 'location' );
		
		if(empty($email)){ 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Please enter email address.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  			exit();
		}
		$API_BaseController = new API_BaseController();
		$user_id = $API_BaseController->custom_validate_token($_SERVER);
		$user_id = (int) $user_id;
		if( $user_id < 1 ){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid loggedin user.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
			exit();	  
		}
		$user_info_id = get_user_by('ID', $user_id);
		if( isset($user_info_id->ID )){
			$update_data = array( 'ID' => $user_id, 'user_login'     => $email,
			'user_email'     => $email,
			'user_nicename'  => $fname,
			'display_name'   => $fname.' '.$lname,
			'nickname'       => $fname,
			'first_name'     => $fname,
			'last_name'     => $lname,  );
			$update_status = wp_update_user( $update_data );
			if ( is_wp_error( $update_status ) ) {
				$error_message = $update_status->get_error_message();
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'error',
						'message'    => __( $error_message, 'jwt-auth' ),
						'data'       => array(),
					),
					
				);
				exit();
				
			}else{
			  	update_user_meta( $user_info_id->ID, "property_name", $property_name);
			  	update_user_meta( $user_info_id->ID, "location", $location);
			 	$user_data = array();
				$user_data = (object) $user_data;
				$user_data->first_name = $user_info_id->first_name;
				$user_data->last_name = $user_info_id->last_name;
				$user_data->user_email = $user_info_id->user_email;
				$user_data->user_login = $user_info_id->user_login;
				$user_data->property_name = get_user_meta( $user_info_id->ID, "property_name", true);
				$user_data->location = get_user_meta( $user_info_id->ID, "location", true);
				$user_data->ID = $user_info_id->ID;
				return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'User profile data successfully updated.', 'jwt-auth' ),
						'data'       => array($user_data),
					),
					
				);
				exit();
			}
			 
				 	
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'User not exists.', 'jwt-auth' ),
				'data'       => array(),
			),
			
		);
		exit();

	}

	public function user_change_password(WP_REST_Request $request){

		$password = $request->get_param( 'password' );
		if(empty($password)){ 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Please enter password.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  			exit();
		}
		$API_BaseController = new API_BaseController();
		$user_id = $API_BaseController->custom_validate_token($_SERVER);
		$user_id = (int) $user_id;
		if( $user_id < 1 ){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid loggedin user.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
			exit();	  
		}
		$user_info_id = get_user_by('ID', $user_id);
		if( isset($user_info_id->ID )){
			wp_set_password( $password, $user_info_id->ID );
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Password successfully changed.', 'jwt-auth' ),
						'data'       => array(),
					),
					
				);
				exit();
				 	
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'User not exists.', 'jwt-auth' ),
				'data'       => array(),
			),
			
		);
		exit();

	}
   /**
    * Create a tenant user for a tenant post with API ( Note this function will work only for API )
    */ 
	public function create_user_for_tenant( $tenant_id  ){

		$tenant_user_id = get_post_meta( $tenant_id, '_tenant_user_id', true );
		$tenant_user_id = (int) $tenant_user_id;
		$user_data = get_user_by( 'ID', $tenant_user_id );
		if ( ! empty( $user_data ) ) {
			return array('status' => true, 'message' => 'User creating successfully.', 'user_id' => $user_data->ID );
		}

		$API_ContactsController = new API_ContactsController();
		$contacts = $API_ContactsController->get_contacts( $tenant_id, "post" );
		if( !empty( $contacts ) ){
			// get first contact
			$first_contact_id = sanitize_text_field( $contacts[0]->contact_id );
			$first_name = sanitize_text_field( $contacts[0]->first_name );
			$last_name = sanitize_text_field( $contacts[0]->last_name );
			$email = sanitize_text_field( $contacts[0]->email );
			$password = wp_generate_password(12, true, true );
			$email = trim( $email );
			if(empty($email)){ 
				return array('status' => false, 'message' => 'Please enter email in first contact.');
			}
			// check if email exists already
			if ( email_exists( $email ) ) { 
				return array('status' => false, 'message' => 'This email is already exist.');
		    }
		    if ( username_exists( $email ) ) { 
				return array('status' => false, 'message' => 'This email is already exist as a username.');
		    }
	    	$userReg = wp_insert_user(array(
				'user_login'     => $email,
				'user_email'     => $email,
				'user_pass'      => $password,
				'user_nicename'  => $first_name,
				'display_name'   => $first_name.' '.$last_name,
				'nickname'       => $first_name,
				'first_name'     => $first_name,
				'last_name'     => $last_name, 
			));
			if( isset( $userReg ) && is_numeric( $userReg ) ){
				$u = new WP_User($userReg);
	        	$u->remove_role('subscriber');
	        	$u->remove_role('customer');
	        	$u->add_role('tenant');
	        	update_user_meta($userReg, 'user_type', 'tenant');
	        	update_user_meta($userReg, 'first_name', $first_name);
	        	update_user_meta($userReg, 'last_name', $last_name);
	        	update_user_meta($userReg, '_tenant_post_id', $tenant_id);
	        	update_post_meta($tenant_id, '_tenant_user_id', $userReg);
	        	// create propertyMap for all users
		        $map_args = array(
					'post_title'     => 'oakland',
					'post_type'     => 'project_maps',
					'post_content'     => 'oakland',
					'post_status'   => 'publish',
					'post_author'   => $userReg,
					 
				);
		    
			    $mapRes = wp_insert_post( $map_args );
			    if( isset( $mapRes ) && is_numeric( $mapRes ) ){
					update_user_meta($userReg, 'any_project_map_created', 'yes');
					update_post_meta( $tenant_id, 'primary_email', $email);
				}
				return array('status' => true, 'message' => 'User creating successfully.', 'user_id' => $userReg );
			}else{
				return array('status' => false, 'message' => 'User is not creating.');
			}
		}else{
		 	return array('status' => false, 'message' => 'Contact was not found.');
		
		}
	}
   
	/**
	 * 
	 * Create tenant user for a tenant using contact without API
	 */
	public function create_user_for_tenant_without_api( $tenant_id  ){

		$tenant_user_id = get_post_meta( $tenant_id, '_tenant_user_id', true );
		$tenant_user_id = (int) $tenant_user_id;
		$user_data = get_user_by( 'ID', $tenant_user_id );
		if ( ! empty( $user_data ) ) {
			return array('status' => true, 'message' => 'User creating successfully.', 'user_id' => $user_data->ID );
		}

		$API_ContactsController = new API_ContactsController();
		$contacts = $API_ContactsController->get_contacts( $tenant_id, "post" );
		if( !empty( $contacts ) ){
			$tenant_created_response = array( 'status' => false, 'message' => 'user is not creating');
			foreach( $contacts as $contact):
			// get first contact
			// $first_contact_id = sanitize_text_field( $contacts[0]->contact_id );
			// $first_name = sanitize_text_field( $contacts[0]->first_name );
			// $last_name = sanitize_text_field( $contacts[0]->last_name );
			// $email = sanitize_text_field( $contacts[0]->email );
			$first_contact_id = sanitize_text_field( $contact->contact_id );
			$first_name = sanitize_text_field( $contact->first_name );
			$last_name = sanitize_text_field( $contact->last_name );
			$email = sanitize_text_field( $contact->email );
					
			$password = wp_generate_password(12, true, true );
			$email = trim( $email );
			if(empty($email)){ 
				continue;
				// return array('status' => false, 'message' => 'Please enter email in first contact.');
			}
			// check if email exists already
			if ( email_exists( $email ) ) { 
				continue;
				// return array('status' => false, 'message' => 'This email is already exist.');
		    }
		   if ( username_exists( $email ) ) { 
				continue;
				//return array('status' => false, 'message' => 'This email is already exist as a username.');
		   }
	    	$userReg = wp_insert_user(array(
				'user_login'     => $email,
				'user_email'     => $email,
				'user_pass'      => $password,
				'user_nicename'  => $first_name,
				'display_name'   => $first_name.' '.$last_name,
				'nickname'       => $first_name,
				'first_name'     => $first_name,
				'last_name'     => $last_name, 
			));
			if( isset( $userReg ) && is_numeric( $userReg ) ){
				$u = new WP_User($userReg);
	        	$u->remove_role('subscriber');
	        	$u->remove_role('customer');
	        	$u->add_role('tenant');
	        	update_user_meta($userReg, 'user_type', 'tenant');
	        	update_user_meta($userReg, 'first_name', $first_name);
	        	update_user_meta($userReg, 'last_name', $last_name);
	        	update_user_meta($userReg, '_tenant_post_id', $tenant_id);
	        	update_post_meta($tenant_id, '_tenant_user_id', $userReg);
	        	// create propertyMap for all users
		        $map_args = array(
					'post_title'     => 'oakland',
					'post_type'     => 'project_maps',
					'post_content'     => 'oakland',
					'post_status'   => 'publish',
					'post_author'   => $userReg,
					 
				);
		    
			    $mapRes = wp_insert_post( $map_args );
			    if( isset( $mapRes ) && is_numeric( $mapRes ) ){
					update_user_meta($userReg, 'any_project_map_created', 'yes');
					update_post_meta( $tenant_id, 'primary_email', $email);
					update_post_meta( $tenant_id, 'login_access', 1 ); // 1 means enabled
				}
				return array('status' => true, 'message' => 'User creating successfully.', 'user_id' => $userReg );
			}else{
				return array('status' => false, 'message' => 'User is not creating.');
			}
			endforeach;
		}else{
		 	return array('status' => false, 'message' => 'Contact was not found.');
		
		}
	}


	public function send_email( WP_REST_Request $request ){
		//return "hello";exit;
		$body = $request->get_param('message');
   	$subject = $request->get_param('radioButtonValue');
   	$from = "feedback@getaccountableapp.com";
   	$to = "charles@avtexcommercial.com";
   	$site_title = get_bloginfo( 'name' );
   	$admin_email =  get_option( 'admin_email' );
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
		$message = "<p> ".$body.",</p>
		<p>Thanks<br/><span>". $site_title."</span></p>";
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);
		try {
		    //Server settings
		    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;  //Enable verbose debug output
		    $mail->isSMTP();                          //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];  //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                 //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];   //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password']; //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;              //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $to);     //Add a recipient
		    // $mail->addAddress('ellen@example.com');//Name is optional
		    $mail->addReplyTo( $admin_email, $site_title);
		    //Content
		    $mail->isHTML(true);               //Set email format to HTML
		    $mail->Subject = $subject;
		    $mail->Body    = $message;
		    // $mail->AltBody = '';

		    $mail_status = $mail->send();
		    return $mail_status;
		} catch (Exception $e) {
			$mail_status = 0;
			return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	 	
		// $mail_status = wp_mail( $to, $subject, $message, $headers );
		return $mail_status;
	}

}
new API_UserControler();
?>
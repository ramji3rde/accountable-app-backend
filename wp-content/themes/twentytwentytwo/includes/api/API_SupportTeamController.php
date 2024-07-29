<?php

class API_SupportTeamController extends API_BaseController {

	public function __construct() 
	{  

 	}

 	public function get_all_support_users(WP_REST_Request $request){
    	
    	$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 
    	$args = array(
    		'role' => 'support_team',
    		'number' => -1,
    		 'meta_key'     => 'supportteam_user_created_by',
		    'meta_value'   => $post_author,
		    'meta_compare' => '=',
    		

    	);
    	 
    	  	 
 		$any_support_team_created = get_user_meta($post_author, 'any_support_team_created', true); 
    	$support_teams = array();
    	// $the_query = new WP_Query( $args );
    	$user_query = new WP_User_Query( $args );
    	// The User Loop
		if ( ! empty( $user_query->results ) ) {
			$totak_supportTeam = $user_query->get_total(); ;
			foreach ( $user_query->results as $user ) {
				// do something
				$userInfo = get_user_by( 'ID', $user->ID );
				// var_dump(  $user->ID );
				if( ! $userInfo ):
					continue;
				endif;
				$user_info = new StdClass();
				$user_id = $userInfo->ID;
	 			$user_info->ID = $userInfo->ID;
	            $user_info->user_login = $userInfo->user_login;
				 
	    	    $user_info->user_created = $userInfo->user_registered;
				$user_info->first_name = get_user_meta($user_id, 'first_name', true);
	        	$user_info->last_name = get_user_meta($user_id, 'last_name', true); 

	        	$user_info->primary_title = get_user_meta($user_id, 'primary_title', true); 
	        	$user_info->description = get_user_meta($user_id, 'description', true); 

	        	$user_info->street_address = get_user_meta($user_id, 'street_address', true); 
	        	$user_info->street_address_2 = get_user_meta($user_id, 'street_address_2', true); 
	        	$user_info->state = get_user_meta($user_id, 'state', true); 
	        	$user_info->city = get_user_meta($user_id, 'city', true); 
	        	$user_info->zip_code = get_user_meta($user_id, 'zip_code', true); 

	        	$user_info->primary_phone = get_user_meta($user_id, 'primary_phone', true); 
	        	$user_info->primary_phone_type = get_user_meta($user_id, 'primary_phone_type', true); 
	        	$user_info->secondary_phone = get_user_meta($user_id, 'secondary_phone', true); 
	        	$user_info->secondary_phone_type = get_user_meta($user_id, 'secondary_phone_type', true);
	        	$user_info->primary_email = get_user_meta($user_id, 'primary_email', true);
	         
	        	$user_info->secondary_email = get_user_meta($user_id, 'secondary_email', true); 
	    
	       		array_push( $support_teams, $user_info);
	   		
				 
			}
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All users data list.', 'jwt-auth' ),
					'totak_supportTeam' => $totak_supportTeam,
					'data'       => $support_teams,
					'any_record_created' => $any_support_team_created,
				),
				
			); 
		} else {
			// no users found
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'User not found.', 'jwt-auth' ),
					'totak_supportTeam' => 0,
					'data'       => array(),
				),
				
			); 
		}
		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'User not found.', 'jwt-auth' ),
					'totak_supportTeam' => 0,
					'data'       => array(),
				),
				
			); 
    	
    }

 	/*
 	 * Create new support team
 	 */
 	public function create_new_team(WP_REST_Request $request)
 	{
 		//contect info
 		$API_BaseController = new API_BaseController();
 		$permission = $API_BaseController->check_user_permission($_SERVER, 'create');
 		if( ! $permission ):
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'permission denied.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		endif; 
 		$first_name    = $request->get_param( 'first_name' );
 		$last_name    = $request->get_param( 'last_name' ); 
 		$primary_title    = $request->get_param( 'primary_title' ); 
 		$description    = $request->get_param( 'description' );
 		$primary_phone    = $request->get_param( 'primary_phone' ); 
 		$primary_phone_type    = $request->get_param( 'primary_phone_type' );
 		$secondary_phone    = $request->get_param( 'secondary_phone' );
 		$secondary_phone_type    = $request->get_param( 'secondary_phone_type' ); 
 		$primary_email    = $request->get_param( 'primary_email' ); 
 		$secondary_email    = $request->get_param( 'secondary_email' );

 		$street_address    = $request->get_param( 'street_address' );
 		$street_address_2    = $request->get_param( 'street_address_2' ); 
 		$state    = $request->get_param( 'state' ); 
 		$city    = $request->get_param( 'city' ); 
 		$zip_code    = $request->get_param( 'zip_code' );
 		
 		$author    = $request->get_param( 'author' );

		$errors_arr = array();

		if(empty($primary_email)){ 
			$current_time = time();
			$primary_email = $current_time.'_ag@dev.getsmiapp.com';
			// $errors_arr[] = __( 'Please enter primary email address.', 'jwt-auth' ); 
		} 
		if(empty($first_name)){

 			$errors_arr[] = __( 'Please enter first name.', 'jwt-auth' ); 
		} 

		// if(empty($last_name)){

		
		if(empty($author)){

 			$errors_arr[] = __( 'You are invalid user.', 'jwt-auth' ); 
		}

		
		if ( !empty($errors_arr) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'The fields are required', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
				
			); 
	    }
 		
 		if ( email_exists( $primary_email ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This email is already exist.', 'jwt-auth' ),
					'data'       => array(),
				),
			); 
	    }
	    if( username_exists( $primary_email ) ){
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This email is already exist.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
	    $API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
	 	$password = wp_generate_password( 10, true, true );
	 	$userReg=wp_insert_user(array(
			'user_login'     => $primary_email,
			'user_email'     => $primary_email,
			'user_pass'      => $password,
			'user_nicename'  => $first_name.' '.$last_name,
			'display_name'   => $first_name.' '.$last_name,
			'nickname'       => $first_name.' '.$last_name,
			'first_name'     => $first_name, 
			'last_name'      => $first_name, 
		));

	
		if( isset( $userReg ) && is_numeric( $userReg ) ){

 			$u = new WP_User($userReg);
	        $u->remove_role('subscriber');
	        $u->remove_role('customer');	         
        	$u->add_role('support_team');
        	update_user_meta($post_author, 'any_support_team_created', 'yes');
        	update_user_meta($userReg, 'supportteam_user_created_by', $post_author);
        	update_user_meta($userReg, 'user_type', 'support_team');
	        update_user_meta($userReg, 'first_name', $first_name);
	        update_user_meta($userReg, 'last_name', $last_name); 
	        update_user_meta($userReg, 'primary_title', $primary_title);   

	        update_user_meta($userReg, 'street_address', $street_address);  
	        update_user_meta($userReg, 'street_address_2', $street_address_2);  
	        update_user_meta($userReg, 'state', $state);  
	        update_user_meta($userReg, 'city', $city);  
	        update_user_meta($userReg, 'zip_code', $zip_code);  

	        //contect info
	        update_user_meta($userReg, 'description', $description);  
	        
	        update_user_meta($userReg, 'primary_phone', $primary_phone);  
	        update_user_meta($userReg, 'primary_phone_type', $primary_phone_type);  
	        update_user_meta($userReg, 'secondary_phone', $secondary_phone);  
	        update_user_meta($userReg, 'secondary_phone_type', $secondary_phone_type);  
	        update_user_meta($userReg, 'primary_email', $primary_email);  
	        update_user_meta($userReg, 'secondary_email', $secondary_email);
     
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
					'code'       => 'success',
					'message'    => __( 'User sucessfully created', 'jwt-auth' ),
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

 
 	}

 	

 	/**
 	 * Get all users with search data with pagination
 	 * 
 	 */ 
 	public function search_users(WP_REST_Request $request){
    	$search_by_keyword    = $request->get_param( 'search_by_keyword' );
    	$paged = ($request->get_param('paged')) ? $request->get_param('paged') : 1;
    	$posts_per_page = ($request->get_param('posts_per_page')) ? $request->get_param('posts_per_page') : 10;
    	$sort_by_field    = $request->get_param( 'sort_by_field' );
    	if( 1 === $paged ):
    		$offset = 0;
    	else:
    		$offset= ($paged-1)*$posts_per_page;
    	endif;
    	$orderby = "display_name";
    	$order = "ASC";

    	if( 'a-z' == $sort_by_field ):
    		$orderby = "display_name";
    		$order = "ASC";
    	endif;
    	if( 'z-a' == $sort_by_field ):
    		$orderby = "display_name";
    		$order = "DESC";
    	endif;

    	$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 
    	$args = array(
    		'role' => 'support_team',
    		'number' => $posts_per_page,
    		'offset' => $offset,
    		'order' => $order,
			'orderby' => $orderby,


    	);
    	// if( 'services' == $sort_by_field ):
    	//  	$args['meta_key'] = "services";
    	// 	$args['orderby'] = "meta_value";
    	// 	$args['order'] = "ASC";

    	// endif;
    	 
    	if( '' != trim( $search_by_keyword ) ):
    		$args['meta_query'] = array(
    				'relation' => 'OR',
    				array(
				            'key' => 'supportteam_user_created_by',
				            'value' => $post_author,
				            'compare' => '='
				        ),
				        array(
				            'value' => $search_by_keyword,
				            'compare' => "LIKE"
				        )
				    );
    	else:
    		$args['meta_query'] = array(
    				'relation' => 'AND',
			        array(
			            'key' => 'supportteam_user_created_by',
			            'value' => $post_author,
			            'compare' => '='
			        ),
				         
				        
				    );	
    	endif;
    	
  	 
 		$any_support_team_created = get_user_meta($post_author, 'any_support_team_created', true); 
    	$support_teams = array();
    	// $the_query = new WP_Query( $args );
    	$user_query = new WP_User_Query( $args );
    	// The User Loop
		if ( ! empty( $user_query->results ) ) {
			$totak_supportTeam = $user_query->get_total(); ;
			foreach ( $user_query->results as $user ) {
				// do something
				$userInfo = get_user_by( 'ID', $user->ID );
				// var_dump(  $user->ID );
				if( ! $userInfo ):
					continue;
				endif;
				$user_info = new StdClass();
				$user_id = $userInfo->ID;
	 			$user_info->ID = $userInfo->ID;
	            $user_info->user_login = $userInfo->user_login;
				 
	    	    $user_info->user_created = $userInfo->user_registered;
				$user_info->first_name = get_user_meta($user_id, 'first_name', true);
	        	$user_info->last_name = get_user_meta($user_id, 'last_name', true); 

	        	$user_info->primary_title = get_user_meta($user_id, 'primary_title', true); 
	        	$user_info->description = get_user_meta($user_id, 'description', true); 

	        	$user_info->street_address = get_user_meta($user_id, 'street_address', true); 
	        	$user_info->street_address_2 = get_user_meta($user_id, 'street_address_2', true); 
	        	$user_info->state = get_user_meta($user_id, 'state', true); 
	        	$user_info->city = get_user_meta($user_id, 'city', true); 
	        	$user_info->zip_code = get_user_meta($user_id, 'zip_code', true); 

	        	$user_info->primary_phone = get_user_meta($user_id, 'primary_phone', true); 
	        	$user_info->primary_phone_type = get_user_meta($user_id, 'primary_phone_type', true); 
	        	$user_info->secondary_phone = get_user_meta($user_id, 'secondary_phone', true); 
	        	$user_info->secondary_phone_type = get_user_meta($user_id, 'secondary_phone_type', true);
	        	$user_info->primary_email = get_user_meta($user_id, 'primary_email', true);
	         
	        	$user_info->secondary_email = get_user_meta($user_id, 'secondary_email', true); 
	    
	       		array_push( $support_teams, $user_info);
	   		
				 
			}
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All users data list.', 'jwt-auth' ),
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'totak_supportTeam' => $totak_supportTeam,
					'data'       => $support_teams,
					'any_record_created' => $any_support_team_created,
				),
				
			); 
		} else {
			// no users found
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'User not found.', 'jwt-auth' ),
					'totak_supportTeam' => 0,
					'data'       => array(),
				),
				
			); 
		}
		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'User not found.', 'jwt-auth' ),
					'totak_supportTeam' => 0,
					'data'       => array(),
				),
				
			); 
    	
    }


    /**
     * Get a user detail
     */ 
    public function get_single_user(WP_REST_Request $request)
 	{
 		$user_id    = $request->get_param( 'userId' );  

 		if ( ( empty($user_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter user ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
 
		$userinfo = get_user_by( 'ID', $user_id );

		if ( empty($user_id) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This user is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{
	    	$user_info = new StdClass();
 			$user_info->ID = $userinfo->ID;
            $user_info->user_login = $userinfo->user_login;
			 
    	    $user_info->user_created = $userinfo->user_registered;
			$user_info->first_name = get_user_meta($user_id, 'first_name', true);
        	$user_info->last_name = get_user_meta($user_id, 'last_name', true); 

        	$user_info->primary_title = get_user_meta($user_id, 'primary_title', true); 
        	$user_info->description = get_user_meta($user_id, 'description', true); 

        	$user_info->street_address = get_user_meta($user_id, 'street_address', true); 
        	$user_info->street_address_2 = get_user_meta($user_id, 'street_address_2', true); 
        	$user_info->state = get_user_meta($user_id, 'state', true); 
        	$user_info->city = get_user_meta($user_id, 'city', true); 
        	$user_info->zip_code = get_user_meta($user_id, 'zip_code', true); 

        	$user_info->primary_phone = get_user_meta($user_id, 'primary_phone', true); 
        	$user_info->primary_phone_type = get_user_meta($user_id, 'primary_phone_type', true); 
        	$user_info->secondary_phone = get_user_meta($user_id, 'secondary_phone', true); 
        	$user_info->secondary_phone_type = get_user_meta($user_id, 'secondary_phone_type', true);
        	$user_info->primary_email = get_user_meta($user_id, 'primary_email', true);
         
        	$user_info->secondary_email = get_user_meta($user_id, 'secondary_email', true);
        	$API_NotesController = new API_NotesController();
        	$user_info->notes = $API_NotesController->user_notes( $user_id ); 
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'User Details', 'jwt-auth' ),
						'data'       => $user_info,
					),
					 
				);
		}

 	}


 	/**
 	 * Delete single or multiple support teams
 	 * 
 	 */ 
 	public function delete_user_by_id(WP_REST_Request $request){
 		//contect info
 		$API_BaseController = new API_BaseController();
 		$permission = $API_BaseController->check_user_permission($_SERVER, 'delete');
 		if( ! $permission ):
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'permission denied.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		endif; 
 		require_once( ABSPATH.'wp-admin/includes/user.php' );
 		$user_ids    = $request->get_param( 'user_ids' ); 
 		
 		if( empty( $user_ids) || gettype($user_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one user id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$total_delete_users = 0;
 		$API_NotesController = new API_NotesController();
 		foreach( $user_ids as $user_id ){
 			$user = get_user_by( 'ID', $user_id );
 			if( $user ){
 				 
                if(wp_delete_user( $user->ID ) ):
                	$API_NotesController->delete_all_notes_by_user_id($user->ID);
 				$total_delete_users++;
                endif;
 			}else{
 				continue;
 			}
 			 
 			 
 		}
 		if( $total_delete_users > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Users successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_delete_users' => $total_delete_users),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any user', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

 	/**
 	 * Update a support team API
 	 */ 

 	public function update_support_team_by_id( WP_REST_Request $request ){
 		$API_BaseController = new API_BaseController();
 		$permission = $API_BaseController->check_user_permission($_SERVER, 'update');
 		if( ! $permission ):
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'permission denied.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		endif; 
 		$user_id    = $request->get_param( 'userId' ); 
 		$first_name    = $request->get_param( 'first_name' );
 		$last_name    = $request->get_param( 'last_name' ); 
 		$primary_title    = $request->get_param( 'primary_title' ); 
 		$description    = $request->get_param( 'description' );
 		$primary_phone    = $request->get_param( 'primary_phone' ); 
 		$primary_phone_type    = $request->get_param( 'primary_phone_type' );
 		$secondary_phone    = $request->get_param( 'secondary_phone' );
 		$secondary_phone_type    = $request->get_param( 'secondary_phone_type' ); 
 		$primary_email    = $request->get_param( 'primary_email' ); 
 		$secondary_email    = $request->get_param( 'secondary_email' ); 
 	   
 	    $street_address    = $request->get_param( 'street_address' );
 		$street_address_2    = $request->get_param( 'street_address_2' ); 
 		$state    = $request->get_param( 'state' ); 
 		$city    = $request->get_param( 'city' ); 
 		$zip_code    = $request->get_param( 'zip_code' );

 		if(empty($user_id)){ 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Please enter user Id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  
		}
		$errors_arr = array();

		if(empty($primary_email)){ 

			$errors_arr[] = __( 'Please enter primary email address.', 'jwt-auth' ); 
		} 
		if(empty($first_name)){

 			$errors_arr[] = __( 'Please enter first name.', 'jwt-auth' ); 
		} 

		// if(empty($last_name)){

 	// 		$errors_arr[] = __( 'Please enter last name.', 'jwt-auth' ); 
		// } 
		 

		
		if ( !empty($errors_arr) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'The fields are required', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
				
			); 
	    }
 		$user = get_user_by( 'id', $user_id );
 		if ( $user ) {
 			wp_update_user(
 			 array( 'ID' => $user_id, 
 			 	'user_nicename'  => $first_name.' '.$last_name,
				'display_name'   => $first_name.' '.$last_name,
				'nickname'       => $first_name.' '.$last_name,
				'first_name'     => $first_name, 
				'last_name'      => $last_name,
 			 	 ) );
 			update_user_meta($user_id, 'user_type', 'support_team');
	        update_user_meta($user_id, 'first_name', $first_name);
	        update_user_meta($user_id, 'last_name', $last_name); 
	        update_user_meta($user_id, 'primary_title', $primary_title);   

	        //contect info
	        update_user_meta($user_id, 'description', $description);  
	        
	        update_user_meta($user_id, 'street_address', $street_address);  
	        update_user_meta($user_id, 'street_address_2', $street_address_2);  
	        update_user_meta($user_id, 'state', $state);  
	        update_user_meta($user_id, 'city', $city);  
	        update_user_meta($user_id, 'zip_code', $zip_code);
	        
	        update_user_meta($user_id, 'primary_phone', $primary_phone);  
	        update_user_meta($user_id, 'primary_phone_type', $primary_phone_type);  
	        update_user_meta($user_id, 'secondary_phone', $secondary_phone);  
	        update_user_meta($user_id, 'secondary_phone_type', $secondary_phone_type);  
	        update_user_meta($user_id, 'primary_email', $primary_email);  
	        update_user_meta($user_id, 'secondary_email', $secondary_email); 
 			 return new WP_REST_Response(

					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Successfull updated', 'jwt-auth' ),
						'data'       => array(),
					),
			); 
 		}

 		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid user Id.', 'jwt-auth' ),
					'data'       => array(),
				),
		);
 	}
}

new API_SupportTeamController();
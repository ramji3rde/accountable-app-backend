<?php

class API_ContractorsController extends API_BaseController {

	public function __construct() 
	{  

 	}
 	/**
 	 * Get all constractors
 	 */ 
 	public function get_contractors_count(WP_REST_Request $request)
 	{

 		//$API_BaseController = new API_BaseController();
		$post_author = $this->custom_validate_token($_SERVER);
		$any_contractor_created = get_user_meta($post_author, 'any_contractor_created', true);  	 
 		$contractors = get_users( 
							array( 
								'role' => 'contractor',
								'orderby' => 'ID',
								'fields' => array( 'ID', 'user_login', 'user_email', 'display_name', 'user_registered'),
								'meta_key'     => 'user_created_by',
							    'meta_value'   => $post_author,
							    'meta_compare' => '=',
								 // 'meta_query' => array(
								 // 	'key' => 'user_created_by',
						         //    'value' => $post_author,
						         //    'compare' => '='
								 	// 'relation' => 'AND',
								    //     array(
								    //         'key' => 'user_created_by',
								    //         'value' => $post_author,
								    //         'compare' => '='
								    //     ),
								        // array(
                                        //     'key' => 'company_flag',
                                        //     'value' => "true",
                                        //     'compare' => '='
                                        // )
								    //)
							) 
						);
 		if(!empty($contractors))
 		{ 
 			$API_ContactsController = new API_ContactsController();
  			foreach ($contractors as $key => $data) {
 				 
 				$contractors[$key]->first_name = get_user_meta($data->ID, 'first_name', true);
	        	$contractors[$key]->last_name = get_user_meta($data->ID, 'last_name', true); 
  				$contractors[$key]->contractor_created = $data->user_registered;
 				$contractors[$key]->company_name = get_user_meta($data->ID, 'company_name', true);
	        	$contractors[$key]->street_address = get_user_meta($data->ID, 'street_address', true); 
	        	$contractors[$key]->street_address_2 = get_user_meta($data->ID, 'street_address_2', true); 
	        	$contractors[$key]->account_number = get_user_meta($data->ID, 'account_number', true); 
	        	$contractors[$key]->street_address = get_user_meta($data->ID, 'street_address', true); 
	        	// $contractors[$key]->unit = get_user_meta($data->ID, 'unit', true); 
	        	$contractors[$key]->city = get_user_meta($data->ID, 'city', true); 
	        	$contractors[$key]->state = get_user_meta($data->ID, 'state', true); 
	        	$contractors[$key]->zip = get_user_meta($data->ID, 'zip', true); 
	        	// $contractors[$key]->unit = get_user_meta($data->ID, 'unit', true); 
	        	$services = get_user_meta($data->ID, 'services', true);
        		$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
	        	
	        	$contractors[$key]->services = $services; 
	        	 
	        	$contractors[$key]->company_primary_phone = get_user_meta($data->ID, 'company_primary_phone', true); 
	        	$contractors[$key]->company_primary_phone_type = get_user_meta($data->ID, 'company_primary_phone_type', true); 
 
	        	$contractors[$key]->company_secondary_phone = get_user_meta($data->ID, 'company_secondary_phone', true); 
	        	$contractors[$key]->company_secondary_phone_type = get_user_meta($data->ID, 'company_secondary_phone_type', true); 
	        	$contractors[$key]->company_email = get_user_meta($data->ID, 'company_email', true); 

	        	//primary info
	        	$contractors[$key]->primary_fname = get_user_meta($data->ID, 'primary_fname', true); 
	        	$contractors[$key]->primary_lname = get_user_meta($data->ID, 'primary_lname', true); 
	        	$contractors[$key]->primary_title = get_user_meta($data->ID, 'primary_title', true); 
	        	$contractors[$key]->primary_phone = get_user_meta($data->ID, 'primary_phone', true); 
	        	$contractors[$key]->primary_phone_type = get_user_meta($data->ID, 'primary_phone_type', true); 
	        	$contractors[$key]->primary_secondary_number = get_user_meta($data->ID, 'primary_secondary_number', true); 
	        	$contractors[$key]->primary_secondary_number_type = get_user_meta($data->ID, 'primary_secondary_number_type', true); 
	        	$contractors[$key]->primary_email = get_user_meta($data->ID, 'primary_email', true); 
	        	$contractors[$key]->notes = get_user_meta($data->ID, 'notes', true); 
    			$contractors[$key]->company_flag = get_user_meta($data->ID, 'company_flag', true); 
    			$auto_generate_email = get_user_meta($data->ID, 'auto_generate_email', true);
    			$auto_generate_email_address = get_user_meta($data->ID, 'auto_generate_email_address', true);
    			$auto_generate_email = ( 1 == $auto_generate_email ) ? 1 : 0;
    			$contractors[$key]->auto_generate_email = $auto_generate_email; 
    			$contractors[$key]->auto_generate_email_address = $auto_generate_email_address; 
 				$contractors[$key]->contacts = $API_ContactsController->get_contacts( $data->ID, "user" );
 			}
 		}
 		if(is_array($contractors)){
 			$count = count($contractors);
 		}else{
 			$count = 0;
 		}

		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All contractors', 'jwt-auth' ),
					'data'       => $contractors,
					'any_record_created' => $any_contractor_created,
					'contactors_count'=> $count,
				),
				
			); 

 	} 


 	/**
 	 * Get all constractors
 	 */ 
 	public function get_all_flaged_contractors_list(WP_REST_Request $request)
 	{

 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_contractor_created = get_user_meta($post_author, 'any_contractor_created', true);  	 
 		$contractors = get_users( 
							array( 
								'role' => 'contractor',
								'orderby' => 'ID',
								'fields' => array( 'ID', 'user_login', 'user_email', 'display_name', 'user_registered'),
								 'meta_query' => array(
								 	'relation' => 'AND',
								        array(
								            'key' => 'user_created_by',
								            'value' => $post_author,
								            'compare' => '='
								        ),
								        array(
                                            'key' => 'company_flag',
                                            'value' => "true",
                                            'compare' => '='
                                        )
								    )
							) 
						);
 		if(!empty($contractors))
 		{ 
 			$API_ContactsController = new API_ContactsController();
  			foreach ($contractors as $key => $data) {
 				 
 				$contractors[$key]->first_name = get_user_meta($data->ID, 'first_name', true);
	        	$contractors[$key]->last_name = get_user_meta($data->ID, 'last_name', true); 
  				$contractors[$key]->contractor_created = $data->user_registered;
 				$contractors[$key]->company_name = get_user_meta($data->ID, 'company_name', true);
	        	$contractors[$key]->street_address = get_user_meta($data->ID, 'street_address', true); 
	        	$contractors[$key]->street_address_2 = get_user_meta($data->ID, 'street_address_2', true); 
	        	$contractors[$key]->account_number = get_user_meta($data->ID, 'account_number', true); 
	        	$contractors[$key]->street_address = get_user_meta($data->ID, 'street_address', true); 
	        	// $contractors[$key]->unit = get_user_meta($data->ID, 'unit', true); 
	        	$contractors[$key]->city = get_user_meta($data->ID, 'city', true); 
	        	$contractors[$key]->state = get_user_meta($data->ID, 'state', true); 
	        	$contractors[$key]->zip = get_user_meta($data->ID, 'zip', true); 
	        	// $contractors[$key]->unit = get_user_meta($data->ID, 'unit', true); 
	        	$services = get_user_meta($data->ID, 'services', true);
        		$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
	        	
	        	$contractors[$key]->services = $services; 
	        	 
	        	$contractors[$key]->company_primary_phone = get_user_meta($data->ID, 'company_primary_phone', true); 
	        	$contractors[$key]->company_primary_phone_type = get_user_meta($data->ID, 'company_primary_phone_type', true); 
 
	        	$contractors[$key]->company_secondary_phone = get_user_meta($data->ID, 'company_secondary_phone', true); 
	        	$contractors[$key]->company_secondary_phone_type = get_user_meta($data->ID, 'company_secondary_phone_type', true); 
	        	$contractors[$key]->company_email = get_user_meta($data->ID, 'company_email', true); 

	        	//primary info
	        	$contractors[$key]->primary_fname = get_user_meta($data->ID, 'primary_fname', true); 
	        	$contractors[$key]->primary_lname = get_user_meta($data->ID, 'primary_lname', true); 
	        	$contractors[$key]->primary_title = get_user_meta($data->ID, 'primary_title', true); 
	        	$contractors[$key]->primary_phone = get_user_meta($data->ID, 'primary_phone', true); 
	        	$contractors[$key]->primary_phone_type = get_user_meta($data->ID, 'primary_phone_type', true); 
	        	$contractors[$key]->primary_secondary_number = get_user_meta($data->ID, 'primary_secondary_number', true); 
	        	$contractors[$key]->primary_secondary_number_type = get_user_meta($data->ID, 'primary_secondary_number_type', true); 
	        	$contractors[$key]->primary_email = get_user_meta($data->ID, 'primary_email', true); 
	        	$contractors[$key]->notes = get_user_meta($data->ID, 'notes', true); 
    			$contractors[$key]->company_flag = get_user_meta($data->ID, 'company_flag', true); 
    			$auto_generate_email = get_user_meta($data->ID, 'auto_generate_email', true);
    			$auto_generate_email_address = get_user_meta($data->ID, 'auto_generate_email_address', true);
    			$auto_generate_email = ( 1 == $auto_generate_email ) ? 1 : 0;
    			$contractors[$key]->auto_generate_email = $auto_generate_email; 
    			$contractors[$key]->auto_generate_email_address = $auto_generate_email_address; 
 				$contractors[$key]->contacts = $API_ContactsController->get_contacts( $data->ID, "user" );
 			}
 		}
 		if(is_array($contractors)){
 			$count = count($contractors);
 		}else{
 			$count = 0;
 		}

		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All contractors', 'jwt-auth' ),
					'data'       => $contractors,
					'any_record_created' => $any_contractor_created,
					'contactors_count'=> $count,
				),
				
			); 

 	} 


 	/*
 	 * Create new contractor
 	 */
 	public function contractor_api_create_new_contractor(WP_REST_Request $request)
 	{
 		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
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
 		//contect info
 		$company_name    = $request->get_param( 'company_name' );
 		$company_flag    = $request->get_param( 'company_flag' ); 
 		$author    = $request->get_param( 'author' ); 
 		$account_number    = $request->get_param( 'account_number' ); 
 		$street_address    = $request->get_param( 'street_address' );
 		$street_address_2    = $request->get_param( 'street_address_2' ); 
 		$services    = $request->get_param( 'services' );
 		$services = ( ''  != $services ) ? $services : 'zzzzzzzzzz';
 		$city    = $request->get_param( 'city' ); 
 		$state    = $request->get_param( 'state' );
 		$is_new_user = $request->get_param('is_new_user');
 		$is_new_user = ('yes' == $is_new_user ) ? 'yes' : 'no'; // default no

 		$zip    = $request->get_param( 'zip' ); 
 		// $unit    = $request->get_param( 'unit' );
 		$company_primary_phone    = $request->get_param( 'company_primary_phone' ); 
 		$company_primary_phone_type    = $request->get_param( 'company_primary_phone_type' ); 
 		$company_secondary_phone    = $request->get_param( 'company_secondary_phone' );

 		$company_secondary_phone_type    = $request->get_param( 'company_secondary_phone_type' ); 
 		$company_email    = $request->get_param( 'company_email' );
 		$company_email = trim( $company_email );

 		 // primary detalis
 		$primary_fname    = $request->get_param( 'primary_fname' );
 		$primary_lname    = $request->get_param( 'primary_lname' );
 		$primary_title    = $request->get_param( 'primary_title' ); 

 		$primary_phone    = $request->get_param( 'primary_phone' );
 		$primary_phone_type    = $request->get_param( 'primary_phone_type' );
 		

 		$primary_secondary_number    = $request->get_param( 'primary_secondary_number' );
 		$primary_secondary_number_type    = $request->get_param( 'primary_secondary_number_type' ); 
 		$primary_email    = $request->get_param( 'primary_email' );
 		
 		//notes
 		$notes    = $request->get_param( 'notes' ); 

		$errors_arr = array();
		$auto_generate_email = 0;
		if(empty($company_email)){ 
			$current_time = time();
			$company_email = $current_time.'_ag@dev.getsmiapp.com';
			$auto_generate_email = 1;
			// $errors_arr[] = __( 'Please enter email address.', 'jwt-auth' ); 
		} 
		// if(empty($primary_fname)){



		
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
 		
 		if ( email_exists( $company_email ) ) { 

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
	    if( username_exists( $company_email ) ){
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
	 	$password = wp_generate_password( 10, true, true );
	 	$display_name = '';
	 	if( '' != trim( $company_name ) ){
	 		$display_name = $company_name;
	 	}else if( '' != trim( $primary_fname ) ){
	 		$display_name = $primary_fname.' '.$primary_lname;
	 	}else{
	 		$display_name = explode("@", $company_email)[0];
	 	}
	 	$userReg=wp_insert_user(array(
			'user_login'     => $company_email,
			'user_email'     => $company_email,
			'user_pass'      => $password,
			// 'user_nicename'  => $primary_fname.' '.$primary_lname,
			'display_name'   => $display_name,
			// 'nickname'       => $primary_fname.' '.$primary_lname,
			// 'first_name'     => $primary_fname, 
			// 'last_name'      => $primary_lname, 
		));

		 
		if( isset( $userReg ) && is_numeric( $userReg ) ){

 			$u = new WP_User($userReg);
	        $u->remove_role('subscriber');
	        $u->remove_role('customer');	         
        	$u->add_role('contractor');
        	update_user_meta($post_author, 'any_contractor_created', 'yes');
        	update_user_meta($userReg, 'user_created_by', $post_author);
        	update_user_meta($userReg, 'company_flag', 'company_flag');
        	update_user_meta($userReg, 'user_type', 'contractor');
	        update_user_meta($userReg, 'first_name', $primary_fname);
	        update_user_meta($userReg, 'last_name', $primary_lname); 
	        update_user_meta($userReg, 'primary_title', $primary_title);   

	        //contect info
	        update_user_meta($userReg, 'company_name', $company_name);  
	        update_user_meta($userReg, 'street_address', $street_address);
	        update_user_meta($userReg, 'street_address_2', $street_address_2); 
	        // update_user_meta($userReg, 'unit', $unit);  
	        update_user_meta($userReg, 'city', $city);  
	        update_user_meta($userReg, 'state', $state);  
	        update_user_meta($userReg, 'zip', $zip); 
	        // update_user_meta($userReg, 'unit', $unit); 
	        update_user_meta($userReg, 'account_number', $account_number);  
	        update_user_meta($userReg, 'company_primary_phone', $company_primary_phone);  
	        update_user_meta($userReg, 'company_primary_phone_type', $company_primary_phone_type);  
	        update_user_meta($userReg, 'company_secondary_phone', $company_secondary_phone);  
	        update_user_meta($userReg, 'company_secondary_phone_type', $company_secondary_phone_type);  
	        update_user_meta($userReg, 'company_email', $company_email);  

 
	 		//primary info
	        update_user_meta($userReg, 'primary_fname', $primary_fname);
	        update_user_meta($userReg, 'primary_lname', $primary_lname); 
	        update_user_meta($userReg, 'primary_title', $primary_title); 
	        update_user_meta($userReg, 'primary_phone', $primary_phone); 
	        update_user_meta($userReg, 'primary_phone_type', $primary_phone_type); 

	        update_user_meta($userReg, 'primary_secondary_number', $primary_secondary_number); 
	        update_user_meta($userReg, 'primary_secondary_number_type', $primary_secondary_number_type);  
	        update_user_meta($userReg, 'primary_email', $primary_email); 
	        update_user_meta($userReg, 'services', $services);

	        //extra 
	        update_user_meta($userReg, 'notes', $notes);
	        update_user_meta($userReg, 'auto_generate_email', $auto_generate_email);   

	        
	        
	        if( $auto_generate_email ):
	        	update_user_meta($userReg, 'auto_generate_email_address', $company_email); 
	        endif;
	        update_user_meta($userReg, 'is_new_user', $is_new_user);
	        return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Contractor sucessfully created', 'jwt-auth' ),
					'data'       => array('contractor_id' => $userReg),
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
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( $massage, 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		} 

 
 	}

 	/**
 	 * Get all constractors
 	 */ 
 	public function get_all_contractors_list(WP_REST_Request $request)
 	{

 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_contractor_created = get_user_meta($post_author, 'any_contractor_created', true);  	 
 		$contractors = get_users( 
							array( 
								'role' => 'contractor',
								'orderby' => 'ID',
								'fields' => array( 'ID', 'user_login', 'user_email', 'display_name', 'user_registered'),
								 'meta_query' => array(
								        array(
								            'key' => 'user_created_by',
								            'value' => $post_author,
								            'compare' => '='
								        )
								    )
							) 
						);
 		if(!empty($contractors))
 		{ 
 			$API_ContactsController = new API_ContactsController();
  			foreach ($contractors as $key => $data) {
 				 
 				$contractors[$key]->first_name = get_user_meta($data->ID, 'first_name', true);
	        	$contractors[$key]->last_name = get_user_meta($data->ID, 'last_name', true); 
  				$contractors[$key]->contractor_created = $data->user_registered;
 				$contractors[$key]->company_name = get_user_meta($data->ID, 'company_name', true);
	        	$contractors[$key]->street_address = get_user_meta($data->ID, 'street_address', true); 
	        	$contractors[$key]->street_address_2 = get_user_meta($data->ID, 'street_address_2', true); 
	        	$contractors[$key]->account_number = get_user_meta($data->ID, 'account_number', true); 
	        	$contractors[$key]->street_address = get_user_meta($data->ID, 'street_address', true); 
	        	// $contractors[$key]->unit = get_user_meta($data->ID, 'unit', true); 
	        	$contractors[$key]->city = get_user_meta($data->ID, 'city', true); 
	        	$contractors[$key]->state = get_user_meta($data->ID, 'state', true); 
	        	$contractors[$key]->zip = get_user_meta($data->ID, 'zip', true); 
	        	// $contractors[$key]->unit = get_user_meta($data->ID, 'unit', true); 
	        	$services = get_user_meta($data->ID, 'services', true);
        		$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
	        	
	        	$contractors[$key]->services = $services; 
	        	 
	        	$contractors[$key]->company_primary_phone = get_user_meta($data->ID, 'company_primary_phone', true); 
	        	$contractors[$key]->company_primary_phone_type = get_user_meta($data->ID, 'company_primary_phone_type', true); 
 
	        	$contractors[$key]->company_secondary_phone = get_user_meta($data->ID, 'company_secondary_phone', true); 
	        	$contractors[$key]->company_secondary_phone_type = get_user_meta($data->ID, 'company_secondary_phone_type', true); 
	        	$contractors[$key]->company_email = get_user_meta($data->ID, 'company_email', true); 

	        	//primary info
	        	$contractors[$key]->primary_fname = get_user_meta($data->ID, 'primary_fname', true); 
	        	$contractors[$key]->primary_lname = get_user_meta($data->ID, 'primary_lname', true); 
	        	$contractors[$key]->primary_title = get_user_meta($data->ID, 'primary_title', true); 
	        	$contractors[$key]->primary_phone = get_user_meta($data->ID, 'primary_phone', true); 
	        	$contractors[$key]->primary_phone_type = get_user_meta($data->ID, 'primary_phone_type', true); 
	        	$contractors[$key]->primary_secondary_number = get_user_meta($data->ID, 'primary_secondary_number', true); 
	        	$contractors[$key]->primary_secondary_number_type = get_user_meta($data->ID, 'primary_secondary_number_type', true); 
	        	$contractors[$key]->primary_email = get_user_meta($data->ID, 'primary_email', true); 
	        	$contractors[$key]->notes = get_user_meta($data->ID, 'notes', true); 
    			$contractors[$key]->company_flag = get_user_meta($data->ID, 'company_flag', true); 
    			$auto_generate_email = get_user_meta($data->ID, 'auto_generate_email', true);
    			$auto_generate_email_address = get_user_meta($data->ID, 'auto_generate_email_address', true);
    			$auto_generate_email = ( 1 == $auto_generate_email ) ? 1 : 0;
    			$contractors[$key]->auto_generate_email = $auto_generate_email; 
    			$contractors[$key]->auto_generate_email_address = $auto_generate_email_address; 
 				$contractors[$key]->contacts = $API_ContactsController->get_contacts( $data->ID, "user" );
 			}
 		}
 		if(is_array($contractors)){
 			$count = count($contractors);
 		}else{
 			$count = 0;
 		}

		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All contractors', 'jwt-auth' ),
					'data'       => $contractors,
					'any_record_created' => $any_contractor_created,
					'contactors_count'=> $count,
				),
				
			); 

 	}

 	/**
 	 * Get all constractors with search data with pagination
 	 * 
 	 */ 
 	public function search_contractors(WP_REST_Request $request){
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
		$any_contractor_created = get_user_meta($post_author, 'any_contractor_created', true);  
    	$args = array(
    		'role' => 'contractor',
    		'number' => $posts_per_page,
    		'offset' => $offset,
    		'order' => $order,
			'orderby' => $orderby,


    	);
    	if( 'services' == $sort_by_field ):
    	 	$args['meta_key'] = "services";
    		$args['orderby'] = "meta_value";
    		$args['order'] = "ASC";

    	endif;
    	 
    	if( '' != trim( $search_by_keyword ) ):

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
				            'value' => $search_by_keyword,
						            'compare' => "LIKE"
						        )
				    		)
				        
				    );
    	else:
    		$args['meta_query'] = array(
    				'relation' => 'AND',
			        array(
			            'key' => 'user_created_by',
			            'value' => $post_author,
			            'compare' => '='
			        ),
				         
				        
				    );	
    	endif;


    	$contractors = array();
    	// $the_query = new WP_Query( $args );
    	$user_query = new WP_User_Query( $args );
    	// The User Loop
		if ( ! empty( $user_query->results ) ) {
			$API_ContactsController = new API_ContactsController();
			$total_contractors = $user_query->get_total(); ;
			foreach ( $user_query->results as $user ) {
				// do something
				$contractor_info = get_user_by( 'ID', $user->ID );
				// var_dump(  $user->ID );
				if( ! $contractor_info ):
					continue;
				endif;
				$contractor_info_new = new StdClass();
 
 				$contractor_info_new->ID = $contractor_info->ID;
                $contractor_info_new->user_login = $contractor_info->user_login;
                $contractor_info_new->first_name = get_user_meta($contractor_info->ID, 'first_name', true);
	        	$contractor_info_new->last_name = get_user_meta($contractor_info->ID, 'last_name', true); 
                // $contractor_info_new->user_pass = $contractor_info->user_pass;
                $contractor_info_new->user_nicename = $contractor_info->user_nicename;
                $contractor_info_new->user_email = $contractor_info->user_email;
                $contractor_info_new->user_url = $contractor_info->user_url;
                $contractor_info_new->user_registered = $contractor_info->user_registered;
                // $contractor_info_new->user_activation_key = $contractor_info->user_activation_key;
                $contractor_info_new->user_status = $contractor_info->user_status;
                $contractor_info_new->display_name = $contractor_info->display_name;
				$contractor_id = $user->ID;
		       	$contractor_info_new->contractor_created = $contractor_info->user_registered;
		       	$contractor_info_new->company_flag = get_user_meta($contractor_id, 'company_flag', true);
				$contractor_info_new->company_name = get_user_meta($contractor_id, 'company_name', true);

	        	$contractor_info_new->account_number = get_user_meta($contractor_id, 'account_number', true); 
	        	$contractor_info_new->street_address = get_user_meta($contractor_id, 'street_address', true);
	        	$contractor_info_new->street_address_2 = get_user_meta($contractor_id, 'street_address_2', true); 
	        	// $contractor_info_new->unit = get_user_meta($contractor_id, 'unit', true); 
	        	$contractor_info_new->city = get_user_meta($contractor_id, 'city', true); 
	        	$contractor_info_new->state = get_user_meta($contractor_id, 'state', true); 
	        	$contractor_info_new->zip = get_user_meta($contractor_id, 'zip', true); 
	        	// $contractor_info_new->unit = get_user_meta($contractor_id, 'unit', true); 
	        	$services = get_user_meta($contractor_id, 'services', true);
        		$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
	        	$contractor_info_new->services = $services; 
	        	$contractor_info_new->company_primary_phone = get_user_meta($contractor_id, 'company_primary_phone', true); 
	        	$contractor_info_new->company_primary_phone_type = get_user_meta($contractor_id, 'company_primary_phone_type', true); 

	        	$contractor_info_new->company_secondary_phone = get_user_meta($contractor_id, 'company_secondary_phone', true); 
	        	$contractor_info_new->company_secondary_phone_type = get_user_meta($contractor_id, 'company_secondary_phone_type', true); 
	        	$contractor_info_new->company_email = get_user_meta($contractor_id, 'company_email', true); 

	        	//primary info
	        	$contractor_info_new->primary_fname = get_user_meta($contractor_id, 'primary_fname', true); 
	        	$contractor_info_new->primary_lname = get_user_meta($contractor_id, 'primary_lname', true); 
	        	$contractor_info_new->primary_title = get_user_meta($contractor_id, 'primary_title', true); 
	        	$contractor_info_new->primary_phone = get_user_meta($contractor_id, 'primary_phone', true); 
	        	$contractor_info_new->primary_phone_type = get_user_meta($contractor_id, 'primary_phone_type', true); 
	        	$contractor_info_new->primary_secondary_number = get_user_meta($contractor_id, 'primary_secondary_number', true); 
	        	$contractor_info_new->primary_secondary_number_type = get_user_meta($contractor_id, 'primary_secondary_number_type', true); 
	        	$contractor_info_new->primary_email = get_user_meta($contractor_id, 'primary_email', true); 
	        	$contractor_info_new->notes = get_user_meta($contractor_id, 'notes', true); 
	    
	        	$auto_generate_email = get_user_meta($contractor_id, 'auto_generate_email', true);
    			$auto_generate_email_address = get_user_meta($contractor_id, 'auto_generate_email_address', true);
    			$auto_generate_email = ( 1 == $auto_generate_email ) ? 1 : 0;
    			$contractor_info_new->auto_generate_email = $auto_generate_email; 
    			$contractor_info_new->auto_generate_email_address = $auto_generate_email_address; 	

		 		$contractor_info_new->contacts = $API_ContactsController->get_contacts( $contractor_info->ID, "user" );
	       		array_push( $contractors, $contractor_info_new);
	   		
				 
			}
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All contractor data list.', 'jwt-auth' ),
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'total_contractors' => $total_contractors,
					'data'       => $contractors,
					'any_record_created' => $any_contractor_created,
				),
				
			); 
		} else {
			// no users found
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Contractor not found.', 'jwt-auth' ),
					'total_contractors' => 0,
					'data'       => array(),
					'any_record_created' => $any_contractor_created,
				),
				
			); 
		}
		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Contractor not found.', 'jwt-auth' ),
					'total_contractors' => 0,
					'data'       => array(),
					'any_record_created' => $any_contractor_created,
				),
				
			); 
    	
    }


    /**
     * Get a contractor detail
     */ 
    public function get_single_contractor_by_id(WP_REST_Request $request)
 	{
 		
 		$contractor_id    = $request->get_param( 'contractorId' );  

 		if ( ( empty($contractor_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter contractor ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

 
		$contractor_info = get_user_by( 'ID', $contractor_id );

		if ( empty($contractor_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This contractor is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{

			 
    	    $contractor_info->contractor_created = $contractor_info->user_registered;
			$contractor_info->company_name = get_user_meta($contractor_id, 'company_name', true);
			$contractor_info->company_flag = get_user_meta($contractor_id, 'company_flag', true); 
        	$contractor_info->account_number = get_user_meta($contractor_id, 'account_number', true); 
        	$contractor_info->street_address = get_user_meta($contractor_id, 'street_address', true);
        	$contractor_info->street_address_2 = get_user_meta($contractor_id, 'street_address_2', true); 
        	// $contractor_info->unit = get_user_meta($contractor_id, 'unit', true); 
        	$contractor_info->city = get_user_meta($contractor_id, 'city', true); 
        	$contractor_info->state = get_user_meta($contractor_id, 'state', true); 
        	$contractor_info->zip = get_user_meta($contractor_id, 'zip', true);
        	// $contractor_info->unit = get_user_meta($contractor_id, 'unit', true);
        	$services = get_user_meta($contractor_id, 'services', true);
        	$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
        	$contractor_info->services = $services;
        	$contractor_info->company_primary_phone = get_user_meta($contractor_id, 'company_primary_phone', true); 
        	$contractor_info->company_primary_phone_type = get_user_meta($contractor_id, 'company_primary_phone_type', true); 

        	$contractor_info->company_secondary_phone = get_user_meta($contractor_id, 'company_secondary_phone', true); 
        	$contractor_info->company_secondary_phone_type = get_user_meta($contractor_id, 'company_secondary_phone_type', true); 
        	$contractor_info->company_email = get_user_meta($contractor_id, 'company_email', true); 

        	//primary info
        	$contractor_info->primary_fname = get_user_meta($contractor_id, 'primary_fname', true); 
        	$contractor_info->primary_lname = get_user_meta($contractor_id, 'primary_lname', true); 
        	$contractor_info->primary_title = get_user_meta($contractor_id, 'primary_title', true); 
        	$contractor_info->primary_phone = get_user_meta($contractor_id, 'primary_phone', true); 
        	$contractor_info->primary_phone_type = get_user_meta($contractor_id, 'primary_phone_type', true); 
        	$contractor_info->primary_secondary_number = get_user_meta($contractor_id, 'primary_secondary_number', true); 
        	$contractor_info->primary_secondary_number_type = get_user_meta($contractor_id, 'primary_secondary_number_type', true); 
        	$contractor_info->primary_email = get_user_meta($contractor_id, 'primary_email', true);

        	$auto_generate_email = get_user_meta($contractor_id, 'auto_generate_email', true);
        	$auto_generate_email = ( 1 == $auto_generate_email ) ? 1 : 0;
    		$auto_generate_email_address = get_user_meta($contractor_id, 'auto_generate_email_address', true);
    		$contractor_info->auto_generate_email = $auto_generate_email; 
    		$contractor_info->auto_generate_email_address = $auto_generate_email_address;
    		$is_new_user =  get_user_meta( $contractor_id, 'is_new_user', true );
    		$is_new_user = ('yes' == $is_new_user ) ? 'yes' : 'no';
    		$contractor_info->is_new_user =  $is_new_user;
        	$notes = get_posts( 
				array( 
					'post_type'   => 'notes',
					'posts_per_page' => -1,
					'author' => $contractor_id,
					'post_status' => array('publish')
				) 
			);
			$all_notes = array();
			foreach( $notes as $single_note ):
				$temp_note = array();
				$temp_note = (object) $temp_note;
				$temp_note->note_id = $single_note->ID;
				$temp_note->note = $single_note->post_content;
				$temp_note->created_date = $single_note->post_date;
				array_push( $all_notes, $temp_note);
			endforeach;
	        $contractor_info->notes = $all_notes;

	        $attachment_args = array(
			    'post_type' => "attachment",
			    'author' => (int) $contractor_id,
			);
			 
	        $photos = array();
			$attachments = get_posts($attachment_args);
			foreach( $attachments  as $attachment):
				$temp = array();
				$temp = (object) $temp;
				$temp->photo_id = $attachment->ID;
				$temp->real_file_name = get_post_meta( $attachment->ID, 'real_file_name', true );
				$temp->photo_detail = $attachment->post_content;
				$temp->photo_src = $attachment->guid;
				$temp->photo_created = $attachment->post_date; 
				 
				array_push( $photos,  $temp );
			endforeach;
			$contractor_info->photos = $photos;

	        $API_ContactsController = new API_ContactsController();
		 	$contractor_info->contacts = $API_ContactsController->get_contacts( $contractor_id, "user" );

		 	// Now Get all projects for current contracots
		 	 $API_BaseController = new API_BaseController();
			$post_author = $API_BaseController->custom_validate_token($_SERVER);
			// $post_author = 1;
			$project_args = array( 
	    	 	'author' => $post_author,
		        'post_type' => 'projects',
		        'post_status' => 'publish',
		        'posts_per_page' => -1, 
		        
		    );
		    $the_query = new WP_Query( $project_args );
    	 	$contractor_projects = array();
	        $unique_contractor_projects = array();
		    while ( $the_query->have_posts() ) : $the_query->the_post();
		    	$pro_id = get_the_ID();
		        $pro_info = get_post( $pro_id );

		        if ( empty($pro_info) ) :
		        	continue;
		        endif;
		        if( in_array( $pro_id, $unique_contractor_projects ) ) :
		        	continue;
		        endif;
		        $unique_contractor_projects[] = $pro_id;
		         $temp_pro = array();
        		    $temp_pro = (object) $temp_pro;
        		    $temp_pro->ID = $pro_id;
        		    $temp_pro->post_title = $pro_info->post_title;
        		    $temp_pro->post_content = $pro_info->post_content;
		        // get all requested/assign contractors
		        $requested_contractors = get_post_meta($pro_id, 'requested_contractors', true);
		        if('array' != gettype($requested_contractors) ):
					$requested_contractors = array();
				endif;
		        foreach( $requested_contractors as $contractorId ){
		        	
		        	if( $contractorId == $contractor_id ){
		        		$temp_pro->project_created = get_the_date( "d/m/y" , $pro_id);
			 	    	$services = get_post_meta($pro_id, 'services', true);
			        	$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
				        $temp_pro->project_url = site_url(base64_encode( $pro_id));
				        $temp_pro->project_name = get_post_meta($pro_id, 'project_name', true);
				        $temp_pro->project_date = get_post_meta($pro_id, 'project_date', true);
				        $temp_pro->project_detail = get_post_meta($pro_id, 'project_detail', true);
				        $temp_pro->status = get_post_meta($pro_id, 'status', true);
				        $temp_pro->services = $services;
				        $temp_pro->project_status = "request_assigned";
				        array_push( $contractor_projects, $temp_pro);
		        		break;
		        	}
		        }
		        // get all not interested contractors
	        	$not_interested_contractor = get_post_meta($pro_id, 'not_interested_contractor', true);
	        	 if('array' != gettype($not_interested_contractor) ):
					$not_interested_contractor = array();
				endif;
		        foreach( $not_interested_contractor as $contractorId ){
		        	if( $contractorId == $contractor_id ){
		        		$temp_pro->project_created = get_the_date( "d/m/y" , $pro_id);
			 	    	$services = get_post_meta($pro_id, 'services', true);
			        	$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
				        $temp_pro->project_url = site_url(base64_encode( $pro_id));
				        $temp_pro->project_name = get_post_meta($pro_id, 'project_name', true);
				        $temp_pro->project_date = get_post_meta($pro_id, 'project_date', true);
				        $temp_pro->project_detail = get_post_meta($pro_id, 'project_detail', true);
				        $temp_pro->status = get_post_meta($pro_id, 'status', true);
				        $temp_pro->services = $services;
				        $temp_pro->project_status = "not_interested_contractor";
				        array_push( $contractor_projects, $temp_pro);
		        		break;
		        	}
		        }

			    $bids_args = array(
			    	'fields'         => 'ids',
				    'numberposts' => 1,
				    'post_type' => 'project_bids',
				    'post_status' => 'publish',
				    'meta_query' => array(
				        array(
				            'key'       => 'project_id',
				            'value'     =>  $pro_id,
				            'compare' => '='
				        ),
				         array(
				            'key'       => 'contractor_id',
				            'value'     =>  $contractor_id,
				            'compare' => '='
				        ),
				    )
				);
				$all_bids = get_posts( $bids_args );
				if( ! empty( $all_bids ) ):
					foreach( $all_bids as $bid_id){
						$temp_pro->project_created = get_the_date( "d/m/y" , $pro_id);
			 	    	$services = get_post_meta($pro_id, 'services', true);
			        	$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
				        $temp_pro->project_url = site_url(base64_encode( $pro_id));
				        $temp_pro->project_name = get_post_meta($pro_id, 'project_name', true);
				        $temp_pro->project_date = get_post_meta($pro_id, 'project_date', true);
				        $temp_pro->project_detail = get_post_meta($pro_id, 'project_detail', true);
				        $temp_pro->status = get_post_meta($pro_id, 'status', true);
				        $temp_pro->services = $services;
				        $temp_pro->project_status = "project_bid";
				        $temp_pro->bid_price = get_post_meta($bid_id, 'bid_price', true);
	            		$temp_pro->bid_additional_info = get_post_meta($bid_id, 'additional_info', true);
	            		$temp_pro->bid_accepted_by_admin = get_post_meta($bid_id, 'bid_accepted_by_admin', true);
	            		$temp_pro->services = get_post_meta($bid_id, 'services', true);
	            		$temp_pro->primary_phone_number = get_post_meta($bid_id, 'primary_phone_number', true);
	            		$temp_pro->primary_phone_number_type = get_post_meta($bid_id, 'primary_phone_number_type', true);
	            		$temp_pro->secondary_phone_number = get_post_meta($bid_id, 'secondary_phone_number', true);
	            		$temp_pro->secondary_phone_number_type = get_post_meta($bid_id, 'secondary_phone_number_type', true);
	            		$temp_pro->primary_contact_number_type = get_post_meta($bid_id, 'primary_contact_number_type', true);
	            		$temp_pro->primary_contact_number = get_post_meta($bid_id, 'primary_contact_number', true);
	            		$temp_pro->street_address = get_post_meta($bid_id, 'street_address', true);
	            		$temp_pro->street_address_2 = get_post_meta($bid_id, 'street_address_2', true);
	            		$temp_pro->city = get_post_meta($bid_id, 'city', true);
	            		$temp_pro->state = get_post_meta($bid_id, 'state', true);
	            		$temp_pro->zip = get_post_meta($bid_id, 'zip', true);
	            		$temp_pro->honorific = get_post_meta($bid_id, 'honorific', true);
				        array_push( $contractor_projects, $temp_pro);	
					}
				endif;

				// project photo
				$pro_attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $pro_id,
			    'posts_per_page' => -1,
				);
		        $project_photos = array();
				$pro_attachments = get_posts($pro_attachment_args);
				foreach( $pro_attachments  as $attachment):
					$temp_1 = array();
					$temp_1 = (object) $temp_1;
					$temp_1->photo_id = $attachment->ID;
	 				$temp_1->real_file_name = get_post_meta( $attachment->ID, 'real_file_name', true );
					$temp_1->photo_detail = $attachment->post_content;
					$temp_1->photo_src = $attachment->guid;
					$temp_1->photo_created = $attachment->post_date; 
					array_push( $project_photos,  $temp_1 );
				endforeach;
				$temp_pro->project_photos = $project_photos;
		    endwhile; 
	    	/* Restore original Post Data */
			wp_reset_postdata();
			$contractor_info->contractor_projects = $contractor_projects;
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Contractor Details', 'jwt-auth' ),
						'data'       => $contractor_info,
					),
					 
				);
		}

 	}


 	/**
 	 * Delete single or multiple contractors
 	 * 
 	 */ 
 	public function delete_contractor_by_id(WP_REST_Request $request){
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
 		$contractor_ids    = $request->get_param( 'contractor_ids' ); 
 		
 		if( empty( $contractor_ids) || gettype($contractor_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one contractor id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		$API_ContactsController = new API_ContactsController();
 		$total_delete_contractors = 0;
 		foreach( $contractor_ids as $contractor_id ){
 			$user = get_user_by( 'ID', $contractor_id );
 			if( $user ){
                if(wp_delete_user( $user->ID ) ):
                	// delete contacts
 				$API_ContactsController->delete_all_notes_by_user_post_id($user->ID, "post" );
 				$total_delete_contractors++;
                endif;
 			}else{
 				continue;
 			}
 			 
 			 
 		}
 		if( $total_delete_contractors > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Contractor successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_deleted_contractors' => $total_delete_contractors),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any contractor', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

 	/**
 	 * Update a contractor API
 	 */ 

 	public function update_contractor_by_id( WP_REST_Request $request ){
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
 		$user_id    = $request->get_param( 'contractorId' ); 
 		$company_name    = $request->get_param( 'company_name' );
 		// $company_flag    = $request->get_param( 'company_flag' ); 
 		$author    = $request->get_param( 'author' ); 
 		$account_number    = $request->get_param( 'account_number' ); 
 		$street_address    = $request->get_param( 'street_address' );
 		$street_address_2    = $request->get_param( 'street_address_2' ); 
 		$services    = $request->get_param( 'services' );
 		$services = ( ''  != $services ) ? $services : 'zzzzzzzzzz';
 		$city    = $request->get_param( 'city' ); 
 		$state    = $request->get_param( 'state' );

 		$zip    = $request->get_param( 'zip' ); 
 		// $unit    = $request->get_param( 'unit' ); 
 		$company_primary_phone    = $request->get_param( 'company_primary_phone' ); 
 		$company_primary_phone_type    = $request->get_param( 'company_primary_phone_type' ); 
 		$company_secondary_phone    = $request->get_param( 'company_secondary_phone' );

 		$company_secondary_phone_type    = $request->get_param( 'company_secondary_phone_type' ); 
 		$company_email    = $request->get_param( 'company_email' );

 		 // primary detalis
 		$primary_fname    = $request->get_param( 'primary_fname' );
 		$primary_lname    = $request->get_param( 'primary_lname' );
 		$primary_title    = $request->get_param( 'primary_title' ); 

 		$primary_phone    = $request->get_param( 'primary_phone' );
 		$primary_phone_type    = $request->get_param( 'primary_phone_type' );
 		 

 		$primary_secondary_number    = $request->get_param( 'primary_secondary_number' );
 		$primary_secondary_number_type    = $request->get_param( 'primary_secondary_number_type' );
 		$primary_email    = $request->get_param( 'primary_email' );
 		
 		//notes
 		$notes    = $request->get_param( 'notes' );
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

		if(empty($company_email)){ 

			$errors_arr[] = __( 'Please enter email address.', 'jwt-auth' ); 
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
	    $auto_generate_email_stataus = 1;
	    $user = get_user_by( 'id', $user_id );
	    if ( $user ) {
	    	$old_company_email = $user->user_email;
	    	$auto_generate_email = get_user_meta($user_id, 'auto_generate_email', true);
	    	$auto_generate_email = ( 1 == $auto_generate_email ) ? 1 : 0;
    		$auto_generate_email_address = get_user_meta($user_id, 'auto_generate_email_address', true);
    		
    		if( ( 1 == $auto_generate_email ) && ($company_email != $auto_generate_email_address) ):
    			$auto_generate_email_stataus = 0;
    		endif;
    		
	    }
	    $display_name = '';
	 	if( '' != trim( $company_name ) ){
	 		$display_name = $company_name;
	 	}else if( '' != trim( $primary_fname ) ){
	 		$display_name = $primary_fname.' '.$primary_lname;
	 	}else{
	 		$display_name = explode("@", $company_email)[0];
	 	}
 		

 		if ( $user ) {
 			$update_status = wp_update_user(
 			 array( 'ID' => $user_id,
 			 	'user_email' => $company_email,
 			 	'user_login' => $company_email,
 			 	'user_nicename'  => $primary_fname.' '.$primary_lname,
				'display_name'   => $display_name,
				'nickname'       => $primary_fname.' '.$primary_lname,
				'first_name'     => $primary_fname, 
				'last_name'      => $primary_lname,
 			 	 ) );
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
				update_user_meta($user_id, 'first_name', $primary_fname);
		        update_user_meta($user_id, 'last_name', $primary_lname); 
		        update_user_meta($user_id, 'primary_title', $primary_title);   
		        // update_user_meta($user_id, 'company_flag', $company_flag);   
		        //contect info
		        update_user_meta($user_id, 'company_name', $company_name);  
		        update_user_meta($user_id, 'street_address', $street_address);
		        update_user_meta($user_id, 'street_address_2', $street_address_2); 
		        // update_user_meta($user_id, 'unit', $unit);  
		        update_user_meta($user_id, 'city', $city);  
		        update_user_meta($user_id, 'state', $state);  
		        update_user_meta($user_id, 'zip', $zip); 
		        // update_user_meta($user_id, 'unit', $unit); 
		        update_user_meta($user_id, 'account_number', $account_number);  
		        update_user_meta($user_id, 'company_primary_phone', $company_primary_phone);  
		        update_user_meta($user_id, 'company_primary_phone_type', $company_primary_phone_type);  
		        update_user_meta($user_id, 'company_secondary_phone', $company_secondary_phone);  
		        update_user_meta($user_id, 'company_secondary_phone_type', $company_secondary_phone_type);  
		        update_user_meta($user_id, 'company_email', $company_email);  

	 
		 		//primary info
		        update_user_meta($user_id, 'primary_fname', $primary_fname);
		        update_user_meta($user_id, 'primary_lname', $primary_lname); 
		        update_user_meta($user_id, 'primary_title', $primary_title); 
		        update_user_meta($user_id, 'primary_phone', $primary_phone); 
		        update_user_meta($user_id, 'primary_phone_type', $primary_phone_type); 

		        update_user_meta($user_id, 'primary_secondary_number', $primary_secondary_number); 
		        update_user_meta($user_id, 'primary_secondary_number_type', $primary_secondary_number_type);  
		        update_user_meta($user_id, 'primary_email', $primary_email); 
		        update_user_meta($user_id, 'services', $services);

	        	//extra 
	        	update_user_meta($user_id, 'notes', $notes);
	        	if( 0 === $auto_generate_email_stataus ):
	        		update_user_meta($user_id, 'auto_generate_email', 0);
	        	endif; 
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

 	public function contractor_conpany_flag( WP_REST_Request $request ){
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
    	$contractor_id    = $request->get_param( 'contractor_id' );
 		$company_flag    = $request->get_param( 'company_flag' );
 		$errors_arr = array();

		if(empty($contractor_id)){

 			$errors_arr[] = __( 'Please enter contractor id', 'jwt-auth' ); 
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
	    $user = get_user_by( 'id', $contractor_id );
	    if ( $user ) {
	    	update_user_meta( $contractor_id, 'company_flag', $company_flag);
		    return new WP_REST_Response(
						array(
							'success'    => true,
							'statusCode' => 200,
							'code'       => 'success',
							'message'    => __( 'Company flag successfully updated.', 'jwt-auth' ),
							'data'       => array(),
						),
						 
					);
	    }else {
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter valid contractor ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
	    
    }
}

new API_ContractorsController();
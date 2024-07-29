<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class API_TenantsControler extends API_BaseController {

	public function __construct() 
	{  
		//add_action('wp_footer','get_flaged_tenants_count');

 	}

 	public function get_tenants_count(WP_REST_Request $request)
 	{

 		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER); 
 		$tenants_with_reminder_status = get_posts(array(
            'numberposts'   => -1,
            'post_type'     => array('tenants'),
            'author' => $post_author,
            // 'meta_key'     => 'company_flag',
            // 'meta_value'   => 'true',
        ));
        // echo "<pre>";
        // print_r($tenants_with_reminder_status);die;
        if(is_array($tenants_with_reminder_status)){
        	$count = count($tenants_with_reminder_status);
        }else{
        	$count = 0;
        }

        if(!empty($tenants_with_reminder_status))
 		{ 
 			//$API_ContactsController = new API_ContactsController();
  			foreach ($tenants_with_reminder_status as $key => $data) {
 				// code...
  				//$tenants_with_reminder_status[$key]->tenant_created = get_the_date( "d/m/y" , $data->ID);
  				$tenants_with_reminder_status[$key]->login_access = get_post_meta($data->ID, 'login_access', true); 
 				$tenants_with_reminder_status[$key]->first_name = get_post_meta($data->ID, 'first_name', true);
	        	$tenants_with_reminder_status[$key]->last_name = get_post_meta($data->ID, 'last_name', true); 

	        	$tenants_with_reminder_status[$key]->company_name = get_post_meta($data->ID, 'company_name', true);
	        	$tenants_with_reminder_status[$key]->company_flag = get_post_meta($data->ID, 'company_flag', true); 
	        	$tenants_with_reminder_status[$key]->street_address = get_post_meta($data->ID, 'street_address', true);
	        	$tenants_with_reminder_status[$key]->street_address_2 = get_post_meta($data->ID, 'street_address_2', true); 
	        	$tenants_with_reminder_status[$key]->unit = get_post_meta($data->ID, 'unit', true);
	        	$tenants_with_reminder_status[$key]->unit_type = get_post_meta($data->ID, 'unit_type', true); 
	        	$tenants_with_reminder_status[$key]->city = get_post_meta($data->ID, 'city', true); 
	        	$tenants_with_reminder_status[$key]->state = get_post_meta($data->ID, 'state', true); 
	        	$tenants_with_reminder_status[$key]->zip_code = get_post_meta($data->ID, 'zip_code', true); 
	        	$tenants_with_reminder_status[$key]->status = get_post_meta($data->ID, 'status', true); 
	        	$tenants_with_reminder_status[$key]->complex = get_post_meta($data->ID, 'complex', true); 
 				$tenants_with_reminder_status[$key]->mailbox = get_post_meta($data->ID, 'mailbox', true); 
	        	$tenants_with_reminder_status[$key]->phone_number = get_post_meta($data->ID, 'phone_number', true); 
	        	$tenants_with_reminder_status[$key]->phone_number_type = get_post_meta($data->ID, 'phone_number_type', true); 
	        	$tenants_with_reminder_status[$key]->primary_email = get_post_meta($data->ID, 'primary_email', true); 

	        	//primary info
	        	$tenants_with_reminder_status[$key]->primary_fname = get_post_meta($data->ID, 'primary_fname', true); 
	        	$tenants_with_reminder_status[$key]->primary_lname = get_post_meta($data->ID, 'primary_lname', true); 
	        	$tenants_with_reminder_status[$key]->primary_title = get_post_meta($data->ID, 'primary_title', true); 
	        	$tenants_with_reminder_status[$key]->primary_phone = get_post_meta($data->ID, 'primary_phone', true); 
	        	$tenants_with_reminder_status[$key]->primary_phone_type = get_post_meta($data->ID, 'primary_phone_type', true); 
	        	$tenants_with_reminder_status[$key]->primary_second_phone = get_post_meta($data->ID, 'primary_second_phone', true); 
	        	$tenants_with_reminder_status[$key]->primary_second_phone_type = get_post_meta($data->ID, 'primary_second_phone_type', true); 
	        	$tenants_with_reminder_status[$key]->primary_contact_email = get_post_meta($data->ID, 'primary_contact_email', true); 

	         	//secondary info  
	        	$tenants_with_reminder_status[$key]->secondary_fname = get_post_meta($data->ID, 'secondary_fname', true); 
	        	$tenants_with_reminder_status[$key]->secondary_lname = get_post_meta($data->ID, 'secondary_lname', true); 
	        	$tenants_with_reminder_status[$key]->secondary_title = get_post_meta($data->ID, 'secondary_title', true); 
	        	$tenants_with_reminder_status[$key]->secondary_primary_phone = get_post_meta($data->ID, 'secondary_primary_phone', true); 
	        	$tenants_with_reminder_status[$key]->secondary_primary_phone_type = get_post_meta($data->ID, 'secondary_primary_phone_type', true); 
	        	$tenants_with_reminder_status[$key]->secondary_phone = get_post_meta($data->ID, 'secondary_phone', true); 
	        	$tenants_with_reminder_status[$key]->secondary_phone_type = get_post_meta($data->ID, 'secondary_phone_type', true); 
	        	$tenants_with_reminder_status[$key]->secondary_contact_email = get_post_meta($data->ID, 'secondary_contact_email', true); 
	        	
	        	$tenants_with_reminder_status[$key]->notes = get_post_meta($data->ID, 'notes', true); 
	        	$tenants_with_reminder_status[$key]->photos = get_post_meta($data->ID, 'photos', true); 
	        	$tenants_with_reminder_status[$key]->photos_details = get_post_meta($data->ID, 'photos_details', true); 
    			$tenants_with_reminder_status[$key]->property = get_post_meta($data->ID, 'property', true);
 				// $tenants_with_reminder_status[$key]->contacts = $API_ContactsController->get_contacts( $data->ID, "post" );
 			}
 		}
        
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All tenant', 'jwt-auth' ),
					'total_count'       => $count,
					'tenant_list' => $tenants_with_reminder_status,
				),
				
			); 

 	}
 	 
 	public function get_flaged_tenants_count(WP_REST_Request $request)
 	{

 		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER); 
 		$tenants_with_reminder_status = get_posts(array(
            'numberposts'   => -1,
            'post_type'     => array('tenants'),
            'author' => $post_author,
            'meta_key'     => 'company_flag',
            'meta_value'   => 'true',
        ));
        // echo "<pre>";
        // print_r($tenants_with_reminder_status);die;
        if(is_array($tenants_with_reminder_status)){
        	$count = count($tenants_with_reminder_status);
        }else{
        	$count = 0;
        }

        if(!empty($tenants_with_reminder_status))
 		{ 
 			//$API_ContactsController = new API_ContactsController();
  			foreach ($tenants_with_reminder_status as $key => $data) {
 				// code...
  				//$tenants_with_reminder_status[$key]->tenant_created = get_the_date( "d/m/y" , $data->ID);
  				$tenants_with_reminder_status[$key]->login_access = get_post_meta($data->ID, 'login_access', true); 
 				$tenants_with_reminder_status[$key]->first_name = get_post_meta($data->ID, 'first_name', true);
	        	$tenants_with_reminder_status[$key]->last_name = get_post_meta($data->ID, 'last_name', true); 

	        	$tenants_with_reminder_status[$key]->company_name = get_post_meta($data->ID, 'company_name', true);
	        	$tenants_with_reminder_status[$key]->company_flag = get_post_meta($data->ID, 'company_flag', true); 
	        	$tenants_with_reminder_status[$key]->street_address = get_post_meta($data->ID, 'street_address', true);
	        	$tenants_with_reminder_status[$key]->street_address_2 = get_post_meta($data->ID, 'street_address_2', true); 
	        	$tenants_with_reminder_status[$key]->unit = get_post_meta($data->ID, 'unit', true);
	        	$tenants_with_reminder_status[$key]->unit_type = get_post_meta($data->ID, 'unit_type', true); 
	        	$tenants_with_reminder_status[$key]->city = get_post_meta($data->ID, 'city', true); 
	        	$tenants_with_reminder_status[$key]->state = get_post_meta($data->ID, 'state', true); 
	        	$tenants_with_reminder_status[$key]->zip_code = get_post_meta($data->ID, 'zip_code', true); 
	        	$tenants_with_reminder_status[$key]->status = get_post_meta($data->ID, 'status', true); 
	        	$tenants_with_reminder_status[$key]->complex = get_post_meta($data->ID, 'complex', true); 
 				$tenants_with_reminder_status[$key]->mailbox = get_post_meta($data->ID, 'mailbox', true); 
	        	$tenants_with_reminder_status[$key]->phone_number = get_post_meta($data->ID, 'phone_number', true); 
	        	$tenants_with_reminder_status[$key]->phone_number_type = get_post_meta($data->ID, 'phone_number_type', true); 
	        	$tenants_with_reminder_status[$key]->primary_email = get_post_meta($data->ID, 'primary_email', true); 

	        	//primary info
	        	$tenants_with_reminder_status[$key]->primary_fname = get_post_meta($data->ID, 'primary_fname', true); 
	        	$tenants_with_reminder_status[$key]->primary_lname = get_post_meta($data->ID, 'primary_lname', true); 
	        	$tenants_with_reminder_status[$key]->primary_title = get_post_meta($data->ID, 'primary_title', true); 
	        	$tenants_with_reminder_status[$key]->primary_phone = get_post_meta($data->ID, 'primary_phone', true); 
	        	$tenants_with_reminder_status[$key]->primary_phone_type = get_post_meta($data->ID, 'primary_phone_type', true); 
	        	$tenants_with_reminder_status[$key]->primary_second_phone = get_post_meta($data->ID, 'primary_second_phone', true); 
	        	$tenants_with_reminder_status[$key]->primary_second_phone_type = get_post_meta($data->ID, 'primary_second_phone_type', true); 
	        	$tenants_with_reminder_status[$key]->primary_contact_email = get_post_meta($data->ID, 'primary_contact_email', true); 

	         	//secondary info  
	        	$tenants_with_reminder_status[$key]->secondary_fname = get_post_meta($data->ID, 'secondary_fname', true); 
	        	$tenants_with_reminder_status[$key]->secondary_lname = get_post_meta($data->ID, 'secondary_lname', true); 
	        	$tenants_with_reminder_status[$key]->secondary_title = get_post_meta($data->ID, 'secondary_title', true); 
	        	$tenants_with_reminder_status[$key]->secondary_primary_phone = get_post_meta($data->ID, 'secondary_primary_phone', true); 
	        	$tenants_with_reminder_status[$key]->secondary_primary_phone_type = get_post_meta($data->ID, 'secondary_primary_phone_type', true); 
	        	$tenants_with_reminder_status[$key]->secondary_phone = get_post_meta($data->ID, 'secondary_phone', true); 
	        	$tenants_with_reminder_status[$key]->secondary_phone_type = get_post_meta($data->ID, 'secondary_phone_type', true); 
	        	$tenants_with_reminder_status[$key]->secondary_contact_email = get_post_meta($data->ID, 'secondary_contact_email', true); 
	        	
	        	$tenants_with_reminder_status[$key]->notes = get_post_meta($data->ID, 'notes', true); 
	        	$tenants_with_reminder_status[$key]->photos = get_post_meta($data->ID, 'photos', true); 
	        	$tenants_with_reminder_status[$key]->photos_details = get_post_meta($data->ID, 'photos_details', true); 
    			$tenants_with_reminder_status[$key]->property = get_post_meta($data->ID, 'property', true);
 				// $tenants_with_reminder_status[$key]->contacts = $API_ContactsController->get_contacts( $data->ID, "post" );
 			}
 		}
        
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All tenant', 'jwt-auth' ),
					'total_count'       => $count,
					'tenant_list' => $tenants_with_reminder_status,
				),
				
			); 

 	}
 	public function get_all_client_list(WP_REST_Request $request)
 	{

 		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 	 	$any_tenant_created = get_user_meta($post_author, 'any_tenant_created', true); 
 		$tenant = get_posts( 
							array( 
								'author' => $post_author,
								'post_type'   => 'tenants',
								 'post_status' => array('publish'),
								 'posts_per_page' => -1,
							) 
						);
 		if(!empty($tenant))
 		{ 
 			$API_ContactsController = new API_ContactsController();
  			foreach ($tenant as $key => $data) {
 				// code...
  				$tenant[$key]->tenant_created = get_the_date( "d/m/y" , $data->ID);
  				$tenant[$key]->login_access = get_post_meta($data->ID, 'login_access', true); 
 				$tenant[$key]->first_name = get_post_meta($data->ID, 'first_name', true);
	        	$tenant[$key]->last_name = get_post_meta($data->ID, 'last_name', true); 

	        	$tenant[$key]->company_name = get_post_meta($data->ID, 'company_name', true);
	        	$tenant[$key]->company_flag = get_post_meta($data->ID, 'company_flag', true); 
	        	$tenant[$key]->street_address = get_post_meta($data->ID, 'street_address', true);
	        	$tenant[$key]->street_address_2 = get_post_meta($data->ID, 'street_address_2', true); 
	        	$tenant[$key]->unit = get_post_meta($data->ID, 'unit', true);
	        	$tenant[$key]->unit_type = get_post_meta($data->ID, 'unit_type', true); 
	        	$tenant[$key]->city = get_post_meta($data->ID, 'city', true); 
	        	$tenant[$key]->state = get_post_meta($data->ID, 'state', true); 
	        	$tenant[$key]->zip_code = get_post_meta($data->ID, 'zip_code', true); 
	        	$tenant[$key]->status = get_post_meta($data->ID, 'status', true); 
	        	$tenant[$key]->complex = get_post_meta($data->ID, 'complex', true); 
 				$tenant[$key]->mailbox = get_post_meta($data->ID, 'mailbox', true); 
	        	$tenant[$key]->phone_number = get_post_meta($data->ID, 'phone_number', true); 
	        	$tenant[$key]->phone_number_type = get_post_meta($data->ID, 'phone_number_type', true); 
	        	$tenant[$key]->primary_email = get_post_meta($data->ID, 'primary_email', true); 

	        	//primary info
	        	$tenant[$key]->primary_fname = get_post_meta($data->ID, 'primary_fname', true); 
	        	$tenant[$key]->primary_lname = get_post_meta($data->ID, 'primary_lname', true); 
	        	$tenant[$key]->primary_title = get_post_meta($data->ID, 'primary_title', true); 
	        	$tenant[$key]->primary_phone = get_post_meta($data->ID, 'primary_phone', true); 
	        	$tenant[$key]->primary_phone_type = get_post_meta($data->ID, 'primary_phone_type', true); 
	        	$tenant[$key]->primary_second_phone = get_post_meta($data->ID, 'primary_second_phone', true); 
	        	$tenant[$key]->primary_second_phone_type = get_post_meta($data->ID, 'primary_second_phone_type', true); 
	        	$tenant[$key]->primary_contact_email = get_post_meta($data->ID, 'primary_contact_email', true); 

	         	//secondary info  
	        	$tenant[$key]->secondary_fname = get_post_meta($data->ID, 'secondary_fname', true); 
	        	$tenant[$key]->secondary_lname = get_post_meta($data->ID, 'secondary_lname', true); 
	        	$tenant[$key]->secondary_title = get_post_meta($data->ID, 'secondary_title', true); 
	        	$tenant[$key]->secondary_primary_phone = get_post_meta($data->ID, 'secondary_primary_phone', true); 
	        	$tenant[$key]->secondary_primary_phone_type = get_post_meta($data->ID, 'secondary_primary_phone_type', true); 
	        	$tenant[$key]->secondary_phone = get_post_meta($data->ID, 'secondary_phone', true); 
	        	$tenant[$key]->secondary_phone_type = get_post_meta($data->ID, 'secondary_phone_type', true); 
	        	$tenant[$key]->secondary_contact_email = get_post_meta($data->ID, 'secondary_contact_email', true); 
	        	
	        	$tenant[$key]->notes = get_post_meta($data->ID, 'notes', true); 
	        	$tenant[$key]->photos = get_post_meta($data->ID, 'photos', true); 
	        	$tenant[$key]->photos_details = get_post_meta($data->ID, 'photos_details', true); 
    			$tenant[$key]->property = get_post_meta($data->ID, 'property', true);
 				$tenant[$key]->contacts = $API_ContactsController->get_contacts( $data->ID, "post" );
 			}
 		}

		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All tenant', 'jwt-auth' ),
					'data'       => $tenant,
					'any_record_created' => $any_tenant_created,
				),
				
			); 

 	}

 	public function get_single_tenant_by_id(WP_REST_Request $request)
 	{
 		$tenant_id    = $request->get_param( 'tenantId' );  
 		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		if ( ( empty($tenant_id) ) || ( 'tenants' != get_post_type($tenant_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter tenant ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

 
		$tenant_info = get_post( $tenant_id );

		if ( empty($tenant_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This tenant is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{

	    	 

 	    	$tenant_info->tenant_created = get_the_date( "d/m/y" , $tenant_id);
 	    	$tenant_info->login_access = get_post_meta($tenant_id, 'login_access', true); 
	    	$tenant_info->first_name = get_post_meta($tenant_id, 'first_name', true);
	        $tenant_info->last_name = get_post_meta($tenant_id, 'last_name', true); 

	        $tenant_info->company_name = get_post_meta($tenant_id, 'company_name', true); 
	        $tenant_info->company_flag = get_post_meta($tenant_id, 'company_flag', true); 
	        $tenant_info->street_address = get_post_meta($tenant_id, 'street_address', true);
	        $tenant_info->street_address_2 = get_post_meta($tenant_id, 'street_address_2', true);
	        
	        $tenant_info->unit = get_post_meta($tenant_id, 'unit', true);
	        $tenant_info->unit_type = get_post_meta($tenant_id, 'unit_type', true);  
	        $tenant_info->city = get_post_meta($tenant_id, 'city', true); 
	        $tenant_info->state = get_post_meta($tenant_id, 'state', true); 
	        $tenant_info->zip_code = get_post_meta($tenant_id, 'zip_code', true); 
	        $tenant_info->status = get_post_meta($tenant_id, 'status', true); 
	        $tenant_info->complex = get_post_meta($tenant_id, 'complex', true); 
 			$tenant_info->mailbox =  get_post_meta( $tenant_id, 'mailbox', true);
	        $tenant_info->phone_number = get_post_meta($tenant_id, 'phone_number', true); 
	        $tenant_info->phone_number_type = get_post_meta($tenant_id, 'phone_number_type', true);
	        $tenant_info->property = get_post_meta($tenant_id, 'property', true);
	         
	        $tenant_info->primary_email = get_post_meta($tenant_id, 'primary_email', true); 

	        	//primary info
	        $tenant_info->primary_fname = get_post_meta($tenant_id, 'primary_fname', true); 
	        $tenant_info->primary_lname = get_post_meta($tenant_id, 'primary_lname', true); 
	        $tenant_info->primary_title = get_post_meta($tenant_id, 'primary_title', true); 
	        $tenant_info->primary_phone = get_post_meta($tenant_id, 'primary_phone', true); 
	        $tenant_info->primary_phone_type = get_post_meta($tenant_id, 'primary_phone_type', true); 
	        $tenant_info->primary_second_phone = get_post_meta($tenant_id, 'primary_second_phone', true); 
	        $tenant_info->primary_second_phone_type = get_post_meta($tenant_id, 'primary_second_phone_type', true); 
	        $tenant_info->primary_contact_email = get_post_meta($tenant_id, 'primary_contact_email', true); 

	         	//secondary info  
	        $tenant_info->secondary_fname = get_post_meta($tenant_id, 'secondary_fname', true); 
	        $tenant_info->secondary_lname = get_post_meta($tenant_id, 'secondary_lname', true); 
	        $tenant_info->secondary_title = get_post_meta($tenant_id, 'secondary_title', true); 
	        $tenant_info->secondary_primary_phone = get_post_meta($tenant_id, 'secondary_primary_phone', true); 
	        $tenant_info->secondary_primary_phone_type = get_post_meta($tenant_id, 'secondary_primary_phone_type', true); 
	        $tenant_info->secondary_phone = get_post_meta($tenant_id, 'secondary_phone', true); 
	        $tenant_info->secondary_phone_type = get_post_meta($tenant_id, 'secondary_phone_type', true); 
	        $tenant_info->secondary_contact_email = get_post_meta($tenant_id, 'secondary_contact_email', true); 
	        $notes = get_posts( 
				array( 
					'post_type'   => 'notes',
					'post_parent' => $tenant_id,
					'post_status' => array('publish'),
					'posts_per_page' => -1,
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
	        	
	        $tenant_info->notes = $all_notes; 
	        $tenant_info->photos = get_post_meta($tenant_id, 'photos', true); 
	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $tenant_id,
			    'posts_per_page' => -1,
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
			$tenant_info->photos = $photos;
			$shared_attachment_args = array(
			    'post_type' => "attachment",
			    'post_author' => $post_author,
			    'posts_per_page' => -1,
			    'meta_query' => array(
			    	'relation' => 'AND',
			        array(
			            'key'       => 'shared_from	',
			            'value'     => $post_author,
			            'compare' => '='
			        ),array(
			            'key'       => 'shared_to',
			            'value'     => $tenant_id,
			            'compare' => '='
			        ),array(
			            'key'       => 'shared_for',
			            'value'     => 'tenant',
			            'compare' => '='
			        )
			    )
			);
	        $shared_files = array();
			$shared_attachments = get_posts($shared_attachment_args);
			foreach( $shared_attachments  as $attachment):
				$temp = array();
				$temp = (object) $temp;
				$temp->photo_id = $attachment->ID;
				$temp->real_file_name = get_post_meta( $attachment->ID, 'real_file_name', true );
				$temp->photo_detail = $attachment->post_content;
				$temp->photo_src = $attachment->guid;
				$temp->photo_created = $attachment->post_date; 
				array_push( $shared_files,  $temp );
			endforeach;
			$tenant_info->shared_files = $shared_files;
		 	$API_ContactsController = new API_ContactsController();
		 	$tenant_info->contacts = $API_ContactsController->get_contacts( $tenant_id, "post" );
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'tenant Details', 'jwt-auth' ),
						'data'       => $tenant_info,
					),
					 
				);
		}

 	}

 	public function update_client_by_id(WP_REST_Request $request)
 	{ 
 		
 		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
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
 		$tenant_id    = $request->get_param( 'tenantId' ); 
 		//contect info
 		$company_name    = $request->get_param( 'company_name' );
 		$login_access    = $request->get_param( 'login_access' );
 		// $company_flag    = $request->get_param( 'company_flag' );
 		$address    = $request->get_param( 'street_address' );
 		$street_address_2    = $request->get_param( 'street_address_2' ); 
 		$unit    = $request->get_param( 'unit' ); 
 		$unit_type    = $request->get_param( 'unit_type' ); 
 		$state    = $request->get_param( 'state' ); 
 		$city    = $request->get_param( 'city' ); 
 		$zip_code    = $request->get_param( 'zip_code' );

 		$phone_number    = $request->get_param( 'phone_number' ); 
 		$phone_number_type    = $request->get_param( 'phone_number_type' ); 
 		$phone_number_type_custom    = $request->get_param( 'phone_number_type_custom' ); 
 		$primary_email    = $request->get_param( 'primary_email' );

 		$status    = $request->get_param( 'status' ); 
 		$complex    = $request->get_param( 'complex' );
 		$mailbox    = $request->get_param( 'mailbox' );
 		//primary info
 		$primary_fname    = $request->get_param( 'primary_fname' );
 		$primary_lname    = $request->get_param( 'primary_lname' );
 		$primary_title    = $request->get_param( 'primary_title' ); 

 		$primary_phone    = $request->get_param( 'primary_phone' );
 		$primary_phone_type    = $request->get_param( 'primary_phone_type' );
 		$primary_phone_type_custom    = $request->get_param( 'primary_phone_type_custom' );

 		$primary_second_phone    = $request->get_param( 'primary_second_phone' );
 		$primary_second_phone_type    = $request->get_param( 'primary_second_phone_type' );
 		$primary_second_phone_type_custom    = $request->get_param( 'primary_second_phone_type_custom' ); 
 		$primary_contact_email    = $request->get_param( 'primary_contact_email' );
 		

 		//secondary info  
 		$secondary_fname    = $request->get_param( 'secondary_fname' );
 		$secondary_lname    = $request->get_param( 'secondary_lname' );
 		$secondary_title    = $request->get_param( 'secondary_title' ); 

 		$secondary_primary_phone    = $request->get_param( 'secondary_primary_phone' );
 		$secondary_primary_phone_type    = $request->get_param( 'secondary_primary_phone_type' );
 		$secondary_primary_phone_type_custom    = $request->get_param( 'secondary_primary_phone_type_custom' );

 		$secondary_phone    = $request->get_param( 'secondary_phone' );
 		$secondary_phone_type    = $request->get_param( 'secondary_phone_type' );
 		$secondary_phone_type_custom    = $request->get_param( 'secondary_phone_type_custom' );

 		$secondary_contact_email    = $request->get_param( 'secondary_contact_email' );
 		$property    = $request->get_param( 'property' );
 		//notes
 		$notes    = $request->get_param( 'notes' ); 

 		//photos
 		$photos = array();
 	

 		if( ( empty( $tenant_id ) ) || "tenants" != get_post_type( $tenant_id ) ) { 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Tenant id is not exist.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  
		} 
  

		$errors_arr = array();

		// if(empty($primary_fname)){

 	// 		$errors_arr[] = __( 'Please enter primary first name.', 'jwt-auth' ); 
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

	    $update_args = array();
	    $update_args['ID'] = $tenant_id;
		$update_args['post_title'] = $company_name; // Tenant Title
		// $update_args['post_title'] = $primary_fname.' '.$primary_lname; // Tenant Title
		$update_args['post_content'] = $primary_fname.' '.$primary_lname; // Tenant Description
 		if(wp_update_post( $update_args )):
 			update_post_meta( $tenant_id, 'first_name', $primary_fname);
	        update_post_meta( $tenant_id, 'last_name', $primary_lname); 
	        update_post_meta( $tenant_id, 'primary_title', $primary_title);   
	        update_post_meta( $tenant_id, 'login_access', $login_access);


	        //contect info
	        update_post_meta( $tenant_id, 'company_name', $company_name);
	        // update_post_meta( $tenant_id, 'company_flag', $company_flag);  
	        update_post_meta( $tenant_id, 'street_address', $address);  
	        update_post_meta( $tenant_id, 'street_address_2', $street_address_2);  
	        update_post_meta( $tenant_id, 'unit', $unit);
	        update_post_meta( $tenant_id, 'unit_type', $unit_type);  
	        update_post_meta( $tenant_id, 'city', $city);  
	        update_post_meta( $tenant_id, 'state', $state);  
	        update_post_meta( $tenant_id, 'zip_code', $zip_code); 
	        update_post_meta( $tenant_id, 'phone_number', $phone_number);  
	        update_post_meta( $tenant_id, 'phone_number_type', $phone_number_type);  
	        update_post_meta( $tenant_id, 'phone_number_type_custom', $phone_number_type_custom);  
	        update_post_meta( $tenant_id, 'primary_email', $primary_email);  
	        update_post_meta( $tenant_id, 'status', $status);  
	        update_post_meta( $tenant_id, 'complex', $complex);  
	        update_post_meta( $tenant_id, 'mailbox', $mailbox);  
 
	 		//primary info
	        update_post_meta( $tenant_id, 'primary_fname', $primary_fname);
	        update_post_meta( $tenant_id, 'primary_lname', $primary_lname); 
	        update_post_meta( $tenant_id, 'primary_title', $primary_title); 
	        update_post_meta( $tenant_id, 'primary_phone', $primary_phone); 
	        update_post_meta( $tenant_id, 'primary_phone_type', $primary_phone_type); 
	        update_post_meta( $tenant_id, 'primary_phone_type_custom', $primary_phone_type_custom); 

	        update_post_meta( $tenant_id, 'primary_second_phone', $primary_second_phone); 
	        update_post_meta( $tenant_id, 'primary_second_phone_type', $primary_second_phone_type);  
	        update_post_meta( $tenant_id, 'primary_second_phone_type_custom', $primary_second_phone_type_custom);  
	        update_post_meta( $tenant_id, 'primary_contact_email', $primary_contact_email); 

	         //secondary info  
	        update_post_meta( $tenant_id, 'secondary_fname', $secondary_fname);   
	        update_post_meta( $tenant_id, 'secondary_lname', $secondary_lname);   
	        update_post_meta( $tenant_id, 'secondary_title', $secondary_title);   
	        update_post_meta( $tenant_id, 'secondary_primary_phone', $secondary_primary_phone);   
	        update_post_meta( $tenant_id, 'secondary_primary_phone_type', $secondary_primary_phone_type);   
	        update_post_meta( $tenant_id, 'secondary_primary_phone_type_custom', $secondary_primary_phone_type_custom);   
	        update_post_meta( $tenant_id, 'secondary_phone', $secondary_phone);   
	        update_post_meta( $tenant_id, 'secondary_phone_type', $secondary_phone_type);   
	        update_post_meta( $tenant_id, 'secondary_phone_type_custom', $secondary_phone_type_custom);   
	        update_post_meta( $tenant_id, 'secondary_contact_email', $secondary_contact_email);  
	        update_post_meta( $tenant_id, 'property', $property); 
	        //extra 
	        update_post_meta( $tenant_id, 'notes', $notes);   
	        update_post_meta( $tenant_id, 'photos', $photos);   
	        update_post_meta( $tenant_id, 'photos_details', $photos); 

            return new WP_REST_Response(

					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Successfull updated', 'jwt-auth' ),
						'data'       => array(),
					),
					
			); 
        endif;
		

        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid Tenant Id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
		);
		

 	}

 	public function tenant_api_create_new_tenant(WP_REST_Request $request)
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
 		$login_access    = $request->get_param( 'login_access' );
 		$company_flag    = $request->get_param( 'company_flag' ); 
 		$author    = $request->get_param( 'author' ); 
 		$address    = $request->get_param( 'street_address' );
 		$street_address_2    = $request->get_param( 'street_address_2' ); 
 		$unit    = $request->get_param( 'unit' );
 		$unit_type    = $request->get_param( 'unit_type' ); 
 		$state    = $request->get_param( 'state' ); 
 		$city    = $request->get_param( 'city' ); 
 		$zip_code    = $request->get_param( 'zip_code' );

 		$phone_number    = $request->get_param( 'phone_number' ); 
 		$phone_number_type    = $request->get_param( 'phone_number_type' ); 
 		$phone_number_type_custom    = $request->get_param( 'phone_number_type_custom' ); 
 		$primary_email    = $request->get_param( 'primary_email' );

 		$status    = $request->get_param( 'status' ); 
 		$complex    = $request->get_param( 'complex' );
 		$mailbox    = $request->get_param( 'mailbox' );
 		//primary info
 		$primary_fname    = $request->get_param( 'primary_fname' );
 		$primary_lname    = $request->get_param( 'primary_lname' );
 		$primary_title    = $request->get_param( 'primary_title' ); 

 		$primary_phone    = $request->get_param( 'primary_phone' );
 		$primary_phone_type    = $request->get_param( 'primary_phone_type' );
 		$primary_phone_type_custom    = $request->get_param( 'primary_phone_type_custom' );

 		$primary_second_phone    = $request->get_param( 'primary_second_phone' );
 		$primary_second_phone_type    = $request->get_param( 'primary_second_phone_type' );
 		$primary_second_phone_type_custom    = $request->get_param( 'primary_second_phone_type_custom' ); 
 		$primary_contact_email    = $request->get_param( 'primary_contact_email' );
 		

 		//secondary info  
 		$secondary_fname    = $request->get_param( 'secondary_fname' );
 		$secondary_lname    = $request->get_param( 'secondary_lname' );
 		$secondary_title    = $request->get_param( 'secondary_title' ); 

 		$secondary_primary_phone    = $request->get_param( 'secondary_primary_phone' );
 		$secondary_primary_phone_type    = $request->get_param( 'secondary_primary_phone_type' );
 		$secondary_primary_phone_type_custom    = $request->get_param( 'secondary_primary_phone_type_custom' );

 		$secondary_phone    = $request->get_param( 'secondary_phone' );
 		$secondary_phone_type    = $request->get_param( 'secondary_phone_type' );
 		$secondary_phone_type_custom    = $request->get_param( 'secondary_phone_type_custom' );

 		$secondary_contact_email    = $request->get_param( 'secondary_contact_email' );
 		$property    = $request->get_param( 'property' );
 		//notes
 		$notes    = $request->get_param( 'notes' ); 

 		//photos
 		// $photos    = $request->get_param( 'photos' );
 		// $photos_details    = $request->get_param( 'photos_details' );
 		$photos = array();
 		$photos_details = array();
  

		$errors_arr = array();

		// if(empty($primary_email)){ 

		// 	$errors_arr[] = __( 'Please enter primary email address.', 'jwt-auth' ); 
		// } 

		// if(empty($phone_number)){

 	// 		$errors_arr[] = __( 'Please enter phone number.', 'jwt-auth' ); 
		// }

		if(empty($company_name)){

 			$errors_arr[] = __( 'Please enter company name.', 'jwt-auth' ); 
		}

		// if(empty($address)){

 	// 		$errors_arr[] = __( 'Please enter address.', 'jwt-auth' ); 
		// } 

		// if(empty($unit)){

 	// 		$errors_arr[] = __( 'Please enter unit.', 'jwt-auth' ); 
		// }

		// if(empty($city)){

 	// 		$errors_arr[] = __( 'Please enter city.', 'jwt-auth' ); 
		// }

		// if(empty($state)){

 	// 		$errors_arr[] = __( 'Please enter state.', 'jwt-auth' ); 
		// }

		// if(empty($zip_code)){

 	// 		$errors_arr[] = __( 'Please enter zip code.', 'jwt-auth' ); 
		// }

		// if(empty($status)){

 	// 		$errors_arr[] = __( 'Please enter status.', 'jwt-auth' ); 
		// }

		// if(empty($complex)){

 	// 		$errors_arr[] = __( 'Please enter complex.', 'jwt-auth' ); 
		// }
		// if(empty($author)){

 	// 		$errors_arr[] = __( 'You are invalid user.', 'jwt-auth' ); 
		// }

		// if(empty($title)){

 	// 		$errors_arr[] = __( 'Please enter title.', 'jwt-auth' ); 
		// }

		// if(empty($primary_fname)){

 	// 		$errors_arr[] = __( 'Please enter primary first name.', 'jwt-auth' ); 
		// } 

		// if(empty($primary_lname)){

 	// 		$errors_arr[] = __( 'Please enter primary last name.', 'jwt-auth' ); 
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
 

		// if ( email_exists( $primary_email ) ) { 

		// 	return new WP_REST_Response(
		// 		array(
		// 			'success'    => false,
		// 			'statusCode' => 403,
		// 			'code'       => 'error',
		// 			'message'    => __( 'This email is already exist.', 'jwt-auth' ),
		// 			'data'       => array(),
		// 		),
		// 		403
		// 	); 
	 //    }

	     

		// $userReg=wp_insert_user(array(
		// 	'user_login'     => $primary_email,
		// 	'user_email'     => $primary_email,
		// 	'user_pass'      => $password,
		// 	'user_nicename'  => $primary_fname,
		// 	'display_name'   => $primary_fname.' '.$primary_lname,
		// 	'nickname'       => $primary_fname,
		// 	'first_name'     => $primary_fname, 
		// ));
		// $post_title = $primary_fname.' '.$primary_lname;
		// if( trim( $post_title ) == '' ){
			$post_title = $company_name;
		// }
		$userReg = wp_insert_post(array(
			'post_title'     => $post_title,
			'post_type'     => 'tenants',
			'post_status'   => 'publish',
			'post_author'   => $post_author,
			 
		));
		if( isset( $userReg ) && is_numeric( $userReg ) ){

			// $u = new WP_User($userReg);
	  //       $u->remove_role('subscriber');
	  //       $u->remove_role('customer');	         
   //      	$u->add_role('tenant');
			update_user_meta($post_author, 'any_tenant_created', 'yes');
        	update_post_meta($userReg, 'user_type', 'tenant');
	        update_post_meta($userReg, 'login_access', $login_access);
	        update_post_meta($userReg, 'first_name', $primary_fname);
	        update_post_meta($userReg, 'last_name', $primary_lname); 
	        update_post_meta($userReg, 'primary_title', $primary_title);   

	        //contect info
	        update_post_meta($userReg, 'company_name', $company_name);
	        update_post_meta($userReg, 'company_flag', $company_flag);  
	        update_post_meta($userReg, 'street_address', $address); 
	        update_post_meta($userReg, 'street_address_2', $street_address_2); 
	        update_post_meta($userReg, 'unit', $unit);
	        update_post_meta($userReg, 'unit_type', $unit_type);  
	        update_post_meta($userReg, 'city', $city);  
	        update_post_meta($userReg, 'state', $state);  
	        update_post_meta($userReg, 'zip_code', $zip_code); 
	        update_post_meta($userReg, 'phone_number', $phone_number);  
	        update_post_meta($userReg, 'phone_number_type', $phone_number_type);  
	        update_post_meta($userReg, 'phone_number_type_custom', $phone_number_type_custom);  
	        update_post_meta($userReg, 'primary_email', $primary_email);  
	        update_post_meta($userReg, 'status', $status);  
	        update_post_meta($userReg, 'complex', $complex);  
	        update_post_meta($userReg, 'mailbox', $mailbox);
 
	 		//primary info
	        update_post_meta($userReg, 'primary_fname', $primary_fname);
	        update_post_meta($userReg, 'primary_lname', $primary_lname); 
	        update_post_meta($userReg, 'primary_title', $primary_title); 
	        update_post_meta($userReg, 'primary_phone', $primary_phone); 
	        update_post_meta($userReg, 'primary_phone_type', $primary_phone_type); 
	        update_post_meta($userReg, 'primary_phone_type_custom', $primary_phone_type_custom); 

	        update_post_meta($userReg, 'primary_second_phone', $primary_second_phone); 
	        update_post_meta($userReg, 'primary_second_phone_type', $primary_second_phone_type);  
	        update_post_meta($userReg, 'primary_second_phone_type_custom', $primary_second_phone_type_custom);  
	        update_post_meta($userReg, 'primary_contact_email', $primary_contact_email); 

	         //secondary info  
	        update_post_meta($userReg, 'secondary_fname', $secondary_fname);   
	        update_post_meta($userReg, 'secondary_lname', $secondary_lname);   
	        update_post_meta($userReg, 'secondary_title', $secondary_title);   
	        update_post_meta($userReg, 'secondary_primary_phone', $secondary_primary_phone);   
	        update_post_meta($userReg, 'secondary_primary_phone_type', $secondary_primary_phone_type);   
	        update_post_meta($userReg, 'secondary_primary_phone_type_custom', $secondary_primary_phone_type_custom);   
	        update_post_meta($userReg, 'secondary_phone', $secondary_phone);   
	        update_post_meta($userReg, 'secondary_phone_type', $secondary_phone_type);   
	        update_post_meta($userReg, 'secondary_phone_type_custom', $secondary_phone_type_custom);   
	        update_post_meta($userReg, 'secondary_contact_email', $secondary_contact_email);  
	        update_post_meta($userReg, 'property', $property);
	        //extra 
	        update_post_meta($userReg, 'notes', $notes);   
	        update_post_meta($userReg, 'photos', $photos);   
	        update_post_meta($userReg, 'photos_details', $photos_details);   
 		   


	        return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Sucessfull create user', 'jwt-auth' ),
					'data'       => array('tenant_id' => $userReg),
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

 	public function delete_tenant_by_id(WP_REST_Request $request){
 		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
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
 		$API_PhotosController = new API_PhotosController();
 		//contect info
 		$tenent_ids    = $request->get_param( 'tenent_ids' ); 
 		
 		if( empty( $tenent_ids) || gettype($tenent_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one tenant id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$total_delete_tenants = 0;
 		$API_ContactsController = new API_ContactsController();
		 	
 		foreach( $tenent_ids as $tenant_id ){
 			if( get_post_type($tenant_id) != "tenants"):
 				continue;
 			endif;
 			if(wp_delete_post($tenant_id, true)):
 				// delete attachment/photos
 				$attachment_args = array(
				    'post_type' => "attachment",
				    'post_parent' => $tenant_id,
				    'posts_per_page' => -1,
				);
				$attachments = get_posts($attachment_args);
				foreach( $attachments  as $attachment):
					$API_PhotosController->ballon_delete_media_from_s3($attachment->guid);
				    wp_delete_attachment($attachment->ID, true);
				endforeach;
 				$total_delete_tenants++;
 				// delete contacts
 				$API_ContactsController->delete_all_notes_by_user_post_id( $tenant_id, "post" );
 			endif;
 		}
 		if( $total_delete_tenants > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Tenant successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_deleted_tenants' => $total_delete_tenants),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any tenant', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

    public function search_tenants(WP_REST_Request $request){
    	$API_BaseController = new API_BaseController();
    	$API_ContactsController = new API_ContactsController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		
 		$any_tenant_created = get_user_meta($post_author, 'any_tenant_created', true); 
    	$search_by_keyword    = $request->get_param( 'search_by_keyword' );
    	$paged = ($request->get_param('paged')) ? $request->get_param('paged') : 1;
    	$posts_per_page = ($request->get_param('posts_per_page')) ? $request->get_param('posts_per_page') : 10;
    	$sort_by_field    = $request->get_param( 'sort_by_field' );
    	$orderby = "date";
    	$order = "DESC";

    	if( 'a-z' == $sort_by_field ):
    		$orderby = "title";
    		$order = "ASC";
    	endif;
    	if( 'z-a' == $sort_by_field ):
    		$orderby = "title";
    		$order = "DESC";
    	endif;

    	// var_dump( $search_by_keyword );
    	$total_tenants = 0;
    	 $args = array(
    	 	'author' => $post_author,  
	        'post_type' => 'tenants',
	        'post_status' => 'publish',
	        'paged' => $paged, 
	        'orderby' => $orderby,
			'order'   => $order,
	        'posts_per_page' => $posts_per_page, 
	        
	    );
    	if( 'status' == $sort_by_field ):
    	 	$args['meta_key'] = "status";
    		$args['orderby'] = "meta_value";
    		$args['order'] = "ASC";
    	endif;
    	if( 'unit' == $sort_by_field ):
    	 	$args['meta_key'] = "unit";
    		$args['orderby'] = "meta_value_num";
    		$args['order'] = "ASC";
    	endif;
    	if( 'company_name' == $sort_by_field ):
    	 	$args['meta_key'] = "company_name";
    		$args['orderby'] = "meta_value";
    		$args['order'] = "ASC";
    	endif;
    	if( '' != trim( $search_by_keyword ) ):
    		$args['meta_query'] = array(
    				'relation' => 'AND',
				        array(
				            'value' => $search_by_keyword,
				            'compare' => "LIKE"
				        )
				    );
    	endif;
   //  	foreach( $search_by_fields as $search_by_field ){
 		// 	if( '' == $search_by_field['key'] || '' == $search_by_field['value'] ):
 		// 		continue;
 		// 	endif;
 			 
 		// }
    	$tenants = array();
    	$the_query = new WP_Query( $args );
    	$total_tenants = $the_query->found_posts;
	        
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$tenant_id = get_the_ID();
	        $tenant_info = get_post( $tenant_id );

	        if ( empty($tenant_info) ) :
	        	continue;
	        endif;
	        
	        $tenant_info->tenant_created = get_post_meta($tenant_id, 'first_name', true);
	        $tenant_info->login_access = get_post_meta($tenant_id, 'login_access', true);
	        $tenant_info->first_name = get_post_meta($tenant_id, 'first_name', true);
	        $tenant_info->last_name = get_post_meta($tenant_id, 'last_name', true); 

	        $tenant_info->company_name = get_post_meta($tenant_id, 'company_name', true);
	        $tenant_info->company_flag = get_post_meta($tenant_id, 'company_flag', true); 
	        $tenant_info->street_address = get_post_meta($tenant_id, 'street_address', true);
	        $tenant_info->street_address_2 = get_post_meta($tenant_id, 'street_address_2', true); 
	        $tenant_info->unit = get_post_meta($tenant_id, 'unit', true);
	        $tenant_info->unit_type = get_post_meta($tenant_id, 'unit_type', true); 
	        $tenant_info->city = get_post_meta($tenant_id, 'city', true); 
	        $tenant_info->state = get_post_meta($tenant_id, 'state', true); 
	        $tenant_info->zip_code = get_post_meta($tenant_id, 'zip_code', true); 
	        $tenant_info->status = get_post_meta($tenant_id, 'status', true); 
	        $tenant_info->complex = get_post_meta($tenant_id, 'complex', true); 
 			$tenant_info->mailbox = get_post_meta($tenant_id, 'mailbox', true); 

	        $tenant_info->phone_number = get_post_meta($tenant_id, 'phone_number', true); 
	        $tenant_info->phone_number_type = get_post_meta($tenant_id, 'phone_number_type', true); 
	        $tenant_info->primary_email = get_post_meta($tenant_id, 'primary_email', true); 

	        	//primary info
	        $tenant_info->primary_fname = get_post_meta($tenant_id, 'primary_fname', true); 
	        $tenant_info->primary_lname = get_post_meta($tenant_id, 'primary_lname', true); 
	        $tenant_info->primary_title = get_post_meta($tenant_id, 'primary_title', true); 
	        $tenant_info->primary_phone = get_post_meta($tenant_id, 'primary_phone', true); 
	        $tenant_info->primary_phone_type = get_post_meta($tenant_id, 'primary_phone_type', true); 
	        $tenant_info->primary_second_phone = get_post_meta($tenant_id, 'primary_second_phone', true); 
	        $tenant_info->primary_second_phone_type = get_post_meta($tenant_id, 'primary_second_phone_type', true); 
	        $tenant_info->primary_contact_email = get_post_meta($tenant_id, 'primary_contact_email', true); 

	         	//secondary info  
	        $tenant_info->secondary_fname = get_post_meta($tenant_id, 'secondary_fname', true); 
	        $tenant_info->secondary_lname = get_post_meta($tenant_id, 'secondary_lname', true); 
	        $tenant_info->secondary_title = get_post_meta($tenant_id, 'secondary_title', true); 
	        $tenant_info->secondary_primary_phone = get_post_meta($tenant_id, 'secondary_primary_phone', true); 
	        $tenant_info->secondary_primary_phone_type = get_post_meta($tenant_id, 'secondary_primary_phone_type', true); 
	        $tenant_info->secondary_phone = get_post_meta($tenant_id, 'secondary_phone', true); 
	        $tenant_info->secondary_phone_type = get_post_meta($tenant_id, 'secondary_phone_type', true); 
	        $tenant_info->secondary_contact_email = get_post_meta($tenant_id, 'secondary_contact_email', true); 
	        $tenant_info->property = get_post_meta($tenant_id, 'property', true); 	
	        $tenant_info->notes = get_post_meta($tenant_id, 'notes', true); 
	        $tenant_info->photos = get_post_meta($tenant_id, 'photos', true); 
	        $tenant_info->photos_details = get_post_meta($tenant_id, 'photos_details', true); 
    		$tenant_info->contacts = $API_ContactsController->get_contacts( $tenant_id, "post" );
	       array_push( $tenants, $tenant_info);
	    endwhile;

    // wp_reset_postdata(); 
    	// var_dump( $search_by_field );
    	return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Tenant successfully deleted.', 'jwt-auth' ),
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'total_tenants' => $total_tenants,
					'data'       => $tenants,
					'any_record_created' => $any_tenant_created,
				),
				
			); 
    }

    public function tenant_conpany_flag( WP_REST_Request $request ){
    	$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
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
    	$tenant_id    = $request->get_param( 'tenant_id' );
 		$company_flag    = $request->get_param( 'company_flag' );
 		$errors_arr = array();

		if(empty($tenant_id)){

 			$errors_arr[] = __( 'Please enter tenant id', 'jwt-auth' ); 
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
	    if( 'tenants' != get_post_type($tenant_id) ){
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter valid tenant ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
	    update_post_meta( $tenant_id, 'company_flag', $company_flag);
	    return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Company detail successfully updated.', 'jwt-auth' ),
						'data'       => array(),
					),
					 
				);
    }


    public function create_tenant_password(WP_REST_Request $request){
    	// $API_BaseController = new API_BaseController();
    	// $API_ContactsController = new API_ContactsController();
 		// $post_author = $API_BaseController->custom_validate_token($_SERVER);
 		// $any_tenant_created = get_user_meta($post_author, 'any_tenant_created', true); 
    	$tenant_email    = $request->get_param( 'tenant_email' );
    	$tenant_email = trim( $tenant_email );
    	$tenant_email = sanitize_text_field( $tenant_email );
    	$errors_arr = array();

		if(empty($tenant_email)){ 

			$errors_arr[] = __( 'Please enter  email address.', 'jwt-auth' ); 
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
	    $user_id = email_exists( $tenant_email );
    	if( $user_id ){
    		// means user already created
    		// check role
    		$user = get_userdata( $user_id );
    		$user_roles = $user->roles;
    		
    		if ( in_array( 'tenant', $user_roles ) ) {
			    // Do something.
			    // echo 'YES, User is a tenant';
			    $tenant_post_id = get_user_meta( $user_id, '_tenant_post_id', true );
			    $tenant_user_id = get_post_meta( $tenant_post_id, '_tenant_user_id', true );
			    $login_access = get_post_meta( $tenant_post_id, 'login_access', true );
			   
			    if( ( '1' == $login_access) && ($tenant_user_id == $user_id ) && ( 'tenants' == get_post_type($tenant_post_id) ) ){
			    	$rand_pass = rand('123456', '987654'); 
			    	wp_update_user( array('ID' => $user_id, 'user_pass' => $rand_pass) );
			    	update_user_meta( $user_id, '_password_use_limit', '1');
			    	$mail_status = $this->cloudemail_send_create_passowrd_email( $tenant_post_id, $tenant_user_id, $rand_pass );
			    	if( $mail_status ){
			    		return new WP_REST_Response(
							array(
								'success'    => true,
								'statusCode' => 200,
								'code'       => 'success',
								'message'    => __( 'Password send on your email', 'jwt-auth' ),
								'data'       => array('tenant_user_id' => $tenant_user_id),
							),
							
						); 
			    	}else{
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
			    }
			}
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invaild email for the tenant login', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
				
			); 
    	}
	    

		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'This email id is not exist', 'jwt-auth' ),
				'data'       => $errors_arr,
			),
			
		); 
	 
	     $tenants = get_posts( array(
            'fields'         => 'ids',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'post_type'      => array('tenants'),
            'orderby'        => 'date',
            'order'          => 'ASC',
            'meta_query'      => array(
            	'relation' => 'AND',
            	array(
            		'key' => 'primary_email',
                	'value' => $tenant_email,
				    'compare' => "="
              
            ))
        ));
	    if( !empty( $tenants ) ){
	    	$id = $tenants[0];
	    	$rand_pass = rand('123456', '987654'); 
	    	update_post_meta( $id, "create_password", $rand_pass );
	    	update_post_meta($id, 'tenant_access_token', '');
	    	$tenant_user_id = get_post_meta( $id, '_tenant_user_id', true );
	    	
	    	$mail_status = $this->cloudemail_send_create_passowrd_email( $id, $rand_pass );
	    	if( $mail_status ){
	    		return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Password send on your email', 'jwt-auth' ),
						'data'       => array('tenant_id' => $id),
					),
					
				); 
	    	}else{
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
	    	
	    }
	    return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid Tenant Id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
		);
		
     
    }

    /**
	 * Send email from cloudemail 
	 */
	public function cloudemail_send_create_passowrd_email( $tenant_post_id, $tenant_user_id, $password ){
		 
		$my_app_url =  get_option( 'my_app_url' );
		$admin_email =  get_option( 'admin_email' );
		$from = "charles@getaccountableapp.com";
		
		$company_name = get_post_meta( $tenant_post_id, 'company_name', true );
        $user = get_user_by('ID', $tenant_user_id );
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        if( '' != trim( $first_name.' '. $last_name ) ){
        	$company_name = trim( $first_name.' '. $last_name );
        }				
		$to = $user->user_email;
		$to = trim( $to );
	 
		$site_title = get_bloginfo( 'name' );
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
	 

		$subject = "New generated password";
		$property_name_string = '';
		if( $property_name != '' ){
			$subject = $property_name.' '. $project_info->post_title;
			$property_name_string = "<span>". $property_name."</span><br/>";
		}
		
		
		$message = "<p>Hi ".$company_name.",</p>
		<br/> 
		<p>Your generated password is : <strong>".$password."</strong></p>
		<br/>
		<br/>
		<p>Thanks<br/><span>". $site_title."</span></p>";
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = 'smtp.cloudmta.net';                     //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = 'c1d6dea731b2f948';                     //SMTP username
		    $mail->Password   = 'AcALJEqLt26qo8TU1XeoTo3k';                               //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $to);     //Add a recipient
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

	public function validate_tenant_password( WP_REST_Request $request ){
		$tenant_user_id    = $request->get_param( 'tenant_id' );
		$password    = $request->get_param( 'password' );
		$tenant_user_id = trim( $tenant_user_id );
    	$password = trim( $password );
    	$errors_arr = array();
    	if ( ( empty($tenant_user_id) ) || ( 'tenants' != get_post_type($tenant_user_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter tenant ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
		if(empty($tenant_user_id)){ 
			$errors_arr[] = __( 'Please enter tenant id.', 'jwt-auth' ); 
		}
		if(empty($password)){ 
			$errors_arr[] = __( 'Please enter password.', 'jwt-auth' ); 
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
 
	    	
	    $get_pass = get_post_meta( $tenant_user_id, 'create_password', true );
	    if( $get_pass == $password ){
	    	$string = $tenant_id.'_'.$password;
	    	// var_dump( $string);
	    	$md = md5($string);
	    	// var_dump( $md );
	    	update_post_meta($tenant_id, 'tenant_access_token', $md );

	    	return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Loggin successfully', 'jwt-auth' ),
						'data'       => array('tenant_id' => $tenant_id, 'access_token' => $md ),
					),
					
				); 

	    }
	    return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Password is not matching.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	}

	public function enable_disable_login_access(WP_REST_Request $request)
 	{ 
 		 
 		$tenant_id    = $request->get_param( 'tenantId' );
 		$login_access    = $request->get_param( 'login_access' );
		if(empty($tenant_id)){

 			$errors_arr[] = __( 'Please enter tenant id', 'jwt-auth' ); 
		}
		if(empty($login_access)){

 			$errors_arr[] = __( 'Please enter access field', 'jwt-auth' ); 
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
	    if( 'tenants' != get_post_type($tenant_id) ){
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter valid tenant ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
	   update_post_meta( $tenant_id, 'login_access', $login_access );
	    if( '1' != $login_access ){

	    	return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Access Disabled successfully.', 'jwt-auth' ),
						'data'       => array( ),
					)
					 
				);
	    }

	    $API_UserControler = new API_UserControler();
	    $user_response = $API_UserControler->create_user_for_tenant( $tenant_id );
	    if( isset($user_response['status']) ) {

	    	if($user_response['status']){
	    		return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( $user_response['message'], 'jwt-auth' ),
						'data'       => array( 'user_id' => $user_response['user_id']),
					)
					 
				);
	    	}else{
	    		return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'error',
						'message'    => __( $user_response['message'], 'jwt-auth' ),
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
					'message'    => __( 'Invalid tentat id', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 	}

	/**
	 *  From the tenant account tenant will get shared files 
	 */ 				        
 	public function tenant_get_shared_files(WP_REST_Request $request){
 
 		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		$tenant_id  = $request->get_param( 'tenant_id' );
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
 		$shared_attachment_args = array(
			    'post_type' => "attachment",
			    'posts_per_page' => -1,
			    'meta_query' => array(
			    	'relation' => 'AND',
			        array(
			            'key'       => 'shared_to',
			            'value'     => $tenant_id,
			            'compare' => '='
			        ),array(
			            'key'       => 'shared_for',
			            'value'     => 'tenant',
			            'compare' => '='
			        )
			    )
			);
 			// echo "<pre>";
 			// print_r( $shared_attachment_args );
 			// echo "</pre>";
	        $shared_files = array();
			$shared_attachments = get_posts($shared_attachment_args);
			foreach( $shared_attachments  as $attachment):
				$temp = array();
				$temp = (object) $temp;
				$temp->photo_id = $attachment->ID;
				$temp->real_file_name = get_post_meta( $attachment->ID, 'real_file_name', true );
				$temp->photo_detail = $attachment->post_content;
				$temp->photo_src = $attachment->guid;
				$temp->photo_created = $attachment->post_date; 
				array_push( $shared_files,  $temp );
			endforeach;
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Shared Private Files', 'jwt-auth' ),
						'data'       => $shared_files,
					),
					 
				);
 	}
}

new API_TenantsControler();
?>
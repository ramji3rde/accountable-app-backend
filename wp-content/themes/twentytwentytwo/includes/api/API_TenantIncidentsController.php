<?php

class API_TenantIncidentsController extends API_BaseController {

	public function __construct() 
	{  

 	}
 	 
 	public function get_single_incident(WP_REST_Request $request)
 	{
 		$incident_id    = $request->get_param( 'incidentId' );  

 		if ( ( empty($incident_id) ) || ( 'incident' != get_post_type($incident_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter incident ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

 
		$incident_info = get_post( $incident_id );

		if ( empty($incident_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This incident is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{

	    	 

	    	$incident_info->date = get_post_meta($incident_id, 'date', true);
	        $incident_info->status = get_post_meta($incident_id, 'status', true); 
	        $incident_info->property = get_post_meta($incident_id, 'property', true);
	        $tenant_ids = get_post_meta($incident_id, 'tenant_ids', true);
	        $tenants = array();
	        foreach( $tenant_ids as $tenant_id):
	        	if( 'tenants' != get_post_type( $tenant_id) ){
	        		continue;
	        	}
	        	$temp = array();
    		    $temp = (object) $temp;
    			$temp->ID =$tenant_id;
    			$temp->primary_fname = get_post_meta($tenant_id, 'primary_fname', true);
    			$temp->primary_lname = get_post_meta($tenant_id, 'primary_lname', true); 
    			$temp->primary_title = get_post_meta($tenant_id, 'primary_title', true); 
    			$temp->company_name = get_post_meta($tenant_id, 'company_name', true); 
    			$temp->unit = get_post_meta($tenant_id, 'unit', true); 
    			$temp->unit_type = get_post_meta($tenant_id, 'unit_type', true); 
    			$temp->complex = get_post_meta($tenant_id, 'complex', true); 
    			$temp->status = get_post_meta($tenant_id, 'status', true); 
    			$temp->primary_phone_type = get_post_meta($tenant_id, 'primary_phone_type', true); 
    			$temp->primary_phone = get_post_meta($tenant_id, 'primary_phone', true); 
    			$temp->primary_contact_email = get_post_meta($tenant_id, 'primary_contact_email', true); 
    			$temp->city = get_post_meta($tenant_id, 'city', true); 
		        array_push( $tenants, $temp); 
	        endforeach;
	        $contractor_ids = get_post_meta($incident_id, 'contractor_ids', true);
	        if( 'array' != gettype( $contractor_ids ) ){
	        	$contractor_ids = array();
	        }
	        $contractors = array();
	        foreach( $contractor_ids as $contractor_id):
	        	$contractor_info = get_user_by( 'ID', $contractor_id );
	        	if( !isset( $contractor_info->ID ) ):
	        		continue;
	        	endif;

	 	    	$temp_contr = array();
    		    $temp_contr = (object) $temp_contr;
    			$temp_contr->ID = $contractor_info->ID;
    			$temp_contr->user_login = $contractor_info->user_login;
    			$temp_contr->user_nicename = $contractor_info->user_nicename;
    			$temp_contr->user_email = $contractor_info->user_email;
    			$temp_contr->primary_fname = get_post_meta($contractor_id, 'primary_fname', true);
    			$temp_contr->primary_lname = get_post_meta($contractor_id, 'primary_lname', true); 
    			$temp_contr->primary_title = get_post_meta($contractor_id, 'primary_title', true); 
    			$temp_contr->company_name = get_post_meta($contractor_id, 'company_name', true); 
    			$temp_contr->primary_phone_type = get_post_meta($contractor_id, 'primary_phone_type', true); 
    			$temp_contr->primary_phone = get_post_meta($contractor_id, 'primary_phone', true); 
    			$temp_contr->primary_contact_email = get_post_meta($contractor_id, 'primary_contact_email', true); 
    			$temp_contr->city = get_post_meta($contractor_id, 'city', true); 

		        array_push( $contractors, $temp_contr); 
	        endforeach;
	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $incident_id,
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
			$incident_info->tenants = $tenants;
			$incident_info->contractors = $contractors;
			
			$incident_info->photos = $photos;
		 
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Incident Details', 'jwt-auth' ),
						'data'       => $incident_info,
					),
					 
				);
		}

 	}

 	public function update_incident(WP_REST_Request $request)
 	{ 
 		 
 		$incident_id    = $request->get_param( 'incidentId' ); 
 		//contect info
 		$title    = $request->get_param( 'title' );
 		$date    = $request->get_param( 'date' ); 
 		$detail    = $request->get_param( 'detail' ); 
 		$status    = $request->get_param( 'status' ); 
 		$property    = $request->get_param( 'property' );

 		$tenant_ids    = $request->get_param( 'tenant_ids' ); // array 
 		$contractor_ids    = $request->get_param( 'contractor_ids' ); // array 
 		 
 		if( ( empty( $incident_id ) ) || "incident" != get_post_type( $incident_id ) ) { 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'incident id is not exist.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
  
		} 
  

		$errors_arr = array();

		if(empty($title)){

 			$errors_arr[] = __( 'Please enter short title.', 'jwt-auth' ); 
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
	    if( 'array' != gettype( $tenant_ids ) ):
       		$tenant_ids = array();
       	endif;
       	if( 'array' != gettype( $contractor_ids ) ):
       		$contractor_ids = array();
       	endif;
	    $update_args = array();
	    $update_args['ID'] = $incident_id;
		$update_args['post_title'] = $title; //  Title
		$update_args['post_content'] = $detail; // Description
 		if(wp_update_post( $update_args )):
			update_post_meta($incident_id, 'date', $date);
	        update_post_meta($incident_id, 'status', $status);
	        
	        update_post_meta($incident_id, 'property', $property); 
	        update_post_meta($incident_id, 'tenant_ids', array_unique($tenant_ids));   
	        update_post_meta($incident_id, 'contractor_ids', array_unique($contractor_ids)); 
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
					'message'    => __( 'Invalid incident Id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
		);
		

 	}
 	/**
 	 * create a new incident 
 	 * 
 	 */ 
 	public function create_new_incident(WP_REST_Request $request)
 	{
 		//contect info
 		$title    = $request->get_param( 'title' );
 		$date    = $request->get_param( 'date' ); 
 		$detail    = $request->get_param( 'detail' ); 
 		$status    = $request->get_param( 'status' ); 
 		$property    = $request->get_param( 'property' );
 		$tenant_ids    = $request->get_param( 'tenant_ids' ); // array
 		$contractor_ids    = $request->get_param( 'contractor_ids' ); // array
 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$errors_arr = array();
		if(empty($title)){

 			$errors_arr[] = __( 'Please enter incident short title.', 'jwt-auth' ); 
		}
       	if( 'array' != gettype( $tenant_ids ) ):
       		$tenant_ids = array();
       	endif;
       	if( 'array' != gettype( $contractor_ids ) ):
       		$contractor_ids = array();
       	endif;
       	array_push( $tenant_ids, $post_author);
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
 
  
		$IncidentReg = wp_insert_post(array(
			'post_title'     => $title,
			'post_content'  => $detail,
			'post_type'     => 'incident',
			'post_status'   => 'publish',
			'post_author'   => $post_author,
			 
		));
		if( isset( $IncidentReg ) && is_numeric( $IncidentReg ) ){
			update_user_meta($post_author, 'any_incident_created', 'yes');
			update_post_meta($IncidentReg, 'real_post_author', $post_author);
        	update_post_meta($IncidentReg, 'date', $date);
	        update_post_meta($IncidentReg, 'status', $status);
	        update_post_meta($IncidentReg, 'property', $property); 
	        update_post_meta($IncidentReg, 'tenant_ids', array_unique($tenant_ids));   
	        update_post_meta($IncidentReg, 'contractor_ids', array_unique($contractor_ids)); 
	        return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Incident successfully created.', 'jwt-auth' ),
					'data'       => array('incident_id' => $IncidentReg),
				),
				
			); 

		} else {
			$massage ='';
			foreach ($IncidentReg->errors as $key => $errors) {
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
 	 * Delete multiple incidents by id 
 	 * 
 	 */
 	public function delete_incident(WP_REST_Request $request){
 		//contect info
 		$incident_ids    = $request->get_param( 'incident_ids' ); 
 		$API_PhotosController = new API_PhotosController();
 		if( empty( $incident_ids) || gettype($incident_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one incident id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$total_delete_incidents = 0;
 		foreach( $incident_ids as $incident_id ){
 			if( get_post_type($incident_id) != "incident"):
 				continue;
 			endif;
 			if(wp_delete_post($incident_id, true)):
 				// delete attachment/photos
 				$attachment_args = array(
				    'post_type' => "attachment",
				    'post_parent' => $incident_id,
				    'posts_per_page' => -1,
				);
				$attachments = get_posts($attachment_args);
				foreach( $attachments  as $attachment):
					$API_PhotosController->ballon_delete_media_from_s3($attachment->guid);
				    wp_delete_attachment($attachment->ID, true);
				endforeach;
 				$total_delete_incidents++;
 			endif;
 		}
 		if( $total_delete_incidents > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Incidents successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_deleted_incidents' => $total_delete_incidents),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any incident', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

	/**
	 * Search or get all incidents with pagination
	 * 
	 */ 	
    public function search_incidents(WP_REST_Request $request){
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
    	$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
    	 
    	$total_incidents = 0;
    	 $args = array(  
    	 	'author' => $post_author,
	        'post_type' => 'incident',
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
    	if( 'date' == $sort_by_field ):
    	 	$args['meta_key'] = "date";
    		$args['orderby'] = "meta_value_num";
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
   		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_incident_created = get_user_meta($post_author, 'any_incident_created', true); 
    	$incidents = array();
    	$the_query = new WP_Query( $args );
    	$total_incidents = $the_query->found_posts;
	        
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$incident_id = get_the_ID();
	        $incident_info = get_post( $incident_id );

	        if ( empty($incident_info) ) :
	        	continue;
	        endif;
	        
	        $incident_info->date = get_post_meta($incident_id, 'date', true);
	        $incident_info->status = get_post_meta($incident_id, 'status', true); 
	        $incident_info->property = get_post_meta($incident_id, 'property', true);
	        // $incident_info->tenant_ids = get_post_meta($incident_id, 'tenant_ids', true);
	        $tenant_ids = get_post_meta($incident_id, 'tenant_ids', true);
	        $tenants = array();
	        foreach( $tenant_ids as $tenant_id):
	        	if( 'tenants' != get_post_type( $tenant_id) ){
	        		continue;
	        	}
	        	$temp_tenant = array();
    		    $temp_tenant = (object) $temp_tenant;
    			$temp_tenant->ID =$tenant_id;
    			$temp_tenant->primary_fname = get_post_meta($tenant_id, 'primary_fname', true);
    			$temp_tenant->primary_lname = get_post_meta($tenant_id, 'primary_lname', true); 
    			$temp_tenant->primary_title = get_post_meta($tenant_id, 'primary_title', true); 
    			$temp_tenant->company_name = get_post_meta($tenant_id, 'company_name', true); 
    			$temp_tenant->unit = get_post_meta($tenant_id, 'unit', true); 
    			$temp_tenant->unit_type = get_post_meta($tenant_id, 'unit_type', true); 
    			$temp_tenant->complex = get_post_meta($tenant_id, 'complex', true); 
    			$temp_tenant->status = get_post_meta($tenant_id, 'status', true); 
    			$temp_tenant->primary_phone_type = get_post_meta($tenant_id, 'primary_phone_type', true); 
    			$temp_tenant->primary_phone = get_post_meta($tenant_id, 'primary_phone', true); 
    			$temp_tenant->primary_contact_email = get_post_meta($tenant_id, 'primary_contact_email', true); 
    			$temp_tenant->city = get_post_meta($tenant_id, 'city', true); 
		        array_push( $tenants, $temp_tenant); 
	        endforeach;
	        $incident_info->tenant_ids = $tenants;
	        $contractor_ids = get_post_meta($incident_id, 'contractor_ids', true);
	        if( 'array' != gettype( $contractor_ids ) ){
	        	$contractor_ids = array();
	        }
	        $contractors = array();
	        foreach( $contractor_ids as $contractor_id):
	        	$contractor_info = get_user_by( 'ID', $contractor_id );
	        	if( !isset( $contractor_info->ID ) ):
	        		continue;
	        	endif;

	 	    	$temp_contr = array();
    		    $temp_contr = (object) $temp_contr;
    			$temp_contr->ID = $contractor_info->ID;
    			$temp_contr->user_login = $contractor_info->user_login;
    			$temp_contr->user_nicename = $contractor_info->user_nicename;
    			$temp_contr->user_email = $contractor_info->user_email;
    			$temp_contr->primary_fname = get_post_meta($contractor_id, 'primary_fname', true);
    			$temp_contr->primary_lname = get_post_meta($contractor_id, 'primary_lname', true); 
    			$temp_contr->primary_title = get_post_meta($contractor_id, 'primary_title', true); 
    			$temp_contr->company_name = get_post_meta($contractor_id, 'company_name', true); 
    			$temp_contr->primary_phone_type = get_post_meta($contractor_id, 'primary_phone_type', true); 
    			$temp_contr->primary_phone = get_post_meta($contractor_id, 'primary_phone', true); 
    			$temp_contr->primary_contact_email = get_post_meta($contractor_id, 'primary_contact_email', true); 
    			$temp_contr->city = get_post_meta($contractor_id, 'city', true); 

		        array_push( $contractors, $temp_contr); 
	        endforeach;
	        $incident_info->contractor_ids = $contractors;
    
	       array_push( $incidents, $incident_info);
	    endwhile;

	    wp_reset_postdata(); 
    	return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Incidents successfully getting.', 'jwt-auth' ),
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'total_incidents' => $total_incidents,
					'data'       => $incidents,
					'any_record_created' => $any_incident_created,
				),
				
			); 
    }

    /**
	 * Remove tenant from the Incident
	 */ 
	public function remove_tenant_from_incident(WP_REST_Request $request ){
		$tenant_ids = $request->get_param('tenant_ids');
		$incident_id = $request->get_param('incident_id');
		if( 'incident' != get_post_type( $incident_id)){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid incident id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
		}
		if( 'array' != gettype( $tenant_ids)){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one tenant id required.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
		}
		$old_tenant_ids = get_post_meta($incident_id, 'tenant_ids', true );
		if('array' != gettype( $old_tenant_ids ) ):
			$old_tenant_ids = array();
		endif;
		foreach( $tenant_ids as $tenant_id ):
			$find = $tenant_id;
			if( in_array($find, $old_tenant_ids) ){
				$index =  array_search($find,$old_tenant_ids);
				unset( $old_tenant_ids[$index] );
			}
			
		endforeach;
		$final_tenant_ids = array_merge( array(), $old_tenant_ids );
		$final_tenant_ids = array_unique($final_tenant_ids);
		update_post_meta($incident_id, 'tenant_ids', $final_tenant_ids );
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Tenant successfully deleted.', 'jwt-auth' ),
					'data'       => array('tenant_ids' => $final_tenant_ids),
				),
				
			); 
	}

	/**
	 * Add tenant in the Incident
	 */ 
	public function add_tenant_in_incident(WP_REST_Request $request ){
		$tenant_ids = $request->get_param('tenant_ids');
		$incident_id = $request->get_param('incident_id');
		if( 'incident' != get_post_type( $incident_id)){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid incident id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
		}
		if( 'array' != gettype( $tenant_ids)){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one tenant id required.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
		}
		$old_tenant_ids = get_post_meta($incident_id, 'tenant_ids', true );
		if('array' != gettype( $old_tenant_ids ) ):
			$old_tenant_ids = array();
		endif;
		 
		$final_tenant_ids = array_merge( $tenant_ids, $old_tenant_ids );
		$final_tenant_ids = array_unique($final_tenant_ids);
		update_post_meta($incident_id, 'tenant_ids', $final_tenant_ids );
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Tenant successfully added.', 'jwt-auth' ),
					'data'       => array('tenant_ids' => $final_tenant_ids),
				),
				
			); 
	}
	/**
	 * Remove contractor from the incident
	 */ 
	public function remove_contractor_from_incident(WP_REST_Request $request ){
		$contractor_ids = $request->get_param('contractor_ids');
		$incident_id = $request->get_param('incident_id');
		if( 'incident' != get_post_type( $incident_id)){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid incident id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
		}
		if( 'array' != gettype( $contractor_ids)){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one contractor id required.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
		}

		$old_contractor_ids = get_post_meta($incident_id, 'contractor_ids', true );
		if('array' != gettype( $old_contractor_ids ) ):
			$old_contractor_ids = array();
		endif;
		foreach( $contractor_ids as $contractor_id ):
			$find = $contractor_id;
			if( in_array($find, $old_contractor_ids) ){
				$index =  array_search($find,$old_contractor_ids);
				unset( $old_contractor_ids[$index] );
			}
			
		endforeach;
		$final_contractor_ids = array_merge( array(), $old_contractor_ids );
		$final_contractor_ids = array_unique($final_contractor_ids);
		update_post_meta($incident_id, 'contractor_ids', $final_contractor_ids );
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Contractor successfully deleted.', 'jwt-auth' ),
					'data'       => array('contractor_ids' => $final_contractor_ids),
				),
				
			);
		 
	}

	/**
	 * Add contractor in the incident
	 */ 
	public function add_contractor_in_incident(WP_REST_Request $request ){
		$contractor_ids = $request->get_param('contractor_ids');
		$incident_id = $request->get_param('incident_id');
		if( 'incident' != get_post_type( $incident_id)){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid incident id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
		}
		if( 'array' != gettype( $contractor_ids)){
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one contractor id required.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 	
		}

		$old_contractor_ids = get_post_meta($incident_id, 'contractor_ids', true );
		if('array' != gettype( $old_contractor_ids ) ):
			$old_contractor_ids = array();
		endif;
		 
		$final_contractor_ids = array_merge( $contractor_ids, $old_contractor_ids );
		$final_contractor_ids = array_unique($final_contractor_ids);
		update_post_meta($incident_id, 'contractor_ids', $final_contractor_ids );
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Contractor successfully added.', 'jwt-auth' ),
					'data'       => array('contractor_ids' => $final_contractor_ids),
				),
				
			);
		 
	}

}

new API_TenantIncidentsController();
?>
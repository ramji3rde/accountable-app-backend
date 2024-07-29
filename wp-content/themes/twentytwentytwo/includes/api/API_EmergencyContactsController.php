<?php
class API_EmergencyContactsController extends API_BaseController {

	public function __construct() 
	{  

 	}

	//  emergency-contacts

	//get_single_emergency Apis Code 
	public function get_single_emergency(WP_REST_Request $request)
 	{
 		$emergency_id    = $request->get_param( 'emergencyId' );  

 		if ( ( empty($emergency_id) ) || ( 'emergency-contacts' != get_post_type($emergency_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter Emergency ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

 
		$emergency_info = get_post( $emergency_id );

		if ( empty($emergency_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This Emergency is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{

	    	$temp_emergency = array();
	    	$temp_emergency = (object) $temp_emergency;
	    	
	    	$temp_emergency->emergency_id = $emergency_id;
	    	$temp_emergency->created_date = $emergency_info->post_date;
	    	$temp_emergency->item_name = $emergency_info->post_title;
	    	$temp_emergency->description = $emergency_info->post_content;
	    	$temp_emergency->author_id = $emergency_info->post_author;

	    	$temp_emergency->item_name  				=	get_post_meta($emergency_id, 'item_name', true);
			$temp_emergency->title  					=	get_post_meta($emergency_id, 'title', true);
			$temp_emergency->service  					=	get_post_meta($emergency_id, 'service', true);
			$temp_emergency->street_address  			=	get_post_meta($emergency_id, 'street_address', true);
			$temp_emergency->street_address_2  			=	get_post_meta($emergency_id, 'street_address_2', true);
			$temp_emergency->city  						=	get_post_meta($emergency_id, 'city', true);
			$temp_emergency->state  					=	get_post_meta($emergency_id, 'state', true);
			$temp_emergency->zip_code  					=	get_post_meta($emergency_id, 'zip_code', true);
			$temp_emergency->primary_phone  			=	get_post_meta($emergency_id, 'primary_phone', true);
			$temp_emergency->primary_phone_type  		=	get_post_meta($emergency_id, 'primary_phone_type', true);
			$temp_emergency->secondary_phone  			=	get_post_meta($emergency_id, 'secondary_phone', true);
			$temp_emergency->secondary_phone_type  		=	get_post_meta($emergency_id, 'secondary_phone_type', true);
			$temp_emergency->primary_email  			=	get_post_meta($emergency_id, 'primary_email', true);
			$temp_emergency->secondary_email  			=	get_post_meta($emergency_id, 'secondary_email', true);

	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $emergency_id,
			    'posts_per_page' => -1,
			);

			$notes = get_posts( 
				array( 
					'post_type'   => 'notes',
					'post_parent' => $emergency_id,
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
	        	
	        $temp_emergency->notes = $all_notes; 
		 
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Emergency Detail', 'jwt-auth' ),
						'data'       => $temp_emergency,
					),
					 
				);
		}

 	}

	/**
 	 * Update a emergency
 	 * 
 	 */ 
	public function update_emergency(WP_REST_Request $request)
	{ 
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
		 $emergency_id 		 	=	$request->get_param( 'emergencyId' ); 
		 $item_name  			=	$request->get_param( 'item_name' );
		 $title  				=	$request->get_param( 'title' ); 
		 $service  				=	$request->get_param( 'service' ); 
		 $street_address  		=	$request->get_param( 'street_address' ); 
		 $street_address_2 	 	=	$request->get_param( 'street_address_2' );
		 $city     				=	$request->get_param( 'city' );
		 $state     			=	$request->get_param( 'state' );
		 $zip_code     			=	$request->get_param( 'zip_code' );
		 $primary_phone    	 	=	$request->get_param( 'primary_phone' );
		 $primary_phone_type   	=	$request->get_param( 'primary_phone_type' );
		 $secondary_phone      	=	$request->get_param( 'secondary_phone' );
		 $secondary_phone_type 	=	$request->get_param( 'secondary_phone_type' );
		 $primary_email     	=	$request->get_param( 'primary_email' );
		 $secondary_email     	=	$request->get_param( 'secondary_email' );
		 
		  if( ( empty( $emergency_id ) ) || "emergency-contacts" != get_post_type( $emergency_id ) ) { 
 
			 return new WP_REST_Response(
				 array(
					 'success'    => false,
					 'statusCode' => 403,
					 'code'       => 'jwt_auth_bad_config',
					 'message'    => __( 'expense id is not exist.', 'jwt-auth' ),
					 'data'       => array(),
				 ),
				 
			 );
   
		 } 
 
		 $errors_arr = array();
		 if(empty($item_name)){
			  $errors_arr[] = __( 'Please enter item name.', 'jwt-auth' ); 
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
 
		 $update_args = array();
		 $update_args['ID'] = $emergency_id;
		 $update_args['post_title'] = $item_name; //  Title
		 $update_args['post_content'] = $title; // Description
		  if(wp_update_post( $update_args )):
			 update_post_meta($emergency_id, 'item_name', $item_name);
			 update_post_meta($emergency_id, 'title', $title);
			 update_post_meta($emergency_id, 'service', $service);
			 update_post_meta($emergency_id, 'street_address', $street_address);
			 update_post_meta($emergency_id, 'street_address_2', $street_address_2);
			 update_post_meta($emergency_id, 'city', $city);
			 update_post_meta($emergency_id, 'state', $state);
			 update_post_meta($emergency_id, 'zip_code', $zip_code);
			 update_post_meta($emergency_id, 'primary_phone', $primary_phone);
			 update_post_meta($emergency_id, 'primary_phone_type', $primary_phone_type);
			 update_post_meta($emergency_id, 'secondary_phone', $secondary_phone);
			 update_post_meta($emergency_id, 'secondary_phone_type', $secondary_phone_type);
			 update_post_meta($emergency_id, 'primary_email', $primary_email);
			 update_post_meta($emergency_id, 'secondary_email', $secondary_email);
 
 
		 return new WP_REST_Response(
 
					 array(
						 'success'    => true,
						 'statusCode' => 200,
						 'code'       => 'success',
						 'message'    => __( 'Successfull updated.', 'jwt-auth' ),
						 'data'       => array(),
					 ),
					 
			 ); 
		 endif;
		 
 
		 return new WP_REST_Response(
				 array(
					 'success'    => false,
					 'statusCode' => 403,
					 'code'       => 'error',
					 'message'    => __( 'Invalid expense Id.', 'jwt-auth' ),
					 'data'       => array(),
				 ),
				 
		 );
		 
 
    }

	/**
 	 * create a new emergency 
 	 * 
 	 */ 
	  public function create_new_emergency(WP_REST_Request $request)
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
		  $item_name    = $request->get_param( 'item_name' );
		  $title    = $request->get_param( 'title' ); 
		  $service    = $request->get_param( 'service' ); 
		  $street_address    = $request->get_param( 'street_address' ); 
		  $street_address_2    = $request->get_param( 'street_address_2' );
		  $city    = $request->get_param( 'city' );
		  $state    = $request->get_param( 'state' );
		  $zip_code    = $request->get_param( 'zip_code' );
		  $primary_phone    = $request->get_param( 'primary_phone' );
		  $primary_phone_type    = $request->get_param( 'primary_phone_type' );
		  $secondary_phone    = $request->get_param( 'secondary_phone' );
		  $secondary_phone_type    = $request->get_param( 'secondary_phone_type' );
		  $primary_email    = $request->get_param( 'primary_email' );
		  $secondary_email    = $request->get_param( 'secondary_email' );

		  $API_BaseController = new API_BaseController();
		  $post_author = $API_BaseController->custom_validate_token($_SERVER);

		 $errors_arr = array();
		 if(empty($item_name)){
			  $errors_arr[] = __( 'Please enter item name.', 'jwt-auth' ); 
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
  
		
		 $EmergencyRes = wp_insert_post(array(
			 'post_title'     => $item_name,
			 'post_content'  => $title,
			 'post_type'     => 'emergency-contacts',
			 'post_status'   => 'publish',
			 'post_author'   => $post_author,
			  
		 ));
		 if( isset( $EmergencyRes ) && is_numeric( $EmergencyRes ) ){
			 update_user_meta($post_author, 'any_emergency_created', 'yes');
 
			 update_post_meta($EmergencyRes, 'item_name', $item_name);
			 update_post_meta($EmergencyRes, 'title', $title);
			 update_post_meta($EmergencyRes, 'service', $service);
			 update_post_meta($EmergencyRes, 'street_address', $street_address);
			 update_post_meta($EmergencyRes, 'street_address_2', $street_address_2);
			 update_post_meta($EmergencyRes, 'city', $city);
			 update_post_meta($EmergencyRes, 'state', $state);
			 update_post_meta($EmergencyRes, 'zip_code', $zip_code);
			 update_post_meta($EmergencyRes, 'primary_phone', $primary_phone);
			 update_post_meta($EmergencyRes, 'primary_phone_type', $primary_phone_type);
			 update_post_meta($EmergencyRes, 'secondary_phone', $secondary_phone);
			 update_post_meta($EmergencyRes, 'secondary_phone_type', $secondary_phone_type);
			 update_post_meta($EmergencyRes, 'primary_email', $primary_email);
			 update_post_meta($EmergencyRes, 'secondary_email', $secondary_email);
			 
			 // $taxonomy = 'expense_category';
			 // foreach( $category_ids as $cat_id ):
				 // wp_set_object_terms($EmergencyRes, $category_ids, $taxonomy);
			 // endforeach;
			 return new WP_REST_Response(
				 array(
					 'success'    => true,
					 'statusCode' => 200,
					 'code'       => 'success',
					 'message'    => __( 'Emergency successfully created.', 'jwt-auth' ),
					 'data'       => array('emergency_id' => $EmergencyRes),
				 ),
				 
			 ); 
 
		 } else {
			 $massage ='';
			 foreach ($EmergencyRes->errors as $key => $errors) {
				 $massage .= $errors[0];
			 } 
 
			 return new WP_REST_Response(
				 array(
					 'success'    => false,
					 'statusCode' => 403,
					 'code'       => 'jwt_auth_bad_config this is error source',
					 'message'    => __( $massage, 'jwt-auth' ),
					 'data'       => array(),
				 ),
				 
			 ); 
		  } 
 
  
	  }


	


	/**
 	 * Delete multiple emergency by id 
 	 * 
 	 */
	public function delete_emergency(WP_REST_Request $request)
	{
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

		//contect info
		$emergency_ids    = $request->get_param( 'emergency_ids' ); 
		
		if( empty( $emergency_ids) || gettype($emergency_ids) != 'array' ){
			return new WP_REST_Response(
			   array(
				   'success'    => false,
				   'statusCode' => 403,
				   'code'       => 'error',
				   'message'    => __( 'Minimum one Emergency Contacts id is required', 'jwt-auth' ),
				   'data'       => '',
			   ),
			   
		   ); 
		}

		$total_delete_emergency = 0;

		foreach( $emergency_ids as $emergency_id ){
			if( get_post_type($emergency_id) != "emergency-contacts"):
				continue;
			endif;
			if(wp_delete_post($emergency_id, true)):
			// 	// delete attachment/photos
			// 	$attachment_args = array(
			// 	   'post_type' => "attachment",
			// 	   'post_parent' => $emergency_id,
			// 	   'posts_per_page' => -1,
			//    );
			//    $attachments = get_posts($attachment_args);
			//    foreach( $attachments  as $attachment):
			// 	   $API_PhotosController->ballon_delete_media_from_s3($attachment->guid);
			// 	   wp_delete_attachment($attachment->ID, true);

			//    endforeach;
			
				$total_delete_emergency++;
				
				// delete notes
				$notes = get_posts( 
				   array( 
					   'post_type'   => 'notes',
					   'post_parent' => $emergency_id,
					   'post_status' => array('publish'),
					   'posts_per_page' => -1,
				   ) 
			   );
			   $all_notes = array();
			   foreach( $notes as $single_note ):
				   wp_delete_post($single_note->ID, true);
			   endforeach;
			endif;
		}
		if( $total_delete_emergency > 0 ):
			return new WP_REST_Response(
			   array(
				   'success'    => true,
				   'statusCode' => 200,
				   'code'       => 'success',
				   'message'    => __( 'Emergency successfully deleted.', 'jwt-auth' ),
				   'data'       => array('total_delete_emergency' => $total_delete_emergency),
			   ),
			   
		   ); 
		else:
			return new WP_REST_Response(
			   array(
				   'success'    => false,
				   'statusCode' => 403,
				   'code'       => 'error',
				   'message'    => __( 'Due to technical issue not deleting any Emergency', 'jwt-auth' ),
				   'data'       => '',
			   ),
			   
		   ); 
		endif;
		
	}



 	 
    /**
	 * Search or get all emergency with pagination
	 * 
	 */ 	
    public function search_emergency(WP_REST_Request $request){
    	$search_by_keyword = $request->get_param( 'search_by_keyword' );
    	$paged = ($request->get_param('paged')) ? $request->get_param('paged') : 1;
    	$posts_per_page = ($request->get_param('posts_per_page')) ? $request->get_param('posts_per_page') : 10;
    	$sort_by_field    = $request->get_param( 'sort_by_field' );
    	// $orderby = "date";
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
		$any_emergency_created = get_user_meta($post_author, 'any_emergency_created', true);
    	 
    	$total_emergency = 0;
    	 $args = array(  
    	 	'author' => $post_author,
	        'post_type' => 'emergency-contacts',
	        'post_status' => 'publish',
	        'paged' => $paged, 
	        'orderby' => $orderby,
			'order'   => $order,
	        'posts_per_page' => $posts_per_page, 
	        
	    );

    	if( '' != trim( $search_by_keyword ) ):
    		$args['meta_query'] = array(
    				'relation' => 'AND',
				        array(
				            'value' => $search_by_keyword,
				            'compare' => "LIKE"
				        )
				    );
    	endif;

		if( '' != $start_date  && '' != $end_date ):
			$args['date_query'] = array(
			   array(
				   'after'     => $start_date,
				   'before'    => $end_date,
				   'inclusive' => true,
			   ),
		   );
		endif;

		$emergency = array();
    	$the_query = new WP_Query( $args );
    	$total_emergency = $the_query->found_posts;


		// api code start 

		while ( $the_query->have_posts() ) : $the_query->the_post();
		$emergency_id = get_the_ID();
		$emergency_info = get_post( $emergency_id );

		if ( empty($emergency_info) ) :
			continue;
		endif;
		$temp_emergency = array();
		$temp_emergency = (object) $temp_emergency;
		$temp_emergency->emergency_id = $emergency_id;
		$temp_emergency->created_date = $emergency_info->post_date;
		$temp_emergency->item_name = $emergency_info->post_title;
		$temp_emergency->description = $emergency_info->post_content;
		$temp_emergency->author_id = $emergency_info->post_author;
		$temp_emergency->item_name = get_post_meta($emergency_id, 'item_name', true);
		$temp_emergency->title = get_post_meta($emergency_id, 'title', true);
		$temp_emergency->service = get_post_meta($emergency_id, 'service', true);
		$temp_emergency->street_address = get_post_meta($emergency_id, 'street_address', true);
		$temp_emergency->street_address_2 = get_post_meta($emergency_id, 'street_address_2', true);
		$temp_emergency->city = get_post_meta($emergency_id, 'city', true);
		$temp_emergency->state = get_post_meta($emergency_id, 'state', true);
		$temp_emergency->zip_code = get_post_meta($emergency_id, 'zip_code', true);
		$temp_emergency->primary_phone = get_post_meta($emergency_id, 'primary_phone', true);
		$temp_emergency->primary_phone_type = get_post_meta($emergency_id, 'primary_phone_type', true);
		$temp_emergency->secondary_phone = get_post_meta($emergency_id, 'secondary_phone', true);
		$temp_emergency->secondary_phone_type = get_post_meta($emergency_id, 'secondary_phone_type', true);
		$temp_emergency->primary_email = get_post_meta($emergency_id, 'primary_email', true);
		$temp_emergency->secondary_email = get_post_meta($emergency_id, 'secondary_email', true);

		$notes = get_posts( 
			array(
				'numberposts' => 1,
				'orderby'  => 'date',
				'order' => 'ASC',
				'post_type'   => 'notes',
				'post_parent' => $emergency_id,
				'post_status' => array('publish'),

			) 
		);


	   array_push( $emergency, $temp_emergency);
		endwhile;

   	
    	return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Emergency Successfully Getting.', 'jwt-auth' ),
					'data'       => $emergency,
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					// 'total_expenses_in_sp' => $total_expenses_in_sp,
					'total_emergency' => $total_emergency,
					'any_emergency_created' => $any_emergency_created
				),
				
			); 
    }

}

new API_EmergencyContactsController();
?>
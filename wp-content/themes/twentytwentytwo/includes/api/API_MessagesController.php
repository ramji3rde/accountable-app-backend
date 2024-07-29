<?php

class API_MessagesController extends API_BaseController {

	public function __construct() 
	{  

 	}
 	/**
 	 * create a new message. tenant is sending messge for admin 
 	 * 
 	 */ 
 	public function create_new_message(WP_REST_Request $request)
 	{
 		//contect info
 		$title    = $request->get_param( 'title' );
 		$date    = $request->get_param( 'date' ); 
 		$detail    = $request->get_param( 'detail' ); 
 		$status    = $request->get_param( 'status' ); 
 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$tenant_post_id = get_user_meta( $post_author, '_tenant_post_id', true );
	    $app_admin_id = get_post_field ('post_author', $tenant_post_id);
		$errors_arr = array();
		if(empty($title)){

 			$errors_arr[] = __( 'Please enter message.', 'jwt-auth' ); 
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
 
  
		$MessageReg = wp_insert_post(array(
			'post_title'     => $title,
			'post_content'  => $detail,
			'post_type'     => 'messages',
			'post_status'   => 'publish',
			'post_author'   => $post_author,
			 
		));
		if( isset( $MessageReg ) && is_numeric( $MessageReg ) ){
			update_user_meta($post_author, 'any_message_created', 'yes');
			update_post_meta($MessageReg, 'real_post_author', $post_author);
        	update_post_meta($MessageReg, 'date', $date);
	        update_post_meta($MessageReg, 'status', $status);
	        update_post_meta($MessageReg, '_app_admin_id', $app_admin_id );
	        return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Message successfully saved!', 'jwt-auth' ),
					'data'       => array('message_id' => $MessageReg),
				),
				
			); 

		} else {
			$massage ='';
			foreach ($MessageReg->errors as $key => $errors) {
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
	 * tenant Search or get all messages with pagination
	 * 
	 */ 	
    public function search_messages(WP_REST_Request $request){
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
    	 
    	$total_messages = 0;
    	 $args = array(  
    	 	'author' => $post_author,
	        'post_type' => 'messages',
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
		$any_message_created = get_user_meta($post_author, 'any_message_created', true); 
    	$messages = array();
    	$the_query = new WP_Query( $args );
    	$total_messages = $the_query->found_posts;
	        
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$message_id = get_the_ID();
	        $message_info = get_post( $message_id );

	        if ( empty($message_info) ) :
	        	continue;
	        endif;
	        
	        $message_info->date = get_post_meta($message_id, 'date', true);
	        $message_info->status = get_post_meta($message_id, 'status', true); 
	        $message_info->property = get_post_meta($message_id, 'property', true);
	        // $message_info->tenant_ids = get_post_meta($message_id, 'tenant_ids', true);
	        $comments = get_comments(array(
					        'post_id' => $message_id,
					        'number' =>  -1 )
					        );
    	   $message_info->comments = $comments;	
	       array_push( $messages, $message_info);
	    endwhile;

	    wp_reset_postdata(); 
    	return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'messages successfully getting.', 'jwt-auth' ),
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'total_messages' => $total_messages,
					'data'       => $messages,
					'any_record_created' => $any_message_created,
				),
				
			); 
    }
    /**
     * Tenant will get a single message
     */

 	public function get_single_message(WP_REST_Request $request)
 	{
 		$message_id    = $request->get_param( 'messageId' );  

 		if ( ( empty($message_id) ) || ( 'messages' != get_post_type($message_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter message ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

 
		$message_info = get_post( $message_id );

		if ( empty($message_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This message is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{

	    	 

	    	$message_info->date = get_post_meta($message_id, 'date', true);
	        $message_info->status = get_post_meta($message_id, 'status', true); 
	        $comments = get_comments(array(
					        'post_id' => $message_id,
					        'number' =>  -1 )
					        );
    	    $message_info->comments = $comments;	
	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $message_id,
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
		 
			
			$message_info->photos = $photos;
		 
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Message Details', 'jwt-auth' ),
						'data'       => $message_info,
					),
					 
				);
		}

 	}
 	/**
 	 * Tenant can delete multiple messages by id 
 	 * 
 	 */
 	public function delete_message(WP_REST_Request $request){
 		//contect info
 		$message_ids    = $request->get_param( 'message_ids' ); 
 		$API_PhotosController = new API_PhotosController();
 		if( empty( $message_ids) || gettype($message_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one message id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$total_delete_messages = 0;
 		foreach( $message_ids as $message_id ){
 			if( get_post_type($message_id) != "messages"):
 				continue;
 			endif;
 			if(wp_delete_post($message_id, true)):
 				// delete attachment/photos
 				$attachment_args = array(
				    'post_type' => "attachment",
				    'post_parent' => $message_id,
				    'posts_per_page' => -1,
				);
				$attachments = get_posts($attachment_args);
				foreach( $attachments  as $attachment):
					$API_PhotosController->ballon_delete_media_from_s3($attachment->guid);
				    wp_delete_attachment($attachment->ID, true);
				endforeach;
 				$total_delete_messages++;
 			endif;
 		}
 		if( $total_delete_messages > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Messages successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_deleted_messages' => $total_delete_messages),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any message', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}
 	/**
 	 * Tenant can update a message
 	 * 
 	 */ 
 	public function update_message(WP_REST_Request $request)
 	{ 
 		 
 		$message_id    = $request->get_param( 'messageId' ); 
 		//contect info
 		$title    = $request->get_param( 'title' );
 		$date    = $request->get_param( 'date' ); 
 		$detail    = $request->get_param( 'detail' ); 
 		$status    = $request->get_param( 'status' ); 
 		$property    = $request->get_param( 'property' );

 		if( ( empty( $message_id ) ) || "messages" != get_post_type( $message_id ) ) { 

	        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'message id is not exist.', 'jwt-auth' ),
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
	    
	    $update_args = array();
	    $update_args['ID'] = $message_id;
		$update_args['post_title'] = $title; //  Title
		$update_args['post_content'] = $detail; // Description
 		if(wp_update_post( $update_args )):
			update_post_meta($message_id, 'date', $date);
	        update_post_meta($message_id, 'status', $status);
	        
	        // update_post_meta($message_id, 'property', $property); 
            return new WP_REST_Response(

					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Message successfully updated!', 'jwt-auth' ),
						'data'       => array(),
					),
					
			); 
        endif;
		

        return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid message Id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
		);
		

 	}
 	/**
	 *  Get all message for a tenant ( Created by tenant user ). This API for admin
	 */ 
	public function admin_get_all_tenant_message_list(WP_REST_Request $request)
	{
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_message_created = get_user_meta($post_author, 'any_message_created', true); 
		$search_by_keyword    = $request->get_param( 'search_by_keyword' );
    	$paged = ($request->get_param('paged')) ? $request->get_param('paged') : 1;
    	$posts_per_page = ($request->get_param('posts_per_page')) ? $request->get_param('posts_per_page') : 10;
    	$sort_by_field    = $request->get_param( 'sort_by_field' );
    	$orderby = "date";
    	$order = "DESC";
    	// var_dump("expression");
    	if( 'a-z' == $sort_by_field ):
    		$orderby = "title";
    		$order = "ASC";
    	endif;
    	if( 'z-a' == $sort_by_field ):
    		$orderby = "title";
    		$order = "DESC";
    	endif;
    	
    	 $args = array( 
    	 	// 'author' => $post_author,
	        'post_type' => 'messages',
	        'post_status' => array( 'publish'),
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
    	if( 'services' == $sort_by_field ):
    	 	$args['meta_key'] = "services";
    		$args['orderby'] = "meta_value";
    		$args['order'] = "ASC";
    	endif;
    	if( '' != trim( $search_by_keyword ) ):
    		$args['meta_query'] = array(
    				'relation' => 'AND',
				        array(
				            'value' => $search_by_keyword,
				            'compare' => "LIKE"
				        ),
				         array(
				         	'key' => 'real_post_author',
				            'value' => 0,
				            'compare' => ">"
				        ),
				          array(
				         	'key' => '_app_admin_id',
				            'value' => $post_author,
				            'compare' => "="
				        )
				    );
    	else:
    		$args['meta_query'] = array(
    				'relation' => 'AND',
				      
				         array(
				         	'key' => 'real_post_author',
				            'value' => 0,
				            'compare' => ">"
				        ),
				          array(
				         	'key' => '_app_admin_id',
				            'value' => $post_author,
				            'compare' => "="
				        )
				    );
    	endif;
    	 
		$messages = array();
		$total_messages = 0;
    	$the_query = new WP_Query( $args );
    	$total_messages = $the_query->found_posts;
	        
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$message_id = get_the_ID();
	        $message_info = get_post( $message_id );

	        if ( empty($message_info) ) :
	        	continue;
	        endif;
	      	$tenant_id = get_post_field( 'post_author', $message_id );
	        $message_info->message_created =$message_info->post_date;
	        $message_info->message_name = $message_info->post_title;
	        $message_info->message_date = get_post_meta($message_id, 'date', true);
	        $message_info->message_detail = $message_info->post_content;
	        $message_info->status = get_post_meta($message_id, 'status', true);
	        $message_info->company_name = get_post_meta($tenant_id, 'company_name', true);
	       $comments = get_comments(array(
					        'post_id' => $message_id,
					        'number' =>  -1 )
					        );
    	    $message_info->comments = $comments;	
	        
			array_push( $messages, $message_info);
	    endwhile;


		return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'All messages list', 'jwt-auth' ),
						'posts_per_page' => $posts_per_page,
						'paged' => $paged,
						'total_messages' => $total_messages,
						'data'       => $messages,
						'any_record_created' => $any_message_created,
						
					),
					
				); 

	}

	
	/**
 	 * Admin can send a message reply
 	 * 
 	 */ 
 	public function admin_send_message_reply(WP_REST_Request $request)
 	{
 		//contect info
 		$title    = $request->get_param( 'title' );
 		$message_id    = $request->get_param( 'message_id' ); 
 		 
 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		 
		$errors_arr = array();
		if(empty($title)){

 			$errors_arr[] = __( 'Please enter message reply.', 'jwt-auth' ); 
		}
		if ( ( empty($message_id) ) || ( 'messages' != get_post_type($message_id) ) ) { 
			$errors_arr[] = __( 'Invalid message id.', 'jwt-auth' ); 
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
 
  		$user = get_user_by( 'id', $post_author );
		$commentReg = wp_insert_comment(array(
			// 'post_title'     => $title,
			'comment_content'  => $title,
			'comment_post_ID'     => $message_id,
			'comment_type'   => 'comment',
			'comment_author' => $user->display_name,
			'comment_author_email' => $user->user_email,
			'user_id'   => $post_author,
			 
		));
		if( isset( $commentReg ) && is_numeric( $commentReg ) ){
			// update_user_meta($post_author, 'any_message_created', 'yes');
			// update_post_meta($MessageReg, 'real_post_author', $post_author);
        	// update_post_meta($MessageReg, 'date', $date);
	        // update_post_meta($MessageReg, 'status', $status);
	        // update_post_meta($MessageReg, '_app_admin_id', $app_admin_id );
	        return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Comment successfully saved!', 'jwt-auth' ),
					'data'       => array('commentid' => $commentReg),
				),
				
			); 

		} else {
			$massage ='';
			foreach ($MessageReg->errors as $key => $errors) {
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
 }

new API_MessagesController();
?>
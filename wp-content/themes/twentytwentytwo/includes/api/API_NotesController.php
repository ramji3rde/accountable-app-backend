<?php

class API_NotesController extends API_BaseController {

	public function __construct() 
	{  

 	}
 	public function get_all_notes(WP_REST_Request $request)
 	{
 		$post_id = $request->get_param( 'post_id' );
 		$errors_arr = array();
 		if(empty($post_id)){

 			$errors_arr[] = __( 'Required post id.', 'jwt-auth' ); 
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
 		$notes = get_posts( 
				array( 
					'post_type'   => 'notes',
					'post_parent' => $post_id,
					'post_status' => array('publish'),
					'posts_per_page' => -1,
				) 
			);
 		if(!empty($notes))
 		{
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All notes', 'jwt-auth' ),
					'data'       => $notes,
				),
				
			); 
 		} 
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
 	/** create new notes for a post like tenant */
 	public function create_new_note(WP_REST_Request $request)
 	{
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
 		$notes    = $request->get_param( 'notes' );
 		if( empty( $notes) || gettype($notes) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one note is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		 
 		$post_id = $request->get_param( 'post_id' );
 		$author = $request->get_param( 'author' );
 		$errors_arr = array();
 		if(empty($notes)){

 			$errors_arr[] = __( 'Please enter any note.', 'jwt-auth' ); 
		}

		if(empty($post_id)){

 			$errors_arr[] = __( 'Please enter post id.', 'jwt-auth' ); 
		}
		if(empty($author)){

 			$errors_arr[] = __( 'Author id is required.', 'jwt-auth' ); 
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
	    $total_created_notes = array();
	    foreach( $notes as $note ){
	    	if( isset( $note['detail'] ) && trim( $note['detail'] ) != '' ){
	    		$note_Detail = trim( $note['detail'] );
	    		 $Reg = wp_insert_post(array(
					'post_title'    => $note_Detail,
					'post_type'     => 'notes',
					'post_content'  => $note_Detail,
					'post_status'   => 'publish',
					'post_parent'   => $post_id,
					'post_author'   => $author,
					 
				));
		    	if( isset( $Reg ) && is_numeric( $Reg ) ){
		    		$temp = array('note_id' => $Reg, 'note_detail' => $note_Detail);
		    		array_push( $total_created_notes, $temp);
					update_post_meta($Reg, 'post_id', $post_id);
					 
				}
	    	}
	    	
	    }
	    
	   
		if( count($total_created_notes ) > 0  ){
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Notes sucessfully created.', 'jwt-auth' ),
					'data'       => $total_created_notes,
				),

			);
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Note is not creating due to technical issue.', 'jwt-auth' ),
				'data'       => '',
			),
			
		); 
	}

	/** update exists notes for a post like tenant */
 	public function update_note(WP_REST_Request $request)
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
 		$notes = $request->get_param('notes');
 		if( empty( $notes) || gettype($notes) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one note is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		
 		$post_id = $request->get_param( 'post_id' );
 	 
 		$errors_arr = array();
 		if(empty($notes)){

 			$errors_arr[] = __( 'Please enter any note.', 'jwt-auth' ); 
		}
		// if(empty($note_id)){

 	// 		$errors_arr[] = __( 'Note id is required.', 'jwt-auth' ); 
		// }
		if(empty($post_id)){

 			$errors_arr[] = __( 'Please enter post id.', 'jwt-auth' ); 
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
	   $total_updated_notes = array();
	   $not_updated_notes = array();
	   foreach( $notes as $note ){
	   		$note_id = $note['note_id'];
 			$note    = $note['note'];
 			 if( get_post_type($note_id) != "notes" ){
 			 	$temp = array( "note_id" => $note_id, 'reason' => "this id is not a note id");
 			 	array_push( $not_updated_notes, $temp);
 			 	continue;
 			 }
 			$update_args = array();
	 		$update_args['ID'] = $note_id;
			$update_args['post_title'] = $note; 
			$update_args['post_content'] = $note;
			$update_args['post_parent'] = $post_id;
		     
			if(wp_update_post( $update_args )){
				update_post_meta($note_id, 'post_id', $post_id);
				$temp = array( "note_id" => $note_id);
				array_push( $total_updated_notes, $temp);

			}else{
				$temp = array( "note_id" => $note_id, 'reason' => "This note id not updating.");
 			 	array_push( $not_updated_notes, $temp);
			}

	   }
		if( count( $total_updated_notes) > 0 ){
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Note sucessfully updated.', 'jwt-auth' ),
					'data'       => array('total_updated_note' => $total_updated_notes, 'total_not_updated_notes' => $not_updated_notes)
				),

			);
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Note is not updating.', 'jwt-auth' ),
				'data'       => '',
			),
			
		); 
	}

	/** Delete a notes */
	public function delete_note(WP_REST_Request $request){
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
		$note_ids    = $request->get_param( 'note_ids' );
		if( empty( $note_ids) || gettype($note_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one note id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		$total_delete_notes = 0;
 		foreach( $note_ids as $note_id ){
 			if( get_post_type($note_id) != "notes"):
 				continue;
 			endif;
 			if(wp_delete_post($note_id, true)):
 				 
 				$total_delete_notes++;
 			endif;
 		}
 		if( $total_delete_notes > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Notes successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_deleted_notes' => $total_delete_notes),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any note', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
	}

	/** create new notes for a user like Team Support */
 	public function create_new_user_note(WP_REST_Request $request)
 	{
 		
 		$notes    = $request->get_param( 'notes' );
 		if( empty( $notes) || gettype($notes) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one note is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		 
 		$user_id = $request->get_param( 'user_id' );
 		$author = $request->get_param( 'author' );
 		$errors_arr = array();
 		if(empty($notes)){

 			$errors_arr[] = __( 'Please enter any note.', 'jwt-auth' ); 
		}

		if(empty($user_id)){

 			$errors_arr[] = __( 'Please enter user id.', 'jwt-auth' ); 
		}
		if(empty($author)){

 			$errors_arr[] = __( 'Author id is required.', 'jwt-auth' ); 
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
	    $total_created_notes = array();
	    foreach( $notes as $note ){
	    	if( isset( $note['detail'] ) && trim( $note['detail'] ) != '' ){
	    		$note_Detail = trim( $note['detail'] );
	    		 $Reg = wp_insert_post(array(
					'post_title'    => $note_Detail,
					'post_type'     => 'notes',
					'post_content'  => $note_Detail,
					'post_status'   => 'publish',
					'post_author'   => $user_id,
					 
				));
		    	if( isset( $Reg ) && is_numeric( $Reg ) ){
		    		$temp = array('note_id' => $Reg, 'note_detail' => $note_Detail);
		    		array_push( $total_created_notes, $temp);
					update_post_meta($Reg, 'user_id', $user_id);
					 
				}
	    	}
	    	
	    }
	    
	   
		if( count($total_created_notes ) > 0  ){
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Notes sucessfully created.', 'jwt-auth' ),
					'data'       => $total_created_notes,
				),

			);
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Note is not creating due to technical issue.', 'jwt-auth' ),
				'data'       => '',
			),
			
		); 
	}
	public function delete_all_notes_by_user_id( $user_id ){
		$user_id = $user_id;
		$notes_ids = get_posts( array(
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'post_type'      => array('notes'),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'author'        =>  $user_id, 
            'meta_query'    => array(
            	array(
				'key'       => 'user_id',
				'value'     => $user_id,
				'compare'   => '=',
				)
            )
        ));
 		$total_delete_notes = 0; 
 		foreach( $notes_ids as $note_id ){
 			if( get_post_type($note_id) != "notes"):
 				continue;
 			endif;
 			if(wp_delete_post($note_id, true)):
 				 
 				$total_delete_notes++;
 			endif;
 		}
 		$return_array = array('total_deleted_notes' => $total_delete_notes, 'notes_ids' => $notes_ids);
 		return $return_array; 
	}
	/** Delete a user notes */
	public function delete_user_note(WP_REST_Request $request){
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
		$note_ids    = $request->get_param( 'note_ids' );
		if( empty( $note_ids) || gettype($note_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one note id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		$total_delete_notes = 0;
 		foreach( $note_ids as $note_id ){
 			if( get_post_type($note_id) != "notes"):
 				continue;
 			endif;
 			if(wp_delete_post($note_id, true)):
 				 
 				$total_delete_notes++;
 			endif;
 		}
 		if( $total_delete_notes > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Notes successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_deleted_notes' => $total_delete_notes),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any note', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
	}

	/** update exists notes for a post like contractor or support team */
 	public function update_user_note(WP_REST_Request $request)
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
 		$notes = $request->get_param('notes');
 		if( empty( $notes) || gettype($notes) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one note is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		
 		$user_id = $request->get_param( 'user_id' );
 	 
 		$errors_arr = array();
 		if(empty($notes)){

 			$errors_arr[] = __( 'Please enter any note.', 'jwt-auth' ); 
		}
		
		if(empty($user_id)){

 			$errors_arr[] = __( 'Please enter user id.', 'jwt-auth' ); 
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
	    $user_info = get_user_by('ID', $user_id);
	    if( empty( $user_info ) ):
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter valid user id', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
	    endif;
	   $total_updated_notes = array();
	   $not_updated_notes = array();
	   foreach( $notes as $note ){
	   		$note_id = $note['note_id'];
 			$note    = $note['note'];
 			 if( get_post_type($note_id) != "notes" ){
 			 	$temp = array( "note_id" => $note_id, 'reason' => "this id is not a note id");
 			 	array_push( $not_updated_notes, $temp);
 			 	continue;
 			 }
 			$update_args = array();
	 		$update_args['ID'] = $note_id;
			$update_args['post_title'] = $note; 
			$update_args['post_content'] = $note;
			 
		     
			if(wp_update_post( $update_args )){
				update_post_meta($note_id, 'user_id', $user_id);
				$temp = array( "note_id" => $note_id);
				array_push( $total_updated_notes, $temp);

			}else{
				$temp = array( "note_id" => $note_id, 'reason' => "This note id not updating.");
 			 	array_push( $not_updated_notes, $temp);
			}

	   }
		if( count( $total_updated_notes) > 0 ){
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Note sucessfully updated.', 'jwt-auth' ),
					'data'       => array('total_updated_note' => $total_updated_notes, 'total_not_updated_notes' => $not_updated_notes)
				),

			);
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Note is not updating.', 'jwt-auth' ),
				'data'       => '',
			),
			
		); 
	}
	public function user_notes( $user_id ){
		$notes = get_posts( 
				array( 
					'post_type'   => 'notes',
					'author' => $user_id,
					'post_status' => array('publish'),
					'posts_per_page' => -1,
					'meta_query'  => array(
						array(
							'key'       => 'user_id',
							'value'          => $user_id,
							'compare' => '=',
							)
					)
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
		return $all_notes;
	}
	public function get_user_all_notes(WP_REST_Request $request)
 	{
 		$user_id = $request->get_param( 'user_id' );
 		$errors_arr = array();
 		if(empty($user_id)){

 			$errors_arr[] = __( 'Required user id.', 'jwt-auth' ); 
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
 		$notes = $this->user_notes( $user_id );
 		if(!empty($notes))
 		{
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All notes', 'jwt-auth' ),
					'data'       => $notes,
				),
				
			); 
 		} 
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

}

new API_NotesController();
?>
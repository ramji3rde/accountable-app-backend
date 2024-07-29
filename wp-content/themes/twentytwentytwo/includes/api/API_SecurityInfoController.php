<?php
class API_SecurityInfoController extends API_BaseController {

	public function __construct() 
	{  

 	}
 	 
    public function create_master_password(WP_REST_Request $request){
        $API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
        $master_password = $request->get_param('master_password');
        $password_hint = $request->get_param('password_hint');
        if(empty($master_password)){ 

			$errors_arr[] = __( 'Please enter master password.', 'jwt-auth' ); 
		} 

        
		if(empty($password_hint)){

 			$errors_arr[] = __( 'Please enter password hint.', 'jwt-auth' ); 
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

        // Save the master password in the user's meta data
        update_user_meta($post_author, 'master_password', $master_password);
        update_user_meta($post_author, 'password_hint', $password_hint);
        return new WP_REST_Response(
            array(
                'success'    => true,
                'statusCode' => 200,
                'code'       => 'success',
                'message'    => __( 'password created successfully', 'jwt-auth' ),
                'data'       => array('master_password' => $master_password),
            ),
            
        ); 
	}

	public function show_password_hint(WP_REST_Request $request){
        $API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
        $password_hint = get_user_meta($post_author, 'password_hint', true);
		if(empty($password_hint)):

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Unable to fetch hint. Please recreate master password and hint', 'jwt-auth' ),
				),
				
			); 

		endif;

        return new WP_REST_Response(
            array(
                'success'    => true,
                'statusCode' => 200,
                'code'       => 'success',
                'message'    => __( 'password created successfully', 'jwt-auth' ),
                'data'       => array('password_hint' => $password_hint),
            ),
            
        ); 
	}

	public function security_login(WP_REST_Request $request){
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$master_password = $request->get_param('master_password');
		
		if($master_password === get_user_meta($post_author, 'master_password', true)):
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'successfully logged-in', 'jwt-auth' ),
					'data'       => array($master_password),
				),
				
			); 
		endif;

		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Something went wrong. Please check your master password', 'jwt-auth' ),
				'data'       => array($master_password),
			),
			
		); 
	}
	

	public function create_security_note(WP_REST_Request $request){
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

		$master_password = $request->get_param('master_password');
		
		if($master_password != get_user_meta($post_author, 'master_password', true)):
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'please provide correct master password', 'jwt-auth' ),
					'data'       => array($master_password),
				),
				
			); 
		endif;

		$category = $request->get_param('category');
		$name = $request->get_param('name');
		$username = $request->get_param('username');
		$password = $request->get_param('password');
		$account_url = $request->get_param('account_url');
		$group = $request->get_param('group');
		$notes = $request->get_param('notes');

		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);

	   $errors_arr = array();
	   if(empty($name)){
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

	  
	   $SecurityInfo = wp_insert_post(array(
		   'post_title'     => $name,
		   'post_content'  => $username,
		   'post_type'     => 'security-info',
		   'post_status'   => 'publish',
		   'post_author'   => $post_author,
			
	   ));

	   if (!is_wp_error($SecurityInfo)) {
			// Add the category to the post
			$category_id = get_cat_ID($category); // Replace 'Your Category Name' with the actual category name
			if ($category_id != 0) { // Make sure the category exists
				wp_set_post_categories($SecurityInfo, array($category_id));
			}
		}
	   if( isset( $SecurityInfo ) && is_numeric( $SecurityInfo ) ){
		   //update_user_meta($post_author, 'any_note_created', 'yes');
		   if($name){
				update_post_meta($SecurityInfo, 'name', $name);
		   }
		   if($username){
				update_post_meta($SecurityInfo, 'username', $username);
		   }
		   if($password){
				update_post_meta($SecurityInfo, 'password', $password);
		   }
		   if($account_url){
				update_post_meta($SecurityInfo, 'account_url', $account_url);
		   }
		   if($group){
				update_post_meta($SecurityInfo, 'group', $group);
		   }
		   if($notes){
				update_post_meta($SecurityInfo, 'notes', $notes);
		   }
		   
		   return new WP_REST_Response(
			   array(
				   'success'    => true,
				   'statusCode' => 200,
				   'code'       => 'success',
				   'message'    => __( 'Note successfully Added.', 'jwt-auth' ),
				   'data'       => array('note_id' => $SecurityInfo),
			   ),
			   
		   ); 

	   } else {
		   $massage ='';
		   foreach ($SecurityInfo->errors as $key => $errors) {
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
 	 * Update a Note
 	 * 
 	 */ 
	  public function update_security_note(WP_REST_Request $request)
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

			$master_password = $request->get_param('master_password');
		
			if($master_password != get_user_meta($post_author, 'master_password', true)):
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'error',
						'message'    => __( 'please provide correct master password', 'jwt-auth' ),
						'data'       => array($master_password),
					),
					
				); 
			endif;
		   $note_id = $request->get_param( 'id' ); 
		   $fname = $request->get_param('fname');
		   $username = $request->get_param('username');
		   $password = $request->get_param('password');
		   $account_url = $request->get_param('account_url');
		   $group = $request->get_param('group');
		   $notes = $request->get_param('notes');
		   
			if( ( empty( $note_id ) ) || "security-info" != get_post_type( $note_id ) ) { 
   
			   return new WP_REST_Response(
				   array(
					   'success'    => false,
					   'statusCode' => 403,
					   'code'       => 'jwt_auth_bad_config',
					   'message'    => __( 'Note is not exist.', 'jwt-auth' ),
					   'data'       => array(),
				   ),
				   
			   );
	 
		   } 
   
		   $errors_arr = array();
		   if(empty($fname)){
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
		   $update_args['ID'] = $note_id;
		   $update_args['post_title'] = $fname; //  Title
		   $update_args['post_content'] = $username; // Description
			if(wp_update_post( $update_args )):
				if($name){
					update_post_meta($note_id, 'name', $fname);
			   }
			   if($username){
					update_post_meta($note_id, 'username', $username);
			   }
			   if($password){
					update_post_meta($note_id, 'password', $password);
			   }
			   if($account_url){
					update_post_meta($note_id, 'account_url', $account_url);
			   }
			   if($group){
					update_post_meta($note_id, 'group', $group);
			   }
   
   
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
					   'message'    => __( 'Invalid note Id.', 'jwt-auth' ),
					   'data'       => array(),
				   ),
				   
		   );
		   
   
	  }

	public function get_security_notes(WP_REST_Request $request){
        $API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
        $master_password    = $request->get_param('master_password');

		if($master_password != get_user_meta($post_author, 'master_password', true)):
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'please provide correct master password', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
		endif;

		$category_name = $request->get_param('category');
		$keywords = $request->get_param('keywords');
		$group = $request->get_param('group'); // Replace 'Your Category Name' with the name of your desired category

		$category = get_term_by('name', $category_name, 'category');
		$args = array(
			'post_type' => 'security-info',
			'post_status' => 'publish',
			'posts_per_page' => -1, // Retrieve all posts. You can change this value as needed.
		);

	// Check if the $category_name is set
	if ($category) {
		if ($category) {
			$category_id = $category->term_id;
			$args['cat'] = $category_id;
		}
	}

	// Check if the $group is set
	if ($group) {
		$args['meta_query'] = array(
			array(
				'key' => 'group', // Replace 'your_meta_key' with the actual meta key for the group
				'value' => $group,
				'compare' => '=',
			),
		);
	}

	// Check if the $keywords is set
	if ($keywords) {
		$args['s'] = $keywords;
	}

			$query = new WP_Query($args);

			if ($query->have_posts()) {
				$posts = array();

				while ($query->have_posts()) {
					$query->the_post();

					// Get the category name
					$category_name = get_the_category()[0]->name; // Assuming you want the first category if there are multiple

					// Get the post ID
					$post_id = get_the_ID();
					// Prepare post data
					$post_data = array(
						'id' => $post_id,
						'title' => get_the_title(),
						'content' => get_the_content(),
						'type' => $category_name,

					);

					// Retrieve and include post metadata
					$metadata = get_post_meta(get_the_ID());
					foreach ($metadata as $key => $value) {
						$post_data[$key] = $value[0];
					}

					$posts[] = $post_data;
				}
				wp_reset_postdata(); // Reset post data to ensure other queries work correctly

				// Return the posts as a JSON response
				$response = array(
					'status' => 'success',
					'posts' => $posts,
				);
				$temp_args = array(
					'post_type' => 'security-info',
					'post_status' => 'publish',
					'posts_per_page' => -1, // Retrieve all posts. You can change this value as needed.
				);
				$temp_posts = get_posts($temp_args);
				$groups = array();
				foreach ($temp_posts as $temp_post) {
					$group_value = get_post_meta($temp_post->ID, 'group', true); // Replace 'your_meta_key' with the actual meta key for the group
					if (!empty($group_value)) {
						$groups[] = $group_value;
					}
				}

				// Remove duplicate group values
				$unique_groups = array_unique($groups);
				return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'data'       => array('result' => $posts, 'groups' => $unique_groups)
					),
					
				); 
			} else {

				return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 403,
						'code'       => 'error',
						'message'    => __( 'No posts found in the category', 'jwt-auth' ),
					),
					
				); 
			}
		
       
	}

	/**
 	 * Delete multiple note by id 
 	 * 
 	 */
	  public function delete_security_notes(WP_REST_Request $request)
	  {
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$permission = $API_BaseController->check_user_permission($_SERVER, 'delete');
		$master_password    = $request->get_param( 'master_password' );
		// return new WP_REST_Response(
		// 	array(
		// 		'success'    => false,
		// 		'statusCode' => 403,
		// 		'code'       => 'error',
		// 		'message'    => __( 'please provide correct master password', 'jwt-auth' ),
		// 		'data'       => array($request->get_param('note_ids')),
		// 	),
			
		// ); 
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
		  
		  if($master_password != get_user_meta($post_author, 'master_password', true)):
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'please provide correct master password', 'jwt-auth' ),
					'data'       => array($master_password),
				),
				
			); 
		endif;
		
		  //contect info
		  $note_ids    = $request->get_param( 'note_ids' ); 
		  
		  if( empty( $note_ids) || gettype($note_ids) != 'array' ){
			  return new WP_REST_Response(
				 array(
					 'success'    => false,
					 'statusCode' => 403,
					 'code'       => 'error',
					 'message'    => __( 'Minimum one Security Notes id is required', 'jwt-auth' ),
					 'data'       => '',
				 ),
				 
			 ); 
		  }
  
		  $total_delete_security_info = 0;
  
		  foreach( $note_ids as $note_id ){
			  if( get_post_type($note_id) != "security-info"):
				  continue;
			  endif;
			  if(wp_delete_post($note_id, true)):
			  
				  $total_delete_security_info++;
				  
				  // delete notes
				  $notes = get_posts( 
					 array( 
						 'post_type'   => 'notes',
						 'post_parent' => $note_id,
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
		  if( $total_delete_security_info > 0 ):
			  return new WP_REST_Response(
				 array(
					 'success'    => true,
					 'statusCode' => 200,
					 'code'       => 'success',
					 'message'    => __( 'Item successfully deleted.', 'jwt-auth' ),
					 'data'       => array('total_delete_security_info' => $total_delete_security_info),
				 ),
				 
			 ); 
		  else:
			  return new WP_REST_Response(
				 array(
					 'success'    => false,
					 'statusCode' => 403,
					 'code'       => 'error',
					 'message'    => __( 'Due to technical issue not deleting any item', 'jwt-auth' ),
					 'data'       => $total_delete_security_info,
				 ),
				 
			 ); 
		  endif;
		  
	}

	//get_single_emergency Apis Code 
	public function get_single_security_note(WP_REST_Request $request)
 	{
 		$note_id    = $request->get_param( 'note_id' );  

 		if ( ( empty($note_id) ) || ( 'security-info' != get_post_type($note_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter Security Info ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

 
		$emergency_info = get_post( $note_id );

		if ( empty($emergency_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This Info is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{

	    	$temp_security_info = array();
	    	$temp_security_info = (object) $temp_security_info;
	    	
	    	$temp_security_info->id = $note_id;
	    	$temp_security_info->created_date = $emergency_info->post_date;
	    	$temp_security_info->item_name = $emergency_info->post_title;
	    	$temp_security_info->description = $emergency_info->post_content;
	    	$temp_security_info->author_id = $emergency_info->post_author;

	    	$temp_security_info->name  				=	get_post_meta($note_id, 'name', true);
			$temp_security_info->username  					=	get_post_meta($note_id, 'username', true);
			$temp_security_info->password  					=	get_post_meta($note_id, 'password', true);
			$temp_security_info->account_url  			=	get_post_meta($note_id, 'account_url', true);
			$temp_security_info->group  			=	get_post_meta($note_id, 'group', true);
			$temp_security_info->type = get_the_category($note_id)[0]->name;
	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $note_id,
			    'posts_per_page' => -1,
			);

			$notes = get_posts( 
				array( 
					'post_type'   => 'notes',
					'post_parent' => $note_id,
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
	        	
	        $temp_security_info->notes = $all_notes; 
		 
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Security Detail', 'jwt-auth' ),
						'data'       => $temp_security_info,
					),
					 
				);
		}

 	}

}

new API_SecurityInfoController();
?>
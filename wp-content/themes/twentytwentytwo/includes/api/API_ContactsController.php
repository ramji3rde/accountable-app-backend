<?php
class API_ContactsController extends API_BaseController {

	public function __construct() 
	{  

 	}
 	 
  	/** create new contact for a user like Team Support or any post type */
 	public function create_contact(WP_REST_Request $request)
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
 		$contacts    = $request->get_param( 'contacts' );
 		if( empty( $contacts) || gettype($contacts) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one contact is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		 
 		$user_id = $request->get_param( 'user_id' );
 		$post_id = $request->get_param( 'post_id' );
 		$contact_category = $request->get_param( 'contact_category' );
 		$contact_category = ( "user" == $contact_category ) ? "user" : "post";
 		$author = $request->get_param( 'author' );
 		$title = '';
 		$errors_arr = array();
 		if(empty($contacts)){

 			$errors_arr[] = __( 'Please enter any contact.', 'jwt-auth' ); 
		}

		
		if(empty($author)){

 			$errors_arr[] = __( 'Author id is required.', 'jwt-auth' ); 
		}
		if(empty($contact_category)){

 			$errors_arr[] = __( 'Contact category id is required.', 'jwt-auth' ); 
		}
		if( "user" == $contact_category ){
			if(empty($user_id)){

	 			$errors_arr[] = __( 'Please enter user id.', 'jwt-auth' ); 
			}
			$user_info = get_user_by("ID", $user_id );
			if(!isset($user_info->ID)){

	 			$errors_arr[] = __( 'Please enter valid user id.', 'jwt-auth' ); 
			}
		}else{
			if(empty($post_id)){

	 			$errors_arr[] = __( 'Please enter post id.', 'jwt-auth' ); 
			}
			$post_type = get_post_type($post_id);
			 
			if( $post_type == false ){

	 			$errors_arr[] = __( 'Please enter valid post id.', 'jwt-auth' ); 
			}
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
	    $user_post_id = 0;
	    if( "user" == $contact_category ){
			$user_post_id = $user_id;
			$title = $user_info->user_login. " ".$user_info->display_name;
		}else{
			$user_post_id = $post_id;
			$title = get_the_title($post_id) ." ( ". $post_type ." )";
		}
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
	    $total_created_contacts = array();
	    foreach( $contacts as $contact ){
	    	if( isset( $contact['first_name'] ) ){
	    		$first_name = $contact['first_name'];
	    		$last_name = $contact['last_name'];
	    		$title = $contact['title'];
	    		$primary_phone = $contact['primary_phone'];
	    		$primary_phone_type = $contact['primary_phone_type'];
	    		$secondary_phone = $contact['secondary_phone'];
	    		$secondary_phone_type = $contact['secondary_phone_type'];
	    		$email = $contact['email'];
	    		 $Reg = wp_insert_post(array(
					'post_title'    => $title,
					'post_type'     => 'contacts',
					'post_content'  => $title,
					'post_status'   => 'publish',
					'post_author'   => $post_author,
					 
				));
		    	if( isset( $Reg ) && is_numeric( $Reg ) ){
		    		$temp = array('contact_id' => $Reg);
		    		array_push( $total_created_contacts, $temp);
					update_post_meta($Reg, 'contact_category', $contact_category);
					update_post_meta($Reg, 'user_post_id', $user_post_id);

					update_post_meta($Reg, 'first_name', $first_name);
					update_post_meta($Reg, 'last_name', $last_name);
					update_post_meta($Reg, 'title', $title);
					update_post_meta($Reg, 'primary_phone', $primary_phone);
					update_post_meta($Reg, 'primary_phone_type', $primary_phone_type);
					update_post_meta($Reg, 'secondary_phone', $secondary_phone);
					update_post_meta($Reg, 'secondary_phone_type', $secondary_phone_type);
					update_post_meta($Reg, 'email', $email);
					 
					 
				}
	    	}
	    	
	    }
	    
	   
		if( count($total_created_contacts ) > 0  ){

			// create tenant user for tenant post type
			$user_created_resposne = array();
		
			if( ($contact_category == 'post' ) && ( get_post_type( $user_post_id ) == 'tenants') ){
				$API_UserControler = new API_UserControler();
				$user_created_resposne = $API_UserControler->create_user_for_tenant_without_api($user_post_id);
				 
			}
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Contacts sucessfully created.', 'jwt-auth' ),
					'data'       => $total_created_contacts,
					'user_created_resposne' => $user_created_resposne
				),

			);
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Contact is not creating due to technical issue.', 'jwt-auth' ),
				'data'       => '',
			),
			
		); 
	}
	public function delete_all_notes_by_user_post_id( $user_post_id, $contact_category ){
		$contacts = get_posts( 
				array( 
					'fields' => "ids",
					'post_type'   => 'contacts',
					'posts_per_page' => -1,
					'post_status' => array('publish'),
					'meta_query'  => array(
						array(
							'key'       => 'user_post_id',
							'value'          => $user_post_id,
							'compare' => '=',
							),
						array(
							'key'       => 'contact_category',
							'value'          => $contact_category,
							'compare' => '=',
							)
					)
				) 
			);
 		$total_delete_contacts = 0; 
 		foreach( $contacts as $contact_id ){
 			if( get_post_type($contact_id) != "contacts"):
 				continue;
 			endif;
 			if(wp_delete_post($contact_id, true)):
 				 
 				$total_delete_contacts++;
 			endif;
 		}
 		$return_array = array('total_deleted_contacts' => $total_delete_contacts, 'contacts_ids' => $contacts_ids);
 		return $return_array; 
	}
	/** Delete multiple contacts */
	public function delete_contact(WP_REST_Request $request){
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
		$contact_ids    = $request->get_param( 'contact_ids' );
		if( empty( $contact_ids) || gettype($contact_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one contact id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		$total_delete_contacts = 0;
 		foreach( $contact_ids as $contact_id ){
 			if( get_post_type($contact_id) != "contacts"):
 				continue;
 			endif;
 			if(wp_delete_post($contact_id, true)):
 				 
 				$total_delete_contacts++;
 			endif;
 		}
 		if( $total_delete_contacts > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'contacts successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_deleted_contacts' => $total_delete_contacts),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any contact', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
	}

	/** update exists contact for a user or post */
 	public function update_contact(WP_REST_Request $request)
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
 		$contacts = $request->get_param('contacts');
 		if( empty( $contacts) || gettype($contacts) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one contact is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		
 		 
 	 
 		$errors_arr = array();
 		if(empty($contacts)){

 			$errors_arr[] = __( 'Please enter any contact.', 'jwt-auth' ); 
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
	    
	   $total_updated_contacts = array();
	   $not_updated_contacts = array();
	   foreach( $contacts as $contact ){
	   		$contact_id = $contact['contact_id'];
			$first_name = $contact['first_name'];
			$last_name = $contact['last_name'];
			$title = $contact['title'];
			$primary_phone = $contact['primary_phone'];
			$primary_phone_type = $contact['primary_phone_type'];
			$secondary_phone = $contact['secondary_phone'];
			$secondary_phone_type = $contact['secondary_phone_type'];
			$email = $contact['email'];
 			 if( get_post_type($contact_id) != "contacts" ){
 			 	$temp = array( "contact_id" => $contact_id, 'reason' => "this id is not a contact id");
 			 	array_push( $not_updated_contacts, $temp);
 			 	continue;
 			 }
			update_post_meta($contact_id, 'first_name', $first_name);
			update_post_meta($contact_id, 'last_name', $last_name);
			update_post_meta($contact_id, 'title', $title);
			update_post_meta($contact_id, 'primary_phone', $primary_phone);
			update_post_meta($contact_id, 'primary_phone_type', $primary_phone_type);
			update_post_meta($contact_id, 'secondary_phone', $secondary_phone);
			update_post_meta($contact_id, 'secondary_phone_type', $secondary_phone_type);
			update_post_meta($contact_id, 'email', $email);
			$temp = array( "contact_id" => $contact_id);
			array_push( $total_updated_contacts, $temp); 
		     
	   }
		if( count( $total_updated_contacts) > 0 ){
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'contact sucessfully updated.', 'jwt-auth' ),
					'data'       => array('total_updated_contact' => $total_updated_contacts, 'total_not_updated_contacts' => $not_updated_contacts)
				),

			);
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'contact is not updating.', 'jwt-auth' ),
				'data'       => '',
			),
			
		); 
	}
	public function get_contacts( $user_post_id, $contact_category ){
	 
		$contacts = get_posts( 
				array( 
					'fields' => "ids",
					'post_type'   => 'contacts',
					'posts_per_page' => -1,
					'post_status' => array('publish'),
					'meta_query'  => array(
						array(
							'key'       => 'user_post_id',
							'value'          => $user_post_id,
							'compare' => '=',
							),
						array(
							'key'       => 'contact_category',
							'value'          => $contact_category,
							'compare' => '=',
							)
					)
				) 
			);
		$contact_list = array();
		 
		foreach( $contacts as $contact_id ){
			$temp = array();
			$temp = (object) $temp;
			$temp->contact_id = $contact_id;

			$temp->contact_category = get_post_meta($contact_id, "contact_category", true);
			$temp->user_post_id = get_post_meta($contact_id, "user_post_id", true);;
			$temp->first_name = get_post_meta($contact_id, "first_name", true);
			$temp->last_name = get_post_meta($contact_id, "last_name", true);
			$temp->title = get_post_meta($contact_id, "title", true);
			$temp->primary_phone = get_post_meta($contact_id, "primary_phone", true);
			$temp->primary_phone_type = get_post_meta($contact_id, "primary_phone_type", true);
			$temp->secondary_phone = get_post_meta($contact_id, "secondary_phone", true);
			$temp->secondary_phone_type = get_post_meta($contact_id, "secondary_phone_type", true);
			$temp->email = get_post_meta($contact_id, "email", true);
			array_push( $contact_list,  $temp );
		}
		return $contact_list;
	}
	public function get_all_contacts(WP_REST_Request $request)
 	{
 		$user_id = $request->get_param( 'user_id' );
 		$post_id = $request->get_param( 'post_id' );
 		$contact_category = $request->get_param( 'contact_category' );
 		$contact_category = ( "user" == $contact_category ) ? "user" : "post";
 		$user_post_id = ( "user" == $contact_category ) ? $user_id : $post_id;
 		$errors_arr = array();
 		if(empty($contact_category)){

 			$errors_arr[] = __( 'Contact category id is required.', 'jwt-auth' ); 
		}
		if( "user" == $contact_category ){
			if(empty($user_id)){

	 			$errors_arr[] = __( 'Please enter user id.', 'jwt-auth' ); 
			}
			$user_info = get_user_by("ID", $user_id );
			if(!isset($user_info->ID)){

	 			$errors_arr[] = __( 'Please enter valid user id.', 'jwt-auth' ); 
			}
		}else{
			if(empty($post_id)){

	 			$errors_arr[] = __( 'Please enter post id.', 'jwt-auth' ); 
			}
			$post_type = get_post_type($post_id);
			 
			if( $post_type == false ){

	 			$errors_arr[] = __( 'Please enter valid post id.', 'jwt-auth' ); 
			}
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
 		$contacts = $this->get_contacts( $user_post_id, $contact_category );
 		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'All contacts', 'jwt-auth' ),
					'data'       => $contacts,
				),
				
			); 
 		
 	}

}

new API_ContactsController();
?>
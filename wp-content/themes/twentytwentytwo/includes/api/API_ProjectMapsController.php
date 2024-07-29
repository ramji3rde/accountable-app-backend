<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class API_ProjectMapsController extends API_BaseController {

	public function __construct() 
	{  
		$this->init_action();
	}

	public function init_action()
	{
		
	}  

	public function get_all_projects_maps_list(WP_REST_Request $request)
	{
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_project_map_created = get_user_meta($post_author, 'any_project_map_created', true); 
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
    	
    	 $args = array( 
    	 	'author' => $post_author,
	        'post_type' => 'project_maps',
	        'post_status' => 'publish',
	        'paged' => $paged, 
	        'orderby' => $orderby,
			'order'   => $order,
	        'posts_per_page' => $posts_per_page, 
	        
	    );
    	
    	
		$project_maps = array();
    	$the_query = new WP_Query( $args );
    	$total_project_maps = $the_query->found_posts;
	        
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$map_id = get_the_ID();
	        $map_info = get_post( $map_id );

	        if ( empty($map_info) ) :
	        	continue;
	        endif;
	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $map_id,
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
			$map_info->photos = $photos;
			array_push( $project_maps, $map_info);
	    endwhile;


		return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'All Project maps list', 'jwt-auth' ),
						'posts_per_page' => $posts_per_page,
						'paged' => $paged,
						'total_project_maps' => count($project_maps),
						'data'       => $project_maps,
						'any_record_created' => $any_project_map_created,
						
					),
					
				); 

	}

	public function create_new_project_map(WP_REST_Request $request){
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
		$map_name    = $request->get_param( 'map_name' );
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$errors_arr = array();
		if(empty($map_name)){ 
			$errors_arr[] = __( 'Please enter project map name.', 'jwt-auth' ); 
		}
		
		if(empty($post_author)){ 
			$errors_arr[] = __( 'Invalid loggedin user.', 'jwt-auth' ); 
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
	   
	    $map_args = array(
			'post_title'     => $map_name,
			'post_type'     => 'project_maps',
			'post_content'     => $map_name,
			'post_status'   => 'publish',
			'post_author'   => $post_author,
			 
		);
	    
	    $mapRes = wp_insert_post( $map_args );
		if( isset( $mapRes ) && is_numeric( $mapRes ) ){
			update_user_meta($post_author, 'any_project_map_created', 'yes');
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Project map successfully created', 'jwt-auth' ),
					'data'       => array('map_id' => $mapRes),
				),
				
			);
		}else{
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
	

	public function update_project_map_by_id( WP_REST_Request $request ){
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
		$map_id = $request->get_param( 'map_id' );
		$map_name    = $request->get_param( 'map_name' );
		
		if( 'project_maps' != get_post_type( $map_id ) ){
 			 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid map id.', 'jwt-auth' ),
					'data'       => array(),
				),
			);
 		}
		$errors_arr = array();
		if(empty($map_name)){ 
			$errors_arr[] = __( 'Please enter project map name.', 'jwt-auth' ); 
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
	    $update_args['ID'] = $map_id;
		$update_args['post_title'] = $map_name; // Post Title
		$update_args['post_content'] = $map_name; // post Description

 		if(wp_update_post( $update_args )):
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
					'message'    => __( 'Project map information updation failed.', 'jwt-auth' ),
					'data'       => array(),
				),
				
		);
	}

	public function get_single_project_map( WP_REST_Request $request ){
		$map_id = $request->get_param( 'map_id' );
		if( 'project_maps' != get_post_type( $map_id ) ){
 			 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project map id.', 'jwt-auth' ),
					'data'       => array(),
				),
			);
 		}
 		$map_info = get_post( $map_id );
 		if ( empty($map_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This project map is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{


 	    	$map_info->map_created = get_the_date( "d/m/y" , $map_id);
	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $map_id,
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
			$map_info->photos = $photos;
		 
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Project maps Details', 'jwt-auth' ),
						'data'       => $map_info,
					),
					 
				);
		}
	}
	public function delete_project_maps_by_ids(WP_REST_Request $request){
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
 		$API_PhotosController = new API_PhotosController();
		$map_ids = $request->get_param( 'map_ids' );
		if( empty( $map_ids) || gettype($map_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one project map id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		$total_delete_project_maps = 0;
 		foreach( $map_ids as $map_id ){
 			if( get_post_type($map_id) != "project_maps"):
 				continue;
 			endif;
 			if(wp_delete_post($map_id, true)):
 				// delete attachment/photos
 				$attachment_args = array(
				    'post_type' => "attachment",
				    'post_parent' => $map_id,
				);
				$attachments = get_posts($attachment_args);
				foreach( $attachments  as $attachment):
					$API_PhotosController->ballon_delete_media_from_s3($attachment->guid);
				    wp_delete_attachment($attachment->ID, true);
				endforeach;
 				$total_delete_project_maps++;
 			endif;
 		}
 		if( $total_delete_project_maps > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Project maps successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_delete_project_maps' => $total_delete_project_maps),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any project map', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;

	}
	
}

new API_ProjectMapsController(); 
?>
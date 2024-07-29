<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class API_TenantProjectsController extends API_BaseController {

	public function __construct() 
	{  
		$this->init_action();
	}

	public function init_action()
	{
		
	}  
	/**
	 *  Get all project for a tenant ( Created by tenant user )
	 */ 
	public function get_all_projects_list(WP_REST_Request $request)
	{
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_project_created = get_user_meta($post_author, 'any_project_created', true); 
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
    	
    	 $args = array( 
    	 	// 'author' => $post_author,
	        'post_type' => 'projects',
	        'post_status' => array( 'publish', 'pending', 'rejected'),
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
				            'value' => $post_author,
				            'compare' => "="
				        )
				    );
    		else:
    		$args['meta_query'] = array(
    				'relation' => 'AND',
				      
				         array(
				         	'key' => 'real_post_author',
				            'value' => $post_author,
				            'compare' => "="
				        )
				    );
    	endif;
		$projects = array();
		$total_projects = 0;
    	$the_query = new WP_Query( $args );
    	$total_projects = $the_query->found_posts;
	        
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$pro_id = get_the_ID();
	        $pro_info = get_post( $pro_id );

	        if ( empty($pro_info) ) :
	        	continue;
	        endif;
	        $services = get_post_meta($pro_id, 'services', true);
        	$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
	        $pro_info->project_created = get_post_meta($pro_id, 'first_name', true);
	        $pro_info->project_name = get_post_meta($pro_id, 'project_name', true);
	        $pro_info->project_date = get_post_meta($pro_id, 'project_date', true);
	        $pro_info->project_detail = get_post_meta($pro_id, 'project_detail', true);
	        $pro_info->status = get_post_meta($pro_id, 'status', true);
	        $pro_info->services = $services;
	        $comments = get_comments(array(
					        'post_id' => $pro_id,
					        'number' =>  -1 )
					        );
    	    $pro_info->toal_comments = count($comments);
	        // $pro_info->total_bids = count($this->get_a_project_bids( $pro_id));
	        // $pro_info->total_bid_list = $this->get_a_project_bids( $pro_id);
			array_push( $projects, $pro_info);
	    endwhile;


		return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'All Projects list', 'jwt-auth' ),
						'posts_per_page' => $posts_per_page,
						'paged' => $paged,
						'total_projects' => $total_projects,
						'data'       => $projects,
						'any_record_created' => $any_project_created,
						
					),
					
				); 

	}
	/**
	 *  Create a project for tenant user
	 */ 
	public function create_new_project(WP_REST_Request $request){
		$project_name    = $request->get_param( 'project_name' );
		$project_date    = $request->get_param( 'project_date' ); 
		$project_detail    = $request->get_param( 'project_detail' ); 
		$status    = $request->get_param( 'status' ); 
		$services    = $request->get_param( 'services' );
		$services = ( ''  != $services ) ? $services : 'zzzzzzzzzz';
		$author = $request->get_param('author');

		$errors_arr = array();
		if(empty($project_name)){ 
			$errors_arr[] = __( 'Please enter project name.', 'jwt-auth' ); 
		}
		
		if(empty($project_date)){ 
			$errors_arr[] = __( 'Please enter project date.', 'jwt-auth' ); 
		}
		
		if(empty($status)){ 
			$errors_arr[] = __( 'Please enter project status.', 'jwt-auth' ); 
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
	    $API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
	    $projects_args = array(
			'post_title'     => $project_name,
			'post_type'     => 'projects',
			'post_content'     => $project_detail,
			'post_status'   => 'pending',
			'post_author'   => $post_author,
			 
		);
	    $tenant_post_id = get_user_meta( $post_author, '_tenant_post_id', true );
	    $app_admin_id = get_post_field ('post_author', $tenant_post_id);

	    $projectReg = wp_insert_post( $projects_args );
		if( isset( $projectReg ) && is_numeric( $projectReg ) ){
			update_user_meta($post_author, 'any_project_created', 'yes');
			update_post_meta($projectReg, 'real_post_author', $post_author);
			update_post_meta($projectReg, 'project_name', $project_name);
			update_post_meta($projectReg, 'project_detail', $project_detail);
			update_post_meta($projectReg, 'project_date', $project_date);
			update_post_meta($projectReg, 'project_date_strtotime', strtotime($project_date));
			update_post_meta($projectReg, 'status', $status);
			update_post_meta($projectReg, 'services', $services);
			update_post_meta($projectReg, 'requested_contractors', array());
			update_post_meta($projectReg, 'requested_tenants', array());
			update_post_meta($projectReg, 'not_interested_contractor', array());
			update_post_meta($projectReg, '_app_admin_id', $app_admin_id );

			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Project successfully created', 'jwt-auth' ),
					'data'       => array('project_id' => $projectReg),
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
	
 

	/**
	 * Update a project for tenant user
	 * 
	 */ 
	public function update_project_by_id( WP_REST_Request $request ){
		$pro_id = $request->get_param( 'project_id' );
		$project_name    = $request->get_param( 'project_name' );
		$project_date    = $request->get_param( 'project_date' ); 
		$project_detail  = $request->get_param( 'project_detail' ); 
		$status    = $request->get_param( 'status' );
		$contractors    = $request->get_param( 'contractors' );
		$tenants    = $request->get_param( 'tenants' ); 
		$services    = $request->get_param( 'services' );
		$services = ( ''  != $services ) ? $services : 'zzzzzzzzzz';
		
		if( 'projects' != get_post_type( $pro_id ) ){
 			 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project id.', 'jwt-auth' ),
					'data'       => array(),
				),
			);
 		}
		$errors_arr = array();
		if(empty($project_name)){ 
			$errors_arr[] = __( 'Please enter project name.', 'jwt-auth' ); 
		}
		
		if(empty($project_date)){ 
			$errors_arr[] = __( 'Please enter project date.', 'jwt-auth' ); 
		}
		
		if(empty($status)){ 
			$errors_arr[] = __( 'Please enter project status.', 'jwt-auth' ); 
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
	    $update_args['ID'] = $pro_id;
		$update_args['post_title'] = $project_name; // Post Title
		$update_args['post_content'] = $project_detail; // post Description

 		if(wp_update_post( $update_args )):
 			update_post_meta($pro_id, 'project_name', $project_name);
			update_post_meta($pro_id, 'project_detail', $project_detail);
			update_post_meta($pro_id, 'project_date', $project_date);
			update_post_meta($pro_id, 'project_date_strtotime', strtotime($project_date));
			update_post_meta($pro_id, 'status', $status);
			update_post_meta($pro_id, 'services', $services);
			// update_post_meta($pro_id, 'requested_contractors', $contractors);
			// update_post_meta($pro_id, 'requested_tenants', $tenants);
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
					'message'    => __( 'Project information updation failed.', 'jwt-auth' ),
					'data'       => array(),
				),
				
		);
	}

	public function get_single_project( WP_REST_Request $request ){
		$pro_id = $request->get_param( 'project_id' );
		$contractor_id = $request->get_param( 'contractor_id' ); // assigned contractor. this field is not required
		$contractor_id = (int) $contractor_id;
		if( 'projects' != get_post_type( $pro_id ) ){
 			 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project id.', 'jwt-auth' ),
					'data'       => array(),
				),
			);
 		}
 		$pro_info = get_post( $pro_id );
 		if ( empty($pro_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This project is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{


 	    	$pro_info->project_created = get_the_date( "d/m/y" , $pro_id);
 	    	$services = get_post_meta($pro_id, 'services', true);
        	$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
	        $pro_info->project_url = site_url(base64_encode( $pro_id));
	        $pro_info->project_name = get_post_meta($pro_id, 'project_name', true);
	        $pro_info->project_date = get_post_meta($pro_id, 'project_date', true);
	        $pro_info->project_detail = get_post_meta($pro_id, 'project_detail', true);
	        $pro_info->status = get_post_meta($pro_id, 'status', true);
	        $pro_info->services = $services;
            
	        $comments = get_comments(array(
					        'post_id' => $pro_id,
					        'number' =>  -1 )
					        );
    	    $pro_info->comments = $comments;      
	        
	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $pro_id,
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
			$pro_info->photos = $photos;
		 
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Project Details', 'jwt-auth' ),
						'data'       => $pro_info,
					),
					 
				);
		}
	}

	/**
	 *  Get all project for a tenant ( Created by tenant user ). This API for admin
	 */ 
	public function admin_get_all_tenant_projects_list(WP_REST_Request $request)
	{
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_project_created = get_user_meta($post_author, 'any_project_created', true); 
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
	        'post_type' => 'projects',
	        'post_status' => array( 'publish', 'pending', 'rejected'),
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
    	// echo "<pre>";
    	// print_r($args);
    	// echo "</pre>";
		$projects = array();
		$total_projects = 0;
    	$the_query = new WP_Query( $args );
    	$total_projects = $the_query->found_posts;
	        
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$pro_id = get_the_ID();
	        $pro_info = get_post( $pro_id );

	        if ( empty($pro_info) ) :
	        	continue;
	        endif;
	        $services = get_post_meta($pro_id, 'services', true);
        	$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ;
	        $pro_info->project_created = get_post_meta($pro_id, 'first_name', true);
	        $pro_info->project_name = get_post_meta($pro_id, 'project_name', true);
	        $pro_info->project_date = get_post_meta($pro_id, 'project_date', true);
	        $pro_info->project_detail = get_post_meta($pro_id, 'project_detail', true);
	        $pro_info->status = get_post_meta($pro_id, 'status', true);
	        $pro_info->services = $services;
	        // $pro_info->total_bids = count($this->get_a_project_bids( $pro_id));
	        // $pro_info->total_bid_list = $this->get_a_project_bids( $pro_id);
			array_push( $projects, $pro_info);
	    endwhile;


		return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'All Projects list', 'jwt-auth' ),
						'posts_per_page' => $posts_per_page,
						'paged' => $paged,
						'total_projects' => $total_projects,
						'data'       => $projects,
						'any_record_created' => $any_project_created,
						
					),
					
				); 

	}

	/**
	 * admin will update a tenant requested project
	 * 
	 */ 
	public function addmin_update_requested_project( WP_REST_Request $request ){
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
		$pro_id = $request->get_param( 'project_id' );
		$project_name    = $request->get_param( 'project_name' );
		$project_date    = $request->get_param( 'project_date' ); 
		$project_detail  = $request->get_param( 'project_detail' ); 
		$status    = $request->get_param( 'status' );
	 	$project_status =  $request->get_param( 'project_status' ); // default pending
	 	if('' == trim( $project_status ) ){
	 		$project_status = 'pending';
	 	}
		 
		$services    = $request->get_param( 'services' );
		$services = ( ''  != $services ) ? $services : 'zzzzzzzzzz';
		
		if( 'projects' != get_post_type( $pro_id ) ){
 			 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project id.', 'jwt-auth' ),
					'data'       => array(),
				),
			);
 		}
		$errors_arr = array();
		if(empty($project_name)){ 
			$errors_arr[] = __( 'Please enter project name.', 'jwt-auth' ); 
		}
		
		if(empty($project_date)){ 
			$errors_arr[] = __( 'Please enter project date.', 'jwt-auth' ); 
		}
		
		if(empty($status)){ 
			$errors_arr[] = __( 'Please enter project status.', 'jwt-auth' ); 
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
	    $real_post_author = get_post_meta( $pro_id, 'real_post_author', true );
	    $update_args = array();
	    $update_args['ID'] = $pro_id;
		$update_args['post_title'] = $project_name; // Post Title
		$update_args['post_content'] = $project_detail; // post Description
		$update_args['post_status'] = $project_status;
		$update_args['post_author'] = $real_post_author;
		if( 'publish' == $project_status ){
			$update_args['post_author'] = $post_author;
		}
 		if(wp_update_post( $update_args )):
 			update_post_meta($pro_id, 'project_name', $project_name);
			update_post_meta($pro_id, 'project_detail', $project_detail);
			update_post_meta($pro_id, 'project_date', $project_date);
			update_post_meta($pro_id, 'project_date_strtotime', strtotime($project_date));
			update_post_meta($pro_id, 'status', $status);
			update_post_meta($pro_id, 'services', $services);
			// update_post_meta($pro_id, 'requested_contractors', $contractors);
			// update_post_meta($pro_id, 'requested_tenants', $tenants);
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
					'message'    => __( 'Project information updation failed.', 'jwt-auth' ),
					'data'       => array(),
				),
				
		);
	}

	public function delete_tenant_project_by_ids(WP_REST_Request $request){
		$API_BaseController = new API_BaseController();
 		$permission = $API_BaseController->check_user_permission($_SERVER, 'delete');
 		if( $permission ):
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
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		
 		$API_PhotosController = new API_PhotosController();
		$project_ids = $request->get_param( 'project_ids' );
		if( empty( $project_ids) || gettype($project_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one project id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		$total_delete_projects = 0;
 		$error_message =  'Due to technical issue not deleting any project';
 		foreach( $project_ids as $project_id ){
 			if( get_post_type($project_id) != "projects"):
 				continue;
 			endif;
 			$author_id = (int) get_post_field( 'post_author', $project_id );
 			if( $author_id != $post_author):
 				$error_message =  'You can not delete this project.';
 				continue;

 			endif;
 			if(wp_delete_post($project_id, true)):
 				// delete attachment/photos
 				$attachment_args = array(
				    'post_type' => "attachment",
				    'post_parent' => $project_id,
				    'posts_per_page' => -1,
				);
				$attachments = get_posts($attachment_args);
				foreach( $attachments  as $attachment):
					$API_PhotosController->ballon_delete_media_from_s3($attachment->guid);
				    wp_delete_attachment($attachment->ID, true);
				endforeach;
 				$total_delete_projects++;
 			endif;
 		}
 		if( $total_delete_projects > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Project successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_delete_projects' => $total_delete_projects),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __($error_message, 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;

	}
	
}

new API_TenantProjectsController(); 
?>
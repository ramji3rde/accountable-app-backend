<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class API_ProjectsController extends API_BaseController {

	public function __construct() 
	{  
		$this->init_action();
	}

	public function init_action()
	{
		
	}  

	public function get_all_projects_list(WP_REST_Request $request)
	{
		$post_author = $this->custom_validate_token($_SERVER);
		$any_project_created = get_user_meta($post_author, 'any_project_created', true); 
		   	
    	 $args = array( 
    	 	'author' => $post_author,
	        'post_type' => 'projects',
	        'post_status' => 'publish',
	        'posts_per_page' => -1, 
	        
	    );
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
	        $pro_info->total_bids = count($this->get_a_project_bids( $pro_id));
	        $pro_info->total_bid_list = $this->get_a_project_bids( $pro_id);
			array_push( $projects, $pro_info);
	    endwhile;


		return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'All Projects list', 'jwt-auth' ),
						'total_projects' => $total_projects,
						'data'       => $projects,
						'any_record_created' => $any_project_created,
						
					),
					
				); 

	}
	public function search_projects_list(WP_REST_Request $request)
	{
		$post_author = $this->custom_validate_token($_SERVER);
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
    	 	'author' => $post_author,
	        'post_type' => 'projects',
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
	        $pro_info->total_bids = count($this->get_a_project_bids( $pro_id));
	        $pro_info->total_bid_list = $this->get_a_project_bids( $pro_id);
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

	public function create_new_project(WP_REST_Request $request){
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
			'post_status'   => 'publish',
			'post_author'   => $post_author,
			 
		);
	    
	    $projectReg = wp_insert_post( $projects_args );
		if( isset( $projectReg ) && is_numeric( $projectReg ) ){
			update_user_meta($post_author, 'any_project_created', 'yes');
			update_post_meta($projectReg, 'project_name', $project_name);
			update_post_meta($projectReg, 'project_detail', $project_detail);
			update_post_meta($projectReg, 'project_date', $project_date);
			update_post_meta($projectReg, 'project_date_strtotime', strtotime($project_date));
			update_post_meta($projectReg, 'status', $status);
			update_post_meta($projectReg, 'services', $services);
			update_post_meta($projectReg, 'requested_contractors', array());
			update_post_meta($projectReg, 'requested_tenants', array());
			update_post_meta($projectReg, 'not_interested_contractor', array());

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
	
	public function validate_project_bid(WP_REST_Request $request){
		
		$project_id = $request->get_param( 'project_id' );
		$contractor_id = $request->get_param( 'contractor_id' );
		$errors_arr = array();
		if(empty($project_id)){ 
			$errors_arr[] = __( 'Please enter project id.', 'jwt-auth' ); 
		}
		
		if(empty($contractor_id)){ 
			$errors_arr[] = __( 'Please enter contractor id.', 'jwt-auth' ); 
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
	    if( 'projects' != get_post_type( $project_id ) ){
 			$errors_arr[] = __( 'Invalid project id.', 'jwt-auth' ); 
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
 		$user = get_user_by( 'id',$contractor_id );
		if( !$user)
		{
		    return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid contractor id.', 'jwt-auth' ),
					'data'       => array(),
				),
			);
		}
	    $not_int_cont = get_post_meta( $project_id, 'not_interested_contractor', true);
	    if( 'array' != gettype($not_int_cont ) ):
	    	$not_int_cont = array();
	    endif;
 		 // validate = contractor already told 'not interested' or already created bid
	    if( in_array( $contractor_id , $not_int_cont ) ):
	    	// means contractor already told "I not interested"
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'You have already sent not interested request.', 'jwt-auth' ),
					'data'       => array('redirect' => true),
				),
				
			);
	    endif;
	    $args1 = array(
	    	'fields'         => 'ids',
		    'numberposts' => -1,
		    'post_type' => 'project_bids',
		    'post_status' => 'publish',
		    'meta_query' => array(
		        array(
		            'key'       => 'project_id',
		            'value'     =>  $project_id,
		            'compare' => '='
		        ),
		         array(
		            'key'       => 'contractor_id',
		            'value'     =>  $contractor_id,
		            'compare' => '='
		        ),
		    )
		);
		$all_bids = get_posts( $args1 );
		if( ! empty( $all_bids ) ):
			// means contractor already created bid
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'You have already created bid.', 'jwt-auth' ),
					'data'       => array('redirect' => true),
				),
				
			);
		endif;
 		
		
		return new WP_REST_Response(
			array(
				'success'    => true,
				'statusCode' => 200,
				'code'       => 'success',
				'message'    => __( 'You can create a bid.', 'jwt-auth' ),
				'data'       => '',
			),
			
		); 

	}
	public function update_project_by_id( WP_REST_Request $request ){
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
            // required document conditions
            
            $temp_key = 'contractor_'.$contractor_id.'_required_docs';
            $contractor_required_docs = get_post_meta( $pro_id, $temp_key, true );
            if( 'array' != gettype($contractor_required_docs) ):
            	$contractor_required_docs = array();
            endif;
            $pro_info->contractor_required_docs = $contractor_required_docs;
	        $pro_info->total_bids = count($this->get_a_project_bids( $pro_id));
	        // get all requested/assign tenants
	        $tenant_ids = get_post_meta($pro_id, 'requested_tenants', true);
	        $requested_tenants = array();

	        foreach( $tenant_ids as $tenant_id ){
	        	if( 'tenants' == get_post_type( $tenant_id) ){
        		    $temp = array();
        		    $temp = (object) $temp;
        			// $t_info = get_post( $tenant_id );
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
	        		array_push( $requested_tenants, $temp);
	        	}
	        
	        }
	        // get all requested/assign contractors
	        $contractor_ids = get_post_meta($pro_id, 'requested_contractors', true);
	        $requested_contractors = array();

	        foreach( $contractor_ids as $contractor_id ){
	        	$c_info = get_user_by( 'ID', $contractor_id );
	        	if( !empty( $c_info ) ){
        		    $temp = array();
        		    $temp = (object) $temp;
        			// // $t_info = get_post( $tenant_id );
        			$temp->ID = $c_info->ID;
        			$temp->user_login = $c_info->user_login;
        			$temp->user_nicename = $c_info->user_nicename;
        			$temp->user_email = $c_info->user_email;
        			$temp->first_name = $c_info->first_name;
        			$temp->last_name = $c_info->last_name;
        			$temp->primary_fname = get_post_meta($contractor_id, 'primary_fname', true);
        			$temp->primary_lname = get_post_meta($contractor_id, 'primary_lname', true); 
        			$temp->primary_title = get_post_meta($contractor_id, 'primary_title', true); 
        			$temp->company_name = get_post_meta($contractor_id, 'company_name', true); 
        			$temp->primary_phone_type = get_post_meta($contractor_id, 'primary_phone_type', true); 
        			$temp->primary_phone = get_post_meta($contractor_id, 'primary_phone', true); 
        			$temp->primary_contact_email = get_post_meta($contractor_id, 'primary_contact_email', true); 
        			$temp->city = get_post_meta($contractor_id, 'city', true); 
	        		array_push( $requested_contractors, $temp);
	        	}
	        
	        }
	         // get all not interested contractors
	        $not_interested_contractor = get_post_meta($pro_id, 'not_interested_contractor', true);
	        $not_interested_contractors = array();

	        foreach( $not_interested_contractor as $contractor_id ){
	        	$c_info = get_user_by( 'ID', $contractor_id );
	        	if( !empty( $c_info ) ){
        		    $temp = array();
        		    $temp = (object) $temp;
        			// // $t_info = get_post( $tenant_id );
        			$temp->ID = $c_info->ID;
        			$temp->user_login = $c_info->user_login;
        			$temp->user_nicename = $c_info->user_nicename;
        			$temp->user_email = $c_info->user_email;
        			$temp->primary_fname = get_post_meta($contractor_id, 'primary_fname', true);
        			$temp->primary_lname = get_post_meta($contractor_id, 'primary_lname', true); 
        			$temp->primary_title = get_post_meta($contractor_id, 'primary_title', true); 
        			$temp->company_name = get_post_meta($contractor_id, 'company_name', true); 
        			$temp->primary_phone_type = get_post_meta($contractor_id, 'primary_phone_type', true); 
        			$temp->primary_phone = get_post_meta($contractor_id, 'primary_phone', true); 
        			$temp->primary_contact_email = get_post_meta($contractor_id, 'primary_contact_email', true); 
        			$temp->city = get_post_meta($contractor_id, 'city', true); 
	        		array_push( $not_interested_contractors, $temp);
	        	}
	        
	        }
	    	$pro_info->not_interested_contractor = $not_interested_contractors;
	    	$pro_info->requested_contractors = $requested_contractors;
	    	$pro_info->requested_tenants = $requested_tenants;

	    	$total_bids = $this->get_a_project_bids( $pro_id);
	       	$pro_info->total_bids = $total_bids;
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
	public function delete_project_by_ids(WP_REST_Request $request){
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
 		foreach( $project_ids as $project_id ){
 			if( get_post_type($project_id) != "projects"):
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
					'message'    => __( 'Due to technical issue not deleting any project', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;

	}
	public function create_project_photo(WP_REST_Request $request){
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$project_id = $request->get_param( 'project_id' ); 
 		$author  = $request->get_param( 'author' ); 
 		$photos  = $request->get_param( 'photos' );
 		$errors_arr = array();
 		if( 'projects' != get_post_type( $project_id ) ){
 			$errors_arr[] = __( 'Invalid project id.', 'jwt-auth' ); 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project id.', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
			);
 		}
 		if( empty( $photos) || gettype($photos) != 'array' ){
 			
			$errors_arr[] = __( 'Minimum one image required.', 'jwt-auth' ); 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one image required.', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
			);
 		}
 		$API_PhotosController = new API_PhotosController();
 		$total_upload_photos = array();
 		foreach( $photos as $photo_data ){
 			if( '' == $photo_data['image'] ):
 				continue;
 			endif;
 			 
 			$imageData = array(
 				'image' => $photo_data['image'],
 				'image_detail' => $photo_data['detail'],
 				'parent_post' => $project_id,
 				'post_author' => $post_author
 			);
 			$insert_status = $API_PhotosController->import_base64_image($imageData);
 			 
 			if( $insert_status ):

 				array_push( $total_upload_photos, $insert_status);
 			endif;
 			 
 			 
 		}
 		// var_dump($total_upload_photos);
		if( count($total_upload_photos) > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Photo successfully uploaded.', 'jwt-auth' ),
					'data'       => array('upload_photo_ids' => $total_upload_photos),
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

	public function update_project_photo(WP_REST_Request $request){

		$project_id = $request->get_param( 'project_id' ); 
 		$author  = $request->get_param( 'author' ); 
 		$photos  = $request->get_param( 'photos' );
 		$errors_arr = array();
 		if( 'projects' != get_post_type( $project_id ) ){
 			$errors_arr[] = __( 'Invalid project id.', 'jwt-auth' ); 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project id.', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
			);
 		}
 		 		 
 		if( empty( $photos) || gettype($photos) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one image required', 'jwt-auth' ),
					'data'       => '',
				),
				
			);
 		}
 		$total_updated_photos = 0;
 		foreach( $photos as $photo ){
 			$photo_id = $photo['photo_id'];
 		 
 			$detail = $photo['detail'];
 			$uploaded_image = array();
			$uploaded_image['ID'] = $photo_id;
			$uploaded_image['post_title'] = $detail; // Image Title
			$uploaded_image['post_excerpt'] = $detail; // Image Caption
			$uploaded_image['post_content'] = $detail; // Image Description
			if( "attachment" != get_post_type( $photo_id ) ):
				continue;
			endif;
			
 			if(wp_update_post( $uploaded_image )):
 				$total_updated_photos++;
 			endif;
 		}
 		if( $total_updated_photos > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Photos successfully updated.', 'jwt-auth' ),
					'data'       => array('total_updated_photos' => $total_updated_photos),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any project', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
	}

	public function delete_project_photos(WP_REST_Request $request){
		$API_PhotosController = new API_PhotosController();
		$project_id = $request->get_param( 'project_id' );
		$photo_ids    = $request->get_param( 'photo_ids' ); 
 		$errors_arr = array();
 		if( 'projects' != get_post_type( $project_id ) ){
 			$errors_arr[] = __( 'Invalid project id.', 'jwt-auth' ); 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project id.', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
			);
 		}
 		if( empty( $photo_ids) || gettype($photo_ids) != 'array' ){

 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one photo id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}
 		$total_delete_photos = 0;
 		foreach( $photo_ids as $photo_id ){
 			$getPostAttach  = get_post( $photo_id ); 
 			if(wp_delete_attachment($photo_id, true)):
 				
 				$API_PhotosController->ballon_delete_media_from_s3($getPostAttach->guid);
 				$total_delete_photos++;
 			endif;
 		}
 		if( $total_delete_photos > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Photos successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_delete_photos' => $total_delete_photos),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any project', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
	}

	/**
	 * Assign tenant to project
	 */ 
	public function assign_tenant_to_project(WP_REST_Request $request){
		$project_id = $request->get_param( 'project_id' );
		$tenant_ids  = $request->get_param( 'tenant_ids' );
		if( 'projects' != get_post_type( $project_id ) ){
 		 
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
 		if( empty( $tenant_ids) || gettype($tenant_ids) != 'array' ){

 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one tenant id is required', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		}
 		$tenants = get_post_meta( $project_id, 'requested_tenants', true);
 		
 		$final_tenants = array();
 		foreach( $tenant_ids as $tenant_id ):
 			array_push( $final_tenants,  $tenant_id );
 		endforeach;
 		foreach( $tenants as $tenant_id ):
 			array_push( $final_tenants,  $tenant_id );
 		endforeach;

 		$unique = array_unique( $final_tenants );
 		$final_tenants = array_filter( $unique );
 		update_post_meta( $project_id, 'requested_tenants', $final_tenants );
 		return new WP_REST_Response(
			array(
				'success'    => true,
				'statusCode' => 200,
				'code'       => 'success',
				'message'    => __( 'Tenant successfully assigned.', 'jwt-auth' ),
				'data'       => array('requested_tenants' => $final_tenants),
			),
			 
		);

	}

	/**
	 * Assign contractor to project
	 */ 
	public function assign_contractor_to_project(WP_REST_Request $request){
		$project_id = $request->get_param( 'project_id' );
		$send_by = $request->get_param( 'send_by' );
		$contractor_ids  = $request->get_param( 'contractor_ids' );
		$required_documents = $request->get_param( 'required_documents' );
		$pro_cont_license = $request->get_param( 'pro_cont_license' );
		 
		if( 'array' != gettype( $pro_cont_license ) ){
			$pro_cont_license = array();
		}
		$another_documents = $request->get_param( 'another_documents' );
		$contractor_required_docs = array(
			'required_documents' => $required_documents,
			'pro_cont_license' => $pro_cont_license,
			'another_documents' => $another_documents

		);
		if( 'projects' != get_post_type( $project_id ) ){
 		 
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
 		if( empty( $contractor_ids) || gettype($contractor_ids) != 'array' ){

 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one contractor id is required', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		}
 		$contractors = get_post_meta( $project_id, 'requested_contractors', true);
 		$final_contractors = array();
 		foreach( $contractor_ids as $cont_id ):

 			array_push( $final_contractors,  $cont_id );
 		endforeach;
 		foreach( $contractors as $tcont_id ):
 			array_push( $final_contractors,  $cont_id );
 		endforeach;

 		$unique = array_unique( $final_contractors );
 		$final_contractors = array_filter( $unique );
 		$header_token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
 		 $header_token = str_replace("Bearer", "", $header_token);
 		 $header_token = trim( $header_token);
 		 $API_BaseController = new API_BaseController();
		$vendor_id = $API_BaseController->custom_validate_token($_SERVER);
		$vendor_id = (int) $vendor_id;
 		// $mail_status = $this->send_project_email_to_contractor($project_id, $cont_id, $header_token );
 		$mail_status = $this->send_project_cloudemail_to_contractor($project_id, $cont_id, $header_token, $vendor_id);
 		if( $mail_status ){
 			update_post_meta( $project_id, 'requested_contractors', $final_contractors );
 			update_post_meta( $project_id, 'contractor_'.$cont_id.'_required_docs', $contractor_required_docs);
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Contractor successfully assigned.', 'jwt-auth' ),
					'data'       => array('requested_contractors' => $final_contractors, "mail_status" => $mail_status),
					'check_token' => $header_token
				),
				 
			);
 		}

 		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Email is not sending.', 'jwt-auth' ),
					'data'       => array("mail_status" => $mail_status),
				),
				 
			);

	}

	/**
	  * If contractor not interested in any project then call this function
	  * 
	  */
	public function set_project_not_interested( WP_REST_Request $request){
		$project_id = $request->get_param( 'project_id' );
		$contractor_id = $request->get_param( 'contractor_id' );
		$errors_arr = array();
		if(empty($project_id)){ 
			$errors_arr[] = __( 'Please enter project id.', 'jwt-auth' ); 
		}
		
		if(empty($contractor_id)){ 
			$errors_arr[] = __( 'Please enter contractor id.', 'jwt-auth' ); 
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
	    if( 'projects' != get_post_type( $project_id ) ){
 			$errors_arr[] = __( 'Invalid project id.', 'jwt-auth' ); 
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
 		$user = get_user_by( 'ID', $contractor_id );
 		if ( empty($user) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid contractor id.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
	    $requested_contractors = get_post_meta( $project_id, 'requested_contractors', true);
	    if('array' != gettype($requested_contractors) ):
				$requested_contractors = array();
			endif;
	    $not_int_cont = get_post_meta( $project_id, 'not_interested_contractor', true);
	    if( 'array' != gettype($not_int_cont ) ):
	    	$not_int_cont = array();
	    endif;

	    // validate = contractor already told 'not interested' or already created bid
	    if( in_array( $contractor_id , $not_int_cont ) ):
	    	// means contractor already told "I not interested"
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'You have already sent not interested request.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
	    endif;
	    $args = array(
	    	'fields'         => 'ids',
		    'numberposts' => -1,
		    'post_type' => 'project_bids',
		    'post_status' => 'publish',
		    'meta_query' => array(
		        array(
		            'key'       => 'project_id',
		            'value'     =>  $project_id,
		            'compare' => '='
		        ),
		         array(
		            'key'       => 'contractor_id',
		            'value'     =>  $contractor_id,
		            'compare' => '='
		        ),
		    )
		);
		$all_bids = get_posts( $args );
		if( ! empty( $all_bids ) ):
			// means contractor already created bid
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'You have already created bid.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
		endif;
		array_push( $not_int_cont,  $contractor_id );

 		$unique = array_unique( $not_int_cont );
 		$not_int_cont = array_filter( $unique );
 		update_post_meta( $project_id, 'not_interested_contractor', $not_int_cont );
 		// remove this contractors from the requested contractors array
		if (($key = array_search($contractor_id, $requested_contractors)) !== false) {
			unset($requested_contractors[$key]);
			$unique = array_unique( $requested_contractors );
 			$requested_contractors = array_filter( $unique );
 			update_post_meta( $project_id, 'requested_contractors', $requested_contractors );
		}
 		return new WP_REST_Response(
			array(
				'success'    => true,
				'statusCode' => 200,
				'code'       => 'success',
				'message'    => __( 'Contractor interested successfully saved.', 'jwt-auth' ),
				'data'       => array(),
			),
			 
		);

	}

	/**
	 * Create project bids
	 */ 
	public function create_project_bid(WP_REST_Request $request ){
		$company_name = $request->get_param( 'company_name' );
		$honorific = $request->get_param( 'honorific' );
		$first_name = $request->get_param( 'first_name' );
		$last_name = $request->get_param( 'last_name' );
		$email_id = $request->get_param( 'email_id' );
		$primary_contact_number = $request->get_param( 'primary_contact_number' );
		$primary_contact_number_type = $request->get_param( 'primary_contact_number_type' );
		$primary_phone_number = $request->get_param( 'primary_phone_number' );
		$primary_phone_number_type = $request->get_param( 'primary_phone_number_type' );
		$secondary_phone_number = $request->get_param( 'secondary_phone_number' );
		$secondary_phone_number_type = $request->get_param( 'secondary_phone_number_type' );
		$bid_price = $request->get_param( 'bid_price' );
		$additional_info = $request->get_param( 'additional_info' );
		$project_id = $request->get_param( 'project_id' );
		$contractor_id = $request->get_param( 'contractor_id' );
		$services = $request->get_param( 'services' );
		$street_address = $request->get_param( 'street_address' );
		$street_address_2 = $request->get_param( 'street_address_2' );
		$city = $request->get_param( 'city' );
		$state = $request->get_param( 'state' );
		$zip = $request->get_param( 'zip' );
		$errors_arr = array();
		// if(empty($project_id)){ 
		// 	$errors_arr[] = __( 'Please enter project id.', 'jwt-auth' ); 
		// }
		
		// if(empty($contractor_id)){ 
		// 	$errors_arr[] = __( 'Please enter contractor id.', 'jwt-auth' ); 
		// }
		// if(empty($first_name)){ 
		// 	$errors_arr[] = __( 'Please enter first name.', 'jwt-auth' ); 
		// }
		// if(empty($last_name)){ 
		// 	$errors_arr[] = __( 'Please enter last name.', 'jwt-auth' ); 
		// }
		// if(empty($email_id)){ 
		// 	$errors_arr[] = __( 'Please enter email address.', 'jwt-auth' ); 
		// }
		// if(empty($primary_phone_number)){ 
		// 	$errors_arr[] = __( 'Please enter primary phone number.', 'jwt-auth' ); 
		// }
		// if(empty($primary_phone_number_type)){ 
		// 	$errors_arr[] = __( 'Please enter primary phone number type.', 'jwt-auth' ); 
		// }
		// if(empty($bid_price)){ 
		// 	$errors_arr[] = __( 'Please enter project quote price.', 'jwt-auth' ); 
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
	    if( 'projects' != get_post_type( $project_id ) ){
 			$errors_arr[] = __( 'Invalid project id.', 'jwt-auth' ); 
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

	    $not_int_cont = get_post_meta( $project_id, 'not_interested_contractor', true);
	    if( 'array' != gettype($not_int_cont ) ):
	    	$not_int_cont = array();
	    endif;
 		 // validate = contractor already told 'not interested' or already created bid
	    if( in_array( $contractor_id , $not_int_cont ) ):
	    	// means contractor already told "I not interested"
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'You have already sent not interested request.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
	    endif;
	    $args1 = array(
	    	'fields'         => 'ids',
		    'numberposts' => -1,
		    'post_type' => 'project_bids',
		    'post_status' => 'publish',
		    'meta_query' => array(
		        array(
		            'key'       => 'project_id',
		            'value'     =>  $project_id,
		            'compare' => '='
		        ),
		         array(
		            'key'       => 'contractor_id',
		            'value'     =>  $contractor_id,
		            'compare' => '='
		        ),
		    )
		);
		$all_bids = get_posts( $args1 );
		if( ! empty( $all_bids ) ):
			// means contractor already created bid
	    	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'You have already created bid.', 'jwt-auth' ),
					'data'       => array(),
				),
				
			);
		endif;
 		$post_title = get_the_title($project_id) .' ( '.$first_name . ' '. $last_name.' ) '. $bid_price;
 		$Reg = wp_insert_post(array(
			'post_title'    => $post_title,
			'post_type'     => 'project_bids',
			'post_content'  => $post_title,
			'post_status'   => 'publish',
			'post_author' => $contractor_id
			 
		));
		if( isset( $Reg ) && is_numeric( $Reg ) ){
			update_post_meta($Reg, 'company_name', $company_name);
			update_post_meta($Reg, 'honorific', $honorific);
			update_post_meta($Reg, 'first_name', $first_name);
			update_post_meta($Reg, 'last_name', $last_name);
			update_post_meta($Reg, 'email_id', $email_id);
			update_post_meta($Reg, 'primary_contact_number', $primary_contact_number);
			update_post_meta($Reg, 'primary_contact_number_type', $primary_contact_number_type);
			update_post_meta($Reg, 'primary_phone_number', $primary_phone_number);
			update_post_meta($Reg, 'primary_phone_number_type', $primary_phone_number_type);
			update_post_meta($Reg, 'secondary_phone_number', $secondary_phone_number);
			update_post_meta($Reg, 'secondary_phone_number_type', $secondary_phone_number_type);
			update_post_meta($Reg, 'bid_price', $bid_price);
			update_post_meta($Reg, 'additional_info', $additional_info);
			update_post_meta($Reg, 'project_id', $project_id);
			update_post_meta($Reg, 'contractor_id', $contractor_id);
			update_post_meta($Reg, 'services', $services);
			update_post_meta($Reg, 'street_address', $street_address);
			update_post_meta($Reg, 'street_address_2', $street_address_2);
			update_post_meta($Reg, 'city', $city);
			update_post_meta($Reg, 'state', $state);
			update_post_meta($Reg, 'zip', $zip);

			// change is_new_user key value from the contractor
			update_user_meta( $contractor_id, 'is_new_user', 'no');
		 	// remove this contractors from the requested contractors array
			$requested_contractors = get_post_meta( $project_id, 'requested_contractors', true);
			if('array' != gettype($requested_contractors) ):
				$requested_contractors = array();
			endif;
			if (($key = array_search($contractor_id, $requested_contractors)) !== false) {
				unset($requested_contractors[$key]);
				$unique = array_unique( $requested_contractors );
	 			$requested_contractors = array_filter( $unique );
	 			update_post_meta( $project_id, 'requested_contractors', $requested_contractors );
			}
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Bids sucessfully created.', 'jwt-auth' ),
					'data'       => array('bid_id' => $Reg),
				),

			);
		}
		return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Bid is not creating due to technical issue.', 'jwt-auth' ),
				'data'       => '',
			),
			
		); 
	}
	/**
	 * Send email from cloudemail 
	 */
	public function send_project_cloudemail_to_contractor( $project_id, $contractor_id, $securiy_tokan='', $vendor_id = 0 ){
		$user_info = get_user_by( 'ID', $contractor_id);
		$project_info = get_post($project_id);
		$my_app_url =  get_option( 'my_app_url' );
		$admin_email =  get_option( 'admin_email' );
		$from = "charles@getaccountableapp.com";
		$complex_name = '';
		$company_name = get_user_meta( $contractor_id, 'company_name', true );
		$contact_name = $user_info->first_name .' '. $user_info->last_name;
		if( '' == $company_name ):
			$company_name = $contact_name;
		endif;
		$to =  $user_info->user_email;
		// $to =  "yogendra3rde@gmail.com";
		$site_title = get_bloginfo( 'name' );
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
		$access_data = base64_encode( $project_id.'@'.$contractor_id);
		
		// $encrypt_pro = base64_encode( $project_id );
		$encrypt_pro = 'bid';
		// $string = ':3000/'.$encrypt_pro.'?access_token='.$securiy_tokan.'&access_data='.$access_data;
		$string = '/'.$encrypt_pro.'?access_token='.$securiy_tokan.'&access_data='.$access_data;
		// $project_link = site_url($string);
		$project_link = $my_app_url.''.$string;
		// $project_link = str_replace('/:3000', ':3000', $project_link);
		$property_name = get_user_meta( $vendor_id, 'property_name', true );
		$property_name = trim( $property_name );
		$subject = $project_info->post_title;
		$property_name_string = '';
		if( $property_name != '' ){
			$subject = $property_name.' '. $project_info->post_title;
			$property_name_string = "<span>". $property_name."</span><br/>";
		}
		
		$vendor_name = get_user_meta( $vendor_id, 'first_name', true )." ". get_user_meta( $vendor_id, 'last_name', true );
		$vendor_location = get_user_meta( $vendor_id, "location", true );
		$message = "<p>Hi ".$company_name.",</p>
		<p>We would like to work with you on a project at ".$property_name."</p>

		<p><strong>Here are the project details:</strong></p>
		<span>". $project_info->post_title."</span><br/>
		<span>". $project_info->post_content."</span>
		<br/> 
		<p>if you're interested, click or copy the link below to see the full project details and photos.</p>
		<p><a href='".$project_link."'>".$project_link."</a></p>
	
		<p>Thanks,</p>

		<p><span>". $vendor_name."</span><br/>".$property_name_string."
		<span>". $vendor_location."</span></p>
		";
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
		    $mail->addAddress($to, $company_name);     //Add a recipient
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

	public function send_project_email_to_contractor( $project_id, $contractor_id, $securiy_tokan='' ){
		$user_info = get_user_by( 'ID', $contractor_id);
		$project_info = get_post($project_id);
		$from =  get_option( 'admin_email' );
		$complex_name = '';
		$company_name = get_user_meta( $contractor_id, 'company_name', true );
		$contact_name = $user_info->first_name .' '. $user_info->last_name;
		if( '' == $company_name ):
			$company_name = $contact_name;
		endif;
		// $to =  $user_info->user_email;
		$to =  "webtest2105@gmail.com";
		$site_title = get_bloginfo( 'name' );
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
		$access_data = base64_encode( $project_id.'@'.$contractor_id);
		
		// $encrypt_pro = base64_encode( $project_id );
		$encrypt_pro = 'bid';
		$string = $encrypt_pro.'?access_token='.$securiy_tokan.'&access_data='.$access_data;
		$project_link = site_url($string);

		$subject = $complex_name.' '. $project_info->post_title;

		$message = "<p>Hi ".$company_name.",</p>
		<p>We would like to work with you on a project at ".$complex_name."</p>

		<p><strong>Here are the project details:</strong></p>
		<span>". $project_info->post_title."</span><br/>
		<span>". $project_info->post_content."</span>
		<br/> 
		<p>if you're interested, click or copy the link below to see the full project details and photos.</p>
		<p><a href='".$project_link."'>Project detail link</a></p>
	
		<p>Thanks,</p>

		<p>Administrator</p>
		";
		
	 
		$mail_status = wp_mail( $to, $subject, $message, $headers );
		return $mail_status;

	}

	public function get_a_project_bids( $project_id ){
		$args = array(
		    'numberposts' => -1,
		    'post_type' => 'project_bids',
		    'post_status' => 'publish',
		    'meta_query' => array(
		        array(
		            'key'       => 'project_id',
		            'value'     =>  $project_id,
		        )
		    )
		);
		$all_bids = get_posts( $args );
		$total_bids = array();
		foreach( $all_bids as $bid ){
			$bid_info = get_post( $bid->ID );

            if ( empty($bid_info) ) :
                continue;
            endif;
            $contractor_id = get_post_meta($bid->ID, 'contractor_id', true);
            $user_info = get_user_by('ID', $contractor_id );
            if( ! isset($user_info->ID) ){
            	continue;
            }
            $bid_info->company_name = get_post_meta($bid->ID, 'company_name', true);
            $bid_info->first_name = get_post_meta($bid->ID, 'first_name', true);
            $bid_info->last_name = get_post_meta($bid->ID, 'last_name', true);
            $bid_info->email_id = get_post_meta($bid->ID, 'email_id', true);
            $bid_info->primary_contact_number = get_post_meta($bid->ID, 'primary_contact_number', true);
             $bid_info->primary_contact_number_type = get_post_meta($bid->ID, 'primary_contact_number_type', true);
            $bid_info->primary_phone_number = get_post_meta($bid->ID, 'primary_phone_number', true);
            $bid_info->primary_phone_number_type = get_post_meta($bid->ID, 'primary_phone_number_type', true);
            $bid_info->secondary_phone_number = get_post_meta($bid->ID, 'secondary_phone_number', true);
            $bid_info->secondary_phone_number_type = get_post_meta($bid->ID, 'secondary_phone_number_type', true);
            $bid_info->bid_price = get_post_meta($bid->ID, 'bid_price', true);
            $bid_info->additional_info = get_post_meta($bid->ID, 'additional_info', true);
            $bid_info->contractor_id = get_post_meta($bid->ID, 'contractor_id', true);
            $bid_info->project_id = get_post_meta($bid->ID, 'project_id', true);
            $bid_info->bid_accepted_by_admin = get_post_meta($bid->ID, 'bid_accepted_by_admin', true);
            $bid_info->services = get_post_meta($bid->ID, 'services', true);
            $bid_info->street_address = get_post_meta($bid->ID, 'street_address', true);
            $bid_info->street_address_2 = get_post_meta($bid->ID, 'street_address_2', true);
            $bid_info->city = get_post_meta($bid->ID, 'city', true);
            $bid_info->state = get_post_meta($bid->ID, 'state', true);
            $bid_info->zip = get_post_meta($bid->ID, 'zip', true);
            $bid_info->honorific = get_post_meta($bid->ID, 'honorific', true);
            $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $bid->ID,
			    'posts_per_page' => -1,
			);
	        $bid_documents = array();
			$attachments = get_posts($attachment_args);
			foreach( $attachments  as $attachment):
				$temp = array();
				$temp = (object) $temp;
				$temp->photo_id = $attachment->ID;
				$temp->real_file_name = get_post_meta( $attachment->ID, 'real_file_name', true );
				$temp->photo_detail = $attachment->post_content;
				$temp->photo_src = $attachment->guid;
				$temp->photo_created = $attachment->post_date;
				$temp->post_mime_type = $attachment->post_mime_type; 
				array_push( $bid_documents,  $temp );
			endforeach;
            $bid_info->bid_documents = $bid_documents;
            array_push( $total_bids, $bid_info);
		}
		return $total_bids;
	}

	public function upload_project_document(WP_REST_Request $request){
		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$bid = $request->get_param( 'bid' ); 
 		$author  = $request->get_param( 'author' ); 
 		$photos  = $request->get_param( 'photos' );
 		$errors_arr = array();
 		if( 'project_bids' != get_post_type( $bid ) ){
 			$errors_arr[] = __( 'Invalid project bid id.', 'jwt-auth' ); 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project bid id.', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
			);
 		}
 		if( empty( $photos) || gettype($photos) != 'array' ){
 			
			$errors_arr[] = __( 'Minimum one image required.', 'jwt-auth' ); 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one image required.', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
			);
 		}
 		$API_PhotosController = new API_PhotosController();
 		$total_upload_photos = array();
 		foreach( $photos as $photo_data ){
 			if( '' == $photo_data['image'] ):
 				continue;
 			endif;
 			 
 			$imageData = array(
 				'image' => $photo_data['image'],
 				'image_detail' => $photo_data['detail'],
 				'parent_post' => $bid,
 				'post_author' => $post_author
 			);
 			$insert_status = $API_PhotosController->import_base64_image($imageData);
 			 
 			if( $insert_status ):

 				array_push( $total_upload_photos, $insert_status);
 			endif;
 			 
 			 
 		}
 		// var_dump($total_upload_photos);
		if( count($total_upload_photos) > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Photo successfully uploaded.', 'jwt-auth' ),
					'data'       => array('upload_photo_ids' => $total_upload_photos),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any bid', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
	}

	public function admin_bid_accept_decline(WP_REST_Request $request){
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
		$bid = $request->get_param( 'bid' ); 
		$API_BaseController = new API_BaseController();
		$author = $API_BaseController->custom_validate_token($_SERVER);
 		// $author  = $request->get_param( 'author' ); // author id means admin
 		$bid_status = $request->get_param('bid_status'); // 1 means accepted and 0 means decline and blank means undu
 		$errors_arr = array();
 		if( 'project_bids' != get_post_type( $bid ) ){
 			$errors_arr[] = __( 'Invalid project bid id.', 'jwt-auth' ); 
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Invalid project bid id.', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
			);
 		}
 		if(empty($bid)){ 
			$errors_arr[] = __( 'Bid id is required.', 'jwt-auth' ); 
		}
		// if(empty($bid_status)){ 
		// 	$errors_arr[] = __( 'Bid status is required.', 'jwt-auth' ); 
		// }
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
	    update_post_meta($bid, 'bid_accepted_by_admin', $bid_status);
	    update_post_meta($bid, 'bid_accepted_by_admin_id', $author);
	    return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Bid response successfully updated.', 'jwt-auth' ),
					'data'       => array('bid' => $bid),
				),
				
			); 
 
 	}
	public function count_total_bids_for_project( $project_id ){
		$args = array(
		    'numberposts' => -1,
		    'post_type' => 'project_bids',
		    'post_status' => 'publish',
		    'meta_query' => array(
		        array(
		            'key'       => 'project_id',
		            'value'     =>  $project_id,
		        )
		    )
		);
		$all_bids = get_posts( $args );
		return count( $all_bids );
	}

	/**
	 * Remove tenant from the project
	 */ 
	public function remove_tenant_from_project(WP_REST_Request $request ){
		$tenant_ids = $request->get_param('tenant_ids');
		$project_id = $request->get_param('project_id');
		if( 'projects' != get_post_type( $project_id)){
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
		$requested_tenants = get_post_meta($project_id, 'requested_tenants', true );
		if('array' != gettype( $requested_tenants ) ):
			$requested_tenants = array();
		endif;
		foreach( $tenant_ids as $tenant_id ):
			$find = $tenant_id;
			if( in_array($find, $requested_tenants) ){
				$index =  array_search($find,$requested_tenants);
				unset( $requested_tenants[$index] );
			}
			
		endforeach;
		$final_requested_tenants = array_merge( array(), $requested_tenants );
		update_post_meta($project_id, 'requested_tenants', $final_requested_tenants );
		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Tenant successfully deleted.', 'jwt-auth' ),
					'data'       => array('requested_tenants' => $final_requested_tenants),
				),
				
			); 
	}
	/**
	 * Remove contractor from the project
	 */ 
	public function remove_contractor_from_project(WP_REST_Request $request ){
		$contractor_ids = $request->get_param('contractor_ids');
		$project_id = $request->get_param('project_id');
		if( 'projects' != get_post_type( $project_id)){
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

		$requested_contractors = get_post_meta($project_id, 'requested_contractors', true );
		if('array' != gettype( $requested_contractors ) ):
			$requested_contractors = array();
		endif;

		$ni_contractors = get_post_meta($project_id, 'not_interested_contractor', true );
		if('array' != gettype( $ni_contractors ) ):
			$ni_contractors = array();
		endif;

		foreach( $contractor_ids as $contractor_id ):
			$find = $contractor_id;
			if( in_array($find, $requested_contractors) ){
				$index =  array_search($find,$requested_contractors);
				unset( $requested_contractors[$index] );
			}
			if( in_array($find, $ni_contractors) ){
				$index_ni =  array_search($find,$ni_contractors);
				unset( $ni_contractors[$index_ni] );
			}

			$args1 = array(
	    	'fields'         => 'ids',
		    'numberposts' => -1,
		    'post_type' => 'project_bids',
		    'post_status' => 'publish',
		    'meta_query' => array(
		        array(
		            'key'       => 'project_id',
		            'value'     =>  $project_id,
		            'compare' => '='
		        ),
		         array(
		            	'key'       => 'contractor_id',
		            	'value'     =>  $contractor_id,
			            'compare' => '='
			        ),
			    )
			);
			$all_bids = get_posts( $args1 );
			if( !empty( $all_bids ) ):
				foreach( $all_bids as $bid_id ):
					$post = array( 'ID' => $bid_id, 'post_status' => 'admin_removed' );
					wp_update_post($post);
				endforeach;	
			endif;
			$item_key = 'contractor_'.$contractor_id.'_required_docs';
			delete_post_meta( $project_id, $item_key );
		endforeach;
		$final_requested_contractors = array_merge( array(), $requested_contractors );
		update_post_meta($project_id, 'requested_contractors', $final_requested_contractors );

		$final_ni_contractors = array_merge( array(), $ni_contractors );
		update_post_meta($project_id, 'not_interested_contractor', $final_ni_contractors );


		 

		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Contractor successfully deleted.', 'jwt-auth' ),
					'data'       => array('requested_contractors' => $final_requested_contractors,
				'not_interested_contractor' => $final_ni_contractors),
				),
				
			); 
	}
	/**
 	 * Admin can send a project comment reply
 	 * 
 	 */ 
 	public function admin_send_pro_comment_reply(WP_REST_Request $request)
 	{
 		//contect info
 		$title    = $request->get_param( 'title' );
 		$project_id    = $request->get_param( 'project_id' ); 
 		 
 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		 
		$errors_arr = array();
		if(empty($title)){

 			$errors_arr[] = __( 'Please enter message reply.', 'jwt-auth' ); 
		}
		if ( ( empty($project_id) ) || ( 'projects' != get_post_type($project_id) ) ) { 
			$errors_arr[] = __( 'Invalid project id.', 'jwt-auth' ); 
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
			'comment_post_ID'     => $project_id,
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

new API_ProjectsController(); 
?>
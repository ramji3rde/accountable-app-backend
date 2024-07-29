<?php

class API_ScheduleController extends API_BaseController {

	public function __construct() 
	{  

 	}


 	 public function create_schedule_group( WP_REST_Request $request ){
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
    	$group_name    = $request->get_param( 'group_name' );
    	$schedule_period    = $request->get_param( 'schedule_period' );
     	$author    = $request->get_param( 'author' );
     	$errors_arr = array();

		if(empty($group_name)){ 
			$errors_arr[] = __( 'Please enter group name.', 'jwt-auth' ); 
		} 
		if(empty($schedule_period)){
 			$errors_arr[] = __( 'Please any schedule period.', 'jwt-auth' ); 
		}
		if(empty($author)){
 			$errors_arr[] = __( 'Please enter author id.', 'jwt-auth' ); 
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
	 
		$schedule_Reg = wp_insert_post(array(
					'post_title'     => $group_name,
					'post_type'     => 'schedule_group',
					'post_status'   => 'publish',
					'post_author'   => $post_author,
					 
				));
		if( isset( $schedule_Reg ) && is_numeric( $schedule_Reg ) ){
			update_user_meta($post_author, 'any_schedule_group_created', 'yes');
 			update_post_meta($schedule_Reg, 'schedule_period', $schedule_period);
     		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Schedule group successfully created.', 'jwt-auth' ),
					'data'       => array('schedule_group_id' => $schedule_Reg ),
				),
				
			); 
     	}
     	return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Due to technical issue schedule group is not creating.', 'jwt-auth' ),
				'data'       => '',
			),
			
		);
    }

    public function update_schedule_group( WP_REST_Request $request ){
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
    	$task_groups = $request->get_param('task_groups');
    	if( empty( $task_groups) || gettype($task_groups) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one task group is required', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
 		}
 		 $total_updated_task_groups = array();
	   	$not_updated_task_groups = array();
 		foreach( $task_groups as $task_group ){
 			$group_name    = $task_group['group_name'];
    		$group_id    = $task_group['group_id'];
    		$schedule_period    = $task_group['schedule_period'];
    		if( get_post_type($group_id) != "schedule_group" ){
 			 	$temp = array( "group_id" => $group_id, 'reason' => "this id is not a task group id");
 			 	array_push( $not_updated_task_groups, $temp);
 			 	continue;
 			}
 			$update_args = array();
	    	$update_args['ID'] = $group_id;
			$update_args['post_title'] = $group_name; 
			$update_args['post_content'] = $group_name;  
		     
			if(wp_update_post( $update_args )){
				update_post_meta($group_id, 'schedule_period', $schedule_period);
				$temp = array( "group_id" => $group_id);
				array_push( $total_updated_task_groups, $temp);

			}else{
				$temp = array( "group_id" => $group_id, 'reason' => "This task group id not updating.");
 			 	array_push( $not_updated_task_groups, $temp);
			}
 		} 
    	
 
		if( count( $total_updated_task_groups) > 0 ){
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Task group sucessfully updated.', 'jwt-auth' ),
					'data'       => array('total_updated_task_groups' => $total_updated_task_groups, 'not_updated_task_groups' => $not_updated_task_groups)
				),

			);
		}
     	return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Due to technical issue schedule group is not updating.', 'jwt-auth' ),
				'data'       => array('not_updated_task_groups' => $not_updated_task_groups),
			),
			
		);
    }

    /**
	 * Search or get all schedule group with pagination
	 * 
	 */ 	
    public function get_all_schedule_groups(WP_REST_Request $request){
    	$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_schedule_group_created = get_user_meta($post_author, 'any_schedule_group_created', true); 
    	$schedule_period    = $request->get_param( 'schedule_period' );
    	$paged = ($request->get_param('paged')) ? $request->get_param('paged') : 1;
    	$posts_per_page = ($request->get_param('posts_per_page')) ? $request->get_param('posts_per_page') : 10;
    	
    	// $orderby = "title";
    	// $order = "ASC";
    	$orderby = "menu_order";
    	$order = "ASC";
    	$total_groups = 0;
    	$args = array(  
    		'author' => $post_author,
	        'post_type' => 'schedule_group',
	        'post_status' => 'publish',
	        'paged' => $paged, 
	        'orderby' => $orderby,
			'order'   => $order,
	        'posts_per_page' => $posts_per_page, 
	        
	    );
    	 
    	if( '' != trim( $schedule_period ) ):
    		$args['meta_query'] = array(
    				'relation' => 'AND',
				        array(
				        	'key' => 'schedule_period',
				            'value' => $schedule_period,
				            'compare' => "="
				        )
				    );
    	endif;
   
    	$groups = array();
    	$the_query = new WP_Query( $args );
    	$total_groups = $the_query->found_posts;
	    $total_schedule_groups = array();
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$group_id = get_the_ID();
	        $group_info = get_post( $group_id );
	        if ( empty($group_info) ) :
	        	continue;
	        endif;
	        $temp = array();
			$temp = (object) $temp;
			$group_id = get_the_ID();
			$temp->group_id = $group_id;
			$temp->group_name = get_the_title();
			$temp->schedule_period = get_post_meta($group_id, 'schedule_period', true);
			$temp->created_date = get_the_date();

			// get schedule from the group id
			$schedule_ids = get_posts( array(
	            'fields'         => 'ids',
	            'posts_per_page' => -1,
	            'post_status'    => 'publish',
	            'post_type'      => array('schedule'),
	            'orderby'        => 'menu_order',
	            'order'          => 'ASC',
	            'meta_query'      => array(
	            	'relation' => 'AND',
	            	array(
	                'key'       => 'group_id',
	                'value'     => $group_id,
	                'compare'   => '='
	            ))
	        ));
	        $first_schedule_info = array();
	        if( count($schedule_ids) > 0 ){
	        	$first_id = $schedule_ids[0];
	        	$temp_s = array();
	        	$temp_s = (object) $temp_s;
	        	$temp_s->title = get_the_title($first_id);
	        	$temp_s->schedule_id = $first_id;
	        	array_push( $first_schedule_info, $temp_s);
	        }
	        $temp->total_schedules = count( $schedule_ids );
	        $temp->first_schedule_info = $first_schedule_info;
	       array_push( $total_schedule_groups, $temp);
	    endwhile;

	    wp_reset_postdata(); 
    	return new WP_REST_Response(
			array(
				'success'    => true,
				'statusCode' => 200,
				'code'       => 'success',
				'message'    => __( 'Schedule group successfully getting.', 'jwt-auth' ),
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'total_schedule_groups' => $total_groups,
				'data'       => $total_schedule_groups,
				'any_record_created'       => $any_schedule_group_created,
			),
			
		);
    }
    /**
 	 * Delete multiple Schedule groups by id 
 	 * 
 	 */
 	public function delete_schedule_groups(WP_REST_Request $request){
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
 		$group_ids    = $request->get_param( 'group_ids' ); 
 		
 		if( empty( $group_ids) || gettype($group_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one schedule group id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$total_delete_schedule_groups = 0;
 		foreach( $group_ids as $group_id ){
 			if( get_post_type($group_id) != "schedule_group"):
 				continue;
 			endif;
 			if(wp_delete_post($group_id, true)):
 				// get schedule from the group id
			$schedule_ids = get_posts(
				array(
		            'fields'         => 'ids',
		            'posts_per_page' => -1,
		            'post_status'    => 'publish',
		            'post_type'      => array('schedule'),
		            'orderby'        => 'title',
		            'order'          => 'ASC',
		            'meta_query'      => array(
		            	'relation' => 'AND',
			            	array(
			                'key'       => 'group_id',
			                'value'     => $group_id,
			                'compare'   => '='
			            )
		            )
		        )
	        );
	        foreach( $schedule_ids as $schedule_id ):
	        	wp_delete_post($schedule_id, true);
	        endforeach;
 				$total_delete_schedule_groups++;
 			endif;
 		}
 		if( $total_delete_schedule_groups > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Schedule group successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_delete_schedule_groups' => $total_delete_schedule_groups),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Schedule groups are not deleting. Maybe schedule groups are not exists', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}
 	public function create_schedule( WP_REST_Request $request ){
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
    	$schedule_name    = $request->get_param( 'schedule_name' );
    	$group_id    = $request->get_param( 'group_id' );
     	$author    = $request->get_param( 'author' );
     	$errors_arr = array();

		if(empty($schedule_name)){ 
			$errors_arr[] = __( 'Please enter schedule name.', 'jwt-auth' ); 
		} 
		if(empty($group_id)){
 			$errors_arr[] = __( 'Please enter group id.', 'jwt-auth' ); 
		}
		if(empty($author)){
 			$errors_arr[] = __( 'Please enter author id.', 'jwt-auth' ); 
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
	 
		$schedule_Reg = wp_insert_post(array(
					'post_title'     => $schedule_name,
					'post_type'     => 'schedule',
					'post_status'   => 'publish',
					'post_author'   => $post_author,
					 
				));
		if( isset( $schedule_Reg ) && is_numeric( $schedule_Reg ) ){
			update_user_meta($post_author, 'any_schedule_created', 'yes');
 			update_post_meta($schedule_Reg, 'group_id', $group_id);
 			update_post_meta($schedule_Reg, 'schedule_complete', 0);
     		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Schedule successfully created.', 'jwt-auth' ),
					'data'       => array('schedule_id' => $schedule_Reg ),
				),
				
			); 
     	}
     	return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Due to technical issue schedule is not creating.', 'jwt-auth' ),
				'data'       => '',
			),
			
		);
    }
    public function update_schedule( WP_REST_Request $request ){
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
    	$schedule_name    = $request->get_param( 'schedule_name' );
    	$group_id    = $request->get_param( 'group_id' );
    	$schedule_id    = $request->get_param( 'schedule_id' );
    	$schedule_complete = $request->get_param( 'schedule_complete' );
    	$schedule_complete_date = $request->get_param( 'current_date' );
     	$schedule_complete =( $schedule_complete == '1' ) ? '1' : '0';
     	
     	$errors_arr = array();
		if(empty($schedule_name)){ 
			$errors_arr[] = __( 'Please enter schedule name.', 'jwt-auth' ); 
		} 
		if(empty($schedule_id)){
 			$errors_arr[] = __( 'Please enter schedule id.', 'jwt-auth' ); 
		}
		if(empty($group_id)){
 			$errors_arr[] = __( 'Please enter schedule group id.', 'jwt-auth' ); 
		}
		if(empty($schedule_complete_date)){
 			$errors_arr[] = __( 'Please enter current date.', 'jwt-auth' ); 
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

 		if ( ( 'schedule_group' != get_post_type($group_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter valid group id', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
	 	if ( ( 'schedule' != get_post_type($schedule_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter valid schedule id', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
		$update_args = array();
	    $update_args['ID'] = $schedule_id;
		$update_args['post_title'] = $schedule_name; 
		
     	$schedule_complete_date = date("Y-m-d", strtotime( $schedule_complete_date ));
     	$schedule_complete_date_strtotime = (int) str_replace( '-','', $schedule_complete_date) ;
		if(wp_update_post( $update_args )):
 			update_post_meta( $schedule_id, 'group_id', $group_id);
 			update_post_meta( $schedule_id, 'schedule_complete', $schedule_complete );
 			update_post_meta( $schedule_id, 'schedule_complete_date', '' );
 			update_post_meta( $schedule_id, 'schedule_complete_date_strtotime', '' );
 			if( 1 == $schedule_complete ):
 				update_post_meta( $schedule_id, 'schedule_complete_date', $schedule_complete_date );
 				update_post_meta( $schedule_id, 'schedule_complete_date_strtotime', $schedule_complete_date_strtotime );
 			endif;
 			
     		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Schedule successfully updated.', 'jwt-auth' ),
					'data'       => array('schedule_id' => $schedule_id ),
				),
				
			); 
     	endif;
     	return new WP_REST_Response(
			array(
				'success'    => false,
				'statusCode' => 403,
				'code'       => 'error',
				'message'    => __( 'Due to technical issue schedule is not updating.', 'jwt-auth' ),
				'data'       => '',
			),
			
		);
    }
    public function get_all_schedule(WP_REST_Request $request){
    	$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_schedule_created = get_user_meta($post_author, 'any_schedule_created', true); 
    	$main_group_id    = $request->get_param( 'group_id' );
    	$current_date = $request->get_param('current_date');
    	
    	$current_date = ( '' == trim( $current_date ) ) ? date_i18n("Y-m-d") : $current_date;
    	$current_strtotime = (int) str_replace('-', '', $current_date );
     	$paged = ($request->get_param('paged')) ? $request->get_param('paged') : 1;
    	$posts_per_page = ($request->get_param('posts_per_page')) ? $request->get_param('posts_per_page') : 10;
    	$schedule_period = get_post_meta($main_group_id, 'schedule_period', true);
    	if ( ( 'schedule_group' != get_post_type($main_group_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter group id', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }
    	// $orderby = "title";
    	// $order = "ASC";
    	$orderby = "menu_order";
    	$order = "ASC";

    	$total_groups = 0;
    	$args = array(  
    		'author' => $post_author,
	        'post_type' => 'schedule',
	        'post_status' => 'publish',
	        'paged' => $paged, 
	        'orderby' => $orderby,
			'order'   => $order,
	        'posts_per_page' => $posts_per_page, 
	        
	    );
    	 
    	if( '' != trim( $main_group_id ) ):
    		$args['meta_query'] = array(
    				'relation' => 'AND',
				        array(
				        	'key' => 'group_id',
				            'value' => $main_group_id,
				            'compare' => "="
				        )
				    );
    	endif;
 	 
    	$groups = array();

    	$the_query = new WP_Query( $args );
    	$total_number_of_schedule = $the_query->found_posts;
	    $total_schedule = array();
	    $group_name = get_the_title($main_group_id);
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$group_id = get_the_ID();
	        $group_info = get_post( $group_id );
	        if ( empty($group_info) ) :
	        	continue;
	        endif;
	        $temp = array();
			$temp = (object) $temp;
			$schedule_id = get_the_ID();
			$schedule_complete = get_post_meta($schedule_id,  'schedule_complete', true );
			$schedule_complete = ( $schedule_complete == '1') ? '1' : '0'; // 1 mean completed and 0 means not completed
			$schedule_complete_date = get_post_meta( $schedule_id, 'schedule_complete_date', true ); 
			$schedule_complete_date_str = str_replace('-', '', $schedule_complete_date );
			$completed_date_strtotime = get_post_meta( $schedule_id, 'schedule_complete_date_strtotime', true );
			$completed_date_strtotime = ( '' == $completed_date_strtotime ) ? str_replace('-', '',$current_date) : $completed_date_strtotime;
			$completed_date_strtotime = (int) $completed_date_strtotime;
			switch ($schedule_period) {
			  case "1":
			  	// dailly
			   /// if currnet date and complete mark date samed. Also schedule complete value is  1 means schedule is completed mark otherwise not completed
			  	if( ( $schedule_complete_date_str == $current_strtotime ) && ( 1 == $schedule_complete) ){
			  		$schedule_complete = 1;
			  	}else{
			  		$schedule_complete = 0;
			  	}
			    
			    break;
			  case "2":
			    // weekly
			 	$saturday_date = date('Y-m-d', strtotime("saturday 0 week", strtotime($schedule_complete_date)));
			 	$saturday_strtotime = (int) str_replace('-', '', $saturday_date);

			 	// if current date is less or equal to next saturday date. also schedule complete value is 1 means  schedule is completed mark otherwise not completed
			 	if( ( $current_strtotime <= $saturday_strtotime ) && ( 1 == $schedule_complete) ){
			  		$schedule_complete = 1;
			  	}else{
			  		$schedule_complete = 0;
			  	}
			    break;
			  case "3":
			    // monthly
			  	// // get last day of month
				  	$last_day_this_month = date('Y-m-t', $current_strtotime);
				  	// $last_day_month_strtotime = strtotime( $last_day_this_month );
				  	$last_day_month_strtotime = (int) str_replace('-', '', $last_day_this_month );
				  	// var_dump($current_date);
				 	if( ( $completed_date_strtotime >= $last_day_month_strtotime ) && ( 1 == $schedule_complete) ){
				 		$schedule_complete = 1;
				  	}else{
				  		$schedule_complete = 0;
				  	}
			    break;
			    case "4":
			    //quaterly
				    $current_month = date('n', $current_strtotime);
				    $quater_range = array();
				    if( (1 <= $current_month) && ($current_month <= 3) ){
					 	$quater_range[] = array(
							date('Y-01-01', $current_strtotime),
							date('Y-03-31', $current_strtotime)
											);
					 }else if( (4 <= $current_month) && ($current_month <= 6) ){
					 	$quater_range[] = array(
							date('Y-04-01', $current_strtotime),
							date('Y-06-30', $current_strtotime)
											);
					 }else if( (7 <= $current_month) && ($current_month <= 9) ){
					 	$quater_range[] = array(
							date('Y-07-01', $current_strtotime),
							date('Y-09-30', $current_strtotime)
											);
					 }else if( (10 <= $current_month) && ($current_month <= 12) ){
					 	$quater_range[] = array(
							date('Y-10-01', $current_strtotime),
							date('Y-12-31', $current_strtotime)
											);
					 }
			    	
					$temp_complete = 0;
					for( $i = 0; $i < count( $quater_range); $i++){
						
						$start_from = strtotime( $quater_range[$i][0] );
						$end_to = strtotime( $quater_range[$i][1] );
						if( ($start_from <= $completed_date_strtotime) && ($completed_date_strtotime <= $end_to) && ( 1 == $schedule_complete) ){
						 	$temp_complete = 1;
						 	break;
						 }
					}
					$schedule_complete = ( 1 == $temp_complete ) ? 1 : 0;
			    	break;
			    case "5":
			   		 // half yearly
			    	 $current_month = date('n', $current_strtotime);
				    $half_quater_range = array();
				    if( (1 <= $current_month) && ($current_month <= 3) ){
					 	$half_quater_range[] = array(
							date('Y-01-01', $current_strtotime),
							date('Y-06-30', $current_strtotime)
											);
					 }else if( (10 <= $current_month) && ($current_month <= 12) ){
					 	$half_quater_range[] = array(
							date('Y-07-01', $current_strtotime),
							date('Y-12-31', $current_strtotime)
											);
					 }
			    	
					$temp_complete = 0;
					for( $i = 0; $i < count( $half_quater_range); $i++){
						
						$start_from = strtotime( $half_quater_range[$i][0] );
						$end_to = strtotime( $half_quater_range[$i][1] );
						if( ($start_from <= $completed_date_strtotime) && ($completed_date_strtotime <= $end_to) && ( 1 == $schedule_complete) ){
						 	$temp_complete = 1;
						 	break;
						 }
					}
					$schedule_complete = ( 1 == $temp_complete ) ? 1 : 0;
			    break;
			    case "6":
			    // yearly
			    	 
				    $year_range = array(
				    	date('Y-01-01', $current_strtotime),
						date('Y-12-31', $current_strtotime)
				    );
				    
					$start_from = strtotime( $year_range[0] );
					$end_to = strtotime( $year_range[1] );
					if( ($start_from <= $completed_date_strtotime) && ($completed_date_strtotime <= $end_to) && ( 1 == $schedule_complete) ){
					 	$schedule_complete = 1;
					 	 
					 }else{
					 	$schedule_complete = 0;
					 }
					 
					 
			    break;
			  default:
			    // none
			} 
			// $schedule_complete_date = date_i18n("Y-m-d", strtotime(get_the_date()));
			update_post_meta( $schedule_id, 'schedule_complete', $schedule_complete );
			$temp->schedule_id = $schedule_id;
			$temp->schedule_name = get_the_title();
			$temp->schedule_complete = $schedule_complete;
			$temp->menu_order =get_post_field( 'menu_order', $schedule_id);
			$temp->group_id = get_post_meta($group_id, 'group_id', true);
			$temp->created_date = get_the_date();
			// $temp->schedule_complete_date = $schedule_complete_date;
	       array_push( $total_schedule, $temp);
	    endwhile;

	    wp_reset_postdata();
	  
	   
	    $group_data = array(
	    	'group_name' => $group_name,
	    	'schedule_list' => $total_schedule,
	    	'total_schedule' => $total_number_of_schedule,
	    	'schedule_period' => $schedule_period,
	    );
    	return new WP_REST_Response(
			array(
				'success'    => true,
				'statusCode' => 200,
				'code'       => 'success',
				'message'    => __( 'Schedule successfully getting.', 'jwt-auth' ),
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'total_schedule' => $total_number_of_schedule,
				'schedule_period' => $schedule_period,
				'data'       => $group_data,
				'any_schedule_created' => $any_schedule_created,
			),
			
		);
    }

 	public function get_single_schedule(WP_REST_Request $request)
 	{
 		$schedule_id    = $request->get_param( 'schedule_id' );  

 		if ( ( empty($schedule_id) ) || ( 'schedule' != get_post_type($schedule_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter valid schedule id', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

 
		$schedule_info = get_post( $schedule_id );

		if ( empty($schedule_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This schedule is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{

	    	 
	    	$temp = array();
			$temp = (object) $temp;
			$schedule_complete = get_post_meta($schedule_id,  'schedule_complete', true );
			$schedule_complete = ( $schedule_complete == '1') ? '1' : '0'; // 1 mean completed and 0 means not completed
			$temp->schedule_id = $schedule_info->ID;
			$temp->schedule_name = $schedule_info->post_title;
			$temp->schedule_complete = $schedule_complete; 
			$temp->created_date = $schedule_info->post_date; 
			$temp->created_date = $schedule_info->post_date; 
			$group_id = get_post_meta($schedule_id, 'group_id', true);
			$temp->group_id = $group_id;
			$temp->group_name = get_the_title( $group_id );
	    	$temp->schedule_period = get_post_meta($group_id, 'schedule_period', true);
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'Schedule Detail', 'jwt-auth' ),
						'data'       => $temp,
					),
					 
				);
		}

 	}

 	/**
 	 * Delete multiple Schedules by id 
 	 * 
 	 */
 	public function delete_schedules(WP_REST_Request $request){
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
 		$schedule_ids    = $request->get_param( 'schedule_ids' ); 
 		
 		if( empty( $schedule_ids) || gettype($schedule_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one schedule id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$total_delete_schedules = 0;
 		foreach( $schedule_ids as $schedule_id ){
 			if( get_post_type($schedule_id) != "schedule"):
 				continue;
 			endif;
 			if(wp_delete_post($schedule_id, true)):
 				$total_delete_schedules++;
 			endif;
 		}
 		if( $total_delete_schedules > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Schedule successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_delete_schedules' => $total_delete_schedules),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Schedules are not deleting. Maybe schedule id not exists', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

 	public function rearrange_schedules(WP_REST_Request $request){
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
 		$group_ids    = $request->get_param( 'schedule_ids' ); 
 		//var_dump($group_ids);die;
 		if( empty( $group_ids) || gettype($group_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one schedule group id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$loop_index = 0;
 		$total_rearrange_schedules = 0;
 		foreach( $group_ids as $schedule_id ){
 			if( get_post_type($schedule_id) != "schedule"):
 				continue;
 			endif;
	 		$update_args = array();
		    $update_args['ID'] = $schedule_id;
			$update_args['menu_order'] = $loop_index;
			 
			if(wp_update_post( $update_args )):
	 			 $loop_index++;
	 			 $total_rearrange_schedules++;
	 		endif;
 		}
 		if( $total_rearrange_schedules > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Schedule successfully rearraged.', 'jwt-auth' ),
					'data'       => array('total_rearrange_schedules' => $total_rearrange_schedules),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Schedule are not rearraging. Maybe schedule group id not exists', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

 	/**
 	 * Re-arrange group schedules
 	 */ 
 	public function rearrange_group_schedules(WP_REST_Request $request){
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
 		$group_ids    = $request->get_param( 'group_ids' ); 
 		//var_dump($group_ids);die;
 		if( empty( $group_ids) || gettype($group_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one schedule group id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$loop_index = 0;
 		$total_rearrange_schedules = 0;
 		foreach( $group_ids as $schedule_id ){
 			if( get_post_type($schedule_id) != "schedule_group"):
 				continue;
 			endif;
	 		$update_args = array();
		    $update_args['ID'] = $schedule_id;
			$update_args['menu_order'] = $loop_index;
			 
			if(wp_update_post( $update_args )):
	 			 $loop_index++;
	 			 $total_rearrange_schedules++;
	 		endif;
 		}
 		if( $total_rearrange_schedules > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Schedule group successfully rearraged.', 'jwt-auth' ),
					'data'       => array('total_rearrange_schedules' => $total_rearrange_schedules),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Schedule groups are not rearraging. Maybe schedule group id not exists', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

 	public function count_total_mmonth_year_between_date( $find = 'month', $from, $to, $format= "Y-m-d" ){
 		$date1 = $from;
		$date2 = $to;

		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff_year = $year2 - $year1;
		if( "year" == $find ){
			return $diff_year;
		}
		 
		$diff_month = (($diff_year) * 12) + ($month2 - $month1);
		return $diff_month;
 	}
 	public function displayDates($date1, $date2, $format = 'd-m-Y', $inner_val = array() ) {
      $dates = array();
      $current = strtotime($date1);
      $date2 = strtotime($date2);
      $stepVal = '+1 day';
      while( $current <= $date2 ) {
      	$c_date = date_i18n($format, $current);
        // $dates[] = $c_date;
        $dates[$c_date] = array();
         $current = strtotime($stepVal, $current);
      }
      return $dates;
   }
   
   	public function get_single_schedule__by_id( $schedule_id, $group_id, $startDate, $endDate, $current_date ){
   		 
	    	$group_id = get_post_meta($schedule_id, 'group_id', true);
	    	$schedule_period = get_post_meta( $group_id, 'schedule_period', true );
	     
	    	$temp = array();
			$temp = (object) $temp;
			$created_date = get_the_date('Y-m-d'); 

			$temp->id = $schedule_id;
			$temp->title = get_the_title($schedule_id);
			$temp->start = $startDate;
			$temp->end = $endDate;
			$current_date = date("Y-m-d", strtotime( $current_date ) );
			 
			// $schedule_complete = get_post_meta($schedule_id,  'schedule_complete', true );
			// $schedule_complete = ( $schedule_complete == '1') ? '1' : '0'; // 1 mean completed and 0 means not completed
			 
			$created_date_strtotime = strtotime($created_date);
			// $schedule_complete_date = get_post_meta($schedule_id, "schedule_complete_date", true);
			// if startDate is passed date according to current date
			$schedule_complete = 0;
			// $need = true;
			// if( strtotime( date_i18n("Y-m-d") ) > strtotime( $startDate ) ){
			// 	$schedule_complete = 1;
			// 	$need = false;
			// }else {

				 
			// }
			$current_date_str = (int) str_replace('-', '', $current_date );
			$startDate_str = (int) str_replace('-', '', $startDate );
			// if( $current_date_str > $startDate_str ):
			// 	$schedule_complete = 1;
			// 	$need = false;
			// endif;
			// New Condition Start //
			
			// if( $need ):
				$temp_schedule_complete = get_post_meta($schedule_id,  'schedule_complete', true );
				$temp_schedule_complete = ( $temp_schedule_complete == '1') ? '1' : '0'; // 1 mean completed and 0 means not completed
				$temp_schedule_complete = (int) $temp_schedule_complete;
				$completed_date_strtotime = get_post_meta( $schedule_id, 'schedule_complete_date_strtotime', true );
				$schedule_complete_date = get_post_meta( $schedule_id, 'schedule_complete_date', true ); 
				$temp_startDate = date("Y-m-d", strtotime( $startDate ) );
				$temp_startDate_str = (int) str_replace('-', '', $temp_startDate);
				// $completed_date_strtotime = ( '' == $completed_date_strtotime ) ? strtotime($current_date) : $completed_date_strtotime;
				if( '1' == $schedule_period ){
					// means now completed date is available
					// daily
					$schedule_complete_date = date('Y-m-d', strtotime( $schedule_complete_date ) );
					$schedule_complete_date_str = (int) str_replace('-', '', $schedule_complete_date);
					
					// if currnet date and complete mark date samed. Also schedule complete value is  1 means schedule is completed mark otherwise not completed
					if( $current_date_str > $startDate_str ){
					//past date
						$schedule_complete = 1;
					}else if( '' == trim( $completed_date_strtotime ) ){
					// means no completed date
					$schedule_complete = 0;
					}else if( ( $schedule_complete_date_str == $temp_startDate_str ) && ( 1 == $temp_schedule_complete) ){
				  		$schedule_complete = 1;
				  	}else{
				  		$schedule_complete = 0;
				  	}
				}else if( '2' == $schedule_period ){
					// means now completed date is available
					// weekly
					// if current date is less or equal to next sunday date. also schedule complete value is 1 means  schedule is completed mark otherwise not completed
					$schedule_complete_date = date('Y-m-d', strtotime( $schedule_complete_date ) );
					$schedule_complete_date_str = (int) str_replace('-', '', $schedule_complete_date);
					$saturday_date = date('Y-m-d', strtotime("saturday 0 week", strtotime( $startDate )));
					$saturday_date_str = (int) str_replace('-', '', $saturday_date);
					
					// if currnet date and complete mark date samed. Also schedule complete value is  1 means schedule is completed mark otherwise not completed
				  	if( $saturday_date_str < $current_date_str  ){
				  		// past date
				  		$schedule_complete = 1;
				  	}else if( '' == $completed_date_strtotime ){
				  		// schedule not completed by user
				  		$schedule_complete = 0;

				  	}else if( ($temp_startDate_str <= $schedule_complete_date_str) && ($schedule_complete_date_str <= $saturday_date_str) && ( 1 == $temp_schedule_complete) ){
				  		 
				  		$schedule_complete = 1;
				  	}else{
				  		$schedule_complete = 0;
				  	}
				}else if( '3' == $schedule_period ){
					// means now completed date is available
					// monthly
					// if current date is less or equal to next sunday date. also schedule complete value is 1 means  schedule is completed mark otherwise not completed
					$schedule_complete_date = date('Y-m-d', strtotime( $schedule_complete_date ) );
					$schedule_complete_date_str = (int) str_replace('-', '', $schedule_complete_date);
					 
					 
					// get last day of month
				  	 
				  	$last_day_this_month = date("Y-m-t", strtotime($startDate));
				  	$last_day_this_month_str = (int) str_replace('-', '', $last_day_this_month );
				  	if( $last_day_this_month_str < $current_date_str  ){
				  		// past date
				  		$schedule_complete = 1;
				  	}else if( '' == $completed_date_strtotime ){
				  		// schedule not completed by user
				  		$schedule_complete = 0;

				  	}else{
				  		
				  		$schedule_complete = 0;
				  		// cheched current month completed or not
				  		if( ($temp_startDate_str <= $schedule_complete_date_str) && ($schedule_complete_date_str <= $last_day_this_month_str) && ( 1 == $temp_schedule_complete) ){
						  		$schedule_complete = 1;

						  	}
				  			
				  	}
				  	// var_dump($current_date);
				  
				  	// $first_day_month_strtotime = strtotime( $first_day_this_month );
					// $sunday_date_str = (int) str_replace('-', '', $first_day_this_month);
					 
					// For Monthly, the checkbox should stay checked until the first day of the next month at 12:00am
				  	// if( $last_day_this_month_str > $cu)
				  	// if( ($temp_startDate_str <= $last_day_this_month_str) && ($last_day_this_month_str <= $sunday_date_str) && ( 1 == $temp_schedule_complete) ){
				  	// 	$schedule_complete = 1;
				  	// }else{
				  	// 	$schedule_complete = 0;
				  	// }
				}else if( '4' == $schedule_period ){
					// means now completed date is available
					// quanterly
					// 	For Quarterly, the checkbox should stay checked until:
					// - 1st Quarter: January 1st at 12:00am
					// - 2nd Quarter: April 1st at 12:00am
					// - 3rd Quarter: July 1st at 12:00am
					// - 4th Quarter: October 1st at 12:00am
					$startDate_month = date('n',strtotime($startDate));
					 

					if( (1 <= $startDate_month) && ( $startDate_month <= 3)){
						$quanter_endDate = date("Y-03-31", strtotime($startDate) );
					}else if( (4 <= $startDate_month) && ( $startDate_month <= 6)){
						$quanter_endDate = date("Y-06-30", strtotime($startDate) );
					}else if( (7 <= $startDate_month) && ( $startDate_month <= 9)){
						$quanter_endDate = date("Y-09-30", strtotime($startDate) );
					}else{
						// 10 - 12
						$quanter_endDate = date("Y-12-31", strtotime($startDate) );
					}
					$quanter_endDate_str = (int) str_replace('-', '', $quanter_endDate ); 
					// if current date is less or equal to next sunday date. also schedule complete value is 1 means  schedule is completed mark otherwise not completed
					$schedule_complete_date = date('Y-m-d', strtotime( $schedule_complete_date ) );
					$schedule_complete_date_str = (int) str_replace('-', '', $schedule_complete_date);
					 
				  	if( $quanter_endDate_str < $current_date_str  ){
				  		// past date
				  		$schedule_complete = 1;
				  	}else if( '' == $completed_date_strtotime ){
				  	// 	// schedule not completed by user
				  		$schedule_complete = 0;

				  	}else{
				  		
				  	// 	$schedule_complete = 0;
				  		// cheched current month completed or not
				  		if( ($temp_startDate_str <= $schedule_complete_date_str) && ($schedule_complete_date_str <= $quanter_endDate_str) && ( 1 == $temp_schedule_complete) ){
						  	$schedule_complete = 1;

						}
				  			
				  	}
				   
				}else if( '5' == $schedule_period ){
					// means now completed date is available
					// Half yearly
					// 	For Semi-annually the checkbox should stay checked until June 1st at 12:00am
					$startDate_month = date('n',strtotime($startDate));
					 

					if( (1 <= $startDate_month) && ( $startDate_month <= 6)){
						$halfYear_endDate = date("Y-06-30", strtotime($startDate) );
					}else{
						// 10 - 12
						$halfYear_endDate = date("Y-12-31", strtotime($startDate) );
					}
					$halfYear_endDate_str = (int) str_replace('-', '', $halfYear_endDate ); 
					// if current date is less or equal to next sunday date. also schedule complete value is 1 means  schedule is completed mark otherwise not completed
					$schedule_complete_date = date('Y-m-d', strtotime( $schedule_complete_date ) );
					$schedule_complete_date_str = (int) str_replace('-', '', $schedule_complete_date);
					 
				  	if( $halfYear_endDate_str < $current_date_str  ){
				  		// past date
				  		$schedule_complete = 1;
				  	}else if( '' == $completed_date_strtotime ){
				  	// 	// schedule not completed by user
				  		$schedule_complete = 0;

				  	}else{
				  		
				  	// 	$schedule_complete = 0;
				  		// cheched current month completed or not
				  		if( ($temp_startDate_str <= $schedule_complete_date_str) && ($schedule_complete_date_str <= $halfYear_endDate_str) && ( 1 == $temp_schedule_complete) ){
						  	$schedule_complete = 1;

						}
				  			
				  	}
				   
				}else if( '6' == $schedule_period ){
					// means now completed date is available
					// yearly
					// 	For annually the checkbox should stay checked until Dec 31st at 12:00am
					 
					$Year_endDate = date("Y-12-31", strtotime($startDate) );
				 
					$Year_endDate_str = (int) str_replace('-', '', $Year_endDate ); 
 
					$schedule_complete_date = date('Y-m-d', strtotime( $schedule_complete_date ) );
					$schedule_complete_date_str = (int) str_replace('-', '', $schedule_complete_date);
					 
				  	if( $Year_endDate_str < $current_date_str  ){
				  		// past date
				  		$schedule_complete = 1;
				  	}else if( '' == $completed_date_strtotime ){
				  	// 	// schedule not completed by user
				  		$schedule_complete = 0;

				  	}else{
				  		
				  	// 	$schedule_complete = 0;
				  		// cheched current month completed or not
				  		if( ($temp_startDate_str <= $schedule_complete_date_str) && ($schedule_complete_date_str <= $Year_endDate_str) && ( 1 == $temp_schedule_complete) ){
						  	$schedule_complete = 1;

						}
				  			
				  	}
				   
				}else{
					$schedule_complete = 0;
				}

					 

				 
			// endif;
			// New Condition End //
			
			$temp->schedule_id = $schedule_id;
			$temp->schedule_name = get_the_title($schedule_id);
			$temp->schedule_complete = $schedule_complete;
			 
			$temp->menu_order =get_post_field( 'menu_order', $schedule_id);
			$temp->group_id = $group_id;
			$temp->created_date = get_the_date('', $schedule_id);
			$temp->schedule_period = $schedule_period;
			return $temp;
   	}

 	public function get_schedule_calendar(WP_REST_Request $request){
 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$any_schedule_created = get_user_meta($post_author, 'any_schedule_created', true);
		$current_date  = $request->get_param( 'current_date' );
		$current_date = ( '' == trim( $current_date ) ) ? date_i18n("Y-m-d") : $current_date;
		 
    	$calendar_start_date  = $request->get_param( 'start_date' );
    	$calendar_end_date  = $request->get_param( 'end_date' );
    	$paged = 1;
    	$posts_per_page = -1;
    	$after = date_i18n('Y-m-d', strtotime($calendar_start_date)); // month start date
    	$before = date_i18n('Y-m-d',  strtotime($calendar_end_date)); // month end date

    	if( '' == $calendar_end_date || '' == $calendar_end_date){

    		$after = date_i18n('Y-m-01'); // month start date
    		$before = date_i18n('Y-m-t'); // month end date
    	}
    	// $orderby = "title";
    	// $order = "ASC";
    	$orderby = "date";
    	$order = "ASC";

    	// $before = '2022-09-23';
    	// $after = '2022-09-23';
   //  	$args = array(  
	  //       'post_type' => 'schedule',
	  //       'post_status' => 'publish',
	  //       'paged' => $paged, 
	  //       'orderby' => $orderby,
			// 'order'   => $order,
	  //       'posts_per_page' => $posts_per_page, 
	  //       'date_query' => array(
		 //        array(
		 //            'after'     => $after,
		 //            'before'    => $before,
		 //            'inclusive' => true,
		 //        ),
		 //    )
	  //   );
	    $args = array(
	    	'author' => $post_author,
	        'post_type' => 'schedule',
	        'post_status' => 'publish',
	        'paged' => $paged, 
	        'orderby' => $orderby,
			'order'   => $order,
	        'posts_per_page' => $posts_per_page, 
	        
	    );
    	
   $calendars = $this->displayDates($after, $before, 'Y-m-d');
  
    	$groups = array();
    	$the_query = new WP_Query( $args );
    	$total_number_of_schedule = $the_query->found_posts;
	    $total_calendar_events = array();
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$schedule_id = get_the_ID();
	    	$group_id = get_post_meta($schedule_id, 'group_id', true);
	    	$schedule_period = get_post_meta( $group_id, 'schedule_period', true );
	    	// var_dump( $schedule_period);
// 	    	ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
	    	$temp = array();
			$temp = (object) $temp;
			$created_date = get_the_date('Y-m-d'); 

			$temp->id = $schedule_id;
			$temp->title = get_the_title();
			$temp->start = $created_date .' 00:00:01';
			$temp->end = $created_date. ' 23:59:59';

			$schedule_complete = get_post_meta($schedule_id,  'schedule_complete', true );
			$schedule_complete = ( $schedule_complete == '1') ? '1' : '0'; // 1 mean completed and 0 means not completed
			$temp->schedule_id = $schedule_id;
			$temp->schedule_name = get_the_title();
			$temp->schedule_complete = $schedule_complete;
			$temp->menu_order =get_post_field( 'menu_order', $schedule_id);
			$temp->group_id = $group_id;
			$temp->created_date = get_the_date();
			$temp->schedule_period = $schedule_period;

			
			 


	       	// $temp->schedule_period = $schedule_period;
	    		//daily
	    	if( 1 == $schedule_period){
	    		foreach( $calendars as $key => $calendar_date):
					 
					$event_data = $this->get_single_schedule__by_id($schedule_id, $group_id, $key .' 00:00:01', $key. ' 23:59:59', $current_date );
	    				 
	    			array_push( $total_calendar_events, $event_data);
	    			// array_push( $calendars[$key], $temp);
	    		endforeach;
	    	}else if( 2 == $schedule_period){
	    		//weekly
	    		$schedule_weekly = date_i18n('w', strtotime($created_date));
	    		foreach( $calendars as $key => $calendar_date):
	    			
	    			$date_weekly = date_i18n('w', strtotime($key));

					if( $date_weekly === $schedule_weekly){
	    				 
	    				$event_data = $this->get_single_schedule__by_id($schedule_id, $group_id, $key .' 00:00:01', $key. ' 23:59:59', $current_date );
	    				 
	    				array_push( $total_calendar_events, $event_data);
	    				// array_push( $calendars[$key], $temp);
	    			}
	    			
	    		endforeach;
	    	}else if( 3 == $schedule_period){
	    		//monthly
	    		$total_months = $this->count_total_mmonth_year_between_date('month', $created_date, $before);
	    		$total_months = $total_months +1;
	    		for( $i=1; $i <= $total_months;$i++){
	    			$str_string = "+".$i." month";
	    			$loop_published_date = date_i18n("Y-m-d", strtotime($str_string, strtotime($created_date) ) );
	    			foreach( $calendars as $key => $calendar_date):
						 
						if( $loop_published_date === $key){
							 
							$event_data = $this->get_single_schedule__by_id($schedule_id, $group_id, $key .' 00:00:01', $key. ' 23:59:59', $current_date );
	    				 
	    				array_push( $total_calendar_events, $event_data);
							// array_push( $calendars[$key], $temp);
						}
	    			endforeach; 
	    		}
	    		
	    	}else if( 4 == $schedule_period){
	    		//quateraly
	    		$total_months = $this->count_total_mmonth_year_between_date('month', $created_date, $before);
	    		$total_months = $total_months +1;
	    		for( $i=1; $i <= $total_months;$i=$i+3){
	    			$str_string = "+".$i." month";
	    			 
	    			$loop_published_date = date_i18n("Y-m-d", strtotime($str_string, strtotime($created_date) ) );
	    			 
	    			foreach( $calendars as $key => $calendar_date):
						 
						if( $loop_published_date === $key){
						 
							$event_data = $this->get_single_schedule__by_id($schedule_id, $group_id, $key .' 00:00:01', $key. ' 23:59:59', $current_date );
	    				 
	    					array_push( $total_calendar_events, $event_data);
							// array_push( $calendars[$key], $temp);
						}
	    			endforeach; 
	    		}
	    	}else if( 5 == $schedule_period){
	    		// Half Yearly
	    		$total_months = $this->count_total_mmonth_year_between_date('month', $created_date, $before);
	    		$total_months = $total_months +1;
	    		for( $i=1; $i <= $total_months;$i=$i+6){
	    			$str_string = "+".$i." month";
	    			 
	    			$loop_published_date = date_i18n("Y-m-d", strtotime($str_string, strtotime($created_date) ) );
	    			 
	    			foreach( $calendars as $key => $calendar_date):
						 
						if( $loop_published_date === $key){
						 
							$event_data = $this->get_single_schedule__by_id($schedule_id, $group_id, $key .' 00:00:01', $key. ' 23:59:59', $current_date );
	    				 
	    					array_push( $total_calendar_events, $event_data);
							// array_push( $calendars[$key], $temp);
						}
	    			endforeach; 
	    		}
	    	}else if( 6 == $schedule_period){
	    		//Yearly
	    		$total_years = $this->count_total_mmonth_year_between_date('year', $created_date, $before);
	    		$total_years = $total_years +1;
	    		for( $i=1; $i <= $total_years;$i++){
	    			$str_string = "+".$i." year";
	    			$loop_published_date = date_i18n("Y-m-d", strtotime($str_string, strtotime($created_date) ) );
	    			foreach( $calendars as $key => $calendar_date):
						 
						if( $loop_published_date === $key){
							$event_data = $this->get_single_schedule__by_id($schedule_id, $group_id, $key .' 00:00:01', $key. ' 23:59:59', $current_date );
	    				 
	    					array_push( $total_calendar_events, $event_data);
							// array_push( $calendars[$key], $temp);
						}
	    			endforeach; 
	    		}
	    	}else{
	    		//custom
	    		
		        if (array_key_exists( $created_date, $calendars)){
		        	$schedule_complete = 0;
					if( strtotime( date_i18n("Y-m-d") ) > strtotime( $temp->start ) ){
						$schedule_complete = 1;
					}
					$temp->schedule_complete = $schedule_complete;
		        	 array_push( $total_calendar_events, $temp);
			       // array_push( $calendars[$created_date], $temp);
		        }
	    	}
	        
	        
	    endwhile;

	    wp_reset_postdata(); 
	    // echo "<pre>";
					// 	print_r( $total_calendar_events);
					// 	echo "</pre>";
    	return new WP_REST_Response(
			array(
				'success'    => true,
				'statusCode' => 200,
				'code'       => 'success',
				'message'    => __( 'Schedule successfully getting.', 'jwt-auth' ),
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				
				'data'      => array(),
				'calendars'=> $total_calendar_events,
				'any_record_created' => $any_schedule_created,
			),
			
		);
    }

}

new API_ScheduleController();
?>
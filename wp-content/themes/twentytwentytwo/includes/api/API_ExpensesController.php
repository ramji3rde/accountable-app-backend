<?php

class API_ExpensesController extends API_BaseController {

	public function __construct() 
	{  
		// add_filter( 'posts_orderby', array($this, 'color_orderby'), 10, 2 );
 	}

 	/**
	 * Search or get all expenses with pagination
	 * 
	 */ 	
    public function get_all_expenses(WP_REST_Request $request){

    	$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		$any_expenses_created = get_user_meta($post_author, 'any_expenses_created', true);

    	$total_expenses = 0;
    	$args = array(  
    	 	'author' => $post_author,
	        'post_type' => 'expense',
	        'post_status' => 'publish',
	        'posts_per_page' => -1, 
	        
	    );
    	$expenses = array();
    	$the_query = new WP_Query( $args );
    	$total_expenses_in_sp = $the_query->found_posts;
	   
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$expense_id = get_the_ID();
	        $expense_info = get_post( $expense_id );

	        if ( empty($expense_info) ) :
	        	continue;
	        endif;
	        $temp_expense = array();
	        $temp_expense = (object) $temp_expense;
	        $temp_expense->expense_id = $expense_id;
	    	$temp_expense->created_date = $expense_info->post_date;
	    	$temp_expense->item_name = $expense_info->post_title;
	    	$temp_expense->description = $expense_info->post_content;
	    	$temp_expense->author_id = $expense_info->post_author;
	    	$temp_expense->purchase_date = get_post_meta($expense_id, 'purchase_date', true);
	        $temp_expense->expense_amount = get_post_meta($expense_id, 'expense_amount', true);
	        $temp_expense->property_name = get_post_meta($expense_id, 'property_name', true);
	        $expense_category =  get_post_meta($expense_id, 'expense_category', true);
	        $expense_category = ( 'zzzzzzzzzz'  == $expense_category ) ? '' : $expense_category ;
	        $temp_expense->expense_category = $expense_category;

	        $notes = get_posts( 
				array(
					'numberposts' => 1,
					'orderby'  => 'date',
					'order' => 'ASC',
					'post_type'   => 'notes',
					'post_parent' => $expense_id,
					'post_status' => array('publish'),

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

			$temp_expense->all_notes = $all_notes;

	        
	       array_push( $expenses, $temp_expense);
	    endwhile;

	    wp_reset_postdata(); 

	    // again get data without pagination
	    $args['paged'] = 1;
	    $args['posts_per_page'] = -1;
	    $the_query = new WP_Query( $args );
	    $sum_total_of_expenses_of_sp = 0;
	    // $sum_total_of_each_category = array();
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$expense_id = get_the_ID();
	    	$amount = get_post_meta($expense_id, 'expense_amount', true);
	    	$amount = ($amount > -1 )? $amount : 0;
	    	$sum_total_of_expenses_of_sp = $sum_total_of_expenses_of_sp + $amount;

	    		
	    endwhile;
	    wp_reset_postdata(); 
    	return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'expenses successfully getting.', 'jwt-auth' ),
					'total_expenses_in_sp' => $total_expenses_in_sp,
					'sum_total_of_expenses_in_sp' => $sum_total_of_expenses_of_sp,
					// 'sum_total_of_each_category' => $sum_total_of_each_category,
					'data'       => $expenses,
					'any_expenses_created' => $any_expenses_created
				),
				
			); 
    }
 	 
 	public function get_single_expense(WP_REST_Request $request)
 	{
 		$expense_id    = $request->get_param( 'expenseId' );  

 		if ( ( empty($expense_id) ) || ( 'expense' != get_post_type($expense_id) ) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Please enter expense ID', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
	    }

 
		$expense_info = get_post( $expense_id );

		if ( empty($expense_info) ) { 

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This expense is not found', 'jwt-auth' ),
					'data'       => array(),
				),
				
			); 
			
	    }else{

	    	$temp_expense = array();
	    	$temp_expense = (object) $temp_expense;
	    	
	    	$temp_expense->expense_id = $expense_id;
	    	$temp_expense->created_date = $expense_info->post_date;
	    	$temp_expense->item_name = $expense_info->post_title;
	    	$temp_expense->description = $expense_info->post_content;
	    	$temp_expense->author_id = $expense_info->post_author;
	    	$temp_expense->purchase_date = get_post_meta($expense_id, 'purchase_date', true);
	        $temp_expense->expense_amount = get_post_meta($expense_id, 'expense_amount', true);
	        $temp_expense->property_name = get_post_meta($expense_id, 'property_name', true);
	        $expense_category = get_post_meta($expense_id, 'expense_category', true);
	        $expense_category = ( 'zzzzzzzzzz'  == $expense_category ) ? '' : $expense_category ;
	        $temp_expense->expense_category = $expense_category;
	        // $categories = get_the_terms( $expense_id, "expense_category");
	        $attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $expense_id,
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
			// $category_list = array();
			// foreach( $categories  as $category):

			// 	$temp = array();
			// 	$temp = (object) $temp;
			// 	$temp->category_id = $category->term_id;
			// 	$temp->category_name = $category->name;
			// 	array_push( $category_list,  $temp );
			// endforeach;
			$notes = get_posts( 
				array( 
					'post_type'   => 'notes',
					'post_parent' => $expense_id,
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
	        	
	        $temp_expense->notes = $all_notes; 
			// $temp_expense->category_list = $category_list;
			$temp_expense->photos = $photos;
		 
			return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'expense Details', 'jwt-auth' ),
						'data'       => $temp_expense,
					),
					 
				);
		}

 	}
 	/**
 	 * Update a expense
 	 * 
 	 */ 
 	public function update_expense(WP_REST_Request $request)
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
 		$expense_id    = $request->get_param( 'expenseId' ); 
 		$item_name    = $request->get_param( 'item_name' );
 		$purchase_date    = $request->get_param( 'purchase_date' ); 
 		$expense_amount    = $request->get_param( 'expense_amount' ); 
 		$expense_category    = $request->get_param( 'expense_category' );
 		$expense_category = trim( $expense_category );
 		$expense_category = ( ''  != $expense_category ) ? $expense_category : 'zzzzzzzzzz';
 		$expense_detail    = $request->get_param( 'expense_detail' ); 
 		// $category_ids    = $request->get_param( 'category_ids' ); // array
 		$property_name = $request->get_param('property_name');
 		if( ( empty( $expense_id ) ) || "expense" != get_post_type( $expense_id ) ) { 

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
 		if(empty($purchase_date)){
 			$errors_arr[] = __( 'Please enter purchase date.', 'jwt-auth' ); 
		}
		if(empty($expense_amount)){
 			$errors_arr[] = __( 'Please enter expense amount.', 'jwt-auth' ); 
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
	    $update_args['ID'] = $expense_id;
		$update_args['post_title'] = $item_name; //  Title
		$update_args['post_content'] = $expense_detail; // Description
 		if(wp_update_post( $update_args )):
			update_post_meta($expense_id, 'item_name', $item_name);
	        update_post_meta($expense_id, 'expense_detail', $expense_detail);
	        update_post_meta($expense_id, 'purchase_date', $purchase_date);
	        update_post_meta($expense_id, 'expense_amount', $expense_amount);
	        update_post_meta($expense_id, 'property_name', $property_name);
	        update_post_meta($expense_id, 'expense_category', $expense_category);
	        // get old terms using expense id
	        // $taxonomy = 'expense_category';
	        // $old_terms = get_the_terms( $expense_id, $taxonomy);

	        // if ( ! empty( $old_terms ) ) {
	        // 	foreach ( $terms as $term ) {
	        // 		// remove old terms from the post
	        // 		wp_remove_object_terms( $expense_id, $term->term_id, $taxonomy );
	        // 	}
	        // }
	        // now assing new terms in a post

	        // foreach( $category_ids as $cat_id ):
				
				// wp_set_object_terms($expense_id, $category_ids, $taxonomy);
			// endforeach;
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
 	 * create a new expense 
 	 * 
 	 */ 
 	public function create_new_expense(WP_REST_Request $request)
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
 		$purchase_date    = $request->get_param( 'purchase_date' ); 
 		$expense_amount    = $request->get_param( 'expense_amount' ); 
 		$expense_detail    = $request->get_param( 'expense_detail' ); 
 		$expense_category    = $request->get_param( 'expense_category' );
 		$expense_category = trim( $expense_category );
 		$expense_category = ( ''  != $expense_category ) ? $expense_category : 'zzzzzzzzzz';
 	 	$property_name    = $request->get_param( 'property_name' );
		$errors_arr = array();
		if(empty($item_name)){

 			$errors_arr[] = __( 'Please enter item name.', 'jwt-auth' ); 
		}
 		if(empty($purchase_date)){

 			$errors_arr[] = __( 'Please enter purchase date.', 'jwt-auth' ); 
		}
		if(empty($expense_amount)){

 			$errors_arr[] = __( 'Please enter expense amount.', 'jwt-auth' ); 
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
		$ExpenseRes = wp_insert_post(array(
			'post_title'     => $item_name,
			'post_content'  => $expense_detail,
			'post_type'     => 'expense',
			'post_status'   => 'publish',
			'post_author'   => $post_author,
			 
		));
		if( isset( $ExpenseRes ) && is_numeric( $ExpenseRes ) ){
			update_user_meta($post_author, 'any_expenses_created', 'yes');

			update_post_meta($ExpenseRes, 'item_name', $item_name);
	        update_post_meta($ExpenseRes, 'expense_detail', $expense_detail);
        	update_post_meta($ExpenseRes, 'purchase_date', $purchase_date);
	        update_post_meta($ExpenseRes, 'expense_amount', $expense_amount);
	        update_post_meta($ExpenseRes, 'property_name', $property_name);
	        update_post_meta($ExpenseRes, 'expense_category', $expense_category);
	        
	        // $taxonomy = 'expense_category';
	        // foreach( $category_ids as $cat_id ):
				// wp_set_object_terms($ExpenseRes, $category_ids, $taxonomy);
			// endforeach;
	        return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Expense successfully created.', 'jwt-auth' ),
					'data'       => array('expense_id' => $ExpenseRes),
				),
				
			); 

		} else {
			$massage ='';
			foreach ($ExpenseRes->errors as $key => $errors) {
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
 	 * Delete multiple expense by id 
 	 * 
 	 */
 	public function delete_expense(WP_REST_Request $request){
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
 		//contect info
 		$expense_ids    = $request->get_param( 'expense_ids' ); 
 		
 		if( empty( $expense_ids) || gettype($expense_ids) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one expense id is required', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		}

 		$total_delete_expenses = 0;

 		foreach( $expense_ids as $expense_id ){
 			if( get_post_type($expense_id) != "expense"):
 				continue;
 			endif;
 			if(wp_delete_post($expense_id, true)):
 				// delete attachment/photos
 				$attachment_args = array(
				    'post_type' => "attachment",
				    'post_parent' => $expense_id,
				    'posts_per_page' => -1,
				);
				$attachments = get_posts($attachment_args);
				foreach( $attachments  as $attachment):
				    $API_PhotosController->ballon_delete_media_from_s3($attachment->guid);
				    wp_delete_attachment($attachment->ID, true);

				endforeach;
 				$total_delete_expenses++;
 				
 				// delete notes
 				$notes = get_posts( 
					array( 
						'post_type'   => 'notes',
						'post_parent' => $expense_id,
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
 		if( $total_delete_expenses > 0 ):
 			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'expenses successfully deleted.', 'jwt-auth' ),
					'data'       => array('total_deleted_expenses' => $total_delete_expenses),
				),
				
			); 
 		else:
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Due to technical issue not deleting any expense', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

	/**
	 * Search or get all expenses with pagination
	 * 
	 */ 	
    public function search_expenses(WP_REST_Request $request){

    	$search_by_keyword    = $request->get_param( 'search_by_keyword' );
    	$paged = ($request->get_param('paged')) ? $request->get_param('paged') : 1;
    	$posts_per_page = ($request->get_param('posts_per_page')) ? $request->get_param('posts_per_page') : 10;
    	$sort_by_field    = $request->get_param( 'sort_by_field' );
    	$start_date = $request->get_param('start_date');
    	$end_date = $request->get_param('end_date');
    	$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		$any_expenses_created = get_user_meta($post_author, 'any_expenses_created', true);
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
    	if( 'date' == $sort_by_field ):
    		$orderby = "date";
    		$order = "ASC";
    	endif;
    	// if( 'category' == $sort_by_field ):
    	// 	$orderby = "category";
    	// 	$order = "ASC";
    	// endif;
    	$total_expenses = 0;
    	$args = array(  
    	 	'author' => $post_author,
	        'post_type' => 'expense',
	        'post_status' => 'publish',
	        'paged' => $paged, 
	        'orderby' => $orderby,
			'order'   => $order,
	        'posts_per_page' => $posts_per_page, 
	        
	    );
    	// if( 'status' == $sort_by_field ):
    	//  	$args['meta_key'] = "status";
    	// 	$args['orderby'] = "meta_value";
    	// 	$args['order'] = "ASC";
    	// endif;
    	// if( 'date' == $sort_by_field ):
    	//  	$args['meta_key'] = "date";
    	// 	// $args['orderby'] = "meta_value_num";
    	// 	$args['order'] = "ASC";
    	// endif;
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
 		// echo "<pre>";
 		// print_r( $args );
 		// echo "</pre>";
    	$expenses = array();
    	$the_query = new WP_Query( $args );
    	$total_expenses_in_sp = $the_query->found_posts;
	   
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$expense_id = get_the_ID();
	        $expense_info = get_post( $expense_id );

	        if ( empty($expense_info) ) :
	        	continue;
	        endif;
	        $temp_expense = array();
	        $temp_expense = (object) $temp_expense;
	        $temp_expense->expense_id = $expense_id;
	    	$temp_expense->created_date = $expense_info->post_date;
	    	$temp_expense->item_name = $expense_info->post_title;
	    	$temp_expense->description = $expense_info->post_content;
	    	$temp_expense->author_id = $expense_info->post_author;
	    	$temp_expense->purchase_date = get_post_meta($expense_id, 'purchase_date', true);
	        $temp_expense->expense_amount = get_post_meta($expense_id, 'expense_amount', true);
	        $temp_expense->property_name = get_post_meta($expense_id, 'property_name', true);
	        $expense_category =  get_post_meta($expense_id, 'expense_category', true);
	        $expense_category = ( 'zzzzzzzzzz'  == $expense_category ) ? '' : $expense_category ;
	        $temp_expense->expense_category = $expense_category;

	        $notes = get_posts( 
				array(
					'numberposts' => 1,
					'orderby'  => 'date',
					'order' => 'ASC',
					'post_type'   => 'notes',
					'post_parent' => $expense_id,
					'post_status' => array('publish'),

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

			$temp_expense->all_notes = $all_notes;

	        // $categories = get_the_terms( $expense_id, "expense_category");
    		// $category_list = array();
    		// if(  (is_array($categories) || is_object($categories)  )):
    		// 	foreach( $categories  as $category):

			// 	$temp = array();
			// 	$temp = (object) $temp;
			// 	$temp->category_id = $category->term_id;
			// 	$temp->category_name = $category->name;
			// 	array_push( $category_list,  $temp );
			// endforeach;
    		// endif;
			
			// $temp_expense->category_list = $category_list;
	       array_push( $expenses, $temp_expense);
	    endwhile;

	    wp_reset_postdata(); 

	    // again get data without pagination
	    $args['paged'] = 1;
	    $args['posts_per_page'] = -1;
	    $the_query = new WP_Query( $args );
	    $sum_total_of_expenses_of_sp = 0;
	    // $sum_total_of_each_category = array();
	    while ( $the_query->have_posts() ) : $the_query->the_post();
	    	$expense_id = get_the_ID();
	    	$amount = get_post_meta($expense_id, 'expense_amount', true);
	    	$amount = ($amount > -1 )? $amount : 0;
	    	$sum_total_of_expenses_of_sp = $sum_total_of_expenses_of_sp + $amount;

	    	// $categories = get_the_terms( $expense_id, "expense_category");
	    	// if(  (is_array($categories) || is_object($categories)  )):
	    	//  	foreach( $categories  as $category):
	    	//  		$cat_name = trim( $category->name);
	    	//  		if( array_key_exists( $cat_name, $sum_total_of_each_category)){
	    	//  			$sum_total_of_each_category[$cat_name] = $sum_total_of_each_category[$cat_name] + $amount;
	    	//  		}else{
	    	//  			$sum_total_of_each_category[$cat_name] = $amount;
	    	//  		}
			// 	endforeach;
			// endif;	
	    endwhile;
	    wp_reset_postdata(); 
    	return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'expenses successfully getting.', 'jwt-auth' ),
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'total_expenses_in_sp' => $total_expenses_in_sp,
					'sum_total_of_expenses_in_sp' => $sum_total_of_expenses_of_sp,
					// 'sum_total_of_each_category' => $sum_total_of_each_category,
					'data'       => $expenses,
					'any_expenses_created' => $any_expenses_created
				),
				
			); 
    }

    /**
     * Create a expense category
     * 
     */ 

    public function create_expense_category( WP_REST_Request $request ){
    	$category_name = $request->get_param( 'category_name' );
    	$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		 
    	$errors_arr = array();
		if(empty($category_name)){

 			$errors_arr[] = __( 'Please enter category name.', 'jwt-auth' ); 
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
    	$rest = wp_insert_term( $category_name, 'expense_category');
    	if( $rest ){
    		$term_id = $rest['term_id'];
    		update_term_meta( $term_id, 'term_author', $post_author);
    		return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Category successfully created.', 'jwt-auth' ),
					'data'       => array('category_id' => $rest),
				),
				
			); 
    	}else {
			$massage ='';
			foreach ($rest->errors as $key => $errors) {
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
     * get all expense category
     * 
     */ 

    public function get_all_expense_categories( WP_REST_Request $request ){
		$API_BaseController = new API_BaseController();
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		 
    	$terms = get_terms( array(
		    'taxonomy' => 'expense_category',
		    'hide_empty' => false,
		    'meta_query' => array(
			         array(
			            'key'       => 'term_author',
			            'value'     => $post_author,
			            'compare'   => '='
			         )
			    )
			) 
    	);
    	 
		return new WP_REST_Response(
			array(
				'success'    => true,
				'statusCode' => 200,
				'code'       => 'success',
				'message'    => __( 'category getting.', 'jwt-auth' ),
				'data'       => $terms,
			),
			
		); 
    	  

    }
    /** Show expenses order by category */
    public function color_orderby( $orderby, $wp_query ) {
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'category' == $wp_query->query['orderby'] ) {
			$orderby = "(
				SELECT GROUP_CONCAT(name ORDER BY name ASC)
				FROM $wpdb->term_relationships
				INNER JOIN $wpdb->term_taxonomy USING (term_taxonomy_id)
				INNER JOIN $wpdb->terms USING (term_id)
				WHERE $wpdb->posts.ID = object_id
				AND taxonomy = 'expense_category'
				GROUP BY object_id
			) ";
			$orderby .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
		}

		return $orderby;
	}


}

new API_ExpensesController();
?>
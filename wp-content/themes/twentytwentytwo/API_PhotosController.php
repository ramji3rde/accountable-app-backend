<?php

class API_PhotosController extends API_BaseController {

	public function __construct() 
	{  

 	}
 	public function import_base64_image($imageData){
 		$upload_dir = wp_upload_dir();

 		$post_title = $imageData['image_detail'];
		$post_author = $imageData['post_author'];
		$parent_post = $imageData['parent_post'];		 
		// @new
		$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

		 $image_parts = explode(";base64,",$imageData['image']);
		// $decoded = $image;
		$decoded = base64_decode($image_parts[1]);
		$filename = '.png';

		$hashed_filename = md5( $filename . microtime() ) . '_' . $filename;

		// @new
		$image_upload = file_put_contents( $upload_path . $hashed_filename, $decoded );

		//HANDLE UPLOADED FILE
		require_once(ABSPATH . 'wp-admin/includes/image.php');
    	require_once(ABSPATH . 'wp-admin/includes/media.php');
    	require_once(ABSPATH . 'wp-admin/includes/file.php');
		// if( !function_exists( 'wp_handle_sideload' ) ) {
		//   require_once( ABSPATH . 'wp-admin/includes/file.php' );
		// }

		// // Without that I'm getting a debug error!?
		// if( !function_exists( 'wp_get_current_user' ) ) {
		//   require_once( ABSPATH . 'wp-includes/pluggable.php' );
		// }

		// @new
		$file             = array();
		$file['error']    = '';
		$file['tmp_name'] = $upload_path . $hashed_filename;
		$file['name']     = $hashed_filename;
		$file['type']     = 'image/png';
		$file['size']     = filesize( $upload_path . $hashed_filename );

		// upload file to server
		// @new use $file instead of $image_upload
		$file_return = wp_handle_sideload( $file, array( 'test_form' => false ) );
		// $file_return = media_handle_sideload($file, $parent_post);
// var_dump($file);
		
		if( !isset($file_return['file'], $file_return['url'])){
			 
			return false;
		}
		 
	 
		$filename = $file_return['file'];
		
		$attachment = array(
		 'post_mime_type' => $file_return['type'],
		 'post_title' => preg_replace('/\.[^.]+$/', '', basename($post_title)),
		 'post_content' => $post_title,
		 'post_author' => $post_author,
		 'post_status' => 'inherit',
		 'guid' => $upload_dir['url'] . '/' . basename($filename)
		 );
		$attach_id = wp_insert_attachment( $attachment, $filename, $parent_post );
		// var_dump($attachment);
		// var_dump($filename);
		return  $attach_id;
 	}
 	 

 	 
 	/**
 	 * Upload photo for post type like: tenant, schedule and etc.
 	 */ 
 	public function upload_post_photos(WP_REST_Request $request)
 	{
 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		$post_id = $request->get_param( 'post_id' ); 
 		$author  = $request->get_param( 'author' ); 
 		$photos  = $request->get_param( 'photos' ); 
 		 
 		if( empty( $photos) || gettype($photos) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one image required', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
				
			);
 		}
 		$total_upload_photos = array();
 		foreach( $photos as $photo_data ){
 			if( '' == $photo_data['image'] ):
 				continue;
 			endif;
 			 
 			$imageData = array(
 				'image' => $photo_data['image'],
 				'image_detail' => $photo_data['detail'],
 				'parent_post' => $post_id,
 				'post_author' => $post_author
 			);
 			$insert_status = $this->import_base64_image($imageData);
 			 
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
					'message'    => __( 'Due to technical issue not deleting', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;

 
 	}

 	/**
 	 * Upload photo for user like: Contractor.
 	 */ 
 	public function upload_user_photos(WP_REST_Request $request)
 	{
 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		$user_id = $request->get_param( 'user_id' ); 
 		// $author  = $request->get_param( 'author' ); 
 		$photos  = $request->get_param( 'photos' ); 
 		 
 		if( empty( $photos) || gettype($photos) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one image required', 'jwt-auth' ),
					'data'       => $errors_arr,
				),
				
			);
 		}
 		$total_upload_photos = array();
 		foreach( $photos as $photo_data ){
 			if( '' == $photo_data['image'] ):
 				continue;
 			endif;
 			 
 			$imageData = array(
 				'image' => $photo_data['image'],
 				'image_detail' => $photo_data['detail'],
 				'post_author' => $user_id
 			);
 			$insert_status = $this->import_base64_image($imageData);
 			 
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
					'message'    => __( 'Due to technical issue not deleting', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;

 
 	}

 	public function delete_photo_by_id(WP_REST_Request $request){
 		//contect info
 		$photo_ids    = $request->get_param( 'photo_ids' ); 
 		
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
 			// if( get_post_type($tenant_id) != "tenants"):
 			// 	continue;
 			// endif;
 			if(wp_delete_attachment($photo_id, true)):
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
					'message'    => __( 'Due to technical issue not deleting any tenant', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}

 	public function update_photo_by_id(WP_REST_Request $request){
 		//contect info
 		$author    = $request->get_param( 'author' ); 
 		$photos    = $request->get_param( 'photos' ); 
 		if( empty( $photos) || gettype($photos) != 'array' ){
 			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'Minimum one photo is required', 'jwt-auth' ),
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
					'message'    => __( 'Due to technical issue not deleting', 'jwt-auth' ),
					'data'       => '',
				),
				
			); 
 		endif;
 		
 	}
}

new API_PhotosController();
?>
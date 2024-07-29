<?php
use Aws\S3\S3Client;
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
		
		$media_type = $image_parts[0];
		$media_type_array = explode("/", $media_type);
		// $filename = '.PDF';
		$filename = '.'.$media_type_array[1];
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
		$file['type']     = $media_type;
		$file['size']     = filesize( $upload_path . $hashed_filename );

		// upload file to server
		// @new use $file instead of $image_upload
		$file_return = wp_handle_sideload( $file, array( 'test_form' => false ) );
		// $file_return = media_handle_sideload($file, $parent_post);
		
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
 	public function import_base64_docs($imageData, $final_path, $changed_name){
 		$upload_dir = wp_upload_dir();

 		$post_title = $imageData['image_detail'];
		$post_author = $imageData['post_author'];
		$parent_post = $imageData['parent_post'];		 
		
		$media_type = $imageData['image']['upload_media']['type'];
		 
		$fileName = $imageData['image']['upload_media']['name'];
		 
		$temp_name = $imageData['image']['upload_media']['tmp_name'];
		 

		//HANDLE UPLOADED FILE
		require_once(ABSPATH . 'wp-admin/includes/image.php');
    	require_once(ABSPATH . 'wp-admin/includes/media.php');
    	require_once(ABSPATH . 'wp-admin/includes/file.php');
		// @new
		$file             = array();
		$file['error']    = '';
		$file['tmp_name'] = $final_path;
		$file['name']     = $changed_name;
		$file['type']     = $media_type;
		// $file['size']     = filesize( $upload_path . $hashed_filename );
		$file['size']     = filesize( $final_path);
		// upload file to server
		// @new use $file instead of $image_upload
		$file_return = wp_handle_sideload( $file, array( 'test_form' => false ) );
		// $file_return = media_handle_sideload($file, $parent_post);
 
		
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
		return  $attach_id;
 	}
 	
 	/**
 	 * Delete Media from the S3 buckets
 	 */ 
    public function ballon_delete_media_from_s3( $url ){
    	$buckect = 'smi-reports-development';
		$find = 'https://'.$buckect.'.s3.amazonaws.com/';
		$new_string = str_replace($find, '', $url); // like tenants/0bfa8291a76a4c441511047464788611_pdf.pdf
    	$s3Client = new S3Client([
			'version' => 'latest',
			'region'  => 'us-east-1',
			'credentials' => [
			'key'    => AS3CF_SETTINGS['access-key-id'],
			'secret' => AS3CF_SETTINGS['secret-access-key']
			]
		]);
		$bucket = AS3CF_SETTINGS['buckect'];
	    try {
		    $result = $s3Client->deleteObject(array(
		        'Bucket' => $bucket,
		        'Key'    => $new_string
		        )); 
		    return 1;
		} catch (S3Exception $e) {
			return 0;
		    // return $e->getMessage() . PHP_EOL;
		}
    }

    /**
     * Upload media from wordpress to S3
     */ 
    
    public function ballon_upload_media_in_s3($key, $final_path ){
    	$s3_bucket_file_url = '';
	
    	// Instantiate an Amazon S3 client.
		$s3Client = new S3Client([
			'version' => 'latest',
			'region'  => 'us-east-1',
			'credentials' => [
			'key'    => AS3CF_SETTINGS['access-key-id'],
			'secret' => AS3CF_SETTINGS['secret-access-key']
			]
		]);
		$bucket = AS3CF_SETTINGS['buckect'];
		$output = array('status' => false,
	                     'message' => '',
	                 	'url' => '');
		try {
				$result = $s3Client->putObject([
				'Bucket' => $bucket,
				'Key'    => $key,
				'Body'   => fopen($final_path, 'r'),
				'ACL'    => 'public-read', // make file 'public'
				]);

			$s3_bucket_file_url = $result->get('ObjectURL');
			$output['status'] = true;
			$output['message'] = 'uploaded';
			$output['url'] = $s3_bucket_file_url;

		} catch (Aws\S3\Exception\S3Exception $e) {
				// echo "There was an error uploading the file.\n";
			$errorMessage = $e->getMessage();
			$output['status'] = false;
			$output['message'] = $errorMessage;
			$output['url'] = '';
		
    	}

    	return $output;
    }
 	/**
 * Upload photo for post type like: tenant, schedule and etc.
 */
public function upload_post_add_docs(WP_REST_Request $request)
{
    $API_BaseController = new API_BaseController();
    $post_author = $API_BaseController->custom_validate_token($_SERVER);
    $permission = $API_BaseController->check_user_permission($_SERVER, 'create');
    if (!$permission) {
        return new WP_REST_Response(
            array(
                'success'    => false,
                'statusCode' => 403,
                'code'       => 'error',
                'message'    => __('permission denied.', 'jwt-auth'),
                'data'       => array(),
            ),
        );
    }

    $user_post_id = $request->get_param('user_post_id');
    $author  = $request->get_param('author');
    $detail  = $request->get_param('detail');
    $upload_for  = trim($request->get_param('upload_for')); // default post
    $upload_for = ('user' == $upload_for) ? 'user' : 'post';

    $uploading_folder_name = 'user';
    if ('post' == $upload_for) {
        $post_type = get_post_type($user_post_id);
        $uploading_folder_name = ($post_type != false) ? $post_type : 'other';
    }

    $post_author = $API_BaseController->custom_validate_token($_SERVER);
    // $post_author = ('user' == $upload_for) ? $user_post_id : $post_author;
    $upload_dir = wp_upload_dir();
    // $path = $upload_dir['path'];
    $path = $upload_dir['basedir'];

    $uploaded_files = $_FILES['upload_media'];
    $file_count = count($uploaded_files['name']);

    $uploaded_file_ids = array();
	$uploaded_file_urls = array();
    for ($i = 0; $i < $file_count; $i++) {
        $temp_name = $uploaded_files['tmp_name'][$i];
        $fileName = $uploaded_files['name'][$i];
        // var_dump( $temp_name );

        // var_dump($fileName);
        $file_extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $hashed_filename = md5(microtime()) . '_' . $file_extension . '.' . $file_extension;
        $final_path = $path . '/' . $hashed_filename;
        // var_dump( $final_path );
        $uploadfile = move_uploaded_file($temp_name, $final_path);
        $real_file_name = str_replace('.' . $file_extension, '', $fileName);
        $real_file_name = str_replace(' ', '-', strtolower($real_file_name));
        $real_file_name = $real_file_name . '-' . rand(9876543, 3456789);

        // var_dump($path);
        // var_dump($uploadfile);
        if ($uploadfile) {
            $Files = $_FILES;
            $imageData = array(
                'image' => $Files,
                'image_detail' => $detail,
                'parent_post' => $user_post_id,
                'post_author' => $post_author,
                'upload_for' => $upload_for
            );
            if ('user' == $upload_for) {
                $imageData['parent_post'] = 0;
                $imageData['post_author'] = $user_post_id;
            }

            $bucket = AS3CF_SETTINGS['buckect'];
            $s3_bucket_file_url = '';
            // $key = basename($final_path);
            $key = $uploading_folder_name . '/' . basename($final_path);
            $s3_status = $this->ballon_upload_media_in_s3($key, $final_path);
            $errorMessage = '';
            if ($s3_status['status'] == true) {

                $s3_bucket_file_url = $s3_status['url'];
                $post_title = $imageData['image_detail'];
                $post_author = $imageData['post_author'];
                $parent_post = $imageData['parent_post'];
                $media_type = $imageData['image']['upload_media']['type'];
                $attachment = array(
                    'post_mime_type' => $media_type,
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($post_title)),
                    'post_content' => $post_title,
                    'post_author' => $post_author,
                    'post_status' => 'inherit',
                    'guid' => $s3_bucket_file_url
                );

                $insert_status = wp_insert_attachment($attachment, false, $parent_post);
                // var_dump( $final_path );
                unlink($final_path);

                if ($insert_status) {
                    update_post_meta($insert_status, 'real_file_name', $real_file_name);
                    $uploaded_file_ids[] = $insert_status;
					$uploaded_file_urls[] = $s3_bucket_file_url;
                }
            } else {
                // echo "There was an error uploading the file.\n";
                $errorMessage = $s3_status['message'];
                unlink($final_path);
            }
        }

		
    }

    if (!empty($uploaded_file_ids)) {
        return new WP_REST_Response(
            array(
                'success'    => true,
                'statusCode' => 200,
                'code'       => 'success',
                'message'    => __('Files successfully uploaded.', 'jwt-auth'),
                'data'       => array('file_ids' => $uploaded_file_ids, 's3_bucket_file_url' => $uploaded_file_urls),
            ),
        );
    } else {
        return new WP_REST_Response(
            array(
                'success'    => false,
                'statusCode' => 403,
                'code'       => 'error',
                'message'    => __('Error uploading files.', 'jwt-auth'),
                'data'       => array('s3_status'=>$s3_status),
            ),
        );
    }
}

 	
 	/**
 	 * Share private file from one to another 
 	 */ 
 	public function share_private_file(WP_REST_Request $request)
 	{

 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
		$post_author = (int) $post_author;
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
 		 
 		$user_post_id = $post_author; 
 		$author  = $post_author; 
 		$detail  = $request->get_param( 'detail' );
 		$upload_for = 'user';
 		$to_id = $request->get_param( 'to_id' );  
 		$shared_for = $request->get_param( 'shared_for' ); //  post id
 		$uploading_folder_name = 'user';
 		if( 'post' == $upload_for ){
 			$post_type = get_post_type( $user_post_id );
 			$uploading_folder_name = ( $post_type != false ) ? $post_type : 'other'; 
 		}
 	 
 		 
 		// $post_author = ( 'user' == $upload_for )? $user_post_id : $post_author;
 		$upload_dir = wp_upload_dir(); 
 		// $path = $upload_dir['path'];
 		$path = $upload_dir['basedir'];

		 $uploaded_files = $_FILES['upload_media'];
		 $file_count = count($uploaded_files['name']);
	 
		$uploaded_file_ids = array();
		$uploaded_file_urls = array();
		for ($i = 0; $i < $file_count; $i++) {
 		
			$temp_name = $uploaded_files['tmp_name'][$i];
			$fileName = $uploaded_files['name'][$i];
			// var_dump( $temp_name );

			// var_dump($fileName);
			$file_extension = pathinfo($fileName, PATHINFO_EXTENSION);
			$hashed_filename = md5(  microtime() ) . '_' . $file_extension.'.'.$file_extension;
			$final_path = $path.'/'.$hashed_filename;
			// var_dump( $final_path );
			$uploadfile = move_uploaded_file($temp_name,$final_path);
			$real_file_name = str_replace('.'.$file_extension, '', $fileName);
			$real_file_name = str_replace(' ', '-', strtolower( $real_file_name ));
			$real_file_name = $real_file_name.'-'.rand(9876543, 3456789);
			// var_dump( $uploadfile );
			// die("end");
			// var_dump($path);
			// var_dump($uploadfile);
			if( $uploadfile ){
				$Files = $_FILES; 
				$imageData = array(
					'image' => $Files,
					'image_detail' => $detail,
					'parent_post' => $user_post_id,
					'post_author' => $post_author,
					'upload_for' => $upload_for
				);
				if( 'user'== $upload_for){
					$imageData['parent_post'] = 0;
				}


				
				$bucket = AS3CF_SETTINGS['buckect'];
				$s3_bucket_file_url = '';
				// $key = basename($final_path);
				$key = $uploading_folder_name.'/'.basename($final_path);
				$s3_status = $this->ballon_upload_media_in_s3($key, $final_path);
				$errorMessage = '';
				if( $s3_status['status'] == true ) {
					
					$s3_bucket_file_url = $s3_status['url'];
					$post_title = $imageData['image_detail'];
					$post_author = $imageData['post_author'];
					$parent_post = $imageData['parent_post'];
					$media_type = $imageData['image']['upload_media']['type'];
					$attachment = array(
						'post_mime_type' => $media_type,
						'post_title' => preg_replace('/\.[^.]+$/', '', basename($post_title)),
						'post_content' => $post_title,
						'post_author' => $post_author,
						'post_status' => 'inherit',
						'guid' => $s3_bucket_file_url
					);
					
					$insert_status = wp_insert_attachment( $attachment, false, $parent_post );
					// var_dump( $final_path );
					unlink($final_path);
					update_post_meta($insert_status, 'shared_file', 'yes');
					update_post_meta($insert_status, 'shared_for', $shared_for);
					update_post_meta($insert_status, 'shared_from', $post_author);
					update_post_meta($insert_status, 'shared_to', $to_id);
					if( $insert_status ):
						update_post_meta($insert_status, 'real_file_name', $real_file_name );
						$uploaded_file_ids[] = $insert_status;
						$uploaded_file_urls[] = $s3_bucket_file_url;
						// return new WP_REST_Response(
						// 	array(
						// 		'success'    => true,
						// 		'statusCode' => 200,
						// 		'code'       => 'success',
						// 		'message'    => __( 'File successfully uploaded.', 'jwt-auth' ),
						// 		'data'       => array('file_id' => $insert_status, 's3_bucket_file_url' => $s3_bucket_file_url),
						// 	),
							
						// ); 
					endif; 
					// return new WP_REST_Response(
					// 	array(
					// 		'success'    => false,
					// 		'statusCode' => 403,
					// 		'code'       => 'error',
					// 		'message'    => __( 'This file is not uploading.', 'jwt-auth' ),
					// 		'data'       => array($uploadfile),
					// 	),
						
					// );
				} else {
					// echo "There was an error uploading the file.\n";
					$errorMessage = $s3_status['message'];
					unlink($final_path);
					// return new WP_REST_Response(
					// 	array(
					// 		'success'    => false,
					// 		'statusCode' => 403,
					// 		'code'       => 'error',
					// 		'message'    => __( $errorMessage, 'jwt-auth' ),
					// 		'data'       => array($uploadfile),
					// 	),
					
					// ); 
				
				}

			
				
			}
		}

		if (!empty($uploaded_file_ids)) {
			return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __('Files successfully uploaded.', 'jwt-auth'),
					'data'       => array('file_ids' => $uploaded_file_ids, 's3_bucket_file_url' => $uploaded_file_urls),
				),
			);
		} else {
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __('Error uploading files.', 'jwt-auth'),
					'data'       => array(),
				),
			);
		}
 	 
 	}

    /**
 	 * Upload photo for post type like: tenant, schedule and etc.
 	 */ 
 	public function upload_post_add_docs_old(WP_REST_Request $request)
 	{
 		$API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		$user_post_id = $request->get_param( 'user_post_id' ); 
 		$author  = $request->get_param( 'author' ); 
 		$detail  = $request->get_param( 'detail' );
 		$upload_for  = $request->get_param( 'upload_for' ); // default post
 		$upload_for = ( 'user' == $upload_for )? 'user' : 'post';
 		$post_author = $API_BaseController->custom_validate_token($_SERVER);
 		$post_author = ( 'user' == $upload_for )? $user_post_id : $post_author;
 		$upload_dir = wp_upload_dir(); 
 		$path = $upload_dir['path'];

 		$temp_name = $_FILES['upload_media']['tmp_name'];
 		$fileName = $_FILES['upload_media']['name'];
 		
 		// var_dump($fileName);
 		$file_extension = pathinfo($fileName, PATHINFO_EXTENSION);
 		$hashed_filename = md5(  microtime() ) . '_' . $file_extension.'.'.$file_extension;
 		$final_path = $path.'/'.$hashed_filename;
 		$uploadfile = move_uploaded_file($temp_name,$final_path);
 	 	// var_dump($path);
 	 	// var_dump($uploadfile);
 		if( $uploadfile ){
 			$Files = $_FILES; 
 			$imageData = array(
 				'image' => $Files,
 				'image_detail' => $detail,
 				'parent_post' => $user_post_id,
 				'post_author' => $post_author,
 				'upload_for' => $upload_for
 			);
 			if( 'user'== $upload_for){
 				$imageData['parent_post'] = 0;
 			}
 			
 			$insert_status = $this->import_base64_docs($imageData,$final_path, $hashed_filename);
 			if( $insert_status ):

 				return new WP_REST_Response(
					array(
						'success'    => true,
						'statusCode' => 200,
						'code'       => 'success',
						'message'    => __( 'File successfully uploaded.', 'jwt-auth' ),
						'data'       => array('file_id' => $insert_status),
					),
					
				); 
 			endif; 
 		 	return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This file is not uploading.', 'jwt-auth' ),
					'data'       => array($uploadfile),
				),
				
			); 
 			
 		}
 		return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'error',
					'message'    => __( 'This file is not uploading.', 'jwt-auth' ),
					'data'       => array($uploadfile),
				),
				
			); 
 	 
 	}

 	/**
 	 * Upload photo for post type like: tenant, schedule and etc.
 	 */ 
 	public function upload_post_photos(WP_REST_Request $request)
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
					'message'    => __( 'File successfully uploaded.', 'jwt-auth' ),
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
 		$photo_ids    = $request->get_param( 'photo_ids' ); 
 		// print_r($photo_ids);die;
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
 			$getpost  = get_post( $photo_id );
 			 

 			// if( get_post_type($tenant_id) != "tenants"):
 			// 	continue;
 			// endif;
 			//var_dump($getpost->guid);die;
 			if(isset($getpost->guid)){
 				$s3_delete_status =  $this->ballon_delete_media_from_s3($getpost->guid);
 			}
 			
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
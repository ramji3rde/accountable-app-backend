<?php

function update_attachment_user_guid( $postID, $newGUID ){
    global $wpdb;
     // $newGUID = 'https://smi-reports-development.s3.amazonaws.com/tenants/bb71a292dd6f824e171f39321e1e4a7b_pdf.pdf';
   // $postID = 62;
    $status = $wpdb->update($wpdb->posts, array('guid' => $newGUID), array('ID' => $postID));
    $wp_attached_file = get_post_meta($postID, '_wp_attached_file', true );
    update_post_meta( $postID, '_wp_attached_file_old', $wp_attached_file);
    update_post_meta( $postID, '_wp_attached_file', '');
    return $status;
}

   // $uploaded_image = array();
   // $newGUID = 'https://smi-reports-development.s3.amazonaws.com/tenants/bb71a292dd6f824e171f39321e1e4a7b_pdf.pdf';
   // $postID = 208;

   // $ss = $wpdb->update($wpdb->posts, array('guid' => $newGUID), array('ID' => $postID));
 
            
            // var_dump( $uploaded_image );
            // $dd = wp_update_post( $uploaded_image );
            // update_attached_file( 62, $uploaded_image['guid']  );
            //  update_post_meta( 258, '_wp_attached_file',  'https://smi-reports-development.s3.amazonaws.com/tenants/0bfa8291a76a4c441511047464788611_pdf.pdf' );
            // var_dump($dd );
              
require 'vendor/autoload.php';
use Aws\S3\S3Client;
// Instantiate an Amazon S3 client.
$s3Client = new S3Client([
'version' => 'latest',
'region'  => 'us-east-1',
'credentials' => [
'key'    => 'AKIA6D6DECHRL5IAJMED',
'secret' => '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'
]
]);

function delete_Image($s3Client){
    $bucket = 'smi-reports-development';
     try {
    $result = $s3Client->deleteObject(array(
        'Bucket' => $bucket,
        'Key'    => "tenants/0bfa8291a76a4c441511047464788611_pdf.pdf"
        )); 
        return 1;
    } catch (S3Exception $e) {
        return $e->getMessage() . PHP_EOL;
    }
}
function upload_file_in_s3( $key, $final_path ){
    $s3_bucket_file_url = '';
    
        // Instantiate an Amazon S3 client.
        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => [
            'key'    => 'AKIA6D6DECHRL5IAJMED',
            'secret' => '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'
            ]
        ]);
        $bucket = 'smi-reports-development';
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
$wp_upload_dir = _wp_upload_dir();
$baseurl = $wp_upload_dir['baseurl'];
$basedir = $wp_upload_dir['basedir'];
if( isset( $_GET['pageded']) ){
     $paged = $_GET['pageded'];
     $paged = (int) $paged;
}else{
    die("end");
}
 

$postsPerPage = 20;
$postOffset = $paged * $postsPerPage;
var_dump( $paged );
var_dump( $postsPerPage );
var_dump( $postOffset );
 
 $attachment_args = array(
    'post_type' => "attachment",
    'oderby' => 'ID',
    'order' => 'ASC',
    'posts_per_page' => $postsPerPage,
        'offset'         => $postOffset,
);
    $attachments = get_posts($attachment_args);
    foreach( $attachments  as $attachment):
      $guid = $attachment->guid;

      $new_string = str_replace( $baseurl, '', $guid );
      // $new_string = str_replace('/',)
      $file_name =  basename($new_string);
      $sub_folders =  str_replace('/'.$file_name, '', $new_string);
      // $sub_folders =  str_replace('/', '\\', $sub_folders);
      // $final_path = $basedir.''.$sub_folders.'/'.$file_name;
      $final_path = $basedir.''.$new_string;
      // basename($final_path);
      $ID = $attachment->ID;

// echo "<br/>";
if( file_exists($final_path )){
    $s3_status = upload_file_in_s3($file_name, $final_path );
    if( $s3_status['status'] == true ) {
        $s3_bucket_file_url = $s3_status['url'];
        $updated = update_attachment_user_guid( $ID, $s3_bucket_file_url );
        echo "<p style='text-color:green;'>uploaded post ID : " .$ID. " and S3 URL is : ". $s3_bucket_file_url ."</p>";
        echo "<br/>";
    }else{
         echo "<p style='text-color:red;'>File not uploading for post ID : " .$ID. " </p>";
         echo "<br/>";
    }
    // echo "<pre>";
    // var_dump($ID);
    // print_r( $s3_status );
    // echo "</pre>";
}else{
     echo "<p style='text-color:yellow;'>File not exist for post ID : " .$ID. " </p>";
    // echo "<br/>";
    // var_dump($final_path);
     echo "<br/>";
}

// var_dump($sub_folders);
// var_dump($attachment->post_mime_type );
 
        // wp_delete_attachment($attachment->ID, true);
    endforeach;

// $dd = delete_Image( $s3Client );
// var_dump( $dd) ;
// // Check if the form was submitted
// if($_SERVER["REQUEST_METHOD"] == "POST"){
// // Check if file was uploaded without errors
// if(isset($_FILES["anyfile"]) && $_FILES["anyfile"]["error"] == 0){
// $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
// $filename = $_FILES["anyfile"]["name"];
// $filetype = $_FILES["anyfile"]["type"];
// $filesize = $_FILES["anyfile"]["size"];
// // Validate file extension
// $ext = pathinfo($filename, PATHINFO_EXTENSION);
// if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
// // Validate file size - 10MB maximum
// $maxsize = 10 * 1024 * 1024;
// if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");
// // Validate type of the file
// if(in_array($filetype, $allowed)){
// // Check whether file exists before uploading it
// if(file_exists( __DIR__ . '/upload/' . $filename)){
// echo $filename . " is already exists.";
// } else{
// if(move_uploaded_file($_FILES["anyfile"]["tmp_name"],  __DIR__ . '/upload/' . $filename)){
// $bucket = 'smi-reports-development';
// $file_Path = __DIR__ . '/upload/'. $filename;
// $key = basename($file_Path);
// try {
// $result = $s3Client->putObject([
// 'Bucket' => $bucket,
// 'Key'    => $key,
// 'Body'   => fopen($file_Path, 'r'),
// 'ACL'    => 'public-read', // make file 'public'
// ]);
// echo "Image uploaded successfully. Image path is: ". $result->get('ObjectURL');
// } catch (Aws\S3\Exception\S3Exception $e) {
// echo "There was an error uploading the file.\n";
// echo $e->getMessage();
// }
// echo "Your file was uploaded successfully.";
// }else{
// echo "File is not uploaded";
// }
// } 
// } else{
// echo "Error: There was a problem uploading your file. Please try again."; 
// }
// } else{
// echo "Error: " . $_FILES["anyfile"]["error"];
// }
// }else{
	 
// }
?> 



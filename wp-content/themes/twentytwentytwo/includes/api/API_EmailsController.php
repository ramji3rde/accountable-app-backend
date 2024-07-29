<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class API_EmailsController extends API_BaseController {

	public function __construct() 
	{  

 	}
 	/**
 	 * Get all constractors
 	 */ 
 	public function get_contractors_emails()
 	{

 		$contractors = get_users( 
							array( 
								'role' => 'contractor',
								'orderby' => 'ID',
								'fields' => array( 'ID', 'user_login', 'user_email', 'display_name', 'user_registered'),
							) 
						);
 		$all_tenants_mail = array();
 		foreach ($contractors as $key => $value) {
 			if(!empty($value->user_email)){
 				$all_tenants_mail[] = $value->user_email;
 			}
 		}
 		return $all_tenants_mail;

 	} 
 	/**
 	 * Get all Tenants
 	 */ 
 	public function get_tenants_emails()
 	{
 		$contractors = get_users( 
							array( 
								'role' => 'tenant',
								'orderby' => 'ID',
								'fields' => array( 'ID', 'user_login', 'user_email'),
							) 
						);
 		$all_tenants_mail = array();
 		foreach ($contractors as $key => $value) {
 			if(!empty($value->user_email)){
 				$all_tenants_mail[] = $value->user_email;
 			}
 		}
 		return $all_tenants_mail;
 	} 
 	/*Get all Supportteams
 	 */ 
 	public function get_support_teams_emails()
 	{

 		$contractors = get_users( 
							array( 
								'role' => 'support_team',
								'orderby' => 'ID',
								'fields' => array( 'ID', 'user_login', 'user_email', 'display_name', 'user_registered'),
							) 
						);
 		$all_tenants_mail = array();
 		foreach ($contractors as $key => $value) {
 			if(!empty($value->user_email)){
 				$all_tenants_mail[] = $value->user_email;
 			}
 		}
 		return $all_tenants_mail;

 	}
 	/**
 	 * Get all users mails
 	 */ 
 	public function get_all_emails()
 	{

 		$contractors = get_users( 
							array( 
								// 'role' => 'contractor',
								'orderby' => 'ID',
								'fields' => array( 'ID', 'user_login', 'user_email', 'display_name', 'user_registered'),
							) 
						);
 		$all_tenants_mail = array();
 		foreach ($contractors as $key => $value) {
 			if(!empty($value->user_email)){
 				$all_tenants_mail[] = $value->user_email;
 			}
 		}
 		return $all_tenants_mail;

 	}
 	/**
     * Send expense detail via email
     * parameters will be sender's email id, receiver's email, title, cc, bcc, subject, body, files
     */
 	public function send_bulk_emails( WP_REST_Request $request){
    	
    	//$API_BaseController = new API_BaseController();
        $post_author = $this->custom_validate_token($_SERVER);
        $body = $request->get_param('body');
        $from = $request->get_param('from');
        $subject = $request->get_param('subject');
        $to = $request->get_param('to');
        $photos = $request->get_param('photos');
        $cc = $request->get_param('cc');
        $bcc = $request->get_param('bcc');
        
        if($to == 'tenants'){
        	$receiver_ids = $this->get_tenants_emails();
        }else if ($to == 'contractors') {
        	$receiver_ids = $this->get_contractors_emails();
        }else if ($to == 'support_team') {
        	$receiver_ids = $this->get_support_teams_emails();
        }else if ($to == 'all') {
        	$receiver_ids = $this->get_all_emails();
        }else{
        	$multiples = $request->get_param('multiple');
        	$mails_array = array();
        	foreach ($multiples as $key => $value) {
        		$mails_array[] = $value['value'];
        	}
        	$receiver_ids = $mails_array;
        }
        //print_r($photos);die;

    	$site_title = get_bloginfo( 'name' );
    	$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
    	// $subject = 'Report Detail';
    	
    	 
    	// $message = "<p>Hi,".$title."</p>

		$body = "<p><strong>". $body."</strong></p>";
		
		// <br/>
		// <p>Thanks<br/><span>". $site_title."</span></p>
		// ";
		
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    		$mail->isSMTP();                                           
		    		//Send using SMTP
				    $mail->Host       = SMTP_SETTINGS['host'];                     
				    //Set the SMTP server to send through
				    $mail->SMTPAuth   = true;                                   
				    //Enable SMTP authentication
				    $mail->Username   = SMTP_SETTINGS['username'];                     
				    //SMTP username
				    $mail->Password   = SMTP_SETTINGS['password'];                              
				    //SMTP password
				    $mail->SMTPSecure = "TLS";            
				    //Enable implicit TLS encryption
				    $mail->Port       = 587;                                    
				    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
				    //Recipients
				    $mail->setFrom($from, $from);
				    //$mail->addReplyTo( $from, $from);
				    // if(!empty($cc)){
				    // 	$mail->addCC($cc);
				    // }
				    // if(!empty($bcc)){
				    // 	$mail->addBCC($bcc);
				    // }
				    if(is_array($receiver_ids) && !empty($receiver_ids)){
				    	//$mail->addAddress($receiver_ids[0],$receiver_ids[0]); 
				    	foreach ($receiver_ids as $key => $mails) {
						   $mail->addAddress($mails,$mails);
						}
				    }
				    
					
				    //Attachments
				    if(!empty($photos)){
				    	$image_string = '</br>';
				    	foreach ($photos['cloudImage'] as $key => $value) {
							// Check if the URL is a valid image
							$file_extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
							$validImgExt = array('jpg', 'jpeg', 'png', 'gif');
							$validDocExt = array('pdf', 'doc', 'docx', 'xls', 'xlsx');
							if (in_array($file_extension, $validImgExt)) {
				    			$image_string .= "<a href='".$value."'><img style='width:100px;height:100px;margin:5px;' src='".$value."' /></a>";
				    			//$mail->addAttachment($path); 
							}else if(in_array($file_extension, $validDocExt)){
								$image_string .= "<a href='".$value."'><img style='width:100px;height:100px;margin:5px;' src='https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_document_x64.png' /></a>";
							} else {
								// It's not a valid image
								$errormessage .= "Invalid image or doc at URL: " . $value . "</br>";
								$mail_status = 0;
								return "Message could not be sent. Mailer Error: {$errormessage}";
							}
				    	}
				    	
				    }

				    //Content
				    $mail->isHTML(true);                                  
				    //Set email format to HTML
				    $mail->Subject = $subject;
				    $mail->Body    = $body.$image_string;
				    // $mail->AltBody = '';
				    //print_r($mail);die;
				    $mail_status = $mail->send();
		    
		    //return $mail_status;
		} catch (Exception $e) {
			$mail_status = 0;
			return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	 	
		// $mail_status = wp_mail( $to, $subject, $message, $headers );
		return $mail_status;
    }
    
    /**
     * Send expense detail via email
     //parameters will be sender's email id, receiver's email, title, cc, bcc, subject, body, files
     */ 
    public function send_expense_detail_via_email( $email_id, $expense_id ){
    	$expense_info = get_post( $expense_id );
    	$admin_email =  get_option( 'admin_email' );
    	$from = "charles@getaccountableapp.com";
    	$to =  $email_id;
    	$site_title = get_bloginfo( 'name' );
    	$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
    	$subject = 'Expense Detail';
    	$expense_name = $expense_info->post_title;
    	$expense_amount = get_post_meta( $expense_id, 'expense_amount', true );
    	$property_name = get_post_meta( $expense_id, 'property_name', true );
    	$purchase_date = get_post_meta( $expense_id, 'purchase_date', true );
    	$expense_category = get_post_meta($expense_id, 'expense_category', true);
    	$expense_category = ( 'zzzzzzzzzz'  == $expense_category ) ? '' : $expense_category ;
    	$attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $expense_id,
			    'posts_per_page' => -1,
			);
         $image_string = "<div>";
		$attachments = get_posts($attachment_args);
		$file_available = false;
		foreach( $attachments  as $attachment):
			$file_available = true;
			// echo "<pre>";
			$ext = end(explode('.', $attachment->guid));
			$ext = strtolower(trim( $ext));
			if (str_contains($attachment->post_mime_type, 'image')) { 
			    $image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$attachment->guid."' /></a>";
			}else if (str_contains($attachment->post_mime_type, 'pdf')) { 
				$pdf_file_icon = get_template_directory_uri().'/assets/images/pdf_file.png';
			    $image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$pdf_file_icon."' /></a>";
			}else{
				$other_file_icon = get_template_directory_uri().'/assets/images/other_file.png';
				$image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$other_file_icon."' /></a>";
			}
			 
		endforeach; 
		$image_string .= "</div>";
    	$message = "<p>Hi,</p>

		<p><strong>Here are the expense details.</strong></p>
		<span><strong>Name : </strong>". $expense_name."</span><br/>
		<span><strong>Amount : $</strong>". $expense_amount."</span><br/>
		<span><strong>Purchase Date : </strong>". $purchase_date."</span><br/>
		<span><strong>Category : </strong>". $expense_category."</span><br/>
		<br/>";
		if( $file_available ){
			$message .= "<p><strong>Photos:</strong><br/>".$image_string."</p><br/><br/>";
		}

		$message .= "<p>Thanks<br/><span>". $site_title."</span></p>";
		
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];                     //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];                     //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password'];                              //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $full_name);     //Add a recipient
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

    /**
     * Send incident detail via email
     */ 
    public function send_incident_detail_via_email( $email_id, $incident_id ){
    	$incident_info = get_post( $incident_id );
    	$admin_email =  get_option( 'admin_email' );
    	$from = "charles@getaccountableapp.com";
    	$to =  $email_id;
    	$site_title = get_bloginfo( 'name' );
    	$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
    	$subject = 'Incident Detail';
    	$incident_name = $incident_info->post_title;
    	$incident_content = $incident_info->post_content;
    	$incident_date = get_post_meta( $incident_id, 'date', true );
    	$property_name = get_post_meta( $incident_id, 'property', true );
    	 
    	$incident_status = get_post_meta($incident_id, 'status', true);
    	$attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $incident_id,
			    'posts_per_page' => -1,
			);
         $image_string = "<div>";
		$attachments = get_posts($attachment_args);
		$file_available = false;
		foreach( $attachments  as $attachment):
			$file_available = true;
			// echo "<pre>";
			$ext = end(explode('.', $attachment->guid));
			$ext = strtolower(trim( $ext));
			if (str_contains($attachment->post_mime_type, 'image')) { 
			    $image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$attachment->guid."' /></a>";
			}else if (str_contains($attachment->post_mime_type, 'pdf')) { 
				$pdf_file_icon = get_template_directory_uri().'/assets/images/pdf_file.png';
			    $image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$pdf_file_icon."' /></a>";
			}else{
				$other_file_icon = get_template_directory_uri().'/assets/images/other_file.png';
				$image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$other_file_icon."' /></a>";
			}
			 
		endforeach; 
		$image_string .= "</div>"; 
    	$message = "<p>Hi,</p>

		<p><strong>Here are the incident details.</strong></p>
		<span><strong>Name : </strong>". $incident_name."</span><br/>
		<span><strong>Description : </strong>". $incident_content."</span><br/>
		<span><strong>Date : </strong>". $incident_date."</span><br/>
		<span><strong>Property Name : </strong>". $property_name."</span><br/>
		<span><strong>Status : </strong>". $incident_status."</span><br/>
		<br/>";
		if( $file_available ){
			$message .= "<p><strong>Photos:</strong><br/>".$image_string."</p><br/><br/>";
		}
		$message .= "<p>Thanks<br/><span>". $site_title."</span></p>
		";
		
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];                     //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];                    //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password'];                               //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $full_name);     //Add a recipient
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

     /**
     * Send project detail via email
     */ 
    public function send_project_detail_via_email( $email_id, $project_id ){
    	$project_info = get_post( $project_id );
    	$admin_email =  get_option( 'admin_email' );
    	$from = "charles@getaccountableapp.com";
    	$to =  $email_id;
    	$site_title = get_bloginfo( 'name' );
    	$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
    	$subject = 'Project Detail';
    	$project_name = $project_info->post_title;
    	$project_content = $project_info->post_content;
    	$project_date = get_post_meta( $project_id, 'project_date', true );
    	$services = get_post_meta( $project_id, 'services', true );
    	$services = ( 'zzzzzzzzzz'  == $services ) ? '' : $services ; 
    	$project_status = get_post_meta($project_id, 'status', true);
    	
    	$attachment_args = array(
			    'post_type' => "attachment",
			    'post_parent' => $project_id,
			    'posts_per_page' => -1,
			);
         $image_string = "<div>";
		$attachments = get_posts($attachment_args);
		$file_available = false;
		foreach( $attachments  as $attachment):
			$file_available = true;
			// echo "<pre>";
			$ext = end(explode('.', $attachment->guid));
			$ext = strtolower(trim( $ext));
			if (str_contains($attachment->post_mime_type, 'image')) { 
			    $image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$attachment->guid."' /></a>";
			}else if (str_contains($attachment->post_mime_type, 'pdf')) { 
				$pdf_file_icon = get_template_directory_uri().'/assets/images/pdf_file.png';
			    $image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$pdf_file_icon."' /></a>";
			}else{
				$other_file_icon = get_template_directory_uri().'/assets/images/other_file.png';
				$image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='".$other_file_icon."' /></a>";
			}
			 
		endforeach; 
		$image_string .= "</div>";

		$bid_html = "<div>";
		$API_ProjectsController = new API_ProjectsController();
		$total_bids = $API_ProjectsController->get_a_project_bids( $project_id); 
		$bids_available = false;
		foreach( $total_bids  as $single_bid):
			$company_name = $single_bid->company_name;
			$additional_info = $single_bid->additional_info;
			$bid_documents = $single_bid->bid_documents;
			$bid_price = $single_bid->bid_price;
			$bid_accepted_by_admin = $single_bid->bid_accepted_by_admin;
			$contractor_id = $single_bid->contractor_id;
			$contractor_detail = get_user_by('id', $contractor_id );

			$bids_available = true;
			// echo "<pre>";
			// print_r( $single_bid );
			// echo "</pre>"; 
			$bid_html .= "<div><p> <strong>".$company_name."</strong><br/>
			<span>".$additional_info."</span><br/>
			<strong>Quote : $".$bid_price."</strong><br/>";
			if( isset( $contractor_detail->ID));
			{
				$bid_status = "<span style='background-color:yellow;padding:3px;'>Pending</span>";
				if( 1 == $bid_accepted_by_admin){
					$bid_status = "<span style='background-color:green;padding:3px;'>Accepted</span>";
				}else if( 0 == $bid_accepted_by_admin){
					$bid_status = "<span style='background-color:red;padding:3px;'>Declined</span>";
				}
				$contractor_name = $contractor_detail->display_name;
				$contractor_email = $contractor_detail->user_email;
				$bid_html .= "<strong>Contractor Name : ".$contractor_name."</strong><br/>";
				$bid_html .= "<strong>Contractor Email : ".$contractor_email."</strong><br/>";
				$bid_html .= "<strong>Bid Status : ".$bid_status."</strong><br/>";
			}
			foreach( $bid_documents  as $bid_document):
				$ext = end(explode('.', $bid_document->photo_src));
				$ext = strtolower(trim( $ext));
				if (str_contains($bid_document->post_mime_type, 'image')) { 
				    $bid_html .= "<a href='".$bid_document->photo_src."'><img style='width:100px;height:100px;margin:5px;' src='".$bid_document->photo_src."' /></a>";
				}else if (str_contains($bid_document->post_mime_type, 'pdf')) { 
					$pdf_file_icon = get_template_directory_uri().'/assets/images/pdf_file.png';
				    $bid_html .= "<a href='".$bid_document->photo_src."'><img style='width:100px;height:100px;margin:5px;' src='".$pdf_file_icon."' /></a>";
				}else{
					$other_file_icon = get_template_directory_uri().'/assets/images/other_file.png';
					$bid_html .= "<a href='".$bid_document->photo_src."'><img style='width:100px;height:100px;margin:5px;' src='".$other_file_icon."' /></a>";
				}
			endforeach;
			$bid_html .= "</p> </div>"; 
			// $image_string .= "<a href='".$attachment->guid."'><img style='width:100px;height:100px;margin:5px;' src='" 
		endforeach; 
		$bid_html .= "</div>";
		// var_dump( $image_string );
		// return false;
    	$message = "<p>Hi,</p>

		<p><strong>Here are the project details.</strong></p>
		<span><strong>Name : </strong>". $project_name."</span><br/>
		<span><strong>Description : </strong>". $project_content."</span><br/>
		<span><strong>Date : </strong>". $project_date."</span><br/>
		<span><strong>Service/ Industry : </strong>". $property_name."</span><br/>
		<span><strong>Status : </strong>". $project_status."</span><br/>
		<br/>";
		if( $file_available ){
			$message .= "<p><strong>Project related photos:</strong><br/>".$image_string."</p><br/><br/>";
		}
		if( $bids_available ){
			$message .= "<p><strong>Bid/Quote on project:</strong><br/>".$bid_html."</p><br/><br/>";
		}
		$message .= "<p>Thanks<br/><span>". $site_title."</span></p>
		";
		// var_dump( $message );
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];                  //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];                  //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password'];                             //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $full_name);     //Add a recipient
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

     /**
     * Send report detail via email
     */ 
    public function send_report_detail_via_email( $email_id, $file_path ){
    	
    	$admin_email =  get_option( 'admin_email' );
    	$from = "charles@getaccountableapp.com";
    	$to =  $email_id;
    	$site_title = get_bloginfo( 'name' );
    	$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$site_title.' <'.$from.'>');
    	$subject = 'Report Detail';
    	
    	 
    	$message = "<p>Hi,</p>

		<p><strong>Here are the report details in PDF. Please open the pdf and review it.</strong></p>
		<span><a href='". $file_path."'>View Report</a></span><br/>
		<span><strong>OR</strong></span><br/>
		<span>Copy and paste this ". $file_path." URL.</span><br/>
		<br/>
		<p>Thanks<br/><span>". $site_title."</span></p>
		";
		
		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		    $mail->isSMTP();                                            //Send using SMTP
		    $mail->Host       = SMTP_SETTINGS['host'];                    //Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		    $mail->Username   = SMTP_SETTINGS['username'];                    //SMTP username
		    $mail->Password   = SMTP_SETTINGS['password'];                               //SMTP password
		    $mail->SMTPSecure = "TLS";            //Enable implicit TLS encryption
		    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		    //Recipients
		    $mail->setFrom($from, $site_title);
		    $mail->addAddress($to, $full_name);     //Add a recipient
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
 	/*
 	 * Admin can send projects, reports, incidents, and expenses detail via email to support team email or any other email
 	 */
 	public function send_items_detail_to_any_email(WP_REST_Request $request)
 	{

 		$email_id    = trim($request->get_param( 'email_id' ));
 		$detail_type    = trim($request->get_param( 'detail_type' )); 
 		$id    = trim($request->get_param( 'id' ));
 		$pdf_path    = trim($request->get_param( 'pdf_path' )); 
 		//$API_BaseController = new API_BaseController();
 		$post_author = $this->custom_validate_token($_SERVER);
 		$permission = $this->check_user_permission($_SERVER, 'create');
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
 		$errors_arr = array();
 		if(empty($email_id)){
 			$errors_arr[] = __( 'Please enter valid email address.', 'jwt-auth' ); 
		} 
		if(empty($id)){
 			$errors_arr[] = __( 'Please enter valid type.', 'jwt-auth' ); 
		}
		if(empty($detail_type)){
 			$errors_arr[] = __( 'Which detail you want to send', 'jwt-auth' ); 
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
 		$mail_status =  false;		
	 	if( $detail_type == 'expense'){
	 		$mail_status = $this->send_expense_detail_via_email( $email_id, $id );
	 	}else if( $detail_type == 'incident'){
	 		$mail_status = $this->send_incident_detail_via_email( $email_id, $id );
	 	}else if( $detail_type == 'project'){
	 		$mail_status = $this->send_project_detail_via_email( $email_id, $id );
	 	}else if( $detail_type == 'report'){
	 		if ( empty($pdf_path) ) { 

				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'error',
						'message'    => __( 'Please enter valid file path.', 'jwt-auth' ),
						'data'       => $pdf_path,
					),
					
				);
				exit();
	    	}
	 		$mail_status = $this->send_report_detail_via_email( $email_id, $pdf_path );
	 	}
		if( $mail_status ){
	        	 

	     return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'success',
					'message'    => __( 'Email send successfully.', 'jwt-auth' ),
					'data'       => array('mail_status' => $mail_status),
				),
				
			); 
	    }else{

	        return new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'jwt_auth_bad_config',
					'message'    => __( 'Email is not sending due to technical issue.', 'jwt-auth' ),
					'data'       => array('mail_status' => $mail_status),
				),
				
			); 
	    } 
 	}

}

new API_EmailsController();
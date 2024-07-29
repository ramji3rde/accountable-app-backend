<?php
class API_PushNotificationController extends API_BaseController {

	public function __construct() 
	{  
        //add_action('init', array($this, 'schedule_custom_cron_job'));
        if (!wp_next_scheduled('scheduler_for_push_batches')) {
            // Schedule your cron job to run once every hour (adjust the interval as needed).
            wp_schedule_event(time(), 'per_minute', 'scheduler_for_push_batches');
        }
        if (!wp_next_scheduled('scheduler_for_push_batches_contractor')) {
            // Schedule your cron job to run once every hour (adjust the interval as needed).
            wp_schedule_event(time(), 'per_minute', 'scheduler_for_push_batches_contractor');
        }
        add_action( 'scheduler_for_push_batches', array($this, 'schedule_push_batches') );
        add_filter('cron_schedules',array($this, 'my_cron_schedules'));
        add_action('scheduler_for_push_batches_contractor',array($this,'schedule_push_batches_for_contractor'));
        //add_action( 'wp_footer', array($this, 'schedule_push_batches') );
 	}
    public function my_cron_schedules($schedules){
        if(!isset($schedules["per_minute"])){
            $schedules["per_minute"] = array(
                'interval' => 60,
                'display' => __('Once a Minute'));
        }
        return $schedules;
    }

    public function get_user_device_token(WP_REST_Request $request){
        $API_BaseController = new API_BaseController();
        //get user id from header
        $admin_id = $API_BaseController->custom_validate_token($_SERVER);
        $device_token = $request->get_param('device_token');
        $result = array();
        //add new device for login user
        if($device_token){
            $token_key = 'thirde_device_token';
            $token_val = get_user_meta( $admin_id, $token_key, true );
            $token = unserialize($token_val);
            array_push($result,$token);
            $flag = 0;
            if( !empty( $token ) ) {
                foreach ($token as $key => $value) {
                    if($value == $device_token){
                        $flag = 1;
                    }
                }
                if($flag == 0){
                    array_push($token,$device_token);
                    array_push($result,"new device");
                    //delete_user_meta($admin_id, $token_key);
                    update_user_meta( $admin_id, $token_key, serialize($token) );
                }else{
                    $check = "duplicate";
                    array_push($result,$check);
                }
                
            }else{
                $token = array();
                array_push($token,$device_token);
                array_push($result,"very first");
                update_user_meta( $admin_id, $token_key, serialize($token) );
            }
        
            return new WP_REST_Response(
                array(
                    'success'    => true,
                    'statusCode' => 200,
                    'code'       => 'success',
                    'data'       => array('Status' => $result),
                ),
            );
        }else{
            return new WP_REST_Response(
                array(
                    'success'    => false,
                    'statusCode' => 403,
                    'code'       => 'error',
                    'data'       => array('Status' => 'something went wrong'),
                ),
                
            );
        }   
    }
 	 
    public function push_notification_send(WP_REST_Request $request){
        $API_BaseController = new API_BaseController();
		$post_author = $API_BaseController->custom_validate_token($_SERVER);
        $tenant_id = $request->get_param('user_id');
        $device_token = $request->get_param('device_token');
        $time_period = $request->get_param('time_period');
        $title = $request->get_param('title');
        $type = $request->get_param('type');
        $body = $request->get_param('body');
        $reminder_status = $request->get_param('reminder_status');
        //$type = $request->get_param('body');
        $current_time = current_time('timestamp');
        if($tenant_id){
            //update meta for notification
            if($type == 'tenant'){
                update_post_meta($tenant_id , 'thirde_admin_id', $post_author);
                update_post_meta($tenant_id , 'thirde_device_token', $device_token);
                update_post_meta($tenant_id , 'thirde_time_period', $time_period);
                update_post_meta($tenant_id , 'thirde_message_title', $title);
                update_post_meta($tenant_id , 'thirde_message_body', $body);
                update_post_meta($tenant_id , 'thirde_reminder_status', $reminder_status);
                update_post_meta($tenant_id, 'thirde_last_timestamp', $current_time);
                $result = "tenant updated!";
            }else{
                update_user_meta($tenant_id , 'thirde_admin_id', $post_author);
                update_user_meta($tenant_id , 'thirde_device_token', $device_token);
                update_user_meta($tenant_id , 'thirde_time_period', $time_period);
                update_user_meta($tenant_id , 'thirde_message_title', $title);
                update_user_meta($tenant_id , 'thirde_message_body', $body);
                update_user_meta($tenant_id , 'thirde_reminder_status', $reminder_status);
                update_user_meta($tenant_id, 'thirde_last_timestamp', $current_time);
                $result = "contractor updated!";
            }
            
            return new WP_REST_Response(
                array(
                    'success'    => true,
                    'statusCode' => 200,
                    'code'       => 'success',
                    'data'       => array('Status' => $post_author.$result),
                ),
            );
        }else{
            return new WP_REST_Response(
                array(
                    'success'    => false,
                    'statusCode' => 403,
                    'code'       => 'error',
                    'data'       => array('Status' => 'something went wrong'),
                ),
                
            );
        }
	}

    public function test_push_notification_send(WP_REST_Request $request){
        //$API_BaseController = new API_BaseController();
        //$post_author = $API_BaseController->custom_validate_token($_SERVER);
        //$user_id = $request->get_param('user_id');
        $device_token = $request->get_param('device_token');
        //$time_period = $request->get_param('time_period');
        $title = $request->get_param('title');
        $body = $request->get_param('body');
        $data = [
            'to' => $device_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        $response = $this->curl_function($data);
        if($response){
            $current_time = current_time('timestamp');
            update_post_meta($user_id, 'thirde_last_timestamp', $current_time);
            return new WP_REST_Response(
                array(
                    'success'    => true,
                    'statusCode' => 200,
                    'code'       => 'success',
                    'data'       => array('Status' => $response),
                ),
            );
        }else{
            return new WP_REST_Response(
                array(
                    'success'    => false,
                    'statusCode' => 403,
                    'code'       => 'error',
                    'data'       => array('Status' => 'something went wrong1'),
                ),
                
            );
        }   
    }

    // Function to execute the custom cron job
    public function schedule_push_batches() {
        // Get users with 'thirde_reminder_status' set to true
        $tenants_with_reminder_status = get_posts(array(
            'numberposts'   => -1,
            'post_type'     => array('tenants'),
            'meta_key'     => 'company_flag',
            'meta_value'   => 'true',
        ));
        // echo "<pre>";
        // print_r($tenants_with_reminder_status);
        $current_time = current_time('timestamp'); // Get the current timestamp
        $message_batch = array();
        foreach ($tenants_with_reminder_status as $tenant) {
            $tenant_id = $tenant->ID;
            // Get tenant meta values
            $time_period = get_post_meta($tenant_id, 'thirde_time_period', true);
            $last_timestamp = get_post_meta($tenant_id, 'thirde_last_timestamp', true);
            $next_expected_timestamp = '';
            //update_post_meta($tenant_id, 'thirde_last_timestamp', $current_time);

            // Calculate the next expected timestamp based on the time period
            if(!empty($time_period)){
                $time_period = str_replace(":"," ",$time_period);
                $next_expected_timestamp = strtotime("+$time_period", $last_timestamp);
            }
            //echo "Time period: ".$time_period."next expected time:".$next_expected_timestamp." current time: ".$current_time."</br>";
            // Check if it's time to send a message
            if (!empty($next_expected_timestamp) && $next_expected_timestamp <= $current_time) {
                // Time to send a message for this tenant
                // Get other tenant meta values
                //$device_token = get_post_meta($tenant_id, 'thirde_device_token', true);
                $title = get_post_meta($tenant_id, 'thirde_message_title', true);
                $body = get_post_meta($tenant_id, 'thirde_message_body', true);
                //get admin id and admin device codes
                $author_id = get_post_meta($tenant_id, 'thirde_admin_id', true);
                $device_token_key = 'thirde_device_token';
                $device_token_val = get_user_meta( $author_id, $device_token_key, true );
                $device_token_array = unserialize($device_token_val);
                //print_r($device_token_array);
                foreach ($device_token_array as $key1 => $device_token) {
                    $data = [
                        'to' => $device_token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ];
                    $response = $this->curl_function($data);
                }
                // Update the 'thirde_last_timestamp' to the current time
                    update_post_meta($tenant_id, 'thirde_last_timestamp', $current_time);
            }
        }
    }
    // Function to execute the custom cron job
    public function schedule_push_batches_for_contractor() {
        // Get users with 'thirde_reminder_status' set to true
        $contractors = get_users( 
                            array( 
                                'role' => 'contractor',
                                'orderby' => 'ID',
                                'fields' => array( 'ID', 'user_email'),
                                 'meta_query' => array(
                                        array(
                                            'key' => 'company_flag',
                                            'value' => "true",
                                            'compare' => '='
                                        )
                                    )
                            ) 
                        );
        // echo "<pre>";
        // print_r($contractors);
        $current_time = current_time('timestamp'); // Get the current timestamp
        $message_batch = array();
        foreach ($contractors as $data) {
            $contractor_id = $data->ID;
            // Get tenant meta values
            $time_period = get_user_meta($contractor_id, 'thirde_time_period', true);
            $last_timestamp = get_user_meta($contractor_id, 'thirde_last_timestamp', true);
            $next_expected_timestamp = '';
            //update_post_meta($contractor_id, 'thirde_last_timestamp', $current_time);

            // Calculate the next expected timestamp based on the time period
            if(!empty($time_period)){
                $time_period = str_replace(":"," ",$time_period);
                $next_expected_timestamp = strtotime("+$time_period", $last_timestamp);
            }
            echo "Time period: ".$time_period."next expected time:".$next_expected_timestamp." current time: ".$current_time."</br>";
            // Check if it's time to send a message
            if (!empty($next_expected_timestamp) && $next_expected_timestamp <= $current_time) {
                // Time to send a message for this tenant
                // Get other tenant meta values
                //$device_token = get_user_meta($contractor_id, 'thirde_device_token', true);
                $title = get_user_meta($contractor_id, 'thirde_message_title', true);
                $body = get_user_meta($contractor_id, 'thirde_message_body', true);
                //get admin id and admin device codes
                $author_id = get_user_meta($contractor_id, 'thirde_admin_id', true);
                $device_token_key = 'thirde_device_token';
                $device_token_val = get_user_meta( $author_id, $device_token_key, true );
                $device_token_array = unserialize($device_token_val);
                //print_r($device_token_array);
                foreach ($device_token_array as $key1 => $device_token) {
                    $data = [
                        'to' => $device_token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ];
                    $response = $this->curl_function($data);
                }
                // Update the 'thirde_last_timestamp' to the current time
                    update_user_meta($contractor_id, 'thirde_last_timestamp', $current_time);
            }
        }
    }
    //Function for curl request
    public function curl_function($payload){
        //$server_key = 'AAAANPgbuaw:APA91bGcDm3fNx7lFeQCYphGwXx7CKHpjmv8N7fNiAyWQaPrmhXlsefnz5pbzAfTRsrYk2ZwEVWqWJgQUmPbGM2XnsLuUaNDU8CcZTkC3MGORNcRVTWK8HL9XTmLvbl6iv-YjqTN2st6';
        // Convert the payload to JSON
        $jsonPayload = json_encode($payload);

        // Set the FCM endpoint URL
        $url = 'https://fcm.googleapis.com/fcm/send';

        // Create headers with the Authorization key and content type
        $headers = [
            'Authorization: key=' . FIREBASE_SERVER_KEY,
            'Content-Type: application/json'
        ];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);

        // Execute the cURL session
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    } 
}

new API_PushNotificationController();
?>
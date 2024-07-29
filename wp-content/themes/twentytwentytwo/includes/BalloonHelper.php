<?php
if ( ! class_exists( 'Balloon_Helper', false ) ) : 

class Balloon_Helper {

	function __construct()
	{
		$this->init_action();
	}

	function init_action()
	{

		//register membership type post
		add_action('init',array($this,'register_gigs_post_type'));

		//create user role vendor and client
		add_action('admin_init', array($this,'add_client_and_vendor_user_role'));

		// create custom taxonomy
		add_action( 'init',  array( $this, 'register_okland_smi_custom_taxonomy' ), 0 );

		// register custom post status
		add_action( 'init', array($this, 'jc_custom_post_status') );
		add_action('admin_footer-post.php', array($this,'jc_append_post_status_list'));


		 
	}

	//create user role vendor and client
	function add_client_and_vendor_user_role() {  

		//add the new user role
		add_role('app_admin', 'App Admin', array( 'read' => true, 'edit_posts' => false, 'delete_posts' => false) );
		add_role('tenant', 'Tenant', array( 'read' => true, 'edit_posts' => false, 'delete_posts' => false) );
		add_role('vendor', 'Vendor', array( 'read' => true, 'edit_posts' => false, 'delete_posts' => false) );
		add_role('contractor', 'Contractor', array( 'read' => true, 'edit_posts' => false, 'delete_posts' => false) );
		add_role('support_team', 'Support Team', array( 'read' => true, 'edit_posts' => false, 'delete_posts' => false) );
		add_role('app_manager', 'App Manager', array( 'read' => true, 'edit_posts' => false, 'delete_posts' => false) );


	}

	public function register_gigs_post_type()
	{
		// Add new taxonomy, make it hierarchical (like categories)
			  $labels = array(
			    'name' => _x( 'Project Category', 'project-category' ),
			    'singular_name' => _x( 'Project Category', 'project-category' ),
 			  );
			  register_taxonomy('project-category',array('discography'), array(
			    'hierarchical' => true,
			    'labels' => $labels,
			    'show_ui' => true,
			    'query_var' => true,
			    'rewrite' => array( 'slug' => 'project-category' ),
			  ));

			  
			/**
			 * Post Type: Projects
			*/
			$projects_labels = array(

				"name" => __( "Projects", "projects" ),

				"singular_name" => __( "Projects", "projects" ),
 			);

			$projects_args = array(

				"label" => __( "Projects", "projects" ),

				"labels" => $projects_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",

				"map_meta_cap" => true,

				"hierarchical" => false,

				"rewrite" => array( "slug" => "projects", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","categories","custom-fields" ),

				'taxonomies'          => array( 'project-category'),

			);
			
			register_post_type( "projects", $projects_args ); 

			  
			/**
			 * Post Type: Tenants
			*/
			$tenants_labels = array(

				"name" => __( "Tenants", "tenants" ),

				"singular_name" => __( "Tenants", "tenants" ),
 			);

			$tenants_args = array(

				"label" => __( "Projects", "projects" ),

				"labels" => $tenants_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-admin-users",

				"map_meta_cap" => true,

				"hierarchical" => false,

				"rewrite" => array( "slug" => "tenants", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "tenants", $tenants_args ); 

			/**
			 * Post Type: Notes
			*/
			$notes_labels = array(

				"name" => __( "Notes", "notes" ),

				"singular_name" => __( "notes", "notes" ),
 			);

			$notes_args = array(

				"label" => __( "Notes", "notes" ),

				"labels" => $notes_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-format-status",

				"map_meta_cap" => true,

				"hierarchical" => false,

				"rewrite" => array( "slug" => "notes", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "notes", $notes_args ); 
			

			/**
			 * Post Type: Bids
			*/
			$bids_labels = array(

				"name" => __( "Project Bids", "notes" ),

				"singular_name" => __( "Project Bids", "notes" ),
 			);

			$bids_args = array(

				"label" => __( "Project Bids", "notes" ),

				"labels" => $bids_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-list-view",

				"map_meta_cap" => true,

				"hierarchical" => false,

				"rewrite" => array( "slug" => "bids", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "project_bids", $bids_args ); 

			/**
			 * Post Type: Incident
			*/
			$incidents_labels = array(

				"name" => __( "Incidents" ),

				"singular_name" => __( "Incidents"),
 			);

			$incident_args = array(

				"label" => __( "Incidents"),

				"labels" => $incidents_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-list-view",

				"map_meta_cap" => true,

				"hierarchical" => false,

				// "rewrite" => array( "slug" => "incident", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "incident", $incident_args ); 



			/**
			 * Post Type: Emergency Contacts
			*/
			$emergency_labels = array(

				"name" => __( "Emergency Contacts" ),

				"singular_name" => __( "Emergency Contacts"),
 			);

			$emergency_args = array(

				"label" => __( "Emergency Contacts"),

				"labels" => $emergency_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-list-view",

				"map_meta_cap" => true,

				"hierarchical" => false,

				// "rewrite" => array( "slug" => "incident", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "emergency-contacts", $emergency_args ); 

			/**
			 * Post Type: Secure Notes
			*/
			$security_info_labels = array(

				"name" => __( "Security Info" ),

				"singular_name" => __( "Secure Info"),
 			);

			$security_info_args = array(

				"label" => __( "Secure Notes"),

				"labels" => $security_info_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-list-view",

				"map_meta_cap" => true,

				"hierarchical" => false,

				'taxonomies' => array( 'category' ),

				// "rewrite" => array( "slug" => "incident", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "security-info", $security_info_args ); 




			/**
			 * Post Type: Expense
			*/
			$expense_labels = array(

				"name" => __( "Expenses" ),

				"singular_name" => __( "Expenses"),
 			);

			$expense_args = array(

				"label" => __( "Expenses"),

				"labels" => $expense_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-list-view",

				"map_meta_cap" => true,

				"hierarchical" => false,

				// "rewrite" => array( "slug" => "incident", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "expense", $expense_args );

			/**
			 * Post Type: Schedule
			*/
			$schedule_labels = array(

				"name" => __( "Schedule" ),

				"singular_name" => __( "Schedules"),
 			);

			$schedule_args = array(

				"label" => __( "Schedules"),

				"labels" => $schedule_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-schedule",

				"map_meta_cap" => true,

				"hierarchical" => false,

				// "rewrite" => array( "slug" => "incident", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "schedule", $schedule_args );
			/**
			 * Post Type: Schedule Group
			*/
			$schedule_group_labels = array(

				"name" => __( "Schedule Group" ),

				"singular_name" => __( "Schedule Group"),
 			);

			$schedule_group_args = array(

				"label" => __( "Schedule Groups"),

				"labels" => $schedule_group_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-schedule",

				"map_meta_cap" => true,

				"hierarchical" => false,

				// "rewrite" => array( "slug" => "incident", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "schedule_group", $schedule_group_args );

			/**
			 * Post Type: Contacts List
			*/
			$contact_list_labels = array(

				"name" => __( "Contacts" ),

				"singular_name" => __( "Contacts"),
 			);

			$contact_list_args = array(

				"label" => __( "Contacts"),

				"labels" => $contact_list_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,

				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",
				"menu_icon" => "dashicons-phone",

				"map_meta_cap" => true,

				"hierarchical" => false,

				// "rewrite" => array( "slug" => "incident", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "author","custom-fields" ),

			);
			
			register_post_type( "contacts", $contact_list_args );

			/**
			 * Post Type: Project Maps
			*/
			$project_maps_labels = array(

				"name" => __( "Project maps", "project maps" ),

				"singular_name" => __( "Project maps", "project maps" ),
 			);

			$project_maps_args = array(

				"label" => __( "Project maps", "project maps" ),

				"labels" => $project_maps_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,
				"menu_icon" => "dashicons-location-alt",
				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",

				"map_meta_cap" => true,

				"hierarchical" => false,

				"rewrite" => array( "slug" => "project_maps", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title",  "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "project_maps", $project_maps_args ); 
			/**
			 * Post Type: Messages
			*/
			$messages_labels = array(

				"name" => __( "Messages", "messages" ),

				"singular_name" => __( "Messages", "messages" ),
 			);

			$messages_args = array(

				"label" => __( "Messages", "messages" ),

				"labels" => $messages_labels,

				"description" => "",

				"public" => true,

				"publicly_queryable" => true,

				"show_ui" => true,

				"delete_with_user" => false,
				"menu_icon" => "dashicons-buddicons-pm",
				"show_in_rest" => false,

				"rest_base" => "",

				"rest_controller_class" => "WP_REST_Posts_Controller",

				"has_archive" => true,

				"show_in_menu" => true,

				"show_in_nav_menus" => true,

				"exclude_from_search" => false,

				"capability_type" => "post",

				"map_meta_cap" => true,

				"hierarchical" => false,

				"rewrite" => array( "slug" => "messages", "with_front" => true, "pages" => true,),

				"query_var" => true,

				"supports" => array( "title", "editor", "thumbnail","comments","author","custom-fields" ),

			);
			
			register_post_type( "messages", $messages_args );	
	}

 
	// Register Custom Taxonomy
	public function register_okland_smi_custom_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Expense Categories', 'Taxonomy General Name', 'text_domain' ),
			'singular_name'              => _x( 'Expense category', 'Taxonomy Singular Name', 'text_domain' ),
			'menu_name'                  => __( 'Category', 'text_domain' ),
			'all_items'                  => __( 'All Categories', 'text_domain' ),
			'parent_item'                => __( 'Parent Category', 'text_domain' ),
			'parent_item_colon'          => __( 'Parent Category:', 'text_domain' ),
			'new_item_name'              => __( 'New Category Name', 'text_domain' ),
			'add_new_item'               => __( 'Add New Category', 'text_domain' ),
			'edit_item'                  => __( 'Edit Category', 'text_domain' ),
			'update_item'                => __( 'Update Category', 'text_domain' ),
			'view_item'                  => __( 'View Category', 'text_domain' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
			'popular_items'              => __( 'Popular Categories', 'text_domain' ),
			'search_items'               => __( 'Search Categories', 'text_domain' ),
			'not_found'                  => __( 'Not Found', 'text_domain' ),
			'no_terms'                   => __( 'No items', 'text_domain' ),
			'items_list'                 => __( 'Categories list', 'text_domain' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'text_domain' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'expense_category', array( 'expense' ), $args );
	}

	public function jc_custom_post_status(){
	     register_post_status( 'admin_removed', array(
	          'label'                     => _x( 'Admin removed', 'project_bids' ),
	          'public'                    => false,
	          'show_in_admin_all_list'    => false,
	          'show_in_admin_status_list' => true,
	          'label_count'               => _n_noop( 'Admin removed <span class="count">(%s)</span>', 'Admin removed <span class="count">(%s)</span>' )
	     ) );

	     register_post_status( 'rejected', array(
	          'label'                     => _x( 'Rejected', 'projects' ),
	          'public'                    => false,
	          'show_in_admin_all_list'    => false,
	          'show_in_admin_status_list' => true,
	          'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>' )
	     ) );
	}

	public function jc_append_post_status_list(){
	     global $post;
	     $complete = '';
	     $label = '';
	     if($post->post_type == 'project_bids'){
	          if($post->post_status == 'admin_removed'){
	               $complete = ' selected="selected"';
	               $label = '<span id="post-status-display"> Admin removed</span>';
	          }
	          echo '
	          <script>
	          jQuery(document).ready(function($){
	               $("select#post_status").append("<option value=\"admin_removed\" '.$complete.'>Admin removed</option>");
	               $(".misc-pub-section label").append("'.$label.'");
	          });
	          </script>
	          ';
	     }
	    if($post->post_type == 'projects'){

		 	 $complete = '';
	     		$label = '';
			if($post->post_status == 'rejected'){
	               $complete = ' selected="selected"';
	               $label = '<span id="post-status-display"> Rejected</span>';
	          }
	          echo '
	          <script>
	          jQuery(document).ready(function($){
	               $("select#post_status").append("<option value=\"rejected\" '.$complete.'>Rejected</option>");
	               $(".misc-pub-section label").append("'.$label.'");
	          });
	          </script>
	          ';
			}
	   }
}

endif; 

new Balloon_Helper();

?>
<?php
if ( ! class_exists( 'API_RoutesController', false ) ) : 

class API_RoutesController extends API_BaseController {

 
	function __construct()
	{ 
		$this->init_action();
 	}

	function init_action()
	{
		add_action( 'rest_api_init', array($this, 'balloon_register_api_rest_routes' )); 

		add_filter( 'jwt_auth_whitelist', array($this, 'jwt_auth_whitelist_callback'));
		add_filter( 'jwt_auth_whitelist', array($this, 'jwt_auth_with_auth_callback'));
		// add_filter( 'jwt_auth', array($this, 'jwt_auth_with_auth_callback'));
		
	}
	function jwt_auth_with_auth_callback( $endpoints ){
		$custom_endpoints = array( 

							'/wp-json/api/v1/projects',
					        '/wp-json/api/v1/project/create',
					        '/wp-json/api/v1/project/add_photo',
					        '/wp-json/api/v1/project/update_project',
					        '/wp-json/api/v1/project/get_single_project',
					        '/wp-json/api/v1/project/assign_tenant',
					        '/wp-json/api/v1/project/assign_contractor',
					        '/wp-json/api/v1/project/delete',
					        '/wp-json/api/v1/project/update',
					        '/wp-json/api/v1/project/delete_photo',
					        '/wp-json/api/v1/project/not_interested',

					        '/wp-json/api/v1/bid/create',
					        '/wp-json/api/v1/bid/validate',
					        '/wp-json/api/v1/bid/add_document',
					        '/wp-json/api/v1/bid/admin_accept_decline',

					        '/wp-json/api/v1/tenants',
					        '/wp-json/api/v1/tenant/detail',
					        '/wp-json/api/v1/tenant/create',
					        '/wp-json/api/v1/tenant/update',
					        '/wp-json/api/v1/tenant/delete',
					        '/wp-json/api/v1/tenant/search',
					        '/wp-json/api/v1/tenant/conpany_flag',
					        '/wp-json/api/v1/photos/add',
					        '/wp-json/api/v1/photos/add_user',
					        '/wp-json/api/v1/photos/delete',
					        '/wp-json/api/v1/photos/update',

					        '/wp-json/api/v1/contractors',
					        '/wp-json/api/v1/contractor/detail',
					        '/wp-json/api/v1/contractor/create',
					        '/wp-json/api/v1/contractor/update',
					        '/wp-json/api/v1/contractor/delete',
					        '/wp-json/api/v1/contractor/search',
					        '/wp-json/api/v1/notes',
					        '/wp-json/api/v1/note/create',
					        '/wp-json/api/v1/note/update',
					        '/wp-json/api/v1/note/delete',

					        '/wp-json/api/v1/user_notes',
					        '/wp-json/api/v1/user_note/create',
					        '/wp-json/api/v1/user_note/update',
					        '/wp-json/api/v1/user_note/delete',
					        
					         '/wp-json/api/v1/schedule_group/create',
					        '/wp-json/api/v1/schedule_group/update',
					        '/wp-json/api/v1/schedule_group/all',
					        '/wp-json/api/v1/schedule_group/delete',
					        '/wp-json/api/v1/schedule_group/reorders',
					        '/wp-json/api/v1/schedule/create',
					        '/wp-json/api/v1/schedule/update',
					        '/wp-json/api/v1/schedule/all',
					        '/wp-json/api/v1/schedule/get_one',
					        '/wp-json/api/v1/schedule/delete',
					        '/wp-json/api/v1/schedule/reorders',
					        '/wp-json/api/v1/schedule/calendar',

					        
					        '/wp-json/api/v1/incident/create',
					        '/wp-json/api/v1/incident/update',
					        '/wp-json/api/v1/incident/delete',
					        '/wp-json/api/v1/incident/all',
					        '/wp-json/api/v1/incident/get_one',

					        '/wp-json/api/v1/expense_category/create',
					        '/wp-json/api/v1/expense_category/all',
					        '/wp-json/api/v1/expense/create',
					        '/wp-json/api/v1/expense/update',
					        '/wp-json/api/v1/expense/delete',
					        '/wp-json/api/v1/expense/get_one',
					        '/wp-json/api/v1/expense/all',

					        '/wp-json/api/v1/support_team/create',
					        '/wp-json/api/v1/support_team/update',
					        '/wp-json/api/v1/support_team/delete',
					        '/wp-json/api/v1/support_team/get_one',
					        '/wp-json/api/v1/support_team/all',

					        '/wp-json/api/v1/contacts',
					        '/wp-json/api/v1/contacts/create',
					        '/wp-json/api/v1/contacts/update',
					        '/wp-json/api/v1/contacts/delete',

					        '/wp-json/api/v1/get-profile',
					        '/wp-json/api/v1/update-profile',

							'/wp-json/api/v1/security/create_master_password',
					        '/wp-json/api/v1/security/create_contact',
					        '/wp-json/api/v1/security/update_contact',
					        '/wp-json/api/v1/security/delete_contact',
							
							'/wp-json/api/v1/emergency_contacts/all',
							

					    ); 
			
		return array_unique( array_merge( $endpoints, $custom_endpoints ) );
	}
	function jwt_auth_whitelist_callback($endpoints)
	{
		
		$custom_endpoints = array( 
					        '/wp-json/jwt-auth/v1/token',
					        '/wp-json/api/v1/register',
					        '/wp-json/api/v1/forget-password',
					        '/wp-json/api/v1/forget-otp',
					        '/wp-json/api/v1/new-password',
					        '/wp-json/api/v1/reset-password',
					    ); 
			
		return array_unique( array_merge( $endpoints, $custom_endpoints ) );
		//return $endpoints;
	}

	public function balloon_register_api_rest_routes()
	{
 		$API_UserControler = new API_UserControler();
 		$API_ProjectsController = new API_ProjectsController();
 		$API_TenantsControler = new API_TenantsControler();
 		$API_VendorControler = new API_VendorControler();
 		$API_VendorControler = new API_VendorControler();
 		$API_PhotosController = new API_PhotosController();
 		$API_ContractorsController = new API_ContractorsController();
 		$API_NotesController = new API_NotesController();
 		$API_ScheduleController = new API_ScheduleController();
 		$API_SupportTeamController = new API_SupportTeamController();
 		$API_ContactsController = new API_ContactsController();
		$API_SecurityInforController = new API_SecurityInfoController();
		$API_EmergencyContactController = new API_EmergencyContactsController();
 		// $API_IncidentsController =  new API_IncidentsController();
 		$API_IncidentsController = '';
 		// $API_ExpensesController =  new API_ExpensesController();
 		$API_ExpensesController = '';



 		
 		 
		register_rest_route(
			$this->apinamespace,
			'register',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_UserControler, 'user_api_create_vendor_and_client'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'forget-password',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_UserControler, 'user_api_forget_password'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'register',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_UserControler, 'user_api_create_vendor_and_client'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'forget-otp',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_UserControler, 'user_api_forget_password_otp'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'new-password',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_UserControler, 'user_api_new_password'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'reset-password',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_UserControler, 'user_api_reset_password'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'get-profile',
			array(
				'methods'             => 'GET',
				'callback'            => array($API_UserControler, 'get_profile_data'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'update-profile',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_UserControler, 'update_profile_data'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'change-password',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_UserControler, 'user_change_password'),
				'permission_callback' => '__return_true',
			)
		);
		/** Start: Note API */
		register_rest_route(
			$this->apinamespace,
			'note/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_NotesController, 'create_new_note'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'notes',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_NotesController, 'get_all_notes'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'note/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_NotesController, 'update_note'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'note/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_NotesController, 'delete_note'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'user_note/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_NotesController, 'create_new_user_note'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'user_notes',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_NotesController, 'get_user_all_notes'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'user_note/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_NotesController, 'update_user_note'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'user_note/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_NotesController, 'delete_user_note'),
				'permission_callback' => '__return_true',
			)
		);
		/** End : Note API */
		/** Project API Start */
		register_rest_route(
			$this->apinamespace,
			'projects',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'get_all_projects_list'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'project/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'create_new_project'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'project/add_photo',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'create_project_photo'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'project/update_project',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'update_project_by_id'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'project/get_single_project',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'get_single_project'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'project/assign_tenant',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'assign_tenant_to_project'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'project/assign_contractor',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'assign_contractor_to_project'),
				'permission_callback' => '__return_true',
			)
		);
		
		register_rest_route(
			$this->apinamespace,
			'project/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'delete_project_by_ids'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'project/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'update_project_photo'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'project/delete_photo',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'delete_project_photos'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'project/not_interested',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'set_project_not_interested'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'bid/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'create_project_bid'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'bid/validate',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'validate_project_bid'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'bid/add_document',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'upload_project_document'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'bid/admin_accept_decline',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ProjectsController, 'admin_bid_accept_decline'),
				'permission_callback' => '__return_true',
			)
		);
		
		
		/** Project API End */
		register_rest_route(
			$this->apinamespace,
			'tenants',
			array(
				'methods'             => 'GET',
				'callback'            => array($API_TenantsControler, 'get_all_client_list'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'tenant/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_TenantsControler, 'tenant_api_create_new_tenant'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'tenant/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_TenantsControler, 'update_client_by_id'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'tenant/detail',
			array(
				'methods'             => 'GET',
				'callback'            => array($API_TenantsControler, 'get_single_tenant_by_id'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'tenant/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_TenantsControler, 'delete_tenant_by_id'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'tenant/search',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_TenantsControler, 'search_tenants'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'tenant/conpany_flag',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_TenantsControler, 'tenant_conpany_flag'),
				'permission_callback' => '__return_true',
			)
		);
		/** Photo Route Start */
		register_rest_route(
			$this->apinamespace,
			'photos/add',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_PhotosController, 'upload_post_photos'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'photos/add_user',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_PhotosController, 'upload_user_photos'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'photos/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_PhotosController, 'delete_photo_by_id'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'photos/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_PhotosController, 'update_photo_by_id'),
				'permission_callback' => '__return_true',
			)
		);
		/** Photo Route End */

		/** Contractor Route START **/

		register_rest_route(
			$this->apinamespace,
			'contractors',
			array(
				'methods'             => 'GET',
				'callback'            => array($API_ContractorsController, 'get_all_contractors_list'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'contractor/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ContractorsController, 'contractor_api_create_new_contractor'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'contractor/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ContractorsController, 'update_contractor_by_id'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'contractor/detail',
			array(
				'methods'             => 'GET',
				'callback'            => array($API_ContractorsController, 'get_single_contractor_by_id'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$this->apinamespace,
			'contractor/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ContractorsController, 'delete_contractor_by_id'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'contractor/search',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ContractorsController, 'search_contractors'),
				'permission_callback' => '__return_true',
			)
		);
		
		/** Contractor Route END **/

		/** Schedule Route Start */
		register_rest_route(
			$this->apinamespace,
			'schedule_group/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'create_schedule_group'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule_group/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'update_schedule_group'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule_group/all',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'get_all_schedule_groups'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule_group/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'delete_schedule_groups'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule_group/reorders',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'rearrange_group_schedules'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'create_schedule'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'update_schedule'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule/all',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'get_all_schedule'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule/get_one',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'get_single_schedule'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'delete_schedules'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule/reorders',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'rearrange_schedules'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'schedule/calendar',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ScheduleController, 'get_schedule_calendar'),
				'permission_callback' => '__return_true',
			)
		);
		
		
		/** Schedule Route End */

		/** Incident Route Start */

		
		
		register_rest_route(
			$this->apinamespace,
			'incident/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_IncidentsController, 'create_new_incident'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'incident/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_IncidentsController, 'update_incident'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'incident/all',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_IncidentsController, 'search_incidents'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'incident/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_IncidentsController, 'delete_incident'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'incident/get_one',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_IncidentsController, 'get_single_incident'),
				'permission_callback' => '__return_true',
			)
		);
		/** Incident Route End */

		/** Expense Route Start */
		register_rest_route(
			$this->apinamespace,
			'expense_category/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ExpensesController, 'create_expense_category'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'expense_category/all',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ExpensesController, 'get_all_expense_categories'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'expense/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ExpensesController, 'create_new_expense'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'expense/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ExpensesController, 'update_expense'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'expense/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ExpensesController, 'delete_expense'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'expense/all',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ExpensesController, 'search_expenses'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'expense/get_one',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ExpensesController, 'get_single_expense'),
				'permission_callback' => '__return_true',
			)
		);
		/** Expense Route Ens */

		/** Supoort Team Route Start */
		 
		register_rest_route(
			$this->apinamespace,
			'support_team/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_SupportTeamController, 'create_new_team'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'support_team/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_SupportTeamController, 'update_support_team_by_id'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'support_team/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_SupportTeamController, 'delete_user_by_id'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'support_team/all',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_SupportTeamController, 'search_users'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'support_team/get_one',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_SupportTeamController, 'get_single_user'),
				'permission_callback' => '__return_true',
			)
		);
		/** Supoort Team Route End */
		/** Start : Contact API */
		register_rest_route(
			$this->apinamespace,
			'contacts/create',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ContactsController, 'create_contact'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'contacts',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ContactsController, 'get_all_contacts'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'contacts/update',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ContactsController, 'update_contact'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->apinamespace,
			'contacts/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_ContactsController, 'delete_contact'),
				'permission_callback' => '__return_true',
			)
		);
		/** End : Contact API */

		register_rest_route(
			$this->apinamespace,
			'security/create_master_password',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_SecurityInforController, 'create_master_password'),
				'permission_callback' => '__return_true',
			)
		);

		// register_rest_route(
		// 	$this->apinamespace,
		// 	'security/create_contact',
		// 	array(
		// 		'methods'             => 'POST',
		// 		'callback'            => array($API_SecurityInforController, 'create_contact'),
		// 		'permission_callback' => '__return_true',
		// 	)
		// );

		// register_rest_route(
		// 	$this->apinamespace,
		// 	'security/update_contact',
		// 	array(
		// 		'methods'             => 'POST',
		// 		'callback'            => array($API_SecurityInforController, 'update_contact'),
		// 		'permission_callback' => '__return_true',
		// 	)
		// );

		// register_rest_route(
		// 	$this->apinamespace,
		// 	'security/delete_contact',
		// 	array(
		// 		'methods'             => 'POST',
		// 		'callback'            => array($API_SecurityInforController, 'delete_contact'),
		// 		'permission_callback' => '__return_true',
		// 	)
		// );


		// emergency_contacts

		register_rest_route(
			$this->apinamespace,
			'emergency_contacts/all',
			array(
				'methods'             => 'POST',
				'callback'            => array($API_EmergencyContactController, 'search_emergency'),
				'permission_callback' => '__return_true',
			)
		);
	}
	
}

endif;
new API_RoutesController()

?>
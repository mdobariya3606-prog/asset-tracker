<?php

/**
 * ============================================================================
 * SECTION 1: SESSION INITIALIZATION & IMPORTS
 * ============================================================================
 * Starts the user session and imports all necessary controllers and configuration.
 */

session_start();

use App\Config\Database;
use App\Controllers\Asset\CreateAssetController;
use App\Controllers\Asset\SelectAssetController;
use App\Controllers\Asset_request\ManageRequestController;
use App\Controllers\Asset_request\RequestAssetController;
use App\Controllers\Asset_request\SelectAssetRequestController;
use App\Controllers\Department\CreateDepartmentController;
use App\Controllers\Department\SelectDepartmentController;
use App\Controllers\Designation\CreateDesignationController;
use App\Controllers\Designation\SelectDesignationController;
use App\Controllers\User\CreateUserController;
use App\Controllers\User\EditUserController;
use App\Controllers\User\LoginController;
use App\Controllers\User\ProfileController;
use App\Controllers\User\ResetPasswordController;
use App\Controllers\User\SelectUserController;
use App\Models\Asset;
use App\Models\AssetRequest;

/**
 * ============================================================================
 * SECTION 2: AUTOLOADING & ENVIRONMENT SETUP
 * ============================================================================
 * Registers the Composer autoloader and loads environment variables from the
 * .env file located in the root directory.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

/**
 * ============================================================================
 * SECTION 3: DATABASE CONNECTION
 * ============================================================================
 * Initializes the database configuration and establishes a connection using PDO.
 */

$conn = (new Database())->getConnection();

// Keep dashboard identity data in sync with admin updates made in another session.
// The session retains authentication state; name, email, and role are refreshed
// from the database for every authenticated request.
if (!empty($_SESSION['user_id'])) {
	$currentUser = (new \App\Models\User($conn))->find((int)$_SESSION['user_id'])[0] ?? null;
	if ($currentUser === null) {
		session_unset();
	} else {
		$_SESSION['user_name'] = $currentUser['name'];
		$_SESSION['user_email'] = $currentUser['email'];
		$_SESSION['user_role'] = $currentUser['role'];
	}
}

/**
 * ============================================================================
 * SECTION 4: ROUTE RESOLUTION
 * ============================================================================
 * Detects the HTTP request method (GET, POST, etc.) and the requested route action
 * from the URL query parameters.
 */

$method = $_SERVER['REQUEST_METHOD'];
$route = $_GET['route'] ?? '';

/**
 * ============================================================================
 * SECTION 5: APP ROUTER (SWITCH SYSTEM)
 * ============================================================================
 * Matches the combination of request method and route parameter to dispatch
 * the request strictly to the corresponding controller actions.
 */

try {
	switch ("$method:$route") {
		// GET:/index - Send visitors to the appropriate landing page
		case 'GET:':
			header('Location: index.php?route=' . (empty($_SESSION['user_id']) ? 'login' : 'users'));
			exit;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: DEPARTMENT VIEWS
		 * ------------------------------------------------------------------------ */

		// GET:departments - View department list or details of a single department
		case 'GET:departments':
			(new SelectDepartmentController($conn))->index($_GET);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: DESIGNATION VIEWS
		 * ------------------------------------------------------------------------ */

		// GET:designations - View designation list or details of a single designation
		case 'GET:designations':
			(new SelectDesignationController($conn))->index($_GET);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: USER VIEWS (PAGINATED LIST)
		 * ------------------------------------------------------------------------ */

		// GET:users - Display paginated, filterable, and sorted list of users
		case 'GET:users':
			(new SelectUserController($conn))->index($_GET);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: ASSET VIEWS
		 * ------------------------------------------------------------------------ */

		case 'GET:assets':
			(new SelectAssetController($conn))->index();
			break;

		case 'GET:assets/show':
			(new SelectAssetController($conn))->show((int)($_GET['id'] ?? 0));
			break;

		case 'GET:assets/create':
			(new CreateAssetController($conn))->create();
			break;

		case 'POST:assets/create':
			(new CreateAssetController($conn))->store($_POST);
			break;

		case 'GET:assets/edit':
			(new CreateAssetController($conn))->edit((int)($_GET['id'] ?? 0));
			break;

		case 'POST:assets/edit':
			(new CreateAssetController($conn))->update((int)($_GET['id'] ?? 0), $_POST);
			break;

		case 'GET:assets/delete':
			(new CreateAssetController($conn))->delete((int)($_GET['id'] ?? 0));
			break;

		case 'GET:assets/requests':
			(new SelectAssetRequestController($conn))->index();
			break;

		case 'GET:assets/requests/show':
			(new SelectAssetRequestController($conn))->show((int)($_GET['id'] ?? 0));
			break;

		case 'GET:assets/requests/manage':
			(new ManageRequestController($conn))->showManageForm((int)($_GET['id'] ?? 0));
			break;

		case 'POST:assets/requests/manage':
			(new ManageRequestController($conn))->update((int)($_GET['id'] ?? 0), $_POST);
			break;

		case 'GET:assets/request':
			(new AssetRequest($conn))->create($_GET['id']);

//			$_SESSION['success'] = 'Asset request submitted successfully.';
//			header('Location: index.php?route=assets');
			break;

		case 'POST:assets/request':
			$asset = (new Asset($conn))->find((int)($_GET['id'] ?? 0));
			if (empty($asset) || strtoupper((string)($asset['status'] ?? '')) !== 'AVAILABLE') {
				$_SESSION['general'] = 'Asset #' . $asset['id'] . ' is not available for request.';
				header('Location: index.php?route=assets');
				exit;
			}

			(new RequestAssetController($conn))->store($_GET['id'], $_POST);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: DEPARTMENT CREATION (GET FORM)
		 * ------------------------------------------------------------------------ */

		// GET:departments/create - Render the department creation form
		case 'GET:departments/create':
			(new CreateDepartmentController($conn))->create();
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: DESIGNATION CREATION (GET FORM)
		 * ------------------------------------------------------------------------ */

		// GET:designations/create - Render the designation creation form
		case 'GET:designations/create':
			(new CreateDesignationController($conn))->create();
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: USER CREATION (GET FORM & POST ACTIONS)
		 * ------------------------------------------------------------------------ */

		// GET:users/create - Render user registration form with departments and designations
		case 'GET:users/create':
			(new CreateUserController($conn))->create();
			break;

		// POST:users/create - Handle submission of user registration form
		case 'POST:users/create':
			(new CreateUserController($conn))->store($_POST);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: USER MODIFICATION (GET & POST ACTIONS)
		 * ------------------------------------------------------------------------ */

		// GET:users/edit - Render edit user form loaded with existing values
		case 'GET:users/edit':
			(new EditUserController($conn))->edit($_GET);
			break;

		// POST:users/edit - Process requested user changes
		case 'POST:users/edit':
			(new EditUserController($conn))->updateUser($_GET, $_POST);
			break;

		// GET:users/profile - Display a user's profile and admin actions
		case 'GET:users/profile':
			(new ProfileController($conn))->show($_GET);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: ADMIN PASSWORD RESET
		 * ------------------------------------------------------------------------ */

		// GET/POST:users/reset-password - Admin resets a user's password
		case 'GET:users/reset-password':
			(new ResetPasswordController($conn))->edit($_GET);
			break;

		case 'POST:users/reset-password':
			(new ResetPasswordController($conn))->store($_GET, $_POST);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: USER DELETION
		 * ------------------------------------------------------------------------ */

		// GET:users/delete - Permanently deletes a single user
		case 'GET:users/delete':
			(new EditUserController($conn))->destroy($_GET);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: FORM SUBMISSIONS (DEPARTMENTS & DESIGNATIONS)
		 * ------------------------------------------------------------------------ */

		// POST:designations/create - Store a newly created designation
		case 'POST:designations/create':
			(new CreateDesignationController($conn))->store($_POST);
			break;

		// POST:departments/create - Store a newly created department
		case 'POST:departments/create':
			(new CreateDepartmentController($conn))->store($_POST);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: AUTHENTICATION FLOW (LOGIN & LOGOUT)
		 * ------------------------------------------------------------------------ */

		// GET:login - Render the user login form page
		case 'GET:login':
			(new LoginController($conn))->showLoginForm();
			break;

		// POST:login - Process login credentials and authenticate user
		case 'POST:login':
			(new LoginController($conn))->login($_POST);
			break;

		// GET:logout - Ends the user session and redirects to login
		case 'GET:logout':
			(new LoginController($conn))->signout();
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: FALLBACK DEFAULTS
		 * ------------------------------------------------------------------------ */

		// Default fallback for layout requests that match no routes (404 Page Not Found)
		default:
			require '../resources/views/errors/404.php';
			break;
	}
} catch (\Throwable $e) {
	/**
	 * ============================================================================
	 * SECTION 6: GLOBAL EXCEPTION HANDLING
	 * ============================================================================
	 * Catches all unhandled exceptions, logs detailed traces internally, and
	 * displays a generic 500 server error view to the user.
	 */
	error_log($e->getMessage() . "\n" . $e->getTraceAsString());
//	require '../resources/views/errors/500.php';
	throw $e;
	exit;
}

function view(string $viewFile, array $vars = []): void
{
	$pages = [
		'login' => '../resources/views/users/login.php',

		'403' => '../resources/views/errors/403.php',
		'404' => '../resources/views/errors/404.php',
		'500' => '../resources/views/errors/500.php',

		'users.edit' => '../resources/views/users/edit.php',
		'users.profile' => '../resources/views/users/profile.php',
		'users.register' => '../resources/views/users/register.php',
		'users.reset-password' => '../resources/views/users/reset-password.php',
		'users.select' => '../resources/views/users/select.php',

		'departments.create' => '../resources/views/departments/create.php',
		'departments.select' => '../resources/views/departments/select.php',

		'designations.create' => '../resources/views/designation/create.php',
		'designations.select' => '../resources/views/designation/select.php',

		'assets.create' => '../resources/views/assets/create.php',
		'assets.edit' => '../resources/views/assets/edit.php',
		'assets.select' => '../resources/views/assets/select.php',
		'assets.show' => '../resources/views/assets/show.php',

		'asset.requests.create' => '../resources/views/asset_requests/create.php',
		'asset.requests.select' => '../resources/views/asset_requests/select.php',
		'asset.requests.manage' => '../resources/views/asset_requests/manage.php',
		'asset.requests.show' => '../resources/views/asset_requests/show.php',
	];

	$viewFile = $pages[$viewFile] ?? null;
	if (!$viewFile) {
		require '../resources/views/errors/404.php';
	}

	extract($vars, EXTR_SKIP);
	require $viewFile;
}

function redirect(string $page)
{
	$pages = [
		'login' => 'index.php?route=login',
	];
}
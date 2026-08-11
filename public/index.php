<?php

use App\Config\Mail;
use App\Controllers\Email\ForgotPasswordEmail;

//set_error_handler(function (
//  int    $severity,
//  string $message,
//  string $file,
//  int    $line
//) {
//  logError(sprintf(
//     "[%s] %s\nFile: %s\nLine: %d",
//     match ($severity) {
//        E_WARNING => 'WARNING',
//        E_NOTICE => 'NOTICE',
//        E_USER_WARNING => 'USER WARNING',
//        E_USER_NOTICE => 'USER NOTICE',
//        default => 'ERROR',
//     },
//     $message,
//     $file,
//     $line
//  ));
//
//  // Don't let PHP display its default warning
//  return true;
//});

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
use App\Controllers\Email\ResetPasswordController as EmailResetPasswordController;
use App\Controllers\User\CreateUserController;
use App\Controllers\User\EditUserController;
use App\Controllers\User\LoginController;
use App\Controllers\User\ProfileController;
use App\Controllers\User\ResetPasswordController;
use App\Controllers\User\SelectUserController;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Controllers\Email\ResetPasswordController as ResetViaEmail;

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
$back = null;

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
			route(empty($_SESSION['user_id']) ? 'login' : 'users');
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
			break;

		case 'POST:assets/request':
			$asset = (new Asset($conn))->find((int)($_GET['id'] ?? 0));
			if (empty($asset) || strtoupper((string)($asset['status'] ?? '')) !== 'AVAILABLE') {
				$_SESSION['general'] = 'Asset #' . $asset['id'] . ' is not available for request.';
				route('assets');
				exit;
			}

			(new RequestAssetController($conn))->store($_GET['id'], $_POST);
			break;


		case 'GET:assets/pdf':
			$stmt = $conn->query('select * from assets order by status');
			$assets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			view('assets.pdf', ['assets' => $assets]);
			break;

		case 'GET:assets/requests/pdf':
			$stmt = $conn->query('select * from asset_requests order by status');
			$requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			view('asset.requests.pdf', ['requests' => $requests]);
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

		case 'GET:users/pdf':
			$stmt = $conn->query('
			select u.*, dep.name as department_name, des.name as designation_name
			from users u
			left join departments dep
			on u.department_id = dep.id
			
			left join designations des
			on u.designation_id = des.id

			order by role');
			$users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			view('users.pdf', ['users' => $users]);
			break;

		case 'GET:users/employees/pdf':
			$stmt = $conn->query('select * from users where role = "EMPLOYEE"');
			$users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			view('users.pdf', ['users' => $users]);
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
		case 'POST:logout':
			(new LoginController($conn))->signout();
			break;

		// GET:send-rp-mail - Send a password reset email to the authenticated user
		case 'GET:send-rp-mail':
			(new ForgotPasswordEmail($conn))->sendResetPasswordMail();
			break;

		// GET:reset-password - Verify the reset link and display the password reset form
		case 'GET:reset-password':
			(new EmailResetPasswordController($conn))->resetPassword($_GET);
			break;

		// GET:fp-mail - Display the forgot password form
		case 'GET:fp-mail':
			view('fp-mail');
			break;

		// POST:fp-mail - Process the forgot password form and send a password reset link
		case 'POST:fp-mail':
			(new ForgotPasswordEmail($conn))->sendForgotPasswordMail($_POST);
			break;

		// POST:reset-password - Verify the reset link and update the user's password
		case 'POST:reset-password':
			(new ResetViaEmail($conn))->updatePassword($_GET, $_POST);
			break;

		/* ------------------------------------------------------------------------
		 * ROUTE GROUP: FALLBACK DEFAULTS
		 * ------------------------------------------------------------------------ */

		// Default fallback for layout requests that match no routes (404 Page Not Found)
		default:
			view(404);
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
	logError($e);
	view(500);
	exit;
}

/**
 * ============================================================================
 * SECTION 7: VIEW RENDERING
 * ============================================================================
 * Maps logical view names to template files and renders the selected view.
 */

function view(int|string $viewFile, array $vars = []): void
{
	$prefix = '../resources/views';
	$pages = [
		'login' => $prefix .  '/users/login.php',

		403 => $prefix .  '/errors/403.php',
		404 => $prefix .  '/errors/404.php',
		500 => $prefix .  '/errors/500.php',

		'users.edit' => $prefix .  '/users/edit.php',
		'users.profile' => $prefix .  '/users/profile.php',
		'users.register' => $prefix .  '/users/register.php',
		'users.reset-password' => $prefix .  '/users/reset-password.php',
		'users.select' => $prefix .  '/users/select.php',
		'users.pdf' => $prefix .  '/users/export-pdf.php',

		'departments.create' => $prefix .  '/departments/create.php',
		'departments.select' => $prefix .  '/departments/select.php',

		'designations.create' => $prefix .  '/designation/create.php',
		'designations.select' => $prefix .  '/designation/select.php',

		'assets.create' => $prefix .  '/assets/create.php',
		'assets.edit' => $prefix .  '/assets/edit.php',
		'assets.select' => $prefix .  '/assets/select.php',
		'assets.show' => $prefix .  '/assets/show.php',
		'assets.pdf' => $prefix .  '/assets/export-pdf.php',

		'asset.requests.create' => $prefix .  '/asset_requests/create.php',
		'asset.requests.select' => $prefix .  '/asset_requests/select.php',
		'asset.requests.manage' => $prefix .  '/asset_requests/manage.php',
		'asset.requests.show' => $prefix .  '/asset_requests/show.php',
		'asset.requests.pdf' => $prefix . '/asset_requests/export-pdf.php',

		'reset-password' => $prefix .  '/auth/reset_password.php',
		'fp-mail' => $prefix .  '/auth/fp_mail.php',
	];

	$viewFile = $pages[$viewFile] ?? null;
	if (!$viewFile) {
		view(404);
		exit;
	}

	extract($vars, EXTR_SKIP);
	require $viewFile;
}

/**
 * ============================================================================
 * SECTION 8: ROUTE REDIRECTION HELPER
 * ============================================================================
 * Validates allowed redirect routes and redirects the browser to the target
 * application route.
 */

function route(string $route, $params = '')
{
	$routes = [
		'login',
		'assets',
		'assets/requests',
		'departments',
		'designations',
		'users',
		'users/create',
		'fp-mail',
	];

	if (!in_array($route, $routes, true)) {
		view(404);
		exit;
	}

	header("Location: index.php?route=$route&" . $params);
}

/**
 * ============================================================================
 * SECTION 9: ERROR LOGGING
 * ============================================================================
 * Normalizes exceptions or string errors, creates the log directory when
 * required, and appends the error details to the configured log file.
 */

function logError(Throwable|string $error, string $file = 'errors'): void
{
	if ($error instanceof Throwable) {
		$message = sprintf(
			"[%s]\nFile: %s\nLine: %d\n\n%s",
			$error->getMessage(),
			$error->getFile(),
			$error->getLine(),
			$error->getTraceAsString()
		);
	} else {
		$message = $error;
	}

	$logDir = '../logs';

	if (!is_dir($logDir)) {
		mkdir($logDir, 0777, true);
	}

	$logFile = "{$logDir}/{$file}.log";

	$entry = sprintf(
		"[%s] %s%s",
		date('Y-m-d H:i:s'),
		$message,
		PHP_EOL
	);

	file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

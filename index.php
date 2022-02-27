<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, PUT, GET, OPTIONS, DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With,Authorization");
	header("Access-Control-Allow-Headers: cache-control,X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
	header("HTTP/1.1 200 OK");
	
	$method = $_SERVER['REQUEST_METHOD'];
	 
	if ($method == "OPTIONS")
	{
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: cache-control,X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
		header("HTTP/1.1 200 OK");
		die();
	}  

	//print_r($_SERVER);
	//database config

	$dirSep = DIRECTORY_SEPARATOR;
	$dir =  __DIR__;

	defined('DB_SERVER') ? null : define("DB_SERVER", "");   
	defined('DB_USER')   ? null : define("DB_USER", "");
	defined('DB_PASS')   ? null : define("DB_PASS", "");
	defined('DB_NAME')   ? null : define("DB_NAME", "");

	defined('APP_NAME') ? null : define("APP_NAME", "");
	defined('SESSION_NAME') ? null : define("SESSION_NAME", ""); 

	define("BASE_DIR", __DIR__);

	define("APP_DIR", BASE_DIR . "{$dirSep}App{$dirSep}");
	define("CORE_DIR", BASE_DIR . "{$dirSep}Core{$dirSep}");
	define("CONTROLLERS_DIR", BASE_DIR . "{$dirSep}App{$dirSep}Controllers{$dirSep}");
	define("REPOSITORIES_DIR", BASE_DIR . "{$dirSep}App{$dirSep}Repositories{$dirSep}");
	define("MODELS_DIR", BASE_DIR . "{$dirSep}App{$dirSep}Models{$dirSep}");
	define("UPLOADS_DIR", BASE_DIR . "{$dirSep}assets{$dirSep}uploads{$dirSep}");

	define("SERVER_ROOT", $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] );

	define("SHOW_ERRORS", true);

	require_once CORE_DIR . "Db{$dirSep}autoload.php";
	require_once CORE_DIR . "Sms{$dirSep}AfricasTalkingGateway.php";
	require_once CORE_DIR . "Router.php";
	require_once CORE_DIR . "App.php";
	require_once CORE_DIR . "Files.php";
	require_once CORE_DIR . "Errors.php";
	require_once CORE_DIR . "Controller.php";
	require_once CORE_DIR . "Functions.php";
	require_once CORE_DIR . "Mailer.php";
	require_once CORE_DIR . "JwtTokens.php";

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	set_error_handler('Errors::errorHandler');
	set_exception_handler('Errors::exceptionHandler');
	//echo "hello  is8uauishaihasghjk ";
	$app = new App;  

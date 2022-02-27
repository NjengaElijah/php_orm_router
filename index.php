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

	// defined('DB_SERVER') ? null : define("DB_SERVER", "www.domainessays.com");
	// defined('DB_USER')   ? null : define("DB_USER", "ADMIN_USR");
	// defined('DB_PASS')   ? null : define("DB_PASS", "gzrxTr7dMiccDdZ7");
	// defined('DB_NAME')   ? null : define("DB_NAME", "writting_v2");

	defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");   
	defined('DB_USER')   ? null : define("DB_USER", "GLOBAL_USER");
	defined('DB_PASS')   ? null : define("DB_PASS", "EiXXAUvXVyYHCoAx");
	defined('DB_NAME')   ? null : define("DB_NAME", "admin_writting_v3");

	defined('APP_NAME') ? null : define("APP_NAME", "Api");
	defined('SESSION_NAME') ? null : define("SESSION_NAME", "api_class_writers"); 

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

	function getAllowedDomains()
	{
		$sites = SQLQuery::query("SELECT url FROM sites WHERE status = 1");
		$sts = [];
		foreach($sites as $st)
		{
			$sts[] = $st['url'];
		}
		return $sts;
	}

	$allowed_domains = getAllowedDomains();
	$url = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://admin.urgentwriters.com/';
	$url = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://admin.urgentwriters.com/';
	
	$host = parse_url($url)['host'];
	$host = str_replace('www.','',$host);
	//die($host);
	if(!in_array($host,$allowed_domains))
	{
		// exit;
		//die(printError("Unauthorized access , invalid domain ",null,401))	;
	}

	$dirStr = $dir . '';

	$dir = $dirStr . "{$dirSep}App{$dirSep}Models{$dirSep}";
	$dirRepo = $dirStr . "{$dirSep}App{$dirSep}Repositories{$dirSep}";
	$dirDB = $dirStr . "{$dirSep}App{$dirSep}core{$dirSep}";

	//echo $dir;

	//use var x to escape the two virtual dirs created i.e .. and .

	$x = 0;
	foreach (scandir($dir) as $file)
	{
		if ($x > 1) {
			require_once $dir . $file;
		}
		$x++;
	}
	//use var x to escape the two virtual dirs created i.e .. and .

	$x = 0;
	foreach (scandir($dirRepo) as $file)
	{
		if ($x > 1) {
			require_once $dirRepo . $file;
		}
		$x++;
	}
	/**
	 * Error and Exception handling
	 */
	error_reporting(E_ALL);
	function getCurrentDateTime()
	{
		return SQLQuery::query("SELECT NOW() AS 'now';")[0]['now'];
	}
	function seconds2human($ss) 
	{
		$s = $ss%60;
		$m = floor(($ss%3600)/60);
		$h = floor(($ss%86400)/3600);
		$d = floor(($ss%2592000)/86400);
		$M = floor($ss/2592000);

		return "$M months, $d days, $h hours, $m minutes, $s seconds";
	}
	function getPagenationFilters($limit = "LIMIT")
	{
		$data = validateFormData($_GET);
		$chunk = 20;
		$page = 0;
		if(isset($data['chunk'])  && is_numeric($_GET['chunk']) &&  $_GET['chunk'] != 0){
			$chunk = ($data['chunk']);
		}
		if(isset($_GET['page']) && is_numeric($_GET['page']) &&  $_GET['page'] != 0){
			$page = $data['page'];
		}
		//e.g 0 - 36 , 36 - 72 , 72 - 108
		//
		$from = ($page - 1) * $chunk;
		$to   = $chunk;
		//echo $from .' '.$to;
		//if we have a valid chung and a valid page
		if(isset($data['page'])){
			return (($chunk != -1) && ($page != -1)) ? " $limit {$from} , {$to} " : "";
		}
		else{
			return "";
		}
	}
	/**
	 *	get the currently running site
	 */
	function getSite()
	{
		//print_r($_SERVER);   
		$url = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://admin.urgentwriters.com';
		$host = @parse_url($url)['host'];
		$port = isset(parse_url($url)['port']) ? parse_url($url)['port'] : 80;
		
		$host = str_replace('www.','',$host);
		if(substr($host, 0, 7 ) === "192.168")
		{
			$host = "nursingtermpapers.com"; 
		}
		
		//if(substr($host, 0, 10 ) === "admin.urge" || $port === ':8088')
		if($port == '8088' || substr($host, 0, 10 ) === "admin.urge") 
		{
			$host = "admin.urgentwriters.com";
		}
		//echo $host;
		$site = Site::fromQuery("SELECT * FROM sites WHERE application_url LIKE '%".$host."%' and deleted = 0");
		
		if(count($site)) 
		{
			//var_dump($site);
			return $site[0];
		}
		else
		{
			die(printError("Unauthorized site",  [ 'host' => $host ],401));
		}
	}
	function getFtpCreds()
	{
		$site_id = getSite()->id;
		$site = Site::id($site_id); 
		$ftp_creds = SQLQuery::query(" 
		SELECT sd.value as 'ftp_server',b.ftp_user,c.ftp_password,d.ftp_dir FROM `user_data` as sd 
		inner join (SELECT usr.client_id,usr.value as 'ftp_user' FROM `user_data` as usr where usr.subject LIKE '%ftp_user%') as b on b.client_id = sd.client_id 
		inner join (SELECT usr.client_id,usr.value as 'ftp_password' FROM `user_data` as usr where usr.subject LIKE '%ftp_password%') as c on c.client_id = sd.client_id 
		inner join (SELECT usr.client_id,usr.value as 'ftp_dir' FROM `user_data` as usr where usr.subject LIKE '%ftp_dir%') as d on d.client_id = sd.client_id  
		where sd.subject LIKE '%ftp_server%' and sd.client_id = ".$site->client_id);
		
		//.getSite()->id);
		if(count($ftp_creds))
		{		
			return $ftp_creds[0];
		}
		else
		{
			printError("Ftp Credentials not set up, Contact admin ",null,401);
		}
	}
	function getSmtpCreds()
	{
		$site_id = getSite()->id;
		$ftp_creds = SQLQuery::query("SELECT sd.value as 'smtp_server',b.smtp_user,c.smtp_password,d.smtp_port FROM `site_data` as sd inner join (SELECT usr.site_id,usr.value as 'smtp_user' FROM `site_data` as usr where usr.subject LIKE '%smtp_user%') as b on b.site_id = sd.site_id inner join (SELECT usr.site_id,usr.value as 'smtp_password' FROM `site_data` as usr where usr.subject LIKE '%smtp_password%') as c on c.site_id = sd.site_id inner join (SELECT usr.site_id,usr.value as 'smtp_port' FROM `site_data` as usr where usr.subject LIKE '%smtp_port%') as d on d.site_id = sd.site_id where sd.subject LIKE '%smtp_server%' and sd.site_id = ".$site_id);//.getSite()->id);
		if(count($ftp_creds))
		{
			return $ftp_creds[0];
		}
		else
		{
			printError("Ftp Credentials not set up, Contact admin ",null,401);
		}
	}
	function getSmtpServer()
	{
		return getSmtpCreds();
	}
	function getData()
	{
		//die(file_get_contents('php://input'));
		$data = (isset($_SERVER['HTTP_ORIGIN']) || $_SERVER['REQUEST_METHOD'] != 'POST') ? json_decode(file_get_contents('php://input'),true) : $_POST;
		//$data = json_decode($request, true);
		return validateFormData($data);   

		//return validateFormData($_POST);
	}
	function getUser()
	{
		//print_r($_GET);
		//die(print_r(apache_request_headers()));
		if(isset(apache_request_headers()['Authorization']))
		{
			$token = explode('Bearer ',apache_request_headers()['Authorization'])[1];
			//echo $token;
			$data = (array)json_decode(JwtTokens::read($token));
			if(!count($data))
			{
				die(printError("Invalid Session Expired Kindly Login again ",null,401));
			}
			$user = $data['user'];
			$expiry = $data['expiry'];
			if(time() > ($expiry))
			{
				die(printError("Session Expired Kindly Login again ",null,401));
			}
			return $user;
			// $client = Client::fromQuery("SELECT * FROM clients WHERE `guid` LIKE '%".$c_id."%'");
			// if(count($client))
			// {
				// return $user->id;
			// }
			// else
			// {
				// die(printError("Un authorized",null,401));
				// //return false;
			// }
		}
		else
		{
			die(printError("Un authorized",null,401));
			//return false;
		}
	}
	function getClient()
	{
		return getUser();
	}
	function isAdmin()
	{
		$usr = User::id(getUser()->id);
		$type = $usr->type;
		if($type == User::$ADMIN || $type == User::$SYSTEM_CLIENT)
		{
			return true;
		}
		else
		{
			die(printError("Un authorized",null,401));
		}
	}
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	set_error_handler('Errors::errorHandler');
	set_exception_handler('Errors::exceptionHandler');
	//echo "hello  is8uauishaihasghjk ";
	//Mailer::SendMail("njengaelijah456@gmail.com","Njenga Elijah","Testing ... ","Message ....");
	//$timezone = "Africa/Nairobi";
	//date_default_timezone_set($timezone);
	$app = new App;  
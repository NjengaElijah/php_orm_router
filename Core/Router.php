<?php
class Router
{
  public static $controller = "Api";
  public static $mControllerName = "Api";
  public static $action = "index";
  public static $params = [];


  protected static $adminRoutes = [];


  public static function GetControllerName()
  {
    if (is_object(self::$controller)) {
      $className = strtolower(get_class(self::$controller));
      return ucfirst(str_replace('controller', '', $className));
    } else {
      return self::$controller;
    }
  }
  public static function __autoload($class_name)
  {

    $pathModels = MODELS_DIR . $class_name . '.php';

    if (file_exists($pathModels)) {
      require_once($pathModels);
    }
  }

  public static function Redirect($controller = "Api", $action = "index", $args = [])
  {
    $controller = str_replace('Controller', '', $controller);
    $url =  $controller . '/' . $action . '/' . implode('/', $args);
    // echo $page;
    // echo "<hr>";
    // echo SERVER_ROOT;
    // self::Dispatch($url);
    self::$controller = $controller;
    self::$action = $action;

    redirectPage($url);
  }
  public static function ErrorPage($error)
  {
    header("HTTP/1.1 $error ");
    //View::render($error . ".php", ['title' => 'Error - ' . $error], []);
    echo "Unauthorized Access";
  } 
  public static function ErrorPage2($error,$message)
  {
    header("HTTP/1.1 $error ");
    //View::render($error . ".php", ['title' => 'Error - ' . $error], []);
    die(printJson(array('code' => $error, 'message' => $message)));
  }
  public static function Dispatch($url)
  {
	
    $data = self::parseUrl($url);
	if(strtolower($data[0]) == 'v2'){ 
		$controllerName = ucwords($data[1]);
		self::$controller = $controllerName . 'Controller';
		self::$action = $data[2];
		self::$params = $data[3];
	}else{
		
		$controllerName = ucwords($data[0]);
		self::$controller = $controllerName . 'Controller';
		
		self::$action = $data[1];
		self::$params = $data[2];
	}
	
    Files::RequireController(self::$controller);
	
	//echo self::$controller;
    self::$mControllerName = $controllerName;
    self::$controller = new self::$controller;
	$method = $_SERVER['REQUEST_METHOD']; 
    //
    // //if the action is not a method in the controller lets push it into the params array as a parameter
    if (
		!(method_exists(self::$controller, $method.self::$action) || 
		method_exists(self::$controller, self::$action))) {
		
      array_push(self::$params, self::$action);
      //reset the action to index
      self::$action = "index";
    }
	
	//echo $mControllerName;
	
	//var_dump(self::$controller);
	//die($method.self::$action);
	if(method_exists(self::$controller,$method.self::$action))
	{
		self::$controller->{$method.self::$action}(self::$params);
	}	
	else
	{
		self::$controller->{self::$action}(self::$params);
	}	
    
  }
  /**
   * @param a url
   * @return array 0 = controller , 1 = action , 2 = params
   */
  public static function parseUrl($url)
  {
    $controller = "api";
    $action = "index";
    $params = null;
	 
    $nUrl = filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL);
    $vars = explode('/', $nUrl);
    if ($url != '') {

      if (!isset($vars[0])) {
        $controller = "api";
      } else {
        $controller = $vars[0];
        unset($vars[0]);
      }


      if (!isset($vars[1])) {
        $action = "index";
      } else {
        $action = $vars[1];
        unset($vars[1]);
      }
    }
	
    $params = $vars ? array_values($vars) : [];
	
	for($x = 0;$x < count($params) ;$x++)
	{
		
		if(substr($params[$x],0,1) == '?')
		{
			unset($params[$x]);
		}
	}
	
	
    return array($controller, $action, $params);
  }
  public static function getUrl()
  {
    $url = $_SERVER['REQUEST_URI'];
    //$url = $_GET['url'];
    $url = explode('/', $url);
    if ($url[0] == '') {
      unset($url[0]);
    }
    
    $url = array_values($url);
    $url = implode('/', $url);
    //echo $url;
    return $url;
  }
  /**
   * @param controller@action or controller/action
   * @return boolean match
   */
  public static function Match($route)
  {
    $route = str_replace('@', '/', $route);
    $route =  self::parseUrl($route);

    $url = self::parseUrl(self::getUrl());
    /*if (strtolower($route[0]) == 'bida') {
      unset($route[0]);
      $route = array_values($route);
    }
    //only for urls with another path before management and domain e.g bida/management
    $url = self::parseUrl(self::getUrl())[2];
    /*if (count($url) == 0) {
      return false;
    }*/
    //return ($route[0] == $url[0]) && (isset($url[1]) && isset($route[1])) ? ($route[1] == $url[1]) : true;
    if (count($route) == 1 && count($url) == 2) {
      //echo "2";
      return (strtolower($route[0]) == strtolower($url[0])) && (strtolower($route[1]) == strtolower($url[1]));
    } else {
      //echo $route[0].' '.$url[0];
     return (strtolower($route[0]) == strtolower($url[0]));
    }
  }
  public static function MatchController($controller)
  {
    $url = self::parseUrl(self::getUrl());
	$m_controller = strtolower($url[1]);
    return ($m_controller == strtolower($controller));
  }
  public static function MatchControllerR($controller)
  {
    $url = self::parseUrl(self::getUrl());
	
    $flag = (strtolower($controller) == strtolower($url[0]));
    if ($flag) {
      return ' active';
    }
    return '';
  }
  public static function Get($controllerAction)
  {
    $url = str_replace('@', '/', $controllerAction);
    return SERVER_ROOT . $url;
  }
  public static function ControllerAction($controller, $action = 'Index', $args = [])
  {
    return SERVER_ROOT .'/'. $controller . '/' . $action . '/' . implode('/', $args);
  }
  public static function GetPath()
  {
	  return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  }
}

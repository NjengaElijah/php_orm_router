<?php


class App
{
  public $nonLoginControllers = ["api"];
  public function __construct()
  {
	 
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('/',$url);
	//print_r($url);
    if($url[0] == ''){
		unset($url[0]); 
    }
	//if(strtolower($url[1]) != 'order_images')
		if(strtolower($url[1]) == 'v2'){
			unset($url[1]);
		}
		$url = array_values($url);
		$boolNoLoginRequired = false;
		

		if(isset($url[0])){ 
		  $controller = $url[0];
		  /*if(strtolower($controller) == 'collaborations')
		  {
			  if(isset($_SERVER['HTTP_ORIGIN']))
			  {
				$url = $_SERVER['HTTP_ORIGIN'];
				$host = parse_url($url)['host'];
				$host = str_replace('www.','',$host);
				
				if($host != 'kollabo.co.ke')
				{
					Router::ErrorPage2(401,"Unauthorized domain or no domain");
				}
			  }
			  else{
				  Router::ErrorPage2(403,"Sorry you are forbidden from accessing this resource");
			  }
		  }	  */
		 
		}    
		
		$url = implode('/',$url);   
		
		Router::Dispatch($url);
  
   
  }
}

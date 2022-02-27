<?php

function generateGuid()
{
	return uniqid().'-'.uniqid();
}

/*
    * This methods receives an object or associative array and clean it
    */

function validateFormData($formData)
{
  if (is_array($formData)) {
    $cleanData = array();
    foreach ($formData as $key => $value) {
      if (is_array($value)) {
        $cleanData[$key] = cleanElement($value);
      } else {
        $cleanData[$key] = trim(stripslashes(htmlspecialchars($value)));
      }
    }
    return $cleanData;
  } else {

    return trim(stripslashes(htmlspecialchars($formData)));
  }
}

/*
      * This methods receives an associative array and clean it loops for inner
      */

function cleanElement($element)
{
  $cleanData = array();
  foreach ($element as $key => $value) {
    if (is_array($value)) {
      $cleanData[$key]  = cleanElement($value);
    } else {
      $cleanData[$key] = trim(stripslashes(htmlspecialchars($value)));
    }
  }
  return $cleanData;
}

/*
       *  Returns the current url if no args or creates an appropriate url with args as from root point
       */
function url($ref = null)
{
  //check if server has https otherwise give an http url
  if (isset($_SERVER['HTTPS'])) {
    $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
  } else {
    $protocol = 'http';
  }
  if ($ref == '/') {
    $ref = '/';
  }
  //check if the function has args if so
  else if (func_num_args() != 0 && $ref != 'root') {
    $ref = '/' . $ref . '/';
  } else  if ($ref == null) {
    $ref = '/';
    $ref =  $_SERVER['REQUEST_URI'];
  } else if ($ref == 'root') {
    $ref = '/';
  }
  //return a well formatted url
  if ($_SERVER['SERVER_NAME'] == 'localhost') {
    //return $protocol . '://' . SERVER_ROOT .$ref ;
  } else {
    // / return $protocol . '://' . $_SERVER['SERVER_NAME'] .$ref ;
  }
  return $protocol . '://' . SERVER_ROOT . $ref;
}
/**
 * URL from routes
 * @param definedroute
 */
function route($route)
{
  //return url(ROUTES[$route]);
  //return url(ROUTES[$route]);
}
/**
 *
 * Route with args
 * @param definedroute
 * @param getParamArgs
 */
/**
 * URL for assets
 */
function assets_route($asset)
{
  return 'http://' . SERVER_ROOT . '/app/assets/' . $asset . '/';
}
/*
       *  Pass a link argument and the server redirect to it
       */
function app_route($page)
{
  return 'http://' . SERVER_ROOT . "/app//" . $page;
}

function getServerRoot()
{
  return 'http://' . SERVER_ROOT;
}

function redirectPage($page = '')
{
  if ($page == '') {
    $page = $_SERVER['REQUEST_URI'];
  }
  header("Location:" . SERVER_ROOT . "$page");
  exit;
}

/*
       *  the function returns to us the current subtitle or an approptiote title
       *  for the page/resource the user is currently viewing
       */

function getTitle()
{
  //return APP_PREFIX . ' - ' . getPageName();;
}
/*
       * returns the last part of the current route
       */
function getPageName()
{
  $uriArr = getStrippedURL();
  $title = "";
  $stitle = end($uriArr);
  if ($stitle == '') {
    $stitle = 'Home';
  }
  $title = strtoupper($title);
  //Converts the first character of each word in a string to uppercase
  $stitle = ucwords($stitle);
  return $title .= $stitle;
}

function getStrippedURL()
{
  $current_uri = $_SERVER['REQUEST_URI'];
  //slit url to chunks using / as split char
  $uriArr = explode('/', $current_uri);
  //remove last index of array since it is blank
  array_pop($uriArr);
  return $uriArr;
}

function isTab($route)
{
  // //echo $route;
  // $current_uri =  'http://' . SERVER_BASE . $_SERVER['REQUEST_URI'];
  // //slit url to chunks using / as split char
  // $uriArr = explode('/', $current_uri);
  // //remove last index of array since it is blank
  // array_pop($uriArr);

  // $reqUri =  implode('/', $uriArr);
  // // echo $route;
  // // echo $reqUri;
  // // echo route($route);
  // // echo "<br>";
  // //  // echo $reqUri;
  // // echo $reqUri;
  // if (route($route) == $reqUri . '/') {

  //   echo "style='font-size:19px;color:#ffffff;'";
  // }
  // //  echo "style='font-size:17px;'";

}

function getAppName()
{
  return APP_NAME;
}

function showError($title, $message)
{
  echo "
          <div class='alert alert-error' role='alert'>
            <strong>$title</strong>$message
          </div>";
}/*
      function showError($message){
        echo "
          <div class='alert alert-success' role='alert'>
            $message
          </div>
          ";
      }*/

function printJson($arr)
{
  @header("content-type:json");
  echo json_encode($arr);
}


function printSuccess2($msg, $data, $code = 200)
{
  http_response_code($code);
  header('Content-Type: application/json');
  return printJson(array(
    "Type" => true,
    "Message" => $msg,
    "Data" => $data
  ));
}
function printError2($msg, $data = null, $code = 400,$dev_error = null)
{
	if($dev_error)
	{
		Mailer::SendExceptionEmail($msg ,json_encode($dev_error));
	}
  http_response_code($code);
  @header('Content-Type: application/json');
  return printJson(array(
    "Type" => false,
    "Message" => $msg,
    "Data" => $data
  ));
}
function printError($msg, $data, $code = 400)
{
  http_response_code($code);
  header('Content-Type: application/json');
  return printJson(array(
    'IsSuccess' => false,
    "Type" => "ERROR",
    "Message" => $msg,
    "Data" => $data
  ));
}
function printSuccess($msg, $data)
{
  http_response_code(200);
  header('Content-Type: application/json');
  return printJson(array(
    "Type" => "SUCCESS",
    "IsSuccess" => true,
    "Message" => $msg,
    "Data" => $data
  ));
}




function printObject($obj)
{
  echo var_export($obj);
}
function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function include_layout($layout)
{
  //include_once DOCUMENT_ROOT . '/app/layouts/' . $layout . '.php';
}
function getLayout($layout)
{
  //return DOCUMENT_ROOT . '/app/layouts/' . $layout . '.php';
}
function include_image($name)
{
  return url('app/assets/icons/') . $name;
}
function passwords_match($raw_password, $hash_password)
{
  #return password_verify($raw_password, $hash_password);
  return hash("sha256",$raw_password)  == $hash_password;
}
function createHashPassword($raw_password)
{
  #return password_hash($raw_password, PASSWORD_DEFAULT);
  return hash("sha256",$raw_password);
}
function toMySqlDateTime($timeStamp)
{
  return date("Y-m-d H:i:s", $timeStamp);
}
function getPriceByPackage($package)
{
  switch ($package) {
    case 1:
      return 60;
      break;
    case 2:
      return 80;
      break;
    case 3:
      return 100;
      break;
  }
}
function friendlyTime($hr)
{
  if ($hr > 12) {
    return ($hr - 12) . ' PM';
  }
  if ($hr == 0) {
    $hr = 12;
  }
  return $hr . ' AM';
}
function activateTab($page)
{
  $current_uri = $_SERVER['REQUEST_URI'];
  if (strpos($current_uri, $page) > 0) {
    return 'active';
  }
  // if ($current_uri == SERVER_BASER) {
  //   return 'active';
  // }
  return '';
}
function alert($title = '', $ms, $type = 'dark')
{

  echo "<div class='alert alert-{$type} alert-bordered'>
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>&times;</span>
      <span class='sr-only'>Close</span>
    </button>
    <strong class='text-semibold'>{$title}</strong> {$ms}.
  </div>";
}
function alertError($title = 'error', $ms)
{
  alert($title, $ms, 'danger');
}

function alertSuccess($title = 'success', $ms)
{
  alert($title, $ms, 'success');
}

function success($stat)
{
  return '<div class="m-auto text-center">
    <svg version="1.0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 700.000000 700.000000" preserveAspectRatio="xMidYMid meet" width="150" height="150" class="stat-img">
        <g transform="translate(0.000000,700.000000) scale(0.100000,-0.100000)" fill="#3ab54a" stroke="none">
        <path d="M3090 6440 c-509 -75 -956 -254 -1351 -543 -318 -232 -609 -547 -800
                -868 -362 -607 -496 -1284 -393 -1989 70 -477 278 -958 589 -1360 111 -144
                397 -430 533 -534 413 -313 863 -505 1385 -588 124 -20 178 -23 432 -22 254 0
                307 3 430 23 516 84 948 268 1365 579 125 94 421 381 512 497 346 442 546 904
                629 1450 33 216 33 602 0 820 -54 360 -157 677 -319 983 -77 146 -103 177
                -159 188 -41 8 -99 -19 -123 -58 -31 -48 -22 -98 34 -194 151 -260 272 -623
                322 -969 25 -167 25 -554 1 -715 -37 -249 -95 -464 -180 -675 -36 -88 -165
                -334 -224 -427 -161 -254 -413 -526 -649 -704 -383 -289 -795 -461 -1269 -531
                -169 -25 -592 -24 -755 1 -592 91 -1092 337 -1514 746 -450 436 -725 992 -808
                1630 -16 127 -16 499 1 630 64 511 247 956 560 1360 93 120 307 337 431 437
                318 257 717 452 1100 539 374 84 798 87 1180 8 176 -37 430 -123 599 -205 79
                -38 153 -69 165 -69 29 0 81 34 102 65 9 14 17 44 17 66 0 74 -38 104 -243
                195 -274 121 -504 186 -826 233 -190 28 -585 28 -774 1z"></path>
                <path d="M5894 6236 c-79 -25 -558 -361 -745 -523 -31 -26 -98 -84 -150 -128
                -151 -127 -583 -566 -730 -740 -425 -504 -729 -964 -1009 -1525 -98 -196 -190
                -401 -252 -562 -18 -49 -36 -88 -39 -88 -3 0 -53 53 -110 117 -163 185 -272
                295 -434 438 -421 372 -764 600 -1038 689 -112 37 -306 70 -323 55 -8 -6 -14
                -17 -14 -23 0 -13 198 -209 285 -283 33 -28 146 -115 250 -193 105 -79 235
                -182 290 -229 368 -316 683 -690 944 -1118 34 -56 78 -115 96 -132 59 -52 162
                -59 244 -16 53 27 96 88 135 191 21 54 42 108 47 119 4 11 24 58 43 105 125
                312 326 727 466 960 239 400 520 759 874 1118 289 294 561 518 911 753 229
                153 277 183 446 278 109 61 157 95 195 136 128 138 133 347 10 482 -101 112
                -260 160 -392 119z"></path>
                <animate attributeName="fill" from="#fff" to="#3ab54a" dur="3s"/>
            </g>
        </svg>
        <div class="stat-text">
        <span style="display:block;font-size:17px; font-weight:700; margin-bottom:10px;">Congrats! </span>
        <span>
            ' . $stat . '<br>
            <br>
        </span>
        </div>
        </div>';
}


function getIcon($icon)
{
  return "<i class='fa fa-{$icon}'></i>";
}
function sendResponse($type, $msg, $data)
{
  die(json_encode(['Type' => $type, 'Message' => $msg, 'Data' => $data]));
}
function sendSuccessResponse($msg, $data = null)
{
  sendResponse('SUCCESS', $msg, $data);
}
function sendWarningResponse($msg, $data = null)
{
  sendResponse('warning', $msg, $data);
}
function sendErrorResponse($msg, $data = null)
{
  sendResponse('ERROR', $msg, $data);
}
function check_in_range($start_date, $end_date, $date_from_user)
{
  // Convert to timestamp
  $start_ts = strtotime($start_date);
  $end_ts = strtotime($end_date);
  $user_ts = strtotime($date_from_user);

  // Check that user date is between start & end
  return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}
function format_total($total,$decimals = 2)
{
  return number_format($total, $decimals, '.', ',');
}
function getName($name)
{
  return ucfirst(str_replace('_', ' ', str_replace('-', ' ', $name)));
}
function getUrlFromName($name)
{
  return strtolower(str_replace(' ', '_', str_replace('-', '_', $name)));
}
function addSchema($url , $schema = "https://")
{
	if( is_null(parse_url($url,PHP_URL_SCHEME)))
	{
		$url = $schema . $url;
	}
	return $url;
}
/*
function sendAdminOrderEmail($bookingId)
{
  $admin_email = "elijah@kensoko.com";
  $booking = Booking::id($bookingId);
  $customer = Customer::id($booking->customer_id);
  $cName = $customer->getNames();
  $cEmail = $customer->email;
  $cPhone = $customer->phone;
  $days = $booking->days;
  $ppn = $booking->price_per_night;
  $total = format_total($booking->total);
  $dtI = date('d M Y', strtotime($booking->dateIn));
  $dtO = date('d M Y', strtotime($booking->dateOut));
  //get order total
  $subject = "New Booking Placed ";

  $from = "BIDA Bookings <>";
  $headers  = "From: $admin_email\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $message = "<div style='border: 1px solid #5e9ca0; border-radius: 10px; padding: 20px;'>
  <h2 style='color: #5e9ca0;'><span style='color: #000000;'><strong>Hello Bida a new order has been placed</strong>,</span></h2>
  <p>A new booking has been placed for one bedroom by <strong>{$cName}, phone:&nbsp; {$cPhone} , email: {$cEmail}&nbsp;</strong>
  a new customer.</p>
  <p>{$cName} has requested a room to use from <strong>{$dtI} to {$dtO}</strong>.</p>
  <p>Total Number of days is <strong>{$days}</strong>.</p>
  <p>Price per night is <strong>{$ppn}</strong>.</p>
  <p>Total <strong>{$total}</strong></p>
  <p>Click <a title='New Booking ' href='https://www.google.com' target='_blank' rel='noopener'><strong>here</strong> </a>to view the booking details</p>
  <p><strong>Regards Management.</strong></p>
  </div>";

  //echo $message;
  //
  $sql4 = @mail($admin_email, $subject, $message, $headers . "X-Mailer: PHP/" . phpversion());
  //sendMail($message, $admin_email);
  //echo $sql4;
  //$status = $sql4 ? 1 : 0;
}*/

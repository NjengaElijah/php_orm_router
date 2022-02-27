<?php


/**
 * Error and exception handler
 *
 * PHP version 7.0
 */
class Errors
{

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level  Error level
     * @param string $message  Error message
     * @param string $file  Filename the error was raised in
     * @param int $line  Line number in the file
     *
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line)
    {
    	$ms = $message."<br>".$file."<br> on line : ".$line;
		Mailer::SendExceptionEmail("Error In File: ". ($file) ,$ms);
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler.
     *
     * @param Exception $exception  The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception)
    { 
        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);
		$error_code = uniqid().uniqid();
		$error_code = strtoupper(substr($error_code,-10)); 
		$log = dirname(__DIR__) . '/logs/'.$error_code. date('Y-m-d') . '.txt';
		ini_set('error_log', $log);
		
        if (SHOW_ERRORS) {
			$ms = "";
            $ms .=  "<div style='border:1px double black;margin:10px;padding:10px'>";
            $ms .=  "<h1 style='color:red' >Fatal error</h1><hr>";
            $ms .=  "<p>Exception: '" . get_class($exception) . "'</p>";
            $ms .=  "<p style='background-color:#f98889;padding:10px;'>Message: '" . $exception->getMessage() . "'</p>";
            $ms .=  "<p><pre style='background-color:#eee;padding:10px' >" . $exception->getTraceAsString() . "</pre></p><hr>";
            $ms .=  "<p style='background-color:#ffcf59;padding:10px;' >Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
            $ms .=  '<br><hr><pre>'.json_encode($_SERVER).'</pre></div>';
			
			Mailer::SendExceptionEmail($error_code.":Exception: ". get_class($exception) ,$ms);

        } 
            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
		printError2(
		"A technical error has occurred , Kindly contact support or try again later with error code : ".$error_code,
		['msg' => $message],500,null);

            error_log($message);
			Mailer::SendExceptionEmail($error_code.":Exception: ". get_class($exception) ,$message);
            //View::renderTemplate("$code.html");
        
    }
}

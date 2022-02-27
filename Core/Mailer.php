<?php
require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class Mailer  
{
    public static function SendMail($destination_email,$destination_name,$subject,$message)
    {

  
		try {
			$smtp_config = getSmtpCreds();  
			if(is_array($smtp_config))
			{
			
				$site = getSite();
				// Instantiation and passing `true` enables exceptions
				$mail = new PHPMailer(false);
				//Server settings
				$mail->SMTPDebug = 0;                       // Enable verbose debug output
				$mail->isSMTP();                                            // Send using SMTP
				$mail->SMTPOptions = array(
					'ssl' => array( 
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
				);
				$mail->Host       = $smtp_config['smtp_server'];                    // Set the SMTP server to send through
				$mail->SMTPAuth   = true;                                  // Enable SMTP authentication
				$mail->Username   = $smtp_config['smtp_user'];            // SMTP username
				$mail->Password   = $smtp_config['smtp_password'];                          // SMTP password
				//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
				$mail->Port       = 587;                                   // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

				//Recipients
				$mail->setFrom($site->email,$site->name);
				$mail->addAddress($destination_email,$destination_name);


				// Add attachments

				// Content
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = $subject;

				$containerStart = '<html><head></head><body style="width:600px !important"><div style="padding:10px; line-height:22px; -moz-border-radius: 5px;-webkit-border-radius: 5px;	border-radius: 5px; color:#003366;
								background:#e6efee; border:1px solid #c4de95; font-family: Corbel; font-size:14px;">';

				$containerClose =
				   '<br/><br/>
					</div>
					</body>
					</html>';

				$mail->Body    = $containerStart.'Hello '.$destination_name."<br>".$message.''.$containerClose;
				$mail->send();
				echo 'Message has been sent';
			}
		} catch (Exception $e)
		{
			throw $e;
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}
    public static function SendExceptionEmail($subject,$message)
    {
		$siteName = "dev@v2.api.classwriters.com";
		$url	  = "v2.api.classwriters.com";
		$to_admin = "dev@v2.api.classwriters.com";
		$from     = "Class Writers Api Error <dev@v2.api.classwriters.com>";
		$headers  = "From: $from\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$message = "<div style='border: 1px solid black; border-radius: 5px; background-color: #d7e3d1;'>
		<pre>&nbsp;From Class Writers Api</pre>
		<hr/>
		<pre>$message</pre>
		<hr/></div>";


		//echo $message;
		$sql4 = mail("developer.njenga@gmail.com", $subject, $message , $headers."X-Mailer: PHP/" . phpversion());
		return $sql4 ? 1 : 0;
		//self::SendAdminEmail($subject,$message,"elijah@kensoko.com");
	}
	public static function DispatchMail($site_id,$destination_email,$destination_name,$subject,$message,$has_attatchments = 0,$files = [])
    {
		
		$site = Site::id($site_id);
		$smtp_config = $site->getSmtpCreds();
		var_dump($smtp_config);
		
		if(is_array($smtp_config))
		{
			// Instantiation and passing `true` enables exceptions
			$mail = new PHPMailer(false);
			//$smtp_config['smtp_password'] = 'MsP3rTSTbh'; 
			try {
				//Server settings
				$mail->SMTPDebug = 1;                       // Enable verbose debug output
				$mail->isSMTP();                                            // Send using SMTP
				$mail->SMTPOptions = array(
					'ssl' => array( 
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
				);
				$mail->Host       = $smtp_config['smtp_server'];                    // Set the SMTP server to send through
				$mail->SMTPAuth   = true;                                  // Enable SMTP authentication
				$mail->Username   = $smtp_config['smtp_user'];            // SMTP username
				$mail->Password   = $smtp_config['smtp_password'];                          // SMTP password
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
				$mail->Port       = 587;                                   // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

				//Recipients
				$mail->setFrom($site->email,$site->name);
				$mail->addAddress($destination_email,$destination_name);


				// Add attachments
				$filesRepo = new FilesRepository;
				if($has_attatchments)
				{
					if(is_array($files))
					{
						foreach($files as $file)
						{
							try{
								
							$content = $filesRepo->GetContent($site,$file->name,$file->mime_type);
							//cleaning the file_name
							$file_name = $file->name;
							// $pp = explode('.',$file_name);
							// $d = (explode('_',$pp[0]));
							// unset($d[count($d) - 1]);
							// $file_name = implode('-',$d).'.'.$pp[1];
							//adding atattchemnt
							$mail->addStringAttachment($content,$file_name);
							}
							catch(Exception $e)
							{
									continue;
							}
						}
						//continue; 
					}
				}
				// Content
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = $subject;

				$containerStart = '<html><head></head><body style="width:600px !important"><div style="padding:10px; line-height:22px; -moz-border-radius: 5px;-webkit-border-radius: 5px;	border-radius: 5px; color:#003366;
								background:#e6efee; border:1px solid #c4de95; font-family: Corbel; font-size:14px;">';

				$containerClose =
				   '<br/><br/>
					</div>
					</body>
					</html>';

				$mail->Body    = $containerStart."Hello , ".$destination_name."<br>".$message.$containerClose;
				$mail->send();
				echo 'Message has been sent'; 
			} catch (Exception $e)
			{
				//throw $e;
				echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}
		}else{
			echo "not sent";
		}
	}
	public static function sendAttatchment($content,$file_name)
	{
		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);
		//echo "Here"; 
		try {
			//Server settings
			$mail->SMTPDebug = 1;                      // Enable verbose debug output
			$mail->isSMTP();                                            // Send using SMTP
			$mail->SMTPOptions = array(
				'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
			);
			$mail->Host       = 'urgentwriters.com';                    // Set the SMTP server to send through
			$mail->SMTPAuth   = 1;                                  // Enable SMTP authentication
			$mail->Username   = 'support@urgentwriters.com';            // SMTP username
			$mail->Password   = 'PHUvzdE8c8';                          // SMTP password
			//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = 587;                                   // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

			//Recipients 
			$mail->setFrom('support@urgentwriters.com', "File - Test - Support");
			//$mail->addAddress($client['Email'], ucwords($client['Full_name']));      // Add a recipient\
			//echo "Email : ".$client['Email'];
			$mail->addAddress('njengaelijah456@gmail.com', 'NjengaDev');  
			//print_r($orderFiles);
			
			$mail->addStringAttachment($content,$file_name);         // Add attachments
			
			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Your files are ready';
			
			$containerStart = '<html><head></head><body style="width:600px !important"><div style="padding:10px; line-height:22px; -moz-border-radius: 5px;-webkit-border-radius: 5px;	border-radius: 5px; color:#003366;  
							background:#e6efee; border:1px solid #c4de95; font-family: verdana; font-size:14px;">';
			
			$containerClose = '<br/><br/>
				<span style="color:#253350; font-weight:bold; font-size:15px;">
				Regards,<br>
				The Support Department, <br/>classwriters.com<br/>
				<strong><i></i></strong><br>
				</span>				
				
				</div>
				</body>
				</html>
				';
			$mail->Body    = $containerStart.'New File New File '.$containerClose;
			//echo $containerStart.'Hello '.explode(' ',ucwords($client['Full_name']))[0].' , <br> Your order has been completed.Below are the order files attatched.'.$containerClose;;
			echo $mail->send();
			//echo 'Message has been sent';
		} catch (Exception $e) 
		{
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}

}

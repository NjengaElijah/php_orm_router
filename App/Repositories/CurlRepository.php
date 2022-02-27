<?php
class CurlRepository
{
	public static function ReturnToSender($url,$data)
	{
		$data = [ 'data' => base64_encode($data) ];
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
}

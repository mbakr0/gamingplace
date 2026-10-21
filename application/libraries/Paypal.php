<?php
/**
 * 
 */
class Paypal
{
	
		public function PPHttpPost($methodName_, $nvpStr_,$PayPalApiUsername,$PayPalApiPassword,$PayPalApiSignature,$paypalmode) {
			      $API_UserName = urlencode($PayPalApiUsername);
			      $API_Password = urlencode($PayPalApiPassword);
			      $API_Signature = urlencode($PayPalApiSignature);

			      $paypalmode = ($paypalmode=='sandbox') ? '.sandbox':'';

			      $API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
			      $version = urlencode('109.0');
			     
			      // Set the curl parameters.
			      $ch = curl_init();
			      curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
			      curl_setopt($ch, CURLOPT_VERBOSE, 0);
			     
			      // Verify PayPal's TLS certificate to prevent man-in-the-middle attacks.
			      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			     
			      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			      curl_setopt($ch, CURLOPT_POST, 1);
			     
			      // Set the API operation, version, and API signature in the request.
			      $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature&BUTTONSOURCE=".urlencode("PHPMOOT_WPP_DP_US")."$nvpStr_";
			     
			      // Set the request as a POST FIELD for curl.
			      curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
			     
			      // Get response from the server.
			      $httpResponse = curl_exec($ch);
			     
			      if(!$httpResponse) {
			         exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
			      }
			     
			      // Extract the response details.
			      $httpResponseAr = explode("&", $httpResponse);
			     
			      $httpParsedResponseAr = array();
			      foreach ($httpResponseAr as $i => $value) {
			         $tmpAr = explode("=", $value, 2);
			         if(sizeof($tmpAr) > 1) {
			            $httpParsedResponseAr[urldecode($tmpAr[0])] = urldecode($tmpAr[1]);
			         }
			      }
			     
			      if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			         error_log("PayPal API returned an invalid response.");
			         curl_close($ch);
			         return array("ACK" => "Failure", "L_LONGMESSAGE0" => "Payment provider returned an invalid response.");
			      }
			     
			      curl_close($ch);
			      return $httpParsedResponseAr;
			   }

}
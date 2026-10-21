<?php
/**
 * PayPal Express Checkout NVP client.
 */
class Paypal
{
    public function PPHttpPost($methodName_, $nvpStr_, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $paypalmode)
    {
        $API_UserName = urlencode($PayPalApiUsername);
        $API_Password = urlencode($PayPalApiPassword);
        $API_Signature = urlencode($PayPalApiSignature);

        $paypalmode = ($paypalmode === 'sandbox') ? '.sandbox' : '';
        $API_Endpoint = 'https://api-3t' . $paypalmode . '.paypal.com/nvp';
        $version = urlencode('109.0');

        $ch = curl_init();
        if ($ch === FALSE) {
            return array('ACK' => 'Failure', 'L_LONGMESSAGE0' => 'Could not initialise the payment request.');
        }

        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $nvpreq = 'METHOD=' . rawurlencode($methodName_) .
            '&VERSION=' . $version .
            '&PWD=' . $API_Password .
            '&USER=' . $API_UserName .
            '&SIGNATURE=' . $API_Signature .
            '&BUTTONSOURCE=' . rawurlencode('PHPMOOT_WPP_DP_US') . $nvpStr_;

        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
        $httpResponse = curl_exec($ch);

        if ($httpResponse === FALSE) {
            $curlError = curl_error($ch);
            $curlNumber = curl_errno($ch);
            error_log('GamingPlace PayPal request failed: ' . $curlError . ' (' . $curlNumber . ')');
            curl_close($ch);
            return array('ACK' => 'Failure', 'L_LONGMESSAGE0' => 'The payment provider could not be reached.');
        }

        $httpStatus = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpStatus < 200 || $httpStatus >= 300) {
            error_log('GamingPlace PayPal returned HTTP status ' . $httpStatus . '.');
            return array('ACK' => 'Failure', 'L_LONGMESSAGE0' => 'The payment provider returned an unexpected response.');
        }

        $httpResponseAr = explode('&', $httpResponse);
        $httpParsedResponseAr = array();

        foreach ($httpResponseAr as $value) {
            $tmpAr = explode('=', $value, 2);
            if (count($tmpAr) > 1) {
                $httpParsedResponseAr[urldecode($tmpAr[0])] = urldecode($tmpAr[1]);
            }
        }

        if (empty($httpParsedResponseAr) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            error_log('PayPal API returned an invalid response.');
            return array('ACK' => 'Failure', 'L_LONGMESSAGE0' => 'Payment provider returned an invalid response.');
        }

        return $httpParsedResponseAr;
    }
}

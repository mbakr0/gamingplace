<?php

class Cart extends CI_controller
{
    public $paypal_data = '';
    public $tax = 0;
    public $shipping = 0;
    public $total = 0;
    public $grand_total = 0;

    public function index()
    {
        $data['main_content'] = 'cart';
        $this->load->view('layouts/main', $data);
    }

    public function add()
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            redirect('products');
            return;
        }

        $itemId = filter_var($this->input->post('item_number'), FILTER_VALIDATE_INT);
        $qty = filter_var($this->input->post('qty'), FILTER_VALIDATE_INT);

        if (!$itemId || !$qty || $qty < 1 || $qty > 99) {
            $this->session->set_flashdata('cart_error', 'Choose a valid game and quantity before adding it to your bag.');
            redirect('products');
            return;
        }

        $product = $this->Product_model->get_product_details($itemId);
        if (!$product) {
            $this->session->set_flashdata('cart_error', 'That game could not be found. Please refresh the collection and try again.');
            redirect('products');
            return;
        }

        // The product database is the source of truth for price and title.
        $this->cart->insert(array(
            'id' => (int) $itemId,
            'qty' => (int) $qty,
            'price' => (float) $product->price,
            'name' => (string) $product->title
        ));

        redirect('cart');
    }

    public function update()
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            redirect('cart');
            return;
        }

        $items = $this->input->post('qty');
        if (is_array($items)) {
            $validRows = array();
            foreach ($this->cart->contents() as $cartItem) {
                $validRows[$cartItem['rowid']] = TRUE;
            }

            foreach ($items as $rowId => $qty) {
                if (!isset($validRows[$rowId])) {
                    continue;
                }

                $qty = filter_var($qty, FILTER_VALIDATE_INT);
                if ($qty === false) {
                    continue;
                }

                $this->cart->update(array(
                    'rowid' => $rowId,
                    'qty' => max(0, min(99, (int) $qty))
                ));
            }
        }

        redirect('cart');
    }

    public function process()
    {
        if (!$this->session->userdata('logged_in') || !$this->session->userdata('user_id')) {
            $this->session->set_flashdata('fail_login', 'Please sign in before checking out.');
            redirect('products');
            return;
        }

        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            redirect('cart');
            return;
        }

        $cartItems = $this->cart->contents();
        if (empty($cartItems)) {
            $this->session->set_flashdata('cart_error', 'Your shopping bag is empty.');
            redirect('cart');
            return;
        }

        // A previously confirmed payment must be resumed, never charged again.
        $alreadyConfirmed = $this->session->userdata('paypal_payment_confirmed');
        if (is_array($alreadyConfirmed) && !empty($alreadyConfirmed['token']) && !empty($alreadyConfirmed['payer_id']) && !empty($alreadyConfirmed['transaction_id'])) {
            $resumeUrl = base_url('cart/success')
                . '?token=' . rawurlencode((string) $alreadyConfirmed['token'])
                . '&PayerID=' . rawurlencode((string) $alreadyConfirmed['payer_id']);
            redirect($resumeUrl);
            return;
        }

        $address = trim((string) $this->input->post('address', TRUE));
        $address2 = trim((string) $this->input->post('address2', TRUE));
        $city = trim((string) $this->input->post('city', TRUE));
        $state = trim((string) $this->input->post('state', TRUE));
        $zipcode = trim((string) $this->input->post('zipcode', TRUE));
        $country = strtoupper(trim((string) $this->input->post('country', TRUE)));
        $allowedCountries = array('EG', 'SA', 'AE', 'GB', 'US', 'CA');

        if ($address === '' || $city === '' || $state === '' || $zipcode === '' || !in_array($country, $allowedCountries, TRUE)) {
            $this->session->set_flashdata('cart_error', 'Complete the required shipping fields and choose a supported country.');
            redirect('cart');
            return;
        }

        $credentials = array(
            'username' => $this->environmentValue('PAYPAL_API_USERNAME'),
            'password' => $this->environmentValue('PAYPAL_API_PASSWORD'),
            'signature' => $this->environmentValue('PAYPAL_API_SIGNATURE')
        );
        $mode = strtolower($this->environmentValue('PAYPAL_MODE'));
        if ($mode === '') {
            $mode = 'sandbox';
        }

        if ($credentials['username'] === '' || $credentials['password'] === '' || $credentials['signature'] === '') {
            $this->session->set_flashdata(
                'cart_error',
                'PayPal checkout is not configured yet. Add PAYPAL_API_USERNAME, PAYPAL_API_PASSWORD, PAYPAL_API_SIGNATURE, and PAYPAL_MODE to the server environment before accepting payments.'
            );
            redirect('cart');
            return;
        }

        if (!in_array($mode, array('sandbox', 'live'), TRUE)) {
            $this->session->set_flashdata('cart_error', 'PAYPAL_MODE must be set to sandbox or live.');
            redirect('cart');
            return;
        }

        if (!function_exists('curl_init')) {
            $this->session->set_flashdata('cart_error', 'The server is missing PHP cURL, which is required to connect to PayPal.');
            redirect('cart');
            return;
        }

        $checkoutItems = array();
        $this->total = 0.0;

        foreach ($cartItems as $cartItem) {
            $product = $this->Product_model->get_product_details((int) $cartItem['id']);
            $qty = (int) $cartItem['qty'];

            if (!$product || $qty < 1 || $qty > 99 || !is_numeric($product->price) || (float) $product->price < 0) {
                continue;
            }

            $price = (float) $product->price;
            $lineTotal = round($price * $qty, 2);
            $this->total += $lineTotal;

            $checkoutItems[] = array(
                'product_id' => (int) $product->id,
                'title' => (string) $product->title,
                'qty' => $qty,
                'price' => $price,
                'subtotal' => $lineTotal
            );
        }

        if (empty($checkoutItems)) {
            $this->session->set_flashdata('cart_error', 'None of the items in your bag are available. Please review your selection.');
            redirect('cart');
            return;
        }

        $this->tax = (float) $this->config->item('tax');
        $this->shipping = (float) $this->config->item('shipping');
        $this->grand_total = round($this->total + $this->tax + $this->shipping, 2);

        $paypalParams = array(
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYMENTREQUEST_0_AMT' => number_format($this->grand_total, 2, '.', ''),
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
            'PAYMENTREQUEST_0_ITEMAMT' => number_format($this->total, 2, '.', ''),
            'PAYMENTREQUEST_0_TAXAMT' => number_format($this->tax, 2, '.', ''),
            'PAYMENTREQUEST_0_SHIPPINGAMT' => number_format($this->shipping, 2, '.', ''),
            'PAYMENTREQUEST_0_SHIPTOSTREET' => $address,
            'PAYMENTREQUEST_0_SHIPTOSTREET2' => $address2,
            'PAYMENTREQUEST_0_SHIPTOCITY' => $city,
            'PAYMENTREQUEST_0_SHIPTOSTATE' => $state,
            'PAYMENTREQUEST_0_SHIPTOZIP' => $zipcode,
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $country,
            'RETURNURL' => base_url('cart/success'),
            'CANCELURL' => base_url('cart/cancel'),
            'NOSHIPPING' => '0',
            'ALLOWNOTE' => '0',
            'BRANDNAME' => 'GamingPlace'
        );

        foreach ($checkoutItems as $index => $item) {
            $paypalParams['L_PAYMENTREQUEST_0_NAME' . $index] = $item['title'];
            $paypalParams['L_PAYMENTREQUEST_0_NUMBER' . $index] = (string) $item['product_id'];
            $paypalParams['L_PAYMENTREQUEST_0_AMT' . $index] = number_format($item['price'], 2, '.', '');
            $paypalParams['L_PAYMENTREQUEST_0_QTY' . $index] = (string) $item['qty'];
        }

        $this->load->library('paypal');
        $response = $this->paypal->PPHttpPost(
            'SetExpressCheckout',
            '&' . http_build_query($paypalParams, '', '&', PHP_QUERY_RFC3986),
            $credentials['username'],
            $credentials['password'],
            $credentials['signature'],
            $mode
        );

        if (!$this->paypalResponseSucceeded($response) || empty($response['TOKEN'])) {
            log_message('error', 'GamingPlace PayPal SetExpressCheckout failed: ' . json_encode($response));
            $this->session->set_flashdata('cart_error', 'PayPal could not start this checkout. Please verify the payment credentials and try again.');
            redirect('cart');
            return;
        }

        $checkout = array(
            'token' => (string) $response['TOKEN'],
            'items' => $checkoutItems,
            'subtotal' => round($this->total, 2),
            'tax' => $this->tax,
            'shipping' => $this->shipping,
            'grand_total' => $this->grand_total,
            'address' => $address,
            'address2' => $address2,
            'city' => $city,
            'state' => $state,
            'zipcode' => $zipcode,
            'country' => $country,
            'created_at' => time()
        );
        $this->session->set_userdata('paypal_checkout', $checkout);

        $paypalHost = ($mode === 'sandbox') ? 'https://www.sandbox.paypal.com' : 'https://www.paypal.com';
        redirect($paypalHost . '/cgi-bin/webscr?cmd=_express-checkout&token=' . rawurlencode($checkout['token']));
    }

    public function success()
    {
        if (!$this->session->userdata('logged_in') || !$this->session->userdata('user_id')) {
            $this->session->set_flashdata('fail_login', 'Sign in again to complete your checkout.');
            redirect('products');
            return;
        }

        $checkout = $this->session->userdata('paypal_checkout');
        $token = trim((string) $this->input->get('token', TRUE));
        $payerId = trim((string) $this->input->get('PayerID', TRUE));

        if (!is_array($checkout) || empty($checkout['token']) || $token === '' || !hash_equals((string) $checkout['token'], $token) || $payerId === '') {
            $this->session->set_flashdata('cart_error', 'The payment return could not be verified. Your bag has been kept unchanged.');
            redirect('cart');
            return;
        }

        // If PayPal has already confirmed this token, resume order persistence without charging again.
        $confirmedPayment = $this->session->userdata('paypal_payment_confirmed');
        $reusingConfirmedPayment = is_array($confirmedPayment)
            && isset($confirmedPayment['token'], $confirmedPayment['transaction_id'], $confirmedPayment['payer_id'])
            && hash_equals((string) $confirmedPayment['token'], $token)
            && (string) $confirmedPayment['transaction_id'] !== '';

        if ($reusingConfirmedPayment) {
            $transactionId = (string) $confirmedPayment['transaction_id'];
        } else {
            $credentials = array(
                'username' => $this->environmentValue('PAYPAL_API_USERNAME'),
                'password' => $this->environmentValue('PAYPAL_API_PASSWORD'),
                'signature' => $this->environmentValue('PAYPAL_API_SIGNATURE')
            );
            $mode = strtolower($this->environmentValue('PAYPAL_MODE'));
            if ($mode === '') {
                $mode = 'sandbox';
            }

            if ($credentials['username'] === '' || $credentials['password'] === '' || $credentials['signature'] === '' || !in_array($mode, array('sandbox', 'live'), TRUE) || !function_exists('curl_init')) {
                $this->session->set_flashdata('cart_error', 'The server cannot verify this PayPal return. Please contact the store administrator before trying to pay again.');
                redirect('cart');
                return;
            }

            $this->load->library('paypal');
            $details = $this->paypal->PPHttpPost(
                'GetExpressCheckoutDetails',
                '&' . http_build_query(array('TOKEN' => $token), '', '&', PHP_QUERY_RFC3986),
                $credentials['username'],
                $credentials['password'],
                $credentials['signature'],
                $mode
            );

            if (!$this->paypalResponseSucceeded($details)) {
                log_message('error', 'GamingPlace PayPal GetExpressCheckoutDetails failed: ' . json_encode($details));
                $this->session->set_flashdata('cart_error', 'PayPal could not verify this checkout. Your bag has been kept unchanged.');
                redirect('cart');
                return;
            }

            $paymentParams = array(
                'TOKEN' => $token,
                'PAYERID' => $payerId,
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                'PAYMENTREQUEST_0_AMT' => number_format((float) $checkout['grand_total'], 2, '.', ''),
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD'
            );
            $payment = $this->paypal->PPHttpPost(
                'DoExpressCheckoutPayment',
                '&' . http_build_query($paymentParams, '', '&', PHP_QUERY_RFC3986),
                $credentials['username'],
                $credentials['password'],
                $credentials['signature'],
                $mode
            );

            if (!$this->paypalResponseSucceeded($payment)) {
                log_message('error', 'GamingPlace PayPal DoExpressCheckoutPayment failed: ' . json_encode($payment));
                $this->session->set_flashdata('cart_error', 'PayPal did not confirm the payment. Your bag has been kept unchanged.');
                redirect('cart');
                return;
            }

            $paymentStatus = isset($payment['PAYMENTINFO_0_PAYMENTSTATUS']) ? $payment['PAYMENTINFO_0_PAYMENTSTATUS'] : '';
            $transactionId = isset($payment['PAYMENTINFO_0_TRANSACTIONID']) ? trim((string) $payment['PAYMENTINFO_0_TRANSACTIONID']) : '';

            if (strcasecmp($paymentStatus, 'Completed') !== 0 || $transactionId === '') {
                log_message('error', 'GamingPlace PayPal payment not completed. Response: ' . json_encode($payment));
                $this->session->set_flashdata('cart_error', 'PayPal has not returned a completed payment confirmation. Your bag has been kept unchanged.');
                redirect('cart');
                return;
            }

            // Save payment confirmation before touching the order table. If storage fails,
            // a refresh can retry order storage without calling PayPal a second time.
            $confirmedPayment = array(
                'token' => $token,
                'payer_id' => $payerId,
                'transaction_id' => $transactionId
            );
            $this->session->set_userdata('paypal_payment_confirmed', $confirmedPayment);
        }

        // Make the return route safe to refresh after an order was already recorded.
        $existingOrder = $this->db->where('transaction_id', $transactionId)->limit(1)->get('orders');
        if ($existingOrder === FALSE) {
            log_message('error', 'Could not check existing GamingPlace orders for PayPal transaction ' . $transactionId . '.');
            $this->session->set_flashdata('cart_error', 'PayPal confirmed the payment, but order storage is temporarily unavailable. Do not pay again; refresh this page later or contact the store administrator with reference ' . $transactionId . '.');
            redirect('cart');
            return;
        }

        if ($existingOrder->num_rows() > 0) {
            $this->db->trans_status(); // Ensure the query connection is active before finishing the session.
            $this->session->unset_userdata('paypal_checkout');
            $this->session->unset_userdata('paypal_payment_confirmed');
            $this->cart->destroy();
            $this->session->set_flashdata('checkout_success', 'Payment confirmed. Your order reference is ' . $transactionId . '.');
            redirect('cart');
            return;
        }

        $this->db->trans_begin();
        $ordersSaved = TRUE;

        foreach ($checkout['items'] as $item) {
            $orderData = array(
                'product_id' => (int) $item['product_id'],
                'user_id' => (int) $this->session->userdata('user_id'),
                'transaction_id' => $transactionId,
                'qty' => (int) $item['qty'],
                'price' => (float) $item['subtotal'],
                'address' => (string) $checkout['address'],
                'address2' => (string) $checkout['address2'],
                'city' => (string) $checkout['city'],
                'state' => (string) $checkout['state'],
                'zipcode' => (string) $checkout['zipcode']
            );

            if (!$this->Product_model->add_order($orderData)) {
                $ordersSaved = FALSE;
                break;
            }
        }

        if (!$ordersSaved || $this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            log_message('error', 'PayPal transaction ' . $transactionId . ' completed but GamingPlace could not save the order rows.');
            $this->session->set_userdata('paypal_payment_confirmed', array(
                'token' => $token,
                'payer_id' => $payerId,
                'transaction_id' => $transactionId
            ));
            $this->session->set_flashdata('cart_error', 'The payment was confirmed, but the order record could not be saved automatically. Do not pay again. Contact the store administrator with reference ' . $transactionId . '.');
            redirect('cart');
            return;
        }

        $this->db->trans_commit();
        $this->session->unset_userdata('paypal_checkout');
        $this->session->unset_userdata('paypal_payment_confirmed');
        $this->cart->destroy();
        $this->session->set_flashdata('checkout_success', 'Payment confirmed. Your order reference is ' . $transactionId . '.');
        redirect('cart');
    }

    public function cancel()
    {
        $confirmedPayment = $this->session->userdata('paypal_payment_confirmed');
        if (is_array($confirmedPayment) && !empty($confirmedPayment['token']) && !empty($confirmedPayment['payer_id'])) {
            $resumeUrl = base_url('cart/success')
                . '?token=' . rawurlencode((string) $confirmedPayment['token'])
                . '&PayerID=' . rawurlencode((string) $confirmedPayment['payer_id']);
            redirect($resumeUrl);
            return;
        }

        $this->session->unset_userdata('paypal_checkout');
        $this->session->set_flashdata('checkout_info', 'Checkout was cancelled. Nothing was charged by GamingPlace, and your bag is still here.');
        redirect('cart');
    }

    private function environmentValue($name)
    {
        $value = getenv($name);
        if ($value === FALSE || $value === '') {
            $value = isset($_SERVER[$name]) ? $_SERVER[$name] : (isset($_ENV[$name]) ? $_ENV[$name] : '');
        }

        return trim((string) $value);
    }

    private function paypalResponseSucceeded($response)
    {
        if (!is_array($response) || !isset($response['ACK'])) {
            return FALSE;
        }

        return in_array(strtolower((string) $response['ACK']), array('success', 'successwithwarning'), TRUE);
    }
}

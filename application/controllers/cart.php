<?php

class Cart extends CI_controller
{
    public $paypal_data = '';
    public $tax = 2.99;
    public $shipping = 6.99;
    public $total = 0;
    public $grand_total = 0;

    public function index()
    {
        $data['main_content'] = 'cart';
        $this->load->view('layouts/main', $data);
    }

    public function add()
    {
        $itemId = filter_var($this->input->post('item_number'), FILTER_VALIDATE_INT);
        $qty = filter_var($this->input->post('qty'), FILTER_VALIDATE_INT);

        if (!$itemId || !$qty || $qty < 1 || $qty > 99) {
            $this->session->set_flashdata('cart_error', 'Invalid product or quantity.');
            redirect('products');
            return;
        }

        $product = $this->Product_model->get_product_details($itemId);
        if (!$product) {
            $this->session->set_flashdata('cart_error', 'Product not found.');
            redirect('products');
            return;
        }

        // Never trust a price submitted by the browser.
        $this->cart->insert([
            'id' => $itemId,
            'qty' => $qty,
            'price' => (float)$product->price,
            'name' => $product->title
        ]);

        redirect('cart');
    }

    public function update()
    {
        $items = $this->input->post('qty');
        if (is_array($items)) {
            foreach ($items as $rowId => $qty) {
                $qty = (int)$qty;
                $this->cart->update([
                    'rowid' => $rowId,
                    'qty' => max(0, min(99, $qty))
                ]);
            }
        }
        redirect('cart');
    }

    public function process()
    {
        if (!$this->input->post()) {
            redirect('cart');
            return;
        }

        $itemIds = (array)$this->input->post('item_code');
        $quantities = (array)$this->input->post('item_qty');
        if (!$itemIds) {
            redirect('cart');
            return;
        }

        $this->tax = (float)$this->config->item('tax');
        $this->shipping = (float)$this->config->item('shipping');
        $paypalProduct = ['items' => []];

        foreach ($itemIds as $key => $itemId) {
            $itemId = (int)$itemId;
            $qty = isset($quantities[$key]) ? (int)$quantities[$key] : 0;
            if (!$itemId || $qty < 1 || $qty > 99) continue;

            $product = $this->Product_model->get_product_details($itemId);
            if (!$product) continue;

            $subtotal = (float)$product->price * $qty;
            $this->total += $subtotal;

            $paypalProduct['items'][] = [
                'itm_name' => $product->title,
                'itm_price' => (float)$product->price,
                'itm_code' => $itemId,
                'itm_qty' => $qty
            ];

            $orderData = [
                'product_id' => $itemId,
                'user_id' => $this->session->userdata('user_id'),
                'transaction_id' => 0,
                'qty' => $qty,
                'price' => $subtotal,
                'address' => trim((string)$this->input->post('address', true)),
                'address2' => trim((string)$this->input->post('address2', true)),
                'city' => trim((string)$this->input->post('city', true)),
                'state' => trim((string)$this->input->post('state', true)),
                'zipcode' => trim((string)$this->input->post('zipcode', true))
            ];
            $this->Product_model->add_order($orderData);
        }

        $this->grand_total = $this->total + $this->tax + $this->shipping;
        $paypalProduct['assets'] = [
            'tax_total' => $this->tax,
            'shipping_cost' => $this->shipping,
            'grand_total' => $this->grand_total
        ];
        $_SESSION['paypal_product'] = $paypalProduct;
    }
}

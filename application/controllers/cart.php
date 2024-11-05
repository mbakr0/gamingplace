<?php

/**
 * 
 */
class Cart extends CI_controller
{
	public $paypal_data = "";
	public $tax = 2.99;
	public $shipping = 6.99; 
	public $total = 0;
	public $grand_total;
	
	public function index()
	{
		$data["main_content"] = "cart";
		$this->load->view("layouts/main",$data);
	}

	public function add(){
		$data = array(
			'id' =>$this->input->post("item_number"),
			'qty' =>$this->input->post("qty"),
			'price' =>$this->input->post("price"),
			'name' =>$this->input->post("title")
			 );
		
		$this->cart->insert($data);
		redirect("products");
	}

	public function update($in_cart = null){

		$data = $_POST;
		$this->cart->update($data);
		redirect('cart','refresh');
	}

	public function process(){

		if ($_POST) {
			foreach ($this->input->post('item_name') as $key => $value) {

				$this->tax = $this->config->item('tax');
				$this->shipping = $this->config->item('shipping');
				
				$item_id = $this->input->post("item_code")[$key];
				$product = $this->Product_model->get_product_details($item_id);

				$this->paypal_data .='&L_PAYMENTREQUEST_O_NAME'.$key.'='.urlencode($product->title);
				$this->paypal_data .='&L_PAYMENTREQUEST_O_NUMBER'.$key.'='.urlencode($item_id);
				$this->paypal_data .='&L_PAYMENTREQUEST_O_AMT'.$key.'='.urlencode($product->price);
				$this->paypal_data .='&L_PAYMENTREQUEST_O_QTY'.$key.'='.urlencode($this->input->post("item_qty")[$key]);


				$subtotal = ($product->price * $this->input->post("item_qty")[$key]);
				$this->total = $this->total + $subtotal;

				$paypal_product['items'][] = array(
					'itm_name' => $product->title,
					'itm_price' => $product->price,
					'itm_code' => $item_id,
					'itm_qty' => $this->input->post("item_qty")[$key]
				);

				$order_data = array(
					'product_id' => $item_id,
					'user_id' => $this->session->userdata('user_id'),
					'transaction_id' => 0,
					'qty' => $this->input->post("item_qty")[$key],
					'price' => $subtotal,
					'address' => $this->input->post("address"),
					'address2' => $this->input->post("address2"),
					'city' => $this->input->post("city"),
					'state' => $this->input->post("state"),
					'zipcode' => $this->input->post("zipcode")
				);

				$this->Product_model->add_order($order_data);

			}
			$this->grand_total = $this->total + $this->tax + $this->shipping;

			$paypal_product['assets'] = array(
				'tax_total' => $this->tax,
				'shipping_cost' => $this->shipping,
				'grand_total' => $this->grand_total
			);

			$_SESSION["paypal_product"] = $paypal_product;

		}
	}
}
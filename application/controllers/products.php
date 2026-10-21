<?php

class Products extends CI_controller
{
    public function index()
    {
        $data['products'] = $this->Product_model->get_products();
        $data['main_content'] = 'products';
        $this->load->view('layouts/main', $data);
    }

    public function details($id = null)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            show_404();
            return;
        }

        $data['product'] = $this->Product_model->get_product_details($id);
        if (!$data['product']) {
            show_404();
            return;
        }

        $data['main_content'] = 'details';
        $this->load->view('layouts/main', $data);
    }
}

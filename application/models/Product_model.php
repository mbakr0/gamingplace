<?php
/**
 * Product model.
 */
class Product_model extends CI_model
{
    public function get_products()
    {
        return $this->db->order_by('title', 'ASC')->get('products')->result();
    }

    public function get_product_details($id)
    {
        return $this->db->where('id', (int)$id)->get('products')->row();
    }

    public function get_categories()
    {
        // Keep the existing database table name for backwards compatibility.
        return $this->db->get('categoies')->result();
    }

    public function get_popular()
    {
        return $this->db
            ->select('p.*, COUNT(o.product_id) AS total')
            ->from('orders AS o')
            ->join('products AS p', 'o.product_id = p.id', 'INNER')
            ->group_by('o.product_id')
            ->order_by('total', 'DESC')
            ->get()->result();
    }

    public function add_order($order_data)
    {
        return $this->db->insert('orders', $order_data);
    }
}

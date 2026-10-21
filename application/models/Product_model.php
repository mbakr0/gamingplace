<?php
/**
 * Product model.
 */
class Product_model extends CI_model
{
    public function get_products()
    {
        $query = $this->db->order_by('title', 'ASC')->get('products');

        // In production, CodeIgniter may return FALSE when the database
        // is unreachable or the products table is missing. Keep the shop
        // page renderable instead of triggering a fatal error.
        return ($query !== FALSE) ? $query->result() : array();
    }

    public function get_product_details($id)
    {
        $query = $this->db->where('id', (int) $id)->get('products');
        return ($query !== FALSE) ? $query->row() : NULL;
    }

    public function get_categories()
    {
        // Keep the existing database table name for backwards compatibility.
        $query = $this->db->get('categoies');
        return ($query !== FALSE) ? $query->result() : array();
    }

    public function get_popular()
    {
        $query = $this->db
            ->select('p.*, COUNT(o.product_id) AS total')
            ->from('orders AS o')
            ->join('products AS p', 'o.product_id = p.id', 'INNER')
            ->group_by('o.product_id')
            ->order_by('total', 'DESC')
            ->get();

        return ($query !== FALSE) ? $query->result() : array();
    }

    public function add_order($order_data)
    {
        return $this->db->insert('orders', $order_data);
    }
}

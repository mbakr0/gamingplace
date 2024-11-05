<?php

/**
 * 
 */
class product_model extends CI_model
{
	

	public function get_products()
	{
		$this->db->select('*');
		$this->db->from('products');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_product_details($id){

		$this->db->select('*');
		$this->db->from('products');
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_categories()
	{
		$this->db->select('*');
		$this->db->from('categoies');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_popular(){
		$this->db->select("p.*,COUNT(o.product_id) as total");
		$this->db->from("orders AS o");
		$this->db->join("products AS p","o.product_id = p.id","INNER");
		$this->db->group_by("o.product_id");
		$this->db->order_by("total","DESC");
		$query = $this->db->get();
		return $query->result();
	}

	public function add_order($order_data){

		$insert = $this->db->insert('orders',$order_data);
		return $insert;
	}


}
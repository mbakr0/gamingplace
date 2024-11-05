<?php

/*
*
*	Get Categories
*/

function get_categories_h(){
	$CI = get_instance();
	$Categories = $CI->Product_model->get_categories();
	return $Categories;
}

function get_popular_h(){
	$CI = get_instance();
	$CI->load->model('Product_model');
	$popular_product = $CI->Product_model->get_popular();
	return $popular_product;
}
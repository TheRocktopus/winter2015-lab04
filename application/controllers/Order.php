<?php

/**
 * Order handler
 * 
 * Implement the different order handling usecases.
 * 
 * controllers/welcome.php
 *
 * ------------------------------------------------------------------------
 */
 
class Order extends Application {

	function __construct() {
        parent::__construct();
    }

    // start a new order
    function neworder() {
        
		date_default_timezone_set('America/Los_Angeles');
		
		$order_num = $this->orders->highest() + 1;
		$new_order = $this->orders->create();
		
		// set properties
		$new_order->num = $order_num;
		$new_order->date = date('Y-m-d H:i:s'); // set date to now
		$new_order->status = 'a';
		$new_order->total = 0;
		
		$this->orders->add($new_order);

        redirect('/order/display_menu/' . $order_num);
    }

    // add to an order
    function display_menu($order_num = null) {
        
		if ($order_num == null)
		{
            redirect('/order/neworder');
		}

		$order = $this->orders->get($order_num);
		$orderTotal = $this->total($order_num);
			
        $this->data['pagebody'] = 'show_menu';
        $this->data['order_num'] = $order_num;
		$this->data['title'] = "Order # " . $order_num . " (" . $orderTotal . ")";
		
        // Make the columns
        $this->data['meals'] = $this->make_column('m');
        $this->data['drinks'] = $this->make_column('d');
        $this->data['sweets'] = $this->make_column('s');

        $this->render();
    }

    // make a menu ordering column
    function make_column($category) {
		
		$column = $this->menu->some('category', $category);
		
		foreach( $column as $col )
		{
			$col->order_num = $this->data['order_num'];
		}
		
		return $column;
    }

    // add an item to an order
    function add($order_num, $item) {
        
		$this->orders->add_item( $order_num, $item );
		
        redirect('/order/display_menu/' . $order_num);
    }
	
	function total($order_num)
	{
		$total;
		$totalFormatted;
		
		$total = $this->orders->total($order_num);
		$totalFormatted = sprintf("$%0.2f", $total);
		
		return $totalFormatted;
	}

    // checkout
    function checkout($order_num) {
        $this->data['title'] = 'Checking Out';
        $this->data['pagebody'] = 'show_order';
        $this->data['order_num'] = $order_num;
		$this->data['proceed_link'] = "";
		$this->data['proceed_warning'] = "";
        
		$order = $this->orders->get($order_num);
		$this->data['total'] = sprintf("$%0.2f", $order->total);	
		$this->data['items'] = $this->orders->details($order_num);		
		$validated = $this->orders->validate($order_num);
		
		if(!$validated)
		{
			$this->data['okornot'] = "disabled";
			$this->data['proceed_warning'] = "You must order one item in" .
				" each of the categories on the previous page to proceed";
		}
		else
		{
			$this->data['proceed_link'] = "/order/proceed/" . $order_num;
		}
		

        $this->render();
    }

    // proceed with checkout
    function proceed($order_num) {
        
		$order = $this->orders->get($order_num);
		$order->status = "c";
		$order->date = date('Y-m-d H:i:s'); // set date to now
		
		$this->orders->update($order);
		
        redirect('/');
    }

    // cancel the order
    function cancel($order_num) {
        
		$order = $this->orders->get($order_num);
		$order->status = "x";
		$order->date = date('Y-m-d H:i:s'); // set date to now
		
		$this->orders->update($order);
		
		$this->orders->flush($order_num);
		
        redirect('/');
    }

}

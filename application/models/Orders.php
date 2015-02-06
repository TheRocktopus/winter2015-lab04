<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
		
		$CI = &get_instance(); 
		$CI->load->model('orderitems');
    }

    // add an item to an order
    function add_item($num, $code) {
        
		// if the order exists...
		if($this->exists($num))
		{
			// if the item exists in this order, increment the quantity!
			if($this->orderitems->exists($num, $code))
			{
				$orderItem = $this->orderitems->get($num, $code);
				$qty = $orderItem->quantity;
				
				$qty++;
				$orderItem->quantity = $qty;
				
				$this->orderitems->update($orderItem);
			}
			else // otherwise, insert a record
			{
				$orderItem = $this->orderitems->create();
				$orderItem->order = $num;
				$orderItem->item = $code;
				$orderItem->quantity = 1;
				
				$this->orderitems->add($orderItem);
			}
		}
    }

    // calculate the total for an order
    function total($num) {
	
		// query for items in this order and their price
		// by joining orderitems on the menu table
		$orderItems = $this->db
			->select('m.price, oi.quantity')
			->from('orderitems AS oi')
			->join('menu AS m', 'oi.item = m.code', 'left outer')
			->where('oi.order = ' . $num)
			->get();
			
		$total = 0;
		
		// iterate through items in the order and add to total
		if( $orderItems->result() > 0 )
		{
			foreach( $orderItems->result() as $item )
			{
				$total += $item->price * $item->quantity;	
			}
		}
		
		// update the order with the new total
		$order = $this->get($num);
		$order->total = $total;
		$this->update($order);
	
        return $total;
    }

    // retrieve the details for an order
    function details($num) {
        
		$orderItems = $this->db
			->select('m.code, m.name, m.price, oi.quantity')
			->from('orderitems as oi')
			->join('menu as m', 'oi.item = m.code', 'left outer')
			->where('oi.order = ' . $num)
			->get();
			
		$items = array();
			
		if( $orderItems->result() > 0 )
		{
			foreach( $orderItems->result() as $item )
			{
				$items[] = array(
					"code" => $item->code,
					"name" => $item->name,
					"quantity" => $item->quantity,
					"price" => sprintf("$%0.2f", $item->price),
					"subtotal" => sprintf("$%0.2f", $item->quantity * $item->price)
				);
			}
		}
		
		return $items;
    }

    // cancel an order
    function flush($num) {
        
		// if the order exists, delete all related orderitems
		if($this->exists($num))
		{
			$items = $this->orderitems->delete_some($num);
		}
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
		
		// get order items with this order number
		$orderItems = $this->db
			->select('m.category, oi.quantity')
			->from('orderitems as oi')
			->join('menu as m', 'oi.item = m.code', 'left outer')
			->where('oi.order = ' . $num)
			->get();
		
		$numMeal = 0;
		$numDrink = 0;
		$numSweet = 0;
		
		// count number of items of each type
		foreach( $orderItems->result() as $item )
		{
			if( $item->quantity > 0 )
			{
				if( $item->category == 'm' )
				{
					$numMeal++;
				}
				else if( $item->category == 'd' )
				{
					$numDrink++;
				}
				else
				{
					$numSweet++;
				}
			}
		}
		
		// if the order has one of each, it is valid
		if( ( $numMeal > 0 ) && ( $numDrink > 0 ) && ( $numSweet > 0 ) )
		{
			return true;
		}
		
		return false;
    }

}

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
    }

    // add an item to an order
    function add_item($num, $code) {
        
		if($this->exists($num))
		{
			// if the item exists, increment the quantity!
			$orderItems = $this->orderitems->get($num);
			$qty = $orderItem->quantity;
			
			$qty++;
			$orderItem->quantity = $qty;
			
			$this->orderitems->update($orderItem);
		}
		else // otherwise, insert a record
		{
			$orderItem = $this->orderitems->create();
			$orderItem->order
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
	
        return $total;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        return false;
    }

}

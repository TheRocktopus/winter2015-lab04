<p class="lead">
    Order # {order_num} for {total}
</p>

<div class="row">
	<table class="table">
		<tr>
			<th>Item Name</th>
			<th>Price</th>
			<th>Quantity</th>
			<th>Subtotal</th>
		</tr>
		{items}
		<tr>
			<td>{name}</td>
			<td>{price}</td>
			<td>{quantity}</td>
			<td>{subtotal}</td>
		</tr>
		{/items}
	</table>
</div>

<div class="row pull-right">
	<h2><small>Total:</small> {total}</h2>
</div>

<div class="row">
	<br><br>
	<p class="text-danger"><small><strong>{proceed_warning}</strong></small></p>
	<br>
    <a href="{proceed_link}" class="btn btn-large btn-success {okornot}">Proceed</a>
    <a href="/order/display_menu/{order_num}" class="btn btn-large btn-primary">Keep shopping</a>
    <a href="/order/cancel/{order_num}" class="btn btn-large btn-danger">Forget about it</a>
	<br><br><br><br>
</div>
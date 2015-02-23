<script src="<?php echo JS;?>pos/home.js"></script>
<style>
.navbar{
margin-bottom:2px;
}
.operation_div {
top:86%;
left:13%;
position:absolute;
width:600px;
}
.operation_div button {
	margin:10px;
	float:left;
    line-height:4px; height:16px; width:150px; text-align:center; color:black;
	
}
</style>
<div class="container" >
	<div class="wrapper" style="margin-top:34px;">
<?php 

	//require_once DIR.'/sales_register/views/modal_expense.php';
	require_once DIR.'/sales_register/views/paid_bills_table.php';
	//require_once DIR.'/sales_register/views/load_card_table.php';
?>
	</div>
</div>


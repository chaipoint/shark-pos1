<style>
	
	.menu_div {	
		top:4%;
		left:.8%;
		
		width:600px;
	   }
	.menu_div button {
		margin:2px;
		margin-left:21px;
		float:left;
		line-height:4px; height:16px; width:150px; text-align:center; color:black;
		
}
</style>

<div class="menu_div">
	<?php 
		/* To do Dynamic */
		$walk_in = ((strpos($_SESSION['user']['store']['bill_type'], WALKIN) === false) ? 'hide' : '');    
		$olo = 	((strpos($_SESSION['user']['store']['bill_type'], COC) === false) ? 'hide' : ''); ;
		$coc = ((strpos($_SESSION['user']['store']['bill_type'], OLO) === false) ? 'hide' : ''); ;
		$preorder = ((strpos($_SESSION['user']['store']['bill_type'], PREORDER) === false) ? 'hide' : ''); ;
	?>
	<button class="alert <?php echo ((MODULE=='billing') ? 'active3' : '');?>  store-operation <?php echo $walk_in; ?>" id="walk-in" >Walk-in</button>
	<button class="alert <?php echo ((MODULE=='orders' && MODE=='index') ? 'active3' : '');?> store-operation <?php echo $coc; ?>" id="coc" >COC</button>
	<button class="alert <?php echo ((MODE=='olo') ? 'active3' : '');?> store-operation <?php echo $olo; ?>" id="olo" >OLO</button>
	<button class="alert <?php echo ((MODULE=='preorder') ? 'active3' : '');?> store-operation <?php echo $preorder; ?>"" id="preorder" >Pre-Order</button>
	
</div>
<style>
.navbar {margin-bottom: 0px;}
.menu_div {padding-top: 2px; padding-bottom: 0px;}
</style>
<div class="padded menu_div">
	<?php 
		/* To do Dynamic */
		$walk_in = ((strpos($_SESSION['user']['store']['bill_type'], WALKIN) === false) ? 'hide' : '');    
		$olo = 	((strpos($_SESSION['user']['store']['bill_type'], COC) === false) ? 'hide' : ''); ;
		$coc = ((strpos($_SESSION['user']['store']['bill_type'], OLO) === false) ? 'hide' : ''); ;
		$preorder = ((strpos($_SESSION['user']['store']['bill_type'], PREORDER) === false) ? 'hide' : ''); ;
		$caw = ((strpos($_SESSION['user']['store']['bill_type'], CAW) === false) ? 'hide' : ''); ;
		
	?>
	<button class="btn <?php echo ((MODULE=='billing') ? 'active3' : '');?>  store-operation <?php echo $walk_in; ?>" id="walk-in" >Walk-in</button>
	<button class="btn <?php echo ((MODULE=='orders' && MODE=='index') ? 'active3' : '');?> store-operation <?php echo $coc; ?>" id="coc" >COC</button>
	<button class="btn <?php echo ((MODE=='olo') ? 'active3' : '');?> store-operation <?php echo $olo; ?>" id="olo" >OLO</button>
	<button class="btn <?php echo ((MODULE=='caw') ? 'active3' : '');?> store-operation <?php echo $caw; ?>"" id="caw" >CAW</button>
	<button class="btn <?php echo ((MODULE=='preorder') ? 'active3' : '');?> store-operation <?php echo $preorder; ?>"" id="preorder" >Pre-Order</button>
	
</div>
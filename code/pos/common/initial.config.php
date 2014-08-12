<div class="text-center container">
	<div><img src="<?php echo IMG;?>loader.gif"></div>
	<div>Wait we are Initailizing...</div>
	<?php
		if(array_key_exists('error', $data) && $data['error']){
		?>
			<div class="alert alert-danger"><?php echo $data['message']?></div>
		<?php
		}elseif(!$data['data']['store_config']['is_configured']){
		?>
			<script>var store = <?php echo $data['data']['store_config']['store_id'];?>;</script>
			<script src="<?php echo JS;?>pos/config.js"></script>
		<?php
		}
	?>
</div>
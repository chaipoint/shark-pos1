<style>
.navbar-inverse {
background-color: #f5f5f5;
border-color: #0066cc;
}
.navbar-inverse .navbar-nav > li > a {
color: #007fff;
}

</style>	
	
		<div class="navbar navbar-static-top navbar-inverse">
			<div class="container">
				<div class="navbar-header">
				  <img src="<?php echo IMG?>logo.png" height="50px">
				</div>
				<ul class="nav navbar-nav">
					<iframe name="myiframe" src="<?php echo APP ?>/common/checknet.php" frameBorder="0" scrolling="no" style="width:250px;height:50px;float:right;margin-left:80px;"></iframe>
				</ul>
				
				<?php if(MODULE != 'store' && MODULE != 'home' && MODULE != 'billing' && MODULE != 'orders' && MODULE != 'dashboard'  ){?>
				<ul class="nav navbar-nav">
					<li>
						<a href="index.php?dispatch=home.index" class="btn nav-button btn-success btn-sm external <?php echo (MODULE == 'home' ? 'active-btn' : ''); ?>" style="padding: 5px 8px; margin: 10px 0 5px 5px;" data-placement="right" title="Home">Home</a>
					</li>
				<?php if(array_key_exists('shift', $_SESSION['user'])){?>
					<li>
						<a href="index.php?dispatch=billing.index" class="btn nav-button btn-success btn-sm external <?php echo (MODULE == 'billing' ? 'active-btn' : ''); ?>" style="padding: 5px 8px; margin: 10px 0 5px 5px;" data-placement="right" title="Sales">Billing </a>
					</li>
					<li>
						<a href="index.php?dispatch=orders" class="btn nav-button btn-success btn-sm external <?php echo (MODULE == 'orders' ? 'active-btn' : ''); ?>" style="padding: 5px 8px; margin: 10px 0 5px 5px;">CoC Orders</a>
					</li>

				<?php }?>
				<?php if(MODULE != 'billing'){?>					
                     <li>
						<a href="javascript:void(0)" class="btn nav-button btn-success btn-sm external" style="padding: 5px 8px; margin: 10px 0 5px 5px;" data-toggle="modal" data-target="" id="data_sync">Data Sync</a>
					</li>
				<?php } if(MODULE != 'billing' && MODULE != 'orders' ){?>
					<li>
						<a href="javascript:void(0)" class="require_valid_user btn nav-button btn-success btn-sm external <?php echo (MODULE == 'sales_register' ? 'active-btn' : ''); ?>" style="padding: 5px 8px; margin: 10px 0 5px 5px;" data-toggle="modal" data-target="" id="sales_register">Sale Register</a>
					</li>
				<?php }?>
				</ul>
				<?php } ?>
				<ul class="nav navbar-nav navbar-right">
					<li><a><?php echo $_SESSION['user']['name']." (".$_SESSION['user']['title']['name'].") " ; 
							      echo (array_key_exists('store', $_SESSION['user']) ? ','.$_SESSION['user']['store']['name'] : '');
							?>
						</a>
					</li>
					<?php if(MODULE != 'store' && MODULE != 'home' ){?>
					<li><a class="tip" data-placement="left" href="index.php?dispatch=home" title="Home" id="home" style="margin-right:-22px">
							Home |
						</a>
					</li>
					<?php } ?>
					
					<?php if(MODULE == 'home' ){?>
					<li><a class="tip" data-placement="left" href="index.php?dispatch=store" title="Change Store" id="change_store" style="margin-right:-22px">
							Change Store |
						</a>
					</li>
					<?php } ?>
					
					<li>
						<a class="tip" data-placement="left" href="javascirpt:void(0)" title="Logout" id="logout">
							  Logout 
						</a>
					</li>

					
					
				</ul>
			</div>
		</div>
	<div id="printBill" style="display:none;" ></div>
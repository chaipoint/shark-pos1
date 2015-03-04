<style>
.navbar-inverse {
background-color: #f5f5f5;
border-color: #0066cc;
}
.navbar-inverse .navbar-nav > li > a {
color: #007fff;
}
.navbar-inverse .navbar-nav:hover > li > a {
color:black;
}
.navbar-nav.navbar-right:last-child {
margin-right: 126px;
}
</style>	
	
		<div class="navbar navbar-static-top navbar-inverse">
			<div class="container">
				<div class="navbar-header" style="margin-left:10px">
				  <img src="<?php echo IMG?>logo.png" height="50px">
				</div>
				<ul class="nav navbar-nav">
					<iframe name="myiframe" src="<?php echo APP ?>/common/checknet.php" frameBorder="0" scrolling="no" style="width:250px;height:50px;float:right;margin-left:80px;"></iframe>
				</ul>
				
				<ul class="nav navbar-nav navbar-right">
					<li><a><?php echo $_SESSION['user']['name']; 
							     echo (array_key_exists('store', $_SESSION['user']) ? ','.$_SESSION['user']['store']['name'] : '');
							?>
						</a>
					</li>
					
					
					<?php if((MODULE=='home' && MODE != 'index') || MODULE == 'billing' || MODULE == 'caw' || MODULE == 'orders' ){?>
					<li><a class="tip" data-placement="left" href="index.php?dispatch=dashboard" title="Change Store" style="margin-right:-22px">
							Dashboard |
						</a>
					</li>
					<?php } ?>
					
					<?php if(MODULE == 'dashboard' ){?>
					<li><a class="tip" data-placement="left" href="index.php?dispatch=home.index" title="Change Store" style="margin-right:-22px">
							End Shift |
						</a>
					</li>
					<?php } ?>
					
					<?php if(MODULE == 'home' && MODE == 'index'){?>
					<li><a class="tip" data-placement="left" href="index.php?dispatch=store.index" title="Change Store" style="margin-right:-22px">
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
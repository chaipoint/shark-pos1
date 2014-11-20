	<style>
@-webkit-keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
@-moz-keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
@-o-keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
.con {
    -webkit-animation: blink 1s;
    -webkit-animation-iteration-count: infinite;
    -moz-animation: blink 1s;
    -moz-animation-iteration-count: infinite;
    -o-animation: blink 1s;
    -o-animation-iteration-count: infinite;
    height: 40px;
    width: 34px;
    margin-left: 11px;
    margin-top: 5px;
}
</style>
	<div id="wrap">
		<div class="navbar navbar-static-top navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse"
						data-target=".navbar-inverse-collapse">
						<span class="icon-bar"></span> <span class="icon-bar"></span> <span
							class="icon-bar"></span>
					</button>
					<a class="navbar-brand" style="font-size:22px;font-weight:bold">Shark</a>
				</div>
				<ul class="nav navbar-nav">
					<li class="dropdown"><a class="dropdown-toggle tip"
						data-toggle="dropdown" href="#" data-placement="right"
						title="Language"><img
							src=""
							style="margin-top: -1px" align="middle"> </a>
						<ul class="dropdown-menu" style="min-width: 60px;" role="menu"
							aria-labelledby="dLabel">
							<li><a><img
									src=""
									class="language-img"> &nbsp;&nbsp; English </a></li>
						</ul>
					</li>
					<li><a href="index.php?dispatch=home.index" class="btn nav-button btn-success btn-sm external <?php echo (MODULE == 'home' ? 'active-btn' : ''); ?>" style="padding: 5px 8px; margin: 10px 0 5px 5px;" data-placement="right" title="Home">Home</a></li>
				<?php if(array_key_exists('shift', $_SESSION['user'])){?>
					<li><a href="index.php?dispatch=billing.index" class="btn nav-button btn-success btn-sm external <?php echo (MODULE == 'billing' ? 'active-btn' : ''); ?>" style="padding: 5px 8px; margin: 10px 0 5px 5px;" data-placement="right" title="Sales">Billing </a></li>
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
					<li class="hide" id="notification">
						<img src="<?php echo IMG; ?>noti.ico" class="con">
					</li>
					<!--<li>
					    <a
					class="btn nav-button btn-success btn-sm external"
					style="padding: 5px 8px; margin: 10px 0 5px 5px;"
					data-toggle="modal" data-target="#saleModal" id="todays_sale"> Today's Sale 
				        </a>

					</li>-->
                    
					</ul>
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a>
							<?php echo $_SESSION['user']['name']." (".$_SESSION['user']['title']['name']."), ".$_SESSION['user']['store']['name'];?>
						</a>
					</li>
					
					<li>
						<a class="tip" data-placement="left" href="javascirpt:void(0)" title="Logout" id="logout">
							<i class="glyphicon glyphicon-log-out"></i>&nbsp;Logout 
						</a>
					</li>
					<li>
						<?php 
						$connected = @fsockopen("www.google.com", 80);
						if($connected) { 
							echo '<img src="'.IMG.'ok.png" class="con">';
							fclose($connected);
						}else {
							echo '<img src="'.IMG.'not_ok.png" class="con" >';
						} 
						?>
						
					</li>
				</ul>
			</div>
		</div>
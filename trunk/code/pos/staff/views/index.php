<script>var is_store_open = <?php echo $is_store_open; ?>;</script>
<script>var is_shift_running = <?php echo $is_shift_running; ?>;</script>
<script src="<?php echo JS;?>pos/staff.js"></script>
<script src="<?php echo JS;?>pos/login.js"></script>
<div class="panel panel-success">
    <div class="panel-heading">
      <h4 class="panel-title">Dashboard</h4>
  	</div>
    <div class="panel-body">
    	<div class="col-lg-12">
    		<div class="alert alert-warning" id="store_shift_message">Store is Closed.</div>
    		<span class="hidden" id="shift_count"><?php echo $total_shift;?></span>
		</div>
		<div class="col-lg-12">
	    	<div class="col-lg-3">
				<ul class="nav nav-pills nav-stacked" role="tablist" id="shift_nav">
				  <li><a class="" id="day_start">Day Start</a></li>
				  <li><a class="" id="shift_start">Shift Start</a></li>
				  <li><a class="" id="shift_end">Shift End</a></li>
				  <li><a class="" id="day_end">Day End</a></li>
			    </ul>
			</div>
			<div class="col-lg-8 col-md-8">
				<div class="col-lg-12">
					<ol class="breadcrumb">
						<li id="shift_breadcrumb">Day Start</li>
	  					<li class="active" id="shift_breadcrumb_module">Login</li>
					</ol>
				</div>
				<div class="col-lg-6 col-lg-offset-1">
					<div class="alert alert-danger" id="error_message"><ul></ul></div>
					<form id="loginform" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" value="MTF0081" name="identity" value="" id="username" class="form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="password" value="test@789" name="password" value="" id="password" class="form-control" placeholder="Password" autocomplete="off"/>
						</div>
						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-block btn-lg">Validate 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>
					
					<form id="petty_cash_form" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal hide" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="petty_cash" value="" id="petty_cash" class="form-control" placeholder="Petty Cash" autocomplete="off"/>
						</div>
						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-block btn-lg">Register 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
    </div>
</div>
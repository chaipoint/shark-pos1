<script src="<?php echo JS;?>pos/staff.js"></script>
<div class="row">
	<div class="col-lg-offset-1">
		<div class="col-lg-3">
			<ul class="nav nav-pills nav-stacked" role="tablist" id="shift_nav">
			  <li class="active"><a class="">Day Start</a></li>
			  <li><a class="">Shift Start</a></li>
			  <li><a class="">Shift End</a></li>
			  <li><a class="">Day End</a></li>
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
				<form id="loginform" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
					<div class="input-group padded">
						<span class="input-group-addon"> 
							<i class="glyphicon glyphicon-user"></i>
						</span> 
						<input type="text" name="identity" value="" id="username" class="form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
					</div>
					<div class="input-group padded">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-lock"></i> 
						</span> 
						<input type="password" name="password" value="" id="password" class="form-control" placeholder="Password" autocomplete="off"/>
					</div>
					<div class="row padded">
						<div class="col-md-12">
							<button type="submit" class="btn btn-success btn-block btn-lg">Validate 
								<i class="glyphicon glyphicon-log-in"></i>
							</button>
						</div>
					</div>
				</form>
			</div>
			<div class="col-lg-4 col-sm-4 col-lg-push-2 col-sm-push-2">
				<button class="btn btn-primary btn-sm" id="petty_cash">Petty Cash</button>
			</div>
		</div>
	</div>
</div>
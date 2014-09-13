<script>var is_store_open = <?php echo $is_store_open; ?>;</script>
<script>var is_shift_running = <?php echo $is_shift_running; ?>;</script>
<script src="<?php echo JS;?>pos/staff.js"></script>
<script src="<?php echo JS;?>pos/login.js"></script>
<div class="panel panel-success" id="store_shift_logic">
    <div class="panel-heading">
      <h4 class="panel-title">Dashboard</h4>
  	</div>
    <div class="panel-body">
    		<div class="col-lg-12">
				<ul class="nav nav-pills " role="tablist" id="shift_nav">
				  <li><a class="" id="day_start">Day Start</a></li>
				  <li><a class="" id="shift_start">Shift Start</a></li>
				  <li><a class="" id="shift_end">Shift End</a></li>
				  <li><a class="" id="day_end">Day End</a></li>
			    </ul>
			    <ul class="pull-right nav nav-pills " role="tablist">
			    	<li class="active">
			    		<a class="" id="pe_tg" data-toggle="dropdown">Petty Expence</a>

            			<ul class="dropdown-menu" role="menu">
            				<li><a href='#' id="add_expense">Add Expense</a></li>
            				<li><a href='#' id="view_expense">View Expense</a></li>
          				</ul>
			    	</li>
			    </ul>
			</div>
		<div class="col-lg-12 padded">
		    	<div class="col-lg-12">
    				<div class="alert alert-warning" id="store_shift_message">Store is Closed.</div>
    				<span class="hidden" id="shift_count"><?php echo $total_shift;?></span>
				</div>

				<div class="col-lg-6 col-lg-offset-1">
					<div class="alert alert-danger" id="error_message"><ul></ul></div>
					<form id="loginform" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-usd"></i> 
							</span> 
							<input type="text" name="petty_cash" value="" id="petty_cash" class="form-control" placeholder="Petty Cash" autocomplete="off"/>
						</div>
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
								<button type="submit" class="btn btn-success btn-block btn-lg">Start Day 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>

					<form id="counter_no_form" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="counter_no" value="" id="counter_no" class="form-control" placeholder="Counter Number" autocomplete="off"/>
						</div>
						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-block btn-lg">Start Shift 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>

					<form id="shift_end_form" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="petty_cash_end" value="" id="petty_cash_end" class="form-control" placeholder="Petty Cash" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="box_cash" value="" id="box_cash" class="form-control" placeholder="Box Cash" autocomplete="off"/>
						</div>
						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-block btn-lg">End Shift 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>

					<form id="day_end_form" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="box_cash_end" value="" id="box_cash_end" class="form-control" placeholder="Box Cash" autocomplete="off"/>
						</div>
						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-block btn-lg">End Day 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>

					<!--
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
				-->
				</div>

		</div>
    </div>
</div>
<?php require_once DIR.'/sales_register/views/paid_bills_table.php';?>
<script>var is_store_open = <?php echo $is_store_open; ?>;</script>
<script>var is_shift_running = <?php echo $is_shift_running; ?>;</script>
<script>var is_login_allowed = <?php echo $is_login_allowed; ?>;</script>
<script src="<?php echo JS;?>pos/home.js"></script>

<script type="text/javascript" src="<?php echo (JS.'jquery.dataTables.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.tableTools.js');?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'ui-lightness/jquery-ui-1.8.20.custom.css'); ?>" />
<link rel="stylesheet" href="<?php echo (CSS.'jquery.dataTables_themeroller.css');?>">

<div class="panel panel-info" style="margin-left:20px;margin-right:20px;" id="store_shift_logic">
    <div class="panel-heading">
      <h4 class="panel-title">Home</h4>
  	</div>
  	<div class="panel-body tabbable">
        <ul id="tab_selection_menu"class="nav nav-pills" role="tablist">
        	<li class='active'><a id='home_tab' class="home_tabs" href="javascript:void(0)">Home</a></li>
        	<li><a id='sales_tab' class="home_tabs" href="javascript:void(0)">Sales</a></li>
			<li><a id='shift_data_tab' class="home_tabs" href="javascript:void(0)">Shift Data</a></li>
        </ul>
    		<div id="shift_data_tab_data" class="tabs_data hidden">
    			<?php
    				echo $shift.$reconcilation;
    			?> 
			</div>

    		<div id="home_tab_data" class="tabs_data">
    			
    		<div class="col-lg-12">
				<ul class="list-unstyled list-inline" role="tablist" id="shift_nav">
				  <li><a class="btn btn-default btn-lg btn3d" id="day_start">Day Start</a></li>
				  <li><a class="btn btn-default btn-lg btn3d" id="shift_start">Shift Start</a></li>
				  <li><a class="btn btn-default btn-lg btn3d" id="shift_end">Shift End</a></li>
				  <li><a class="btn btn-default btn-lg btn3d" id="day_end">Day End</a></li>
			    </ul>
			</div>
		<div class="col-lg-12 padded">
				<div class="col-lg-6 col-md-5 col-sm-6">
					<ul class="list-unstyled">
						<?php if(array_key_exists('rows', $shift_data) && count($shift_data['rows']) > 0) {?>
						<li><span class="glyphicon glyphicon-ok"></span> &nbsp;POS Login by <?php echo $shift_data['rows'][0]['doc']['login_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($shift_data['rows'][0]['doc']['login_time']));?></li>
						<li><span class="glyphicon glyphicon-ok"></span> &nbsp;Day Started by <?php echo $shift_data['rows'][0]['doc']['day']['start_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($shift_data['rows'][0]['doc']['day']['start_time']));?></li>
						<?php foreach($shift_data['rows'][0]['doc']['shift'] as $key => $value) {?>
						<li><span class="glyphicon glyphicon-ok"></span> &nbsp;Shift <?php echo $value['shift_no']; ?> Started by <?php echo $value['start_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($value['start_time']));?> </li>
						<?php if(!empty($value['end_time'])) {?>
						<li><span class="glyphicon glyphicon-ok"></span> &nbsp;Shift <?php echo $value['shift_no']; ?> Closed by <?php echo $value['end_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($value['end_time']));?> </li>
						<?php }?>
						<?php }?>
						<?php if(!empty($shift_data['rows'][0]['doc']['day']['end_time'])) {?>
						<li><span class="glyphicon glyphicon-ok"></span> &nbsp;Day Closed by <?php echo $shift_data['rows'][0]['doc']['day']['end_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($shift_data['rows'][0]['doc']['day']['end_time']));?></li>
						<?php }?>
						<?php }else{ ?>
						<li><span class="glyphicon glyphicon-ok"></span> &nbsp;POS Login by <?php echo $_SESSION['user']['name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($_SESSION['user']['login']['time']));?></li>
						<?php }?>
					</ul>
				</div>
				<div class="col-lg-3 col-md-5 col-sm-5">
					<div class="alert alert-danger" id="error_message"><ul></ul></div>
					<form id="store_day_start_form"  class="store_shift hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" name="identity" value="" class="input-sm form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="password" name="password" value="" class="input-sm form-control" placeholder="Password" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-usd"></i> 
							</span> 
							<input type="text" name="petty_cash" value="" id="petty_cash" class="input-sm form-control" placeholder="Petty Cash" autocomplete="off"/>
						</div>
						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-sm btn-block btn-lg">Start Day 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>

					<form id="store_shift_start_form"  class="store_shift hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" name="identity" value=""  class="input-sm form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="password" name="password" value="" class="input-sm form-control" placeholder="Password" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="counter_no" value="1" id="counter_no" class="input-sm form-control" placeholder="Counter Number" autocomplete="off"/>
						</div>

						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-sm btn-block btn-lg">Start Shift 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>

					<form id="store_shift_end_form"  class="store_shift hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" name="identity" value="" class="input-sm form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="password" name="password" value="" class="input-sm form-control" placeholder="Password" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="petty_cash_end" value="" id="petty_cash_end" class="input-sm form-control" placeholder="Petty Cash" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="box_cash" value="" id="box_cash" class="input-sm form-control" placeholder="Box Cash" autocomplete="off"/>
						</div>

						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-sm btn-block btn-lg">End Shift 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>

					<form id="store_day_end_form"  class="store_shift hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" name="identity" value="" class="input-sm form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="password" name="password" value="" class="input-sm form-control" placeholder="Password" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="text" name="box_cash_end" value="" id="box_cash_end" class="input-sm form-control" placeholder="Box Cash" autocomplete="off"/>
						</div>
						<div class="row padded">
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-sm btn-block btn-lg">End Day 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
						</div>
					</form>
				</div>
		</div>
	</div>
	
	<div id="sales_tab_data" class="tabs_data hidden" style='margin-top:20px;'>
		<!-- Split button -->
		<div class="btn-group pull-right" style="margin-top:-55px;margin-right:120px;">
  			<button type="button" class="btn btn-sm btn-info"><strong>Petty Expense</strong></button>
  			<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown"  style="margin-right:10px;">
    			<span class="caret"></span>
    			<span class="sr-only">Toggle Dropdown</span>
  			</button>
  			<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="add_expense">Add Expense</a></li>
    			<li><a href="#" id="view_expense">View Expense</a></li>
    			
  			</ul>

  			
		</div>
		<div class="btn-group pull-right" style="margin-top:-55px;">
			<button type="button" class="btn btn-sm btn-success"><strong>Petty Inward</strong></button>
  			<button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
    			<span class="caret"></span>
    			<span class="sr-only">Toggle Dropdown</span>
  			</button>
  			<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="add_inward">Add Inward</a></li>
    			<!-- <li><a href="#" id="view_inward">View Inward</a></li> -->
    			
  			</ul>
		</div>
		<?php require_once DIR.'/sales_register/views/modal_expense.php';?>
		<?php require_once DIR.'/sales_register/views/paid_bills_table.php';?>
	</div>
  </div>
</div>
<div id="login_holder_home" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <?php require_once DIR.'/login/views/index.php';?>
</div><?php require_once 'modal_inward.php';?>
<?php //require_once DIR.'/sales_register/views/modal_expense.php';?>
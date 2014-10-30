<script>var is_store_open = <?php echo $is_store_open; ?>;</script>
<script>var is_shift_running = <?php echo $is_shift_running; ?>;</script>
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
        <ul id="tab_selection_menu" class="nav nav-pills" role="tablist">
        	<li class='active'><a id='home_tab' class="home_tabs" href="javascript:void(0)">Home</a></li>
        	<li><a id='sales_tab' class="home_tabs" href="javascript:void(0)">Sales</a></li>
			<li><a id='shift_data_tab' class="home_tabs require_valid_user" href="javascript:void(0)">Shift Data</a></li>
			<li class='dropdown'><a id='card__tab' class="dropdown-toggle home_tabs require_valid_user" data-toggle="dropdown" href="javascript:void(0)">Card Transaction <span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
      				<li><a id='ppa_card_tab' class="home_tabs" href="javascript:void(0)">PPA CARD</a></li>
      				<li><a id='ppc_card_tab' class="home_tabs" href="javascript:void(0)">PPC CARD</a></li>
    			</ul>
    		</li>
        </ul>
        <div class="hidden" id="shift_data_tab_holder">
		   	<form class="form-inline alert" id="shift_search_form" action="#"> 
		  		<input type="hidden" name="dispatch" value="sales_register"/>
				<div class="form-group">
					<div class="input-group">
		      			<input type="text" name = "shift_data_search" id="shift_data_search" class="form-control datepicker" required data-provide="datepicker-inline" data-date-format="dd-MM-yyyy"  data-date-autoclose = "true" data-date-end-date="+0d" name="expense_date" readonly/>      
		      			<span class="input-group-btn">
		        			<button class="btn btn-primary" type="button" style="padding-top:4px; padding-bottom:5px;" id="search_button"><i class="glyphicon glyphicon-search"></i></button>
			      		</span>
					</div>
				</div>
			</form>
    		<div id="shift_data_tab_data" class="tabs_data hidden">
    			<?php
    				echo $shift.$reconcilation;
    			?> 
			</div>
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
					<div class="alert alert-danger" id="error_message_shift"><ul></ul></div>

					<form id="store_day_start_form"  class="store_shift hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<input type="hidden" name="validateFor" value="shift">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" name="username1" value="" class="input-sm form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
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
							<div class="col-md-7">
								<button type="submit" class="btn btn-success btn-sm btn-block btn-lg">Start Day 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
							<div class="col-md-5">
								<button type="button" class="btn btn-danger btn-sm btn-block btn-lg cancel-btn">Cancel 
									<i class="glyphicon glyphicon-home"></i>
								</button>
							</div>
							
						</div>
					</form>

					<form id="store_shift_start_form"  class="store_shift hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<input type="hidden" name="validateFor" value="shift">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" name="username1" value=""  class="input-sm form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="password" name="password" value="" class="input-sm form-control" placeholder="Password" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-open"></i> 
							</span> 
							<input type="text" name="counter_no" value="1" id="counter_no" class="input-sm form-control" placeholder="Counter Number" autocomplete="off"/>
						</div>

						<div class="row padded">
							<div class="col-md-7">
								<button type="submit" class="btn btn-success btn-sm btn-block btn-lg">Start Shift 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
							<div class="col-md-5">
								<button type="button" class="btn btn-danger btn-sm btn-block btn-lg cancel-btn">Cancel 
									<i class="glyphicon glyphicon-home"></i>
								</button>
							</div>
						</div>
					</form>

					<form id="store_shift_end_form"  class="store_shift hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<input type="hidden" name="validateFor" value="shift">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" name="username1" value="" class="input-sm form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
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
							<input type="text" name="petty_cash_end" value="" id="petty_cash_end" class="input-sm form-control" placeholder="Petty Cash" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-briefcase"></i> 
							</span> 
							<input type="text" name="box_cash" value="" id="box_cash" class="input-sm form-control" placeholder="Box Cash" autocomplete="off"/>
						</div>

						<div class="row padded">
							<div class="col-md-7">
								<button type="submit" class="btn btn-success btn-sm btn-block btn-lg">End Shift 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
							<div class="col-md-5">
								<button type="button" class="btn btn-danger btn-sm btn-block btn-lg cancel-btn">Cancel 
									<i class="glyphicon glyphicon-home"></i>
								</button>
							</div>
						</div>
					</form>

					<form id="store_day_end_form"  class="store_shift hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
						<input type="hidden" name="validateFor" value="shift">
						<div class="input-group padded">
							<span class="input-group-addon"> 
								<i class="glyphicon glyphicon-user"></i>
							</span> 
							<input type="text" name="username1" value="" class="input-sm form-control" placeholder="Employee Code" autocomplete="off" autofocus="true"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i> 
							</span> 
							<input type="password" name="password" value="" class="input-sm form-control" placeholder="Password" autocomplete="off"/>
						</div>
						<div class="input-group padded">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-briefcase"></i> 
							</span> 
							<input type="text" name="box_cash_end" value="" id="box_cash_end" class="input-sm form-control" placeholder="Box Cash" autocomplete="off"/>
						</div>
						<div class="row padded">
							<div class="col-md-7">
								<button type="submit" class="btn btn-success btn-sm btn-block btn-lg">End Day 
									<i class="glyphicon glyphicon-log-in"></i>
								</button>
							</div>
							<div class="col-md-5">
								<button type="button" class="btn btn-danger btn-sm btn-block btn-lg cancel-btn">Cancel 
									<i class="glyphicon glyphicon-home"></i>
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
  			<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown"><strong>Petty Expense</strong><span class="caret"></span></button>
  			<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="add_expense">Add Expense</a></li>
    			<li><a href="#" id="view_expense">View Expense</a></li>
    		</ul>
		</div>
		
		<div class="btn-group pull-right" style="margin-top:-55px;">
			<button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown"><strong>Petty Inward</strong><span class="caret"></span></button>
  			<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="add_inward">Add Inward</a></li>
    			<!-- <li><a href="#" id="view_inward">View Inward</a></li> -->
			</ul>
		</div>
		<?php require_once DIR.'/sales_register/views/modal_expense.php';?>
		<?php require_once DIR.'/sales_register/views/paid_bills_table.php';?>
	</div>
	<div id="ppc_card_tab_data" class="tabs_data hidden">
			<div class="col-lg-12">
				<ul class="list-inline" role="tablist" id="shift_nav">
					<li><a class="btn btn-primary btn-lg btn3d" id="ppc_card_activate">Activate Card</a></li>
				  	<li><a class="btn btn-primary btn-lg btn3d" id="ppc_card_load">Load Card</a></li>
				  	<li><a class="btn btn-primary btn-lg btn3d" id="ppc_card_balance_check">Balance Check</a></li>
				</ul>
			</div>
			<div class="col-lg-12 padded">
				<div class="col-lg-3 col-md-5 col-sm-5">
					<div class="alert alert-danger" id="error_message_card"><ul></ul></div>
						<form id="store_ppc_card_activate_form"  class="card_form hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
							<input type="hidden" name="validateFor" value="card">
							<div class="input-group padded">
								<span class="input-group-addon"> 
									<i class="glyphicon glyphicon-user"></i>
								</span> 
								<input type="text" name="first_name" id="first_name" class="input-sm form-control" placeholder="First Name" autocomplete="off" autofocus="true"/>
							</div>
							<div class="input-group padded">
								<span class="input-group-addon"> 
									<i class="glyphicon glyphicon-user"></i>
								</span> 
								<input type="text" name="last_name" id="last_name" class="input-sm form-control" placeholder="Last Name" autocomplete="off" autofocus="true"/>
							</div>
							<div class="input-group padded">
								<span class="input-group-addon"> 
									<i class="glyphicon glyphicon-user"></i>
								</span> 
								<input type="text" name="mobile_no" id="mobile_no" class="input-sm form-control" placeholder="Mobile No" autocomplete="off" autofocus="true"/>
							</div>
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-usd"></i> 
								</span> 
								<input type="text" name="amount" value="" id="amount" class="input-sm form-control" placeholder="Amount" autocomplete="off"/>
							</div>
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-usd"></i> 
								</span> 
								<input type="text" name="card_number" value="" id="card_no" class="input-sm form-control" placeholder="Card No" autocomplete="off"/>
							</div>

							

							<div class="row padded">
								
								<div class="col-md-5">
									<button type="button" class="btn btn-danger btn-sm btn-block btn-lg cancel-btn">Cancel 
										<i class="glyphicon glyphicon-home"></i>
									</button>
								</div>
							
							</div>
						</form>
						<form id="store_ppc_card_load_form"  class="card_form hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
							<input type="hidden" name="validateFor" value="card">
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-usd"></i> 
								</span> 
								<input type="text" name="amount" value="" id="amount" class="input-sm form-control" placeholder="Amount" autocomplete="off"/>
							</div>
							
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-usd"></i> 
								</span> 
								<input type="text" name="card_number" value="" id="card_no" class="input-sm form-control" placeholder="Card No" autocomplete="off"/>
							</div>

						

							<div class="row padded">
								
								<div class="col-md-5">
									<button type="button" class="btn btn-danger btn-sm btn-block btn-lg cancel-btn">Cancel 
										<i class="glyphicon glyphicon-home"></i>
									</button>
								</div>
							
							</div>
						</form>
						<form id="store_ppc_card_balance_check_form"  class="card_form hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
							<input type="hidden" name="validateFor" value="card">
							
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-usd"></i> 
								</span> 
								<input type="text" name="card_number" value="" id="card_no" class="input-sm form-control" placeholder="Card No" autocomplete="off"/>
								<input type='hidden' name='amount' value=''/>
							</div>

							<div class="row padded">
								
								<div class="col-md-5">
									<button type="button" class="btn btn-danger btn-sm btn-block btn-lg cancel-btn">Cancel 
										<i class="glyphicon glyphicon-home"></i>
									</button>
								</div>
							
							</div>
						</form>
					</div>
				</div>
		</div>
  </div>
</div>
<?php require_once 'modal_inward.php';?>
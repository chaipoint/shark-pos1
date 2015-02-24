<style>
	.alert-info{
		color:black;
	}
	.alert-danger{
		color:grey;
	}
</style>
<script>var is_store_open = <?php echo $is_store_open; ?>;</script>
<script>var is_shift_running = <?php echo $is_shift_running; ?>;</script>
<script src="<?php echo JS;?>pos/home.js"></script>
	<div>
		<button class="alert <?php echo (($is_store_open=='true') ? 'alert-danger' : 'alert-info'); ?>  store-operation" id="day_start" style="margin-left:168px; width:30%; text-align:center;">Open Store</button>
		<button class="alert <?php echo (($is_store_open=='true' && $is_shift_running=='false') ? 'alert-info' : 'alert-danger'); ?> store-operation" id="day_end" style="margin-left:184px; margin-right:20px; width:30%; text-align:center;">Close Store</button>
	</div>
	<div style='margin-top:18px'>
		<button class="alert <?php echo (($is_store_open=='true' && $is_shift_running=='false' ) ? 'alert-info' : 'alert-danger'); ?> store-operation" id="shift_start" style="margin-left:315px; width:15%; text-align:center;">Start My Shift</button>
		<button class="alert <?php echo (($is_store_open=='true' && $is_shift_running=='true') ? 'alert-info' : 'alert-danger'); ?> store-operation" id="shift_end" style="margin-left:184px; margin-right:20px; width:15%; text-align:center;">End My Shift</button>
	</div>
	<div class="panel panel-info" style="margin-left:168px;margin-right:184px;margin-top:20px;" id="store_shift_logic">
		<div class="panel-body tabbable">
			<div id="home_tab_data" class="tabs_data">
				<div class="col-lg-12 padded">
					<div class="col-lg-4 col-md-5 col-sm-5">
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
									<i class="glyphicon glyphicon-briefcase"></i> 
								</span> 
								<input type="text" name="opening_box_cash" value="" id="opening_box_cash" class="input-sm form-control" placeholder="Box Cash" autocomplete="off"/>
							</div>
							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-open"></i> 
								</span> 
								<input type="text" readonly="readonly" name="counter_no" value="1" id="counter_no" class="input-sm form-control" placeholder="Counter Number" autocomplete="off"/>
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

							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-briefcase"></i> 
								</span> 
								<input type="text" name="cash_denomination" value="" id="cash_denomination" class="input-sm form-control" placeholder="Cash Denomination" autocomplete="off"/>
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
							<input type='hidden' name='qty_10' id='qty_10' value='0'>
							<input type='hidden' name='qty_20' id='qty_20' value='0'>
							<input type='hidden' name='qty_50' id='qty_50' value='0'>
							<input type='hidden' name='qty_100' id='qty_100' value='0'>
							<input type='hidden' name='qty_500' id='qty_500' value='0'>
							<input type='hidden' name='qty_sodex' id='qty_sodex' value='0'>
							<input type='hidden' name='amount_sodex' id='amount_sodex' value='0'>
							<input type='hidden' name='qty_restaurent' id='qty_restaurent' value='0'>
							<input type='hidden' name='amount_restaurent' id='amount_restaurent' value='0'>
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
					<div class="col-lg-8 col-md-7 col-sm-6 pull-right">
						<ul class="list-unstyled">
							<?php if(array_key_exists('rows', $shift_data) && count($shift_data['rows']) > 0) {?>
							<li style=""><span class="glyphicon glyphicon-ok"></span> &nbsp;Login By <?php echo $shift_data['rows'][0]['doc']['login_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($shift_data['rows'][0]['doc']['login_time']));?></li>
							<li style=""><span class="glyphicon glyphicon-ok"></span> &nbsp;Store Open by <?php echo $shift_data['rows'][0]['doc']['day']['start_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($shift_data['rows'][0]['doc']['day']['start_time']));?></li>
							<?php foreach($shift_data['rows'][0]['doc']['shift'] as $key => $value) {?>
							<li style=""><span class="glyphicon glyphicon-ok"></span> &nbsp;Shift <?php echo $value['shift_no']; ?> Started by <?php echo $value['start_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($value['start_time']));?> </li>
							<?php if(!empty($value['end_time'])) {?>
							<li style=""><span class="glyphicon glyphicon-ok"></span> &nbsp;Shift <?php echo $value['shift_no']; ?> Closed by <?php echo $value['end_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($value['end_time']));?> </li>
							<?php }?>
							<?php }?>
							<?php if(!empty($shift_data['rows'][0]['doc']['day']['end_time'])) {?>
							<li style=""><span class="glyphicon glyphicon-ok"></span> &nbsp;Store Closed by <?php echo $shift_data['rows'][0]['doc']['day']['end_staff_name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($shift_data['rows'][0]['doc']['day']['end_time']));?></li>
							<?php }?>
							<?php }else{ ?>
							<li style=""><span class="glyphicon glyphicon-ok"></span> &nbsp;Login By <?php echo $_SESSION['user']['name'];?> at <?php echo date('d-m-Y H:i:s',strtotime($_SESSION['user']['login']['time']));?></li>
							<?php }?>
						</ul>
						<div>
							
							<button class="alert alert-success store-operation"  id="dashboard" style="width:160px;margin-top:6px;color:black">Goto Dashboard</button>
						</div>
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
						</ul>
				</div>
				<?php //require_once DIR.'/sales_register/views/modal_expense.php';?>
				<?php //require_once DIR.'/sales_register/views/paid_bills_table.php';?>
				<?php //require_once DIR.'/sales_register/views/load_card_table.php';?>
			</div>
	
			
			
				
					<div class="alert alert-danger" id="error_message_card"><ul></ul></div>
						<form id="store_ppc_card_issue_form"  class="card_form hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
							<input type="hidden" name="validateFor" value="card">
							<div class="input-group padded">
								<span class="input-group-addon"> 
									<i class="glyphicon glyphicon-user"></i>
								</span> 
								<input type="text" name="card_group_name" id="card_group_name" class="input-sm form-control" placeholder="Card Group Name" autocomplete="off" autofocus="true"/>
							</div>
							<div class="input-group padded">
								<span class="input-group-addon"> 
									<i class="glyphicon glyphicon-user"></i>
								</span> 
								<input type="text" name="corporate_name" id="corporate_name" class="input-sm form-control" placeholder="Corporate Name" autocomplete="off" autofocus="true"/>
							</div>
							<div class="input-group padded">
								<span class="input-group-addon"> 
									<i class="glyphicon glyphicon-user"></i>
								</span> 
								<input type="text" name="empolye_id" id="empolye_id" class="input-sm form-control" placeholder="Empolyee Id" autocomplete="off" autofocus="true"/>
							</div>
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
								<input type="text" name="address" id="address" class="input-sm form-control" placeholder="Address" autocomplete="off" autofocus="true"/>
							</div>
							<div class="input-group padded">
								<span class="input-group-addon"> 
									<i class="glyphicon glyphicon-phone"></i>
								</span> 
								<input type="text" name="mobile_no" id="mobile_no" class="input-sm form-control" placeholder="Phone No" autocomplete="off" autofocus="true"/>
							</div>
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-credit-card"></i> 
								</span> 
								<input type="text" name="email" value="" id="email" class="input-sm form-control" placeholder="Email" autocomplete="off"/>
							</div>

							<div class="input-group padded">
								<span class="input-group-addon" >
									<img src="<?php echo IMG ?>inr.png"> 
								</span> 
								<input type="text" name="amount" value="" id="amount" class="input-sm form-control" placeholder="Amount" autocomplete="off"/>
							</div>

							

							<div class="row padded">
								<div class="col-md-7">
									<button type="submit" style="" class="btn btn-success btn-sm btn-block btn-lg">Issue Card 
										<i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>

								<div class="col-md-5">
									<button type="reset" class="btn btn-danger btn-sm btn-block btn-lg">Reset 
										<i class="glyphicon glyphicon-refresh"></i>
									</button>
								</div>
							</div>
						</form>

						<form id="store_ppc_card_reissue_form"  class="card_form hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
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
									<i class="glyphicon glyphicon-phone"></i>
								</span> 
								<input type="text" name="mobile_no" id="mobile_no" class="input-sm form-control" placeholder="Mobile No" autocomplete="off" autofocus="true"/>
							</div>

							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-credit-card"></i> 
								</span> 
								<input type="text" name="original_card_no" value="" id="original_card_no" class="input-sm form-control" placeholder="Original Card No" autocomplete="off"/>
								<input type='hidden' name='amount' value=''/>
							</div>

							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-credit-card"></i> 
								</span> 
								<input type="password" name="card_number" value="" id="card_number" class="input-sm form-control" placeholder="New Card No" autocomplete="off"/>
								
							</div>

							<div class="row padded">
								
								<div class="col-md-5">
									<button type="reset" class="btn btn-danger btn-sm btn-block btn-lg">Reset
										<i class="glyphicon glyphicon-refresh"></i>
									</button>
								</div>

								<div class="col-md-7">
									<button type="submit" style="" class="btn btn-success btn-sm btn-block btn-lg hides">REISSUE CARD 
										<i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>
								
							
							</div>
						</form>

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
									<i class="glyphicon glyphicon-phone"></i>
								</span> 
								<input type="text" name="mobile_no" id="mobile_no" class="input-sm form-control" placeholder="Mobile No" autocomplete="off" autofocus="true"/>
							</div>
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<img src="<?php echo IMG ?>inr.png"> 
								</span> 
								<input type="text" name="amount" value=""  id="amount" class="input-sm form-control" placeholder="Amount" autocomplete="off"/>
							</div>

							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-credit-card"></i> 
								</span> 
								<input type="password" name="card_number" value="" id="card_number" class="input-sm form-control" placeholder="Card No" autocomplete="off"/>
							</div>

							<div class="row padded">
								

								<div class="col-md-7">
									<button type="submit" style="" class="btn hide btn-success btn-sm btn-block btn-lg">Activate Card 
										<i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>
								<div class="col-md-5">
									<button type="reset" class="btn btn-danger btn-sm btn-block btn-lg">Reset 
										<i class="glyphicon glyphicon-refresh"></i>
									</button>
								</div>
								
							
							</div>
						</form>
						<form id="store_ppc_card_load_form"  class="card_form hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
							<input type="hidden" name="validateFor" value="card">
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<img src="<?php echo IMG ?>inr.png"> 
								</span> 
								<input type="text" name="amount"  value="" id="amount" class="input-sm form-control" placeholder="Amount" autocomplete="off"/>
							</div>

							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-credit-card"></i> 
								</span> 
								<input type="password" name="card_number" value="" id="card_number" class="input-sm form-control" placeholder="Card No" autocomplete="off"/>
							</div>

							<div class="row padded">
								

								<div class="col-md-7">
									<button type="submit" style="" class="btn hide btn-success btn-sm btn-block btn-lg">Load Card 
										<i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>
								<div class="col-md-5">
									<button type="reset" class="btn btn-danger btn-sm btn-block btn-lg">Reset 
										<i class="glyphicon glyphicon-refresh"></i>
									</button>
								</div>
								
							
							</div>
						</form>
						<form id="store_ppc_card_balance_check_form"  class="card_form hide" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
							<input type="hidden" name="validateFor" value="card">
							
							
							<div class="input-group padded">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-credit-card"></i> 
								</span> 
								<input type="password" name="card_number" value="" id="card_number" class="input-sm form-control" placeholder="Card No" autocomplete="off"/>
								<input type='hidden' name='amount' value=''/>
							</div>

							<div class="row padded">
								
								

								<div class="col-md-7">
									<button type="submit" style="" class="btn btn-success hide btn-sm btn-block btn-lg hides">Check Balance 
										<i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>
								<div class="col-md-5">
									<button type="reset" class="btn btn-danger btn-sm btn-block btn-lg">Reset
										<i class="glyphicon glyphicon-refresh"></i>
									</button>
								</div>
								
							
							</div>
						</form>
					</div>
				</div>
				

<?php //require_once 'modal_inward.php';?>
<?php require_once 'cash_denomination_modal.php';?>
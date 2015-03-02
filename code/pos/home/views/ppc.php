<script src="<?php echo JS;?>pos/home.js"></script>
<div class="padded menu_div" style="min-height:450px;">
	<div class="panel panel-info tabs_data" id="ppc_card_tab_data" style="width:60%" >
		<div class="panel-body tabbable">
			
			<ul class="list-inline" role="tablist" id="shift_nav">
				<li><a class="alert alert-info card store-operation" id="ppc_card_activate" href="javascript:void(0)">Activate PPC Card</a></li>
				<li><a class="alert alert-info card store-operation" id="ppc_card_load" href="javascript:void(0)">Load PPC Card</a></li>
				<li><a class="alert alert-info card store-operation" id="ppc_card_balance_check" href="javascript:void(0)">Balance Check</a></li>
			</ul>
			
			<div id="store_shift_logic">
				<div class="col-lg-12 padded">
					<div class="col-lg-4 col-md-5 col-sm-5">
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
										<button type="submit" style="" class="btn btn-success btn-sm btn-block btn-lg">Activate Card 
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
										<button type="submit" style="" class="btn btn-success btn-sm btn-block btn-lg">Load Card 
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
										<button type="submit" style="" class="btn btn-success btn-sm btn-block btn-lg hides">Check Balance 
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
				</div>
			</div>
		</div>
	</div>

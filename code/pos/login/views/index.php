<div class="container">
			<style>
			.container {
				width: 100%;
				max-width: 100%;
			}

			.container.padded>.row {
				margin: 0;
			}

			.padded {
				padding: 15px;
			}

			.separate-sections {
				margin: 0;
				list-style: none;
				padding-bottom: 5px;
			}

			.separate-sections>li,.separate-sections>div {
				margin-bottom: 15px !important;
			}

			.separate-sections>li:last-child,.separate-sections>div:last-child {
				margin-bottom: 0px;
			}

			i {
				margin: 0 10px;
			}
			</style>
			<script src="<?php echo JS;?>pos/login.js" type="text/javascript"></script>
			<div class="col-md-4 col-md-offset-4" id="login-box">
						<div class="padded" style="text-align: center; margin-top: 40px;">
							<img src="<?php echo IMG;?>logo.png"
								alt="ChaiPoint" />
							<div class="panel panel-primary">
								<div class="panel-heading">Login</div>
								<div class="panel-body" style="padding-bottom: 0;">

									<div class="alert alert-danger" id="error_message">
										<ul>
										</ul>
									</div>
								
									<form id="loginform" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
											<input type="hidden" name="validateFor" value="login">
			    							<div class="input-group">
											<span class="input-group-addon"> <i
												class="glyphicon glyphicon-user"></i>
											</span> <input type="text" name="username" value=""
												id="username" class="form-control" placeholder="Username" autocomplete="off" autofocus="true"/>
										</div>
										<?php
											$timestamp = getGmtOffset('Asia/Kolkata');
											$current_time = date('d-m-Y H:i:s', $timestamp-19800);
											$date_a = new DateTime($current_time);
											$date_b = new DateTime(date('d-m-Y H:i:s'));
											$interval = date_diff($date_a,$date_b);
											$status = 'false';
											$day = $interval->format('%d');
											$hours = $interval->format('%H');
											$minute = $interval->format('%i');
											if($day > 0){
												$status = 'true';
											}else if($hours > 0){
												$status = 'true';
											}else if($minute > 5){
												$status = 'true';
											}
											function getGmtOffset($zone){
												$ch = curl_init();
												curl_setopt($ch, CURLOPT_URL, 'http://api.timezonedb.com/?key=KE5HRDHLJVRW&zone=' . $zone . '&format=json');
												curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
												$json = curl_exec($ch);
												curl_close($ch);
												$data = json_decode($json);
												return (int)$data->timestamp;
											}
										?>
										<div class="input-group">
											<span class="input-group-addon"><i
												class="glyphicon glyphicon-lock"></i> </span> <input
												type="password" name="password" value="" id="password"
												class="form-control" placeholder="Password" autocomplete="off"/>
												<input type="hidden" name="time_check" value="<?php echo $status;?>" id="time_check"
												class="form-control" autocomplete="off"/>
												
										</div>
										<div class="row">

											<div class="col-md-12">
												<button type="submit" class="btn btn-success btn-block btn-lg">
													Login <i class="glyphicon glyphicon-log-in"></i>
												</button>
											</div>
										</div>
									</form>
									</div>
							</div>
							
						</div>
					</div>
		</div>
		<div id="footer" style="margin-top:220px;">
		<div class="container" >
			<p class="credit" style="font-size:10px;">
				Copyright &copy; 2014 ChaiPoint <?php global $config; echo $config['version']; ?> <a
					target="_blank" class="tip" title="Help"><i
					class="icon-question-sign"></i> </a>
			</p>
		</div>
	</div>
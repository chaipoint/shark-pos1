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
						<div class="padded" style="text-align: center; margin-top: 10px;">
							<img src="<?php echo IMG;?>logo.png"
								alt="ChaiPoint" height='117px' />
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
											$current_time = '';
											if(empty($_SESSION)){
												$timestamp = getGmtOffset('Asia/Kolkata');
												$current_time = ($timestamp!=0 ? date('Y-m-d', $timestamp-19800) : date('Y-m-d') );
											}
											function getGmtOffset($zone){
													$ch = curl_init();
													curl_setopt($ch, CURLOPT_URL, 'http://api.timezonedb.com/?key=KE5HRDHLJVRW&zone=' . $zone . '&format=json');
													curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
													curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
													curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
													$json = curl_exec($ch);
													$curl_errno = curl_errno($ch);
													$curl_error = curl_error($ch);
													curl_close($ch);
													if ($curl_errno > 0) {
														return 0;
													} 
													$data = json_decode($json);
													return @(int)$data->timestamp;
												}
										?>
										<div class="input-group">
											<span class="input-group-addon"><i
												class="glyphicon glyphicon-lock"></i> </span> <input
												type="password" name="password" value="" id="password"
												class="form-control" placeholder="Password" autocomplete="off"/>
											<input type="hidden" name="current_time" value="<?php echo $current_time;?>" id="current_time"
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
		<!--<div id="footer" style="margin-top:220px;">
		<div class="container" >
			<p class="credit" style="font-size:10px;">
				Copyright &copy; 2014 ChaiPoint <?php global $config; echo $config['version']; ?> <a
					target="_blank" class="tip" title="Help"><i
					class="icon-question-sign"></i> </a>
			</p>
		</div>
	</div>-->
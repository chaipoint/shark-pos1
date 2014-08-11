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
							<img src="http://tecdemo.com/spos3/assets/images/logo1.png"
								alt="Simple POS" />
							<div class="panel panel-primary">
								<div class="panel-heading">Login</div>
								<div class="panel-body" style="padding-bottom: 0;">

									<div class="alert alert-danger" id="error_message">
										<ul>
										</ul>
									</div>
								
									<form id="loginform" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
			    							<div class="input-group">
											<span class="input-group-addon"> <i
												class="glyphicon glyphicon-user"></i>
											</span> <input type="text" name="identity" value=""
												id="username" class="form-control" placeholder="Email" autocomplete="off"/>
										</div>
										<div class="input-group">
											<span class="input-group-addon"><i
												class="glyphicon glyphicon-lock"></i> </span> <input
												type="password" name="password" value="" id="password"
												class="form-control" placeholder="Password" autocomplete="off"/>
										</div>
										<div class="row">

											<div class="col-md-12">
												<button type="submit" class="btn btn-success btn-block btn-lg">
													Login <i class="glyphicon glyphicon-log-in"></i>
												</button>
											</div>
											<div class="col-md-12">
												<a href="" class="">
													Forgot Password? 
												</a>
											</div>
										</div>
									</form>
									</div>
							</div>
							<div class="row"><div class="col-md-8 col-md-offset-2">&copy; 2014 Simple POS</div></div>
						</div>
					</div>
		</div>
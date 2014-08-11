	<div id="wrap">
		<div class="navbar navbar-static-top navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse"
						data-target=".navbar-inverse-collapse">
						<span class="icon-bar"></span> <span class="icon-bar"></span> <span
							class="icon-bar"></span>
					</button>
					<a class="navbar-brand"> Chai Point POS </a>
				</div>
				<ul class="nav navbar-nav">
					<li class="dropdown"><a class="dropdown-toggle tip"
						data-toggle="dropdown" href="#" data-placement="right"
						title="Language"><img
							src=""
							style="margin-top: -1px" align="middle"> </a>
						<ul class="dropdown-menu" style="min-width: 60px;" role="menu"
							aria-labelledby="dLabel">
							<li><a><img
									src=""
									class="language-img"> &nbsp;&nbsp; English </a></li>
						</ul>
					</li>
					<li><a href="index.php?dispatch=sales_register.index"
						class="tip" data-placement="right" title="Sales"><i
							class="glyphicon glyphicon-list"></i> </a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a class="tip" data-placement="left" title="Logout" href="index.php?dispatch=login.out">
							<i class="glyphicon glyphicon-log-out"></i> 
						</a>
					</li>
				</ul>
				<a
					class="btn btn-success btn-sm pull-right external"
					style="padding: 5px 8px; margin: 10px 0 5px 5px;"
					data-toggle="modal" data-target="#saleModal" id="todays_sale"> Today's Sale </a>
				
				<ul class="nav navbar-nav navbar-right">
					<li><a class="hov"><span id="cur-time"></span> </a></li>
				</ul>
			</div>
		</div>
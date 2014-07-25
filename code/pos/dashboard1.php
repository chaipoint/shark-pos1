<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap.min.js"></script>
    <style type="text/css">
      #footer {
        background-color: #f5f5f5;
      }
      /* Lastly, apply responsive CSS fixes as necessary */
      @media (max-width: 767px) {
        #footer {
          margin-left: -20px;
          margin-right: -20px;
          padding-left: 20px;
          padding-right: 20px;
        }
      }
      .credit {
        margin: 20px 0;
      }

    </style></head>
<body>
	<div id="wrap">
	<div class="navbar navbar-static-top navbar-inverse">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target=".navbar-inverse-collapse">
					<span class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand"> Simple POS </a>
			</div>
			<ul class="nav navbar-nav">
				<li class="dropdown"><a class="dropdown-toggle tip"
					data-toggle="dropdown" href="#" data-placement="right"
					title="Language"><img
						src="http://tecdemo.com/spos3/assets/images/english.png"
						style="margin-top: -1px" align="middle"> </a>
					<ul class="dropdown-menu" style="min-width: 60px;" role="menu"
						aria-labelledby="dLabel">
						<li><a
							href="http://tecdemo.com/spos3/index.php?module=pos&view=language&lang=english"><img
								src="http://tecdemo.com/spos3/assets/images/english.png"
								class="language-img"> &nbsp;&nbsp; English </a></li>
					</ul>
				</li>
				<li><a href="http://tecdemo.com/spos3/index.php?module=sales"
					class="tip" data-placement="right" title="Sales"><i
						class="glyphicon glyphicon-list"></i> </a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a
					href="http://tecdemo.com/spos3/index.php?module=auth&amp;view=logout"
					class="tip" data-placement="left" title="Hi, User! Logout"><i
						class="glyphicon glyphicon-log-out"></i> </a></li>
			</ul>
			<a
				href="http://tecdemo.com/spos3/index.php?module=pos&view=today_sale"
				class="btn btn-success btn-sm pull-right external"
				style="padding: 5px 8px; margin: 5px 0 5px 5px;" data-toggle="modal"
				data-target="#saleModal"> Today's Sale </a> <a data-toggle="modal"
				href="http://tecdemo.com/spos3/index.php?module=pos&view=opened_bills"
				data-target="#opModal"
				class="btn btn-info btn-sm pull-right external" id="ob"
				style="padding: 5px 8px; margin: 5px 5px 5px 5px;"> Opened Bills </a>
			<a
				href=""
				class="tip btn btn-default btn-sm pull-right external"
				style="padding: 5px 8px; margin: 5px 5px 5px 5px;" title="" data-html="true"
				data-placement="bottom"> Buy Now </a>
			<ul class="nav navbar-nav navbar-right">
				<li><a class="hov"><span id="cur-time"></span> </a></li>
			</ul>
		</div>
	</div>
	</div>
	
	<div class="container" style="border:1px solid red;">
		<div class="col-lg-6" style="border:1px solid red;"></div>
		<div class="col-lg-6" style="border:1px solid red;">as</div>
		
	</div>
	
	
	<div id="footer">
      <div class="container">
    <p class="credit">Copyright &copy;
          2014          Simple POS          v
          3.0          - Page rendered in 0.0541 seconds. <a href="http://tecdiary.net/support/sma-guide/" target="_blank" class="tip" title="Help"><i class="icon-question-sign"></i></a> </p>
  </div>
    </div>
</body>
</html>

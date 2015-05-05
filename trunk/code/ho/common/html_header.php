<?php 
	$dashboard =(basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active' : ''); 
	$report = (basename($_SERVER['PHP_SELF'])=='report.php' ? 'active' : ''); 
?>
<!-- Fixed navbar -->
  <div class="navbar navbar-default navbar-fixed-top"  role="navigation">
        <div class="container" >
        <div class="navbar-header" >
           <a class="navbar-brand" style="color:#ffffff;margin-right:40px;font-size:22px;font-weight:bold" href="#">Shark</a>
        </div>
        <div class="navbar-collapse collapse" >
            <ul class="nav navbar-nav" >
            <li class="<?php echo $dashboard; ?>"><a  href="dashboard.php">Dashboard</a></li>
            <li class="<?php echo $report; ?>"><a href="report.php" style="color:#ffffff;">Reports </a></li>
            <!--<li><a href="#contact" style="color:#ffffff;">Analytics</a></li>
            <li class="dropdown">
             <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#ffffff;">Data Set <span class="caret"></span></a>
             <ul class="dropdown-menu" role="menu">
             <li><a href="#">Staff Master</a></li>
                <li><a href="#">Store Master</a></li>
                <li><a href="#">Settings</a></li>
                <li class="divider"></li>-->
                <!--<li class="dropdown-header">Nav header</li>-->
                <!--<li><a href="#">Bill Details</a></li>
                <li><a href="#">Attendance Sheet</a></li>
              </ul>
            </li>-->
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#ffffff;">Data Sync <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="javascript:void(0);" id="staff_synk">Staff From CPOS</a></li>
                <li><a href="javascript:void(0);" id="store_synk">Store From CPOS</a></li>
                <li><a href="javascript:void(0);" id="config_synk">Config From CPOS</a></li>
                <li><a href="javascript:void(0);" id="retail_customer_synk">Retail Customers From CPOS</a></li>
                <li class="divider"></li>
                <!--<li class="dropdown-header">Nav header</li>-->
                <li><a href="javascript:void(0);" id="bill_synk">Upload Bill</a></li>
				<li><a href="javascript:void(0);" id="card_synk">Upload Card Sale</a></li>
                <li><a href="javascript:void(0);" id="updated_bill_synk">Upload Updated Bill</a></li>
                <li><a href="javascript:void(0);" id="petty_expense_synk">Upload Petty Expense</a></li>
                <li><a href="javascript:void(0);" id="shift_data_synk">Upload Shift Data</a></li>
                <li><a href="javascript:void(0);" id="login_history_synk">Upload Login History</a></li>
                <!-- <li><a href="#">Upload Attendance</a></li> -->
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <!--<li><a href="../navbar/">Default</a></li>
            <li><a href="../navbar-static-top/">Static top</a></li>-->
            <li class="active"><a href="./" >Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

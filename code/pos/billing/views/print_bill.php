<?php $billdata = $bill;
	echo "<pre>";
//	print_r($billdata);
	echo "</pre>";
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Sale No. 3965</title>
<script src="<?php echo JS;?>jquery.min.js"></script>
<style type="text/css" media="all">
body { max-width: 300px; margin:0 auto; text-align:center; color:#000; font-family: Arial, Helvetica, sans-serif; font-size:12px; }
#wrapper { min-width: 250px; margin: 0 auto; }
#wrapper img { max-width: 300px; width: auto; }

h2, h3, p { margin: 5px 0; }
.left { width:60%; float:left; text-align:left; margin-bottom: 3px; }
.right { width:40%; float:right; text-align:right; margin-bottom: 3px; }
.table, .totals { width: 100%; margin:10px 0; }
.table th { border-bottom: 1px solid #000; }
.table td { padding:0; }
.totals td { width: 24%; padding:0; }
.table td:nth-child(2) { overflow:hidden; }

@media print {
	body { text-transform: uppercase; }
	#buttons { display: none; }
	#wrapper { width: 100%; margin: 0; font-size:9px; }
	#wrapper img { max-width:300px; width: 80%; }
}

</style>
</head>
<body>
<div id="wrapper">
    <h2>
        <strong>CHAIPOINT</strong>
    </h2>
    <p>
       SOUTH END CIRCLE,<br>
       BANGLORE<br/>KARNATKA 560100<br/>CASH/BILL
    </p>    	
    <span class="left">Tel: 01234567890</span> 
	<span class="right">Sale No.: 3965</span><span class="left">Customer: Numpty Shavings</span> 
	<span class="right">Date: 28/07/2014 17:24:31</span>    <div style="clear:both;"></div>
    
	<table class="table" cellspacing="0"  border="0"> 
	<thead> 
	<tr> 
	    <th><em>#</em></th> 
	    <th>Description</th> 
        <th>Quantity</th>
	    <th>Subtotal</th> 
	</tr> 
	</thead> 
	<tbody> 
        <?php
            if(array_key_exists('items', $billdata) && count($billdata['items'])>0){
            	$count = 0;
                foreach($billdata['items'] as $billKey => $billValue){
                    echo '              <tr>
                <td style="text-align:center; width:30px;">'.(++$count).'</td>
                <td style="text-align:left; width:180px;">'.$billValue['name'].'</td>
                <td style="text-align:center; width:50px;">'.$billValue['qty'].'</td>
                <td style="text-align:right; width:70px; ">'.$billValue['netAmount'].'</td>
            </tr> 
';
                }
            }
        ?>
    	</tbody> 
	</table> 
    
    <table class="totals" cellspacing="0" border="0" style="margin-bottom:5px;">
    <tbody>
    <tr>
    <td style="text-align:left;">Total Items</td><td style="text-align:right; padding-right:1.5%; border-right: 1px solid #000;font-weight:bold;"><?php echo $billdata['total_qty'];?></td>
    <td style="text-align:left; padding-left:1.5%;">Total</td><td style="text-align:right;font-weight:bold;"><?php echo $billdata['sub_total'];?></td>
    </tr>
    <tr>
    <td style="text-align:left;">Tax</td><td style="text-align:right; padding-right:1.5%; border-right: 1px solid #000;font-weight:bold;"><?php echo $billdata['total_tax'];?></td>
    <td style="text-align:left; padding-left:1.5%;"></td><td style="text-align:right;font-weight:bold;"></td>
    </tr>
    <tr>
    <td colspan="2" style="text-align:left; font-weight:bold; border-top:1px solid #000; padding-top:5px;">Grand Total</td><td colspan="2" style="border-top:1px solid #000; padding-top:5px; text-align:right; font-weight:bold;"><?php echo $billdata['due_amount'];?></td>
    </tr>
    <tr>    
    <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Paid</td><td colspan="2" style="padding-top:5px; text-align:right; font-weight:bold;"><?php echo $billdata['paid_amount'];?></td>
    </tr>
    <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Change</td><td colspan="2" style="padding-top:5px; text-align:right; font-weight:bold;"><?php echo $billdata['paid_amount'];?></td>
    </tr>
        
    </tbody>
    </table>
        
    <div style="border-top:1px solid #000; padding-top:10px;">
    <p>
                                                                                              Thank you for your business!
</p>    </div>
    
    <div id="buttons" style="padding-top:10px; text-transform:uppercase;">
    <span class="left"><a href="#" style="width:90%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#000; background-color:#4FA950; border:2px solid #4FA950; padding: 10px 1px; font-weight:bold;" id="email">Email</a></span>
    <span class="right"><button type="button" onClick="window.print();return false;" style="width:100%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 1px; font-weight:bold;">Print</button></span>
    <div style="clear:both;"></div>
    <a href="dashboard.php" style="width:95%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#FFF; background-color:#007FFF; border:2px solid #007FFF; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">Back to POS</a>
    
    <div style="background:#F5F5F5; padding:10px;">
    <p style="font-weight:bold;">Please don't forget to disble the header and footer in browser print settings.</p>
    <p style="text-transform: capitalize;"><strong>FF:</strong> File > Print Setup > Margin & Header/Footer Make all --blank--</p>
    <p style="text-transform: capitalize;"><strong>chrome:</strong> Menu > Print > Disable Header/Footer in Option & Set Margins to None</p>	</div>
    <div style="clear:both;"></div>
    </div>
</div>
</body>
</html>
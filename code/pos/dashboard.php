<?php
	$catList = array();
	if(array_key_exists('store', $_GET) && is_numeric($_GET['store']) && $_GET['store'] >0){
		$url = 'http://127.0.0.1:5984/pos/_design/store/_view/store_list?key="'.$_GET['store'].'"';
		require_once 'httpapi.php';
		$storeDataList = json_decode(curl($url),true);
		$result = current($storeDataList['rows']);
		//print_r($result['value']);
		$catList = array();
		//var_dump($catList);
		$productList = array();
		//print_r($result['value']['menu_items']);
		foreach($result['value']['menu_items'] as $key => $Items){
			if(!empty($Items['category_id'])){
				$catList[$Items['category_id']] = $Items['category']; 
				$productList[$Items['category_id']][] = $Items;
			}
 		}

 		ksort($catList);
 	$catList[99] = 'AKESH';
 		 		$catList[] = 'AEWEKESH';
 		 		$catList[] = 'AEWEKESHSDFF';/**/

 		ksort($productList);
// 		print_r($productList);
 		$currectCat = array_keys($catList);
  		$firstCat = $currectCat[0];
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<base href="http://localhost/pos/" />
<title>Chai Point POS</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="shortcut icon"
	href="http://localhost/pos/images/icon.png" />
<script type="text/javascript">if(parent.frames.length>0)top.location='http://localhost/pos/index.php?module=pos'</script>
<link rel="stylesheet" href="css/bootstrap.css" type="text/css"
	charset="utf-8">
<link rel="stylesheet" href="css/posajax.css" type="text/css"
	charset="utf-8">

<link rel="stylesheet" href="css/non-responsive.css" type="text/css">
<link rel="stylesheet" href="css/print.css" type="text/css"
	media="print">
<script src="js/jquery.min.js"></script>


<script src="js/purl.js"></script>
<script src="js/moment.js"></script>
<style>
.btn-product {
	background: #EEE;
	border: 1px solid #EEE;
	border-bottom: 0;
}

.btn-con .btn-default {
	height: 43px;
}
</style>
<?php
	if(count($catList)>0){
		echo '
			<script>
				var catList = \''.json_encode($catList).'\';
				var productList = \''.json_encode($productList).'\';
				var productArray = $.parseJSON(productList);
				var selectedCat = '.$firstCat.';
			</script>
		';
	}

?>
<script>
	$(document).ready(function(){

		var now = moment().format("dddd, MMMM Do, YYYY, h:mm:ss A");
        $('#cur-time').text(now);
       

		var $billingItems = new Object();
		var $totalBillItems = 0;
		var $totalBillCost = 0.0;

		$("#cancel").click(function(){
			$billingItems = new Object();
			$totalBillItems = 0;
			$totalBillCost = 0.0;
			$("#count").text(0);		
			$("#total").text(0);
			$("#total-payable").text(0);	
			$("#saletbl tbody").html("");	
		});
		$(".category-selection").click(function(){
			if(!$(this).hasClass("btn-primary")){
				$(".category-selection").removeClass('btn-primary');
				$(this).addClass('btn-primary');
				//console.log(productArray[$(this).data('category')]);
				$buttonList = "";
				selectedCat = $(this).data('category');
				$.each(productArray[selectedCat],function(key,value){
					$buttonList += '<button type="button" class="btn btn-prni hov category-product" value="'+value.mysql_id+'" category-product-sequence="'+key+'">'+value.name+'</button>';
				});
				$("#proajax").html($buttonList);
			}
		});

		$("#proajax").on("click",".category-product",function(){
				var selectedSequence = $(this).attr('category-product-sequence');
				var productData = productArray[selectedCat][selectedSequence];
				if($billingItems[productData.mysql_id]){
					$billingItems[productData.mysql_id].qty = parseInt($billingItems[productData.mysql_id].qty) + 1;
					$billingItems[productData.mysql_id].price = parseInt($billingItems[productData.mysql_id].price) + parseInt(productData.price);
					var bIO = $('tr[billing-product="'+productData.mysql_id+'"]');					
					bIO.find(".qty").text($billingItems[productData.mysql_id].qty);
					bIO.find(".price").text($billingItems[productData.mysql_id].price);
					
					$totalBillItems +=  1;
					$totalBillCost += parseFloat(productData.price);

					//console.log($billingItems);
				}else{
					$billingItems[productData.mysql_id] = new Object();
					$billingItems[productData.mysql_id].qty = 1;
					$billingItems[productData.mysql_id].price = productData.price;
					$totalBillItems 	+= 	parseInt($billingItems[productData.mysql_id].qty);
					$totalBillCost 		+=  parseFloat(productData.price)
					$("#saletbl tbody").append('<tr billing-product="'+productData.mysql_id+'"><td><span class="glyphicon glyphicon-remove-sign"></span></td><td class="btn-warning">'+productData.name+'&nbsp;@&nbsp;'+productData.price+'</td><td><span class="qty">'+(1)+'</span></td><td><span class="price text-right">'+productData.price+'</span></td></tr>');
					//				console.log($billingItems[productData.mysql_id]);
					//				console.log($billingItems);
				}
				$("#count").text($totalBillItems);		
				$("#total").text($totalBillCost);
				$("#total-payable").text($totalBillCost);		

		});
		$('.btn-category').bxSlider({minSlides:5,maxSlides:5,slideWidth:600,slideMargin:0,ticker:false,infiniteLoop:false,hideControlOnEnd:true,mode:'horizontal'});

	});
</script>
</head>
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
							<li><a
								href=""><img
									src=""
									class="language-img"> &nbsp;&nbsp; English </a></li>
						</ul>
					</li>
					<li><a href="sales.php"
						class="tip" data-placement="right" title="Sales"><i
							class="glyphicon glyphicon-list"></i> </a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a
						href="http://localhost/pos/index.php?module=auth&amp;view=logout"
						class="tip" data-placement="left" title="Hi, User! Logout"><i
							class="glyphicon glyphicon-log-out"></i> </a></li>
				</ul>
				<a
					class="btn btn-success btn-sm pull-right external"
					style="padding: 5px 8px; margin: 10px 0 5px 5px;"
					data-toggle="modal" data-target="#saleModal"> Today's Sale </a> <a
					data-toggle="modal"
					data-target="#opModal"
					class="btn btn-info btn-sm pull-right external" id="ob"
					style="padding: 5px 8px; margin: 10px 5px 5px 5px;"> Opened Bills </a>
				
				<ul class="nav navbar-nav navbar-right">
					<li><a class="hov"><span id="cur-time"></span> </a></li>
				</ul>
			</div>
		</div>
		<div class="container">
			<div id="wrapper">
				<div id="content">
					<div class="c1">
						<div class="pos">
							<div class="alert alert-dismissable alert-success">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<p>Logged In Successfully</p>
							</div>
							<div id="pos">
								<form action="http://localhost/pos/index.php?module=pos"
									method="post" accept-charset="utf-8">
									<div style="display: none">
										<input type="hidden" name="csrf_pos"
											value="d18c90a451393f634f543c90f9a24b6d" />
									</div>
									<div class="well well-sm" id="leftdiv">
										<div id="lefttop">
											<input name="code" id="scancode"
												class="form-control input-sm" placeholder="Barcode Scanner"
												style="margin-bottom: 10px;" />
										</div>
										<div id="printhead">
											<h2>
												<strong>Simple POS</strong>
											</h2>
											<p>
												My Shop Lot, Shopping Mall,<br> Post Code, City
											</p>
											<p>Date: 18/07/2014</p>
										</div>
										<div id="print">
											<table width="100%" border="0" cellpadding="0"
												cellspacing="0"
												class="table table-striped table-condensed table-hover miantable"
												style="margin: 5px 0 0 0;" id="menu-table">
												<thead>
													<tr class="success">
														<th style="width: 9%" class="satu">X</th>
														<th>Product</th>
														<th style="width: 12%">Qty</th>
														<th style="width: 24%">Price</th>
														<th style="width: 19px; padding: 0;">&nbsp;</th>
													</tr>
												</thead>
											</table>
											<div id="prodiv">
												<div id="protbldiv" class="nano">
													<div class="content">
														<table width="100%" border="0" cellpadding="0"
															cellspacing="0"
															class="table table-striped table-condensed table-hover protable"
															id="saletbl" style="margin: 0;">
															<tbody>
															</tbody>
														</table>
													</div>
													<div style="clear: both;"></div>
												</div>
											</div>
											<div style="clear: both;"></div>
											<div id="totaldiv">
												<table id="totaltbl"
													class="table table-striped table-condensed totals"
													style="margin-bottom: 10px;">
													<tbody>
														<tr class="success">
															<td width="25%">Total Items</td>
															<td><span id="count">0</span></td>
															<td width="25%">Total</td>
															<td class="text_right" colspan="2"><span id="total">0</span>
															</td>
														</tr>
														<tr class="success">
															<td width="25%">Discount <a href="#" id="add_discount"
																style="color: #FFF; font-size: 0.80em"><i
																	class="glyphicon glyphicon-pencil"></i> </a>
															</td>
															<td><span id="ds_con">0</span></td>
															<td width="25%">Tax <a href="#" id="add_tax"
																style="color: #FFF; font-size: 0.80em"><i
																	class="glyphicon glyphicon-pencil"></i> </a>
															</td>
															<td class="text_right"><span id="ts_con">0</span></td>
														</tr>
														<tr class="success">
															<td colspan="2">Total Payable</td>
															<td class="text_right" colspan="2"><span
																id="total-payable">0</span></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
										<div id="botbuttons" style="text-align: center;">
											<button type="button" class="btn btn-danger" id="cancel"
												style="width: 90px;">Cancel</button>
											<!--<button type="button" class="btn btn-warning" id="print" onClick="window.print();return false;">
                  Print                  </button>-->
											<button type="button" class="btn btn-info" id="hold"
												style="width: 90px;">Hold</button>
											<button type="button" class="btn btn-success" id="payment"
												style="margin-right: 0; width: 180px;">Payment</button>
										</div>

										<input type="hidden" name="customer" id="customer" value="19" />
										<input type="hidden" name="inv_tax" id="tax_val" value="0" />
										<input type="hidden" name="inv_discount" id="discount_val"
											value="0" /> <input type="hidden" name="rpaidby" id="rpaidby"
											value="cash" style="display: none;" /> <input type="hidden"
											name="count" id="total_item" value="" /> <input type="hidden"
											name="delete_id" id="is_delete" value="" /> <input
											type="hidden" name="hold_ref" id="hold_ref" value="" /> <input
											type="hidden" name="paid_val" id="paid_val" value="" /> <input
											type="hidden" name="cc_no_val" id="cc_no_val" value="" /> <input
											type="hidden" name="cc_holder_val" id="cc_holder_val"
											value="" /> <input type="hidden" name="cheque_no_val"
											id="cheque_no_val" value="" /> <span id="hidesuspend"></span>
										<input type="submit" id="submit" value="Submit Sale"
											style="display: none;" />
									</div>
								</form>
								<div id="cp">
									<div id="slider">
										<div class="btn-category" data-catSelected = "<?php echo $firstCat; ?>">
											<?php
												foreach($catList as $catKey => $catValue){
													echo '<button type="button" class="btn '.($firstCat == $catKey ? 'btn-primary' : '').' category-selection" value="'.$catKey.'"
												id="category-'.$catKey.'" data-category="'.$catKey.'" >'.$catValue.'</button>
';
												}

											?>
										</div>
										<div style="clear: both;"></div>
									</div>
									<div style="clear: both;"></div>
									<div id="ajaxproducts">
										<div class="btn-product clearfix">
											<div id="proajax" style="overflow:scroll;max-height:400px;">
													<?php
														foreach($productList[$firstCat] as $pKey => $pValue){
															echo '<button type="button" class="btn btn-prni hov category-product" value="'.$pValue['mysql_id'].'" category-product-sequence="'.$pKey.'">'.$pValue['name'].'</button>';
														}

													?>

												<div style="clear: both;"></div>
											</div>
											<div class="btn-con">
												<button id="previous" type="button" class="btn btn-default"
													style='z-index: 10002;'>
													<i  style="margin-bottom:100%;"class="glyphicon glyphicon-chevron-left"></i>
												</button>
												<button id="next" type="button" class="btn btn-default"
													style='z-index: 10003;'>
													<i class="glyphicon glyphicon-chevron-right"></i>
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div style="clear: both;"></div>
						</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
	</div>
	</div>
	<div id="footer">
		<div class="container">
			<p class="credit">
				Copyright &copy; 2014 POS <a href="http://tecdiary.net/support/sma-guide/"
					target="_blank" class="tip" title="Help"><i
					class="icon-question-sign"></i> </a>
			</p>
		</div>
	</div>
	<div id="loading" style="display: none;">
		<div class="blackbg"></div>
		<div class="loader">
			<img src="http://localhost/pos/images/loader.gif" alt="" />
		</div>
	</div>
	<div class="modal fade" id="saleModal" tabindex="-1" role="dialog"
		aria-labelledby="saleModalLabel" aria-hidden="true"></div>
	<div class="modal fade" id="opModal" tabindex="-1" role="dialog"
		aria-labelledby="opModalLabel" aria-hidden="true"></div>

	<div class="modal fade" id="payModal" tabindex="-1" role="dialog"
		aria-labelledby="payModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="payModalLabel">Payment</h4>
				</div>
				<div class="modal-body">
					<table class="table table-striped" style="margin-bottom: 0;">
						<tbody>
							<tr>
								<td width="50%">Customer <a href="#"
									class="btn btn-primary btn-xs showCModal"><i
										class="glyphicon glyphicon-plus-sign"></i> Add Customer </a>
								</td>
								<td width="50%"><span class="inv_cus_con"> <select
										class="form-control pcustomer"
										style="padding: 2px !important; height: auto !important;">
											<option value="3">Walk-in Client</option>
											<option value="4">Mendim</option>
											<option value="5">MESA 1</option>
											<option value="6">ry</option>
											<option value="7">chris</option>
											<option value="8">Em Name Table 1</option>
											<option value="9">Internal Affairs</option>
											<option value="10">Joe Smith</option>
											<option value="11">Visitante</option>
											<option value="12">Leo</option>
											<option value="13">Maam Loyola</option>
											<option value="14">adil</option>
											<option value="15">MRK</option>
											<option value="16">Simourad SiFerm</option>
											<option value="17">Jeremy Frank</option>
											<option value="18">Jeremy Frank</option>
											<option value="19">Numpty Shavings</option>
											<option value="20">Pragash Rajarathnam</option>
											<option value="21">PEOOEP</option>
											<option value="22">test name</option>
											<option value="23">Elvis Perez</option>
											<option value="24">ozan</option>
											<option value="25">Tester</option>
											<option value="26">Humza</option>
											<option value="27">testing test</option>
											<option value="28">new customer</option>
											<option value="29">JAMES</option>
											<option value="30">error</option>
											<option value="31">error catch</option>
											<option value="32">Ok Testing</option>
											<option value="33">Saleem Acct</option>
											<option value="34">Alan Wee</option>
											<option value="35">Ahmed raza</option>
											<option value="36">Ban 1</option>
											<option value="37">Gregory Santana</option>
											<option value="38">asek</option>
											<option value="39">Pelo Pelovic</option>
											<option value="40">Fero Hora</option>
											<option value="41">growictafrica.org</option>
											<option value="42">sds</option>
											<option value="43">Alexander Fickel</option>
											<option value="44">testpeter</option>
											<option value="45">Jack Jones</option>
											<option value="46">123456test</option>
											<option value="47">irfan</option>
											<option value="48">Alexis Juventino Valdez Garcia</option>
											<option value="49">Abdul Bloggs</option>
											<option value="50">ini customer</option>
											<option value="51">Teste</option>
											<option value="52">Fero Hora</option>
											<option value="53">da</option>
											<option value="54">sammy</option>
											<option value="55">Davide Brunello</option>
											<option value="56">Jorge</option>
											<option value="57">Selim</option>
											<option value="58">Shimul</option>
											<option value="59">Monira Khatun</option>
											<option value="60">Yovani Martinez</option>
											<option value="61">some one</option>
											<option value="62">jack neo</option>
											<option value="63">John Smith</option>
											<option value="64">teet 01</option>
											<option value="65">Customer</option>
											<option value="66">Bala</option>
											<option value="67">kaushal</option>
											<option value="68">Mubarak Al-Mutawa</option>
											<option value="69">Jim Corners</option>
											<option value="70">rtret</option>
											<option value="71">qaisar</option>
											<option value="72">Talvinder</option>
											<option value="73">paijo</option>
											<option value="74">j</option>
											<option value="75">Ibrahim</option>
											<option value="76">Rodrigo Carvalho de Lima</option>
											<option value="77">Rodrigo Carvalho de Lima</option>
											<option value="78">anas</option>
											<option value="79">Edson Lemes</option>
											<option value="80">sample client</option>
											<option value="81">cristiangarcia</option>
											<option value="82">rr</option>
											<option value="83">MOOI</option>
											<option value="84">sto br 2</option>
											<option value="85">texs SADD</option>
											<option value="86">sobhi</option>
											<option value="87">Pedro Perez</option>
											<option value="88">Emilio Fuentes</option>
											<option value="89">Subbu</option>
											<option value="90">Pancho</option>
											<option value="91">Rajesh</option>
											<option value="92">Rohit</option>
											<option value="93">cgvb</option>
											<option value="94">ff</option>
											<option value="95">czxczxc</option>
											<option value="96">branco design</option>
											<option value="97">Joa Pinga</option>
											<option value="98">Pedro Perez</option>
											<option value="99">hbhjbhjb</option>
											<option value="100">adam</option>
											<option value="101">emirsyaf</option>
											<option value="102">emir</option>
											<option value="103">affan</option>
											<option value="104">test</option>
											<option value="105">jesus peres</option>
											<option value="106">Jennifer L</option>
											<option value="107">Mario Rossi</option>
											<option value="108">Akshay Sharma</option>
											<option value="109">hhfjfh jhghg</option>
											<option value="110">Daaa</option>
											<option value="111">Steve James</option>
											<option value="112">eng james</option>
											<option value="113">test</option>
											<option value="114">Hasan Al Masud</option>
											<option value="115">ogit syafarul mabrur</option>
											<option value="116">uuuuu</option>
											<option value="117">Alamin Mollah</option>
											<option value="118">Akshay</option>
											<option value="119">Ahmet Baki</option>
											<option value="120">Pan</option>
											<option value="121">uonikm</option>
											<option value="122">AAAA</option>
											<option value="123">Table3</option>
											<option value="124">Ryan</option>
											<option value="125">emir</option>
											<option value="126">bram</option>
											<option value="127">bramandita</option>
											<option value="128">bram</option>
											<option value="129">mordhwajh sadfsdaf</option>
											<option value="130">Test customer</option>
											<option value="131">bram</option>
											<option value="132">VAGELAS</option>
											<option value="133">XXXXX</option>
											<option value="134">Jack Robinson</option>
											<option value="135">Razvan</option>
											<option value="136">asd asd asd</option>
											<option value="137">keivn</option>
											<option value="138">joseph West field</option>
											<option value="139">navidaziz</option>
											<option value="140">smith</option>
											<option value="141">shashi</option>
											<option value="142">sgdgdfgdf</option>
											<option value="143">Demo</option>
											<option value="144">Hossain</option>
											<option value="145">Alpina vaerga</option>
											<option value="146">dodi</option>
											<option value="147">Josemar Dias Soares</option>
											<option value="148">test user</option>
											<option value="149">Eduardo</option>
											<option value="150">Chris Giles</option>
											<option value="151">dsadsa</option>
											<option value="152">ma</option>
											<option value="153">Will Kumvag</option>
											<option value="154">Adan Adan</option>
											<option value="155">jesse</option>
											<option value="156">0440044933</option>
											<option value="157">Juan Geldres</option>
											<option value="158">Chris Giles</option>
											<option value="159">Joseph Camblat</option>
											<option value="160">Ramón</option>
											<option value="161">Test Tyler</option>
											<option value="162">andy bayt</option>
											<option value="163">Alex</option>
											<option value="164">Allan ou Rozi</option>
											<option value="165">Chartlotte</option>
											<option value="166">
												<a class="__cf_email__"
													href="http://www.cloudflare.com/email-protection"
													data-cfemail="ecb8899f98ac98899f98c28f8381">[email&nbsp;protected]</a>
											</option>
											<option value="167">Ali</option>
											<option value="168">11</option>
											<option value="169">Mohon Robidas</option>
											<option value="170">Mohon Robidas 2</option>
											<option value="171">Rodrigo Carvalho</option>
											<option value="172">asad</option>
											<option value="173">llllll</option>
											<option value="174">assdc</option>
											<option value="175">test111111</option>
											<option value="176">An Nguyen</option>
											<option value="177">Pedro</option>
											<option value="178">fulando de tal</option>
											<option value="179">sadasd</option>
											<option value="180">asdasd</option>
											<option value="181">Fred bloggs</option>
											<option value="182">John smith</option>
											<option value="183">allay</option>
											<option value="184">abc</option>
											<option value="185">sabbir</option>
											<option value="186">d</option>
											<option value="187">marco</option>
											<option value="188">h</option>
											<option value="189">Test</option>
											<option value="190">Hello</option>
											<option value="191">vcxvcx ffsdfsddf</option>
											<option value="192">Tony Suprano</option>
											<option value="193">Christian renato cueva vega</option>
											<option value="194">ALE</option>
											<option value="195">vikasync</option>
											<option value="196">shihab</option>
											<option value="197">imran</option>
											<option value="198">JOAO COSTA</option>
											<option value="199">aaa</option>
											<option value="200">Nahuel Garraza</option>
											<option value="201">Emilio Garraza</option>
											<option value="202">erererdftgdfgfg</option>
											<option value="203">suman</option>
											<option value="204">ata js</option>
											<option value="205">srgraja</option>
											<option value="206">Jim Beam</option>
									</select>
								</span></td>
							</tr>
							<tr>
								<td>Total Payable Amount :</td>
								<td><span
									style="background: #FFFF99; padding: 5px 10px; text-weight: bold; color: #000;"><span
										id="twt"></span> </span></td>
							</tr>
							<tr>
								<td>Total Purchased Items :</td>
								<td><span
									style="background: #FFFF99; text-weight: bold; padding: 5px 10px; color: #000;"><span
										id="fcount"></span> </span></td>
							</tr>
							</tr>

							<td>Paid by :</td>
							<td><select name="paid_by" id="paid_by" class="form-control"
								style="padding: 2px !important; height: auto !important;">
									<option value="cash">Cash</option>
									<option value="CC">Credit Card</option>
									<option value="Cheque">Cheque</option>
							</select></td>
							</tr>
							<tr class="pcash">
								<td>Paid :</td>
								<td><input type="text" id="paid-amount" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
							<tr class="pcash">
								<td>Return Change :</td>
								<td><span
									style="background: #FFFF99; padding: 5px 10px; text-weight: bold; color: #000;"
									id="balance"></span></td>
							</tr>
							<tr class="pcc" style="display: none;">
								<td>Credit Card No :</td>
								<td><input type="text" id="pcc" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
							<tr class="pcc" style="display: none;">
								<td>Credit Card Holder :</td>
								<td><input type="text" id="pcc_holder" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
							<tr class="pcheque" style="display: none;">
								<td>Cheque No :</td>
								<td><input type="text" id="cheque_no" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
						</tbody>

					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						Close</button>
					<button class="btn btn-success" id="submit-sale">Submit</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="proModal" tabindex="-1" role="dialog"
		aria-labelledby="proModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="proModalLabel">Payment</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="rwNo" value="">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="oPrice" class="control-label"> Current Price </label>
								<input type="text" class="form-control input-sm" id="oPrice"
									disabled="disabled">
							</div>
							<div class="form-group">
								<label for="nPrice" class="control-label"> New Price </label> <input
									type="text" class="form-control input-sm kbp-input" id="nPrice"
									onClick="this.select();" placeholder="New Price">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="oQuantity" class="control-label"> Current Quantity </label>
								<input type="text" class="form-control input-sm" id="oQuantity"
									disabled="disabled">
							</div>
							<div class="form-group">
								<label for="nQuantity" class="control-label"> New Quantity </label>
								<input type="text" class="form-control input-sm kbq-input"
									id="nQuantity" onClick="this.select();"
									placeholder="Current Quantity">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						Close</button>
					<button class="btn btn-success" id="update-row">Update</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="customerModal" tabindex="-1" role="dialog"
		aria-labelledby="proModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="proModalLabel">Add Customer</h4>
				</div>
				<div class="modal-body">
					<div id="customerError"></div>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label class="control-label" for="code"> Name </label> <input
									type="text" name="name" value="" class="form-control input-sm"
									id="cname" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label" for="cemail"> Email Address </label>
								<input type="text" name="email" value=""
									class="form-control input-sm" id="cemail" />
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label" for="phone"> Phone </label> <input
									type="text" name="phone" value="" class="form-control input-sm"
									id="cphone" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label" for="cf1"> Custom Field 1 </label>
								<input type="text" name="cf1" value=""
									class="form-control input-sm" id="cf1" />
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label" for="cf2"> Custom Field 2 </label>
								<input type="text" name="cf2" value=""
									class="form-control input-sm" id="cf2" />
							</div>
						</div>
					</div>
					<input type="hidden" id="show_m" value="">
				</div>
				<div class="modal-footer" style="margin-top: 0;">
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						Close</button>
					<button class="btn btn-success" id="add-customer">Add Customer</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="susModal" tabindex="-1" role="dialog"
		aria-labelledby="susModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="susModalLabel">Save to Opened Bills</h4>
				</div>
				<div class="modal-body">
					<table class="table table-striped" style="margin-bottom: 0;">
						<tbody>
							<tr>
								<td width="50%">Customer <a href="#"
									class="btn btn-primary btn-xs showCModal"><i
										class="glyphicon glyphicon-plus-sign"></i> Add Customer </a>
								</td>
								<td width="50%"><span class="inv_cus_con"> <select
										class="form-control pcustomer"
										style="padding: 2px !important; height: auto !important;">
											<option value="3">Walk-in Client</option>
											<option value="4">Mendim</option>
											<option value="5">MESA 1</option>
											<option value="6">ry</option>
											<option value="7">chris</option>
											<option value="8">Em Name Table 1</option>
											<option value="9">Internal Affairs</option>
											<option value="10">Joe Smith</option>
											<option value="11">Visitante</option>
											<option value="12">Leo</option>
											<option value="13">Maam Loyola</option>
											<option value="14">adil</option>
											<option value="15">MRK</option>
											<option value="16">Simourad SiFerm</option>
											<option value="17">Jeremy Frank</option>
											<option value="18">Jeremy Frank</option>
											<option value="19">Numpty Shavings</option>
											<option value="20">Pragash Rajarathnam</option>
											<option value="21">PEOOEP</option>
											<option value="22">test name</option>
											<option value="23">Elvis Perez</option>
											<option value="24">ozan</option>
											<option value="25">Tester</option>
											<option value="26">Humza</option>
											<option value="27">testing test</option>
											<option value="28">new customer</option>
											<option value="29">JAMES</option>
											<option value="30">error</option>
											<option value="31">error catch</option>
											<option value="32">Ok Testing</option>
											<option value="33">Saleem Acct</option>
											<option value="34">Alan Wee</option>
											<option value="35">Ahmed raza</option>
											<option value="36">Ban 1</option>
											<option value="37">Gregory Santana</option>
											<option value="38">asek</option>
											<option value="39">Pelo Pelovic</option>
											<option value="40">Fero Hora</option>
											<option value="41">growictafrica.org</option>
											<option value="42">sds</option>
											<option value="43">Alexander Fickel</option>
											<option value="44">testpeter</option>
											<option value="45">Jack Jones</option>
											<option value="46">123456test</option>
											<option value="47">irfan</option>
											<option value="48">Alexis Juventino Valdez Garcia</option>
											<option value="49">Abdul Bloggs</option>
											<option value="50">ini customer</option>
											<option value="51">Teste</option>
											<option value="52">Fero Hora</option>
											<option value="53">da</option>
											<option value="54">sammy</option>
											<option value="55">Davide Brunello</option>
											<option value="56">Jorge</option>
											<option value="57">Selim</option>
											<option value="58">Shimul</option>
											<option value="59">Monira Khatun</option>
											<option value="60">Yovani Martinez</option>
											<option value="61">some one</option>
											<option value="62">jack neo</option>
											<option value="63">John Smith</option>
											<option value="64">teet 01</option>
											<option value="65">Customer</option>
											<option value="66">Bala</option>
											<option value="67">kaushal</option>
											<option value="68">Mubarak Al-Mutawa</option>
											<option value="69">Jim Corners</option>
											<option value="70">rtret</option>
											<option value="71">qaisar</option>
											<option value="72">Talvinder</option>
											<option value="73">paijo</option>
											<option value="74">j</option>
											<option value="75">Ibrahim</option>
											<option value="76">Rodrigo Carvalho de Lima</option>
											<option value="77">Rodrigo Carvalho de Lima</option>
											<option value="78">anas</option>
											<option value="79">Edson Lemes</option>
											<option value="80">sample client</option>
											<option value="81">cristiangarcia</option>
											<option value="82">rr</option>
											<option value="83">MOOI</option>
											<option value="84">sto br 2</option>
											<option value="85">texs SADD</option>
											<option value="86">sobhi</option>
											<option value="87">Pedro Perez</option>
											<option value="88">Emilio Fuentes</option>
											<option value="89">Subbu</option>
											<option value="90">Pancho</option>
											<option value="91">Rajesh</option>
											<option value="92">Rohit</option>
											<option value="93">cgvb</option>
											<option value="94">ff</option>
											<option value="95">czxczxc</option>
											<option value="96">branco design</option>
											<option value="97">Joa Pinga</option>
											<option value="98">Pedro Perez</option>
											<option value="99">hbhjbhjb</option>
											<option value="100">adam</option>
											<option value="101">emirsyaf</option>
											<option value="102">emir</option>
											<option value="103">affan</option>
											<option value="104">test</option>
											<option value="105">jesus peres</option>
											<option value="106">Jennifer L</option>
											<option value="107">Mario Rossi</option>
											<option value="108">Akshay Sharma</option>
											<option value="109">hhfjfh jhghg</option>
											<option value="110">Daaa</option>
											<option value="111">Steve James</option>
											<option value="112">eng james</option>
											<option value="113">test</option>
											<option value="114">Hasan Al Masud</option>
											<option value="115">ogit syafarul mabrur</option>
											<option value="116">uuuuu</option>
											<option value="117">Alamin Mollah</option>
											<option value="118">Akshay</option>
											<option value="119">Ahmet Baki</option>
											<option value="120">Pan</option>
											<option value="121">uonikm</option>
											<option value="122">AAAA</option>
											<option value="123">Table3</option>
											<option value="124">Ryan</option>
											<option value="125">emir</option>
											<option value="126">bram</option>
											<option value="127">bramandita</option>
											<option value="128">bram</option>
											<option value="129">mordhwajh sadfsdaf</option>
											<option value="130">Test customer</option>
											<option value="131">bram</option>
											<option value="132">VAGELAS</option>
											<option value="133">XXXXX</option>
											<option value="134">Jack Robinson</option>
											<option value="135">Razvan</option>
											<option value="136">asd asd asd</option>
											<option value="137">keivn</option>
											<option value="138">joseph West field</option>
											<option value="139">navidaziz</option>
											<option value="140">smith</option>
											<option value="141">shashi</option>
											<option value="142">sgdgdfgdf</option>
											<option value="143">Demo</option>
											<option value="144">Hossain</option>
											<option value="145">Alpina vaerga</option>
											<option value="146">dodi</option>
											<option value="147">Josemar Dias Soares</option>
											<option value="148">test user</option>
											<option value="149">Eduardo</option>
											<option value="150">Chris Giles</option>
											<option value="151">dsadsa</option>
											<option value="152">ma</option>
											<option value="153">Will Kumvag</option>
											<option value="154">Adan Adan</option>
											<option value="155">jesse</option>
											<option value="156">0440044933</option>
											<option value="157">Juan Geldres</option>
											<option value="158">Chris Giles</option>
											<option value="159">Joseph Camblat</option>
											<option value="160">Ramón</option>
											<option value="161">Test Tyler</option>
											<option value="162">andy bayt</option>
											<option value="163">Alex</option>
											<option value="164">Allan ou Rozi</option>
											<option value="165">Chartlotte</option>
											<option value="166">
												<a class="__cf_email__"
													href="http://www.cloudflare.com/email-protection"
													data-cfemail="edb9889e99ad99889e99c38e8280">[email&nbsp;protected]</a>
											</option>
											<option value="167">Ali</option>
											<option value="168">11</option>
											<option value="169">Mohon Robidas</option>
											<option value="170">Mohon Robidas 2</option>
											<option value="171">Rodrigo Carvalho</option>
											<option value="172">asad</option>
											<option value="173">llllll</option>
											<option value="174">assdc</option>
											<option value="175">test111111</option>
											<option value="176">An Nguyen</option>
											<option value="177">Pedro</option>
											<option value="178">fulando de tal</option>
											<option value="179">sadasd</option>
											<option value="180">asdasd</option>
											<option value="181">Fred bloggs</option>
											<option value="182">John smith</option>
											<option value="183">allay</option>
											<option value="184">abc</option>
											<option value="185">sabbir</option>
											<option value="186">d</option>
											<option value="187">marco</option>
											<option value="188">h</option>
											<option value="189">Test</option>
											<option value="190">Hello</option>
											<option value="191">vcxvcx ffsdfsddf</option>
											<option value="192">Tony Suprano</option>
											<option value="193">Christian renato cueva vega</option>
											<option value="194">ALE</option>
											<option value="195">vikasync</option>
											<option value="196">shihab</option>
											<option value="197">imran</option>
											<option value="198">JOAO COSTA</option>
											<option value="199">aaa</option>
											<option value="200">Nahuel Garraza</option>
											<option value="201">Emilio Garraza</option>
											<option value="202">erererdftgdfgfg</option>
											<option value="203">suman</option>
											<option value="204">ata js</option>
											<option value="205">srgraja</option>
											<option value="206">Jim Beam</option>
									</select>
								</span></td>
							</tr>

							<tr class="pcash">
								<td>Hold Bill Ref :</td>
								<td><input type="text" name="hold_v" value=""
									class="form-control input-sm" id="hold_ref_v" /></td>
						
						</tbody>

					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						Close</button>
					<button class="btn btn-success" id="submit-hold">Submit</button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/jquery.bxslider.min.js"></script>
	<script type="text/javascript" src="js/jquery.keyboard.min.js"></script>
	<script type="text/javascript" src="js/bootbox.js"></script>
	<script type="text/javascript">
var KB = 1;
var DTIME = 1;
var count = 1;
var total = 0;
var an = 1;
var rt = 1;
var ids = new Array();
var p_page = 0;
var page = 0;
var cat_id = 60;
var sproduct_name;
var slast;
var total_cp = 0;

//$('#opModal').bind().on('click','a',function(){var pg=$.url($(this).attr("href")).param("per_page");
</script>
</body>
</html>

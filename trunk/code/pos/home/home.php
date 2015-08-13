<?php
	class Home extends App_config{
		function __construct(){
			parent::__construct();
		}
		/* This function is automatically called when we come on Home Module */
		public function index(){
			/*Check store id in POST data and if found unset existing session */
			if(array_key_exists('store_id', $_POST)){
				unset($_SESSION['user']['store']);
				$_SESSION['user']['store']['name'] = $_POST['store_name'];
				$_SESSION['user']['store']['id'] = $_POST['store_id'];
				$_SESSION['user']['store']['code'] = $_POST['store_code'];
				$_SESSION['user']['store']['bill_type'] = $_POST['bill_type'];
				$_SESSION['user']['store']['store_message'] = $_POST['store_message'];
				$_SESSION['user']['store']['tin_no'] = $_POST['tin_no'];
				$_SESSION['user']['store']['stn_no'] = $_POST['stn_no'];
				$data = array();
				$data = $_SESSION;
				/* save session data in couchdb   */
				$getRev = $this->cDB->getDocs($this->getCDate());
				if(!array_key_exists('error', $getRev)){
					$data['_rev'] = $getRev['_rev'];
				}
				$data['_id'] = $this->getCDate();
				$this->cDB->saveDocument()->execute($data);
			}
			/* get shift data  */
			$result = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_SHIFT)->setParam(array("startkey" => '["'.$this->getCDate().'","'.$_SESSION['user']['store']['id'].'"]', "endkey" => '["'.$this->getCDate().'","'.$_SESSION['user']['store']['id'].'"]','include_docs'=>'true'))->execute();
			if(!array_key_exists('rows', $result)){
				header("LOCATION:index.php?error=true");
				die;
			}
			$data = array();
			$data['is_store_open'] = 'false';
			$data['is_shift_running'] = 'false';
			$totalShifts = 0;
			/* Check for shift no */
			if(count($result['rows']) == 1){
				$totalShifts = count($result['rows'][0]['doc']['shift']);
				/* Check if shift end or not */
				if(empty($result['rows'][0]['doc']['day']['end_time'])){
					$data['is_store_open'] = 'true';
					if($totalShifts != 0 && empty($result['rows'][0]['doc']['shift'][$totalShifts-1]['end_time'])){
						$data['is_shift_running'] = 'true';
						$_SESSION['user']['shift'] = $result['rows'][0]['doc']['shift'][$totalShifts-1]['shift_no'];
						$_SESSION['user']['counter'] = $result['rows'][0]['doc']['shift'][$totalShifts-1]['counter_no'];
						$data['shift_starter'] = $result['rows'][0]['doc']['shift'][$totalShifts-1]['start_staff_name'];
					}
				}
			}
			
			$data['shift_data'] = $result;
			$data['total_shift'] = $totalShifts;
			/*$configHead = $this->getConfig($this->cDB, 'head');
			$data['head_data'] = $configHead['data']['head'];*/
			$data['shift'] = '';
			$data['reconcilation'] = '';
			
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view($data);
			
			require_once DIR.'/login/login.php';
			$login = new Login();
			$login->form_login();
			
			require_once DIR.'/utils/utils.php';
			$utils = new Utils();
			$utils->generate_rep_running_flag();
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');
			
		} 
		
		function report(){
			$data = array();
			require_once DIR.'/sales_register/sales_register.php';
			$sr = new sales_register();
			$data = $sr->getBills($this->getCDate(), $this->getCDate());
			//print_r($data);die();
			$data['last_ppc_bill'] = $sr->getLastPpcBill($this->getCDate());
			$data['card_load_data'] = $sr->getCardLoadSale($this->getCDate());
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->commonView('menu');
			$this->view($data);
			$this->commonView('operation');
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}
		
		/*Function to show ppc screen  */
		function ppc(){
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->commonView('menu');
			$this->view();
			$this->commonView('operation');
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}
		
		/*Function to show ppa screen  */
		function ppa(){
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->commonView('menu');
			$this->view();
			$this->commonView('operation');
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}
		
		/* Function To Get Shift Data And Cash Reconciliation */
		function reconcilation(){
			$date = $this->getCDate();
			if(array_key_exists('date', $_POST)){
				$date = date('Y-m-d',strtotime($_POST['date']));		
			}
			/*Check session  */
			if(empty($_SESSION['user']['store']['id'])){
				$result = $this->getSessionData();
				if($result['error']){
					$return['error'] = true;
					$return['message'] = $result['message'];
					return json_encode($return);
				}
			}
			$returnData = array('error'=>false,'message'=>'','data'=>array());
			$cash_reconciliation_insert = array();
			$cash_reconciliation_insert['type'] = 'cash_reconciliation';
			$cash_in_box = 0;
			$excess_in_box = 0;
			$minus = 0;
			require_once DIR.'/sales_register/sales_register.php';
			$sr = new sales_register();
			/* get Bill data  */
			$sales_reg = $sr->getBills($date, $date);
			$shift_inward = 0;
			$shift_expense = 0;
			
			/* check store shift open or not  */
			if(array_key_exists('shift', $_SESSION['user'])){
				$resultInward = $this->cDB->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_INWARD)->setParam(array("include_docs"=>"true", "descending"=>"true", "startkey" => '["'.$date.'", "'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]',"endkey" => '["'.$date.'","'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]'))->execute();
				if(count($resultInward['rows'])>0){
					foreach($resultInward['rows'] as $key => $value){
						if($value['doc']['shift_no'] == $_SESSION['user']['shift']){
							$shift_inward += $value['doc']['inward_amount'];
						}

					}
				}
				/* Get Expense Data  */
				$resultExpense = $this->cDB->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_EXPENSE)->setParam(array("include_docs"=>"true","descending"=>"true","startkey" => '["'.$date.'", "'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]',"endkey" => '["'.$date.'","'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]'))->execute();
				if(count($resultExpense['rows'])>0){
					foreach($resultExpense['rows'] as $key => $value){
						if($value['doc']['shift_no'] == $_SESSION['user']['shift']){
							$shift_expense += $value['doc']['expense_amount'];
						}

					}
				}
			}
			/* Get Shift Data  */
			$shift_data = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_SHIFT)->setParam(array("startkey" => '["'.$date.'","'.$_SESSION['user']['store']['id'].'"]', "endkey" => '["'.$date.'","'.$_SESSION['user']['store']['id'].'"]','include_docs'=>'true'))->execute();
			/* Get ppa and ppc sale  */
			$card_sale = $this->cDB->getDesign(CARD_SALE_DESIGN_DOCUMENT)->getList(CARD_SALE_DESIGN_DOCUMENT_LIST_TODAYS_SALE, CARD_SALE_DESIGN_DOCUMENT_VIEW_GET_SALE)->setParam(array("include_docs"=>"true", "startkey" => '["'.$date.'", "'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]',"endkey" => '["'.$date.'","'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]'))->execute();
			$excess = "";
			$tablesShiftData = '<div class="panel panel-success">
									<div class="panel-heading">
										<h4 class="panel-title">Shift Data</h4>
									</div>
									<div class="panel-body">
										<table class="table table-hover table-condensed table-bordered">
											<thead>
												<tr>
													<th style="font-size:11px">Event</th>
													<th style="font-size:11px">Petty Cash</th>
													<th style="font-size:11px">Petty Cash Inward</th>
													<th style="font-size:11px">Petty Cash Expense</th>
													<th style="font-size:11px">Shift End Petty Cash</th>
													<th style="font-size:11px">Shift End (Cash in the box)</th>
													<th style="font-size:11px;background-color:#D0D0D0">Expected Closing Petty  Cash</th>
													<th style="font-size:11px;background-color:#D0D0D0">Petty Cash  Variance</th>
													<th style="font-size:11px;background-color:#D0D0D0">Expected Cash in the box</th>
													<th style="font-size:11px;background-color:#D0D0D0">Sales Cash Variance</th>
												</tr>
											</thead>
    										<tbody>';
		   	$total = 0;
			/*check shift data  */
    		if(count($shift_data['rows'])){
				$shifts = $shift_data['rows'][0]['doc']['shift'];
				$day = $shift_data['rows'][0]['doc']['day'];
				$shift_end_cash = ($day['start_cash']+$day['petty_cash_balance']['inward_petty_cash']-$day['petty_cash_balance']['petty_expense']);
				$tablesShiftData .='<tr><td style="font-size:9px">DAY START</td>
			    	<td class="text-center">'.$day['start_cash'].'</td>
			    	<td class="text-center">'.$day['petty_cash_balance']['inward_petty_cash'].'</td>
			    	<td class="text-center">'.$day['petty_cash_balance']['petty_expense'].'</td>
			    	<td class="text-center"></td>
			    	<td class="text-center"></td>
			    	<td class="text-center"></td>
			    	<td class="text-center"></td>
			    	<td class="text-center"></td>
					<td class="text-center"></td>
			    	</tr>'; 
			    	$shift_in = 0;
			    	$shift_ex = 0;
			   		$cashSum = 0;
				
				/*Iterate shift array to calculate CASH RECONCILATION  */
				foreach($shifts as $key => $values){
				    $inw = (empty($values['petty_cash_balance']['inward_petty_cash']) ? $shift_inward : $values['petty_cash_balance']['inward_petty_cash']);
				    $exp = (empty($values['petty_cash_balance']['petty_expense']) ? $shift_expense : $values['petty_cash_balance']['petty_expense']);

				    $shift_in += $inw;
				    $shift_ex += $exp;

					
					$closing_cash = $day['start_cash'] + $shift_in - $shift_ex;
					$saleCashVeriance = ($values['end_cash_inbox']-((array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0)+(array_key_exists($values['shift_no'], $card_sale['shift_cash']) ? $card_sale['shift_cash'][$values['shift_no']] : 0)));
					$pettyCashVeriance = ($values['end_petty_cash']-$closing_cash);
					$tablesShiftData .='<tr>
						<td style="font-size:9px">SHIFT '.$values['shift_no'].'</td>
						<td class="text-center"></td>
						<td class="text-center">'.$inw.'</td>
						<td class="text-center">'.$exp.'</td>
						<td class="text-center">'.$values['end_petty_cash'].'</td>
						<td class="text-center">'.$values['end_cash_inbox'].'</td>
						<td class="text-center" style="background-color:#D0D0D0">'.$closing_cash.'</td>
						<td class="text-center" style="background-color:#D0D0D0">'.$pettyCashVeriance.'</td>
						<td class="text-center" style="background-color:#D0D0D0">'.((array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0) + (array_key_exists($values['shift_no'], $card_sale['shift_cash']) ? $card_sale['shift_cash'][$values['shift_no']] : 0)).'</td>
						<td class="text-center" style="background-color:#D0D0D0">'.$saleCashVeriance.'</td>
						</tr>';
						$cashSum += (array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0);
						$cashSum += (array_key_exists($values['shift_no'], $card_sale['shift_cash']) ? $card_sale['shift_cash'][$values['shift_no']] : 0);
						$excess .= '<tr><td style="font-size:9px">SHIFT '.$values['shift_no'].' EXCESS CASH</td><td class="text-center">'.($values['end_cash_inbox']!='' ? $saleCashVeriance + $pettyCashVeriance : 0).'</td></tr>';
						$cash_reconciliation_insert['shift_'.$values['shift_no'].'_excess_cash'] = ($values['end_cash_inbox']!='' ? $saleCashVeriance + $pettyCashVeriance : 0);
						$excess_in_box	+= $cash_reconciliation_insert['shift_'.$values['shift_no'].'_excess_cash'];
				}
				/* if shift ended than calculate end_petty_cash, end_fullcash   */
				if(!empty($shift_data['rows'][0]['doc']['day']['end_time'])){
					$tablesShiftData .='<tr><td style="font-size:9px">DAY END</td>
				    	<td class="text-center"></td>
				    	<td class="text-center"></td>
				    	<td class="text-center"></td>
				    	<td class="text-center">'.$values['end_petty_cash'].'</td>
				    	<td class="text-center">'.$day['end_fullcash'].'</td>
				    	<td class="text-center" style="background-color:#428BCA">'.$closing_cash.'</td>
				    	<td class="text-center" style="background-color:#428BCA">'.($values['end_petty_cash']-$closing_cash).'</td>
				    	<td class="text-center" style="background-color:#428BCA">'.$cashSum.'</td>
						<td class="text-center" style="background-color:#428BCA">'.($day['end_fullcash']-$cashSum).'</td>
				    	</tr>';
				}
    		}
			$tablesShiftData .='</tbody></table></div></div>';
			$returnData['data']['shift_table'] = $tablesShiftData;
			
			$cash_reconciliation_table = '<div class="panel panel-success"><div class="panel-heading"><h4 class="panel-title">Cash Reconciliation</h4></div><div class="panel-body"><table class="table table-condensed table-bordered"><thead><tr><th style="font-size:12px;text-align:left;">Description</th><th style="font-size:12px">Value</th></tr></thead><tbody>';
    		/* calculate total cash according to payment type  */
			foreach($sales_reg['payment_type']['amount'] as $pKey => $pValue){
				$cash_reconciliation_insert[$pKey] = $pValue;
	    		$cash_reconciliation_table .= '<tr><td style="font-size:9px">'.strtoupper($pKey).'</td><td class="text-center">'.$pValue.'</td></tr>';
				$total += $pValue;
				$minus = ($pKey=='ppa' ? $minus + $pValue :
	    					      ($pKey=='ppc' ? $minus + $pValue : 
	    					      	($pKey=='credit' ? $minus + $pValue : 
	    					      	  ($pKey=='Online-Others' ? $minus + $pValue : 
	    						        ($pKey=='caw' ? $minus + $pValue : $minus)))));
	    	}
	    	/* calculate total according to card sale */
	    	if(is_array($card_sale) && count($card_sale)>0){ 
	    		foreach ($card_sale as $key => $value) {
	    			if($key!='shift_cash'){
	    				$cash_reconciliation_insert[$key] = $value;
	    		    	$cash_reconciliation_table .= '<tr><td style="font-size:9px">'.strtoupper($key).'</td><td class="text-center">'.$value.'</td></tr>';
	    				$total += $value;
	    				$minus = ($key=='ppaActive' ? $minus + $value :
	    					    	  ($key=='ppcActive' ? $minus + $value : $minus));
	    			}
	    		}
	    	}
	    	$total += $excess_in_box; // To Cash
	    	$cash_in_box = $total - $minus; // To Calculate Cash IN BOX 
    		$cash_reconciliation_insert['cash_in_box'] = $cash_in_box;
    		$cash_reconciliation_table .= $excess;
    		$cash_reconciliation_table .= '</tbody><thead><tr><th style="font-size:12px;text-align:left">Total Sale</th><th>'.($total).'</th></tr></thead><thead><tr><th style="font-size:12px;text-align:left">Cash In Box</th><th>'.($cash_in_box).'</th></tr></thead></table></div></div>';
    		/* Insert Cash-Reconcilation in couchdb  */
    		if(!$returnData['error'] && array_key_exists(0, $shift_data['rows'])){ 
				$result = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getUpdate(STORE_DESIGN_DOCUMENT_UPDATE_STORE_SHIFT,$shift_data['rows'][0]['id'])->setParam($cash_reconciliation_insert)->execute();
    		}
			$returnData['data']['cash_reconciliation_table'] = $cash_reconciliation_table;
			
			return $returnData;
		
		}
}
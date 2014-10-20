<?php
	class Home extends App_config{
		function __construct(){
			parent::__construct();
		}
		public function index(){
			$result = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_SHIFT)->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
			if(!array_key_exists('rows', $result)){
				header("LOCATION:index.php?error=true");
				die;
			}			
			$data = array();
			require_once DIR.'/sales_register/sales_register.php';
			require_once DIR.'/billing/billing.php';
			require_once DIR.'/staff/staff.php';
			$sr = new sales_register();
			$bl = new billing();
			$st = new Staff();

			$data = $sr->getBills($this->getCDate());
			$data['is_store_open'] = 'false';
			$data['is_shift_running'] = 'false';
			$totalShifts = 0;
			if(count($result['rows']) == 1){
				$totalShifts = count($result['rows'][0]['doc']['shift']);
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
			$todaysale = json_decode($sr->getTodaysSale(),true);
			$data['payment_sum'] = (!$todaysale['error']) ? $todaysale['data'] : array();
			$data['shift_data'] = $result;
			$data['staff_list'] = $st->getStaffList();
			$data['total_shift'] = $totalShifts;
			$configHead = $this->getConfig($this->cDB, 'head');
			$data['head_data'] = $configHead['data']['head'];
			$data['expense_data'] = $sr->getExpenseData($this->getCDate());
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
			
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		} 
		/* Function To Get Shift Data And Cash Reconciliation */
		function getShiftAndCashRe(){
			$date = $this->getCDate();
			if(array_key_exists('date', $_POST)){
				$date = date('Y-m-d',strtotime($_POST['date']));		
			}
			$returnData = array('error'=>false,'message'=>'','data'=>array());
			$cash_reconciliation_insert = array();
			$cash_reconciliation_insert['type'] = 'cash_reconciliation';
			require_once DIR.'/billing/billing.php';
			$bl = new billing();
			require_once DIR.'/sales_register/sales_register.php';
			$sr = new sales_register();

			$todaysale = json_decode($sr->getTodaysSale($date),true);
			$sales_reg = $sr->getBills($date);

			$shift_inward = 0;
			$shift_expense = 0;
			if(array_key_exists('shift', $_SESSION['user'])){
				$resultInward = $this->cDB->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_INWARD)->setParam(array('key'=>'"'.$date.'"','include_docs'=>'true'))->execute();
				if(count($resultInward['rows'])>0){
					foreach($resultInward['rows'] as $key => $value){
						if($value['doc']['shift_no'] == $_SESSION['user']['shift']){
							$shift_inward += $value['doc']['inward_amount'];
						}

					}
				}

				$resultExpense = $this->cDB->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_EXPENSE)->setParam(array('key'=>'"'.$date.'"','include_docs'=>'true'))->execute();
				if(count($resultExpense['rows'])>0){
					foreach($resultExpense['rows'] as $key => $value){
						if($value['doc']['shift_no'] == $_SESSION['user']['shift']){
							$shift_expense += $value['doc']['expense_amount'];
						}

					}
				}
			}

			$shift_data = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_SHIFT)->setParam(array('key'=>'"'.$date.'"','include_docs'=>'true'))->execute();
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
													<th style="font-size:11px;background-color:#428BCA">Expected Closing Petty  Cash</th>
													<th style="font-size:11px;background-color:#428BCA">Petty Cash  Variance</th>
													<th style="font-size:11px;background-color:#428BCA">Expected Cash in the box</th>
													<th style="font-size:11px;background-color:#428BCA">Sales Cash Variance</th>
												</tr>
											</thead>
    										<tbody>';
		   	$total = 0;
    		if(count($shift_data['rows'])){
				$shifts = $shift_data['rows'][0]['doc']['shift'];
				//$total = count($shifts);
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
			   
				foreach($shifts as $key => $values){
				    $inw = (empty($values['petty_cash_balance']['inward_petty_cash']) ? $shift_inward : $values['petty_cash_balance']['inward_petty_cash']);
				    $exp = (empty($values['petty_cash_balance']['petty_expense']) ? $shift_expense : $values['petty_cash_balance']['petty_expense']);

				    $shift_in += $inw;
				    $shift_ex += $exp;

					
					$closing_cash = $day['start_cash'] + $shift_in - $shift_ex;
					$saleCashVeriance = ($values['end_cash_inbox']-(array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0));
					$pettyCashVeriance = ($values['end_petty_cash']-$closing_cash);
					$tablesShiftData .='<tr>
						<td style="font-size:9px">SHIFT '.$values['shift_no'].'</td>
						<td class="text-center"></td>
						<td class="text-center">'.$inw.'</td>
						<td class="text-center">'.$exp.'</td>
						<td class="text-center">'.$values['end_petty_cash'].'</td>
						<td class="text-center">'.$values['end_cash_inbox'].'</td>
						<td class="text-center" style="background-color:#428BCA">'.$closing_cash.'</td>
						<td class="text-center" style="background-color:#428BCA">'.$pettyCashVeriance.'</td>
						<td class="text-center" style="background-color:#428BCA">'.(array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0).'</td>
						<td class="text-center" style="background-color:#428BCA">'.$saleCashVeriance.'</td>
						</tr>';
						$cashSum += (array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0);
						$excess .= '<tr><td style="font-size:9px">SHIFT '.$values['shift_no'].' EXCESS CASH</td><td class="text-center">'.(($saleCashVeriance > 0 ? $saleCashVeriance : 0)+($pettyCashVeriance >0 ? $pettyCashVeriance :0 )).'</td></tr>';
						$cash_reconciliation_insert['shift_'.$values['shift_no'].'_excess_cash'] = (($saleCashVeriance > 0 ? $saleCashVeriance : 0)+($pettyCashVeriance >0 ? $pettyCashVeriance : 0 ));
						$total	+= $cash_reconciliation_insert['shift_'.$values['shift_no'].'_excess_cash'];
				}
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
    		//print_r($cash_reconciliation_insert);
    		
    		foreach($sales_reg['payment_type']['amount'] as $pKey => $pValue){
				$cash_reconciliation_insert[$pKey] = $pValue;
	    		$cash_reconciliation_table .= '<tr><td style="font-size:9px">'.strtoupper($pKey).'</td><td class="text-center">'.$pValue.'</td></tr>';
	    		$total += $pValue;
    		}
    		$cash_in_box = $total;
    		foreach ($sales_reg['payment_type']['amount'] as $pKey => $pValue) {
    			$cash_in_box = ($pKey=='caw' ? $cash_in_box - $pValue : ($pKey=='ppc' ? $cash_in_box - $pValue : ($pKey=='ppa' ? $cash_in_box - $pValue : $cash_in_box)));
    		}
    		$cash_reconciliation_insert['cash_in_box'] = $cash_in_box;
    		$cash_reconciliation_table .= $excess;
    		$cash_reconciliation_table .= '</tbody><thead><tr><th style="font-size:12px;text-align:left">Total Sale</th><th>'.($total).'</th></tr></thead><thead><tr><th style="font-size:12px;text-align:left">Cash In Box</th><th>'.($cash_in_box).'</th></tr></thead></table></div></div>';
    		
    		if(!$returnData['error'] && array_key_exists(0, $shift_data['rows']) && array_key_exists('date', $_POST)){ 
				$result = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getUpdate(STORE_DESIGN_DOCUMENT_UPDATE_STORE_SHIFT,$shift_data['rows'][0]['id'])->setParam($cash_reconciliation_insert)->execute();
    		}
			$returnData['data']['cash_reconciliation_table'] = $cash_reconciliation_table;
			return $returnData;
		}
}
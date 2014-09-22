<?php
	class Home extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		public function index(){
			$result = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
			$data = array();
			
			require_once DIR.'/sales_register/sales_register.php';
			require_once DIR.'/billing/billing.php';
			$sr = new sales_register();
			$bl = new billing();

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
			$todaysale = json_decode($bl->getTodaysSale(),true);
			$data['payment_sum'] = (!$todaysale['error']) ? $todaysale['data'] : array();
			$data['shift_data'] = $result;
			$data['staff_list'] = $sr->getStaffList();
			$data['total_shift'] = $totalShifts;
			$configHead = $this->getConfig($this->cDB, 'head');
			$data['head_data'] = $configHead['data']['head'];
			$data['expense_data'] = $sr->getExpenseData($this->getCDate());
			$data['shift'] = '';
			$data['reconcilation'] = '';
			$data['is_login_allowed'] = 'true';
			if($_SESSION['user']['title']['id'] == 2 || $_SESSION['user']['title']['id'] == 6){
				$returned = $this->getShiftAndCashRe();
				$data['shift'] = $returned['data']['shift_table'];
				$data['reconcilation'] = $returned['data']['cash_reconciliation_table'];
				$data['is_login_allowed'] = 'false';
			}
			//print_r($data['payment_sum']);
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view($data);			
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		} 
		function getShiftAndCashRe(){
			$returnData = array('error'=>false,'message'=>'','data'=>array());
			require_once DIR.'/billing/billing.php';
			$bl = new billing();
			require_once DIR.'/sales_register/sales_register.php';
			$sr = new sales_register();

			$todaysale = json_decode($bl->getTodaysSale(),true);
			$sales_reg = $sr->getBills($this->getCDate());

			$shift_inward = 0;
			$shift_expense = 0;
			if(array_key_exists('shift', $_SESSION['user'])){
				$resultInward = $this->cDB->getDesign('petty_expense')->getView('get_inward')->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
				if(count($resultInward['rows'])>0){
					foreach($resultInward['rows'] as $key => $value){
						if($value['doc']['shift_no'] == $_SESSION['user']['shift']){
							$shift_inward += $value['doc']['inward_amount'];
						}

					}
				}

				$resultExpense = $this->cDB->getDesign('petty_expense')->getView('get_expense')->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
				if(count($resultExpense['rows'])>0){
					foreach($resultExpense['rows'] as $key => $value){
						if($value['doc']['shift_no'] == $_SESSION['user']['shift']){
							$shift_expense += $value['doc']['expense_amount'];
						}

					}
				}
			}

			$shift_data = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
			$excess = "";
			$tablesShiftData = '<div class="panel panel-success"><div class="panel-heading"><h4 class="panel-title">Shift Data</h4></div><div class="panel-body"><table class="table">
					<thead><tr><th>Event</th><th>Petty Cash</th><th>Petty Cash Inward</th><th>Petty Cash Expense</th><th>Shift End Petty Cash</th><th>Shift End (Cash in the box)</th><th>Expected Closing Petty  Cash</th><th>Petty Cash  Variance</th><th>Expected Cash in the box</th><th>Sales Cash Variance</th></tr></thead>
    				<tbody>';
    		if(count($shift_data['rows'])){
				$shifts = $shift_data['rows'][0]['doc']['shift'];
				$total = count($shifts);
				$day = $shift_data['rows'][0]['doc']['day'];
				$shift_end_cash = ($day['start_cash']+$day['petty_cash_balance']['inward_petty_cash']-$day['petty_cash_balance']['petty_expense']);
				$tablesShiftData .='<tr><td>DAY START</td>
			    	<td class="text-center">'.$day['start_cash'].'</td>
			    	<td class="text-center">'.$day['petty_cash_balance']['inward_petty_cash'].'</td>
			    	<td class="text-center">'.$day['petty_cash_balance']['petty_expense'].'</td>
			    	<td class="text-center"></td>
			    	<td class="text-center"></td>
			    	<td class="text-center">'.$shift_end_cash.'</td>
			    	<td class="text-center">'.(-$shift_end_cash).'</td>
			    	<td class="text-center"></td>
					<td class="text-center"></td>
			    	</tr>';
			    	$shift_in = 0;
			    	$shift_ex = 0;
			   	$allow_day_end_block = false;
			   	$cashSum = 0;
				foreach($shifts as $key => $values){
				   	$allow_day_end_block = true;
				    $inw = (empty($values['petty_cash_balance']['inward_petty_cash']) ? $shift_inward : $values['petty_cash_balance']['inward_petty_cash']);
				    $exp = (empty($values['petty_cash_balance']['petty_expense']) ? $shift_expense : $values['petty_cash_balance']['petty_expense']);

				    $shift_in += $inw;
				    $shift_ex += $exp;

					$excess .= '<tr><td>SHIFT '.$values['shift_no'].' EXCESS CASH</td><td>'.($values['petty_cash_balance']['closing_petty_cash'] - $values['end_petty_cash']).'</td></tr>';
					
					$closing_cash = $day['start_cash'] + $shift_in - $shift_ex;
					
					$tablesShiftData .='<tr>
						<td>SHIFT '.$values['shift_no'].'</td>
						<td class="text-center"></td>
						<td class="text-center">'.$inw.'</td>
						<td class="text-center">'.$exp.'</td>
						<td class="text-center">'.$values['end_petty_cash'].'</td>
						<td class="text-center">'.$values['end_cash_inbox'].'</td>
						<td class="text-center">'.$closing_cash.'</td>
						<td class="text-center">'.($values['end_petty_cash']-$closing_cash).'</td>
						<td class="text-center">'.(array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0).'</td>
						<td class="text-center">'.($values['end_cash_inbox']-(array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0)).'</td>
						</tr>';
						$cashSum += (array_key_exists($values['shift_no'], $sales_reg['shift_cash']) ? $sales_reg['shift_cash'][$values['shift_no']] : 0);
				}
				if($allow_day_end_block){
					$tablesShiftData .='<tr><td>DAY END</td>
				    	<td class="text-center"></td>
				    	<td class="text-center"></td>
				    	<td class="text-center"></td>
				    	<td class="text-center">'.$values['end_petty_cash'].'</td>
				    	<td class="text-center">'.$day['end_fullcash'].'</td>
				    	<td class="text-center">'.$closing_cash.'</td>
				    	<td class="text-center">'.($values['end_petty_cash']-$closing_cash).'</td>
				    	<td class="text-center">'.$cashSum.'</td>
						<td class="text-center">'.($day['end_fullcash']-$cashSum).'</td>
				    	</tr>';
				}
    		}
			$tablesShiftData .='</tbody></table></div></div>';
			$returnData['data']['shift_table'] = $tablesShiftData;
			$cash_reconciliation_table = '<div class="panel panel-success"><div class="panel-heading"><h4 class="panel-title">Cash Reconciliation</h4></div><div class="panel-body"><table class="table"><tbody>';
    		foreach($sales_reg['payment_type']['amount'] as $pKey => $pValue){
	    		$cash_reconciliation_table .= '<tr><td>'.$pKey.'</td><td class="text-center">'.$pValue.'</td></tr>';
    		}
    		$cash_reconciliation_table .= $excess;
    		$cash_reconciliation_table .= '</tbody></table></div></div>';
			$returnData['data']['cash_reconciliation_table'] = $cash_reconciliation_table;
			return $returnData;
		}
}
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
			if($_SESSION['user']['title']['id'] == 4 || $_SESSION['user']['title']['id'] == 6){
				$returned = $this->getShiftAndCashRe();
				$data['shift'] = $returned['data']['shift_table'];
				$data['reconcilation'] = $returned['data']['cash_reconciliation_table'];
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

			$shift_data = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
			$excess = "";
			$tablesShiftData = '<div class="panel panel-success"><div class="panel-heading"><h4 class="panel-title">Shift Data</h4></div><div class="panel-body"><table class="table">
					<thead><tr><th>Event</th><th>Petty Cash</th><th>Petty Cash Inward</th><th> Petty Cash Variance</th><th>Shift End Petty Cash</th><th>Shift End (Cash in the box)</th><th>Expected Closing Cash</th><th>Excess Cash</th><th>Expected Cash in the Box</th><th>Sales Cash Variance</th></tr></thead>
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
			    	<td class="text-center">0</td>
			    	<td class="text-center">0</td>
			    	<td class="text-center">'.$shift_end_cash.'</td>
			    	<td class="text-center">'.(-$shift_end_cash).'</td>
			    	<td class="text-center">'.$day['end_fullcash'].'</td>
					<td class="text-center">'.($day['end_fullcash']).'</td>
			    	</tr>';
				foreach($shifts as $key => $values){
					$excess .= '<tr><td>SHIFT '.$values['shift_no'].' EXCESS CASH</td><td>'.($values['petty_cash_balance']['closing_petty_cash'] - $values['end_petty_cash']).'</td></tr>';
					$closing_cash = (( $values['petty_cash_balance']['opening_petty_cash'])
				    	+($day['petty_cash_balance']['inward_petty_cash'] + $values['petty_cash_balance']['inward_petty_cash']) 
						-($day['petty_cash_balance']['petty_expense'] + $values['petty_cash_balance']['petty_expense']) 
				    );
					$tablesShiftData .='<tr>
						<td>SHIFT '.$values['shift_no'].'</td>
						<td class="text-center">'.$values['petty_cash_balance']['opening_petty_cash'].'</td>
						<td class="text-center">'.$values['petty_cash_balance']['inward_petty_cash'].'</td>
						<td class="text-center">'.$values['petty_cash_balance']['petty_expense'].'</td>
						<td class="text-center">'.$values['end_petty_cash'].'</td>
						<td class="text-center">'.$values['end_cash_inbox'].'</td>
						<td class="text-center">'.$closing_cash.'</td>
						<td class="text-center">'.($values['end_petty_cash']-$closing_cash).'</td>
						<td class="text-center">'.$values['end_cash_inbox'].'</td>
						<td class="text-center">'.($values['end_cash_inbox']-$values['end_cash_inbox']).'</td>
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
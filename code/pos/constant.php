<?php
/* Constant For Design Document  */
define ('BILLING_DESIGN_DOCUMENT', 'billing');
define ('STORE_DESIGN_DOCUMENT', 'store');
define ('DESIGN_HO_DESIGN_DOCUMENT', 'design_ho');
define ('CUSTOMERS_DESIGN_DOCUMENT', 'customers');
define ('PETTY_EXPENSE_DESIGN_DOCUMENT', 'petty_expense');
define ('STAFF_DESIGN_DOCUMENT', 'staff');
define ('LOGIN_DESIGN_DOCUMENT', 'login');
define ('CARD_SALE_DESIGN_DOCUMENT', 'card_sale');
define ('PPC_DETAIL_DESIGN_DOCUMENT', 'ppc_detail');


/* Constant For Design Document View */
define ('BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS', 'handle_updated_bills');
define ('BILLING_DESIGN_DOCUMENT_VIEW_BILL_BY_ORDER', 'bill_by_order');
define ('BILLING_DESIGN_DOCUMENT_VIEW_BILL_BY_STORE_COUNTER', 'bill_by_store_counter');
define ('STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID', 'store_mysql_id');
define ('STORE_DESIGN_DOCUMENT_VIEW_STORE_SHIFT', 'store_shift');
define ('DESIGN_HO_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST', 'retail_customer_list');
define ('DESIGN_HO_DESIGN_DOCUMENT_VIEW_CONFIG_LIST', 'config_list');
define ('DESIGN_HO_DESIGN_DOCUMENT_VIEW_STORE_BY_MYSQL_ID', 'store_by_mysql_id');
define ('DESIGN_HO_DESIGN_DOCUMENT_VIEW_STAFF_BY_MYSQL_ID', 'staff_by_mysql_id');
//define ('DESIGN_HO_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST', 'retail_customer_list');
define ('CUSTOMERS_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST', 'retail_customer_list');
define ('PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_EXPENSE', 'get_expense');
define ('PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_INWARD', 'get_inward');
define ('STAFF_DESIGN_DOCUMENT_VIEW_STAFF_USERNAME', 'staff_username');
define ('STAFF_DESIGN_DOCUMENT_VIEW_STAFF_NAME', 'staff_name');

define ('CARD_SALE_DESIGN_DOCUMENT_VIEW_GET_SALE', 'get_sale');
define ('PPC_DETAIL_DESIGN_DOCUMENT_VIEW_INITIALIZE_DETAIL', 'initialize_detail');
define ('PPC_DETAIL_DESIGN_DOCUMENT_VIEW_LAST_BILL', 'last_bill');

/* Constant For Design Document Update */
define ('BILLING_DESIGN_DOCUMENT_UPDATE_GET_BILL_NO', 'getbillno');
define ('PPC_DETAIL_DESIGN_DOCUMENT_UPDATE_GET_BILL_NO', 'getbillno');
define ('PPC_DETAIL_DESIGN_DOCUMENT_UPDATE_CHANGE_STATUS', 'change_status');
define ('STORE_DESIGN_DOCUMENT_UPDATE_STORE_SHIFT', 'store_shift');
define ('LOGIN_DESIGN_DOCUMENT_UPDATE_LOGIN_HISTORY', 'login_history');


/* Constant For Design Document List */
define ('BILLING_DESIGN_DOCUMENT_LIST_TODAYS_SALE', 'todays_sale');
define ('BILLING_DESIGN_DOCUMENT_LIST_CAW_SALE', 'caw_sale');
define ('BILLING_DESIGN_DOCUMENT_LIST_SALES_REGISTER', 'sales_register');
define ('STAFF_DESIGN_DOCUMENT_LIST_GET_USER', 'getuser');
define ('CARD_SALE_DESIGN_DOCUMENT_LIST_TODAYS_SALE', 'todays_sale');

/* Constant For Replicate Design Document */
define ('STORE_REPLICATE', "doc_replication/store_replication");
define ('STAFF_REPLICATE', "doc_replication/staff_replication");
define ('CONFIGURATION_REPLICATE', "doc_replication/config_replication");
define ('RETAIL_CUSTOMER_REPLICATE', "doc_replication/retail_customer_replication");
define ('DESIGN_REPLICATE', "doc_replication/design_replication");
define ('BILL_REPLICATE', "doc_replication/bill_replication");

/* Constant For CD Doc Type */
define ('STORE_MASTER_DOC_TYPE', 'store_master');
define ('BILLING_DOC_TYPE', 'store_bill');
define ('PETTY_EXPENSE_DOC_TYPE', 'petty_expense');
define ('PETTY_INWARD_DOC_TYPE', 'petty_inward');
define ('STORE_SHIFT_DOC_TYPE', 'store_shift');
define ('LOGIN_HISTORY_DOC_TYPE', 'login_history');
define ('CARD_SALE_DOC_TYPE', 'card_sale');
define ('LAST_INITIALIZE_DOC_TYPE', 'last_initialize');
define ('LAST_PPC__BILL_DOC_TYPE', 'last_ppc_bill');
define ('CONFIG_MASTER_DOC_TYPE', 'config_master');
define ('STAFF_MASTER_DOC_TYPE', 'staff_master');
define ('RETAIL_CUSTOMERS_DOC_TYPE', 'retail_customers');

/* Constant For Request Type */
define ('GET_RETAIL_CUSTOMER', 'getRetailCustomer');
define ('SAVE_BILL', 'save_bill');
define ('UPDATE_BILL', 'update_bill');
define ('GET_COC_ORDER', 'getCOCOrder');
define ('LOAD_PPA_CARD', 'load_ppa_card');
define ('LOAD_PPC_CARD', 'load_ppc_card');
define ('ACTIVATE_PPA_CARD', 'activate_ppa_card');
define ('ACTIVATE_PPC_CARD', 'activate_ppc_card');
define ('ISSUE_PPA_CARD', 'issue_ppa_card');
define ('ISSUE_PPC_CARD', 'issue_ppc_card');
define ('GET_CUSTOMER_INFO', 'get_customer_info');
define ('REISSUE_PPC_CARD', 'reissue_ppc_card');
define ('BALANCE_CHECK_PPC_CARD', 'balance_check_ppc_card');
define ('BALANCE_CHECK_PPA_CARD', 'balance_check_ppa_card');
define('CANCEL_REDEEM', 'cancel_redeem');
define('CANCEL_LOAD', 'cancel_load');
define('REWARD_REDEMPTION', 'reward_redemption');
define('REWARD_CHECK', 'reward_check');

/* Constant For Request Type Error */
define ('REQUEST_TYPE_NOT_ALLOWED', 'Request Type Not Found');
define ('REQUEST_METHOD_NOT_ALLOWED', 'Request Method Not Allowed');

/* Constant For Dispatch */

define ('LOGIN_DISPATCH', 'index.php?dispatch=store');

/* Other Constant */

define ('ERROR', 'Some Error! Please Contact Admin');
define ('REPLICATE_ERROR', 'Some Error! While Replicating Design Document');
define ('START_DAY_ERROR', 'Can not Start Store Day Again');
define ('START_DAY_SUCCESS', 'Welcome, Shift has been started, Please Start Sales. <a href="index.php?dispatch=billing" class="btn btn-sm btn-primary">Start Billing</a>');
define ('STORE_SHIFT_ENDED', 'Store Shift Ended');
define ('STORE_DAY_ENDED', 'Store Day Ended');
define ('SERVER_DOWN_ERROR', 'server_down');
define ('INTERNET_ERROR', 'Please Connect to Internet to View Dashboard');
define ('SUCCESS', 'Saved Successfully');
define ('NOT_ALLOWED_TO_ACCESS', 'You are not allowed to access.');
define ('HOME', 'home');
define ('NA', 'NA');
define ('LOGIN', 'login');
define ('SALE_REGISTER_PROTECTED_SCREEN', 'sales_register');
define ('DATA_SYNC_PROTECTED_SCREEN', 'data_sync');
define ('SHIFT_DATA_PROTECTED_SCREEN', 'shift_data_tab');
define ('ORDER_ALREADY_BILLED', 'Order Already Billed');
define ('EXE_PATH', 'D:\utility\printBill.exe');
define ('COMPANY_DETAIL_TXT_PATH', 'D:\utility\company.txt');
define ('BILL_DETAIL_TXT_PATH', 'D:\utility\bill.txt');
define ('CARD_SALE_TXT_PATH', 'D:\utility\card_sale.txt');
define ('BILL_DATA_MISSING', 'Bill_Data_Missing');
define ('PRINT_UTILITY_NOT_EXISTS', 'PRINT_UTILITY_NOT_EXISTS');
define ('RESOURCE', 'resource');
define ('CONFIRMED_MESSAGE', "Dear ".ucfirst(!empty($_POST['customer_name']) ? $_POST['customer_name'] : '').", Your Chai-On-Call Order # ".(!empty($_POST['order']) ? $_POST['order'] : '')." Is Confirmed. Thank you!");
define ('PROVIDER_NUMBER', '8808891988');
define ('DISPATCHED_MESSAGE', "Dear ".ucfirst(!empty($_POST['customer_name']) ? $_POST['customer_name'] : '').", Your Chai-On-Call Order # ".(!empty($_POST['order']) ? $_POST['order'] : '')." has been Dispatched From ".(!empty($_POST['store_name']) ? $_POST['store_name'] : '')." Store. Your bill amount is Rs ".(!empty($_POST['net_amount']) ? $_POST['net_amount'] : '').". Thank you!");
define ('PPA_REDEEM', 'redeem');
define ('PPC_REDEEM', 'redeem');
define ('PPA_LOAD', 'load');
define ('PPC_LOAD', 'load');
define ('PPA_ACTIVATE', 'activate');
define ('PPC_ACTIVATE', 'activate');
define ('PPA', 'ppa');
define ('PPC', 'ppc');
define('LOAD', 'load');
define('REDEEM', 'redeem');
define('ACTIVATE', 'activate');

define('BALANCE_CHECK', 'balance_check');
define('STAFF_LOCATION_NOT_FOUND', 'Provide Staff Location to replicate');
define('DATA_NOT_FOUND', 'No Data Found With Associated Store');
define('STAFF_DOWNLOADED', 'Staff Downloaded SuccessFully');
define('STORE_DOWNLOADED', 'Store Downloaded SuccessFully');
define('CONFIGURATION_DOWNLOADED', 'Configuration Downloaded SuccessFully');
define('CUSTOMER_DOWNLOADED', 'Customer Downloaded SuccessFully');
define('DESIGN_DOCUMENT_DOWNLOADED', 'Design Document Downloaded SuccessFully');
define('PROCESS_START', 'Process Start SuccessFully');
define('PROCESS_STOP', 'Process Stop SuccessFully');
define('ACTION_NOT_ALLOWED', 'Not allowed to follow this action');
define('SOURCE', 'source');
define('TARGET', 'target');
define('FILTER', 'filter');
define('QUERY_PARAMS', "'query_params'");
define('CONTINUOUS', "continuous");
define('CANCEL', "cancel");
define('OK', "ok");
define('PAID', "paid");
define('API_URL', 'http://54.178.189.25/cpos/api/coc/coc_api.php');
define('ACTIVITY_TRACKER_API_URL', 'http://cp-os.com/cpos/api/storeActivityTracker.php');
define ('INSERT_SUCCESS', 'RECORD INSERTED SUCCESSFULLY');
define ('UPDATE_SUCCESS', 'RECORD UPDATED SUCCESSFULLY');
define ('WALKIN', 'Walk-in');
define ('COC', 'COC');
define ('OLO', 'OLO');
define ('PREORDER', 'Pre-Order');
define ('CAW', 'CAW');
define ('NO_COUPON_FOUND', 'No Coupon Exists For Your Store');
define ('INVALID_COUPON', 'Enter Valid Coupan');
define ('NOT_FOUND', 'not_found');
define ('BILL_NOT_FOUND', 'Bill Not Found');
define ('LOCAL', 'L');
define ('CLOUD', 'C');
define ('LOCAL_BILLING_MODE', 'local');
define ('NO_CUSTOMER_ASSIGN', 'No Customer Assign');
define ('APPROVED_SCHEDULE_NOT_FOUND', 'Approved Schedule Not Found');
global $PAYMENT_MODE, $ORDER_STATUS, $CARD_RESPONSE_ARRAY, $ERT_PRODUCT_ARRAY;
$PAYMENT_MODE = array('cash'=>0,'ppc'=>0,'ppa'=>0,'caw'=>0,'credit'=>0);
$ORDER_STATUS = array('New'=>0,'Confirmed'=>0,'Cancelled'=>0,'Dispatched'=>0,'Delivered'=>0,'Paid'=>0);
$CARD_RESPONSE_ARRAY = array('success'=>'', 'message'=>'', 'balance'=>'', 'card_number'=>'', 'txn_no'=>'', 'approval_code'=>'', 'txn_type'=>'');
$ERT_PRODUCT_ARRAY = array('102'=>'37', '245'=>'43',
						 '334'=>'107', '120'=>'4', 
						 	'118'=>'2', '107'=>'45', 
						 		'117'=>'1', '332'=>'105', 
						 			'242'=>'35', '106'=>'42', 
						 				'247'=>'54', '333'=>'106', 
						 					'331'=>'104', '110'=>'48', 
						 						'108'=>'46', '246'=>'44', 
						 							'109'=>'47', '103'=>'39', 
						 								'114'=>'53',
						 									'119'=>'3',  
						 										'240'=>'34', '112'=>'51', 
						 											'105'=>'41', '335'=>'108', 
						 												'124'=>'6');


?>
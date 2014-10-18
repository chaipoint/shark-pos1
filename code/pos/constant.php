<?php
/* Constant For Design Document  */
define ('BILLING_DESIGN_DOCUMENT', 'billing');
define ('STORE_DESIGN_DOCUMENT', 'store');
define ('DESIGN_HO_DESIGN_DOCUMENT', 'design_ho');
define ('CUSTOMERS_DESIGN_DOCUMENT', 'customers');
define ('PETTY_EXPENSE_DESIGN_DOCUMENT', 'petty_expense');
define ('STAFF_DESIGN_DOCUMENT', 'staff');
define ('LOGIN_DESIGN_DOCUMENT', 'login');

/* Constant For Design Document View */
define ('BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS', 'handle_updated_bills');
define ('BILLING_DESIGN_DOCUMENT_VIEW_BILL_BY_ORDER', 'bill_by_order');
define ('STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID', 'store_mysql_id');
define ('STORE_DESIGN_DOCUMENT_VIEW_STORE_SHIFT', 'store_shift');
define ('DESIGN_HO_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST', 'retail_customer_list');
define ('CUSTOMERS_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST', 'retail_customer_list');
define ('PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_EXPENSE', 'get_expense');
define ('PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_INWARD', 'get_inward');
define ('STAFF_DESIGN_DOCUMENT_VIEW_STAFF_USERNAME', 'staff_username');

/* Constant For Design Document Update */
define ('BILLING_DESIGN_DOCUMENT_UPDATE_GET_BILL_NO', 'getbillno');
define ('STORE_DESIGN_DOCUMENT_UPDATE_STORE_SHIFT', 'store_shift');
define ('LOGIN_DESIGN_DOCUMENT_UPDATE_LOGIN_HISTORY', 'login_history');


/* Constant For Design Document List */
define ('BILLING_DESIGN_DOCUMENT_LIST_TODAYS_SALE', 'todays_sale');
define ('BILLING_DESIGN_DOCUMENT_LIST_SALES_REGISTER', 'sales_register');
define ('STAFF_DESIGN_DOCUMENT_LIST_GET_USER', 'getuser');

/* Constant For CD Doc Type */
define ('BILLING_DOC_TYPE', 'store_bill');
define ('PETTY_EXPENSE_DOC_TYPE', 'petty_expense');
define ('PETTY_INWARD_DOC_TYPE', 'petty_inward');
define ('STORE_SHIFT_DOC_TYPE', 'store_shift');
define ('LOGIN_HISTORY_DOC_TYPE', 'login_history');


/* Constant For Request Type */
define ('GET_RETAIL_CUSTOMER', 'getRetailCustomer');
define ('SAVE_BILL', 'save_bill');
define ('UPDATE_BILL', 'update_bill');
define ('GET_COC_ORDER', 'getCOCOrder');

/* Constant For Request Type Error */
define ('REQUEST_TYPE_NOT_ALLOWED', 'Request Type Not Found');
define ('REQUEST_METHOD_NOT_ALLOWED', 'Request Method Not Allowed');

/* Constant For Dispatch */

define ('LOGIN_DISPATCH', 'index.php?dispatch=home');

/* Other Constant */

define ('ERROR', 'Some Error! Please Contact Admin');
define ('REPLICATE_ERROR', 'Some Error! While Replicating Design Document');
define ('START_DAY_ERROR', 'Can not Start Store Day Again');
define ('START_DAY_SUCCESS', 'Welcome, Shift has been started, Please Start Sales. <a href="index.php?dispatch=billing" class="btn btn-sm btn-primary">Start Billing</a>');
define ('STORE_SHIFT_ENDED', 'Store Shift Ended');
define ('STORE_DAY_ENDED', 'Store Day Ended');
define ('SERVER_DOWN_ERROR', 'server_down');
define ('SUCCESS', 'Saved Successfully');
define ('NOT_ALLOWED_TO_ACCESS', 'You are not allowed to access.');
define ('HOME', 'home');
define ('NA', 'NA');
define ('LOGIN_VALIDATEFOR', 'login');
define ('SALE_REGISTER_VALIDATEFOR', 'sales_register');
define ('DATA_SYNC_VALIDATEFOR', 'data_sync');
define ('SHIFT_DATA_VALIDATEFOR', 'shift_data_tab');
define ('ORDER_ALREADY_BILLED', 'Order Already Billed');
define ('EXE_PATH', 'D:\utility\printBill.exe');
define ('COMPANY_DETAIL_TXT_PATH', 'D:\utility\company.txt');
define ('BILL_DETAIL_TXT_PATH', 'D:\utility\bill.txt');
define ('BILL_DATA_MISSING', 'Bill_Data_Missing');
define ('PRINT_UTILITY_NOT_EXISTS', 'PRINT_UTILITY_NOT_EXISTS');
define ('RESOURCE', 'resource');
define ('CONFIRMED_MESSAGE', "Dear ".ucfirst(!empty($_POST['customer_name']) ? $_POST['customer_name'] : '').", Your Chai-On-Call Order # ".(!empty($_POST['order']) ? $_POST['order'] : '')." Is Confirmed. Thank you!");
define ('PROVIDER_NUMBER', '8808891988');
define ('DISPATCHED_MESSAGE', "Dear ".ucfirst(!empty($_POST['customer_name']) ? $_POST['customer_name'] : '').", Your Chai-On-Call Order # ".(!empty($_POST['order']) ? $_POST['order'] : '')." has been Dispatched From ".(!empty($_POST['store_name']) ? $_POST['store_name'] : '')." Store. Your bill amount is Rs ".(!empty($_POST['net_amount']) ? $_POST['net_amount'] : '').". Thank you!");




global $PAYMENT_MODE, $ORDER_STATUS, $CARD_RESPONSE_ARRAY;
$PAYMENT_MODE = array('cash'=>0,'ppc'=>0,'credit'=>0,'ppa'=>0);
$ORDER_STATUS = array('New'=>0,'Confirmed'=>0,'Cancelled'=>0,'Dispatched'=>0,'Delivered'=>0,'Paid'=>0);
$CARD_RESPONSE_ARRAY = array('success'=>'', 'message'=>'', 'balance'=>'', 'card_number'=>'', 'txn_no'=>'');


?>
cls
echo "Creating Directory -POS"
mkdir c:\xampp\htdocs\pos
echo "Changing Directory -POS"
cd c:\xampp\htdocs\pos
d:
echo "Taking Checkout of POS"
svn checkout http://54.249.247.15/svn/vente/trunk/code/pos .
echo "Creating Utility Directory"
mkdir d:\utility
cd d:\utility
d:
echo "Taking Checkout of UTILITY"
svn checkout http://54.249.247.15/svn/vente/trunk/code/utility .
echo "Copying POS.ini"
copy c:\xampp\htdocs\pos\pos.ini c:\xampp\htdocs\.
c:
schtasks /create /sc minute /mo 120 /tn "upload_pos_data" /tr "C:\xampp\php\php.exe -f C:\xampp\htdocs\pos\utils\upload_pos_scheduler.php"
echo "Creating Couch Database -pos"

SchTasks /Create /SC ONSTART /TN “XAMPP STOP” /TR “C:\xampp\xampp_stop.exe” 

SchTasks /Create /SC ONSTART /TN “XAMPP START” /TR “C:\xampp\xampp_start.exe”

echo "DESIGN DOCUMENT"
START http://localhost:2020/pos/index.php?dispatch=utils.init
timeout /T 30

echo "CONFIG DOCUMENT"
START http://localhost:2020/pos/download/download.php?param=updateConfig
timeout /T 30

echo "STORE  DOCUMENT"
START http://localhost:2020/pos/download/download.php?param=updateStore-48
timeout /T 30

echo "STAFF DOCUMENT"
START http://localhost:2020/pos/download/download.php?param=updateStaff-2
timeout /T 30

echo "CUSTOMER DOCUMENT"
START http://localhost:2020/pos/download/download.php?param=updateCustomers-2
timeout /T 30

echo "SETTING UP"
#START http://localhost:2020/pos/utils/pos_setup.php
dir
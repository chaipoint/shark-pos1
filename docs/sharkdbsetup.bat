echo "Creating Couch Database -pos"
#create database in couchdb
#setup password


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


cls
echo "Creating Directory -POS"
mkdir d:\xampp\htdocs\pos
echo "Changing Directory -POS"
cd d:\xampp\htdocs\pos
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
copy d:\xampp\htdocs\pos\pos.ini d:\xampp\htdocs\. /v
c:
sharkdbsetup
timeout /T 30
START http://localhost:5984/_utils
START http://localhost:2020/pos
dir
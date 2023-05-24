<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$api_key = $_SERVER['HTTP_API_KEY'];

if ($api_key <> '2fa5c83f005056010a35'){
	echo "ERROR-4053: INVALID API KEY";
        var_dump(getallheaders());
	foreach ($_SERVER as $parm => $value)  echo "$parm = '$value'\n";
	exit();
}

$filecont=base64_decode('php://input');

$filename = 'test.pdf';
echo file_put_contents($filename,$filecont);

/* connect to the db */
$servername = "192.168.2.203";
$username = "boarduser";
$password = "Vilan%2018";
$dbname = "boardroom";


}
?>



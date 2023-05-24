<?php

include "common_functions.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$api_key = $_SERVER['HTTP_API_KEY'];

if ($api_key <> '2fa5c83f005056010a35'){
	echo "ERROR-4053: INVALID API KEY";
        var_dump(getallheaders());
	foreach ($_SERVER as $parm => $value)  echo "$parm = '$value'\n";
	exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$form_id = $data['form_id'];
$form_action = $data['form_action'];
$form_user = $data['form_user'];
$form_locate = $data['form_locate'];

/* connect to the db */
    $servername = getDbCred("s");
    $username = getDbCred("u");
    $password = getDbCred("p");
    $dbname = getDbCred("d");


switch ($form_id){

	case "meeting_form1":
		include 'dc_meeting_form1.php';
	break;
	case "update_pin_code":
		include 'dc_update_pin_code.php';
	break;
        case "inmeeting_form1":
                include 'dc_inmeeting_form1.php';
        break;


}

echo $form_locate;

} else {
echo "ERROR-4052: INVALID CALL METHOD";


}


?>

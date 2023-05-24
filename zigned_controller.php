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


/* connect to the db */
    $servername = getDbCred("s");
    $username = getDbCred("u");
    $password = getDbCred("p");
    $dbname = getDbCred("d");


if (array_key_exists("UpdateId",$data)){
	$request_data = $data["UpdateId"];
	$meeting_id = $request_data["MeetingId"];
        $zigned_id = $request_data["ZignedId"];
        $zigned_status = $request_data["ZignedStatus"];
	$zigned_created = $request_data["ZignedCreated"];
        $zigned_updated = $request_data["ZignedUpdated"];
//	echo $request_data[MeetingId];
//	echo $request_data[ZignedId];
//      echo $request_data[ZignedStatus];
        $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());

        $sql1 = "update public.meetings SET zigned_id='$zigned_id', zigned_status='$zigned_status',
                zigned_created='$zigned_created', zigned_updated='$zigned_updated'
                where meeting_id = $meeting_id;";

        if ($result = pg_query($dbconn, $sql1 )) {
        echo $meeting_id;
        } else {
        echo pg_last_error($dbconn);
        }
 pg_free_result($result);
    pg_close($dbconn);
	exit;
}

if (array_key_exists("UpdateMeeting",$data)){
        $request_data = $data["UpdateMeeting"];
        $meeting_id = $request_data["MeetingId"];
        $meeting_org = $request_data["Organization"];
        $zigned_status = $request_data["ZignedStatus"];
        $zigned_updated = $request_data["ZignedUpdated"];
        $zigned_filename = $request_data["ZignedFilename"];
        $zigned_url = $request_data["ZignedURL"];
        if ($zigned_status == 'fulfilled'){
           $attest_status = 'true';
        } else {
           $attest_status = 'false';
        }
        $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());

        $sql1 = "update public.meetings SET attested_status=$attest_status, zigned_status='$zigned_status',  zigned_updated='$zigned_updated'
                , zigned_filename='$zigned_filename',  zigned_url='$zigned_url'
                where meeting_id = $meeting_id;";

        if ($result = pg_query($dbconn, $sql1 )) {
        echo $meeting_id;
        if ($zigned_status == 'fulfilled'){
           fetchZignedPDF($meeting_id, $meeting_org, $zigned_filename, $zigned_url);
          }
        } else {
        echo pg_last_error($dbconn);
        }
 pg_free_result($result);
    pg_close($dbconn);
        exit;
}

if (array_key_exists("UpdateDoc",$data)){
        $request_data = $data["UpdateDoc"];
        $meeting_id = $request_data["MeetingId"];
        $status = $request_data["ZignedStatus"];
        $doc_id = $request_data["ZignedDocId"];
        $doc_check = $request_data["ZignedDocCheck"];
	$zigned_updated = $request_data["ZignedUpdated"];
        $doc_updated = $request_data["ZignedDocUpdated"];

        $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());

        $sql1 = "update public.meetings SET zigned_docid='$doc_id', zigned_status='$status', zigned_doccheck='$doc_check', zigned_docupdate='$doc_updated',
                zigned_updated='$zigned_updated'
                where meeting_id = $meeting_id;";

        if ($result = pg_query($dbconn, $sql1 )) {
        echo $meeting_id;
        } else {
        echo pg_last_error($dbconn);
        }
 pg_free_result($result);
    pg_close($dbconn);
        exit;
}

if (array_key_exists("InsertSigner",$data)){
        $request_data = $data["InsertSigner"];
        $zigned_id = $request_data["ZignedId"];
        $signing_id = $request_data["SigningId"];
        $member_id = $request_data["MemberId"];
        $attest_status = $request_data["AttestStatus"];
        $notification_sent = $request_data["NotificationSent"];

        $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());

        $sql1 = "INSERT INTO public.protocol_attest(
	zigned_id, signing_id, member_id, attest_status, notification_sent)
	VALUES ('$zigned_id', '$signing_id', '$member_id', '$attest_status', '$notification_sent');";

        if ($result = pg_query($dbconn, $sql1 )) {
        echo $signing_id;
        } else {
        echo pg_last_error($dbconn);
        }
 pg_free_result($result);
    pg_close($dbconn);
        exit;
}

if (array_key_exists("UpdateSigner",$data)){
        $request_data = $data["UpdateSigner"];
        $signing_id = $request_data["SigningId"];
        $attest_status = $request_data["AttestStatus"];
        $attest_timestamp = $request_data["AttestTimestamp"];
        $attest_method = $request_data["AttestMethod"];

        $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());


        $sql1 = "update public.protocol_attest  SET attest_status='$attest_status', attest_timestamp='$attest_timestamp', attest_method='$attest_method'
                where signing_id = '$signing_id';";


        if ($result = pg_query($dbconn, $sql1 )) {
        echo $signing_id;
        } else {
        echo pg_last_error($dbconn);
        }
 pg_free_result($result);
    pg_close($dbconn);
        exit;
}

if (array_key_exists("UpdateSigner2",$data)){
        $request_data = $data["UpdateSigner"];
        $signing_id = $request_data["SigningId"];
        $attest_status = $request_data["AttestStatus"];

        $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());


        $sql1 = "update public.protocol_attest  SET attest_status='$attest_status'
                where signing_id = '$signing_id';";


        if ($result = pg_query($dbconn, $sql1 )) {
        echo $signing_id;
        } else {
        echo pg_last_error($dbconn);
        }
 pg_free_result($result);
    pg_close($dbconn);
        exit;
}
echo "Unknown request";
echo $data;

} else {
echo "ERROR-4052: INVALID CALL METHOD";


}

function fetchZignedPDF($meeting_id, $meeting_org, $zigned_filename, $zigned_url) {
    $service_url =  $zigned_url;
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);

    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additional info: ' . var_export($info));
    }
    curl_close($curl);


    //save it to disk outside webroot
    $file_name = '/org_'.$meeting_org.'/'.$zigned_filename;
//echo $file_name;
    file_put_contents('../broomdocs'.$file_name, $curl_response);

    // if document is created - update db
    if (file_exists('../broomdocs'.$file_name)) {
// echo 'OK';
        updateDb($meeting_id, $file_name, 3);
    }
}

function updateDb($meeting, $file_name, $file_group) {
// Connect to DB

    /* connect to the db */
    $servername = getDbCred("s");
    $username = getDbCred("u");
    $password = getDbCred("p");
    $dbname = getDbCred("d");
    // Connecting, selecting database
    $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());

// Update the form values into the database.
$sql1 = "INSERT INTO public.meeting_files(
    meeting_id, file_name, file_group)
    VALUES ($meeting, '$file_name', $file_group);";

echo $sql1;
if ($result1 = pg_query($dbconn, $sql1 )) {
 echo "db updated";  }
 else {
echo pg_last_error($dbconn);
exit();
}


// Free resultset
pg_free_result($result1);

// Closing connection
//pg_close($dbconn);
}
?>



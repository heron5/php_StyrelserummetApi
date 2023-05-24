<?php
    
    // Connecting, selecting database
    $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());
    
    if ($form_action = "update"){

        $sql1 = "update public.protocol_attest SET pin_code='$data[pin_code]', attest_ok=false, attest_timestamp=null, pincode_sent=now()
                where meeting_id = $data[form_meetingid] and member_id = '$data[form_user]';";

        if ($result = pg_query($dbconn, $sql1 )) {
           
        } else {
        echo pg_last_error($dbconn);
        }
    }
    
    pg_free_result($result);
    pg_close($dbconn);
    exit;
?>

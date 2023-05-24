<?php
    // {"form_id":"meeting_form1","form_action":"update","form_user":"KARRO","form_locate":"meeting.php",
    // "form_meetingid":"10","title":"Styrelsemingel","protocol_status":"true","attested_status":"false",
    // "location":"Vilans Bygdeg\u00e5rd","datefrom":"2022-05-08T18:40:00","dateto":"2022-05-08T19:40:00","meeting_status":"AVSLUTAT",
    // "invite_status":"true","attendees_status":"false","agenda_status":"false","cancelled_status":"false"}
    
    // Connecting, selecting database
    $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());
    
    if ($form_action = "update"){

        $sql1 = "update public.meetings SET title='$data[title]', location='$data[location]', start_date='$data[datefrom]',
                end_date='$data[dateto]', meeting_status='$data[meeting_status]', attendees_status='$data[attendees_status]',
            agenda_status='$data[agenda_status]', protocol_status='$data[protocol_status]',
                invite_status='$data[invite_status]', cancelled_status='$data[cancelled_status]', attested_status='$data[attested_status]'
                where meeting_id = $data[form_meetingid];";

        if ($result = pg_query($dbconn, $sql1 )) {
        } else {
        echo pg_last_error($dbconn);
        }
    }
    
    pg_free_result($result);
    pg_close($dbconn);
?>

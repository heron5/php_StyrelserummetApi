<?php
    // {"form_id":"meeting_form1","form_action":"update","form_user":"KARRO","form_locate":"meeting.php",
    // "form_meetingid":"10","title":"Styrelsemingel","protocol_status":"true","attested_status":"false",
    // "location":"Vilans Bygdeg\u00e5rd","datefrom":"2022-05-08T18:40:00","dateto":"2022-05-08T19:40:00","meeting_status":"AVSLUTAT",
    // "invite_status":"true","attendees_status":"false","agenda_status":"false","cancelled_status":"false"}
    
    // Connecting, selecting database
    $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password sslmode=require") or die('Could not connect: ' . pg_last_error());
    
    if ($form_action = "update"){


	$sql1 = "INSERT INTO public.agenda_notes(meeting_id, user_id, line_id, notes)
	VALUES ($data[form_meetingid], '$data[form_user]', $data[form_lineid], '$data[form_note]')
	ON CONFLICT (meeting_id, user_id, line_id)
	DO
	update SET notes=excluded.notes;";

        if ($result = pg_query($dbconn, $sql1 )) {
        } else {
        echo pg_last_error($dbconn);
        }

	$sql2 = "UPDATE public.agendas
		SET line_status=$data[form_line_status]
		WHERE line_id=$data[form_lineid];";

        if ($result = pg_query($dbconn, $sql2 )) {
        } else {
        echo pg_last_error($dbconn);
        }


    }
    
    pg_free_result($result);
    pg_close($dbconn);
?>

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "common_functions.php";

/* require the model as the parameter */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
   return;
}

if (isset($_GET['model'])) {

    /* soak in the passed variable or set our own */

    $qmodel = $_GET['model']; //no default

    /* connect to the db */
    $servername = getDbCred("s");
    $username = getDbCred("u");
    $password = getDbCred("p");
    $dbname = getDbCred("d");

    // Connecting, selecting database
    $dbconn = pg_connect("host=$servername dbname=$dbname user=$username password=$password") or die('Could not connect: ' . pg_last_error());


    $query = model_sql($qmodel);


    /* grab the posts from the db */

    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

    /* create one master array of the records */
    $posts = array();

    while ($post = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $posts[] = $post;
        /* var_dump($post); */
    }

    /* output in necessary format */

    header('Content-type: application/json');
//	header("Access-Control-Allow-Origin: ht");
//        header('Access-Control-Allow-Credentials: true');
//        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    //  echo json_encode(array('posts'=>$posts));
    echo json_encode($posts);


    // Free resultset
    pg_free_result($result);

    // Closing connection
    pg_close($dbconn);
}

function model_sql($model) {
    switch ($model) {

	case "Organization":         // Return  the organization
            $searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
            return "SELECT organization_id, name, logo_file, logo_width, logo_height, default_location, organization_number,
		   protocol_prefix, protocol_lastnr
                    FROM public.organizations WHERE organization_id= $searchorganizationid;";
	    break;
        case "AllMembers":         // Return all Members
	     $searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
            return "SELECT member_id, e_name, f_name, street, co_addrress, zip, city,
                      phone, mobile, e_mail, fee_payed, board_member, board_from_year,
                      board_to_year, personal_id, family, fee_last_year
                    FROM public.members WHERE archived<>'t' and organization_id = $searchorganizationid
		    ORDER BY member_id;";
            break;
        case "MeetingsToday":         // Return all Meetings for today
		$searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
		$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_STRING);
	    return  "SELECT meeting_id, organization_id, title, location, start_date, end_date, meeting_status,
			to_char(start_date, 'MON') AS month, to_char(start_date, 'DD') AS day,
			to_char(start_date, 'HH24:MI') AS start_time, to_char(end_date, 'HH24:MI') AS end_time
    		FROM public.meetings where organization_id= $searchorganizationid and  start_date::date = now()::date
                    ORDER BY start_date DESC LIMIT $limit;";
            break;
        case "MeetingsPast":         // Return all past Meetings
		$searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
		$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_STRING);
            return  "SELECT meeting_id, organization_id, title, location, start_date, end_date, meeting_status,
			to_char(start_date, 'MON') AS month, to_char(start_date, 'DD') AS day,
			to_char(start_date, 'HH24:MI') AS start_time, to_char(end_date, 'HH24:MI') AS end_time
                FROM public.meetings where organization_id= $searchorganizationid and start_date::date < now()::date
                    ORDER BY start_date DESC LIMIT $limit;";
            break;
        case "MeetingsFuture":         // Return all future 
		$searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
		$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_STRING);
            return  "SELECT meeting_id, organization_id, title, location, start_date, end_date, meeting_status,
                     to_char(start_date, 'MON') AS month, to_char(start_date, 'DD') AS day,
			to_char(start_date, 'HH24:MI') AS start_time, to_char(end_date, 'HH24:MI') AS end_time
                FROM public.meetings where organization_id= $searchorganizationid and start_date::date > now()::date
                    ORDER BY start_date DESC LIMIT $limit;";
            break;
        case "Meeting":         // Return a Meetings for id
	    $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            return  "SELECT meeting_id, organization_id, title, location, start_date, end_date, meeting_status,
                        to_char(start_date, 'MON') AS month, to_char(start_date, 'DD') AS day,
                        to_char(start_date, 'HH24:MI') AS start_time, to_char(end_date, 'HH24:MI') AS end_time,
			type_description,file_prefix, attendees_status, agenda_status, protocol_status, meeting_type, 
			invite_status, cancelled_status, attested_status, e_sign, protocol_id,
                        zigned_id, zigned_status, zigned_docid, zigned_doccheck, zigned_docupdate, zigned_created, zigned_updated
    		FROM meetings INNER JOIN meeting_types
    		ON meetings.meeting_type = meeting_types.type_id
                WHERE meeting_id =$searchmeetingid;";
            break;
        case "MeetingsForEsign":         // Return Meetings for e-sign
            return  "SELECT meeting_id, organization_id, title, location, start_date, end_date, meeting_status,
                        to_char(start_date, 'MON') AS month, to_char(start_date, 'DD') AS day,
                        to_char(start_date, 'HH24:MI') AS start_time, to_char(end_date, 'HH24:MI') AS end_time,
                        type_description,file_prefix, attendees_status, agenda_status, protocol_status, meeting_type,
                        invite_status, cancelled_status, attested_status, e_sign, protocol_id, zigned_id, zigned_status, zigned_docid
                FROM meetings INNER JOIN meeting_types
                ON meetings.meeting_type = meeting_types.type_id
                WHERE protocol_status =true and e_sign=true and attested_status=false and zigned_id is null;";
            break;
        case "MeetingsForEsignDocUpload":         // Return Meetings for e-sign
            return  "SELECT meeting_id, organization_id, title, location, start_date, end_date, meeting_status,
                        type_description,file_prefix, attendees_status, agenda_status, protocol_status, meeting_type,
                        invite_status, cancelled_status, attested_status, e_sign, protocol_id, zigned_id, zigned_status, zigned_docid
                FROM meetings INNER JOIN meeting_types
                ON meetings.meeting_type = meeting_types.type_id
                WHERE protocol_status =true and e_sign=true and attested_status=false and zigned_id is not null and zigned_docid is null;";
            break;
        case "MeetingsForEsigners":         // Return Meetings for e-sign
            return  "SELECT meeting_id, organization_id, title, location, start_date, end_date, meeting_status,
                        type_description,file_prefix, attendees_status, agenda_status, protocol_status, meeting_type,
                        invite_status, cancelled_status, attested_status, e_sign, protocol_id, meetings.zigned_id,
                        zigned_status, zigned_docid, zigned_doccheck, COALESCE( counter, 0 ) AS counter
                FROM meetings INNER JOIN meeting_types
                ON meetings.meeting_type = meeting_types.type_id
				LEFT JOIN  protocol_count_signers pcs
				ON meetings.zigned_id = pcs.zigned_id
                WHERE protocol_status =true and e_sign=true and attested_status=false and meetings.zigned_id is not null and 
                      zigned_docid is not null and zigned_doccheck='successful';";
            break;
        case "MeetingsPendingForEsign":         // Return Meetings pending for e-sign
            return  "SELECT meeting_id, organization_id, title, location, start_date, end_date, meeting_status,
                        type_description,file_prefix, attendees_status, agenda_status, protocol_status, meeting_type,
                        invite_status, cancelled_status, attested_status, e_sign, protocol_id, zigned_id, zigned_status, zigned_docid, zigned_filename
                FROM meetings INNER JOIN meeting_types
                ON meetings.meeting_type = meeting_types.type_id
                WHERE (protocol_status =true and e_sign=true) and  ((zigned_status = 'pending') or (zigned_status = 'fulfilled' and zigned_filename =''));";

        case "Member":         // Return a Member
            $searchmemberid = filter_input(INPUT_GET, 'memberid', FILTER_SANITIZE_STRING);
            return "SELECT member_id, e_name, f_name, street, co_addrress, zip, city,
                      phone, mobile, e_mail, fee_payed, board_member, board_from_year,
                      board_to_year, personal_id, fee_last_year, archived, family, concat(f_name, ' ',e_name) AS cc_name
                    FROM public.members WHERE member_id ='$searchmemberid';";
            break;

        case "User":         // Return User
            $searchuserid = filter_input(INPUT_GET, 'userid', FILTER_SANITIZE_STRING);
            return "SELECT user_id, description FROM users WHERE user_id='$searchuserid'";
            break;

        case "AllBoardroles":         // Return all Board roles
            return "SELECT DISTINCT board_member, description, sort_order
                    FROM public.board_member_types ORDER BY sort_order;";
            break;

        case "AllBoardMembers":         // Return all Board members
	    $searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
            return "SELECT member_id, e_name, f_name, street, zip, city, phone, mobile, e_mail, 
                    board_member, board_from_year, board_to_year, description, sort_order,
			concat(f_name, ' ',e_name) AS cc_name, admin
                    FROM public.board_members WHERE sort_order < 7 and organization_id = $searchorganizationid;";
            break;
        case "AllSubstitutes":         // Return all Board Substitutes
	    $searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
            return "SELECT member_id, e_name, f_name, concat(f_name, ' ',e_name) AS cc_name,
                    board_member, description, sort_order
                    FROM public.board_members WHERE board_member = 'SUBSTITUTE' and organization_id = $searchorganizationid
			ORDER BY member_id;";
            break;
        case "OtherFunctions":         // Return all Board members
	    $searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
            return "SELECT member_id, e_name, f_name, street, zip, city, phone, e_mail,
                    board_member, board_from_year, board_to_year, description, sort_order
                    FROM public.board_members WHERE sort_order > 7 and organization_id = $searchorganizationid;";
            break;

	case "Participants":         // Return all Participants for a meeting
	    $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            return "SELECT meeting_id, member_id, e_name, f_name, concat(f_name, ' ',e_name) AS cc_name,
                    board_member, board_description, sort_order, attestant, present, substitute,chairman, secretary, xid
                    FROM public.participants WHERE meeting_id= $searchmeetingid
			ORDER BY sort_order;";
            break;
 	case "Visitors":         // Return all Visitors for a meeting
	    $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            return "SELECT meeting_id, visitors
                    FROM public.visitors WHERE meeting_id= $searchmeetingid;";
            break;
        case "Agenda":         // Return the Agenda for a meeting
            $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            return "SELECT meeting_id, line_nr, item_nr, description, indent, actions, reporter, 
			bullet, line_id, add_text, line_status
    			FROM public.agendas WHERE meeting_id= $searchmeetingid
			ORDER BY meeting_id, line_nr;";
            break;
        case "AgendaNotes":         // Return the AgendaLine with notes for a meeting
            $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            $searchuserid = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_STRING);
            $searchlineid = filter_input(INPUT_GET, 'line_id', FILTER_SANITIZE_STRING);
	    if  (isset($searchuserid)){
            return "SELECT meeting_id, line_nr, item_nr, description, indent, actions, reporter,
                        bullet, line_id, user_id, notes, line_status
                        FROM public.meeting_agenda_notes WHERE
			meeting_id= $searchmeetingid and user_id = '$searchuserid' and line_id = $searchlineid
                        ORDER BY meeting_id, line_nr;";
	    } else {
	    return "SELECT meeting_id, line_nr, item_nr, description, indent, actions, reporter,
                        bullet, line_id, user_id, notes, line_status
                        FROM public.meeting_agenda_notes WHERE
			meeting_id= $searchmeetingid and line_id = $searchlineid
                        ORDER BY meeting_id, line_nr;";
	    }
            break;
        case "Protocol":         // Return the Protocol for a meeting
            $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            return "SELECT meeting_id, line_nr, item_nr, description, indent, actions, reporter,
                        bullet, line_id, add_text, pline, ptext
                        FROM public.protocol_agenda WHERE meeting_id= $searchmeetingid
                        ORDER BY meeting_id, line_nr;";
            break;
	case "AllProtocolAttests": // Return all ProtocolAttest
	     $searchorganizationid = filter_input(INPUT_GET, 'organization_id', FILTER_SANITIZE_STRING);
	     $searchzignedid = filter_input(INPUT_GET, 'zigned_id', FILTER_SANITIZE_STRING);
		if  (isset($searchzignedid)){
	    return "SELECT zigned_id, signing_id, member_id, attest_status, notification_sent, attest_timestamp,
                    e_name, f_name, cc_name, phone, mobile, e_mail, organization_id, attest_method
    			FROM public.protocol_attest_member WHERE zigned_id = '$searchzignedid';";
		} else {
	    return "SELECT zigned_id, signing_id, member_id, attest_status, notification_sent, attest_timestamp,
                    e_name, f_name, cc_name, phone, mobile, e_mail, organization_id, attest_method
                        FROM public.protocol_attest_member WHERE organization_id = $searchorganizationid ;";
		}
	case "ProtocolAttests": // Return all ProtocolAttest for member_id
	    $searchmemberid = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_STRING);
        $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
        if  (isset($searchmeetingid)){
            return "SELECT meeting_id, protocol_id, member_id, pin_code, attest_ok, attest_type,
                         attest_timestamp, sort_order, e_name, f_name, cc_name, phone, mobile, e_mail, organization_id,
			notification_sent
                        FROM public.protocol_attest_member WHERE member_id= '$searchmemberid' and
                        meeting_id= $searchmeetingid;";
        } else {
            return "SELECT meeting_id, protocol_id, member_id, pin_code, attest_ok, attest_type,
                         attest_timestamp, sort_order, e_name, f_name, cc_name, phone, mobile, e_mail, organization_id,
			notification_sent
                        FROM public.protocol_attest_member WHERE member_id= '$searchmemberid' ;";

        }

	case "Files":         // Return the Files for a meeting
            $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            return "SELECT DISTINCT meeting_id, file_name, file_group 
			FROM public.meeting_files WHERE meeting_id= $searchmeetingid
			ORDER BY file_group, file_name ASC";
            break;
        case "FilesProtocols":         // Return the Files for a meeting
            $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            return "SELECT DISTINCT meeting_id, file_name, file_group, split_part(file_name, '/',3) as file_part
                        FROM public.meeting_files WHERE meeting_id= $searchmeetingid and file_group = 3
                        ORDER BY file_group, file_name ASC";
            break;
        case "MeetingSigners":         // Return the Signers for a meeting
            $searchmeetingid = filter_input(INPUT_GET, 'meeting_id', FILTER_SANITIZE_STRING);
            return "SELECT meeting_id, member_id, e_name, f_name, sort_order, attestant, present, substitute, chairman, secretary, e_mail
                        FROM public.meeting_signers WHERE meeting_id= $searchmeetingid 
                        ORDER BY sort_order ASC";
            break;
        default:
            return "Invalid model";
    }
}
?>

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function getDbCred($parm) {
//	$servername = "192.168.2.203";
//	$username = "boarduser";
//	$password = "Vilan%2018";
//	$dbname = "boardroom";
	$servername = "snuffleupagus.db.elephantsql.com";
	$username = "bduexott";
	$password = "UEUgqOHZH_AmXq4DdTzWTJJ6DZ4JWdAt";
	$dbname = "bduexott";

	switch ($parm)
	{
    	case "s":
    	return $servername;
	case "u":
        return $username;
        case "p":
        return $password;
        case "d":
        return $dbname;
	}
}
?>


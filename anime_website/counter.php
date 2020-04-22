<?php
ob_start();
require_once("db.inc.php");

if (!empty($_GET['link_id']) && is_numeric($_GET['link_id']) && !empty($_GET['action'])) {
	$link_id = escape($_GET['link_id']);
	if ($_GET['action']=='close') {
		if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent']) && $_GET['time_spent']>60) {
			$time_spent = escape($_GET['time_spent']);
			query("REPLACE INTO views SELECT $link_id, '".date('Y-m-d')."', IFNULL((SELECT clicks FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT views+1 FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT time_spent+$time_spent FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),$time_spent)");
		}
		//Else, discard and not even report it: opened and closed in too little time
	} else {
		query("REPLACE INTO views SELECT $link_id, '".date('Y-m-d')."', IFNULL((SELECT clicks+1 FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT time_spent FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0)");
	}
}

ob_flush();
mysqli_close($db_connection);
?>

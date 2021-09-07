<?php

	set_time_limit(0);
    ini_set("display_errors","on");
	
	$mysqli = new mysqli("localhost", "mysql", "", "fb"); 
	//$mysqli = new mysqli("localhost", "co38935_fb", "SuhSn6MV", "co38935_fb");
	$mysqli->query("SET NAMES 'utf8' ");
	
?>
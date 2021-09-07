<?php
	
	include "config.php";
	
	$name = $_GET['name'];
	$spend = $_GET['spend'];
	$leads = $_GET['leads'];
	
	$result = $mysqli->query("SELECT id FROM apix WHERE `date` >= CURRENT_DATE() AND `date` < CURRENT_DATE() + INTERVAL 1 DAY AND `name` = '".$name."'");
	if (mysqli_num_rows($result)) {
		$id_array = $result->fetch_array(MYSQLI_ASSOC);
		$mysqli->query("UPDATE `apix` SET `spend`='".$spend."',`leads`='".$leads."',`date`='".date("Y-m-d H:i:s")."' WHERE `id`='".$id_array['id']."'");
	}
	else $mysqli->query("INSERT INTO `apix`(`name`, `spend`, `leads`, `date`) VALUES ('".$name."','".$spend."','".$leads."','".date("Y-m-d H:i:s")."')");
	
	
?>

<?php include ("scripts/dbconnect.php");

	$query = "UPDATE schedule SET schedule.to_date='2031-04-04' WHERE schedule.code_change='1'";
	
	//db_connect($query);
	
?>
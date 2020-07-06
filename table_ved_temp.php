<?php
require_once('scripts/dbconnect.php');
$res=db_connect("SELECT  graph.programm, schedule.id
FROM graph
  INNER JOIN schedule
    ON (graph.day=schedule.id_day AND graph.branch=schedule.id_branch AND graph.shift =schedule.id_shift)
WHERE schedule.code_change='1'");
while ($row = mysqli_fetch_assoc($res)){
//db_connect("UPDATE schedule SET programm='{$row['programm']}' WHERE id='{$row['id']}'");
    //echo "UPDATE schedule SET programm='{$row['programm']}' WHERE id='{$row['id']}'<br>";
	echo phpinfo();
}

?>
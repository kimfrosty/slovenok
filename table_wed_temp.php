<?php
include ("scripts/dbconnect.php");
/*require_once('scripts/dbconnect.php');
$res=db_connect("SELECT  graph.programm, schedule.id
FROM graph
  INNER JOIN schedule
    ON (graph.day=schedule.id_day AND graph.branch=schedule.id_branch AND graph.shift =schedule.id_shift)
WHERE schedule.code_change='1'");
while ($row = mysqli_fetch_assoc($res)){
db_connect("UPDATE schedule SET programm='{$row['programm']}' WHERE id='{$row['id']}'");
echo "UPDATE schedule SET programm='{$row['programm']}' WHERE id='{$row['id']}'<br>";
}*/

$begin = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date("Y"))).'<br>'; 
$end = date('Y-m-d', mktime(0, 0, 0, date('m'), date("t"), date("Y"))).'<br>';
?>
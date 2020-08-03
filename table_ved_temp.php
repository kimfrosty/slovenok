<?php
require_once('scripts/dbconnect.php');
$res_prog = db_connect("SELECT * FROM programms");
$array_prog = array();
while($row_prog = mysqli_fetch_assoc($res_prog)){
$res = db_connect("SELECT programms.id FROM schedule
		INNER JOIN pupil ON schedule.pupil_id=pupil.id
		INNER JOIN shifts ON schedule.id_shift=shifts.id
		INNER JOIN branches ON schedule.id_branch=branches.id
		INNER JOIN teachers ON schedule.id_teacher=teachers.id
		INNER JOIN graph ON (schedule.id_day=graph.day AND schedule.id_shift=graph.shift AND schedule.id_branch=graph.branch)
		INNER JOIN programms ON graph.programm=programms.id
		WHERE code_change='1' AND programms.id='".$row_prog['id']."' AND to_date='2031-04-04'");
	$num_rows = mysqli_num_rows($res);
	$arr['name'] = $row_prog['name'];
	$arr['sum'] = $num_rows;
array_push($array_prog, $arr);
}
usort($array_prog, function($a, $b){
    return ($b['sum'] - $a['sum']);
});
for($i = 0; $i<=count($array_prog); $i++){
	if($array_prog[$i]['sum']>0){
	echo $array_prog[$i]['name'].' - '.$array_prog[$i]['sum'].' часов<br>';
	}
}
?>
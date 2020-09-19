<?php
//Сумма в день
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])||$_COOKIE['user_group']==0){
header('Location: login.php');
}

include ("scripts/dbconnect.php");
include ("function.php");
$week = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Аналитика</title>
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="css/sm-core-css.css" rel="stylesheet" type="text/css">
<link href="css/sm-blue.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="scripts/jquery-1.8.3.min.js"></script>
<script src="scripts/jquery-ui.min.js"></script>
<script src="scripts/jquery.smartmenus.min.js"></script>
<script src="scripts/showtime.js"></script>
</head>
<body onload="if(window.showTime) showTime();">
<div>
<?php include('scripts/navmenu.php');?>
</div>

<div class="container-around">
<div class="column">
<div class="flex-center">
	<button class="month">Рэйтинг направлений</button>
</div>
<div class="table">
	<table class="oplata">
    <thead>
		<tr class="legend">
        	<th style="width:25px;">п</th>
            <th style="width:200px;">Программа</th>
            <th style="width:200px;">Человеко-часов в неделю</th>
		</tr>
<?php 
$res_prog = db_connect("SELECT * FROM programms");
$array_prog = array();
$arr = array();
while($row_prog = mysqli_fetch_assoc($res_prog)){
	$res = db_connect("SELECT programms.id, programms.name, programms.bg_color, programms.color FROM schedule
		INNER JOIN pupil ON schedule.pupil_id=pupil.id
		INNER JOIN shifts ON schedule.id_shift=shifts.id
		INNER JOIN branches ON schedule.id_branch=branches.id
		INNER JOIN teachers ON schedule.id_teacher=teachers.id
		INNER JOIN graph ON (schedule.id_day=graph.day AND schedule.id_shift=graph.shift AND schedule.id_branch=graph.branch)
		INNER JOIN programms ON graph.programm=programms.id
		WHERE code_change='1' AND programms.id='".$row_prog['id']."' AND to_date='2031-04-04'");
	$num_rows = mysqli_num_rows($res);
	$arr['name'] = $row_prog['name'];
	$arr['bg_color'] = $row_prog['bg_color'];
	$arr['color'] = $row_prog['color'];	
	$arr['sum'] = $num_rows;
	array_push($array_prog, $arr);
}
usort($array_prog, function($a, $b){
    return ($b['sum'] - $a['sum']);
});
for($i = 0; $i<=count($array_prog); $i++){
	if($array_prog[$i]['sum']>0){
	echo '<tr class="legend_all"><td>'.($i+1).'</td><td style="background-color: '.$array_prog[$i]['bg_color'].'; color: '.$array_prog[$i]['color'].';">'.$array_prog[$i]['name'].'</td><td>'.$array_prog[$i]['sum'].'</td></tr>';
	}
}
?>
</thead>
</table>
</div>
</div>

<div class="column">
<div class="flex-center">
	<button class="month">Рэйтинг преподавателей</button>
</div>
<div class="table">
	<table class="oplata">
    <thead>
		<tr class="legend">
        	<th style="width:25px;">п</th>
            <th style="width:200px;">Преподаватель</th>
            <th style="width:200px;">Человеко-часов в неделю</th>
		</tr>
<?php 
$res_teachers = db_connect("SELECT * FROM teachers");
$array_teachers = array();
$arr = array();
while($row_t = mysqli_fetch_assoc($res_teachers)){
	$res_teachers_hours = db_connect("SELECT * FROM `schedule` 
										WHERE code_change='1' 
										AND id_teacher='{$row_t['id']}' 
										AND to_date='2031-04-04'");
	$num_rows_teacher = mysqli_num_rows($res_teachers_hours);
	$arr['name'] = $row_t['name_teacher'];
	$arr['sum'] = $num_rows_teacher;
	array_push($array_teachers, $arr);
	}
usort($array_prog, function($a, $b){
    return ($b['sum'] - $a['sum']);
});
for($i = 0; $i<=count($array_teachers); $i++){
	if($array_teachers[$i]['sum']>0){
	echo '<tr class="legend_all"><td>'.($i+1).'</td>
			<td>'.$array_teachers[$i]['name'].'</td>
			<td>'.$array_teachers[$i]['sum'].'</td></tr>';
	}
}


?>
</thead>
</table>
</div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
	});
	$('.flex-center button').button();
	$('#add_tickets input').button();
});
</script>
</body>
</html>
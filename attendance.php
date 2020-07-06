<?php
include ("scripts/dbconnect.php");
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])){
header('Location: login.php');
}
 include ("function.php");
 $month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
  //Пагинация
/*$count_day_in_month = date('t', $mark_time);
if(empty($_GET['p'])) header('Location:'.$_SERVER['PHP_SELF'].'?p=now');
$p = $_GET['p'];
if($p=='now'){
	$from_date = date('Y-m-01', $mark_time);
	$to_date = date('Y-m-'.$count_day_in_month, $mark_time);
	$m = date('m', $mark_time);
	$k=date('n', $mark_time);
	}
if($p=='prev'){
	$m = date('m', $mark_time)-1;
	$y = date('Y', $mark_time);
	$k=date('n', $mark_time);
	$k--;
	if($m==0) {$m=12;$k=12;$y=date('Y', $mark_time)-1;}
	if($m==13) {$m=1;$k=1;$y =date('Y', $mark_time)+1;}
	if($m<10){$m='0'.$m;}
	$from_date = $y.'-'.$m.'-01';
	$d = date('t', mktime(0,0,0,$m,1,$y));
	$to_date = $y.'-'.$m.'-'.$d;
	}
if($p=='next'){
	$k=date('n', $mark_time);
	$k++;
	$m = date('m', $mark_time)+1;
	$y = date('Y', $mark_time);
	if($m==13) {$m=1;$k=1;$y =date('Y', $mark_time)+1;}
	$from_date = $y.'-'.$m.'-01';
	$d = date('t', mktime(0,0,0,$m,1,$y));
	$to_date = $y.'-'.$m.'-'.$d;
	}
$k--;*/
if(!$_POST){
	$count_day_in_month = date('t', $mark_time);
	$from_date = date('Y-m-01', $mark_time);
	$to_date = date('Y-m-'.$count_day_in_month, $mark_time);
	$m = date('m', $mark_time);
	$k=date('n', $mark_time);
	} else {
		$from_date= $_POST['from_date'];
		$to_date = $_POST['to_date'];
		}
 //Конец пагинации
 ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Посещаемость</title>
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
</div><br>
<div class="flex-center">
<!--<div class="month arrow">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=prev'?>"><<</a>
</div>
<div class="month center">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=now'?>">Текущий месяц</a>
</div>
<div class="month arrow">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=next'?>">>></a>
</div>-->
<div id="period" class="div_form">
<form action="<?=$_SERVER['PHP_SELF'].'?show=1'?>" method="post">
	<label for="from_date">С даты: </label>
	<input type="date" name="from_date" id="from_date" value="<?=$from_date?>">
    <label for="to_date">По дату: </label>
    <input type="date" name="to_date" id="to_date" value="<?=$to_date?>">
    <button>do it</button>
    <input type="hidden" name="select_date" value="select_date">
</form>
</div>
</div>
<div class="flex-center">
	<div class="month center">
		<?php 
		if($_GET['show']!=1){
			echo '<a>Текущий месяц</a>';
			} else {
				echo '<a>Выбранный период: с '.formateDate($from_date).' по '.formateDate($to_date).'</a>';
				}
		?>
    </div>
</div>
<div class="container">
<div class="table">
	<table class="oplata">
    <thead>
		<tr class="legend">
        	<th style="width:25px;">п</th>
            <th style="width:250px;">ФИО</th>
            <th style="width:80px;">Посетил</th>
            <th style="width:80px;">Болел</th>
            <th style="width:80px;">Пропустил</th>
            <th style="width:80px;">Всего занятий</th>
		</tr>
<?php
$finish_arr_pupil=array();
$temp_pupil_row = array();
$res_shift = db_connect("SELECT * FROM shifts");
while($row_s=mysqli_fetch_assoc($res_shift)){$id_shift+=1;}

$count_day_in_month = date('t', $mark_time);
//while($row_dp = mysqli_fetch_assoc($res_data_pupil)){
	$res_date = db_connect("SELECT * FROM calendar 
							WHERE date>='".$from_date."' AND date<='".$to_date."'");
		while($row_date = mysqli_fetch_assoc($res_date)){
			$res_shifts = db_connect("SELECT * FROM shifts");
		while($row_s = mysqli_fetch_assoc($res_shifts)){
			$arr_pupil = getDataAllBranch($row_date['date'], $row_s['id']);
			for($i=1; $i<=$id_shift; $i++){
				//$arr_pupil = getDataPupilCode($row_date['date']);
				for($j=0; $j<count($arr_pupil); $j++){
					if(empty($arr_pupil[$j])) continue;
					if(array_key_exists($arr_pupil[$j]['pupil_id'], $temp_pupil_row)){
						if($arr_pupil[$j]['code_change']==1&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']+=1;
							}
					if($arr_pupil[$j]['code_change']==4&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']+=0;
							}
					if($arr_pupil[$j]['code_change']==3&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']+=1;
							}
					if($arr_pupil[$j]['code_change']==6&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['empty']+=1;
							}
					if($arr_pupil[$j]['code_change']==5&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['seek']+=1;
							}
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['pupil_id'] = $arr_pupil[$j]['pupil_id'];
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['FIO'] = $arr_pupil[$j]['FIO'];
						} else {
							if($arr_pupil[$j]['code_change']==4&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']=0;
							}
							if($arr_pupil[$j]['code_change']==1&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']=1;
							}
					if($arr_pupil[$j]['code_change']==3&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']=1;
						
							}
					if($arr_pupil[$j]['code_change']==6&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['empty']=1;
							}
					if($arr_pupil[$j]['code_change']==5&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$i)){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['seek']=1;
							}
						
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['pupil_id'] = $arr_pupil[$j]['pupil_id'];
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['FIO'] = $arr_pupil[$j]['FIO'];
							}
					}
			}}
		
		}
		//}
	foreach($temp_pupil_row as $key=>$val){
		$val['sum'] = $val['attendance']+$val['seek']+$val['empty'];
			array_push($finish_arr_pupil, $val);
			}
		$count=1;
		for($z=0; $z<count($finish_arr_pupil); $z++){
		if(isset($finish_arr_pupil[$z]['seek'])||isset($finish_arr_pupil[$z]['empty'])){
		echo '<tr class="legend_all"><td>'.$count.'</td><td class="left_align"><a style="color:#0067F4; text-decoration:none;" href="pupils.php?pupil_id='.$finish_arr_pupil[$z]['pupil_id'].'">'.$finish_arr_pupil[$z]['FIO'].'</a></td>
		<td>'.$finish_arr_pupil[$z]['attendance'].'</td>
		<td>'.$finish_arr_pupil[$z]['seek'].'</td>
		<td>'.$finish_arr_pupil[$z]['empty'].'</td>
		<td>'.$finish_arr_pupil[$z]['sum'].'</td></tr>';
		$count++;
			}
		}
	
	
/*echo '<pre>';
print_r($arr_pupil);
echo '</pre>';*/
?>
</thead>
</table>
</div>
</div>
<script type="text/javascript">
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
	});
	//диалоговое окно
	$('#period input, button').button();
	
</script>
</body>
</html>
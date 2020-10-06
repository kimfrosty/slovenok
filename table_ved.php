<?php
include ("scripts/dbconnect.php");
include ("function.php");
//Сумма в день
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])||$_COOKIE['user_group']==0){
header('Location: login.php');
}
//$_SESSION['id_branch']=$_COOKIE['id_branch'];
$month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');

$finish_arr_pupil = array();
$temp_pupil_row = array();
$res_shift = db_connect("SELECT * FROM shifts");
while($row_s=mysqli_fetch_assoc($res_shift)){$id_shift+=1;}

$count_day_in_month = date('t', $mark_time);
if(empty($_GET['p'])) header('Location:'.$_SERVER['PHP_SELF'].'?p=now');
$p = $_GET['p'];
if($p=='now'){
	$from_date = date('Y-m-01', $mark_time);
	$to_date = date('Y-m-'.$count_day_in_month, $mark_time);
	$m = date('m', $mark_time);
	$k=date('n', $mark_time);
	}
else if($p=='prev'){
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
else if($p=='next'){
	$k=date('n', $mark_time);
	$t = date('t', $mark_time);
	$k++;
	
	$m = date('m', $mark_time);
	$last_m = date('m',  mktime(0,0,0,($m+1),1,$y));
	$y = date('Y', $mark_time);
	if($m==12) {$m=1;$k=1;$y =date('Y', $mark_time)+1;}
	$from_date = $y.'-'.$last_m.'-01';
	$d = date('t', mktime(0,0,0,$last_m,1,$y));
	$to_date = $y.'-'.$last_m.'-'.$d;
	}
$k--;

	$res_date = db_connect("SELECT * FROM calendar 
							WHERE date>='".$from_date."' AND date<='".$to_date."'");
			
			
			while($row_date = mysqli_fetch_assoc($res_date)){
				
				
			$res_shifts = db_connect("SELECT * FROM shifts");
			while($row_s = mysqli_fetch_assoc($res_shifts)){
			
			
			$arr_pupil = getDataAllBranchVED($row_date['date'], $row_s['id']);
			/*echo '<pre>';
			print_r($arr_pupil).'<br>';
			echo $row_date['date'];
			echo '</pre>';*/
			
			//for($i=1; $i<=$id_shift; $i++){
				for($j=0; $j<count($arr_pupil); $j++){
					if(empty($arr_pupil[$j])) continue;
                    //$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax']=0;
					//if(array_key_exists($arr_pupil[$j]['pupil_id'], $temp_pupil_row)){
					if($arr_pupil[$j]['code_change']==1){
						if($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$row_s['id']){
							$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']+=1;
							$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax'] += $arr_pupil[$j]['tarif_pupil'];
							$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['pupil_id'] = $arr_pupil[$j]['pupil_id'];
							$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['FIO'] = $arr_pupil[$j]['FIO'];
							}
						}
					/*if($arr_pupil[$j]['code_change']==3&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$row_s['id'])){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']+=1;
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax'] += $arr_pupil[$j]['tarif'];
							}
					if($arr_pupil[$j]['code_change']==6&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$row_s['id'])){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['empty']+=1;
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax'] += $arr_pupil[$j]['tarif'];
							}
					if($arr_pupil[$j]['code_change']==5&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$row_s['id'])){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['seek']+=1;
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax'] += $arr_pupil[$j]['tarif'];
							}
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['pupil_id'] = $arr_pupil[$j]['pupil_id'];
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['FIO'] = $arr_pupil[$j]['FIO'];
						
					}else {
							if($arr_pupil[$j]['code_change']==1&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$row_s['id'])){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']=1;
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax'] += $arr_pupil[$j]['tarif'];
							}
							if($arr_pupil[$j]['code_change']==3&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$row_s['id'])){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['attendance']=1;
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax'] += $arr_pupil[$j]['tarif'];
						
							}
							if($arr_pupil[$j]['code_change']==6&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$row_s['id'])){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['empty']=1;
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax'] += $arr_pupil[$j]['tarif'];
							}
							if($arr_pupil[$j]['code_change']==5&&($arr_pupil[$j]['id_day']==$row_date['weekday']&&$arr_pupil[$j]['id_shift']==$row_s['id'])){
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['seek']=1;
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['tax'] += $arr_pupil[$j]['tarif'];
							}
						
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['pupil_id'] = $arr_pupil[$j]['pupil_id'];
						$temp_pupil_row[$arr_pupil[$j]['pupil_id']]['FIO'] = $arr_pupil[$j]['FIO'];
						
							}*/
					}
				//}
		}
	}
/*echo '<pre>';
print_r(getDataAllBranch('2018-10-17','6'));
echo '</pre>';*/

	foreach($temp_pupil_row as $key=>$val){
		$val['sum'] = $val['attendance']+$val['seek']+$val['empty'];
			array_push($finish_arr_pupil, $val);
			}
	usort($finish_arr_pupil, function($a, $b){
    	return $a['FIO'] > $b['FIO'];
	});
/*echo '<pre>';
print_r($finish_arr_pupil);
echo '</pre>';*/

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Ведомость</title>
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
<div id="modal" style="display: none;">
</div>
<div>
<?php include('scripts/navmenu.php');?>
</div>
<div class="flex-center">
<div class="month arrow">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=prev'?>"><<</a>
</div>
<div class="month center">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=now'?>">Текущий месяц</a>
</div>
<div class="month arrow">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=next'?>">>></a>
</div>
</div>
<div class="flex-center">
	<div class="month center">
		<a><?php echo $month[$k];?></a>
    </div>
</div>
<div class="container">
<div class="table">
	<table class="oplata" id="oplata">
    <thead>
		<tr class="legend">
        	<th style="width:25px;">п</th>
            <th style="width:200px;">ФИО</th>
            <th>Расчетка</th>
			<th style="width:50px;">Напр-ий</th>
            <th style="width:25px;">Уроков</th>
            <th style="width:100px">Сумма по графику</th>
            <th style="width:100px;">Оплачено</th>
            <!--<th style="width:100px;">Возврат по б/л</th>
            <th style="width:100px;">Возврат</th>-->
            <th style="width:100px;">Сумма со скидкой</th>
            <th style="width:100px;">Сумма за доп</th>
            <th style="width:300px;">Комментарий</th>
            <th style="width:100px;">Дата</th>
            <th style="width:200px;">Админ</th>
		</tr>
<?php
	$glob_sum = 0;
	for($i=0; $i<count($finish_arr_pupil); $i++){
		$res_pupil = db_connect("SELECT * FROM payment 
	                            WHERE payment.pupil_id='".$finish_arr_pupil[$i]['pupil_id']."' 
	                            AND (payment.from_date>='".$from_date."' 
	                            AND payment.to_date<='".$to_date."') ORDER BY pay DESC");
        $row_pupil = mysqli_fetch_assoc($res_pupil);
		$res_comment = db_connect("SELECT * FROM comments 
	                            WHERE comments.pupil_id='".$finish_arr_pupil[$i]['pupil_id']."' 
	                            AND (comments.from_date>='".$from_date."' 
	                            AND comments.to_date<='".$to_date."')");
		$row_comment = mysqli_fetch_assoc($res_comment);
		
		$subjects = mysqli_num_rows(db_connect("SELECT DISTINCT graph.programm FROM schedule
												   INNER JOIN graph ON (schedule.id_branch=graph.branch 
                     													AND schedule.id_day=graph.day 
                     													AND schedule.id_shift=graph.shift)
												   WHERE pupil_id='".$finish_arr_pupil[$i]['pupil_id']."' 
												   						AND to_date='2031-04-04'
																		AND code_change='1'"));
		switch($subjects) {
			case '1': $color = '#BBDAFF';
			break;
			case '2': $color = '#6bafff';
			break;
			case ($subjects>=3): $color = '#ff61ea';
			break;
		}
		
		if($res_pupil!='' and mysqli_num_rows($res_pupil)>0){
			//Есть данные по оплатам или возвратам

			$sum_on_pay = $finish_arr_pupil[$i]['tax'];
			if($sum_on_pay!=$row_pupil['pay']){
			    $style = 'color:red;font-weight:bold;';
			}else{
			    $style = 'color:#000000;font-weight:normal;';
            }
			//$sum_cashback=$finish_arr_pupil[$i]['seek']*$finish_arr_pupil[$i]['tax'];
			$res_c_sum = db_connect("SELECT clear_cashback.* FROM clear_cashback
	                                  WHERE clear_cashback.pupil_id='".$finish_arr_pupil[$i]['pupil_id']."' 
	                                  AND (clear_cashback.from_date>='".$from_date."' 
	                                  AND clear_cashback.to_date<='".$to_date."') 
									");
			if(mysqli_num_rows($res_c_sum)>0){
				$row_cc = mysqli_fetch_assoc($res_c_sum);
				$sum_clear_cashback = $row_cc['sum_cashback'];
				} else $sum_clear_cashback=0;
			
			echo '<tr class="legend_all">
				<td>'.($i+1).'</td>
				<td class="left_align">
				<a style="color:#0067F4; text-decoration:none;" href="pupils.php?pupil_id='.$finish_arr_pupil[$i]['pupil_id'].'">'.$finish_arr_pupil[$i]['FIO'].'</a>
				</td>
				<td><button class="ved" pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" from_date="'.$from_date.'" month="'.$k.'" FIO="'.$finish_arr_pupil[$i]['FIO'].'">=></button></td>
				<td style="background-color:'.$color.';">'.$subjects.'</td>
				<td>'.$finish_arr_pupil[$i]['sum'].'</td>';
				if($row_pupil['pay']!=NULL){
					echo '<td style="'.$style.'">'.$sum_on_pay.'</td></td><td class="yes_payment" 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_month" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'"
						change="'.$row_pupil['id'].'">'.$row_pupil['pay'].'</td>';
					} else {
						echo '<td>'.$sum_on_pay.'</td><td class="no_payment" 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_month" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">'.$sum_on_pay.'</td>';
						}
				if($row_pupil['discount']!=NULL){
					echo '<td style="background-color:#DCDD77;" 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_cashback" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">'.($row_pupil['pay']-$row_pupil['discount']).'</td>';
					} else {
						echo '<td 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_cashback" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">'.($row_pupil['pay']-$row_pupil['discount']).'</td>';
						}
					/*if($sum_clear_cashback){
					echo '<td class="yes_payment" 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_clear_cashback" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">'.$sum_clear_cashback.'</td>';
					} else {
						echo '<td class="no_payment clear_cashback" 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_clear_cashback" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">'.$sum_clear_cashback.'</td>';
						}*/
				
					echo '<td>'.$row_pupil['extra'].'</td>';
					echo '<td pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" from_date="'.$from_date.'" to_date="'.$to_date.'" class="comment">'.$row_comment['comment'].'</td>';
                    echo '<td class="yes_payment">'.formateDate($row_pupil['date']).'</td>';
					echo '<td>'.$row_pupil['loger'].'</td>';
					echo '</tr>';
					$glob_sum+=$sum_on_pay; 					
				
			}else{
			$row_pupil = mysqli_fetch_assoc($res_pupil);
				$sum_on_pay = $finish_arr_pupil[$i]['tax'];
				$sum_cashback=$finish_arr_pupil[$i]['seek']*$finish_arr_pupil[$i]['tax'];
				$res_c_sum = db_connect("SELECT clear_cashback.* FROM clear_cashback
	WHERE clear_cashback.pupil_id='".$finish_arr_pupil[$i]['pupil_id']."' AND (clear_cashback.from_date>='".$from_date."' AND clear_cashback.to_date<='".$to_date."') 
	");
			if(mysqli_num_rows($res_c_sum)>0){
				$row_cc = mysqli_fetch_assoc($res_c_sum);
				$sum_clear_cashback = $row_cc['sum_cashback'];
				} else $sum_clear_cashback=0;
				echo '<tr class="legend_all">
				<td>'.($i+1).'</td>
				<td class="left_align">
				<a style="color:#0067F4; text-decoration:none;" href="pupils.php?pupil_id='.$finish_arr_pupil[$i]['pupil_id'].'">'.$finish_arr_pupil[$i]['FIO'].'</a></td>
				<td><button class="ved" pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" from_date="'.$from_date.'" month="'.$k.'" FIO="'.$finish_arr_pupil[$i]['FIO'].'">=></button></td>
				<td style="background-color:'.$color.';">'.$subjects.'</td>
				<td>'.$finish_arr_pupil[$i]['sum'].'</td>
				<td>'.$sum_on_pay.'</td>
				<td class="no_payment" 
						less="'.$finish_arr_pupil[$i]['sum'].'"
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_month" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">'.$sum_on_pay.'</td>
				<td class="no_payment" 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_cashback" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">0</td>';
				/*if($sum_clear_cashback){
					echo '<td class="yes_payment" 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_clear_cashback" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">'.$sum_clear_cashback.'</td>';
					} else {
						echo '<td class="no_payment clear_cashback" 
						pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" 
						type_of_payment="sum_clear_cashback" 
						FIO="'.$finish_arr_pupil[$i]['FIO'].'" 
						date="'.date('Y-m-d', $mark_time).'"
						from_date="'.$from_date.'"
						to_date="'.$to_date.'">'.$sum_clear_cashback.'</td>';
						}*/
						
				echo '<td>'.$row_pupil['extra'].'</td>';
				echo '<td pupil_id="'.$finish_arr_pupil[$i]['pupil_id'].'" from_date="'.$from_date.'" to_date="'.$to_date.'" class="comment">'.$row_comment['comment'].'</td><td></td>';
				echo '<td>'.$row_pupil['loger'].'</td>';
				echo '</tr>';
				$glob_sum+=$sum_on_pay;
				}
				
		}
		$res_sum = db_connect("SELECT SUM(pay), SUM(discount), SUM(extra) FROM payment WHERE from_date<='$from_date' AND to_date>='$to_date'");
		//$res_clear = db_connect("SELECT SUM(sum_cashback) FROM clear_cashback WHERE from_date<='$from_date' AND to_date>='$to_date'");
		$row_sum = mysqli_fetch_assoc($res_sum);
		//$row_clear = mysqli_fetch_assoc($res_clear);
			if ($_COOKIE['id_teacher']=="999"){
				echo '<tr class="strong"><td colspan="5">Итого:</td><td>'.$glob_sum.'</td><td>'.$row_sum['SUM(pay)'].'</td>
					<td>'.($row_sum['SUM(pay)']-$row_sum['SUM(discount)']).'</td><td>'.$row_sum['SUM(extra)'].'</td>
					<td></td><td></td><td></td></tr>';
			}else{
				echo '<tr class="strong"><td colspan="5">Итого:</td><td></td><td></td>
					<td></td><td></td>
					<td></td><td></td><td></td></tr>';
			}
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
	$('.left_align').click(function(e) {
		var $tmp = $("<input>");
		$("body").append($tmp);
		$tmp.val($(this).text()).select();
		document.execCommand("copy");
		$tmp.remove();
	});
	$('.flex-center button').button();
	$('#payment').dialog({
		autoOpen: false,
		modal:true,
		width: 400,
		close:function(){location.reload()}
		});
	$('#change').dialog({
		autoOpen: false,
		modal:true,
		width: 350,
		close:function(){location.reload()}
		});
	$('#sum_cashback').dialog({
		autoOpen: false,
		modal:true,
		width: 300,
		close:function(){location.reload()}
		});
	$('#modal').dialog({
        autoOpen: false,
        modal:true,
        width: 1000
        });
	$('.oplata td').each(function() {
        if($(this).html()=='0'){
			$(this).removeClass('no_payment');
			//$(this).addClass('yes_payment');
			}
    });
	$('.clear_cashback').hover(function(){
		$(this).css('cursor', 'pointer');
		});
	$('.no_payment').click(function(e) {
		e.preventDefault();
		if($(this).attr('type_of_payment')=='sum_month'){
			$('#payment>').html('');
			var data = new Object;
			if($(this).siblings('.comment').is('.yes_item') == true){
				data.comment = $(this).siblings('.comment').text();
			}
			data.money = $(this).html();
			data.less = $(this).attr('less');
			data.type_of_payment = $(this).attr('type_of_payment');
			data.FIO = $(this).attr('FIO');
			data.date = $(this).attr('date');
			data.pupil_id = $(this).attr('pupil_id');
			data.from_date = $(this).attr('from_date');
			data.to_date = $(this).attr('to_date');
			$('#payment').append('<label>Ученик: </label><strong>'+data.FIO+'</strong></br><label>Сумма: </label><strong>'+data.money+'</strong>')
			$('#payment').append('<br><label for="discount_proc">Скидка: </label><select name="discount_proc" id="discount_proc"><option>выберите скидку</option>' +
                '<option value="50">50 руб за урок</option></select>' +
                '<label for="discount_cash"> + </label><input type="text" name="discount_cash" id="discount_cash" placeholder="Доп.скидка">');
			$('#payment').append('<br><label for="extra">Доп. оплата: </label><input type="text" name="extra" id="extra">');
			//$('#payment').append('<br><label for="comment">Комментарий: </label><input type="text" name="comment" id="comment">');
			$('#payment').dialog({title:'Оплата за месяц',
								
								  buttons:{
									  "Оплатить" : function(){
										  data.discount_proc = $('#discount_proc').val();
                                          data.discount_cash = $('#discount_cash').val();
										  data.extra = $('#extra').val();
										  $.post('scripts/add_data.php?type_of_payment='+data.type_of_payment, data, function(){},"json");
										  location.reload();
										  }}
								  })
			}
		if($(this).attr('type_of_payment')=='sum_cashback'){
			$('#payment>').html('');
			var data = new Object;
			data.money = $(this).html();
			data.type_of_payment = $(this).attr('type_of_payment');
			data.FIO = $(this).attr('FIO');
			data.date = $(this).attr('date');
			data.pupil_id = $(this).attr('pupil_id');
			data.from_date = $(this).attr('from_date');
			data.to_date = $(this).attr('to_date');
			$('#payment').append('<span>Ученик: </span><strong>'+data.FIO+'</strong></br><span>Сумма: </span><strong>'+data.money+'</strong>')
			$('#payment').dialog({title:'Возврат больничного',
								  buttons:{
									  "Вернуть" : function(){
										  $.post('scripts/add_data.php?type_of_payment='+data.type_of_payment, data, function(){},"json");
										 location.reload();
										  }}
								  })
			
			}
		$('#payment input, #payment select').button();
		$('#payment').dialog('open');
    });
	
	
	//Чистый возврат
	$('.clear_cashback').click(function(e) {
        e.preventDefault();
		if($(this).attr('type_of_payment')=='sum_clear_cashback'){
			$('#sum_cashback>').html('');
			tmp_str = '';
			tmp_str+='<span>Ученик: </span><strong>'+$(this).attr('FIO')+'</strong>';
			tmp_str+='<form id="form_clear_cashback">';
			tmp_str+='<input type="hidden" name="from_date" value="'+$(this).attr('from_date')+'">';
			tmp_str+='<input type="hidden" name="to_date" value="'+$(this).attr('to_date')+'">';
			tmp_str+='<input type="hidden" name="pupil_id" value="'+$(this).attr('pupil_id')+'">';
			tmp_str+='<input type="hidden" name="type_of_payment" value="'+$(this).attr('type_of_payment')+'">';
			tmp_str+='<label for="sum_cashback">Сумма возврата:</label>';
			tmp_str+='<input type="text" name="sum_cashback" id="sum_cashback">';
			tmp_str+='</form>';
			$('#sum_cashback').append(tmp_str);
			$('#sum_cashback').dialog({title:'Возврат денег',
								  buttons:{
									  "Вернуть" : function(){
										  var data = $('#form_clear_cashback').serializeArray();
										  $.post('scripts/add_data.php?type_of_payment=sum_clear_cashback', data, function(){},"json");
										 location.reload();
										  }}
								  })
			
			}
		$('#sum_cashback').dialog('open');
    });
	//Отмена оплаты
	$('.yes_payment').hover(function(){
		$(this).css('cursor', 'help');
		})
	$('.yes_payment').click(function(e) {
        e.preventDefault();
		$('#change').html();
		tmp_str='';
		tmp_str+='<span>Отменить оплату для <strong>'+$(this).attr('FIO')+'?</strong></span>';
		tmp_str+='<form id="change_payment">';
		tmp_str+='<input type="hidden" name="change_id" value="'+$(this).attr('change')+'">';
		tmp_str+='</form>';
		$('#change').append(tmp_str);
		$('#change').dialog({title:'Отмена оплаты',
						open: function(event, ui) {
      						$(this).parent().find('div.ui-dialog-titlebar').addClass('warning');
						},
								  buttons:{
									  "Отменить" : function(){
										  var data = $('#change_payment').serializeArray();
										  $.post('scripts/add_data.php?change=change', data, function(){},"json");
										 location.reload();
										  }}
								  })
		$('#change').dialog('open');
    });
	//модальное окно с ведомостью
	$('.ved').click(function (e) {
        e.preventDefault();
        $('#modal').html('');
        $('#modal').load('modalved.php?pupil_id='+$(this).attr('pupil_id')+'&from_date='+$(this).attr('from_date')+'&month='+$(this).attr('month'));
        $('#modal').dialog({title:"Расчётка - "+$(this).attr('FIO')});
        $('#modal').dialog('open');
    });
	
	//Комментарии
	$('.comment').hover(function(e){
		$(this).css('cursor', 'pointer');
		})
	var inpMin = '<input class="input-mini">',
        butMin = '<button type="submit" class="save">save</button>';
    $('table').on('click', 'td.comment', function(e) {        
        if($(this).hasClass('edited')) return false;
        $(this).html($(inpMin).val($(this).text())).append(butMin).addClass('edited');
		$('.input-mini').focus().select();
    });
	$('table td').on('click', '.save', function(e) {
		var data = new Object;
		data.pupil_id = $(this).parent().attr('pupil_id');
		data.comment = $('.input-mini').attr('value');
		data.from_date = $(this).parent().attr('from_date');
		data.to_date = $(this).parent().attr('to_date');
		$(this).parent('td').text($(this).siblings('.input-mini').val()).removeClass('edited');
		$.post('scripts/add_data.php?comment=comment', data, function(){},"json");
		
	});
})
</script>
<!--Диалоговое окно-->
<div id="payment"></div>
<div id="sum_cashback"></div>
<div id="change"></div>
</body>
</html>
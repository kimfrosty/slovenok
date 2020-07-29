<?php 
include ("scripts/dbconnect.php");
include ("function.php");
 $month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])){
header('Location: login.php');
}
//Функция подсчета

//Добавление данных о выплатах педагогам
if($_POST['form_pay_out']=='form_pay_out'){
	$id_teacher1 = $_POST['id_teacher'];
	$from_date1 = $_POST['from_date'];
	$to_date1 = $_POST['to_date'];
	$data_pay = $_POST['cash_for_bd'];
	$pupil_hour = $_POST['count_pupil'];
	//$data_pay1 = Payment($from_date1, $to_date1, $id_teacher1);
	//$paid1 = $data_pay1[0];
	//$pupil_hour1 = $data_pay1[1];
	$add_data = db_connect("INSERT INTO payments (date, id_teacher, from_date, to_date, paid, pupil_hour) 
							VALUES (NOW(), '$id_teacher1', '$from_date1', '$to_date1', '$data_pay', '$pupil_hour')");
	//die("INSERT INTO payments (date, id_teacher, from_date, to_date, paid, pupil_hour) 
							//VALUES (NOW(), '$id_teacher1', '$from_date1', '$to_date1', '$paid1', '$pupil_hour1')");
	if($add_data) echo json_encode(array("success"=>"success"));
	exit;
	}

 //Пагинация
$count_day_in_month = date('t', $mark_time);
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
	if($m==13) {$m='01';$k=1;$y =date('Y', $mark_time)+1;}
	$from_date = $y.'-'.$m.'-01';
	$d = date('t', mktime(0,0,0,$m,1,$y));
	$to_date = $y.'-'.$m.'-'.$d;
	}
	$k--;
 //Конец пагинации
//Минимальная ставка учителя в день
 ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Выплаты</title>
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
<div id="accurate" style="display: none;">
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
<table class="oplata">
	<tr class="legend">
    	<th style="width:25px;">п</th>
		<th style="width:250px;">Педагог</th>
		<th style="width:150px;">Учеников</th>
        <th style="width:150px;">Отработано часов</th>
		<th style="width:150px;">Гонорар</th>
        <th>Подробно</th>
	</tr>
    <?php 
	$global_cash = 0;
	$count_p = 1;
	$res_teachers = db_connect("SELECT  teachers.id, teachers.name_teacher, teachers.tax_teacher, tax_change_teachers.new_tax_teacher 
								FROM teachers
								LEFT JOIN tax_change_teachers ON (teachers.id=tax_change_teachers.teacher_id 
								AND tax_change_teachers.from_date<='$from_date' 
								AND tax_change_teachers.to_date>='$to_date')");
	echo '<div id="num_teachers" style="display:none;" num_t="'.mysqli_num_rows($res_teachers).'"></div>';
    $arr_hour_t = array();
	while($row_t = mysqli_fetch_assoc($res_teachers)){
		if($row_t['new_tax_teacher']==NULL){
			$tax_teacher = $row_t['tax_teacher'];
			}else{
				$tax_teacher = $row_t['new_tax_teacher'];
				}
		$count_pup_hour = 0;
		$final_cash = 0;
		$temp_count = array();
		$count_hour = 0;

		
		if($_COOKIE['user_group']==1){
        $oplata=0;
		echo '<tr class="legend_all"><td>'.$count_p.'</td><td>'.$row_t['name_teacher'].'</td>';
		//Данные на каждого ученика
		/*$res_pupil = db_connect("SELECT DISTINCT schedule.pupil_id FROM schedule INNER JOIN pupil ON schedule.pupil_id=pupil.id WHERE pupil.id_teacher='".$row_t['id']."' AND schedule.code_change='1' AND (from_date<='$from_date' AND to_date>='$to_date')");*/		
		$res_date = db_connect("SELECT * FROM calendar WHERE date>='".$from_date."' AND date<='".$to_date."'");
		while($row_date = mysqli_fetch_assoc($res_date)){

		//$arr_hours_t [$row_t['id']][] = getHourTeacher($from_date, $to_date, $row_t['id_teacher']);
		/*=======================Проверка каникул==================*/
		$check_holiday = db_connect("SELECT * FROM holiday WHERE from_date<='".$row_date['date']."' AND to_date>='".$row_date['date']."'");
		if(mysqli_num_rows($check_holiday)>0) continue;
		/*========================================================*/
		
		$test_lesson = CheckTestLessonAllBranch($row_date['date'], $row_t['id']);
		while ($row_tl = mysqli_fetch_assoc($test_lesson)){
			$oplata+=$row_tl['tarif_teacher'];
			$count_hour++;
		}
		$res_shifts = db_connect("SELECT * FROM shifts");
		while($row_s = mysqli_fetch_assoc($res_shifts)){
		$count_pupil = 0;
		$arr_pupil_date = [];
		$arr_pupil_date = getDataAllBranch($row_date['date'], $row_s['id']);
		
		/////////////////////////////////////////////////////////////////
		

			for($i=0; $i<count($arr_pupil_date); $i++){
				if($arr_pupil_date[$i]['code_change']!=6&&$arr_pupil_date[$i]['code_change']!=2&&$arr_pupil_date[$i]['code_change']!=5&&$arr_pupil_date[$i]['code_change']!=4){
					if($row_t['id']==$arr_pupil_date[$i]['id_teacher']){
						$count_hour++;
                        $j = $row_t['id'];
						$temp_arr  = array ($row_date['date'], $row_s['id'], $arr_pupil_date[$i]['id_branch'], $j);
						array_push($arr_hour_t, $temp_arr);
						$temp_arr = array();
						$oplata+=$arr_pupil_date[$i]['tarif_teacher'];
						break;
					}
				}
			}	
		/////////////////////////////////////////////////////////////////

		for($i=0; $i<count($arr_pupil_date); $i++){
			if($row_t['id']==$arr_pupil_date[$i]['id_teacher']){
				if(!in_array($arr_pupil_date[$i]['pupil_id'], $temp_count)) array_push($temp_count, $arr_pupil_date[$i]['pupil_id']);
				}
			if($row_t['id']==$arr_pupil_date[$i]['id_teacher']&&
				$row_date['weekday']==$arr_pupil_date[$i]['id_day']&&
				($arr_pupil_date[$i]['code_change']==1||$arr_pupil_date[$i]['code_change']==3)){
					$count_pup_hour++;
					$count_pupil++;
					}
				}
			if(($count_pupil*$tax_teacher)<$min_tax&&$count_pupil>0){
				$final_cash+=$min_tax;
				}else{
					$final_cash+=($count_pupil*$tax_teacher);
					}
			}
			//////////////////////////
			$check_final_cash = $oplata;

			//////////////////////////
		}
		echo '<td>'.count($temp_count).'</td>';
		/*echo '<td>'.$count_pup_hour.'</td>';
		echo '<td>'.$final_cash.'</td>';*/
		echo '<td>'.$count_hour.'</td>';
		echo '<td>'.$check_final_cash.'</td>';
		echo '<td><button class="hours" id_teacher="'.$row_t['id'].'" name_teacher="'.$row_t['name_teacher'].'" from_date="'.$from_date.'" to_date="'.$to_date.'">=></button></td>';
		echo '</tr>';
		/*echo '<pre>';
		print_r($arr_hour_t);
		echo '</pre>';*/
		$count_p++;
		$global_cash = $global_cash + $check_final_cash;
			} elseif($_COOKIE['id_teacher']==$row_t['id']) {
				echo '<tr class="legend_all"><td>'.$count_p.'</td><td>'.$row_t['name_teacher'].'</td>';
		//Данные на каждого ученика
		/*$res_pupil = db_connect("SELECT DISTINCT schedule.pupil_id FROM schedule INNER JOIN pupil ON schedule.pupil_id=pupil.id WHERE pupil.id_teacher='".$row_t['id']."' 										                                   AND schedule.code_change='1' AND (from_date<='$from_date' AND to_date>='$to_date')");*/
		//echo '<td>'.$tax_teacher.'</td>';
		
		$res_date = db_connect("SELECT * FROM calendar 
							WHERE date>='".$from_date."' AND date<='".$to_date."'");
		while($row_date = mysqli_fetch_assoc($res_date)){
		/*=======================Проверка каникул==================*/
		$check_holiday = db_connect("SELECT * FROM holiday WHERE from_date<='".$row_date['date']."' AND to_date>='".$row_date['date']."'");
		if(mysqli_num_rows($check_holiday)>0) continue;
		/*========================================================*/
		
		$test_lesson = CheckTestLessonAllBranch($row_date['date'], $row_t['id']);
		while ($row_tl = mysqli_fetch_assoc($test_lesson)){
			$oplata+=$row_tl['tarif_teacher'];
			$count_hour++;
		}
		
		$res_shifts = db_connect("SELECT * FROM shifts");
		while($row_s = mysqli_fetch_assoc($res_shifts)){
		$count_pupil = 0;	
		$arr_pupil_date = [];
		$arr_pupil_date = getDataAllBranch($row_date['date'], $row_s['id']);
		/////////////////////////////////////////////////////////////////

			for($i=0; $i<count($arr_pupil_date); $i++){
				if($arr_pupil_date[$i]['code_change']!=6&&$arr_pupil_date[$i]['code_change']!=2&&$arr_pupil_date[$i]['code_change']!=5&&$arr_pupil_date[$i]['code_change']!=4){
					if($row_t['id']==$arr_pupil_date[$i]['id_teacher']){
						$count_hour++;
						$oplata+=$arr_pupil_date[$i]['tarif_teacher'];
						break;
					}
				}
			}	
		/////////////////////////////////////////////////////////////////
		for($i=0; $i<count($arr_pupil_date); $i++){
			if($row_t['id']==$arr_pupil_date[$i]['id_teacher']){
				if(!in_array($arr_pupil_date[$i]['pupil_id'], $temp_count)) array_push($temp_count, $arr_pupil_date[$i]['pupil_id']);
				}
			if($row_t['id']==$arr_pupil_date[$i]['id_teacher']&&
				$row_date['weekday']==$arr_pupil_date[$i]['id_day']&&
				($arr_pupil_date[$i]['code_change']==1||$arr_pupil_date[$i]['code_change']==3)){
					$count_pup_hour++;
					$count_pupil++;
					}
				}
			if(($count_pupil*$tax_teacher)<$min_tax&&$count_pupil>0){
				$final_cash+=$min_tax;
				}else{
					$final_cash+=($count_pupil*$tax_teacher);
					}
			}
			//////////////////////////
            $check_final_cash = $oplata;
			//////////////////////////
		}
		echo '<td>'.count($temp_count).'</td>';
		/*echo '<td>'.$count_pup_hour.'</td>';
		echo '<td>'.$final_cash.'</td>';*/
		echo '<td>'.$count_hour.'</td>';
		echo '<td>'.$check_final_cash.'</td>';
		echo '<td><button class="hours" id_teacher="'.$row_t['id'].'" name_teacher="'.$row_t['name_teacher'].'" from_date="'.$from_date.'" to_date="'.$to_date.'">=></button></td>';
		$count_p++;
				}
			
		}
	echo '</tr><tr class="strong"><td colspan="4">Итого:</td><td>'.$global_cash.'</td><td></td></tr>';
	?>
</table>
</div>
</div>
<br>
<?php if($_COOKIE['id_teacher']==999){ ?>
<div class="flex-center">
	<button id="pay_out" class="month">Выплаты</button>		
</div>
<?php }else{?>
	<div class="flex-center">
	<button class="month" disabled>Выплаты</button>		
</div>
<?php }?>
<div class="container">
<?php 
$res_data_payment = db_connect("SELECT payments.*, teachers.name_teacher FROM payments INNER JOIN teachers ON payments.id_teacher=teachers.id ORDER BY payments.date DESC LIMIT 50");
echo '<table class="oplata"><thead>';
//Добавить шапку
$count_pays = 1;
echo '<tr class="legend">
		<th style="width:25px;">№</th>
		<th style="width:100px;">Дата</th>
		<th style="width:150px;">Учитель</th>
		<th style="width:100px;">С числа:</th>
		<th style="width:100px;">По число:</th>
		<th style="width:100px;">Сумма</th>
		<th style="width:100px;">Часов</th>
		<th style="width:100px;">Отметка</th>';
		if($_COOKIE['id_teacher']==999){
			echo '<th style="width:50px;">Удалить</th>';
		}
echo '</tr>';
while($row_dp=mysqli_fetch_assoc($res_data_payment)){
	$date = substr($row_dp['date'], 0, 10);
	if($_COOKIE['id_teacher']==999){
	echo "<tr class='legend_all'>
			<td>{$count_pays}</td>
			<td>".formateDate($date)."</td>
			<td>{$row_dp['name_teacher']}</td>
			<td>".formateDate($row_dp['from_date'])."</td>
			<td>".formateDate($row_dp['to_date'])."</td>
			<td>{$row_dp['paid']}</td>
			<td>{$row_dp['pupil_hour']}</td>";
			if($row_dp['key_p']==1){
				echo "<td id='".$row_dp['id']."' class='key_1' style='background-color:#8cbf43'>Выплачено</td>";
			} else {
				echo "<td id='".$row_dp['id']."' class='key_0'>-</td>";
				}
			echo "<td><form>
					<button name='butt_del' class='butt_del' item_id='".$row_dp['id']."'>x</button>
    		  	</form></td>
		</tr>";
	$count_pays++;
	} elseif ($_COOKIE['id_teacher']==$row_dp['id_teacher']){
		echo "<tr class='legend_all'>
				<td>{$count_pays}</td>
				<td>".formateDate($date)."</td>
				<td>{$row_dp['name_teacher']}</td>
				<td>".formateDate($row_dp['from_date'])."</td>
				<td>".formateDate($row_dp['to_date'])."</td>
				<td>{$row_dp['paid']}</td>
				<td>{$row_dp['pupil_hour']}</td>";
				if($row_dp['key_p']==1){
				echo "<td id='".$row_dp['id']."' style='background-color:#8cbf43'>Выплачено</td>";
			} else {
				echo "<td id='".$row_dp['id']."'>-</td>";
				}
			echo "</tr>";
		}
}
echo '</thead></div>';
/*echo '</table>';
echo '<pre>';
print_r(getDataAllBranchVED('2018-10-29', '1'));
echo '</pre>';*/
?>

<div id="pays">
<form method="post" action="<?=$_SERVER['SCRIPT_NAME']?>" id="form_pay">
	<label for="id_teacher">Преподаватель:</label>
    <select name="id_teacher" id="id_teacher">
            	<?php $res_s_p = db_connect("SELECT * FROM teachers");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_teacher'].'</option>';
				}?>
    </select><br>
    <label for="from_date">С числа:</label>
    <input type="date" name="from_date" id="from_date"><br>
    <label for="to_date">По число:</label>
    <input type="date" name="to_date" id="to_date"><br>
    <input type="hidden" name="form_pay_out" value="form_pay_out"><br>
   	<button id="temp_payments">Расчет</button><span id="text_payments"></span>
    <input type="hidden" value="" name="cash_for_bd" id="hidden_cash">
    <input type="hidden" value="" name="count_pupil" id="count_pupil">
    <!--<input type="submit" name="but_pay" value="Выплатить" id="but_pay">-->
</form>
</div>
</div>






<script type="text/javascript">
$(document).ready(function() {
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
		//Временно
	$('#pays input, #pays button, .flex-center button').button();
	$('#temp_payments').click(function(e) {
        e.preventDefault();
		var data_payment = $('#form_pay').serializeArray();
				$.post('scripts/get_data.php?temp_payment=temp_payment', data_payment, function(json){
					if(json.success==="success"){
						$('#text_payments').html('');
						$('#text_payments').append('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Сумма: '+json.final_cash+' ');
						$('#text_payments').append('&nbsp;&nbsp;&nbsp;часы: '+json.count_pupil_hour);
						}
					},"json");
    });
	$('#pays').dialog({
		autoOpen: false,
		modal:true,
		title:"Выплаты",
		//close:function(){location.reload()},
		width: 555,
		buttons:{
			"Добавить":function(){
				var data_payment = $('#form_pay').serializeArray();
				$.post('scripts/get_data.php?temp_payment=temp_payment', data_payment, function(json){
					if(json.success==="success"){
						$('#hidden_cash').attr('value', json.final_cash);
						$('#count_pupil').attr('value', json.count_pupil_hour);
					var data = $('#form_pay').serializeArray();
				$.post('payments.php', data, function(json){
					if(json.success==="success"){
						location.reload();
						}
					},"json");
					}
					},"json");
				}
			}
		
	});

       $('#accurate').dialog({
            autoOpen: false,
            modal: true,

            //close:function(){location.reload()},
            width: 860
        });

	$('#pay_out').click(function(e) {
        e.preventDefault();
		$('#pays').dialog('open');
		$('#id_teacher').selectmenu({
			width:218,
			icons:{
				button:"ui-icon-circle-triangle-s"
				}
			});
    });
	//Удаление выплат учителям
	$('#del_payment_form').submit(function(e) {
        return false;
    });
	$('#success').dialog({
			autoOpen: false,
			modal:true,
			open: function(event, ui) {
      			$(this).parent().find('div.ui-dialog-titlebar').addClass('warning');
			},
			//width: 520,
			//close:function(){location.reload()},
			buttons:{
			"Удалить" : function(){
				var data = $('#del_payment_form').serializeArray();
				$.post('scripts/add_data.php?del_payment=del_payment', data, function(){}, "json");
				location.reload();
				}
			}
		});
	$('.butt_del').click(function(e) {
			e.preventDefault();
			$('#success').dialog('open');
			$('#success').html('');
			$('#success form').html('');
			tmp_str='';
			tmp_str+='<h3>Удалить запись?</h3>';
			tmp_str+='<form id="del_payment_form">';
			tmp_str+='<input type="hidden" name="item_id" value="'+$(this).attr('item_id')+'">';
			tmp_str+='</form>';
			$('#success').append(tmp_str);
		});
	});
	//Получение данных для точного графика
    $('.hours').click(function (e) {
        e.preventDefault();
        $('#accurate').html('');
        $('#accurate').load('modalview.php?teacher_id="'+$(this).attr('id_teacher')+'"&from_date="'+
            $(this).attr('from_date')+'"&to_date="'+$(this).attr('to_date')+'"');
        $('#accurate').dialog({title:"Статистика - "+$(this).attr('name_teacher')});
        $('#accurate').dialog('open');
    })
	//Отметка об оплате
	$('#success_pay').dialog({
			autoOpen: false,
			modal:true,
			//width: 520,
			//close:function(){location.reload()},
			buttons:{
			"Добавить отметку" : function(){
				var data = $('#add_payment_form').serializeArray();
				$.post('scripts/add_data.php?add_payment=add_payment', data, function(){}, "json");
				location.reload();
				}
			}
		});
		
	$('#cancel_pay').dialog({
			autoOpen: false,
			modal:true,
			//width: 520,
			//close:function(){location.reload()},
			buttons:{
			"Убрать отметку" : function(){
				var data = $('#cancel_payment_form').serializeArray();
				$.post('scripts/add_data.php?cancel_payment=cancel_payment', data, function(){}, "json");
				location.reload();
				}
			}
		});	
	$('.key_0, .key_1').hover(function(){
	$(this).css({'background-color' : '#DC7713',
	'cursor':'pointer'});
	}, function(){
		$(this).css({'background-color' : '',
		'cursor' : ''});
		});
	$('.key_1').hover(function(){
	$(this).css({'background-color' : '#DC7713',
	'cursor':'pointer'});
	}, function(){
		$(this).css({'background-color' : '#8cbf43',
		'cursor' : ''});
		});
	$('.key_0').click(function (e){
		e.preventDefault();
		$('#success_pay').dialog('open');
		$('#success_pay').html('');
		$('#success_pay form').html('');
		tmp_str='';
			tmp_str+='<form id="add_payment_form">';
			tmp_str+='<input type="hidden" name="id_payment" value="'+$(this).attr('id')+'">';
			tmp_str+='</form>';
			$('#success_pay').append(tmp_str);	
	})
	$('.key_1').click(function (e){
		e.preventDefault();
		$('#cancel_pay').dialog('open');
		$('#cancel_pay').html('');
		$('#cancel_pay form').html('');
		tmp_str='';
			tmp_str+='<form id="cancel_payment_form">';
			tmp_str+='<input type="hidden" name="id_payment" value="'+$(this).attr('id')+'">';
			tmp_str+='</form>';
			$('#cancel_pay').append(tmp_str);	
	})
});
</script>
<div id="success" title="Внимание...">
</div>
<div id="success_pay" title="Отметка об оплате...">
</div>
<div id="cancel_pay" title="Отметка об оплате...">
</div>
</body>
</html>
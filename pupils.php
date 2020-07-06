<?php include ("scripts/dbconnect.php");
if(empty($_GET['p'])) header('Location:'.$_SERVER['PHP_SELF'].'?p=now&pupil_id='.$_GET['pupil_id']);
session_start();
if(!empty($_POST['id_branch'])){
	$_SESSION['id_branch']=$_POST['id_branch'];
	setcookie('id_branch', '', time()-3600);
	setcookie('id_branch', $_SESSION['id_branch'], time()+3600);
	}
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])){
header('Location: login.php');
}
include ("function.php");
$month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
$week = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");
if(isset($_POST['pupil_id'])){
	$pupil_id = '';
}else {
	$pupil_id = $_GET['pupil_id'];
	}

$count_day_in_month = date('t', $mark_time);

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
?>


<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Ученики</title>
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
<?php include('scripts/navmenu.php');
if($pupil_id==''){ //если нет пупил id то выводим всех
?>
</div>
<!---->
<div class="container">
	<form method="post" action="#" id="branches">
    <?php
	$res_branch = db_connect("SELECT * FROM branches");
	$count_id = 1;
	while($row_b = mysqli_fetch_assoc($res_branch)){
		if($count_id==$_SESSION['id_branch']){
			echo '<button name="id_branch" id_attr_branch="'.$_SESSION['id_branch'].'" value="'.$row_b['id'].'" style="background-color:#3D6C80; color:white;">'.$row_b['name_branch'].'</button>';
			} else echo '<button name="id_branch" value="'.$row_b['id'].'">'.$row_b['name_branch'].'</button>';
			$count_id++;
		}
	?>
    </form>
</div>
<!---->
<div class="container">
<div class="table">
<table class="oplata">
	<tr class="legend">
    	<th style="width:25px;">п</th>
		<th style="width:150px;">ФИО</th>
		<th style="width:200px;" colspan="2">1-е занятие</th>
		<th style="width:200px;" colspan="2">2-е занятие</th>
        <th style="width:200px;" colspan="2">3-е занятие</th>
        <th style="width:200px;" colspan="2">4-е занятие</th>
        <th style="width:200px;" colspan="2">5-е занятие</th>
		<!--<th style="width:150px;">Педагог</th>-->
	</tr>
<?php
$res_data_pupil = db_connect("SELECT DISTINCT pupil.FIO, pupil.id, teachers.name_teacher, schedule.id_branch FROM pupil 
							INNER JOIN schedule ON pupil.id=schedule.pupil_id 
							INNER JOIN teachers ON schedule.id_teacher=teachers.id 
							WHERE schedule.code_change='1' AND schedule.to_date='2031-04-04' 
															AND schedule.id_branch='".$_SESSION['id_branch']."'
															ORDER BY pupil.FIO ASC");
	$count_p = 1;
	$temp_arr = array();
	while($row_dp = mysqli_fetch_assoc($res_data_pupil)){
		if(in_array($row_dp['id'], $temp_arr)) continue;
		array_push($temp_arr, $row_dp['id']);
		echo '<tr class="legend_all">
				<td>'.$count_p.'</td>
				<td class="left_align"><a style="color:#0067F4; text-decoration:none;" href="'.$_SERVER['PHP_SELF'].'?pupil_id='.$row_dp['id'].'">'.$row_dp['FIO'].'</a></td>';
				$res_schedule = db_connect("SELECT schedule.id_day, schedule.pupil_id, shifts.shifts 
											FROM schedule 
											INNER JOIN shifts ON schedule.id_shift=shifts.id 
											WHERE schedule.pupil_id='".$row_dp['id']."' AND schedule.code_change='1' 
																						AND schedule.to_date='2031-04-04' 
																						AND schedule.id_branch='".$_SESSION['id_branch']."'
																						ORDER BY schedule.id_day ASC");
				$count_row = mysqli_num_rows($res_schedule);
				while($row_s=mysqli_fetch_assoc($res_schedule)){
					
					echo '<td style="width:120px;">'.$week[$row_s['id_day']-1].'</td><td style="width:50px;">'.$row_s['shifts'].'</td>';
					if($count_row==1){
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						}
					}
					if($count_row==2){
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						}
					if($count_row==3){
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						}
					if($count_row==4){
						echo '<td style="width:120px;">-</td><td style="width:50px;">-</td>';
						}
				echo '</tr>';
	$count_p++;
	}
	echo '</table></div></div>';
} else { //Если есть pupil_id
$array = PupilDataNow($pupil_id, '1', 'to_date', $from_date);
$res_FIO = db_connect("SELECT * FROM pupil WHERE id='$pupil_id'");
$row_FIO = mysqli_fetch_assoc($res_FIO);
$all_sum = 0;
$sum_less = 0;
$FIO = $array[0]['FIO'];//ФИО ученика
?>
<div class="flex-center">
	<button class="month">Личная карточка: <span style="color:#4369FF;">
	<? echo $row_FIO['FIO'].' (#'.$pupil_id.')'; ?></span></button>
</div>
<div class="flex-center">
<div class="month arrow">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=prev&pupil_id='.$_GET['pupil_id']?>"><<</a>
</div>
<div class="month center">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=now&pupil_id='.$_GET['pupil_id']?>">Текущий месяц</a>
</div>
<div class="month arrow">
	<a href="<?php echo $_SERVER['PHP_SELF'].'?p=next&pupil_id='.$_GET['pupil_id']?>">>></a>
</div>
</div>
<div class="flex-center">
	<div class="month center">
		<a><?php echo $month[$k];?></a>
    </div>
</div>
<div class="container section">
	<span>Уроки</span><button id="add_lesson_card">+</button>
</div>
<div class="container">
<div class="table">
	<table class="oplata">
	<tr class="legend">
    	<th style="width:25px;">п</th>
		<th style="width:100px;">Филиал</th>
		<th style="width:100px;">День недели</th>
		<th style="width:70px;">Время</th>
        <th style="width:150px;">Программа</th>
        <th style="width:150px;">Учитель</th>
        <th style="width:150px;" colspan="2">Период обучения</th>
        <th style="width:50px;">Тариф</th>
        <th style="width:100px;" colspan="2">Уроков|Сумма</th>
        <th class="hide" style="width:100px;">Когда добавлен</th>
        <th class="hide" style="width:150px;">Админ</th>
        <th class="hide" style="width:25px;">Редактор</th>
        <th class="hide" style="width:25px;">Удалить</th>
    </tr>

	<?php 
	for($i=0; $i<count($array); $i++){
		$count_less = 0;
		echo '<tr class="legend_all">
		<td>'.($i+1).'</td>
		<td>'.$array[$i]['name_branch'].'</td>
		<td>'.$week[$array[$i]['id_day']-1].'</td>
		<td>'.$array[$i]['shifts'].'</td>
		<td style="background-color:'.$array[$i]['bg_color'].'; color:'.$array[$i]['color'].'">'.$array[$i]['name'].'</td>
		<td>'.$array[$i]['name_teacher'].'</td>';		
		
		$a=strtotime($from_date);
		$from_date1 = date('Y-m', $a);
		$sum_days = date('t', $a);
		unset($day);
		for($day=1; $day<=$sum_days; $day++){
			
			if($day<10){$day='0'.$day;}else{$day = $day;}
			$temp_date='';
			$temp_date = $from_date1.'-'.$day;
			$res_oplata = db_connect("SELECT schedule.*, calendar.date, calendar.weekday
										FROM schedule  
										INNER JOIN calendar ON schedule.id_day=calendar.weekday
										WHERE pupil_id='$pupil_id' 
										AND schedule.id_day = '".$array[$i]['id_day']."' 
										AND schedule.id_shift='".$array[$i]['shifts_id']."' 
										AND schedule.id_branch= '".$array[$i]['branch_id']."'
										AND (schedule.from_date<='$temp_date' AND '$temp_date'<=schedule.to_date) 
										AND calendar.date='$temp_date'
										AND (code_change='1' OR code_change='4')
										ORDER BY code_change DESC");
			if($res_oplata!='' and mysqli_num_rows($res_oplata)>0){
				while($row_o = mysqli_fetch_assoc($res_oplata)){
					if($row_o['code_change']==4)
					{
						break;
					}else{
						//Проверка каникул
						$res_hol = db_connect("SELECT * FROM holiday WHERE holiday.from_date<='$temp_date' AND holiday.to_date>='$temp_date'");
						if($res_hol!='' and mysqli_num_rows($res_hol)>0) continue;
						/////////
						$count_less++;
						}
					}
				}
			}
		echo '<td>'.formateDate($array[$i]['from_date']).'</td>
		<td style="min-width:65px;">';
			if(formateDate($array[$i]['to_date'])=='04.04.2031'){
				echo '-</td>';
			}else {
				echo formateDate($array[$i]['to_date']).'</td>';}
		echo'</td>
		<td>'.$array[$i]['tarif_pupil'].'</td>
		<td style="width:40px;">'.$count_less.'</td><td>'.$count_less*$array[$i]['tarif_pupil'].'</td>';
		$all_sum += $count_less*$array[$i]['tarif_pupil'];
		$sum_less += $count_less;
		////////////////////////////////////////////////////
		echo '<td class="hide">'.formateDate(substr($array[$i]['date'], 0, 10)).'</td>
		<td class="hide">'.$array[$i]['loger'].'</td>';
		
		if($_COOKIE['user_group']==1){
			echo'
                <td class="hide"><button name="change_graph_but" item_id="'.$array[$i]['id'].'" class="change_graph_but">Изменить</button>
    		
		</td>
		<td class="hide"><form>
				<button name="butt_del" class="butt_del" item_id="'.$array[$i]['id'].'">x</button>
    		</form></td>
		</tr>';
		}else{
			echo '</td></tr>';
			}
	};
	?>
	
    </table><button id="hide">↓</button></div></div>
<div class="flex-center">
	<div class="month center">
		<a href="#" id="hide_disc"><?php echo 'ИТОГО: '.$all_sum?></a>
    </div>
    <div class="hide_disc month center">
    	<a><?php echo 'СО СКИДКОЙ: '.($all_sum - $sum_less*50)?></a>
    </div>
</div>
<div class="container section">
	<span>Пропуски по болезни</span>
</div>
<div class="container">
<div class="table">
	<table class="oplata">
	<tr class="legend">
    	<th style="width:25px;">п</th>
		<th style="width:150px;">Филиал</th>
		<th style="width:150px;">День недели</th>
		<th style="width:100px;">Время</th>
        <th style="width:200px;">Учитель</th>
        <th style="width:100px;">Дата</th>      
	</tr>
<?php 
$array_sick = PupilDataNow($pupil_id, '5', 'from_date', $from_date);
for($i=0; $i<count($array_sick); $i++){
		echo '<tr class="legend_all">
		<td>'.($i+1).'</td>
		<td>'.$array_sick[$i]['name_branch'].'</td>
		<td>'.$week[$array_sick[$i]['id_day']-1].'</td>
		<td>'.$array_sick[$i]['shifts'].'</td>
		<td>'.$array_sick[$i]['name_teacher'].'</td>
		<td>'.formateDate($array_sick[$i]['from_date']).'</td>';
		}

?>
    </table></div></div>
<div class="container section">
	<span>Пропуски</span>
</div>
<div class="container">
<div class="table">
	<table class="oplata">
	<tr class="legend">
    	<th style="width:25px;">п</th>
		<th style="width:150px;">Филиал</th>
		<th style="width:150px;">День недели</th>
		<th style="width:100px;">Время</th>
        <th style="width:200px;">Учитель</th>
        <th style="width:100px;">Дата</th>      
	</tr>
<?php 
$array_empty = PupilDataNow($pupil_id, '6', 'from_date', $from_date);
for($i=0; $i<count($array_empty); $i++){
		echo '<tr class="legend_all">
		<td>'.($i+1).'</td>
		<td>'.$array_empty[$i]['name_branch'].'</td>
		<td>'.$week[$array_empty[$i]['id_day']-1].'</td>
		<td>'.$array_empty[$i]['shifts'].'</td>
		<td>'.$array_empty[$i]['name_teacher'].'</td>
		<td>'.formateDate($array_empty[$i]['from_date']).'</td>';
		}
	}//конец else
?>
</table></div></div>
        
<!--Модальные окна-->
<div id="warning" title="Внимание..."></div>
    <div id="form_change_graph"></div>
    <div id="form_add_lesson_card" title="<?=$FIO?> - добавление урока">
        <form id="form_add_lesson" method="post">
            <input type="hidden" name="add_one_lesson" value="add_one_lesson">
        	<label for="from_date">Начало обучения: </label>
        	<input type="date" name="from_date" id="from_date" required><br>
        <?php
        echo '<input type="hidden" name="pupil_id" value="'.$pupil_id.'">';
        $res = db_connect("SELECT * FROM programms");
        echo '<label>Программа: </label><select name = "programm" id="select_programm">';
        echo "<option>Выберите программу</option>";
        while($row=mysqli_fetch_assoc($res)) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
            echo '</select><br>';
        ?>
            <div id="form_data_jq"></div>
        </form>
    </div>
    
<script type="text/javascript">
$(document).ready(function() {
	//Скрыть/показать часть таблицы
	$('#hide').click(function(){
		$('.hide').fadeToggle();
		});
	$('#hide_disc').click(function(){
		$('.hide_disc').fadeToggle();
		});
	//Стлизация кнопок
	$('.flex-center button, #branches button').button();
	
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
	});
	//Удаление урока
	$('#warning').dialog({
			autoOpen: false,
			modal:true,
			open: function() {
      			$(this).parent().find('div.ui-dialog-titlebar').addClass('warning');
			},
			//width: 520,
			close:function(){location.reload()},
			buttons:{
			"Удалить" : function(){
				data = $('#del_less_form').serializeArray();
				$.post('scripts/add_data.php?del_less=del_less', data, function(){}, "json");
				location.reload();
                location.reload();
				}
			}
		});
	$('#form_change_graph').dialog({
        autoOpen: false,
        modal:true,
        buttons:{
            "Сохранить" : function () {
                data = $('#data_graph').serializeArray();
                $.post('scripts/add_data.php?mode=change_graph_date', data, function(){}, "json");
                location.reload();
            }
        }
    });
	$('.butt_del').click(function(e) {
			e.preventDefault();
			$('#warning').dialog('open');
			$('#warning form').html('');
        var tmp_str = '';
			tmp_str+='<h3>Удалить запись?</h3>';
			tmp_str+='<form id="del_less_form">';
			tmp_str+='<input type="hidden" name="item_id" value="'+$(this).attr('item_id')+'">';
			tmp_str+='</form>';
			$('#warning').append(tmp_str);
		});
	$('.change_graph_but').click(function (e) {
        e.preventDefault();
        $('#form_change_graph').html('');
        $('#form_change_graph').append('<form id="data_graph"><input type="hidden" name="id_item" value="'+$(this).attr('item_id')+'">');
        $('#data_graph').append('Конец обучения<input type="date" name="to_date" value="">');
        $('#form_change_graph').append('</form>');
        $('#form_change_graph').dialog({title:'Редактирование конца'});
        $('#form_change_graph').dialog('open');
        });
    /*Диалоговое окно с формой для создания нового урока с применением программ и графика*/
	$('#form_add_lesson_card input, select').button();
    $('#form_add_lesson_card').dialog({
        autoOpen: false,
        modal:true,
        width: 470,
        close:function(){location.reload()}
    });
    /*Создание формы добавления урока*/
    $('#add_lesson_card').click(function (e) {
        e.preventDefault();
        $('#select_programm').css('background-color', 'Salmon');
        $('#select_programm').change(function(){
            if($(this).val()>0){$(this).css('background-color', 'LimeGreen')}else{$(this).css('background-color', 'Salmon')}
            $('#select_branch').remove();
            $('#day').remove();
            $('.branch, .day, .shift').remove();
            $('#shift').remove();
            data = new Object();
            data.q='getprog';
            data.prog=$(this).val();
            $.getJSON('scripts/get_data.php', data, function(json){
               // branches = ['Ленина', 'Славского', 'Менделеева'];
                $('#form_data_jq').append('<label class="branch">Филиал: </label><select name="branch" id="select_branch" class="ui-button ui-corner-all ui-widget"><option>Выберите филиал</option>');
                var branch_check;
                $.each(json, function(){
                    if(branch_check==this.branch) return;
                    $('#select_branch').append('<option value="' + this.branch + '">' + this.branch_name + '</option>');
                    branch_check=this.branch;
                });
                $('#form_data_jq').append('</select>');

                /* Запрос на день*/
                $('#select_branch').css('background-color', 'Salmon');
                $('#select_branch').change(function () {
                    if($(this).val()>0){$(this).css('background-color', 'LimeGreen')}else{$(this).css('background-color', 'Salmon')}
                    $('#day').remove();
                    $('#shift').remove();
                    $('.day, .shift').remove();
                    data.q='get_day';
                    data.branch = $(this).val();
                    $.getJSON('scripts/get_data.php', data, function(json){
                    days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
                    $('#select_branch').after('<label class="day">Дни недели: </label><select name="day" id="day" class="ui-button ui-corner-all ui-widget"><option>Выберите день недели</option>');
                    $('#day').css('background-color', 'Salmon');
                        $.each(json, function () {
                            $('#day').append('<option value="'+this.day+'">'+days[this.day-1]+'</option>');
                        });
                        $('#day').append('</select>');
                    $('#day').change(function () {
                        $('#shift').remove();
                        $('.shift').remove();
                        if($(this).val()>0){$(this).css('background-color', 'LimeGreen')}else{$(this).css('background-color', 'Salmon')}
                        $('#day').after('<label class="shift">Время: </label><select name="shift" id="shift" class="ui-button ui-corner-all ui-widget"><option>Выберите время</option>');
                        $('#shift').css('background-color', 'Salmon');
                        data.q='get_shift';
                        data.day=$(this).val();
                        $.getJSON('scripts/get_data.php', data, function(json){
                            $.each(json, function(){
                                $('#shift').append('<option value="'+this.shift+'">'+this.shifts+'</option>');
                            })
                        });
                        $('#shift').append('</select');
                        $('#shift').change(function () {
                            if($(this).val()>0){$(this).css('background-color', 'LimeGreen');
                            $('#form_data_jq').append('<br><input type="submit" value="Добавить" id="but_add_lesson" class="ui-button ui-corner-all ui-widget">')
                                $('#but_add_lesson').click(function (e) {
                                    e.preventDefault();
                                    data_less = $('#form_add_lesson').serializeArray();
                                    $.post('scripts/add_data.php', data_less, function(json){},"json");
                                    location.reload();
                                })
                            }else{$(this).css('background-color', 'Salmon')}
                        });
                    })
                })
            })
         })

        });
		$('#form_add_lesson_card select, input').button();
        $('#form_add_lesson_card').dialog('open');
    });

});
</script>
</body>
</html>
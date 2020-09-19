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
<title>Талоны</title>
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
	<button class="month">Список действующих талонов</button>
	<button class="month" id="butt_add_tickets">Добавить</button>
</div>
<div class="table">
	<table class="oplata">
    <thead>
		<tr class="legend">
        	<th style="width:25px;">п</th>
            <th style="width:200px;">ФИО ученика</th>
            <th style="width:75px;">Кол. талонов</th>
            <th style="width:100px;">Убрать талон</th>
            <th style="width:100px;">Добавить талон</th>
			<th style="width:100px;">Удалить запись</th>
		</tr>
<?php 
	//Получение данных о талонах
	$res_tickets = db_connect("SELECT * FROM pupil WHERE tickets>0");
	$count_p = 1;
	while($row_t = (mysqli_fetch_assoc($res_tickets))){
		$date = substr($row_t['date'], 0, 10);
		echo '<tr class="legend_all">
				<td>'.$count_p.'</td>
				<td><a style="color:#0067F4; text-decoration:none;" href="pupils.php?pupil_id='.$row_t['id'].'">'.$row_t['FIO'].'</a></td>
				<td>'.$row_t['tickets'].'</td>';
				
			echo '<td>
					<form>
						<button name="butt_del" class="butt_del" item_id="'.$row_t['id'].'" tickets="'.$row_t['tickets'].'">-</button>
    		  		</form>
				</td>
				<td>
					<form>
						<button name="butt_add" class="butt_add" item_id="'.$row_t['id'].'" tickets="'.$row_t['tickets'].'">+</button>
    		  		</form>
				</td>
				<td>
					<form>
						<button name="butt_del_item" class="butt_del_item" item_id="'.$row_t['id'].'">x</button>
    		  		</form>
				</td>
			</tr>';
		$count_p++;
	}
?>
</thead>
</table>
</div>
</div>

<div class="column">
<div class="flex-center">
	<button class="month">Посещения по талонам</button>
</div>
<div class="table">
	<table class="oplata">
    <thead>
		<tr class="legend">
        	<th style="width:25px;">п</th>
            <th style="width:150px;">ФИО ученика</th>
            <th style="width:75px;">Дата</th>
            <th style="width:100px;">Филиал</th>
            <th style="width:75px;">День</th>
			<th style="width:40px;">Время</th>
            <th style="width:150px;">Программа</th>
            <th style="width:150px;">Педагог</th>
         </tr>
<?php
	$res_tickets_use = db_connect("SELECT schedule.id_day, schedule.from_date, pupil.FIO, schedule.pupil_id, shifts.shifts
									AS shifts_id, branches.name_branch
									AS branch_id, teachers.name_teacher, programms.name, programms.bg_color, programms.color
									FROM schedule
									INNER JOIN pupil ON schedule.pupil_id=pupil.id
									INNER JOIN shifts ON schedule.id_shift=shifts.id
									INNER JOIN branches ON schedule.id_branch=branches.id
									INNER JOIN teachers ON schedule.id_teacher=teachers.id
									INNER JOIN graph ON (schedule.id_day=graph.day 
														AND schedule.id_shift=graph.shift 
														AND schedule.id_branch=graph.branch)
									INNER JOIN programms ON graph.programm=programms.id
									WHERE code_change='7' ORDER BY from_date DESC");
	$count_p = 1;
	while ($row_t_u = mysqli_fetch_assoc($res_tickets_use)){
			echo '<tr class="legend_all">
				<td>'.$count_p.'</td>
				<td><a style="color:#0067F4; text-decoration:none;" href="pupils.php?pupil_id='.$row_t_u['pupil_id'].'">'.$row_t_u['FIO'].'</a></td>
				<td>'.formateDate($row_t_u['from_date']).'</td>
				<td>'.$row_t_u['branch_id'].'</td>
				<td>'.$week[$row_t_u['id_day']-1].'</td>
				<td>'.$row_t_u['shifts_id'].'</td>
				<td style="background-color:'.$row_t_u['bg_color'].'; color:'.$row_t_u['color'].'">'.$row_t_u['name'].'</td>
				<td>'.$row_t_u['name_teacher'].'</td>';
			$count_p++;
		}

?>
    </thead>
</table>
</div>
</div>
<!--Окно добавления талонов-->
<div id="add_tickets" title="Электронные талоны">
	<form id="form_add_tickets">
    		<input type="search" id="id_search" name="FIO" placeholder="Введите фамилию"><br><br>
			<label for="tickets">Кол-во талонов</label>
			<input type="number" id="tickets" size="3" name="num" min="1" max="10" value="1">
		<br><br>        
	</form>
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

	//Удаление пробных уроков
	$('#del_tickets_form').submit(function() {
        return false;
    });
	$('#add_tickets_form').submit(function() {
        return false;
    });
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
				data = $('#del_tickets_form').serializeArray();
				$.post('scripts/add_data.php?del_tickets=del_tickets', data, function(){}, "json");
				location.reload();
				}
			}
		});
	$('#warning_item').dialog({
			autoOpen: false,
			modal:true,
			open: function() {
      			$(this).parent().find('div.ui-dialog-titlebar').addClass('warning');
			},
			//width: 520,
			close:function(){location.reload()},
			buttons:{
			"Удалить" : function(){
				data = $('#del_item_form').serializeArray();
				$.post('scripts/add_data.php?del_item=del_item', data, function(){}, "json");
				location.reload();
				}
			}
		});
	$('#succes').dialog({
			autoOpen: false,
			modal:true,
			open: function() {
      			$(this).parent().find('div.ui-dialog-titlebar').addClass('succes');
			},
			//width: 520,
			close:function(){location.reload()},
			buttons:{
			"Добавить" : function(){
				data = $('#add_tickets_form').serializeArray();
				$.post('scripts/add_data.php?add_tickets=add_tickets', data, function(){}, "json");
				location.reload();
				}
			}
		});
	$('.butt_del').click(function(e) {
			e.preventDefault();
			$('#warning').dialog('open');
			$('#warning form').html('');
			tmp_str='';
			tmp_str+='<h3>Удалить 1 талон?</h3>';
			tmp_str+='<form id="del_tickets_form">';
			tmp_str+='<input type="hidden" name="item_id" value="'+$(this).attr('item_id')+'">';
			tmp_str+='<input type="hidden" name="tickets" value="'+$(this).attr('tickets')+'">';
			tmp_str+='</form>';
			$('#warning').append(tmp_str);
		});
	$('.butt_del_item').click(function(e) {
			e.preventDefault();
			$('#warning_item').dialog('open');
			$('#warning_item form').html('');
			tmp_str='';
			tmp_str+='<h3>Удалить запись?</h3>';
			tmp_str+='<form id="del_item_form">';
			tmp_str+='<input type="hidden" name="item_id" value="'+$(this).attr('item_id')+'">';
			tmp_str+='</form>';
			$('#warning').append(tmp_str);
		});
	$('.butt_add').click(function(e) {
			e.preventDefault();
			$('#succes').dialog('open');
			$('#succes form').html('');
			tmp_str='';
			tmp_str+='<h3>Добавить 1 талон?</h3>';
			tmp_str+='<form id="add_tickets_form">';
			tmp_str+='<input type="hidden" name="item_id" value="'+$(this).attr('item_id')+'">';
			tmp_str+='<input type="hidden" name="tickets" value="'+$(this).attr('tickets')+'">';
			tmp_str+='</form>';
			$('#succes').append(tmp_str);
		});
	
	//Автокомплит ученика
		$('#id_search').autocomplete({
		       source: 'scripts/get_data.php'
			   //select: showpupil
		});//Конец
	$('#add_tickets').dialog({
		autoOpen: false,
		modal:true,
		buttons:{
			"Добавить" : function(){
			var data = $('#form_add_tickets').serializeArray();
			$.post('scripts/add_data.php?add_pupil_tickets=add_pupil_tickets', data, function(json){},"json");
			location.reload();
				}
			}
		})
	
	$('#butt_add_tickets').click(function(e) {
        e.preventDefault();
		$('#add_tickets').dialog('open');
	})
});
</script>
<!--Окно подтверждения-->
<div id="warning" title="Внимание..."></div>
<div id="warning_item" title="Внимание..."></div>
<div id="succes" title="Внимание..."></div>
</div>
</body>
</html>
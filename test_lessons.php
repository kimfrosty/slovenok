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
<title>Пробные уроки</title>
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
<div class="flex-center">
	<button class="month">Список пробных уроков (добавляются через "РАСПИСАНИЕ")</button>
</div>
<div class="container">
<div class="table">
	<table class="oplata">
    <thead>
		<tr class="legend">
        	<th style="width:25px;">п</th>
            <th style="width:100px;">Дата</th>
            <th style="width:200px;">ФИО учителя</th>
            <th style="width:150px;">День недели</th>
            <th style="width:150px;">Филиал</th>
            <th style="width:75px;">Время</th>
            <th style="width:150px;">Программа</th>
            <th style="width:75px;">Тариф</th>
            <th style="width:50px;">Удалить</th>
		</tr>
<?php 
	//Получение данных о больничных листах из БД и вывод в таблицу
	$res_test = db_connect("SELECT test_lessons.id, test_lessons.date, test_lessons.id_day, teachers.name_teacher, programms.name, programms.bg_color, 
										programms.color, programms.tarif_teacher, shifts.shifts, branches.name_branch 
										FROM test_lessons
										INNER JOIN teachers ON test_lessons.id_teacher=teachers.id
										INNER JOIN programms ON test_lessons.programm=programms.id
										INNER JOIN shifts ON test_lessons.id_shift=shifts.id
										INNER JOIN branches ON test_lessons.id_branch=branches.id
										ORDER BY date DESC LIMIT 50");
	$count_p = 1;
	while($row_t = (mysqli_fetch_assoc($res_test))){
		$date = substr($row_t['date'], 0, 10);
		echo '<tr class="legend_all">
				<td>'.$count_p.'</td>
				<td>'.formateDate($date).'</td>
				<td>'.$row_t['name_teacher'].'</td>
				<td>'.$week[$row_t['id_day']-1].'</td>
				<td>'.$row_t['name_branch'].'</td>
				<td>'.$row_t['shifts'].'</td>
				<td style="background-color:'.$row_t['bg_color'].'; color:'.$row_t['color'].'">'.$row_t['name'].'</td>
				<td>'.$row_t['tarif_teacher'].'</td>';
				echo '<td>
					<form>
						<button name="butt_del" class="butt_del" item_id="'.$row_t['id'].'">x</button>
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
<script type="text/javascript">
$(document).ready(function() {
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
	});
	$('.flex-center button').button();

	//Удаление пробных уроков
	$('#del_test_form').submit(function() {
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
				data = $('#del_test_form').serializeArray();
				$.post('scripts/add_data.php?del_test=del_test', data, function(){}, "json");
				location.reload();
				}
			}
		});
	$('.butt_del').click(function(e) {
			e.preventDefault();
			$('#warning').dialog('open');
			$('#warning form').html('');
			tmp_str='';
			tmp_str+='<h3>Удалить запись?</h3>';
			tmp_str+='<form id="del_test_form">';
			tmp_str+='<input type="hidden" name="item_id" value="'+$(this).attr('item_id')+'">';
			tmp_str+='</form>';
			$('#warning').append(tmp_str);
		});
		
});
</script>
<!--Окно подтверждения-->
<div id="warning" title="Внимание...">
</div>
</body>
</html>
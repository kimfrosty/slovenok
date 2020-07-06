<?php
//Сумма в день
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])||$_COOKIE['user_group']==0){
header('Location: login.php');
}

include ("scripts/dbconnect.php");
include ("function.php");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Больничные</title>
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
	<button id="add_sick_btn" class="month">Добавить больничный лист</button>	
</div>
<div class="container">
<div class="table">
	<table class="oplata">
    <thead>
		<tr class="legend">
        	<th style="width:25px;">п</th>
            <th style="width:225px;">ФИО</th>
            <th style="width:100px;">Дата</th>
            <th style="width:250px;">Период по справке</th>
            <th style="width:100px;">Кол-во талонов</th>
            <th style="width:150px;">Использовать до ...</th>
            <!--<th style="width:100px">Сумма возврата</th>
            <th style="width:150px">Перерасчет за месяц</th>-->
            <th style="width:50px;">Удалить</th>
		</tr>
<?php 
	//Получение данных о больничных листах из БД и вывод в таблицу
	$res_sick = db_connect("SELECT * FROM sicklist ORDER BY date DESC LIMIT 50");
	$count_p = 1;
	while($row_s = (mysqli_fetch_assoc($res_sick))){
		$date = substr($row_s['date'], 0, 10);
		echo '<tr class="legend_all">
				<td>'.$count_p.'</td>
				<td><a style="color:#0067F4; text-decoration:none;" href="pupils.php?pupil_id='.$row_s['pupil_id'].'">'.$row_s['FIO'].'</a></td>
				<td>'.formateDate($date).'</td>
				<td>'.formateDate($row_s['from_date']).' - '.formateDate($row_s['to_date']).'</td>
				<td>'.$row_s['count_lesson'].'</td>
				<td>'.formateDate(date('Y-m-d', (strtotime('+60 days', strtotime($row_s['to_date']))))).'</td>';
				/*<td>'.$row_s['sum_sick'].'</td>
				<td>'.$row_s['month'].'</td>*/
				echo '<td>
					<form>
						<button name="butt_del" class="butt_del" item_id="'.$row_s['id'].'">x</button>
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
<!--Диалоговое окно-->
<div id="add_sick">
<form method="post" action="" id="form_add_sick">
	<label for="id_pupil">Ученик:</label>
    <input type="search" name="id_pupil" id="id_search" placeholder="Введите ФИО">
    <label for="from_date">С числа:</label>
    <input type="date" name="from_date" id="from_date"><br>
    <label for="to_date">По число:</label>
    <input type="date" name="to_date" id="to_date"><br>
    <!--<label for="date">Перерасчет за:</label>
    <input type="month" name="date"><br>-->
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
	$('#form_add_sick input, .flex-center button').button();
	$('#add_sick').dialog({
		//Автооткрытие. НЕ ЗАБЫТЬ
		autoOpen: false,
		modal:true,
		title:"Больничный по справке",
		close:function(){location.reload()},
		width: 460,
		buttons:{
			"Добавить":function(){
				//Отправка данных по больничному перерасчету
				let data = $('#form_add_sick').serializeArray();
				$.post('scripts/add_data.php?sick=sick', data, function(json){
				},"json");
				location.reload();
				location.reload();
			}
		}
		
	});
	$('#add_sick_btn').click(function(e) {
        e.preventDefault();
		$('#add_sick').dialog('open');
		//Автокомплит ученика
		$('#id_search').autocomplete({
		        source: 'scripts/get_data.php',
				select:showpupil
				 	});//Конец
		
					
	function showpupil(event, ui){
		$('#form_add_sick input:hidden').remove();
		$.getJSON('scripts/get_data.php?c='+ui.item.value, function(json){
			$('#form_add_sick').append('<input type="hidden" name="pupil_id" value="'+json.id+'">');
			})
		}
    });
	//Удаление больничных
	$('#del_sick_form').submit(function() {
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
				let data = $('#del_sick_form').serializeArray();
				$.post('scripts/add_data.php?del_sick=del_sick', data, function(){}, "json");
				location.reload();
				}
			}
		});
	$('.butt_del').click(function(e) {
			e.preventDefault();
			$('#warning').dialog('open');
			$('#warning form').html('');
			let tmp_str='';
			tmp_str+='<h3>Удалить запись?</h3>';
			tmp_str+='<form id="del_sick_form">';
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
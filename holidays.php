<?php 
include ("scripts/dbconnect.php");
include ("function.php");
$month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])){
header('Location: login.php');
}
if(isset($_POST['id'])){
	$res = db_connect("DELETE FROM holiday WHERE id='".$_POST['id']."'");
	 header( "refresh:1;url=".$_SERVER['PHP_SELF']);
	}

 ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Каникулы</title>
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
<br>
<?php if($_COOKIE['user_group']==1){ ?>
<div class="flex-center">
	<button id="holiday_but" class="month">Добавить каникулы</button>		
</div>
<?php }else{?>
	<div class="flex-center">
	<button class="month" disabled>Добавить каникулы</button>		
</div>
<?php }?>
<div class="container">
<div class="table">
<table class="oplata">
	<tr class="legend">
    	<th style="width:25px;">п</th>
		<th style="width:250px;">Начало</th>
		<th style="width:250px;">Конец</th>
		<th style="width:150px;">Всего дней</th>
        <th style="width:150px;">Удалить</th>
    </tr>
    <?php
	$res = db_connect("SELECT * FROM holiday");
	$count = 1;
	while($row = mysqli_fetch_assoc($res)){
		$datetime1 = new DateTime($row['from_date']);
		$datetime2 = new DateTime($row['to_date']);
		$interval = $datetime1->diff($datetime2);
		;
		echo '<tr class="legend_all">
				<td>'.$count.'</td>
				<td>'.formateDate($row['from_date']).'</td>
				<td>'.formateDate($row['to_date']).'</td>
				<td>'.($interval->format('%a')+1).'</td>
				<td><form action="'.$_SERVER['PHP_SELF'].'" method="post">
					<button>X</button>
					<input type="hidden" name="id" value="'.$row['id'].'">
					</form>
				</td></tr>';
		$count++;
		}
	?>
</table>
</div>
</div>
<br>
<div id="form_holiday">
<form id="data_holiday">
	<label for="from_date">С числа:</label>
    <input type="date" name="from_date"><br>
    <label for="to_date">По число:</label>
    <input type="date" name="to_date"><br>
    <input type="hidden" name="add_data_holiday" value="add_data_holiday"><br>
</form>
</div>
</body>
<script type="text/javascript">
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
	$('#form_holiday').dialog({
		//Автооткрытие. НЕ ЗАБЫТЬ
		autoOpen: false,
		modal:true,
		title:"Добавление каникул",
		close:function(){location.reload()},
		width: 420,
		buttons:{
			"Добавить":function(){
				var data = $('#data_holiday').serializeArray();
				$.post('scripts/add_data.php?add_holiday=add_holiday', data, function(json){
					if(json.success=="success"){
						location.reload();
						}
					},"json");
				
				}
			}
		
	})
	$('.month').button();
	$('#holiday_but').click(function(e) {
        e.preventDefault();
		$('#data_holiday input').button();
		$('#form_holiday').dialog('open');
    });
	});
</script>
<?php include ("scripts/dbconnect.php");
 include ("function.php");
 $month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
 ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Журнал</title>
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
<div class="container">
<div class="table">
<table class="oplata">
<thead>
	<tr class="legend">
    	<th style="width:25px;">п</th>
		<th style="width:100px;">Дата</th>
		<th style="width:50px;">Время</th>
        <th style="width:200px;">ФИО</th>
		<th style="width:100px;">Действие</th>
        <th style="width:300px;" colspan="4">С даты</th>
		<th style="width:300px;" colspan="4">На дату</th>
        <th style="width:150px;">Оформил</th>
        <th style="width:50px;">Удалить</th>
	</tr>
    <?php 
		$res_change = db_connect("SELECT schedule.*, pupil.FIO, shifts.shifts, branches.name_branch, teachers.name_teacher
									FROM schedule 
									INNER JOIN pupil ON schedule.pupil_id=pupil.id 
									INNER JOIN shifts ON schedule.id_shift=shifts.id 
									INNER JOIN branches ON schedule.id_branch=branches.id 
									INNER JOIN teachers ON schedule.id_teacher=teachers.id 
									WHERE (schedule.code_change='2' OR schedule.code_change='3') 
									ORDER BY date DESC LIMIT 500");
		$count_p = 1;
		while ($row_c = mysqli_fetch_assoc($res_change)){
		$to_date = formateDate($row_c['from_date']);
		$res_to_date = db_connect("SELECT schedule.from_date, schedule.id, shifts.shifts, branches.name_branch, teachers.name_teacher
									FROM schedule 
									INNER JOIN shifts ON schedule.id_shift=shifts.id 
									INNER JOIN branches ON schedule.id_branch=branches.id 
									INNER JOIN teachers ON schedule.id_teacher=teachers.id 
									WHERE (schedule.code_change='2' OR schedule.code_change='3') AND schedule.id = '".($row_c['id']-1)."'"); 		
		$row_to_date = mysqli_fetch_assoc($res_to_date);
		$from_date = formateDate($row_to_date['from_date']);
		$date = substr($row_c['date'], 0, 10);
		$time = substr($row_c['date'], 10, 6);
		if($row_c['code_change']==2) continue;
		/*echo '<pre>';
		print_r($row_c);
		echo '</pre>';*/
		echo '<tr class="legend_all"><td>'.$count_p.'</td>
				<td>'.formateDate($date).'</td>
				<td>'.$time.'</td>
				<td><a style="color:#0067F4; text-decoration:none;" href="pupils.php?pupil_id='.$row_c['pupil_id'].'">'.$row_c['FIO'].'</a></td>
				<td>Переведен</td>
				<td style="width:100px;">'.$from_date.'</td>
				<td style="width:50px;">'.$row_to_date['shifts'].'</td>
				<td>'.$row_to_date['name_branch'].'</td>
				<td>'.$row_to_date['name_teacher'].'</td>
				<td style="width:100px;">'.$to_date.'</td>
				<td style="width:50px;">'.$row_c['shifts'].'</td>
				<td>'.$row_c['name_branch'].'</td>
				<td>'.$row_c['name_teacher'].'</td>
				<td>'.$row_c['loger'].'</td>
				<td>';
				echo '<form>
							<button name="butt_del" class="butt_del" from_date_id="'.$row_c['id'].'" to_date_id="'.$row_to_date['id'].'">x</button>
    				  </form></td></tr>';
		$count_p++;
		}
	?>
</thead>    
</table>
</div>
</div>
<!--Окно подтверждения-->
<div id="success" title="Внимание...">
</div>
<script type="text/javascript">
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
		$('#del_change_form').submit(function(e) {
        return false;
    });
		$('#success').dialog({
			autoOpen: false,
			modal:true,
			open: function(event, ui) {
      			$(this).parent().find('div.ui-dialog-titlebar').addClass('warning');
			},
			//width: 520,
			close:function(){location.reload()},
			buttons:{
			"Удалить" : function(){
				var data = $('#del_change_form').serializeArray();
				$.post('scripts/add_data.php?del_change=del_change', data, function(){}, "json");
				location.reload();
				}
			}
		})
		$('.butt_del').click(function(e) {
			e.preventDefault();
			$('#success').dialog('open');
			$('#success form').html('');
			tmp_str='';
			tmp_str+='<h3>Удалить запись?</h3>';
			tmp_str+='<form id="del_change_form">';
			tmp_str+='<input type="hidden" name="from_date_id" value="'+$(this).attr('from_date_id')+'">';
			tmp_str+='<input type="hidden" name="to_date_id" value="'+$(this).attr('to_date_id')+'">';
			tmp_str+='</form>';
			$('#success').append(tmp_str);
		});
		
	});
</script>
</body>
</html>
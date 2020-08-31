<?php 
include ("scripts/dbconnect.php");
include ("function.php");
$month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])||$_COOKIE['user_group']==0){
header('Location: login.php');
}
 ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Настройки</title>
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="css/sm-core-css.css" rel="stylesheet" type="text/css">
<link href="css/sm-blue.css" rel="stylesheet" type="text/css">
<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">-->
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="scripts/jquery-ui.min.js"></script>
<script src="scripts/jquery.smartmenus.min.js"></script>
<script src="scripts/showtime.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>-->
</head>
<body onload="if(window.showTime) showTime();">
<div>
<?php include('scripts/navmenu.php');?>
</div>
<div class="container">
<div id="tabs" class="variant back">
  <ul>
    <li><a href="#tabs-1">Расписание</a></li>
    <li><a href="#tabs-2">Педагоги</a></li>
    <li><a href="#tabs-3">Программы</a></li>
  </ul>
  <div id="tabs-1">
  <div class="container">
    <table class="oplata">
    	<tr class="legend">
        	<th style="width:100px">№ Урока</th>
            <th style="width:150px">Время</th>
        </tr>
        <?php 
			$res_shifts = db_connect("SELECT * FROM shifts");
			while($row_s = mysqli_fetch_assoc($res_shifts)){
				echo '<tr class="legend_all"><td>'.$row_s['id'].'</td><td>'.$row_s['shifts'].'</td></tr>';
				}
		?>
    </table>
  </div><br>
  <div class="flex-center">
  	<button id="" class="ui-button ui-corner-all">Добавить урок</button>
  </div>
  </div>
  <div id="tabs-2">
    <div class="container">
    <table class="oplata">
    	<tr class="legend">
        	<th style="width:25px">№</th>
            <th style="width:150px">Педагог</th>
            <th style="width:100px">Логин</th>
            <th style="width:100px">Группа</th>            
            <th style="width:100px">Редактировать</th>
        </tr>
        <?php 
			$res_teachers = db_connect("SELECT teachers.id, teachers.name_teacher, teachers.tax_teacher, administrators.username, administrators.password, administrators.user_group FROM teachers INNER JOIN administrators USING(id)");
			$count_teacher=1;
			while($row_t = mysqli_fetch_assoc($res_teachers)){
				$text = ($row_t['user_group']==1)? "Администратор":"Пользователь";
				echo '<tr class="legend_all">
						  <td>'.$count_teacher.'</td>
						  <td>'.$row_t['name_teacher'].'</td>
						  <td>'.$row_t['username'].'</td>
						  <td>'.$text.'</td><td><form>
						  <button name="butt_edit" class="butt_edit"
						  id_teacher = "'.$row_t['id'].'" 
						  name_teacher = "'.$row_t['name_teacher'].'" 
						  login =  "'.$row_t['username'].'"
						  password="'.$row_t['password'].'"
						  >Изменить</button>
    				  	  </form></td></tr>';
					  	$count_teacher++;
				}
		?>
    </table>
  </div><br>
  <div class="flex-center">
  	<button id="but_add_teacher" class="ui-button ui-corner-all">Добавить педагога</button>
  </div>
  </div>
  <div id="tabs-3">
    <div class="container">
    <table class="oplata">
    	<tr class="legend">
        	<th style="width:25px">№</th>
            <th style="width:200px">Программа</th>
            <th style="width:100px">Тариф ученика</th>
            <th style="width:100px">Тариф учителя</th>          
        </tr>
        <?php 
			$res_prog = db_connect("SELECT * FROM programms");
			while($row_prog = mysqli_fetch_assoc($res_prog)){
				echo '<tr class="legend_all">
				<td>'.$row_prog['id'].'</td>
				<td style="background-color:'.$row_prog['bg_color'].'; color:'.$row_prog['color'].'">'.$row_prog['name'].'</td>
				<td>'.$row_prog['tarif_pupil'].'</td>
				<td>'.$row_prog['tarif_teacher'].'</td></tr>';
				}
		?>
    </table>
  </div><br>
  <div class="flex-center">
  	<button id="but_add_prog" class="ui-button ui-corner-all">Добавить программу</button>
  </div>
  </div>
</div>
</div>
<div id="edit_teacher" title="Редактировать педагога">
	<form id="form_edit_teacher">
    	<label for="name_teacher">Имя:</label>
    	<input type="text" name="name_teacher" id="name_teacher"><br>
        <label for="user_name">Логин:</label>
        <input type="text" name="user_name" id="user_name">
        <label for="tax_teacher">Тариф:</label>
        <input type="text" name="tax_teacher" id="tax_teacher"><br>
        <label for="new_password">Пароль:</label>
        <input type "text" name="new_password" id="new_password"><br>
        <label for="administrator">Администратор</label>
        <input type="checkbox" name="administrator" value="1">
    </form>
</div>
<div id="add_teacher" title="Добавление педагога">
	<form id="form_add_teacher">
    	<label for="add_name_teacher">Имя:</label>
    	<input type="text" name="add_name_teacher" id="add_name_teacher"><br>
        <label for="add_user_name">Логин:</label>
        <input type="text" name="add_user_name" id="add_user_name"><br>
        <label for="add_tax_teacher">Тариф:</label>
        <input type="text" name="add_tax_teacher" id="add_tax_teacher"><br>
        <label for="add_new_password">Пароль:</label>
        <input type "text" name="add_new_password" id="add_new_password"><br>
        <label for="add_administrator">Администратор</label>
        <input type="checkbox" name="add_administrator" value="1">
    </form>
</div>
<div id="add_prog" title="Добавление программы">
	<form id="form_add_prog" method="post" action="scripts/add_data.php">
    	<label for="name_prog">Название:</label>
    	<input type="text" name="name_prog" id="name_prog">
        <label for="tarif_pupil">Тариф ученика:</label>
        <input type="text" name="tarif_pupil" id="tarif_pupil">
        <label for="tarif_teacher">Тариф учителя:</label>
        <input type="text" name="tarif_teacher" id="tarif_teacher">
        <label for="bg_color">Цвет фона:</label>
        <input type="text" name="bg_color" id="bg_color">
        <label for="color">Цвет текста:</label>
        <input type="text" name="color" id="color">
    </form>
</div>
<div id="add_tarif">
</div>
<script type="text/javascript">
$(document).ready(function(e) {
	$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
	$( "#tabs" ).tabs();
	$( '#add_teacher input' ).button();
	$( '#edit_teacher input' ).button();
	$( '#add_prog input' ).button();
	$( '#edit_teacher' ).dialog({
		autoOpen: false,
		modal:true,
		width: 450,
		close:function(){
			location.reload()
			},
		buttons : {
				"Изменить" : function(){
				var data = $('#form_edit_teacher').serializeArray();
				$.post('scripts/add_data.php?edit_teacher=edit_teacher', data, function(json){
					},"json");
				location.reload();
			},
				"Удалить" : function(){
				var data = $('#form_edit_teacher').serializeArray();
				$.post('scripts/add_data.php?edit_teacher=del_teacher', data, function(json){
					 location.reload();
					},"json");
					}
		}
		});
		
		$('#add_teacher').dialog({
		autoOpen: false,
		modal:true,
		width: 450,
		close:function(){
			location.reload()
			},
		buttons : {
				"Добавить" : function(){
				var data = $('#form_add_teacher').serializeArray();
				$.post('scripts/add_data.php?add_teacher=add_teacher', data, function(json){
					 location.reload();
					},"json");
			}
		}
		});
	
	$('.butt_edit').click(function(e) {
        e.preventDefault();
		$('#name_teacher').val($(this).attr('name_teacher'));
		$('#user_name').val($(this).attr('login'));
		$('#tax_teacher').val($(this).attr('tax'));
		$('#id_teacher').val();
		$('#form_edit_teacher').append('<input type="hidden" name="id_teacher" value="'+$(this).attr('id_teacher')+'">')
		$('#form_edit_teacher').append('<input type="hidden" name="password" value="'+$(this).attr('password')+'">')
		$('#edit_teacher').dialog('open');
    });
	
	$('#add_prog').dialog({
		autoOpen: false,
		modal:true,
		width: 430,
		close:function(){
			location.reload()
			},
		buttons : {
				"Добавить" : function(){
				var data = $('#form_add_prog').serializeArray();
				$.post('scripts/add_data.php?add_prog=add_prog', data, function(json){
					 //location.reload();
					},"json");
			}
		}
		});
	
	$('#but_add_teacher').click(function(e) {
        e.preventDefault();
		$('#add_teacher').dialog('open');		
    });
	
	$('#but_add_prog').click(function(e) {
        e.preventDefault();
		$('#add_prog').dialog('open');
		$( '#bg_color, #color' ).colorpicker();
		$( '#bg_color, #color' ).on('colorpickerChange', function(event) {
        $( '#add_prog' ).css('background-color', event.color.toString());
      });
	});
});
</script>
</body>
</html>
<?php 
require('scripts/dbconnect.php');
session_start();
if(!empty($_POST['id_branch'])){
	$_SESSION['id_branch']=$_POST['id_branch'];
	setcookie('id_branch', '', time()-3600);
	setcookie('id_branch', $_SESSION['id_branch'], time()+3600);
	}
require('function.php');
//Авторизация
if(!isset($_COOKIE['session_id'])||!isset($_COOKIE['username'])){
$_SESSION=array();
header('Location: login.php');
}
//Данные из БД
$count_items = 15;
//Счетчик для дней недели
$week = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");
$week_count = 0;

//Конец данных из БД

//Отладочка
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>График</title>
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
<div class="container">
<?php
//Пагинация-кнопки
$temp_links = array();
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=-3><<<</a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=-2><<</a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=-1><</a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=0>Текущая</a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=1>></a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=2>>></a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=3>>>></a>");
echo '<div class="pagin">';
			for($i=0; $i<count($temp_links); $i++){
				echo '<span class="pagination">'.$temp_links[$i].'</span>';
				}			
		echo '</div>';
		
//Временные метки
$start_day = $mark_time-mktime(0,0,0,date('m',$mark_time), date('d', $mark_time), date('Y',$mark_time));
$end_date = mktime(0,0,0,date('m',$mark_time), date('d', $mark_time), date('Y',$mark_time))-$mark_time;
$now_weekday = (date('w', $mark_time)==0)? 7 :date('w', $mark_time);
$sec_in_day = 24*60*60;
$first_date_week = date('d.m.Y',($mark_time-$sec_in_day*($now_weekday-1)-$start_day));
$last_date_week = date('d.m.Y',$mark_time+$sec_in_day*(7-$now_weekday)+$end_date);
//Массив дат
$week_arr = array();
$k = (empty($_GET['page'])) ? 0 : $_GET['page'];
$fd = $k*7;
$ld = $fd+6;
for($i=$fd; $i<=$ld; $i++){
	array_push($week_arr, date('Y-m-d', ($mark_time-$sec_in_day*($now_weekday-1)-$start_day+$sec_in_day*$i)));
	}

/*echo '<pre>';		
print_r(getDataPupil('2017-09-20')); //getDataPupil('2017-09-20')
echo '</pre>';
echo '<pre>';		
print_r($temp_arr_pupil); //getDataPupil('2017-09-20')
echo '</pre>';*/
?>
</div>
<!---->
<div class="container">
	<form method="post" action="#">
    <?php
	$res_branch = db_connect("SELECT * FROM branches");
	$count_id = 1;
	while($row_b=mysqli_fetch_assoc($res_branch)){
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
<div>
	<table class="time">
    <thead>
    	<tr>
        	<th>группы</th>
        </tr>
        <tr>
        	<td>Время</td>
        </tr>
        <?php
			$res_shift = db_connect("SELECT * FROM shifts WHERE shifts.id>0");
			while($row_s = mysqli_fetch_assoc($res_shift)){
				
				///Проверка на пустые часы в графике
				$count = 0;
				for($x=0; $x<count($week_arr); $x++){
					$res_test_lesson = CheckTestLesson($week_arr[$x],$_SESSION['id_branch'],$x+1,$row_s['id']);
					if(mysqli_num_rows($res_test_lesson)<1){$count++;}
				}
				$res_all_week = db_connect("SELECT * FROM schedule WHERE id_branch='".$_SESSION['id_branch']."' AND id_shift='".$row_s['id']."'");
				if(mysqli_num_rows($res_all_week)<1 && $count == 7){continue;}
				/////////////
				
				echo '<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>'.$row_s['shifts'].'</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>';
			}
		?>
    </thead>   
    </table>
</div>
<div  id="container" class="table">
<?php 

$num_links = 5;//Количество ссылок
$temp_links = array();
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=-3><<<</a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=-2><<</a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=-1><</a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=0>Текущая</a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=1>></a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=2>>></a>");
array_push($temp_links, "<a href=".$_SERVER['PHP_SELF']."?page=3>>>></a>");
//$temp_arr_pupil = array();
//
for($i=0; $i<count($week_arr); $i++){
	$res_date = db_connect("SELECT * FROM calendar WHERE date='".$week_arr[$i]."'");//получаем дату LIMIT ".$page*$numbers_msg.",".$numbers_msg
		while($row_d = mysqli_fetch_assoc($res_date)){
		echo '<table><thead>';
		echo '<tr class="day_week"><th colspan="3" class="date">'.$week[$week_count].' - '.formateDate($row_d['date']).'</th></tr>';
		$week_count++;
		echo '<tr class="legend"><td>п</td><td>ФИО</td><td>отм</td></tr>';
		$res_shifts = db_connect("SELECT * FROM shifts WHERE shifts.id>0");
		while($row_s = mysqli_fetch_assoc($res_shifts)){
			
			///Проверка на пустые часы в графике
			$count = 0;
			for($x=0; $x<count($week_arr); $x++){
				$res_test_lesson = CheckTestLesson($week_arr[$x],$_SESSION['id_branch'],$x+1,$row_s['id']);
				if(mysqli_num_rows($res_test_lesson)<1){$count++;}
			}
			$res_all_week = db_connect("SELECT * FROM schedule WHERE id_branch='".$_SESSION['id_branch']."' AND id_shift='".$row_s['id']."'");
			if(mysqli_num_rows($res_all_week)<1 && $count == 7){continue;}
			/////////////
			
			$arr_pupil = getDataPupil($row_d['date'], $row_s['id']);
			$count_pupil = 1;
			
			for($j=0; $j<count($arr_pupil); $j++){
				if(empty($arr_pupil[$j])||$arr_pupil[$j]['code_change']==2) continue;
				/////////
				//ПОЛУЧЕНИЕ ДАННЫХ ДЛЯ ПОДСКАЗКИ
				$res_prog = db_connect("SELECT * FROM pupil WHERE pupil.id='".$arr_pupil[$j]['pupil_id']."'");
				$row_prog = mysqli_fetch_assoc($res_prog);
				$tooltip = '<span>Фамилия: </span><span><b>'.$arr_pupil[$j]['FIO'].'</b></span><br>
							<span>ID ученика: </span><span><b>'.$arr_pupil[$j]['pupil_id'].'</b></span><br>
							<span>Учитель: </span><span><b>'.$arr_pupil[$j]['name_teacher'].'</b></span><br>';
							
				
				/////////
				echo '<tr class="legend_graf">';
				//Получение данных об оплате ученика
				$res_pay = db_connect("SELECT * FROM payment WHERE pupil_id='".$arr_pupil[$j]['pupil_id']."' AND '".$row_d['date']."'>=from_date AND '".$row_d['date']."'<=to_date AND pay<>discount");
				if(mysqli_num_rows($res_pay)<=0){echo '<td style="background-color:#ff5ecf;font-weight:bold;">'.$count_pupil.'</td>';}else{echo '<td>'.$count_pupil.'</td>';}
				//////////
				
				//////////////////////
				echo '<td class="pupil-present left_align pre" id="'.$arr_pupil[$j]['id'].'" id_branch="'.$arr_pupil[$j]['id_branch'].'" id_teacher="'.$arr_pupil[$j]['id_teacher'].'" pupil_id="'.$arr_pupil[$j]['pupil_id'].'" id_day="'.$row_d['weekday'].'" id_shift="'.$row_s['id'].'" date="'.$row_d['date'].'" format_date="'.formateDate($row_d['date']).'" data-pupil="'.$tooltip.'">'.$arr_pupil[$j]['FIO'].'</td>';
				if($arr_pupil[$j]['code_change']==5){
					echo '<td class="seek" id="'.$arr_pupil[$j]['id'].'" id_branch="'.$arr_pupil[$j]['id_branch'].'"  id_teacher="'.$arr_pupil[$j]['id_teacher'].'" pupil_id="'.$arr_pupil[$j]['pupil_id'].'" id_day="'.$row_d['weekday'].'" id_shift="'.$row_s['id'].'" date="'.$row_d['date'].'">-</td></tr>';
					}else if($arr_pupil[$j]['code_change']==6){
						echo '<td class="empty" id="'.$arr_pupil[$j]['id'].'" id_branch="'.$arr_pupil[$j]['id_branch'].'" id_teacher="'.$arr_pupil[$j]['id_teacher'].'" pupil_id="'.$arr_pupil[$j]['pupil_id'].'" id_day="'.$row_d['weekday'].'" id_shift="'.$row_s['id'].'" date="'.$row_d['date'].'">-</td></tr>';
						}else if($arr_pupil[$j]['code_change']==4){
				        echo '<td class="cancel" cookie_teacher = "'.$_COOKIE['id_teacher'].'" id="'.$arr_pupil[$j]['id'].'" id_branch="'.$arr_pupil[$j]['id_branch'].'" id_teacher="'.$arr_pupil[$j]['id_teacher'].'" pupil_id="'.$arr_pupil[$j]['pupil_id'].'" id_day="'.$row_d['weekday'].'" id_shift="'.$row_s['id'].'" date="'.$row_d['date'].'">-</td></tr>';}
						else echo '<td class="present"  id_branch="'.$arr_pupil[$j]['id_branch'].'" id_teacher="'.$arr_pupil[$j]['id_teacher'].'" pupil_id="'.$arr_pupil[$j]['pupil_id'].'" id_day="'.$row_d['weekday'].'" id_shift="'.$row_s['id'].'" date="'.$row_d['date'].'">+</td></tr>';
				$count_pupil++;
				$id_teacher = $arr_pupil[$j]['id_teacher'];
			
			}
			$test_lesson = CheckTestLesson($week_arr[$i],$_SESSION['id_branch'],$week_count,$row_s['id']);
			$row_tl = mysqli_fetch_assoc($test_lesson);
			if(mysqli_num_rows($test_lesson)>0){
				echo '<tr class="legend_graf empty"><td>1</td><td class="pupil test_lesson">Пробный урок</td><td></td></tr>';
				echo '<tr class="legend_graf empty"><td>2</td><td class="pupil test_lesson">'.$row_tl['name_teacher'].'</td><td></td></tr>';
					for($k=3; $k<=12; $k++){
						echo '<tr class="legend_graf empty"><td>'.$k.'</td><td class="pupil test_lesson" date="'.$row_d['date'].'"></td><td></td></tr>';
					}
				}else{
					if($count_pupil>1){
					echo '<tr class="legend_graf"><td>'.$count_pupil.'</td><td></td>
							<td class="pupil_ticket" 
							date="'.$row_d['date'].'" 
							branch="'.$_SESSION['id_branch'].'" 
							day="'.($week_count).'" 
							shift="'.$row_s['id'].'" 
							teacher="'.$id_teacher.'">т</td></tr>';
					for($k=$count_pupil+1; $k<=12; $k++){
						echo '<tr class="legend_graf empty"><td>'.$k.'</td><td class="pupil" date="'.$row_d['date'].'"></td><td></td></tr>';
					}
					}else{
						for($k=$count_pupil; $k<=12; $k++){
						echo '<tr class="legend_graf empty"><td>'.$k.'</td><td class="pupil" date="'.$row_d['date'].'"></td><td></td></tr>';
					}
					}
				}
			}
		echo '</thead></table>';
		
	}
}
?>
<table class="time">
<thead>
    	<tr>
        	<th>группы</th>
        </tr>
        <tr>
        	<td>Время</td>
        </tr>
        <?php
			$res_shift = db_connect("SELECT * FROM shifts WHERE shifts.id>0");
			while($row_s = mysqli_fetch_assoc($res_shift)){
				
				///Проверка на пустые часы в графике
				$count = 0;
				for($x=0; $x<count($week_arr); $x++){
					$res_test_lesson = CheckTestLesson($week_arr[$x],$_SESSION['id_branch'],$x+1,$row_s['id']);
					if(mysqli_num_rows($res_test_lesson)<1){$count++;}
				}
				$res_all_week = db_connect("SELECT * FROM schedule WHERE id_branch='".$_SESSION['id_branch']."' AND id_shift='".$row_s['id']."'");
				if(mysqli_num_rows($res_all_week)<1 && $count == 7){continue;}
				/////////////
				
				echo '<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>'.$row_s['shifts'].'</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>';
			}
		?>
    </thead>   
    </table>
</div>
</div>
<div class="container">
	<form method="post" action="#">
    <?php
	$res_branch = db_connect("SELECT * FROM branches");
	$count_id=1;
	while($row_b=mysqli_fetch_assoc($res_branch)){
		if($count_id==$_SESSION['id_branch']){
			echo '<button name="id_branch" id_attr_branch="'.$_SESSION['id_branch'].'" value="'.$row_b['id'].'" 
			style="background-color:#3D6C80; color:white;">'.$row_b['name_branch'].'</button>';
			} else echo '<button name="id_branch" value="'.$row_b['id'].'">'.$row_b['name_branch'].'</button>';
			$count_id++;
		}
	?>
    </form>
</div>
<div class="container">
<?php
//Пагинация-кнопки
echo '<div class="pagin">';
			for($i=0; $i<count($temp_links); $i++){
				echo '<span class="pagination">'.$temp_links[$i].'</span>';
				}			
		echo '</div>';
/*echo '<pre>';		
print_r(getDataPupil('2017-09-20')); //getDataPupil('2017-09-20')
echo '</pre>';
echo '<pre>';		
print_r($temp_arr_pupil); //getDataPupil('2017-09-20')
echo '</pre>';*/
?>
</div>
<!--Модальные окна-->
<!--Окно добавления ученика-->





<?php
if($_COOKIE['user_group']==1){
?>



<!--<div id="new_add_pupil" title="Добавление ученика">
<div id="tab">
  <ul>
    <li><a href="#tab-1">Добавить нового ученика</a></li>
    <li><a href="#tab-2">Добавить уроки старому ученику</a></li>
  </ul>
  <div id="tab-1">
<div id="add_pupil">
		<form id="add_data_pupil">
            <label for="id_search">ФИО ученика:</label>
            <input type="search" id="id_search" name="FIO"><br>
            <label for="birthday">Дата рождения:</label>
		    <input type="text" name="birthday" id="birthday"><br>
            <label for="FIO_parent">Имя родителя:</label>
            <input type="text" id="FIO_parent" name="FIO_parent"><br>
            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone"><br>
            <label for="school">Школа:</label>
            <input type="text" id="school" name="school"><br>
            <label for="tax_pupil">Тариф:</label>
       	    <select name="tax_pupil" id="tax_pupil">
            	<?php /*?><?php $res_s_p = db_connect("SELECT * FROM tax_pupil");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							echo '<option value="'.$row_sp['id'].'">'.$row_sp['tax'].'</option>';
				}?><?php */?>
            </select><br>
            <label for="school_prog">Программа:</label>
       	    <select name="school_prog" id="school_prog">
            	<?php /*?><?php $res_s_p = db_connect("SELECT * FROM school_prog");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							echo '<option value="'.$row_sp['id'].'">'.$row_sp['program'].'</option>';
				}?><?php */?>
            </select><br>
            <label for="id_teacher">Учитель:</label>
       	    <select name="id_teacher" id="id_teacher">
            	<?php /*?><?php $res_s_p = db_connect("SELECT * FROM teachers");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_teacher'].'</option>';
				}?><?php */?>
            </select><br>
            <label for="id_branch">Филиал:</label>
       	    <select name="id_branch" id="id_branch">
            	<?php /*?><?php $res_s_p = db_connect("SELECT * FROM branches");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							if($_SESSION['id_branch']==$row_sp['id']){
								echo '<option value="'.$row_sp['id'].'" selected>'.$row_sp['name_branch'].'</option>';
							} else {
								echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_branch'].'</option>';
								}
				}?><?php */?>
            </select><br>
            <label for="from_date">Начало обучения:</label>
	        <input type="date" name="from_date" id="from_date"><br>
            <input type="hidden" name="form_pupil" value="form_pupil">
            <div class="flex-container">  
              <div id="lesson_block"></div>
            </div>
            <div class="flex-center">
              <button id="add_lesson">Добавить урок</button>
            </div>
            <label for="add_new_pupil">Внести изменения</label>
            <input type="checkbox" name="add_new_pupil" value="add_new_pupil">
         </form>
	 </div>
</div>-->
<!--<div id="tab-2">
<div id="old_add_pupil" title="Добавление ученика">
		<form id="add_one_less">
            <label for="FIO">ФИО ученика:</label>
            <input type="search" id="id_search_one" name="FIO"><br>
            <label for="birthday">Дата рождения:</label>
		    <input type="text" name="birthday" id="birthday"><br>
            <label for="FIO_parent">Имя родителя:</label>
            <input type="text" id="FIO_parent" name="FIO_parent"><br>
            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone"><br>
            <label for="school">Школа:</label>
            <input type="text" id="school" name="school"><br>
            
            <label for="id_teacher_one">Учитель:</label>
       	    <select name="id_teacher_one" id="id_teacher_one">-->
            	<?php /*?><?php $res_s_p = db_connect("SELECT * FROM teachers");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_teacher'].'</option>';
				}?><?php */?>
            <!--</select><br>
            
            <label for="id_branch_one">Филиал:</label>
       	    <select name="id_branch_one" id="id_branch_one">
            	<?php /*?><?php $res_s_p = db_connect("SELECT * FROM branches");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							if($_SESSION['id_branch']==$row_sp['id']){
								echo '<option value="'.$row_sp['id'].'" selected>'.$row_sp['name'].'</option>';
							} else {
								echo '<option value="'.$row_sp['id'].'">'.$row_sp['name'].'</option>';
								}
				}?><?php */?>
            </select><br>
            
            <label for="from_date_one">Начало обучения:</label>
	        <input type="date" name="from_date_one" id="from_date_one"><br>
            <input type="hidden" name="form_pupil" value="form_pupil">
            <div class="flex-container">  
              <div id="lesson_one_block"></div>
            </div>
            <div class="flex-center">
              <button id="add_one_lesson">Добавить урок</button>
            </div>
            <label for="add_one_less">Внести изменения</label>
            <input type="checkbox" name="add_one_less" value="add_one_less">
         </form>
	 </div>
</div>-->
</div>
</div>
<!--Окно предупреждения-->
<div id="warning" title="Внимание...">
	<h3>Добавьте урок</h3>
</div>

<?php } ?>

<!--Окно редактирования посещений-->
<div id="edit_pupil" title="Посещаемость">
	<form id="form_edit_pupil">
    	<fieldset id="att">

    		<legend>Выберите действие: </legend>
    		<label for="radio-1">Пропустил</label>
			<input type="radio" name="code_change" id="radio-1" value="6"><br>
			<label for="radio-2">Заболел</label>
			<input type="radio" name="code_change" id="radio-2" value="5"><br>
		<?php if($_COOKIE['user_group']==1){?>	
            <label for="radio-3">Закончил обучение</label>
			<input type="radio" name="code_change" id="radio-3" value="7"><br>
            <label for="radio-5" class="temp_class">Отменить занятие</label>
            <input type="radio" name="code_change" id="radio-5" value="4"><br>

    	<?php } ?>
    
       </fieldset><br><br>
        
	</form>
</div>




<?php if($_COOKIE['user_group']==1){?>
<!--Окно редактирования графика-->
<div id="tabs" pupil_id="">
  <ul>
    <li><a href="#tabs-1">Перенос ученика</a></li>
    <!--<li><a href="#tabs-2">Сменить график</a></li>-->
    <li><a href="#tabs-4">Перенос группы</a></li>
    <li><a href="#tabs-3">Редактировать</a></li>
  </ul>
  <div id="tabs-1">
    <form class="form-add-pupil" id="one_time_change">
    	<!--<fieldset>
        	<legend>Перенести ученика (разово): </legend>-->
            <label for="to_date">С даты:</label>
            <input type="date" name="from_date" id="from_date_change">
            <select name="shift_from_change" id="shift_from_change">
				<?php $res_s = db_connect("SELECT * FROM shifts");
						while($row_s = mysqli_fetch_assoc($res_s)){
							echo '<option value="'.$row_s['id'].'" class="o_change">'.$row_s['shifts'].'</option>';
				}?>
			</select><br>
        	<label for="to_date">На дату:</label>
            <input type="date" name="to_date" id="to_date_change">
            <select name="shift_to_change" id="shift_to_change">
				<?php $res_s = db_connect("SELECT * FROM shifts");
						while($row_s = mysqli_fetch_assoc($res_s)){
							echo '<option value="'.$row_s['id'].'">'.$row_s['shifts'].'</option>';
				}?>
			</select><br>
             <label for="id_teacher">Учитель:</label>
       	    <select name="id_teacher" id="edit_id_teacher">
            	<?php $res_s_p = db_connect("SELECT * FROM teachers");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_teacher'].'</option>';
				}?>
            </select><br>
            <!---->
            <label for="edit_id_branch">Филиал:</label>
       	    <select name="new_id_branch" id="edit_id_branch">
            	<?php $res_s_p = db_connect("SELECT * FROM branches");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							if($_SESSION['id_branch']==$row_sp['id']){
								echo '<option value="'.$row_sp['id'].'" selected>'.$row_sp['name_branch'].'</option>';
							} else {
								echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_branch'].'</option>';
								}
				}?>
            </select><br>
            <!---->
            <label for="one_change">Внести изменения</label>
            <input type="checkbox" name="one_change" value="yes" id="one_change">
            <input type="hidden" name="edit_mode" value="one_time_change">
         <!--</fieldset> -->
         
         </form>
  </div>
  <!--<div id="tabs-2">
  <form id="edit_schedule">
	<label for="from_date">Изменить график с:</label>
    <input type="date" name="from_date" id="from_date_edit"><br>
    <input type="hidden" name="edit_mode" value="edit_schedule">
     <label for="id_teacher_edit">Учитель:</label>
       	    <select name="id_teacher" id="id_teacher_edit">
            	<?php /*?><?php $res_s_p = db_connect("SELECT * FROM teachers");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_teacher'].'</option>';
				}?><?php */?>
            </select><br>
           
            <label for="edit_id_branch">Филиал:</label>
       	    <select name="new_id_branch" id="edit_id_branch">
            	<?php /*?><?php $res_s_p = db_connect("SELECT * FROM branches");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							if($_SESSION['id_branch']==$row_sp['id']){
								echo '<option value="'.$row_sp['id'].'" selected>'.$row_sp['name'].'</option>';
							} else {
								echo '<option value="'.$row_sp['id'].'">'.$row_sp['name'].'</option>';
								}
				}?><?php */?>
            </select><br>
            
    <div id="lesson_block_edit">
    
    </div>
    <div class="flex-center">
    <button id="add_lesson_edit">Новый урок</button>
    </div>
    <label for="change_schedule">Внести изменения</label>
    <input type="checkbox" name="change_schedule" value="yes" id="change_schedule">
   </form>
  </div>-->
  <div id="tabs-3">
  	    <form id="edit_data_pupil">
         	<input type="hidden" name="edit_mode" value="edit_data_pupil">
            <label for="FIO">ФИО ученика:</label>
			<input type="text" id="FIO" name="FIO"><br>
            <!--<label for="birthday">Дата рождения:</label>
		    <input type="text" name="birthday" id="birthday"><br>
            <label for="FIO_parent">Имя родителя:</label>
            <input type="text" id="FIO_parent" name="FIO_parent"><br>
            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone"><br>
            <label for="school">Школа:</label>
            <input type="text" id="school" name="school"><br>
            <label for="school_prog">Программа:</label>
       	    <select name="school_prog" id="edit_school_prog">
            	<?php //$res_s_p = db_connect("SELECT * FROM school_prog");
						//while($row_sp = mysqli_fetch_assoc($res_s_p)){
							//echo '<option value="'.$row_sp['id'].'">'.$row_sp['program'].'</option>';
				?>
            </select><br>-->
            
            <!--<label for="tax_pupil">Тариф:</label>
       	    <select name="tax_pupil" id="edit_tax_pupil">-->
            	<?php //$res_s_p = db_connect("SELECT * FROM tax_pupil");
						//while($row_sp = mysqli_fetch_assoc($res_s_p)){
						//	echo '<option value="'.$row_sp['id'].'">'.$row_sp['tax'].'</option>';
				?>
            <!--</select><br>
            <label for="from_date">С даты:</label>
            <select name="from_date_tax" id="edit_from_date">
            	<option value="01">Январь</option>
                <option value="02">Февраль</option>
                <option value="03">Март</option>
                <option value="04">Апрель</option>
                <option value="05">Май</option>
                <option value="06">Июнь</option>
                <option value="07">Июль</option>
                <option value="08">Август</option>
                <option value="09">Сентябрь</option>
                <option value="10">Октябрь</option>
                <option value="11">Ноябрь</option>
                <option value="12">Декабрь</option>
            </select><br>-->
            <label for="confirm_tax">Внести изменения</label>
            <input type="checkbox" name="confirm_tax" value="yes" id="confirm_tax">
</form>
</div>
  	<div id="tabs-4">
    	<form class="form-add-pupil" id="group_change">
    	<!--<fieldset>
        	<legend>Перенести ученика (разово): </legend>-->
            <label for="to_date">С даты:</label>
            <input type="date" name="from_date" id="from_date_group_change">
            <select name="shift_from_group_change" id="shift_from_group_change">
				<?php $res_s = db_connect("SELECT * FROM shifts");
						while($row_s = mysqli_fetch_assoc($res_s)){
							echo '<option value="'.$row_s['id'].'" class="o_change_group">'.$row_s['shifts'].'</option>';
				}?>
			</select><br>
        	<label for="to_date">На дату:</label>
            <input type="date" name="to_date" id="to_date_group_change">
            <select name="shift_to_group_change" id="shift_to_group_change">
				<?php $res_s = db_connect("SELECT * FROM shifts");
						while($row_s = mysqli_fetch_assoc($res_s)){
							echo '<option value="'.$row_s['id'].'">'.$row_s['shifts'].'</option>';
				}?>
			</select><br>
             <label for="id_teacher_group_change">Учитель:</label>
       	    <select name="id_teacher_group_change" id="edit_id_teacher_group_change">
            	<?php $res_s_p = db_connect("SELECT * FROM teachers");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_teacher'].'</option>';
				}?>
            </select><br>
            <!---->
            <label for="edit_id_branch">Филиал:</label>
       	    <select name="new_id_branch" id="edit_id_branch">
            	<?php $res_s_p = db_connect("SELECT * FROM branches");
						while($row_sp = mysqli_fetch_assoc($res_s_p)){
							if($_SESSION['id_branch']==$row_sp['id']){
								echo '<option value="'.$row_sp['id'].'" selected>'.$row_sp['name_branch'].'</option>';
							} else {
								echo '<option value="'.$row_sp['id'].'">'.$row_sp['name_branch'].'</option>';
								}
				}?>
            </select><br>
            <!---->
            <input type="hidden" name="old_id_branch" value="<?=$_SESSION['id_branch']?>">
            <label for="one_group_change">Внести изменения</label>
            <input type="checkbox" name="one_group_change" value="yes" id="one_group_change">
            <input type="hidden" name="edit_mode" value="one_time_group_change">
         <!--</fieldset> -->
         
         </form>
    </div>
</div>
<?php } ?>
<div id="pupil_ticket" title="Ученики с талонами">
		<form method="post" id="pupil_select_form"> 
		<label for="pupil_select">Выберите:</label>
        <select id="pupil_select">
			<?php $res_pupil_tickets = db_connect("SELECT * FROM pupil WHERE tickets>0");
					while($row_pt = mysqli_fetch_assoc($res_pupil_tickets)){
						echo '<option value="'.$row_pt['FIO'].'">'.$row_pt['FIO'].'</option>';
					}
			?>
		</select>
		</form>
	</div>
</body>
<script>
$(document).ready(function(e) {
	//Подсветка ячеек в зависимости от учителя
	$('.pupil-present').each(function() {
	var attr = $(this).attr('id_teacher');
	   switch (attr){
			case '1':
			$(this).addClass("color1");
			break;
			case '2':
			$(this).addClass("color2");
			break;
			case '3':
			$(this).addClass("color3");
			break;
			case '4':
			$(this).addClass("color4");
			break;
			case '5':
			$(this).addClass("color5");
			break;
			case '6':
			$(this).addClass("color6");
			break;
			case '7':
			$(this).addClass("color7");
			break;
			case '8':
			$(this).addClass("color8");
			break;
			case '9':
			$(this).addClass("color9");
			break;
			case '11':
			$(this).addClass("color11");
			break;
			case '12':
			$(this).addClass("color12");
			break;
			case '13':
			$(this).addClass("color13");
			break;
			case '14':
			$(this).addClass("color14");
			break;
			case '15':
			$(this).addClass("color15");
			break;
			case '16':
			$(this).addClass("color16");
			break;
			case '17':
			$(this).addClass("color17");
			break;
		}
    });
	////////////////////
	$('#add_data_pupil, #one_time_change').submit(function(e) {
        return false;
    });
	
	////////////////////
	$('.pupil-present').tooltip({
		items:"[data-pupil]",
		content:function(){
			return $(this).attr('data-pupil');
			},
		position: {
                  my: "center bottom" ,
                  at: "center+220 top+120" ,
                  collision: "none"
               }
	});
	////////////////////
	
	$( "#tabs" ).tabs();
	$( "#edit_pupil input" ).checkboxradio();
    $( "#edit_pupil fieldset" ).controlgroup();
	$('.seek').css('background', 'yellow');
	$('.empty').css('background', 'red');
	$('.present').css('background', 'green');
	$('.cancel').css('background', 'grey');
	
	//Меню	
	$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
	//Подсветка ячеек таблицы
	$('.pupil-present').hover(function(){
	$(this).css({'background-color' : '#DC7713',
	'cursor':'pointer'});
	}, function(){
		$(this).css({'background-color' : '',
		'cursor' : ''});
		});
	$('.legend_graf>td:nth-child(3n+3)').hover(function(){
	$(this).css('cursor','pointer');
	});
	
	
	//Получаем высоту ячейки td
	/*var hTd = $('.legend_graf td').outerHeight();
	//Присваеваем высоту сменам
	$('.time tr:nth-child(n+3)').outerHeight(hTd*10+1);*/
	//Стлизация кнопок
	$('.pagin a').button();
	$('#add_pupil input, button').button();
	$('#old_add_pupil input, button').button();
	$('#tabs input, button').button();
	$('#tab input, button').button();
	$('#add_interval input').button();
	
	//Вывод подсказки
	$('.pupil-present').tooltip({
		items:"[data-pupil]",
		content:function(){
			return $(this).attr('data-pupil');
			},
		position: {
                  my: "center bottom" ,
                  at: "center+220 top+120" ,
                  collision: "none"
               }
	});
	
	//Редактирование ученика
	$('.pupil-present').click(function(e){
		$('#tabs').attr('pupil_id' , $(this).attr('pupil_id'));
		$('div[role=dialog] button:contains("Личная карточка")').css({'background-color' :'#49FF53'});
		var id_shift = $(this).attr('id_shift');
		$('#tabs-1 .o_change').each(function(){
			if(id_shift==$(this).attr('value')){
				$(this).prop('selected', true);
				}
			});
		$('.o_change_group').each(function(){
			if(id_shift==$(this).attr('value')){
				$(this).prop('selected', true);
				}
			});
		$('#shift_from_change,#shift_from_group_change,#shift_to_group_change,#shift_to_change').selectmenu({
			width:100,
			icons:{
				button:"ui-icon-circle-triangle-s"
				}
			});
		$('#edit_id_teacher,#edit_id_teacher_group_change,#id_teacher_edit,#edit_tax_pupil,#edit_school_prog,#edit_from_date,#edit_id_branch,#pupil_select').selectmenu({
			width:200,
			icons:{
				button:"ui-icon-circle-triangle-s"
				}
			});
		$('#from_date_change, #from_date_edit, #from_date_group_change').val($(this).attr('date'));
		$('#one_time_change').append('<input type="hidden" name="from_teacher_id" value="'+$(this).attr('id_teacher')+'">');
		$('#one_time_change').append('<input type="hidden" name="old_id_branch" value="'+$(this).attr('id_branch')+'">');
		$('#one_time_change').append('<input type="hidden" name="pupil_id" value="'+$(this).attr('pupil_id')+'">');
		$('#one_time_change').append('<input type="hidden" name="from_id_day" value="'+$(this).attr('id_day')+'">');
		$('#group_change').append('<input type="hidden" name="from_id_day" value="'+$(this).attr('id_day')+'">');
		$('#group_change').append('<input type="hidden" name="from_teacher_id" value="'+$(this).attr('id_teacher')+'">');

		var date = $(this).siblings('.date');
		//$('#date_edit').append('Изменения графика с '+$(this).attr('format_date'));
		//$('#edit_schedule').append('<input type="hidden" name="prev_date" value="'+$(this).attr('prev_date')+'">');
		//$('#edit_schedule').append('<input type="hidden" name="from_date" value="'+$(this).attr('date')+'">');
		$('#edit_schedule').append('<input type="hidden" name="pupil_id" value="'+$(this).attr('pupil_id')+'">');
		$('#edit_schedule').append('<input type="hidden" name="old_id_branch" value="'+$(this).attr('id_branch')+'">');
		$('#edit_data_pupil').append('<input type="hidden" name="pupil_id" value="'+$(this).attr('pupil_id')+'">');
		$('#edit_data_pupil').append('<input type="hidden" name="old_id_branch" value="'+$(this).attr('id_branch')+'">');
		$.post('scripts/get_data.php?get_data_pupil=get_data_pupil&pupil_id='+$(this).attr('pupil_id'), function(json){
				$('#edit_data_pupil #FIO').val(json.FIO);
				/*$('#edit_data_pupil #birthday').val(json.age);
				$('#edit_data_pupil #FIO_parent').val(json.FIO_parent);
				$('#edit_data_pupil #phone').val(json.phone);
				$('#edit_data_pupil #school').val(json.school);	
				$('#edit_data_pupil #birthday').val(json.age);*/
			},"json")
		$('#tabs').dialog({title:"Редактирование - "+$(this).text()});
		$('#tabs').dialog('open');
		$('#one_time_change').submit(function(e) {
        return false;
    	});
		});
	//Добавление ученика
	/*$('.empty td.pupil').click(function(evt){
		$('#tab').tabs();
		$('#tax_pupil, #tax_pupil_one, #school_prog, #school_prog_one, #id_teacher, #id_teacher_one, #id_branch, #id_branch_one').selectmenu({
			width:200,
			icons:{
				button:"ui-icon-circle-triangle-s"
				}
			});
		evt.preventDefault();
		$('#from_date').val($(this).attr('date'));
		$('#new_add_pupil').dialog('open');
		
		})*/
		
	//Добавление урока
	//Счетчик уроков
	count_less = 1;
	$('#add_lesson, #add_one_lesson').click(function(e) {
    e.preventDefault();
	$('#del_less').css('display', 'block');
	function addLesson(count_less){
		
		if(count_less>3){return false};
		tmp_str='<div class="div'+count_less+' flex-container lesson"><label for="weekday_lesson'+count_less+'" class="lesson">Урок '+count_less+':</label>';
		tmp_str+='<select name="weekday_lesson'+count_less+'" class="sel'+count_less+'">';
		tmp_str+='<option value="1">Понедельник</option>';
		tmp_str+='<option value="2">Вторник</option>';
		tmp_str+='<option value="3">Среда</option>';
		tmp_str+='<option value="4">Четверг</option>';
		tmp_str+='<option value="5">Пятница</option>';
		tmp_str+='<option value="6">Суббота</option>';
		tmp_str+='<option value="7">Воскресенье</option>';		
		tmp_str+='</select>';
		tmp_str+='<select name="shift_lesson'+count_less+'" class="sel_shift'+count_less+'">';
		var less = $.getJSON('scripts/get_data.php?getshift=getshift', function(json){
						$.each(json,function(){
					$('.sel_shift'+count_less).append('<option value="'+this.id+'">'+this.shifts+'</option>');
				});
			})
		tmp_str+='</select><button id="del_less" class="delete_lesson">Удалить</button></div>';
		// language=JQuery-CSS
        $('#lesson_block, #lesson_one_block').append(tmp_str);
		$('.sel'+count_less).selectmenu({
			icons:{
				button:"ui-icon-circle-triangle-s"
				},
			width:220
			});
		$('.sel_shift'+count_less).selectmenu({
			width:120,
			icons:{
				button:"ui-icon-circle-triangle-s"
				}
			});
		
		
		}
	if(count_less<1) {count_less=1};	
	addLesson(count_less);
	count_less+=1;
	//Удаление строки урока
	$('.delete_lesson').click(function() {
		$('#lesson_block, #lesson_one_block').html('');
		count_less=0;
		//$('.delete_lesson').css('display', 'none');
		 });
    });
	
	//Конец добавления ученика
		/////////
		count_less_edit = 1;
	$('#add_lesson_edit').click(function(e) {
    e.preventDefault();
	$('#del_less_edit').css('display', 'block');
	function addLessonEdit(count_less_edit){
		if(count_less_edit>3){return false};
		tmp_str='<div class="div'+count_less_edit+' flex-container lesson"><label for="weekday_lesson'+count_less_edit+'" class="lesson">Урок '+count_less_edit+':</label>';
		tmp_str+='<select name="weekday_lesson'+count_less_edit+'" class="sel'+count_less_edit+'">';
		tmp_str+='<option value="1">Понедельник</option>';
		tmp_str+='<option value="2">Вторник</option>';
		tmp_str+='<option value="3">Среда</option>';
		tmp_str+='<option value="4">Четверг</option>';
		tmp_str+='<option value="5">Пятница</option>';
		tmp_str+='<option value="6">Суббота</option>';
		tmp_str+='<option value="7">Воскресенье</option>';		
		tmp_str+='</select>';
		tmp_str+='<select name="shift_lesson'+count_less_edit+'" class="sel_shift'+count_less_edit+'">';
		var less = $.getJSON('scripts/get_data.php?getshift=getshift', function(json){
						$.each(json,function(){
					$('.sel_shift'+count_less_edit).append('<option value="'+this.id+'">'+this.shifts+'</option>');
				});
			})
		tmp_str+='</select><button id="del_less_edit" class="delete_lesson">Удалить</button></div>';
		$('#lesson_block_edit').append(tmp_str);
		$('.sel'+count_less_edit).selectmenu({
			icons:{
				button:"ui-icon-circle-triangle-s"
				},
			width:220
			});
		$('.sel_shift'+count_less_edit).selectmenu({
			width:120,
			icons:{
				button:"ui-icon-circle-triangle-s"
				}
			});
		
		
		}
	if(count_less_edit<1) {count_less_edit=1};	
	addLessonEdit(count_less_edit);
	count_less_edit+=1;
	//Удаление строки урока
	$('.delete_lesson').click(function() {
		$('#lesson_block_edit').html('');
		count_less_edit=0;
		//$('.delete_lesson').css('display', 'none');
		 });
    });
		/////////
	//Модальное окно добавления ученика
	$('#new_add_pupil').dialog({
		//Автооткрытие. НЕ ЗАБЫТЬ
		autoOpen: false,
		modal:true,
		close:function(){location.reload()},
		width: 560,
		position:{
				my: "center top",
				at: "center top",
				of: "#container"
			},
		buttons:{
			"Добавить" : function(){
				//Действия после добавление
				//Валидация
				input_val =$('#add_data_pupil input');
				check_input = true;
				$.each(input_val, function(){
					if($(this).val()==''){
						$(this).css('background-color','#FFB8BA');
						check_input=false;
						} else {
							$(this).css('background-color', '#A7FFB1');
							}
					});
				if(check_input){
				var data = $('#add_data_pupil').serializeArray(); 
				
				$.post('scripts/add_data.php', data, function(json){
					if(json.fail=="fail"){
						$('#warning').dialog('open');					
						} 
					if(json.success_add=="success_add"){
						$('#new_add_pupil').dialog('close');
						location.reload(); 
						location.reload();
							}
					
					},"json");
				//$('#confirm').dialog('open');
				//
				}
				input_val =$('#add_one_less input');
				check_input2 = true;
				$.each(input_val, function(){
					if($(this).val()==''){
						$(this).css('background-color','#FFB8BA');
						check_input2=false;
						} else {
							$(this).css('background-color', '#A7FFB1');
							}
					});
				if(check_input2){
				var data2 = $('#add_one_less').serializeArray(); 
				
				$.post('scripts/add_data.php', data2, function(json){
					if(json.fail=="fail"){
						$('#warning').dialog('open');					
						} 
					if(json.success_add=="success_add"){
						$('#new_add_pupil').dialog('close');
						location.reload(); 
						location.reload();
							}
					
					},"json");
				
				}
				location.reload();
				},
			"Отменить": function(){
				//Действия при отмене
				location.reload();
				}
			}
		});
	$('#warning').dialog({
		autoOpen: false,
		modal:true
		})
	$('#edit_pupil').dialog({
		autoOpen: false,
		modal:true,
		//width: 530,
		close:function(){location.reload()},
		buttons:{
			"Добавить" : function(){
			var data = $('#form_edit_pupil').serializeArray();
			$.post('scripts/add_data.php?add_change=add_change&id_branch='+$('button[id_attr_branch]').val(), data, function(json){},"json");
			location.reload();
				}
			}
		})
	$('#tabs').dialog({
		autoOpen: false,
		modal:true,
		width: 530,
		close:function(){location.reload()},
		buttons:{
			"Личная карточка" : function(){
				
				$(location).attr('href','pupils.php?pupil_id='+$(this).attr('pupil_id')+'');
				},
			"Сохранить" : function(){
					check_date = true;
					if($('#to_date_change').val()==''){
						$('#to_date_change').css('background-color','#FFB8BA');
						check_date=false;
						} else {
							$('#to_date_change').css('background-color', '#A7FFB1');
							}
					check_date2 = true		
					if($('#to_date_group_change').val()==''){
						$('#to_date_group_change').css('background-color','#FFB8BA');
						check_date2=false;
						} else {
							$('#to_date_group_change').css('background-color', '#A7FFB1');
							}
				if(check_date){	
				var data_change = $('#one_time_change').serializeArray();
				$.post('scripts/add_data.php?change_shifts=change_shifts', data_change, function(){
					},"json");
				}
				var data_edit = $('#edit_schedule').serializeArray();
				var data_edit_pupil = $('#edit_data_pupil').serializeArray();
				if(check_date2){
				var data_group_change = $('#group_change').serializeArray();
				$.post('scripts/add_data.php?change_shifts=change_shifts', data_group_change, function(){
					},"json");
				}
				
				$.post('scripts/add_data.php?change_shifts=change_shifts', data_edit, function(){
					},"json");
				$.post('scripts/add_data.php?change_shifts=change_shifts', data_edit_pupil, function(){
					},"json");
					
				location.reload();
				location.reload();
				}
			}
		})
	//Редактирование посещения
	$('td.present, td.empty, td.seek, td.cancel').click(function(e) {
        //e.preventDefault();
		//Стили по требованию
		$('#edit_pupil').dialog('open');
		$('#form_edit_pupil:hidden').html('');
		cookie_teacher = $(this).attr('cookie_teacher');
		$('#form_edit_pupil').append('<input type="hidden" name="id_teacher" value="'+$(this).attr('id_teacher')+'">');
		$('#form_edit_pupil').append('<input type="hidden" name="pupil_id" value="'+$(this).attr('pupil_id')+'">');
		$('#form_edit_pupil').append('<input type="hidden" name="id_day" value="'+$(this).attr('id_day')+'">');
		$('#form_edit_pupil').append('<input type="hidden" name="id_shift" value="'+$(this).attr('id_shift')+'">');
		$('#form_edit_pupil').append('<input type="hidden" name="from_date" value="'+$(this).attr('date')+'">');
		$('#form_edit_pupil').append('<input type="hidden" name="to_date" value="'+$(this).attr('date')+'">');
		var data = $('#form_edit_pupil').serializeArray();
		
		$.post('scripts/get_data.php?get_change=get_change', data, function(json){
		    if(json.code_change=="4"){
		        $('#form_edit_pupil fieldset').html('');
            }
			if(json.success_check==="success_check"){
		        $('#form_edit_pupil fieldset').append('<label for="radio-4">Отменить</label>');
				$('#form_edit_pupil fieldset').append('<input type="radio" name="code_change" id="radio-4" value="cancel">');

				if(json.code_change==="4") {
                    $('#form_edit_pupil fieldset').append('<input type="hidden" name="change_cancel" value="change_cancel">');


                    if (cookie_teacher !=="999" && cookie_teacher !=="8") {
                        $('#form_edit_pupil').html('');
                    }
                }
				$( "#form_edit_pupil input" ).checkboxradio();
			    $( "#form_edit_pupil fieldset" ).controlgroup();
				}

			}, "json");
		$( "#form_edit_pupil input" ).checkboxradio();
	    $( "#form_edit_pupil fieldset" ).controlgroup();
    	});
		//Автокомплит ученика
		$('#id_search, #id_search_one').autocomplete({
		       source: 'scripts/get_data.php',
			   select: showpupil
			   });//Конец
		
					
	function showpupil(event, ui){
		$('#add_data_pupil input:hidden, #add_one_less input:hidden').remove();
		$.getJSON('scripts/get_data.php?c='+ui.item.value, function(json){
			$('#add_data_pupil, #add_one_less').append('<input type="hidden" name="pupil_id" value="'+json.id+'">');
			})
		}
   $('#pupil_ticket').dialog({
		autoOpen: false,
		modal:true,
		width: 450,
	    buttons:{
			"Добавить":function(){
				$('#pupil_select_form:hidden').html('');
				$('#pupil_select_form').append('<input type="hidden" name="FIO" value="'+$('#pupil_select').val()+'">');
				var data = $('#pupil_select_form').serializeArray();
				$.post('scripts/add_data.php?pupil_ticket=pupil_ticket', data, function(json){},"json");
				location.reload();
			}
		}
	   
	})
		
	$('.pupil_ticket').click(function(e){
		e.preventDefault();
		$('#pupil_ticket').dialog('open');
		$('#pupil_select_form:hidden').html('');
		$('#pupil_select_form').append('<input type="hidden" name="teacher" value="'+$(this).attr('teacher')+'">');
		$('#pupil_select_form').append('<input type="hidden" name="day" value="'+$(this).attr('day')+'">');
		$('#pupil_select_form').append('<input type="hidden" name="shift" value="'+$(this).attr('shift')+'">');
		$('#pupil_select_form').append('<input type="hidden" name="branch" value="'+$(this).attr('branch')+'">');
		$('#pupil_select_form').append('<input type="hidden" name="date" value="'+$(this).attr('date')+'">');
	})
	
});

</script>
</html>

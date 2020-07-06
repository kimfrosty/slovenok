<?php
include ('function.php');
include ('scripts/dbconnect.php');
$month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
$week = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");
$from_date = $_GET['from_date'];
$pupil_id = $_GET['pupil_id'];
$k = $_GET['month'];

//Если есть pupil_id
$array = PupilDataNow($pupil_id, '1', 'to_date', $from_date);
$res_FIO = db_connect("SELECT * FROM pupil WHERE id=$pupil_id");
$row_FIO = mysqli_fetch_assoc($res_FIO);
$all_sum = 0;
$sum_less = 0;
$FIO = $array[0]['FIO'];//ФИО ученика
?>
<div class="flex-center">
	<div class="month_modal center">
    	<a>Личная карточка: <span style="color:#4369FF;">
		<? echo $FIO.' (#'.$pupil_id.')'; ?></span></a>
    </div>
</div>

<div class="flex-center">
	<div class="month_modal center">
		<a><?php echo $month[$k];?></a>
    </div>
</div>
<div class="container section">
	<span>Уроки</span>
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
	
    </table></div></div>
<div class="flex-center">
	<div class="month_modal center" id="top">
		<a id="hide_disc"><?php echo 'ИТОГО: '.$all_sum?></a>
    </div>
    <div class="hide_disc month_modal center">
    	<a><?php echo 'СО СКИДКОЙ: '.($all_sum - $sum_less*50)?></a>
    </div>
</div>
<script type="text/javascript">
	$(function() {
	$('#hide_disc').click(function(){
		$('.hide_disc').fadeToggle();
		});
	$('#top').hover(function(){
		$(this).css('cursor', 'pointer');
		});
	});
</script>
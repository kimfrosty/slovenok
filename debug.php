<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	
</body>
</html>
<?php
include ("scripts/dbconnect.php");
function getDataPupil($date, $shift){
//Временные данные
$temp_arr_pupil = array();//Временный массив пупилов
$temp_finish_arr = array();//Временный финишный массив
$finish_arr_pupil = array();//Финишный массив пупилов
$del_arr_pupil = array();
$res_data_calendar = db_connect("SELECT * FROM calendar WHERE date='$date'");//Получаем weekday
			while($row_dc = mysqli_fetch_assoc($res_data_calendar)){
				$week_day = $row_dc['weekday'];//День недели номер			
				}//Конец $res_data_calendar
$res_all_pupil_from_now_date = db_connect("SELECT schedule.id_day, schedule.id_shift, schedule.pupil_id, schedule.code_change, pupil.FIO 
FROM schedule 
INNER JOIN pupil ON schedule.pupil_id=pupil.id 
WHERE schedule.from_date<='$date' AND schedule.to_date>='$date' 
ORDER BY schedule.from_date DESC");
if($res_all_pupil_from_now_date!='' and mysqli_num_rows($res_all_pupil_from_now_date)>0){
		while($row_apfnd = mysqli_fetch_assoc($res_all_pupil_from_now_date)){
			if($row_apfnd['code_change']==4||$row_apfnd['code_change']==2){
						array_push($del_arr_pupil, $row_apfnd);
						array_push($finish_arr_pupil, $row_apfnd);
						}
					if($row_apfnd['code_change']==1||$row_apfnd['code_change']==3){
						array_push($finish_arr_pupil, $row_apfnd);
						}
			for($i=0; $i<count($del_arr_pupil); $i++){
				for($j=0; $j<count($finish_arr_pupil); $j++){
					if($del_arr_pupil[$i]['pupil_id']==$finish_arr_pupil[$j]['pupil_id']&&
					($finish_arr_pupil[$j]['code_change']==1||$finish_arr_pupil[$j]['code_change']==2)){
						unset($finish_arr_pupil[$j]);
						}
					
					}
				}
			
			
					
			}//Конец $res_all_pupil_from_now_date
		
	
		foreach($finish_arr_pupil as $key=>$val){
			array_push($temp_arr_pupil, $val);
			}
	
		
		
		return $temp_arr_pupil;
		}else return false;//В БД нет чуваков на текущую дату
	}
//ОТЛАДКА
echo '<pre>';
print_r(getDataPupil('2017-08-26','5'));
echo '</pre>';


/*$arr = array();
$arr['3']['code_change']='3';
$arr['3']['code_change']='2';
if(in_array('2', $arr)){
	echo 'Ключ найден';
	} else {
		echo 'Ключ не найден';
		}*/
?>
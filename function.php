<?php
//include ("scripts/dbconnect.php");

function getDataPupil($date, $id_shift){
/*=======================Проверка каникул==================*/
$check_holiday = db_connect("SELECT * FROM holiday WHERE from_date<='".$date."' AND to_date>='".$date."'");
if(mysqli_num_rows($check_holiday)>0) return;
/*========================================================*/



//Временные данные
$temp_arr_pupil = array();//Временный массив пупилов
$temp_finish_arr = array();//Временный финишный массив
$finish_arr_pupil = array();//Финишный массив пупилов
$del_arr_pupil = array();
$del_arr_pupil2 = array();
//$del_data = db_connect("DELETE FROM schedule WHERE code_change='999'");
$res_data_calendar = db_connect("SELECT * FROM calendar WHERE date='$date'");//Получаем weekday
			while($row_dc = mysqli_fetch_assoc($res_data_calendar)){
				$week_day = $row_dc['weekday'];//День недели номер
				}//Конец $res_data_calendar
/*$res_all_pupil_from_now_date = db_connect("SELECT schedule.id, schedule.id_day, schedule.id_shift, schedule.pupil_id, schedule.code_change, pupil.FIO, pupil.id_teacher, teachers.name_teacher, tax_pupil.tax
FROM schedule
INNER JOIN pupil ON schedule.pupil_id=pupil.id
INNER JOIN teachers ON pupil.id_teacher=teachers.id
INNER JOIN tax_pupil ON pupil.tax_pupil=tax_pupil.id
WHERE schedule.from_date<='$date' AND schedule.to_date>='$date'
ORDER BY schedule.from_date DESC");*/
$res_all_pupil_from_now_date = db_connect("SELECT schedule.code_change, schedule.id_day, schedule.id_branch, schedule.id_shift, schedule.pupil_id, 
pupil.FIO, schedule.id_teacher, teachers.name_teacher 
 FROM schedule 
 INNER JOIN pupil ON schedule.pupil_id=pupil.id 
 INNER JOIN teachers ON schedule.id_teacher=teachers.id
 WHERE schedule.from_date<='$date' AND schedule.to_date>='$date' AND schedule.id_shift='$id_shift' 
 AND schedule.id_day='$week_day' AND schedule.id_branch='".$_SESSION['id_branch']."' ORDER BY schedule.id DESC");


if($res_all_pupil_from_now_date!='' and mysqli_num_rows($res_all_pupil_from_now_date)>0){
		while($row_apfnd = mysqli_fetch_assoc($res_all_pupil_from_now_date)){
			if(in_array($row_apfnd['pupil_id'], $del_arr_pupil2)) continue;
			if($row_apfnd['new_tax']==NULL){
				array_push($temp_arr_pupil, $row_apfnd);
				array_push($del_arr_pupil2, $row_apfnd['pupil_id']);
				}else{
					$arr = array();
					$arr['id']=$row_apfnd['id'];
					$arr['id_day']=$row_apfnd['id_day'];
					$arr['id_shift']=$row_apfnd['id_shift'];
					$arr['pupil_id']=$row_apfnd['pupil_id'];
					$arr['code_change']=$row_apfnd['code_change'];
					$arr['FIO']=$row_apfnd['FIO'];
					$arr['id_teacher']=$row_apfnd['id_teacher'];
					$arr['name_teacher']=$row_apfnd['name_teacher'];
					$arr['tax']=$row_apfnd['new_tax'];
					array_push($temp_arr_pupil, $arr);
					array_push($del_arr_pupil2, $row_apfnd['pupil_id']);
				}
		}//Конец $res_all_pupil_from_now_date

		for($i=0; $i<count($temp_arr_pupil); $i++){
			array_push($finish_arr_pupil, $temp_arr_pupil[$i]);
			for($j=0; $j<count($finish_arr_pupil); $j++){
				if($temp_arr_pupil[$i]['pupil_id']==$finish_arr_pupil[$j]['pupil_id']&&
				$temp_arr_pupil[$i]['id_day']==$finish_arr_pupil[$j]['id_day']&&
				$temp_arr_pupil[$i]['id_shift']==$finish_arr_pupil[$j]['id_shift']&&
				$temp_arr_pupil[$i]['code_change']!=$finish_arr_pupil[$j]['code_change']||
				$temp_arr_pupil[$i]['code_change']==2){
					unset($temp_arr_pupil[$i]);
					}
				}
			}
		foreach($temp_arr_pupil as $val=>$key){
			array_push($del_arr_pupil, $key);
			}
		return $del_arr_pupil;
		}else return false;//В БД нет чуваков на текущую дату
	}
//Функция возврата текущего тарифа по id преподавателя
/*function getLastTax($id_programm){
	global $mark_time;
	$query = "SELECT teachers.id, graph.programm, programms.tarif_teacher
	            FROM teachers
	            INNER JOIN graph ON teachers.id=graph.teacher
	            INNER JOIN programms ON graph.programm=programms.id 
	            WHERE  programms.id='$id_programm'";
	$res = db_connect($query);
	if($res!='' and mysqli_num_rows($res)>0){
		$row = mysqli_fetch_assoc($res);
		$tax = $row['tarif_teacher'];
		}
	return $tax;
	}*/
//Функция возрата для всех пупилов
function getDataAllBranch($date, $id_shift){
/*=======================Проверка каникул==================*/
$check_holiday = db_connect("SELECT * FROM holiday WHERE from_date<='".$date."' AND to_date>='".$date."'");
if(mysqli_num_rows($check_holiday)>0) return;
/*========================================================*/



//Временные данные
$temp_arr_pupil = array();//Временный массив пупилов
$temp_finish_arr = array();//Временный финишный массив
$finish_arr_pupil = array();//Финишный массив пупилов
$del_arr_pupil = array();
$del_arr_pupil2 = array();
//$del_data = db_connect("DELETE FROM schedule WHERE code_change='999'");
$res_data_calendar = db_connect("SELECT * FROM calendar WHERE date='$date'");//Получаем weekday
			while($row_dc = mysqli_fetch_assoc($res_data_calendar)){
				$week_day = $row_dc['weekday'];//День недели номер
				}//Конец $res_data_calendar
/*$res_all_pupil_from_now_date = db_connect("SELECT schedule.id, schedule.id_day, schedule.id_shift, schedule.pupil_id, schedule.code_change, pupil.FIO, pupil.id_teacher, teachers.name_teacher, tax_pupil.tax
FROM schedule
INNER JOIN pupil ON schedule.pupil_id=pupil.id
INNER JOIN teachers ON pupil.id_teacher=teachers.id
INNER JOIN tax_pupil ON pupil.tax_pupil=tax_pupil.id
WHERE schedule.from_date<='$date' AND schedule.to_date>='$date'
ORDER BY schedule.from_date DESC");*/
$res_all_pupil_from_now_date = db_connect("SELECT schedule.id, schedule.id_day, schedule.id_shift, schedule.id_branch, schedule.pupil_id, schedule.code_change, pupil.FIO, schedule.id_teacher, teachers.name_teacher, programms.tarif_pupil, programms.tarif_teacher, graph.programm
FROM schedule 
INNER JOIN pupil ON schedule.pupil_id=pupil.id 
INNER JOIN teachers ON schedule.id_teacher=teachers.id 
INNER JOIN graph ON (schedule.id_day=graph.day AND schedule.id_shift=graph.shift AND schedule.id_branch=graph.branch)
INNER JOIN programms ON graph.programm=programms.id
WHERE schedule.from_date<='$date' AND schedule.to_date>='$date' AND schedule.id_shift='$id_shift' AND schedule.id_day='$week_day' 
ORDER BY schedule.id DESC");

if($res_all_pupil_from_now_date!='' and mysqli_num_rows($res_all_pupil_from_now_date)>0){
		while($row_apfnd = mysqli_fetch_assoc($res_all_pupil_from_now_date)){
			if(in_array($row_apfnd['pupil_id'], $del_arr_pupil2)) continue;
			if($row_apfnd['new_tax']==NULL){
				array_push($temp_arr_pupil, $row_apfnd);
				array_push($del_arr_pupil2, $row_apfnd['pupil_id']);
				}else{
					$arr = array();
					$arr['id']=$row_apfnd['id'];
					$arr['id_day']=$row_apfnd['id_day'];
					$arr['id_shift']=$row_apfnd['id_shift'];
					$arr['pupil_id']=$row_apfnd['pupil_id'];
					$arr['programm']=$row_apfnd['programm'];
					$arr['code_change']=$row_apfnd['code_change'];
					$arr['FIO']=$row_apfnd['FIO'];
					$arr['id_teacher']=$row_apfnd['id_teacher'];
					$arr['name_teacher']=$row_apfnd['name_teacher'];
					$arr['tax']=$row_apfnd['tarif'];
					array_push($temp_arr_pupil, $arr);
					array_push($del_arr_pupil2, $row_apfnd['pupil_id']);
				}
		}//Конец $res_all_pupil_from_now_date
		for($i=0; $i<count($temp_arr_pupil); $i++){
			array_push($finish_arr_pupil, $temp_arr_pupil[$i]);
			for($j=0; $j<count($finish_arr_pupil); $j++){
				if($temp_arr_pupil[$i]['pupil_id']==$finish_arr_pupil[$j]['pupil_id']&&
				$temp_arr_pupil[$i]['id_day']==$finish_arr_pupil[$j]['id_day']&&
				$temp_arr_pupil[$i]['id_shift']==$finish_arr_pupil[$j]['id_shift']&&
				$temp_arr_pupil[$i]['code_change']!=$finish_arr_pupil[$j]['code_change']||
				$temp_arr_pupil[$i]['code_change']==2){
					unset($temp_arr_pupil[$i]);
					}
				}
			}
		foreach($temp_arr_pupil as $val=>$key){
			array_push($del_arr_pupil, $key);
			}
		return $del_arr_pupil;
		}else return false;//В БД нет чуваков на текущую дату
	}

function getDataAllBranchVED($date, $id_shift){
/*=======================Проверка каникул==================*/
$check_holiday = db_connect("SELECT * FROM holiday WHERE from_date<='".$date."' AND to_date>='".$date."'");
if(mysqli_num_rows($check_holiday)>0) return;
/*========================================================*/



//Временные данные
$temp_arr_pupil = array();//Временный массив пупилов
$temp_finish_arr = array();//Временный финишный массив
$finish_arr_pupil = array();//Финишный массив пупилов
$del_arr_pupil = array();
$del_arr_pupil2 = array();
//$del_data = db_connect("DELETE FROM schedule WHERE code_change='999'");
$res_data_calendar = db_connect("SELECT * FROM calendar WHERE date='$date'");//Получаем weekday
    		while($row_dc = mysqli_fetch_assoc($res_data_calendar)){
        		$week_day = $row_dc['weekday'];//День недели номер
				}//Конец $res_data_calendar
/*$res_all_pupil_from_now_date = db_connect("SELECT schedule.id, schedule.id_day, schedule.id_shift, schedule.pupil_id, schedule.code_change, pupil.FIO, pupil.id_teacher, teachers.name_teacher, tax_pupil.tax
FROM schedule
INNER JOIN pupil ON schedule.pupil_id=pupil.id
INNER JOIN teachers ON pupil.id_teacher=teachers.id
INNER JOIN tax_pupil ON pupil.tax_pupil=tax_pupil.id
WHERE schedule.from_date<='$date' AND schedule.to_date>='$date'
ORDER BY schedule.from_date DESC");*/
$res_all_pupil_from_now_date = db_connect("SELECT schedule.id, schedule.id_day, schedule.id_shift, schedule.id_branch,
 schedule.pupil_id,  schedule.code_change, pupil.FIO, schedule.id_teacher, teachers.name_teacher, programms.tarif_pupil,
  programms.tarif_teacher, graph.programm
FROM schedule 
INNER JOIN pupil ON schedule.pupil_id=pupil.id 
INNER JOIN teachers ON schedule.id_teacher=teachers.id 
INNER JOIN graph ON (schedule.id_day=graph.day AND schedule.id_shift=graph.shift AND schedule.id_branch=graph.branch)
INNER JOIN programms ON graph.programm=programms.id
WHERE schedule.from_date<='$date' AND schedule.to_date>='$date' AND schedule.id_shift='$id_shift' 
AND schedule.id_day='$week_day' AND (schedule.code_change='1' OR schedule.code_change='4')
ORDER BY schedule.id DESC");

if($res_all_pupil_from_now_date!='' and mysqli_num_rows($res_all_pupil_from_now_date)>0){
        while($row_apfnd = mysqli_fetch_assoc($res_all_pupil_from_now_date)){
            if(in_array($row_apfnd['pupil_id'], $del_arr_pupil2)) continue;
            if($row_apfnd['new_tax']==NULL){
                array_push($temp_arr_pupil, $row_apfnd);
                array_push($del_arr_pupil2, $row_apfnd['pupil_id']);
           		}else{
                	$arr = array();
                	$arr['id']=$row_apfnd['id'];
                	$arr['id_day']=$row_apfnd['id_day'];
                	$arr['id_shift']=$row_apfnd['id_shift'];
                	$arr['pupil_id']=$row_apfnd['pupil_id'];
                	$arr['programm']=$row_apfnd['programm'];
                	$arr['code_change']=$row_apfnd['code_change'];
                	$arr['FIO']=$row_apfnd['FIO'];
                	$arr['id_teacher']=$row_apfnd['id_teacher'];
                	$arr['name_teacher']=$row_apfnd['name_teacher'];
                	$arr['tax']=$row_apfnd['tarif_pupil'];
                	array_push($temp_arr_pupil, $arr);
                	array_push($del_arr_pupil2, $row_apfnd['pupil_id']);
           		}
        }//Конец $res_all_pupil_from_now_date
        for($i=0; $i<count($temp_arr_pupil); $i++){
            array_push($finish_arr_pupil, $temp_arr_pupil[$i]);
            for($j=0; $j<count($finish_arr_pupil); $j++){
                if($temp_arr_pupil[$i]['pupil_id']==$finish_arr_pupil[$j]['pupil_id']&&
                $temp_arr_pupil[$i]['id_day']==$finish_arr_pupil[$j]['id_day']&&
                $temp_arr_pupil[$i]['id_shift']==$finish_arr_pupil[$j]['id_shift']&&
                $temp_arr_pupil[$i]['code_change']!=$finish_arr_pupil[$j]['code_change']||
                $temp_arr_pupil[$i]['code_change']==2){
                	unset($temp_arr_pupil[$i]);
                	}
            	}
        	}
        foreach($temp_arr_pupil as $val=>$key){
            array_push($del_arr_pupil, $key);
        	}
        return $del_arr_pupil;
    	}else return false;//В БД нет чуваков на текущую дату
	}
/////////////////////////////////////
function getDataByTeacher($date, $id_shift, $id_teacher){
    /*=======================Проверка каникул==================*/
    $check_holiday = db_connect("SELECT * FROM holiday WHERE from_date<='".$date."' AND to_date>='".$date."'");
    if(mysqli_num_rows($check_holiday)>0) return;
    /*========================================================*/



//Временные данные
    $temp_arr_pupil = array();//Временный массив пупилов
    $temp_finish_arr = array();//Временный финишный массив
    $finish_arr_pupil = array();//Финишный массив пупилов
    $del_arr_pupil = array();
    $del_arr_pupil2 = array();
//$del_data = db_connect("DELETE FROM schedule WHERE code_change='999'");
    $res_data_calendar = db_connect("SELECT * FROM calendar WHERE date='$date'");//Получаем weekday
    while($row_dc = mysqli_fetch_assoc($res_data_calendar)){
        $week_day = $row_dc['weekday'];//День недели номер
    }//Конец $res_data_calendar
    /*$res_all_pupil_from_now_date = db_connect("SELECT schedule.id, schedule.id_day, schedule.id_shift, schedule.pupil_id, schedule.code_change, pupil.FIO, pupil.id_teacher, teachers.name_teacher, tax_pupil.tax
    FROM schedule
    INNER JOIN pupil ON schedule.pupil_id=pupil.id
    INNER JOIN teachers ON pupil.id_teacher=teachers.id
    INNER JOIN tax_pupil ON pupil.tax_pupil=tax_pupil.id
    WHERE schedule.from_date<='$date' AND schedule.to_date>='$date'
    ORDER BY schedule.from_date DESC");*/
    $res_all_pupil_from_now_date = db_connect("SELECT schedule.id, schedule.id_day, schedule.id_shift, schedule.id_branch, schedule.pupil_id, schedule.code_change, pupil.FIO, schedule.id_teacher, teachers.name_teacher, programms.tarif_pupil, programms.tarif_teacher, graph.programm
FROM schedule 
INNER JOIN pupil ON schedule.pupil_id=pupil.id 
INNER JOIN teachers ON schedule.id_teacher=teachers.id 
INNER JOIN graph ON (schedule.id_day=graph.day AND schedule.id_shift=graph.shift AND schedule.id_branch=graph.branch)
INNER JOIN programms ON graph.programm=programms.id
WHERE schedule.from_date<='$date' 
AND schedule.to_date>='$date' 
AND schedule.id_shift='$id_shift' 
AND schedule.id_day='$week_day'
AND schedule.id_teacher='$id_teacher' 
ORDER BY schedule.id DESC");

    if($res_all_pupil_from_now_date!='' and mysqli_num_rows($res_all_pupil_from_now_date)>0){
        while($row_apfnd = mysqli_fetch_assoc($res_all_pupil_from_now_date)){
            if(in_array($row_apfnd['pupil_id'], $del_arr_pupil2)) continue;
            if($row_apfnd['new_tax']==NULL){
                array_push($temp_arr_pupil, $row_apfnd);
                array_push($del_arr_pupil2, $row_apfnd['pupil_id']);
            }else{
                $arr = array();
                $arr['id']=$row_apfnd['id'];
                $arr['id_day']=$row_apfnd['id_day'];
                $arr['id_shift']=$row_apfnd['id_shift'];
                $arr['pupil_id']=$row_apfnd['pupil_id'];
                $arr['programm']=$row_apfnd['programm'];
                $arr['code_change']=$row_apfnd['code_change'];
                $arr['FIO']=$row_apfnd['FIO'];
                $arr['id_teacher']=$row_apfnd['id_teacher'];
                $arr['name_teacher']=$row_apfnd['name_teacher'];
                $arr['tax']=$row_apfnd['tarif'];
                array_push($temp_arr_pupil, $arr);
                array_push($del_arr_pupil2, $row_apfnd['pupil_id']);
            }
        }//Конец $res_all_pupil_from_now_date
        for($i=0; $i<count($temp_arr_pupil); $i++){
            array_push($finish_arr_pupil, $temp_arr_pupil[$i]);
            for($j=0; $j<count($finish_arr_pupil); $j++){
                if($temp_arr_pupil[$i]['pupil_id']==$finish_arr_pupil[$j]['pupil_id']&&
                    $temp_arr_pupil[$i]['id_day']==$finish_arr_pupil[$j]['id_day']&&
                    $temp_arr_pupil[$i]['id_shift']==$finish_arr_pupil[$j]['id_shift']&&
                    $temp_arr_pupil[$i]['code_change']!=$finish_arr_pupil[$j]['code_change']||
                    $temp_arr_pupil[$i]['code_change']==2){
                    unset($temp_arr_pupil[$i]);
                }
            }
        }
        foreach($temp_arr_pupil as $val=>$key){
            array_push($del_arr_pupil, $key);
        }
        return $del_arr_pupil;
    }else return false;//В БД нет чуваков на текущую дату
}
/////////////////////////////////////
/*//Функция возврата графика уроков ученика на текущую дату
function PupilDataNow($pupil_id, $code_change, $sort){
		$res_data_pupil = db_connect("SELECT schedule.id, schedule.id_day, schedule.date, schedule.from_date, schedule.to_date, schedule.loger,
		pupil.FIO, shifts.shifts, shifts.id AS shifts_id, branches.name_branch, branches.id AS branch_id, teachers.name_teacher, programms.name, programms.bg_color, programms.color, programms.tarif_pupil
		FROM schedule
		INNER JOIN pupil ON schedule.pupil_id=pupil.id
		INNER JOIN shifts ON schedule.id_shift=shifts.id
		INNER JOIN branches ON schedule.id_branch=branches.id
		INNER JOIN teachers ON schedule.id_teacher=teachers.id
		INNER JOIN graph ON (schedule.id_day=graph.day AND schedule.id_shift=graph.shift AND schedule.id_branch=graph.branch)
		INNER JOIN programms ON graph.programm=programms.id
		WHERE pupil_id='$pupil_id' AND code_change='$code_change' 
		ORDER BY $sort ASC");
		$array = array();
		if($res_data_pupil!='' and mysqli_num_rows($res_data_pupil)>0){
			while($row_dp = mysqli_fetch_assoc($res_data_pupil)){
				array_push($array, $row_dp);
			}
		return $array;
		}
}*/
//Функция возврата графика уроков ученика на текущую дату
function PupilDataNow($pupil_id, $code_change, $sort, $from_date){
		$array = array();
		$a=strtotime($from_date);
		$from_date1 = date('Y-m', $a);
		$sum_days = date('t', $a);
		unset($day);
		$check_arr =array();
		for($day=1; $day<=$sum_days; $day++){
			
			if($day<10){$day='0'.$day;}else{$day = $day;}
			$temp_date='';
			$temp_date = $from_date1.'-'.$day;
		
		$res_data_pupil = db_connect("SELECT schedule.id, schedule.id_day, schedule.date, schedule.from_date, schedule.to_date, schedule.loger,
		pupil.FIO, shifts.shifts, shifts.id AS shifts_id, branches.name_branch, branches.id AS branch_id, teachers.name_teacher, programms.name, programms.bg_color, programms.color, programms.tarif_pupil
		FROM schedule
		INNER JOIN pupil ON schedule.pupil_id=pupil.id
		INNER JOIN shifts ON schedule.id_shift=shifts.id
		INNER JOIN branches ON schedule.id_branch=branches.id
		INNER JOIN teachers ON schedule.id_teacher=teachers.id
		INNER JOIN graph ON (schedule.id_day=graph.day AND schedule.id_shift=graph.shift AND schedule.id_branch=graph.branch)
		INNER JOIN programms ON graph.programm=programms.id
		WHERE pupil_id='$pupil_id' AND code_change='$code_change'
		AND schedule.from_date<='$temp_date' AND schedule.to_date>='$temp_date' 
		ORDER BY $sort ASC");
		/*echo "SELECT schedule.id, schedule.id_day, schedule.date, schedule.from_date, schedule.to_date, schedule.loger,
		pupil.FIO, shifts.shifts, shifts.id AS shifts_id, branches.name_branch, branches.id AS branch_id, teachers.name_teacher, programms.name, programms.bg_color, programms.color, programms.tarif_pupil
		FROM schedule
		INNER JOIN pupil ON schedule.pupil_id=pupil.id
		INNER JOIN shifts ON schedule.id_shift=shifts.id
		INNER JOIN branches ON schedule.id_branch=branches.id
		INNER JOIN teachers ON schedule.id_teacher=teachers.id
		INNER JOIN graph ON (schedule.id_day=graph.day AND schedule.id_shift=graph.shift AND schedule.id_branch=graph.branch)
		INNER JOIN programms ON graph.programm=programms.id
		WHERE pupil_id='$pupil_id' AND code_change='$code_change'
		AND schedule.from_date<='$temp_date' AND schedule.to_date>='$temp_date' 
		ORDER BY $sort ASC";*/
		
		
		if($res_data_pupil!='' and mysqli_num_rows($res_data_pupil)>0){
			
			while($row_dp = mysqli_fetch_assoc($res_data_pupil)){
				if(in_array($row_dp['id'], $check_arr)){
					continue;
					}else{
						array_push($check_arr, $row_dp['id']);
						}
				
				
				array_push($array, $row_dp);
				
			}
		
		}
	}
	return $array;
}
function CheckTestLesson($date, $branch, $day, $shift){
	$res_test_lesson = db_connect("SELECT *, teachers.name_teacher FROM test_lessons 
									INNER JOIN teachers ON test_lessons.id_teacher=teachers.id
									WHERE date='".$date."' 
																		AND id_branch='".$branch."'
																		AND id_day='".$day."'
																		AND id_shift='".$shift."'");
	$resault = $res_test_lesson;
	return $resault;
	}
	
function CheckTestLessonAllBranch($date, $teacher){
	$res_test_lesson = db_connect("SELECT * FROM test_lessons 
									INNER JOIN programms ON test_lessons.programm=programms.id
									WHERE date='".$date."'
									AND id_teacher='".$teacher."'");
	$resault = $res_test_lesson;
	return $resault;
	}

function CheckTestLessonTeacher($date, $branch, $day, $shift, $teacher){
	$res_test_lesson = db_connect("SELECT *, teachers.name_teacher FROM test_lessons 
									INNER JOIN teachers ON test_lessons.id_teacher=teachers.id
									WHERE date='".$date."' 
																		AND id_branch='".$branch."'
																		AND id_day='".$day."'
																		AND id_shift='".$shift."'
																		AND id_teacher='".$teacher."'");
	$resault = $res_test_lesson;
	return $resault;
	}

//Функция возврата часов учителей в месяц
/*echo '<pre>';
print_r(PupilDataNow(131, 1, 'to_date', '2019-06-24'));
echo '</pre>';*/

  





/*echo '<pre>';
print_r(getDataAllBranchVED('2019-02-05', '10'));
echo '</pre>';*/
//echo  getLastTax('3', '22');


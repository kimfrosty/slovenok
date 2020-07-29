<?php
require('dbconnect.php');
require('../function.php');
//Получение данных ученика
if(isset($_GET['data_id'],$_GET['data_date'],$_GET['data_shift'])){
	$result_data = db_connect("SELECT pupil.FIO, pupil.age, pupil.FIO_parent, pupil.phone, school_prog.program
								FROM pupil 
								INNER JOIN school_prog ON pupil.school_prog=school_prog.id
								WHERE pupil.id = '".$_GET['data_id']."'");
	if($result_data!='' and mysqli_num_rows($result_data)>0){
		echo json_encode(array("data_pupil"=>mysqli_fetch_assoc($result_data)));
		}else json_encode(array('fail'=>'fail'));
	}
	
//Получение смен
if($_GET['getshift']=='getshift'){
	$arr_shift = array();
	$res_shifts = db_connect("SELECT * FROM shifts");
	if($res_shifts!='' and mysqli_num_rows($res_shifts)>0){
		while($row_s = mysqli_fetch_assoc($res_shifts)){
			array_push($arr_shift, $row_s);
			}
		echo json_encode($arr_shift);
		}
	}
//Получение данных по подмене для скрывания кнопки отменить
if($_GET['get_change']=='get_change'){
	
	$from_date = $_POST['from_date'];
	$to_date = $_POST['to_date'];
	$pupil_id = $_POST['pupil_id'];
	$id_day = $_POST['id_day'];
	$id_shift= $_POST['id_shift'];
	$res_data_change_check = db_connect("SELECT * FROM schedule WHERE pupil_id='$pupil_id' 
																AND from_date='$from_date' 
																AND to_date='$to_date' 
																AND id_day='$id_day' 
																AND id_shift='$id_shift' AND (code_change!='2' AND code_change!='3')");
															
	if($res_data_change_check!='' and mysqli_num_rows($res_data_change_check)>0){
		$row_cancel = mysqli_fetch_assoc($res_data_change_check);
	    echo json_encode(array('success_check'=>'success_check', 'code_change'=>$row_cancel['code_change']));
		}
	}
if($_GET['get_all_pupil']=='get_all_pupil'){
	$all_pupil = array();
	$res = db_connect("SELECT FIO FROM pupil");
	while($row=mysqli_fetch_assoc($res)){
		array_push($all_pupil, $row);
		}	
	echo json_encode($all_pupil);
	}
if($_GET['get_data_pupil']=="get_data_pupil"){
	$pupil_id = $_GET['pupil_id'];
	$res = db_connect("SELECT * FROM pupil WHERE id='".$pupil_id."'");
	$row = mysqli_fetch_assoc($res);
	echo json_encode($row);
		}
/*Предварительный расчет*/
if($_GET['temp_payment']=='temp_payment'){
	
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$id_teacher= $_POST['id_teacher'];
	
$count_p = 1;
	
	$res_teachers = db_connect("SELECT  teachers.id, teachers.name_teacher, teachers.tax_teacher, tax_change_teachers.new_tax_teacher 
FROM teachers
LEFT JOIN tax_change_teachers ON (teachers.id=tax_change_teachers.teacher_id AND tax_change_teachers.from_date<='$from_date' AND tax_change_teachers.to_date>='$to_date')");
	
	while($row_t = mysqli_fetch_assoc($res_teachers)){
		$final_cash=0;
		$oplata =0;
		$tax_teacher=0;
		if($row_t['new_tax_teacher']==NULL){
			$tax_teacher = $row_t['tax_teacher'];
			}else{
				$tax_teacher = $row_t['new_tax_teacher'];
				}
		
		$count_pup_hour = 0;
		$temp_count = array();
		$count_hour=0;
		$res_date = db_connect("SELECT * FROM calendar 
							WHERE date>='".$from_date."' AND date<='".$to_date."'");
		while($row_date = mysqli_fetch_assoc($res_date)){
		/*=======================Проверка каникул==================*/
		$check_holiday = db_connect("SELECT * FROM holiday WHERE from_date<='".$row_date['date']."' AND to_date>='".$row_date['date']."'");
		if(mysqli_num_rows($check_holiday)>0) continue;
		/*========================================================*/
		
		$test_lesson = CheckTestLessonAllBranch($row_date['date'], $row_t['id']);
		while ($row_tl = mysqli_fetch_assoc($test_lesson)){
			$oplata+=$row_tl['tarif_teacher'];
			$count_hour++;
		}
		
		$res_shifts = db_connect("SELECT * FROM shifts");
		while($row_s = mysqli_fetch_assoc($res_shifts)){
		$count_pupil = 0;
		$arr_pupil_date = [];
		$arr_pupil_date = getDataAllBranch($row_date['date'], $row_s['id']);
		/////////////////////////////////////////////////////////////////

			for($i=0; $i<count($arr_pupil_date); $i++){
				if($arr_pupil_date[$i]['code_change']!=6&&$arr_pupil_date[$i]['code_change']!=2&&$arr_pupil_date[$i]['code_change']!=5&&$arr_pupil_date[$i]['code_change']!=4){
					if($row_t['id']==$arr_pupil_date[$i]['id_teacher']){
						$count_hour++;
						$oplata+=$arr_pupil_date[$i]['tarif_teacher'];
						break;
					}
				}
			}
		/////////////////////////////////////////////////////////////////		
		for($i=0; $i<count($arr_pupil_date); $i++){
			/*if($row_t['id']==$arr_pupil_date[$i]['id_teacher']){
								if(!in_array($arr_pupil_date[$i]['pupil_id'], $temp_count)) array_push($temp_count, $arr_pupil_date[$i]['pupil_id']);
				}*/
			if($id_teacher==$arr_pupil_date[$i]['id_teacher']&&
				$row_date['weekday']==$arr_pupil_date[$i]['id_day']&&
				($arr_pupil_date[$i]['code_change']==1||$arr_pupil_date[$i]['code_change']==3)){
					$count_pup_hour++;
					$count_pupil++;
					}
				}
			
			if(($count_pupil*$tax_teacher)<$min_tax&&$count_pupil>0){
				$final_cash+=$min_tax;
				}else{
					$final_cash+=($count_pupil*$tax_teacher);
					}
			}
			//////////////////////////
			$check_final_cash = $oplata;
			//////////////////////////
		}	
	
	if($row_t['id']==$id_teacher) {
		
		/*echo json_encode(array('success'=>'success', 'final_cash'=>$final_cash, 'count_pupil_hour'=>$count_pup_hour));*/
		echo json_encode(array('success'=>'success', 'final_cash'=>$check_final_cash, 'count_pupil_hour'=>$count_hour));
		exit;
	
	}
		}
	


}

//автокомплит
if(!empty($_GET['term'])){
	$term = $_GET['term'];
	// Шаблон рег. выражения
	$query = "SELECT DISTINCT pupil.FIO, pupil.id FROM pupil WHERE pupil.FIO LIKE '%$term%' ORDER BY pupil.id";
	$res = db_connect($query);
	$all_pupil = array();
	
	while($row = mysqli_fetch_assoc($res)){
		array_push($all_pupil, $row['FIO']);
		}
		echo json_encode($all_pupil);
		}
if(!empty($_GET['c'])){
	$get_pupil = "SELECT id FROM pupil WHERE FIO='".$_GET['c']."'";
	$res_c = db_connect($get_pupil);
	$data_pupil = mysqli_fetch_assoc($res_c);
	echo json_encode($data_pupil);
	}


	//Запрос данных про графику и программе
if($_GET['q']=='getprog'){
    $programm = $_GET['prog'];
    $temp_arr = [];
    $fin_arr = [];
    $res = db_connect("SELECT graph.branch, branches.name_branch FROM graph INNER JOIN branches 
                       ON graph.branch=branches.id WHERE graph.programm='$programm'");
    while($row = mysqli_fetch_assoc($res)){
     $temp_arr['branch'] = $row['branch'];
     $temp_arr['branch_name']= $row['name_branch'];
     array_push($fin_arr, $temp_arr);
    }
    echo json_encode($fin_arr);
}
    //Запрос данных на дни
if($_GET['q']=='get_day'){
    $branch = $_GET['branch'];
    $programm = $_GET['prog'];
    $res_day = db_connect("SELECT DISTINCT day FROM graph WHERE programm='$programm' AND branch='$branch' ORDER BY day ASC ");
    $fin_day=[];
    while($row_d = mysqli_fetch_assoc($res_day)){
        array_push($fin_day, $row_d);
    }
    echo json_encode($fin_day);
}
//Запрос данных на смены
if($_GET['q']=='get_shift'){
    $programm = $_GET['prog'];
    $branch = $_GET['branch'];
    $day = $_GET['day'];
    $res_shift = db_connect("SELECT graph.shift, shifts.shifts FROM graph INNER JOIN shifts ON graph.shift=shifts.id
                              WHERE graph.programm='$programm' AND graph.branch='$branch' AND graph.day='$day' ORDER BY graph.shift ASC");
    $fin_shift = [];
    while($row_shift = mysqli_fetch_assoc($res_shift)){
        array_push($fin_shift, $row_shift);
    }
    echo json_encode($fin_shift);
}
/*-------Изменение программы----------------*/
//Запрос на получение свободных дней
if($_GET['p']=='getday'){
    //Запрос на смены
    $res_s = db_connect("SELECT * FROM shifts");
    $fin_p_day = [];
    while($rows = mysqli_fetch_assoc($res_s)) {
        for ($i = 1; $i <= 7; $i++) {
            $res_day = db_connect("SELECT * FROM graph WHERE branch='{$_GET['branch']}' AND day='$i' 
                                  AND shift ='{$rows['id']}'");
            if(mysqli_num_rows($res_day)<1){
                $fin_p_day[]=$i;
            }

        }
    }
    $temp_arr = array_unique($fin_p_day);
    sort($temp_arr);
    echo json_encode($temp_arr);
}
//Запрос на свободные смены
if($_GET['p']=='getshifts'){
    $res_s = db_connect("SELECT * FROM shifts");
    $fin_p_shift=$fin_temp=[];
    while($rows=mysqli_fetch_assoc($res_s)){
        $res_shift = db_connect("SELECT * FROM graph WHERE branch='{$_GET['branch']}' AND day='{$_GET['day']}' 
                                  AND shift ='{$rows['id']}'");
        if(mysqli_num_rows($res_shift)<1){
            $fin_temp['id']=$rows['id'];
            $fin_temp['shift']=$rows['shifts'];
            array_push($fin_p_shift, $fin_temp);
        }
    }
    echo json_encode($fin_p_shift);
}
//Добавление программы
if($_GET['p']=='get_teacher'){
    $temp_teach = [];
    $res = db_connect("SELECT * FROM teachers");
    while($row=mysqli_fetch_assoc($res)){
        array_push($temp_teach, $row);
    }
    echo json_encode($temp_teach);
}
if($_GET['p']=='get_programm'){
    $temp_prog = [];
    $res = db_connect("SELECT * FROM programms");
    while($row=mysqli_fetch_assoc($res)){
        array_push($temp_prog, $row);
    }
    echo json_encode($temp_prog);
}





























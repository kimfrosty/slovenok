<?php
include ('function.php');
include ('scripts/dbconnect.php');
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$hours_all = 0;
$res_a_t = db_connect("SELECT * FROM teachers WHERE id=".$_GET['teacher_id']);
$row_a_t = mysqli_fetch_assoc($res_a_t);
$res_a_b = db_connect("SELECT * FROM branches");
while ($row_a_b = mysqli_fetch_assoc($res_a_b)) {
    $hours = 0;
	$j = 1;
    $res_a_s = db_connect("SELECT * FROM shifts");
    echo '<table id_branch ="'.$row_a_b['id'].'"><tr><td style="min-width:100px; background-color: #fcd113;">'.$row_a_b['name_branch'].'</td>';
    $res_d = db_connect("SELECT * FROM calendar WHERE date>=$from_date AND date<=$to_date");
        while ($row_d = mysqli_fetch_assoc($res_d)) {
		if($row_d['weekday']==6||$row_d['weekday']==7){
				echo '<td style="min-width:20px; background-color: #FFE5E4; font-size:12px;">'.$j++.'</td>'; 
			}
        		else {echo '<td style="min-width:20px; background-color: #3092c0; font-size:12px;">'.$j++.'</td>';}
    }
	$j = 1;
    echo '</tr>';
    while ($row_a_s = mysqli_fetch_assoc($res_a_s)) {

        echo '<tr><td style="font-size:10px; background-color: #acdd4a;">'.$row_a_s['shifts'].'</td>';
        $res_d = db_connect("SELECT * FROM calendar WHERE date>=$from_date AND date<=$to_date");
        while ($row_d = mysqli_fetch_assoc($res_d)) {
            $check_holiday = db_connect("SELECT * FROM holiday WHERE from_date<='".$row_d['date']."' AND to_date>='".$row_d['date']."'");
            if(mysqli_num_rows($check_holiday)>0) {echo '<td style="width: 20px; height: 10px;"></td>';continue;}
            $date = $row_d['date'];
            $shift = $row_a_s['id'];
            $teacher = $row_a_t['id'];
            $day = $row_d['weekday'];
            $branch = $row_a_b['id'];
            $res_all = db_connect("SELECT schedule.id_day, schedule.id_shift, schedule.id_branch, schedule.id_teacher, schedule.code_change FROM schedule 
										WHERE schedule.from_date<='$date' 
										AND schedule.to_date>='$date' 
										AND schedule.id_shift='$shift' 
										AND schedule.id_day='$day'
										AND schedule.id_teacher='$teacher' 
										AND schedule.id_branch='$branch' 
										AND (schedule.code_change='1' OR schedule.code_change='3' OR schedule.code_change='2' OR schedule.code_change='4') 
										ORDER BY schedule.code_change DESC LIMIT 1");
			$res_sum = db_connect("SELECT programms.tarif_teacher FROM `graph` 
														INNER JOIN programms ON graph.programm=programms.id
														WHERE branch='$branch' AND day='$day' AND shift='$shift'");
			$row_sum = mysqli_fetch_assoc($res_sum);
			$coeff = ($row_sum['tarif_teacher']/400);
			$test_lesson = CheckTestLessonTeacher($date, $branch, $day, $shift, $teacher);
			$check_empty = CheckEmptyLesson($branch, $date, $day, $shift);
			$row_all = mysqli_fetch_assoc($res_all);
			
			if (mysqli_num_rows($test_lesson)>0) {
                    	echo '<td class="coeff" style="width: 20px; height: 10px; background-color: #00aaff"; color: white;>'.$coeff.'</td>'; $hours++;
                	}elseif($row_all['code_change'] == 1 || $row_all['code_change'] == 3) {
						if($check_empty == false){
                    		echo '<td class="coeff" style="width: 20px; height: 10px; background-color: green; color: white;">'.$coeff.'</td>'; $hours++;
						}else {echo '<td style="width: 20px; height: 10px;"></td>';}
                	} elseif ($row_all['code_change'] == 4){
                echo '<td style="width: 20px; height: 10px; background-color: red"></td>';
            }elseif($row_d['weekday']==6||$row_d['weekday']==7){echo '<td style="width: 20px; height: 10px; background-color: #FFE5E4;"></td>';} 
			else {echo '<td style="width: 20px; height: 10px;"></td>';}
		}
        echo '</tr>';
    }
    $hours_all = $hours_all + $hours;
    echo '</table>'.$hours.'</br></br>';
}
echo 'Всего: '.$hours_all;
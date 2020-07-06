<?php
require("dbconnect.php");
require("../function.php");
//Добавление ученика
/*if($_POST['form_pupil']=='form_pupil'&&$_POST['add_new_pupil']=='add_new_pupil'){
	if(!isset($_POST['weekday_lesson1'])){
		die(json_encode(array("fail"=>"fail")));
		}
	//Урок добавлен
	//Добавляем переменные
	$fio = $_POST['FIO'];
	$from_date = $_POST['from_date'];
	$to_date = '2031-04-04';
	$id_teacher = $_POST['id_teacher'];
	$id_branch = $_POST['id_branch'];
	$tax_pupil = $_POST['tax_pupil'];
	$loger = $_COOKIE['name'];//Получить из куки
	//Добавляем информацию об ученике
	$res_add_pupil = db_connect("INSERT INTO pupil (FIO) 
									VALUES('$fio')");
	$res_pupil_id = db_connect("SELECT * FROM pupil ORDER BY id DESC");
	$row_id = mysqli_fetch_assoc($res_pupil_id);
	$pupil_id = $row_id['id'];
	if(!$res_add_pupil) {die(json_encode(array("fail"=>"fail", "messege"=>"2")));
	}else{ echo json_encode(array("success_add"=>"success_add"));}
	if(isset($_POST['weekday_lesson1'])&&isset($_POST['shift_lesson1'])){
		$day_less1 = $_POST['weekday_lesson1'];
		$shift_less1=$_POST['shift_lesson1'];
		$res_less1 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`,`id_branch`, `id_teacher`, `code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$to_date', '$day_less1', '$shift_less1', '$id_branch', '$id_teacher','1', '$loger', '')");
		if(!$res_less1) die(json_encode(array("fail"=>"fail", "messege"=>"3")));
		}
	if(isset($_POST['weekday_lesson2'])&&isset($_POST['shift_lesson2'])){
		$day_less2 = $_POST['weekday_lesson2'];
		$shift_less2=$_POST['shift_lesson2'];
		$res_less2 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`, `id_teacher`,`code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$to_date', '$day_less2', '$shift_less2','$id_branch', '$id_teacher','1', '$loger', '')");
		if(!$res_less2) die(json_encode(array("fail"=>"fail", "messege"=>"4")));
		}
	if(isset($_POST['weekday_lesson3'])&&isset($_POST['shift_lesson3'])){
		$day_less3 = $_POST['weekday_lesson3'];
		$shift_less3=$_POST['shift_lesson3'];
		$res_less3 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`,`id_teacher`,`code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$to_date', '$day_less3', '$shift_less3','$id_branch', '$id_teacher','1', '$loger', '')");
		if(!$res_less3) die(json_encode(array("fail"=>"fail", "messege"=>"5")));
		}
	}*/
//Добавление ученика
if ($_POST['add_one_pupil'] == 'add_one_pupil') {
    $FIO = $_POST['FIO'];
    $res_add_pupil = mysqli_query($dbc, "INSERT INTO pupil (FIO) VALUES ('$FIO')");
    $insert_id = mysqli_insert_id($dbc);
    db_connect("INSERT INTO schedule (date, pupil_id, from_date, to_date, id_day, id_shift, id_branch, id_teacher,
                code_change, loger) 
                VALUES (NOW(), '$insert_id', '{$_POST['from_date']}', '2031-04-04', '{$_POST['old_day']}', 
                '{$_POST['old_shift']}', '{$_POST['old_branch']}', '{$_POST['id_teacher']}', '1', '{$_COOKIE['name']}')");

}
//Конец добавления ученика

//Добавление одного урока старому ученику
if ($_POST['add_one_less'] == 'add_one_less') {
    if (!isset($_POST['weekday_lesson1'])) {
        die(json_encode(array("fail" => "fail")));
    }
    //Урок добавлен
    //Добавляем переменные
    $fio = $_POST['FIO'];
    $from_date = $_POST['from_date_one'];
    $to_date = '2031-04-04';
    $id_teacher = $_POST['id_teacher_one'];
    $id_branch = $_POST['id_branch_one'];
    $tax_pupil = $_POST['tax_pupil'];
    $pupil_id = $_POST['pupil_id'];
    ////////
    if (mysqli_num_rows(db_connect("SELECT * FROM pupil WHERE id='$pupil_id'")) < 1) {
        die(json_encode(array("fail" => "fail", "messege" => "Такого ученика нет")));
    }
    ///////////
    $loger = $_COOKIE['name'];//Получить из куки
    //Добавляем информацию об ученике
    if (isset($_POST['weekday_lesson1']) && isset($_POST['shift_lesson1'])) {
        $day_less1 = $_POST['weekday_lesson1'];
        $shift_less1 = $_POST['shift_lesson1'];
        $res_less1 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`,`id_branch`, `id_teacher`, `code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$to_date', '$day_less1', '$shift_less1', '$id_branch', '$id_teacher','1', '$loger', '')");
        if (!$res_less1) die(json_encode(array("fail" => "fail", "messege" => "3")));
    }
    if (isset($_POST['weekday_lesson2']) && isset($_POST['shift_lesson2'])) {
        $day_less2 = $_POST['weekday_lesson2'];
        $shift_less2 = $_POST['shift_lesson2'];
        $res_less2 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`, `id_teacher`,`code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$to_date', '$day_less2', '$shift_less2','$id_branch', '$id_teacher','1', '$loger', '')");
        if (!$res_less2) die(json_encode(array("fail" => "fail", "messege" => "4")));
    }
    if (isset($_POST['weekday_lesson3']) && isset($_POST['shift_lesson3'])) {
        $day_less3 = $_POST['weekday_lesson3'];
        $shift_less3 = $_POST['shift_lesson3'];
        $res_less3 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`,`id_teacher`,`code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$to_date', '$day_less3', '$shift_less3','$id_branch', '$id_teacher','1', '$loger', '')");
        if (!$res_less3) die(json_encode(array("fail" => "fail", "messege" => "5")));
    }
}


//Добавление подмены(заболел, пропустил, закончил, отменить занятие)
if ($_GET['add_change'] == 'add_change') {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $code_change = $_POST['code_change'];
    $pupil_id = $_POST['pupil_id'];
    $id_day = $_POST['id_day'];
    $id_shift = $_POST['id_shift'];
    $id_teacher = $_POST['id_teacher'];
    $loger = $_COOKIE['name'];
    $id_branch = $_GET['id_branch'];
    //Получаем данные по подмене на текущего ученика
    if ($code_change == 7) {
        //Получаем штатное расписание
        $temp_date = new DateTime($from_date);
        $new_from_date = date('Y-m-d', $temp_date->getTimestamp() - 86400);
        $update_schedule = db_connect("UPDATE schedule SET to_date='$new_from_date' WHERE pupil_id='$pupil_id' AND code_change='1' AND to_date>='$new_from_date'");
        $update_schedule1 = db_connect("DELETE FROM schedule WHERE pupil_id='$pupil_id' AND code_change='6' AND to_date>='$new_from_date'");
    }
    if ($code_change != 7 && $code_change!=4) {
        $res_data_change = db_connect("SELECT * FROM schedule WHERE pupil_id='$pupil_id' 
																AND from_date='$from_date' 
																AND to_date='$to_date' 
																AND id_day='$id_day' 
																AND id_shift='$id_shift' AND code_change!='3' AND code_change!='2'");
        if ($res_data_change != '' and mysqli_num_rows($res_data_change) > 0) {
            $row_dc = mysqli_fetch_assoc($res_data_change);
            if ($code_change != 'cancel') {
                $res_change = db_connect("UPDATE schedule SET code_change='$code_change' WHERE id='" . $row_dc['id'] . "'");
            } else {

                //Удаление отмены занятий
                if ($_POST['change_cancel'] == 'change_cancel') {
                    $res_cancel1 = db_connect("SELECT * FROM `schedule` WHERE id_day='$id_day' AND id_shift='$id_shift' 
                    AND (from_date<='$from_date' AND to_date>='$from_date') AND id_branch='$id_branch' AND code_change='4'");
                    if ($res_cancel1 != '' and mysqli_num_rows($res_cancel1) > 0) {
                        while ($row_c1 = mysqli_fetch_assoc($res_cancel1)) {
                            db_connect("DELETE FROM schedule WHERE id = '" . $row_c1['id'] . "'");
                        }
                    }

            }  //
                $res_change = db_connect("DELETE FROM schedule WHERE id = '" . $row_dc['id'] . "'");

            }
        } else {
            $res_less2 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, 
                                        `id_shift`, `id_branch`,`id_teacher`, `code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$to_date', '$id_day', '$id_shift', '$id_branch', '$id_teacher', 
		'$code_change', '$loger', '')");
        }
    }
    //Отмена занятия
    if ($code_change == 4) {
        $res_cancel = db_connect("SELECT * FROM `schedule` WHERE id_day='$id_day' AND id_shift='$id_shift' 
                AND (from_date<='$from_date' AND to_date>='$from_date') AND id_branch='$id_branch' 
                AND (code_change='1' OR code_change='3')");
        if ($res_cancel != '' and mysqli_num_rows($res_cancel) > 0) {
            while ($row_c = mysqli_fetch_assoc($res_cancel)) {
                db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, 
                                                  `id_branch`,`id_teacher`, `code_change`, `loger`, `comment`)
		                       VALUES ('$mark_date','" . $row_c['pupil_id'] . "','$from_date','$to_date', '$id_day', 
		                       '$id_shift', '$id_branch', '$id_teacher', '$code_change', '$loger', '')");
            }
        }
    }
    //Конец отмены занятия
}

//Оплата месяца
if ($_GET['type_of_payment'] == 'sum_month') {
    $count_day_in_month = date('t', $mark_time);
    $pupil_id = $_POST['pupil_id'];
    $date = $_POST['date'];
    $money = $_POST['money'];
	$less = $_POST['less'];
    $from_date_payment = $_POST['from_date'];
    $to_date_payment = $_POST['to_date'];
    $discount_proc = $_POST['discount_proc'];
    $discount_cash = $_POST['discount_cash'];
    $discount = (($less*$discount_proc) + $discount_cash);
    $extra = $_POST['extra'];
    $comment = $_POST['comment'];
    $loger = $_COOKIE['name'];
    //Получаем данные об оплате если есть
    $res_data_payment = db_connect("SELECT * FROM payment WHERE pupil_id='$pupil_id' AND from_date<='$from_date_payment' 
                                    AND to_date>='$to_date_payment'");
    if ($res_data_payment != '' and mysqli_num_rows($res_data_payment) > 0) {
        $row_payment = mysqli_fetch_assoc($res_data_payment);
        $res_update_payment = db_connect("UPDATE payment SET pay='$money', extra='$extra', 
                                          comment='$comment', loger='$loger'
                                          WHERE id='" . $row_payment['id'] . "'");
        if (!$res_update_payment) die(json_encode(array('fail' => 'fail', 'message' => 'Ошибка обновления оплаты месяца')));
    } else {
        $res_insert_month = db_connect("INSERT INTO payment (date, pupil_id, from_date, to_date, pay, discount, extra, comment, loger) 
											VALUES ('$date', '$pupil_id', '$from_date_payment', '$to_date_payment', '$money', '$discount', '$extra', '$comment', '$loger')");
    }


}
//Возврат денег
if ($_GET['type_of_payment'] == 'sum_cashback') {
    $count_day_in_month = date('t', $mark_time);
    $pupil_id = $_POST['pupil_id'];
    $date = $_POST['date'];
    $money = $_POST['money'];
    $from_date_payment = $_POST['from_date'];
    $to_date_payment = $_POST['to_date'];
    //Получаем данные об оплате если есть
    $res_data_payment = db_connect("SELECT * FROM payment WHERE pupil_id='$pupil_id' AND from_date<='$from_date_payment' AND to_date>='$to_date_payment'");
    if ($res_data_payment != '' and mysqli_num_rows($res_data_payment) > 0) {
        $row_cashback = mysqli_fetch_assoc($res_data_payment);

        //$res_update_payment = db_connect("UPDATE payment SET cashback='$money' WHERE id='".$row_cashback['id']."'");
        //if(!$res_update_payment) die(json_encode(array('fail'=>'fail', 'message'=>'Ошибка обновления оплаты возврата')));
    } else {
        //$res_insert_month = db_connect("INSERT INTO payment (date, pupil_id, from_date, to_date, cashback)
        //								VALUES ('$date', '$pupil_id', '$from_date_payment', '$to_date_payment', '$money')");
    }


}
//Чистый возврат денег
if ($_GET['type_of_payment'] == 'sum_clear_cashback') {
    $pupil_id = $_POST['pupil_id'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $sum = $_POST['sum_cashback'];
    $res = db_connect("INSERT INTO clear_cashback (pupil_id, from_date, to_date, sum_cashback) VALUES ('$pupil_id', '$from_date','$to_date', '$sum')");
    if (!$res) die(json_encode(array('fail' => 'fail', 'message' => 'Ошибка добавления чистой оплаты')));
}
//Редактирование графиков
//Функция проверки графика
function checkGraph($day, $shift, $branch)
{
    $res = db_connect("SELECT * FROM graph WHERE day='$day' AND shift='$shift' AND branch='$branch'");
    if ($res == '' or mysqli_num_rows($res) < 1) {
        return false;
    } else return true;
}

if ($_GET['change_shifts'] == 'change_shifts') {
    //Редактирование разового переноса
    if ($_POST['edit_mode'] == 'one_time_change') {
        if ($_POST['one_change'] == 'yes') {

            //Данные для снятия с графика
            $pupil_id = $_POST['pupil_id'];
            $from_date = $to_date = $_POST['from_date'];
            $id_shift = $_POST['shift_from_change'];
            $id_day = $_POST['from_id_day'];
            $id_teacher = $_POST['id_teacher'];
            $new_id_branch = $_POST['new_id_branch'];
            $old_id_branch = $_POST['old_id_branch'];
            $new_id_teacher = $_POST['from_teacher_id'];
            $code_change = 2;
            $loger = $_COOKIE['name'];//Взять cookies
            //Данные на новую дату
            $new_from_date = $new_to_date = $_POST['to_date'];
            $res_get_id_day = db_connect("SELECT * FROM calendar WHERE date='$new_from_date'");
            $row_gid = mysqli_fetch_assoc($res_get_id_day);
            /*if (!checkGraph($row_gid['weekday'], $_POST['shift_to_change'], $_POST['new_id_branch'])) {
                echo json_encode(array('fail' => 'fail_graph'));
                exit;
            }*/
            $res_check = db_connect("SELECT * FROM schedule WHERE from_date='$from_date' AND to_date='$from_date' AND pupil_id='$pupil_id' AND code_change='3'");
            if ($res_check != '' and mysqli_num_rows($res_check) > 0) {
                $row_check = mysqli_fetch_assoc($res_check);
                $new_shift = $_POST['shift_to_change'];
                $new_code_change = 3;
                $res_get_id_day = db_connect("SELECT * FROM calendar WHERE date='$new_from_date'");
                $row_gid = mysqli_fetch_assoc($res_get_id_day);
                $res_check_update = db_connect("UPDATE schedule SET from_date='$new_from_date', to_date='$new_from_date', id_shift='$new_shift', id_branch='$new_id_branch', id_day='" . $row_gid['weekday'] . "' WHERE id='" . $row_check['id'] . "'");
            } else {
                $res_remove_date = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`, `id_teacher`, `code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$to_date', '$id_day', '$id_shift', '$old_id_branch',  '$new_id_teacher', '$code_change', '$loger', '')");
                if (!$res_remove_date) die(json_encode(array('fail' => 'fail', 'message' => 'Ошибка снятия с даты')));
                $new_shift = $_POST['shift_to_change'];
                $new_code_change = 3;
                $res_new_date = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`, `id_teacher`, `code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$new_from_date','$new_to_date', '" . $row_gid['weekday'] . "', '$new_shift', '$new_id_branch',  '$id_teacher', '$new_code_change', '$loger', '')");
                if (!$res_new_date) die(json_encode(array('fail' => 'fail', 'message' => 'Ошибка добавления на дату')));
            }
        }
        exit;
    }

    if ($_POST['edit_mode'] == 'edit_schedule') {
        $pupil_id = $_POST['pupil_id'];
        $from_date = $_POST['from_date'];
        $temp_prev_date = new DateTime($from_date);
        $prev_date = date('Y-m-d', $temp_prev_date->getTimestamp() - 86400);
        $id_teacher = $_POST['id_teacher'];
        $new_id_branch = $_POST['new_id_branch'];
        $old_id_branch = $_POST['old_id_branch'];
        $end_date = '2031-04-04';
        $loger = $_COOKIE['name'];
        //Обновление старого графика
        if ($_POST['change_schedule'] == 'yes') {
            $res_up_old_schedule = db_connect("UPDATE schedule SET to_date='$prev_date', id_branch='$old_id_branch' WHERE pupil_id='$pupil_id' AND code_change='1' AND to_date='2031-04-04'");
            if (!$res_up_old_schedule) die(json_encode(array('fail' => 'fail', 'message' => 'Ошибка обновления графика')));
            //Добавление нового графика
            if (isset($_POST['weekday_lesson1']) && isset($_POST['shift_lesson1'])) {
                $day_less1 = $_POST['weekday_lesson1'];
                $shift_less1 = $_POST['shift_lesson1'];
                $res_less1 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`, `id_teacher`, `code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$end_date', '$day_less1', '$shift_less1', '$new_id_branch','$id_teacher','1', '$loger', '')");
                if (!$res_less1) die(json_encode(array("fail" => "fail", "messege" => "3")));
            }
            if (isset($_POST['weekday_lesson2']) && isset($_POST['shift_lesson2'])) {
                $day_less2 = $_POST['weekday_lesson2'];
                $shift_less2 = $_POST['shift_lesson2'];
                $res_less2 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`, `id_teacher`,`code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$end_date', '$day_less2', '$shift_less2', '$new_id_branch','$id_teacher','1', '$loger', '')");
                if (!$res_less2) die(json_encode(array("fail" => "fail", "messege" => "4")));
            }
            if (isset($_POST['weekday_lesson3']) && isset($_POST['shift_lesson3'])) {
                $day_less3 = $_POST['weekday_lesson3'];
                $shift_less3 = $_POST['shift_lesson3'];
                $res_less3 = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`, `id_teacher`,`code_change`, `loger`, `comment`)
		VALUES ('$mark_date','$pupil_id','$from_date','$end_date', '$day_less3', '$shift_less3', '$new_id_branch','$id_teacher','1', '$loger', '')");
                if (!$res_less3) die(json_encode(array("fail" => "fail", "messege" => "5")));
            }
        }
        exit;
    }
    if ($_POST['edit_mode'] == 'edit_data_pupil') {
        $pupil_id = $_POST['pupil_id'];
        $phone = $_POST['phone'];
        $age = $_POST['birthday'];
        $FIO = $_POST['FIO'];
        $FIO_parent = $_POST['FIO_parent'];
        $school = $_POST['school'];
        $school_prog = $_POST['school_prog'];
        $new_id_branch = $_POST['new_id_branch'];
        $old_id_branch = $_POST['old_id_branch'];
        //Обновление данных
        $res = db_connect("UPDATE pupil SET FIO='$FIO' WHERE id='$pupil_id'");
        exit;
    }
    if ($_POST['edit_mode'] == 'one_time_group_change') {
        if ($_POST['one_group_change'] == 'yes') {
            //Данные для снятия с графика
            $from_date = $_POST['from_date'];
            $id_shift = $_POST['shift_from_group_change'];
            $id_day = $_POST['from_id_day'];
            $new_id_teacher = $_POST['from_teacher_id'];
            $id_teacher = $_POST['id_teacher_group_change'];
            $code_change = 2;
            $new_id_branch = $_POST['new_id_branch'];
            $old_id_branch = $_POST['old_id_branch'];
            $loger = $_COOKIE['name'];//Взять cookies
            //Данные на новую дату
            $new_from_date = $new_to_date = $_POST['to_date'];
            //Выбор данных по ученикам
            $res_group_pupil = db_connect("SELECT * FROM `schedule` WHERE id_day='$id_day' AND id_shift='$id_shift' 
                AND (from_date<='$from_date' AND to_date>='$from_date') AND id_branch='$old_id_branch'");
            if ($res_group_pupil != '' and mysqli_num_rows($res_group_pupil) > 0) {
                while ($row_gp = mysqli_fetch_assoc($res_group_pupil)) {
                    $res_check = db_connect("SELECT * FROM schedule WHERE from_date='$from_date' AND to_date='$from_date' 
                AND pupil_id='{$row_gp['pupil_id']}' AND code_change='3' AND id_branch='$old_id_branch'");
                    if ($res_check != '' and mysqli_num_rows($res_check) > 0) {
                        $row_check = mysqli_fetch_assoc($res_check);
                        $new_shift = $_POST['shift_to_group_change'];
                        $new_code_change = 3;
                        $res_get_id_day = db_connect("SELECT * FROM calendar WHERE date='$new_from_date'");
                        $row_gid = mysqli_fetch_assoc($res_get_id_day);
                        $res_check_update = db_connect("UPDATE schedule SET from_date='$new_from_date', to_date='$new_from_date', id_shift='$new_shift', id_day='" . $row_gid['weekday'] . "', id_teacher='$id_teacher' WHERE id='" . $row_check['id'] . "'");
                    } else {
                        $res_remove_date = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`,`id_teacher`, `code_change`, `loger`, `comment`)
		VALUES ('$mark_date','{$row_gp['pupil_id']}','$from_date','$from_date', '$id_day', '$id_shift', '$old_id_branch','$new_id_teacher', '$code_change', '$loger', '')");
                        if (!$res_remove_date) die(json_encode(array('fail' => 'fail', 'message' => 'Ошибка снятия с даты')));
                        $new_shift = $_POST['shift_to_group_change'];
                        $new_code_change = 3;
                        $res_get_id_day = db_connect("SELECT * FROM calendar WHERE date='$new_from_date'");

                        $row_gid = mysqli_fetch_assoc($res_get_id_day);
                        $res_new_date = db_connect("INSERT INTO `schedule`(`date`, `pupil_id`, `from_date`, `to_date`, `id_day`, `id_shift`, `id_branch`, `id_teacher`, `code_change`, `loger`, `comment`)
		VALUES ('$mark_date','{$row_gp['pupil_id']}','$new_from_date','$new_to_date', '" . $row_gid['weekday'] . "', '$new_shift','$new_id_branch', '$id_teacher', '$new_code_change', '$loger', '')");
                        if (!$res_new_date) die(json_encode(array('fail' => 'fail', 'message' => 'Ошибка добавления на дату')));
                    }
                }
            }
            exit;
        }
    }
}
//Удаление подмен (коды 2,3)
if ($_GET['del_change'] == 'del_change') {
    $res = db_connect("DELETE FROM schedule WHERE id='" . $_POST['from_date_id'] . "'");
    $res = db_connect("DELETE FROM schedule WHERE id='" . $_POST['to_date_id'] . "'");
}
//Удаление выплат учителям
if ($_GET['del_payment'] == 'del_payment') {
    $res = db_connect("DELETE FROM payments WHERE id='" . $_POST['item_id'] . "'");
}
//Отметка об оплате
if ($_GET['add_payment'] == 'add_payment') {
	$key = 1;
    $res = db_connect("UPDATE payments SET key_p = '$key' WHERE id='{$_POST['id_payment']}'");
}
if ($_GET['cancel_payment'] == 'cancel_payment') {
	$key = 0;
    $res = db_connect("UPDATE payments SET key_p = '$key' WHERE id='{$_POST['id_payment']}'");
}
//Удаление больничных
if ($_GET['del_sick'] == 'del_sick') {
    $res = db_connect("DELETE FROM sicklist WHERE id='" . $_POST['item_id'] . "'");
}
//Удаление уроков
if ($_GET['del_less'] == 'del_less') {
    $res = db_connect("DELETE FROM schedule WHERE id='" . $_POST['item_id'] . "'");
}
//ДОБАВЛЕНИЕ КАНИКУЛ
if ($_GET['add_holiday'] == 'add_holiday') {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $loger = $_COOKIE['username'];
    $res_add_holiday = db_connect("INSERT INTO holiday (from_date, to_date, loger) VALUES ('$from_date', '$to_date', '$loger')");
    if ($res_add_holiday) echo json_encode(array('success' => 'success'));
}

//Отмена Оплаты
if ($_GET['change'] == 'change') {
    $delete_id = $_POST['change_id'];
    $q_change = "DELETE FROM payment WHERE id='" . $_POST['change_id'] . "'";
    $result = db_connect($q_change);
}

//Добавление возврата по больничному(перерасчет)
if ($_GET['sick'] == 'sick') {
    //Переменные
    $FIO = $_POST['id_pupil'];
    $pupil_id = $_POST['pupil_id'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $month = $_POST['date'];
    $loger = $_COOKIE['name'];
    //Количество занятий
    $q_sum_day = db_connect("SELECT * FROM calendar 
							WHERE date>='" . $from_date . "' AND date<='" . $to_date . "'");
    //перебор дат
    //счетчик дней болезни
    $count_sick = 0;
    $sum_lesson = 0;
    while ($row_calendar = mysqli_fetch_assoc($q_sum_day)) {
        //перебор смен
        $res_shifts = db_connect("SELECT * FROM shifts");
        while ($row_s = mysqli_fetch_assoc($res_shifts)) {
            //Запрашиваем данные по ученикам
            $pupils = getDataAllBranch($row_calendar['date'], $row_s['id']);
            for ($i = 0; $i < count($pupils); $i++) {
                //echo "UPDATE schedule SET from_date='".$row_calendar['date']."', to_date='".$row_calendar['date']."', code_change='5' WHERE pupil_id=$pupil_id";
                if ($pupil_id == $pupils[$i]['pupil_id'] && ($pupils[$i]['code_change'] == 1 || $pupils[$i]['code_change'] == 3 || $pupils[$i]['code_change'] == 5 || $pupils[$i]['code_change'] == 6)) {
                    $count_sick++;
                    $sum_lesson += $pupils[$i]['tax'];
                    if (empty($id_teacher)) $id_teacher = $pupils[$i]['id_teacher'];
                    $miss_check = db_connect("SELECT * FROM schedule WHERE pupil_id='$pupil_id' AND from_date='" . $row_calendar['date'] . "' AND to_date='" . $row_calendar['date'] . "' AND id_day = '" . $row_calendar['weekday'] . "' AND id_shift = '" . $row_s['id'] . "' AND code_change='6'");
                    if (mysqli_num_rows($miss_check) > 0) {
                        db_connect("DELETE FROM schedule WHERE pupil_id='$pupil_id' AND from_date='" . $row_calendar['date'] . "' AND to_date='" . $row_calendar['date'] . "' AND id_day = '" . $row_calendar['weekday'] . "' AND id_shift = '" . $row_s['id'] . "' AND code_change='6'");
                    }
                    $q_check = db_connect("SELECT * FROM schedule WHERE pupil_id='$pupil_id' AND from_date='" . $row_calendar['date'] . "' AND to_date='" . $row_calendar['date'] . "' AND id_day = '" . $row_calendar['weekday'] . "' AND id_shift = '" . $row_s['id'] . "' AND code_change='5'");
                    if (!mysqli_num_rows($q_check) > 0) {
                        $q_up_sick = db_connect("INSERT INTO schedule (date, pupil_id, from_date, to_date, id_day, id_shift, id_branch, id_teacher, code_change, loger) VALUES 
					(NOW(), '$pupil_id', '" . $row_calendar['date'] . "', '" . $row_calendar['date'] . "', '" . $row_calendar['weekday'] . "', '" . $row_s['id'] . "', '" . $pupils[$i]['id_branch'] . "', '" . $pupils[$i]['id_teacher'] . "', '5', '" . $loger . "')");
                    }

                }
            }
        }
    }
    $q_check_data = db_connect("SELECT * FROM sicklist WHERE from_date='$from_date' AND to_date='$to_date' AND pupil_id = '$pupil_id'");
    if (!mysqli_num_rows($q_check_data) > 0) {
        $q_add_sick = db_connect("INSERT INTO sicklist (FIO, pupil_id, date, from_date, to_date, count_lesson, sum_sick, month, id_teacher) 
										VALUES('$FIO', '$pupil_id', NOW(), '$from_date', '$to_date', '$count_sick', '$sum_lesson', '$month', '$id_teacher')");
    }
    //

}

//Изменения данных учителя
if ($_GET['edit_teacher'] == 'edit_teacher') {
    $id_teacher = $_POST['id_teacher'];
    if (empty($_POST['new_password'])) {
        $password = $_POST['password'];
    } else {
        $password = sha1($_POST['new_password']);
    }
    $user_name = $_POST['name_teacher'];
    $tax_teacher = $_POST['tax_teacher'];
    $login = $_POST['user_name'];
    if (!isset($_POST['administrator'])) {
        $user_group = 0;
    } else $user_group = 1;
    db_connect("UPDATE administrators SET username='$login', name='$user_name', password='$password', user_group='$user_group' WHERE id='$id_teacher'");
    db_connect("UPDATE teachers SET name_teacher = '$user_name' WHERE id='$id_teacher'");
    if ($tax_teacher != getLastTax($id_teacher)) {
        db_connect("INSERT INTO tax_change_teachers (teacher_id, from_date, to_date, new_tax_teacher)
					VALUES ('$id_teacher', '" . date('Y-m-d', $mark_time) . "', '2031-04-04', '$tax_teacher')");
    }
}
//Добавления учителя
if ($_GET['add_teacher'] == 'add_teacher') {
    $password = sha1($_POST['add_new_password']);
    $user_name = $_POST['add_name_teacher'];
    $login = $_POST['add_user_name'];
    $tax_teacher = $_POST['add_tax_teacher'];
    if (!isset($_POST['add_administrator'])) {
        $user_group = 0;
    } else $user_group = 1;
    $res1 = db_connect("INSERT INTO administrators (username, name, password, user_group) VALUES
				('$login', '$user_name', '$password', '$user_group')");
    $ins_id = mysqli_insert_id($dbc);
    db_connect("INSERT INTO teachers (name_teacher, tax_teacher) VALUES ('$user_name', '400')");
    db_connect("INSERT INTO tax_change_teachers (teacher_id, from_date, to_date, new_tax_teacher)
					VALUES ('$ins_id', '" . date('Y-m-d', $mark_time) . "', '2031-04-04', '$tax_teacher')");

}
//Удаление учителя
if ($_GET['edit_teacher'] == 'del_teacher') {
    $id_teacher = $_POST['id_teacher'];
    db_connect("DELETE FROM administrators WHERE id='$id_teacher'");
    db_connect("DELETE FROM teachers WHERE id='$id_teacher'");
    db_connect("DELETE FROM tax_change_teachers WHERE teacher_id='$id_teacher'");
}
if ($_GET['mode'] == 'change_graph_date') {
    db_connect("UPDATE schedule SET to_date='{$_POST['to_date']}' WHERE id='{$_POST['id_item']}'");
}
/*Изменение программы и расписания учеников*/
if ($_GET['prog'] == 'change') {
    $branch = $_POST['branch'];
    $day = $_POST['day'];
    $shift = $_POST['shift'];
    $from_date_p = $_POST['from_date'];
    $ch_date = date('Y-m-d', strtotime('-1 day', strtotime($from_date_p))); //Отнять один день
    $id_item = $_POST['id_item'];
    $id_teacher = $_POST['id_teacher'];
    $old_branch = $_POST['old_branch'];
    $old_day = $_POST['old_day'];
    $old_shift = $_POST['old_shift'];
    //Выбор учеников
    $res_pupils = db_connect("SELECT * FROM schedule WHERE id_branch='$old_branch' AND id_day='$old_day' 
                            AND id_shift='$old_shift'
                            AND to_date>='$from_date_p' AND code_change='1'");
    if ($res_pupils != '' and mysqli_num_rows($res_pupils) > 0) {
        while ($row_pupil = mysqli_fetch_assoc($res_pupils)) {
            $query_cut = "UPDATE schedule SET to_date='$ch_date' WHERE id='{$row_pupil['id']}'";
            $query_add = "INSERT INTO schedule (date, pupil_id, from_date, to_date, id_day, id_shift, id_branch, id_teacher, 
                        code_change, loger) 
                        VALUES (NOW(),'{$row_pupil['pupil_id']}', '$from_date_p', '2031-04-04', '$day', '$shift', 
                        '$branch', '$id_teacher', '1', '{$_COOKIE['username']}')";

            db_connect($query_cut);
            db_connect($query_add);
        }
        $query_update_graph = "UPDATE graph SET branch='$branch', day='$day', shift='$shift'
                              WHERE id='$id_item'";
        db_connect($query_update_graph);
    }

}
/*Обработка данных формы добавления урока*/
if ($_POST['add_one_lesson'] == 'add_one_lesson') {
    $pupil_id = $_POST['pupil_id'];
    $from_date = $_POST['from_date'];
    $to_date = '2031-04-04';
    $id_day = $_POST['day'];
    $id_shift = $_POST['shift'];
    $id_branch = $_POST['branch'];
    $programm = $_POST['programm'];
    $res = db_connect("SELECT teacher FROM graph WHERE day='$id_day' AND shift='$id_shift' AND branch='$id_branch' AND programm='$programm'");
    $row = mysqli_fetch_assoc($res);
    $id_teacher = $row['teacher'];
    $code_change = 1;
    db_connect("INSERT INTO schedule (date, pupil_id, from_date, to_date, id_day, id_shift, id_branch, id_teacher, code_change, loger) 
VALUES(NOW(),'$pupil_id', '$from_date', '$to_date', '$id_day', '$id_shift', '$id_branch', '$id_teacher', '$code_change', '" . $_COOKIE['name'] . "')");
    //header("'Location: pupils.php?pupil_id='.$pupil_id.'");
}
//Добавление программы в расписание
if ($_POST['add_data_form'] == 'add_data_form') {
    $programm = $_POST['id_programm_prog'];
    //$res_tarif = db_connect("SELECT tarif_teacher FROM programms WHERE id='{$programm}'");
    //$row_tarif = mysqli_fetch_assoc($res_tarif);
    db_connect("INSERT INTO graph (branch, day, shift, programm, teacher) 
              VALUES ('{$_POST['branch']}', '{$_POST['day']}', '{$_POST['shift']}', '{$programm}', '{$_POST['id_teacher']}')");
}


//Добавление программы в БД
if ($_GET['add_prog'] == 'add_prog') {
    $name_prog = $_POST['name_prog'];
    $tarif_pupil = $_POST['tarif_pupil'];
    $tarif_teacher = $_POST['tarif_teacher'];
    $bg_color = $_POST['bg_color'];
    $color = $_POST['colord'];
    db_connect("INSERT INTO programms (name, bg_color, color, tarif_pupil, tarif_teacher) VALUES('$name_prog','$bg_color','$color','$tarif_pupil','$tarif_teacher')");
}
//Добавление комментария к оплате
if ($_GET['comment'] == 'comment') {
    $pupil_id = $_POST['pupil_id'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $comment = $_POST['comment'];
    $res_check = db_connect("SELECT * FROM comments WHERE pupil_id='$pupil_id' AND from_date='$from_date' AND to_date='$to_date'");
	if(mysqli_num_rows($res_check)>0){
		$res_update = db_connect("UPDATE comments SET comment='$comment' WHERE pupil_id='".$pupil_id."'");
	}else{
		$res_insert = db_connect("INSERT INTO comments (pupil_id, from_date, to_date, comment) 
											VALUES ('$pupil_id', '$from_date', '$to_date', '$comment')");
	}
}
















<?php
//Константы для подключения к БД
define('HOST', 'localhost');
define('NAME', 'u0524_kim');
define('PASS', '212521');
define('BASE', 'u0524993_admin');
//Подключение к БД
$dbc = mysqli_connect(HOST, NAME, PASS, BASE) or die('
		Ошибка подключения к БД');
$dbc->query("SET NAMES 'utf8'");
//Функция обмена данными с БД
function db_connect($q){
	global $dbc;
    mysqli_set_charset($dbc,"utf8");

	return mysqli_query($dbc, $q);
	}
	$mark_time = time();
$mark_date= date('Y-m-d H:i:s', $mark_time);
	//Кнопка выхода
if($_GET['logout']=='logout'){
	setcookie('username', $row['username'], time()-3600);
	setcookie('session_id', session_id(), time()-3600);
	header('Location: login.php');
}
//Функция форматирования даты
function formateDate($date){
	$day = substr($date,-2);//День
	$month = substr($date, 5,2);//Месяц
	$year = substr($date, 0,4);//Год
	$new_date = $day.'.'.$month.'.'.$year;
	return $new_date;
}
$min_tax = 350;
?>
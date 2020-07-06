<?php
//require_once('scripts/settings.php');
//Проверка авторизации пользователя
/*Пользователь авторизован пока существует куки session_id и username
Если в процессе нахождения на странице куки закончили действие (1 час)
Включаем проверку автоматической куки  [PHPSESSID] т.к.
она действует до закрытия браузера и постоянно обновляется.
При проверке  [PHPSESSID] отправляем запрос в БД и проверяем если такая
куку в БД. Если она имеется, значит кончилось время основных куки, поэтому
Отправляем запрос в БД где запрашиваем username
После чего создаем куки username и куки session_id. Круг замкнулся

Если же  [PHPSESSID] отсутствует в БД или не равна ей, значит пользователь
вышел из сайта либо попытка взлома сайта через сессию!!!.
*/
if(!isset($_COOKIE['session_id'])&&!isset($_COOKIE['username'])){
	if(isset($_COOKIE['PHPSESSID'])){
		$res = db_connect("SELECT * FROM administrators WHERE session_id='".session_id()."'");
		if($res!='' and mysqli_num_rows($res)==1){
			$row = mysqli_fetch_assoc($res);
			setcookie('username', $row['username'], time()+60*60);//Время работы куки 1 час
			setcookie('session_id', session_id(), time()+60*60);
			session_start();
			} else {
				setcookie('username', $row['username'], time()-3600);
				setcookie('session_id', session_id(), time()-3600);
				session_destroy();
				header('Location: login.php');
				}
		} else {
			setcookie('username', $row['username'], time()-3600);
			setcookie('session_id', session_id(), time()-3600);
			session_destroy();
			header('Location: login.php');
			}; 
	} else if (isset($_COOKIE['PHPSESSID'])&&isset($_COOKIE['username'])&&isset($_COOKIE['session_id'])){
		session_start();
		header('Location: index.php');
		}
?>
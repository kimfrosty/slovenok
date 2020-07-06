<?php
require('scripts/dbconnect.php');
$_SESSION= array();
//Переадресации
if ($_COOKIE['PHPSESSID']&&$_COOKIE['username']&&$_COOKIE['session_id']){
	session_start();
	header('Location: index.php');
	exit();
	}
	function fail($message, $selector_err){
	die(json_encode(array('status'=>'fail', 'message'=>$message, 'selector_err'=>$selector_err)));
	};
function success($message, $selector_err){
	die(json_encode(array('status'=>'success', 'message'=>$message, 'selector_err'=>$selector_err)));
	};
/*Авторизация пользователя*/
//Проверка отправки формы
if($_POST['admin_connect']=='yes'){
	$err_msg = 'false'; //Переменная сообщения об ошибке
	//Получаем данные и кодируем их
	$username = htmlspecialchars(mysqli_escape_string($dbc, trim($_POST['username'])));//Имя
	$password = htmlspecialchars(mysqli_escape_string($dbc, trim($_POST['password'])));//Пароль
	//Проверяем наличие данного пользователя в БД
	$res = db_connect("SELECT * FROM administrators WHERE username='$username' AND password=SHA('$password')");
	if($res!='' and mysqli_num_rows($res)==1){
		//Пользователь найден
		//Старт сессии входа
		session_start();
		$_SESSION['id_branch']=1;
		/*Автоматически создается уникальный идентификатор пользователя
		вида [PHPSESSID] => ho2ujtm59jck5ldt1ubrnr7g56 и сохраняется в COOKIE
		Он будет являться главным ИД (не путать с user_id) пользователя
		Обновляем сессию в БД*/
		//$res_sess = db_connect("UPDATE administrators SET session_id='".session_id()."'");
		//Обновляем время последнего визита
		//$res_sess = db_connect("UPDATE administrators SET last_visit=NOW()");
		
		/***Получаем данные из запроса для дальнейших операций***/
		$row = mysqli_fetch_assoc($res);
		setcookie('username', $row['username'], time()+60*60);
		setcookie('name', $row['name'], time()+60*60);
		setcookie('id_teacher', $row['id'], time()+60*60);
		setcookie('session_id', session_id(), time()+60*60);
		setcookie('user_group', $row['user_group'], time()+60*60);
		setcookie('id_branch', $_SESSION['id_branch'], time()+60*60);
		//header('Location: index.php');
		//Добавляем данные в лог
		//$res_log = db_connect("INSERT INTO log_history (username, ip, caption, date) VALUES ('".$row['username']."', '".$_SERVER['REMOTE_ADDR']."', 'Выполнен вход в панель', '".(time()+3600)."')");
		success('Спасибо что вошли, '.$row['name'].'...','');
		exit();
		} else {
			fail('Неверный пароль или логин','');
			}
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Авторизация</title>
<script src="scripts/jquery-1.8.3.min.js"></script>
<script src="scripts/jquery-ui.min.js"></script>
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="css/jquery-ui.theme.min.css" rel="stylesheet" type="text/css">
<link href="css/styles-site.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="css/style1.css" rel="stylesheet" type="text/css">
</head>
<body style="background-image:url(../images/fon_1.png);">
<div class="main-container">
<div class="logo">
	<img src="images/1234.svg" width="500">
</div><br>
<div class="login">
		<form name="form-authorization" id="form-authorization" method="post" action="#" class="form-authorization">
        <label for="username">Логин</label>
        <input type="text" class="textinput" name="username" id="username"><br>
        <label for="password">Пароль</label>
        <input type="password" class="textinput" name="password" id="password"><br>
        <input type="submit" name="senddata" value="Войти" class="but-send" id="but-send">
        <input type="hidden" name="admin_connect" value="yes">
    	</form>
</div>
	
</div>
<script src="scripts/snow-fall.js"></script>
</body>
<script type="text/javascript">
$(document).ready(function(){
//Стилизация input
$('.textinput').button({
	icons:{primary:"ui-icon-gear"},
	width:'150'
	});
$('.but-send').button();
//Валидация
//Очистка полей при получении фокуса
$('.textinput').focus(function() {
	$(this).val('');
    $(this).next('.error').empty();
});
//Отправка формы на сервер и проверка ее по клику
//Запрет стандартной отправки
$('#but-send').click(function(e){
	$('#form-authorization').submit(function() {
        return false
    });
//Проверка полей на пустоту и добавление ошибок
data_err = false;
$('.textinput').each(function(index, element) {
   if($(element).val()==''){
    	if($(element).attr('name')=='username'){
			$(element).after('<label id="username-error" class="error" for="username">Введите логин</label>');
			data_err = true;
			}
		if($(element).attr('name')=='password'){
			$(element).after('<label id="password-error" class="error" for="password">Введите пароль</label>');
			data_err = true;
			}
		}
    });
	//Если ошибок нет отправляем форму для
	//проверки логина и пароля
	if(!data_err){
	var dataform = $('#form-authorization').serializeArray();
	$.post('login.php', dataform, function(json){
		if(json.status=='fail'){//Логин не верный
		$('#password').after('');
			$('#password').after('<label id="password-error" class="error" for="password" style="margin-left:140px; width:200px;">Неверные логин или пароль</label>')
			} else {//Логин верный
			$('#password').after('');
				$('#password').after('<label id="password-error" class="error" for="password" style="margin-left:140px; width:200px;">'+json.message+'</label>');
				setTimeout('location="index.php"', 2000);
				}
			}, "json")
		}
	});
	
})
</script>
</html>
<?php
if($_COOKIE['user_group']==1){
?>
<nav id="main-nav" role="navigation">
	<ul id="main-menu" class="sm sm-blue">
		<li><a href="/schedule.php" >Расписание</a></li>
        <li><a href="/index.php" >График</a></li>
		<li><a href="/table_ved.php" >Ведомость</a></li>
		<li><a href="#">Статистика</a>
        	<ul>
				<li><a href="/test_lessons.php">Пробные уроки</a></li>
            	<li><a href="/attendance.php">Посещаемость</a></li>
            	<li><a href="/holidays.php">Каникулы</a></li>
            	<li><a href="/payments.php">Оплата Педагогам</a></li>
                <li><a href="/sicklist.php">Больничные</a></li>
                <li><a href="/tickets.php">Электронные талоны</a></li>
                <li><a href="/statistic.php">Аналитика</a></li>
            </ul>
        </li>
        <li><a href="/events.php" >Журнал событий</a></li>
		<li><a href="/options.php">Настройки</a></li>
        <li><input type="search" id="all_search" name="FIO" placeholder="поиск ученика"></li>
        <li><a id="ShowTime"></a></li>
        <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>?logout=logout" id="logout"><?=$_COOKIE['name']?> | Выход</a></li>
	</ul>
</nav>
<?php
}else{
?>
<nav id="main-nav" role="navigation">
	<ul id="main-menu" class="sm sm-blue">
		<li><a href="/schedule.php" >Расписание</a></li>
        <li><a href="/index.php" >График</a></li>
		<li><a href="/pupils.php">Список учеников</a></li>
        <li><a href="#">Статистика</a>
        	<ul>
            	<li><a href="/attendance.php">Посещаемость</a></li>
            	<li><a href="/holidays.php">Каникулы</a></li>
            	<li><a href="/payments.php">Оплата Педагогам</a></li>
            </ul>
        </li>
        <li><a id="ShowTime"></a></li>
        <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>?logout=logout" id="logout1"><?=$_COOKIE['name']?> | Выход</a></li>
	</ul>
</nav>
<?php
}
?>
<script>
    $(document).ready(function () {
        $('#main-menu input').button();
        //Автокомплит ученика
        $('#all_search').autocomplete({
            source: 'scripts/get_data.php',
            select: redirectPupil
        });//Конец


        function redirectPupil(event, ui){
            $('#add_data_pupil input:hidden, #add_one_less input:hidden').remove();
            $.getJSON('scripts/get_data.php?c='+ui.item.value, function(json){
                location.replace('pupils.php?pupil_id='+json.id);
            })
        }
    })
</script>

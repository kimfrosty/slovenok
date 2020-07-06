<?php
include ("scripts/dbconnect.php");
include ("function.php");
$month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
$week = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Расписание</title>
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="css/sm-core-css.css" rel="stylesheet" type="text/css">
<link href="css/sm-blue.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="scripts/jquery-1.8.3.min.js"></script>
<script src="scripts/jquery-ui.min.js"></script>
<script src="scripts/jquery.smartmenus.min.js"></script>
<script src="scripts/showtime.js"></script>
</head>
<body onload="if(window.showTime) showTime();">
<div>
<?php include('scripts/navmenu.php');?>
</div>

<?php 
	$res_branches = db_connect("SELECT * FROM branches");
	while($row_b = mysqli_fetch_assoc($res_branches)){
		echo '<div class="container">
				<button style="background-color:#3D6C80; color:white;">'.$row_b['name_branch'].'</button>
				</div>';
		
		echo '<div class="container">';
		echo '<div id="container" class="table">';
		for($i=0; $i<count($week); $i++){
			echo '<table>
					<tr class="day_week"><th  colspan="2" class="date" style="width:150px;">'.$week[$i].'</th></tr>
					<tr class="legend"><td>Время</td><td style="width:150px;">Занятие</td></tr>';
			$res_shifts = db_connect("SELECT * FROM shifts");
			while($row_s = mysqli_fetch_assoc($res_shifts)){
				$res_graph = db_connect("SELECT graph.*, programms.name, programms.bg_color, programms.color, 
                                        teachers.name_teacher 
										FROM graph 
										INNER JOIN programms ON graph.programm=programms.id 
										INNER JOIN teachers ON graph.teacher=teachers.id 
										WHERE branch='{$row_b['id']}' 
										AND day='".($i+1)."' 
										AND shift='{$row_s['id']}'");
				$row_g = mysqli_fetch_assoc($res_graph);
				if(!empty($row_g)){
					$begin = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date("Y"))); 
					$end = date('Y-m-d', mktime(0, 0, 0, date('m'), date("t"), date("Y")));
					$res_pupil = db_connect("SELECT COUNT(*) FROM schedule WHERE id_day='{$row_g['day']}' AND id_shift='{$row_g['shift']}' 
													AND id_branch='{$row_g['branch']}' AND code_change='1' AND to_date='2031-04-04'");
					$res_pup = db_connect("SELECT schedule.pupil_id, pupil.FIO
													FROM schedule 
													INNER JOIN pupil ON schedule.pupil_id=pupil.id
													WHERE id_day='{$row_g['day']}' AND id_shift='{$row_g['shift']}' 
													AND id_branch='{$row_g['branch']}' AND code_change='1' AND to_date='2031-04-04'");
					$count = mysqli_fetch_assoc($res_pupil);
					$tooltip = '';
					$k = 1;
					while($row_pup = mysqli_fetch_assoc($res_pup)){
						$tooltip .= '<span><b>'.$k.' - '.$row_pup['FIO'].'</b></span><br>';
						$k++;
					}
					/*echo '<pre>';
					print_r($row_pup);
					echo '</pre>';*/
					if ($count['COUNT(*)'] <= 2){
						$info = 'blink';
					} else $info = '';
					echo '<tr class="legend_all"><td class="'.$info.'">'.$row_s['shifts'].'</td>';
					echo '<td class="yes_program" id_item="'.$row_g['id'].'"
					branch = "'.$row_g['branch'].'"
					day = "'.$row_g['day'].'"
					shift = "'.$row_g['shift'].'"
					programm = "'.$row_g['programm'].'"
					id_teacher = "'.$row_g['teacher'].'" 
					style="background-color:'.$row_g['bg_color'].'; 
					color:'.$row_g['color'].'" data="'.$tooltip.'">
					<span>'.$row_g['name'].'</span>
					<br>'.substr(strstr($row_g['name_teacher'], ' '), 1, strlen($row_g['name_teacher'])).' - <strong>'.$count['COUNT(*)'].'</strong> уч.</td>
					</tr>';
					} else {
						echo '<tr class="legend_all"><td>'.$row_s['shifts'].'</td>';
						echo '<td class="no_programm" day="'.($i+1).'" shift="'.$row_s['id'].'" branch="'.$row_b['id'].'"><span>&nbsp;</span><br>&nbsp;</td></tr>';
						}
				}
			}
		echo '</table></div></div>';
		}
if($_COOKIE['user_group']==1){ ?>
<div id="tabs" title="Изменить расписание">
<ul>
    <li><a href="#tabs-1">Перенос Расписания</a></li>
    <li><a href="#tabs-2">Добавление ученика</a></li>
</ul>
<div id="tabs-1">
    <div id="change_programm">
    <form id="check_programm">
            <label for="from_date">Начиная с:</label>
            <input type="date" name="from_date" required id="from_date"><br>
            <label for="branch">Филиал:</label>
            <select name="branch" id="branch" class="branch"><option>Выберите филиал</option>
            <?php
            $res = db_connect("SELECT * FROM branches");
            while($row = mysqli_fetch_assoc($res)){
                echo '<option value="'.$row['id'].'">'.$row['name_branch'].'</option>';
            }
            ?>
            </select>
        <div id="jq_data"></div>
    </form>
</div>
</div>
<div id="tabs-2">
    <div id="add_pupil_block">
        <form id="add_pupil">
            <input type="hidden" name="add_one_pupil" value="add_one_pupil">
            <label for="from_date">С даты:</label>
            <input type="date" name="from_date" required id="from_date"><br>
            <label for="id_search">Фамилия имя:</label>
            <input type="search" name="FIO" id="id_search" placeholder="ФИО" required>
            <div id="jq_data_add_pupil"></div>
            <input type="submit" name="add_pupil" id="but_add_pupil" value="Добавить">
        </form>
    </div>
</div>
</div>
<div id="dialog_add_programm" title="Добавить программу"></div>
<?php } ?>
<script type="text/javascript">
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
        $('#tabs').tabs();
		//Подсветка ячеек таблицы
		$('.legend_all').hover(function(){
			$(this).css({'cursor':'pointer'});
		});
		
		///Подсказка tooltip////
		$('.yes_program').tooltip({
			items:"[data]",
			content:function(){
				return $(this).attr('data');
				},
			position: {
					  my: "left bottom" ,
					  at: "center+70 top+20" ,
					  collision: "none"
				   }
		});
		
		$('#tabs input, button, select').button();
		//Добавление программы
        //Диалоговое окно
        $('#dialog_add_programm').dialog({
            //Автооткрытие. НЕ ЗАБЫТЬ
            autoOpen: false,
            modal: true,
            close: function () {
                location.reload()
            }
        });

    //Перенос программы
    $('.yes_program').click(function (e) {
        e.preventDefault();

        $('#jq_data,#jq_data_add_pupil').html('');
        $('#jq_data').append('<input type="hidden" name="id_item" value="'+$(this).attr('id_item')+'">');
        $('#jq_data, #jq_data_add_pupil').append('<input type="hidden" name="id_teacher" value="'+$(this).attr('id_teacher')+'">');
        $('#jq_data, #jq_data_add_pupil').append('<input type="hidden" name="old_branch" value="'+$(this).attr('branch')+'">');
        $('#jq_data, #jq_data_add_pupil').append('<input type="hidden" name="old_day" value="'+$(this).attr('day')+'">');
        $('#jq_data, #jq_data_add_pupil').append('<input type="hidden" name="old_shift" value="'+$(this).attr('shift')+'">');
        //Подсветка select branch
        $('#branch').css('background-color', 'Salmon');
        $('#branch').change(function () {
            if($(this).val()>0){$(this).css('background-color', 'LimeGreen')
            }else{
                $(this).css('background-color', 'Salmon');
                $('#day').remove();
            }
            $('.day, .shift').remove();
            //Выбор свободных дней на текущий branch
            let changeProg = {};
            changeProg.p='getday';
            changeProg.branch = $(this).val();
            $.getJSON('scripts/get_data.php', changeProg, function (json) {
                $('#jq_data').append('<label for="day">День:</label><select name="day" class="day ui-button ui-corner-all ui-widget" id="day">' +
                    '<option>Выберите день</option>');
                let days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
                $.each(json, function () {
                    $('#day').append('<option value="'+this+'">'+days[this-1]+'</option>');
                });
                $('#day').append('</select><br>');//Дни выводятся
                //Подсветка дней
                $('.day').css('background-color', 'Salmon');
                $('#day').change(function () {
                    if($(this).val()>0){$(this).css('background-color', 'LimeGreen')
                    }else{
                        $(this).css('background-color', 'Salmon')
                    }
                    $('#shift').remove();
                    //Запрос свободных смен
                    changeProg.p='getshifts';
                    changeProg.day=$(this).val();
                    $.getJSON('scripts/get_data.php', changeProg, function(json){
                        $('#day').after('<br><label for="shift">Смена:</label><select name="shift" class="shift ui-button ui-corner-all ui-widget" id="shift">' +
                            '<option>Выберите смену</option>');
                        $.each(json, function () {
                            $('#shift').append('<option value="'+this.id+'">'+this.shift+'</option>');
                        });
                        $('#shift').append('</select>');
                        $('.shift').css('background-color', 'Salmon');
                        $('#shift').change(function () {
                            if($(this).val()>0){$(this).css('background-color', 'LimeGreen');
                                $('#check_programm').append('<input id="send_form" type="submit" name="send_form" value="Перенести" class="ui-button ui-corner-all ui-widget">');
                                $('#send_form').click(function (e) {
                                    e.preventDefault();
                                    let data_form_prog = $('#check_programm').serializeArray();
                                    $.post('scripts/add_data.php?prog=change', data_form_prog, function(json){},"json");
                                    location.reload();
                                });

                            }else{
                                $(this).css('background-color', 'Salmon')
                            }
                        })
                    })
                })
            })//конец запроса на возврат свободных дней
        });

        $('#tabs').dialog('open');

    });
        $('#tabs').dialog({
            autoOpen: false,
            modal: true,
            width: 500,
			height: 430,
            close: function () {
               location.reload()
            }
        });
        //Кнопка добавления ученика
        $('#but_add_pupil').click(function (evt) {
            evt.preventDefault();
            var data_pupil = $('#add_pupil').serializeArray();
            $.post('scripts/add_data.php', data_pupil, function (json) {}, "json");
            location.reload();
            
        })
        //Автокомплит ученика
        $('#id_search').autocomplete({
            source: 'scripts/get_data.php',
            select:showpupil
        });
        function showpupil(event, ui){
            $('#add_data_pupil input:hidden, #add_one_less input:hidden').remove();
            $.getJSON('scripts/get_data.php?c='+ui.item.value, function(json){
                $('#jq_data_add_pupil').append('<input type="hidden" name="pupil_id" value="'+json.id+'">');
            })
        }
     //   Добавление программы
        $('.no_programm').click(function (evt) {
            evt.preventDefault();
            $('#dialog_add_programm').append('<form id="add_programm">');
            $('#add_programm').append('<input type="hidden" name="add_data_form" value="add_data_form">');
            $('#add_programm').append('<input type="hidden" name="branch" value="'+$(this).attr('branch')+'">');
            $('#add_programm').append('<input type="hidden" name="day" value="'+$(this).attr('day')+'">');
            $('#add_programm').append('<input type="hidden" name="shift" value="'+$(this).attr('shift')+'">');

            $('#add_programm').append('<label for="shift">Программа:</label><select name="id_programm_prog" id="id_programm_prog" class="id_programm_prog ui-button ui-corner-all ui-widget"><option>Выберите Программу</option> ')
            $.getJSON('scripts/get_data.php?p=get_programm', function(json){
                $.each(json, function () {
                    $('#id_programm_prog').append('<option value="'+this.id+'">'+this.name+'</option>');
                });
                $('#id_programm_prog').append('</select><br>');
            });
            $('.id_programm_prog').css('background-color', 'Salmon');
                $('#id_programm_prog').change(function () {
                    if($(this).val()>0){$(this).css('background-color', 'LimeGreen');
                        $('#id_teacher_prog, #send_form').remove();
                        $('#id_programm_prog').after('<label for="shift">Учитель:</label><select name="id_teacher" id="id_teacher_prog" class="id_teacher_prog ui-button ui-corner-all ui-widget"><option>Выберите учителя</option> ')
                        $.getJSON('scripts/get_data.php?p=get_teacher', function(json){
                            $.each(json, function () {
                                $('#id_teacher_prog').append('<option value="'+this.id+'" >'+this.name_teacher+'</option>');
                            });
                            $('#id_teacher_prog').append('</select><br>');
                            $('.id_teacher_prog').css('background-color', 'Salmon');
                            $('#id_teacher_prog').change(function () {
                                if($(this).val()>0){
                                $(this).css('background-color', 'LimeGreen');
                                $('#send_form').remove();
                                $('#id_teacher_prog').after('<input type="submit" name="send_form" value="Добавить" id="send_form" class="ui-button ui-corner-all ui-widget">');
								$('#send_form').click(function (e) {
    								e.preventDefault();
    								var data_programm =$('#add_programm').serializeArray();
    								$.post('scripts/add_data.php', data_programm, function(json){},"json");
									location.reload();
								})
                                }else{
                                    $(this).css('background-color', 'Salmon');
                                    $('#send_form').remove();
                                }
                            })
                        })
                    }else{
                        $(this).css('background-color', 'Salmon');
                        $('#id_teacher_prog, #send_from').remove();
                    }
                });

            $('#add_programm').append('</form>');
            $('#dialog_add_programm').dialog('open');
			$('#dialog_add_programm').dialog({
            	autoOpen: false,
            	modal: true,
            	width: 500,
				height: 220,
            	close: function () {
               		location.reload();
            	}
        	});
		})
	});

</script>

</body>
</html>


<!--SELECT graph.id, schedule.*,programms.id
FROM graph
INNER JOIN schedule ON graph.day=schedule.id_day AND graph.shift=schedule.id_shift AND graph.branch = schedule.id_branch
INNER JOIN programms ON graph.programm=programms.id
WHERE programms.id='2'
AND schedule.id_day='3' AND schedule.id_shift='2'
AND schedule.id_branch='2' AND schedule.id_teacher='6' AND schedule.code_change='1' AND schedule.to_date>='2018-10-28'-->
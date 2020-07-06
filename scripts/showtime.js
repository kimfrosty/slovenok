function showTime ()
{
   if(document.getElementById)
   {
      nowtime = new Date()

      if(parseInt(nowtime.getHours()) > 9) { var nowtimeHours = nowtime.getHours()} else { var nowtimeHours = '0' + nowtime.getHours() }
      if(parseInt(nowtime.getMinutes()) > 9) { var nowtimeMinutes = nowtime.getMinutes()} else { var nowtimeMinutes = '0' + nowtime.getMinutes() }
      if(parseInt(nowtime.getSeconds()) > 9) { var nowtimeSeconds = nowtime.getSeconds()} else { var nowtimeSeconds = '0' + nowtime.getSeconds() }

      var TimeString = nowtimeHours + ':' + nowtimeMinutes + ':' + nowtimeSeconds

      if(document.getElementById('ShowTime'))
      {
         document.getElementById('ShowTime').innerHTML = TimeString;
         setTimeout("showTime()", 1000);
      }
   }
}
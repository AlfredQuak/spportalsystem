<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>spPortalSystem - Installer</title>

  
</head>
<body  bgcolor="Silver">
<table style="text-align: left; width: 100%; height: 70px;" background="../module/admin/template/images/logo_bg.jpg" border="0" cellspacing="0">
    <tr>
      <td style="vertical-align: top;">spPortalSystem Installer v0.1 </td>
    </tr>
</table>

<br>
<br>

<center>
<form action="http://<?php echo $_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],"step1.php")). 'step2.php'; ?>" method="get">
  <table border="0" width="80%">
    <tbody>
      <tr>
        <td><?php echo substr(getcwd(),0,strpos(getcwd(),"/install"));
                ?>
        <br>
        </td>
        <td><?php if(substr(decoct( fileperms(substr(getcwd(),0,strpos(getcwd(),"/install"))) ), 2) == "777"){
                        echo '<font color="green">Permission ok</font>';
                    }else{
                        echo '<font color="red">Please set path Permission on '.substr(getcwd(),0,strpos(getcwd(),"/install")).' to 777</font>';
                    }
                ?>
        <br>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: top;">Database Settings</td>
        <td>
        <table width="100%">
          <tbody>
            <tr>
              <td width="300px"> Server Url </td>
              <td> <input name="SP_CORE_DB_SERVER" size="60" value="localhost" type="text"> </td>
            </tr>
            <tr>
              <td> DB User </td>
              <td> <input name="SP_CORE_DB_USER" size="60" value="root" type="text"> </td>
            </tr>
            <tr>
              <td> DB User Password </td>
              <td> <input name="SP_CORE_DB_PASS" size="60" value="pass" type="text"> </td>
            </tr>
            <tr>
              <td> DB Database </td>
              <td> <input name="SP_CORE_DB_DATABASE" size="60" value="testdb_install" type="text"> </td>
            </tr>
          </tbody>
        </table>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: top;">System Settings</td>
        <td>
        <table width="100%">
          <tbody>
            <tr>
              <td width="300px">Only www </td>
              <td><input name="SP_CORE_ONLY_WWW" value="1" type="radio">on
              <input name="SP_CORE_ONLY_WWW" value="0" checked="checked" type="radio">off</td>
            </tr>
            <tr>
              <td>Admincenter over https</td>
              <td><input name="SP_CORE_ADMIN_CENTER_HTTPS" value="1" type="radio">on <input name="SP_CORE_ADMIN_CENTER_HTTPS" value="0" checked="checked" type="radio">off</td>
            </tr>
            <tr>
              <td>IP crunch</td>
              <td><input name="SP_CORE_IP_CRUNCHER" value="1" type="radio">on <input name="SP_CORE_IP_CRUNCHER" value="0" checked="checked" type="radio">off</td>
            </tr>
            <tr>
              <td>XML based Template</td>
              <td><input name="SP_CORE_TEMPLATE_XML" value="1" checked="checked" type="radio">on <input name="SP_CORE_TEMPLATE_XML" value="0" type="radio">off</td>
            </tr>
            <tr>
              <td>Your template path</td>
              <td><input name="SP_CORE_TEMPLATE_PATH" size="60" value="template/blueSteel" type="text"></td>
            </tr>
          </tbody>
        </table>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: top;">Logging</td>
        <td>
        <table width="100%">
          <tbody>
            <tr>
              <td width="300px">Log on the webside</td>
              <td><input name="SP_CORE_LOG_WEB" value="1" type="radio">on
              <input name="SP_CORE_LOG_WEB" value="0" checked="checked" type="radio">off</td>
            </tr>
            <tr>
              <td>Log into file</td>
              <td><input name="SP_CORE_LOG_FILE" value="1" type="radio">on
              <input name="SP_CORE_LOG_FILE" value="0" checked="checked" type="radio">off</td>
            </tr>
            <tr>
              <td>Log into mysql table</td>
              <td><input name="SP_CORE_LOG_MYSQL" value="1" type="radio">on
              <input name="SP_CORE_LOG_MYSQL" value="0" checked="checked" type="radio">off</td>
            </tr>
            <tr>
              <td>Log into webservice</td>
              <td><input name="SP_CORE_LOG_WEBSERVICE" value="1" type="radio">on <input name="SP_CORE_LOG_WEBSERVICE" value="0" checked="checked" type="radio">off</td>
            </tr>
            <tr>
              <td><br>
              </td>
              <td><input size="60" name="SP_CORE_WEBSERVICE_URL" value="http://10.10.144.90/logservice/webservice1.asmx?wsdl" type="text"></td>
            </tr>
            <tr>
              <td>XHPROF Profiler</td>
              <td><input name="SP_CORE_XHPROF" value="1" type="radio">on
              <input name="SP_CORE_XHPROF" value="0" checked="checked" type="radio">off</td>
            </tr>
            <tr>
              <td>XHPROF path</td>
              <td><input size="60" name="SP_CORE_DEBUG_XHPROF_PATH" value="/home/dev/xhprof" type="text"></td>
            </tr>
            <tr>
              <td>Logfile path and name</td>
              <td><input size="60" name="SP_CORE_DEBUG_LOGFILE_PATH" value="/home/dev/mylog.log" type="text"></td>
            </tr>
          </tbody>
        </table>
        </td>
      </tr>
      <tr>
        <td>-</td>
        <td>
        <table width="100%">
          <tbody>
            <tr>
              <td width="300px"><input name="log_level" value="SP_LOG_NOTHING" checked="checked" type="radio"></td>
              <td>SP_LOG_NOTHING</td>
            </tr>
            <tr>
              <td><input name="log_level" value="SP_LOG_NOTICE" type="radio"></td>
              <td>SP_LOG_NOTICE</td>
            </tr>
            <tr>
              <td><input name="log_level" value="SP_LOG_WARNING" type="radio"></td>
              <td>SP_LOG_WARNING</td>
            </tr>
            <tr>
              <td><input name="log_level" value="SP_LOG_ERROR" type="radio"></td>
              <td>SP_LOG_ERROR</td>
            </tr>
            <tr>
              <td><input name="log_level" value="SP_LOG_DEBUG" type="radio"></td>
              <td>SP_LOG_DEBUG</td>
            </tr>
            <tr>
              <td><input name="log_level" value="SP_LOG_OWN" type="radio"></td>
              <td>SP_LOG_OWN</td>
            </tr>
            <tr>
              <td>-</td>
              <td>-</td>
            </tr>
            <tr>
              <td><input name="SP_LOG_SQL" value="1" type="radio">on <input name="SP_LOG_SQL" value="0" checked="checked" type="radio">off</td>
              <td>SP_LOG_SQL</td>
            </tr>
          </tbody>
        </table>
        </td>
      </tr>
      <tr>
        <td><br>
        </td>
        <td><input class="button" value="Next -&gt; Step 2" type="submit"></td>
      </tr>
    </tbody>
  </table>
</form>
</center>

<?php /*
if(substr(decoct( fileperms(substr(getcwd(),0,strpos(getcwd(),"/install"))) ), 2) == "777"){
    echo "Permission ok";
}else{
    echo "Please set path Permission on ".substr(getcwd(),0,strpos(getcwd(),"/install"))." to 777";
}
$file_config = str_replace("//","/",substr(getcwd(),0,strpos(getcwd(),"/install"))."/config.inc.php");
echo "<br>". substr(getcwd(),0,strpos(getcwd(),"/install"));
echo "<br>".substr(decoct( fileperms(substr(getcwd(),0,strpos(getcwd(),"/install"))) ), 2);

/*
$config_file = fopen($file_config,"w+");
if(!$config_file){
    echo "<br>cant create config.inc.php on : ".$file_config;
}else{
    echo "create config.inc.php ok ".$file_config;
}
 */
?>
</body></html>
spPortalSystem Installer v0.0.1 
<br>
<br>
<?php

include substr(getcwd(), 0, strpos(getcwd(), "/install")) . '/includes/CHelper.php';

$writeConfig[] = "date_default_timezone_set('Europe/Berlin');";
$writeConfig[] = "define('SP_LOG_NOTHING',   1);";
$writeConfig[] = "define('SP_LOG_NOTICE',    2);";
$writeConfig[] = "define('SP_LOG_WARNING',   3);";
$writeConfig[] = "define('SP_LOG_ERROR',     4);";
$writeConfig[] = "define('SP_LOG_DEBUG',     5);";
$writeConfig[] = "define('SP_LOG_OWN',       6);";

$data = spCore\CHelper::getRequestVar();

foreach ($data as $index => $value) {
    if ($index == "SP_LOG_SQL") {
        if ($value == 1) {
            $writeConfig[] = "define('SP_LOG_SQL',   true);";
        } else {
            $writeConfig[] = "define('SP_LOG_SQL',   false);";
        }
    }elseif($index == "log_level"){
        $writeConfig[] = "define('SP_CORE_DEBUG',".$value.");";
    }elseif($index == "SP_CORE_LOG_WEB"){
        if($value == 0){
            $writeConfig[] = "define('".$index."',   false);";
        }else{
            $writeConfig[] = "define('".$index."',   true);";
        }
    }elseif($index == "SP_CORE_LOG_FILE"){
        if($value == 0){
            $writeConfig[] = "define('".$index."',   false);";
        }else{
            $writeConfig[] = "define('".$index."',   true);";
        }
    }elseif($index == "SP_CORE_LOG_MYSQL"){
        if($value == 0){
            $writeConfig[] = "define('".$index."',   false);";
        }else{
            $writeConfig[] = "define('".$index."',   true);";
        }
    }elseif($index == "SP_CORE_LOG_WEBSERVICE"){
        if($value == 0){
            $writeConfig[] = "define('".$index."',   false);";
        }else{
            $writeConfig[] = "define('".$index."',   true);";
            $writeConfig[] = "define('SP_CORE_WEBSERVICE_URL','".$data['SP_CORE_WEBSERVICE_URL']."');";
        }
    }elseif($index == "SP_CORE_XHPROF"){
        if($value == 0){
            $writeConfig[] = "define('".$index."',   false);";
        }else{
            $writeConfig[] = "define('".$index."',   true);";
        }
    }elseif($index == "SP_CORE_DB_SERVER"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }elseif($index == "SP_CORE_DB_USER"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }elseif($index == "SP_CORE_DB_PASS"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }elseif($index == "SP_CORE_DB_DATABASE"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }elseif($index == "SP_CORE_ONLY_WWW"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }elseif($index == "SP_CORE_ADMIN_CENTER_HTTPS"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }elseif($index == "SP_CORE_IP_CRUNCHER"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }elseif($index == "SP_CORE_TEMPLATE_XML"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }elseif($index == "SP_CORE_TEMPLATE_PATH"){
            $writeConfig[] = "define('".$index."','".str_replace("//","/",$data[$index]."/")."');";
    }elseif($index == "SP_CORE_DEBUG_LOGFILE_PATH"){
            $writeConfig[] = "define('".$index."','".$data[$index]."');";
    }
}
//$writeConfig[] = "define('SP_CORE_DEBUG_XHPROF_PATH','".(isset($data['SP_CORE_DEBUG_XHPROF_PATH'])?$data['SP_CORE_DEBUG_XHPROF_PATH']:"")."');";
//$writeConfig[] = "define('SP_CORE_DEBUG_XHPROF_PATH','".(isset($data['SP_CORE_DEBUG_XHPROF_PATH'])?$data['SP_CORE_DEBUG_XHPROF_PATH']:"")."');";
$writeConfig[] = "define('SP_CORE_HTTP_PROTOKOLL',SP_CORE_ADMIN_CENTER_HTTPS == 1 ? (empty(\$_SERVER['HTTPS'])?'http':'https') : 'http');";
$writeConfig[] = "define('SP_CORE_LANG', 'de');";
$writeConfig[] = "define('SP_CORE_ENCODING', 'UTF-8');";
$writeConfig[] = "define('SP_CORE_SUB_DOC_PATH','".substr(getcwd(), strlen($_SERVER['DOCUMENT_ROOT']), strpos(getcwd(), "/install")-strlen("/install"))."');";
$writeConfig[] = "define('SP_CORE_DOC_ROOT',\$_SERVER['DOCUMENT_ROOT']. SP_CORE_SUB_DOC_PATH );";

// portal Meta Settings
$writeConfig[] = "define('SP_CORE_TEMPLATE_META_TITEL','sp-portalsystem');";
$writeConfig[] = "define('SP_CORE_TEMPLATE_META_AUTHOR','sp-portalsystem');";
$writeConfig[] = "define('SP_PORTAL_META_DESCRIPTION', '');";
$writeConfig[] = "define('SP_PORTAL_META_KEYWOERDS','' );";
$writeConfig[] = "define('SP_PORTAL_META_ROBOTS','index, follow');";
$writeConfig[] = "define('SP_PORTAL_META_COMPANY','sp-portalsystem');";
$writeConfig[] = "define('SP_PORTAL_META_CREATOR','sp-portalsystem');";
$writeConfig[] = "define('SP_PORTAL_META_PUBLISCHER','www.sp-portalsystem.com');";
$writeConfig[] = "define('SP_PORTAL_META_COPYRIGHT','http://www.sp-portalsystem.com');";
$writeConfig[] = "define('SP_PORTAL_META_LANGUAGE','de, deutsch, en, englisch');";
$writeConfig[] = "define('SP_PORTAL_META_CONTENT','de, deutsch, en, englisch');";
$writeConfig[] = "define('SP_PORTAL_HTTP_HOST',SP_CORE_HTTP_PROTOKOLL.\"://\".\$_SERVER['HTTP_HOST'].\"/\".SP_CORE_SUB_DOC_PATH);";
$writeConfig[] = "define('SP_PORTAL_SITEMAP_URL','http://www.sp-portalsystem.com/sitemap.xml');";
$writeConfig[] = "define('SP_PORTAL_SYSTEM_URL_REWRITE',false);";

//RSS standard
$writeConfig[] = "define('SP_CORE_RSS_AUTHOR','spPortal-System');";
$writeConfig[] = "define('SP_CORE_RSS_DESCRIPTION','Neuigkeiten von www.sp-portalsystem.com');";
$writeConfig[] = "define('SP_CORE_RSS_LINK','http://www.sp-portalsystem.com');";
$writeConfig[] = "define('SP_CORE_RSS_SYNDICATION_URL','http://www.sp-portalsystem.com');";
$writeConfig[] = "define('SP_CORE_RSS_IMAGE_URL','images/logo.de');";
$writeConfig[] = "define('SP_CORE_RSS_IMAGE_DESCRIPTION','Unser Feed, klicken Sie hier um uns zu Besuchen');";

$file_config = str_replace("//","/",substr(getcwd(),0,strpos(getcwd(),"/install"))."/config.inc.php");
$config_file = fopen($file_config,"w+");

if(!$config_file){
    echo "<br>cant create config.inc.php on : ".$file_config;
}else{
    echo "create config.inc.php ok ".$file_config;
}
fputs($config_file, "<?php\n");
foreach ($writeConfig as $index) {
    fputs($config_file,$index."\n");
}
fputs($config_file, "?>");
fclose($config_file);


require_once substr(getcwd(), 0, strpos(getcwd(), "/install")) . '/config.inc.php';
require_once SP_CORE_DOC_ROOT .'/includes/CDatabase.php';
require_once SP_CORE_DOC_ROOT .'/includes/CHelper.php';
require_once SP_CORE_DOC_ROOT .'/includes/CUser.php';
require_once SP_CORE_DOC_ROOT .'/includes/CPermissions.php';


$db		= new spcore\CDatabase();
if($db === false){
    echo "db conn failed !";
}

$user		= new spcore\CUser($db,spcore\CHelper::getRequestVar());

$db->importFile(SP_CORE_DOC_ROOT ."/install/installSQL/install_sp_portal.sql");

$startUser = array(	'u_rfirstname' 	=> 'root',
                        'u_rlastname' 	=> 'root',
                        'u_name' 	=> 'root',
                        'u_password' 	=> 'test',
                        'u_email' 	=> 'test@test.de',
                        'u_groups' 	=> array(0 => 1),
                        'u_startActiv'	=> 1);

$sql = " INSERT INTO `sp_user`
                    (
                            `real_firstname` ,
                            `real_lastname` ,
                            `name` ,
                            `password` ,
                            `email` ,
                            `user_groups` ,
                            `active`
                    ) VALUES (
                            '".$db->checkValue($startUser['u_rfirstname'])."',
                            '".$db->checkValue($startUser['u_rlastname'])."',
                            '".$db->checkValue($startUser['u_name'])."',
                            '".md5($db->checkValue($startUser['u_password']))."',
                            '".$db->checkValue($startUser['u_email'])."',
                            '".serialize($startUser['u_groups'])."',
                            ".(isset($startUser['u_startActiv'])?"'1'":"'0'")."
                    ) ";

$result = $db->query($sql);
if(!$result){
    echo $db->getError();
}

$grpDetail = array(	'admin'	=> array(   'admin'	=> 'true',
                                            'detail'	=> array(
                                                'modul_settings'        => 1,
						'permission_setting'	=> 1,
						'system_settings'	=> 1
								)
					)
		);
spcore\CPermissions::getInstance($db)->addNewGroupe("Administrator",$grpDetail);
header('Location: '.SP_PORTAL_HTTP_HOST .'?modul=admin');
?>

<?xml version = "1.0" encoding = "UTF-8" ?>
<main>
    <adminInstall_SQL><![CDATA[
INSERT INTO `sp_modul_settings` (`modul_name`, `modul_active`, `modul_installed`, `modul_admin_box`, `modul_admin_box_r`) VALUES ('##MODULNAME##', 0, 1, 1, 1);
CREATE TABLE IF NOT EXISTS `sp_modul_##MODULNAME##` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
)AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `sp_modul_##MODULNAME##_permissions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
)AUTO_INCREMENT=1 ;
        ]]></adminInstall_SQL>    
    <adminUninstall_SQL><![CDATA[
DROP TABLE 
`sp_modul_##MODULNAME##`,
`sp_modul_##MODULNAME##_permissions`;
DELETE FROM `sp_modul_settings` WHERE `modul_name` = '##MODULNAME##';
        ]]></adminUninstall_SQL>
    <createClass><![CDATA[
require_once (SP_CORE_DOC_ROOT . "/includes/CUser.php");
require_once (SP_CORE_DOC_ROOT . "/includes/CPermissions.php");

class C##MODULNAME## {

    private $reqVar;
    private $tplObj;
    private $db;
    private $permission;

    public function __construct($mydb) {
        $this->reqVar = spcore\CHelper::getInstance()->getRequestVar();
        $this->tplObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("##MODULNAME##", "index");
        $this->db = $mydb;
        // loading permissions, uncomment this if you want
        /*
        if(empty($this->permission)) {
            //$this->permission	= spcore\CPermissions::getInstance($this->db)->getPermissionForUser($userData->id);
            //$this->permission = spcore\CPermissions::getInstance($this->db)->getPermissionForUser();
        }
        if($this->permission === false) {
            unset($this->permission);
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE ), __CLASS__."::".__FUNCTION__ , xdebug_call_class( )."->".xdebug_call_function()."::Line ".xdebug_call_line(), null, "Line: ".__LINE__." Session not loaded...");
        }*/
    }

    /**
     * in navBoxes
     */
    public function _nav_extportal() {
        
    }

    /**
     * in maincontent
     */
    public function _cont_extportal() {
        /*if($this->permission['##MODULNAME##']['detail']['permission_default'] != 1 ) {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=##MODULNAME##');
            exit();
        }*/
        
    }

    public static function getMyContent($myurl) {
        
    }
    
    /**
     * entry point
     */
    public function run(){
        //spcore\CTemplate::getInstance()->loadStandardTemplateXML('##MODULNAME##','index');
        //spcore\CTemplate::getInstance()->setWatchRightBox(false);
        //spcore\CTemplate::getInstance()->setWatchLeftBox(false);
        //spcore\CTemplate::getInstance()->setWatchContentBox(true);
        spcore\CTemplate::getInstance()->loadStandardTemplateXML('##MODULNAME##', 'index');
        spcore\CTemplate::getInstance()->addContentBox("Titel", "Hello Content");
    }

    function __destruct() {
        
    }

}
        ]]></createClass>
    <createClassAdmin><![CDATA[
require_once SP_CORE_DOC_ROOT . '/includes/CHelper.php';
require_once SP_CORE_DOC_ROOT . '/includes/CUser.php';
require_once SP_CORE_DOC_ROOT . '/includes/CPermissions.php';
require_once SP_CORE_DOC_ROOT . '/includes/CSession.php';

class C##MODULNAME##_admincenter {

    private $db;
    private $reqVar;
    private $permission;
    private $tplxml;

    public function __construct($db, $template, $session, $requestVar, $permission) {
        $this->db = $db;
        $this->reqVar = $requestVar;
        $this->tplxml = spcore\CTemplate::getInstance()->loadAdminModulTemplateXML("##MODULNAME##", "index");
        $this->permission = $permission;

        spcore\CTemplate::getInstance()->addJsScript("##MODULNAME##", "scripts/js/##MODULNAME##.js");
        spcore\CTemplate::getInstance()->addCssScript("##MODULNAME##", "scripts/css/##MODULNAME##.css");
    }

    public function ##MODULNAME##_yourFunction() {
        if (spcore\CSession::getInstance($this->db)->checkSession()) {
            spcore\CSession::getInstance($this->db)->writeSession();
            spcore\CTemplate::getInstance()->addContentBox("Installed", "Installes - please remove this Text");
        } else {
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

}
        ]]></createClassAdmin>
    <createAdminInstall><![CDATA[
function ##MODULNAME##_install($db) {
    $db->importFile(SP_CORE_DOC_ROOT ."/module/##MODULNAME##/admin/install.sql");
    // adding new user groupe for ##MODULNAME## modul
    $grpDetail = array(	'##MODULNAME##'	=> array(   'admin'	=> 'false',
                                                    'detail'	=> array(
                                                            'something'    => 1
                                                    )
                                            )
                                    );
    spcore\CPermissions::getInstance($db)->addNewGroupe("##MODULNAME##User",$grpDetail);
    //adding new admin user group for ##MODULNAME## modul
    $grpDetail = array(	'##MODULNAME##'	=> array(   'admin'	=> 'true',
                                                    'detail'	=> array(
                                                            '##MODULNAME##something'    => 0
                                                    )
                                            )
                                    );
    spcore\CPermissions::getInstance($db)->addNewGroupe("##MODULNAME##Admin",$grpDetail);
}

function ##MODULNAME##_uninstall($db) {
    $db->importFile(SP_CORE_DOC_ROOT ."/module/##MODULNAME##/admin/uninstall.sql");
}
    ]]></createAdminInstall>
    <createAdminInstallPermission><![CDATA[
require_once SP_CORE_DOC_ROOT .'/includes/CPermissions.php';

class ##MODULNAME##_permission extends spcore\CPermissions {
	public function permission_getConfigurePermissions(){
		return parent::loadFromDatabase("##MODULNAME##");
	}
	
	public function permission_getShortDescription(){
		return "##MODULNAME## Modul";
	}
}
        ]]></createAdminInstallPermission>
        <createAdminIndexFile><![CDATA[
require_once 'includes/C##MODULNAME##.php';

$##MODULNAME## = new C##MODULNAME##($session);
$##MODULNAME##->run();
        ]]></createAdminIndexFile>
    <createAdminNavigation><![CDATA[
<ul id="Navigation">
    <li><h1>.:: ##MODULNAME## Settings</h1></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=##MODULNAME##_settings">##MODULNAME## Settings</a></li>
</ul>
    ]]></createAdminNavigation>
</main>
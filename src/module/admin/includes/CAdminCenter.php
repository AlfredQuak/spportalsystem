<?php

/* spPortalSystem CAdminCenter.php
 * Created on 19.05.2009 from misterice
 *
 * spPortalSystem was written by Daniel Stecker 2009
 * please visit my website www.sploindy.de
 *
 * This file is part of spPortalSystem.
 * spPortalSystem is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or any later version.
 *
 * spPortalSystem is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
require_once SP_CORE_DOC_ROOT . '/includes/CUser.php';
require_once SP_CORE_DOC_ROOT . '/includes/CPermissions.php';


if (SP_CORE_ENCODING == "UTF-8") {
    if (!empty($requestVar) && is_array($requestVar)) {
        foreach ($requestVar as $index => $value) {
            if ($index != "sites") {
                $requestVar[$index] = @utf8_decode((empty($value) ? $index : $value));
            }
        }
    }
}

/**
 * \brief
 * Main AdminCenter Class
 * \details
 * Main for use moduladministration and dynamic loading from moduladmin sections.<br>
 * System administration worked here.
 * \defgroup admincenter
 * \ref CAdminCenter
 */
final class CAdminCenter extends spcore\CUser {

    private $session;
    private $reqVar;
    private $tplObj;
    public $permission;

    /**
     * @param unknown_type $template
     * @param unknown_type $session
     * @param unknown_type $requestVar
     * @param unknown_type $g_system
     */
    public function __construct($template, $session) {

        $this->session = $session;
        $this->reqVar = spCore\CHelper::getInstance()->getRequestVar();

        spcore\CTemplate::getInstance()->renderExtensionInEditor(false);

        parent::__construct(spCore\CDatabase::getInstance(), (isset($this->reqVar) ? $this->reqVar : "")); //cuser
        spcore\CTemplate::getInstance()->addJsScript("admin", "js/jquery.js");
        spcore\CTemplate::getInstance()->addJsScript("admin", "js/layout.js");
        spcore\CTemplate::getInstance()->addCssScript("admin", "css/layout.css");
        spcore\CTemplate::getInstance()->setWatchLeftBox(false);
        spcore\CTemplate::getInstance()->setWatchRightBox(false);

        if (file_exists(SP_CORE_DOC_ROOT . '/module/admin/lang/' . SP_CORE_LANG . '_admin.xml')) {
            spcore\CTemplate::getInstance()->setLangObj(simplexml_load_file(SP_CORE_DOC_ROOT . '/module/admin/lang/' . SP_CORE_LANG . '_admin.xml'));
        }

        // load admin Template
        $this->tplObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin", "mainTemplate");

        // some informations in maintemplate
        $someInformations['username'] = spCore\CSession::getInstance()->getUserName();

        $myAdminTemplate['index'] = $this->tplObj->index;
        $myAdminTemplate['indexHeader'] = spcore\CTemplate::getInstance()->parseModulTemplate("admin", $someInformations, $this->tplObj->indexHeader);
        $myAdminTemplate['indexFooter'] = $this->tplObj->indexFooter;
        $myAdminTemplate['indexBox'] = $this->tplObj->indexBox;
        $myAdminTemplate['indexContentBox'] = $this->tplObj->indexContentBox;

        spcore\CTemplate::getInstance()->setNewTemplate($myAdminTemplate, true);

        //spcore\CSession::getInstance($this->db)->checkSession();
        $this->permission = spcore\CPermissions::getInstance()->getPermissionForUser();
        if ($this->permission === false) {
            unset($this->permission);
        }
    }

    /**
     * \brief load all admin boxes
     * \details
     * Here you can watch side klicks in systemsettingsbox.<br>
     * Admin Boxes only load if your active user have the permission to see.<br>
     * If User have no permission to go into Admincenter this function log out the current user.
     */
    public function admin_admin_loadAdminBoxes() {
        $canUse = false;


        if (empty($this->permission)) {
            $this->permission = spcore\CPermissions::getInstance()->getPermissionForUser($_SESSION['user_id']);
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::"
                    . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission for user loaded ");
        }

        foreach ($this->permission as $pIndex => $a) {
            if ($this->permission[$pIndex]['admin'] == true) {
                $canUse = true;
            }
        }

        if ($canUse == true) {
            $sql = "SELECT counter FROM sp_settings WHERE id=0";
            $result = spCore\CDatabase::getInstance()->query($sql);
            $res = spCore\CDatabase::getInstance()->fetch_array($result);
            $boxarray['counter'] = $res['counter'];

            spcore\CTemplate::getInstance()->addLeftBox("{LANG_admin_TboxSystemSettings}::"
                    . $_SESSION['user_name'], spcore\CTemplate::getInstance()->parseModulTemplate("admin", $boxarray, $this->tplObj->basicNav));

            $sql = "SELECT * FROM sp_modul_settings WHERE modul_admin_box = 1";
            $result = spCore\CDatabase::getInstance()->query($sql);

            if ($result) {
                while ($res = spCore\CDatabase::getInstance()->fetch_array($result)) {
                    if (isset($this->permission[$res['modul_name']]) && $this->permission[$res['modul_name']]['admin'] == 'true') {
                        if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $res['modul_name'] . '/admin/lang/' . SP_CORE_LANG . '_' . $res['modul_name'] . '.xml')) {
                            spcore\CTemplate::getInstance()->setLangObj(simplexml_load_file(SP_CORE_DOC_ROOT . '/module/' . $res['modul_name'] . '/admin/lang/'
                                            . SP_CORE_LANG . '_' . $res['modul_name'] . '.xml'));
                        }
                        if ($res['modul_admin_box_r'] == 1) {
                            spcore\CTemplate::getInstance()->addRightBox("Modul::"
                                    . $res['modul_name'], spcore\CTemplate::getInstance()->loadModulTemplate($res['modul_name'], "admin_box"));
                        } else {
                            spcore\CTemplate::getInstance()->addLeftBox("Modul::"
                                    . $res['modul_name'], spcore\CTemplate::getInstance()->loadModulTemplate($res['modul_name'], "admin_box"));
                        }
                    }
                }
            }
        } else {
            $this->admin_admin_logout();
        }
    }

    /**
     * \brief
     * Check login for admincenter
     * \details
     * Here where load the permissions for Admincenter modul standard watch permission.
     *
     */
    public function admin_admin_login() {
        if (!isset($this->reqVar['name'])) {
            $this->reqVar['name'] = null;
        }
        if (!isset($this->reqVar['password'])) {
            $this->reqVar['password'] = null;
        }
        $myError = true;

        if ($this->reqVar['name'] == null || $this->reqVar['name'] == null) {
            spcore\CSession::getInstance()->deleteSession();
            $myError = false;
        }
        // check if your login is allowed
        $sql = "SELECT * FROM sp_user WHERE name ='"
                . spCore\CDatabase::getInstance()->checkValue($this->reqVar['name']) .
                "' AND password = '" . spCore\CDatabase::getInstance()->checkValue(md5($this->reqVar['password'])) . "'";
        $result = spCore\CDatabase::getInstance()->query($sql);
        if ($result) {
            $userData = spCore\CDatabase::getInstance()->fetch_object($result);
            if (is_object($userData)) {
                if (empty($this->permission)) {
                    $this->permission = spcore\CPermissions::getInstance()->getPermissionForUser($userData->id);
                }
            } else {
                // kill old Session (relogg as other user)
                spcore\CSession::getInstance()->deleteSession();
                $myError = false;
                spCore\CLog::getInstance()->log(SP_LOG_NOTICE, SP_LOG_NOTICE, __CLASS__
                        . "::" . __FUNCTION__, null, null, "Username(" . $this->reqVar['name']
                        . ") or password failed");
            }
        } else {
            // kill old Session (relogg as other user)
            spcore\CSession::getInstance()->deleteSession();
            $myError = false;
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                    . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " User logged out ...");
        }
        if (!isset($this->permission['admin']['admin']) || $this->permission['admin']['admin'] != true ||
                !isset($userData->active) || $userData->active == 0 || $myError == false) {
            if (!empty($this->reqVar['name']) && !empty($this->reqVar['password']) && ($this->reqVar['name'] != "Name" || $this->reqVar['password'] != "Passwort")) {
                //$this->myErrorLog("Login Error",true);
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                        . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Login error ...");
            }

            $myAdminTemplate['index'] = $this->tplObj->login;
            /* $myAdminTemplate['indexHeader'] = "";// spcore\CTemplate::getInstance()->parseModulTemplate("admin", $someInformations, $this->tplObj->indexHeader);
              $myAdminTemplate['indexFooter'] = "";//$this->tplObj->indexFooter;
              $myAdminTemplate['indexBox'] = "";//$this->tplObj->indexBox;
              $myAdminTemplate['indexContentBox'] = "";//$this->tplObj->login; */

            spcore\CTemplate::getInstance()->setNewTemplate($myAdminTemplate, true);
            //spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxAdminCenterLogin}", $this->tplObj->login);
        } else {
            spcore\CSession::getInstance()->startSession($userData->name, $userData->id);
            spcore\CSession::getInstance()->writeSession();
            $this->admin_admin_loadAdminBoxes();
            spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxLoginSucessfull}", "{LANG_admin_CboxLoginSucessfull}");
        }
    }

    /**
     * \brief
     * Check Modules in directory and give back
     * @return array
     */
    private function getModules() {
        $dir = dir(SP_CORE_DOC_ROOT . "/module/");
        while ($datei = $dir->read()) {
            if (($datei != ".svn") && ($datei != ".") &&
                    ($datei != "..") && file_exists(SP_CORE_DOC_ROOT . "/module/" . $datei . "/admin/" . $datei . "_install.php") &&
                    ($datei != "admin" || $datei != "")) {
                $installModul[$datei] = null;
            }
        }
        $dir->close();
        return $installModul;
    }

    /**
     * \brief
     * Watch all Moduls
     * \details
     * Bring up modul list mask. You
     * @return unknown_type
     */
    public function admin_admin_modulView() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if (isset($this->permission['admin']['admin']) && $this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['modul_settings'] == 1) {

                $myTplObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin", "modul_installx");
                $installModul = $this->getModules();

                $sql = "SELECT * FROM sp_modul_settings";
                $result = spCore\CDatabase::getInstance()->query($sql);
                if ($result != null) {
                    while ($res = spCore\CDatabase::getInstance()->fetch_object($result)) {
                        if ($res->modul_name != "admin") {
                            $installModul[$res->modul_name] = $res;
                        }
                    }
                }

                $tplContent['dir'] = null;
                foreach ($installModul as $index => $value) {
                    if (!empty($index) || $index != "") {
                        if (is_object($installModul[$index])) {
                            $url_install = '<a href="?modul=admin&action=admin_mInstall&' . $index . '='
                                    . ($installModul[$index]->modul_installed == 1 ? '0' : '1') . '">';
                            $url_activate = '<a href="?modul=admin&action=admin_mActivate&modulName='
                                    . $index . '&status=' . ($installModul[$index]->modul_active == 1 ? '0' : '1') . '">';

                            if ($installModul[$index]->modul_installed == 0) {
                                $boxArray['install'] = $url_install . $myTplObj->contentLinkModulInstall;
                            } else {
                                $boxArray['install'] = $url_install . $myTplObj->contentLinkModulUninstall;
                            }
                            if ($installModul[$index]->modul_active == 0) {
                                $boxArray['activate'] = $url_activate . $myTplObj->contentLinkModulActiv;
                            } else {
                                $boxArray['activate'] = $url_activate . $myTplObj->contentLinkModulInactiv;
                            }
                        } else {
                            $url_install = '<a href="?modul=admin&action=admin_mInstall&' . $index . '='
                                    . ($installModul[$index]['modul_installed'] == 1 ? '0' : '1') . '">';
                            $url_activate = '<a href="?modul=admin&action=admin_mActivate&modulName=' . $index . '&status=0">';

                            $boxArray['install'] = $url_install . $myTplObj->contentLinkModulInstall;
                            $boxArray['activate'] = $myTplObj->contentLinkModulInstallInactiv;
                        }
                        $boxArray['name'] = $index;
                        $tplContent['dir'] .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $boxArray, $myTplObj->content);
                    }
                }
                $this->admin_admin_loadAdminBoxes();
                spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxModulInstall}"
                        , spcore\CTemplate::getInstance()->parseModulTemplate("admin", $tplContent, $myTplObj->main));
            } else {
                $this->admin_admin_loadAdminBoxes();
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed ");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                        . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                    . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ...");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief Main install function
     */
    public function admin_admin_mInstall() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['modul_settings'] == 1) {

                $installModul = $this->getModules();
                $sql = "SELECT * FROM sp_modul_settings";
                $result = spCore\CDatabase::getInstance()->query($sql);
                if ($result && spCore\CDatabase::getInstance()->num_rows($result) > 0) {
                    while ($res = spCore\CDatabase::getInstance()->fetch_array($result)) {
                        if ($res['modul_name'] != "admin") {
                            $userData[$res['modul_name']] = $res;
                        }
                    }

                    foreach ($installModul as $modulName => $value) {
                        if (!isset($userData[$modulName])) {
                            $userData[$modulName] = null;
                        }
                        try {
                            if (isset($this->reqVar[$modulName]) && $this->reqVar[$modulName] == 1 && $userData[$modulName]['modul_installed'] == 0) {
                                include (SP_CORE_DOC_ROOT . "/module/" . $modulName . "/admin/" . $modulName . "_install.php");
                                eval($modulName . "_install(\spCore\\CDatabase::getInstance());");
                            } elseif (isset($this->reqVar[$modulName]) && $this->reqVar[$modulName] == 0 && $userData[$modulName]['modul_installed'] == 1) {
                                include (SP_CORE_DOC_ROOT . "/module/" . $modulName . "/admin/" . $modulName . "_install.php");
                                $installModul[$datei] = SP_CORE_DOC_ROOT . "/module/" . $modulName . "/admin/" . $modulName . "_install.php";
                                eval($modulName . "_uninstall(\spCore\\CDatabase::getInstance());");
                            }
                        } catch (Exception $e) {
                            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Install Error : " . $e->getMessage());
                            spcore\CLog::getInstance()->log(SP_LOG_ERROR, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__
                                    . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Install Error : " . $e->getMessage());
                        }
                    }
                }
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_modulView');
                exit;
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                        . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::"
                    . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            //$this->myErrorLog("Session Error");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief Activate or Deactivate modul
     */
    public function admin_admin_mActivate() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['modul_settings'] == 1) {
                if ($this->reqVar['modulName'] != "admin") {
                    $sql = "UPDATE sp_modul_settings SET modul_active=IF(modul_active=0,'1','0') WHERE modul_name = '"
                            . spCore\CDatabase::getInstance()->checkValue($this->reqVar['modulName']) . "'";
                    spCore\CDatabase::getInstance()->query($sql);
                }
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_modulView');
                exit;
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                        . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                    . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            //$this->myErrorLog("Session Error");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Set your standard start modul
     */
    public function admin_admin_mStart() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['modul_settings'] == 1) {
                if (!empty($this->reqVar['modulName'])) {
                    $sql = "UPDATE sp_settings SET start_modul = '" . spCore\CDatabase::getInstance()->checkValue($this->reqVar['modulName']) . "' WHERE id = 0 ";
                    $result = spCore\CDatabase::getInstance()->query($sql);
                }

                $sql = "SELECT start_modul FROM sp_settings WHERE id = 0";
                $result = spCore\CDatabase::getInstance()->query($sql);
                $userData = spCore\CDatabase::getInstance()->fetch_object($result);

                $sql = "SELECT modul_name, modul_active FROM `sp_modul_settings`";
                $result = spCore\CDatabase::getInstance()->query($sql);

                while ($res = spCore\CDatabase::getInstance()->fetch_array($result)) {
                    $modulSettings[$res['modul_name']] = $res;
                }

                $dir = dir(SP_CORE_DOC_ROOT . "/module/");
                $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("admin", "box_content_modul_start");
                $boxArray['name'] = "<input type=\"radio\" name=\"modulName\" value=\"noModul\" " . ($userData->start_modul == "noModul" ? "checked" : "")
                        . "> kein Modul";
                $tplContent['dir'] = spcore\CTemplate::getInstance()->parseModulTemplate("admin", $boxArray, $boxContent) . "\n";

                while ($datei = $dir->read()) {
                    if (($datei != ".svn") && ($datei != ".") && ($datei != "..")) {
                        if (isset($modulSettings[$datei]['modul_active']) && $modulSettings[$datei]['modul_active'] == 1) {
                            $boxArray['name'] = '<input type="radio" name="modulName" value="' . $datei . '" ' . ($userData->start_modul == $datei ? 'checked' : '')
                                    . '> modul_' . $datei;
                            $tplContent['dir'] .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $boxArray, $boxContent) . "\n";
                        }
                    }
                }
                $dir->close();

                $this->admin_admin_loadAdminBoxes();
                spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxModulSetStartModul}"
                        , spcore\CTemplate::getInstance()->loadModulTemplate("admin", "start_modul", $tplContent));
            } else {
                $this->admin_admin_loadAdminBoxes();
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                        . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                    . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Log out the admin.
     */
    public function admin_admin_logout() {
        spcore\CSession::getInstance()->deleteSession();
        header('Location: HTTP://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF']);
        exit;
    }

    /**
     * \brief
     * Only watch the settings
     * @return none
     */
    public function admin_admin_settings() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            $this->admin_admin_loadAdminBoxes();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['system_settings'] == 1) {

                $boxArray['databaseServer'] = "no permission"; //SP_CORE_DB_SERVER ;
                $boxArray['databaseName'] = "no permission"; //SP_CORE_DB_DATABASE;
                $boxArray['databaseUser'] = "no permission"; //SP_CORE_DB_USER;
                $boxArray['databasePass'] = "no permission"; //SP_CORE_DB_PASS;

                $boxArray['debugMode'] = SP_CORE_DEBUG;
                $boxArray['templatePath'] = SP_CORE_TEMPLATE_PATH;

                $sql = "SELECT * FROM `sp_modul_admin_settings` WHERE id=1";
                $result = spCore\CDatabase::getInstance()->query($sql);
                $res = spCore\CDatabase::getInstance()->fetch_array($result);

                $boxArray['ip1'] = $res['ip1'];
                $boxArray['ip2'] = $res['ip2'];
                $boxArray['ip3'] = $res['ip3'];
                $boxArray['ip4'] = $res['ip4'];

                $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("admin", "box_content_settings");
                spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxSystemSettings}"
                        , spcore\CTemplate::getInstance()->parseModulTemplate("admin", $boxArray, $boxContent));
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                        . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                    . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Change the your password.
     * \todo
     * change user passwort
     * @return none
     */
    public function admin_admin_changePW() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['system_settings'] == 1) {
                $sessData = spcore\CSession::getInstance()->getUserObj();
                $sql = "UPDATE sp_user SET password = '" . $this->db->checkValue(md5($this->reqVar['newPW'])) . "'
                                WHERE
                                    name ='" . spCore\CDatabase::getInstance()->checkValue($sessData['user_name']) . "'
                                AND password = '" . spCore\CDatabase::getInstance()->checkValue(md5($this->reqVar['oldPW'])) . "'";
                $result = spCore\CDatabase::getInstance()->query($sql);
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_settings');
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                        . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__
                    . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Bind admincenter to ip
     * return none
     */
    public function admin_admin_bindToIp() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['system_settings'] == 1) {
                $sessData = spcore\CSession::getInstance()->getUserObj();
                $sql = "UPDATE `sp_modul_admin_settings`  SET
                                `ip1` = '" . $this->db->checkValue($this->reqVar['ip1']) . "',
                                `ip2` = '" . $this->db->checkValue($this->reqVar['ip2']) . "',
                                `ip3` = '" . $this->db->checkValue($this->reqVar['ip3']) . "',
                                `ip4` = '" . $this->db->checkValue($this->reqVar['ip4']) . "' WHERE `id` =1";
                $result = spCore\CDatabase::getInstance()->query($sql);
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_settings');
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Add new language
     * \details
     * Add and delete language infodata into table
     */
    /*
      public function admin_admin_lang(){
      if ($this->checkSession()) {
      $this->writeSession();
      $this->admin_admin_loadAdminBoxes();
      if($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['system_settings'] == 1){
      $myTmpObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin","lang_settings");
      $contentArray = array();
      if ($handle = opendir( SP_CORE_TEMPLATE_PATH .'images/flagicons')) {
      while (false !== ($file = readdir($handle))) {
      if ($file != "." && $file != ".." && $file != "readme.txt" && eregi(".png",$file) == 1) {
      $langArray[substr($file,0,(int)strpos($file,"."))]['value']	= substr($file,0,(int)strpos($file,"."));
      $langArray[substr($file,0,(int)strpos($file,"."))]['select']= "";
      $langArray[substr($file,0,(int)strpos($file,"."))]['text']	= substr($file,0,(int)strpos($file,"."));
      }
      }
      closedir($handle);
      }
      $handle = fopen ("./module/admin/lang/importme.csv","r");
      while ( ($data = fgetcsv ($handle, 1000, ",")) !== FALSE ) {
      $myLang[strtolower($data[0])] = $data[1];
      }
      fclose ($handle);
      sort($langArray);

      foreach($langArray as $value){
      $boxContent['value']			= $value['value'];
      $boxContent['select']			= $value['select'];
      $boxContent['text']				= (isset($myLang[$value['value']])?$myLang[$value['value']]:$value['value'])."[".$value['value']."]";
      $contentArray['langOptions']	.= spcore\CTemplate::getInstance()->parseModulTemplate("admin",$boxContent,$myTmpObj->langOptions);
      }

      $boxContent 	= array();
      $sql 			= "SELECT * FROM sp_lang";
      $result 		= $this->db->query($sql);
      if($result){
      while($res = $this->db->fetch_assoc($result)){
      $boxContent['langTag'] 		= $res['lang_id'];
      $boxContent['langCountry'] 	= "[".$res['lang_id']."] ".(isset($myLang[$res['lang_id']])?$myLang[$res['lang_id']]:$res['lang_id']);
      $contentArray['installedLangs'] .= spcore\CTemplate::getInstance()->parseModulTemplate("admin",$boxContent,$myTmpObj->installedLangs);;
      }
      }else{
      $contentArray['installedLangs'] = "Keine Sprachen installiert";
      }

      spcore\CTemplate::getInstance()->addContentBox("Language Settings",spcore\CTemplate::getInstance()->parseModulTemplate("admin",$contentArray,$myTmpObj->mainForm));
      }else{
      spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}","Not Allowed");
      }
      } else {
      header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
      exit;
      }
      }
     */

    /**
     * \brief
     * Insert systemavaible language to database
     * \details
     * After your add you can use this data for your own multilanguage settings.<br>
     * ! This function does <b>not</b> add your language strings into database ! <br>
     * Only for infoholding to use your own multilanguage coding !
     */
    public function admin_admin_setLang() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['system_settings'] == 1) {
                $sql = "INSERT INTO `sp_lang` (`lang_id`) VALUES ('" . spCore\CDatabase::getInstance()->checkValue($this->reqVar['lang']) . "')";
                spCore\CDatabase::getInstance()->query($sql);
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_lang');
                exit;
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Delete language from database
     * \details
     * Delete database entry from tabel
     */
    public function admin_admin_delLang() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['system_settings'] == 1) {
                $sql = "DELETE FROM `sp_lang` WHERE `lang_id` = '" . $this->db->checkValue($this->reqVar['langTag']) . "' LIMIT 1";
                $this->db->query($sql);
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_lang');
                exit;
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \todo replace old confic file.
     * @return unknown_type
     */
    public function admin_admin_writeConfig() {
        /*
          $setting['dbServer'] = "isset($requestVar['setDBServer'])?$requestVar['setDBServer']: SP_CORE_DB_SERVER  . "');";
          $setting['dbUser'] = "isset($requestVar['setDBUser'])?$requestVar['setDBUser']: SP_CORE_DB_USER . "');";
          $setting['dbPass'] = "isset($requestVar['setDBPass'])?$requestVar['setDBPass']: SP_CORE_DB_SERVER  . "');";
          $setting['dbName'] = "isset($requestVar['setDBName'])?$requestVar['setDBName']: SP_CORE_DB_SERVER  . "');";

          $setting['debugMode'] = "define(' SP_CORE_DEBUG ','" . isset($requestVar['setDebugMode'])?$requestVar['setDebugMode']: SP_CORE_DEBUG  . "');";
          $setting['templatePath'] = "define(' SP_CORE_TEMPLATE_PATH','" . isset($requestVar['setTemplate'])?$requestVar['setTemplate']: SP_CORE_TEMPLATE_PATH . "');";

          //if(isset($requestVar['setTemplate'])) $setting['adminPW'] = $requestVar['setAdminPW'];
         */
    }

    /**
     * \brief
     * Watch all users here.
     * \details
     * You can adiministrate all user here.
     * @return none
     */
    public function admin_admin_userManagemant() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            $this->admin_admin_loadAdminBoxes();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                $mytemplate = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin", "permission_user");
                $sql = "SELECT * FROM sp_user";
                $result = spCore\CDatabase::getInstance()->query($sql);
                $boxArray['table'] = null;

                while ($res = spCore\CDatabase::getInstance()->fetch_array($result)) {
                    $groupArray = unserialize($res['user_groups']);
                    $sql = "SELECT * FROM sp_user_group WHERE id=" . implode(" OR id=", $groupArray);
                    $gr_result = spCore\CDatabase::getInstance()->query($sql);

                    while ($gr_res = spCore\CDatabase::getInstance()->fetch_array($gr_result)) {
                        $mygroups[] = $gr_res['groupe_name'];
                    }
                    $mysnipped['snippedID'] = $res['id'];
                    $mysnipped['snippedFirstname'] = $res['real_firstname'];
                    $mysnipped['snippedLastname'] = $res['real_lastname'];
                    $mysnipped['snippedActiv'] = $res['active'] == 1 ? 'Apply.png' : 'No.png';
                    $mysnipped['snippedUsername'] = $res['name'];
                    $mysnipped['snippedUsergroups'] = is_array($mygroups) ? implode(",", $mygroups) : null;

                    $boxArray['table'] .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $mysnipped, $mytemplate->tablesnipped);
                    $mygroups = null;
                }

                spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxSystemSettings}", spcore\CTemplate::getInstance()->parseModulTemplate("admin", $boxArray, $mytemplate->maintable));
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Activate user
     * \details
     * Activate or deactivate user
     *
     * @return none
     */
    public function admin_admin_activateUser() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                if ($this->reqVar['userid'] != $_SESSION['user_id']) {
                    $sql = "UPDATE `sp_user` SET `active` =IF(active=0,'1','0') WHERE `id` =" . spCore\CDatabase::getInstance()->checkValue($this->reqVar['userid']) . " LIMIT 1;";
                    spCore\CDatabase::getInstance()->query($sql);
                }
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_userManagemant&userid=' . $this->reqVar['userid']);
                exit;
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Delete User from database
     * \details
     * Delete User from database and logg the deletet user out
     *
     * @return none
     */
    public function admin_admin_delUserdata() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                if ($this->reqVar['userid'] != $_SESSION['user_id']) {
                    $sql = "DELETE FROM `sp_user` WHERE `id` = " . spCore\CDatabase::getInstance()->checkValue($this->reqVar['userid']) . " LIMIT 1";
                    spCore\CDatabase::getInstance()->query($sql);
                    $sql = "DELETE FROM sp_session WHERE user_id = " . spCore\CDatabase::getInstance()->checkValue($this->reqVar['userid']);
                    spCore\CDatabase::getInstance()->query($sql);
                }
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_userManagemant&userid=' . $this->reqVar['userid']);
                exit;
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Loading the add user template
     * @return none
     */
    public function admin_admin_userAdd() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            $this->admin_admin_loadAdminBoxes();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                $boxContent = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin", "permission_addUser");

                $sql = "SELECT * FROM sp_user_group";
                $result = spCore\CDatabase::getInstance()->query($sql);
                $content['option'] = null;

                if ($result) {
                    while ($res = spCore\CDatabase::getInstance()->fetch_array($result)) {
                        $myArray['value'] = $res['id'];
                        $myArray['groupname'] = $res['groupe_name'];

                        $content['option'] .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $myArray, $boxContent->snippedGroups);
                    }
                }
                spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxSystemSettings}", spcore\CTemplate::getInstance()->parseModulTemplate("admin", $content, $boxContent->mainform));
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief New user adding.
     * \details
     * This function add the user in the database.
     * Administrator must be enable the user.
     * @return none
     */
    public function admin_admin_userAddData() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                if (!empty($this->reqVar['per_username']) && !empty($this->reqVar['per_password']) && !empty($this->reqVar['per_email'])) {

                    $userArray['u_rfirstname'] = $this->reqVar['per_firstname'];
                    $userArray['u_rlastname'] = $this->reqVar['per_lastname'];
                    $userArray['u_name'] = $this->reqVar['per_username'];
                    $userArray['u_password'] = $this->reqVar['per_password'];
                    $userArray['u_email'] = $this->reqVar['per_email'];
                    $userArray['u_groups'] = $this->reqVar['per_group'];

                    if (!$this->addUser($userArray) && !is_array($userArray['u_groups'])) {
                        header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_userAdd');
                        exit;
                    } else {
                        header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_userManagemant');
                        exit;
                    }
                } else {
                    header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_userAdd');
                    exit;
                }
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     *
     * @return unknown_type
     */
    public function admin_admin_getUserData() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            $this->admin_admin_loadAdminBoxes();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {

                $userArr = $this->getUserDataFromId($this->reqVar['userid']);
                $allGrp = $this->getUserGroups();
                if ($userArr !== false) {
                    $userData['firstname'] = $userArr['real_firstname'];
                    $userData['lastname'] = $userArr['real_lastname'];
                    $userData['username'] = $userArr['name'];
                    $userData['email'] = $userArr['email'];
                    $userData['userid'] = $userArr['id'];
                    $userData['groups'] = null;

                    $sql = "SELECT * FROM sp_user_group";
                    $result = spCore\CDatabase::getInstance()->query($sql);
                    while ($res = spCore\CDatabase::getInstance()->fetch_assoc($result)) {
                        $userData['groups'] .= spcore\CTemplate::getInstance()->makeOptionTag($res['id'], $res['groupe_name'], isset($userArr['user_groups'][$res['id']]));
                    }

                    $myTemplateObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin", "permission_user");
                    spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxSystemSettings}", spcore\CTemplate::getInstance()->parseModulTemplate("admin", $userData, $myTemplateObj->userEdit));
                } else {
                    header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_userManagemant');
                    exit;
                }
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Update User Settings
     * \detail
     * Here you can edit system User settings
     * @return unknown_type
     */
    public function admin_admin_userDataUpdate() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1 && !empty($this->reqVar['userid'])) {

                $updateSet['real_firstname'] = $this->reqVar['firstname'];
                $updateSet['real_lastname'] = $this->reqVar['lastname'];
                $updateSet['name'] = $this->reqVar['username'];
                $updateSet['user_groups'] = serialize($this->reqVar['group']);

                if ($this->reqVar['newpass1'] == $this->reqVar['newpass2']) {
                    $updateSet['password'] = md5($this->reqVar['newpass1']);
                }
                if ($this->reqVar['newemail1'] == $this->reqVar['newemail2']) {
                    $updateSet['email'] = $this->reqVar['newemail1'];
                }
                foreach ($updateSet as $index => $value) {
                    if (!empty($value)) {
                        $sqlQuery[] = spCore\CDatabase::getInstance()->checkValue($index) . "='" . spCore\CDatabase::getInstance()->checkValue($value) . "'";
                    }
                }

                $sql = "UPDATE sp_user SET " . implode(",", $sqlQuery) . " WHERE id=" . spCore\CDatabase::getInstance()->checkValue($this->reqVar['userid']);
                spCore\CDatabase::getInstance()->query($sql);
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_userManagemant');
                exit;
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Loads all Permissions to edit for Groups
     * \details
     * Here we load all permissions and create a new Group.<br>
     * You can edit detail permissions of every modul for <br>
     * We load here the permission template into private variable permissionTemplate<br>
     * for use in function admin_admin_loadGroupTemplate() <br>
     *
     * @return unknown_type
     */
    public function admin_admin_groupAdd() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            $this->admin_admin_loadAdminBoxes();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                $myTemplateObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin", "permission_groupAddXML");
                //load and display modul permission managemant
                $sql = "SELECT modul_name FROM `sp_modul_settings` WHERE modul_installed = 1 ";
                $result = spCore\CDatabase::getInstance()->query($sql);
                $content = null;
                $boxContent = null;

                while ($res = spCore\CDatabase::getInstance()->fetch_array($result)) {
                    if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $res['modul_name'] . "/admin/" . $res['modul_name'] . "_permissions.php")) {
                        require_once SP_CORE_DOC_ROOT . '/module/' . $res['modul_name'] . '/admin/' . $res['modul_name'] . '_permissions.php';

                        $modulSettings[$res['modul_name']] = $res;
                        $myclass = $res['modul_name'] . "_permission";

                        if (!class_exists($myclass)) {
                            continue;
                        }
                        $myeval = $myclass::permission_getConfigurePermissions(spCore\CDatabase::getInstance());
                        //eval("\$myeval =".$res['modul_name']."_permission::permission_getConfigurePermissions(\$this->db);");

                        if (is_array($myeval)) {
                            foreach ($myeval as $value) {
                                $value['modulname'] = $res['modul_name'];
                                $content .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $value, $myTemplateObj->snipped);
                            }
                            //eval("\$myeval =".$res['modul_name']."_permission::permission_getShortDescription(\$this->db);");
                            $myeval = $myclass::permission_getShortDescription();

                            $boxContentArray['modulname'] = $res['modul_name'];
                            $boxContentArray['modulCoreText'] = $myeval;
                            $boxContentArray['modulContent'] = $content;

                            $boxContentArray['coreTrue'] = isset($corePermission['modul'][$res['modul_name']]['admin']) && $corePermission['modul'][$res['modul_name']]['admin'] == 'true' ? 'checked="checked"' : '';
                            $boxContentArray['coreFalse'] = isset($corePermission['modul'][$res['modul_name']]['admin']) && $corePermission['modul'][$res['modul_name']]['admin'] != 'true' ? 'checked="checked"' : '';

                            $boxContent .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $boxContentArray, $myTemplateObj->mainAdd);
                            $content = null;
                            $boxContentArray = null;
                        }
                    }
                }
                $mainContent['content'] = $boxContent;
                $content = spcore\CTemplate::getInstance()->loadModulTemplate("admin", "permission_groupAddgrpAdd", $mainContent);
                spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxSystemSettings}", $content);
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Add new group to database
     * \details
     * not details yet, in deployement
     *
     * @return none
     */
    public function admin_admin_addGroupPermissonToDb() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                if (empty($this->reqVar['groupeName'])) {
                    header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_groupAdd');
                    exit;
                }
                $modul = null;
                foreach ($this->reqVar as $index => $value) {
                    $pos = strpos($index, 'per_');
                    if ($pos !== false) {
                        $sub = substr($index, 4);

                        if (substr($index, strpos($index, "_", 4) + 1) == 'core') {
                            $modul[substr($sub, 0, strpos($sub, "_"))]['admin'] = $this->db->checkValue($value);
                        } else {
                            $modul[substr($sub, 0, strpos($sub, "_"))]['detail'][substr($index, strpos($index, "_", 4) + 1)] = spCore\CDatabase::getInstance()->checkValue($value);
                        }
                        if (!isset($modul[substr($sub, 0, strpos($sub, "_"))]['detail']) || empty($modul[substr($sub, 0, strpos($sub, "_"))]['detail'])) {
                            $modul[substr($sub, 0, strpos($sub, "_"))]['detail'] = null;
                        }
                    }
                }
                spcore\CPermissions::getInstance()->addNewGroupe(spCore\CDatabase::getInstance()->checkValue($this->reqVar['groupeName']), $modul);
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_groupSettings');
                exit;
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Update group permission
     * \details
     * Update permissions for group givenn by id.
     *
     * @return none
     */
    public function admin_admin_updateGroupPermission() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                $modul = null;
                foreach ($this->reqVar as $index => $value) {
                    $pos = strpos($index, 'per_');
                    if ($pos !== false) {
                        $sub = substr($index, 4);
                        if (substr($index, strpos($index, "_", 4) + 1) == 'core') {
                            $modul[substr($sub, 0, strpos($sub, "_"))]['admin'] = spCore\CDatabase::getInstance()->checkValue($value);
                        } else {
                            $modul[substr($sub, 0, strpos($sub, "_"))]['detail'][substr($index, strpos($index, "_", 4) + 1)] = spCore\CDatabase::getInstance()->checkValue($value);
                        }
                        if (!isset($modul[substr($sub, 0, strpos($sub, "_"))]['detail']) || empty($modul[substr($sub, 0, strpos($sub, "_"))]['detail'])) {
                            $modul[substr($sub, 0, strpos($sub, "_"))]['detail'] = null;
                        }
                    }
                }
                if (spcore\CPermissions::getInstance()->updateGroupe($modul, spCore\CDatabase::getInstance()->checkValue($this->reqVar['groupid']))) {
                    header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_groupSettings');
                    exit;
                } else {
                    $this->myErrorLog("Permission update failed");
                    header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_groupSettings');
                    exit;
                }
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Delete group
     * \details
     * Delete groupe from Database and detailpermissions for this group in all detail tables. <br>
     * Update user groups, delete this group from userprofile.
     *
     * @return none
     */
    public function admin_admin_delGrp() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                $sql = "SELECT * FROM sp_user_group WHERE id=" . $this->db->checkValue($this->reqVar['grpID']);
                $result = spCore\CDatabase::getInstance()->query($sql);
                if ($result) {
                    $res = spCore\CDatabase::getInstance()->fetch_object($result);
                    $grp = unserialize($res->admin);
                    if (is_array($grp)) {
                        $sql = array();
                        $sql[] = "DELETE FROM sp_user_group WHERE id =" . spCore\CDatabase::getInstance()->checkValue($this->reqVar['grpID']) . " LIMIT 1";
                        foreach ($grp as $index => $value) {
                            $sql[] = "DELETE FROM `sp_modul_" . $index . "_permissions` WHERE `id` =" . $grp[$index]['id'] . " LIMIT 1 ;";
                        }
                        $result = spCore\CDatabase::getInstance()->query("SELECT id,user_groups FROM sp_user");
                        $newArray = array();
                        while ($res = spCore\CDatabase::getInstance()->fetch_array($result)) {
                            $checkUser = false;
                            $newArray = null;
                            $gprArray = null;
                            $gprArray = $res['user_groups'];
                            $gprArray = unserialize($gprArray);
                            if (is_array($gprArray)) {
                                foreach ($gprArray as $index => $value) {
                                    if ($value != $this->reqVar['grpID']) {
                                        $newArray[] = $value;
                                    } else {
                                        $checkUser = true;
                                    }
                                }
                                // if group is in, update
                                if ($checkUser) {
                                    $sql[] = "UPDATE sp_user SET user_groups=" . serialize($newArray) . " WHERE id=" . $res['id'] . " LIMIT 1;";
                                }
                            }
                        }
                    }
                    if (is_array($sql)) {
                        foreach ($sql as $query) {
                            spCore\CDatabase::getInstance()->query($query);
                        }
                    }
                }
                header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=admin_groupSettings');
                exit;
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * List all groups for edit
     * \details
     * no details yet in deployment
     *
     * @return unknown_type
     */
    public function admin_admin_groupSettings() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            $this->admin_admin_loadAdminBoxes();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {
                $myTemplateObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin", "permission_groupList");
                $allgrp = spcore\CPermissions::getInstance()->getAllGroups();
                $content['content'] = null;

                foreach ($allgrp as $index => $value) {
                    $myArray['grpID'] = $value['id'];
                    $myArray['grpName'] = $index;
                    $content['content'] .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $myArray, $myTemplateObj->snipped);
                }
                /**
                 * \todo
                 * clearing the template and add some edit functions
                 */
                spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxSystemSettings}", spcore\CTemplate::getInstance()->parseModulTemplate("admin", $content, $myTemplateObj->mainform));
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    /**
     * \brief
     * Load all permissions from selected group
     *
     * @return none
     */
    public function admin_admin_groupEdit() {
        if (spcore\CSession::getInstance()->checkSession()) {
            spcore\CSession::getInstance()->writeSession();
            $this->admin_admin_loadAdminBoxes();
            if ($this->permission['admin']['admin'] == true && $this->permission['admin']['detail']['permission_setting'] == 1) {

                $myTemplateObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("admin", "permission_groupAddXML");
                $corePermission = spcore\CPermissions::getInstance()->getGroupFromId(spCore\CDatabase::getInstance()->checkValue($this->reqVar['id']));

                //load and display modul permission managemant
                $sql = "SELECT modul_name FROM `sp_modul_settings` WHERE modul_installed = 1 ";
                $result = spCore\CDatabase::getInstance()->query($sql);
                $content = null;
                $boxContent = null;

                while ($res = spCore\CDatabase::getInstance()->fetch_assoc($result)) {
                    if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $res['modul_name'] . "/admin/" . $res['modul_name'] . "_permissions.php")) {
                        require_once SP_CORE_DOC_ROOT . '/module/' . $res['modul_name'] . '/admin/' . $res['modul_name'] . '_permissions.php';
                        $modulSettings[$res['modul_name']] = $res;
                        $myclass = $res['modul_name'] . "_permission";

                        if (class_exists($myclass)) {
                            $myeval = $myclass::getInstance()->permission_getConfigurePermissions();
                            // load lang file
                            if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $res['modul_name'] . '/admin/lang/' . SP_CORE_LANG . '_' . $res['modul_name'] . '.xml')) {
                                spcore\CTemplate::getInstance()->setLangObj(simplexml_load_file(SP_CORE_DOC_ROOT . '/module/' . $res['modul_name'] . '/admin/lang/' . SP_CORE_LANG . '_' . $res['modul_name'] . '.xml'));
                            }

                            if (is_array($myeval)) {
                                $mypermission[$res['modul_name']] = spcore\CPermissions::getInstance()->getModulGroupPermissionFrom($res['modul_name'], spCore\CDatabase::getInstance()->checkValue($this->reqVar['id']));
                                foreach ($myeval as $value) {
                                    $value['modulname'] = $res['modul_name'];
                                    $value['activ'] = (is_array($mypermission[$res['modul_name']]) && $mypermission[$res['modul_name']][$value['inputname']] == 1 ? 'checked' : '');
                                    $content .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $value, $myTemplateObj->snipped);
                                }

                                $myeval = $myclass::getInstance()->permission_getShortDescription();
                                $boxContentArray['modulname'] = $res['modul_name'];

                                $boxContentArray['modulCoreText'] = $myeval;
                                $boxContentArray['modulContent'] = $content;

                                if (!isset($corePermission['modul'][$res['modul_name']])) {
                                    $corePermission['modul'][$res['modul_name']]['admin'] = false;
                                }

                                $boxContentArray['coreTrue'] = $corePermission['modul'][$res['modul_name']]['admin'] == 'true' ? 'checked="checked"' : '';
                                $boxContentArray['coreFalse'] = $corePermission['modul'][$res['modul_name']]['admin'] != 'true' ? 'checked="checked"' : '';

                                $boxContent .= spcore\CTemplate::getInstance()->parseModulTemplate("admin", $boxContentArray, $myTemplateObj->main);
                                $content = null;
                                $boxContentArray = null;
                            } else {
                                
                            }
                        }
                    }
                }
                $mainContent['content'] = $boxContent;
                $mainContent['groupename'] = $corePermission['name'];
                $mainContent['grpid'] = spCore\CDatabase::getInstance()->checkValue($this->reqVar['id']);

                spcore\CTemplate::getInstance()->addContentBox("{LANG_admin_TboxSystemSettings}", spcore\CTemplate::getInstance()->parseModulTemplate("admin", $mainContent, $myTemplateObj->mainformUpdate));
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
                spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            }
        } else {
            //$this->myErrorLog("Session Error");
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session error ... ");
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin');
            exit;
        }
    }

    function __destruct() {
        
    }

}

?>
<?php
/* spPortalSystem admin_index.php
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
/**
 * @ingroup admincenter
 * @file
 */



require_once SP_CORE_DOC_ROOT . '/includes/CDatabase.php';

if( SP_CORE_ADMIN_CENTER_HTTPS && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' )) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . '/'.SP_CORE_SUB_DOC_PATH. '/?modul=admin');
    exit;
}

$sql        = "SELECT * FROM `sp_modul_admin_settings` WHERE id=1";
$result     = spCore\CDatabase::getInstance()->query($sql);
$res        = spCore\CDatabase::getInstance()->fetch_array($result);
$myIp       = explode(".", $_SERVER['REMOTE_ADDR']);
$adminGoIn  = true;

if($res['ip1'] != 0) {
    if($myIp[0] != $res['ip1']) $adminGoIn = false;
}
if($res['ip2'] != 0) {
    if($myIp[1] != $res['ip2']) $adminGoIn = false;
}
if($res['ip3'] != 0) {
    if($myIp[2] != $res['ip3']) $adminGoIn = false;
}
if($res['ip4'] != 0) {
    if($myIp[3] != $res['ip4']) $adminGoIn = false;
}

if($adminGoIn === false) {
    header('Location: '. SP_CORE_HTTP_PROTOKOLL .'://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF']);
    exit;
}else {
    require_once ( SP_CORE_DOC_ROOT .'/module/admin/includes/CAdminCenter.php' );
    $admin = new CAdminCenter($template, $session, $requestVar);
    $modul = array();
    if(!isset($requestVar['action'])) {
        $requestVar['action']=null;
    }
    spcore\CTemplate::getInstance()->doNotCacheThisSide();
    $modul = explode('_',$requestVar['action']);

    if (isset($modul[0]) && file_exists(SP_CORE_DOC_ROOT .'/module/'.spcore\CHelper::stripRequestVar($modul[0])
            .'/includes/C'.spcore\CHelper::stripRequestVar($modul[0]).'_admincenter.php')) {
        if($session->checkSession()) {
            $myGenClass = "C".spcore\CHelper::stripRequestVar($modul[0])."_admincenter";
            require_once SP_CORE_DOC_ROOT .'/module/'.spcore\CHelper::stripRequestVar($modul[0]).'/includes/'.$myGenClass.'.php';
            if(class_exists($myGenClass)) {
                if (spcore\CSession::getInstance(spCore\CDatabase::getInstance())->checkSession()) {
                    spcore\CSession::getInstance(spCore\CDatabase::getInstance())->writeSession();
                    $admin->admin_admin_loadAdminBoxes();
                    $myClass 	= new $myGenClass(spCore\CDatabase::getInstance(), $template, $session, $requestVar, $admin->permission);
                    $myFunction = spcore\CHelper::stripRequestVar($modul[0])."_".spcore\CHelper::stripRequestVar($modul[1]);
                    if(method_exists($myGenClass,$myFunction)) {
                        $myClass->$myFunction();
                    }else {
                        spcore\CTemplate::getInstance()->addContentBox("Error", "Function not found ,".$myGenClass."->".$myFunction."(); ");
                        spcore\CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__."::".__FUNCTION__ , null, null, "Function not found ,".$myGenClass."->".$myFunction."(); ");
                    }
                }else {
                    $admin->admin_admin_login($requestVar);
                }
            }else {
                spcore\CTemplate::getInstance()->addContentBox("Error","Class ".$myClass." not found !");
                spcore\CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__."::".__FUNCTION__ , null, null, "Class ".$myClass." not found !");
            }
        }else {
            $admin->admin_admin_login($requestVar);
        }
    }else if(isset($modul[0]) && file_exists(SP_CORE_DOC_ROOT .'/module/'.spcore\CHelper::stripRequestVar($modul[0])
            .'/admin/'.spcore\CHelper::stripRequestVar($modul[0]).'_admin_index.php')) {
        require_once ( SP_CORE_DOC_ROOT .'/module/'.spcore\CHelper::stripRequestVar($modul[0]).'/admin/'
                .spcore\CHelper::stripRequestVar($modul[0]).'_admin_index.php');
        if(function_exists(spcore\CHelper::stripRequestVar($modul[0])."_".spcore\CHelper::stripRequestVar($modul[1]))) {
            if($session->checkSession()) {
                $admin->admin_admin_loadAdminBoxes();
                eval(spcore\CHelper::stripRequestVar($modul[0])."_".spcore\CHelper::stripRequestVar($modul[1])
                        ."(\spcore\\CTemplate::getInstance(), \spCore\\CDatabase::getInstance(), \$requestVar, \$admin->permission);");
            }else {
                $admin->admin_admin_login($requestVar);
            }
        }else if (method_exists($admin,"admin_" . spcore\CHelper::stripRequestVar($modul[0]) . "_" . spcore\CHelper::stripRequestVar($modul[1]) )) {
            $myCall = "admin_" . spcore\CHelper::stripRequestVar($modul[0]) . "_" . spcore\CHelper::stripRequestVar($modul[1]);
            if(method_exists($admin,$myCall)) {
                $admin->$myCall();
            }else {
                spcore\CTemplate::getInstance()->addContentBox("Error", "Function not found ,".$myGenClass."->".$myFunction."(); ");
                spcore\CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__."::"
                        .__FUNCTION__ , null, null, "Function not found ,".$myGenClass."->".$myFunction."(); ");
            }
            //eval ("\$admin->admin_" . spcore\CHelper::stripRequestVar($modul[0]) . "_" . spcore\CHelper::stripRequestVar($modul[1]) . "();");
        }
    }else if (isset($modul[0]) && isset($modul[1]) && method_exists($admin,"admin_" . spcore\CHelper::stripRequestVar($modul[0]) 
            . "_" . spcore\CHelper::stripRequestVar($modul[1]) )) {
        $myCall = "admin_" . spcore\CHelper::stripRequestVar($modul[0]) . "_" . spcore\CHelper::stripRequestVar($modul[1]);
        if(method_exists($admin,$myCall)) {
            $admin->$myCall();
        }else {
            spcore\CTemplate::getInstance()->addContentBox("Error", "Function not found ,".$myGenClass."->".$myFunction."(); ");
            spcore\CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__."::".__FUNCTION__ , null, null, "Function not found ,".$myGenClass."->".$myFunction."(); ");
        }
        //eval ("\$admin->admin_" . spcore\CHelper::stripRequestVar($modul[0]) . "_" . spcore\CHelper::stripRequestVar($modul[1]) . "();");
    } else {
        $admin->admin_admin_login($requestVar);
    }
}
?>
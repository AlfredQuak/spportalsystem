<?php

/* spPortalSystem spcore\CPermissions.php
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

namespace spCore;

require_once SP_CORE_DOC_ROOT . '/includes/CLog.php';

/**
 * \ingroup systemcore
 * \brief
 * Basic permission class
 * \details
 * Type : Singelton
 * @author MisterIce
 */
class CPermissions {

    private static $instances = array();

    /**
     * \brief constructor
     * @param $this->db
     * @return none
     */
    protected function __construct() {

        $class = get_called_class();
        if (array_key_exists($class, self::$instances)) {
            trigger_error("Tried to construct  a second instance of class \"$class\"", E_USER_WARNING);
        }
    }

    private function __clone() {
        
    }

    /**
     * \brief give class instance
     * @param $this->db
     * @return object
     */
    public static function getInstance() {
        $class = get_called_class();
        if (array_key_exists($class, self::$instances) === false) {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }

    /**
     * \brief
     * Get all permission from table
     * \details
     * Load permission names and generate lang tags
     * @param $modulname
     * @return array for Admincenter
     */
    public function loadFromDatabase($modulname) {
        $giveBack = null;
        $sql = "SHOW FIELDS FROM sp_modul_" . $modulname . "_permissions";
        $result = CDatabase::getInstance()->query($sql);
        if ($result) {
            while ($res = CDatabase::getInstance()->fetch_array($result)) {
                if ($res['Field'] != "id") {
                    $gb['inputname'] = $res['Field'];
                    $gb['description'] = "{LANG_" . $modulname . "_permission_" . $res['Field'] . "}";
                    $giveBack[] = $gb;
                }
            }
        }
        return $giveBack;
    }

    /**
     * \brief
     * Get the group permission for modul foo
     *
     * @param $this->db
     * @param $modulname
     * @param $id groupid
     * @return $resultset
     */
    public function getModulGroupPermissionFrom($modulname, $id) {

        $res = null;
        $data = array();
        $data[$modulname] = null;

        $sql = "SELECT admin FROM sp_user_group WHERE id=" . CDatabase::getInstance()->checkValue($id);
        $result = CDatabase::getInstance()->query($sql);
        if ($result && CDatabase::getInstance()->num_rows($result) >= 1) {
            $data = CDatabase::getInstance()->fetch_array($result);
            $data = @unserialize($data[0]);
            if (!isset($data[$modulname])) {
                $data[$modulname] = null;
            }
            $sql = "SELECT * FROM sp_modul_" . CDatabase::getInstance()->checkValue($modulname) .
                    "_permissions WHERE id=" . CDatabase::getInstance()->checkValue($data[$modulname]['id']);
            $result = CDatabase::getInstance()->query($sql);
            if ($result && CDatabase::getInstance()->num_rows($result) > 0) {
                $res = CDatabase::getInstance()->fetch_assoc($result);
            }
            return $res;
        }
        if (function_exists("xdebug_time_index")) {
            CLog::getInstance()->log(SP_LOG_ERROR, (
                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                    "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() .
                    "::Line " . xdebug_call_line(), null, "Line: " . __LINE__ . " Permission not loaded ...");
        } else {
            CLog::getInstance()->log(SP_LOG_ERROR, (
                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                    "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission not loaded ...");
        }
        return false;
    }

    /**
     * \brief
     * Generate new Group
     * \details
     * Generate a new Groupe with all know permissions.<br>
     * Logic combine Database and PHP : <br>
     * \image html permission_addNewGroupe.png
     * \image latex permission_addNewGroupe.png
     * <br>
     * In sp_user field is serialize array the hold all groups for active user.
     * Only group id's where hold here.<br>
     * In sp_user_group in field admin is serialize array for permissions :
     * \code
     * array[$modulname]['admin']; // here is true or false for admincenter section or login
     * array[$modulname]['id'];    // here is the id for group permission for permission table from modulname
     * \endcode
     * Details in $detail add directly into sp_modul_$modulname_permssions.<br>
     * Sampel for $detail array:
     * \code
     * Array
     *    [portal] => Array
     *           [admin] => true
     *           [detail] => Array
     *                   [box_new] => 1
     *                   [box_edit] => 1
     *                   [side_new] => 1
     *                   [side_edit] => 1
     *                   [news_new] => 1
     *                   [news_edit] => 1
     * \endcode
     *
     * @param $this->db
     * @param $groupename
     * @param $detail
     * @return none
     */
    public function addNewGroupe($groupename, array $detail) {

        $sql = array();
        foreach ($detail as $myindex => $myvalue) {
            $groupArray[$myindex]['admin'] = $detail[$myindex]['admin'];

            if (!is_array($detail[$myindex]['detail'])) {
                $sql = "SHOW COLUMNS FROM sp_modul_" . $myindex . "_permissions";
                $result = CDatabase::getInstance()->query($sql);
                if ($result) {
                    while ($this->dbPermission = CDatabase::getInstance()->fetch_assoc($result)) {
                        if ($this->dbPermission['Field'] != 'id') {
                            if (isset($detail[$myindex]['detail'][$this->dbPermission['Field']])) {
                                $ti[] = $groupArray[$myindex]['detail'][$this->dbPermission['Field']];
                                $tv[] = '0';
                            } else {
                                $ti[] = $this->dbPermission['Field'];
                                $tv[] = '0';
                            }
                        }
                    }

                    $sql = "INSERT INTO sp_modul_" . $myindex . "_permissions (" .
                            implode(",", $ti) . ") VALUES ('" . implode("','", $tv) . "')";
                    CDatabase::getInstance()->query($sql);
                    $groupArray[$myindex]['id'] = CDatabase::getInstance()->insert_id();
                    $ti = null;
                    $tv = null;
                } else {
                    if (function_exists("xdebug_time_index")) {
                        CLog::getInstance()->log(SP_LOG_ERROR, (
                                isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                                "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                                xdebug_call_line(), null, "Line: " . __LINE__ . " Groupe add failed ...");
                    } else {
                        CLog::getInstance()->log(SP_LOG_ERROR, (
                                isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                                "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Groupe add failed ...");
                    }
                    return false;
                }
            } else {
                foreach ($detail[$myindex]['detail'] as $tableIndex => $tableValue) {
                    if ($tableIndex != "core") {
                        $ti[] = $tableIndex;
                        $tv[] = $tableValue;
                    }
                }
                if (sizeof($ti) > 0) {
                    $sql = "INSERT INTO sp_modul_" . $myindex . "_permissions (" .
                            implode(",", $ti) . ") VALUES ('" . implode("','", $tv) . "')";
                    CDatabase::getInstance()->query($sql);
                    $groupArray[$myindex]['id'] = CDatabase::getInstance()->insert_id();
                    $ti = null;
                    $tv = null;
                }
            }
        }
        $sql = "INSERT INTO sp_user_group (groupe_name, admin) VALUES ('" .
                $groupename . "','" . serialize($groupArray) . "')";
        CDatabase::getInstance()->query($sql);
    }

    /**
     * \brief
     * Update groupe permission
     * \details
     * Same as CPermission::addNewGroupe() with the differend groupid and update not insert <br>
     *
     * @param $grpId groupeid
     * @param $this->db
     * @param $groupename
     * @param $detail
     * @return bool
     */
    public function updateGroupe(array $detail, $grpid = null) {
        if ($grpid == null) {
            //self::$db->out_dbg("gruppen id ist nicht gesetzt");
            if (function_exists("xdebug_time_index")) {
                CLog::getInstance()->log(SP_LOG_ERROR, (
                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                        "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                        xdebug_call_line(), null, "Line: " . __LINE__ . " Groupe id not set ...");
            } else {
                CLog::getInstance()->log(SP_LOG_ERROR, (
                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                        "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Groupe id not set ...");
            }
            return false;
        }

        $activePermission = self::getGroupFromId($grpid);

        foreach ($detail as $index => $value) {
            if ($activePermission['modul'][$index]['id'] == 0) {
                $activePermission['modul'][$index]['id'] = 1;
            }
            if (!isset($activePermission['modul'][$index])) {
                if (is_array($detail[$index]['detail'])) {
                    foreach ($detail[$index]['detail'] as $dIndex => $dValue) {
                        $sqlIndex[] = $dIndex;
                        $sqlValues[] = $dValue;
                    }
                    $sql = "INSERT INTO sp_modul_" . $index . "_permissions (" .
                            implode(",", $sqlIndex) . ") VALUES ('" . implode("','", $sqlValues) . "')";
                    CDatabase::getInstance()->query($sql);
                    $activePermission['modul'][$index]['id'] = CDatabase::getInstance()->insert_id();
                } else {
                    $sql = "SHOW COLUMNS FROM sp_modul_" . $index . "_permissions";
                    $result = CDatabase::getInstance()->query($sql);
                    if ($result) {
                        while ($this->dbPermission = CDatabase::getInstance()->fetch_assoc($result)) {
                            if ($this->dbPermission['Field'] != 'id') {
                                if (isset($detail[$index]['detail'][$this->dbPermission['Field']])) {
                                    $sqlIndex[] = $detail[$index]['detail'][$this->dbPermission['Field']];
                                    $sqlValues[] = '0';
                                } else {
                                    $sqlIndex[] = $this->dbPermission['Field'];
                                    $sqlValues[] = '0';
                                }
                            }
                        }
                        $sql = "INSERT INTO sp_modul_" . $index . "_permissions (" .
                                implode(",", $sqlIndex) . ") VALUES ('" . implode("','", $sqlValues) . "')";
                        CDatabase::getInstance()->query($sql);
                        $activePermission['modul'][$index]['id'] = CDatabase::getInstance()->insert_id();
                        $sqlIndex = null;
                        $sqlValue = null;
                    } else {
                        if (function_exists("xdebug_time_index")) {
                            CLog::getInstance()->log(SP_LOG_ERROR, (
                                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                                    "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                                    xdebug_call_line(), null, "Line: " . __LINE__ . " Update group failed ...");
                        } else {
                            CLog::getInstance()->log(SP_LOG_ERROR, (
                                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                                    "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Update group failed ...");
                        }
                        return false;
                    }
                }
                $activePermission['modul'][$index]['admin'] = $detail[$index]['admin'];
            } else {
                $sql = "SELECT * FROM sp_modul_" . $index . "_permissions WHERE id=" . $activePermission['modul'][$index]['id'];
                $result = CDatabase::getInstance()->query($sql);

                if ($result) {
                    $activePermission['modul'][$index]['admin'] = $detail[$index]['admin'];
                    $this->dbPermission = CDatabase::getInstance()->fetch_assoc($result);
                    if (is_array($this->dbPermission)) {
                        foreach ($this->dbPermission as $pIndex => $pValue) {
                            if ($pIndex != 'id') {
                                if (isset($detail[$index]['detail'][$pIndex])) {
                                    $sqlUpdate[] = $pIndex . '=' . $detail[$index]['detail'][$pIndex];
                                } else {
                                    $sqlUpdate[] = $pIndex . '=0';
                                }
                            }
                        }
                        $sql = "UPDATE sp_modul_" . $index . "_permissions SET " . implode(",", $sqlUpdate) .
                                " WHERE id=" . $activePermission['modul'][$index]['id'];
                        CDatabase::getInstance()->query($sql);
                        $sqlUpdate = null;
                    } else {
                        if (is_array($detail)) {
                            foreach ($detail[$index]['detail'] as $inIndex => $inValue) {
                                $sqlIndex[] = $inIndex;
                                $sqlValues[] = $inValue;
                            }
                        }
                        $sql = "INSERT INTO sp_modul_" . $index . "_permissions (" .
                                implode(",", $sqlIndex) . ") VALUES ('" . implode("','", $sqlValues) . "')";
                        CDatabase::getInstance()->query($sql);
                        $activePermission['modul'][$index]['id'] = CDatabase::getInstance()->insert_id();
                        $sqlIndex = null;
                        $sqlValue = null;
                    }
                } else {
                    return false;
                }
            }
        }
        $sql = 'UPDATE sp_user_group SET admin="' .
                CDatabase::getInstance()->checkValue(serialize($activePermission['modul'])) .
                '" WHERE id=' . $grpid;
        CDatabase::getInstance()->query($sql);
        // hier nach user gruppe schaun
        //$activePermission = self::getGroupFromId($grpid);#
        //CDatabase::getInstance()->out_dbg($activePermission);
        //CDatabase::getInstance()->out_dbg($detail);
        //return false;
        return true;
    }

    /**
     * \brief
     * Return all groups
     * \detail
     * Return all groups from database table sp_user_group
     *
     * @param $this->db
     * @return
     * \code
     * Array
     *    [groupname] => Array
     *            [id] => 1
     *            [name] => groupname
     *            [modul] => Array
     *               [modulname] => Array
     *                       [admin] => true    // access to modul configure
     *                       [id] => 10         // id for detail permissions
     * \endcode
     */
    public function getAllGroups() {

        $sql = "SELECT * FROM sp_user_group";
        $result = CDatabase::getInstance()->query($sql);
        while ($res = CDatabase::getInstance()->fetch_array($result)) {
            $retVal[$res['groupe_name']]['id'] = $res['id'];
            $retVal[$res['groupe_name']]['name'] = $res['groupe_name'];
            $retVal[$res['groupe_name']]['modul'] = unserialize($res['admin']);
        }
        return $retVal;
    }

    /**
     * \brief
     * Return group by id
     * \detail
     * Return group from database table sp_user_group
     *
     * @param $this->db
     * @param $id
     * @return
     * \code
     * Array
     *            [id] => 1
     *            [name] => groupname
     *            [modul] => Array
     *               [modulname] => Array
     *                       [admin] => true    // access to modul configure
     *                       [id] => 10         // id for detail permissions
     * \endcode
     */
    public function getGroupFromId($id) {

        $sql = "SELECT * FROM sp_user_group WHERE id=" . $id;
        $result = CDatabase::getInstance()->query($sql);
        while ($res = CDatabase::getInstance()->fetch_array($result)) {
            $retVal['id'] = $res['id'];
            $retVal['name'] = $res['groupe_name'];
            $retVal['modul'] = unserialize($res['admin']);
        }
        return $retVal;
    }

    /**
     * \brief
     * Get permissions for user
     * \detail
     * Get permissions for active user or give user id
     *
     * @param unknown_type $this->db
     * @param unknown_type $uid
     * @return unknown
     */
    public function getPermissionForUser($uid = NULL) {
        $mypermissions = NULL;
        if ($uid == NULL) {
            if (isset($_SESSION['user_id'])) {
                $uid = $_SESSION['user_id'];
            } else {
                if (function_exists("xdebug_time_index")) {
                    CLog::getInstance()->log(SP_LOG_ERROR, (
                            isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                            "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                            xdebug_call_line(), null, "Line: " . __LINE__ . " Permission not loaded ...");
                } else {
                    CLog::getInstance()->log(SP_LOG_ERROR, (
                            isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ .
                            "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission not loaded ...");
                }
                return false;
            }
        }
        $sql = "SELECT * FROM sp_user WHERE id=" . $uid;
        $result = CDatabase::getInstance()->query($sql);
        if ($result) {
            $res = CDatabase::getInstance()->fetch_array($result);
            $sql = "SELECT * FROM sp_user_group WHERE id=" . implode(" OR id=", unserialize($res['user_groups']));
            $gr_result = CDatabase::getInstance()->query($sql);
            while ($gr_res = CDatabase::getInstance()->fetch_assoc($gr_result)) {
                $tmp = unserialize($gr_res['admin']);
                foreach ($tmp as $index => $value) {
                    if (isset($mypermissions[$index]['admin'])) {
                        if ($mypermissions[$index]['admin'] == 'false' && $value != 'false') {
                            $mypermissions[$index] = $value;
                        } else {
                            continue;
                        }
                    } else {
                        $mypermissions[$index] = $value;
                    }
                    $myTmpPermission = $this->getModulGroupPermissionFrom($index, $gr_res['id']);
                    if (is_array($myTmpPermission)) {
                        foreach ($myTmpPermission as $tmpPermissionIndex => $tmpPermissionValue) {
                            if (isset($mypermissions[$index]['detail'][$tmpPermissionIndex])) {
                                if ($mypermissions[$index]['detail'][$tmpPermissionIndex] == 0 && $tmpPermissionValue != 0) {
                                    $mypermissions[$index]['detail'][$tmpPermissionIndex] = $tmpPermissionValue;
                                } else {
                                    continue;
                                }
                            } else {
                                $mypermissions[$index]['detail'][$tmpPermissionIndex] = $tmpPermissionValue;
                            }
                        }
                    }
                }
            }
        } else {
            if (function_exists("xdebug_time_index")) {
                CLog::getInstance()->log(SP_LOG_WARNING, (
                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                        "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                        xdebug_call_line(), null, "Line: " . __LINE__ . " Permission not loaded ...");
            } else {
                CLog::getInstance()->log(SP_LOG_WARNING, (
                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                        "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission not loaded ...");
            }
            return false;
        }
        if (function_exists("xdebug_time_index")) {
            CLog::getInstance()->log(SP_LOG_NOTICE, (
                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ .
                    "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                    xdebug_call_line(), null, "Line: " . __LINE__ . " Permission loaded ...");
        } else {
            CLog::getInstance()->log(SP_LOG_NOTICE, (
                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ .
                    "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission loaded ...");
        }
        return $mypermissions;
    }

}

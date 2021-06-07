<?php

/* sploindyPortal CUser.php
 * Created on 25.05.2009 from misterice
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

require_once 'CSession.php';

/**
 * \ingroup systemcore
 * \author Daniel Stecker <dstecker@sploindy.de>
 * \date May 2009
 * \since v0.1
 * \brief Usermanagemant
 * \details
 * Here you can add, remove and edit your new User
 */
class CUser {

    private $reqVar;

    public function __construct($rVar) {
        $reqVar = $rVar;
    }

    /**
     * \brief check if user is in database.
     * \details
     * True is in, False is not in database.
     * @param $u_name
     * @param $u_pwd
     * @return boolean
     */
    public function userLogin($u_name, $u_pwd) {

        if (!isset($u_name)) {
            return false;
        }
        if (!isset($u_pwd)) {
            return false;
        }

        $sql = "	SELECT
                                *
                        FROM
                                sp_user
                        WHERE
                                name ='" . CDatabase::getInstance()->checkValue($u_name) . "'
                        AND
                                password = '" . CDatabase::getInstance()->checkValue(md5($u_pwd)) . "'";

        $result = CDatabase::getInstance()->query($sql);
        $this->userData = CDatabase::getInstance()->fetch_object($result);

        if (!is_object($this->userData)) {
            return false;
        } else {
            CSession::getInstance()->startSession($this->userData->name, $this->userData->id);
            CSession::getInstance()->writeSession();
            return true;
        }
    }

    /**
     * \brief Log out the current User
     *
     * @return boolean
     */
    public function userLogOut() {
        if (CSession::getInstance()->checkSession()) {
            CSession::getInstance()->deleteSession();
            return true;
        }
        return false;
    }

    /**
     * \brief
     * Check if session is active for current User
     *
     * @return boolean
     */
    public function isActive() {
        if (CSession::getInstance()->checkSession()) {
            CSession::getInstance()->writeSession();
            return true;
        } else {
            return false;
        }
    }

    /**
     * \brief Adding a new User into Database
     * \details
     * $user must be an array
     * \code
     * $user['u_rfirstname']= "John";
     * $user['u_rlastname'] = "Doo";
     * $user['u_name'] 		= "username";
     * $user['u_password'] 	= "userpassword"; md5 encrypted
     * $user['u_email'] 	= "useremail";
     * $user['u_groups']    = array();
     * \endcode
     * Groups add as serialize array into database. The user starts with no activ modus.
     * @param array $user
     * @return boolean
     * */
    public function addUser(array $user) {
        if (CSession::getInstance()->checkSession()) {
            CSession::getInstance()->writeSession();
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
                            '" . CDatabase::getInstance()->checkValue($user['u_rfirstname']) . "',
                            '" . CDatabase::getInstance()->checkValue($user['u_rlastname']) . "',
                            '" . CDatabase::getInstance()->checkValue($user['u_name']) . "',
                            '" . md5(CDatabase::getInstance()->checkValue($user['u_password'])) . "',
                            '" . CDatabase::getInstance()->checkValue($user['u_email']) . "',
                            '" . serialize($user['u_groups']) . "',
                            " . (isset($user['u_startActiv']) ? "'1'" : "'0'") . "
                    ) ";
            $result = CDatabase::getInstance()->query($sql);
            if ($result != null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * \todo
     * @return unknown_type
     */
    public function getUserGroups($id = NULL) {
        if ($id === NULL) {
            $sql = "SELECT * FROM sp_user WHERE id=" . CDatabase::getInstance()->checkValue($_SESSION['user_id']);
            $result = CDatabase::getInstance()->query($sql);
            while ($res = CDatabase::getInstance()->fetch_array($result)) {
                $groupArray = unserialize($res['user_groups']);
                $sql = "SELECT * FROM sp_user_group WHERE id=" . implode(" OR id=", $groupArray);
                $gr_result = CDatabase::getInstance()->query($sql);
                while ($gr_res = CDatabase::getInstance()->fetch_array($gr_result)) {
                    $mygroups[$gr_res['id']] = $gr_res['groupe_name'];
                }
            }
        } else {
            $sql = "SELECT * FROM sp_user WHERE id=" . CDatabase::getInstance()->checkValue($id);
            $result = CDatabase::getInstance()->query($sql);
            while ($res = CDatabase::getInstance()->fetch_array($result)) {
                $groupArray = unserialize($res['user_groups']);
                $sql = "SELECT * FROM sp_user_group WHERE id=" . implode(" OR id=", $groupArray);
                $gr_result = CDatabase::getInstance()->query($sql);
                while ($gr_res = CDatabase::getInstance()->fetch_array($gr_result)) {
                    $mygroups[$gr_res['id']] = $gr_res['groupe_name'];
                }
            }
        }
        return $mygroups;
    }

    /**
     * \todo
     * @return unknown_type
     */
    public function getUserStatus() {
        return $user_status;
    }

    /**
     * \todo
     * @return unknown_type
     */
    public function getUserName() {
        return $user_name;
    }

    /**
     * \todo
     * @param $u_id
     * @param $u_name
     * @return unknown_type
     */
    public function setUserGroup($u_id, $u_name) {
        
    }

    /**
     * \todo
     * @param $u_id
     * @param $u_name
     * @return unknown_type
     */
    public function setUserName($u_id, $u_name) {
        
    }

    /**
     * \brief
     * Load userdata
     *
     * @param $userid
     * @return object
     */
    public function getUserDataFromId($id) {
        $sql = "SELECT * FROM sp_user WHERE id=" . CDatabase::getInstance()->checkValue($id);
        $result = CDatabase::getInstance()->query($sql);
        if ($result) {
            $userData = CDatabase::getInstance()->fetch_assoc($result);
            $groupArray = unserialize($userData['user_groups']);
            $sql = "SELECT * FROM sp_user_group WHERE id=" . implode(" OR id=", $groupArray);
            $gr_result = CDatabase::getInstance()->query($sql);
            while ($gr_res = CDatabase::getInstance()->fetch_array($gr_result)) {
                $mygroups[$gr_res['id']] = $gr_res['groupe_name'];
            }

            $userData['user_groups'] = $mygroups;
            return $userData;
        } else {
            return false;
        }
    }

}

?>

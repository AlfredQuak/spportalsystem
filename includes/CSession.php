<?php

/* spPortalSystem spcore\CSession.php
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

/**
 * @author Daniel Stecker <dstecker@sploindy.de>
 * @date Oktober 2008
 * \ingroup systemcore
 * \brief Basic session class
 * \details
 * This is the system basic session class. The session managemant<br>
 * is database based and combined session id with your ip.<br>
 * Type : Singelton
 */
final class CSession {

    private $sessObj;
    private static $db;
    public static $instance = null;

    /**
     * \brief give class instance
     * @param $db
     * @return object
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CSession();
        }
        return self::$instance;
    }

    /**
     * \brief constructor
     * @param $db
     * @return none
     */
    protected function __construct() {
        if (self::$instance == null) {
            if (session_id() == "") {
                session_start();
            }
            // Only attempt DB cleanup if the database connection is available
            if (CDatabase::getInstance()->getConnection() !== false) {
                $sql = "DELETE FROM sp_session WHERE session_time < " . ((time() + 5) - (15 * 60));
                CDatabase::getInstance()->query($sql);
            }
        } else {
            if (session_id() == "") {
                session_start();
            }
        }
    }

    private function __clone() {
        
    }

    public function getUserName() {
        if (isset($_SESSION['user_name'])) {
            return $_SESSION['user_name'];
        } else {
            return false;
        }
    }

    public function getUserID() {
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        } else {
            return false;
        }
    }

    public function setLang($lang) {
        $_SESSION['user_lang'] = $lang;
    }

    public function getLang() {
        if (isset($_SESSION['user_lang'])) {
            return $_SESSION['user_lang'];
        } else {
            return false;
        }
    }

    /**
     * \brief Session starts here
     * \details
     * Here you start your session, only phpsession will start here.
     *
     * @param $userName
     * @param $userId
     * @return none
     */
    public function startSession($userName, $userId) {
        $_SESSION['user_name'] = $userName;
        $_SESSION['user_id'] = $userId;
        $xdebugDevelop = function_exists('xdebug_call_class') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
        if ($xdebugDevelop) {
            CLog::getInstance()->log(SP_LOG_NOTICE, (
                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ .
                    "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() .
                    "::Line " . xdebug_call_line(), null, "Line: " . __LINE__ . " Session started...");
        } else {
            CLog::getInstance()->log(SP_LOG_NOTICE, (
                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ .
                    "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session started...");
        }
    }

    /**
     * \brief
     * Check if Session is active
     * \details
     * If your active status is in session set to inactive session data delete <br>
     * and you logged out .
     * @return boolean
     */
    public function checkSession() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {

            $sql = "SELECT * FROM sp_session WHERE user_id = '" . $_SESSION['user_id'] . "'";
            $result = CDatabase::getInstance()->query($sql);

            if ($result === null) {
                $this->deleteSession();
                $xdebugDevelop = function_exists('xdebug_call_class') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
                if ($xdebugDevelop) {
                    CLog::getInstance()->log(SP_LOG_WARNING, (
                            isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                            "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                            xdebug_call_line(), null, "Line: " . __LINE__ . " No session ...");
                } else {
                    CLog::getInstance()->log(SP_LOG_WARNING, (
                            isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                            "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " No session ...");
                }
                return false;
            } else {
                $this->sessObj = CDatabase::getInstance()->fetch_object($result);
                if (is_object($this->sessObj) && ($this->sessObj->user_sess_id == session_id())
                        && ($_SESSION['user_name'] == $this->sessObj->user_name)
                        && ($this->getClientIp() == $this->sessObj->user_ip)) {

                    $sql = "SELECT active FROM sp_user WHERE id=" . $_SESSION['user_id'];
                    $result = CDatabase::getInstance()->query($sql);
                    if ($result === null) {
                        $xdebugDevelop = function_exists('xdebug_call_class') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
                        if ($xdebugDevelop) {
                            CLog::getInstance()->log(SP_LOG_WARNING, (
                                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                                    "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                                    xdebug_call_line(), null, "Line: " . __LINE__ . " No session ...");
                        } else {
                            CLog::getInstance()->log(SP_LOG_WARNING, (
                                    isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                                    "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " No session ...");
                        }
                        return false;
                    } else {
                        $userData = CDatabase::getInstance()->fetch_object($result);
                        if ($userData->active == '0') {
                            $this->deleteSession();
                            $xdebugDevelop = function_exists('xdebug_call_class') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
                            if ($xdebugDevelop) {
                                CLog::getInstance()->log(SP_LOG_WARNING, (
                                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                                        "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                                        xdebug_call_line(), null, "Line: " . __LINE__ . " No session ...");
                            } else {
                                CLog::getInstance()->log(SP_LOG_WARNING, (
                                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                                        "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " No session ...");
                            }
                            return false;
                        } else {
                            $xdebugDevelop = function_exists('xdebug_call_class') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
                            if ($xdebugDevelop) {
                                CLog::getInstance()->log(SP_LOG_NOTICE, (
                                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ .
                                        "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                                        xdebug_call_line(), null, "Line: " . __LINE__ . " Session checked ...");
                            } else {
                                CLog::getInstance()->log(SP_LOG_NOTICE, (
                                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ .
                                        "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session checked ...");
                            }
                            return true;
                        }
                    }
                } else {
                    $this->deleteSession();
                    CLog::getInstance()->log(SP_LOG_NOTICE, (
                            isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ .
                            "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session delete ...");
                    $xdebugDevelop = function_exists('xdebug_call_class') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
                    if ($xdebugDevelop) {
                        CLog::getInstance()->log(SP_LOG_WARNING, (
                                isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                                "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() .
                                "::Line " . xdebug_call_line(), null, "Line: " . __LINE__ . " Session delete ...");
                    } else {
                        CLog::getInstance()->log(SP_LOG_WARNING, (
                                isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                                "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Session delete ...");
                    }
                    return false;
                }
            }
        } else {
            $xdebugDevelop = function_exists('xdebug_call_class') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
            if ($xdebugDevelop) {
                CLog::getInstance()->log(SP_LOG_WARNING, (
                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                        "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " .
                        xdebug_call_line(), null, "Line: " . __LINE__ . " No session ...");
            } else {
                CLog::getInstance()->log(SP_LOG_WARNING, (
                        isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ .
                        "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " No session ...");
            }
            return false;
        }
    }

    /**
     * \brief Write session into database
     * \details
     * If existing session into table, replace it with new session.<br>
     * (refresch)
     *
     * @return none
     */
    public function writeSession() {

        $sql = "REPLACE INTO sp_session (
					user_id,
					user_name,
					user_sess_id,
					user_ip,
                                        session_time
				) VALUES (
					'" . CDatabase::getInstance()->checkValue($_SESSION['user_id']) . "',
					'" . CDatabase::getInstance()->checkValue($_SESSION['user_name']) . "',
					'" . CDatabase::getInstance()->checkValue(session_id()) . "',
					'" . CDatabase::getInstance()->checkValue($this->getClientIp()) . "',
                                        '" . CDatabase::getInstance()->checkValue(time()) . "'
				) ";
        CDatabase::getInstance()->query($sql);
    }

    /**
     * \brief Delete session
     * \details
     * Delete own session in database. <br />
     * Automatik user log out !
     */
    public function deleteSession() {
        if (!empty($_SESSION['user_id'])) {
            $sql = "DELETE FROM sp_session WHERE user_id = " .
                    CDatabase::getInstance()->checkValue($_SESSION['user_id']) .
                    " OR user_ip = '" . $this->getClientIp() . "'";
            CDatabase::getInstance()->query($sql);
        }
        if (is_array($_SESSION)) {
            foreach ($_SESSION as $x) {
                unset($_SESSION[$x]);
            }
        }
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
            );
        }
        $_SESSION = array();
        session_unset();
        @session_destroy();
    }

    /**
     * \brief
     * Get the client ip from user.
     *
     * @return IP
     */
    public function getClientIp() {
        $ip = "";
        if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        }
        return $ip;
    }

    /**
     * \brief Helper function
     * \details
     * Helper function, gets the actually user session object.
     * \code
     * $_SESSION['user_name']
     * $_SESSION['user_id']
     * \endcode
     * @return $_SESSION
     */
    public function getUserObj() {
        return $_SESSION;
    }

}

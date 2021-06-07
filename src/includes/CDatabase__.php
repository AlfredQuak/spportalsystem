<?php

/* spPortalSystem CDatabase.php
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
 * \author Daniel Stecker <dstecker@sploindy.de>
 * \date Oct. 2008
 * \ingroup systemcore
 * \brief
 * Wrapped for database connection
 * \details
 * Wrapped MySql database functions and give some debug functions.
 * */
final class CDatabase {

    private static $instance = null;

    private function __clone() {
        
    }

    private static $connection = null;

    /**
     * /brief give class instance
     * @param $g_system
     * @return object
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CDatabase();
        }
        return self::$instance;
    }

    /**
     * \brief Construcor
     * \details
     * Init and connect to database
     * */
    protected function __construct() {
        try {
            self::$connection = mysqli_connect(SP_CORE_DB_SERVER, SP_CORE_DB_USER, SP_CORE_DB_PASS,SP_CORE_DB_DATABASE);

            if (!self::$connection) {
                CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, self::$connection->error);
                return false;
            } elseif (!self::$connection->select_db(SP_CORE_DB_DATABASE)) {
                CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, self::$connection->error);
                return false;
            }
            self::$connection->set_charset(SP_CORE_ENCODING);
        } catch (Exception $e) {
            CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, $e->getMessage());
        }
    }

    public function getConnection() {
        return self::$connection;
    }

    public function fetch_array($result, $option = null) {
        if ($option != null) {
            return mysqli_fetch_array($result, $option);
        } else {
            return mysqli_fetch_array($result, MYSQLI_ASSOC);
        }
    }

    public function fetch_object($result) {
        return mysqli_fetch_object($result);
    }

    public function fetch_assoc($result) {
        return mysqli_fetch_assoc($result);
    }

    public function query($query, $logging = true) {
        if (SP_LOG_SQL && $logging) {
            if (function_exists("xdebug_time_index")) {
                CLog::getInstance()->log(SP_LOG_DEBUG, SP_LOG_DEBUG, __CLASS__ .
                        "::" . __FUNCTION__, xdebug_call_class() . "->" .
                        xdebug_call_function() . "::Line " . xdebug_call_line(), null, $query);
            } else {
                CLog::getInstance()->log(SP_LOG_DEBUG, SP_LOG_DEBUG, __CLASS__ .
                        "::" . __FUNCTION__, null, null, $query);
            }
        }

        $result = self::$connection->query($query);
        if (!$result && $logging) {
            if (function_exists("xdebug_time_index")) {
                CLog::getInstance()->log(SP_LOG_ERROR, null, __CLASS__ . "::" .
                        __FUNCTION__, xdebug_call_class() . "->" .
                        xdebug_call_function() . "::Line " . xdebug_call_line(), null, $this->getError());
            } else {
                CLog::getInstance()->log(SP_LOG_ERROR, null, __CLASS__ . "::" .
                        __FUNCTION__, null, null, $this->getError());
            }
        }
        return $result;
    }

    public function num_rows($result) {
        return $result->num_rows;
    }

    public function checkValue($query) {
        if (get_magic_quotes_gpc()) {
            if (function_exists("mysql_real_escape_string")) {
                return mysql_real_escape_string(stripslashes($query));
            } else {
                return $query;
            }
        } else {
            if (function_exists("mysql_real_escape_string")) {
                return @mysql_real_escape_string($query);
            } else {
                return addslashes($query);
            }
        }
    }

    /**
     * \brief
     * Import sql file and execute sql querys
     * */
    public function importFile($file) {
        $import = file_get_contents($file);
        $import = preg_replace("%/\*(.*)\*/%Us", '', $import);
        $import = preg_replace("%^--(.*)\n%mU", '', $import);
        $import = preg_replace("%^$\n%mU", '', $import);
        $import = explode(";", $import);

        if (is_array($import)) {
            foreach ($import as $imp) {
                if ($imp != '' && $imp != ' ') {
                    $this->query($imp);
                } else {
                    $this->query($imp);
                }
            }
        } else {
            if ($import != '' && $import != ' ') {
                $this->query($import);
            } else {
                $this->query($import);
            }
        }
    }

    /**
     * Get the last insert ID
     * @return mysql_insert_id();
     */
    public function insert_id() {
        return mysql_insert_id();
    }

    public function getError() {
        CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, "" . mysql_errno() . " : " . mysql_error());
        return "" . mysql_errno() . " : " . mysql_error();
    }

    function __destruct() {
        if (self::$connection != null) {
            @mysql_close(self::$connection);
        }
    }

}

?>
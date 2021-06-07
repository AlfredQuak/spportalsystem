<?php

namespace spCore;

final class CDatabase
{

    private static CDatabase $instance;

    private function __clone()
    {

    }

    private $connection = null;

    /**
     * /brief give class instance
     * @param $g_system
     * @return object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new CDatabase();
        }
        return self::$instance;
    }

    /**
     * \brief Construcor
     * \details
     * Init and connect to database
     *
     * @throws \ErrorException
     */
    public function __construct()
    {
        try {
            $this->connection = mysqli_connect(SP_CORE_DB_SERVER, SP_CORE_DB_USER, SP_CORE_DB_PASS);
            $throw_ifError = static function () {
                CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, mysqli_error());
                throw new \ErrorException("Database Error");
            };
            if (!$this->connection) {
                $throw_ifError();
            } elseif (!mysqli_select_db(SP_CORE_DB_DATABASE, $this->connection)) {
                $throw_ifError();
            }
            mysqli_set_charset(SP_CORE_ENCODING, $this->connection);
        } catch (Exception $e) {
            CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function fetch_array($result, $option = null)
    {
        if ($option != null) {
            return @mysqli_fetch_array($result, $option);
        } else {
            return @mysqli_fetch_array($result);
        }
    }

    public function fetch_object($result)
    {
        return mysqli_fetch_object($result);
    }

    public function fetch_assoc($result)
    {
        return mysqli_fetch_assoc($result);
    }

    public function query($query, $logging = true)
    {
        if (SP_LOG_SQL && $logging) {
            if (function_exists("xdebug_time_index")) {
                CLog::getInstance()->log(SP_LOG_DEBUG, SP_LOG_DEBUG, __CLASS__ . "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " . xdebug_call_line(), null, $query);
            } else {
                CLog::getInstance()->log(SP_LOG_DEBUG, SP_LOG_DEBUG, __CLASS__ . "::" . __FUNCTION__, null, null, $query);
            }
        }

        $result = @mysqli_query($query);
        if (!$result && $logging) {
            if (function_exists("xdebug_time_index")) {
                CLog::getInstance()->log(SP_LOG_ERROR, null, __CLASS__ . "::" . __FUNCTION__, xdebug_call_class() . "->" . xdebug_call_function() . "::Line " . xdebug_call_line(), null, $this->getError());
            } else {
                CLog::getInstance()->log(SP_LOG_ERROR, null, __CLASS__ . "::" . __FUNCTION__, null, null, $this->getError());
            }
        }
        return $result;
    }

    public function num_rows($result)
    {
        return mysqli_num_rows($result);
    }

    public function checkValue($query)
    {
        if (function_exists("mysql_real_escape_string")) {
            return mysqli_real_escape_string(stripslashes($query));
        } else {
            return $query;
        }
    }

    /**
     * \brief
     * Import sql file and execute sql querys
     * */
    public function importFile($file)
    {
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

    public function insert_id()
    {
        return mysqli_insert_id();
    }

    public function getError()
    {
        CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, "" . mysqli_errno() . " : " . mysqli_error());
        return "" . mysqli_errno() . " : " . mysqli_error();
    }

    function __destruct()
    {
        if ($this->connection != null) {
            @mysqli_close($this->connection);
        }
    }

}

?>
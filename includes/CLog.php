<?php
/* spPortalSystem CLog.php
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

require_once 'CHelper.php';

class WriteLog {

    var $FunctionLvl = 1;
    var $usrLvl = 1;
    var $ip;
    var $callerMtd = "tescht";
    var $debugInf = "1";
    var $debugUsr = "1";
    var $debugMsg = "1";

}

/**
 * Description of CLog
 *
 * @author ds
 */
class CLog {

    private static $instance = null;
    private static $stringOut = array();
    private static $stringWrite = array();
    private static $soapClientLogger;
    private static $soapClient;
    private static $logDb;

    protected function __construct() {
        if (SP_CORE_LOG_WEBSERVICE) {
            self::$soapClient = new \SoapClient(SP_CORE_WEBSERVICE_URL,
                            array("style" => SOAP_RPC, "use" => SOAP_ENCODED));
            self::$soapClientLogger = new WriteLog();
        }
        if (SP_CORE_LOG_MYSQL) {
            require_once SP_CORE_DOC_ROOT . '/includes/CDatabase.php';
            self::$logDb = new CDatabase();
        }
    }

    private function __clone() {
        
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CLog();
        }
        return self::$instance;
    }

    public function log($loglvl, $userlvl, $function, $call, $user, $message) {
        if ($user == null) {
            if (isset($_SESSION['user_name'])) {
                $user = $_SESSION['user_name'];
            } else {
                $user = "system";
            }
        }
        if ($userlvl == null) {
            if (isset($_SESSION['user_dblvl'])) {
                $userlvl = $_SESSION['user_dblvl'];
            } else {
                $userlvl = SP_CORE_DEBUG;
            }
        }
        if ($call == null) {
            $xdebugDevelop = function_exists('xdebug_call_class') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
            if ($xdebugDevelop) {
                $call = "[" . xdebug_call_class() . "->" . xdebug_call_function() . "::Line " . xdebug_call_line() . "] ";
            } else {
                $call = "";
            }
        }
        if (SP_CORE_DEBUG >= $loglvl || (SP_CORE_DEBUG >= $loglvl && $userlvl >= $loglvl)) {
            if (SP_CORE_LOG_WEBSERVICE) {
                $this->logService($loglvl, $userlvl, $function, $call, $user, $message);
            }
            if (SP_CORE_LOG_FILE) {
                $this->logFile($loglvl, $userlvl, $function, $call, $user, $message);
            }
            if (SP_CORE_LOG_WEB) {
                $this->logWeb($loglvl, $userlvl, $function, $call, $user, $message);
            }
            if (SP_CORE_LOG_MYSQL) {
                $this->logMySql($loglvl, $userlvl, $function, $call, $user, $message);
            }
        }
    }

    public function logMySql($loglvl, $userlvl, $function, $call, $user, $message) {
        $sql = "INSERT INTO `sp_logging` (
                                    `log_ip`,
                                    `log_functionlvl`,
                                    `log_usrlvl`,
                                    `log_callMethod`,
                                    `log_info`,
                                    `log_user`,
                                    `log_message`
                                ) VALUES (
                                    '" . $_SERVER['REMOTE_ADDR'] . "',
                                    '" . self::$logDb->checkValue($loglvl) . "',
                                    '" . self::$logDb->checkValue($userlvl) . "',
                                    '" . self::$logDb->checkValue($call) . "',
                                    '" . self::$logDb->checkValue($function) . "',
                                    '" . self::$logDb->checkValue($user) . "',
                                    '" . self::$logDb->checkValue($message) . "'
                                )";
        self::$logDb->query($sql, false);
    }

    public function logWeb($loglvl, $userlvl, $function, $call, $user, $message) {
        $fontCol = "<span>";
        switch ($loglvl) {
            case SP_LOG_NOTICE:
                $fontCol = '<span style="color:#dd6;">NOTICE ';
                break;
            case SP_LOG_WARNING:
                $fontCol = '<span class="warning" style="color:orange;">WARNING ';
                break;
            case SP_LOG_ERROR:
                $fontCol = '<span class="error" style="color:#d66">ERROR ';
                break;
            case SP_LOG_DEBUG:
                $fontCol = '<span>DEBUG ';
                break;
            case SP_LOG_OWN:
                $fontCol = '<span style="color:black">OWN ';
                break;
            default:
                $fontCol = '<span style="color:black">DEBUG ';
                break;
        }
        self::$stringOut[] = $fontCol . "*#*user:" . $user . "*#*function: " . $function . "*#*call:" . $call . "*#*msg: " . $message . "</span >";
    }

    private function logService($loglvl, $userlvl, $function, $call, $user, $message) {
        try {
            self::$soapClientLogger->FunctionLvl = $loglvl;
            self::$soapClientLogger->usrLvl = $userlvl;
            self::$soapClientLogger->ip = $_SERVER['REMOTE_ADDR'];
            self::$soapClientLogger->callerMtd = $function;
            self::$soapClientLogger->debugInf = $call;
            self::$soapClientLogger->debugUsr = $user;
            self::$soapClientLogger->debugMsg = $message;

            self::$soapClient->WriteClientLog(self::$soapClientLogger);
        } catch (\SoapFault $e) {
            print ($e);
        }
    }

    private function logFile($loglvl, $userlvl, $function, $call, $user, $message) {
        switch ($loglvl) {
            case SP_LOG_NOTICE:
                $fontCol = 'NOTICE ';
                break;
            case SP_LOG_WARNING:
                $fontCol = 'WARNING ';
                break;
            case SP_LOG_ERROR:
                $fontCol = 'ERROR ';
                break;
            case SP_LOG_DEBUG:
                $fontCol = 'DEBUG ';
                break;
            case SP_LOG_OWN:
                $fontCol = 'OWN ';
                break;
            default:
                $fontCol = 'DEBUG ';
                break;
        }
        self::$stringWrite[] = $fontCol . " : *#*user:" . $user . "*#*function: " . $function . "*#*call:" . $call . "*#*msg: " . $message . "\r\n";
    }

    function __destruct() {
        $requestVar = CHelper::getInstance()->getRequestVar();

        if (SP_CORE_LOG_FILE && ($_SERVER["REMOTE_ADDR"] != "10.10.144.14")) {
            $datei = fopen(SP_CORE_DEBUG_LOGFILE_PATH, "a");

            $xdebugDevelop = function_exists('xdebug_time_index') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
            if ($xdebugDevelop) {
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t----------------------------------------------------------\r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ [NEW SIDE CLICK ] -----------------------\r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Modul\t\t: " . (isset($requestVar['modul']) ? $requestVar['modul'] : "No modul call") . " \r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Action\t\t: " . (isset($requestVar['action']) ? $requestVar['action'] : "No action call") . " \r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ IP\t\t: " . $_SERVER["REMOTE_ADDR"] . " \r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Script Time (sec)\t: " . xdebug_time_index() . "\r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ URL\t\t: " . $_SERVER['REQUEST_URI'] . "\r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Memory usage\t: " . xdebug_memory_usage() . "\r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Memory peak usage\t: " . xdebug_peak_memory_usage() . "\r\n");
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t----------------------------------------------------------\r\n");
            }
            foreach (self::$stringWrite as $string) {
                fputs($datei, date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . $string);
            }
        }

        if (SP_CORE_LOG_WEB) {
            //echo '<div id="sp_console" style="position:fixed;bottom:0;width:100%;height:20%;overflow:auto;background: #383f38;box-shadow: inset 0 4px 8px #222; padding: 10px 10px; line-height: 18px; font-size: 11px; color:#efe;border-top:2px solid #ccc;z-index:10;">';
            echo '<div id="sp_console_button">&#x3c;&#x3c;click&#x3e;&#x3e; view debugging log</div><div id="sp_console">';
            $xdebugDevelop = function_exists('xdebug_time_index') && strpos((string)ini_get('xdebug.mode'), 'develop') !== false;
            if ($xdebugDevelop) {
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t----------------------------------------------------------<br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ [NEW SIDE CLICK ] -----------------------<br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Modul\t\t: " . (isset($requestVar['modul']) ? $requestVar['modul'] : "No modul call") . " <br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Action\t\t: " . (isset($requestVar['action']) ? $requestVar['action'] : "No action call") . " <br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ IP\t\t: " . $_SERVER["REMOTE_ADDR"] . " <br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Script Time (sec)\t: " . xdebug_time_index() . "<br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Memory usage\t: " . xdebug_memory_usage() . "<br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ URL\t\t:" . $_SERVER['REQUEST_URI'] . "<br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t#++++ Memory peak usage\t: " . xdebug_peak_memory_usage() . "<br>";
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . "\t\t----------------------------------------------------------<br>";
            }
            foreach (self::$stringOut as $string) {
                echo date("[" . $_SERVER["REMOTE_ADDR"] . "][d/m/Y H:i:s] ", time()) . $string . "<br>";
            }
            echo "</div>";
        }
    }

}

?>

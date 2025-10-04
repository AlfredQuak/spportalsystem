<?php

/* spPortalSystem spcore\CHelper.php
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

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

/**
 * \ingroup systemcore
 * @author misterice
 * \brief Helping functions
 * \details
 * Some functions the make your live easyer
 * Class Type : Singelton
 */
class CHelper {

    public static $instance = null;

    protected function __construct() {
        
    }

    private function __clone() {
        
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CHelper();
        }
        return self::$instance;
    }

    public static function formatTimeBack($time) {
        if (!empty($time)) {
            $datum = explode("/", trim($time));
            return mktime(0, 0, 0, $datum[1], $datum[0], $datum[2]);
        }
    }

    /**
     * \brief
     * Get all POST or GET Variabels
     * @return array
     */
    public static function getRequestVar() {
        $request = null;
        if (isset($_GET)) {
            foreach ($_GET as $index => $req) {
                $request[$index] = $req;
            }
        }
        if (isset($_POST)) {
            foreach ($_POST as $index => $req) {
                $request[$index] = $req;
            }
        }
        if (isset($_REQUEST)) {
            foreach ($_REQUEST as $index => $req) {
                $request[$index] = $req;
            }
        }
        return $request;
    }

    /**
     * \brief
     * Get all POST or GET Variabels and convert charset
     * @return array
     */
    public static function getConvertedRequestVar($inCharset, $outCharset) {

        $request = null;
        if (isset($_GET)) {
            foreach ($_GET as $index => $req) {
                $request[$index] = self::convertCharsetTo($inCharset, $outCharset, $req);
            }
        }
        if (isset($_POST)) {
            foreach ($_POST as $index => $req) {
                $request[$index] = self::convertCharsetTo($inCharset, $outCharset, $req);
            }
        }
        if (isset($_REQUEST)) {
            foreach ($_REQUEST as $index => $req) {
                $request[$index] = self::convertCharsetTo($inCharset, $outCharset, $req);
            }
        }
        return $request;
    }

    public static function convertCharsetTo($inCharset, $outCharset, $req) {
        if ($outCharset === false)
            return $req;
        if (preg_match('/^.{1}/us', $req, $ar) != 1 && $outCharset == "UTF-8") {
            return @iconv(($inCharset === null ? mb_detect_encoding($req, "auto") : $inCharset), $outCharset . "//IGNORE", $req);
        } else {
            return @iconv(($inCharset === null ? mb_detect_encoding($req, "auto") : $inCharset), $outCharset . "//TRANSLIT//IGNORE", $req);
        }
    }

    /**
     * \brief
     * Convert your $reqVar Array into yout own charset
     * @return array
     */
    public static function convertRequestVars($inCharset, $outCharset, array $reqVarArray) {
        foreach ($reqVarArray as $index => $value) {
            $reqVarArray[$index] = self::convertCharsetTo($inCharset, $outCharset, $value);
        }
        return $reqVarArray;
    }

    /**
     * \brief Strip and trim your vars
     * \details
     * \code
     * foreach($request as $index => $value){
     *    $request[$index] = strip_tags($value);
     *    $request[$index] = htmlspecialchars($value);
     *    $request[$index] = trim($value);
     * }
     * \endcode
     * @param array $request
     * @return unknown_type
     */
    public static function stripRequestArray($request) {
        if (is_array($request)) {
            foreach ($request as $index => $value) {
                $request[$index] = strip_tags($value);
                $request[$index] = htmlspecialchars($value);
                $request[$index] = trim($value);
            }
        } else {
            $request = self::stripRequestVar($request);
        }
        return $request;
    }

    /**
     * \brief Strip and trim your var
     * @param  $request
     * @return unknown_type
     */
    public static function stripRequestVar($request) {
        $retVal = strip_tags($request);
        $retVal = htmlspecialchars($retVal);
        $retVal = trim($retVal);
        return $retVal;
    }

    /**
     * \brief
     * Get userlang from browser
     * \details
     * Five back array, array is sorted by %
     * @return Array('0' => 'lang')
     */
    public static function getUserLangByBrowser() {
        $parts = split(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $mylangs = array();
        foreach ($parts as $part) {
            preg_match('/([a-zA-Z\-]{2,})(?:;q=([0-9\.]+))?/', trim($part), $matches);
            $mylangs[empty($matches[2]) ? 1 : $matches[2]] = $matches[1];
        }
        sort($mylangs);
        return $mylangs;
    }

    /**
     * \brief
     * set the language
     * \details
     * Put into sessionvar your lang
     * @param $lang
     * @return none
     */
    public static function setUserLang($lang) {
        session_start();
        $_SESSION['lang'] = $lang;
    }

    /**
     * \brief
     * Get lang
     * \details
     * If lang is set give the lang back, if not comes false
     * @return lang oder false
     */
    public static function getLang() {
        session_start();
        if (isset($_SESSION['lang'])) {
            return $_SESSION['lang'];
        } else {
            return false;
        }
    }

    /**
     * \brief Clear your HTML chars
     * \details
     * This function convert your chars to html encodet tags,<br>
     * HTML and scripts works after.
     *
     * @param $string
     * @return worked html
     */
    public static function clearHTML($string) {
        $retVar = htmlentities($string);
        $retVar = str_replace("&lt;", "<", $retVar);
        $retVar = str_replace("&gt;", ">", $retVar);
        $retVar = str_replace("&quot;", '"', $retVar);
        $retVar = str_replace("&amp;", '&', $retVar);
        return $retVar;
    }

    public static function clearString($string) {
        $string = str_replace("/", "-", $string);
        $string = str_replace(" ", "-", $string);
        $string = str_replace(".", "-", $string);
        $string = str_replace(":", "", $string);
        return $string;
    }

    public static function makeHTMLwork($string) {
        $string = str_replace("&lt;", "<", $string);
        $string = str_replace("&gt;", ">", $string);
        $string = str_replace("&quot;", '"', $string);
        $string = str_replace("&amp;", '&', $string);
        return $string;
    }

    public static function logFile($fileName, $string, $option) {
        $datei = fopen($fileName, $option);
        fputs($datei, $string);
        fclose($datei);
    }

    #
    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */

    public static function toXml($data, $rootNodeName = 'data', $xml = null) {
// turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }
        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }
// loop through the data passed in.
        foreach ($data as $key => $value) {
// no numeric keys in our xml please!
            if (empty($value)) {
                continue;
            }
            if (is_numeric($key)) {
// make string key...
                $key = "data" . (string) $key;
                //$key = $rootNodeName;
            }
// replace anything not alpha numeric
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);
// if there is another array found recrusively call this function
            if (is_array($value)) {
                if (empty($value[0]))
                    continue;
// create a new node unless this is an array of elements
                // determine if a variable is an associative array
                $myself = function ( $array ) {
                            return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
                        };

                //$node = $myself($value) ? $xml->addChild($key) : $xml;
                //$node = $myself( $value ) || $numeric ? $xml->addChild( $key ) : $xml;
//if ( $numeric ) $key = 'anon';
                //self::toXml( $value, $key, $node );

                $node = $myself($value) ? $xml->addChild($key) : $xml;
                $node = $xml->addChild("Settings");
                $node = self::toXml($value, $rootNodeName, $node);
// recrusive call.
            } else {
// add single node.
                $value = htmlentities($value);
                //$xml->addChild($key, $value);
                $xml->addAttribute($key, $value);
            }
        }
// pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }

    public static function writeTMP($scriptname, $value) {
        include_once 'CDatabase.php';
        include_once 'CLog.php';
        CDatabase::getInstance()->query("REPLACE INTO `sp_thirdpt_tmp` (name,value,time) VALUES ('"
                . CDatabase::getInstance()->checkValue($scriptname) . "','"
                . CDatabase::getInstance()->checkValue($value) . "',
                '" . time() . "')");
    }

    public static function getTMP($scriptname) {
        include_once 'CDatabase.php';
        include_once 'CLog.php';
        $result = CDatabase::getInstance()->query("SELECT value FROM `sp_thirdpt_tmp` WHERE name='" . CDatabase::getInstance()->checkValue($scriptname) . "'");
        $res = CDatabase::getInstance()->fetch_assoc($result);
        return $res['value'];
    }

    public static function getTMPTimestamp($scriptname) {
        include_once 'CDatabase.php';
        include_once 'CLog.php';

        $result = CDatabase::getInstance()->query("SELECT time FROM `sp_thirdpt_tmp` WHERE name='" . CDatabase::getInstance()->checkValue($scriptname) . "'");
        $res = CDatabase::getInstance()->fetch_assoc($result);
        return $res['time'];
    }

}

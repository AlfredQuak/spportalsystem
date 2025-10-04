<?php

/* spPortalSystem spcore\CTemplate.php
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
 * \version 0.3
 * \ingroup systemcore
 * \brief Basic Template class for template and language support
 * \details
 * The template class is for bring up your template and dynamic template snippeds to front.<br>
 * You can parse dynamic template snippeds with functions about this class<br>
 * and replace tags with your dynamic content. Look at class definition for details. <br>
 * Type : Singelton
 */
final class CTemplate {

    private $templateIndex;
    private $templateHeader;
    private $templateToplink = "";
    private $templateFooter;
    private $templateBox;
    private $templateContentBox;
    private $leftBoxes;
    private $rightBoxes;
    private $contentBoxes;
    private $lastOut = array();
    private $newTemplate = false;
    private $watchLeftBox = true;
    private $watchContentBox = true;
    private $watchRigthBox = true;
    private $modul = array();
    private $jsNoModFileName = array();
    private $jsFileName = array();
    private $cssNoModFileName = array();
    private $cssFileName = array();
    public $langObj;  //language XML Object
    public $showHeader = true;
    public $showFooter = true;
    private $tplXmlObj = null;
    private $metaTags;
    private $titelTag = null;
    private $noTemplate = false;
    private $strReplace = array();
    private $renderExtensionEdit = true;
    private static $instance = null;

    private function __clone() {
        
    }

    /**
     * /brief give class instance
     * @param $g_system
     * @return object
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CTemplate();
        }
        return self::$instance;
    }

    protected function __construct() {
        switch (SP_CORE_TEMPLATE_XML) {
            case 1:
                self::loadStandardTemplateXML('systemCore', 'mainTemplate');
                break;
            default:
                $this->templateIndex = file_get_contents(SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . '/index.tpl.php');
                $this->templateHeader = file_get_contents(SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . '/header.tpl.php');
                $this->templateFooter = file_get_contents(SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . "/footer.tpl.php");
                $this->templateBox = file_get_contents(SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . '/box.tpl.php');
                $this->templateContentBox = file_get_contents(SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . '/contenBox.tpl.php');
                break;
        }
    }

    public function loadStandardTemplateXML($modulname, $template) {
        $this->tplXmlObj = self::loadModulTemplateXML($modulname, $template);
        $this->templateIndex = $this->tplXmlObj->index;
        $this->templateHeader = $this->tplXmlObj->indexHeader;
        $this->templateFooter = $this->tplXmlObj->indexFooter;
        $this->templateBox = $this->tplXmlObj->indexBox;
        $this->templateContentBox = $this->tplXmlObj->indexContentBox;
    }

    public function renderExtensionInEditor($render) {
        $this->renderExtensionEdit = $render;
    }

    /**
     * Returns the loaded main XML template
     */
    public function getXMLTemplate() {
        if (SP_CORE_TEMPLATE_XML == true) {
            return $this->tplXmlObj;
        } else {
            return false;
        }
    }

    /**
     * Set the System Template out to off
     * @param <type> $status
     */
    public function setNoTemplate($status) {
        $this->noTemplate = $status;
    }

    public function replace($string, $replaceWith) {
        $this->strReplace[$string] = $replaceWith;
    }

    /**
     * \brief Replace standard Template
     * \details Here you can overwrite the loaded template
     * \code
     * $this->templateIndex		= $newTemplate['index'];
     * $this->templateHeader            = $newTemplate['indexHeader'];
     * $this->templateFooter            = $newTemplate['indexFooter'];
     * $this->templateBox 		= $newTemplate['indexBox'];
     * $this->templateContentBox 	= $newTemplate['indexContentBox'];
     * \endcode
     * @param array $newTemplate
     * @return none
     */
    public function setNewTemplate(array $newTemplate, $xml = false) {
        $this->templateIndex = isset($newTemplate['index']) ? $newTemplate['index'] : null;
        $this->templateHeader = isset($newTemplate['indexHeader']) ? $newTemplate['indexHeader'] : null;
        $this->templateFooter = isset($newTemplate['indexFooter']) ? $newTemplate['indexFooter'] : null;
        $this->templateBox = isset($newTemplate['indexBox']) ? $newTemplate['indexBox'] : null;
        $this->templateContentBox = isset($newTemplate['indexContentBox']) ? $newTemplate['indexContentBox'] : null;
        $this->newTemplate = $xml;
    }

    /**
     * \brief MetaTag generator
     * \details
     * For your website you can give here metatags the will be
     * generate for active side metatags.<br>
     * Syntax are :
     * \code
     * spcore\CTemplate::getInstance()->setMetaTags(array('metatagname' => 'value', ...));
     * \endcode
     * Generation finaly in \ref getRenderdSite()
     * @param array $meta
     * @return none
     */
    public function setMetaTags($meta) {
        foreach ($meta as $index => $value) {
            $this->metaTags[$index] = $value;
        }
    }

    /**
     * \brief Set your titel tag
     * @param <type> $titel
     */
    public function setTitelTag($titel) {
        $this->titelTag = $titel;
    }

    /**
     * \brief Here you can set your own Modulname
     * \details
     * Sometime you will change temporaly change your modulname,
     * you can make it here.
     * @param $name
     * @return none
     */
    public function setModulName($name) {
        $this->modulName = $name;
    }

    /**
     * \brief Set the language XML object file
     * \details
     * Normaly this function not needed, the language files where load in index.php <br>
     * You can add your own Language XML file here. Then they will be parse all Tags about : <br><br>
     * {LANG_modulname_xmltag}<br>
     * <br>
     * Using in index.php :
     * \code
     * if (file_exists('./module/'.$requestVar['modul'].'/lang/'. SP_CORE_LANG .'_'.$requestVar['modul'].'.xml')) {
     * 		spcore\CTemplate::getInstance()->setLangObj(simplexml_load_file('./module/'.$requestVar['modul'].'/lang/'. SP_CORE_LANG .'_'.$requestVar['modul'].'.xml'));
     * }
     * \endcode
     * @param $XMLObj
     * @return none
     */
    public function setLangObj($XMLObj) {
        $this->langObj[] = $XMLObj;
    }

    public function addtplXmlObj(array $array) {
        if (!empty($array)) {
            $data = false;

            foreach ($array as $akey => $aval) {
                $this->tplXmlObj->{$akey} = $aval;
            }
//$this->tplXmlObj[] = $data;
        } else {
            return false;
        }
    }

    public function gettplXmlObj() {
        return $this->tplXmlObj;
    }

    /**
     * \brief Return html string
     * \detail Return html option string
     * \code
     * <option value=\"".$value."\" ".($selected==1?"selected":"").">".$text."</option>
     * \endcode
     * @param $value
     * @param $text
     * @return unknown_type
     */
    public function makeOptionTag($value = "", $text = "", $selected = 0) {
        return "<option value=\"" . $value . "\" " . ($selected == 1 ? "selected=\"selected\"" : "") . ">" . $text . "</option>\n";
    }

    /**
     * \brief Loading Modul Tempates
     * \details
     * Load modul template and if you give the content array the parsed for you.<br>
     * Sample with no content array: <br>
     * \code
     * $this->tpl_MenueFooter  = $this->template->loadModulTemplate('portal','menue_box_footer');
     * \endcode
     * This will give back only the content string with nothing parsed snippeds<br>
     * But you can give your dynamic content into this function about this sample : <br>
     * tags in templates<br>
     * \code
     * <table>
     * <tr>
     * 	<td>{mymodulname_dynamic1}</td>
     * </tr>
     * </table>
     * \endcode
     * <br>
     * Your code :
     * \code
     * $boxarray['dynamic1']	= "mycontent";
     * $this->tpl_MenueFooter	= spcore\CTemplate::getInstance()->loadModulTemplate('mymodulname','templatename',$boxarray);
     * \endcode
     * <br>
     * Result :
     * \code
     * <table>
     * <tr>
     * 	<td>mycontent</td>
     * </tr>
     * </table>
     * \endcode
     * <br>
     * or you parse your loaded string later with parseModulTemplate()
     * @param modulname
     * @param filename
     * @param contentarray
     * @return string
     *
     */
    public function loadModulTemplate($modulName, $fileName, $content = null) {
        $file = SP_CORE_DOC_ROOT . '/module/' . $modulName . '/template/' . $modulName . '_' . $fileName . '.tpl.php';

        if (is_file($file)) {
            $tplDummy = file_get_contents($file);

            if (is_array($content)) {
                foreach ($content as $index => $value) {
                    $tplDummy = str_replace("{" . $modulName . "_" . $index . "}", $value, $tplDummy);
                }
                CLog::getInstance()->log(SP_LOG_DEBUG, SP_LOG_DEBUG, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . "\n" . $file);
                return $tplDummy;
            } else {
                CLog::getInstance()->log(SP_LOG_DEBUG, SP_LOG_DEBUG, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . "\n" . $file);
                return $tplDummy;
            }
        } else {
            CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . "\n" . $file);
            return "no Box Template : " . $file;
        }
    }

    /**
     * \brief
     * Load Template as xml object
     * \details
     * load Template as xml object for easy handle
     *
     * @param $modulName
     * @param $fileName
     * @return object simpel xml object
     */
    public function loadModulTemplateXML($modulName, $fileName) {
        if ($modulName == "systemCore") {
            $xmlRaw = file_get_contents(SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . "/" . $fileName . ".tpl.php");
        } else {
            $xmlRaw = file_get_contents(SP_CORE_DOC_ROOT . '/module/' . $modulName . '/template/' . $modulName . '_' . $fileName . '.tpl.php');
        }
        // Replace deprecated utf8_decode(): convert from UTF-8 to ISO-8859-1 if needed, otherwise keep as-is
        if (function_exists('mb_convert_encoding')) {
            $xml = mb_convert_encoding($xmlRaw, 'ISO-8859-1', 'UTF-8');
        } elseif (function_exists('iconv')) {
            $xml = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $xmlRaw);
        } else {
            $xml = $xmlRaw;
        }
        try {
            libxml_use_internal_errors(true);
            $outXML = simplexml_load_string($xml, null, LIBXML_NOCDATA | LIBXML_NOEMPTYTAG);
            if ($outXML === false) {
                $error = "Loading " . $fileName . " failed ";
                foreach (libxml_get_errors() as $err) {
                    $error .= "\t" . $err->message;
                }
                CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . "\n" . $error);
            } else {
                return $outXML;
            }
        } catch (Exception $e) {
            CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . "\n" . $e->getMessage());
        }
    }

    public function loadAdminModulTemplateXML($modulName, $fileName) {
        if ($modulName == "systemCore") {
            $xmlRaw = file_get_contents(SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . "/admin/a_" . $fileName . ".tpl.php");
        } else {
            $xmlRaw = file_get_contents(SP_CORE_DOC_ROOT . '/module/' . $modulName . '/template/admin/a_' . $modulName . '_' . $fileName . '.tpl.php');
        }
        // Replace deprecated utf8_decode(): convert from UTF-8 to ISO-8859-1 if needed, otherwise keep as-is
        if (function_exists('mb_convert_encoding')) {
            $xml = mb_convert_encoding($xmlRaw, 'ISO-8859-1', 'UTF-8');
        } elseif (function_exists('iconv')) {
            $xml = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $xmlRaw);
        } else {
            $xml = $xmlRaw;
        }
        try {
            libxml_use_internal_errors(true);
            $outXML = simplexml_load_string($xml, null, LIBXML_NOCDATA | LIBXML_NOEMPTYTAG);
            if ($outXML === false) {
                $error = "Loading " . $fileName . " failed ";
                foreach (libxml_get_errors() as $err) {
                    $error .= "\t" . $err->message;
                }
                CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . "\n" . $error);
            } else {
                return $outXML;
            }
        } catch (Exception $e) {
            CLog::getInstance()->log(SP_LOG_ERROR, SP_LOG_ERROR, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . "\n" . $e->getMessage());
        }
    }

    /**
     * \brief Parse template string
     * \details
     * This function make the same as loadModulTemplate() exlusiv template loading.<br>
     * \code
     * $boxarray['dynamic1']	= "mycontent";
     * $this->content 	= spcore\CTemplate::getInstance()->parseModulTemplate("mymodulname",$boxArray, $templatestring);
     * \endcode
     * @param modulname
     * @param array contentarray
     * @param modulcontent
     * @return string
     */
    public function parseModulTemplate($modulName, $content, $string) {
        foreach ($content as $index => $value) {
            $string = str_replace("{" . $modulName . "_" . $index . "}", $value, $string);
        }
        return $string;
    }

    /**
     * \brief Set activate status for right box
     * @param boolean
     * @return none
     */
    public function setWatchRightBox($status) {
        $this->watchRigthBox = $status;
    }

    /**
     * \brief Set activate status for right box
     * @param boolean
     * @return none
     */
    public function setWatchLeftBox($status) {
        $this->watchLeftBox = $status;
    }

    /**
     * \brief Set activate status for content box
     * @param boolean
     * @return none
     */
    public function setWatchContentBox($status) {
        $this->watchContentBox = $status;
    }

    /**
     * \brief Add a content box
     * \details
     * Adding a content box. You can give the position from the box
     * and the aling position from the titel.<br>
     * If you don't give an aling standard is 'left'.
     * @param $titel
     * @param $content
     * @param $aling
     * @param $pos
     * @return none
     *
     */
    public function addContentBoxNews($date, $titel, $content, $aling = 'left', $pos = null, $w = null) {

        self::setWatchContentBox(true);
        $box_out = str_replace('{BOX_DATE}', $date, $this->templateContentBox);
        $box_out = str_replace('{BOX_TITEL}', $titel, $box_out);
        $box_out = str_replace('{BOX_CONTENT}', self::checkThirdPartyScripts($content), $box_out);
        $box_out = str_replace('{BOX_ALIGN}', $aling, $box_out);
        $box_out = str_replace('{BOX_TD_WIDTH}', $w ?? '', $box_out);

        if ($pos != null) {
            $this->contentBoxes[$pos] = $box_out;
        } else {
            $this->contentBoxes[] = $box_out;
        }
    }

    /**
     * \brief Add a content box
     * \details
     * Adding a content box. You can give the position from the box
     * and the aling position from the titel.<br>
     * If you don't give an aling standard is 'left'.
     * @param $titel
     * @param $content
     * @param $aling
     * @param $pos
     * @return none
     *
     */
    public function addContentBox($titel, $content, $aling = 'left', $pos = null, $w = null) {

        self::setWatchContentBox(true);
        $box_out = str_replace('{BOX_TITEL}', $titel, $this->templateContentBox);
        $box_out = str_replace('{BOX_CONTENT}', self::checkThirdPartyScripts($content), $box_out);
        $box_out = str_replace('{BOX_ALIGN}', $aling, $box_out);
        $box_out = str_replace('{BOX_TD_WIDTH}', $w ?? '', $box_out);

        if ($pos != null) {
            $this->contentBoxes[$pos] = $box_out;
        } else {
            $this->contentBoxes[] = $box_out;
        }
    }

    public function addToplink($content) {
        $this->templateToplink .= $content;
    }

    /**
     * \brief Give back complete box
     * \details
     * You can generate a box templatestring the were not automatic put into your template.<br>
     * You can handel this template snipped where you want.
     * @param $titel
     * @param $content
     * @param $aling
     * @return string
     */
    public function addBox($titel, $content, $aling = 'left') {
        $box_out = str_replace('{BOX_TITEL}', $titel, $this->templateContentBox);
        $box_out = str_replace('{BOX_CONTENT}', self::checkThirdPartyScripts($content), $box_out);
        $box_out = str_replace('{BOX_ALIGN}', $aling, $box_out);
        return $box_out;
    }

    /**
     * \brief Add a left box
     * \details
     * Same as addContentBox()
     * @param $titel
     * @param $content
     * @param $aling
     * @param $pos
     * @return none
     */
    public function addLeftBox($titel, $content, $aling = 'left', $pos = null) {
        self::setWatchLeftBox(true);
        $box_out = str_replace('{BOX_TITEL}', $titel, $this->templateBox);
        $box_out = str_replace('{BOX_CONTENT}', self::checkThirdPartyScripts($content), $box_out);
        $box_out = str_replace('{BOX_ALIGN}', $aling, $box_out);
        if ($pos != null) {
            $this->leftBoxes[$pos] = $box_out;
        } else {
            $this->leftBoxes[] = $box_out;
        }
    }

    /**
     * \brief Add a left box
     * \details
     * Same as addContentBox()
     * @param $titel
     * @param $content
     * @param $aling
     * @param $pos
     * @return none
     */
    public function addRightBox($titel, $content, $aling = 'left', $pos = null) {
        self::setWatchRightBox(true);

        $box_out = str_replace('{BOX_TITEL}', $titel, $this->templateBox);
        $box_out = str_replace('{BOX_CONTENT}', self::checkThirdPartyScripts($content), $box_out);
        $box_out = str_replace('{BOX_ALIGN}', $aling, $box_out);
        if ($pos != null) {
            $this->rightBoxes[$pos] = $box_out;
        } else {
            $this->rightBoxes[] = $box_out;
        }
    }

    /**
     * \brief Add javascriptfile to your side
     * \details
     * Here you can add javascriptfiles to your side.
     * \code
     * <script type="text/javascript" language="JavaScript" src="./module/portal/template/js_scripts.php"></script>
     * \endcode
     * You can add more the one file, with every add that will we generate one hmtl include.<br>
     * If your javascriptfile ist a php script you can give attributes :
     * \code
     * spcore\CTemplate::getInstance()->addJsScript("portal","js_scripts.php","foo=foo&bar=bar");
     * \endcode
     * @param modulname
     * @param filename
     * @param tags
     * @return none
     */
    public function addJsScript($modul, $filename = "scripts.js", $tags = "") {
        $this->jsFileName[][$modul][$filename] = $tags;
    }

    /**
     * \brief Add javascriptfile to your side
     * \details
     * Here you can add javascriptfiles to your side.
     * \code
     * <script type="text/javascript" language="JavaScript" src="./module/portal/template/js_scripts.php"></script>
     * \endcode
     * You can add more the one file, with every add that will we generate one hmtl include.<br>
     * If your javascriptfile ist a php script you can give attributes :
     * \code
     * spcore\CTemplate::getInstance()->addJsScript("portal","js_scripts.php","foo=foo&bar=bar");
     * \endcode
     * @param modulname
     * @param filename
     * @param tags
     * @return none
     */
    public function addNoModJsScript($path, $filename = "scripts.js", $tags = "") {
        $this->jsNoModFileName[][$path][$filename] = $tags;
    }

    /**
     * \brief Add CSS to your side
     * \details
     * Here you can add CSS to your side.
     * \code
     * <link rel="stylesheet" type="text/css" href="yourcss.css">
     * \endcode
     * You can add more the one file, with every add that will we generate one hmtl include.<br>
     * If your javascriptfile ist a php script you can give attributes :
     * \code
     * spcore\CTemplate::getInstance()->addJsScript("portal","css_scripts.php","foo=foo&bar=bar");
     * \endcode
     * @param modulname
     * @param filename
     * @param tags
     * @return none
     */
    public function addCssScript($modul, $filename = "style.css", $tags = "") {
        $this->cssFileName[][$modul][$filename] = $tags;
    }

    /**
     * \brief Add CSS to your side
     * \details
     * Here you can add CSS to your side.
     * \code
     * <link rel="stylesheet" type="text/css" href="yourcss.css">
     * \endcode
     * You can add more the one file, with every add that will we generate one hmtl include.<br>
     * If your javascriptfile ist a php script you can give attributes :
     * \code
     * spcore\CTemplate::getInstance()->addJsScript("portal","css_scripts.php","foo=foo&bar=bar");
     * \endcode
     * @param modulname
     * @param filename
     * @param tags
     * @return none
     */
    public function addNoModCssScript($path, $filename = "style.css", $tags = "") {
        $this->cssNoModFileName[][$path][$filename] = $tags;
    }

    /**
     * \brief
     * Set header for non caching
     * \details
     *
     * @return none
     */
    public function doNotCacheThisSide() {
        header("Expires: Mon, 12 Jul 1995 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    private function clearMetaTag($string) {
        $string = trim($string);
        $string = strip_tags($string);
        $string = str_replace('\n', '', $string);
        $string = preg_replace('/\h+/', ' ', $string);
        $string = preg_replace('/\v{3,}/', PHP_EOL . PHP_EOL, $string);
        return $string;
    }

    private function myReplace($s) {
        if (file_exists(SP_CORE_DOC_ROOT . "/thirdPT/thirdPT_" . $s[1] . ".php")) {
            require_once (SP_CORE_DOC_ROOT . "/thirdPT/thirdPT_" . $s[1] . ".php");
            try {
                $myFunction = "thirdPT_" . $s[1];
                return $myFunction(CDatabase::getInstance());
            } catch (Exception $e) {
                return $e->__toString();
            }
        } else {
            return "not found : " . SP_CORE_DOC_ROOT . "/thirdPT/thirdPT_" . $s[1] . ".php";
        }
    }

    private function myReplaceModul_cont($s) {
        if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php")) {
            require_once (SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php");
            try {
                $extClass = "C" . $s[1];
                $extmodul = new $extClass(CDatabase::getInstance());
                return $extmodul->_cont_extportal();
            } catch (Exception $e) {
                return $e->__toString();
            }
        } else {
            return "not found : " . SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php";
        }
    }

    private function myReplaceModul_nav($s) {
        if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php")) {
            require_once (SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php");
            try {
                $extClass = "C" . $s[1];
                $extmodul = new $extClass(CDatabase::getInstance());
                return $extmodul->_nav_extportal();
            } catch (Exception $e) {
                return $e->__toString();
            }
        } else {
            return "not found : " . SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php";
        }
    }

    private function myReplaceTemplate($s) {
        if (file_exists(SP_CORE_DOC_ROOT . '/' . SP_CORE_TEMPLATE_PATH . '/' . $s[1] . '.tpl.php')) {
            return file_get_contents(SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . "/" . $s[1] . ".tpl.php");
        } else {
            return "not found : " . SP_CORE_DOC_ROOT . "/" . SP_CORE_TEMPLATE_PATH . "/" . $s[1] . ".tpl.php";
        }
    }

    private function myReplaceModul_func($s) {
        if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php")) {
            require_once (SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php");
            $extClass = "C" . $s[1];
            try {
                $extmodul = new $extClass(CDatabase::getInstance());
                return $extmodul->$s[2]($s[3]);
            } catch (Exception $e) {
                return $e->__toString();
            }
        } else {
            return "not found : " . SP_CORE_DOC_ROOT . "/module/" . $s[0] . "/includes/C" . $s[0] . ".php";
        }
    }

    public function checkThirdPartyScripts($string) {
        if ($this->renderExtensionEdit) {
            $string = preg_replace_callback("'\{modul_cont::([a-z]+)}'", [$this, 'myReplaceModul_cont'], $string);
            $string = preg_replace_callback("'\{modul_nav::([a-z]+)}'", [$this, 'myReplaceModul_nav'], $string);
            $string = preg_replace_callback("'\{modul_func::(.*)::(.*)\((.*)\)}'", [$this, 'myReplaceModul_func'], $string);
            $string = preg_replace_callback("'\{template::([a-z]+)}'", [$this, 'myReplaceTemplate'], $string);
            $string = preg_replace_callback("'\{thirdPT::([a-z]+)}'", [$this, 'myReplace'], $string);
        }
        return $string;
    }

    /**
     * \brief Finally function get complety rendered side out
     * \details
     * If all think do, the side becomes there last rendering and give the side string out.<br>
     * The return string the give, that is the page.
     * @return string
     */
    public function getRenderdSite() {
        $boxContent = '';
        $boxright = '';
        $boxLeft = '';

        if ($this->noTemplate == true) {
            return "";
        }

        if ($this->showHeader === true) {
            $complete_out = str_replace('{TEMPLATE_HEADER}', $this->templateHeader, $this->templateIndex);
        } else {
            $complete_out = str_replace('{TEMPLATE_HEADER}', "", $this->templateIndex);
        }

        $complete_out = self::checkThirdPartyScripts($complete_out);
// toplink - index
//$this->templateToplink = "test";
////die($this->templateToplink);
//var_dump($this->templateToplink);
//die("debug: ");

        if (empty($this->templateToplink)) {
            $complete_out = str_replace('{TEMPLATE_TOPLINK}', '', $complete_out);
        } else {
            $complete_out = str_replace('{TEMPLATE_TOPLINK}', $this->templateToplink, $complete_out);
        }

        if ($this->watchLeftBox && is_array($this->leftBoxes)) {
            if (SP_CORE_TEMPLATE_XML == 1 || $this->newTemplate == true) {
                $complete_out = str_replace('{TEMPLATE_LEFT_BOX_TRUE}', '{TEMPLATE_LEFT_BOXES}', $complete_out);
            } else {
                $complete_out = str_replace('{TEMPLATE_LEFT_BOX_TRUE}', '{TEMPLATE_LEFT_BOXES}', $complete_out);
            }
            foreach ($this->leftBoxes as $box) {
                $boxLeft .= $box;
            }
            $complete_out = str_replace('{TEMPLATE_LEFT_BOXES}', $boxLeft, $complete_out);
        } else {
            $complete_out = str_replace('{TEMPLATE_LEFT_BOX_TRUE}', '', $complete_out);
        }

        if ($this->watchContentBox && is_array($this->contentBoxes)) {
            if (SP_CORE_TEMPLATE_XML == 1 || $this->newTemplate == true) {
                $complete_out = str_replace('{TEMPLATE_CONTENT_BOX_TRUE}', '{TEMPLATE_CONTENT}', $complete_out);
            } else {
                $complete_out = str_replace('{TEMPLATE_CONTENT_BOX_TRUE}', '{TEMPLATE_CONTENT}', $complete_out);
            }
            foreach ($this->contentBoxes as $box) {
                $boxContent .= $box;
            }
            $complete_out = str_replace('{TEMPLATE_CONTENT}', $boxContent, $complete_out);
        } else {
            $complete_out = str_replace('{TEMPLATE_CONTENT_BOX_TRUE}', '', $complete_out);
        }

        if ($this->watchRigthBox && is_array($this->rightBoxes)) {
            if (SP_CORE_TEMPLATE_XML == 1 || $this->newTemplate == true) {
                $complete_out = str_replace('{TEMPLATE_RIGTH_BOX_TRUE}', '{TEMPLATE_RIGTH_BOXES}', $complete_out);
            } else {
                $complete_out = str_replace('{TEMPLATE_RIGTH_BOX_TRUE}', '{TEMPLATE_RIGTH_BOXES}', $complete_out);
            }
            foreach ($this->rightBoxes as $box) {
                $boxright .= $box;
            }
            $complete_out = str_replace('{TEMPLATE_RIGTH_BOXES}', $boxright, $complete_out);
        } else {
            $complete_out = str_replace('{TEMPLATE_RIGTH_BOX_TRUE}', '', $complete_out);
        }

        if ($this->showFooter === true) {
            $complete_out = str_replace('{TEMPLATE_FOOTER}', $this->templateFooter, $complete_out);
        } else {
            $complete_out = str_replace('{TEMPLATE_FOOTER}', "", $complete_out);
        }

// parse and replace language tags {LANG_modulname_tag}
        if (!empty($this->langObj)) {
            foreach ($this->langObj as $language) {
                if (is_object($language)) {
                    foreach ($language->children() as $obj) {
                        $complete_out = str_replace('{LANG_' . $language->getName() . '_' . $obj->getName() . '}', $obj, $complete_out);
                    }
                }
            }
        }
// Portal side name
        if ($this->titelTag != null) {
            $complete_out = str_replace('{PORTAL_TEMPLATE_PORTALTITEL}', $this->titelTag, $complete_out);
        } else {
            $complete_out = str_replace('{PORTAL_TEMPLATE_PORTALTITEL}', SP_CORE_TEMPLATE_META_TITEL, $complete_out);
        }

//generate metatags
        $myMetaTags = '';
        if (is_array($this->metaTags)) {
            foreach ($this->metaTags as $index => $value) {
                $myMetaTags .= "<meta name=\"" . $index . "\" content=\"" . self::clearMetaTag($value) . "\">\r\n";
            }
        }
//var_dump($this->metaTags);die("moep");
        $complete_out = str_replace('{PORTAL_TEMPLATE_META_TAGS}', $myMetaTags, $complete_out);
        $jsScript = "";
        if (is_array($this->jsNoModFileName)) {
            foreach ($this->jsNoModFileName as $nextArr) {
                foreach ($nextArr as $jsIndex => $jsValue) {
                    if (is_array($jsValue)) {
                        foreach ($jsValue as $a => $b) {
// ($_SERVER['SERVER_ADDR']."/". SP_CORE_SUB_DOC_PATH ."/". $filename)
                            if (file_exists(SP_CORE_DOC_ROOT . '/' . SP_CORE_TEMPLATE_PATH . '/' . $jsIndex)) {
                                if (!empty($b)) {
                                    $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                            SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                            SP_CORE_SUB_DOC_PATH . "/" . SP_CORE_TEMPLATE_PATH . "/" . $a .
                                            "?" . $b . "\"></script>\n";
                                } else {
                                    $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                            SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                            SP_CORE_SUB_DOC_PATH . "/" . SP_CORE_TEMPLATE_PATH . "/" .
                                            $jsIndex . "\"></script>\n";
                                }
                            }/* elseif($jsIndex == "systemCore") {
                              if(!empty($b)) {
                              $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"". SP_CORE_TEMPLATE_PATH ."/".$a."?".$b."\"></script>\n";
                              }else {
                              $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"". SP_CORE_TEMPLATE_PATH ."/".$a."\"></script>\n";
                              }
                              }else {
                              } */
                        }
                    } else {
                        if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $jsIndex . '/template/' . $jsValue)) {
                            $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                    $_SERVER['HTTP_HOST'] . "/" . SP_CORE_SUB_DOC_PATH . "/module/" .
                                    $jsIndex . "/template/" . $jsValue . "\"></script>\n";
                        } elseif ($jsIndex == "systemCore") {
                            $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                    SP_CORE_TEMPLATE_PATH . "/" . $jsValue . "\"></script>\n";
                        } else {
                            self::addError($jsValue . " not Found");
                        }
                    }
                }
            }
        }
        if (is_array($this->jsFileName)) {
            foreach ($this->jsFileName as $nextArr) {
                foreach ($nextArr as $jsIndex => $jsValue) {
                    if (is_array($jsValue)) {
                        foreach ($jsValue as $a => $b) {
// ($_SERVER['SERVER_ADDR']."/". SP_CORE_SUB_DOC_PATH ."/". $filename)
                            if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $jsIndex . '/template/' . $a)) {
                                if (!empty($b)) {
                                    $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                            SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                            SP_CORE_SUB_DOC_PATH . "/module/" . $jsIndex . "/template/" . $a .
                                            "?" . $b . "\"></script>\n";
                                } else {
                                    $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                            SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                            SP_CORE_SUB_DOC_PATH . "/module/" . $jsIndex . "/template/" . $a .
                                            "\"></script>\n";
                                }
                            } elseif ($jsIndex == "systemCore") {
                                if (!empty($b)) {
                                    $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                            SP_CORE_TEMPLATE_PATH . "/" . $a . "?" . $b . "\"></script>\n";
                                } else {
                                    $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                            SP_CORE_TEMPLATE_PATH . "/" . $a . "\"></script>\n";
                                }
                            } else {
                                self::addError($a . " not Found");
                            }
                        }
                    } else {
                        if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $jsIndex . '/template/' . $jsValue)) {
                            $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                    $_SERVER['HTTP_HOST'] . "/" . SP_CORE_SUB_DOC_PATH . "/module/" .
                                    $jsIndex . "/template/" . $jsValue . "\"></script>\n";
                        } elseif ($jsIndex == "systemCore") {
                            $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"" .
                                    SP_CORE_TEMPLATE_PATH . "/" . $jsValue . "\"></script>\n";
                        } else {
                            self::addError($jsValue . " not Found");
                        }
                    }
                }
            }
        }
        $complete_out = str_replace('{PORTAL_TEMPLATE_JSFILES}', $jsScript, $complete_out);
        $cssScript = "";
        if (is_array($this->cssNoModFileName)) {
            foreach ($this->cssNoModFileName as $nextArr) {
                foreach ($nextArr as $cssIndex => $cssValue) {
                    if (is_array($cssValue)) {
                        foreach ($cssValue as $a => $b) {
                            if (file_exists(SP_CORE_DOC_ROOT . '/' . SP_CORE_TEMPLATE_PATH . '/' . $cssIndex)) {
//if(file_exists(SP_CORE_DOC_ROOT .'/module/'.$cssIndex.'/template/'.$a)) {
                                /*
                                 * if(!empty($b)) {
                                  $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"". SP_CORE_HTTP_PROTOKOLL ."://".$_SERVER['HTTP_HOST']."/". SP_CORE_SUB_DOC_PATH ."/". SP_CORE_TEMPLATE_PATH ."/".$a."?".$b."\"></script>\n";
                                  }else {
                                  $jsScript .= "<script type=\"text/javascript\" language=\"JavaScript\" src=\"". SP_CORE_HTTP_PROTOKOLL ."://".$_SERVER['HTTP_HOST']."/". SP_CORE_SUB_DOC_PATH ."/". SP_CORE_TEMPLATE_PATH ."/".$jsIndex."\"></script>\n";
                                  }
                                 */
                                if (!empty($b)) {
                                    $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                            SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                            SP_CORE_SUB_DOC_PATH . "/" . SP_CORE_TEMPLATE_PATH . "/" .
                                            $a . "?" . $b . "\">\n";
                                } else {
                                    $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                            SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                            SP_CORE_SUB_DOC_PATH . "/" . SP_CORE_TEMPLATE_PATH . "/" .
                                            $cssIndex . "/" . $a . "\">\n";
                                }
                            }/* elseif($cssIndex == "systemCore") {
                              if(!empty($b)) {
                              $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"". SP_CORE_TEMPLATE_PATH ."/".$a."?".$b."\">\n";
                              }else {
                              $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"". SP_CORE_TEMPLATE_PATH ."/".$a."\">\n";
                              }
                              }else {
                              self::addError($a." not Found");
                              } */
                        }
                    } else {
                        if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $cssIndex . '/template/' . $cssValue)) {
                            $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                    SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                    SP_CORE_SUB_DOC_PATH . "/module/" . $cssIndex . "/template/" . $cssValue . "\">\n";
                        } elseif ($cssIndex == "systemCore") {
                            $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                    SP_CORE_TEMPLATE_PATH . "/" . $cssValue . "\">\n";
                        } else {
                            self::addError($cssValue . " not Found");
                        }
                    }
                }
            }
        }
        if (is_array($this->cssFileName)) {
            foreach ($this->cssFileName as $nextArr) {
                foreach ($nextArr as $cssIndex => $cssValue) {
                    if (is_array($cssValue)) {
                        foreach ($cssValue as $a => $b) {
                            if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $cssIndex . '/template/' . $a)) {
                                if (!empty($b)) {
                                    $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                            SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                            SP_CORE_SUB_DOC_PATH . "/module/" . $cssIndex . "/template/" .
                                            $a . "?" . $b . "\">\n";
                                } else {
                                    $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                            SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                            SP_CORE_SUB_DOC_PATH . "/module/" . $cssIndex . "/template/" .
                                            $a . "\">\n";
                                }
                            } elseif ($cssIndex == "systemCore") {
                                if (!empty($b)) {
                                    $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                            SP_CORE_TEMPLATE_PATH . "/" . $a . "?" . $b . "\">\n";
                                } else {
                                    $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                            SP_CORE_TEMPLATE_PATH . "/" . $a . "\">\n";
                                }
                            } else {
                                self::addError($a . " not Found");
                            }
                        }
                    } else {
                        if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $cssIndex . '/template/' . $cssValue)) {
                            $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                    SP_CORE_HTTP_PROTOKOLL . "://" . $_SERVER['HTTP_HOST'] . "/" .
                                    SP_CORE_SUB_DOC_PATH . "/module/" . $cssIndex . "/template/" . $cssValue . "\">\n";
                        } elseif ($cssIndex == "systemCore") {
                            $cssScript .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" .
                                    SP_CORE_TEMPLATE_PATH . "/" . $cssValue . "\">\n";
                        } else {
                            self::addError($cssValue . " not Found");
                        }
                    }
                }
            }
        }
        if (!empty($this->strReplace)) {
            foreach ($this->strReplace as $index => $value) {
                $complete_out = str_replace($index, $value, $complete_out);
//die($value);
            }
        }

//$complete_out = self::checkThirdPartyScripts($complete_out);

        $complete_out = str_replace('{SP_PORTAL_SITEMAP_URL}', SP_PORTAL_SITEMAP_URL, $complete_out);
        $complete_out = str_replace('{PORTAL_TEMPLATE_CSSFILES}', $cssScript, $complete_out);
        $complete_out = str_replace("{PORTAL_SUBDOC_PATH}", SP_CORE_SUB_DOC_PATH, $complete_out);
        $complete_out = str_replace("{PORTAL_HTTP_HOST}", SP_PORTAL_HTTP_HOST, $complete_out);


        return str_replace('{PORTAL_TEMPLATE_PATH}', SP_CORE_TEMPLATE_PATH, $complete_out);
    }

    public function addError($err) {
        $this->lastOut[] = '<span style="color:#FF0000">err : ' . $err . '</span><br>';
    }

    public function addLastOut($out) {
        $this->lastOut[] = $out;
    }

    function __destruct() {
        if (is_array($this->lastOut)) {
            foreach ($this->lastOut as $out) {
                echo $out;
            }
        }
    }

}

<?php

/* spPortalSystem Cportal_admincenter.php
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
require_once (SP_CORE_DOC_ROOT . "/module/portal/includes/CLang.php");

class Cportal_admincenter {

    private $db;
    private $reqVar;
    private $permission;
    private $myPBoxes;
    private $uploadPathImg = "/module/portal/uploadimage/";

    public function __construct($db, $template, $session, $requestVar, $permission) {
        $this->db = $db;
        $this->reqVar = $requestVar;
        $this->permission = $permission;
        /*
          if (SP_CORE_ENCODING == "UTF-8") {
          foreach ($this->reqVar as $index => $value) {
          if ($index != "sites") {
          $this->reqVar[$index] = @utf8_decode((empty($value) ? $index : $value));
          }
          }
          }
         */
        // -> load box struc and side struc
        $sql = "SELECT * FROM sp_modul_settings WHERE modul_name = '' AND modul_installed = 0 AND modul_box_activ = 1 ORDER BY modul_box_pos ASC";
        $result = $this->db->query($sql);

        $this->dynBox[0]['boxObj'] = null;
        if ($result) {
            while ($o_res = $this->db->fetch_assoc($result)) {
                $this->dynBox[$o_res['ID']]['boxObj'] = $o_res;
                $this->myPBoxes[$o_res['ID']] = $o_res['modul_box_titel'];
            }
        }
    }

    public function portal_genSitemapManuell() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            if ($this->portal_genSitemapXML() === true) {
                spcore\CTemplate::getInstance()->addLastOut("<script>alert(\"Sitemap saved/update \")</script>");
            } else {
                spcore\CTemplate::getInstance()->addLastOut("<script>alert(\"Sitemap saved/update \")</script>");
            }
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_settings');
            exit;
        } else {
            $this->notAllowed();
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
        }
    }

    private function portal_genSitemapXML() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $source = 'sitemap.xml';
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->formatOutput = true;

            $xml = $dom->appendChild($dom->createElement('urlset'));
            $xml_attr = $xml->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");

            $sql = "SELECT * FROM sp_modul_settings WHERE modul_active=1 AND modul_installed=1";
            $result = $this->db->query($sql);

            while ($res = $this->db->fetch_assoc($result)) {
                if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $res['modul_name'] . "/includes/C" . $res['modul_name'] . "_admincenter.php")) {
                    require_once SP_CORE_DOC_ROOT . '/module/' . $res['modul_name'] . '/includes/C' . $res['modul_name'] . '_admincenter.php';
                    if (method_exists("C" . $res['modul_name'] . "_admincenter", '_ext_getSidemapArray')) {
                        $myobjClass = "C" . $res['modul_name'] . "_admincenter";
                        $tmp = $myobjClass::_ext_getSidemapArray($this->db);
                        foreach ($tmp as $tmpURL) {
                            $url = $xml->appendChild($dom->createElement('url'));
                            $loc = $url->appendChild($dom->createElement('loc'));
                            $loc->appendChild($dom->createTextNode($tmpURL));
                            // OPTIONAL
                            $lastmod = $url->appendChild($dom->createElement('lastmod'));
                            $lastmod->appendChild($dom->createTextNode(date(DATE_W3C)));
                            $changefreq = $url->appendChild($dom->createElement('changefreq'));
                            $changefreq->appendChild($dom->createTextNode("daily"));
                            $priority = $url->appendChild($dom->createElement('priority'));
                            $priority->appendChild($dom->createTextNode("1.0"));
                        }
                    }
                }
            }

            /*
             * // Subsites in sitemap

             */

            // -> load box struc and side struc
            $sql = "SELECT * FROM sp_modul_settings WHERE modul_name = '' AND modul_installed = 0 AND modul_box_activ = 1 ORDER BY modul_box_pos ASC";
            $result = $this->db->query($sql);



            $this->dynBox[0]['boxObj'] = null;
            if ($result) {
                while ($o_res = $this->db->fetch_assoc($result)) {
                    $this->dynBox[$o_res['ID']]['boxObj'] = $o_res;
                    $this->myPBoxes[$o_res['ID']] = $o_res['modul_box_titel'];
                }
            }

            // <- load box struc and side struc
            // -> add main side for struc
            $sql = "SELECT
                            a.*,
                            b.titel_text as page_titel
                    FROM
                            sp_modul_portal_cms_menue_main_side as a,
                            sp_modul_portal_cms_page as b
                    WHERE
                            a.page_id = b.ID AND
                            a.active = 1
                    ORDER BY
                            a.position
                    ASC";

            $result = $this->db->query($sql);
            if ($result) {
                while ($o_res = $this->db->fetch_assoc($result)) {
                    $this->dynBox[($o_res['box'] == 0 ? 0 : $o_res['box'])]['sides'][$o_res['ID']] = $o_res;
                }
                // <- add main side for struc
                // -> add sub side for struc
                $sql = "SELECT
					a.*,
					b.titel_text as page_titel,
					c.box as main_box
				FROM
					sp_modul_portal_cms_menue_sub_side as a,
					sp_modul_portal_cms_menue_main_side as c,
					sp_modul_portal_cms_page as b
				WHERE
					a.page_id = b.ID
				AND c.ID = a.mainlink_id
				AND a.active = 1
				AND c.active = 1
				ORDER BY a.position ASC";

                $result = $this->db->query($sql);
                if ($result) {
                    while ($o_res = $this->db->fetch_assoc($result)) {
                        $this->dynBox[($o_res['main_box'] == 0 ? 0 : $o_res['main_box'])]['sides'][$o_res['mainlink_id']]['subsides'][$o_res['page_id']] = $o_res;
                    }
                }
                // <- add sub side for struc
            }

            if (is_array($this->dynBox)) {

                foreach ($this->dynBox as $myBox => $value) {
                    // mainlinks ->
                    if (isset($value['sides']) && is_array($value['sides'])) {
                        foreach ($value['sides'] as $sides) {
                            //$boxTag = null;
                            //$boxTag = $this->dynBox[($myBox == 0 ? 0 : $myBox)]['boxObj']['modul_box_titel'];
                            if (!isset($this->dynBox[($myBox == 0 ? 0 : $myBox)]['boxObj']['modul_box_titel']) || empty($this->dynBox[($myBox == 0 ? 0 : $myBox)]['boxObj']['modul_box_titel'])) {
                                $boxTag = "Navigation";
                            }
                            $boxTag = str_replace("/", "-", $boxTag);
                            if (isset($sides['ID'])) {
                                $box['jsId'] = $sides['ID'];
                                if (isset($this->boxMenue[($myBox == 0 ? 0 : $myBox)]) &&
                                        isset($this->boxMenue[($myBox == 0 ? 0 : $myBox)][$sides['position']]) &&
                                        $this->boxMenue[($myBox == 0 ? 0 : $myBox)][$sides['position']] == 0) {

                                    if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
                                        $myUrl = $this->clearURLForRewrite($sides['page_id'], $sides['ID'], $boxTag, $sides['link_text']);
                                    } else {
                                        $myUrl = "http://" . $_SERVER['HTTP_HOST'] . "?modul=portal&action=CMS&page=" . $sides['page_id'] . "&sub=" . $sides['ID'];
                                    }
                                } else {
                                    if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
                                        $myUrl = $this->clearURLForRewrite($sides['page_id'], $sides['ID'], $boxTag, $sides['link_text']);
                                    } else {
                                        $myUrl = "http://" . $_SERVER['HTTP_HOST'] . "?modul=portal&action=CMS&page=" . $sides['page_id'] . "&sub=" . $sides['ID'];
                                    }
                                }
                                // <- mainlinks
                                // Subsites in sitemap
                                $url = $xml->appendChild($dom->createElement('url'));
                                $loc = $url->appendChild($dom->createElement('loc'));
                                $loc->appendChild($dom->createTextNode($myUrl));
                                // OPTIONAL
                                $lastmod = $url->appendChild($dom->createElement('lastmod'));
                                $lastmod->appendChild($dom->createTextNode(date(DATE_W3C)));
                                $changefreq = $url->appendChild($dom->createElement('changefreq'));
                                $changefreq->appendChild($dom->createTextNode("daily"));
                                $priority = $url->appendChild($dom->createElement('priority'));
                                $priority->appendChild($dom->createTextNode("1.0"));
                                // sublinks ->
                                if (isset($sides['subsides'])) {
                                    foreach ($sides['subsides'] as $mySides => $mySidesValue) {
                                        if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
                                            $myUrl = $this->clearURLForRewrite($mySidesValue['page_id'], $sides['ID'], $boxTag, $mySidesValue['link_text']);
                                        } else {
                                            $myUrl = "http://" . $_SERVER['HTTP_HOST'] . "?modul=portal&action=CMS&page=" . $mySidesValue['page_id'] . "&sub=" . $sides['ID'];
                                        }
                                        // Subsites in sitemap
                                        $url = $xml->appendChild($dom->createElement('url'));
                                        $loc = $url->appendChild($dom->createElement('loc'));
                                        $loc->appendChild($dom->createTextNode($myUrl));
                                        // OPTIONAL
                                        $lastmod = $url->appendChild($dom->createElement('lastmod'));
                                        $lastmod->appendChild($dom->createTextNode(date(DATE_W3C)));
                                        $changefreq = $url->appendChild($dom->createElement('changefreq'));
                                        $changefreq->appendChild($dom->createTextNode("daily"));
                                        $priority = $url->appendChild($dom->createElement('priority'));
                                        $priority->appendChild($dom->createTextNode("0.8"));
                                    }
                                }
                                //<- sublinks
                            }
                        }
                    }
                }
            }
            // SAVE sitemap
            try {
                if (@$dom->save(SP_CORE_DOC_ROOT . "/" . $source)) {
                    return true;
                } else {
                    throw new Exception(" Write permission failed ...");
                }
            } catch (Exception $e) {
                spcore\CLog::getInstance()->log(SP_LOG_ERROR, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " " . $e->getMessage());
            }
        }
    }

    public static function notAllowed() {
        spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", "Not Allowed");
    }

    private function clearoldUrl($s) {
        $sql = "SELECT
                        a.*,
                        b.titel_text as page_titel,
                        c.box as main_box
                FROM
                        sp_modul_portal_cms_menue_sub_side as a,
                        sp_modul_portal_cms_menue_main_side as c,
                        sp_modul_portal_cms_page as b
                WHERE
                        a.page_id = " . $this->db->checkValue($s[2]) . "
                AND a.page_id = b.ID
                AND c.ID = a.mainlink_id
                ORDER BY a.position ASC";

        $result = $this->db->query($sql);

        if ($result && $this->db->num_rows($result) > 0) {
            $res = $this->db->fetch_array($result);
            return ("<a href=\"{PORTAL_HTTP_HOST}/p/" . $s[2] . "_" . $s[3] . "_" . $this->clearString($this->myPBoxes[$res['main_box']]) . "-" . $this->clearString($res['link_text']) . ".html");
        } else {
            $sql = "SELECT
                            a.*, 
                            b.titel_text as page_titel
                    FROM
                            sp_modul_portal_cms_menue_main_side as a,
                            sp_modul_portal_cms_page as b
                    WHERE
                            a.page_id = b.ID AND
                           a.page_id = " . $this->db->checkValue($s[2]);

            $result = $this->db->query($sql);
            $res = $this->db->fetch_array($result);
            return ("<a href=\"{PORTAL_HTTP_HOST}/p/" . $s[2] . "_" . $s[3] . "_" . $this->clearString($this->myPBoxes[$res['main_box']]) . "-" . $this->clearString($res['link_text']) . ".html");
        }
    }

    private function clearString($string) {
        $string = str_replace("/", "-", $string);
        $string = str_replace(" ", "-", $string);
        $string = str_replace(".", "-", $string);
        $string = str_replace(":", "", $string);
        return $string;
    }

    private function clearForURLRewrite($content) {
        if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
            $content = preg_replace_callback("!<a href=(.*?)modul=portal&amp;action=CMS&amp;page=([0-9]+)&amp;sub=([0-9]+),([0-9]+)!", 'self::clearoldUrl', $content);
            $content = preg_replace_callback("!<a href=(.*?)modul=portal\&action=CMS\&page=([0-9]+)\&sub=([0-9]+),([0-9]+)!", 'self::clearoldUrl', $content);
        }
        return $content;
    }

    private function clearURLForRewrite($pageid, $subid, $boxname, $linkname) {
        return"http://" . $_SERVER['HTTP_HOST'] . "/" . (SP_CORE_SUB_DOC_PATH != "" ? SP_CORE_SUB_DOC_PATH . "/" : "") . "p/" . $pageid . "_" . $subid . "_" . $boxname . "-" . $this->clearString($linkname) . ".html";
    }

    /**
     * @brief Add lang into database
     *
     * @return unknown_type
     */
    public function portal_setLang() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $langArray = CLang::getInstance()->getAvaibleLangs();
            $lang = $langArray[$this->reqVar['lang']];
            unset($langArray);
            CLang::getInstance()->setLanguage($this->db, $lang['value'], $lang['text']);
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_settings');
            exit;
        } else {
            $this->notAllowed();
        }
    }

    /**
     * @brief Set your portal settings here
     *
     * @param unknown_type $template
     * @param unknown_type $db
     * @param unknown_type $reqVar
     */
    public function portal_settings() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $boxArray = array();
            $rssSystem = 0;
            $newsSystem = 0;

            $xmlObj = spcore\CTemplate::getInstance()->loadModulTemplateXML("portal", "admin_settings");
            $tpl_main = $xmlObj->mainForm;
            $tpl_langOptions = $xmlObj->langOptions;
            $tpl_installedLangs = $xmlObj->installedLangs;
            $tpl_langMainForm = $xmlObj->langMainForm;

            $langArray = CLang::getInstance()->getAvaibleLangs();
            //---
            $lang_boxArray['langOptions'] = null;
            foreach ($langArray as $value) {
                $boxContent['value'] = $value['value'];
                $boxContent['select'] = $value['select'];
                $boxContent['text'] = $value['text'];
                $lang_boxArray['langOptions'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxContent, $tpl_langOptions);
            }
            //---
            $sql = "SELECT * FROM sp_modul_portal_settings WHERE ID = 1";
            $result = $this->db->query($sql);

            if ($result && $this->db->num_rows($result) > 0) {
                $resObj = $this->db->fetch_object($result);
                $rssSystem = $resObj->func_rss;
                $newsSystem = $resObj->func_news;
                $domainSystem = $resObj->domainActive;
                $boxArray['rssCount'] = $resObj->rss_counter;
            }
            $lang_boxArray['installedLangs'] = null;

            $sql = "SELECT * FROM sp_modul_portal_language";
            $result = $this->db->query($sql);
            if ($result && $this->db->num_rows($result) > 0) {
                unset($boxContent);
                while ($res = $this->db->fetch_assoc($result)) {
                    $boxContent['langId'] = $res['id'];
                    $boxContent['langTag'] = $res['lang_lang'];
                    $boxContent['langCountry'] = $res['lang_name'];
                    $lang_boxArray['installedLangs'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxContent, $tpl_installedLangs);
                }
            }

            $boxArray['langSettings'] = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $lang_boxArray, $tpl_langMainForm);
            $boxArray['imgRSS'] = ($rssSystem == 1 ? '{LANG_portal_imgRSSActive}' : '{LANG_portal_imgRSSInactive}');
            $boxArray['imgNews'] = ($newsSystem == 1 ? '{LANG_portal_imgNewsActive}' : '{LANG_portal_imgNewsInactive}');
            $boxArray['imgDomain'] = ($domainSystem == 1 ? '{LANG_portal_imgDomainActive}' : '{LANG_portal_imgDomainInactive}');

            $boxContent = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $tpl_main);
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_setBoxPosUp() {
        if (isset($this->permission['portal']['admin'])
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['admin'] == true
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $sql = "SELECT * FROM sp_modul_settings WHERE modul_box_activ = 1 ORDER BY modul_box_pos ASC";
            $result = $this->db->query($sql);
            $i = 1;
            while ($res = $this->db->fetch_array($result)) {
                $myBoxes[$i++] = $res['ID']; //$res['modul_box_pos'];
            }
            //swap position
            foreach ($myBoxes as $pos => $id) {
                if ($id == $this->reqVar['id']) {
                    $a = $myBoxes[$pos];
                    $b = $myBoxes[$pos - 1];

                    $myBoxes[$pos] = $b;
                    $myBoxes[$pos - 1] = $a;

                    continue;
                }
            }
            // why all boxes update ? to clear position !
            foreach ($myBoxes as $pos => $id) {
                $sql = "UPDATE `sp_modul_settings` SET `modul_box_pos` = " . $pos . " WHERE `ID` = " . $this->db->checkValue($id);
                $result = $this->db->query($sql);
            }

            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_watchBoxes');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_setBoxPosDown() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $sql = "SELECT * FROM sp_modul_settings WHERE modul_box_activ = 1 ORDER BY modul_box_pos ASC";
            $result = $this->db->query($sql);
            $i = 1;
            while ($res = $this->db->fetch_array($result)) {
                $myBoxes[$i++] = $res['ID']; //$res['modul_box_pos'];
            }
            // swap position
            foreach ($myBoxes as $pos => $id) {
                if ($id == $this->reqVar['id']) {
                    $a = $myBoxes[$pos];
                    $b = $myBoxes[$pos + 1];

                    $myBoxes[$pos] = $b;
                    $myBoxes[$pos + 1] = $a;

                    continue;
                }
            }
            // why all boxes update ? to clear position !
            foreach ($myBoxes as $pos => $id) {
                $sql = "UPDATE `sp_modul_settings` SET `modul_box_pos` = " . $pos . " WHERE `ID` = " . $this->db->checkValue($id);
                $result = $this->db->query($sql);
            }

            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_watchBoxes');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * @brief Delete portal language
     * @details
     * You can't delete your system language.
     *
     */
    public function portal_delLang() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            if ($this->reqVar['langTag'] != SP_CORE_LANG) {
                CLang::getInstance()->delLanguage($this->db, $this->reqVar['langTag']);
            }
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_settings');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * @method portal_newsActive
     * @brief update news status
     *
     */
    public function portal_newsActive() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {
            /* $sql = "SELECT func_news FROM sp_modul_portal_settings WHERE ID = 1";
              $result = $this->db->query($sql);
              if ($result && $this->db->num_rows($result) > 0) {
              $res = $this->db->fetch_array($result);
             */
            $sql = "UPDATE sp_modul_portal_settings SET func_news = IF(func_news=0,'1','0') WHERE ID = 1";
            $this->db->query($sql);
            //}
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_settings');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief update rss status
     *
     */
    public function portal_rssActive() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {
            /* $sql = "SELECT func_rss FROM sp_modul_portal_settings WHERE ID = 1";
              $result = $this->db->query($sql);
              if ($result && $this->db->num_rows($result) > 0) {
              $res = $this->db->fetch_array($result);
             */
            $sql = "UPDATE sp_modul_portal_settings SET func_rss = IF(func_rss=0,'1','0') WHERE ID = 1";
            $this->db->query($sql);
            //}
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_settings');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief add your box to system
     */
    public function portal_addBox() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_new'] == 1) {

            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/tiny_mce/tiny_mce.js");
            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/myPortal.js");
            spcore\CTemplate::getInstance()->addCssScript("portal", "scripts/css/myPortal.css");

            $boxArray = Array();
            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_addBox_content");
            $boxContent = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $boxContent);

            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_adminBoxAdd}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief adding new box to database
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_newBox() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_new'] == 1) {

            $sql = "SELECT modul_box_pos FROM sp_modul_settings ORDER BY modul_box_pos DESC";
            $result = $this->db->query($sql);
            $res = $this->db->fetch_object($result);

            $sql = "INSERT INTO
                            sp_modul_settings (
                                    modul_name,
                                    modul_active,
                                    modul_installed,
                                    modul_admin_box,
                                    modul_admin_box_r,
                                    modul_box_activ,
                                    modul_box_r,
                                    modul_box_content,
                                    modul_box_content_dyn,
                                    modul_box_titel,
                                    modul_box_pos
                            ) VALUES ( '',0,0,0,0,0,0,
                                '" . $this->db->checkValue($this->reqVar['box_content']) . "',
                                0,
                                '" . $this->db->checkValue(self::clearMe($this->reqVar['box_titel'])) . "',
                                " . ($res->modul_box_pos + 1) . ")";

            $result = $this->db->query($sql);
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_watchBoxes');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief watch all boxes
     *
     */
    public function portal_watchBoxes() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_edit'] == 1) {

            $box['content'] = null;
            $tplContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_editBox_contentContent");
            $myTmp = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_editBox_contentMain");
            $sql = "SELECT * FROM sp_modul_settings WHERE modul_name = '' AND modul_installed = 0 AND modul_box_r = 0 ORDER BY modul_box_pos ASC";
            $result = $this->db->query($sql);

            while ($res = $this->db->fetch_array($result)) {
                $boxArray['boxId'] = $res['ID'];
                $boxArray['boxTitel'] = self::clearMe($res['modul_box_titel']);
                $boxArray['boxContent'] = $res['modul_box_content'];
                $boxArray['boxStatus'] = $res['modul_box_activ'] == 1 ? '{LANG_portal_imgBoxActive}' : '{LANG_portal_imgBoxInactive}';
                $boxArray['boxPos'] = $res['modul_box_r'] == 1 ? '{LANG_portal_imgBoxR}' : '{LANG_portal_imgBoxL}';
                //$boxArray['boxDel']	= 'del';
                //$boxArray['boxEdit']	= 'edit';

                $box['content'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $tplContent);
            }

            $boxContent = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $box, $myTmp);
            $box['content'] = null;
            $sql = "SELECT * FROM sp_modul_settings WHERE modul_name = '' AND modul_installed = 0 AND modul_box_r = 1 ORDER BY modul_box_pos ASC";
            $result = $this->db->query($sql);

            while ($res = $this->db->fetch_array($result)) {
                $boxArray['boxId'] = $res['ID'];
                $boxArray['boxTitel'] = self::clearMe($res['modul_box_titel']);
                $boxArray['boxContent'] = $res['modul_box_content'];
                $boxArray['boxStatus'] = $res['modul_box_activ'] == 1 ? '{LANG_portal_imgBoxActive}' : '{LANG_portal_imgBoxInactive}';
                $boxArray['boxPos'] = $res['modul_box_r'] == 1 ? '{LANG_portal_imgBoxR}' : '{LANG_portal_imgBoxL}';
                //$boxArray['boxDel']	= 'del';
                //$boxArray['boxEdit']	= 'edit';

                $box['content'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $tplContent);
            }

            //$boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal","admin_editBox_contentMain");
            $boxContent .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $box, $myTmp);

            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief Set the box active status
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_setBoxStatus() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_edit'] == 1) {
            /* $sql = "SELECT modul_box_activ FROM sp_modul_settings WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
              $result = $this->db->query($sql);
              $res = $this->db->fetch_array($result);
             *
             */
            $sql = "UPDATE sp_modul_settings SET modul_box_activ = IF(modul_box_activ =0,'1','0') WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
            $result = $this->db->query($sql);

            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_watchBoxes');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief set box side position r or l
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_setBoxPos() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_edit'] == 1) {
            /* $sql = "SELECT modul_box_r FROM sp_modul_settings WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
              $result = $this->db->query($sql);
              $res = $this->db->fetch_array($result);
             *
             */
            $sql = "UPDATE sp_modul_settings SET modul_box_r = IF(modul_box_r=0,'1','0') WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
            $result = $this->db->query($sql);

            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_watchBoxes');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief set the list position for boxes
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_setBoxListPos() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_edit'] == 1) {
            // ToDo
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief delete box with id
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_delBox() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_edit'] == 1) {

            $sql = "DELETE FROM `sp_modul_settings` WHERE `ID` = " . $this->db->checkValue($this->reqVar['id']) . " LIMIT 1";
            $this->db->query($sql);
            $sql = "UPDATE  `sp_modul_portal_cms_menue_main_side` SET box = 0 ,active = 0 WHERE box = " . $this->db->checkValue($this->reqVar['id']);
            $this->db->query($sql);
            $sql = "UPDATE  `sp_modul_portal_cms_menue_sub_side` SET box = 0 ,active = 0 WHERE box = " . $this->db->checkValue($this->reqVar['id']);
            $this->db->query($sql);

            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_watchBoxes');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief edit your box
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_editBox() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_edit'] == 1) {

            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/tiny_mce/tiny_mce.js");
            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/myPortal.js");
            spcore\CTemplate::getInstance()->addCssScript("portal", "scripts/css/myPortal.css");

            $sql = "SELECT ID,modul_box_titel,modul_box_content FROM sp_modul_settings WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
            $result = $this->db->query($sql);
            $res = $this->db->fetch_array($result);

            $boxArray['boxId'] = $res['ID'];
            $boxArray['valueBoxTitel'] = self::clearMe($res['modul_box_titel']);
            $boxArray['valueBoxContent'] = $res['modul_box_content'];
            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_editBox_content");
            $boxContent = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $boxContent);

            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_adminBoxEdit}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    private function portal_transformToDynamic($content) {
        return str_replace('{PORTAL_HTTP_HOST}', SP_PORTAL_HTTP_HOST, $content);
    }

    private function portal_transformToStatic($content) {
        return str_replace(SP_PORTAL_HTTP_HOST, '{PORTAL_HTTP_HOST}', $content);
    }

    /**
     * \brief update box titel and content
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_updateBoxContent() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['box_edit'] == 1) {

            $sql = "UPDATE
                        sp_modul_settings 
                    SET 
                            modul_box_titel = '" . $this->db->checkValue(self::clearMe($this->reqVar['box_titel'])) . "',
                            modul_box_content='" . $this->db->checkValue($this->reqVar['box_content']) . "'
                    WHERE 
                            ID =" . $this->db->checkValue($this->reqVar['box_id']);
            $result = $this->db->query($sql);
            $this->portal_genSitemapXML();
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_watchBoxes');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief Add menue link
     *
     * @param unknown_type $template
     * @param unknown_type $db
     * @param unknown_type $reqVar
     */
    public function portal_addMenue() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $boxArray = array();
            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_box_content_settings");
            $boxContent = spcore\CTemplate::getInstance()->parseModulTemplate("nopaste", $boxArray, $boxContent);
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief Add a modul to main menue
     *
     * @param unknown_type $template
     * @param unknown_type $db
     * @param unknown_type $reqVar
     */
    public function portal_menueModulSettings() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $boxArray = array();
            $userData = array();
            $sql = array();
            $tplContent['dir'] = "";
            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_box_content_modul_addMenue");

            $sql = "SELECT * FROM sp_modul_portal_modul_menue";
            $result = $this->db->query($sql);

            while ($res = $this->db->fetch_array($result)) {
                $userData[$res['name']]['modul_active'] = $res['modul_active'];
                $userData[$res['name']]['modul_pos'] = $res['pos'];
            }

            $dir = dir("module/");
            while ($modulName = $dir->read()) {
                if (($modulName != ".svn") && ($modulName != ".") && ($modulName != "..") && file_exists("module/" . $modulName . "/" . $modulName . "_portal_menue.php")) {

                    require_once ("module/" . $modulName . "/" . $modulName . "_portal_menue.php");

                    if (!function_exists($modulName . "_watchModulMenue(&\$template, &\$db, &\$reqVar)")) {
                        if (!isset($userData[$modulName]) || empty($userData[$modulName]['modul_active'])) {
                            $userData[$modulName]['modul_active'] = 0;
                        }
                        if (isset($reqVar[$modulName]) && $reqVar[$modulName] == 1) {
                            $userData[$modulName]['modul_active'] = 1;
                            $this->db->query("REPLACE INTO  sp_modul_portal_modul_menue (pos, name, modul_active ) VALUES ('" . $this->db->checkValue($this->reqVar[$modulName . '_pos']) . "','" . $modulName . "','1')");
                        } elseif (isset($this->reqVar[$modulName]) && $this->reqVar[$modulName] == 0) {
                            $userData[$modulName]['modul_active'] = 0;
                            $this->reqVar[$modulName . '_pos'] = '';
                            $this->db->query("REPLACE INTO  sp_modul_portal_modul_menue (pos, name, modul_active ) VALUES ('" . $this->db->checkValue($this->reqVar[$modulName . '_pos']) . "','" . $modulName . "','0')");
                        }
                        $boxArray['pos'] = (isset($userData[$modulName]['modul_pos']) ? $userData[$modulName]['modul_pos'] : '');
                        $boxArray['install'] = '<input type="radio" name="' . $modulName . '" value="1" ' . ($userData[$modulName]['modul_active'] == 1 ? 'checked' : '') . '>';
                        $boxArray['uninstall'] = '<input type="radio" name="' . $modulName . '" value="0" ' . ($userData[$modulName]['modul_active'] == 0 ? 'checked' : '') . '>';
                        $boxArray['name'] = $modulName;
                        $tplContent['dir'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $boxContent);
                    } else {
                        echo ($modulName . "_watchModulMenue(&\$template, &\$db, &\$reqVar)");
                    }
                }
            }
            $dir->close();

            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_box_modul_addMenue", $tplContent);
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_mainmenue}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief List all sides and subsides
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSWatchAllSides() {

        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            // template loading ->
            spcore\CTemplate::getInstance()->doNotCacheThisSide();
            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/prototype-1.6.0.3.js");
            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/myPortal.js");
            spcore\CTemplate::getInstance()->addCssScript("portal", "scripts/css/myPortal.css");

            $tplXML = spcore\CTemplate::getInstance()->loadModulTemplateXML("portal", "admin_box_editSide"); //page titel
            $mySideMain = $tplXML->mainform;  // 1

            $tpl_LangTab = $tplXML->langTab;  // mySideMain 				1:n mySideLangTab
            $tpl_LangTabHead = $tplXML->langTabHead;  // mySideMain 				1:n mySideLangTab
            $tpl_LangTabContent = $tplXML->langTabContent; // mySideLangTab 			n:n mySideLangTabContent
            $tpl_LangTabContentSub = $tplXML->langTabContentSub; // mySideLangTabContent                 1:n mySideLangTabContentSub

            $myTemplate['tabTabbing'] = '';
            $myTemplate['sides'] = '';
            $myLangArray = CLang::getInstance()->getInstalledLangs($this->db);

            //** loading all domains from database for checkbox
            $isDomains = true;
            $sql = "SELECT * FROM sp_modul_portal_domain ORDER BY ID";
            $domainResult = $this->db->query($sql);
            if ($this->db->num_rows($domainResult) > 0) {
                while ($domainRes = $this->db->fetch_array($domainResult)) {
                    $myDomain[] = $domainRes;
                }
            } else {
                $isDomains = false;
            }
            //** --
            //** flagicons
            if (is_array($myLangArray)) {
                foreach ($myLangArray as $langValue) {
                    $myTemplate['tabTabbing'] .= "<li><a href=\"#" . $langValue['lang_lang'] . "\">
                        <img src=\"module/portal/template/images/flagicons/" . $langValue['lang_lang'] . ".png\"> "
                            . $langValue['lang_name'] . "</a></li>\n";
                }
            }

            // <- template loading
            // -> load box struc and side struc
            $sql = "SELECT * FROM sp_modul_settings WHERE modul_name = '' AND modul_installed = 0 AND modul_box_activ = 1 ORDER BY modul_box_pos";
            $result = $this->db->query($sql);

            $dynBox['boxObj'] = null;
            if ($result) {
                while ($o_res = $this->db->fetch_assoc($result)) {
                    $dynBox['boxObj'][$o_res['ID']] = $o_res;
                    $myPBoxes[$o_res['ID']] = $o_res['modul_box_titel'];
                }
            }
            // <- load box struc and side struc
            // -> add main side for struc
            $sql = "SELECT
                                            a.*,
                            b.titel_text as page_titel,
                            b.page_domain,
                            d.modul_box_r as boxposition
                                    FROM
                                            sp_modul_portal_cms_menue_main_side as a,
                            sp_modul_portal_cms_page as b,
                            sp_modul_settings as d
                    WHERE
                            a.page_id = b.ID
                                    ORDER BY
                                            a.position
                                    ASC";
            $result = $this->db->query($sql);
            if ($result) {
                while ($o_res = $this->db->fetch_assoc($result)) {
                    $dynBox[$o_res['lang_id']][($o_res['box'] == 0 ? 0 : $o_res['box'])]['sides'][$o_res['ID']] = $o_res;
                    $dynBox[$o_res['lang_id']][($o_res['box'] == 0 ? 0 : $o_res['box'])]['position'] = $o_res['boxposition'];
                }
            }
            // <- add main side for struc
            // -> add sub side for struc
            $sql = "SELECT
                                            a.*,
                                            b.titel_text as page_titel,
                                            b.page_domain,
                                            c.box as main_box,
                                            c.lang_id
                                    FROM
                                            sp_modul_portal_cms_menue_sub_side as a,
                                            sp_modul_portal_cms_menue_main_side as c,
                                            sp_modul_portal_cms_page as b
                                    WHERE
                                            a.page_id = b.ID
                                    AND
                                            c.ID = a.mainlink_id
                                    ORDER BY
                                            a.position";
            $result = $this->db->query($sql);
            if ($result) {
                while ($o_res = $this->db->fetch_assoc($result)) {
                    $dynBox[$o_res['lang_id']][($o_res['main_box'] == 0 ? 0 : $o_res['main_box'])]['sides'][$o_res['mainlink_id']]['subsides'][$o_res['page_id']] = $o_res;
                }
            }
            // <- add sub side for struc
            $myTemplate['langTabs'] = '';

            if (is_array($myLangArray)) {
                foreach ($myLangArray as $myLangIndex) {
                    if (isset($dynBox[$myLangIndex['id']]) && is_array($dynBox[$myLangIndex['id']])) {
                        foreach ($dynBox[$myLangIndex['id']] as $langSorted) {

                            if (isset($langSorted['position'])) {
                                switch ($langSorted['position']) {
                                    case 0:
                                        $boxside = "shape_align_left";
                                        break;
                                    case 1:
                                        $boxside = "shape_align_right";
                                        break;
                                    case 2:
                                        $boxside = "shape_align_top";
                                        break;
                                    default:
                                        $boxside = "shape_align_left";
                                        break;
                                }
                            }
                            // mainlinks ->
                            // box Marked
                            if (!isset($tpl_tmp['sides'])) {
                                //$tpl_tmp['sides'] = $tpl_LangTabHead;
                                $tpl_tmp['sides'] = spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('boxside' => $boxside), $tpl_LangTabHead);
                            } else {
                                $tpl_tmp['sides'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('boxside' => $boxside), $tpl_LangTabHead);
                                ;
                            }

                            if (isset($langSorted['sides']) && is_array($langSorted['sides'])) {
                                foreach ($langSorted['sides'] as $mySide) {
                                    if (isset($myLangArray[$mySide['lang_id']]['lang_lang'])) {
                                        // build boxmenue ->
                                        foreach ($langSorted as $myBox => $value) {
                                            $box = null;
                                            $box['boxName'] = spcore\CTemplate::getInstance()->makeOptionTag(0, "{LANG_portal_mainmenue}", ($myBox == 0 ? 1 : 0));
                                            if (isset($myPBoxes) && is_array($myPBoxes)) {
                                                foreach ($dynBox['boxObj'] as $p_index => $p_value) {
                                                    $box['boxName'] .= spcore\CTemplate::getInstance()->makeOptionTag($p_value['ID'], $p_value['modul_box_titel'], ($p_value['ID'] == $mySide['box'] ? 1 : 0));
                                                }
                                            }
                                        } // <- build boxmenue
                                        $box['langTag'] = $myLangArray[$mySide['lang_id']]['lang_lang'];
                                        $box['sideName'] = $mySide['link_text'];
                                        $box['sideTitle'] = substr($mySide['page_titel'], 0, 32) . " ...";

                                        $box['posValue'] = empty($mySide['position']) ? 0 : $mySide['position'];
                                        $box['sumUndersides'] = isset($mySide['subsides']) ? count($mySide['subsides']) : 0;
                                        $box['toplinkactive'] = isset($mySide['toplink']) && $mySide['toplink'] == 0 ? 'shape_align_top_grey' : 'shape_align_top';
                                        $box['pageid'] = $mySide['page_id'];
                                        $box['posId'] = $mySide['ID'];
                                        $box['box'] = $mySide['box'] == 0 ? '0' : $mySide['box'];
                                        $box['jsId'] = $mySide['ID'];

                                        if ($mySide['active'] == 1) {
                                            $box['active'] = "OK";
                                        } else {
                                            $box['active'] = "No";
                                        }
                                        //** Adding domain checkboxes for mainsides
                                        if (SP_CORE_LANG === $myLangArray[$mySide['lang_id']]['lang_lang']) {
                                            if ($isDomains) {
                                                $p_tmp = null;
                                                $p_tmp = @unserialize($mySide['page_domain']);
                                                $box['domain'] = "<nobr>";
                                                foreach ($myDomain as $domainIndex) {
                                                    //$box['domain'] .= "<a href=\"#\" onclick=\"addPageToDomain('" . $box['pageid'] . "','" . $domainIndex['ID'] . "','{PORTAL_TEMPLATE_PATH}',1)\">
                                                    $box['domain'] .= "<img onclick=\"addPageToDomain('" . $box['pageid'] . "','"
                                                            . $domainIndex['ID'] . "','{PORTAL_TEMPLATE_PATH}',1)\" id=\"d_img_"
                                                            . (isset($p_tmp[$domainIndex['ID']]) && is_array($p_tmp) ? "OK" : "NO") . "_"
                                                            . $box['pageid'] . "_" . $domainIndex['ID'] . "\" src=\"{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/"
                                                            . (isset($p_tmp[$domainIndex['ID']]) && is_array($p_tmp) ? "OK.png" : "No.png")
                                                            . "\" alt=\"Status\" border=\"0\" title=\"" . $domainIndex['domain'] . "\">";
                                                }
                                                $box['domain'] .= "</nobr>";
                                            } else {
                                                $box['domain'] = "";
                                            }
                                        } else {
                                            /*
                                              $box['active'] = "No";
                                              $box['setActive'] = "activeMain";
                                             */
                                            $box['domain'] = "";
                                        }
                                        //** --

                                        $tpl_tmp['tabId'] = $myLangArray[$mySide['lang_id']]['lang_lang'];
                                        if (isset($tpl_tmp['sides'])) {
                                            $tpl_tmp['sides'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $box, $tpl_LangTabContent);
                                        } else {
                                            $tpl_tmp['sides'] = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $box, $tpl_LangTabContent);
                                        }
                                        // <- mainlinks
                                        // sublinks ->
                                        if (isset($mySide['subsides'])) {
                                            $i = 0;
                                            foreach ($mySide['subsides'] as $mySides => $mySidesValue) {
                                                $box['sideName'] = $mySidesValue['link_text'];
                                                $box['sideTitle'] = substr($mySidesValue['page_titel'], 0, 32) . "...";
                                                $box['posValue'] = $mySidesValue['position'];
                                                $box['pageid'] = $mySidesValue['page_id'];
                                                $box['posId'] = $mySidesValue['page_id'];
                                                $box['trId'] = $i++;

                                                if ($mySidesValue['active'] == 1) {
                                                    $box['active'] = "OK";
                                                } else {
                                                    $box['active'] = "No";
                                                }
                                                //** Adding domain checkboxes for subsides
                                                if (SP_CORE_LANG === $myLangArray[$mySide['lang_id']]['lang_lang']) {
                                                    if ($isDomains) {
                                                        $subDomainTMP = unserialize($mySidesValue['page_domain']);
                                                        $box['domain'] = "<nobr>";
                                                        foreach ($myDomain as $domainIndex) {
                                                            //$box['domain'] .= "<a href=\"#\" onclick=\"addPageToDomain('" . $box['pageid'] . "','" . $domainIndex['ID'] . "','{PORTAL_TEMPLATE_PATH}',4)\">
                                                            $box['domain'] .= "<img onclick=\"addPageToDomain('"
                                                                    . $box['pageid'] . "','" . $domainIndex['ID'] . "','{PORTAL_TEMPLATE_PATH}',4)\" id=\"d_sub_img_"
                                                                    . (@in_array($domainIndex['ID'], $subDomainTMP) ? "OK" : "NO") . "_" . $box['pageid'] . "_"
                                                                    . $domainIndex['ID'] . "\" src=\"{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/"
                                                                    . (@in_array($domainIndex['ID'], $subDomainTMP) ? "OK" : "No")
                                                                    . ".png\" alt=\"Status\" border=\"0\" title=\"" . $domainIndex['domain'] . "\">";
                                                        }
                                                        $box['domain'] .= "</nobr>";
                                                    } else {
                                                        $box['domain'] = "";
                                                    }
                                                } else {
                                                    $box['domain'] = "";
                                                    /*
                                                      $box['active'] = "No";
                                                      $box['setActive'] = "activeSub";
                                                     */
                                                }
                                                //** --
                                                $tpl_tmp['sides'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $box, $tpl_LangTabContentSub);
                                            }
                                        }
                                    }//<- sides
                                    $meineTestBox[$box['langTag']][$box['box']] = $tpl_tmp;
                                }
                                $tpl_tmp = array('tabId', 'langTabs');
                            }
                        }
                    }
                }
            }
            if (isset($meineTestBox) && is_array($meineTestBox)) {
                // sort sides in boxes pos
                foreach ($meineTestBox as $langIndex => $foo) {
                    $mainM = true;
                    $tmp = null;
                    if (is_array($dynBox['boxObj'])) {
                        foreach ($dynBox['boxObj'] as $di) {
                            if (isset($meineTestBox[$langIndex][0]['sides']) && $mainM == true) {
                                if (!isset($tmp['sides'])) {
                                    $tmp['sides'] = null;
                                }
                                $tmp['sides'] .= $meineTestBox[$langIndex][0]['sides'];
                                $tmp['tabId'] = $meineTestBox[$langIndex][0]['tabId'];
                                $mainM = false;
                            }
                            if (isset($foo[$di['ID']]) && isset($meineTestBox[$langIndex][$di['ID']]['sides'])) {
                                if (!isset($tmp['sides'])) {
                                    $tmp['sides'] = "";
                                }
                                $tmp['sides'] .= $meineTestBox[$langIndex][$di['ID']]['sides'];
                                $tmp['tabId'] = $meineTestBox[$langIndex][$di['ID']]['tabId'];
                            }
                        }
                    } else {
                        foreach ($meineTestBox[$langIndex] as $index => $value) {
                            $tmp['sides'] = $meineTestBox[$langIndex][$index]['sides'];
                            $tmp['tabId'] = $meineTestBox[$langIndex][$index]['tabId'];
                        }
                    }
                    $myTemplate['langTabs'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $tmp, $tpl_LangTab);
                }
            }
            $boxContent = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $myTemplate, $mySideMain);
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_adminCMSSideEdit}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_CMSsideAssignment() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            $sql = "SELECT
                            page_lang,
                            titel_text,
                            page_relation
                           FROM sp_modul_portal_cms_page WHERE ID =" . $this->db->checkValue($this->reqVar['id']);

            $result = $this->db->query($sql);
            $mainSideObj = $this->db->fetch_object($result);
            $langArray = CLang::getInstance()->getInstalledLangs($this->db);

            if ($mainSideObj->page_lang == 0) {
                $sql = "SELECT a.lang_id FROM
                            sp_modul_portal_cms_menue_main_side as a,
                            sp_modul_portal_cms_menue_sub_side as b
                            WHERE a.page_id = " . $this->db->checkValue($this->reqVar['id']) . "
                                OR ( b.page_id = " . $this->db->checkValue($this->reqVar['id']) . " AND b.mainlink_id = a.ID)
                                    LIMIT 0 , 1";
                $res = $this->db->fetch_assoc($this->db->query($sql));
                $mainSideObj->page_lang = $res['lang_id'];
            }

            $mainSideObj->page_lang = ($mainSideObj->page_lang == 0 ? 1 : $mainSideObj->page_lang);

            $tplXML = spcore\CTemplate::getInstance()->loadModulTemplateXML("portal", "admin_box_editSide"); //page titel
            $mainForm = $tplXML->side_assignment;
            $box['selectedSite'] = $this->reqVar['id'];
            $box['flag'] = $langArray[$mainSideObj->page_lang]['lang_lang'];
            $box['sidename'] = $mainSideObj->titel_text;
            $box['editside'] = $this->reqVar['id'];
            $box['sideAssi'] = null;

            foreach ($langArray as $index => $value) {
                if ($mainSideObj->page_lang != $index) {

                    //@todo this murmel murmel change to 1 query

                    $sql = "SELECT * FROM  sp_modul_portal_cms_menue_main_side WHERE lang_id = " . $this->db->checkValue(($index == 1 ? " 0 OR lang_id = 1" : $index));
                    $result = $this->db->query($sql);
                    if ($this->db->num_rows($result) != 0) {
                        while ($res = $this->db->fetch_assoc($result)) {
                            $pages['mpage'][$res['page_id']] = $res['page_id'];
                            $pages['apage'][$res['page_id']] = $res['page_id'];
                            $pages['lang'][$res['page_id']]['lang_id'] = $res['lang_id'];
                        }
                        $sql = "SELECT * FROM sp_modul_portal_cms_menue_sub_side WHERE mainlink_id = " . $this->db->checkValue(implode(" OR mainlink_id = ", $pages['mpage'])) . " ORDER BY mainlink_id ASC";
                        $sresult = $this->db->query($sql);
                        if ($this->db->num_rows($sresult) != 0) {
                            while ($sres = $this->db->fetch_assoc($sresult)) {
                                $pages['apage'][$sres['page_id']] = $sres['page_id'];
                                $pages['lang'][$sres['page_id']]['lang_id'] = $res['lang_id'];
                            }
                        }
                        $sql = "SELECT * FROM sp_modul_portal_cms_page WHERE ID = " . $this->db->checkValue(implode(" OR ID = ", $pages['apage'])) . " ORDER BY ID ASC";
                        $result = $this->db->query($sql);

                        $subbox['sideOption'] = spCore\CTemplate::getInstance()->makeOptionTag(0, "", "");
                        while ($res = $this->db->fetch_assoc($result)) {
                            $select = is_array(unserialize($mainSideObj->page_relation))
                                    && in_array($res['ID'], unserialize($mainSideObj->page_relation)) ? 1 : 0;
                            if (isset($langArray[$pages['lang'][$res['ID']]['lang_id']]['lang_lang'])) {
                                $subbox['flag'] = $langArray[$pages['lang'][$res['ID']]['lang_id']]['lang_lang'];
                            }
                            $subbox['sideOption'] .= spCore\CTemplate::getInstance()->makeOptionTag($res['ID'], $res['ID'] . " : " . $res['titel_text'], $select);
                        }
                        unset($pages);
                        $box['sideAssi'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $subbox, $tplXML->side_assignmentData);
                    }
                }
            }
            $mainForm = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $box, $mainForm);
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_adminCMSSideEdit} - Verknuepfung", $mainForm);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_CMSsideAssignmentToDB() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            foreach ($this->reqVar['sites'] as $index => $value) {
                if ($this->reqVar['sites'][$index] == 0) {
                    unset($this->reqVar['sites'][$index]);
                }
            }
            $sql = 'UPDATE sp_modul_portal_cms_page
                    SET page_relation = \'' . $this->db->checkValue(serialize($this->reqVar['sites'])) . '\'
                        WHERE ID = ' . $this->db->checkValue(implode(" OR ID =", $this->reqVar['sites']));
            $this->db->query($sql);
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id=' . $this->reqVar['editSite']);
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    private function clearMe($string) {
        return htmlentities(html_entity_decode($string));
    }

    /**
     * \brief Update positition
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSUpdatePos() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            $this->portal_genSitemapXML();
            $sql = 'SELECT * FROM `sp_modul_portal_cms_page`';
            $result = $this->db->query($sql);


            while ($res = $this->db->fetch_array($result)) {
                if (isset($this->reqVar['artikel_' . $res['ID']])) {
                    $upSql[] = 'UPDATE sp_modul_portal_cms_menue_main_side
                                    SET
                                            position = "' . $this->db->checkValue((int) $this->reqVar['portalPos_' . $res['ID']]) . '",
                                            link_text = "' . $this->db->checkValue(self::clearMe($this->reqVar['portalLinkName_' . $res['ID']])) . '",
                                            box = ' . $this->db->checkValue($this->reqVar['artikel_' . $res['ID']] == 0 ? 'NULL' : $this->reqVar['artikel_' . $res['ID']]) . '
                                    WHERE
                                            page_id = ' . $res['ID'];
                }
                if (isset($this->reqVar['portalSubLinkName_' . $res['ID']])) {
                    $upSql[] = 'UPDATE sp_modul_portal_cms_menue_sub_side
                                    SET
                                            position = "' . $this->db->checkValue((int) $this->reqVar['portalSubLinkPos_' . $res['ID']]) . '",
                                            link_text = "' . $this->db->checkValue(self::clearMe($this->reqVar['portalSubLinkName_' . $res['ID']])) . '"
                                    WHERE
                                            page_id = ' . $res['ID'];
                }
            }
            if (!empty($upSql)) {
                foreach ($upSql as $sql) {
                    $this->db->query($sql);
                }
            }
            $this->portal_CMSWatchAllSides();
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief set side status
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSActivate() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            $this->portal_genSitemapXML();
            if ($this->reqVar['doit'] == "activeMain") {
                $sql = "UPDATE sp_modul_portal_cms_menue_main_side SET active = IF(active=0,'1','0') WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
            } elseif ($this->reqVar['doit'] == "activeSub") {
                $sql = "UPDATE sp_modul_portal_cms_menue_sub_side SET active = IF(active=0,'1','0') WHERE page_id = " . $this->db->checkValue($this->reqVar['id']);
            }

            $this->db->query($sql);
            $this->portal_CMSWatchAllSides();
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief Delete page and subsides
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSDeletePage() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            $this->portal_genSitemapXML();
            $delSql = Array();

            if ($this->reqVar['doit'] == 'deleteSub') {
                $delSql[] = "DELETE FROM `sp_modul_portal_cms_menue_sub_side` WHERE `page_id` = " . $this->db->checkValue($this->reqVar['id']);
                $delSql[] = "DELETE FROM sp_modul_portal_cms_page WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
            } elseif ($this->reqVar['doit'] == 'deleteMain') {

                $sql = "SELECT
              a.page_id as ID,
              b.page_id as bID
              FROM
              sp_modul_portal_cms_menue_sub_side as a,
              sp_modul_portal_cms_menue_main_side as b
              WHERE
              a.mainlink_id = b.ID
              AND
              b.page_id = " . $this->db->checkValue($this->reqVar['id']);
                $result = $this->db->query($sql);
                while ($res = $this->db->fetch_assoc($result)) {
                    $delArray[$res['ID']] = null;
                    $delArray[$res['bID']] = null;
                }
                if (isset($delArray) && is_array($delArray)) {
                    foreach ($delArray as $index => $val) {
                        $delSql[] = "DELETE FROM sp_modul_portal_cms_page WHERE ID = " . $index;
                    }
                }
                $sql = "SELECT ID, page_id FROM sp_modul_portal_cms_menue_main_side WHERE page_id = " . $this->db->checkValue($this->reqVar['id']);
                $result = $this->db->query($sql);

                $rObj = $this->db->fetch_object($result);
                if (is_object($rObj)) {
                    $delSql[] = "DELETE FROM `sp_modul_portal_cms_menue_sub_side` WHERE mainlink_id = " . $rObj->page_id;
                }
                $delSql[] = "DELETE FROM `sp_modul_portal_cms_menue_main_side` WHERE page_id = " . $this->db->checkValue($this->reqVar['id']) . " LIMIT 1";
            }

            foreach ($delSql as $sql) {
                $this->db->query($sql);
            }

            $this->portal_CMSWatchAllSides();
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_toplink() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {
            $sql = "UPDATE sp_modul_portal_cms_menue_main_side
                        SET
                            toplink = IF(toplink=0,'1','0')
                    WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
            $this->db->query($sql);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
        }
        spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " portal_domainAddPageToDomain ...");
        exit();
    }

    /**
     * \brief edit your sides
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     * @todo clean up code !!
     */
    public function portal_CMSEditSide() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            if (!isset($this->reqVar['doit'])) {
                $this->reqVar['doit'] = "";
            }

            $loadScripts = function() {
                        spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/tiny_mce/tiny_mce.js");
                        spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/myPortal.js");
                        spcore\CTemplate::getInstance()->addCssScript("portal", "scripts/css/myPortal.css");
                    };

            $searchSQL['mainsides'] = function($id) {
                        $sql = "SELECT
                                                    a.lang_id,
                                                    b.*
                                            FROM
                                                    sp_modul_portal_cms_menue_main_side as a,
                                                    sp_modul_portal_cms_page as b
                                            WHERE
                                                    a.page_id = b.ID
                                            AND b.ID = " . $id;
                        return $sql;
                    };
            $searchSQL['subsides'] = function($id) {
                        $sql = "SELECT
                                                    a.mainlink_id,
                                                    a.page_id as sub_page_id,
                                                    b.*
                                            FROM
                                                    sp_modul_portal_cms_menue_sub_side as a,
                                                    sp_modul_portal_cms_menue_main_side as b
                                            WHERE
                                                    a.page_id = " . $id . "
                                            AND
                                                    a.mainlink_id = b.ID";
                        return $sql;
                    };

            $myLangs = CLang::getInstance()->getInstalledLangs($this->db);
            $tpl_tmp = null;
            $boxArray['sites'] = null;
            $contentArr['langTab'] = null;
            $contentArr['javascript'] = null;
            $contentArr['sites'] = null;

            $tpl_XML = spcore\CTemplate::getInstance()->loadModulTemplateXML("portal", "cms_box_content_editSite");
            $tpl_langTab = $tpl_XML->langTab;
            $tpl_mainform = $tpl_XML->mainform;
            $tpl_langTabCont = $tpl_XML->langTabContent;

            if (isset($this->reqVar['doit'])) {
                switch ($this->reqVar['doit']) {
                    case 'editPageWatch':
                        foreach ($myLangs as $value) {
                            $contentArr['langTab'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('tabLangID' => $value['lang_lang'],
                                'tabLanguage' => $value['lang_name']), $tpl_langTab);
                        }

                        $sql = "SELECT page_relation FROM sp_modul_portal_cms_page WHERE ID = " . $this->db->checkValue($this->reqVar['id']);
                        $p_result = $this->db->query($sql);
                        $p_res = $this->db->fetch_assoc($p_result);
                        $pages = unserialize($p_res['page_relation']);
                        $tmp_boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_box_content_contentEdit_editSide");
                        $boxContent = null;

                        if (is_array($pages)) {
                            $loadScripts();
                            foreach ($pages as $lang_page) {
                                // @todo must be clean up !!
                                $result = $this->db->query($searchSQL['mainsides']($this->db->checkValue($lang_page)));

                                if (!$result || $this->db->num_rows($result) == 0) {
                                    $result = $this->db->query($searchSQL['subsides']($this->db->checkValue($lang_page)));
                                    $myUndersite = $this->db->fetch_object($result);
                                    $sql = "SELECT * FROM sp_modul_portal_cms_page WHERE ID = " . $myUndersite->sub_page_id;
                                    $result = $this->db->query($sql);
                                }

                                $res = $this->db->fetch_assoc($result);

                                $contentArr['javascript'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('pageid' => $lang_page), $tpl_XML->javascript);
                                if (!isset($res['lang_id'])) {
                                    $res['lang_id'] = $myUndersite->lang_id;
                                }
                                $boxArray['pageTitel'] = self::clearMe($res['titel_text']);
                                $boxArray['pageContent'] = $res['page_content'];
                                //$this->db->out_dbg("debug out");

                                $boxArray['pageid'] = $lang_page;
                                $boxArray['tabLangID'] = $myLangs[$res['lang_id']]['lang_lang'];

                                $contentArr['sites'] .= "&sites[" . $myLangs[$res['lang_id']]['lang_lang'] . "]=" . $lang_page;

                                // main pages for underside box
                                $sql = "SELECT * FROM   sp_modul_portal_cms_menue_main_side WHERE lang_id = " . $res['lang_id'];
                                $u_result = $this->db->query($sql);

                                $boxArray['underside'] = spcore\CTemplate::getInstance()->makeOptionTag(0, "", 0);
                                if ($u_result != false && $this->db->num_rows($u_result) > 0) {
                                    while ($u_res = $this->db->fetch_assoc($u_result)) {
                                        if ($u_res['page_id'] != $res['ID']) {
                                            if (isset($myUndersite) && $myUndersite->ID == $u_res['ID']) {
                                                $boxArray['underside'] .= spcore\CTemplate::getInstance()->makeOptionTag($u_res['ID'], $u_res['ID'] . " : " . self::clearMe($u_res['link_text']), 1);
                                            } else {
                                                $boxArray['underside'] .= spcore\CTemplate::getInstance()->makeOptionTag($u_res['ID'], $u_res['ID'] . " : " . self::clearMe($u_res['link_text']), 0);
                                            }
                                        }
                                    }
                                }
                                if (!isset($contentArr['langTabContent'])) {
                                    $contentArr['langTabContent'] = null;
                                }
                                $contentArr['langTabContent'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $tpl_langTabCont);
                                $boxContent = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $contentArr, $tpl_mainform);
                                //$this->db->out_dbg($boxContent);
                                unset($boxArray);
                            }
                        } else {
                            $loadScripts();
                            $contentArr['javascript'] = spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('pageid' => $this->reqVar['id']), $tpl_XML->javascript);
                            // must be clean up !!
                            $result = $this->db->query($searchSQL['mainsides']($this->db->checkValue($this->reqVar['id'])));
                            if (!$result || $this->db->num_rows($result) == 0) {
                                $result = $this->db->query($searchSQL['subsides']($this->db->checkValue($this->reqVar['id'])));
                                $myUndersite = $this->db->fetch_object($result);
                                $sql = "SELECT * FROM sp_modul_portal_cms_page WHERE ID = " . $myUndersite->sub_page_id;
                                $result = $this->db->query($sql);
                            }
                            $res = $this->db->fetch_assoc($result);

                            $boxArray['pageTitel'] = self::clearMe($res['titel_text']);
                            $boxArray['pageContent'] = $res['page_content'];
                            $boxArray['pageid'] = $this->reqVar['id'];
                            if (!isset($res['lang_id'])) {
                                $res['lang_id'] = $myUndersite->lang_id;
                            }
                            $boxArray['tabLangID'] = $myLangs[$res['lang_id']]['lang_lang'];
                            $contentArr['langTab'] = spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('tabLangID' => $myLangs[$res['lang_id']]['lang_lang'],
                                'tabLanguage' => $myLangs[$res['lang_id']]['lang_name']), $tpl_langTab);
                            $contentArr['sites'] .= "&sites[" . $myLangs[$res['lang_id']]['lang_lang'] . "]=" . $this->reqVar['id'];

                            // main pages for underside box
                            $sql = "SELECT * FROM    sp_modul_portal_cms_menue_main_side WHERE   lang_id = " . $res['lang_id'];
                            $u_result = $this->db->query($sql);

                            $boxArray['underside'] = spcore\CTemplate::getInstance()->makeOptionTag(0, "", 0);
                            if ($u_result != false && $this->db->num_rows($u_result) > 0) {
                                while ($u_res = $this->db->fetch_assoc($u_result)) {
                                    if ($u_res['page_id'] != $res['ID']) {
                                        if (isset($myUndersite) && $myUndersite->ID == $u_res['ID']) {
                                            $boxArray['underside'] .= spcore\CTemplate::getInstance()->makeOptionTag($u_res['ID'], $u_res['ID'] . " : " . self::clearMe($u_res['link_text']), 1);
                                        } else {
                                            $boxArray['underside'] .= spcore\CTemplate::getInstance()->makeOptionTag($u_res['ID'], $u_res['ID'] . " : " . self::clearMe($u_res['link_text']), 0);
                                        }
                                    }
                                }
                            }
                            $contentArr['langTabContent'] = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $tpl_langTabCont);
                            $boxContent = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $contentArr, $tpl_mainform);
                        }
                        break;
                    case 'editPageUpdate':
                        foreach ($this->reqVar['sites'] as $reqLang => $reqPageID) {
                            // if subside set
                            if (!is_numeric($reqLang)) {
                                if (isset($this->reqVar['side_subside_' . $reqLang]) && $this->reqVar['side_subside_' . $reqLang] != 0) {
                                    $sql = "SELECT * FROM sp_modul_portal_cms_menue_main_side WHERE page_id = " . $this->db->checkValue($reqPageID);
                                    $result = $this->db->query($sql);
                                    // if side is mainside
                                    if ($this->db->num_rows($result)) {
                                        $myRes = $this->db->fetch_assoc($result);
                                        $sql = "INSERT INTO `sp_modul_portal_cms_menue_sub_side` (
                                                                                    page_id,
                                                                                    link_text,
                                                                                    mainlink_id,
                                                                                    position,
                                                                                    active
                                                                            ) VALUES (
                                                                                    " . $myRes['page_id'] . ",
                                                                                    '" . self::clearMe($myRes['link_text']) . "',
                                                                                    " . $this->db->checkValue($this->reqVar['side_subside_' . $reqLang]) . ",
                                                                                    0,
                                                                                    0
                                                                            )";
                                        $this->db->query($sql);
                                        $sql = "DELETE FROM sp_modul_portal_cms_menue_main_side WHERE page_id = " . $this->db->checkValue($reqPageID);
                                        $this->db->query($sql);
                                    } else {
                                        $sql = "UPDATE sp_modul_portal_cms_menue_sub_side
                                                                            SET
                                                                                mainlink_id = " . $this->db->checkValue($this->reqVar['side_subside_' . $reqLang]) . "
                                                                            WHERE page_id = " . $this->db->checkValue($reqPageID);
                                        $this->db->query($sql);
                                    }
                                } else {
                                    //make subside to mainside
                                    //check if this site is
                                    $sql = 'SELECT * FROM sp_modul_portal_cms_menue_sub_side WHERE page_id = ' . $this->db->checkValue($reqPageID);
                                    $result = $this->db->query($sql);
                                    if ($this->db->num_rows($result) > 0) {
                                        // it is subsite
                                        $res = $this->db->fetch_assoc($result);
                                        $sql = "SELECT lang_id FROM sp_modul_portal_cms_menue_main_side WHERE ID = " . $res['mainlink_id'];
                                        $main_result = $this->db->query($sql);
                                        $main_res = $this->db->fetch_assoc($main_result);
                                        $sql = "INSERT INTO `sp_modul_portal_cms_menue_main_side` (
                                                                                                    page_id,
                                                                                                    link_text,
                                                                                                    lang_id,
                                                                                                    position,
                                                                                                    active
                                                                                            ) VALUES (
                                                                                                    '" . $this->db->checkValue($reqPageID) . "',
                                                                                                    '" . $this->db->checkValue(self::clearMe($res['link_text'])) . "',
                                                                                                    " . $this->db->checkValue($main_res['lang_id']) . ",
                                                                                                    0,
                                                                                                    0
                                                                                            )";
                                        $this->db->query($sql);
                                        $sql = "DELETE FROM sp_modul_portal_cms_menue_sub_side WHERE page_id = " . $this->db->checkValue($reqPageID);
                                        $this->db->query($sql);
                                    }
                                }
                                // update page content
                                $sql = 'UPDATE sp_modul_portal_cms_page SET
                                                                    titel_text = "' . $this->db->checkValue(self::clearMe($this->reqVar['side_titel_' . $reqLang])) . '",
                                                                    page_content = "' . $this->db->checkValue($this->portal_transformToDynamic($this->reqVar['side_content_' . $reqLang])) . '"
                                                               WHERE ID = ' . $this->db->checkValue($reqPageID);
                            }
                            $this->db->query($sql);
                        }

                        header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_CMSWatchAllSides');
                        break;
                }
            }
            //$this->db->out_dbg(self::clearForURLRewrite($boxContent));
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_adminCMSSideEdit}", self::clearForURLRewrite($boxContent));
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief adding new Side
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSNewSide() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_new'] == 1) {

            if (!isset($this->reqVar['side_step'])) {
                $this->reqVar['side_step'] = "";
            }

            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/tiny_mce/tiny_mce.js");
            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/myPortal.js");
            spcore\CTemplate::getInstance()->addCssScript("portal", "scripts/css/myPortal.css");
            $myXML = spcore\CTemplate::getInstance()->loadModulTemplateXML("portal", "cms_box_content_addNewSide");
            // boost performance
            $tplMainForm = $myXML->mainform;
            $tplLangTab = $myXML->langTab;
            $tplTabContent = $myXML->langTabContent;
            // --
            switch ($this->reqVar['side_step']) {
                case '1':
                    $pages = array();
                    $sql = "SELECT * FROM sp_modul_portal_language";
                    $result = $this->db->query($sql);
                    if ($result) {
                        while ($res = $this->db->fetch_assoc($result)) {
                            if (isset($this->reqVar['side_subside_' . $res['lang_lang']])
                                    && $this->reqVar['side_subside_' . $res['lang_lang']] != $this->reqVar['side_subside_' . $res['lang_lang']]
                                    && !empty($this->reqVar['side_subside_' . $res['lang_lang']])) {
                                if (isset($this->reqVar['side_titel_' . $res['lang_lang']]) && !empty($this->reqVar['side_titel_' . $res['lang_lang']])) {
                                    $sql = "INSERT INTO `sp_modul_portal_cms_page` (
												titel_text, 
												page_content,
                                                                                                page_lang,
                                                                                                page_relation,
                                                                                                page_domain
											) VALUES (
												'" . $this->db->checkValue(htmlentities($this->reqVar['side_titel_' . $res['lang_lang']])) . "',
												'" . $this->db->checkValue($this->portal_transformToDynamic($this->reqVar['side_content_' . $res['lang_lang']])) . "',
                                                                                                " . $res['id'] . ",
                                                                                                '',
                                                                                                0
											)";
                                    $this->db->query($sql);
                                    $pages[] = $pageInsertId = $this->db->insert_id();
                                    $sql = "INSERT INTO `sp_modul_portal_cms_menue_sub_side` (
												page_id, 
												link_text, 
												mainlink_id,
                                                                                                position,
                                                                                                active
											) VALUES (
												'" . $pageInsertId . "',
												'" . $this->db->checkValue(htmlentities($this->reqVar['side_titel_' . $res['lang_lang']])) . "',
												'" . $this->db->checkValue(htmlentities($this->reqVar['side_subside_' . $res['lang_lang']])) . "',
                                                                                                0,
                                                                                                0
											)";
                                    $this->db->query($sql);
                                } else {
                                    //$this->db->out_dbg($this->reqVar);                              
                                    spcore\CLog::getInstance()->log(SP_LOG_ERROR, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_ERROR), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " " . $this->reqVar);
                                }
                            } else {
                                if (isset($this->reqVar['side_titel_' . $res['lang_lang']]) && !empty($this->reqVar['side_titel_' . $res['lang_lang']])) {
                                    $sql = "INSERT INTO `sp_modul_portal_cms_page` (
																								titel_text, 
																								page_content,
                                                                                                page_lang,
                                                                                                page_relation,
                                                                                                page_domain
											) VALUES (
												'" . $this->db->checkValue(htmlentities($this->reqVar['side_titel_' . $res['lang_lang']])) . "',
												'" . $this->db->checkValue($this->portal_transformToDynamic($this->reqVar['side_content_' . $res['lang_lang']])) . "',
                                                                                                " . $res['id'] . ",
                                                                                                '',
																								0
											)";
                                    $this->db->query($sql);
                                    $pages[] = $pageInsertId = $this->db->insert_id();
                                    $sql = "INSERT INTO `sp_modul_portal_cms_menue_main_side` (
																								page_id, 
																								link_text,
																								lang_id,
                                                                                                position,
																								active
											) VALUES (
												'" . $pageInsertId . "',
												'" . $this->db->checkValue(htmlentities($this->reqVar['side_titel_' . $res['lang_lang']])) . "',
												" . $res['id'] . ",
                                                                                                0,
																								0
											)";
                                    $this->db->query($sql);
                                }
                            }
                        }
                    }
                    foreach ($pages as $value) {
                        $sql = "UPDATE `sp_modul_portal_cms_page` SET `page_relation` = '" . serialize($pages) . "' WHERE `sp_modul_portal_cms_page`.`ID` =" . $value;
                        $this->db->query($sql);
                    }
                    $this->portal_CMSWatchAllSides();
                    break;
                default:
                    $insLang = CLang::getInstance()->getInstalledLangs($this->db);
                    $myArray['tabLangID'] = null;
                    $myArray['langTab'] = null;
                    $myArray['langTabContent'] = null;
                    $contentArr['langTab'] = null;
                    $contentArr['langTabContent'] = null;

                    $sql = "SELECT * FROM sp_modul_portal_cms_menue_main_side";
                    $result = $this->db->query($sql);

                    while ($rObj = $this->db->fetch_object($result)) {
                        if (!isset($langUnderside[$rObj->lang_id])) {
                            $langUnderside[$rObj->lang_id] = spcore\CTemplate::getInstance()->makeOptionTag(0, "", 0);
                        }
                        $langUnderside[$rObj->lang_id] .= spcore\CTemplate::getInstance()->makeOptionTag($rObj->ID, $rObj->ID . " : " . self::clearMe($rObj->link_text), 0);
                    }
                    try {
                        if (!is_array($insLang)) {
                            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_settings');
                            exit;
                        }
                        foreach ($insLang as $value) {
                            $myArray['underside'] = null;
                            if (isset($langUnderside[$value['id']])) {
                                $myArray['underside'] .= $langUnderside[$value['id']];
                            }

                            $myArray['tabLangID'] = $value['lang_lang'];
                            $myArray['langTab'] = $value['lang_lang'];
                            $myArray['langTabContent'] = $value['lang_name'];
                            $myArray['tabLanguage'] = $value['lang_name'];
                            $contentArr['langTab'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $myArray, $tplLangTab);
                            $contentArr['langTabContent'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $myArray, $tplTabContent);
                        }

                        $content = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $contentArr, $tplMainForm);
                        spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_adminCMSNewSide}", $content);
                        break;
                    } catch (Exception $e) {
                        $this->addContentBox("Error", "No language installed !<br>Error Message : " . $e->getMessage());
                    }
            }
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief write some new news
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSWriteNews() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_new'] == 1) {

            if (!isset($this->reqVar['side_step'])) {
                $this->reqVar['side_step'] = "";
            }
            switch ($this->reqVar['side_step']) {
                case '1':
                    $sql = "INSERT INTO `sp_modul_portal_cms_news`
                        (news_date, titel_text, page_content, active)
                        VALUES
                        ('" . time() . "',
                            '" . $this->db->checkValue(htmlentities($this->reqVar['side_titel'])) . "',
                            '" . $this->db->checkValue(htmlentities($this->reqVar['side_content'])) . "',
                                '0')";
                    $this->db->query($sql);
                    header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_CMSListNews');
                    break;
            }
            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/tiny_mce/tiny_mce.js");
            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/myPortal.js");
            spcore\CTemplate::getInstance()->addCssScript("portal", "scripts/css/myPortal.css");

            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "cms_box_content_addNewNews");
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_newsSystemAddNew}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief watch all News
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSListNews() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            $sql = "SELECT * FROM sp_modul_portal_cms_news ORDER BY ID DESC";
            $result = $this->db->query($sql);

            $box['titel_text'] = null;
            $box['page_content'] = null;
            $box['news_date'] = null;
            $tplContent['dir'] = null;

            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_news_mainbox_editContent");

            if ($result != null) {
                while ($res = $this->db->fetch_array($result)) {
                    $boxArray['newsId'] = $res['ID'];
                    $boxArray['activ'] = ($res['active'] == 1 ? '{LANG_portal_imgNewActiv}" alt="activ" ' : '{LANG_portal_imgNewInactiv}" alt="inactiv"');
                    $boxArray['date'] = date("D d.m.Y", $res['news_date']);
                    $boxArray['newsTitel'] = $res['titel_text'];
                    $boxArray['newsActiv'] = $res['active'];
                    $tplContent['dir'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $boxContent);
                }
            }


            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_news_mainbox_edit", $tplContent);
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_adminNewsEdit}", $boxContent);
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief edit your News
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSEditNews() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/tiny_mce/tiny_mce.js");
            spcore\CTemplate::getInstance()->addJsScript("portal", "scripts/js/myPortal.js");
            spcore\CTemplate::getInstance()->addCssScript("portal", "scripts/css/myPortal.css");

            $sql = "SELECT * FROM sp_modul_portal_cms_news WHERE ID = " . $this->db->checkValue($this->reqVar['newsid']) . " LIMIT 1";
            $result = $this->db->query($sql);

            if ($result != null) {
                while ($res = $this->db->fetch_array($result)) {
                    $boxArray['newsId'] = $res['ID'];
                    $boxArray['valueSideTitel'] = $res['titel_text'];
                    $boxArray['valueSideContent'] = $res['page_content'];
                    $boxArray['newsActiv'] = $res['active'];
                }
            }
            $boxContent = spcore\CTemplate::getInstance()->loadModulTemplate("portal", "admin_news_mainbox_editNews_content");
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_newsEditNews}", spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $boxContent));
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief Delete News
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSDelNews() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            $sql = "DELETE FROM sp_modul_portal_cms_news WHERE ID = " . $this->db->checkValue($this->reqVar['newsid']) . " LIMIT 1";
            $result = $this->db->query($sql);
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_CMSListNews');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief Update News
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSUpdateNews() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            $sql = "UPDATE `sp_modul_portal_cms_news`
                    SET
                        titel_text= '" . $this->db->checkValue(self::clearMe($this->reqVar['side_titel'])) . "',
                        page_content='" . $this->db->checkValue(self::clearMe($this->portal_transformToDynamic($this->reqVar['side_content']))) . "'
                            WHERE ID = " . $this->db->checkValue($this->reqVar['newsid']);
            $this->db->query($sql);
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_CMSListNews');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * \brief Activate News
     *
     * @param $template
     * @param $db
     * @param $reqVar
     * @return unknown_type
     */
    public function portal_CMSActivNews() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['side_edit'] == 1) {

            $sql = "UPDATE `sp_modul_portal_cms_news` SET active= IF(active=0,'1','0')  WHERE ID = " . $this->db->checkValue($this->reqVar['newsid']);
            $this->db->query($sql);
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_CMSListNews');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * domain sektion
     */

    /**
     * Add a new Domain to Portal configuration
     */
    public function portal_domainSettings() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['add_domain'] == 1) {

            spcore\CTemplate::getInstance()->addContentBox("Add Domain", spCore\CTemplate::loadModulTemplateXML("portal", "admin_domain")->domainAdd);
            $this->portal_domainList();
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_domainAddData() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['add_domain'] == 1) {

            if (!empty($this->reqVar['domain'])) {
                $sql = "INSERT INTO sp_modul_portal_domain (
                        ID,
                        domain,
                        description
                    )VALUES(
                        NULL,
                        '" . $this->db->checkValue($this->reqVar['domain']) . "',
                        '" . $this->db->checkValue($this->reqVar['domain_description']) . "'
                    )";
                $this->db->query($sql);
            }
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_domainSettings');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * Delete domain configuration for selected domains
     */
    public function portal_domainDel() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['delete_domain'] == 1) {

            $sql = "DELETE FROM `sp_modul_portal_domain` WHERE `ID` = " . $this->db->checkValue($this->reqVar['delDomainID']) . " LIMIT 1";
            $this->db->query($sql);
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_domainSettings');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    /**
     * Edit domain configurations
     */
    public function portal_domainEdit() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['edit_domain'] == 1) {

            spcore\CTemplate::getInstance()->addContentBox("Edit Domain", spCore\CTemplate::loadModulTemplateXML("portal", "admin_domain")->domainEdit);
            $this->portal_domainList();
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_domainList() {
        if ($this->permission['portal']['admin'] == 'true'
                && isset($this->permission['portal']['detail'])
                && $this->permission['portal']['detail']['edit_domain'] == 1) {

            $sql = "SELECT * FROM sp_modul_portal_domain ORDER BY ID";
            $result = $this->db->query($sql);

            $sql = "SELECT * FROM sp_modul_portal_language";
            $lang_result = $this->db->query($sql);
#
            $myTpl = spCore\CTemplate::loadModulTemplateXML("portal", "admin_domain");
            $boxContent['domainListTableData'] = null;

            while ($res = $this->db->fetch_assoc($lang_result)) {
                $langArray[] = $res;
            }

            while ($res = $this->db->fetch_array($result)) {
                $boxArray['ID'] = $res['ID'];
                $boxArray['onOffImage'] = $res['ID'];
                $boxArray['domainName'] = $res['domain'];
                $boxArray['domainDescription'] = $res['description'];
                $boxArray['domainlang'] = "<div align=\"right\">";

                $activeLangsPerDomain = unserialize($res['domainLangs']);
                foreach ($langArray as $value) {
                    // $res['ID']       // domainID
                    // $value['id']     // langid

                    if (isset($activeLangsPerDomain[$value['id']])) {
                        $checkValue = 1;
                        $checked = "checked";
                    } else {
                        $checkValue = 0;
                        $checked = "";
                    }
                    $boxArray['domainlang'] .="<input type='checkbox' name='domainLang_" . $res['ID'] . "_" . $value['id'] . "' value='" . $checkValue . "' " . $checked . ">"
                            . "<img src=\"{PORTAL_HTTP_HOST}/module/portal/template/images/flagicons/" . $value['lang_lang'] . ".png\" border=\"0\"> ";
                }
                $boxArray['domainlang'] .= "</div>";
                $boxContent['domainListTableData'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $myTpl->domainListTableData);
            }

            spcore\CTemplate::getInstance()->addContentBox("Domain List", spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxContent, $myTpl->domainList));
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, SP_LOG_NOTICE, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_domainActive() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $sql = "UPDATE sp_modul_portal_settings SET domainActive = IF(domainActive=0,'1','0') WHERE ID = 1";
            $this->db->query($sql);
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_settings');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, SP_LOG_NOTICE, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

    public function portal_domainAddPageToDomain() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $sql = "SELECT page_domain FROM sp_modul_portal_cms_page WHERE ID = " . $this->db->checkValue($this->reqVar['pageID']);
            $result = $this->db->query($sql);
            $page = $this->db->fetch_array($result);
            $p_tmp = @unserialize($page['page_domain']);

            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, $page['page_domain']);

            if (is_array($p_tmp)) {
                if (isset($p_tmp[$this->reqVar['domainID']])) {
                    unset($p_tmp[$this->reqVar['domainID']]);
                } else {
                    $p_tmp[$this->reqVar['domainID']] = $this->reqVar['domainID'];
                }
            } else {
                $p_tmp[$this->reqVar['domainID']] = 1;
            }

            $sql = "SELECT page_relation FROM sp_modul_portal_cms_page WHERE ID= " . $this->db->checkValue($this->reqVar['pageID']);
            $relationResult = $this->db->query($sql);
            $relationResult = $this->db->fetch_array($relationResult);
            $relationResult = unserialize($relationResult['page_relation']);
            $relationID = array();

            if (count($relationResult) > 0) {
                foreach ($relationResult as $index) {
                    $sql = "UPDATE sp_modul_portal_cms_page SET page_domain = '" . $this->db->checkValue(serialize($p_tmp)) . "' WHERE ID = " . $this->db->checkValue($index);
                    $result = $this->db->query($sql);
                }
            } else {
                $sql = "UPDATE sp_modul_portal_cms_page SET page_domain = '" . $this->db->checkValue(serialize($p_tmp)) . "' WHERE ID = " . $this->db->checkValue($this->reqVar['pageID']);
                $result = $this->db->query($sql);
            }
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_NOTICE), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " portal_domainAddPageToDomain ...");
            exit();
        }
    }

    public function portal_domainDomainAddLanguage() {
        if ($this->permission['portal']['admin'] == true
                && isset($this->permission['portal']['detail'])
                && $this->permission['admin']['detail']['system_settings'] == 1) {

            $sql = "SELECT * FROM sp_modul_portal_language";
            $lang_result = $this->db->query($sql);

            $sql = "SELECT * FROM sp_modul_portal_domain";
            $domain_result = $this->db->query($sql);

            while ($langRes = $this->db->fetch_array($lang_result)) {
                $myLangRes [] = $langRes;
            }
            $sql = array();

            while ($res = $this->db->fetch_array($domain_result)) {
                foreach ($myLangRes as $index) {
                    if (isset($this->reqVar['domainLang_' . $res['ID'] . '_' . $index['id']])) {
                        $update[$index['id']] = $index['id'];
                    }
                }
                $sql = "UPDATE sp_modul_portal_domain SET domainLangs = '" . $this->db->checkValue(serialize($update)) . "' WHERE ID=" . $res['ID'];
                $update = null;

                $this->db->query($sql);
            }
            header('Location: ' . SP_CORE_HTTP_PROTOKOLL . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'] . '?modul=admin&action=portal_domainSettings');
            exit;
        } else {
            spcore\CLog::getInstance()->log(SP_LOG_NOTICE, SP_LOG_NOTICE, __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Permission failed ...");
            $this->notAllowed();
        }
    }

}

?>
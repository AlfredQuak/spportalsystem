<?php

/* spPortalSystem CPortal.php
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
require_once (SP_CORE_DOC_ROOT . "/includes/CSession.php");

/**
 * @defgroup portalsystem
 * @ref CPortal
 * @brief Main Class for standard portalfunctions
 * @details
 * This Class generate your menue and watch your sides,
 * you can here add your own menues to this mainfunction
 * with the modulplugin functions.
 * @todo
 * - lang userbased
 * - neuer editor
 */
class CPortal {

    private $reqVar;
    private $mainBox;
    private $tpl_MenueHeader;
    private $tpl_MenueLink;
    private $tpl_MenueLinkSub;
    private $tpl_MenueFooter;
    private $domainSetting;
    private $newsSystem;
    private $rssSystem;
    private $dynBox;
    private $boxMenue = array();
    private $myLang = array();
    private $tpl_myLangUrl = null;
    private $metaTag = array();
    private $startPage = null;
    private $domainLangArray = array();

    /**
     * \brief Constructor loads all needet Data
     * \details
     * Here we load all needed Data :
     * - loading box struc
     * - rss system
     * - javascripts
     * - general meta tags
     *
     * @param spcore\CDatabase::getInstance()
     * @param $template
     * @param $session
     * @param $requestVar
     * @param $g_system
     * @return none
     */
    public function __construct($session, $requestVar) {
        $this->reqVar = $requestVar;
        $this->myLang = array(SP_CORE_LANG);
        $myTmpLang = null;
        // standard settings from config.inc.php
        // can overwrite from system
        $this->metaTag['description'] = SP_PORTAL_META_DESCRIPTION;
        $this->metaTag['keywords'] = SP_PORTAL_META_KEYWOERDS;
        $this->metaTag['robots'] = SP_PORTAL_META_ROBOTS;
        $this->metaTag['company'] = SP_PORTAL_META_COMPANY;
        $this->metaTag['creator'] = SP_PORTAL_META_CREATOR;
        $this->metaTag['publisher'] = SP_PORTAL_META_PUBLISCHER;
        $this->metaTag['copyright'] = SP_PORTAL_META_COPYRIGHT;
        $this->metaTag['language'] = SP_PORTAL_META_LANGUAGE;
        $this->metaTag['content'] = SP_PORTAL_META_CONTENT;
        $this->metaTag['revisit-after'] = "2 days";

        spcore\CTemplate::getInstance()->setWatchRightBox(false);
        spcore\CTemplate::getInstance()->setWatchLeftBox(false);
        spcore\CTemplate::getInstance()->setWatchContentBox(false);

        $this->tpl_MenueHeader = spcore\CTemplate::getInstance()->loadModulTemplate('portal', 'menue_text_header');
        $this->tpl_MenueLink = spcore\CTemplate::getInstance()->loadModulTemplate('portal', 'menue_text_link');
        $this->tpl_MenueLinkSub = spcore\CTemplate::getInstance()->loadModulTemplate('portal', 'menue_text_link_sub');
        $this->tpl_MenueFooter = spcore\CTemplate::getInstance()->loadModulTemplate('portal', 'menue_box_footer');
        /*
          $this->tpl_MenueHeader	= self::checkThirdPartyScripts($this->tpl_MenueHeader);
          $this->tpl_MenueLink	= self::checkThirdPartyScripts($this->tpl_MenueLink);
          $this->tpl_MenueLinkSub	= self::checkThirdPartyScripts($this->tpl_MenueLinkSub);
          $this->tpl_MenueFooter  = self::checkThirdPartyScripts($this->tpl_MenueFooter);
         */
        //spcore\CTemplate::getInstance()->getXMLTemplate()->index = self::checkThirdPartyScripts(spcore\CTemplate::getInstance()->getXMLTemplate()->index);
        // set page language
        session_cache_limiter('public');

        //--
        $sql = "SELECT func_rss, func_news, domainActive FROM sp_modul_portal_settings WHERE ID = 1";
        $result = spcore\CDatabase::getInstance()->query($sql);
        if ($result) {
            $resObj = spcore\CDatabase::getInstance()->fetch_object($result);
            $this->rssSystem = $resObj->func_rss;
            $this->newsSystem = $resObj->func_news;
            $this->domainSetting = $resObj->domainActive;
        }

        if ($this->domainSetting == 1) {

            if (SP_PORTAL_DOMAIN_ID == 0 || SP_PORTAL_DOMAIN_ID === null) {
                spcore\CTemplate::getInstance()->addContentBox("Error", "No Domain ID ( SP_PORTAL_DOMAIN_ID )");
            }
            $sql = "SELECT domainLangs FROM `sp_modul_portal_domain` WHERE ID =" . SP_PORTAL_DOMAIN_ID;
            $tmpResult = spcore\CDatabase::getInstance()->query($sql);
            if ($tmpResult) {
                $tmpRes = spcore\CDatabase::getInstance()->fetch_array($tmpResult);
                $this->domainLangArray = unserialize($tmpRes['domainLangs']);
            }
            //var_dump($domainLangArray);
        }

        $sql = "SELECT * FROM sp_modul_portal_language";
        $result = spcore\CDatabase::getInstance()->query($sql);

        if ($result && spcore\CDatabase::getInstance()->num_rows($result) > 1) {
            if (isset($this->reqVar['lang'])) {
                spcore\CSession::getInstance(spcore\CDatabase::getInstance())->setLang($this->reqVar['lang']);
            } elseif (spcore\CSession::getInstance(spcore\CDatabase::getInstance())->getLang() === false) {
                spcore\CSession::getInstance(spcore\CDatabase::getInstance())->setLang(SP_CORE_LANG);
            }

            $this->myLang = null;
            while ($res = spcore\CDatabase::getInstance()->fetch_assoc($result)) {
                if ($this->domainSetting == 1 && isset($this->domainLangArray[$res['id']]) && count($this->domainLangArray) > 1) {
                    $this->tpl_myLangUrl .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('langId' => $res['lang_lang'], 'langTag' => $res['lang_lang']), spcore\CTemplate::getInstance()->gettplXmlObj()->indexLangURL);

                    $this->myLang[$res['lang_lang']] = $res;
                    $myTmpLang[] = $res['lang_lang'];
                } elseif ($this->domainSetting == 1 && isset($this->domainLangArray[$res['id']]) && count($this->domainLangArray) <= 1) {
                    $this->myLang[$res['lang_lang']] = $res;
                    $myTmpLang[] = $res['lang_lang'];
                } elseif ($this->domainSetting == 0) {
                    $this->tpl_myLangUrl .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('langId' => $res['lang_lang'], 'langTag' => $res['lang_lang']), spcore\CTemplate::getInstance()->gettplXmlObj()->indexLangURL);
                    $this->myLang[$res['lang_lang']] = $res;
                    $myTmpLang[] = $res['lang_lang'];
                }

                //$this->myLang[$res['lang_lang']] = $res;
                //$myTmpLang[] = $res['lang_lang'];
            }
            spcore\CTemplate::getInstance()->addtplXmlObj(array("indexHeader" => spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('langURL' => $this->tpl_myLangUrl), spcore\CTemplate::getInstance()->gettplXmlObj()->indexHeader)));

            $this->myLang = $this->myLang[CLang::getInstance()->prefered_language($myTmpLang, spcore\CSession::getInstance(spcore\CDatabase::getInstance())->getLang())];
            $langQuerySnipped = "AND a.lang_id = " . $this->myLang['id'];
        } else {
            $langQuerySnipped = null;
        }

        // delete logged IP older than one day
        $sql = "DELETE FROM `sp_settings_counter` WHERE `time` < (NOW() - INTERVAL 1 DAY)";
        $result = spcore\CDatabase::getInstance()->query($sql);
        if ($result) {
            // user iss count ?
            $sql = "SELECT COUNT(*) FROM `sp_settings_counter` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "'";
            $result = spcore\CDatabase::getInstance()->query($sql);
            $res = spcore\CDatabase::getInstance()->fetch_array($result);

            // if not, count+1
            if (empty($res['COUNT(*)']) || $res['COUNT(*)'] <= 1) {
                $sql = "INSERT INTO `sp_settings_counter` (`ip`, `time`) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', NOW())";
                $result = spcore\CDatabase::getInstance()->query($sql);
                $sql = "UPDATE `sp_settings` SET `counter` = (`counter` +1) WHERE `ID` = 0";
                $result = spcore\CDatabase::getInstance()->query($sql);
            }
        }

        if ($this->newsSystem == 1) {
            if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
                $this->boxMenue[0][0] = $this->addBoxMenueLink("/", "/", "/", "{PORTAL_HTTP_HOST}", "Home");
            } else {
                $this->boxMenue[0][0] = $this->addBoxMenueLink("", "", "", "{PORTAL_HTTP_HOST}", "Home");
            }
        }

        // -> load box struc and side struc
        $sql = "SELECT * FROM sp_modul_settings WHERE modul_name = '' AND modul_installed = 0 AND modul_box_activ = 1 ORDER BY modul_box_pos ASC";
        $result = spcore\CDatabase::getInstance()->query($sql);

        $this->dynBox[0]['boxObj'] = null;
        if ($result) {
            while ($o_res = spcore\CDatabase::getInstance()->fetch_assoc($result)) {
                $this->dynBox[$o_res['ID']]['boxObj'] = $o_res;
                $myPBoxes[$o_res['ID']] = $o_res['modul_box_titel'];
            }
        }

        // <- load box struc and side struc
        // -> add main side for struc
        $sql = "SELECT
                            a.*, 
                            b.titel_text as page_titel,
                            b.page_domain
                    FROM
                            sp_modul_portal_cms_menue_main_side as a,
                            sp_modul_portal_cms_page as b
                    WHERE
                            a.page_id = b.ID AND
                            a.active = 1
                            " . spcore\CDatabase::getInstance()->checkValue($langQuerySnipped) . "
                    ORDER BY
                            a.position
                    ASC";

        $result = spcore\CDatabase::getInstance()->query($sql);
        if ($result) {
            while ($o_res = spcore\CDatabase::getInstance()->fetch_assoc($result)) {
                //** domain settings on or off ?
                //** is page active for this domain ?
                $myDomain = true;
                if ($this->domainSetting == 1 && isset($o_res['page_domain'])) {
                    $d_tmp = @unserialize($o_res['page_domain']);
                    if (is_array($d_tmp) === true) {
                        if (in_array(SP_PORTAL_DOMAIN_ID, $d_tmp)) {
                            $myDomain = true;
                        } else {
                            $myDomain = false;
                        }
                    } else {
                        $myDomain = false;
                    }
                }
                if ($myDomain) {
                    $this->dynBox[($o_res['box'] == 0 ? 0 : $o_res['box'])]['sides'][$o_res['ID']] = $o_res;
                } else {
                    if ($this->domainSetting != 1) {
                        $this->dynBox[($o_res['box'] == 0 ? 0 : $o_res['box'])]['sides'][$o_res['ID']] = $o_res;
                    }
                }
            }
            // <- add main side for struc
            // -> add sub side for struc
            $sql = "SELECT
					a.*, 
					b.titel_text as page_titel,
                                        b.page_domain,
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

            $result = spcore\CDatabase::getInstance()->query($sql);
            if ($result) {
                while ($o_res = spcore\CDatabase::getInstance()->fetch_assoc($result)) {
                    //** domain settings on or off ?
                    //** is page active for this domain ?
                    if ($this->domainSetting == 1 && isset($o_res['page_domain'])) {
                        $d_tmp = @unserialize($o_res['page_domain']);
                        //var_dump($d_tmp);
                        if (isset($d_tmp[SP_PORTAL_DOMAIN_ID])) {
                            $this->dynBox[($o_res['main_box'] == 0 ? 0 : $o_res['main_box'])]['sides'][$o_res['mainlink_id']]['subsides'][$o_res['page_id']] = $o_res;
                        }
                    } else {
                        $this->dynBox[($o_res['main_box'] == 0 ? 0 : $o_res['main_box'])]['sides'][$o_res['mainlink_id']]['subsides'][$o_res['page_id']] = $o_res;
                    }
                }
            }
            // <- add sub side for struc
        }
        if (is_array($this->dynBox)) {
            foreach ($this->dynBox as $myBox => $value) {
                // mainlinks ->
                if (isset($value['sides']) && is_array($value['sides'])) {
                    foreach ($value['sides'] as $sides) {
                        $boxTag = null;
                        $boxTag = $this->dynBox[($myBox == 0 ? 0 : $myBox)]['boxObj']['modul_box_titel'];
                        if (empty($boxTag)) {
                            $boxTag = "Navigation";
                        }
                        $boxTag = str_replace("/", "-", $boxTag);

                        //** load boxmenue and add to frontend
                        if (isset($sides['ID'])) {
                            $box['jsId'] = $sides['ID'];
                            //** ----
                            if ($this->startPage == null) {
                                $this->startPage = $sides['page_id'];
                            }
                            //---
                            if (isset($this->boxMenue[($myBox == 0 ? 0 : $myBox)]) &&
                                    isset($this->boxMenue[($myBox == 0 ? 0 : $myBox)][$sides['position']]) &&
                                    $this->boxMenue[($myBox == 0 ? 0 : $myBox)][$sides['position']] == 0) {

                                if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
                                    $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][0] =
                                            $this->addBoxMenueLink($sides['ID'], $sides['page_id'], (isset($sides['subsides']) && is_array($sides['subsides']) ? count($sides['subsides']) : 0), $this->clearURLForRewrite($sides['page_id'], $sides['ID'], $boxTag, $sides['link_text']), $sides['link_text']);
                                } else {
                                    $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][0] =
                                            $this->addBoxMenueLink($sides['ID'], $sides['page_id'], (isset($sides['subsides']) && is_array($sides['subsides']) ? count($sides['subsides']) : 0), "{PORTAL_HTTP_HOST}?modul=portal&action=CMS&page=" . $sides['page_id'] . "&sub=" . $sides['ID'], $sides['link_text']);
                                }
                            } else {
                                if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
                                    $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][] =
                                            $this->addBoxMenueLink($sides['ID'], $sides['page_id'], (isset($sides['subsides']) && is_array($sides['subsides']) ? count($sides['subsides']) : 0), $this->clearURLForRewrite($sides['page_id'], $sides['ID'], $boxTag, $sides['link_text']), $sides['link_text']);
                                } else {
                                    $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][] =
                                            $this->addBoxMenueLink($sides['ID'], $sides['page_id'], (isset($sides['subsides']) && is_array($sides['subsides']) ? count($sides['subsides']) : 0), "{PORTAL_HTTP_HOST}?modul=portal&action=CMS&page=" . $sides['page_id'] . "&sub=" . $sides['ID'], $sides['link_text']);
                                }
                            }
                            // <- mainlinks
                            // sublinks ->
                            if (isset($sides['subsides']) && $myDomain) {
                                $i = 0;
                                $putIn = "<!-- subsite beginn -->\n";
                                if ($this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']] == 0) {
                                    $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][0] = $putIn;
                                } else {
                                    $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][] = $putIn;
                                }

                                foreach ($sides['subsides'] as $mySides => $mySidesValue) {
                                    if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
                                        $putIn = $this->addBoxMenueLinkSub($sides['ID'], $i++, $this->clearURLForRewrite($mySidesValue['page_id'], $sides['ID'], $boxTag, $mySidesValue['link_text']), $mySidesValue['link_text'], $mySidesValue['page_id']);
                                    } else {
                                        $putIn = $this->addBoxMenueLinkSub($sides['ID'], $i++, "{PORTAL_HTTP_HOST}?modul=portal&action=CMS&page=" . $mySidesValue['page_id'] . "&sub=" . $sides['ID'], $mySidesValue['link_text'], $mySidesValue['page_id']);
                                    }
                                    if (!empty($putIn)) {
                                        if ($this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']] == 0) {
                                            $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][0] .= $putIn;
                                        } else {
                                            $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][] = $putIn;
                                        }
                                    }
                                }
                                $this->boxMenue[$myBox == 0 ? 0 : $myBox][$sides['position']][] = "<!-- subsite end -->\n";
                            }
                            //<- sublinks
                        }
                    }
                }
            }
        }
    }

    /**
     * \brief Search function
     * \details Find your artikel about your search woerds
     */
    public function CMSSearching() {

        $myContent['1select'] = null;
        $myContent['2select'] = null;
        $myContent['3select'] = null;

        if (isset($this->reqVar['searchOption'])) {
            switch ($this->reqVar['searchOption']) {
                case "1":   // every woerd
                    $myContent['1select'] = "selected";

                    foreach (explode(" ", $this->reqVar['searchword']) as $value) {
                        $myTmp[] = "(  titel_text LIKE '%" . spcore\CDatabase::getInstance()->checkValue($value) . "%'
                               OR page_content LIKE '%" . spcore\CDatabase::getInstance()->checkValue($value) . "%' )";
                    }
                    $sqlWhere = implode(" OR ", $myTmp);
                    break;
                case "2":   // all woerds
                    $myContent['2select'] = "selected";

                    $sqlWhere = "(  titel_text LIKE '%" . spcore\CDatabase::getInstance()->checkValue($this->reqVar['searchword']) . "%'
                               OR page_content LIKE '%" . spcore\CDatabase::getInstance()->checkValue($this->reqVar['searchword']) . "%' )";
                    break;
                case "3":   // exacly woerd
                    $myContent['3select'] = "selected";

                    foreach (explode(" ", $this->reqVar['searchword']) as $value) {
                        $myTmp[] = "(  titel_text = '" . spcore\CDatabase::getInstance()->checkValue($value) . "'
                               OR page_content = '" . spcore\CDatabase::getInstance()->checkValue($value) . "' )";
                    }
                    $sqlWhere = implode(" OR ", $myTmp);
                    break;
            }
        } else {
            $this->reqVar['searchOption'] = 1;
            $sqlWhere = "(  titel_text LIKE '%" . spcore\CDatabase::getInstance()->checkValue($this->reqVar['searchword']) . "%'
                       OR page_content LIKE '%" . spcore\CDatabase::getInstance()->checkValue($this->reqVar['searchword']) . "%' )";
        }

        $sql = "SELECT sp_modul_portal_cms_page.ID AS PAGE_ID
                    FROM sp_modul_portal_cms_page
                        INNER JOIN sp_modul_portal_cms_menue_main_side ON sp_modul_portal_cms_page.ID = sp_modul_portal_cms_menue_main_side.page_id
                    WHERE " . $sqlWhere . "
                        AND page_lang = " . spcore\CDatabase::getInstance()->checkValue(isset($this->myLang['id']) ? $this->myLang['id'] : "") . "
                        AND sp_modul_portal_cms_menue_main_side.active = 1
                    UNION
                    SELECT sp_modul_portal_cms_page.ID AS PAGE_ID
                        FROM sp_modul_portal_cms_page
                            INNER JOIN sp_modul_portal_cms_menue_sub_side ON sp_modul_portal_cms_page.ID = sp_modul_portal_cms_menue_sub_side.page_id
                    WHERE " . $sqlWhere . "
                        AND page_lang = " . spcore\CDatabase::getInstance()->checkValue(isset($this->myLang['id']) ? $this->myLang['id'] : "") . "
                        AND sp_modul_portal_cms_menue_sub_side.active = 1";

        $result = spcore\CDatabase::getInstance()->query($sql);
        $sum = spcore\CDatabase::getInstance()->num_rows($result);

        $step = 9;
        $stepContentOut = null;
        $searchStep = 0;
        $index = 1;
        $tmp = floor($sum / $step + 1) * $step;

        if ($sum % $step > 0) {
            while ($searchStep != $tmp) {
                $stepContentOut .= " <a href=\"{PORTAL_HTTP_HOST}?modul=portal&action=CMSSearching&searchOption=" . $this->reqVar['searchOption'] . "&searchword=" . $this->reqVar['searchword'] . "&a=" . $searchStep . "&b=" . ($searchStep + $step) . "\">[" . $index . "]</a> ";
                $searchStep += $step;
                $index++;
            }
        }


        $myContent['searchword'] = htmlentities($this->reqVar['searchword']);

        if (isset($this->reqVar['a']) && isset($this->reqVar['b'])) {
            $myContent['SearchBack'] = "{PORTAL_HTTP_HOST}?modul=portal&action=CMSSearching&searchOption=" . htmlentities($this->reqVar['searchOption']) . "&searchword=" . htmlentities($this->reqVar['searchword']) . "&a=" . ((htmlentities($this->reqVar['b']) - $step) <= 0 ? "0" : (htmlentities($this->reqVar['a']) - $step)) . "&b=" . ((htmlentities($this->reqVar['b']) - $step) <= 0 ? $step : (htmlentities($this->reqVar['b']) - $step));
            $myContent['SearchTo'] = "{PORTAL_HTTP_HOST}?modul=portal&action=CMSSearching&searchOption=" . htmlentities($this->reqVar['searchOption']) . "&searchword=" . htmlentities($this->reqVar['searchword']) . "&a=" . ((htmlentities($this->reqVar['b']) + $step) > $tmp ? ($tmp - $step) : htmlentities($this->reqVar['b']) ) . "&b=" . ((htmlentities($this->reqVar['b']) + $step) > $tmp ? $tmp : (htmlentities($this->reqVar['b']) + $step));
            $limit = "LIMIT " . spcore\CDatabase::getInstance()->checkValue($this->reqVar['a']) . " , " . spcore\CDatabase::getInstance()->checkValue($this->reqVar['b']);
        } else {
            $myContent['SearchBack'] = "{PORTAL_HTTP_HOST}?modul=portal&action=CMSSearching&searchOption=" . htmlentities($this->reqVar['searchOption']) . "&searchword=" . htmlentities($this->reqVar['searchword']) . "&a=0&b=" . $step;
            $myContent['SearchTo'] = "{PORTAL_HTTP_HOST}?modul=portal&action=CMSSearching&searchOption=" . htmlentities($this->reqVar['searchOption']) . "&searchword=" . htmlentities($this->reqVar['searchword']) . "&a=" . $step . "&b=" . ($step + $step);
            $limit = "LIMIT 0 , " . spcore\CDatabase::getInstance()->checkValue($step);
        }

        $boxsql = "SELECT * FROM sp_modul_settings WHERE modul_name = '' AND modul_installed = 0 AND modul_box_activ = 1 ORDER BY modul_box_pos ASC";
        $boxresult = spcore\CDatabase::getInstance()->query($sql);

        if ($boxresult) {
            while ($o_res = spcore\CDatabase::getInstance()->fetch_assoc($boxresult)) {
                if (isset($o_res['modul_box_titel'])) {
                    $myPBoxes[$o_res['ID']] = $o_res['modul_box_titel'];
                }
            }
        }
        $sql = "SELECT sp_modul_portal_cms_page.ID AS ID,
                       sp_modul_portal_cms_page.titel_text,
                       sp_modul_portal_cms_page.page_content,
                       sp_modul_portal_cms_page.page_domain,
                       'MAINSIDE' AS KENNUNG,
                       sp_modul_portal_cms_menue_main_side.ID AS PAGE_ID_SIDE,
                       sp_modul_portal_cms_menue_main_side.active,
                       sp_modul_portal_cms_menue_main_side.box
                FROM sp_modul_portal_cms_page
                INNER JOIN sp_modul_portal_cms_menue_main_side ON sp_modul_portal_cms_page.ID = sp_modul_portal_cms_menue_main_side.page_id
                WHERE " . $sqlWhere . "
                AND page_lang = " . spcore\CDatabase::getInstance()->checkValue(isset($this->myLang['id']) ? $this->myLang['id'] : "") . "
                " . /* AND sp_modul_portal_cms_menue_main_side.active = 1 */"
                UNION
                SELECT sp_modul_portal_cms_page.ID AS PAGE_ID,
                       sp_modul_portal_cms_page.titel_text,
                       sp_modul_portal_cms_page.page_content,
                       sp_modul_portal_cms_page.page_domain,
                       'SUBSIDE' AS KENNUNG,
                       sp_modul_portal_cms_menue_sub_side.mainlink_id AS PAGE_ID_SIDE,
                       sp_modul_portal_cms_menue_sub_side.active,
                       sp_modul_portal_cms_menue_sub_side.box
                FROM sp_modul_portal_cms_page
                INNER JOIN sp_modul_portal_cms_menue_sub_side ON sp_modul_portal_cms_page.ID = sp_modul_portal_cms_menue_sub_side.page_id
                WHERE " . $sqlWhere . "
                AND page_lang = " . spcore\CDatabase::getInstance()->checkValue(isset($this->myLang['id']) ? $this->myLang['id'] : "") . "
                " . /* AND sp_modul_portal_cms_menue_sub_side.active = 1 */"
                " . $limit;

        $result = spcore\CDatabase::getInstance()->query($sql);
        $myContentMain = spcore\CTemplate::getInstance()->parseModulTemplate("portal", array('searchStep' => $stepContentOut), spcore\CTemplate::getInstance()->gettplXmlObj()->searchAmountMain);
        $myContent['searchBoxes'] = null;
        if ($result !== false && spcore\CDatabase::getInstance()->num_rows($result) != 0) {
            while ($res = spcore\CDatabase::getInstance()->fetch_assoc($result)) {
                //** is page active for this domain ?
                $myDomain = true;
                if ($this->domainSetting == 1 && isset($res['page_domain'])) {
                    $d_tmp = @unserialize($res['page_domain']);
                    if (is_array($d_tmp) === true) {
                        if (in_array(SP_PORTAL_DOMAIN_ID, $d_tmp)) {
                            $myDomain = true;
                        } else {
                            $myDomain = false;
                        }
                    } else {
                        $myDomain = false;
                    }
                }
                if ($myDomain) {
                    if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
                        $boxContent['amountURL'] = "{PORTAL_HTTP_HOST}/p/" . $res['ID'] . "_" . $res['PAGE_ID_SIDE'] . "_" . $myPBoxes[$res['box']] . "-" . $this->clearString($res['titel_text']) . ".html";
                    } else {
                        $boxContent['amountURL'] = "{PORTAL_HTTP_HOST}?modul=portal&action=CMS&page=" . $res['ID'] . "&sub=" . $res['PAGE_ID_SIDE'];
                    }
                    $boxContent['titel'] = strip_tags(self::clearHTMLout($res['titel_text']));
                    $boxContent['searchAmount'] = substr(strip_tags(self::clearHTMLout($res['page_content'])), 0, 200) . "..";
                    $myContent['searchBoxes'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxContent, spcore\CTemplate::getInstance()->gettplXmlObj()->searchAmount);
                }
            }
        } else {
            $boxContent['titel'] = null;
            $boxContent['searchAmount'] = "{LANG_portal_noAmount}";
            $myContent['searchBoxes'] .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxContent, spcore\CTemplate::getInstance()->gettplXmlObj()->searchAmount);
        }

        $myContentMain = spcore\CTemplate::getInstance()->parseModulTemplate("portal", $myContent, $myContentMain);
        spcore\CTemplate::getInstance()->addContentBox("suche", $myContentMain);
    }

    /**
     * \brief Load articel with $id
     * \details
     * Load articel with $id and add this to content in contentbox
     * @param $id
     * @return none
     */
    public function loadArtikel($id) {
        if ($id === false) {
            //** emergency if/else ,if no startpage give system will select start page about page active and language
            if ($this->startPage != null) {
                $sql = "SELECT
                        b.*,
                        c.*
                    FROM
                        sp_modul_portal_cms_menue_main_side as b,
                        sp_modul_portal_cms_page as c
                    WHERE
                        " . (isset($this->myLang['id']) ? "b.lang_id       = " . spcore\CDatabase::getInstance()->checkValue($this->myLang['id']) . " AND " : "") . "
                        b.active    = 1
                        AND b.page_id   = c.ID
                        AND b.page_id   = " . $this->startPage . "
                        ORDER BY b.box, b.position ASC
                    LIMIT 0,1";
            } else {
                $sql = "SELECT
                              b.*,
                              c.*
                          FROM
                              sp_modul_portal_cms_menue_main_side as b,
                              sp_modul_portal_cms_page as c
                          WHERE
                              " . (isset($this->myLang['id']) ? "b.lang_id       = " . spcore\CDatabase::getInstance()->checkValue($this->myLang['id']) . " AND " : "") . "
                              b.active = 1
                              AND b.page_id = c.ID
                              ORDER BY b.box, b.position ASC
                          LIMIT 0,1";
            }
        } else {
            $sql = 'SELECT * FROM `sp_modul_portal_cms_page` WHERE ID = ' . spcore\CDatabase::getInstance()->checkValue($id);
        }
        if ($this->checkSearch()) {
            $this->CMSSearching();
        } else {
            $result = spcore\CDatabase::getInstance()->query($sql);
            if ($result && spcore\CDatabase::getInstance()->num_rows($result) > 0) {
                $res = spcore\CDatabase::getInstance()->fetch_array($result);

                //** domain settings on or off ?
                if ($this->domainSetting == 1) {
                    //** is page active for this domain ?
                    $myDomain = true;
                    if ($this->domainSetting == 1 && isset($res['page_domain'])) {
                        $d_tmp = @unserialize($res['page_domain']);
                        if (is_array($d_tmp) === true) {
                            if (in_array(SP_PORTAL_DOMAIN_ID, $d_tmp)) {
                                $myDomain = true;
                            } else {
                                $myDomain = false;
                            }
                        } else {
                            $myDomain = false;
                        }
                    }
                    if ($myDomain) {
                        $box['titel_text'] = self::clearHTMLout($res['titel_text']);
                        $box['page_content'] = self::clearHTMLout($res['page_content']);

                        $this->metaTag['description'] = substr(strip_tags(self::clearMetaTag($res['page_content'])), 0, 250) . "...";
                        spcore\CTemplate::getInstance()->setTitelTag(self::clearMetaTag($res['titel_text']));
                        spcore\CTemplate::getInstance()->addContentBox($box['titel_text'], $box['page_content']);
                    } else {
                        spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_error}", "{LANG_portal_error}");
                        spcore\CLog::getInstance()->log(SP_LOG_WARNING, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Page not found ...");
                    }
                } else {
                    $box['titel_text'] = self::clearHTMLout($res['titel_text']);
                    $box['page_content'] = self::clearHTMLout($res['page_content']);

                    $this->metaTag['description'] = substr(strip_tags(self::clearMetaTag($res['page_content'])), 0, 250) . "...";
                    spcore\CTemplate::getInstance()->setTitelTag(self::clearHTMLout($res['titel_text']));
                    spcore\CTemplate::getInstance()->addContentBox($box['titel_text'], $box['page_content']);
                }
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_error}", "{LANG_portal_error}");
                spcore\CLog::getInstance()->log(SP_LOG_WARNING, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Page not found ...");
            }
        }
    }

    public function load_extmodul() {
        $this->loadBoxes();
        if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $this->reqVar['mod'] . "/includes/C" . $this->reqVar['mod'] . ".php")) {
            require_once (SP_CORE_DOC_ROOT . "/module/" . $this->reqVar['mod'] . "/includes/C" . $this->reqVar['mod'] . ".php");
            $extClass = "C" . $this->reqVar['mod'];
            $extmodul = new $extClass(spcore\CDatabase::getInstance());
            $extmodul->_cont_extportal();
        } else {
            spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_error}", "{LANG_portal_error}");
            spcore\CLog::getInstance()->log(SP_LOG_WARNING, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Extern modul (" . $this->reqVar['mod'] . ") not found ...");
        }
    }

    private function clearForURLRewrite($content) {
        if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
            //module/portal/uploadimage
            $content = str_replace("../../{PORTAL_SUBDOC_PATH}", SP_PORTAL_HTTP_HOST, $content);
            $content = str_replace("<img src=\"template", "<img src=\"" . SP_PORTAL_HTTP_HOST . "/template", $content);
            $content = str_replace("../{PORTAL_SUBDOC_PATH}", SP_PORTAL_HTTP_HOST, $content);
            $content = str_replace("=\"module/portal/uploadimage", "=\"" . SP_PORTAL_HTTP_HOST . "/module/portal/uploadimage/", $content);
            $content = str_replace("=\"uploadfiles/", "=\"" . SP_PORTAL_HTTP_HOST . "/uploadfiles/", $content);
        }
        return $content;
    }

    /*
      private function myReplace($s) {
      if (file_exists(SP_CORE_DOC_ROOT . "/thirdPT/thirdPT_" . $s[1] . ".php")) {
      require_once (SP_CORE_DOC_ROOT ."/thirdPT/thirdPT_" . $s[1] . ".php");
      $myFunction = "thirdPT_" . $s[1];
      // $myFunction($wert) ist möglich
      return $myFunction(spcore\CDatabase::getInstance());
      } else {
      return "not found : " . SP_CORE_DOC_ROOT . "/thirdPT/thirdPT_" . $s[1] . ".php";
      }
      }

      private function myReplaceModul_cont($s) {
      if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php")) {
      require_once (SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php");
      $extClass = "C" . $s[1];
      $extmodul = new $extClass(spcore\CDatabase::getInstance()); //(spcore\CDatabase::getInstance());
      //return $extmodul;
      return $extmodul->_cont_extportal();
      } else {
      return "not found : " . SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php";
      }
      }

      private function myReplaceModul_nav($s) {
      if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php")) {
      require_once (SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php");
      $extClass = "C" . $s[1];
      $extmodul = new $extClass(spcore\CDatabase::getInstance()); //(spcore\CDatabase::getInstance());
      //return $extmodul;
      return $extmodul->_nav_extportal();
      } else {
      return "not found : " . SP_CORE_DOC_ROOT . "/module/" . $s[1] . "/includes/C" . $s[1] . ".php";
      }
      }

      private function checkThirdPartyScripts($string) {
      $string = preg_replace_callback("'\{thirdPT_([a-z]+)}'", 'self::myReplace', $string);
      $string = preg_replace_callback("'\{modul_cont_([a-z]+)}'", 'self::myReplaceModul_cont', $string);
      $string = preg_replace_callback("'\{modul_nav_([a-z]+)}'", 'self::myReplaceModul_nav', $string);
      return $string;
      }
     */

    private function clearString($string) {
        $string = str_replace("/", "-", $string);
        $string = str_replace(" ", "-", $string);
        $string = str_replace(".", "-", $string);
        $string = str_replace(":", "", $string);
        return $string;
    }

    private function clearURLForRewrite($pageid, $subid, $boxname, $linkname) {
        return "{PORTAL_HTTP_HOST}/p/" . $pageid . "_" . $subid . "_" . $boxname . "-" . $this->clearString($linkname) . ".html";
    }

    /**
     * \brief check if search option is active
     * \details
     * Check if search option active, return true if active
     * and return false if not
     * @return bool
     */
    public function checkSearch() {
        if (isset($this->reqVar['action']) && $this->reqVar['action'] == "CMSSearching" && isset($this->reqVar['searchword']) && !empty($this->reqVar['searchword'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * \brief Clear HTML only for this Modul
     * \details
     * You can add HTML or Javascript into your pages,<br>
     * Chars will be convert into entitis but HTML Tags <br>
     * and scripts make workable.<br>
     *
     * @param $string encode HTML
     * @return string cleared HTML
     */
    private function clearHTMLout($string) {
        $string = str_replace("&lt;", "<", $string);
        $string = str_replace("&gt;", ">", $string);
        $string = str_replace("&quot;", '"', $string);
        $string = str_replace("&amp;", '&', $string);
        return $this->clearForURLRewrite($string);
    }

    private function clearMetaTag($string) {
        $string = preg_replace("'\{modul_cont::([a-z]+)}'", "", $string);
        $string = preg_replace("'\{modul_nav::([a-z]+)}'", "", $string);
        $string = preg_replace("'\{modul_func::(.*)::(.*)\((.*)\)}'", "", $string);
        $string = preg_replace("'\{template::([a-z]+)}'", "", $string);
        $string = preg_replace("'\{thirdPT::([a-z]+)}'", "", $string);

        $string = str_replace("&lt;", "<", $string);
        $string = str_replace("&gt;", ">", $string);
        $string = str_replace("&quot;", '"', $string);
        $string = str_replace("&amp;", '&', $string);
        return $this->clearForURLRewrite($string);
    }

    /**
     * \brief Main function
     * \details
     * If you give id as $loadPage this function loads your page.<br>
     * If you give false and News System is set here loads the news system.<br>
     * Menue boxes loads here too.<br>
     * @param $loadPage
     * @return none
     */
    public function main($loadPage) {
        if ($this->checkSearch()) {
            $this->CMSSearching();
        } else if ($loadPage === false && $this->newsSystem == 1) {
            $sql = "SELECT * FROM sp_modul_portal_cms_news WHERE active=1 ORDER BY news_date DESC LIMIT 0 , 5";
            $result = spcore\CDatabase::getInstance()->query($sql);

            $box['titel_text'] = null;
            $box['page_content'] = null;
            $box['news_date'] = null;

            if ($result != null) {
                if (spcore\CDatabase::getInstance()->num_rows($result) == 0) {
                    @spcore\CTemplate::getInstance()->addContentBox(date("D d.m.Y", time()) . " / {LANG_portal_noNews}", "{LANG_portal_noNewsToday}");
                } else {
                    while ($res = spcore\CDatabase::getInstance()->fetch_array($result)) {
                        $box['titel_date'] = date("D d.m.Y", $res['news_date']);
                        $box['titel_text'] = $res['titel_text'];
                        $box['page_content'] = self::clearHTMLout($res['page_content']);
                        spcore\CTemplate::getInstance()->addContentBoxNews($box['titel_date'],$box['titel_text'], $box['page_content']);
                    }
                }
            } else {
                spcore\CTemplate::getInstance()->addContentBox("{LANG_portal_error}", "{LANG_portal_error}");
                spcore\CLog::getInstance()->log(SP_LOG_WARNING, ( isset($_SESSION['user_dblvl']) ? $_SESSION['user_dblvl'] : SP_LOG_WARNING), __CLASS__ . "::" . __FUNCTION__, null, null, "Line: " . __LINE__ . " Page not found ...");
            }
        } else {
            $this->loadArtikel($loadPage);
        }

        $this->loadBoxes();
        spcore\CTemplate::getInstance()->setMetaTags($this->metaTag);
    }

    private function loadBoxes() {
        if (isset($this->boxMenue[0]) && is_array($this->boxMenue[0])) {
            foreach ($this->boxMenue[0] as $index => $var) {
                if (is_array($this->boxMenue[0][$index])) {
                    foreach ($this->boxMenue[0][$index] as $mainIndex => $mainValue) {
                        $this->mainBox .= $this->boxMenue[0][$index][$mainIndex];
                    }
                } else {
                    $this->mainBox .= $this->boxMenue[0][$index];
                }
            }
        }

        if (!empty($this->mainBox)) {
            spcore\CTemplate::getInstance()->addLeftBox("{LANG_portal_main}", $this->mainBox);
        }

        // load box settings from database
        $sql = 'SELECT * FROM `sp_modul_settings` WHERE modul_box_activ = 1 ORDER BY modul_box_pos ASC';
        $result = spcore\CDatabase::getInstance()->query($sql);

        if ($result != null) {
            while ($res = spcore\CDatabase::getInstance()->fetch_array($result)) {
                $box['page_content'] = '';
                $box['titel_text'] = $res['modul_box_titel'];
                if ($res['modul_box_content_dyn'] == 0) {
                    if (isset($this->boxMenue[$res['ID']])) {
                        // generate link 4 boxmenue + sublinks
                        //$box['page_content'] .= "<ul class=\"Navigation\">\n";
                        $box['page_content'] .= "\n";
                        foreach ($this->boxMenue[$res['ID']] as $index => $var) {
                            if (is_array($this->boxMenue[$res['ID']][$index])) {
                                foreach ($this->boxMenue[$res['ID']][$index] as $subIndex => $subVar) {
                                    $box['page_content'] .= $this->boxMenue[$res['ID']][$index][$subIndex] . "\n";
                                }
                            } else {
                                $box['page_content'] .= $this->boxMenue[$res['ID']][$index] . "\n";
                            }
                        }
                        //$box['page_content'] .= "</u1>\n";
                        $box['page_content'] .= "\n";
                    } else {
                        $box['page_content'] = $res['modul_box_content'];
                    }
                } else {
                    // @todo load dynamic box from modul
                }

                if ($res['modul_box_r'] == 0) {
                    if (!empty($box['page_content'])) {
                        spcore\CTemplate::getInstance()->addLeftBox($box['titel_text'], $box['page_content']);
                    }
                } else {
                    if (!empty($box['page_content'])) {
                        spcore\CTemplate::getInstance()->addRightBox($box['titel_text'], $box['page_content']);
                    }
                }
            }
        }
    }

    /**
     * \brief Add your menue header
     * \details
     * In mainbenue you can add a menue header.
     * @param $name
     * @return none
     */
    private function addMenueHeader($name) {
        $boxArray['header'] = $name;
        $this->mainBox .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $this->tpl_MenueHeader);
    }

    /**
     * \brief Add menue link in own box.
     * \details
     * adding menue link for user box
     *
     * \param $id javascriptid
     * \param $pageId pageid
     * \param $sum subpages sum
     * \param $link link
     * \param $linkText linktext
     */
    private function addBoxMenueLink($id, $pageId, $sum, $link, $linkText) {
        $boxArray['link'] = $link;
        $boxArray['linkText'] = $linkText;
        $boxArray['jsId'] = $id;
        $boxArray['onClick'] = ($sum > 0 ? 'onclick="main_myBoxHide(' . $id . ',' . $sum . ')"' : '');
        $boxArray['linkActive'] = (isset($this->reqVar['page']) && $pageId == $this->reqVar['page'] ? ' id="linkActive" ' : '');

        return spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $this->tpl_MenueLink);
    }

    /**
     * \brief Add submenuelink.
     * \details
     * Adding submenue link for user box
     *
     * @param $id
     * @param $pid
     * @param $link
     * @param $linkText
     * @param $pageid
     * @return none
     */
    private function addBoxMenueLinkSub($id, $pid, $link, $linkText, $pageID = 0) {
        $boxArray['link'] = $link;
        $boxArray['linkText'] = $linkText;
        $boxArray['jsId'] = $id;
        $boxArray['trId'] = $pid;
        if (isset($this->reqVar['sub']) && $this->reqVar['sub'] == $id) {
            $boxArray['linkActive'] = (isset($this->reqVar['page']) && $pageID == $this->reqVar['page'] ? ' id="linkActive" ' : '');
        } else {
            $boxArray['linkActive'] = 'id="sub_' . $id . '" style="display: none;"';
        }
        return spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $this->tpl_MenueLinkSub);
    }

    /**
     * \brief
     * get menue data from modul
     * \todo
     * function is not ready
     *
     * @return unknown_type
     */
    private function getMenueModule() {
        /**
         * ToDo: prüfen ob das entsprechende modul auch schon installiert ist.
         * wenn nein nicht anzeigen
         */
        $boxArray['menueAdd'] = "";
        $sql = "SELECT * FROM sp_modul_portal_modul_menue WHERE modul_active = 1 ORDER BY pos";
        $result = spcore\CDatabase::getInstance()->query($sql);

        while ($res = spcore\CDatabase::getInstance()->fetch_array($result)) {
            $userData[$res['name']]['modul_active'] = $res['modul_active'];
            $userData[$res['name']]['modul_pos'] = $res['pos'];
        }

        if (isset($userData)) {
            foreach ($userData as $modulName => $value) {
                if (file_exists(SP_CORE_DOC_ROOT . "/module/" . $modulName . "/" . $modulName . "_portal_menue.php")) {
                    require_once (SP_CORE_DOC_ROOT . "/module/" . $modulName . "/" . $modulName . "_portal_menue.php");
                    //retFuncVal kann hier später noch angepasst werden damit positionen angegeben werden können
                    eval("\$retFuncVal = " . $modulName . "_watchModulMenue(\spcore\CTemplate::getInstance(), \spcore\CDatabase::getInstance(), \$this->reqVar);");
                }
            }
        }

        if (isset($retFuncVal) && is_array($retFuncVal)) {
            foreach ($retFuncVal as $index => $value) {
                // generate add menu links
                if ($retFuncVal[$index]['status'] == 0) {
                    $b = sizeof($retFuncVal[$index]['menue']['link']);
                    for ($i = 0; $i <= $b - 1; $i++) {
                        $boxArray['menueAdd'] .= $this->addMenueLink($retFuncVal[$index]['menue']['link'][$i], $retFuncVal[$index]['menue']['name'][$i]);
                    }
                    // generate add Menue Sektion or other
                } elseif ($retFuncVal[$index]['status'] == 1) {
                    $boxArray['menueAdd'] .= $retFuncVal[$index]['other'];
                }
            }
        }

        $this->mainBox .= spcore\CTemplate::getInstance()->parseModulTemplate("portal", $boxArray, $this->tpl_MenueFooter);
    }

    /**
     * \brief unload class
     *
     * @return unknown_type
     */
    function __destruct() {
        if ($this->rssSystem == 1) {
            spcore\CTemplate::getInstance()->addLastOut('<link rel="alternate" type="application/rss+xml" href="' . SP_PORTAL_HTTP_HOST . '/module/portal/portal_rss.php" />');
        }
    }

}

?>
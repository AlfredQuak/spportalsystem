<?php
/* spPortalSystem portal_rss.php
 * Created on 11.06.2009 from misterice
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
require_once '../../config.inc.php';
require_once SP_CORE_DOC_ROOT .'/includes/CLog.php';
require_once SP_CORE_DOC_ROOT .'/includes/CDatabase.php';
require_once SP_CORE_DOC_ROOT .'/module/portal/includes/feedcreator.class.php';

// build RSS Feed	
$rss 			= new UniversalFeedCreator();
$rss->useCached();
$rss->title 		= SP_CORE_TEMPLATE_META_TITEL ;
$rss->description 	= SP_CORE_RSS_DESCRIPTION ;
$rss->link 		= SP_CORE_RSS_LINK ;
$rss->syndicationURL 	= SP_CORE_RSS_SYNDICATION_URL ;

$image 			= new FeedImage();
$image->title 		= SP_CORE_TEMPLATE_META_TITEL ;
$image->url 		= 'http://'. $_SERVER['HTTP_HOST'] . '/'. SP_CORE_TEMPLATE_PATH .'/'. SP_CORE_RSS_IMAGE_URL ;
$image->link 		= 'http://'. $_SERVER['HTTP_HOST'];
$image->description 	= SP_CORE_RSS_IMAGE_DESCRIPTION ;
$rss->image 		= $image;

// load active news for Feed

// delete logged IP older than one day
$sql	= "DELETE FROM `sp_modul_portal_rss_counter_user` WHERE `time` < (NOW() - INTERVAL 1 DAY)";
$result	= spCore\CDatabase::getInstance()->query($sql);

// user iss count ?
$sql    = "SELECT COUNT(*) FROM `sp_modul_portal_rss_counter_user` WHERE `ip` = '".$_SERVER['REMOTE_ADDR']."'";
$result	= spCore\CDatabase::getInstance()->query($sql);
$res 	= spCore\CDatabase::getInstance()->fetch_array($result);

// if not, count+1
if (empty($res['COUNT(*)'])) {
    $sql 	= "INSERT INTO `sp_modul_portal_rss_counter_user` (`ip`, `time`) VALUES ('".$_SERVER['REMOTE_ADDR']."', NOW())";
    $result	= spCore\CDatabase::getInstance()->query($sql);
    $sql 	= "UPDATE `sp_modul_portal_settings` SET `rss_counter` = `rss_counter` +1 WHERE `ID` = 1";
    $result	= spCore\CDatabase::getInstance()->query($sql);
}

$sql 	= "SELECT * FROM sp_modul_portal_cms_news WHERE active=1 ORDER BY news_date DESC LIMIT 0 , 15";
$result	= spCore\CDatabase::getInstance()->query($sql);

if($result != null) {
    while ($res = spCore\CDatabase::getInstance()->fetch_array($result)) {
        //channel items/entries
        $item = new FeedItem();
        $item->title 		= $res['titel_text'];
        $item->link 		= 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],"module")). '?modul=portal&watchnews='.$res['ID'];
        $item->description 	= html_entity_decode($res['page_content']);//substr(html_entity_decode($res['page_content']),0,strpos(html_entity_decode($res['page_content']),".",200))." [-]";
        $item->date 		= $res['news_date'];
        $item->source 		= 'http://' . $_SERVER['HTTP_HOST'];
        $item->author 		= SP_CORE_RSS_AUTHOR ;
        $rss->addItem($item);
    }
}
$rss->outputFeed("RSS2.0"); 
?>

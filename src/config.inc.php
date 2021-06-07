<?php
/* spPortalSystem config.inc.php
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

date_default_timezone_set('Europe/Berlin');

//debug level
define('SP_LOG_NOTHING',   1);
define('SP_LOG_NOTICE',    2);
define('SP_LOG_WARNING',   3);
define('SP_LOG_ERROR',     4);
define('SP_LOG_DEBUG',     5);
define('SP_LOG_OWN',       6);
define('SP_LOG_SQL',   false);

//debug settings
define('SP_CORE_DEBUG',SP_LOG_NOTHING);
define('SP_CORE_LOG_WEB', false);
define('SP_CORE_LOG_FILE', false);
define('SP_CORE_LOG_MYSQL', false);
define('SP_CORE_LOG_WEBSERVICE', false);
define('SP_CORE_WEBSERVICE_URL','http://10.10.144.90/logservice/webservice1.asmx?wsdl');
define('SP_CORE_DEBUG_XHPROF_PATH','/home/dev/xhprof');
define('SP_CORE_DEBUG_LOGFILE_PATH', '/home/dev/mylog.log');

//database settings
define('SP_CORE_DB_SERVER','localhost');
define('SP_CORE_DB_USER','root');
define('SP_CORE_DB_PASS','pass');
define('SP_CORE_DB_DATABASE','spportal');

//system settings
define('SP_PORTAL_DOMAIN_ID',1);
define('SP_CORE_ONLY_WWW',0);
define('SP_CORE_ADMIN_CENTER_HTTPS',0);
define('SP_CORE_HTTP_PROTOKOLL',SP_CORE_ADMIN_CENTER_HTTPS == 1 ? (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off'?'http':'https') : 'http');
define('SP_CORE_IP_CRUNCHER',false);
define('SP_CORE_LANG', 'de');
define('SP_CORE_ENCODING', 'UTF-8');
define('SP_CORE_SUB_DOC_PATH','');
define('SP_CORE_DOC_ROOT',$_SERVER['DOCUMENT_ROOT']. SP_CORE_SUB_DOC_PATH );
define('SP_CORE_TEMPLATE_XML',1);
define('SP_CORE_TEMPLATE_PATH','template/blueSteel/');

// portal Meta Settings
define('SP_CORE_TEMPLATE_META_TITEL','www.sp-portalsystem.com');
define('SP_CORE_TEMPLATE_META_AUTHOR','www.sp-portalsystem.com');
define('SP_PORTAL_META_DESCRIPTION', 'www.sp-portalsystem.com');
define('SP_PORTAL_META_KEYWOERDS','www.sp-portalsystem.com' );
define('SP_PORTAL_META_ROBOTS','index, follow');
define('SP_PORTAL_META_COMPANY','www.sp-portalsystem.com');
define('SP_PORTAL_META_CREATOR','www.sp-portalsystem.com');
define('SP_PORTAL_META_PUBLISCHER','www.sp-portalsystem.com');
define('SP_PORTAL_META_COPYRIGHT','www.sp-portalsystem.com');
define('SP_PORTAL_META_LANGUAGE','de, deutsch, en, englisch');
define('SP_PORTAL_META_CONTENT','de, deutsch, en, englisch');
define('SP_PORTAL_HTTP_HOST',SP_CORE_HTTP_PROTOKOLL."://".$_SERVER['HTTP_HOST']."".SP_CORE_SUB_DOC_PATH);
define('SP_PORTAL_SITEMAP_URL',"http://www.sp-portalsystem.com/sitemap.xml");
define('SP_PORTAL_SYSTEM_URL_REWRITE',true);

//RSS standard
define('SP_CORE_RSS_AUTHOR','www.sp-portalsystem.com');
define('SP_CORE_RSS_DESCRIPTION','www.sp-portalsystem.com');
define('SP_CORE_RSS_LINK','www.sp-portalsystem.com');
define('SP_CORE_RSS_SYNDICATION_URL','www.sp-portalsystem.com');
define('SP_CORE_RSS_IMAGE_URL','images/logo.de');
define('SP_CORE_RSS_IMAGE_DESCRIPTION','www.sp-portalsystem.com');
?>

<?php
/* spPortalSystem portal_install.php
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
function portal_install($db){
	$db->importFile( SP_CORE_DOC_ROOT ."/module/portal/admin/install.sql");
	require_once (SP_CORE_DOC_ROOT ."/module/portal/includes/CLang.php");
	$langArray 	= CLang::getInstance()->getAvaibleLangs();
	CLang::getInstance()->setLanguage($db, $langArray[ SP_CORE_LANG ]['value'], $langArray[ SP_CORE_LANG ]['text']);
}

function portal_uninstall($db){
	$db->importFile( SP_CORE_DOC_ROOT ."/module/portal/admin/uninstall.sql");
}
?>
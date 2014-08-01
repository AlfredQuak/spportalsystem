<?php

/* spPortalSystem portal_index.php
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
/**
 * @ingroup portalsystem
 * @file
 */
require_once 'includes/CPortal.php';

//''''
$sub = explode("/", $_SERVER['REQUEST_URI']);
if (SP_PORTAL_SYSTEM_URL_REWRITE === true) {
    foreach ($sub as $index => $value) {
        if ($value == "p") {
            $page = explode("_", $sub[$index + 1]);
            $requestVar['page'] = $page[0];
            $requestVar['sub'] = (isset($page[1]) ? $page[1] : "");
        } elseif ($value == "pext") {
            // /pext/pmanage_16_Multireplacer.html
            $page = explode("_", $sub[$index + 1]);
            $requestVar['action'] = "extmodul";
            $requestVar['mod'] = $page[0];
        }
    }
} else {
    unset($sub);
}
//''''

$portal = new CPortal($session, spcore\CHelper::stripRequestArray($requestVar));
if (isset($requestVar['action']) && $requestVar['action'] == 'extmodul') {
    $portal->load_extmodul();
} else {
    $portal->main(isset($requestVar['page']) ? spcore\CHelper::stripRequestVar($requestVar['page']) : false);
}
?>
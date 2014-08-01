<?php
/* spPortalSystem admin_permissions.php
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
class admin_permission extends spcore\CPermissions {
	
	/**
	 * \brief 
	 * Give back complete permissions
	 * \details
	 * Main function called about CAdminCenter::admin_admin_groupAdd() <br>
	 * Here we give back all modulerigths for configure, only display.
	 * @return $permissionarray
	 */
	public function permission_getConfigurePermissions(){
		return parent::loadFromDatabase("admin");
	}
	
	public static function permission_getShortDescription(){
		return "Admincenter";
	}

        public function test(){
            die("test");
        }
}
?>
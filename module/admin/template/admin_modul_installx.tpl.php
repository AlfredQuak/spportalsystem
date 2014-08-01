<?xml version="1.0" encoding="UTF-8"?>
<admin>
	<main><![CDATA[
		<FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_mInstall" method="post" encType="multipart/form-data">
		<table>
			<tr>
				<!--<th width="1%"><img src="{LANG_admin_imgStartModul}" border="0"></th>-->
				<th width="1%"><img src="{LANG_admin_imgModulSetActiv}" alt="{LANG_admin_modulActiv}" border="0"></th>
				<th width="1%"><img src="{LANG_admin_imgModulInstall}" alt="{LANG_admin_modulInstall}" border="0"></th>
				<th width="98%">{LANG_admin_modulName}</th>
			</tr>
			{admin_dir}
			<tr>
				<!--<td> &nbsp;</span></td>-->
				<td> &nbsp;</td>
				<td> &nbsp;</td>
				<td> &nbsp;</td>
			</tr>
		</table>
		</FORM>
	]]></main>
	<content><![CDATA[
		<tr>
		  <!--<td><a href="#"> <img src="./module/admin/template/images/iconset/plugin_disabled.png" alt="install" border="0"></a></td>-->
		  <td> {admin_activate}</td>
		  <td> {admin_install}</td>
		  <td> {admin_name}</td>
		</tr>
	]]></content>
	<contentLinkModulInstall><![CDATA[
		<img src="{PORTAL_HTTP_HOST}/module/admin/template/images/iconset/database_add.png" alt="install" border="0"></a>
	]]></contentLinkModulInstall>
	<contentLinkModulInstallInactiv><![CDATA[
		<img src="{PORTAL_HTTP_HOST}/module/admin/template/images/iconset/plugin_disabled.png" alt="install" border="0">
	]]></contentLinkModulInstallInactiv>
	<contentLinkModulUninstall><![CDATA[
		<img src="{PORTAL_HTTP_HOST}/module/admin/template/images/iconset/database_connect.png" alt="deinstall" border="0"></a>
	]]></contentLinkModulUninstall>
	<contentLinkModulActiv><![CDATA[
		<img src="{PORTAL_HTTP_HOST}/module/admin/template/images/iconset/plugin_add.png"  border="0"></a>
	]]></contentLinkModulActiv>
	<contentLinkModulInactiv><![CDATA[
		<img src="{PORTAL_HTTP_HOST}/module/admin/template/images/iconset/plugin_go.png"  border="0"></a>
	]]></contentLinkModulInactiv>
</admin>
<FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_mActivate" method="post" encType="multipart/form-data">
<table>
	<tr>
		<th width="1%"><img src="{LANG_admin_imgActiv}" alt="{LANG_admin_modulActiv}" border="0"></th>
		<th width="1%"><img src="{LANG_admin_imgInactiv}" alt="{LANG_admin_modulInactiv}" border="0"></th>
		<th width="98%">{LANG_admin_modulName}</th>
	</tr>
	{admin_dir}
	<tr>
		<td ><span class="genmed"> &nbsp;</span></td>
		<td ><span class="genmed"> &nbsp;</span></td>
		<td class="row1"><span class="genmed">
		<div><input type="submit" value="{LANG_admin_buttonSave}"></div>
		</span></td>
	</tr>
</table>
</FORM>

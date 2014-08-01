<?xml version="1.0" encoding="utf-8" ?>
<useradd>
<mainform>
<![CDATA[
<form name="form1" method="post" action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_userAddData">
    <table width="100%" border="0">
        <tr>
            <th width="5%"><nobr>Neuen User Anlegen</nobr></th>
        <th></th>
        </tr>
        <tr>
            <td width="106">Name</td>
            <td width="207">
                <label>
                    <input type="text" name="per_lastname" id="per_lastname">
                </label>
            </td>
        </tr>
        <tr>
            <td>Vorname</td>
            <td>
                <label>
                    <input type="text" name="per_firstname" id="per_firstname">
                </label>
            </td>
        </tr>
        <tr>
            <td>Username*</td>
            <td>
                <label>
                    <input type="text" name="per_username" id="per_username">
                </label>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>E-Mail*</td>
            <td>
                <label>
                    <input type="text" name="per_email" id="per_email">
                </label>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Passwort*</td>
            <td>
                <label>
                    <input type="password" name="per_password" id="per_password">
                </label>
            </td>
        </tr>
        <tr>
            <td>Passwort*</td>
            <td>
                <label>
                    <input type="password" name="per_passwordcheck" id="per_passwordcheck">
                </label>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Gruppe</td>
            <td>
                <select name="per_group[]" id="per_group" size="5" multiple="multiple">
		    	{admin_option}
                </select>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <label>
                    <div align="right">
                        <input type="submit" name="button" id="button" value="Senden">
                    </div>
                </label>
            </td>
        </tr>
    </table>
</form>
	]]>
</mainform>
<snippedGroups>
<![CDATA[
		<option value="{admin_value}">{admin_groupname}</option>
	]]>
</snippedGroups>
</useradd>

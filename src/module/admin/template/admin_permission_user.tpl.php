<?xml version="1.0" encoding="utf-8" ?>
<user>
<maintable>
<![CDATA[
<table width="100%" border="1">
    <tr>
        <td>{LANG_admin_perUserEnable}</td>
        <td>{LANG_admin_perFullName}</td>
        <td>{LANG_admin_perUserName}</td>
        <td>{LANG_admin_perUserGroup}</td>
        <td>&nbsp;</td>
    </tr>
	 {admin_table}
</table>
	]]>
</maintable>
<tablesnipped>
<![CDATA[
<tr>
    <td>
        <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_activateUser&userid={admin_snippedID}" >
            <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/{admin_snippedActiv}"  border="0">
        </a>
    </td>
    <td>
        <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_getUserData&userid={admin_snippedID}" >{admin_snippedFirstname} {admin_snippedLastname}</a>
    </td>
    <td>{admin_snippedUsername}</td>
    <td>{admin_snippedUsergroups}</td>
    <td>
        <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_delUserData&userid={admin_snippedID}">
            <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Erase.png" border="0">
        </a>
    </td>
</tr>
	]]>
</tablesnipped>
<userEdit>
<![CDATA[
<form action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_userDataUpdate" method="post">
    <input type="hidden" name="userid" value="{admin_userid}">
    <fieldset>
        <legend> User Name </legend>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td >Vorname</td>
            </tr>
            <tr>
                <td width="1%"><input type="text" name="firstname" id="firstname" value="{admin_firstname}"></td>
            </tr>
            <tr>
                <td>Nachname</td>
            </tr>
            <tr>
                <td><input type="text" name="lastname" id="lastname" value="{admin_lastname}"></td>
            </tr>
            <tr>
                <td>Username</td>
            </tr>
            <tr>
                <td><input type="text" name="username" id="username" value={admin_username}></td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend> Kennwort &auml;ndern (Optional) </legend>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>Neues Kennwort:</td>
            </tr>
            <tr>
                <td width="1%"><input type="password" name="newpass1" id="newpass1"></td>
            </tr>
            <tr>
            </tr>
            <td>Neues Kennwort erneut eingeben:</td>
            <tr>
                <td><input type="password" name="newpass2" id="newpass2"></td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend> E-Mail-Adresse &auml;ndern (Optional) </legend>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>Neue E-Mail-Adresse:</td>
            </tr>
            <tr>
                <td width="1%"><input type="text" name="newemail1" id="newemail1" value="{admin_email}"></td>
            </tr>
            <tr>
            </tr>
            <td>Neue E-Mail-Adresse erneut eingeben:</td>
            <tr>
                <td><input type="text" name="newemail2" id="newemail2" value="{admin_email}"></td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend> Aktive Gruppen </legend>
        <select name="group[]" size="10" multiple>
			{admin_groups}
        </select>

    </fieldset>
    <br>
    <input type="submit" value=" Absenden ">
    <input type="reset" value=" Abbrechen">

</form>
	]]>
</userEdit>
</user>



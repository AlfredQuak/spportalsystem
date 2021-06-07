<FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_changePW" method="post" encType="multipart/form-data">
    <table border="0" cellpadding="3" cellspacing="1" width="100%">
        <tbody>
            <tr>
                <th id="adminConHeader">Neues Administrator Passwort</th>
            </tr>
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td width="15%"><nobr>Admin Passwort alt</nobr></td>
                <td><input name=oldPW type=password></td>
            </tr>
            <tr>
                <td width="15%"><nobr>Admin Passwort neu</nobr></td>
        <td><input name=newPW type=password></td>
        </tr>
        <tr>
            <td></td>
            <td><input class="button" type="submit" value="speichern"></td>
        </tr>
    </table>
</td>
</tr>
</tbody>
</table>
</FORM>
<FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_changePW" method="post" encType="multipart/form-data">
    <table border="0" cellpadding="3" cellspacing="1" width="100%">
        <tbody>
            <tr>
                <th>Datenbank Settings</th>
            </tr>
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td width="15%"><nobr>Datenbank Server</nobr></td>
                <td><input name=name value="{admin_databaseServer}"></td>
            </tr>
            <tr>
                <td width="15%"><nobr>Datenbank Name</nobr></td>
        <td><input name=name value="{admin_databaseName}"></td>
        </tr>
        <tr>
            <td width="15%"><nobr>Datenbank User</nobr></td>
        <td><input name=name value="{admin_databaseUser}"></td>
        </tr>
        <tr>
            <td width="15%"><nobr>Datenbank Passwort</nobr></td>
        <td><input name=name value="{admin_databasePass}"></td>
        </tr>
    </table>
</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="3" cellspacing="1" width="100%">
    <tbody>
        <tr>
            <th>Globale Settings</th>
        </tr>
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td width="15%"><nobr>Debug Mode</nobr></td>
            <td><input name=name value="{admin_debugMode}"></td>
        </tr>
        <tr>
            <td width="15%"><nobr>Template</nobr></td>
    <td><input name=name value="{admin_templatePath}"></td>
    </tr>
</table>
</td>
</tr>
</tbody>
</table>
</FORM>
<FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_bindToIp" method="post" encType="multipart/form-data">
    <table border="0" cellpadding="3" cellspacing="1" width="100%">
        <tr>
            <th>Admincenter auf IP binden</th>
        </tr>
        <tr>
            <td>
                <input name="ip1" value="{admin_ip1}" size="3">.
                <input name="ip2" value="{admin_ip2}" size="3">.
                <input name="ip3" value="{admin_ip3}" size="3">.
                <input name="ip4" value="{admin_ip4}" size="3">
                <input class="button" type="submit" value="speichern">
            </td>
        </tr>
    </table>
</FORM>
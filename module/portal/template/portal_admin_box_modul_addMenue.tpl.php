<FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_menueModulSettings" method="post" encType="multipart/form-data">
    <table>
        <tr>
            <th width="1%"></th>
            <th width="1%"><img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/topic_unlock.gif" alt="Aktivieren" border="0"></th>
            <th width="1%"><img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/topic_lock.gif" alt="Deaktivieren" border="0"></th>
            <th width="97%">Modul zum Menue hinzufuegen ( Menue Funktion wird verwendet ! )</th>
        </tr>
        {portal_dir}
        <tr>
            <td class="row1"><span class="genmed">    &nbsp;</span>
            </td>
            <td class="row1"><span class="genmed">    &nbsp;</span>
            </td>
            <td class="row1"><span class="genmed">    &nbsp;</span>
            </td>
            <td class="row1">
                <span class="genmed"><div>
                        <input type="button" value="Zur&uuml;ck" onClick="window.location='?modul=admin&action=portal_CMSWatchAllSides'">
                        <input class="button" type="submit" value="speichern" ></div></span>
            </td>
        </tr>
    </table>
</FORM>

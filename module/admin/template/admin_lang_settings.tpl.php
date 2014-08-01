<?xml version = "1.0" encoding = "utf-8" ?> 
<admincenter>
    <langOptions><![CDATA[
        <option {admin_select} value="{admin_value}" style="background-image: url({PORTAL_TEMPLATE_PATH}images/flagicons/{admin_value}.png);
            background-repeat: no-repeat;
            text-align:right;
            left-padding:20px;" >{admin_text}
    </option>
    ]]></langOptions>
<installedLangs><![CDATA[
    <tr>
        <td class="row1" width="1%"><img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}images/flagicons/{admin_langTag}.png" alt="{admin_langTag}"></td>
        <td class="row1">{admin_langCountry}</td>
        <td class="row1" width="1%"><img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}images/flagicons/{admin_langTag}.png" alt="{admin_langTag}"></td>
        <td class="row1" width="1%"><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_delLang&langTag={admin_langTag}"><img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Cancel.png" alt="{admin_langTag}" height="11px"></a></td>
    </tr>
    ]]></installedLangs>
<mainForm><![CDATA[
    <FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_setLang" method=post encType=multipart/form-data>
          <table class="forumline" border="0" cellpadding="3" cellspacing="1" width="100%">
            <tbody><tr>
                    <th>Neue Sprache verfuegbar manchen</th>
                </tr>
                <tr>
                    <td class="row2">
                        <table width="100%">
                            <tr>
                                <td class="row1" width="15%">
                                    <span class="genmed"><nobr>Neue Sprache
                                            <select name="lang">
                                                {admin_langOptions}
                                            </select>
                                        </nobr></span>
                                </td>
                                <td class="row1"><input class=button type=submit value="speichern"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody></table>
    </FORM> 
    <FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_changePW" method=post encType=multipart/form-data>
          <table class="forumline" border="0" cellpadding="3" cellspacing="1" width="100%">
            <tbody><tr>
                    <th>Installierte Sprachen</th>
                </tr>
                <tr>
                    <td class="row2">
                        <table width="100%">
                            {admin_installedLangs}
                        </table>
                    </td>
                </tr>
            </tbody></table>
    </FORM> 
    ]]></mainForm>
</admincenter>
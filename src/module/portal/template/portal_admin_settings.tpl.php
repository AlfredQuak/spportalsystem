<?xml version="1.0" encoding="utf-8" ?> 
<admincenter>
    <mainForm><![CDATA[
        <table width="100%">
            <tr>
                <th class="row1"><span class="genmed"> {LANG_portal_mainsettings}</span></th>
            </tr>
            <tr>
                <td class="row1"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="5%"><nobr><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_rssActive"><img src="{PORTAL_HTTP_HOST}/{portal_imgRSS}" /></a> ( {portal_rssCount} )</nobr></td>
                            <td width="95%"><nobr>{LANG_portal_rssActive}</nobr></td>
                        </tr>
                        <tr>
                            <td><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_newsActive"><img src="{PORTAL_HTTP_HOST}/{portal_imgNews}" /></a></td>
                            <td>{LANG_portal_newsActive}</td>
                        </tr>
                        <tr>
                            <td><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_domainActive"><img src="{portal_imgDomain}" /></a></td>
                            <td>{LANG_portal_domainActive}</td>
                        </tr>
                        <tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table></td>
            </tr>
        </table>
        {portal_langSettings}
	]]></mainForm>
    <langOptions><![CDATA[
        <option {portal_select} value="{portal_value}" style="background-image: url({PORTAL_HTTP_HOST}/module/portal/template/images/flagicons/{portal_value}.png);
			background-repeat: no-repeat;
			text-align:right;
			left-padding:20px;" >{portal_text}
    </option>
	]]></langOptions>
<installedLangs><![CDATA[
    <tr>
        <td class="row1" width="1%"><img src="{PORTAL_HTTP_HOST}/module/portal/template/images/flagicons/{portal_langTag}.png" alt="{portal_langTag}"></td>
        <td class="row1"> {portal_langCountry}</td>
        <td class="row1" width="1%"><img src="{PORTAL_HTTP_HOST}/module/portal/template/images/flagicons/{portal_langTag}.png" alt="{portal_langTag}"></td>
        <td class="row1" width="1%"><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_delLang&langTag={portal_langId}"><img src="{PORTAL_HTTP_HOST}{PORTAL_TEMPLATE_PATH}/images/iconset/Cancel.png" alt="{portal_langTag}" height="11px"></a></td>
    </tr>
	]]></installedLangs>
<langMainForm><![CDATA[
    <FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_setLang" method="post" encType="multipart/form-data">
        <table class="forumline" border="0" cellpadding="3" cellspacing="1" width="100%">
            <tbody>
                <tr>
                    <th>Neue Sprache verfuegbar manchen</th>
                </tr>
                <tr>
                    <td class="row2">
                        <table width="100%">
                            <tr>
                                <td class="row1" width="15%">
                                    <span class="genmed"><nobr>Neue Sprache
                                            <select name="lang">
								{portal_langOptions}
                                            </select>
                                        </nobr>
                                    </span>
                                </td>
                                <td class="row1"><input class="button" type="submit" value="speichern"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </FORM>
    <FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_changePW" method="post" encType="multipart/form-data">
        <table class="forumline" border="0" cellpadding="3" cellspacing="1" width="100%">
            <tbody>
                <tr>
                    <th>Installierte Sprachen</th>
                </tr>
                <tr>
                    <td class="row2">
                        <table width="100%">
						  {portal_installedLangs}
                        </table>
                    </td>
                </tr>
                <tr><td>&nbsp;</td></tr>
            </tbody>
        </table>
    </FORM>
	]]></langMainForm>
</admincenter>
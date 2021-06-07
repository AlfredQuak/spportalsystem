<?xml version="1.0" encoding="utf-8" ?> 
<grouplist>
	<mainform><![CDATA[
            <table width="100%">
				{admin_content}
            </table>
	]]></mainform>
	<snipped><![CDATA[
            <tr>
                <td >
                    <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_groupEdit&id={admin_grpID}">{admin_grpName}</a>
                </td>
                <td width="1%">
                    <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_groupEdit&id={admin_grpID}">
                        <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Modify.png" border="0">
                    </a>
                </td>
                <td width="1%">
                    <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_delGrp&grpID={admin_grpID}">
                        <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Erase.png" border="0">
                    </a>
                </td>
            </tr>
	]]></snipped>
</grouplist>
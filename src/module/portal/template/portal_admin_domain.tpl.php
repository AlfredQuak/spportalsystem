<?xml version="1.0" encoding="UTF-8"?>
<main>
    <domainAdd><![CDATA[
        <form action="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_domainAddData" method="post">
            <table width="100%" border="0" cellspacing="10" cellpadding="20">
                <tr>
                    <th width="200px">Domain</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td VALIGN=TOP><input type="text" name="domain" value="" /></td>
                    <td><textarea name="domain_description" cols="100" rows="8"/></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" /></td>
                </tr>
            </table>
        </form>
        ]]></domainAdd>
    <domainDelete>
        test
    </domainDelete>
    <domainEdit>
        test
    </domainEdit>
    <domainList><![CDATA[
        <form action="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_domainDomainAddLanguage" method="post">
            <table width="100%" border="0" cellspacing="10" cellpadding="20">
                <tr>
                    <th width="50px">ID</th>
                    <th width="200px">Domain</th>
                    <th>Description</th>
                    <th></th>
                    <th width="25px"></th>
                    <th></th>
                    <th></th>
                </tr>
                {portal_domainListTableData}
            </table>
            <br>
            <div align="right">
                <input type="submit" />
            </div>
        </form>
    ]]></domainList>
    <domainListTableData><![CDATA[
        <tr>
            <td>{portal_onOffImage}</td>
            <td>{portal_domainName}</td>
            <td>{portal_domainDescription}</td>
            <td>{portal_domainlang}</td>
            <td></td>
            <td width="16px">
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_domainDel&delDomainID={portal_ID}">
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Cancel.png" alt="delete" border="0" title="{portal_domainName}">
                </a>
            </td>
            <td width="16px"><img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Modify.png" alt="edit" border="0" title="{portal_domainName}"></td>
        </tr>
    ]]></domainListTableData>
</main>



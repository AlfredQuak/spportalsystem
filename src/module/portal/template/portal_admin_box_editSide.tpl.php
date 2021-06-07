<?xml version="1.0" encoding="utf-8" ?> 
<editSide>
    <mainform><![CDATA[
        <body onload="init()">
            <script type="text/javascript" language="JavaScript1.2" >
                function cms_myBoxHide(id,count){
                    for(i=0;i<=(count-1);i++){
                        if(document.getElementById("sub_"+id+"_"+i).style.display == 'none'){
                            document.getElementById("sub_"+id+"_"+i).style.display = '';
                        }else{
                            document.getElementById("sub_"+id+"_"+i).style.display = 'none';
                        }
                    }
                }
            </script>
            <ul id="tabs">
                {portal_tabTabbing}
            </ul>
            <FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSUpdatePos" method="post" encType="multipart/form-data">
                {portal_langTabs}
                <div align="right">
                    <span class="genmed"><input class="button" type="submit" value="{LANG_portal_buttonSaveSettings}" ></span>
                </div>
            </FORM>
	]]></mainform>
    <langTabHead><![CDATA[
        <tr>
            <th width="1%"></th>
            <th width="1%">Pos</th>
            <th width="1%">Aktiv</th>
            <th width="1%">Unterseiten</th>
            <th width="1%">Box</th>
            <th width="1%">Linkname</th>
            <th >Seiten Name</th>
            <th width="1%"></th>
            <th width="1%"></th>
            <th width="1%"></th>
            <th width="1%"></th>
            <th width="1%"></th>
        </tr>
	]]></langTabHead>
    <langTab><![CDATA[
        <div class="tabContent" id="{portal_tabId}">
            <table width="100%">
                {portal_sides}
            </table>
        </div>
	]]></langTab>
    <langTabContent><![CDATA[
        <tr id="main_{portal_jsId}" >		<!-- pos value -->
            <td class="row1">				<!-- side active -->
                <img src="{PORTAL_HTTP_HOST}/module/portal/template/images/flagicons/{portal_langTag}.png" border="0">
            </td>
            <td class="row1">
                <span class="genmed"><div><!--<nobr>
                        <img src="module/admin/template/images/iconset/Up.png" border="0">
                        <img src="module/admin/template/images/iconset/Down.png" border="0"></nobr>-->
                        <input class=test value="{portal_posValue}" size=2 name="portalPos_{portal_pageid}">
                        </div></span>
            </td>
            <td class="row1">				<!-- side active -->
                <img onclick="activateMainPage('{portal_posId}','{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}')"
                     id="a_img_{portal_active}_{portal_posId}"
                     src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/{portal_active}.png"
                     alt="Status" border="0">
            </td>
            <td class="row1">				<!-- open undersides -->
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Folder.png"
                         alt="edit" border="0">[ {portal_sumUndersides} ]
                    <a href="#" onclick="cms_myBoxHide({portal_jsId},{portal_sumUndersides})">+</a>
            </td>
            <td class="row1">				<!-- selectbox Boxes -->
                <select name="artikel_{portal_pageid}" size="1">{portal_boxName}</select>
            </td>
            <td class="row1">				<!-- textfeld side linkname -->
                <input class=test value="{portal_sideName}" name="portalLinkName_{portal_pageid}">
            </td>
            <td class="row1">				<!-- side name &edit link -->
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_pageid}">{portal_sideTitle}</a>
            </td>
            <td class="row1">				<!-- verknuepfung -->
                <span class="genmed"><div><!--<a href="?modul=admin&action=portal_CMSsideAssignment&id={portal_pageid}" ><img src="{PORTAL_TEMPLATE_PATH}/images/iconset/Anchor.png" alt="delete" border="0"></a>--></div></span>
            </td>
            <td class="row1">				<!-- domain -->
                {portal_domain}
            </td>
            <td class="row1">				<!-- verknuepfung -->
                <div><!--<a href="?modul=admin&action=portal_CMSsideAssignment&id={portal_pageid}" >
                     <img src="{PORTAL_TEMPLATE_PATH}/images/iconset/Anchor.png" alt="delete" border="0"></a>--></div>
            </td>
            <td class="row1">				<!-- delete icon -->
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSDeletePage&doit=deleteMain&id={portal_pageid}"
                   onclick="return confirm('{LANG_portal_confirmDelSide}')">
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Cancel.png" alt="delete" border="0">
                </a>
            </td>
            <td class="row1">				<!-- edit icon -->
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_pageid}">
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Modify.png" alt="edit" border="0">
                </a>
            </td>
        </tr>
	]]></langTabContent>
    <langTabContentSub><![CDATA[
        <!- ## subside ->
        <tr id="sub_{portal_jsId}_{portal_trId}" style="display:none">
            <td class="row1"><div></div></td>
            <td class="row1">
                <input class=test value="{portal_posValue}" size=2 name="portalSubLinkPos_{portal_pageid}">
            </td>
            <td class="row1">
                <img id="a_sub_img_{portal_active}_{portal_posId}"
                     onclick="activateSubPage('{portal_posId}','{PORTAL_TEMPLATE_PATH}',3)"
                     src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/{portal_active}.png"
                     alt="Status" border="0">
            </td>
            <td class="row1"><input type="hidden" name="subside" value="{portal_box}" ></td>
            <td class="row1"><div></div></td>
            <td class="row1"><input class=test value="{portal_sideName}" name="portalSubLinkName_{portal_pageid}"></td>
            <td class="row1">
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_pageid}">{portal_sideTitle}</a>
            </td>
            <td class="row1"><div>{portal_domain}</div></td>
            <td class="row1">
                <div>
                    <!--<a href="?modul=admin&action=portal_CMSsideAssignment&id={portal_pageid}" >
                    <img src="{PORTAL_TEMPLATE_PATH}/images/iconset/Anchor.png" alt="delete" border="0"></a>-->
                </div>
            </td>
            <td class="row1">
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSDeletePage&doit=deleteSub&id={portal_pageid}"
                   onclick="return confirm('{LANG_portal_confirmDelSide}')">
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Cancel.png" alt="delete" border="0">
                </a>
            </td>
            <td class="row1">
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_pageid}">
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Modify.png" alt="edit" border="0">
                </a>
            </td>
        </tr>
        <!- ###################### ->
	]]></langTabContentSub>
    <side_assignment><![CDATA[
        <FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSsideAssignmentToDB" method="post" encType="multipart/form-data">
            <input type="hidden" name="sites[]" value="{portal_selectedSite}">
            <input type="hidden" name="editSite" value="{portal_editside}">

            <table width="100%">
                <tr>
                    <th width="1%"><span class="genmed">Lang</span></th>
                    <th>&#160;</th>
                    <th><span class="genmed">Ausgewaehlte Seite</span></th>
                </tr>
                <tr>
                    <td class="row1"><img src="{PORTAL_HTTP_HOST}/module/portal/template/images/flagicons/{portal_flag}.png" border="0"></td>
                    <td></td>
                    <td class="row1">{portal_sidename}</td>
                </tr>
                <tr>
                    <th width="1%"><span class="genmed">Lang</span></th>
                    <th></th>
                    <th><span class="genmed">Verknuepfte Seiten</span></th>
                </tr>
                {portal_sideAssi}
                <tr>
                    <th width="1%">&#160;</th>
                    <th></th>
                    <th>&#160;</th>
                </tr>
            </table>
            <input type="button" value="Zur&uuml;ck" onClick="window.location='?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_selectedSite}'">
            <input class="button" type="submit" value="Einstellungen Sichern" >
        </FORM>
        ]]></side_assignment>
    <side_assignmentData><![CDATA[
        <tr>
            <td class="row1"><img src="{PORTAL_HTTP_HOST}/module/portal/template/images/flagicons/{portal_flag}.png" border="0"></td>
            <td></td>
            <td class="row1"><select name="sites[]">{portal_sideOption}</select></td>
        </tr>
        ]]></side_assignmentData>
</editSide>
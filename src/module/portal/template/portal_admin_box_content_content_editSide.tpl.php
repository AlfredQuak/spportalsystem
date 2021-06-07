<tr id="main_{portal_jsId}" >		<!-- pos value -->
    <td class="row1">				<!-- side active -->
        <span class="genmed">
            <div>
                <img src="{PORTAL_HTTP_HOST}/module/portal/template/images/flagicons/{portal_langTag}.png" border="0">
            </div>
        </span>
    </td>
    <td class="row1">
        <span class="genmed">
            <div>
                <input class=test value="{portal_posValue}" size=2 name="portalPos_{portal_pageid}">
            </div>
        </span>
    </td>
    <td class="row1">				<!-- side active -->
        <span class="genmed">
            <div>
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSActivate&doit=activeMain&id={portal_posId}">
                    <img src="{PORTAL_TEMPLATE_PATH}/images/{portal_active}" alt="Status" border="0">
                </a>
            </div>
        </span>
    </td>
    <td class="row1">				<!-- open undersides -->
        <span class="genmed">
            <div>
                [ {portal_sumUndersides} ]  <a href="#" onclick="cms_myBoxHide({portal_jsId},{portal_sumUndersides})">+</a> 
            </div>
        </span>
    </td>
    <td class="row1">				<!-- selectbox Boxes -->
        <span class="genmed">
            <div>
                <select name="artikel_{portal_pageid}" size="1">{portal_boxName}</select>
            </div>
        </span>
    </td>
    <td class="row1">				<!-- textfeld side linkname -->
        <span class="genmed">
            <div>
                <input class=test value="{portal_sideName}" name="portalLinkName_{portal_pageid}">
            </div>
        </span>
    </td>
    <td class="row1">				<!-- side name &edit link -->
        <span class="genmed">
            <div>
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_pageid}">{portal_sideTitle}</a>
            </div>
        </span>
    </td>
    <td class="row1">				<!-- delete icon -->
        <span class="genmed">
            <div>
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSDeletePage&doit=deleteMain&id={portal_pageid}" onclick="return confirm('{LANG_portal_confirmDelSide}')">
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/topic_delete.gif" alt="delete" border="0">
                </a>
            </div>
        </span>
    </td>    
    <td class="row1">				<!-- edit icon -->
        <span class="genmed">
            <div>
                <a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_pageid}">
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/icon_mini_members.gif" alt="edit" border="0">
                </a>
            </div>
        </span>
    </td>
</tr>
 <!- ## subside ->
  <tr id="sub_{portal_jsId}_{portal_trId}" style="display:none">
    <td class="row1"><span class="genmed"><div><input class=test value="{portal_posValue}" size=2 name="portalSubLinkPos_{portal_pageid}"></div></span></td>
    <td class="row1"><span class="genmed"><div><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSActivate&doit={portal_setActive}&id={portal_posId}"><img src="{PORTAL_TEMPLATE_PATH}/images/{portal_active}" alt="Status" border="0"></a></div></span></td>
    <td class="row1"><span class="genmed"><div><input type="hidden" name="subside" value="{portal_box}" ></div></span></td>
    <td class="row1"><span class="genmed"><div></span></td>
    <td class="row1"><span class="genmed"><div><input class=test value="{portal_sideName}" name="portalSubLinkName_{portal_pageid}"></div></span></td>
    <td class="row1"><span class="genmed"><div><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_pageid}">{portal_sideTitle}</a></div></span></td>
    <td class="row1"><span class="genmed"><div><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSDeletePage&doit=deleteSub&id={portal_pageid}" onclick="return confirm('{LANG_portal_confirmDelSide}')"><img src="{PORTAL_TEMPLATE_PATH}/images/topic_delete.gif" alt="delete" border="0"></a></div></span></td>
    <td class="row1"><span class="genmed"><div><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSEditSide&doit=editPageWatch&id={portal_pageid}"><img src="{PORTAL_TEMPLATE_PATH}/images/icon_mini_members.gif" alt="edit" border="0"></a></div></span></td>
  </tr>
 <!- ###################### ->

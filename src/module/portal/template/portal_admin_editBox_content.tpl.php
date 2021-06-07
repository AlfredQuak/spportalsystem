<script type="text/javascript">
    tinyMCE.init({
        // General options
        mode : "exact",
        elements : "elm2",
        theme : "advanced",
        skin : "o2k7",
        plugins : "filemanager,phpimage,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,filemanager,link,unlink,anchor,phpimage,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        theme_advanced_disable: "image,advimage",
        forced_root_block : "",

        // Example content CSS (should be your site CSS)
        content_css : "css/content.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "lists/template_list.js",
        external_link_list_url : "lists/link_list.js",
        external_image_list_url : "lists/image_list.js",
        media_external_list_url : "lists/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
            username : "Some User",
            staffid : "991234"
        }
    });
</script>

<FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_updateBoxContent" method="post" encType="multipart/form-data">
    <input name="box_id" type="hidden" value="{portal_boxId}" />
    <table width="100%">
        <tr>
            <th ><span class="genmed">
                    <div>{LANG_portal_adminBoxTitel}</div>
                </span></th>
        </tr>
        <tr>
            <td>
                <table width="100%">
                    <tr >
                        <td valign="top" >
                            <span class="genmed">
                                <div allign="top"><input name=box_titel value="{portal_valueBoxTitel}" size=“32“></div>
                            </span>
                        </td>
                        <td >

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <th><span class="genmed">
                    <div>{LANG_portal_adminBoxContent}</div>
                </span></th>
        </tr>
        <tr>
            <td valign="top"><span class="genmed">
                    <div>
                        <textarea id="elm2" name="box_content" rows="30" cols="80" style="width: 50%">
    {portal_valueBoxContent}
                        </textarea></div>
                </span></td>
        </tr>
        <tr>
            <td><span class="genmed"><div></div></span></td>
        </tr>
        <tr>
            <td><span class="genmed"><div></div></span></td>
        </tr>
        <tr>
            <td class="row1"><span class="genmed">
                    <input type="button" value="Zur&uuml;ck" onClick="window.location='?modul=admin&action=portal_CMSWatchAllSides'">
                    <input class="button" type="submit" value="{LANG_portal_buttonNewSideSave}" ></span></td>
        </tr>
    </table>
</FORM>
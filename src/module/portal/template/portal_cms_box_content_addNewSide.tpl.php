<?xml version="1.0" encoding="utf-8" ?> 
<addNewSide>
    <mainform><![CDATA[
        <body onload="init()">
            <FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSNewSide" method="post" encType="multipart/form-data">
                <input type="hidden" name=side_step value="1" >
                <ul id="tabs">
  		{portal_langTab}
                </ul>
  		{portal_langTabContent}
                <input class="button" type="submit" value="{LANG_portal_buttonNewSideSave}" >
            </FORM>
	]]></mainform>
    <langTab><![CDATA[
        <li><a href="#{portal_tabLangID}">
                <img src="module/portal/template/images/flagicons/{portal_tabLangID}.png"> {portal_tabLanguage}
            </a>
        </li>
	]]></langTab>
    <langTabContent><![CDATA[
        <script type="text/javascript">
            tinyMCE.init({
                // General options
                mode : "exact",
                elements : "content_{portal_tabLangID}",
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

        <div class="tabContent" id="{portal_tabLangID}">
            <table width="100%">
                <tr>
                    <th ><span class="genmed"><div>{LANG_portal_sideName}</div></span></th>
                </tr>
                <tr>
                    <td>
                        <table width="100%">
                            <tr >
                                <td valign="top" >
                                    <span class="genmed">
                                        <div allign="top"><input name="side_titel_{portal_tabLangID}" value="" size="32"></div>
                                    </span>
                                </td>
                                <td >
                                    <div align="right">
                                        <nobr>
                                                <!--<input type="checkbox" name="side_action" value="1">--> {LANG_portal_underSide}
                                            <select name="side_subside_{portal_tabLangID}">{portal_underside}</select>
                                        </nobr>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>
                        <span class="genmed">
                            <div>{LANG_portal_sideContent}</div>
                        </span>
                    </th>
                </tr>
                <tr>
                    <td valign="top">
                        <span class="genmed">
                            <div>
                                <textarea id="content_{portal_tabLangID}" name="side_content_{portal_tabLangID}" rows="30" cols="80" style="width: 100%">
                                </textarea>
                            </div>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><span class="genmed"><div></div></span></td>
                </tr>
                <tr>
                    <td><span class="genmed"><div></div></span></td>
                </tr>
            </table>
        </div>
	]]></langTabContent>
</addNewSide>
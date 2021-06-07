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
          <table width="100%">
            <tr>
                <th width="1%"><span class="genmed"></span></th>
                <th width="1%"><span class="genmed">Pos</span></th>
                <th width="1%"><span class="genmed">Aktiv</span></th>
                <th width="1%"><span class="genmed">Unterseiten</span></th>
                <th width="1%"><span class="genmed">Box</span></th>
                <th width="1%"><span class="genmed">Linkname</span></th>
                <th ><span class="genmed">Seiten Name</span></th>
                <th width="1%"><span class="genmed"></span></th>
                <th width="1%"><span class="genmed"></span></th>
            </tr>
            {portal_sides}
        </table>
        <div align="right">
            <span class="genmed"><input class="button" type="submit" value="{LANG_portal_buttonSaveSettings}" ></span>
        </div>
    </FORM>
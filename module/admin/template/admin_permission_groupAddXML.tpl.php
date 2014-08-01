<?xml version="1.0" encoding="utf-8" ?> 
<groupadd>
	<mainform><![CDATA[
            <FORM action="{PORTAL_HTTP_HOST}/index.php" method="get" encType="multipart/form-data">
                <input type="hidden" name="modul" value="admin">
                <input type="hidden" name="groupid" value="{admin_grpid}">
                <input type="hidden" name="action" value="admin_addGroupPermissonToDb">
                <div style="clear:left;">
                    <div >{admin_content}</div>
                </div>
            </FORM>
	]]></mainform>
	<mainformUpdate><![CDATA[
            <FORM action="{PORTAL_HTTP_HOST}/index.php" method="get" encType="multipart/form-data">
                <input type="hidden" name="modul" value="admin">
                <input type="hidden" name="groupid" value="{admin_grpid}">
                <input type="hidden" name="action" value="admin_updateGroupPermission">
                <input type="submit" value="Save">
		Groupename: {admin_groupename} 
                <div style="clear:left;">
                    <div >{admin_content}</div>
                </div>
            </FORM>
	]]></mainformUpdate>
	<main><![CDATA[	
            <div >
                <a href="javascript:toggle('{admin_modulname}')" ><img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Bottom.png"></a>
                <input name="per_{admin_modulname}_core" type="radio" value="true" {admin_coreTrue}/>
                       <input name="per_{admin_modulname}_core" type="radio" value="false" {admin_coreFalse} />
			[<img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Component.png"> {admin_modulname}] {admin_modulCoreText}
            </div>
            <div style="display: none;border: 1px ridge silver;" id="{admin_modulname}">
			{admin_modulContent}
            </div>
	]]></main>
	<mainAdd><![CDATA[
            <div >
                <a href="javascript:toggle('{admin_modulname}')" ><img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Bottom.png"></a>
                <input name="per_{admin_modulname}_core" type="radio" value="true" />
                <input name="per_{admin_modulname}_core" type="radio" value="false" checked="checked" />
			[<img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/iconset/Component.png"> {admin_modulname}] {admin_modulCoreText}
            </div>
            <div style="display: none;border: 1px ridge silver;" id="{admin_modulname}">
			{admin_modulContent}
            </div>
	]]></mainAdd>
	<snipped><![CDATA[
            <div style="clear:left;">
                <div style="float:left;width:50px;height:10px">
                    <input name="per_{admin_modulname}_{admin_inputname}" type="checkbox" value="1" {admin_activ}>
                </div>
                <div >{admin_description}</div>
            </div>
	]]></snipped>
</groupadd>
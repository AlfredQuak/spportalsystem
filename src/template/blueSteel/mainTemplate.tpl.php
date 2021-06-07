<?xml version = "1.0" encoding = "UTF-8" ?>
<main>
    <index><![CDATA[
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
            <head>
                <link rel="shortcut icon" href="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/images/spportal.ico" type="image/vnd.microsoft.icon" />
                <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
                <title>{PORTAL_TEMPLATE_PORTALTITEL}</title>
                <!-- add your meta tags here -->
                {PORTAL_TEMPLATE_META_TAGS}
                <link href="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}css/my_layout.css" rel="stylesheet" type="text/css" />
                <!--[if lte IE 8]>
                <link href="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}css/patches/patch_my_layout.css" rel="stylesheet" type="text/css" />
                <![endif]-->
                {PORTAL_TEMPLATE_CSSFILES}
                {PORTAL_TEMPLATE_JSFILES}
            </head>
            <body>
                <div class="page_margins">
                    <div class="page">
                        {TEMPLATE_HEADER}
                        <div id="main">
                            {TEMPLATE_LEFT_BOX_TRUE}
                            {TEMPLATE_CONTENT_BOX_TRUE}
                            {TEMPLATE_RIGTH_BOX_TRUE}
                            <!-- begin: #footer -->
                            {TEMPLATE_FOOTER}
                        </div>
                    </div>
                </div>
            </body>
        </html>
        ]]></index>
    <indexLangURL><![CDATA[
        <div id="langURL">
            <a href="?modul=portal&action=CMS&lang={portal_langId}">
                <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}images/flagicons/png/{portal_langTag}.png" border="0"></img>
            </a>
        </div>
        ]]></indexLangURL>
    <indexHeader><![CDATA[
        {TEMPLATE_TOPLINK}
        <div class="header">
            <div id="search">
                <form action="?modul=portal&action=CMSSearching" method="post">
                    <img src="{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}images/iconset/Zoom.png" border="0"></img>
                    <input name="searchword" id="CMS_sideSearch" maxlength="20" alt="search" 
                           type="text" size="20" value=" {LANG_portal_searchBox}"  
                           onblur="if(this.value=='') this.value=' {LANG_portal_searchBox}';" 
                           onfocus="if(this.value==' {LANG_portal_searchBox}') this.value='';" />
                </form>
            </div>
            <div id="logo"></div>
        </div>
        <div id="bgTopNav">
            <div id="bgTopNavImg"></div>
            <div id="topnav">
                <div id="topnavURL">
                    <a href="{LANG_portal_toplinkHomeURL}">{LANG_portal_toplinkHome}</a> |
                    <a href="/forum/index.php">Forum</a> |
                    <a href="{LANG_portal_toplinkImprintURL}">{LANG_portal_toplinkImprint}</a> |
                    <a href="{LANG_portal_toplinkSitemapURL}">{LANG_portal_toplinkSitemap}</a>
                </div>
                {portal_langURL}
            </div>
        </div>
        ]]></indexHeader>
    <indexFooter><![CDATA[
        <div id="footer">
            Powered by <a href="http://www.sploindy.de" target="_blank">Sploindy Software</a> &copy; 2008 - 2010
        </div>
        ]]></indexFooter>
    <indexBox><![CDATA[
        <div class="box">
            <div class="Navigation">
                <!-- add your content here -->
                <h1>{BOX_TITEL}</h1>
                {BOX_CONTENT}
            </div>
        </div>
        <br />
        ]]></indexBox>
    <indexContentBox><![CDATA[
        <div class="box">
            <!--<div class="clearfix" >-->
            <div class="contentBox">
                <h1>{BOX_TITEL}</h1>
                {BOX_CONTENT}
            </div>
            <!--</div>-->
        </div>
        ]]></indexContentBox>
    <searchAmountMain><![CDATA[
        <div class="clearfix" >
            <div class="contentBox">
                <div class="topSearchGo">
                    <div class="searchSearch">
                        <h1>{LANG_portal_searchTitel}</h1>
                        <form action="?modul=portal&action=CMSSearching" method="post">
                            <div  style="float:left;">
                                {LANG_portal_valueSearchText}
                                <input name="searchword" id="CMS_sideSearch" maxlength="20" alt="search" 
                                       type="text" size="20" value=" {portal_searchword}"  
                                       onblur="if(this.value=='') this.value=' {portal_searchword}';" 
                                       onfocus="if(this.value==' {portal_searchword}') this.value='';" />
                                <select name="searchOption" size="1">
                                    <option value="1" {portal_1select}>{LANG_portal_searchvalueEveryWoerd}</option>
                                    <option value="2" {portal_2select}>{LANG_portal_searchvalueEveryWoerds}</option>
                                    <option value="3" {portal_3select}>{LANG_portal_searchvalueOnlyThisWoerd}</option>
                                </select>
                                <input type="submit" value="search" style="border-style: solid" />
                            </div>
                            <div style="float:right;">
                                <a href="{portal_SearchBack}"><<</a> {portal_searchStep} <a href="{portal_SearchTo}">>></a>
                            </div>
                        </form>
                    </div>
                    <div style="clear: right;"><hr /></div>
                </div>
            </div>
        </div>
        {portal_searchBoxes}
        <br />
        <div class="topSearchGo">
            <div><hr /></div>
            <center><a href="{portal_SearchBack}"><<</a> {portal_searchStep} <a href="{portal_SearchTo}">>></a></center>
        </div>
        ]]></searchAmountMain>
    <searchAmount><![CDATA[
        <div class="clearfix" >
            <div class="contentBoxAmount">
                <h1><a href="?modul=portal&action=CMS&page={portal_amountURL}">{portal_titel}</a></h1>
                <div class="searchAmount">
                    {portal_searchAmount}
                </div>
            </div>
        </div>
        ]]></searchAmount>
    <portalToplinkIndex><![CDATA[
        <div id="navigation">
            <ul class="menu" id="ie6fix">
                {portal_toplinks}
            </ul>
        </div>
        ]]></portalToplinkIndex>
    <portalToplinkMain><![CDATA[
        <li><a href="{portal_mainlinkUrl}">{portal_mainlinkText}</a>
            <!-- Pull-Down Menue -->
            <div class="menuparent">
                <ul>
                    {portal_sublinks}
                </ul>
            </div>
            <!-- Pull-Down Menue Ende -->
        </li>
        ]]></portalToplinkMain>
    <portalToplinkSub><![CDATA[
        <li><a href="{portal_sublinkUrl}">{portal_sublinkText}</a></li>
        ]]></portalToplinkSub>
</main>

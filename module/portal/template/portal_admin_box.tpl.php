<script type="text/javascript">
    function popup (url) {
        if(confirm("Die Sitemap wird bei Google erneut eingereicht\n\n"+url)){
            fenster = window.open(url, "Google Sitemap", "width=800,height=300,resizable=yes");
            fenster.focus();
        }
        return false;
    }
    function popupSitemapWatch (url) {
        fenster = window.open(url, "Google Sitemap", "width=800,height=300,resizable=yes,scrollbars=yes");
        fenster.focus();
        return false;
    }
</script>

<ul id="Navigation">
    <li><h1>.:: Portal Settings</h1></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_settings">Portal Settings</a></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_domainSettings">Domain Settings</a></li>
    <li><h1>.:: Sitemap.xml</h1></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_genSitemapManuell">Sitemap generieren</a></li>
    <li><a href="http://www.google.com/webmasters/tools/ping?sitemap={SP_PORTAL_SITEMAP_URL}" target="_blank" onclick="return popup(this.href);">Sitemap einreichen</a></li>
    <li><a href="#" target="_blank" onclick="return popupSitemapWatch('{SP_PORTAL_SITEMAP_URL}');">Sitemap betrachten</a></li>
    <li><h1>.:: Boxen</h1></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_addBox">Box anlegen</a></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_watchBoxes">Boxen editieren</a></li>
    <li><h1>.:: CMS</h1></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSNewSide">Seite anlegen</a></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSWatchAllSides">Seite Bearbeiten</a></li>
    <li><h1>.:: News</h1></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSWriteNews">{LANG_portal_menueWriteNews}</a></li>
    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=portal_CMSListNews">News Bearbeiten</a></li>
</ul>
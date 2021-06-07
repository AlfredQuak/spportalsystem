<?xml version = "1.0" encoding = "UTF-8" ?>
<main>
    <index><![CDATA[
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

            <head>
                <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
                <title>Spops - Portal Management</title>
                {PORTAL_TEMPLATE_META_TAGS}
                {PORTAL_TEMPLATE_CSSFILES}
                {PORTAL_TEMPLATE_JSFILES}
            </head>
            <body>
                <header>
                    {TEMPLATE_HEADER}
                </header>
                <section id="main">
                    {TEMPLATE_CONTENT_BOX_TRUE}
                </section>
                <aside class="left">
                    {TEMPLATE_LEFT_BOX_TRUE}
                </aside>
                <aside class="right">
                    {TEMPLATE_RIGTH_BOX_TRUE}
                </aside>
                {TEMPLATE_FOOTER}
            </body>
        </html>
        ]]></index>
    <indexHeader><![CDATA[
        <h1>Spops</h1>
        <ul class="level1">
            <li><span>Eingeloggt als {admin_username}</span>
                <ul class="level2">
                    <li>Benutzerverwaltung</li>
                    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_logout">{LANG_admin_CmenuSystemLogout}</a></li>
                </ul>
                <div class="resizer">Resize Window</div>
            </li>
        </ul>
        ]]></indexHeader>
    <indexFooter><![CDATA[
        <footer>
            <p>Powered by <a href="http://www.sploindy.de" target="_blank">Sploindy Software</a> &copy; 2008 - {LANG_admin_versionLegal}</p>
        </footer>
        ]]></indexFooter>
    <indexBox><![CDATA[
        <div class="modul">
            <h1>{BOX_TITEL}</h1>
            {BOX_CONTENT}
        </div>
        ]]></indexBox>
    <indexContentBox><![CDATA[
        <div class="inner">
            <section class="left">
                <h1>{BOX_TITEL}</h1>
                {BOX_CONTENT}
            </section>
            <section class="right">

            </section>
        </div>

        ]]></indexContentBox>
    <basicNav><![CDATA[
        <ul>
            <li>
                <h2>System Info</h2>
                <ul>
                    <li>{LANG_admin_version}</li>
                </ul>
            </li>
            <li>
                <h2>{LANG_admin_TmenuSystemSettings}</h2>
                <ul>
                    <!--<li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_lang">Sprache</a></li>-->
                    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_settings">{LANG_admin_CmenuSystemSettings}</a></li>
                </ul>
            </li>
            <li>
                <h2>{LANG_admin_TmenuModulSettings}</h2>
                <ul>
                    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_modulView">{LANG_admin_CmenuModulDeInstall}</a></li>
                    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_mStart">{LANG_admin_CmenuModulSettStartModul}</a></li>
                </ul>
            </li>
            <li>
                <h2>{LANG_admin_TmenuUserManagemant}</h2>
                <ul>
                    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_userManagemant">{LANG_admin_CmenuSystemGroupUser}</a></li>
                    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_userAdd">{LANG_admin_CmenuSystemUserAdd}</a></li>
                    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_groupAdd">{LANG_admin_CmenuSystemGroupAdd}</a></li>
                    <li><a href="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_groupSettings">{LANG_admin_CmenuSystemGroupEdit}</a></li>
                </ul>
            </li>
        </ul>
        ]]></basicNav>
    <login><![CDATA[
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

            <head>
                <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
                <title>Spops - Portal Management</title>
                {PORTAL_TEMPLATE_META_TAGS}
                {PORTAL_TEMPLATE_CSSFILES}
                {PORTAL_TEMPLATE_JSFILES}
            </head>
            <body id="login">
                <header>
                    <h1>Spops</h1>
                </header>		
                <section>
                    <div class="inner">
                        <h1>Anmelden</h1>
                        <FORM action="{PORTAL_HTTP_HOST}/?modul=admin&action=admin_login" method="post">
                            <fieldset>
                                <div class="field">
                                    <label for="username">Benutzername:</label>
                                    <center>
                                        <input type="text" name='name' placeholder="{LANG_admin_valueName}">
                                    </center>
                                </div>
                                <div class="field">
                                    <label for="password">Passwort:</label>
                                    <center>
                                        <input type="password" name="password" placeholder="{LANG_admin_valuePassword}">
                                    </center>
                                </div>
                            </fieldset>
                            <fieldset>
                                <center>
                                    <input class="button" type="submit" value="Erstellen">
                                </center>
                            </fieldset>
                        </form>
                    </div>
                </section>
                <footer>
                    <p>Powered by <a href="http://www.sploindy.de" target="_blank">Sploindy Software</a> &copy; 2008 - {LANG_admin_versionLegal}</p>
                </footer>
            </body>
        </html>
        ]]></login>
</main>
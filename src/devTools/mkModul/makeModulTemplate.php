<?php

require_once '../../includes/CTemplate.php';
$xmlTemplate = "<?xml version = \"1.0\" encoding = \"UTF-8\" ?>
<main>
    <index><![CDATA[
       Your HTML Content 
    ]]></index>
</main>";
$xmlTemplateNew = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<main>
    <index><![CDATA[
        <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
        <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"de\" lang=\"de\">
            <head>
                <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
                <title>{PORTAL_TEMPLATE_PORTALTITEL}</title>
                <meta name=\"keywords\" content=\"\" />
                <meta name=\"description\" content=\"\" />
                <link href=\"{PORTAL_HTTP_HOST}/{PORTAL_TEMPLATE_PATH}/default.css\" rel=\"stylesheet\" type=\"text/css\" />
                {PORTAL_TEMPLATE_META_TAGS}
                {PORTAL_TEMPLATE_CSSFILES}
                {PORTAL_TEMPLATE_JSFILES}
            </head>
                {TEMPLATE_HEADER}
                {TEMPLATE_LEFT_BOX_TRUE}
                {TEMPLATE_CONTENT_BOX_TRUE}
                {TEMPLATE_FOOTER}
        </html>
        ]]></index>
    <indexLangURL><![CDATA[
        ]]></indexLangURL>
    <indexHeader><![CDATA[
        ]]></indexHeader>
    <indexFooter><![CDATA[
        ]]></indexFooter>
    <indexBox><![CDATA[
        <div>
            <h2>{BOX_TITEL}</h2>       
            <div>
                <ul>
                    {BOX_CONTENT}
                </ul>
            </div>
        </div>
        ]]></indexBox>
    <indexContentBox><![CDATA[
        <div>
            <h2>{BOX_TITEL}</h2>
            <div>
                {BOX_CONTENT}
            </div>
        </div>
        ]]></indexContentBox>
    <searchAmountMain><![CDATA[
        ]]></searchAmountMain>
    <searchAmount><![CDATA[
        ]]></searchAmount>
</main>";

function loadTemplate($fileName) {
    $xml = utf8_decode(file_get_contents("template/" . $fileName . ".tpl.php"));
    libxml_use_internal_errors(true);
    $outXML = simplexml_load_string($xml, null, LIBXML_NOCDATA | LIBXML_NOEMPTYTAG);
    if ($outXML === false) {
        $error = "Loading " . $fileName . " failed ";
        foreach (libxml_get_errors() as $err) {
            $error .= "\t" . $err->message;
        }
    } else {
        return $outXML;
    }
}

function replaceModulName($string, $modulname) {
    return str_replace("##MODULNAME##",$modulname , $string);
}

if (!$rStdin = fopen("php://stdin", "r"))
    die();

echo "Name for modul : ";
$destPath = "../../module/";
$sInput = trim(fgets($rStdin));

mkdir($destPath . $sInput);
fwrite(fopen($destPath . $sInput . "/" . $sInput . "_index.php", "a"), 
        "<?php\n\n".replaceModulName(loadTemplate("modulCreate")->createAdminIndexFile, $sInput)."\n?>\n");

mkdir($destPath. $sInput . "/admin");
fopen($destPath . $sInput . "/admin/" . $sInput . "_admin_index.php", "a");
fwrite(fopen($destPath . $sInput . "/admin/" . $sInput . "_permission.php", "a"), 
        "<?php\n\n".replaceModulName(loadTemplate("modulCreate")->createAdminInstallPermission, $sInput)."\n?>\n");
fwrite(fopen($destPath . $sInput . "/admin/" . $sInput . "_install.php", "a"), 
        "<?php\n\n".replaceModulName(loadTemplate("modulCreate")->createAdminInstall, $sInput)."\n?>\n");
fwrite(fopen($destPath . $sInput . "/admin/install.sql", "a"), 
        replaceModulName(loadTemplate("modulCreate")->adminInstall_SQL, $sInput));
fwrite(fopen($destPath . $sInput . "/admin/uninstall.sql", "a"), 
        replaceModulName(loadTemplate("modulCreate")->adminUninstall_SQL, $sInput));

mkdir($destPath . $sInput . "/admin/lang");
fopen($destPath . $sInput . "/admin/lang/de_" . $sInput . ".xml", "a");
fopen($destPath . $sInput . "/admin/lang/en_" . $sInput . ".xml", "a");

mkdir($destPath . $sInput . "/includes");
fwrite(fopen($destPath . $sInput . "/includes/C" . $sInput . ".php", "a"), 
        "<?php\n\n".replaceModulName(loadTemplate("modulCreate")->createClass, $sInput)."\n?>\n");
fwrite(fopen($destPath . $sInput . "/includes/C" . $sInput . "_admincenter.php", "a"),
        "<?php\n\n".replaceModulName(loadTemplate("modulCreate")->createClassAdmin, $sInput)."\n?>\n");

mkdir($destPath . $sInput . "/lang");
fopen($destPath . $sInput . "/lang/de_admin.xml", "a");

mkdir($destPath . $sInput . "/images");
mkdir($destPath . $sInput . "/template");
mkdir($destPath . $sInput . "/template/admin");
fwrite(fopen($destPath . $sInput . "/template/admin/a_" . $sInput . "_index.tpl.php", "a"),$xmlTemplate);
mkdir($destPath . $sInput . "/template/css");
fopen($destPath . $sInput . "/template/css/" . $sInput . ".css", "a");
mkdir($destPath . $sInput . "/template/js");
fopen($destPath . $sInput . "/template/js/" . $sInput . ".js", "a");
fwrite(fopen($destPath . $sInput . "/template/" . $sInput . "_index.tpl.php", "a"),$xmlTemplateNew);
fwrite(fopen($destPath . $sInput . "/template/" . $sInput . "_admin_box.tpl.php", "a"),
        replaceModulName(loadTemplate("modulCreate")->createAdminNavigation, $sInput));

echo "ausgabe " . $destPath . $sInput;
?>

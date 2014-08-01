<?php
/*
Php Image Plug-in uses a GPL licensed class "class.upload.php"
Authors website: http://www.verot.net/php_class_upload.htm
For a full list of extra options: http://www.verot.net/res/sources/class.upload.html

Default settings will resize any uploaded image to a maxiumum height of 400 px high or wide (saves bandwidth),
will replace spaces in filenames with _ (underscore), and will auto rename the file if it already exists.
*/
/*
 * Modified by Daniel Stecker 2010 for the spPortalSystem
 * Please set your own $cur_dir
 * If your Portal is in subdirectory add the folder name like this :
 * $_cur_dir = $_SERVER['DOCUMENT_ROOT'].'yoursubfolder';
 */
/* add for spPortalSystem Modul Portal */
require_once './../../../../../../../../config.inc.php';
/* ----------------------------------------------------- */

// Simple way to get back to server path minus the javascript directorys
$_cur_dir = SP_CORE_DOC_ROOT ; // minus the amout of directorys back to root directory from current run script e.g. /js/tinymce/plugins/phpimage

// The default language for errors is english to change to another type add lang to the lang folder e.g. fr_FR (french) to get language packs please download the class zip from the above authors link
$language			= 'en_EN';
// server file directory to store images - IMPORTANT CHANGE PATH TO SUIT YOUR NEEDS!!!
$server_image_directory		= $_cur_dir.'/module/portal/uploadimage';  //e.g. '/home/user/public_html/uploads';
$server_image_directory         = str_replace("//", "/", $server_image_directory);
// URL directory to stored images (relative or absoulte) - IMPORTANT CHANGE PATH TO SUIT YOUR NEEDS!!!
//$url_image_directory		= '/'. SP_CORE_SUB_DOC_PATH .'/module/portal/uploadimage';
$url_image_directory		= SP_PORTAL_HTTP_HOST.'module/portal/uploadimage';
$url_image_directoryWatch	= SP_PORTAL_HTTP_HOST.'module/portal/uploadimage';
//$url_image_directory		= '{PORTAL_HTTP_HOST}/module/portal/uploadimage';
//$url_image_directoryWatch	= '{PORTAL_HTTP_HOST}/module/portal/uploadimage';

//die($server_image_directory);


?>
<?php

require_once './../../config.inc.php';
require_once (SP_CORE_DOC_ROOT . "/includes/CHelper.php");
require_once 'includes/Csptwitter.php';

// script main function

$myRss = Csptwitter::getMyContent("http://twitter.com/statuses/user_timeline/" . Csptwitter::$twittername . ".rss");
$myRSSXml = @simplexml_load_string($myRss, null, LIBXML_NOCDATA | LIBXML_NOEMPTYTAG);

if (is_object($myRSSXml)) {
    $chan = $myRSSXml->channel;
    $tmp = $chan->item;
    $out = "";

    $image = "<img src=\"" . SP_PORTAL_HTTP_HOST . "/module/sptwitter/images/twitter.ico\" alt=\"Favicon\" />";
    for ($i = 0; $i <= 4; $i++) {
        $out .= "<table style=\"margin-bottom:5px;margin-top:5px;margin-left:5px;margin-right:5px;\" width=\"95%\">
            <tr bgcolor=\"#d3d3d3\">
            <td height=\"5px\" style=\"line-height: 15px;text-align: left;\">";
        $out .= $image . "<a href=\"" . $tmp[$i]->link . "\" target=\"_blank\">" . date("d.m.Y H:i", strtotime($tmp[$i]->pubDate)) . " Uhr </a></tr></td>";
        $out .= "<tr><td style=\"line-height: 13px;text-align: left;\">" . Csptwitter::format_tweet($tmp[$i]->title, Csptwitter::$twittername) . "</tr><td>";
        $out .= "</table>";
    }
    spCore\CHelper::writeTMP("sptwitter",$out);
    echo $out;
} else {
    echo "Derzeit keine tweets";
}
?>

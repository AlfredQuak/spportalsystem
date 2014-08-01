<?php

function thirdPT_rsshack($e=null) {
    $myRss = file_get_contents("http://sourceforge.net/export/rss2_keepsake.php?group_id=262912");
    $myRSSXml = simplexml_load_string($myRss, null, LIBXML_NOCDATA | LIBXML_NOEMPTYTAG);

    $chan = $myRSSXml->channel;
    $tmp = $chan->item;
    $a = $tmp[0];

    /* spcore\CTemplate::getInstance()->addContentBox("spPortal SVN Commit : ".$a->pubDate,
      "<div style=\"background: blue;bottom:0;right:0;color:white;font-size:75%;\">"
      .$a->title."<br><a href=\"".$a->link."\" target=\"_blank\">".$a->link."</a></div><br>"); */
    return "<div style=\"bottom:0;right:0;color:black;font-size:75%;\"><p><b>Short News: " . $a->pubDate . "</b><br>
            <a style=\"color:black;\" href=\"" . $a->link . "\" target=\"_blank\">" . str_replace("to the spPortalSystem SVN repository", "", $a->title) . "</a></div></p>";
}

?>

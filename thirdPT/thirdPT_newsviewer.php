<?php

function thirdPT_newsviewer($e=null) {
    $sql = "SELECT * FROM sp_modul_portal_cms_news WHERE active=1 ORDER BY news_date DESC LIMIT 0 , 5";
    $result = $e->query($sql);
    $newsout = "<b>News</b><hr>";

    if ($result != null) {
        if ($e->num_rows($result) == 0) {
            $newsout .= "<b>".date("D d.m.Y",time())." / {LANG_portal_noNews}</b>";
            $newsout .= "<p style=\"padding-left: 30px;\">{LANG_portal_noNewsToday}</p>";
        } else {
            while ($res = $e->fetch_array($result)) {
                $newsout .= "(".date("D d.m.Y", $res['news_date']) . ") ";
                $newsout .= "<b>".$res['titel_text']."</b>";
                $newsout .= str_replace("<p>","<p style=\"padding-left: 30px;\">",spCore\CHelper::makeHTMLwork($res['page_content']));
            }
        }
    }
    return $newsout;
}

?>

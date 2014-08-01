<?php

/**
 * Description of twitter
 *
 * @author ds
 */
class Csptwitter {

    public static $twittername = "SirVanDreamer";

    public function _nav_extportal($e=null) {
        spCore\CTemplate::getInstance()->addJsScript("sptwitter", "/js/ajax.js");
        spCore\CTemplate::getInstance()->addJsScript("sptwitter", "/js/ajax-dynamic-content.js");

        $followImage = "<center><a href=\"http://twitter.com/#!/" . Csptwitter::$twittername . "/\" target=\"_blank\">
                        <img style=\"margin-bottom:5px;margin-top:5px;margin-left:5px;margin-right:5px;\" 
                        src=\"{PORTAL_HTTP_HOST}/module/sptwitter/images/twitter_follow.png\" alt=\"follow\" />
                        </a></center>";

        if ((time() - spCore\CHelper::getTMPTimestamp("sptwitter") <= 1800/* check every 15min */)) {
            return spCore\CHelper::getTMP("sptwitter") . $followImage;
        }
        
        $javascript = "<script type=\"text/javascript\">
                            ajax_loadContent('spTwitterScript','{PORTAL_HTTP_HOST}/module/sptwitter/ajax_twitter.php');
                       </script>";
        $htmlout = "<div id=\"spTwitterScript\"><table height=\"250px\"><tr><td>Loading tweets... </td></tr></table></div>";

        return $htmlout . $javascript . $followImage;
    }

    // this function found on http://toscho.de/2010/twitter-einen-tweet-formatieren/
    public static function format_tweet($content, $user = NULL) {
        $content = str_replace('...', '... ', $content); // sploindy hack
        $uri_schemes = array(
            'mailto', 'news', 'nntp', 'callto', 'ed2k', 'irc', 'ssh', 'svn',
            's?ftps?', 'https?');
        $regex['url'] = '(' . implode('|', $uri_schemes)
                . ')://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?';
        $regex['username'] = "@([a-zA-Z_\d]+)";
        $regex['hashtag'] = "([^\pL]){0,1}(#([-_\pL\d]+))";

        if (!is_null($user)) {
            // Usernamen + ': ' aus Text entfernen
            $content = substr($content, ( strlen($user) + 2));
        }
        // Hyperlinks umwandeln
        $content = preg_replace(
                '~' . $regex['url'] . '~u', '<a href="${0}" target=\"_blank\"><b>${0}</b></a> ', $content);
        // Usernamen verlinken
        $content = preg_replace(
                '~' . $regex['username'] . '~u', '<a href="http://twitter.com/${1}" target=\"_blank\"><b>${0}</b></a>', $content);
        // Hashtags verlinken
        $content = preg_replace(
                '~' . $regex['hashtag'] . '~u', ' ${1}<a href="http://twitter.com/search?q=%23${3}" target=\"_blank\"><b>${2}</b></a>', $content);

        return $content;
    }

    public static function getMyContent($myurl) {
        $content = "";

        if (function_exists('curl_version')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $myurl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $content = curl_exec($curl);
            curl_close($curl);
        } else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
            $content = file_get_contents($myurl);
        }
        return $content;
    }

}

?>

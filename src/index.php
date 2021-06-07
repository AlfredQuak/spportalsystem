<?php

//phpinfo();

const SP_PROFILE = false;

if (!file_exists('config.inc.php')) {
    header('Location: ' . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], "/index.php")) . '/install/step1.php');
} else {
    require_once 'config.inc.php';
    if (SP_CORE_DEBUG > SP_LOG_NOTHING && function_exists("xhprof_enable")) {
        xhprof_enable();
    }
    require_once SP_CORE_DOC_ROOT . '/includes/CLog.php';
    require_once SP_CORE_DOC_ROOT . '/includes/CTemplate.php';
    require_once SP_CORE_DOC_ROOT . '/includes/CHelper.php';
    require_once SP_CORE_DOC_ROOT . '/includes/CDatabase.php';
    require_once SP_CORE_DOC_ROOT . '/includes/CSession.php';

    if (!file_exists(SP_CORE_DOC_ROOT . '/includes/CTemplate.php')) {
        die('Config error ! ' . SP_CORE_DOC_ROOT . '/includes/CTemplate.php not found');
    }

    /* ------- IP cruncher ----------------------------------------- */
    if (SP_CORE_IP_CRUNCHER) {
        $ip = explode('.', $_SERVER['REMOTE_ADDR']);
        $_SERVER['REMOTE_ADDR'] = "" . $ip[0] . "." . $ip[1] . "." . $ip[2] . ".XXX";
    }
    /* ------------------------------------------------------------- */
    if (SP_CORE_ONLY_WWW) {
        $tmp = explode(".", $_SERVER['HTTP_HOST']);
        if ($tmp[0] != "www") {
            $_SERVER['HTTP_HOST'] = "www." . $_SERVER['HTTP_HOST'];
        }
    }
    /* ------------------------------------------------------------- */


    $template = spcore\CTemplate::getInstance();
    $session = spcore\CSession::getInstance(spcore\CDatabase::getInstance());
    $requestVar = spcore\CHelper::getRequestVar();

//die("service  and  maintenance, one minute please ... ");
    $noModuleInstalled = function () {
        spcore\CTemplate::getInstance()->setWatchLeftBox(false);
        spcore\CTemplate::getInstance()->setWatchRightBox(false);
        spcore\CTemplate::getInstance()->addContentBox("Welcome", "<br><br><br><center>No Module installed<br>only minimal mode is running !</center><br><br><br>");
    };

    if (spcore\CDatabase::getInstance()->getConnection() === false) {
        $noModuleInstalled();
        die(spcore\CTemplate::getInstance()->getRenderdSite());
    }

//whitelist
    $alMod[] = array();
    $sql = "SELECT modul_name FROM sp_modul_settings WHERE modul_active = 1";
    $result = spcore\CDatabase::getInstance()->query($sql);
    if ($result) {
        while ($res = spcore\CDatabase::getInstance()->fetch_assoc($result)) {
            if (is_dir(SP_CORE_DOC_ROOT . "/module/" . $res['modul_name'])) {
                $alMod[$res['modul_name']] = $res['modul_name'];
            }
        }
    }

// loading content for modul xy
    if (isset($requestVar['modul']) && isset($alMod[trim($requestVar['modul'])])) {
        // load modul index page
        if (file_exists(SP_CORE_DOC_ROOT . '/module/' . $requestVar['modul'] . '/' . $requestVar['modul'] . '_index.php')) {
            require_once SP_CORE_DOC_ROOT . '/module/' . $requestVar['modul'] . '/' . $requestVar['modul'] . '_index.php';
            // check language file for selected modul
            if ($session->getLang() && file_exists(SP_CORE_DOC_ROOT . '/module/' . $requestVar['modul'] . '/lang/' . $session->getLang() . '_' . $requestVar['modul'] . '.xml')) {
                spcore\CTemplate::getInstance()->setLangObj(simplexml_load_file(SP_CORE_DOC_ROOT . '/module/' . $requestVar['modul'] . '/lang/' . $session->getLang() . '_' . $requestVar['modul'] . '.xml'));
            } elseif (file_exists(SP_CORE_DOC_ROOT . '/module/' . $requestVar['modul'] . '/lang/' . SP_CORE_LANG . '_' . $requestVar['modul'] . '.xml')) {
                spcore\CTemplate::getInstance()->setLangObj(simplexml_load_file(SP_CORE_DOC_ROOT . '/module/' . $requestVar['modul'] . '/lang/' . SP_CORE_LANG . '_' . $requestVar['modul'] . '.xml'));
            }
        } else {
            spcore\CTemplate::getInstance()->setWatchLeftBox(false);
            spcore\CTemplate::getInstance()->setWatchRightBox(false);
            spcore\CTemplate::getInstance()->addContentBox("Error", "<br><br><br><center>Module does not exist !</center><br><br><br>");
        }
    } else {
        // no modul give, load start modul if is set.
        $result = spcore\CDatabase::getInstance()->query("SELECT start_modul FROM sp_settings");
        if ($result != null) {
            $resObject = spcore\CDatabase::getInstance()->fetch_object($result);
            if (is_object($resObject) && file_exists(SP_CORE_DOC_ROOT . '/module/' . $resObject->start_modul . '/' . $resObject->start_modul . '_index.php')) {
                require_once SP_CORE_DOC_ROOT . '/module/' . $resObject->start_modul . '/' . $resObject->start_modul . '_index.php';
                if ($session->getLang() && file_exists(SP_CORE_DOC_ROOT . '/module/' . $resObject->start_modul . '/lang/' . $session->getLang() . '_' . $resObject->start_modul . '.xml')) {
                    spcore\CTemplate::getInstance()->setLangObj(simplexml_load_file(SP_CORE_DOC_ROOT . '/module/' . $resObject->start_modul . '/lang/' . $session->getLang() . '_' . $resObject->start_modul . '.xml'));
                } elseif (file_exists(SP_CORE_DOC_ROOT . '/module/' . $resObject->start_modul . '/lang/' . SP_CORE_LANG . '_' . $resObject->start_modul . '.xml')) {
                    spcore\CTemplate::getInstance()->setLangObj(simplexml_load_file(SP_CORE_DOC_ROOT . '/module/' . $resObject->start_modul . '/lang/' . SP_CORE_LANG . '_' . $resObject->start_modul . '.xml'));
                }
            } else {
                $noModuleInstalled();
            }
        } else {
            $noModuleInstalled();
        }
    }
    echo spcore\CTemplate::getInstance()->getRenderdSite();

    if ((SP_CORE_DEBUG > SP_LOG_NOTHING) && SP_PROFILE == true) {
        if (function_exists("xhprof_disable")) {
            $xhprof_data = xhprof_disable();
            include_once SP_CORE_DEBUG_XHPROF_PATH . "/xhprof_lib/utils/xhprof_lib.php";
            include_once SP_CORE_DEBUG_XHPROF_PATH . "/xhprof_lib/utils/xhprof_runs.php";
            $xhprof_runs = new XHProfRuns_Default();
            $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");
            echo "<div style=\"float:clear;\"><center><font color=\"white\">--------------- Assuming you have set up the http based UI for XHProf at some address, you can view run at
      <a href=\"/xhprof/index.php?run=$run_id&source=xhprof_foo\" target=\"_blank\">#klick#</a> ---------------</font></center></div>";
        }
    }
}
?>
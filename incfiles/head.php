<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

include 'inc_nongtrai.php';

$headmod = isset($headmod) ? mysql_real_escape_string($headmod) : '';
$textl = isset($textl) ? $textl : $set['copyright'];
$seokw = (isset($seow) ? $seow : $set['copyright']).' - '.$set['meta_key'];
$seodesc = isset($seod) ? $seod : $set['meta_desc'];
echo '<!DOCTYPE html>' .
    "\n" . '<html lang="en">' .
    "\n" . '<head>' .
    "\n" . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .
    "\n" . '<meta http-equiv="X-UA-Compatible" content="IE=edge">' .
    "\n" . '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes">' .
    "\n" . '<meta name="HandheldFriendly" content="true">' .
    "\n" . '<meta name="MobileOptimized" content="width">' .
    "\n" . '<meta content="yes" name="apple-mobile-web-app-capable">' .
    "\n" . '<meta name="Generator" content="Mod JohnCMS, http://vnfun.pro">' .
    "\n" . '<meta name="robots" content="'.(isset($noRobots) ? 'noindex, nofollow' : 'index, follow, noodp, noydir').'" />' .
    (!empty($set['meta_key']) ? "\n" . '<meta name="keywords" content="' . $seokw . '">' : '') .
    (!empty($set['meta_desc']) ? "\n" . '<meta name="description" content="' . $seodesc . '">' : '') .
    "\n" . '<link rel="stylesheet" href="' . $set['homeurl'] . '/theme/' . $set_user['skin'] . '/style.css">' .
    "\n" . ($is_mobile ? '<link rel="stylesheet" href="' . $set['homeurl'] . '/theme/' . $set_user['skin'] . '/wap-fonts.css">' : '<link rel="stylesheet" href="' . $set['homeurl'] . '/theme/' . $set_user['skin'] . '/web-fonts.css">') .
    "\n" . '<link rel="stylesheet" href="/css/modernforms.css">' .
    "\n" . '<link rel="shortcut icon" href="' . $set['homeurl'] . '/favicon.ico">' .
    "\n" . '<link rel="alternate" type="application/rss+xml" title="RSS | ' . $lng['site_news'] . '" href="' . $set['homeurl'] . '/rss/rss.php">' .
    "\n" . '<title>' . $textl . '</title>' .
    ($user_id ? "\n" . '<script language="javascript" src="/auto/auto.js" type="text/javascript"></script>' : '') .
    "\n" . '<script type="text/javascript" src="/js/jquery-1.11.0.min.js"></script>' .
    /////// "\n" . '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>' .
    ($user_id ? "\n" . '<script language="javascript" src="/js/like.js" type="text/javascript"></script>' : '') .
    "\n" . '<script type="text/javascript" src="'.$home.'/js/jquery.min.js"></script>' .
    "\n" . '<script type="text/javascript" src="/js/jquery.validate.js"></script>'.
    "\n" . '<link type="text/css" rel="stylesheet" href="/css/jquery-ui.css">'.
    "\n" . '<link rel="stylesheet" type="text/css" media="screen" href="/html5-video-player/packages/font-awesome/css/font-awesome.min.css" />'.
    "\n" . '<link rel="stylesheet" type="text/css" media="screen" href="/html5-video-player/css/html5-video-player.min.css" />'.
    "\n" . '<link type="text/css" rel="stylesheet" href="/js/mediaelement/mediaelementplayer.css">'.
    "\n" . '<link type="text/css" rel="stylesheet" href="/css/jplayer.css">'.

    "\n" . '<script type="text/javascript" src="/js/mediaelement/mediaelement-and-player.min.js"></script>'.

    "\n" . '<script type="text/javascript" src="/html5-video-player/js/html5-video-player.jquery.min.js"></script>'.

    "\n" . '<script type="text/javascript" src="/js/jquery.jplayer.min.js"></script>'.
    "\n" . '<script type="text/javascript" src="/js/playerLogic.js"></script>'.
    "\n" . '<script type="text/javascript" src="/js/smoothscroll.js"></script>'.
    "\n" . '<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script><script type="text/javascript" src="/js/jquery-timeago.js"></script><script type="text/javascript" src="/js/func.js"></script>'.

    "\n" . '</head><body data-rewrite="1">' . core::display_core_errors();
    include_once("analyticstracking.php");
    echo ($user_id ? '<div class="page-loading-bar"><dd></dd><dt></dt></div>' : '');
    echo '<div id="main">';
/*
-----------------------------------------------------------------
Рекламный модуль
-----------------------------------------------------------------
*/
$cms_ads = array();
if (!isset($_GET['err']) && $act != '404' && $headmod != 'admin') {
    $view = $user_id ? 2 : 1;
    $layout = ($headmod == 'mainpage' && !$act) ? 1 : 2;
    $req = mysql_query("SELECT * FROM `cms_ads` WHERE `to` = '0' AND (`layout` = '$layout' or `layout` = '0') AND (`view` = '$view' or `view` = '0') ORDER BY  `mesto` ASC");
    if (mysql_num_rows($req)) {
        while (($res = mysql_fetch_assoc($req)) !== FALSE) {
            $name = explode("|", $res['name']);
            $name = htmlentities($name[mt_rand(0, (count($name) - 1))], ENT_QUOTES, 'UTF-8');
            if (!empty($res['color'])) $name = '<span style="color:#' . $res['color'] . '">' . $name . '</span>';
            // Если было задано начертание шрифта, то применяем
            $font = $res['bold'] ? 'font-weight: bold;' : FALSE;
            $font .= $res['italic'] ? ' font-style:italic;' : FALSE;
            $font .= $res['underline'] ? ' text-decoration:underline;' : FALSE;
            if ($font) $name = '<span style="' . $font . '">' . $name . '</span>';
            @$cms_ads[$res['type']] .= '<a href="' . ($res['show'] ? functions::checkout($res['link']) : $set['homeurl'] . '/go.php?id=' . $res['id']) . '">' . $name . '</a><br/>';
            if (($res['day'] != 0 && time() >= ($res['time'] + $res['day'] * 3600 * 24)) || ($res['count_link'] != 0 && $res['count'] >= $res['count_link']))
                mysql_query("UPDATE `cms_ads` SET `to` = '1'  WHERE `id` = '" . $res['id'] . "'");
        }
    }
}

/*
-----------------------------------------------------------------
Рекламный блок сайта
-----------------------------------------------------------------
*/
if (isset($cms_ads[0])) echo $cms_ads[0];

/*
-----------------------------------------------------------------
Выводим логотип и переключатель языков
-----------------------------------------------------------------
*/

$thoiganhientai = gmdate("H:i - d/m/Y", time() + 7*3600);

if($user_id){
    $new_mail = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` LEFT JOIN `cms_contact` ON `cms_mail`.`user_id`=`cms_contact`.`from_id` AND `cms_contact`.`user_id`='$user_id' WHERE `cms_mail`.`from_id`='$user_id' AND `cms_mail`.`sys`='0' AND `cms_mail`.`read`='0' AND `cms_mail`.`delete`!='$user_id' AND `cms_contact`.`ban`!='1' AND `cms_mail`.`spam`='0'"), 0);
    $new_sys_mail = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `from_id`='$user_id' AND `read`='0' AND `sys`='1' AND `delete`!='$user_id';"), 0);
    $new_album_comm = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . core::$user_id . "' AND `unread_comments` = 1"), 0);
    $banmoi = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `from_id`='$user_id' AND `type`='2' AND `friends`='0';"), 0);
}


/*
-----------------------------------------------------------------
Выводим верхний блок с приветствием
-----------------------------------------------------------------
*/
echo '<script type="text/javascript"> function toggle(){var ele=document.getElementById("dropdown-toggleg");if(ele.style.display=="block"){ele.style.display = "none";}else{ele.style.display = "block";}} </script>';
if($user_id){
echo '<div id="trick" class="header" style="border-bottom: 1px solid #ffffff; border-top-right-radius: 5px; border-top-left-radius: 5px;"><a href="/" data-href="?tab1=index.php" style="padding: 5px; color: #FFFFFF;"><h1><span class="uppercase">phieubac.ga</span></h1></a></div>';
echo '<div id="header">
        <ul>
            <li><a href="' . $home . '/users/album.php" id="group_href" style="padding: 6px;"><img src="/images/user/photo.png" /><div class="vhnew" style="display: inline;" id="group">'.$new_album_comm.'</div></a></li>
            <li><a href="'.$home.'/mail/index.php" id="message_href" style="padding: 6px;"><img src="/images/user/mail.png" /><div class="vhnew" id="message">'.$new_mail.'</div></a></li>
            <li><a href="' . $home . '/users/profile.php?act=friends" id="friend_href" style="padding: 6px;"><img src="/images/user/banbe.png" /><div class="vhnew" id="friend">'.$banmoi.'</div></a></li>
            <li><a href="' . $home . '/mail/index.php?act=systems" style="padding: 6px;"><img src="/images/user/new.png" /><div class="vhnew" id="notification">'.$new_sys_mail.'</div></a></li>
            <div style="display: inline;float: right;"><a style="color: #fff;margin-right: 6px;" href="javascript:toggle();"><strong id="vh4">'.$datauser['name'].' <img src="/images/user/arrow.png" alt="" /> </strong><img src="' . $home . '/avatar/'.$user_id.'-15-30.png" /></a></div>
        </ul>
    </div>';
echo '<div id="relative"><div class="dropdown-toggleg" id="dropdown-toggleg">
<a href="' . $home . '/users/profile.php" data-href="?tab1=users&tab2=profile.php"><li><img src="/images/user/info.png" /> Trang cá nhân</li></a>
<a href="' . $home . '/group"><li><img src="/images/user/partner.png" /> Hội nhóm</li></a>
<li><center><table cellpadding="0" cellspacing="0"><tr><td valign="baseline" style="padding: 0 5px 0 5px"><a href="/pages/faq.php"><img src="/images/user/helpdesk.png" /></a></td><td valign="baseline" style="padding: 0 5px 0 5px"><a href="/users/profile.php?act=office"><img src="/images/user/settings.png" /></a></td><td valign="baseline" style="padding: 0 5px 0 5px"><a href="/exit.php"><img src="/images/user/exit.png" /></a></td></tr></table></center></li>
<li style="text-align:center">'.$thoiganhientai.'</li>
</div></div>';
}else{
echo '<div class="header" style="border-top-right-radius: 5px; border-top-left-radius: 5px;"><a href="/" style="padding: 5px; color: #FFFFFF;"><h1><span class="uppercase">phieubac.ga</span></h1></a></div>';
echo '<div class="header" style="border-top: 1px solid #ffffff; text-align: center;"><a href="/login.php" style="padding: 5px; color: #FFFFFF;" class="page_item">Đăng nhập</a> <a href="/registration.php" style="padding: 5px; color: #FFFFFF;" class="page_item">Đăng ký</a></div>';
}

if (!empty($ban)) echo '<br /><div class="alarm" style="text-align:center"><a href="' . $set['homeurl'] . '/users/profile.php?act=ban">Tài khoản bị cấm!!!</a></div>';

/*
-----------------------------------------------------------------
Рекламный блок сайта
-----------------------------------------------------------------
*/
if (!empty($cms_ads[1])) echo '<div class="gmenu">' . $cms_ads[1] . '</div>';

/*
-----------------------------------------------------------------
Фиксация местоположений посетителей
-----------------------------------------------------------------
*/
$sql = '';
$set_karma = unserialize($set['karma']);
if ($user_id) {
    // Фиксируем местоположение авторизованных
    if (!$datauser['karma_off'] && $set_karma['on'] && $datauser['karma_time'] <= (time() - 86400)) {
        $sql .= " `karma_time` = '" . time() . "', ";
    }
    $movings = $datauser['movings'];
    if ($datauser['lastdate'] < (time() - 300)) {
        $movings = 0;
        $sql .= " `sestime` = '" . time() . "', ";
    }
    if ($datauser['place'] != $headmod) {
        ++$movings;
        $sql .= " `place` = '" . mysql_real_escape_string($headmod) . "', ";
    }
    if ($datauser['browser'] != $agn)
        $sql .= " `browser` = '" . mysql_real_escape_string($agn) . "', ";
    $totalonsite = $datauser['total_on_site'];
    if ($datauser['lastdate'] > (time() - 300))
        $totalonsite = $totalonsite + time() - $datauser['lastdate'];
    mysql_query("UPDATE `users` SET $sql
        `movings` = '$movings',
        `total_on_site` = '$totalonsite',
        `lastdate` = '" . time() . "'
        WHERE `id` = '$user_id'
    ");
} else {
    // Фиксируем местоположение гостей
    $movings = 0;
    $session = md5(core::$ip . core::$ip_via_proxy . core::$user_agent);
    $req = mysql_query("SELECT * FROM `cms_sessions` WHERE `session_id` = '$session' LIMIT 1");
    if (mysql_num_rows($req)) {
        // Если есть в базе, то обновляем данные
        $res = mysql_fetch_assoc($req);
        $movings = ++$res['movings'];
        if ($res['sestime'] < (time() - 300)) {
            $movings = 1;
            $sql .= " `sestime` = '" . time() . "', ";
        }
        if ($res['place'] != $headmod) {
            $sql .= " `place` = '" . mysql_real_escape_string($headmod) . "', ";
        }
        mysql_query("UPDATE `cms_sessions` SET $sql
            `movings` = '$movings',
            `lastdate` = '" . time() . "'
            WHERE `session_id` = '$session'
        ");
    } else {
        // Если еще небыло в базе, то добавляем запись
        mysql_query("INSERT INTO `cms_sessions` SET
            `session_id` = '" . $session . "',
            `ip` = '" . core::$ip . "',
            `ip_via_proxy` = '" . core::$ip_via_proxy . "',
            `browser` = '" . mysql_real_escape_string($agn) . "',
            `lastdate` = '" . time() . "',
            `sestime` = '" . time() . "',
            `place` = '" . mysql_real_escape_string($headmod) . "'
        ");
    }
}
echo '<div id="mp3player" style="display:none">
    <div id="jquery_jplayer_1" class="jp-jplayer"></div>

    <div id="jp_container_1">
        <div class="jp-gui ui-widget ui-widget-content ui-corner-all">

            <div class="jp-play ui-state-default ui-corner-all"><a href="javascript:;" class="jp-play ui-icon ui-icon-play" tabindex="1" title="play">play</a></div>
            <div class="jp-pause ui-state-default ui-corner-all"><a href="javascript:;" class="jp-pause ui-icon ui-icon-pause" tabindex="1" title="pause">pause</a></div>
            <div class="jp-current-time"></div>
            <div class="jp-progress-slider">
                <div class="jp-seek-bar" >
                    <div class="jp-play-bar"></div>
                </div>
            </div>
            <div class="jp-duration"></div>
            <!--<li class="jp-repeat-off ui-state-default ui-state-active ui-corner-all"><a href="javascript:;" class="jp-repeat-off ui-icon ui-icon-refresh" tabindex="1" title="repeat off">repeat off</a></li>-->
            <div class="jp-mute ui-state-default ui-corner-all" style="margin-right: 5px;margin-top: 1px;"><a href="javascript:;" class="jp-mute ui-icon ui-icon-volume-on" tabindex="1" title="mute">mute</a></div>
            <div class="jp-unmute ui-state-default ui-state-active ui-corner-all" style="margin-right: 5px;margin-top: 1px;"><a href="javascript:;" class="jp-unmute ui-icon ui-icon-volume-off" tabindex="1" title="unmute">unmute</a></div>
            <div class="jp-volume-slider"></div>
            <div class="jp-clearboth"></div>
        </div>
        <div class="jp-no-solution">
            <span>Update Required</span>
To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
        </div>
    </div>
</div>';

echo '<div class="maintxt">';

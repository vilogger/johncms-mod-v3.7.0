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

$mp = new mainpage();

/*
-----------------------------------------------------------------
Блок информации
-----------------------------------------------------------------
*/
echo $mp->news;


echo '<div class="phdr">Download</div>';
echo '<div class="list1">Code được Mod trên nền <span style="color: #9c27b0">JohnCMS 6.2.0</span>. Thực hiện Mod bởi <span style="color: #f44336">MrT98</span><br /><a href="/JohnCMS-620_Mod-V3.6.27.zip">Bản cập nhật V3.6.27</a></div>';

/*
-----------------------------------------------------------------
Phòng chát
-----------------------------------------------------------------
*/
    include('chatbox.php');

/*
-----------------------------------------------------------------
Bảng tin
-----------------------------------------------------------------
*/
    $reqbt = mysql_query("SELECT COUNT(*) FROM `news`");
    $totalbt = mysql_result($reqbt, 0);
    if($totalbt >= 1){
        echo '<div id="goto" class="phdr"><b>Bảng tin</b></div>';
        if ($rights >= 6)
            echo '<div class="topmenu"><a href="/news/index.php?do=add">' . $lng['add'] . '</a> | <a href="/news/index.php?do=clean">' . $lng['clear'] . '</a></div>';
        $reqbt = mysql_query("SELECT * FROM `news` ORDER BY `time` DESC LIMIT 5");
        $i = 0;
        while ($resbt = mysql_fetch_array($reqbt)) {
            echo '<div class="content">';
            echo '<span class="note">Chú ý</span> <a href="'.$home.'/news/'.$resbt['id'].'/'.$resbt['seo'].'.html">' . $resbt['name'] . '</a>';
            echo '</div>';
            ++$i;
        }
        if ($totalbt > 5) {
            echo '<div class="topmenu"><a href="/news">Các thông báo trước đó...</a></div>';
        }
    }

/*
-----------------------------------------------------------------
Thảo luận mới
-----------------------------------------------------------------
*/
    $lng_forum = core::load_lng('forum');
    echo '<div class="phdr"><b>Diễn Đàn - Thảo luận</b></div>';
    $tongbv = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `close` != '1'"), 0);
    $req = mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `close` != '1' ORDER BY `time` DESC LIMIT $start, $kmess");
    if (mysql_num_rows($req)) {
        for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
            $q3 = mysql_query("select `id`, `refid`, `text` from `forum` where type='r' and id='" . $res['refid'] . "' LIMIT 1");
            $razd = mysql_fetch_assoc($q3);
            $q4 = mysql_query("select `id`, `refid`, `text` from `forum` where type='f' and id='" . $razd['refid'] . "' LIMIT 1");
            $frm = mysql_fetch_assoc($q4);
            $nikuser = mysql_query("SELECT * FROM `forum` WHERE `type` = 'm' AND `close` != '1' AND `refid` = '" . $res['id'] . "'ORDER BY `time` DESC");
            $colmes1 = mysql_num_rows($nikuser);

            $cpg = ceil($colmes1 / $kmess);
            $nam = mysql_fetch_assoc($nikuser);
            if($res['vip']){
                echo '<div class="list1" style="background: #fffdcc url(/images/rosette.png) no-repeat right top;">';
            }else{
                echo '<div class="list1">';
            }
            // Значки
            $np = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_rdm` WHERE `time` >= '" . $res['time'] . "' AND `topic_id` = '" . $res['id'] . "' AND `user_id`='$user_id'"), 0);
            $icons = array(
                ($np ? (!$res['vip'] ? functions::image('op.gif') : '') : functions::image('np.gif')),
                ($res['vip'] ? functions::image('pt.gif') : ''),
                ($res['realid'] ? functions::image('rate.gif') : ''),
                ($res['edit'] ? functions::image('tz.gif') : '')
            );
            echo '<table style="padding: 0;border-spacing: 0;"><tr><td style="padding-right: 5px;">';
                echo '<img src="' . $home . '/avatar/'.$res['user_id'].'-24-48.png" width="48" height="48" alt="' . $res['from'] . '" />&#160;';
            echo '</td><td>';
            echo ($res['edit'] ? functions::image('tz.gif') : '').'<a href="'.$home.'/forum/' . $res['id'] . '/' . $res['seo'] . '.html" title="' . $res['text'] . '">' . $res['text'] . '</a>&#160;['.($cpg > 1 ? '<a href="'.$home.'/forum/' . $res['id'] . '/' . $res['seo'] . '_p' . $cpg . '.html#post'.$nam['id'].'">' . $colmes1 . '</a>' : '' . $colmes1 . '').'] (' . functions::nickcolor($nam['user_id']).')';
            echo '</td></tr></table>';
            echo '<div class="font-xs" style="text-align: right; color: gray; margin: -1px 0px 1px 1px;">' . functions::display_date($nam['time']) . '</div>';
            echo '</div>';
        }
    }else{
        echo '<div class="menu"><p>Không có bài viết</p></div>';
    }
    if ($tongbv > $kmess){
        echo '<div class="menu" style="overflow: auto;">' . functions::display_pagination('/index.php?', $start, $tongbv, $kmess) . '</div>';
    }

/*
-----------------------------------------------------------------
Diễn đàn
-----------------------------------------------------------------
*/
    $lng_forum = core::load_lng('forum');
    $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files`" . ($rights >= 7 ? '' : " WHERE `del` != '1'")), 0);

    echo '<div class="phdr"><b>' . $lng['forum'] . ' - Chuyên mục</b></div>' .
    '<div class="topmenu"><a href="'.$home.'/forum/timkiem.html">' . $lng['search'] . '</a> | <a href="'.$home.'/forum/index.php?act=files">' . $lng_forum['files_forum'] . '</a> <span class="red">(' . $count . ')</span></div>';
        $req = mysql_query("SELECT `id`, `text`, `soft`, `seo` FROM `forum` WHERE `type`='f' ORDER BY `realid`");
        $i = 0;
        while (($res = mysql_fetch_array($req)) !== false) {
            echo '<ul class="ulist">';
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='r' and `refid`='" . $res['id'] . "'"), 0);
            echo '<li><a href="'.$home.'/forum/' . $res['id'] . '/' . $res['seo'] . '.html" style="color: #000; font-size: 14px;"><strong>' . $res['text'] . '</strong></a></li>';
                $reqtl = mysql_query("SELECT `id`, `text`, `soft`, `edit`, `seo` FROM `forum` WHERE `type`='r' AND `refid`='".$res['id']."' ORDER BY `realid`");
                $totaltl = mysql_num_rows($reqtl);
                if ($totaltl) {
                    $tl = 0;
                    while (($restl = mysql_fetch_assoc($reqtl)) !== false) {
                        $coltem = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `refid` = '" . $restl['id'] . "'"), 0);
                        echo '<li style="margin-left: 12px;">'.functions::image('ddmenu.png', array('class' => 'icon-r3')).'<a href="'.$home.'/forum/' . $restl['id'] . '/' . $restl['seo'] . '.html">' . $restl['text'] . '</a> ('.$coltem.')</li>';
                        ++$tl;
                    }
                }
            echo '</ul>';
            ++$i;
        }
?>
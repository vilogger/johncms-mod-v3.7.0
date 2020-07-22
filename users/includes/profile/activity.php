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

/*
-----------------------------------------------------------------
История активности
-----------------------------------------------------------------
*/
$ten = html_entity_decode($user['name'], ENT_QUOTES, 'UTF-8');
$textl = functions::checkout($ten) . ': ' . $lng_profile['activity'];
require('../incfiles/head.php');
echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng_profile['activity'] . '</div>';
$menu = array(
    (!$mod ? '<b>' . $lng['messages'] . '</b>' : '<a href="profile.php?act=activity&amp;user=' . $user['id'] . '">' . $lng['messages'] . '</a>'),
    ($mod == 'topic' ? '<b>' . $lng['themes'] . '</b>' : '<a href="profile.php?act=activity&amp;mod=topic&amp;user=' . $user['id'] . '">' . $lng['themes'] . '</a>'),
    ($mod == 'comments' ? '<b>' . $lng['comments'] . '</b>' : '<a href="profile.php?act=activity&amp;mod=comments&amp;user=' . $user['id'] . '">' . $lng['comments'] . '</a>'),
);
echo '<div class="topmenu">' . functions::display_menu($menu) . '</div>' .
     '<div class="user"><p>' . functions::display_user($user, array('iphide' => 1,)) . '</p></div>';
switch ($mod) {
    case 'comments':
        /*
        -----------------------------------------------------------------
        Список сообщений в Гостевой
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `user_id` = '" . $user['id'] . "'" . ($rights >= 1 ? '' : " AND `adm` = '0'")), 0);
        echo '<div class="phdr"><b>' . $lng['comments'] . '</b></div>';
        if ($total > $kmess) echo '<div class="topmenu">' . functions::display_pagination('profile.php?act=activity&amp;mod=comments&amp;user=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>';
        $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = '" . $user['id'] . "'" . ($rights >= 1 ? '' : " AND `adm` = '0'") . " ORDER BY `id` DESC LIMIT $start, $kmess");
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') . functions::checkout($res['text'], 2, 1) . '<div class="sub">' .
                     '<span class="gray">(' . functions::display_date($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng_profile['guest_empty'] . '</p></div>';
        }
        break;

    case 'topic':
        /*
        -----------------------------------------------------------------
        Список тем Форума
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 't'" . ($rights >= 7 ? '' : " AND `close`!='1'")), 0);
        echo '<div class="phdr"><b>' . $lng['forum'] . '</b>: ' . $lng['themes'] . '</div>';
        if ($total > $kmess) echo '<div class="topmenu">' . functions::display_pagination('profile.php?act=activity&amp;mod=topic&amp;user=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>';
        $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 't'" . ($rights >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` DESC LIMIT $start, $kmess");
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                $post = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `refid` = '" . $res['id'] . "'" . ($rights >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` ASC LIMIT 1"));
                $section = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                $category = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $section['refid'] . "'"));
                $text = mb_substr($post['text'], 0, 300);
                $text = functions::checkout($text, 2, 1);
                echo '<div class="menu">' .
                     '<a href="' . $set['homeurl'] . '/forum/' . $res['id'] . '/' . $res['seo'] . '.html"><strong>' . $res['text'] . '</strong></a> <strong>tại</strong> <a href="' . $set['homeurl'] . '/forum/' . $section['id'] . '/' . $section['seo'] . '.html" style="color: #00ecb1"><strong>' . $section['text'] . '</strong></a>' .
                     '<div style="margin-top: 5px;">' . $text . '</div>' .
                     '<div style="font-size: x-small;margin-top: 5px;">' .
                     '<span class="gray"> <i class="fa fa-clock-o" style="font-size: 14px;"></i> ' . functions::display_date($res['time']) . '</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Список постов Форума
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 'm'" . ($rights >= 7 ? '' : " AND `close`!='1'")), 0);
        echo '<div class="phdr"><b>' . $lng['forum'] . '</b>: ' . $lng['messages'] . '</div>';
        if ($total > $kmess) echo '<div class="topmenu">' . functions::display_pagination('profile.php?act=activity&amp;user=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>';
        $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 'm' " . ($rights >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` DESC LIMIT $start, $kmess");
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                $topic = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                $section = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $topic['refid'] . "'"));
                $category = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $section['refid'] . "'"));
                $text = mb_substr($res['text'], 0, 300);
                $text = functions::checkout($text, 1, 1);
                echo '<div class="menu">' .
                     '<div style="padding: 3px 4px 0 4px;"><a href="' . $set['homeurl'] . '/forum/' . $topic['id'] . '/' . $topic['seo'] . '.html"><strong>' . $topic['text'] . '</strong></a> <strong>tại</strong> <a href="' . $set['homeurl'] . '/forum/' . $section['id'] . '/' . $section['seo'] . '.html" style="color: #00ecb1"><strong>' . $section['text'] . '</strong></a></div>' .
                     '<div style="margin: 5px 0 7px 4px;"><i class="fa fa-clock-o" style="font-size: 14px;"></i>&#160;<span class="gray" style="font-size: x-small;">' . functions::display_date($res['time']) . '</span></div>' . $text . '' .
                    '</div>'
                    .'<div class="list1" style="background-color: #fcfcfc;">' .
                     '<a href="' . $set['homeurl'] . '/forum/post-' . $res['id'] . '.html"> Tới bài viết</a></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<div class="topmenu">' . functions::display_pagination('profile.php?act=activity' . ($mod ? '&amp;mod=' . $mod : '') . '&amp;user=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>' .
         '<p><form action="profile.php?act=activity&amp;user=' . $user['id'] . ($mod ? '&amp;mod=' . $mod : '') . '" method="post">' .
         '<input type="text" name="page" size="2"/>' .
         '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
         '</form></p>';
}
?>
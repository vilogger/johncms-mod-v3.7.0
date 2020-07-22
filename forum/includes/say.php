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
Закрываем доступ для определенных ситуаций
-----------------------------------------------------------------
*/
if (!$id || !$user_id || isset($ban['1']) || isset($ban['11']) || (!core::$user_rights && $set['mod_forum'] == 3)) {
    require('../incfiles/head.php');
    echo functions::display_error($lng['access_forbidden']);
    require('../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Вспомогательная Функция обработки ссылок форума
-----------------------------------------------------------------
*/
function forum_link($m)
{
    global $set;
    if (!isset($m[3])) {
        return '[url=' . $m[1] . ']' . $m[2] . '[/url]';
    } else {
        $p = parse_url($m[3]);
        if ('http://' . $p['host'] . (isset($p['path']) ? $p['path'] : '') . '?id=' == $set['homeurl'] . '/forum/index.php?id=') {
            $thid = abs(intval(preg_replace('/(.*?)id=/si', '', $m[3])));
            $req = mysql_query("SELECT `text` FROM `forum` WHERE `id`= '$thid' AND `type` = 't' AND `close` != '1'");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                $name = strtr($res['text'], array(
                    '&quot;' => '',
                    '&amp;'  => '',
                    '&lt;'   => '',
                    '&gt;'   => '',
                    '&#039;' => '',
                    '['      => '',
                    ']'      => ''
                ));
                if (mb_strlen($name) > 40)
                    $name = mb_substr($name, 0, 40) . '...';

                return '[url=' . $m[3] . ']' . $name . '[/url]';
            } else {
                return $m[3];
            }
        } else
            return $m[3];
    }
}

// Проверка на флуд
$flood = functions::antiflood();
if ($flood) {
    require('../incfiles/head.php');
    echo functions::display_error($lng['error_flood'] . ' ' . $flood . $lng['sec'], '<a href="/forum/index.html">' . $lng['back'] . '</a>');
    require('../incfiles/end.php');
    exit;
}

$headmod = 'forum,' . $id . ',1';
if ($parser->knownbrowser) {
    if ($parser->browsername) {
        $agn1 = $parser->browsername;
    }else{
        $agn1 = $parser->fullname;
    }
}else{
    $agn1 = strtok($agn, ' ');
}
$type = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id'");
$type1 = mysql_fetch_assoc($type);
switch ($type1['type']) {
    case 't':
        /*
        -----------------------------------------------------------------
        Добавление простого сообщения
        -----------------------------------------------------------------
        */
        if (($type1['edit'] == 1 || $type1['close'] == 1) && $rights < 7) {
            // Проверка, закрыта ли тема
            require('../incfiles/head.php');
            echo functions::display_error($lng_forum['error_topic_closed'], '<a href="/forum/' . $id . '/'.$type1['seo'].'.html">' . $lng['back'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }

        $googlemap = isset($_POST['google-map']) ? mb_substr(trim($_POST['google-map']), 0, 50) : '';

        $msg = isset($_POST['msg']) ? functions::checkin(trim($_POST['msg'])) : '';
        if (isset($_POST['msgtrans']))
            $msg = functions::trans($msg);
        //Обрабатываем ссылки
        $msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);

        if (!empty($googlemap) && mb_strlen($googlemap) >= 4){
            $googlemap = functions::checkin($googlemap);
            $msg = $msg.' [map]'.$googlemap.'[/map]';
        }

            $soundcloud_uri = isset($_POST['soundcloud_uri']) ? $_POST['soundcloud_uri'] : false;
        if ($soundcloud_uri){
            $msg = $msg.' [soundcloud]'.$soundcloud_uri.'[/soundcloud]';
        }
        $youtube_id = isset($_POST['youtube_video_id']) ? $_POST['youtube_video_id'] : false;
        if ($youtube_id){
            $msg = $msg.' https://www.youtube.com/watch?v='.$youtube_id;
        }

        if (isset($_POST['submit'])
            && !empty($msg)
            && isset($_POST['token'])
            && isset($_SESSION['token'])
            && $_POST['token'] == $_SESSION['token']
        ) {
            // Проверяем на минимальную длину
            if (mb_strlen($msg) < 4) {
                require('../incfiles/head.php');
                echo functions::display_error($lng['error_message_short'], '<a href="/forum/' . $id . '/'.$type1['seo'].'.html">' . $lng['back'] . '</a>');
                require('../incfiles/end.php');
                exit;
            }
            // Проверяем, не повторяется ли сообщение?
            $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '$user_id' AND `type` = 'm' ORDER BY `time` DESC");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                if ($msg == $res['text']) {
                    require('../incfiles/head.php');
                    echo functions::display_error($lng['error_message_exists'], '<a href="/forum/' . $id . '/'.$type1['seo'].'_start' . $start . '.html">' . $lng['back'] . '</a>');
                    require('../incfiles/end.php');
                    exit;
                }
            }
            // Удаляем фильтр, если он был
            if (isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == $id) {
                unset($_SESSION['fsort_id']);
                unset($_SESSION['fsort_users']);
            }

            unset($_SESSION['token']);

            // Добавляем сообщение в базу
            mysql_query("INSERT INTO `forum` SET
                `refid` = '$id',
                `type` = 'm' ,
                `time` = '" . time() . "',
                `user_id` = '$user_id',
                `from` = '$login',
                `ip` = '" . core::$ip . "',
                `ip_via_proxy` = '" . core::$ip_via_proxy . "',
                `soft` = '" . mysql_real_escape_string($agn1) . "',
                `text` = '" . mysql_real_escape_string($msg) . "',
                `edit` = '',
                `curators` = ''
            ");
            $fadd = mysql_insert_id();
            if(strlen($msg) > 45) {
                $vbcat = mb_substr($msg, 0, 45).'....';
            } else {
                $vbcat = $msg;
            }
            ///mod tag thanh vien
            $exists = array();
            if(preg_match('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $msg)){
                preg_match_all('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $msg, $arr);
                foreach($arr[1] as $tag){
                    $db = mysql_fetch_array(mysql_query("select * from users where name='$tag'"));
                    if(mysql_num_rows(mysql_query("select * from users where name='$tag'"))==0 || $db['id'] == $user_id || $db['id'] == $type1['user_id']){
                    } else if(isset($exists[intval($db['id'])]) == false) {
                        $exists[intval($db['id'])] = true;
                        $tongpge = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $id . "'" . ($db['rights'] >= 7 ? '' : " AND `close` != '1'")),0);
                        $sotrangpge = ceil($tongpge / $kmess);
                        mysql_query ("INSERT INTO `cms_mail` SET
                        `user_id` = '$user_id',
                        `from_id` = '" .$db['id']."',
                        `them`='3',
                        `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã nhắc đến bạn trong bài viết ".functions::sex($user_id)."tại chủ đề: [url=".$home."/forum/".$id."/".$type1['seo']."_p".$sotrangpge.".html#post".$fadd."]".addslashes($type1['text'])."[/url] ( ".addslashes($vbcat)." )',
                        `sys`='1',
                        `time` = '"  . time() . "'
                        " );
                    }
                }
            }
            if(preg_match('#\[\@(.+?)\]#s', $msg)){
                preg_match_all('#\[\@(.+?)\]#s', $msg, $arr);
                foreach($arr[1] as $tag){
                    $var_n = functions::check(trim($tag));
                    $db = mysql_fetch_array(mysql_query("select * from users where name='$var_n'"));
                    if(mysql_num_rows(mysql_query("select * from users where name='$var_n'"))==0 || $db['id'] == $user_id || $db['id'] == $type1['user_id']){
                    } else if(isset($exists[intval($db['id'])]) == false) {
                        $exists[intval($db['id'])] = true;
                        $tongpge = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $id . "'" . ($db['rights'] >= 7 ? '' : " AND `close` != '1'")),0);
                        $sotrangpge = ceil($tongpge / $kmess);
                        mysql_query ("INSERT INTO `cms_mail` SET
                        `user_id` = '$user_id',
                        `from_id` = '" .$db['id']."',
                        `them`='3',
                        `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã nhắc đến bạn trong bài viết ".functions::sex($user_id)."tại chủ đề: [url=".$home."/forum/".$id."/".$type1['seo']."_p".$sotrangpge.".html#post".$fadd."]".addslashes($type1['text'])."[/url] ( ".addslashes($vbcat)." )',
                        `sys`='1',
                        `time` = '"  . time() . "'
                        " );
                    }
                }
            }
            ///ket thuc mod tag thanh vien
            ///mod thong bao binh luan
            $reqp = mysql_query("SELECT DISTINCT `user_id` FROM `forum` WHERE `refid`='$id' AND `user_id` != '$user_id'");
            while ($resp = mysql_fetch_array($reqp)) {
                $typei = mysql_query("SELECT `rights` FROM `users` WHERE `id` = '".$resp['user_id']."'");
                $typei1 = mysql_fetch_assoc($typei);
                $allpge = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $id . "'" . ($typei1['rights'] >= 7 ? '' : " AND `close` != '1'")), 0);
                $alpage = ceil($allpge/$kmess);
                mysql_query ("INSERT INTO `cms_mail` SET
                `user_id` = '$user_id',
                `from_id` = '".$resp['user_id']."',
                `them`='6',
                `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã đăng một bài viết trong chủ đề có mặt bạn: [url=".$home."/forum/".$id."/".$type1['seo']."_p".$alpage.".html#post".$fadd."]".addslashes($type1['text'])."[/url] ( ".addslashes($vbcat)." )',
                `sys`='1',
                `time` = '"  . time() . "'" );
            }
            ///ket thuc mod thong bao binh luan
            // Обновляем время топика
            mysql_query("UPDATE `forum` SET
                `time` = '" . time() . "'
                WHERE `id` = '$id'
            ");
            // Обновляем статистику юзера
            mysql_query("UPDATE `users` SET
                `postforum`='" . ($datauser['postforum'] + 1) . "',
                `lastpost` = '" . time() . "'
                WHERE `id` = '$user_id'
            ");
            // Вычисляем, на какую страницу попадает добавляемый пост
            $page = $set_forum['upfp'] ? 1 : ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '$id'" . ($rights >= 7 ? '' : " AND `close` != '1'")), 0) / $kmess);
            if (isset($_POST['addfiles'])) {
                header("Location: index.php?id=$fadd&act=addfile");
            } else {
                header('Location: /forum/'.$id.'/'.$type1['seo'].'_p'.$page.'.html#post'.$fadd.'');
            }
            exit;
        } else {
            require('../incfiles/head.php');
            if ($datauser['postforum'] == 0) {
                if (!isset($_GET['yes'])) {
                    $lng_faq = core::load_lng('faq');
                    echo '<p>' . $lng_faq['forum_rules_text'] . '</p>' .
                        '<p><a href="index.php?act=say&amp;id=' . $id . '&amp;yes">' . $lng_forum['agree'] . '</a> | ' .
                        '<a href="/forum/'.$id.'/'.$type1['seo'].'.html">' . $lng_forum['not_agree'] . '</a></p>';
                    require('../incfiles/end.php');
                    exit;
                }
            }
            $msg_pre = functions::checkout($msg, 1, 1);
            if ($set_user['smileys']) {
                $msg_pre = functions::smileys($msg_pre, $datauser['rights'] ? 1 : 0);
            }
            $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote"><div class="content">\1</div></div>', $msg_pre);
            echo '<div class="phdr"><b>' . $lng_forum['topic'] . ':</b> ' . $type1['text'] . '</div>';
            if ($msg && !isset($_POST['submit'])) {
                echo '<div class="list1">' . functions::display_user($datauser, array('iphide' => 1, 'header' => '<span class="gray">(' . functions::display_date(time()) . ')</span>', 'body' => $msg_pre)) . '</div>';
            }
            echo '<div class="story-publisher-box"><form name="form" action="index.php?act=say&amp;id=' . $id . '&amp;start=' . $start . '" method="post"><div class="gmenu">' .
                '<p><h3>' . $lng_forum['post'] . '</h3>';
            echo '</p><p>' . bbcode::auto_bb('form', 'msg');
            echo '<textarea  class="box-edit" name="msg">' . (empty($msg) ? '' : functions::checkout($msg)) . '</textarea></p>' .
                '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . $lng_forum['add_file'];
            if ($set_user['translit']) {
                echo '<br /><input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . $lng['translit'];
            }
            $token = mt_rand(1000, 100000);
            $_SESSION['token'] = $token;
            echo '</p><p>' .
                '<input type="submit" name="submit" value="' . $lng['sent'] . '" style="width: 107px; cursor: pointer"/> ' .
                ($set_forum['preview'] ? '<input type="submit" value="' . $lng['preview'] . '" style="width: 107px; cursor: pointer"/>' : '') .
                '<input type="hidden" name="token" value="' . $token . '"/>' .
                '</p></div></form></div>';
        }

        echo '<div class="phdr"><a href="../pages/faq.php?act=trans">' . $lng['translit'] . '</a> | ' .
            '<a href="../pages/faq.php?act=smileys">' . $lng['smileys'] . '</a></div>' .
            '<p><a href="/forum/' . $id . '/'.$type1['seo'].'_start' . $start . '.html">' . $lng['back'] . '</a></p>';
        break;

    case 'm':
        /*
        -----------------------------------------------------------------
        Добавление сообщения с цитированием поста
        -----------------------------------------------------------------
        */
        $th = $type1['refid'];
        $th2 = mysql_query("SELECT * FROM `forum` WHERE `id` = '$th'");
        $th1 = mysql_fetch_array($th2);
        if (($th1['edit'] == 1 || $th1['close'] == 1) && $rights < 7) {
            require('../incfiles/head.php');
            echo functions::display_error($lng_forum['error_topic_closed'], '<a href="/forum/' . $th1['id'] . '/'.$th1['seo'].'.html">' . $lng['back'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        if ($type1['user_id'] == $user_id) {
            require('../incfiles/head.php');
            echo functions::display_error('Нельзя отвечать на свое же сообщение', '<a href="/forum/' . $th1['id'] . '/'.$th1['seo'].'.html">' . $lng['back'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        $shift = (core::$system_set['timeshift'] + core::$user_set['timeshift']) * 3600;
        $vr = date("d.m.Y / H:i", $type1['time'] + $shift);
        $msg = isset($_POST['msg']) ? functions::checkin(trim($_POST['msg'])) : '';
        $vcat = isset($_POST['msg']) ? functions::checkin(trim($_POST['msg'])) : '';
        $txt = isset($_POST['txt']) ? intval($_POST['txt']) : FALSE;
        if (isset($_POST['msgtrans'])) {
            $msg = functions::trans($msg);
        }

        $googlemap = isset($_POST['google-map']) ? mb_substr(trim($_POST['google-map']), 0, 50) : '';
        if (!empty($googlemap) && mb_strlen($googlemap) >= 4){
            $googlemap = functions::checkin($googlemap);
            $msg = $msg.' [map]'.$googlemap.'[/map]';
        }
            $soundcloud_uri = isset($_POST['soundcloud_uri']) ? $_POST['soundcloud_uri'] : false;
        if ($soundcloud_uri){
            $msg = $msg.' [soundcloud]'.$soundcloud_uri.'[/soundcloud]';
        }
        $youtube_id = isset($_POST['youtube_video_id']) ? $_POST['youtube_video_id'] : false;
        if ($youtube_id){
            $msg = $msg.' https://www.youtube.com/watch?v='.$youtube_id;
        }

        if (!empty($_POST['citata'])) {
            // Если была цитата, форматируем ее и обрабатываем
            $citata = isset($_POST['citata']) ? trim($_POST['citata']) : '';
            $citata = bbcode::notags($citata);
            $citata = preg_replace('#\[c\](.*?)\[/c\]#si', '', $citata);
            $citata = mb_substr($citata, 0, 200);
            $tp = date("d.m.Y H:i", $type1['time']);
            $msg = '[c][cnick]' . $type1['user_id'] . '[/cnick]-[stime]' . $tp . '[/stime]-' . $type1['id'] . '- ' . $citata . ' [/c]' . $msg;
        } elseif (isset($_POST['txt'])) {
            // Если был ответ, обрабатываем реплику
            switch ($txt) {
                case 2:
                    $repl = $type1['from'] . ', ' . $lng_forum['reply_1'] . ', ';
                    break;

                case 3:
                    $repl = $type1['from'] . ', ' . $lng_forum['reply_2'] . ' ([url=' . $set['homeurl'] . '/forum/post-' . $type1['id'] . '.html]' . $vr . '[/url]) ' . $lng_forum['reply_3'] . ', ';
                    break;

                case 4:
                    $repl = $type1['from'] . ', ' . $lng_forum['reply_4'] . ' ';
                    break;

                default :
                    $repl = $type1['from'] . ', ';
            }
            $msg = $repl . ' ' . $msg;
        }
        //Обрабатываем ссылки
        $msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);
        if (isset($_POST['submit'])
            && isset($_POST['token'])
            && isset($_SESSION['token'])
            && $_POST['token'] == $_SESSION['token']
        ) {
            if (empty($msg)) {
                require('../incfiles/head.php');
                echo functions::display_error($lng['error_empty_message'], '<a href="index.php?act=say&amp;id=' . $th . (isset($_GET['cyt']) ? '&amp;cyt' : '') . '">' . $lng['repeat'] . '</a>');
                require('../incfiles/end.php');
                exit;
            }
            // Проверяем на минимальную длину
            if (mb_strlen($msg) < 4) {
                require('../incfiles/head.php');
                echo functions::display_error($lng['error_message_short'], '<a href="/forum/' . $id . '/'.$type1['seo'].'.html">' . $lng['back'] . '</a>');
                require('../incfiles/end.php');
                exit;
            }
            // Проверяем, не повторяется ли сообщение?
            $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '$user_id' AND `type` = 'm' ORDER BY `time` DESC LIMIT 1");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                if ($msg == $res['text']) {
                    require('../incfiles/head.php');
                    echo functions::display_error($lng['error_message_exists'], '<a href="/forum/' . $th . '/'.$th1['seo'].'_start' . $start . '.html">' . $lng['back'] . '</a>');
                    require('../incfiles/end.php');
                    exit;
                }
            }
            // Удаляем фильтр, если он был
            if (isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == $th) {
                unset($_SESSION['fsort_id']);
                unset($_SESSION['fsort_users']);
            }

            unset($_SESSION['token']);

            // Добавляем сообщение в базу
            mysql_query("INSERT INTO `forum` SET
                `refid` = '$th',
                `type` = 'm',
                `time` = '" . time() . "',
                `user_id` = '$user_id',
                `from` = '$login',
                `ip` = '" . core::$ip . "',
                `ip_via_proxy` = '" . core::$ip_via_proxy . "',
                `soft` = '" . mysql_real_escape_string($agn1) . "',
                `text` = '" . mysql_real_escape_string($msg) . "',
                `edit` = '',
                `curators` = ''
            ");
            $fadd = mysql_insert_id();
            if(strlen($vcat) > 45) {
                $vbcat = mb_substr($vcat, 0, 45).'....';
            } else {
                $vbcat = $vcat;
            }
            $typei = mysql_query("SELECT `rights` FROM `users` WHERE `id` = '".$type1['user_id']."'");
            $typei1 = mysql_fetch_assoc($typei);
            // mod tag thanh vien
            $exists = array();
            if(preg_match('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $vcat)){
                preg_match_all('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $vcat, $arr);
                foreach($arr[1] as $tag){
                    $db = mysql_fetch_array(mysql_query("select * from users where name='$tag'"));
                    if(mysql_num_rows(mysql_query("select * from users where name='$tag'"))==0 || $db['id'] == $user_id || $db['id'] == $type1['user_id']){
                    } else if(isset($exists[intval($db['id'])]) == false) {
                        $exists[intval($db['id'])] = true;
                        $sobai = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `close` != '1' AND `refid` = '" . $th . "'" . ($typei1['rights'] >= 7 ? '' : " AND `close` != '1'")),0);
                        $sopage = ceil($sobai/$kmess);
                        mysql_query ("INSERT INTO `cms_mail` SET
                        `user_id`='" .$user_id."',
                        `from_id` = '" .$db['id']."',
                        `them`='18',
                        `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã nhắc đến bạn trong bài viết ".functions::sex($user_id)."tại chủ đề: [url=".$home."/forum/".$th."/".$th1['seo'].".html#post".$fadd."]".addslashes($th1['text'])."[/url] ( ".addslashes($vbcat)." )',
                        `sys`='1',
                        `time` = '"  . time() . "'
                        " );
                    }
                }
            }
            // ket thuc mod tag thanh vien
            // mod thong bao trich dan
            $tong = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $th . "'" . ($typei1['rights'] >= 7 ? '' : " AND `close` != '1'")), 0);
            $sotrang = ceil($tong / $kmess);
            mysql_query("INSERT INTO `cms_mail` SET
                `user_id` = '$user_id',
                `from_id` = '".$type1['user_id']."',
                `them`='5',
                `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã trả lời bài viết của bạn trong chủ đề: [url=".$home."/forum/".$th."/".$th1['seo']."_p".$sotrang.".html#post".$fadd."]".addslashes($th1['text'])."[/url] ( ".addslashes($vbcat)." )',
                `sys`='1',
                `time` = '" . time() . "'
            ");
            // ket thuc thong bao trich dan
            // Обновляем время топика
            mysql_query("UPDATE `forum`
                SET `time` = '" . time() . "'
                WHERE `id` = '$th'
            ");
            // Обновляем статистику юзера
            mysql_query("UPDATE `users` SET
                `postforum`='" . ($datauser['postforum'] + 1) . "',
                `lastpost` = '" . time() . "'
                WHERE `id` = '$user_id'
            ");
            // Вычисляем, на какую страницу попадает добавляемый пост
            $page = $set_forum['upfp'] ? 1 : ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '$th'" . ($rights >= 7 ? '' : " AND `close` != '1'")), 0) / $kmess);
            if (isset($_POST['addfiles'])) {
                header("Location: index.php?id=$fadd&act=addfile");
            } else {
                header('Location: /forum/'.$th.'/'.$th1['seo'].'_p'.$page.'.html#post'.$fadd.'');
            }
            exit;
        } else {
            $textl = $lng['forum'];
            require('../incfiles/head.php');
            $qt = " $type1[text]";
            if (($datauser['postforum'] == "" || $datauser['postforum'] == 0)) {
                if (!isset($_GET['yes'])) {
                    $lng_faq = core::load_lng('faq');
                    echo '<p>' . $lng_faq['forum_rules_text'] . '</p>';
                    echo '<p><a href="index.php?act=say&amp;id=' . $id . '&amp;yes&amp;cyt">' . $lng_forum['agree'] . '</a> | <a href="/forum/' . $type1['refid'] . '/' . $th1['seo'] . '.html">' . $lng_forum['not_agree'] . '</a></p>';
                    require('../incfiles/end.php');
                    exit;
                }
            }
            $msg_pre = functions::checkout($msg, 1, 1);
            if ($set_user['smileys']) {
                $msg_pre = functions::smileys($msg_pre, $datauser['rights'] ? 1 : 0);
            }
            $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote"><div class="content">\1</div></div>', $msg_pre);
            echo '<div class="phdr"><b>' . $lng_forum['topic'] . ':</b> ' . $th1['text'] . '</div>';
            $qt = str_replace("<br/>", "\r\n", $qt);
            $qt = trim(preg_replace('#\[c\](.*?)\[/c\]#si', '', $qt));
            $qt = functions::checkout($qt, 0, 2);
            if (!empty($msg) && !isset($_POST['submit'])) {
                echo '<div class="list1">' . functions::display_user($datauser, array('iphide' => 1, 'header' => '<span class="gray">(' . functions::display_date(time()) . ')</span>', 'body' => $msg_pre)) . '</div>';
            }
            echo '<div class="story-publisher-box"><form name="form" action="index.php?act=say&amp;id=' . $id . '&amp;start=' . $start . (isset($_GET['cyt']) ? '&amp;cyt' : '') . '" method="post"><div class="gmenu">';
            if (isset($_GET['cyt'])) {
                // Форма с цитатой
                echo '<p><b>' . $type1['from'] . '</b> <span class="gray">(' . $vr . ')</span></p>' .
                    '<p><h3>' . $lng_forum['cytate'] . '</h3><br />' .
                    '<textarea style="height: 100px;" name="citata">' . (empty($_POST['citata']) ? $qt : functions::checkout($_POST['citata'])) . '</textarea>' .
                    '<br /><small>' . $lng_forum['cytate_help'] . '</small></p>';
            } else {
                // Форма с репликой
                echo '<p><h3>' . $lng_forum['reference'] . '</h3><br />' .
                    '<input type="radio" value="0" ' . (!$txt ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type1['from'] . '</b>,<br />' .
                    '<input type="radio" value="2" ' . ($txt == 2 ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type1['from'] . '</b>, ' . $lng_forum['reply_1'] . ',<br />' .
                    '<input type="radio" value="3" ' . ($txt == 3 ? 'checked="checked"'
                        : '') . ' name="txt" />&#160;<b>' . $type1['from'] . '</b>, ' . $lng_forum['reply_2'] . ' (<a href="index.php?act=post&amp;id=' . $type1['id'] . '">' . $vr . '</a>) ' . $lng_forum['reply_3'] . ',<br />' .
                    '<input type="radio" value="4" ' . ($txt == 4 ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type1['from'] . '</b>, ' . $lng_forum['reply_4'] . '</p>';
            }
            echo '<p><h3>' . $lng_forum['post'] . '</h3><br />';
            echo '</p><p>' . bbcode::auto_bb('form', 'msg');
            echo '<textarea class="box-edit" name="msg">' . (empty($msg) ? '' : functions::checkout($msg)) . '</textarea></p>' .
                '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . $lng_forum['add_file'];
            if ($set_user['translit']) {
                echo '<br /><input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . $lng['translit'];
            }
            $token = mt_rand(1000, 100000);
            $_SESSION['token'] = $token;
            echo '</p><p><input type="submit" name="submit" value="' . $lng['sent'] . '" style="width: 107px; cursor: pointer;"/> ' .
                ($set_forum['preview'] ? '<input type="submit" value="' . $lng['preview'] . '" style="width: 107px; cursor: pointer;"/>' : '') .
                '<input type="hidden" name="token" value="' . $token . '"/>' .
                '</p></div></form></div>';
        }
        echo '<div class="phdr"><a href="../pages/faq.php?act=trans">' . $lng['translit'] . '</a> | ' .
            '<a href="../pages/faq.php?act=smileys">' . $lng['smileys'] . '</a></div>' .
            '<p><a href="/forum/' . $type1['refid'] . '/' . $th1['seo'] . '_start' . $start . '.html">' . $lng['back'] . '</a></p>';
        break;

    default:
        require('../incfiles/head.php');
        echo functions::display_error($lng_forum['error_topic_deleted'], '<a href="index.html">' . $lng['to_forum'] . '</a>');
        require('../incfiles/end.php');
}
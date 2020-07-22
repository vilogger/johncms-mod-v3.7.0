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
if ($parser->knownbrowser) {
    if ($parser->browsername) {
        $MyBrowser = $parser->browsername;
    }else{
        $MyBrowser = $parser->fullname;
    }
}else{
    $MyBrowser = strtok($agn, ' ');
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
    echo functions::display_error($lng['error_flood'] . ' ' . $flood . $lng['sec'] . ', <a href="index.html">' . $lng['back'] . '</a>');
    require('../incfiles/end.php');
    exit;
}

$req_r = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id' AND `type` = 'r' LIMIT 1");
if (!mysql_num_rows($req_r)) {
    require('../incfiles/head.php');
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}
$res_r = mysql_fetch_assoc($req_r);
$seo = addslashes(functions::seourl(mb_substr(trim($_POST['th']), 0, 100)));
$th = isset($_POST['th']) ? functions::check(mb_substr(trim($_POST['th']), 0, 100)) : '';
$msg = isset($_POST['msg']) ? functions::checkin(trim($_POST['msg'])) : '';
$tags = isset($_POST['tags']) ? functions::checkin(trim($_POST['tags'])) : '';
if (isset($_POST['msgtrans'])) {
    $th = functions::trans($th);
    $msg = functions::trans($msg);
}
$msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);

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

if (isset($_POST['submit'])
    && isset($_POST['token'])
    && isset($_SESSION['token'])
    && $_POST['token'] == $_SESSION['token']
) {
    $error = array();
    if (empty($th))
        $error[] = $lng_forum['error_topic_name'];
    if (mb_strlen($th) < 2)
        $error[] = $lng_forum['error_topic_name_lenght'];
    if (empty($msg))
        $error[] = $lng['error_empty_message'];
    if (mb_strlen($msg) < 4)
        $error[] = $lng['error_message_short'];
    if (!$error) {
        $msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);
        // Прверяем, есть ли уже такая тема в текущем разделе?
        if (mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `refid` = '$id' AND `text` = '$th'"), 0) > 0)
            $error[] = $lng_forum['error_topic_exists'];
        // Проверяем, не повторяется ли сообщение?
        $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '$user_id' AND `type` = 'm' ORDER BY `time` DESC");
        if (mysql_num_rows($req) > 0) {
            $res = mysql_fetch_array($req);
            if ($msg == $res['text'])
                $error[] = $lng['error_message_exists'];
        }
    }
    if (!$error) {
        unset($_SESSION['token']);

        // Если задано в настройках, то назначаем топикстартера куратором
        $curator = $res_r['edit'] == 1 ? serialize(array($user_id => $login)) : '';

        // Добавляем тему
        mysql_query("INSERT INTO `forum` SET
            `refid` = '$id',
            `type` = 't',
            `time` = '" . time() . "',
            `user_id` = '$user_id',
            `from` = '$login',
            `text` = '$th',
            `tags` = '" . mysql_real_escape_string($tags) . "',
            `soft` = '',
            `edit` = '',
            `seo` = '".addslashes(functions::seourl(mb_substr(trim($_POST['th']), 0, 100)))."',
            `curators` = '$curator'
        ");
        $rid = mysql_insert_id();

        // Добавляем текст поста
        mysql_query("INSERT INTO `forum` SET
            `refid` = '$rid',
            `type` = 'm',
            `time` = '" . time() . "',
            `user_id` = '$user_id',
            `from` = '$login',
            `ip` = '" . core::$ip . "',
            `ip_via_proxy` = '" . core::$ip_via_proxy . "',
            `soft` = '" . mysql_real_escape_string($MyBrowser) . "',
            `text` = '" . mysql_real_escape_string($msg) . "',
            `edit` = '',
            `curators` = ''
        ");

        $postid = mysql_insert_id();

        // Записываем счетчик постов юзера
        ///mod tag thanh vien
        if(strlen($msg) > 45) {
            $vbcat = mb_substr($msg, 0, 45).'....';
        } else {
            $vbcat = $msg;
        }
        $exists = array();
        if(preg_match('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $msg)){
            preg_match_all('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $msg, $arr);
            foreach($arr[1] as $tag){
                $db = mysql_fetch_array(mysql_query("select * from users where name='{$tag}'"));
                if(mysql_num_rows(mysql_query("select * from users where name='{$tag}'"))==0 || $db['id']==$user_id){
                } else if(isset($exists[intval($db['id'])]) == false) {
                        $exists[intval($db['id'])] = true;
                        mysql_query ("INSERT INTO `cms_mail` SET
                        `user_id` = '$user_id',
                        `from_id` = '" .$db['id']."',
                        `them`='12',
                        `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã nhắc đến bạn trong chủ đề: [url=".$home."/forum/".$rid."/".$seo.".html#post".$postid."]".$th."[/url]  ".functions::sex($user_id)."[br] ( ".addslashes($vbcat)." )',
                        `sys`='1',
                        `time` = '"  . time() . "'
                        " );
                }
            }
        }
        ///ket thuc mod tag thanh vien
        $fpst = $datauser['postforum'] + 1;
        mysql_query("UPDATE `users` SET
            `postforum` = '$fpst',
            `lastpost` = '" . time() . "'
            WHERE `id` = '$user_id'
        ");

        // Ставим метку о прочтении
        mysql_query("INSERT INTO `cms_forum_rdm` SET
            `topic_id`='$rid',
            `user_id`='$user_id',
            `time`='" . time() . "'
        ");

        if ($_POST['addfiles'] == 1) {
            header("Location: index.php?id=$postid&act=addfile");
        } else {
            header('Location: /forum/'.$rid.'/'.$seo.'.html#post'.$postid.'');
        }
    } else {
        // Выводим сообщение об ошибке
        require('../incfiles/head.php');
        echo functions::display_error($error, '<a href="index.php?act=nt&amp;id=' . $id . '">' . $lng['repeat'] . '</a>');
        require('../incfiles/end.php');
        exit;
    }
} else {
    $req_c = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res_r['refid'] . "'");
    $res_c = mysql_fetch_assoc($req_c);
    require('../incfiles/head.php');
    if ($datauser['postforum'] == 0) {
        if (!isset($_GET['yes'])) {
            $lng_faq = core::load_lng('faq');
            echo '<p>' . $lng_faq['forum_rules_text'] . '</p>';
            echo '<p><a href="index.php?act=nt&amp;id=' . $id . '&amp;yes">' . $lng_forum['agree'] . '</a> | <a href="/forum/' . $id . '/'.$res_r['seo'].'.html">' . $lng_forum['not_agree'] . '</a></p>';
            require('../incfiles/end.php');
            exit;
        }
    }
    $msg_pre = functions::checkout($msg, 1, 1);
    if ($set_user['smileys'])
        $msg_pre = functions::smileys($msg_pre, $datauser['rights'] ? 1 : 0);
    $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
    echo '<div class="phdr"><a href="/forum/' . $id . '/'.$res_r['seo'].'.html"><b>' . $lng['forum'] . '</b></a> | ' . $lng_forum['new_topic'] . '</div>';
    if ($msg && $th && !isset($_POST['submit']))
        echo '<div class="list1">' . functions::image('op.gif') . '<span style="font-weight: bold">' . $th . '</span></div>' .
            '<div class="list2">' . functions::display_user($datauser, array('iphide' => 1, 'header' => '<span class="gray">(' . functions::display_date(time()) . ')</span>', 'body' => $msg_pre)) . '</div>';
    echo '<div class="story-publisher-box"><form name="form" action="index.php?act=nt&amp;id=' . $id . '" method="post">' .
        '<div class="gmenu">' .
        '<p><h3>' . $lng['section'] . '</h3><br />' .
        '<a href="/forum/' . $res_c['id'] . '/'.$res_c['seo'].'.html">' . $res_c['text'] . '</a> | <a href="/forum/' . $res_r['id'] . '/'.$res_r['seo'].'.html">' . $res_r['text'] . '</a></p>' .
        '<p><h3>' . $lng_forum['new_topic_name'] . '</h3><br />' .
        '<input type="text" size="20" maxlength="100" name="th" value="' . $th . '"/></p>' .
        '<p><h3>' . $lng_forum['post'] . '</h3>';
    echo '</p><p>' . bbcode::auto_bb('form', 'msg');
    echo '<textarea class="box-edit" name="msg">' . (isset($_POST['msg']) ? functions::checkout($_POST['msg']) : '') . '</textarea></p>' .
        '<p><h3>Tags:</h3><br /><input type="text" name="tags" value="' . (isset($_POST['tags']) ? functions::checkout($_POST['tags']) : '') . '" /> </p>' .
        '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . $lng_forum['add_file'];
    if ($set_user['translit']) {
        echo '<br /><input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . $lng['translit'];
    }
    $token = mt_rand(1000, 100000);
    $_SESSION['token'] = $token;
    echo '</p><p><input type="submit" name="submit" value="' . $lng['save'] . '" style="width: 107px; cursor: pointer;"/> ' .
        ($set_forum['preview'] ? '<input type="submit" value="' . $lng['preview'] . '" style="width: 107px; cursor: pointer;"/>' : '') .
        '<input type="hidden" name="token" value="' . $token . '"/>' .
        '</p></div></form></div>' .
        '<div class="phdr"><a href="../pages/faq.php?act=trans">' . $lng['translit'] . '</a> | ' .
        '<a href="../pages/faq.php?act=smileys">' . $lng['smileys'] . '</a></div>' .
        '<p><a href="' . $id . '/'.$res_r['seo'].'.html">' . $lng['back'] . '</a></p>';
}
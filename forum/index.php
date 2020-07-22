<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require('../incfiles/core.php');
$lng_forum = core::load_lng('forum');
$url = @addslashes($_GET['url']);
if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);

/*
-----------------------------------------------------------------
Настройки форума
-----------------------------------------------------------------
*/
$set_forum = $user_id && !empty($datauser['set_forum']) ? unserialize($datauser['set_forum']) : array(
    'farea'    => 0,
    'upfp'     => 0,
    'preview'  => 1,
    'postclip' => 1,
    'postcut'  => 2
);

/*
-----------------------------------------------------------------
Список расширений файлов, разрешенных к выгрузке
-----------------------------------------------------------------
*/
// Файлы архивов
$ext_arch = array(
    'zip',
    'rar',
    '7z',
    'tar',
    'gz',
    'apk'
);
// Звуковые файлы
$ext_audio = array(
    'mp3',
    'amr'
);
// Файлы документов и тексты
$ext_doc = array(
    'txt',
    'pdf',
    'doc',
    'docx',
    'rtf',
    'djvu',
    'xls',
    'xlsx'
);
// Файлы Java
$ext_java = array(
    'sis',
    'sisx',
    'apk'
);
// Файлы картинок
$ext_pic = array(
    'jpg',
    'jpeg',
    'gif',
    'png',
    'bmp'
);
// Файлы SIS
$ext_sis = array(
    'sis',
    'sisx'
);
// Файлы видео
$ext_video = array(
    '3gp',
    'avi',
    'flv',
    'mpeg',
    'mp4'
);
// Файлы Windows
$ext_win = array(
    'exe',
    'msi'
);
// Другие типы файлов (что не перечислены выше)
$ext_other = array('wmf');

// Ограничиваем доступ к Форуму
$error = '';
if (!$set['mod_forum'] && $rights < 7)
    $error = $lng_forum['forum_closed'];
elseif ($set['mod_forum'] == 1 && !$user_id)
    $error = $lng['access_guest_forbidden'];
if ($error) {
    require('../incfiles/head.php');
    echo '<div class="rmenu"><p>' . $error . '</p></div>';
    require('../incfiles/end.php');
    exit;
}

$headmod = $id ? 'forum,' . $id : 'forum';

// Заголовки страниц форума
if (empty($id)) {
    $textl = '' . $lng['forum'] . '';
} else {
    $req = mysql_query("SELECT `text`,`type` FROM `forum` WHERE `id`= '" . $id . "'");
    $res = mysql_fetch_assoc($req);
    $hdr = strtr($res['text'], array(
        '&laquo;' => '',
        '&raquo;' => '',
        '&quot;'  => '',
        '&amp;'   => '',
        '&lt;'    => '',
        '&gt;'    => '',
        '&#039;'  => ''
    ));
    $hdr = html_entity_decode($hdr, ENT_QUOTES, 'UTF-8');
    $hdr = mb_substr($hdr, 0, 35);
    $hdr = functions::checkout($hdr);
    $shdr = (mb_strlen($res['text']) > 35 ? $hdr . '...' : $hdr).' - '.$set['copyright'];
    $textl = (mb_strlen($shdr) > 60 ? mb_substr($shdr, 0, 57).'...' : mb_substr($shdr, 0, 60));
    $seow = functions::createTags($hdr);
    if($res['type'] == 't') {
        $req_d = mysql_query("SELECT `text` FROM `forum` WHERE `type` = 'm' AND `refid` = '$id' ORDER BY `id` LIMIT 1");
        $res_d = mysql_fetch_array($req_d);
        $seo_d = bbcode::notags($res_d['text']);
        $seo_d = html_entity_decode($seo_d, ENT_QUOTES, 'UTF-8');
        $seo_d = mb_substr($seo_d, 0, 140);
        $seosd = $hdr.' - '.functions::checkout($seo_d);
        $seod = (mb_strlen($seosd) > 200 ? mb_substr($seosd, 0, 197).'...' : mb_substr($seosd, 0, 200));
    }else{
        $seod = $hdr;
    }
}
// Переключаем режимы работы
$mods = array(
    'addfile',
    'addvote',
    'close',
    'deltema',
    'delvote',
    'editpost',
    'editvote',
    'file',
    'files',
    'filter',
    'import',
    'loadtem',
    'massdel',
    'new',
    'nt',
    'per',
    'post',
    'ren',
    'restore',
    'say',
    'tema',
    'users',
    'vip',
    'vote',
    'who',
    'wholike',
    'curators',
    'xoafile'
);
if ($act && ($key = array_search($act, $mods)) !== false && file_exists('includes/' . $mods[$key] . '.php')) {
    require('includes/' . $mods[$key] . '.php');
} else {
    require('../incfiles/head.php');

    // Если форум закрыт, то для Админов выводим напоминание
    if (!$set['mod_forum']) echo '<div class="alarm">' . $lng_forum['forum_closed'] . '</div>';
    elseif ($set['mod_forum'] == 3) echo '<div class="rmenu">' . $lng['read_only'] . '</div>';
    if (!$user_id) {
        if (isset($_GET['newup']))
            $_SESSION['uppost'] = 1;
        if (isset($_GET['newdown']))
            $_SESSION['uppost'] = 0;
    }
    if ($id) {
        // Определяем тип запроса (каталог, или тема)
        $type = mysql_query("SELECT * FROM `forum` WHERE `id`= '$id' AND `seo`= '$url'");
        if (!mysql_num_rows($type)) {
            // Если темы не существует, показываем ошибку
            echo functions::display_error($lng_forum['error_topic_deleted'], '<a href="/forum/index.html">' . $lng['to_forum'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        $type1 = mysql_fetch_assoc($type);

        // Фиксация факта прочтения Топика
        if ($user_id && $type1['type'] == 't') {
            $req_r = mysql_query("SELECT * FROM `cms_forum_rdm` WHERE `topic_id` = '$id' AND `user_id` = '$user_id' LIMIT 1");
            if (mysql_num_rows($req_r)) {
                $res_r = mysql_fetch_assoc($req_r);
                if ($type1['time'] > $res_r['time'])
                    mysql_query("UPDATE `cms_forum_rdm` SET `time` = '" . time() . "' WHERE `topic_id` = '$id' AND `user_id` = '$user_id' LIMIT 1");
            } else {
                mysql_query("INSERT INTO `cms_forum_rdm` SET `topic_id` = '$id', `user_id` = '$user_id', `time` = '" . time() . "'");
            }
        }

        // Получаем структуру форума
        $res = true;
        $allow = 0;
        $parent = $type1['refid'];
        $reqtt = mysql_query("SELECT * FROM `forum` WHERE `id` = '$parent' LIMIT 1");
        $restt = mysql_fetch_assoc($req);
        while ($parent != '0' && $res != false) {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$parent' LIMIT 1");
            $res = mysql_fetch_assoc($req);
            if ($res['type'] == 'f' || $res['type'] == 'r') {
                $tree[] = '<a href="'.$home.'/forum/' . $res['id'] . '/' . $res['seo'] . '.html"><h3>' . $res['text'] . '</h3></a>';
                if ($res['type'] == 'r' && !empty($res['edit'])) {
                    $allow = intval($res['edit']);
                }
            }
            $parent = $res['refid'];
        }
        $tree[] = '<a href="/forum/index.html"><h3>' . $lng['forum'] . '</h3></a>';
        krsort($tree);
        if ($type1['type'] != 't' && $type1['type'] != 'm')
            $tree[] = '<h3>' . $type1['text'] . '</h3>';

        // Счетчик файлов и ссылка на них
        $sql = ($rights == 9) ? "" : " AND `del` != '1'";
        if ($type1['type'] == 'f') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `cat` = '$id'" . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="/forum/index.php?act=files&amp;c=' . $id . '">' . $lng_forum['files_category'] . '</a>';
        } elseif ($type1['type'] == 'r') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `subcat` = '$id'" . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="/forum/index.php?act=files&amp;s=' . $id . '">' . $lng_forum['files_section'] . '</a>';
        } elseif ($type1['type'] == 't') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `topic` = '$id'" . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="/forum/index.php?act=files&amp;t=' . $id . '">' . $lng_forum['files_topic'] . '</a>';
        }
        $filelink = isset($filelink) ? $filelink . '&#160;<span class="red">(' . $count . ')</span>' : false;

        // Счетчик "Кто в теме?"
        $wholink = false;
        if ($user_id && $type1['type'] == 't') {
            $online_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > " . (time() - 300) . " AND `place` = 'forum,$id'"), 0);
            $online_g = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_sessions` WHERE `lastdate` > " . (time() - 300) . " AND `place` = 'forum,$id'"), 0);
            $wholink = '<a href="/forum/index.php?act=who&amp;id=' . $id . '">' . $lng_forum['who_here'] . '?</a>&#160;<span class="red">(' . $online_u . '&#160;/&#160;' . $online_g . ')</span><br/>';
        }

        // Выводим верхнюю панель навигации
        echo '<a id="up"></a>' .
            '<div class="phdr">' . functions::display_menu($tree, ' > ') . '</div>' .
            '<div class="topmenu"><a href="/forum/timkiem_id' . $id . '.html">' . $lng['search'] . '</a>' . ($filelink ? ' | ' . $filelink : '') . ($wholink ? ' | ' . $wholink : '') . '</div>';

        switch ($type1['type']) {
            case 'f':
                ////////////////////////////////////////////////////////////
                // Список разделов форума                                 //
                ////////////////////////////////////////////////////////////
                $req = mysql_query("SELECT * FROM `forum` WHERE `type`='r' AND `refid`='$id' ORDER BY `realid`");
                $total = mysql_num_rows($req);
                if ($total) {
                    $i = 0;
                    while (($res = mysql_fetch_assoc($req)) !== false) {
                        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                        $coltem = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `refid` = '" . $res['id'] . "'"), 0);
                        echo '<a href="/forum/' . $res['id'] . '/' . $res['seo'] . '.html">' . $res['text'] . '</a>';
                        if ($coltem)
                            echo " [$coltem]";
                        if (!empty($res['soft']))
                            echo '<div class="sub"><span class="gray">' . $res['soft'] . '</span></div>';
                        echo '</div>';
                        ++$i;
                    }
                    unset($_SESSION['fsort_id']);
                    unset($_SESSION['fsort_users']);
                } else {
                    echo '<div class="menu"><p>' . $lng_forum['section_list_empty'] . '</p></div>';
                }
                echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
                break;

            case 'r':
                ////////////////////////////////////////////////////////////
                // Список топиков                                         //
                ////////////////////////////////////////////////////////////
                $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `refid`='$id'" . ($rights >= 7 ? '' : " AND `close`!='1'")), 0);
                if (($user_id && !isset($ban['1']) && !isset($ban['11']) && $set['mod_forum'] != 4) || core::$user_rights) {
                    // Кнопка создания новой темы
                    echo '<div class="gmenu"><form action="/forum/index.php?act=nt&amp;id=' . $id . '" method="post"><input type="submit" value="' . $lng_forum['new_topic'] . '" /></form></div>';
                }
                if ($total) {
                    $req = mysql_query("SELECT * FROM `forum` WHERE `type`='t'" . ($rights >= 7 ? '' : " AND `close`!='1'") . " AND `refid`='$id' ORDER BY `vip` DESC, `time` DESC LIMIT $start, $kmess");
                    $i = 0;
                    while (($res = mysql_fetch_assoc($req)) !== false) {
                        if ($res['close'])
                            echo '<div class="rmenu">';
                        else
                            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                        $nikuser = mysql_query("SELECT * FROM `forum` WHERE `type` = 'm' AND `close` != '1' AND `refid` = '" . $res['id'] . "' ORDER BY `time` DESC LIMIT 1");
                        $nam = mysql_fetch_assoc($nikuser);
                        $colmes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m' AND `refid`='" . $res['id'] . "'" . ($rights >= 7 ? '' : " AND `close` != '1'"));
                        $colmes1 = mysql_result($colmes, 0);
                        $cpg = ceil($colmes1 / $kmess);
                        $np = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_rdm` WHERE `time` >= '" . $res['time'] . "' AND `topic_id` = '" . $res['id'] . "' AND `user_id`='$user_id'"), 0);
                        // Значки
                        $icons = array(
                            ($np ? (!$res['vip'] ? functions::image('np.gif') : '') : functions::image('op.gif')),
                            ($res['vip'] ? functions::image('pt.gif') : ''),
                            ($res['realid'] ? functions::image('rate.gif') : ''),
                            ($res['edit'] ? functions::image('tz.gif') : '')
                        );
                        if ($set_user['avatar']) {
                            echo '<table cellpadding="0" cellspacing="0"><tr><td style="padding-right: 5px;">';
                            echo '<img src="' . $home . '/avatar/' . $res['user_id'] . '-24-48.png" alt="" />';
                            echo '</td><td>';
                        }
                        echo functions::display_menu($icons, '');
                        echo '<a href="'.$home.'/forum/' . $res['id'] . '/' . $res['seo'] . '.html">' . $res['text'] . '</a> ['.($cpg > 1 ? '<a href="'.$home.'/forum/' . $res['id'] . '/' . $res['seo'] . '_p'.$cpg.'.html#post'.$nam['id'].'">' . $colmes1 . '</a>' : $colmes1 ). ']';
                        if ($set_user['avatar']) {
                            echo '</td></tr></table>';
                        }
                        echo '<div class="font-xs" style="text-align: right; color: gray; margin: -1px 0px 1px 1px;">';
                        echo functions::nickcolor($res['user_id']);
                        if ($colmes1 > 1) {
                            echo '&#160;/&#160;' . functions::nickcolor($nam['user_id']);
                        }

                        echo ' <span class="gray">(' . functions::display_date($res['time']) . ')</span></div>';
                        echo '</div>';
                        ++$i;
                    }
                    unset($_SESSION['fsort_id']);
                    unset($_SESSION['fsort_users']);
                } else {
                    echo '<div class="menu"><p>' . $lng_forum['topic_list_empty'] . '</p></div>';
                }
                echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
                if ($total > $kmess) {
                    echo '<div class="topmenu">' . functions::display_pagination2('/forum/' . $id . '/'.$type1["seo"].'', $start, $total, $kmess) . '</div>' .
                        '<p><form action="/forum/' . $id . '/'.$type1["seo"].'.html" method="post">' .
                        '<input type="text" name="page" size="2"/>&#160;' .
                        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
                        '</form></p>';
                }
                break;

            case 't':
                ////////////////////////////////////////////////////////////
                // Показываем тему с постами                              //
                ////////////////////////////////////////////////////////////
                $filter = isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == $id ? 1 : 0;
                $sql = '';
                if ($filter && !empty($_SESSION['fsort_users'])) {
                    // Подготавливаем запрос на фильтрацию юзеров
                    $sw = 0;
                    $sql = ' AND (';
                    $fsort_users = unserialize($_SESSION['fsort_users']);
                    foreach ($fsort_users as $val) {
                        if ($sw)
                            $sql .= ' OR ';
                        $sortid = intval($val);
                        $sql .= "`forum`.`user_id` = '$sortid'";
                        $sw = 1;
                    }
                    $sql .= ')';
                }

                // Если тема помечена для удаления, разрешаем доступ только администрации
                if ($rights < 6 && $type1['close'] == 1) {
                    echo '<div class="rmenu"><p>' . $lng_forum['topic_deleted'] . '<br/><a href="/forum/' . $restt['id'] . '/' . $restt['seo'] . '.html">' . $lng_forum['to_section'] . '</a></p></div>';
                    require('../incfiles/end.php');
                    exit;
                }

                if ($user_id && isset($_GET['thank']) || $user_id && isset($_GET['unthank'])) {
                    $id_thank = @addslashes(trim($_GET['id_thank']));
                    $ngkvietb = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id_thank'");
                    $ngkguibai = mysql_fetch_assoc($ngkvietb);
                    $datathankuser = functions::get_user($ngkguibai['user_id']);
                    $checkthankdau = mysql_query('SELECT COUNT(*) FROM `forum_thank` WHERE `userthank` = "' . $user_id . '" and `topic` = "' . $id_thank . '" and `user` = "' . $ngkguibai['user_id'] . '"');
                    $demdauthank = mysql_result($checkthankdau, 0);
                    $tong2 = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `id` <= '$id_thank' AND `refid` = '".$id."'" . ($rights >= 7 ? '' : " AND `close` != '1'")), 0);
                    $sotrang2 = ceil($tong2/$kmess);
                    $tong = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `id` <= '$id_thank' AND `refid` = '".$id."'" . ($datathankuser['rights'] >= 7 ? '' : " AND `close` != '1'")), 0);
                    $sotrang = ceil($tong/$kmess);
                    if (isset($_GET['thank']) && $user_id != $ngkguibai['user_id'] && $demdauthank < 1 && $id_thank > 1 && ($ngkguibai['type'] = 't' || $ngkguibai['type'] = 'm')) {
                        ////mod thong bao like
                        if(strlen($ngkguibai['text']) > 45) {
                            $vbcat = mb_substr($ngkguibai['text'], 0, 45).'....';
                        } else {
                            $vbcat = $ngkguibai['text'];
                        }
                        mysql_query("INSERT INTO `cms_mail` SET
                            `user_id` = '$user_id', 
                            `from_id` = '".$ngkguibai['user_id']."', 

                            `them`='7',
                            `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã thích bài viết của bạn trong chủ đề: [url=".$home."/forum/".$id."/".$type1['seo']."_p".$sotrang.".html#post".$id_thank."]".$type1['text']."[/url] ( " . addslashes($vbcat) . " )',
                            `sys`='1',
                            `time` = '" . time() . "'
                        ");
                        /////ket thuc thong bao like
                        mysql_query("INSERT INTO `forum_thank` SET `user` = '".$ngkguibai['user_id']."', `topic` = '".$id_thank."' , `time` = '$time', `userthank` = '$user_id', `chude` = '".$id."' ");
                        $congcamon = mysql_fetch_array(mysql_query('SELECT * FROM `users` WHERE `id` = "' . $ngkguibai['user_id'] . '"'));
                        mysql_query("UPDATE `users` SET `thank_duoc`='" . ($congcamon['thank_duoc'] + 1) . "' WHERE `id` = '" . $ngkguibai['user_id'] . "'");
                        mysql_query("UPDATE `users` SET `thank_di`='" . ($datauser['thank_di'] + 1) . "' WHERE `id` = '" . $user_id . "'");
                        mysql_query("UPDATE `users` SET `postforum`='" . ($datathankuser['postforum'] + 3) . "' WHERE `id` = '" . $ngkguibai['user_id'] . "'");
                        header('Location: /forum/'.$id.'/'.$type1['seo'].'_p'.$sotrang2.'.html#post'.$id_thank.'');
                    }else if(isset($_GET['unthank']) && $user_id != $ngkguibai['user_id'] && $demdauthank > 0 && $id_thank > 1 && ($ngkguibai['type'] = 't' || $ngkguibai['type'] = 'm')){
                        mysql_query("DELETE FROM `forum_thank` WHERE `user` = '".$ngkguibai['user_id']."' AND `userthank` = '$user_id' AND `topic` = '".$id_thank."'");
                        mysql_query("OPTIMIZE TABLE `forum_thank`");
                        mysql_query("UPDATE `users` SET `thank_duoc`='" . ($datathankuser['thank_duoc'] - 1) . "' WHERE `id` = '" . $ngkguibai['user_id'] . "'");
                        mysql_query("UPDATE `users` SET `thank_di`='" . ($datauser['thank_di'] - 1) . "' WHERE `id` = '" . $user_id . "'");
                        mysql_query("UPDATE `users` SET `postforum`='" . ($datathankuser['postforum'] - 3) . "' WHERE `id` = '" . $ngkguibai['user_id'] . "'");
                        header('Location: /forum/'.$id.'/'.$type1['seo'].'_p'.$sotrang2.'.html#post'.$id_thank.'');
                    }
                }

                // Счетчик постов темы
                $colmes = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m'$sql AND `refid`='$id'" . ($rights >= 7 ? '' : " AND `close` != '1'")), 0);
                if ($start >= $colmes) {
                    // Исправляем запрос на несуществующую страницу
                    $start = max(0, $colmes - (($colmes % $kmess) == 0 ? $kmess : ($colmes % $kmess)));
                }

                // Выводим название топика
                echo '<div class="phdr"><a href="#down">' . functions::image('down.png', array('class' => '')) . '</a>&#160;&#160;<h3>' . $type1['text'] . '</h3></div>';
                echo '<div class="list2 center">' . functions::shareLink($home.'/forum/' . $id . '/'.$type1['seo'].'.html') . '<span style="float: right;">View: ' . $type1['view'] . '</span></div>';
                if ($colmes > $kmess) {
                    echo '<div class="topmenu">' . functions::display_pagination2('/forum/' . $id . '/'.$type1["seo"].'', $start, $colmes, $kmess) . '</div>';
                }

                // Метка удаления темы
                if ($type1['close']) {
                    echo '<div class="rmenu">' . $lng_forum['topic_delete_who'] . ': <b>' . $type1['close_who'] . '</b></div>';
                } elseif (!empty($type1['close_who']) && $rights >= 7) {
                    echo '<div class="gmenu"><small>' . $lng_forum['topic_delete_whocancel'] . ': <b>' . $type1['close_who'] . '</b></small></div>';
                }

                // Метка закрытия темы
                if ($type1['edit']) {
                    echo '<div class="rmenu">' . $lng_forum['topic_closed'] . '</div>';
                }

                // Блок голосований
                if ($type1['realid']) {
                    $clip_forum = isset($_GET['clip']) ? '_clip' : '';
                    $vote_user = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `user`='$user_id' AND `topic`='$id'"), 0);
                    $topic_vote = mysql_fetch_assoc(mysql_query("SELECT `name`, `time`, `count` FROM `cms_forum_vote` WHERE `type`='1' AND `topic`='$id' LIMIT 1"));
                    echo '<div class="content"><b>' . functions::checkout($topic_vote['name']) . '</b><br />';
                    $vote_result = mysql_query("SELECT `id`, `name`, `count` FROM `cms_forum_vote` WHERE `type`='2' AND `topic`='" . $id . "' ORDER BY `id` ASC");
                    if (!$type1['edit'] && !isset($_GET['vote_result']) && $user_id && $vote_user == 0) {
                        // Выводим форму с опросами
                        echo '<form action="/forum/index.php?act=vote&amp;id=' . $id . '" method="post">';
                        while (($vote = mysql_fetch_assoc($vote_result)) !== false) {
                            echo '<input type="radio" value="' . $vote['id'] . '" name="vote"/> ' . functions::checkout($vote['name'], 0, 1) . '<br />';
                        }
                        echo '<p><input type="submit" name="submit" value="' . $lng['vote'] . '"/></p></form></div><div class="bmenu"><a href="/forum/' . $id . '/' . $type1['seo'] . '_start' . $start . '_vote' . '.html">Thống kê bình chọn</a></div>';
                    } else {
                        // Выводим результаты голосования
                        while (($vote = mysql_fetch_assoc($vote_result)) !== false) {
                            $count_vote = $topic_vote['count'] ? round(100 / $topic_vote['count'] * $vote['count']) : 0;
                            echo functions::checkout($vote['name'], 0, 1) . ' [' . $vote['count'] . ']<br />';
                            echo '<img src="/forum/vote_img.php?img=' . $count_vote . '" alt="' . $lng_forum['rating'] . ': ' . $count_vote . '%" /><br />';
                        }
                        echo '</div><div class="menu" style="margin:">' . $lng_forum['total_votes'] . ': ';
                        if (core::$user_rights > 6)
                            echo '<a href="/forum/index.php?act=users&amp;id=' . $id . '">' . $topic_vote['count'] . '</a>';
                        else
                            echo $topic_vote['count'];
                        echo '</div>';
                        if ($user_id && $vote_user == 0)
                            echo '<div class="alarm" ><a href="/forum/' . $id . '/' . $type1['seo'] . '_start' . $start . $clip_forum . '.html">' . $lng['vote'] . '</a></div>';
                    }
                }

                // Получаем данные о кураторах темы
                $curators = !empty($type1['curators']) ? unserialize($type1['curators']) : array();
                $curator = false;
                if ($rights < 6 && $rights != 3 && $user_id) {
                    if (array_key_exists($user_id, $curators)) $curator = true;
                }

                // Фиксация первого поста в теме
                if (($set_forum['postclip'] == 2 && ($set_forum['upfp'] ? $start < (ceil($colmes - $kmess)) : $start > 0)) || isset($_GET['clip'])) {
                    $postreq = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
                    FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
                    WHERE `forum`.`type` = 'm' AND `forum`.`refid` = '$id'" . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . "
                    ORDER BY `forum`.`id` LIMIT 1");
                    $postres = mysql_fetch_assoc($postreq);
                    echo '<div class="topmenu" style="margin-top: 5px;"><p>';
                    if ($postres['sex'])
                        echo functions::image(($postres['sex'] == 'm' ? 'm' : 'w') . ($postres['datereg'] > time() - 86400 ? '_new' : '') . '.png', array('class' => 'icon-inline'));
                    else
                        echo '<img src="/images/del.png" width="10" height="10" alt=""/>&#160;';
                    if ($user_id && $user_id != $postres['user_id']) {
                        echo '<a href="/users/profile.php?user=' . $postres['user_id'] . '&amp;fid=' . $postres['id'] . '"><b>' . $postres['from'] . '</b></a> ' .
                            '<a href="/forum/index.php?act=say&amp;id=' . $postres['id'] . '&amp;start=' . $start . '"> ' . $lng_forum['reply_btn'] . '</a> ' .
                            '<a href="/forum/index.php?act=say&amp;id=' . $postres['id'] . '&amp;start=' . $start . '&amp;cyt"> ' . $lng_forum['cytate_btn'] . '</a> ';
                    } else {
                        echo '<b>' . $postres['from'] . '</b> ';
                    }
                    $user_rights = array(
                        3 => '(FMod)',
                        6 => '(Smd)',
                        7 => '(Adm)',
                        9 => '(SV!)'
                    );
                    echo @$user_rights[$postres['rights']];
                    echo(time() > $postres['lastdate'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
                    echo ' <span class="gray">(' . functions::display_date($postres['time']) . ')</span><br/>';
                    if ($postres['close']) {
                        echo '<span class="red">' . $lng_forum['post_deleted'] . '</span><br/>';
                    }
                    echo functions::checkout(mb_substr($postres['text'], 0, 500), 0, 2);
                    if (mb_strlen($postres['text']) > 500)
                        echo '...<a href="/forum/post-' . $postres['id'] . '.html">' . $lng_forum['read_all'] . '</a>';
                    echo '</p></div>';
                }

                // Памятка, что включен фильтр
                if ($filter) {
                    echo '<div class="rmenu">' . $lng_forum['filter_on'] . '</div>';
                }

                // Задаем правила сортировки (новые внизу / вверху)
                if ($user_id) {
                    $order = $set_forum['upfp'] ? 'DESC' : 'ASC';
                } else {
                    $order = ((empty($_SESSION['uppost'])) || ($_SESSION['uppost'] == 0)) ? 'ASC' : 'DESC';
                }

                ////////////////////////////////////////////////////////////
                // Основной запрос в базу, получаем список постов темы    //
                ////////////////////////////////////////////////////////////
                if($page == 1 && (($user_id && $rights != 9) || (!$user_id))){
                    mysql_query("UPDATE `forum` SET `view`=`view` + 1 WHERE id=$id");
                }
                $req = mysql_query("
                  SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`facebook_ID`, `users`.`google_ID`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
                  FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
                  WHERE `forum`.`type` = 'm' AND `forum`.`refid` = '$id'"
                    . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . "$sql
                  ORDER BY `forum`.`id` $order LIMIT $start, $kmess
                ");

                // Верхнее поле "Написать"
                if (($user_id && !$type1['edit'] && $set_forum['upfp'] && $set['mod_forum'] != 3 && $allow != 4) || ($rights >= 7 && $set_forum['upfp'])) {
                    echo '<div class="gmenu"><form name="form1" action="/forum/index.php?act=say&amp;id=' . $id . '" method="post">';
                    if ($set_forum['farea']) {
                        $token = mt_rand(1000, 100000);
                        $_SESSION['token'] = $token;
                        echo '<p>' .
                            bbcode::auto_bb('form1', 'msg') .
                            '<textarea rows="' . $set_user['field_h'] . '" name="msg"></textarea></p>' .
                            '<p><input type="checkbox" name="addfiles" value="1" /> ' . $lng_forum['add_file'] .
                            ($set_user['translit'] ? '<br /><input type="checkbox" name="msgtrans" value="1" /> ' . $lng['translit'] : '') .
                            '</p><p><input type="submit" name="submit" value="' . $lng['write'] . '" style="width: 107px; cursor: pointer;"/> ' .
                            (isset($set_forum['preview']) && $set_forum['preview'] ? '<input type="submit" value="' . $lng['preview'] . '" style="width: 107px; cursor: pointer;"/>' : '') .
                            '<input type="hidden" name="token" value="' . $token . '"/>' .
                            '</p></form></div>';
                    } else {
                        echo '<p><input type="submit" name="submit" value="' . $lng['write'] . '"/></p></form></div>';
                    }
                }

                // Для администрации включаем форму массового удаления постов
                if ($rights == 3 || $rights >= 6)
                    echo '<form action="/forum/index.php?act=massdel" method="post">';
                $i = 1;

                ////////////////////////////////////////////////////////////
                // Основной список постов                                 //
                ////////////////////////////////////////////////////////////
                while (($res = mysql_fetch_assoc($req)) !== false) {
                    // Фон поста
                    if ($res['close']) {
                        echo '<div class="rmenu" style="margin: 10px 0 0 0;">';
                    } else {
                        echo '<div class="list-f1" '.($res['facebook_ID'] ? 'style="background: #fff url(/images/facebook.png) no-repeat right top;" ' : ($res['google_ID'] ? 'background: #fff url(/images/googlep.png) no-repeat right top;" ' : ' ')).' id="post'.$res['id'].'">';
                    }

                    // Пользовательский аватар
                    if ($set_user['avatar']) {
                        echo '<table style="padding: 0;border-spacing: 0;"><tr><td>';
                        echo '<img src="' . $home . '/avatar/' . $res['user_id'] . '-24-48.png" width="48" height="48" alt="' . $res['from'] . '" />&#160;';
                        echo '</td><td>';
                    }

                    // Метка пола
                    if ($res['sex']) {
                        if(time() > $res['lastdate'] + 30){
                            echo functions::image(($res['sex'] == 'm' ? 'user/man_of' : 'user/j_of') . '.png', array('class' => 'icon-inline'));
                        }else{
                            echo functions::image(($res['sex'] == 'm' ? 'm' : 'w') . ($res['datereg'] > time() - 86400 ? '_new' : '') . '.png', array('class' => 'icon-inline'));
                        }
                    } else {
                        echo functions::image('del.png', array('class' => 'icon-inline'));
                    }

                    // Ник юзера и ссылка на его анкету
                    if ($user_id && $user_id != $res['user_id']) {
                        echo '<a href="/users/profile.php?user=' . $res['user_id'] . '"><b>' . functions::nickcolor($res['user_id']) . '</b></a> ';
                    } else {
                        echo '<b>' . functions::nickcolor($res['user_id']) . '</b> ';
                    }

                    // Метка должности
                    $user_rights = array(
                        3 => '(FMod)',
                        6 => '(Smd)',
                        7 => '(Adm)',
                        9 => '(SV!)'
                    );

                    // Время поста
                    echo '<br />';

                    // Статус пользователя
                    if (!empty($res['status'])) {
                        echo '<div class="status">' . functions::image('label.png', array('class' => 'icon-inline')) . $res['status'] . '</div>';
                    }

                    // Закрываем таблицу с аватаром
                    if ($set_user['avatar']) {
                        echo '</td></tr></table>';
                    }
                    echo '</div>';
                    if ($res['close']) {
                        echo '<div class="rmenu">';
                    } else {
                        echo '<div class="list-f2">';
                    }
                    $count = $start+$i;
                    echo '<div class="info-112"> <table class="f-table"><tr><td style="text-align: left;"><span class="info-c"> <i class="fa fa-clock-o" style="font-size: 14px;"></i> '.functions::display_date($res['time']).'</span></td><td class="right"><a href="/forum/post-' . $res['id'] . '.html" title="Link to post"><span class="info-c">['.($count == 1 ? 'TOP' : '#'.$count).']</span></a></td></tr></table></div>';
 ////////////////////////////////////////////////////////////
                    // Вывод текста поста                                     //
                    ////////////////////////////////////////////////////////////
                    $text = $res['text'];
                    $text = functions::checkout($text, 1, 1);
                    if ($set_user['smileys']) {
                        $text = functions::smileys($text, $res['rights'] ? 1 : 0);
                    }
                    echo $text;

                    // Если пост редактировался, показываем кем и когда
                    if ($res['kedit']) {
                        echo '<div class="gray info-113 font-xs">' . $lng_forum['edited'] . ' <b>' . $res['edit'] . '</b> (' . functions::display_date($res['tedit']) . ') <b>[' . $res['kedit'] . ']</b></div>';
                    }

                    // Если есть прикрепленный файл, выводим его описание
                    $freq = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "'");
                    if (mysql_num_rows($freq) > 0) {
                            echo '<div class="info-file1">' . $lng_forum['attached_file'] . ':</div><div class="info-file2">';
                        while ($fres = mysql_fetch_assoc($freq)) {
                            $fls = round(@filesize('../files/forum/attach/' . $fres['filename']) / 1024, 2);
                            echo '<div class="gray info-file3">';
                            // Предпросмотр изображений
                            $att_ext = strtolower(functions::format('./files/forum/attach/' . $fres['filename']));
                            $pic_ext = array(
                                'gif',
                                'jpg',
                                'jpeg',
                                'png'
                            );
                            if (in_array($att_ext, $pic_ext)) {
                                echo '<table><tr><td><a href="/forum/index.php?act=file&amp;id=' . $fres['id'] . '">';
                                echo '<img src="/forum/thumbinal.php?file=' . (urlencode($fres['filename'])) . '" alt="' . $lng_forum['click_to_view'] . '" /></a></td><td class="font-xs"> (' . $fls . ' kb.)<br/>'.$lng_forum['downloads'] . ': ' . $fres['dlcount'] . ' lượt.</td></tr></table>'.($user_id && ($user_id == $res['user_id'] || $rights >=6) ? '<div style="text-align: right; margin-top: -10px;"><a href="/forum/index.php?act=xoafile&amp;id=' . $fres['id'] . '" style="color: red; padding: 1px 4px 1px 4px;">x</a></div>' : '');
                            } else {
                                echo '<a href="/forum/index.php?act=file&amp;id=' . $fres['id'] . '">' . $fres['filename'] . '</a> (' . $fls . ' kb.)<br/>'.$lng_forum['downloads'] . ': ' . $fres['dlcount'] . ' lượt.'.($user_id && ($user_id == $res['user_id'] || $rights >=6) ? '<span style="float: right;"><a href="/forum/index.php?act=xoafile&amp;id=' . $fres['id'] . '" style="color: red; padding: 1px 4px 1px 4px;">x</a></span>' : '');
                            }
                            echo '</div>';
                            $file_id = $fres['id'];
                            $i;
                        }
                        echo '</div>';
                    }
                    // mrt
                    $omsg_id = $res['id'];
                    if($user_id){
                        // Reaction status check for "Like"
                        $like=functions::Like_Check($res['id'],$user_id, "Like");
                        // If post is not reactioned then show the $like_statusicon = 'icon-like-blf';
                        // $like_statusicon working with all reaction status
                        $like_statusicon = 'icon-like-blf';
                        // $lostyle will working reaction status 
                        // For example if not reactioned post then the style will be display:none;
                        $lostyle='display:none;';
                        if($like) {
                            //If post liked then show UnLike from the div rel and title
                            $like_status='UnLike'; 
                            // If post liked then show new UnLike icon from the reactions box
                            $like_statusicon='icon-like-new';
                        } else {
                            // If post not liked then show UnLike from the div rel and title
                            $like_status='Like'; 
                        }
                        // Reaction status check for "Love"
                        $love=functions::Like_Check($res['id'],$user_id, "Love");
                        if($love){
                            // If post reaction status is UnLove then show UnLove from the div rel and title
                            $love_status='UnLove';
                            // If post reaction status is UnLove then show Love icon from the reactions box
                            $like_statusicon='icon-love-new'; 
                            // If post reaction status is UnLove then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnLove then show Love from the div rel and title
                            $love_status='Love';
                         }
                        // Reaction status check for "Haha"
                        $haha=functions::Like_Check($res['id'],$user_id, "Haha");
                        if($haha){
                            // If post reaction status is UnHaha then show UnHaha from the div rel and title
                            $haha_status='UnHaha';
                            // If post reaction status is UnHaha then show Haha icon from the reactions box
                            $like_statusicon='icon-haha-new'; 
                            // If post reaction status is UnHaha then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnHaha then show Haha from the div rel and title
                            $haha_status='Haha';
                        }
                        // Reaction status check for "Hihi"
                        $hihi=functions::Like_Check($res['id'],$user_id, "Hihi");
                        if($hihi){
                            // If post reaction status is UnHihi then show UnHihi from the div rel and title
                            $hihi_status='UnHihi';
                            // If post reaction status is UnHihi then show Hihi icon from the reactions box
                            $like_statusicon='icon-mmmm-new'; 
                            // If post reaction status is UnHihi then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            //If post reaction status is not UnHihi then show Hihi from the div rel and title
                            $hihi_status='Hihi';
                        }
                        // Reaction status check for "Woww"
                        $woww=functions::Like_Check($res['id'],$user_id, "Woww");
                        if($woww){
                            // If post reaction status is UnWoww then show UnWoww from the div rel and title
                            $woww_status='UnWoww';
                            // If post reaction status is UnWoww then show Woww icon from the reactions box
                            $like_statusicon='icon-wowww-new'; 
                            // If post reaction status is UnWoww then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnWoww then show Woww from the div rel and title
                            $woww_status='Woww';
                        }
                        // Reaction status check for "Cry"
                        $Cry=functions::Like_Check($res['id'],$user_id, "Cry");
                        if($Cry){
                            // If post reaction status is UnCry then show UnCry from the div rel and title
                            $cry_status='UnCry';
                            // If post reaction status is UnCry then show Cry icon from the reactions box
                            $like_statusicon='icon-crying-new'; 
                            // If post reaction status is UnCry then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnCry then show Cry from the div rel and title
                            $cry_status='Cry'; 
                        }
                        // Reaction status check for "Angry"
                        $angry=functions::Like_Check($res['id'],$user_id, "Angry");
                        if($angry){
                            // If post reaction status is UnAngry then show UnAngry from the div rel and title
                            $angry_status='UnAngry';
                            // If post reaction status is UNAngry then show Angry icon from the reactions box
                            $like_statusicon='icon-angry-new'; 
                            // If post reaction status is UnAngry then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnAngry then show Angry from the div rel and title
                            $angry_status='Angry';
                        }

                        echo '<div class="post-like-unlike-comment">
                            <div class="like-post openCommentArea" id="'.$omsg_id.'" title="Comment" rel="'.$res['id'].'" data-id="'.$res['id'].'">
                                <div class="icon-like-comment icon-talk-chat-bubble"></div>
                            </div>
                            <div class="like-it">
                                <div class="new_like" tabindex="0" id="'.$res['id'].'">
                                    <div class="like-pit first_click">
                                        <div class="icon-lpn '.$like_statusicon.'" id="ulk'.$res['id'].'"></div>
                                        <div class="new_like_items first_click_wrap_content">
                                            <div class="op-lw like_button" data-id="0" id="like'.$omsg_id.'" rel="'.$like_status.'" title="'.$like_status.'"><div class="icon-newL icon-like-new"></div></div>
                                            <div class="op-lw like_button" data-id="1" id="love'.$omsg_id.'" rel="'.$love_status.'" title="'.$love_status.'"><div class="icon-newL icon-love-new"></div></div>
                                            <div class="op-lw like_button" data-id="2" id="haha'.$omsg_id.'" rel="'.$haha_status.'" title="'.$haha_status.'"><div class="icon-newL icon-haha-new"></div></div>
                                            <div class="op-lw like_button" data-id="3" id="hihi'.$omsg_id.'" rel="'.$hihi_status.'" title="'.$hihi_status.'"><div class="icon-newL icon-mmmm-new"></div></div>
                                            <div class="op-lw like_button" data-id="4" id="woww'.$omsg_id.'" rel="'.$woww_status.'" title="'.$woww_status.'"><div class="icon-newL icon-wowww-new"></div></div>
                                            <div class="op-lw like_button" data-id="5" id="cry'.$omsg_id.'" rel="'.$cry_status.'" title="'.$cry_status.'"><div class="icon-newL icon-crying-new"></div></div>
                                            <div class="op-lw like_button" data-id="6" id="angry'.$omsg_id.'" rel="'.$angry_status.'" title="'.$angry_status.'"><div class="icon-newL icon-angry-new"></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>';

                        if ($user_id && $user_id != $res['user_id']){
                            if (($user_id && !$type1['edit'] && !$set_forum['upfp'] && $set['mod_forum'] != 3 && $allow != 4) || ($rights >= 7 && !$set_forum['upfp'])){
                                echo '<div class="like-it">';
                                echo '<a href="/forum/index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '&amp;cyt"><div class="icon-newL icon-quotes"></div></a>';
                                echo '</div>';
                            }
                        }
                        echo '</div>';

                    }

                    $sep = '';
                    $lstyle = '';
                    if(functions::Like_CountTotal($res['id'], $user_id, in_array($sep, array('Like','Love','Haha','Hihi','Woww','Cry','Angry')))>0){
                        $lstyle="display:block;";
                    } else {
                        //$lstyle="display:none;";
                    }
                    echo '<div class="who-likes-this-post likes reaction_wrap-style" id="likess'.$omsg_id.'" style="'.$lstyle.'">';
                    //Like Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Like')>0) {
                        echo '<div class="likes reaction_wrap-style bbc" id="elikes'.$omsg_id.'" style="'.$lstyle.'"><span id="like_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-like-new lpos" id="clk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Like\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Like\')"></span><span class="lvspan mrt_Like_'.$res['id'].' no_display" id="public_Like_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'2\', \'Like\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Like\')"><span id="Like_users'.$omsg_id.'"></span></span><span class="lcl" id="lcl'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Like').'</span></span></div>'; 
                    } else {
                        echo '<div class="likes reaction_wrap-style bbc" id="elikes'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Love Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Love')){
                        echo '<div class="loves reaction_wrap-style bbc" id="eloves'.$omsg_id.'" style="'.$lstyle.'"><span id="love_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-love-new lpos" id="llk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Love\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Love\')"></span><span class="lvspan mrt_Love_'.$res['id'].' no_display" id="public_Love_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'2\', \'Love\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Love\')"><span id="Love_users'.$omsg_id.'"></span></span><span class="lco" id="lco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Love').'</span></span></div>'; 
                    } else {
                        echo '<div class="loves reaction_wrap-style bbc" id="eloves'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Haha Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Haha')){
                        echo '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$omsg_id.'" style="'.$lstyle.'"><span id="haha_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-haha-new lpos" id="hlk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Haha\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Haha\')"></span><span class="lvspan mrt_Haha_'.$res['id'].' no_display" id="public_Haha_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'2\', \'Haha\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Haha\')"><span id="Haha_users'.$omsg_id.'"></span></span><span class="hco" id="hco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Haha').'</span></span></div>'; 
                    } else {
                        echo '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Hihi Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Hihi')){
                        echo '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$omsg_id.'" style="'.$lstyle.'"><span id="hihi_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-mmmm-new lpos" id="hilk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Hihi\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Hihi\')"></span><span class="lvspan mrt_Hihi_'.$res['id'].' no_display" id="public_Hihi_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'2\', \'Hihi\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Hihi\')"><span id="Hihi_users'.$omsg_id.'"></span></span><span class="hico" id="hico'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Hihi').'</span></span></div>'; 
                    } else {
                        echo '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Woww Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Woww')){
                        echo '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$omsg_id.'" style="'.$lstyle.'"><span id="woww_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-wowww-new lpos" id="woow'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Woww\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Woww\')"></span><span class="lvspan mrt_Woww_'.$res['id'].' no_display" id="public_Woww_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'2\', \'Woww\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Woww\')"><span id="Woww_users'.$omsg_id.'"></span></span><span class="wco" id="wco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Woww').'</span></span></div>'; 
                    } else {
                        echo '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Cry Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Cry')){
                        echo '<div class="crys reaction_wrap-style bbc" id="ecry'.$omsg_id.'" style="'.$lstyle.'"><span id="cry_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-crying-new lpos" id="cry'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Cry\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Cry\')"></span><span class="lvspan mrt_Cry_'.$res['id'].' no_display" id="public_Cry_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'2\', \'Cry\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Cry\')"><span id="Cry_users'.$omsg_id.'"></span></span><span class="cco" id="cco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Cry').'</span></span></div>';
                    } else {
                        echo '<div class="crys reaction_wrap-style bbc" id="ecry'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Angry Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Angry')){
                        echo '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$omsg_id.'" style="'.$lstyle.'"><span id="angry_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-angry-new lpos" id="angry'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Angry\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Angry\')"></span><span class="lvspan mrt_Angry_'.$res['id'].' no_display" id="public_Angry_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'2\', \'Angry\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\', \'Angry\)"><span id="Angry_users'.$omsg_id.'"></span></span><span class="eco" id="eco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Angry').'</span></span></div>'; 
                    } else {
                        echo '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$omsg_id.'" style="display:none"></div>';
                    }
                    echo '</div>';

                    // mrt
                    echo ($res['user_id'] != $user_id ? '<div class="gray font-xs" style="text-align: right; margin: 0px -3px -4px 0px">'.$res['soft'].'</div>' : '');

                    // Ссылки на редактирование / удаление постов
                    if (
                        (($rights == 3 || $rights >= 6 || $curator) && $rights >= $res['rights'])
                        || ($res['user_id'] == $user_id && !$set_forum['upfp'] && ($start + $i) == $colmes && $res['time'] > time() - 300)
                        || ($res['user_id'] == $user_id && $set_forum['upfp'] && $start == 0 && $i == 1 && $res['time'] > time() - 300)
                        || ($i == 1 && $allow == 2 && $res['user_id'] == $user_id)
                        || ($res['user_id'] == $user_id)
                    ) {
                        echo '<div class="sub2">';

                        // Чекбокс массового удаления постов
                        if ($rights == 3 || $rights >= 6) {
                            echo '<div class="mdn-section">
                                <div class="mdn-group block-group">
                                    <label class="mdn-option">
                                        <input type="checkbox" name="delch[]" value="' . $res['id'] . '" />
                                            <span class="mdn-checkbox"></span>
                                    </label>
                                </div>
                            </div>&#160;';
                        }

                        // Служебное меню поста
                        $menu = array(
                            ' <a href="/forum/index.php?act=addfile&id='.$res['id'].'">Đính kèm file</a>',
                            '<a href="/forum/index.php?act=editpost&amp;id=' . $res['id'] . '">' . $lng['edit'] . '</a>',
                            ($rights >= 7 && $res['close'] == 1 ? '<a href="/forum/index.php?act=editpost&amp;do=restore&amp;id=' . $res['id'] . '">' . $lng_forum['restore'] . '</a>' : ''),
                            ($res['close'] == 1 ? '' : '<a href="/forum/index.php?act=editpost&amp;do=del&amp;id=' . $res['id'] . '">' . $lng['delete'] . '</a>') . ($res['user_id'] == $user_id ? '<span class="gray font-xs" style="float: right;">'.$res['soft'].'</span>' : '')
                        );
                        echo functions::display_menu($menu);

                        // Показываем, кто удалил пост
                        if ($res['close']) {
                            echo '<div class="red">' . $lng_forum['who_delete_post'] . ': <b>' . $res['close_who'] . '</b></div>';
                        } elseif (!empty($res['close_who'])) {
                            echo '<div class="green">' . $lng_forum['who_restore_post'] . ': <b>' . $res['close_who'] . '</b></div>';
                        }

                        // Показываем IP и Useragent
                        if ($rights == 3 || $rights >= 6) {
                            if ($res['ip_via_proxy']) {
                                echo '<div class="gray"><b class="red"><a href="' . $set['homeurl'] . '/' . $set['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($res['ip']) . '">' . long2ip($res['ip']) . '</a></b> - ' .
                                    '<a href="' . $set['homeurl'] . '/' . $set['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($res['ip_via_proxy']) . '">' . long2ip($res['ip_via_proxy']) . '</a></div>';
                            } else {
                                echo '<div class="gray"><a href="' . $set['homeurl'] . '/' . $set['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($res['ip']) . '">' . long2ip($res['ip']) . '</a></div>';
                            }
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                    ++$i;
                }

                echo '<div style="margin-top: 10px;"></div>';
                // Кнопка массового удаления постов
                if ($rights == 3 || $rights >= 6) {
                    echo '<div class="rmenu"><input type="submit" value=" ' . $lng['delete'] . ' "/></div>';
                    echo '</form>';
                }

                // Нижнее поле "Написать"
                if (($user_id && !$type1['edit'] && !$set_forum['upfp'] && $set['mod_forum'] != 3 && $allow != 4) || ($rights >= 7 && !$set_forum['upfp'])) {
                    echo '<div class="gmenu" style="margin-top: 7px"><div class="story-publisher-box"><form name="form2" action="/forum/index.php?act=say&amp;id=' . $id . '" method="post">';
                    $token = mt_rand(1000, 100000);
                    $_SESSION['token'] = $token;
                    echo '<p>';
                    echo bbcode::auto_bb('form2', 'msg');
                    echo '<textarea rows="' . $set_user['field_h'] . '" name="msg"></textarea><br/></p>' .
                        '<p><input type="checkbox" name="addfiles" value="1" /> ' . $lng_forum['add_file'];
                    if ($set_user['translit'])
                        echo '<br /><input type="checkbox" name="msgtrans" value="1" /> ' . $lng['translit'];
                    echo '</p><p><input type="submit" name="submit" value="' . $lng['write'] . '" style="width: 107px; cursor: pointer;"/> ' .
                        (isset($set_forum['preview']) && $set_forum['preview'] ? '<input type="submit" value="' . $lng['preview'] . '" style="width: 107px; cursor: pointer;"/>' : '') .
                        '<input type="hidden" name="token" value="' . $token . '"/>' .
                        '</p></form></div></div>';
                }

                echo '<div class="phdr"><a id="down"></a><a href="#up">' . functions::image('up.png', array('class' => '')) . '</a>' .
                    '&#160;&#160;' . $lng['total'] . ': ' . $colmes . '</div>';

                // Постраничная навигация
                if ($colmes > $kmess) {
                    echo '<div class="topmenu">' . functions::display_pagination2('/forum/' . $id . '/'.$type1["seo"].'', $start, $colmes, $kmess) . '</div>';
                } else {
                    echo '<br />';
                }

                echo '<div class="card s-sh1"><div class="card-content" style="padding: .75rem"><div class="content-tags">';
                if(!empty($type1['tags'])){
                    $demtags = @explode(',', $type1['tags']);
                    foreach ($demtags AS $key => $value) {
                        $datav1 = functions::checkout(trim($value));
                        echo '<a href="'.$home.'/forum/timkiem.html?search='.$datav1.'" class="post-tag" rel="nofollow">'.$datav1.'</a>';
                    }
                }else{
                    echo functions::createTags($type1['text'], 1);
                }
                echo '</div></div></div>';

                // Ссылки на модерские функции управления темой
                if ($rights == 3 || $rights >= 6) {
                    echo '<p><div class="func">';
                    if ($rights >= 7)
                        echo '<a href="/forum/index.php?act=curators&amp;id=' . $id . '&amp;start=' . $start . '">' . $lng_forum['curators_of_the_topic'] . '</a><br />';
                    echo isset($topic_vote) && $topic_vote > 0
                        ? '<a href="/forum/index.php?act=editvote&amp;id=' . $id . '">' . $lng_forum['edit_vote'] . '</a><br/><a href="/forum/index.php?act=delvote&amp;id=' . $id . '">' . $lng_forum['delete_vote'] . '</a><br/>'
                        : '<a href="/forum/index.php?act=addvote&amp;id=' . $id . '">' . $lng_forum['add_vote'] . '</a><br/>';
                    echo '<a href="/forum/index.php?act=ren&amp;id=' . $id . '">' . $lng_forum['topic_rename'] . '</a><br/>';
                    // Закрыть - открыть тему
                    if ($type1['edit'] == 1)
                        echo '<a href="/forum/index.php?act=close&amp;id=' . $id . '">' . $lng_forum['topic_open'] . '</a><br/>';
                    else
                        echo '<a href="/forum/index.php?act=close&amp;id=' . $id . '&amp;closed">' . $lng_forum['topic_close'] . '</a><br/>';
                    // Удалить - восстановить тему
                    if ($type1['close'] == 1)
                        echo '<a href="/forum/index.php?act=restore&amp;id=' . $id . '">' . $lng_forum['topic_restore'] . '</a><br/>';
                    echo '<a href="/forum/index.php?act=deltema&amp;id=' . $id . '">' . $lng_forum['topic_delete'] . '</a><br/>';
                    if ($type1['vip'] == 1)
                        echo '<a href="/forum/index.php?act=vip&amp;id=' . $id . '">' . $lng_forum['topic_unfix'] . '</a>';
                    else
                        echo '<a href="/forum/index.php?act=vip&amp;id=' . $id . '&amp;vip">' . $lng_forum['topic_fix'] . '</a>';
                    echo '<br/><a href="/forum/index.php?act=per&amp;id=' . $id . '">' . $lng_forum['topic_move'] . '</a></div></p>';
                }
                break;

            default:
                // Если неверные данные, показываем ошибку
                echo functions::display_error($lng['error_wrong_data']);
                break;
        }
    } else {
        ////////////////////////////////////////////////////////////
        // Список Категорий форума                                //
        ////////////////////////////////////////////////////////////
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files`" . ($rights >= 7 ? '' : " WHERE `del` != '1'")), 0);
        echo '<p>' . counters::forum_new(1) . '</p>' .
            '<div class="phdr"><b>' . $lng['forum'] . '</b></div>' .
            '<div class="topmenu"><a href="timkiem.html">' . $lng['search'] . '</a> | <a href="/forum/index.php?act=files">' . $lng_forum['files_forum'] . '</a> <span class="red">(' . $count . ')</span></div>';
        $req = mysql_query("SELECT * FROM `forum` WHERE `type`='f' ORDER BY `realid`");
        $i = 0;
        while (($res = mysql_fetch_array($req)) !== false) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='r' and `refid`='" . $res['id'] . "'"), 0);
            echo '<a href="'.$home.'/forum/' . $res['id'] . '/' . $res['seo'] . '.html">' . $res['text'] . '</a> [' . $count . ']';
            if (!empty($res['soft']))
                echo '<div class="sub"><span class="gray">' . $res['soft'] . '</span></div>';
            echo '</div>';
            ++$i;
        }
        $online_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > " . (time() - 300) . " AND `place` LIKE 'forum%'"), 0);
        $online_g = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_sessions` WHERE `lastdate` > " . (time() - 300) . " AND `place` LIKE 'forum%'"), 0);
        echo '<div class="phdr">' . ($user_id ? '<a href="/forum/index.php?act=who">' . $lng_forum['who_in_forum'] . '</a>' : $lng_forum['who_in_forum']) . '&#160;(' . $online_u . '&#160;/&#160;' . $online_g . ')</div>';
        unset($_SESSION['fsort_id']);
        unset($_SESSION['fsort_users']);
    }
}

require_once('../incfiles/end.php');
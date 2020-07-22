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
$lng_profile = core::load_lng('profile');

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!$user_id) {
    require('../incfiles/head.php');
    echo functions::display_error($lng['access_guest_forbidden']);
    require('../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
$user = functions::get_user($user);
if (!$user) {
    require('../incfiles/head.php');
    echo functions::display_error($lng['user_does_not_exist']);
    require('../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$array = array(
    'activity'  => 'includes/profile',
    'ban'       => 'includes/profile',
    'edit'      => 'includes/profile',
    'images'    => 'includes/profile',
    'info'      => 'includes/profile',
    'ip'        => 'includes/profile',
    'guestbook' => 'includes/profile',
    'karma'     => 'includes/profile',
    'status'     => 'includes/profile',
    'office'    => 'includes/profile',
    'password'  => 'includes/profile',
    'reset'     => 'includes/profile',
    'settings'  => 'includes/profile',
    'stat'      => 'includes/profile',
    'friends'   => 'includes/profile'
);
$path = !empty($array[$act]) ? $array[$act] . '/' : '';
if (array_key_exists($act, $array) && file_exists($path . $act . '.php')) {
    require_once($path . $act . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Анкета пользователя
    -----------------------------------------------------------------
    */
    $headmod = 'profile,' . $user['id'];
    $ten = html_entity_decode($user['name'], ENT_QUOTES, 'UTF-8');
    $textl = $lng['profile'] . ': ' . functions::checkout($ten);

    require('../incfiles/head.php');

    // Меню анкеты
    $menu = array();
    if ($user['id'] != $user_id && $rights >= 7 && $rights > $user['rights']) {
        $menu[] = '<a href="' . $set['homeurl'] . '/' . $set['admp'] . '/index.php?act=usr_del&amp;id=' . $user['id'] . '">' . $lng['delete'] . '</a>';
    }
    if ($user['id'] != $user_id && $rights > $user['rights']) {
        $menu[] = '<a href="profile.php?act=ban&amp;mod=do&amp;user=' . $user['id'] . '">' . $lng['ban_do'] . '</a>';
    }
    if (!empty($menu)) {
        echo '<div class="topmenu">' . functions::display_menu($menu) . '</div>';
    }

    // MrT
    echo '<script type="text/javascript" src="/js/jquery.form.min.js"></script><script type="text/javascript" src="/js/profile.js"></script>';

    if (file_exists(($rootpath.'files/users/photo/' . $user['id'] . '.' . $user['cover_extension']))) {
    $img = $home.'/files/users/photo/' . $user['id'] . '.' . $user['cover_extension'];
    } else {
        $img = $home.'/images/default-cover-user.png';
    }

    if (file_exists(($rootpath.'files/users/photo/' . $user['id'] . '_cover.' . $user['cover_extension']))) {
        $cover_img_url = $home.'/files/users/photo/'.$user['id'] . '_cover.' . $user['cover_extension'];
    } else {
        $cover_img_url = $home.'/images/default-cover-user.png';
    }

    $avatari = $home.'/avatar/'.$user['id'].'-50-100.png';

echo '<br /><div class="timeline-header-wrapper">
    <div class="cover-container">
        <div class="cover-wrapper">
            <img src="'.$cover_img_url.'?'.$time.'" alt="'.$user['name'].'">
            <div class="cover-progress"></div>
        </div>
        
        <div class="cover-resize-wrapper">
            <img src="'.$img.'?'.$time.'" alt="'.$user['name'].'">
            <div class="drag-div" align="center">Kéo để đặt lại vị trí</div>
            <div class="cover-progress"></div>
        </div>
        <div class="avatar-wrapper">
            <img class="avatar" src="'.$avatari.'?'.$time.'" alt="'.$user['name'].'">

'.($user['id'] == $user_id ? '<div class="avatar-change-wrapper">
    <i class="fa fa-camera" title="Chọn avatar" onclick="javascript:$(\'.change-avatar-input\').click();"></i>
</div>

<form class="change-avatar-form hidden" method="post" enctype="multipart/form-data" action="/request.php?t=avatar&a=post_upload">
    <input class="change-avatar-input hidden" type="file" name="image" accept="image/jpeg,image/png" onchange="javascript:$(\'form.change-avatar-form\').submit();">
    <input name="timeline_id" value="'.$user['id'].'" type="hidden">
</form>' : '').'
<div class="avatar-progress-wrapper"></div>
        </div>
        <div class="timeline-name-wrapper">
                <div id="ava_abc">'.$user['name'].'</div>'.(!empty($user['status']) ? '<div class="status_p">'.$user['status'].'</div>' : '' ).'
        </div>
    </div>
    <div class="timeline-statistics-wrapper">
        <table border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="statistic" align="center" valign="middle">
    <a href="profile.php?act=info&amp;user=' . $user['id'] . '">Giới thiệu</a>
</td>
            
            <td class="statistic" align="center" valign="middle">
    <a href="profile.php?act=friends&amp;user=' . $user['id'] . '">' . $lng_profile['friends'] . '</a>
</td>

<td class="statistic" align="center" valign="middle">
    <a href="album.php?act=list&amp;user=' . $user['id'] . '">Album ảnh</a>
</td>

            <td class="statistic" align="center" valign="middle">
                <a href="profile.php?act=stat&amp;user=' . $user['id'] . '">' . $lng['statistics'] . '</a>
            </td>
            <td class="statistic" align="center" valign="middle">
                <a href="profile.php?act=activity&amp;user=' . $user['id'] . '">Hoạt động</a>
            </td>
        </tr>
        </table>
    </div>
</div>

'.($user['id'] == $user_id ? '<div class="timeline-buttons cover-resize-buttons">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center" valign="middle">
            <a onclick="SK_saveReposition();"><i class="fa fa-pushpin"></i>Lưu vị trí</a>
        </td>
        <td align="center" valign="middle">
            <a onclick="SK_cancelReposition();"><i class="fa fa-remove"></i> Hủy</a>
        </td>
    </tr>
    </table>
    <form class="cover-position-form hidden" method="post">
        <input class="cover-position" name="pos" value="0" type="hidden">
        <input class="screen-width" name="width" value="920" type="hidden">
        <input name="timeline_id" value="'.$user['id'].'" type="hidden">
    </form>
</div>
<div class="timeline-buttons default-buttons">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center" valign="middle">
        <a onclick="javascript:$(\'.change-avatar-input\').click();">
            <i class="fa fa-picture-o"></i>
            Đổi avatar
        </a>
    </td>
        <td align="center" valign="middle">
            <a onclick="javascript:$(\'.cover-image-input\').click();">
                <i class="fa fa-camera-retro"></i> 
                Đổi ảnh bìa
            </a>
        </td>
        
        <td align="center" valign="middle">
            <a onclick="SK_repositionCover();">
                <i class="fa fa-refresh"></i> 
                Đặt vị trí ảnh bìa
            </a>
        </td>
    </tr>
    </table>
</div>
<form class="cover-form hidden" method="post" enctype="multipart/form-data" action="/request.php?t=cover&a=post_upload">
    <input class="cover-image-input hidden" type="file" name="image" accept="image/jpeg,image/png" onchange="javascript:$(\'form.cover-form\').submit();">
    <input name="timeline_id" value="'.$user['id'].'" type="hidden">
</form>' : '').'
<div class="float-clear"></div>';


    //Уведомление о дне рожденья
    if ($user['dayb'] == date('j', time()) && $user['monthb'] == date('n', time())) {
        echo '<div class="gmenu">' . $lng['birthday'] . '!!!</div>';
    }

    // Информация о юзере
    $arg = array(
        'lastvisit' => 1,
        'iphist'    => 1,
        'addinfo'    => 1
    );

    if ($user['id'] != core::$user_id) {
        $arg['footer'] = '<span class="gray">' . core::$lng['where'] . ':</span> ' . functions::display_place($user['id'], $user['place']);
    }

    echo '<div class="user" '.($user['facebook_ID'] ? 'style="background: #edfff9 url(/images/facebook.png) no-repeat right top;" ' : ($user['google_ID'] ? 'style="background: #edfff9 url(/images/googlep.png) no-repeat right top;" ' : '')).'><p>' . functions::display_user($user, $arg) . '</p></div>';
    // Если юзер ожидает подтверждения регистрации, выводим напоминание
    if ($rights >= 7 && !$user['preg'] && empty($user['regadm'])) {
        echo '<div class="rmenu">' . $lng_profile['awaiting_registration'] . '</div>';
    }
    include 'stt.php';
    // Карма
    if ($set_karma['on']) {
        $karma = $user['karma_plus'] - $user['karma_minus'];
        if ($karma > 0) {
            $images = ($user['karma_minus'] ? ceil($user['karma_plus'] / $user['karma_minus']) : $user['karma_plus']) > 10 ? '2' : '1';
            echo '<div class="gmenu">';
        } else if ($karma < 0) {
            $images = ($user['karma_plus'] ? ceil($user['karma_minus'] / $user['karma_plus']) : $user['karma_minus']) > 10 ? '-2' : '-1';
            echo '<div class="rmenu">';
        } else {
            $images = 0;
            echo '<div class="menu">';
        }
        echo '<table  width="100%"><tr><td width="22" valign="top"><img src="' . $set['homeurl'] . '/images/k_' . $images . '.gif"/></td><td>' .
            '<b>' . $lng['karma'] . ' (' . $karma . ')</b>' .
            '<div class="sub">' .
            '<span class="green"><a href="profile.php?act=karma&amp;user=' . $user['id'] . '&amp;type=1">' . $lng['vote_for'] . ' (' . $user['karma_plus'] . ')</a></span> | ' .
            '<span class="red"><a href="profile.php?act=karma&amp;user=' . $user['id'] . '">' . $lng['vote_against'] . ' (' . $user['karma_minus'] . ')</a></span>';
        if ($user['id'] != $user_id) {
            if (!$datauser['karma_off'] && (!$user['rights'] || ($user['rights'] && !$set_karma['adm'])) && $user['ip'] != $datauser['ip']) {
                $sum = mysql_result(mysql_query("SELECT SUM(`points`) FROM `karma_users` WHERE `user_id` = '$user_id' AND `time` >= '" . $datauser['karma_time'] . "'"), 0);
                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `user_id` = '$user_id' AND `karma_user` = '" . $user['id'] . "' AND `time` > '" . (time() - 86400) . "'"), 0);
                if (!$ban && $datauser['postforum'] >= $set_karma['forum'] && $datauser['total_on_site'] >= $set_karma['karma_time'] && ($set_karma['karma_points'] - $sum) > 0 && !$count) {
                    echo '<br /><a href="profile.php?act=karma&amp;mod=vote&amp;user=' . $user['id'] . '">' . $lng['vote'] . '</a>';
                }
            }
        } else {
            $total_karma = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '$user_id' AND `time` > " . (time() - 86400)), 0);
            if ($total_karma > 0) {
                echo '<br /><a href="profile.php?act=karma&amp;mod=new">' . $lng['responses_new'] . '</a> (' . $total_karma . ')';
            }
        }
        echo '</div></td></tr></table></div>';
    }
    $bancount = @mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['id'] . "'"), 0);

    if ($user_id && $bancount) {
        echo '<div class="rmenu" style="margin-top: 1px;"><img src="../images/block.gif" width="16" height="16"/>&#160;<a href="profile.php?act=ban&amp;user=' . $user['id'] . '">' . $lng['infringements'] . '</a> (' . $bancount . ')</div>';
    }

    if ($user_id && $user['id'] != $user_id) {
        echo '<div class="menu"><p>';
        // Контакты
        if (!functions::is_ignor($user['id']) && functions::is_contact($user['id']) != 2) {
            if (!functions::is_friend($user['id'])) {
                $fr_in = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `type`='2' AND `from_id`='$user_id' AND `user_id`='{$user['id']}'"), 0);
                $fr_out = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `type`='2' AND `user_id`='$user_id' AND `from_id`='{$user['id']}'"), 0);
                if ($fr_in == 1) {
                    $friend = '<a class="underline" href="profile.php?act=friends&amp;do=ok&amp;id=' . $user['id'] . '">' . $lng_profile['confirm_friendship'] . '</a> | <a class="underline" href="profile.php?act=friends&amp;do=no&amp;id=' . $user['id'] . '">' . $lng_profile['decline_friendship'] . '</a>';
                } else if ($fr_out == 1) {
                    $friend = '<a class="underline" href="profile.php?act=friends&amp;do=cancel&amp;id=' . $user['id'] . '">' . $lng_profile['canceled_demand_friend'] . '</a>';
                } else {
                    $friend = '<a href="profile.php?act=friends&amp;do=add&amp;id=' . $user['id'] . '">' . $lng_profile['in_friend'] . '</a>';
                }
            } else {
                $friend = '<a href="profile.php?act=friends&amp;do=delete&amp;id=' . $user['id'] . '">' . $lng_profile['remov_friend'] . '</a>';
            }
            echo '<div>' . functions::image('add.gif') . $friend . '</div>';
        }

        echo '<div>' . functions::image('mail.png') . '<a href="'.$home.'/mail/index.php?act=write&id='.$user['id'].'">Nhắn tin</a></div>';

        if (functions::is_contact($user['id']) != 2) {
            echo '<div><img src="../images/del.png" width="16" height="16"/>&#160;<a href="../mail/index.php?act=ignor&amp;id=' . $user['id'] . '&amp;add">' . $lng_profile['add_ignor'] . '</a></div>';
        } else {
            echo '<div><img src="../images/del.png" width="16" height="16"/>&#160;<a href="../mail/index.php?act=ignor&amp;id=' . $user['id'] . '&amp;del">' . $lng_profile['delete_ignor'] . '</a></div>';
        }
        echo '</p>';
        echo '</div>';
    }

if ($user_id && $user['id'] == $user_id) {

    $total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '$user_id'"), 0);
    $total_friends = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `user_id`='$user_id' AND `type`='2' AND `friends`='1'"), 0);
    $new_friends = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `from_id`='$user_id' AND `type`='2' AND `friends`='0';"), 0);
    $online_friends = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` LEFT JOIN `users` ON `cms_contact`.`from_id`=`users`.`id` WHERE `cms_contact`.`user_id`='$user_id' AND `cms_contact`.`type`='2' AND `cms_contact`.`friends`='1' AND `lastdate` > " . (time() - 300) . ""), 0);
    echo '<div class="phdr"><strong>Cá nhân</strong></div>';

    echo '<div class="list2"><p><h3>' . $lng_profile['my_mail'] . '</h3>';
    //Входящие сообщения
    $count_input = mysql_result(mysql_query("
        SELECT COUNT(*) 
        FROM `cms_mail` 
        LEFT JOIN `cms_contact` 
        ON `cms_mail`.`user_id`=`cms_contact`.`from_id`
        AND `cms_contact`.`user_id`='$user_id' 
WHERE `cms_mail`.`from_id`='$user_id'
        AND `cms_mail`.`sys`='0' AND `cms_mail`.`delete`!='$user_id'
        AND `cms_contact`.`ban`!='1' AND `spam`='0'"), 0);
    echo '<div>' . functions::image('mail-inbox.png') . '<a href="../mail/index.php?act=input">' . $lng_profile['received'] . '</a>&nbsp;(' . $count_input . ($new_mail ? '/<span class="red">+' . $new_mail . '</span>' : '') . ')</div>';
//Исходящие сообщения
$count_output = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` LEFT JOIN `cms_contact` ON `cms_mail`.`from_id`=`cms_contact`.`from_id` AND `cms_contact`.`user_id`='$user_id' 
WHERE `cms_mail`.`user_id`='$user_id' AND `cms_mail`.`delete`!='$user_id' AND `cms_mail`.`sys`='0' AND `cms_contact`.`ban`!='1'"), 0);
//Исходящие непрочитанные сообщения
$count_output_new = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` LEFT JOIN `cms_contact` ON `cms_mail`.`from_id`=`cms_contact`.`from_id` AND `cms_contact`.`user_id`='$user_id' 
WHERE `cms_mail`.`user_id`='$user_id' AND `cms_mail`.`delete`!='$user_id' AND `cms_mail`.`read`='0' AND `cms_mail`.`sys`='0' AND `cms_contact`.`ban`!='1'"), 0);
echo '<div>' . functions::image('mail-send.png') . '<a href="../mail/index.php?act=output">' . $lng_profile['sent'] . '</a>&nbsp;(' . $count_output . ($count_output_new ? '/<span class="red">+' . $count_output_new . '</span>' : '') . ')</div>';
$count_systems = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `from_id`='$user_id' AND `delete`!='$user_id' AND `sys`='1'"), 0);
//Системные сообщения
$count_systems_new = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `from_id`='$user_id' AND `delete`!='$user_id' AND `sys`='1' AND `read`='0'"), 0);
echo '<div>' . functions::image('mail-info.png') . '<a href="../mail/index.php?act=systems">' . $lng_profile['systems'] . '</a>&nbsp;(' . $count_systems . ($count_systems_new ? '/<span class="red">+' . $count_systems_new . '</span>' : '') . ')</div>';
//Файлы
$count_file = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE (`user_id`='$user_id' OR `from_id`='$user_id') AND `delete`!='$user_id' AND `file_name`!='';"), 0);
echo '<div>' . functions::image('file.gif') . '<a href="../mail/index.php?act=files">' . $lng['files'] . '</a>&nbsp;(' . $count_file . ')</div>';
if (empty($ban['1']) && empty($ban['3'])) {
    echo '<p><form action="../mail/index.php?act=write" method="post"><input type="submit" value="' . $lng['write'] . '"/></form></p>';
}
// Блок контактов
echo '</p></div><div class="menu"><p><h3>' . $lng['contacts'] . '</h3>';
//Контакты
$count_contacts = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `user_id`='" . $user_id . "' AND `ban`!='1';"), 0);
echo '<div>' . functions::image('user.png') . '<a href="../mail/">' . $lng['contacts'] . '</a>&nbsp;(' . $count_contacts . ')</div>';
//Заблокированные
$count_ignor = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `user_id`='" . $user_id . "' AND `ban`='1';"), 0);
echo '<div>' . functions::image('user-ok.png') . '<a href="profile.php?act=friends">' . $lng_profile['friends'] . '</a>&#160;(' . $total_friends . ($new_friends ? '/<span class="red">+' . $new_friends . '</span>' : '') . ')&#160;<a href="profile.php?act=friends&amp;do=online">' . $lng['online'] . '</a> (' . $online_friends . ')</div>';
echo '<div>' . functions::image('user-block.png') . '<a href="../mail/index.php?act=ignor">' . $lng_profile['banned'] . '</a>&nbsp;(' . $count_ignor . ')</div>';
echo '</p></div>';

// Блок настроек
echo '<div class="bmenu"><a href="/users/profile.php?act=office"><strong>Thiết lập</strong></a></div>';
}
}

require_once('../incfiles/end.php');
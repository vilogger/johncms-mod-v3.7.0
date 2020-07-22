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

    $ten = html_entity_decode($user['name'], ENT_QUOTES, 'UTF-8');
$textl = functions::checkout($ten) . ': ' . $lng_profile['profile_edit'];
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Проверяем права доступа для редактирования Профиля
-----------------------------------------------------------------
*/
if ($user['id'] != $user_id && ($rights < 7 || $user['rights'] > $rights)) {
    echo functions::display_error($lng_profile['error_rights']);
    require('../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Сброс настроек
-----------------------------------------------------------------
*/
if ($rights >= 7 && $rights > $user['rights'] && $act == 'reset') {
    mysql_query("UPDATE `users` SET `set_user` = '', `set_forum` = '', `set_chat` = '' WHERE `id` = '" . $user['id'] . "'");
    echo '<div class="gmenu"><p>' . $lng['settings_default'] . '<br /><a href="profile.php?user=' . $user['id'] . '">' . $lng['to_form'] . '</a></p></div>';
    require('../incfiles/end.php');
    exit;
}
echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . ($user['id'] != $user_id ? $lng['profile'] : $lng_profile['my_profile']) . '</b></a> | ' . $lng['edit'] . '</div>';
if ($user['id'] == $user_id) {
    if (isset($_GET['delavatar'])) {
    /*
    -----------------------------------------------------------------
    Удаляем аватар
    -----------------------------------------------------------------
    */
    @unlink('../files/users/avatar/' . $user['id'] . '.' . $user['avatar_extension']);
    @unlink('../files/users/avatar/' . $user['id'] . '_100x100.' . $user['avatar_extension']);
    @unlink('../files/users/avatar/' . $user['id'] . '_100x75.' . $user['avatar_extension']);
    @unlink('../files/users/avatar/' . $user['id'] . '_thumb.' . $user['avatar_extension']);

    echo '<div class="rmenu">' . $lng_profile['avatar_deleted'] . '</div>';
} elseif (isset($_GET['delphoto'])) {
    /*
    -----------------------------------------------------------------
    Удаляем фото
    -----------------------------------------------------------------
    */
    @unlink('../files/users/photo/' . $user['id'] . '.' . $user['avatar_extension']);
    @unlink('../files/users/photo/' . $user['id'] . '_cover.' . $user['avatar_extension']);

    echo '<div class="rmenu">' . $lng_profile['photo_deleted'] . '</div>';
} elseif (isset($_GET['del-mail_photo'])) {
    @unlink('../files/users/mail-photo/' . $user['id'] . '_small.jpg');
    @unlink('../files/users/mail-photo/' . $user['id'] . '.jpg');
    echo '<div class="rmenu">' . $lng_profile['photo_deleted'] . ' ảnh nền tin nhắn thành công.</div>';
    }
}


if (isset($_POST['submit'])) {
    /*
    -----------------------------------------------------------------
    Принимаем данные из формы, проверяем и записываем в базу
    -----------------------------------------------------------------
    */
    $error = array ();
    $user['imname'] = isset($_POST['imname']) ? functions::check(mb_substr($_POST['imname'], 0, 25)) : '';
    $user['live'] = isset($_POST['live']) ? functions::check(mb_substr($_POST['live'], 0, 50)) : '';
    $user['dayb'] = isset($_POST['dayb']) ? intval($_POST['dayb']) : 0;
    $user['monthb'] = isset($_POST['monthb']) ? intval($_POST['monthb']) : 0;
    $user['yearofbirth'] = isset($_POST['yearofbirth']) ? intval($_POST['yearofbirth']) : 0;
    $user['about'] = isset($_POST['about']) ? functions::check(mb_substr($_POST['about'], 0, 500)) : '';
    $user['status'] = isset($_POST['status']) ? functions::check(mb_substr($_POST['status'], 0, 50)) : '';
    $user['mibile'] = isset($_POST['mibile']) ? functions::check(mb_substr($_POST['mibile'], 0, 40)) : '';
    $user['mail'] = isset($_POST['mail']) ? functions::check(mb_substr($_POST['mail'], 0, 40)) : '';
    $user['mailvis'] = isset($_POST['mailvis']) ? 1 : 0;
    $user['icq'] = isset($_POST['icq']) ? intval($_POST['icq']) : 0;
    $user['skype'] = isset($_POST['skype']) ? functions::check(mb_substr($_POST['skype'], 0, 40)) : '';
    $user['jabber'] = isset($_POST['jabber']) ? functions::check(mb_substr($_POST['jabber'], 0, 40)) : '';
    $user['www'] = isset($_POST['www']) ? functions::check(mb_substr($_POST['www'], 0, 40)) : '';
    // Данные юзера (для Администраторов)
    $user['name'] = isset($_POST['name']) ? functions::check(mb_substr($_POST['name'], 0, 40)) : $user['name'];
    $user['karma_off'] = isset($_POST['karma_off']) ? 1 : 0;
    $user['sex'] = isset($_POST['sex']) && $_POST['sex'] == 'm' ? 'm' : 'zh';
    $user['rights'] = isset($_POST['rights']) ? abs(intval($_POST['rights'])) : $user['rights'];
    // Проводим необходимые проверки
    if($user['rights'] > $rights || $user['rights'] > 9 || $user['rights'] < 0)
        $user['rights'] = 0;
    if ($rights >= 7) {
        if (mb_strlen($user['name']) < 2 || mb_strlen($user['name']) > 40)
            $error[] = $lng_profile['error_nick_lenght'];

    }
    if ($user['dayb'] || $user['monthb'] || $user['yearofbirth']) {
        if ($user['dayb'] < 1 || $user['dayb'] > 31 || $user['monthb'] < 1 || $user['monthb'] > 12)
            $error[] = $lng_profile['error_birth'];
        $lat_nick = functions::rus_lat(mb_strtolower($user['name']));
    }
    if ($user['icq'] && ($user['icq'] < 10000 || $user['icq'] > 999999999))
        $error[] = $lng_profile['error_icq'];
    if (!$error) {
        mysql_query("UPDATE `users` SET
            `status` = '" . $user['status'] . "',
            `imname` = '" . $user['imname'] . "',
            `live` = '" . $user['live'] . "',
            `dayb` = '" . $user['dayb'] . "',
            `monthb` = '" . $user['monthb'] . "',
            `yearofbirth` = '" . $user['yearofbirth'] . "',
            `about` = '" . $user['about'] . "',
            `mibile` = '" . $user['mibile'] . "',
            `mail` = '" . $user['mail'] . "',
            `mailvis` = '" . $user['mailvis'] . "',
            `icq` = '" . $user['icq'] . "',
            `skype` = '" . $user['skype'] . "',
            `jabber` = '" . $user['jabber'] . "',
            `www` = '" . $user['www'] . "'
            WHERE `id` = '" . $user['id'] . "'
        ");
        if ($rights >= 7) {
            mysql_query("UPDATE `users` SET
                `name` = '" . $user['name'] . "',
                `karma_off` = '" . $user['karma_off'] . "',
                `sex` = '" . $user['sex'] . "',
                `rights` = '" . $user['rights'] . "'
                WHERE `id` = '" . $user['id'] . "'
            ");
        }
        echo '<div class="gmenu">' . $lng_profile['data_saved'] . '</div>';
    } else {
        echo functions::display_error($error);
    }
}

/*
-----------------------------------------------------------------
Форма редактирования анкеты пользователя
-----------------------------------------------------------------
*/
echo '<form action="profile.php?act=edit&amp;user=' . $user['id'] . '" method="post">' .
    '<div class="gmenu"><p>' .
    $lng['login_name'] . ': <b>' . $user['name_lat'] . '</b><br />';
if ($rights >= 7) {
    echo $lng['nick'] . ': (' . $lng_profile['nick_lenght'] . ')<br /><input type="text" value="' . $user['name'] . '" name="name" /><br />';
} else {
    echo '<span class="gray">' . $lng['nick'] . ':</span> <b>' . $user['name'] . '</b><br />';
}
echo $lng['status'] . ': (' . $lng_profile['status_lenght'] . ')<br /><input type="text" value="' . $user['status'] . '" name="status" /><br /></p>';

if ($user['id'] == $user_id) {
echo '<p>' . $lng['avatar'] . ':<br />';
$link = '';
if (file_exists(('../files/users/avatar/' . $user['id'] . '.' . $user['avatar_extension']))) {
    echo '<a href="../files/users/avatar/' . $user['id'] . '.' . $user['avatar_extension'].'"><img src="../files/users/avatar/' . $user['id'] . '_thumb.' . $user['avatar_extension'].'?'.$time.'" alt="' . $user['name'] . '" /></a><br />';
    $link = ' | <a href="profile.php?act=edit&amp;user=' . $user['id'] . '&amp;delavatar">' . $lng['delete'] . '</a>';
}
echo '<small><a href="profile.php?act=images&amp;mod=avatar&amp;user=' . $user['id'] . '">' . $lng_profile['upload'] . '</a>';

echo $link . '</small></p>';
echo '<p>' . $lng_profile['photo'] . ' bìa:<br />';
$link = '';
if (file_exists('../files/users/photo/' . $user['id'] . '.' . $user['cover_extension'])) {
    echo '<a href="../files/users/photo/' . $user['id'] . '.' . $user['cover_extension'].'"><img src="../files/users/photo/' . $user['id'] . '_thumb.' . $user['cover_extension'].'?'.$time.'" alt="' . $user['name'] . '" border="0" /></a><br />';
    $link = ' | <a href="profile.php?act=edit&amp;user=' . $user['id'] . '&amp;delphoto">' . $lng['delete'] . '</a>';
}
echo '<small><a href="profile.php?act=images&amp;mod=up_photo&amp;user=' . $user['id'] . '">' . $lng_profile['upload'] . '</a>' . $link . '</small><br />' .
    '</p>';
echo '<p>' . $lng_profile['photo'] . ' nền tin nhắn:<br />';
$link = '';
if (file_exists('../files/users/mail-photo/' . $user['id'] . '_small.jpg')) {
    echo '<a href="../files/users/mail-photo/' . $user['id'] . '.jpg"><img src="../files/users/mail-photo/' . $user['id'] . '_small.jpg" alt="' . $user['name'] . '" /></a><br />';
    $link = ' | <a href="profile.php?act=edit&amp;user=' . $user['id'] . '&amp;del-mail_photo">' . $lng['delete'] . '</a>';
}
echo '<small><a href="profile.php?act=images&amp;mod=mail_photo&amp;user=' . $user['id'] . '">' . $lng_profile['upload'] . '</a>' . $link . '</small><br />' .
    '</p>';
}
    echo '</div>';
    echo '<div class="menu">' .
    '<p><h3><img src="../images/contacts.png" width="16" height="16" class="left" />&#160;' . $lng_profile['personal_data'] . '</h3><br />' .
    $lng_profile['name'] . ':<br /><input type="text" value="' . $user['imname'] . '" name="imname" /></p>' .
    '<p>' . $lng_profile['birth_date'] . '<br />' .
    '<input type="text" value="' . $user['dayb'] . '" size="2" maxlength="2" name="dayb" style="width: 17px;" />.' .
    '<input type="text" value="' . $user['monthb'] . '" size="2" maxlength="2" name="monthb" style="width: 17px;" />.' .
    '<input type="text" value="' . $user['yearofbirth'] . '" size="4" maxlength="4" name="yearofbirth" style="width: 40px;" /></p>' .
    '<p>' . $lng_profile['city'] . ':<br /><input type="text" value="' . $user['live'] . '" name="live" /></p>' .
    '<p>' . $lng_profile['about'] . ':<br /><textarea rows="' . $set_user['field_h'] . '" name="about">' . strip_tags($user['about']) . '</textarea></p>' .
    '<p><h3><img src="../images/mail.png" width="16" height="16" class="left" />&#160;' . $lng_profile['communication'] . '</h3><br />' .
    $lng_profile['phone_number'] . ':<br /><input type="text" value="' . $user['mibile'] . '" name="mibile" /><br />' .
    '</p><p>E-mail:<br /><small>' . $lng_profile['email_warning'] . '</small><br />' .
    '<input type="text" value="' . $user['mail'] . '" name="mail" /><br />' .
    '<div class="mdn-section"><div class="mdn-group block-group"><label class="mdn-switch modern-switch"><input type="checkbox" name="mailvis" value="1" ' . ($user['mailvis'] ? 'checked="checked"' : '') . ' /><span class="switch-toggle"></span><span class="switch-label"> ' . $lng_profile['show_in_profile'] . '</span></label></div></div><br /><br /></p>' .
    '<p>ICQ:<br /><input type="text" value="' . $user['icq'] . '" name="icq" size="10" maxlength="10" /></p>' .
    '<p>Skype:<br /><input type="text" value="' . $user['skype'] . '" name="skype" /></p>' .
    '<p>Jabber:<br /><input type="text" value="' . $user['jabber'] . '" name="jabber" /></p>' .
    '<p>' . $lng_profile['site'] . ':<br /><input type="text" value="' . $user['www'] . '" name="www" /></p>' .
    '</div>';
// Административные функции
if ($rights >= 7) {
    echo '<div class="rmenu"><p><h3><img src="../images/settings.png" width="16" height="16" class="left" />&#160;' . $lng['settings'] . '</h3><br /><ul>';
    if ($rights == 9) {
        echo '<li><div class="mdn-group block-group">
            <label class="mdn-switch">
                <input name="karma_off" type="checkbox" value="1" ' . ($user['karma_off'] ? 'checked="checked"' : '') . ' />
                <span class="switch-toggle"></span>
                <span class="switch-label"><span class="red"><b> ' . $lng_profile['deny_karma'] . '</b></span></span>
            </label>
        </div></li>';
    }
    echo '<li><a href="profile.php?act=password&amp;user=' . $user['id'] . '">' . $lng['change_password'] . '</a></li>';
    if($rights > $user['rights'])
        echo '<li><a href="profile.php?act=reset&amp;user=' . $user['id'] . '">' . $lng['reset_settings'] . '</a></li>';
    echo '<li>' . $lng_profile['specify_sex'] . ':<br />' .
        '<div class="mdn-section"><div class="mdn-group block-group">' .
        '<label class="mdn-option option-tick"><input type="radio" name="sex" value="m" ' . ($user['sex'] == 'm' ? 'checked="checked"' : '') . ' /><span class="mdn-checkbox"></span><span class="option-label"> ' . $lng_profile['sex_m'] . '</span></label><br />' .
        '<label class="mdn-option option-tick"><input type="radio" name="sex" value="zh" ' . ($user['sex'] == 'zh' ? 'checked="checked"' : '') . ' /><span class="mdn-checkbox"></span><span class="option-label"> ' . $lng_profile['sex_w'] . '</span></label></div></div></li>' .
        '</ul></p>';
    if ($user['id'] != $user_id) {
        echo '<p><h3><img src="../images/forbidden.png" width="16" height="16" class="left" />&#160;' . $lng_profile['rank'] . '</h3><ul>' .
            '<div class="mdn-section"><div class="mdn-group block-group">' .
            '<label class="mdn-option"><input type="radio" name="rights" value="0" ' . (!$user['rights'] ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="#000000">Member</font></b> </span></label><br />' .
            '<label class="mdn-option"><input type="radio" name="rights" value="1" ' . ($user['rights'] == 1 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="#eba501">V.I.P</font></b> </span></label><br />' .
            '<label class="mdn-option"><input type="radio" name="rights" value="2" ' . ($user['rights'] == 2 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="#9932CC">Auto</font></b> </span></label><br />' .
            '<label class="mdn-option"><input type="radio" name="rights" value="3" ' . ($user['rights'] == 3 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="#0000FF">JavaMaster</font></b> </span></label><br />' .
            '<label class="mdn-option"><input type="radio" name="rights" value="4" ' . ($user['rights'] == 4 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="#0896c3">WapMaster</font></b> </span></label><br />' .
            '<label class="mdn-option"><input type="radio" name="rights" value="5" ' . ($user['rights'] == 5 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="#228622">S.W.A.T</font></b> </span></label><br />' .
            '<label class="mdn-option"><input type="radio" name="rights" value="6" ' . ($user['rights'] == 6 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="#7192A8">Mod</font></b> </span></label><br />';
        if ($rights == 9) {
            echo '<label class="mdn-option"><input type="radio" name="rights" value="7" ' . ($user['rights'] == 7 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="#F267AB">SMod</font></b> </span></label><br />' .
                '<label class="mdn-option"><input type="radio" name="rights" value="8" ' . ($user['rights'] == 8 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="red">Admin</font></b> </span></label><br />' .
                '<label class="mdn-option"><input type="radio" name="rights" value="9" ' . ($user['rights'] == 9 ? 'checked="checked"' : '') . ' /><span class="mdn-radio"></span><span class="option-label"> <b><font color="red">Sáng Lập</font></b> </span></label><br />';
        }
        echo '</div></div></ul></p>';
    }
    echo '</div>';
}
echo '<div class="gmenu"><input type="submit" value="' . $lng['save'] . '" name="submit" /></div>' .
    '</form>' .
    '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '">' . $lng['to_form'] . '</a></div>';
?>
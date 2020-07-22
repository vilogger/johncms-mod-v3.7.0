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
$headmod = 'office';
$textl = 'Thiết lập tài khoản';
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if ($user['id'] != $user_id) {
    echo functions::display_error($lng['access_forbidden']);
    require('../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Личный кабинет пользователя
-----------------------------------------------------------------
*/

// Блок настроек
echo '<div class="phdr"><strong>Thiết lập</strong></div>' .
    '<div class="menu">' . functions::image('settings.png') . '<a href="profile.php?act=settings">' . $lng['system_settings'] . '</a></div>' .
    '<div class="menu">' . functions::image('user-edit.png') . '<a href="profile.php?act=edit">' . $lng_profile['profile_edit'] . '</a></div>' .
    '<div class="menu">' . functions::image('lock.png') . '<a href="profile.php?act=password">' . $lng['change_password'] . '</a></div>';
if ($rights >= 1) {
    echo '<div class="menu">' . functions::image('forbidden.png') . '<span class="red"><a href="../' . $set['admp'] . '/index.php"><b>' . $lng['admin_panel'] . '</b></a></span></div>';
}
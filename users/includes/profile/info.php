<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Подробная информация, контактные данные
-----------------------------------------------------------------
*/

$ten = html_entity_decode($user['name'], ENT_QUOTES, 'UTF-8');
$textl = functions::checkout($ten) . ': ' . $lng['information'];
require('../incfiles/head.php');
echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng['information'] . '</div>';
if ($user['id'] == $user_id || ($rights >= 7 && $rights > $user['rights']))
    echo '<div class="topmenu"><a href="profile.php?act=edit&amp;user=' . $user['id'] . '">' . $lng['edit'] . '</a></div>';
echo '<div class="user"><p>' . functions::display_user($user, array ('iphide' => 1,)) . '</p></div>' .
    '<div class="list2"><p>' .
    '<h3><img src="../images/contacts.png" width="16" height="16" class="left" />&#160;' . $lng_profile['personal_data'] . '</h3>' .
    '<ul>';
if (file_exists('../files/users/avatar/' . $user['id'] . '_thumb.' . $user['avatar_extension']))
    echo '<a href="../files/users/avatar/' . $user['id'] . '.' . $user['avatar_extension'] . '"><img src="../files/users/avatar/' . $user['id'] . '_thumb.' . $user['avatar_extension'] . '" alt="' . $user['name'] . '" border="0" /></a>';
if (file_exists('../files/users/photo/' . $user['id'] . '_thumb.' . $user['cover_extension']))
    echo '<a href="../files/users/photo/' . $user['id'] . '.' . $user['cover_extension'] . '"><img src="../files/users/photo/' . $user['id'] . '_thumb.' . $user['cover_extension'] . '" alt="' . $user['name'] . '" border="0" /></a>';
echo (empty($user['imname']) ? '' : '<li><span class="gray">' . $lng_profile['name'] . ':</span> '.$user['imname'].'</li>') .
    (empty($user['dayb']) ? '' : '<li><span class="gray">Ngày sinh: </span> ' .sprintf("%02d", $user['dayb']) . '.' . sprintf("%02d", $user['monthb']) . '.' . $user['yearofbirth'] . '</li>') .
    '<li><span class="gray">Giới tính: ' . ($user['sex'] == 'm' ? 'Nam' : 'Nữ') . '</span></li>' .
    (empty($user['live']) ? '' : '<li><span class="gray">' . $lng_profile['city'] . ':</span> ' . $user['live'] . '</li>') .
    (empty($user['about']) ? '' : '<li><span class="gray">' . $lng_profile['about'] . ':</span> <br />' . functions::smileys(bbcode::tags($user['about'])) . '</li>') .
    '</ul></p><p>';


if (!empty($user['mibile']) || !empty($user['icq']) || !empty($user['skype']) || !empty($user['jabber']) || !empty($user['www']) || (!empty($user['mail']) && $user['mailvis'] || $rights >= 7 || $user['id'] == $user_id)) {
    echo '<h3><img src="../images/mail.png" width="16" height="16" class="left" />&#160;Liên hệ</h3><ul>' .
    (empty($user['mibile']) ? '' : '<li><span class="gray">' . $lng_profile['phone_number'] . ':</span> ' . $user['mibile'] . '</li>');

if (!empty($user['mail']) && $user['mailvis'] || $rights >= 7 || $user['id'] == $user_id) {
    echo '<li><span class="gray">E-mail:</span> '.$user['mail'] . ($user['mailvis'] ? '' : '<span class="gray"> [' . $lng_profile['hidden'] . ']</span>') . '</li>';
}
echo (empty($user['icq']) ? '' : '<li><span class="gray">ICQ:</span> ' . $user['icq'] . '</li>') .
    (empty($user['skype']) ? '' : '<li><span class="gray">Skype:</span> ' . $user['skype'] . '</li>') .
    (empty($user['jabber']) ? '' : '<li><span class="gray">Jabber:</span> ' . $user['jabber'] . '</li>') .
    (empty($user['www']) ? '' : '<li><span class="gray">' . $lng_profile['site'] . ':</span> ' . bbcode::tags($user['www']) . '</li>') .
    '</ul></p>';
}
    echo '<p><h3>' . functions::image('rate.gif') . $lng['statistics'] . '</h3><ul>';
echo '<li><span class="gray">Xu:</span> ' . $user['balans'] . '</li>' .
    '<li><span class="gray">Gold:</span> ' . $user['vgold'] . '</li>' .
    '<li><span class="gray">Ngày đăng ký:</span> ' . date("d.m.Y", $user['datereg']) . '</li>' .
    '<li><span class="gray">' . ($user['sex'] == 'm' ? $lng_profile['stayed_m'] : $lng_profile['stayed_w']) . ':</span> ' . functions::timecount($user['total_on_site']) . '</li>';
$lastvisit = time() > $user['lastdate'] + 300 ? functions::display_date($user['lastdate']) : false;
if ($lastvisit)
    echo '<li><span class="gray">' . $lng['last_visit'] . ':</span> ' . $lastvisit . '</li>';
echo'</ul></p><p>' .
    '<h3>' . functions::image('activity.gif') . $lng_profile['activity'] . '</h3><ul>' .
    '<li><span class="gray">' . $lng['forum'] . ':</span> <a href="profile.php?act=activity&amp;user=' . $user['id'] . '">' . $user['postforum'] . '</a></li>' .
    '<li><span class="gray">' . $lng['guestbook'] . ':</span> <a href="profile.php?act=activity&amp;mod=comments&amp;user=' . $user['id'] . '">' . $user['postguest'] . '</a></li>' .
    '<li><span class="gray">' . $lng['comments'] . ':</span> ' . $user['komm'] . '</li>' .
    '</ul></p>' .
    '<p><h3>' . functions::image('award.png') . $lng_profile['achievements'] . '</h3>';
$num = array(
    50,
    100,
    500,
    1000,
    5000
);
$query = array(
    'postforum' => $lng['forum'],
    'postguest' => $lng['guestbook'],
    'komm' => $lng['comments']
);
echo '<table border="0" cellspacing="0" cellpadding="0"><tr>';
foreach ($num as $val) {
    echo '<td width="28" align="center"><small>' . $val . '</small></td>';
}
echo '<td></td></tr>';
foreach ($query as $key => $val) {
    echo '<tr>';
    foreach ($num as $achieve) {
        echo'<td align="center">' . functions::image(($user[$key] >= $achieve ? 'green' : 'red') . '.gif') . '</td>';
    }
    echo'<td><small><b>' . $val . '</b></small></td></tr>';
}
echo'</table></p></div>' .
    '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '">' . $lng['back'] . '</a></div>';
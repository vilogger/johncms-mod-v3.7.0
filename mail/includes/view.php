<?php

defined('_IN_JOHNCMS') or die('Error: restricted access');
$textl = 'Chi tiết tin nhắn';
require_once('../incfiles/head.php');

$row = mysql_fetch_assoc(mysql_query("SELECT * FROM `cms_mail` WHERE `id`='$id' AND (`from_id`='$user_id' OR `user_id`='$user_id') AND `delete`!='$user_id' AND `sys`!='1' AND `spam`='0'"));

if (!$row) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}
if($row['user_id'] == $user_id){
    $mail = $row['from_id'];
}else{
    $mail = $row['user_id'];
}
$uid = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id`='".$row['user_id']."'"));
echo '<div class="phdr"><a href="/mail/index.php?act=write&id='.$mail.'">Mail</a> | Chi tiết tin nhắn</div>';
$arg = array(
    'iphide' => '1'
);
echo '<div class="menu">'.functions::display_user($uid, $arg).'</div>';
            $post = $row['text'];
            $post = functions::checkout($post, 1, 1);
            if ($set_user['smileys'])
                $post = functions::smileys($post, $rights >= 1 ? 1 : 0);
echo '<div class="list1"><div class="gray font-xs" style="margin: 0px; padding: 0px 5px 5px 5px;"> ' . functions::display_date($row['time']) . '</div>'.$post;
            if ($row['file_name']) {
                $att_ext = strtolower(functions::format('./files/mail/' . $row['file_name']));
                $pic_ext = array(
                    'gif',
                    'jpg',
                    'jpeg',
                    'png'
                );
                if (in_array($att_ext, $pic_ext)) {
                     echo '<div align="center" style="font-size: 12px;"><a href="index.php?act=load&amp;id=' . $row['id'] . '"><img src="/forum/thumbinal.php?file=../../mail/' . (urlencode($row['file_name'])) . '" alt="" style="-webkit-border-radius: 3px; border-radius: 3px; -moz-border-radius: 3px;" /></a><br />(' . formatsize($row['size']) . ')</div>';
                } else {
                    echo '<div align="center" style="font-size: 12px;"><img src="/images/bb/dl.png" alt="" /> <a href="index.php?act=load&amp;id=' . $row['id'] . '">' . $row['file_name'] . '</a> (' . formatsize($row['size']) . ')(' . $row['count'] . ')</div>';
                }
            }
echo ($row['read'] == 1 && $row['user_id'] == $user_id ? '<div class="gray" style="margin: 5px 0px 0px 0px;"> <img src="/images/daxem.png" alt="ok" /> Đã được xem</div>' : '').'</div>';
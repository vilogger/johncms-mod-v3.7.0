<?php
define('_IN_JOHNCMS', 1);
$rootpath = '';
require('incfiles/core.php');

if($id){
$lng_mail = core::load_lng('mail');

function formatsize($size)
{
    // Форматирование размера файлов
    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' Gb';
    } elseif ($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' Mb';
    } elseif ($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' Kb';
    } else {
        $size = $size . ' b';
    }

    return $size;
}
$set_mail = unserialize($user['set_mail']);
$out = '';
$total = 0;
$ch = 0;
$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    $qs = mysql_fetch_assoc($req);
if (isset($_POST['submit']) && empty($ban['1']) && empty($ban['3']) && !functions::is_ignor($id)) {
    if (!$id) {
        $name = isset($_POST['nick']) ? functions::rus_lat(mb_strtolower(trim($_POST['nick']))) : '';
    }
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    if ($set_user['translit'] && isset($_POST['msgtrans']))
        $text = functions::trans($text);

    $error = array();

    if (!$id && empty($name))
        $error[] = $lng_mail['indicate_login_grantee'];
    if (empty($text))
        $error[] = $lng_mail['message_not_empty'];
    elseif (mb_strlen($text) < 2 || mb_strlen($text) > 5000)
        $error[] = $lng_mail['error_long_message'];
    if (($id && $id == $user_id) || !$id && $datauser['name_lat'] == $name)
        $error[] = $lng_mail['impossible_add_message'];
    $flood = functions::antiflood();
    if ($flood)
        $error[] = $lng['error_flood'] . ' ' . $flood . $lng['sec'];
    if (empty($error)) {
        if (!$id) {
            $query = mysql_query("SELECT * FROM `users` WHERE `name_lat`='" . mysql_real_escape_string($name) . "' LIMIT 1");
            if (mysql_num_rows($query) == 0) {
                $error[] = $lng['error_user_not_exist'];
            } else {
                $user = mysql_fetch_assoc($query);
                $id = $user['id'];
                $set_mail = unserialize($user['set_mail']);
            }
        } else {
            $set_mail = unserialize($qs['set_mail']);
        }

        if (empty($error)) {
            if ($set_mail) {
                if ($rights < 1) {
                    if ($set_mail['access']) {
                        if ($set_mail['access'] == 1) {
                            $query = mysql_query("SELECT * FROM `cms_contact` WHERE `user_id`='" . $id . "' AND `from_id`='" . $user_id . "' LIMIT 1");
                            if (mysql_num_rows($query) == 0) {
                                $error[] = $lng_mail['write_contacts'];
                            }
                        } else if ($set_mail['access'] == 2) {
                            $query = mysql_query("SELECT * FROM `cms_contact` WHERE `user_id`='" . $id . "' AND `from_id`='" . $user_id . "' AND `friends`='1' LIMIT 1");
                            if (mysql_num_rows($query) == 0) {
                                $error[] = $lng_mail['write_friends'];
                            }
                        }
                    }
                }
            }
        }
    }

    if (empty($error)) {
        $ignor = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact`
WHERE `user_id`='" . $user_id . "'
AND `from_id`='" . $id . "'
AND `ban`='1';"), 0);
        if ($ignor)
            $error[] = $lng_mail['error_user_ignor_in'];
        if (empty($error)) {
            $ignor_m = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact`
WHERE `user_id`='" . $id . "'
AND `from_id`='" . $user_id . "'
AND `ban`='1';"), 0);
            if ($ignor_m)
                $error[] = $lng_mail['error_user_ignor_out'];
        }
    }

    if (empty($error)) {
        $q = mysql_query("SELECT * FROM `cms_contact`
WHERE `user_id`='" . $user_id . "' AND `from_id`='" . $id . "';");
        if (mysql_num_rows($q) == 0) {
            mysql_query("INSERT INTO `cms_contact` SET
`user_id` = '" . $user_id . "',
`from_id` = '" . $id . "',
`time` = '" . time() . "'");
            $ch = 1;
        }
        $q1 = mysql_query("SELECT * FROM `cms_contact`
WHERE `user_id`='" . $id . "' AND `from_id`='" . $user_id . "';");
        if (mysql_num_rows($q1) == 0) {
            mysql_query("INSERT INTO `cms_contact` SET
`user_id` = '" . $id . "',
`from_id` = '" . $user_id . "',
`time` = '" . time() . "'");
            $ch = 1;
        }

    }

    // Проверяем на повтор сообщения
    if (empty($error)) {
        $rq = mysql_query("SELECT * FROM `cms_mail`
        WHERE `user_id` = $user_id
        AND `from_id` = $id
        ORDER BY `id` DESC
        LIMIT 1
        ") or die(mysql_error());
        $rres = mysql_fetch_assoc($rq);
        if ($rres['text'] == $text) {
            $error[] = $lng['error_message_exists'];
        }
    }


    if (empty($error)) {
        mysql_query("INSERT INTO `cms_mail` SET
`user_id` = '" . $user_id . "',
`from_id` = '" . $id . "',
`text` = '" . mysql_real_escape_string($text) . "',
`time` = '" . time() . "',
`file_name` = '',
`size` = '0'") or die(mysql_error());

        mysql_query("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = '$user_id';");
        if ($ch == 0) {
            mysql_query("UPDATE `cms_contact` SET `time` = '" . time() . "' WHERE `user_id` = '" . $user_id . "' AND
`from_id` = '" . $id . "';");
            mysql_query("UPDATE `cms_contact` SET `time` = '" . time() . "' WHERE `user_id` = '" . $id . "' AND
`from_id` = '" . $user_id . "';");
        }
    } else {
        echo "<script>
            var no = '<div id=check><img src=/images/del.png></div>';
            var t = setTimeout(function(){
                $(\"#CheckSend\").html(no);
                setTimeout('$(\"#check\").remove()', 3000);
            }, 0);
function stopCount() {
                $(\"#check\").remove();
}
        </script>";
    }
}

       $out .= '<div class="phdr">'.$lng_mail['personal_correspondence'] . ' <a href="/users/profile.php?user=' . $qs['id'] . '">' . $qs['name'] . '</a>'.(time() > $qs['lastdate'] + 30 ? '<span class="font-xs" style="color: #0af3f5;"> (Đã Off)</span>' : '<span class="font-xs" style="color: #0af3f5;"> (Đang ON)</span>').'</div>';
$totalchat = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE ((`user_id`='$id' AND `from_id`='$user_id') OR (`user_id`='$user_id' AND `from_id`='$id')) AND `sys`!='1' AND `delete`!='$user_id' AND `spam`='0'"), 0);;
  if ($totalchat) {
        if ($totalchat > $kmess) $out .= '<div class="topmenu">' . functions::display_pagination('index.php?act=write&amp;id=' . $id . '&amp;', $start, $totalchat, $kmess) . '</div>';
    if (file_exists('files/users/mail-photo/'.$user_id.'.jpg')) {
            $out .= '<div style="background:url(/files/users/mail-photo/'.$user_id.'.jpg);background-position: center;background-size: cover;width: 100%;height: auto;background-repeat: no-repeat;"><div style="padding: 4px 10px 5px 5px;">';
        }else{
            $out .= '<div style="background: #fff;"><div style="padding: 4px 10px 5px 5px;">';
        }
       $req = mysql_query("SELECT `cms_mail`.*, `cms_mail`.`id` as `mid`, `cms_mail`.`time` as `mtime`, `users`.*
            FROM `cms_mail`
            LEFT JOIN `users` ON `cms_mail`.`user_id`=`users`.`id`
            WHERE ((`cms_mail`.`user_id`='$id' AND `cms_mail`.`from_id`='$user_id') OR (`cms_mail`.`user_id`='$user_id' AND `cms_mail`.`from_id`='$id'))
            AND `cms_mail`.`delete`!='$user_id'
            AND `cms_mail`.`sys`!='1'
            AND `cms_mail`.`spam`='0'
            ORDER BY `cms_mail`.`time` DESC
            LIMIT " . $start . "," . $kmess);
         $i = 1;
        $mass_read = array();

        while (($row = mysql_fetch_assoc($req)) !== FALSE) {
            $out .= '<table width="100%" style="table-layout: fixed; word-wrap: break-word;"><tr>';


            if ($row['from_id'] == $user_id) {
                    $avatar = '<td style="width: 32px;" valign="top"><img src="' . $home . '/avatar/'.$id.'-16-32.png" width="32" height="32" alt="" />&#160;';
                $out .= $avatar.'</td><td align="left"><div class="tmail">';
                $mau = 'color:#494949;';
                $mautime = 'color: rgba(0, 100, 100, 0.7);';
                $imgxoa = '<img src="/images/user/del1.png" />';
            } else {
                $out .= (!$row['read'] ? '<td style="width: 6px;">' : '<td style="width: 13px;padding: 4px 0px 0px 0px;" valign="top"><img src="/images/daxem.png" alt="ok" />').'</td><td><div class="fmail">';
                $mau = 'color:#fff;';
                $mautime = 'color: rgba(0, 255, 199, 0.8);';
                $imgxoa = '<img src="/images/user/del2.png" />';
            }

            if ($row['read'] == 0 && $row['from_id'] == $user_id)
                $mass_read[] = $row['mid'];
            $post = $row['text'];
            $post = functions::checkout($post, 1, 1);
            if ($set_user['smileys'])
                $post = functions::smileys($post, $row['rights'] >= 1 ? 1 : 0);
            if ($row['file_name']) {
                $att_ext = strtolower(functions::format('./files/mail/' . $row['file_name']));
                $pic_ext = array(
                    'gif',
                    'jpg',
                    'jpeg',
                    'png'
                );
                if (in_array($att_ext, $pic_ext)) {
                    $post .= '<div align="center" style="font-size: 12px;"><a href="index.php?act=load&amp;id=' . $row['mid'] . '"><img src="/forum/thumbinal.php?file=../../mail/' . (urlencode($row['file_name'])) . '" alt="" style="-webkit-border-radius: 3px; border-radius: 3px; -moz-border-radius: 3px;" /></a><br />(' . formatsize($row['size']) . ')</div>';
                } else {
                    $post .= '<div align="center" style="font-size: 12px;"><img src="/images/bb/dl.png" alt="" /> <a href="index.php?act=load&amp;id=' . $row['mid'] . '">' . $row['file_name'] . '</a> (' . formatsize($row['size']) . ')(' . $row['count'] . ')</div>';
                }
            }
            $out .= '<font style="'.$mau.'">'.$post.'</font><div><table class="font-xs" style="width: 100%;"><tr><td><span class="font-xs" style="'.$mautime.'">' . functions::display_date($row['mtime']) . '</span></td><td align="right"><a href="index.php?act=view&amp;id=' . $row['mid'] . '" style="font-size: 12px; font-weight: bold; padding: 1px 6px 1px 6px;">#</a><a href="index.php?act=delete&amp;id=' . $row['mid'] . '" style="padding: 1px 6px 1px 6px;">'.$imgxoa.'</a></td></tr></table></div>';
            $out .= '</div></td></tr></table>';
          ++$i; 
        }
        $out .= '</div></div>';
        if ($mass_read) {
            $result = implode(',', $mass_read);
            mysql_query("UPDATE `cms_mail` SET `read`='1' WHERE `from_id`='$user_id' AND `id` IN (" . $result . ")");
        }
    $out .= '<div class="phdr">' . $lng['total'] . ': ' . $totalchat . '</div>';
    if ($totalchat > $kmess) {
        $out .= '<div class="topmenu">' . functions::display_pagination('index.php?act=write&amp;id=' . $id . '&amp;', $start, $totalchat, $kmess) . '</div>';
    }
  } else {
    $out .= '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
  }
    if ($totalchat) {
        $out .= '<p><a href="index.php?act=write&amp;mod=clear&amp;id=' . $id . '">' . $lng_mail['clear_messages'] . '</a></p>';
    }
echo $out;

}else{
    header('Location: /mail/');
}
?>
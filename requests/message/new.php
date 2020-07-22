<?php
$html = '';
$id = '';
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
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    if ($set_user['translit'] && isset($_POST['msgtrans']))
        $text = functions::trans($text);

$googlemap = isset($_POST['google-map']) ? mb_substr(trim($_POST['google-map']), 0, 50) : '';
if (!empty($googlemap) && mb_strlen($googlemap) >= 4){
    $googlemap = functions::checkin($googlemap);
    $text = $text.' [map]'.$googlemap.'[/map]';
}
    $soundcloud_uri = isset($_POST['soundcloud_uri']) ? $_POST['soundcloud_uri'] : false;
if ($soundcloud_uri){
    $text = $text.' [soundcloud]'.$soundcloud_uri.'[/soundcloud]';
}
$youtube_id = isset($_POST['youtube_video_id']) ? $_POST['youtube_video_id'] : false;
if ($youtube_id){
    $text = $text.' [youtube]'.$youtube_id.'[/youtube]';
}
    $error = array();

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

            $data = array(
                'status' => 200,
                'html' => $html
            );

            header("Content-type: application/json; charset=utf-8");
            echo json_encode($data);
            exit();
        }
    }
}

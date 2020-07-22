<?php
$html = '';
$error = array();
$flood = FALSE;

$msg = isset($_POST['text']) ? functions::checkin(mb_substr(trim($_POST['text']), 0, 5000)) : '';

$from = $user_id ? $login : '';

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

$flood = functions::antiflood();
if (empty($msg)){
    $error[] = $lng['error_empty_message'];
}
if ($ban['1'] || $ban['13'])
   $error[] = $lng['access_forbidden'];

if ($flood)
    $error = $lng['error_flood'] . ' ' . $flood . '&#160;' . $lng['seconds'];
if (!$error) {
       $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = '$user_id' ORDER BY `time` DESC");
       $res = mysql_fetch_array($req);
       if ($res['text'] == $msg) {
           $error[] = 'error';
       }
   }
   if (!$error) {
       mysql_query("INSERT INTO `guest` SET
            `adm` = '$admset',
            `time` = '" . time() . "',
            `user_id` = '$user_id',
            `name` = '$from',
            `text` = '" . mysql_real_escape_string($msg) . "',
            `ip` = '" . core::$ip . "',
            `browser` = '" . mysql_real_escape_string($agn) . "'
       ");

        $fadd = mysql_insert_id();

        $post = functions::checkout($msg, 1, 1);
        if ($set_user['smileys'])
        $post = functions::smileys($post, 0);          $html .= '<div id="story_'.$fadd.'" class="menu story_'.$fadd.'" data-story-id="'.$fadd.'">';
            $html .= functions::image('user/on.png', array('class' => 'icon-r3'));
            $html .= '<b>' . functions::nickcolor($user_id) . '</b> ';
            $freq = mysql_query("SELECT `timestamp` FROM `guest` WHERE `id` = '$fadd'");
            $fres = mysql_fetch_array($freq);
            $html .= ' <span class="gray font-xs">(<span class="ajax-time" title="'.$fres['timestamp'].'">'.functions::display_date(time()).'</span>)</span><br />' . $post . '</div>';

        $data = array(
            'status' => 200,
            'html' => $html
        );

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($data);
        exit();
   }
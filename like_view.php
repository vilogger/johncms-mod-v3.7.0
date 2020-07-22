<?php
    /** Mod ReaCtions for JohnCMS By MrT98
    * NhanhNao.Xyz Team CMS
    * Copyright by MrT98
    * Mọi thắc mắc và hỗ trợ tại http://nhanhnao.xyz và http://phieubac.ga
    */

define('_IN_JOHNCMS', 1);
require('incfiles/core.php');

if($_POST){
    $rid = addslashes($_POST['rid']);
    $mr = addslashes($_POST['mr']);
    $reqs = $_POST['type'];
    switch ($mr) {
        case 'Like':
            $bi = 'Thích ↓<br />';
            $ii = '<img src="' . $home . '/icons/angry.gif" alt="'.$bi.'" />';
            break;

        case 'Love':
            $bi = 'Đáng Yêu ↓<br />';
            $ii = '<img src="' . $home . '/icons/angry.gif" alt="'.$bi.'" />';
            break;

        case 'Haha':
            $bi = 'Haha ↓<br />';
            $ii = '<img src="' . $home . '/icons/angry.gif" alt="'.$bi.'" />';
            break;

        case 'Hihi':
            $bi = 'Hihi ↓<br />';
            $ii = '<img src="' . $home . '/icons/angry.gif" alt="'.$bi.'" />';
            break;

        case 'Woww':
            $bi = 'Ngạc Nhiên ↓<br />';
            $ii = '<img src="' . $home . '/icons/angry.gif" alt="'.$bi.'" />';
            break;

        case 'Cry':
            $bi = 'Buồn ↓<br />';
            $ii = '<img src="' . $home . '/icons/angry.gif" alt="'.$bi.'" />';
            break;

        case 'Angry':
            $bi = 'Phẫn nộ ↓<br />';
            $ii = '<img src="' . $home . '/icons/angry.gif" alt="'.$bi.'" />';
            break;

        default:
            $bi = '';
            $ii = '';
            break;
    }

    if($reqs == 'stt') {
        $req = mysql_query("SELECT * FROM `postlikes` WHERE `post_id` = '$rid' AND `reaction` = '$mr' ORDER BY `time` DESC LIMIT 30");
        echo ''.$bi.'<hr />';
        for($i = 0; $res = mysql_fetch_array($req); $i++) {
            $usr = functions::get_user($res['timeline_id']);
            echo ($user_id && $user_id != $usr['id'] ? '<a href="/users/profile.php?user='.$usr['id'].'"><img src="' . $home . '/avatar/' . $usr['id'] . '-24-48.png" alt="'.$usr['name'].'" width="24" height="24" /> '.$usr['name'].'</a><br />' : '<img src="' . $home . '/avatar/' . $usr['id'] . '-24-48.png" alt="'.$usr['name'].'" width="24" height="24" /> '.$usr['name'].'<br />');
        }
    }else{
        $req = mysql_query("SELECT * FROM `forum_thank` WHERE `topic` = '$rid' AND `reaction_type` = '$mr' ORDER BY `time` DESC LIMIT 30");
        echo ''.$bi.'<hr />';
        for($i = 0; $res = mysql_fetch_array($req); $i++) {
            $usr = functions::get_user($res['userthank']);
            echo ($user_id && $user_id != $usr['id'] ? '<a href="/users/profile.php?user='.$usr['id'].'"><img src="' . $home . '/avatar/' . $usr['id'] . '-24-48.png" alt="'.$usr['name'].'" width="24" height="24" /> '.$usr['name'].'</a><br />' : '<img src="' . $home . '/avatar/' . $usr['id'] . '-24-48.png" alt="'.$usr['name'].'" width="24" height="24" /> '.$usr['name'].'<br />');
        }
    }
}else{
    echo 'ERROR';
}


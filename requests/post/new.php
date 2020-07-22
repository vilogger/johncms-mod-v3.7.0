<?php
$html = '';
$query = "";
$error = array();
$flood = FALSE;

$msg = isset($_POST['text']) ? functions::checkin(trim($_POST['text'])) : '';
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

$user = isset($_POST['user']) ? stringEscape($_POST['user']) : false;

$pry = isset($_POST['post_privacy']) ? stringEscape($_POST['post_privacy']) : 'public';

if ($user){
    $query = "`recipient_id` = '".$user."',";
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
       $req = mysql_query("SELECT * FROM `posts` WHERE `timeline_id` = '$user_id' ORDER BY `time` DESC");
       $res = mysql_fetch_array($req);
       if ($res['text'] == $msg) {
           $error[] = 'error';
       }
   }
   if (!$error) {
       mysql_query("INSERT INTO `posts` SET
            `time` = '" . time() . "',
            ".$query."
            `timeline_id` = '$user_id',
            `privacy` = '$pry',
            `text` = '" . mysql_real_escape_string($msg) . "'
       ");

        $fadd = mysql_insert_id();

        if($user != false && $user != $user_id) {
            mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='$user', `them`='25', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã đăng một [url=".$home."/users/profile.php?act=status&user=".$user."&binhluan&id=".$fadd."]trạng thái[/url] lên tường nhà bạn', `sys`='1', `type_mod`='status', `post_id`='".$fadd."', `time`='".time()."'");
        }

        ///mod tag thanh vien
        $exists = array();
        if(preg_match('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $msg)){
            preg_match_all('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $msg, $arr);
            foreach($arr[1] as $tag){
                $db = mysql_fetch_array(mysql_query("select * from users where name='$tag'"));
                if(mysql_num_rows(mysql_query("select * from users where name='$tag'"))==0 || $db['id'] == $user_id || $db['id'] == $user){
                } else if(isset($exists[intval($db['id'])]) == false) {
                    $exists[intval($db['id'])] = true;
                    mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='" .$db['id']."', `them`='24', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã nhắc đến bạn trong [url=".$home."/users/profile.php?act=status&user=".$user."&binhluan&id=".$fadd."]tâm trạng ".functions::sex($user_id)."[/url]', `sys`='1', `type_mod`='status', `post_id`='".$fadd."', `time`='".time()."'");
                }
            }
        }
        if(preg_match('#\[\@(.+?)\]#s', $msg)){
            preg_match_all('#\[\@(.+?)\]#s', $msg, $arr);
            foreach($arr[1] as $tag){
                $var_n = functions::check(trim($tag));
                $db = mysql_fetch_array(mysql_query("select * from users where name='$var_n'"));
                if(mysql_num_rows(mysql_query("select * from users where name='$var_n'"))==0 || $db['id'] == $user_id || $db['id'] == $user){
                } else if(isset($exists[intval($db['id'])]) == false) {
                    $exists[intval($db['id'])] = true;
                    mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='" .$db['id']."', `them`='24', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã nhắc đến bạn trong [url=".$home."/users/profile.php?act=status&user=".$user."&binhluan&id=".$fadd."]tâm trạng ".functions::sex($user_id)."[/url]', `sys`='1', `type_mod`='status', `post_id`='".$fadd."', `time`='".time()."'");
                }
            }
        }
        ///ket thuc mod tag thanh vien

        $post = functions::checkout($msg, 1, 1);
        if ($set_user['smileys'])
        $post = functions::smileys($post, 0);
        if ($pry == 'public')
            $pry_icon = '<i class="fa fa-globe"></i>';
        else if($pry == 'friends')
            $pry_icon = '<i class="fa fa-users"></i>';
        else if($pry == 'my')
            $pry_icon = '<i class="fa fa-user"></i>';
           $freq = mysql_query("SELECT `timestamp` FROM `posts` WHERE `id` = '$fadd'");
           $fres = mysql_fetch_array($freq);
           $html .= '<div id="story_'.$fadd.'" class="sttlist story_'.$fadd.'" data-story-id="'.$fadd.'"><div class="menu"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td width="48px" align="left" valign="top"><img src="' . $home . '/avatar/'.$user_id.'-20-40.png" width="40" height="40" alt="" /></td><td align="left" valign="middle">'. functions::image('user/on.png', array('class' => 'icon-r3')).($user_id && $user_id != $user_id ? '<a href="/users/profile.php?user=' . $user_id . '"><b>' . functions::nickcolor($user_id) . '</b></a>' : '<b>' . functions::nickcolor($user_id) . '</b>').($user ? ' > '.($user_id && $user_id != $user ? '<a href="/users/profile.php?user='.$user.'"><b>'.functions::nickcolor($user).'</b></a>' : '<b>'.functions::nickcolor($user).'</b>') : '').'<div class="other-data"><span class="gray font-xs"><i class="fa fa-clock-o"></i> <span class="ajax-time" title="'.$fres['timestamp'].'">'.functions::display_date(time()).'</span></span> ·  <span class="gray font-xs">'.$pry_icon.'</span></div></td></tr></tbody></table>
<div class="setting-buttons">
    <span class="remove-btn cursor-hand" title="Remover" onclick="SK_viewRemove('.$fadd.');" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-times progress-icon"></i>
</span>
<a href="/users/profile.php?act=status&user='.$user.'&edit&id='.$fadd.'" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-pencil progress-icon"></i>
</a></div>
<div style="margin-top: 6px;"></div>'.$post;
$html .= '</div>';

                    // mrt
$html .= '<div class="activity-wrapper" style="background: #fff; padding: 0; margin: 0;">';

                        $like_statusicon = 'icon-like-blf';
                            $like_status='Like';
                            $love_status='Love';
                            $haha_status='Haha';
                            $hihi_status='Hihi';
                            $woww_status='Woww';
                            $cry_status='Cry'; 
                            $angry_status='Angry';

                        $html .= '<div class="post-like-unlike-comment" style="padding: 0; border: 0;">';
$html .= '<div class="like-it" style="text-align: center;"><span class="story-comment-activity">
                        <span class="comment-activity activity-btn" onclick="javascript:$(\'#story_'.$fadd.' .comments-container\').slideToggle();" title="Coment&amp;aacute;rios">
                            <i class="fa fa-comments progress-icon" data-icon="comments"></i>
                            0
                        </span>
                    </span></div>';
                            $html .= '<div class="like-it" style="text-align: center;"><div class="new_like" tabindex="0" id="'.$fadd.'">
                                    <div class="like-pit first_click">
                                        <div class="icon-lpn '.$like_statusicon.'" id="ulk'.$fadd.'"></div>
                                        <div class="new_like_items first_click_wrap_content">
                                            <div class="op-lw like_button" data-id="0" id="like'.$fadd.'" data-request="stt" rel="'.$like_status.'" title="'.$like_status.'"><div class="icon-newL icon-like-new"></div></div>
                                            <div class="op-lw like_button" data-id="1" id="love'.$fadd.'" data-request="stt" rel="'.$love_status.'" title="'.$love_status.'"><div class="icon-newL icon-love-new"></div></div>
                                            <div class="op-lw like_button" data-id="2" id="haha'.$fadd.'" data-request="stt" rel="'.$haha_status.'" title="'.$haha_status.'"><div class="icon-newL icon-haha-new"></div></div>
                                            <div class="op-lw like_button" data-id="3" id="hihi'.$fadd.'" data-request="stt" rel="'.$hihi_status.'" title="'.$hihi_status.'"><div class="icon-newL icon-mmmm-new"></div></div>
                                            <div class="op-lw like_button" data-id="4" id="woww'.$fadd.'" data-request="stt" rel="'.$woww_status.'" title="'.$woww_status.'"><div class="icon-newL icon-wowww-new"></div></div>
                                            <div class="op-lw like_button" data-id="5" id="cry'.$fadd.'" data-request="stt" rel="'.$cry_status.'" title="'.$cry_status.'"><div class="icon-newL icon-crying-new"></div></div>
                                            <div class="op-lw like_button" data-id="6" id="angry'.$fadd.'" data-request="stt" rel="'.$angry_status.'" title="'.$angry_status.'"><div class="icon-newL icon-angry-new"></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                        $html .= '</div>';
                    $sep = '';
                    $lstyle = '';
                    $html .= '<div class="who-likes-this-post likes reaction_wrap-style" id="likess'.$fadd.'" style="margin: 0; padding: 0;">';
                    //Like Started
                        $html .= '<div class="likes reaction_wrap-style bbc" id="elikes'.$fadd.'" style="display:none"></div>';
                    //Love Started
                        $html .= '<div class="loves reaction_wrap-style bbc" id="eloves'.$fadd.'" style="display:none"></div>';
                    //Haha Started
                        $html .= '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$fadd.'" style="display:none"></div>';
                    //Hihi Started
                        $html .= '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$fadd.'" style="display:none"></div>';
                    //Woww Started
                        $html .= '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$fadd.'" style="display:none"></div>';
                    //Cry Started
                        $html .= '<div class="crys reaction_wrap-style bbc" id="ecry'.$fadd.'" style="display:none"></div>';
                    //Angry Started
                        $html .= '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$fadd.'" style="display:none"></div>';
                    $html .= '</div>';
                $html .= '</div>
    
    <div class="comments-container hidden">
        <div class="comments-wrapper">
            <div class="comment-wrapper">
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tbody><tr>
                        <td width="40px" align="left" valign="top">
                            <a href="/users/profile.php?user='.$user_id.'">
                                 <img src="/avatar/'.$user_id.'-16-32.png" width="32px" height="32px">
                            </a>
                        </td>
                        <td align="left" valign="top">
                            <div class="comment-textarea">
                                <textarea class="auto-grow-input" name="text" placeholder="Bạn thấy sao.?" data-height="24" onkeyup="postComment(this.value,'.$fadd.','.$user_id.',event);"></textarea>
                                <i class="fa fa-lightbulb progress-icon hide"></i>
                            </div>
                        </td>
                    </tr>
                </tbody></table>
                </div></div></div></div>';

        $data = array(
            'status' => 200,
            'html' => $html
        );

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($data);
        exit();
   }
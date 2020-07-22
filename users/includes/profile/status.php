<?php

defined('_IN_JOHNCMS') or die('Error: restricted access');

$textl = 'Status';
require('../incfiles/head.php');

if(!$id) {
    echo '<div class="rmenu">Có lỗi sảy ra.!</div>';
    require('../incfiles/end.php');
    exit;
}

if (isset($_GET['edit'])){
    // chinh sua tam trang.
    echo '<div class="phdr">Chỉnh sửa Status</div>';
    $kti = mysql_result(mysql_query("SELECT COUNT(*) FROM `posts` WHERE `id`='".$id."'"), 0);
    if($kti == 0) {
        echo '<div class="rmenu">Status không tồn tại.!</div>';
        require('../incfiles/end.php');
        exit;
    }
    $ktime = mysql_fetch_array(mysql_query("SELECT * FROM `posts` WHERE `id`='".$id."'"), 0);
    if($ktime['timeline_id'] != $user_id) {
        echo '<div class="rmenu">Bạn không có quyền chỉnh sửa status này!</div>';
        require('../incfiles/end.php');
        exit;
    }
    if(isset($_POST['submit'])) {
        $text = functions::checkin(trim($_POST['text']));
        $riengtu = functions::checkin(trim($_POST['post_privacy']));
        mysql_query("UPDATE `posts` SET `text`='" . mysql_real_escape_string($text) . "', `privacy`='" . mysql_real_escape_string($riengtu) . "' WHERE `id`='{$id}'");
        echo '<div class="gmenu">Đã lưu.!</div>';
    }
    echo '<div class="list1"><form method="post"><textarea rows="' . $set_user['field_h'] . '" name="text">'.htmlentities($ktime['text'], ENT_QUOTES, 'UTF-8').'</textarea>Riêng tư: <select name="post_privacy">
<option value="public">Tất cả</option>
<option value="friends" ' . ($ktime['privacy'] == 'friends' ? 'selected' : '') . '>Bạn bè</option>
<option value="my" ' . ($ktime['privacy'] == 'my' ? 'selected' : '') . '>Chỉ mình tôi</option>
</select><br /><input type="submit" name="submit" value="Lưu" />&#160;<a href="/users/profile.php?user='.$user['id'].'"><input type="button" value="Hủy" /></a></form></div>';

} else if (isset($_GET['editcomment'])){
    // chinh sua binh luan.
    echo '<div class="phdr">Chỉnh sửa Comments</div>';
    $kti = mysql_result(mysql_query("SELECT COUNT(*) FROM `comments` WHERE `id`='".$id."'"), 0);
    if($kti == 0) {
        echo '<div class="rmenu">Comments không tồn tại.!</div>';
        require('../incfiles/end.php');
        exit;
    }
    $ktime = mysql_fetch_array(mysql_query("SELECT * FROM `comments` WHERE `id`='".$id."'"), 0);
    if($ktime['timeline_id'] != $user_id) {
        echo '<div class="rmenu">Bạn không có quyền chỉnh sửa comments này!</div>';
        require('../incfiles/end.php');
        exit;
    }
    if(isset($_POST['submit'])) {
        $text = functions::checkin(trim($_POST['text']));
        mysql_query("UPDATE `comments` SET `text`='" . mysql_real_escape_string($text) . "' WHERE `id`='{$id}'");
        echo '<div class="gmenu">Đã lưu.!</div>';
    }
    echo '<div class="list1"><form method="post"><textarea rows="' . $set_user['field_h'] . '" name="text">'.htmlentities($ktime['text'], ENT_QUOTES, 'UTF-8').'</textarea><br /><input type="submit" name="submit" value="Lưu" />&#160;<a href="/users/profile.php?user='.$user['id'].'"><input type="button" value="Hủy" /></a></form></div>';
}else{
    echo '<div class="phdr">Status</div>';
    $kti = mysql_result(mysql_query("SELECT COUNT(*) FROM `posts` WHERE `id`='".$id."'"), 0);
    if($kti == 0) {
        echo '<div class="rmenu">Status không tồn tại.!</div>';
        require('../incfiles/end.php');
        exit;
    }
    $status = mysql_fetch_array(mysql_query("SELECT * FROM `posts` WHERE `id`='".$id."'"), 0);
    $ust = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id`='".$status['timeline_id']."'"), 0);

        $post = functions::checkout($status['text'], 1, 1);
        if ($set_user['smileys'])
        $post = functions::smileys($post, $ust['rights'] ? 1 : 0);
        if ($status['privacy'] == 'public')
            $pry_icon = '<i class="fa fa-globe"></i>';
        else if($status['privacy'] == 'friends')
            $pry_icon = '<i class="fa fa-users"></i>';
        else if($status['privacy'] == 'my')
            $pry_icon = '<i class="fa fa-user"></i>';

echo '<style>.setting-buttons {
    position: absolute;
    top: 8px;
    right: 12px;
    color: #898f9c
}

.setting-buttons span:hover {
    color: #4e5665
}

.setting-buttons a{
    color: #898f9c
}

.setting-buttons a:hover {
    color: #4e5665
}
.comment-textarea {
    position: relative;
    background: #fff;
    border-radius: 3px;
    box-shadow: 0 2px 3px 1px #f2f4f6 inset
}

.comment-textarea textarea {
    width: 100%;
    height: 30px;
    background: #fff;
    color: #4e5665;
    margin: 0;
    padding: 4px 6px 4px 6px;
    border-radius: 3px;
    overflow: hidden
}

.comment-textarea .progress-icon {
    position: absolute;
    top: 8px;
    right: 5px
}</style>';
          $omsg_id = $id;
          echo '<script type="text/javascript" src="/js/profile2.js"></script><div class="status_'.$id.'" style="position: relative; float: left; width: 100%; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .06), 0 2px 5px 0 rgba(0, 0, 0, .2); margin-top: 7px;"><div class="menu" style="float: left; width: 100%; margin: 0; border: 0; box-sizing: border-box;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td width="48px" align="left" valign="top"><img src="' . $home . '/avatar/'.$status['timeline_id'].'-20-40.png" width="40" height="40" alt="" /></td><td align="left" valign="middle">'. (time() > $ust['lastdate'] + 30 ? ''.functions::image('user/off.png', array('class' => 'icon-r3')).'' : ''.functions::image('user/on.png', array('class' => 'icon-r3')).'').($user_id && $user_id != $status['timeline_id'] ? '<a href="/users/profile.php?user=' . $status['timeline_id'] . '"><b>' . functions::nickcolor($status['timeline_id']) . '</b></a>' : '<b>' . functions::nickcolor($status['timeline_id']) . '</b>').($status['recipient_id'] ? ' > '.($user_id && $user_id != $status['recipient_id'] ? '<a href="/users/profile.php?user='.$status['recipient_id'].'"><b>'.functions::nickcolor($status['recipient_id']).'</b></a>' : '<b>'.functions::nickcolor($status['recipient_id']).'</b>') : '').'<div class="other-data"><span class="gray font-xs"><i class="fa fa-clock-o"></i> '.(round((time()-$status['time'])/3600) < 2 ? '<span class="ajax-time" title="' . $status['timestamp'] . '">':'').functions::display_date($status['time']).'</span>'.(round((time()-$status['time'])/3600) < 2 ? '</span>':'').' ·  <span class="gray font-xs">'.$pry_icon.'</span></div></td></tr></tbody></table>
    '.($user_id == $status['timeline_id'] ? '<div class="setting-buttons">
    <span class="remove-btn cursor-hand" title="Remover" onclick="SK_viewRemove('.$id.');" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-times progress-icon"></i>
</span>
<a href="/users/profile.php?act=status&user='.$ust['id'].'&edit&id='.$id.'" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-pencil progress-icon"></i>
</a></div>' : '').'
<div style="margin-top: 6px;"></div>'.$post;
echo '</div>';

$result_comment = mysql_result(mysql_query("SELECT COUNT(*) FROM `comments` WHERE `post_id`='".$id."'"), 0);
                    // mrt
echo '<div class="activity-wrapper" style="background: #fff; padding: 0; margin: 0;">';

                    if($user_id){
                        // Reaction status check for "Like"
                        $like=functions::STTLike_Check($id,$user_id, "Like");
                        // If post is not reactioned then show the $like_statusicon = 'icon-like-blf';
                        // $like_statusicon working with all reaction status
                        $like_statusicon = 'icon-like-blf';
                        // $lostyle will working reaction status 
                        // For example if not reactioned post then the style will be display:none;
                        $lostyle='display:none;';
                        if($like) {
                            //If post liked then show UnLike from the div rel and title
                            $like_status='UnLike'; 
                            // If post liked then show new UnLike icon from the reactions box
                            $like_statusicon='icon-like-new';
                        } else {
                            // If post not liked then show UnLike from the div rel and title
                            $like_status='Like'; 
                        }
                        // Reaction status check for "Love"
                        $love=functions::STTLike_Check($id,$user_id, "Love");
                        if($love){
                            // If post reaction status is UnLove then show UnLove from the div rel and title
                            $love_status='UnLove';
                            // If post reaction status is UnLove then show Love icon from the reactions box
                            $like_statusicon='icon-love-new'; 
                            // If post reaction status is UnLove then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnLove then show Love from the div rel and title
                            $love_status='Love';
                         }
                        // Reaction status check for "Haha"
                        $haha=functions::STTLike_Check($id,$user_id, "Haha");
                        if($haha){
                            // If post reaction status is UnHaha then show UnHaha from the div rel and title
                            $haha_status='UnHaha';
                            // If post reaction status is UnHaha then show Haha icon from the reactions box
                            $like_statusicon='icon-haha-new'; 
                            // If post reaction status is UnHaha then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnHaha then show Haha from the div rel and title
                            $haha_status='Haha';
                        }
                        // Reaction status check for "Hihi"
                        $hihi=functions::STTLike_Check($id,$user_id, "Hihi");
                        if($hihi){
                            // If post reaction status is UnHihi then show UnHihi from the div rel and title
                            $hihi_status='UnHihi';
                            // If post reaction status is UnHihi then show Hihi icon from the reactions box
                            $like_statusicon='icon-mmmm-new'; 
                            // If post reaction status is UnHihi then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            //If post reaction status is not UnHihi then show Hihi from the div rel and title
                            $hihi_status='Hihi';
                        }
                        // Reaction status check for "Woww"
                        $woww=functions::STTLike_Check($id,$user_id, "Woww");
                        if($woww){
                            // If post reaction status is UnWoww then show UnWoww from the div rel and title
                            $woww_status='UnWoww';
                            // If post reaction status is UnWoww then show Woww icon from the reactions box
                            $like_statusicon='icon-wowww-new'; 
                            // If post reaction status is UnWoww then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnWoww then show Woww from the div rel and title
                            $woww_status='Woww';
                        }
                        // Reaction status check for "Cry"
                        $Cry=functions::STTLike_Check($id,$user_id, "Cry");
                        if($Cry){
                            // If post reaction status is UnCry then show UnCry from the div rel and title
                            $cry_status='UnCry';
                            // If post reaction status is UnCry then show Cry icon from the reactions box
                            $like_statusicon='icon-crying-new'; 
                            // If post reaction status is UnCry then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnCry then show Cry from the div rel and title
                            $cry_status='Cry'; 
                        }
                        // Reaction status check for "Angry"
                        $angry=functions::STTLike_Check($id,$user_id, "Angry");
                        if($angry){
                            // If post reaction status is UnAngry then show UnAngry from the div rel and title
                            $angry_status='UnAngry';
                            // If post reaction status is UNAngry then show Angry icon from the reactions box
                            $like_statusicon='icon-angry-new'; 
                            // If post reaction status is UnAngry then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnAngry then show Angry from the div rel and title
                            $angry_status='Angry';
                        }

                        echo '<div class="post-like-unlike-comment" style="padding: 0; border: 0;">';
echo '<div class="like-it" style="text-align: center;"><span class="story-comment-activity">
                        <span class="comment-activity activity-btn" title="Comments">
                            <i class="fa fa-comments progress-icon" data-icon="comments"></i>
                            '.$result_comment.'
                        </span>
                    </span></div>';

                            echo '<div class="like-it" style="text-align: center;"><div class="new_like" tabindex="0" id="'.$id.'">
                                    <div class="like-pit first_click">
                                        <div class="icon-lpn '.$like_statusicon.'" id="ulk'.$id.'"></div>
                                        <div class="new_like_items first_click_wrap_content">
                                            <div class="op-lw like_button" data-id="0" id="like'.$omsg_id.'" data-request="stt" rel="'.$like_status.'" title="'.$like_status.'"><div class="icon-newL icon-like-new"></div></div>
                                            <div class="op-lw like_button" data-id="1" id="love'.$omsg_id.'" data-request="stt" rel="'.$love_status.'" title="'.$love_status.'"><div class="icon-newL icon-love-new"></div></div>
                                            <div class="op-lw like_button" data-id="2" id="haha'.$omsg_id.'" data-request="stt" rel="'.$haha_status.'" title="'.$haha_status.'"><div class="icon-newL icon-haha-new"></div></div>
                                            <div class="op-lw like_button" data-id="3" id="hihi'.$omsg_id.'" data-request="stt" rel="'.$hihi_status.'" title="'.$hihi_status.'"><div class="icon-newL icon-mmmm-new"></div></div>
                                            <div class="op-lw like_button" data-id="4" id="woww'.$omsg_id.'" data-request="stt" rel="'.$woww_status.'" title="'.$woww_status.'"><div class="icon-newL icon-wowww-new"></div></div>
                                            <div class="op-lw like_button" data-id="5" id="cry'.$omsg_id.'" data-request="stt" rel="'.$cry_status.'" title="'.$cry_status.'"><div class="icon-newL icon-crying-new"></div></div>
                                            <div class="op-lw like_button" data-id="6" id="angry'.$omsg_id.'" data-request="stt" rel="'.$angry_status.'" title="'.$angry_status.'"><div class="icon-newL icon-angry-new"></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                        echo '</div>';
                    }

                    $sep = '';
                    $lstyle = '';
                    if(functions::STTLike_CountTotal($id, $user_id, in_array($sep, array('Like','Love','Haha','Hihi','Woww','Cry','Angry')))>0){
                        $lstyle="display:block;";
                    } else {
                        //$lstyle="display:none;";
                    }
                    echo '<div class="who-likes-this-post likes reaction_wrap-style" id="likess'.$omsg_id.'" style="margin: 0; padding: 0; '.$lstyle.'">';
                    //Like Started
                    if(functions::STTLike_CountT($id, $user_id, 'Like')>0) {
                        echo '<div class="likes reaction_wrap-style bbc" id="elikes'.$omsg_id.'" style="'.$lstyle.'"><span id="like_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-like-new lpos" id="clk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'uPages\', \'Like\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Like\')"></span><span class="lvspan mrt_Like_'.$id.' no_display" id="public_Like_user_block'.$id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'2\', \'Like\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Like\')"><span id="Like_users'.$omsg_id.'"></span></span><span class="lcl" id="lcl'.$omsg_id.'">'.functions::STTLike_CountT($id, $user_id, 'Like').'</span></span></div>'; 
                    } else {
                        echo '<div class="likes reaction_wrap-style bbc" id="elikes'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Love Started
                    if(functions::STTLike_CountT($id, $user_id, 'Love')){
                        echo '<div class="loves reaction_wrap-style bbc" id="eloves'.$omsg_id.'" style="'.$lstyle.'"><span id="love_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-love-new lpos" id="llk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'uPages\', \'Love\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Love\')"></span><span class="lvspan mrt_Love_'.$id.' no_display" id="public_Love_user_block'.$id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'2\', \'Love\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Love\')"><span id="Love_users'.$omsg_id.'"></span></span><span class="lco" id="lco'.$omsg_id.'">'.functions::STTLike_CountT($id, $user_id, 'Love').'</span></span></div>'; 
                    } else {
                        echo '<div class="loves reaction_wrap-style bbc" id="eloves'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Haha Started
                    if(functions::STTLike_CountT($id, $user_id, 'Haha')){
                        echo '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$omsg_id.'" style="'.$lstyle.'"><span id="haha_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-haha-new lpos" id="hlk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'uPages\', \'Haha\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Haha\')"></span><span class="lvspan mrt_Haha_'.$id.' no_display" id="public_Haha_user_block'.$id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'2\', \'Haha\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Haha\')"><span id="Haha_users'.$omsg_id.'"></span></span><span class="hco" id="hco'.$omsg_id.'">'.functions::STTLike_CountT($id, $user_id, 'Haha').'</span></span></div>'; 
                    } else {
                        echo '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Hihi Started
                    if(functions::STTLike_CountT($id, $user_id, 'Hihi')){
                        echo '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$omsg_id.'" style="'.$lstyle.'"><span id="hihi_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-mmmm-new lpos" id="hilk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'uPages\', \'Hihi\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Hihi\')"></span><span class="lvspan mrt_Hihi_'.$id.' no_display" id="public_Hihi_user_block'.$id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'2\', \'Hihi\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Hihi\')"><span id="Hihi_users'.$omsg_id.'"></span></span><span class="hico" id="hico'.$omsg_id.'">'.functions::STTLike_CountT($id, $user_id, 'Hihi').'</span></span></div>'; 
                    } else {
                        echo '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Woww Started
                    if(functions::STTLike_CountT($id, $user_id, 'Woww')){
                        echo '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$omsg_id.'" style="'.$lstyle.'"><span id="woww_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-wowww-new lpos" id="woow'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'uPages\', \'Woww\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Woww\')"></span><span class="lvspan mrt_Woww_'.$id.' no_display" id="public_Woww_user_block'.$id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'2\', \'Woww\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Woww\')"><span id="Woww_users'.$omsg_id.'"></span></span><span class="wco" id="wco'.$omsg_id.'">'.functions::STTLike_CountT($id, $user_id, 'Woww').'</span></span></div>'; 
                    } else {
                        echo '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Cry Started
                    if(functions::STTLike_CountT($id, $user_id, 'Cry')){
                        echo '<div class="crys reaction_wrap-style bbc" id="ecry'.$omsg_id.'" style="'.$lstyle.'"><span id="cry_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-crying-new lpos" id="cry'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'uPages\', \'Cry\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Cry\')"></span><span class="lvspan mrt_Cry_'.$id.' no_display" id="public_Cry_user_block'.$id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'2\', \'Cry\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Cry\')"><span id="Cry_users'.$omsg_id.'"></span></span><span class="cco" id="cco'.$omsg_id.'">'.functions::STTLike_CountT($id, $user_id, 'Cry').'</span></span></div>';
                    } else {
                        echo '<div class="crys reaction_wrap-style bbc" id="ecry'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Angry Started
                    if(functions::STTLike_CountT($id, $user_id, 'Angry')){
                        echo '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$omsg_id.'" style="'.$lstyle.'"><span id="angry_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-angry-new lpos" id="angry'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'uPages\', \'Angry\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Angry\')"></span><span class="lvspan mrt_Angry_'.$id.' no_display" id="public_Angry_user_block'.$id.'" onMouseOver="wall_like_users_five(\''.$id.'\', \'2\', \'Angry\')" onMouseOut="wall_like_users_five_hide(\''.$id.'\', \'Angry\)"><span id="Angry_users'.$omsg_id.'"></span></span><span class="eco" id="eco'.$omsg_id.'">'.functions::STTLike_CountT($id, $user_id, 'Angry').'</span></span></div>'; 
                    } else {
                        echo '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$omsg_id.'" style="display:none"></div>';
                    }
                    echo '</div>';

    echo '</div></div>';
    echo '<div class="phdr post-id" data-id="'.$id.'" style="display: inline-block; width: 100%; box-sizing: border-box;">Bình luận.</div>';

    echo '<div class="list1"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tbody><tr>
                        <td width="40px" align="left" valign="top">
                            <a href="/users/profile.php?user='.$user_id.'">
                                 <img src="/avatar/'.$user_id.'-16-32.png" width="32px" height="32px">
                            </a>
                        </td>
                        <td align="left" valign="top">
                            <div class="comment-textarea">
                                <textarea class="auto-grow-input" name="text" placeholder="Bạn thấy sao.?" data-height="24" onkeyup="postComment(this.value,'.$id.','.$user_id.',event);" style="margin: 0"></textarea>
                                <i class="fa fa-lightbulb progress-icon hide"></i>
                            </div>
                        </td>
                    </tr>
                </tbody></table></div><div class="stories-wrapper">';


$reqc = mysql_query("SELECT * FROM `comments` WHERE `post_id` = '" . $id . "' ORDER BY `id` DESC LIMIT 4");
        while ($cres = mysql_fetch_assoc($reqc)) {
            $textcomment = functions::checkout($cres['text'], 1, 1);
            if ($set_user['smileys'])
                $textcomment = functions::smileys($textcomment, 0);

            $result_like = mysql_result(mysql_query("SELECT COUNT(*) FROM `commentlikes` WHERE `post_id`='".$cres['id']."'"), 0);
    $dlreq = mysql_query("SELECT `id` FROM `commentlikes` WHERE `post_id` = '".$cres['id']."' AND `timeline_id` = '$user_id' ");

    if (mysql_num_rows($dlreq)) {
$hanhdonglike = 'Unlikes';
    }else{
$hanhdonglike = 'Likes';
    }

            echo '<div id="comment_'.$cres['id'].'" class="list1 listcmt comment_'.$cres['id'].'" data-comment-id="'.$cres['id'].'" style="position: relative;">
    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="commentTable">
    <tr>
        <td width="40px" align="left" valign="top">
            <a href="/users/profile.php?user=' . $cres['timeline_id'] . '">
                <img src="/avatar/'.$cres['timeline_id'].'-16-32.png" width="32px" height="32px">
            </a>
        </td>
        
        <td align="left" valign="top">
            <div class="comment-content">
                <a class="nick" href="/users/profile.php?user=' . $cres['timeline_id'] . '">
    ' . functions::nickcolor($cres['timeline_id']) . '</a>:

                <span class="comment-text" style=" display: inline-block;">
                    '.$textcomment.'
                </span>
                
'.($user_id == $cres['timeline_id'] ? '<div class="setting-buttons">
<span class="comment-remove-btn cursor-hand" title="Remove" onclick="viewCommentRemove('.$cres['id'].');" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-times progress-icon"></i>
</span>

<a href="/users/profile.php?act=status&user='.$cres['timeline_id'].'&editcomment&id='.$cres['id'].'" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-pencil progress-icon"></i>
</a>
</div>' : '').'

                <div class="other-data">
                    <span class="gray font-xs">
                        <i class="fa fa-clock-o"></i> '.functions::display_date($cres['time']).'
                    </span>

                    <abbr class="space1">&#183;</abbr>

<span class="comment-like-activity activity-btn gray font-xs" onclick="viewCommentLikes('.$cres['id'].');" title="likes">
    '.$result_like.'
    <i class="fa fa-thumbs-up progress-icon" data-icon="thumbs-up"></i>
</span>

                    <abbr class="space1">&#183;</abbr>

<span class="comment-like-btn opt gray font-xs" onclick="likeComment('.$cres['id'].');" title="likes button">
    <i class="progress-icon hide" data-icon="thumbs-up"></i> '.$hanhdonglike.'
</span>
                </div>
            </div>
        </td>
    </tr>
    </table>
</div>';
        ++$cm;
    }
    echo '</div><div align="center">
        <div class="load-btn" onclick="addload();" data-id="'.$id.'">
            <i class="fa fa-reorder progress-icon"></i> Trước đó...</div></div>';
}
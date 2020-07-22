<?php
    echo '<div class="phdr"><b>Tường nhà.</b></div>';
$wquery = "";
$sql = "";
if($user['id'] == $user_id){
    $sql = "(`posts`.`timeline_id`='".$user['id']."' OR `posts`.`recipient_id`='".$user['id']."')";
}else{
    if(functions::is_friend($user['id'])){
        $wquery = "(`posts`.`privacy` = 'public' OR `posts`.`privacy` = 'friends')";
    }else{
        $wquery = "`posts`.`privacy` = 'public'";
    }
    $sql = "(((`posts`.`timeline_id`='".$user_id."' AND `posts`.`recipient_id`='".$user['id']."') OR (`posts`.`timeline_id`='".$user['id']."' AND `posts`.`recipient_id`='".$user_id."')) OR (".$wquery." AND (`posts`.`timeline_id`='".$user['id']."' OR `posts`.`recipient_id`='".$user['id']."')))";
}
    echo '<div class="gmenu" style="overflow: auto;"><div class="story-publisher-box">' .
        '<form name="form" method="post">';
    echo bbcode::auto_bb('form', 'text');
    echo '<textarea rows="' . $set_user['field_h'] . '" name="text"></textarea></p>';
    echo ($user['id'] != $user_id ? '<input type="hidden" name="user" value="'.$user['id'].'" />' : '');

    echo '<div class="rightf">Ai có thể xem? <select name="post_privacy">
<option value="public">Tất cả</option>
<option value="friends">Bạn bè</option>
<option value="my">Chỉ mình tôi</option>
</select>';
    echo '<button class="submit-btn active" name="story_submit_btn"><i class="fa fa-edit progress-icon"></i><span> Đăng</span></button></div>' .
        '</form></div></div>';

echo '<div class="stories-container" data-story-timeline="'.$user['id'].'">
    <div class="stories-wrapper">';
$req = mysql_query("SELECT DISTINCT `posts`.*, `posts`.`id` AS `pid`, `users`.`lastdate`, `users`.`id`, `users`.`rights`, `users`.`name`
                    FROM `posts` LEFT JOIN `users` ON `posts`.`timeline_id` = `users`.`id`
                    WHERE ".$sql." ORDER BY `time` DESC LIMIT 10");

        while ($gres = mysql_fetch_assoc($req)) {
        $post = functions::checkout($gres['text'], 1, 1);
        if ($set_user['smileys'])
        $post = functions::smileys($post, $gres['rights'] ? 1 : 0);
        if ($gres['privacy'] == 'public')
            $pry_icon = '<i class="fa fa-globe"></i>';
        else if($gres['privacy'] == 'friends')
            $pry_icon = '<i class="fa fa-users"></i>';
        else if($gres['privacy'] == 'my')
            $pry_icon = '<i class="fa fa-user"></i>';

          $omsg_id = $gres['pid'];
          echo '<div id="story_'.$gres['pid'].'" class="sttlist story_'.$gres['pid'].'" data-story-id="'.$gres['pid'].'"><div class="menu"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td width="48px" align="left" valign="top"><img src="' . $home . '/avatar/'.$gres['id'].'-20-40.png" width="40" height="40" alt="" /></td><td align="left" valign="middle">'. (time() > $gres['lastdate'] + 30 ? ''.functions::image('user/off.png', array('class' => 'icon-r3')).'' : ''.functions::image('user/on.png', array('class' => 'icon-r3')).'').($user_id && $user_id != $gres['id'] ? '<a href="/users/profile.php?user=' . $gres['id'] . '"><b>' . functions::nickcolor($gres['id']) . '</b></a>' : '<b>' . functions::nickcolor($gres['id']) . '</b>').($gres['recipient_id'] ? ' > '.($user_id && $user_id != $gres['recipient_id'] ? '<a href="/users/profile.php?user='.$gres['recipient_id'].'"><b>'.functions::nickcolor($gres['recipient_id']).'</b></a>' : '<b>'.functions::nickcolor($gres['recipient_id']).'</b>') : '').'<div class="other-data"><span class="gray font-xs"><i class="fa fa-clock-o"></i> '.(round((time()-$gres['time'])/3600) < 2 ? '<span class="ajax-time" title="' . $gres['timestamp'] . '">':'').functions::display_date($gres['time']).'</span>'.(round((time()-$gres['time'])/3600) < 2 ? '</span>':'').' ·  <span class="gray font-xs">'.$pry_icon.'</span></div></td></tr></tbody></table>
    '.($user_id == $gres['id'] ? '<div class="setting-buttons">
    <span class="remove-btn cursor-hand" title="Remover" onclick="SK_viewRemove('.$gres['pid'].');" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-times progress-icon"></i>
</span>
<a href="/users/profile.php?act=status&user='.$user['id'].'&edit&id='.$gres['pid'].'" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-pencil progress-icon"></i>
</a></div>' : '').'
<div style="margin-top: 6px;"></div>'.$post;
echo '</div>';

$result_comment = mysql_result(mysql_query("SELECT COUNT(*) FROM `comments` WHERE `post_id`='".$gres['pid']."'"), 0);
$limitc = "";
if($result_comment > 4){
    $tinhc = $result_comment - 4;
    $limitc = " LIMIT " . $tinhc . ", 4";
}
$reqc = mysql_query("SELECT * FROM `comments` WHERE `post_id` = '" . $gres['pid'] . "' ORDER BY `id` ASC" . $limitc);
        $chtml = '';
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

            $chtml .= '<div id="comment_'.$cres['id'].'" class="comment-wrapper comment_'.$cres['id'].'" data-comment-id="'.$cres['id'].'">
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

                <span class="comment-text">
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
                    // mrt
echo '<div class="activity-wrapper" style="background: #fff; padding: 0; margin: 0;">';

                    if($user_id){
                        // Reaction status check for "Like"
                        $like=functions::STTLike_Check($gres['pid'],$user_id, "Like");
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
                        $love=functions::STTLike_Check($gres['pid'],$user_id, "Love");
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
                        $haha=functions::STTLike_Check($gres['pid'],$user_id, "Haha");
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
                        $hihi=functions::STTLike_Check($gres['pid'],$user_id, "Hihi");
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
                        $woww=functions::STTLike_Check($gres['pid'],$user_id, "Woww");
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
                        $Cry=functions::STTLike_Check($gres['pid'],$user_id, "Cry");
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
                        $angry=functions::STTLike_Check($gres['pid'],$user_id, "Angry");
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
                        <span class="comment-activity activity-btn" onclick="javascript:$(\'#story_'.$gres['pid'].' .comments-container\').slideToggle();" title="Comments">
                            <i class="fa fa-comments progress-icon" data-icon="comments"></i>
                            '.$result_comment.'
                        </span>
                    </span></div>';

                            echo '<div class="like-it" style="text-align: center;"><div class="new_like" tabindex="0" id="'.$gres['pid'].'">
                                    <div class="like-pit first_click">
                                        <div class="icon-lpn '.$like_statusicon.'" id="ulk'.$gres['pid'].'"></div>
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
                    if(functions::STTLike_CountTotal($gres['pid'], $user_id, in_array($sep, array('Like','Love','Haha','Hihi','Woww','Cry','Angry')))>0){
                        $lstyle="display:block;";
                    } else {
                        //$lstyle="display:none;";
                    }
                    echo '<div class="who-likes-this-post likes reaction_wrap-style" id="likess'.$omsg_id.'" style="margin: 0; padding: 0; '.$lstyle.'">';
                    //Like Started
                    if(functions::STTLike_CountT($gres['pid'], $user_id, 'Like')>0) {
                        echo '<div class="likes reaction_wrap-style bbc" id="elikes'.$omsg_id.'" style="'.$lstyle.'"><span id="like_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-like-new lpos" id="clk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'uPages\', \'Like\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Like\')"></span><span class="lvspan mrt_Like_'.$gres['pid'].' no_display" id="public_Like_user_block'.$gres['pid'].'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'2\', \'Like\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Like\')"><span id="Like_users'.$omsg_id.'"></span></span><span class="lcl" id="lcl'.$omsg_id.'">'.functions::STTLike_CountT($gres['pid'], $user_id, 'Like').'</span></span></div>'; 
                    } else {
                        echo '<div class="likes reaction_wrap-style bbc" id="elikes'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Love Started
                    if(functions::STTLike_CountT($gres['pid'], $user_id, 'Love')){
                        echo '<div class="loves reaction_wrap-style bbc" id="eloves'.$omsg_id.'" style="'.$lstyle.'"><span id="love_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-love-new lpos" id="llk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'uPages\', \'Love\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Love\')"></span><span class="lvspan mrt_Love_'.$gres['pid'].' no_display" id="public_Love_user_block'.$gres['pid'].'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'2\', \'Love\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Love\')"><span id="Love_users'.$omsg_id.'"></span></span><span class="lco" id="lco'.$omsg_id.'">'.functions::STTLike_CountT($gres['pid'], $user_id, 'Love').'</span></span></div>'; 
                    } else {
                        echo '<div class="loves reaction_wrap-style bbc" id="eloves'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Haha Started
                    if(functions::STTLike_CountT($gres['pid'], $user_id, 'Haha')){
                        echo '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$omsg_id.'" style="'.$lstyle.'"><span id="haha_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-haha-new lpos" id="hlk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'uPages\', \'Haha\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Haha\')"></span><span class="lvspan mrt_Haha_'.$gres['pid'].' no_display" id="public_Haha_user_block'.$gres['pid'].'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'2\', \'Haha\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Haha\')"><span id="Haha_users'.$omsg_id.'"></span></span><span class="hco" id="hco'.$omsg_id.'">'.functions::STTLike_CountT($gres['pid'], $user_id, 'Haha').'</span></span></div>'; 
                    } else {
                        echo '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Hihi Started
                    if(functions::STTLike_CountT($gres['pid'], $user_id, 'Hihi')){
                        echo '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$omsg_id.'" style="'.$lstyle.'"><span id="hihi_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-mmmm-new lpos" id="hilk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'uPages\', \'Hihi\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Hihi\')"></span><span class="lvspan mrt_Hihi_'.$gres['pid'].' no_display" id="public_Hihi_user_block'.$gres['pid'].'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'2\', \'Hihi\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Hihi\')"><span id="Hihi_users'.$omsg_id.'"></span></span><span class="hico" id="hico'.$omsg_id.'">'.functions::STTLike_CountT($gres['pid'], $user_id, 'Hihi').'</span></span></div>'; 
                    } else {
                        echo '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Woww Started
                    if(functions::STTLike_CountT($gres['pid'], $user_id, 'Woww')){
                        echo '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$omsg_id.'" style="'.$lstyle.'"><span id="woww_count'.$omsg_id.'" class="numcount bbc"><span class="icon-newL icon-wowww-new lpos" id="woow'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'uPages\', \'Woww\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Woww\')"></span><span class="lvspan mrt_Woww_'.$gres['pid'].' no_display" id="public_Woww_user_block'.$gres['pid'].'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'2\', \'Woww\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Woww\')"><span id="Woww_users'.$omsg_id.'"></span></span><span class="wco" id="wco'.$omsg_id.'">'.functions::STTLike_CountT($gres['pid'], $user_id, 'Woww').'</span></span></div>'; 
                    } else {
                        echo '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Cry Started
                    if(functions::STTLike_CountT($gres['pid'], $user_id, 'Cry')){
                        echo '<div class="crys reaction_wrap-style bbc" id="ecry'.$omsg_id.'" style="'.$lstyle.'"><span id="cry_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-crying-new lpos" id="cry'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'uPages\', \'Cry\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Cry\')"></span><span class="lvspan mrt_Cry_'.$gres['pid'].' no_display" id="public_Cry_user_block'.$gres['pid'].'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'2\', \'Cry\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Cry\')"><span id="Cry_users'.$omsg_id.'"></span></span><span class="cco" id="cco'.$omsg_id.'">'.functions::STTLike_CountT($gres['pid'], $user_id, 'Cry').'</span></span></div>';
                    } else {
                        echo '<div class="crys reaction_wrap-style bbc" id="ecry'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Angry Started
                    if(functions::STTLike_CountT($gres['pid'], $user_id, 'Angry')){
                        echo '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$omsg_id.'" style="'.$lstyle.'"><span id="angry_count'.$omsg_id.'" class="numcount bbc "><span class="icon-newL icon-angry-new lpos" id="angry'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'uPages\', \'Angry\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Angry\')"></span><span class="lvspan mrt_Angry_'.$gres['pid'].' no_display" id="public_Angry_user_block'.$gres['pid'].'" onMouseOver="wall_like_users_five(\''.$gres['pid'].'\', \'2\', \'Angry\')" onMouseOut="wall_like_users_five_hide(\''.$gres['pid'].'\', \'Angry\)"><span id="Angry_users'.$omsg_id.'"></span></span><span class="eco" id="eco'.$omsg_id.'">'.functions::STTLike_CountT($gres['pid'], $user_id, 'Angry').'</span></span></div>'; 
                    } else {
                        echo '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$omsg_id.'" style="display:none"></div>';
                    }
                    echo '</div>';

    echo '</div>
    
    <div class="comments-container hidden">
'.($result_comment > 4 ? '<div class="view-more-wrapper" align="center" onclick="viewAllComments('.$gres['pid'].');">
    <i class="fa fa-lightbulb progress-icon hide"></i>
    Đọc từ đầu.
</div>' : '').'
        <div class="comments-wrapper">
            '.$chtml.'

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
                                <textarea class="auto-grow-input" name="text" placeholder="Bạn thấy sao.?" data-height="24" onkeyup="postComment(this.value,'.$gres['pid'].','.$user_id.',event);"></textarea>
                                <i class="fa fa-lightbulb progress-icon hide"></i>
                            </div>
                        </td>
                    </tr>
                </tbody></table>
                </div></div></div></div>';
          ++$i;
 
        }

    echo '</div>
    <div align="center">
        <div class="load-btn" onclick="SK_loadOldStories();">
            <i class="fa fa-reorder progress-icon"></i> Xem thêm...</div>
    </div>
</div>';

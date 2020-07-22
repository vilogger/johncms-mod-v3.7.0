<?php
$html = '';
$achtml = '';
$error = array();
$flood = FALSE;

$post_id = stringEscape($_GET['post_id']);
$msg = isset($_POST['text']) ? functions::checkin(trim($_POST['text'])) : '';
$uid = stringEscape($_POST['timeline_id']);

$flood = functions::antiflood();
if (empty($msg)){
    $error[] = $lng['error_empty_message'];
}
if ($ban['1'] || $ban['13'])
   $error[] = $lng['access_forbidden'];

if ($flood)
    $error = $lng['error_flood'] . ' ' . $flood . '&#160;' . $lng['seconds'];
if (!$error) {
       $req = mysql_query("SELECT * FROM `comments` WHERE `post_id` = '$post_id' AND `timeline_id` = '$uid' ORDER BY `time` DESC");
       $res = mysql_fetch_array($req);
       if ($res['text'] == $msg) {
           $error[] = 'error';
       }
   }
   if (!$error) {
       mysql_query("INSERT INTO `comments` SET
            `time` = '" . time() . "',
            `timeline_id` = '$uid',
            `post_id` = '$post_id',
            `text` = '" . mysql_real_escape_string($msg) . "'
       ");
        $fadd = mysql_insert_id();

        $status = mysql_fetch_array(mysql_query("SELECT * FROM `posts` WHERE `id`='".$post_id."'"));

                if($status['timeline_id'] != $user_id) {
                    mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='$status[timeline_id]', `them`='22', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã bình luận về [url=".$home."/users/profile.php?act=status&user=".$status['timeline_id']."&binhluan&id=".$post_id."]tâm trạng[/url] của bạn', `sys`='1', `type_mod`='status_cmt', `post_id`='".$fadd."', `time`='".time()."'");
                }
                $rdem = mysql_result(mysql_query("SELECT COUNT(*) FROM `comments` WHERE `post_id`='{$post_id}'"), 0);
                $req_u = mysql_query("SELECT * FROM `users` WHERE `id` = '$status[timeline_id]' LIMIT 1");
                $res_u = mysql_fetch_array($req_u);
                if($rdem) {
                    $reqp = mysql_query("SELECT DISTINCT `timeline_id` FROM `comments` WHERE `post_id`='{$post_id}' and `timeline_id` !='{$status['timeline_id']}' and `timeline_id` !='{$user_id}'");
                    while ($resp = mysql_fetch_array($reqp)) {
                        mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='".$resp['timeline_id']."', `them`='14', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã bình luận về [url=".$home."/users/profile.php?act=status&user=".$status['timeline_id']."&binhluan&id=".$post_id."]tâm trạng[/url] ".($user_id == $res_u['id'] ? functions::sex($user_id) : 'của '.$res_u['name']).".', `sys`='1', `type_mod`='status_cmt', `post_id`='".$fadd."', `time`='".time()."'");
                    }
                }

        $msg = functions::checkout($msg, 1, 1);
        if ($set_user['smileys'])
        $msg = functions::smileys($msg, 0);
$html .= '<div id="comment_'.$fadd.'" class="list1 listcmt comment_'.$fadd.'" data-comment-id="'.$fadd.'" style="position: relative;">
    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="commentTable">
    <tr>
        <td width="40px" align="left" valign="top">
            <a href="/users/profile.php?user=' . $uid . '">
                <img src="/avatar/'.$uid.'-16-32.png" width="32px" height="32px">
            </a>
        </td>
        
        <td align="left" valign="top">
            <div class="comment-content">
                <a class="nick" href="/users/profile.php?user=' . $uid . '">' . functions::nickcolor($uid) . '</a>:

                <span class="comment-text" style=" display: inline-block;">
                    '.$msg.'
                </span>
                
<div class="setting-buttons">
<span class="comment-remove-btn cursor-hand" title="Remove" onclick="viewCommentRemove('.$fadd.');" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-times progress-icon"></i>
</span>

<a href="/users/profile.php?act=status&user='.$user_id.'&editcomment&id='.$fadd.'" style="padding: 1px 5px 1px 5px;">
    <i class="fa fa-pencil progress-icon"></i>
</a>
</div>

                <div class="other-data">
                    <span class="gray font-xs">
                        <i class="fa fa-clock-o"></i> '.functions::display_date(time()).'
                    </span>

                    <abbr class="space1">&#183;</abbr>

<span class="comment-like-activity activity-btn gray font-xs" onclick="viewCommentLikes('.$fadd.');" title="likes">
    0
    <i class="fa fa-thumbs-up progress-icon" data-icon="thumbs-up"></i>
</span>

                    <abbr class="space1">&#183;</abbr>

<span class="comment-like-btn opt gray font-xs" onclick="likeComment('.$fadd.');" title="likes button">
    <i class="progress-icon hide" data-icon="thumbs-up"></i> Likes
</span>
                </div>
            </div>
        </td>
    </tr>
    </table>
</div>';

$achtml_data = mysql_result(mysql_query("SELECT COUNT(*) FROM `comments` WHERE `post_id`='$post_id'"), 0);

$achtml .= '<span class="comment-activity activity-btn" onclick="javascript:$(\'#story_'.$post_id.' .comments-container\').slideToggle();" title="Coment&amp;aacute;rios">
                            <i class="fa fa-comments progress-icon" data-icon="comments"></i>
                            ' . $achtml_data . '
                        </span>';
$data = array(
    'status' => 200,
    'html' => $html,
    'activity_html' => $achtml
);
   }
header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();
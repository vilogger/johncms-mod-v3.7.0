<?php
$comment_id = stringEscape($_GET['comment_id']);
$bhtml = '';
$ahtml = '';

if (!empty($comment_id)){
    $ktid = mysql_result(mysql_query("SELECT COUNT(*) FROM `comments` WHERE `id`='".$comment_id."'"), 0);
    if($ktid == 0) {
        exit;
    }
    $tkl = mysql_fetch_array(mysql_query("SELECT * FROM `comments` WHERE `id`='{$comment_id}'"));

    $req = mysql_query("SELECT `id` FROM `commentlikes` WHERE `post_id` = '" . $comment_id . "' AND `timeline_id` = '$user_id' ");

    if (mysql_num_rows($req)) {
        mysql_query("DELETE FROM `commentlikes` WHERE `post_id` = '" . $comment_id . "' AND `timeline_id`='" . $user_id . "'");
$bhtml .= '<span class="comment-like-btn opt gray font-xs" onclick="likeComment('.$comment_id.');" title="likes button"><i class="progress-icon hide" data-icon="thumbs-up"></i> Likes</span>';
    }else{
        if($tkl['timeline_id'] != $user_id) {
            mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='".$tkl['timeline_id']."', `them`='7', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã thích [url=".$home."/users/profile.php?act=status&user=".$user_id."&binhluan&id=".$comment_id."]bình luận[/url] của bạn.', `sys`='1', `time`='".time()."'");
            mysql_query("UPDATE `users` SET `thank_duoc` = `thank_duoc` + '1' WHERE `id` = '{$tkl['timeline_id']}'");
            mysql_query("UPDATE `users` SET `thank_di` = `thank_di` + '1' WHERE `id` = '{$user_id}'");
        }
        mysql_query("INSERT INTO `commentlikes` (timeline_id,post_id,time) VALUES (" . $user_id . "," . $comment_id . "," . time() . ")");
$bhtml .= '<span class="comment-like-btn opt gray font-xs" onclick="likeComment('.$comment_id.');" title="likes button"><i class="progress-icon hide" data-icon="thumbs-up"></i> Unlikes</span>';
    }
$result_like = mysql_result(mysql_query("SELECT COUNT(*) FROM `commentlikes` WHERE `post_id`='".$comment_id."'"), 0);
$ahtml .= $result_like.' <i class="fa fa-thumbs-up progress-icon" data-icon="thumbs-up"></i>';
$data = array(
    'status' => 200,
    'button_html' => $bhtml,
    'activity_html' => $ahtml
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();
}
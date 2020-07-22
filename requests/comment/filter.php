<?php
$html = '';
$st = 0;
$before = "";
$post_id = $_GET['post_id'];
if (! empty($_GET['start_row']) && $_GET['start_row'] > 0)
{
    $st = $_GET['start_row'];
}

if (!empty($_GET['before_id']) && $_GET['before_id'] > 0)
{
    $before_id = $_GET['before_id'];
    $before = "`id` > '$before_id' AND";
}

$req = mysql_query("SELECT * FROM `comments` WHERE ".$before." `post_id` = '".$post_id."' ORDER BY `id` DESC LIMIT $st, 4");

        while ($cres = mysql_fetch_assoc($req)) {
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

            $html .= '<div id="comment_'.$cres['id'].'" class="list1 listcmt comment_'.$cres['id'].'" data-comment-id="'.$cres['id'].'" style="position: relative;">
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
             ++$i;
        }

$data = array(
    'status' => 200,
    'html' => $html
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();
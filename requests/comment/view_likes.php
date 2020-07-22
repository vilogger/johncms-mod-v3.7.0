<?php
$comment_id = stringEscape($_GET['comment_id']);

$html = '';

if (!empty($comment_id)){
    $result_like = mysql_result(mysql_query("SELECT COUNT(*) FROM `commentlikes` WHERE `post_id`='".$comment_id."'"), 0);
    if ($result_like > 0){
        $reql = mysql_query("SELECT * FROM `commentlikes` WHERE `post_id` = '" . $comment_id . "' ORDER BY `id` DESC");
        $lhtml = '';
        while ($lres = mysql_fetch_assoc($reql)) {
            $lhtml .= '<div class="window-list-wrapper">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="40px" align="left" valign="middle">
            <a href="/users/profile.php?user=' . $lres['timeline_id'] . '">
                <img class="avatar" src="/avatar/'.$lres['timeline_id'].'-20-40.png" width="32px" alt="">
            </a>
        </td>
        <td align="left" valign="middle">
            <a class="name" href="/users/profile.php?user=' . $lres['timeline_id'] . '">
                <b>' . functions::nickcolor($lres['timeline_id']) . '</b>
            </a>
        </td>
        <td class="window-btn" align="right" valign="middle">
            <i class="fa fa-thumbs-up"></i> 
        </td>
    </tr>
    </table>
</div>';

            ++$l;
        }

        $html = '<div class="window-container">
    <div class="window-background" onclick="SK_closeWindow();"></div>

    <div class="window-wrapper">
        <div class="window-header-wrapper">
            <i class="fa fa-thumbs-up"></i> 
            
            Ai đã thích điều này?

            <span class="window-close-btn" title="Close window" onclick="SK_closeWindow();">
                <i class="fa fa-times"></i>
            </span>
        </div>

        <div class="window-content-wrapper">
            '.$lhtml.'
        </div>
    </div>
</div>';
    }else{
        $html = '<div class="window-container">
    <div class="window-background" onclick="SK_closeWindow();"></div>

    <div class="window-wrapper">
        <div class="window-header-wrapper">
            <i class="fa fa-thumbs-up"></i> 
            
            Ai đã thích điều này?

            <span class="window-close-btn" title="Close window" onclick="SK_closeWindow();">
                <i class="fa fa-times"></i>
            </span>
        </div>

        <div class="window-content-wrapper"><div class="window-list-wrapper">Không có lượt like nào.!</div>
        </div>
    </div>
</div>';
    }
$data = array(
    'status' => 200,
    'html' => $html
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();
}
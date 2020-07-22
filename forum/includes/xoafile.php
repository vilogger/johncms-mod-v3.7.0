<?php

defined('_IN_JOHNCMS') or die('Error: restricted access');
$textl = 'Xóa file đính kèm';
require('../incfiles/head.php');
if (!$id || !$user_id) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}

$req = mysql_query("SELECT * FROM `cms_forum_files` WHERE `id` = '$id'");
$res = mysql_fetch_array($req);
if (!$res) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}

$post = mysql_fetch_assoc(mysql_query("SELECT `user_id` FROM `forum` WHERE `id`='".$res['post']."'"));
if($post['user_id'] == $user_id || $rights >= 6){
    echo'<div class="phdr">Xóa file đính kèm</div>';
        $breq = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
    FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
    WHERE `forum`.`type` = 'm' AND `forum`.`id` = '".$res['post']."'" . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . " LIMIT 1");
        $bres = mysql_fetch_array($breq);
        $them = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `id` = '" . $bres['refid'] . "'"));
        $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $bres['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '".$res['post']."'" . ($rights >= 7 ? '' : " AND `close` != '1'")), 0) / $kmess);
    if(isset($_POST['submit'])){
        mysql_query("DELETE FROM `cms_forum_files` WHERE `id` = '$id'");
        if (file_exists('../files/forum/attach/' . $res['filename'])) {
            unlink('../files/forum/attach/' . $res['filename']);
        }
        echo '<div class="gmenu">File đã được xóa!</div>';
        echo '<div class="phdr"><a href="/forum/' . $bres['refid'] . '/'.$them['seo'].'_p' . $page . '.html#post'.$res['post'].'">Quay lại chủ đề</a></div>';
        require('../incfiles/end.php');
    }else{
        $fls = round(@filesize('../files/forum/attach/' . $res['filename']) / 1024, 2);
        echo'<div class="menu"><strong>Thông tin trước:</strong>
<br/> - Tên file: <span style="color: red">'.$res['filename'].'</span>
<br/> - Kích thước: '.$fls.' kb
<br/> - Lượt tải: '.$res['dlcount'].'
<br/> - Ngày upload: '.functions::display_date($res['time']).'
</div>'.
            '<div class="list1">Bạn thực sự muốn xóa file <span style="color: red">'.$res['filename'].'</span> này không.?'.
            '<form method="post">'.
            '<input name="submit" type="submit" value="Xóa" />'.
            '<a href="/forum/' . $bres['refid'] . '/'.$them['seo'].'_p' . $page . '.html#post'.$res['post'].'"><input name="button" type="button" value="Hủy" /></a>'.
            '</form>'.
            '</div>';
    }
}else{
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}

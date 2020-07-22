<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
require('../incfiles/lib/class.upload.php');
$textl= 'Chia sẻ ảnh';
require('../incfiles/head.php');
require('func.php');

$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
if(!isset($id) || $dem == 0) {
echo '<br/><div class="rmenu">Nhóm không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$id."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
if($kt == 0) {
echo '<div class="rmenu">Phải là thành viên của nhóm!</div>';
require('../incfiles/end.php');
exit;
}
$tnhom = mysql_fetch_array(mysql_query("SELECT * FROM `nhom` WHERE `id`='".$id."'"));
    echo head_nhom($id, $user_id);
    echo '<div class="phdr"><b>Chia sẻ hình ảnh</b></div>';
    if(isset($_POST['submit'])) {
        $mota = functions::checkin($_POST['mota']);

        $timep = mysql_fetch_array(mysql_query("SELECT `lastpost` FROM `users` WHERE `id`='{$user_id}'"));
        if(!empty($ban)) {
            echo '<div class="rmenu">Tài khoản của bạn đang bị khoá nên không thể viết status!</div>';
        } else
        if(($timep['lastpost'] + 30) > time()) {
            echo '<div class="rmenu">Bạn vui lòng đợi sau <b>'.(($timep['lastpost'] +30) - time()).'s</b> nữa!</div>';
        } else if($_FILES['imagefile']['name'] == NULL){
            echo '<div class="rmenu">Bạn chưa chọn hình ảnh!</div>';


        } else if(strlen($_POST['mota']) > 5000) {
            echo '<div class="rmenu">Nội dung chia sẻ đã vượt quá 5000 kí tự!</div>';
        } else {
            $handle = new upload($_FILES['imagefile']);
            if($handle->uploaded) {
                $handle->file_new_name_body = 'anh_'.$time.'';
                //$handle->mime_check = false;
                $handle->allowed = array(
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                );
                $handle->file_max_size = 1024*$set['flsz'];
                $handle->file_overwrite = true;
                $handle->image_resize = true;
                $handle->image_x = 640;
                $handle->image_y = 480;
                $handle->image_ratio_no_zoom_in = true;
                $handle->image_convert ='jpg';
                $handle->process('files/');
                if($handle->processed) {
                    mysql_query("UPDATE `users` SET `lastpost`='".time()."' WHERE `id`='".$user_id."'");
                    @mysql_query("INSERT INTO `nhom_bd` SET `sid`='$id', `user_id`='$user_id', `time`='$time', `stime`='$time', `text`='$mota', `type`='2'");
                    $rid = mysql_insert_id();
                    $reqp = mysql_query("SELECT DISTINCT `user_id` FROM `nhom_user` WHERE `id`='$id' AND `user_id` != '$user_id'");
                    while ($resp = mysql_fetch_array($reqp)) {
                            mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='".$resp['user_id']."', `them`='1', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã đăng nên nhóm [url=".$home."/group/cmt.php?id=".$rid."]".$tnhom['name']."[/url] một hình ảnh.', `sys`='1', `time`='".time()."'");
                    }
                    echo '<div class="gmenu">Tải lên thành công!</div>';
                    echo '<div class="list2"><a href="page.php?id='.$id.'">Trở về nhóm >></a></div>';
                } else {
                    echo functions::display_error($handle->error);
                }
                $handle->clean();
            }
        }
    } else {
        echo '<div class="list1"><form enctype="multipart/form-data" method="post">Chọn hình ảnh:<br/><input type="file" name="imagefile" value=""/><input type="hidden" name="MAX_FILE_SIZE" value="'. (1024 * $set['flsz']) .'"/><br/>Mô tả ảnh:<br/><textarea name="mota" cows="3"></textarea><div style="margin-top:4px;"><input type="submit" name="submit" value="Tải lên" /></div></form></div>';
    }

require('../incfiles/end.php');
?>
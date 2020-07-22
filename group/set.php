<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$textl= 'Quyền hạn';
require('../incfiles/head.php');
require('func.php');
$id= intval(abs($_GET['id']));
$sid= intval(abs($_GET['sid']));
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
if(!isset($id) || $dem == 0) {
echo '<div class="rmenu">Nhóm không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$nhom = nhom($id);
if($nhom['user_id'] != $user_id) {
echo '<div class="rmenu">Bạn không đủ quyền!</div>';
require('../incfiles/end.php');
exit;
}
if($sid == $user_id) {
echo '<div class="rmenu">Không thực hiện được!</div>';
require('../incfiles/end.php');
exit;
}
$user =mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `id`='$id' AND `duyet`='1' AND `user_id`='$sid'"));
$kt =mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='$id' AND `duyet`='1' AND `user_id`='$sid'"),0);
if($kt == 0) {
echo '<div class="rmenu">Người này không nằm trong nhóm của bạn!</div>';
require('../incfiles/end.php');
exit;
}

switch($act) {
default:
echo '<div class="phdr"><b>Thiết lập quyền hạn</b></div>';
$ri = intval($_POST['ri']);
if(isset($_POST['sub'])) {
mysql_query("UPDATE `nhom_user` SET `rights`='$ri' WHERE `id`='$id' AND `user_id`='$sid'");

echo '<div class="gmenu">Lưu thành công!</div>';
}
echo '<div class="rtb">'.ten_nick($sid,1,$id).'</div>';
echo '<div class="list1"><b>Quyền hạn:</b><br/><form method="post"><input type="radio"'.($user['rights'] == 0 ? ' checked="checked"':'').' value="0" name="ri" /> <span class="gray">Thành viên</span><br/><input type="radio"'.($user['rights'] == 1 ? ' checked="checked"':'').' value="1" name="ri" /> <span class="gray">Quản trị viên</span><br/><input type="submit" name="sub" value="Lưu" /></div>';
break;
case 'duoi':
echo '<div class="phdr"><b>Đuổi thành viên</b></div>';
if(isset($_POST['sub'])) {
mysql_query("DELETE FROM `nhom_user` WHERE `id`='$id' AND `user_id`='$sid'");
mysql_query("DELETE FROM `nhom_bd` WHERE `sid`='$id' AND `user_id`='$sid'");
echo '<div class="gmenu">Thành công!</div>';
} else {
echo '<div class="list1">Bạn thực sự muốn đuổi người này ra khỏi nhóm?<br/><form method="post"><input type="submit" name="sub" value="Đuổi" />&#160;&#160;&#160;&#160;<a href="page.php?id='.$id.'"><input type="button" value="Hủy" /></a></div>';
}
break;
}
echo '<div class="list2"><a href="page.php?id='.$id.'">Trở về nhóm >></a></div>';
require('../incfiles/end.php');
?>
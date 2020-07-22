<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$textl= 'Chỉnh sửa nhóm';
require('../incfiles/head.php');
require('func.php');
$id= intval(abs($_GET['id']));
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
if(!isset($id) || $dem == 0) {
echo '<div class="rmenu">Nhóm không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$nhom = nhom($id);
echo head_nhom($id, $user_id);
if($nhom['user_id']!=$user_id) {
echo '<div class="rmenu">Bạn không đủ quyền!</div>';
require('../incfiles/end.php');
exit;
}
echo '<div class="phdr"><b>Chỉnh sửa nhóm</b></div><div class="list1"><a href="avatar.php?id='.$id.'"><b>Thay ảnh đại diện của nhóm >></b></a></div>';
$ten = functions::check($_POST['ten']);
$mota = functions::checkin(trim($_POST['mota']));
$riengtu = intval($_POST['riengtu']);
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `name`='$ten'"),0);

$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$id."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
if(isset($_POST['sub'])) {
if(empty($ten)) {
echo '<div class="rmenu">Bạn không được để trống bất kỳ thông tin nào!</div>';
} else if(strlen($ten) > 100) {
echo '<div class="rmenu">Tên nhóm quá dài!</div>';
} else if($dem > 0 && $nhom['name'] != $ten) {
echo '<div class="rmenu">Tên nhóm đã có người sử dụng. Hãy chọn một tên khác!</div>';
} else if(empty($mota)) {
echo '<div class="rmenu">Bạn không được để trống bất kỳ thông tin nào!</div>';
} else if(strlen($mota) > 5000) {
echo '<div class="rmenu">Nội quy và mục tiêu hoạt động quá dài!</div>';
} else {
mysql_query("UPDATE `nhom` SET `name`='$ten', `gt`='" . mysql_real_escape_string($mota) . "', `set`='$riengtu' WHERE `id`='$id'");
header("Location: page.php?id=$id");
}
}
echo '<form method="post"><div class="list1">Tên nhóm(Max: 100 kí tự):<br/><textarea maxlength="100" rows="' . $set_user['field_h'] . '" name="ten">'.$nhom['name'].'</textarea></div><div class="list1">Nội quy và mục tiêu hoạt động của nhóm: (Max. 5000 kí tự)<br/ ><textarea rows="' . $set_user['field_h'] . '" name="mota">'.htmlentities($nhom['gt'], ENT_QUOTES, 'UTF-8').'</textarea><br/ ><span class="gray">Lưu ý: Mô tả phải viết bằng tiếng việt có dấu, không sử dụng ngôn ngữ teen, nghiêm cấm spam nếu không nhóm có thể bị xoá mà không báo trước.</span></div><div class="list1">Quản lý riêng tư:<br/><input type="radio"'.($nhom['set']==0 ? ' checked="checked"':'').' name="riengtu" value="0" /><b>Mở:</b> <span class="gray">Ai cũng có thể nhìn thấy nhóm, những thành viên trong nhóm và bài đăng của các thành viên.</span><br/><input type="radio"'.($nhom['set']==1 ? ' checked="checked"':'').' name="riengtu" value="1" /><b>Đã đóng:</b> <span class="gray">Ai cũng có thể nhìn thấy nhóm và những thành viên trong nhóm. Nhưng chỉ thành viên mới có thể thấy các bài đăng.</span><br/><input type="radio"'.($nhom['set']==2 ? ' checked="checked"':'').' name="riengtu" value="2" /><b>Bí mật:</b> <span class="gray">Chỉ thành viên mới có thể thấy nhóm, những thành viên khác trong nhóm và bài đăng của các thành viên.</span><br/></div><div class="list1"><input type="submit" name="sub" value="Tiếp tục" /></div></form>';

require('../incfiles/end.php');
?>
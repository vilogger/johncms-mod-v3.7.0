<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$textl= 'Tạo nhóm mới';
require('../incfiles/head.php');
require('func.php');
echo '<div class="phdr"><b>Tạo nhóm</b></div>';
$ten = functions::check($_POST['ten']);;
$mota = functions::checkin(trim($_POST['mota']));
$riengtu = intval($_POST['riengtu']);
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `user_id`='$user_id'"),0);
$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `name`='$ten'"),0);
if(isset($_POST['sub'])) {
if($dem >= 5) {
echo '<div class="rmenu">Mỗi người chỉ được phép tạo tối đa 5 nhóm!</div>';
} else if(empty($ten)) {
echo '<div class="rmenu">Bạn không được để trống bất kỳ thông tin nào!</div>';
} else if(strlen($ten) > 100) {
echo '<div class="rmenu">Tên nhóm quá dài!</div>';
} else if($kt > 0) {
echo '<div class="rmenu">Tên nhóm đã có người sử dụng. Hãy chọn một tên khác!</div>';
} else if(empty($mota)) {
echo '<div class="rmenu">Bạn không được để trống bất kỳ thông tin nào!!</div>';
} else if(strlen($mota) > 5000) {
echo '<div class="tb">Nội quy và mục tiêu hoạt động quá dài!</div>';
} else {
mysql_query("INSERT INTO `nhom` SET `name`='".$ten."', `gt`='".mysql_real_escape_string($mota)."', `set`='".$riengtu."', `user_id`='".$user_id."', `time`='".$time."'");
$rid = mysql_insert_id();
mysql_query("INSERT INTO `nhom_user` SET `id`='$rid', `user_id`='$user_id', `time`='$time', `rights`='2', `duyet`='1'");
header("Location: page.php?id=$rid");
}
}
echo '<form method="post"><div class="list1">Tên nhóm(Max: 100 kí tự):<br/><textarea rows="3" name="ten"></textarea></div><div class="list1">Nộ quy và mục tiêu hoạt động của nhóm: (Max. 5000 kí tự)<br/ ><textarea rows="3" name="mota"></textarea><br/ ><span class="gray">Lưu ý: Mô tả phải viết bằng tiếng việt có dấu, không sử dụng ngôn ngữ teen, nghiên cấm spam và quảng cáo wap nếu không nhóm có thể bị xóa mà không báo trước.</span></div><div class="list1">Quản lý riêng tư:<br/><input type="radio" checked="checked" name="riengtu" value="0" /><b>Mở:</b> <span class="gray">Ai cũng có thể nhìn thấy nhóm, những thành viên trong nhóm và bài đăng của các thành viên.</span><br/><input type="radio" name="riengtu" value="1" /><b>Đã đóng:</b> <span class="gray">Ai cũng có thể nhìn thấy nhóm và những thành viên trong nhóm. Nhưng chỉ thành viên mới có thể thấy các bài đăng.</span><br/><input type="radio" name="riengtu" value="2" /><b>Bí mật:</b> <span class="gray">Chỉ thành viên của nhóm mới có thể nhìn thấy nội dung của nhóm.</span><br/></div><div class="list1"><input type="submit" name="sub" value="Tiếp tục" /></div></form>';

require('../incfiles/end.php');
?>
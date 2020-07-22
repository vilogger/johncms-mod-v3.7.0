<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$textl = 'Thông tin nhóm';
require('../incfiles/head.php');
require('func.php');
$id = intval(abs($_GET['id']));
echo head_nhom($id, $user_id);
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
if(!isset($id) || $dem == 0) {
echo '<br/><div class="tb">Nhóm không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$nhom = nhom($id);
$user = user_nick($nhom['user_id']);
$t = $nhom['time'];
$anh = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `sid`='$id' AND `type`='2'"),0);
$baidang = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `sid`='$id' AND (`type`='0' OR `type`='2')"),0);
$binhluan = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `sid`='$id' AND `type`='1'"),0);
$thv = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$id."' AND `duyet`='1'") ,0);
$gt = $nhom['gt'];
$gt = html_entity_decode($gt, ENT_QUOTES, 'UTF-8');
$gt = functions::checkout($gt, 1, 1);
echo '<div class="phdr"><b>Thông tin nhóm</b></div><div class="list1"><b>Người lập: </b>'.$user['name'].'</div><div class="list1"><b>Ngày lập: </b><span class="gray">'.date("d/m/Y",$t+7*3600).'</span></div><div class="list1"><b>Thành viên: </b><span class="gray">'.$thv.'</span></div><div class="list1"><b>Hình ảnh: </b><span class="gray">'.$anh.'</span></div><div class="list1"><b>Bài đăng: </b><span class="gray">'.$baidang.'</span></div><div class="list1"><b>Thảo luận: </b><span class="gray">'.$binhluan.'</span></div><div class="list1"><b>Nội quy và mục tiêu hoạt động của nhóm: </b><br /><span class="gray">'.functions::smileys($gt).'</span></div>';
require('../incfiles/end.php');
?>
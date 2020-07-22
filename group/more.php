<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$textl = 'Thao tác';
require('../incfiles/head.php');
require('func.php');
$id = intval(abs($_GET['id']));

switch($act) {
default:
echo '<div class="phdr"><b>Nhóm đã tham gia</b></div>';
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `user_id`='$user_id'"),0);
if($dem) {
$req = mysql_query("SELECT * FROM `nhom_user` WHERE `user_id`='$user_id' ORDER BY `stime` DESC LIMIT $start,$kmess");
while($res=mysql_fetch_array($req)) {
$nhom = nhom($res['id']);
echo '<div class="list1"><table cellpadding="0" cellspacing="0"><tr><td>';
$url = @getimagesize('avatar/'.$res['id'].'.png');
if(is_array($url)){
echo '<img src="avatar/'.$res['id'].'.png" width="35" height="35" alt="" />';
}else{
echo '<img src="avatar/noavatar.png" width="35" height="35" alt="" />';
}





echo '</td><td style="padding: 0px 0px 0px 4px;"><a href="page.php?id='.$res['id'].'"><b>'.$nhom['name'].'</b></a></td></tr></table></div>';
}
if ($dem > $kmess){echo '<div class="topmenu">' . functions::display_pagination('more.php?', $start, $dem, $kmess) . '</div>';
}
} else {
echo '<div class="rmenu">Chưa tham gia nhóm nào!</div>';
}
break;

case 'nhom':
echo '<div class="phdr"><b>Danh sách nhóm</b></div>';
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom`"),0);
if($dem) {
$req = mysql_query("SELECT * FROM `nhom` ORDER BY `time` DESC LIMIT $start,$kmess");
while($res = mysql_fetch_array($req)) {
$nhom = nhom($res['id']);


echo '<div class="list1"><table cellpadding="0" cellspacing="0"><tr><td>';
$url = @getimagesize('avatar/'.$res['id'].'.png');
if(is_array($url)){
echo '<img src="avatar/'.$res['id'].'.png" width="35" height="35" alt="" />';
}else{
echo '<img src="avatar/noavatar.png" width="35" height="35" alt="" />';
}
echo '</td><td style="padding: 0px 0px 0px 4px;"><a href="page.php?id='.$res['id'].'"><b>'.$nhom['name'].'</b></a></td></tr></table></div>';
}
if ($dem > $kmess){echo '<div class="topmenu">' . functions::display_pagination('more.php?act=nhom&', $start, $dem, $kmess) . '</div>';
}
} else {
echo '<div class="rmenu">Chưa có nhóm nào!</div>';
}
break;
case 'mem':
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
if(isset($id) && $dem == 0) {
echo '<br/><div class="rmenu">Nhóm không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
if(!isset($id) || $dem == 0) {
echo '<br/><div class="rmenu">Nhóm không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$nhom = nhom($id);
    $ktviet = mysql_result( mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `user_id`='$user_id' AND `id`='$id' AND `duyet`='1'"),0);
    if($ktviet == 0 && $nhom['set'] == 2){
        echo '<div class="rmenu">Chỉ dành cho thành viên của nhóm</div>';
        require('../incfiles/end.php');
        exit;
}
echo head_nhom($id, $user_id);
echo '<div class="phdr"><b>Thành viên</b></div>';
$tong =mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='$id' AND `duyet`='1'"),0);
$req =mysql_query("SELECT * FROM `nhom_user` WHERE `id`='$id' AND `duyet`='1' ORDER BY `time` DESC LIMIT $start,$kmess");
while($res=mysql_fetch_array($req)) {
echo '<div class="list1">'.ten_nick($res['user_id'],1,$res['id']).''.($res['user_id']!=$user_id && $nhom['user_id']==$user_id ? '<div class="sub"><a href="set.php?id='.$id.'&sid='.$res['user_id'].'"><b>Quyền hạn</b></a> | <a href="set.php?act=duoi&id='.$id.'&sid='.$res['user_id'].'" style="color:red;">Đuổi</a></div>':'').'</div>';
}
if ($tong > $kmess){echo '<div class="topmenu">' . functions::display_pagination('more.php?act=mem&id='.$id.'&', $start, $tong, $kmess) . '</div>';
}
echo '<div class="list2"><a href="page.php?id='.$id.'">Trở về nhóm >></a></div></div>';
break;
case 'like':
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `id`='".$id."'"),0);
if($dem == 0) {
echo '<br/><div class="rmenu">Bài đăng không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}

echo '<div class="phdr"><b>Những người thích điều này.!</b></div>';
$tong =mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='$id'"),0);
$req =mysql_query("SELECT * FROM `nhom_like` WHERE `id`='$id' ORDER BY `time` DESC LIMIT $start,$kmess");
while($red=mysql_fetch_array($req)) {
$res = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_bd` WHERE `id`='".$red['id']."'"));
echo '<div class="list1">'.ten_nick($red['user_id'],1,$res['id']).'</div>';
}
if ($tong > $kmess){echo '<div class="topmenu">' . functions::display_pagination('more.php?act=like&id='.$id.'&', $start, $tong, $kmess) . '</div>';
}
echo '</div>';
break;
}

require('../incfiles/end.php');
?>
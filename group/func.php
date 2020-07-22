<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/












































if(!$user_id) {
echo '<div class="rmenu">Chỉ dành cho thành viên đăng nhập!</div>';
require('../incfiles/end.php');
exit;
}

$time = time();
//func time viet bai
function ngaygio($var) {
$time = time();
$jun = round(($time-$var)/60);
$shift = ($system_set['timeshift']+$user_set['timeshift'])*3600;
if (date('Y', $var) == date('Y', time())) {
if($jun < 1) {
$jun = 'Vừa xong';
}
if($jun >= 1 && $jun < 60){
$jun = "$jun phút trước";
}
if($jun >= 60 && $jun < 1440){
$jun = round($jun/60);
$jun = "$jun giờ trước";
}
if($jun >= 1440 && $jun < 2880){
$jun = "Hôm qua";
}
if($jun >= 2880 && $jun < 10080){
$day = round($jun/60/24);
$jun = "$day ngày trước";
}
}
if($jun > 10080){
$jun = date("d/m/Y - H:i", $var+$shift);
}
$xuat = '<span class="gray">'.$jun.'</span>';
return $xuat;
}
//func hien tên nick
function ten_nick($id, $set = 0, $sid = 0) {
$var = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id`='".$id."'"));
$vad = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `id`='".$sid."'AND `user_id`='".$id."'"));

$array = array(
0 => '(Thành viên)',
1 => '(Quản trị)',
2 => '(Thủ lĩnh)'
);

if($set==0) {
$xuat .= (time() > $var['lastdate']+300 ? '<span style="color:red;">&#8226;</span>' : '<span style="color:green;">&#8226;</span>');
$xuat .= ' <a href="/users/profile.php?user='.$id.'"><b>'.$var['name'].'</b></a>';







} else {
$xuat .= '<table style="padding: 0; border-spacing: 0;"><tr><td>';
$ur = @getimagesize('../files/users/avatar/'.$id.'.png');
if(is_array($ur))
$xuat .= '<div style="WIDTH: 40px; BACKGROUND: url(/files/users/avatar/'.$id.'.png) no-repeat; HEIGHT: 40px; background-size: 40px 40px; -webkit-border-radius: 50%; border-radius: 50%; -moz-border-radius: 50%;"></div>';
else
$xuat .= '<div style="WIDTH: 40px; BACKGROUND: url(/images/empty.png) no-repeat; HEIGHT: 40px; background-size: 40px 40px; -webkit-border-radius: 50%; border-radius: 50%; -moz-border-radius: 50%;"></div>';

$xuat .= '</td><td style="padding: 0px 0px 0px 4px;">';
$xuat .= (time() > $var['lastdate']+300 ? '<span style="color:red;">&#8226;</span>' : '<span style="color:green;">&#8226;</span>');
$xuat .= '&#160<a href="/users/profile.php?user='.$id.'"><b>'.$var['name'].'</b></a>';
$xuat .='<br /><span class="gray">'.$array[$vad['rights']].'</span>';
}
$xuat .= '</td></tr></table>';

return $xuat;
}
//func xuat thong tin tu user
function user_nick($id) {
$var = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id`='".$id."'"));
return $var;
}
//func hien head nhom
function head_nhom($id, $user_id) {
$nhom = mysql_fetch_array(mysql_query("SELECT * FROM `nhom` WHERE `id`='".$id."'"));
$array = array(
0 => 'Nhóm công khai',
1 => 'Nhóm đóng',
2 => 'Nhóm kín');
$url = @getimagesize('avatar/'.$id.'.png');
if(is_array($url))
$xuat .= '<div class="phdr">Hội nhóm - Clan</div><div class="list1"><table width="100%" style="table-layout: fixed; word-wrap: break-word;"><tr><td width="35"><center><img src="avatar/'.$id.'.png" alt="" /></center></td><td style="padding: 0px 0px 0px 4px;"><b><a href="page.php?id='.$nhom['id'].'">'.$nhom['name'].'</a></b><br/ ><span class="gray">'.$array[$nhom['set']].'</span></td></tr></table>';
else
$xuat .= '<div class="phdr">Hội nhóm - Clan</div><div class="list1"><table cellpadding="0" cellspacing="0" width="100%"><tr><td width="35px"><center><img src="avatar/noavatar.png" alt="" /></center></td><td><b><a href="page.php?id='.$nhom['id'].'">'.$nhom['name'].'</a></b><br/ ><span class="gray">'.$array[$nhom['set']].'</span></td></tr></table>';
$xuat .= '</div>';
$ktdem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `user_id`='".$user_id."' AND `id`='".$id."'"), 0);
$kt = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `user_id`='".$user_id."' AND `id`='".$id."'"));
if($ktdem != 0 && $kt['duyet'] != 0){
mysql_query("UPDATE `nhom_user` SET  `stime` = ". time() . " WHERE `id` = '$id' AND `user_id`='$user_id'");
mysql_query("UPDATE `nhom_user` SET  `view` = '0' WHERE `id` = '$id' AND `user_id`='$user_id'");
}
$xuat .= ''.($ktdem == 0 ? '<div class="list1"><form method="post" action="page.php?thamgia&id='.$id.'"><input type="submit" name="sub" value="Tham gia nhóm" /></form></div>' : ''.($kt['duyet'] == 0 ? '<div class="list2"><form method="post" action="page.php?rutkhoi&id='.$id.'"><input type="submit" name="sub" value="Đang chờ duyệt" /></form></div>' : '').'').'';
if($nhom['set'] == 0 || $kt['duyet'] == 1) $xuat .= '<div class="topmenu" style="text-align:center; font-weight: bold;"><a href="album.php?id='.$id.'">Album ảnh</a> · <a href="thongtin.php?id='.$id.'">Thông tin nhóm</a></div>';
    //duyet don
    $duyet = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='$id' AND `duyet`='0'"),0);
    if($nhom['user_id'] == $user_id) {
        if($duyet > 0){
            $xuat .= '<div class="gmenu"><a href="duyet.php?id='.$id.'"><b>Đơn xin gia nhập ('.$duyet.')</b></a></div>';
        }
    }
return $xuat;
}
function catchu($string, $start, $length){
$arrwords = explode(" ",$string);
$arrsubwords = array_slice($arrwords, $start, $length);
$result = implode(" ",$arrsubwords);
return $result;
}
//func lay info nhom theo id
function nhom($id) {
$var = mysql_fetch_array(mysql_query("SELECT * FROM `nhom` WHERE `id`='".$id."'"));
return $var;
}
//func chuc vu trong nhom
function quyen_nhom($id,$user) {
$var = mysql_fetch_array(mysql_query("SELECT `rights` FROM `nhom_user` WHERE `id`='".$id."' AND `user_id`='".$user."'"));
$mang = array(
0 => '(Thành viên)', 1 => '(Quản trị)', 2 => '(Thủ lĩnh)');
return $mang[$var['rights']];
}
//func cat ngan
function thugon($text,$id) {
$tach = explode(' ',$text);
$dem = count($tach);
$luong = 30;
if($dem > $luong) {
$xuat =functions::checkout($text, 1, 1);
$xuat = functions::smileys(bbcode::tags($xuat));
$xuat = ''.catchu(bbcode::notags($xuat), 0, $luong).' ... <a href="action.php?act=post&id='.$id.'">Đọc tiếp... >></a>';
} else {
$xuat =functions::checkout($text, 1, 1);
$xuat = functions::smileys(bbcode::tags($xuat));
}
return $xuat;
}

?>
<?php
define('_IN_JOHNCMS',1);
require('../../incfiles/core.php');
$textl = 'Upload ảnh miễn phí';
require('../../incfiles/head.php');
if (!$user_id) {
    echo functions::display_error($lng['access_forbidden']);
    require('../../incfiles/end.php');
    exit;
}
$id = isset($_GET['id']) ? functions::check(intval($_GET['id'])):'2';
$check = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `cms_image` WHERE `id` = '$id'"),0);
if($check >0){
echo '<div class="phdr"><i class="fa fa-info-circle"></i> Thông tin ảnh</div>'.(isset($_GET['new']) ? '<div class=list1><center><b><font size=4><font color=red>Tải Ảnh Lên Thành Công!!</font></font></b></center></div>' : '');
$reg = mysql_query("SELECT * FROM `cms_image` WHERE `id` = '$id'");
while($arr=mysql_fetch_assoc($reg)){
$res = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = {$arr['user']} LIMIT 1"));

echo '<div class="menu"><center><img style="max-width: 100%;" src="'.$arr['url'].'" alt="Upload ảnh miễn phí"><br /><a href="'.$arr['url'].'"><div style="background:#9C27B0;border:2px solid #9C27B0;padding:4px;margin-top: 3px;width:45%;text-align:center;border-radius:2px"><i class="fa fa-cloud-download" style="color:white"></i> <b><font color=#ffffff>Download ảnh ('.$arr['size'].'KB)</font></b></a></center>';

if(($user_id == $res['id'] || $rights > $res['rights']) && !isset($_GET['new'])){
echo '<br /><a href="/tool/image-upload/delete.php?id='.$arr['id'].'"><b>Xóa</b></a>';
}
echo '</div><div class="menu">'.(!isset($_GET['new']) ? '<b>• Người  Upload: '.functions::nickcolor($res['id']).'<br />• Lúc: '.functions::display_date($arr['time']).'<br />• Kích thước: '.$arr['size'].' KB</b></div><div class="list1">' : '').'Chia sẻ:<br /><form><input value="[img='.$arr['url'].']" /></form>
<form><input value="[img]'.$arr['url'].'[/img]" /></form></div>'.(isset($_GET['new']) ? '<div class="menu"><a href="/tool/image-upload/upload.php"><button class="topic cat_red" style="padding:4px;">Tiếp tục Upload</button></a>&#160;&#160;<a href="/tool/image-upload/"><button type="button" class="topic cat_green" style="padding:4px;">Danh sách ảnh</button></a></div>' : '');
}
} else {
echo '<div class="rmenu">File ảnh không tồn tại hoặc đã bị xóa</div>';
}
require('../../incfiles/end.php');
?>
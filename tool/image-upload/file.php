<?php
define('_IN_JOHNCMS',1);
require('../../incfiles/core.php');
$textl = 'Ảnh của tôi - Upload ảnh miễn phí';
require('../../incfiles/head.php');
if (!$user_id) {
    echo functions::display_error($lng['access_forbidden']);
    require('../../incfiles/end.php');
    exit;
}
$id = isset($_GET['id']) ? functions::check(intval($_GET['id'])) : $user_id;
echo '<style>.category{overflow:hidden;}
.listcat{list-style:none outside none;float:left;width:100px;height:100px;margin:2px;text-align:center;}</style>';
echo '<div class="phdr"><i class="fa fa-picture-o"></i> Bộ sưu tập của '.functions::nickcolor($id).'</div>';
$tong = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `cms_image` WHERE `user` = '".intval($id)."'"),0);
if($tong > 0){
echo '<div class="menu"><ul class="category">';
$reg = mysql_query("SELECT * FROM `cms_image` WHERE `user` = '".intval($id)."' ORDER BY `time` DESC LIMIT $start,$kmess");
while($res=mysql_fetch_assoc($reg)){
echo '<li class="listcat"><a href="'.$home.'/hinhanh'.$res['id'].'.html" title="Upload ảnh nhanh - miễn phí"><img style="max-width:100px;max-height:100px;" src="'.$res['url'].'"></a></li>';
}
echo '</ul></div>';
} else {
echo '<div class="list1">'.functions::nickcolor($id).' chưa upload ảnh nào </div>';
}
if ( $tong > $kmess ){echo '<div class="topmenu">' . functions :: display_pagination2 ( 'file'.$id , $start , $tong , $kmess ) . '</div>' ;}

echo '<div class="phdr">Tổng: '.$tong.' ảnh</div>';
require('../../incfiles/end.php');
?>
<?php
define('_IN_JOHNCMS',1);
require('../../incfiles/core.php');
$textl = 'Chia sẽ ảnh miễn phí';
require('../../incfiles/head.php');
if (!$user_id) {
    echo functions::display_error($lng['access_forbidden']);
    require('../../incfiles/end.php');
    exit;
}
echo '<style>.category{overflow:hidden;}
.listcat{list-style:none outside none;float:left;width:100px;height:100px;margin:2px;text-align:center;}
.fileupload-example-4-label {
    border: 1px solid #009688;
    padding: 5px 15px;
    margin:5px 3px;
   background:#009688;
   color:white;
   border-radius:4px;
}
</style>';

$res = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `cms_image` WHERE `user`='$user_id' "),0);

echo '<div class="phdr">Chia sẽ ảnh miễn phí</div><div class="menu"><a href="'.$home.'/aa" title="Upload ảnh miễn phí">
<button type="submit" name="submit" class="fileupload-example-4-label"><i class="fa fa-upload"></i> Upload Ảnh</button></a>&#160;
<a href="'.$home.'/upload-hinhanh/file'.$user_id.'.html"><button type="submit" name="submit" class="fileupload-example-4-label" style="border:1px solid #9C27B0;background:#9C27B0"><i class="fa fa-picture-o"></i> Của bạn [<b>'.$res.'</b>]</button></a></div>';

echo '<div class="menu"><ul class="category">';
$data = mysql_query("SELECT * FROM `cms_image` ORDER BY `time` DESC LIMIT $start,$kmess");
$total = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `cms_image`"),0);
while($img = mysql_fetch_assoc($data)){

echo '<li class="listcat"><a href="'.$home.'/hinhanh'.$img['id'].'.html" title="Upload ảnh nhanh - miễn phí"><img style="max-width: 100px; max-height: 100px;" src="'.$img['url'].'"></a></li>';


}
echo '</ul></div>';
if ( $total > $kmess ){echo '<div class="topmenu">' . functions :: display_pagination ( ''.$home.'/tool/image-upload/index.php?' , $start , $total , $kmess ) . '</div>' ;}

require('../../incfiles/end.php');
?>
<?php
   define('_IN_JOHNCMS', 1);
   require('../incfiles/core.php');

   if ($user_id) {
    $xemnotification = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `from_id`='$user_id' AND `read`='0' AND `sys`='1' AND `delete`!='$user_id';"), 0);
    $xemmessage = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` LEFT JOIN `cms_contact` ON `cms_mail`.`user_id`=`cms_contact`.`from_id` AND `cms_contact`.`user_id`='$user_id' WHERE `cms_mail`.`from_id`='$user_id' AND `cms_mail`.`sys`='0' AND `cms_mail`.`read`='0' AND `cms_mail`.`delete`!='$user_id' AND `cms_contact`.`ban`!='1' AND `cms_mail`.`spam`='0'"), 0);
    $xemfriend = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `from_id`='$user_id' AND `type`='2' AND `friends`='0';"), 0);
    $xemalbum = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . core::$user_id . "' AND `unread_comments` = 1"), 0);
    $tongthongbao = ($xemnotification + $xemfriend + $xemalbum + $xemmessage);
    $xemthongbao = ($xemnotification + $xemfriend + $xemalbum);

echo "<script type=\"text/javascript\">
    function flash_title() {
        if($tongthongbao > 0){
            step++;
            if(step==3){step=1};
            if(step==1){document.title=title};
            if(step==2){document.title='Bạn có ".($xemthongbao > 0 && $xemmessage > 0 ? $xemmessage.' tin nhắn và '.$xemthongbao.' thông báo' : ($xemthongbao > 0 ? $xemthongbao.' thông báo' : ($xemmessage > 0 ? $xemmessage.' tin nhắn' : '')))." mới .!!'};
            setTimeout(\"flash_title()\",1500);
        };
    };
    flash_title();
    </script>";
   }

?>
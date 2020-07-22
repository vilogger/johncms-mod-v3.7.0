<?php

    /**
      * Code auto load by Izero
      */
   define('_IN_JOHNCMS', 1);
   require('../incfiles/core.php');

   if (isset($_POST['load'])) {
        mysql_query("UPDATE `users` SET  `lastdate` = ". time() . " WHERE `id` = '$user_id'");
        $notification = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `from_id`='$user_id' AND `read`='0' AND `sys`='1' AND `delete`!='$user_id';"), 0);
        $message = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` LEFT JOIN `cms_contact` ON `cms_mail`.`user_id`=`cms_contact`.`from_id` AND `cms_contact`.`user_id`='$user_id' WHERE `cms_mail`.`from_id`='$user_id' AND `cms_mail`.`sys`='0' AND `cms_mail`.`read`='0' AND `cms_mail`.`delete`!='$user_id' AND `cms_contact`.`ban`!='1' AND `cms_mail`.`spam`='0'"), 0); // Truy vấn tin nhắn
        $friend = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `from_id`='$user_id' AND `type`='2' AND `friends`='0';"), 0); // Truy vấn kết bạn
        $group = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . core::$user_id . "' AND `unread_comments` = 1"), 0); // Truy vấn hội nhóm

        if (function_exists('json_encode'))
            echo json_encode(array('notification' => $notification, 'message' => $message, 'friend' => $friend, 'group' => $group));
        else
            echo "{\"notification\":$notification,\"message\":$message,\"friend\":$friend,\"group\":$group}";
   } else {
       echo 'Error';
   }

?>
<?php
$comment_id = stringEscape($_GET['comment_id']);

if (!empty($comment_id)){
    $q = mysql_query("SELECT `timeline_id` FROM `comments` WHERE `id`='$comment_id' ");

    $kt = mysql_fetch_array($q);
    if($kt['timeline_id'] == $user_id) {
        if(mysql_num_rows($q)>0) {
            mysql_query("DELETE FROM `comments` WHERE `id`='$comment_id' ");
            $l = mysql_query("SELECT `timeline_id` FROM `commentlikes` WHERE `post_id`='$comment_id' ");
            if(mysql_num_rows($l)>0) {
                mysql_query("DELETE FROM `commentlikes` WHERE `post_id`='$comment_id' ");
            }

            $data = array(
                'status' => 200
            );
    
            header("Content-type: application/json; charset=utf-8");
            echo json_encode($data);
            exit();
        }
    }
}
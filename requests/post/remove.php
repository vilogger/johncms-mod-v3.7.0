<?php
$post_id = stringEscape($_GET['post_id']);

if (!empty($post_id)){
    $q = mysql_query("SELECT `timeline_id` FROM `posts` WHERE `id`='$post_id' ");

    $kt = mysql_fetch_array($q);
    if($kt['timeline_id'] == $user_id) {
        if(mysql_num_rows($q)>0) {
            mysql_query("DELETE FROM `posts` WHERE `id`='$post_id' ");
            $l = mysql_query("SELECT `timeline_id` FROM `posttlikes` WHERE `post_id`='$post_id' ");
            if(mysql_num_rows($l)>0) {
                mysql_query("DELETE FROM `postlikes` WHERE `post_id`='$post_id' ");
            }

            $c = mysql_query("SELECT `id` FROM `comments` WHERE `post_id`='$post_id' ");
            while ($cdata = mysql_fetch_array($c)){
                $cid = $cdata['id'];
                $lc = mysql_query("SELECT `timeline_id` FROM `commentlikes` WHERE `post_id`='".$cid."' ");
                if(mysql_num_rows($lc)>0) {
                    mysql_query("DELETE FROM `commentlikes` WHERE `post_id`='$cid' ");
                }
                ++$i;
            }
            if(mysql_num_rows($c)>0) {
                mysql_query("DELETE FROM `comments` WHERE `post_id`='$post_id' ");
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
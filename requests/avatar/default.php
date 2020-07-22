<?php

$timelineId = $user_id;

if (isset($_FILES['image']['tmp_name'])){
    $image = $_FILES['image'];
    $avatar = registerMedia($image);

        if (isset($avatar['id']))
        {
            $query = mysql_query("UPDATE `users` SET avatar_extension='" . $avatar['extension'] . "',cover_position=0 WHERE id=" . $timelineId);

            if ($query)
            {
                echo  '<div class="menu center"><img src="/'.$avatar['url'].'.'.$avatar['extension'].'?'.$time.'" style="max-width: 100%;box-sizing: border-box;" /><br /> Avatar tải lên thành công....<br /><a href="profile.php?act=edit&amp;user=' . $user_id . '">Tiếp tục</a></div>';
            }
        }

}
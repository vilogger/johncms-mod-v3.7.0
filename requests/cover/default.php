<?php
if (isset($_FILES['image']['tmp_name'])){
    $coverImage = $_FILES['image'];
    $timelineId =  $user_id;
        $coverData = registerCoverImage($coverImage);
        
        if (isset($coverData['id']))
        {
            $query = mysql_query("UPDATE `users` SET cover_extension='" . $coverData['extension'] . "',cover_position=0 WHERE id=" . $timelineId);

            if ($query)
            {
                echo  '<div class="menu center"><img src="/'.$coverData['url'].'.'.$coverData['extension'].'?'.$time.'" style="max-width: 100%;box-sizing: border-box;" /><br /> Ảnh bìa tải lên thành công....<br /><a href="profile.php?act=edit&amp;user=' . $user_id . '">Tiếp tục</a></div>';
            }
        }
}
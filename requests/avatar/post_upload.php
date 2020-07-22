<?php
$continue = false;
$processed = false;

if ($_POST['timeline_id'] == $user_id)
{
    $timelineId = $user_id;
    $continue = true;
}

if (isset($_FILES['image']['tmp_name']) && $continue == true)
{
    $image = $_FILES['image'];
    $avatar = registerMedia($image);
    
    if (isset($avatar['id']))
    {
        $query = mysql_query("UPDATE `users` SET avatar_extension='" . $avatar['extension'] . "' WHERE id=$timelineId");
    }
}